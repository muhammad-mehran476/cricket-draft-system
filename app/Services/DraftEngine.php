<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DraftPick;
use App\Models\DraftQueue;
use App\Models\DraftRound;
use App\Models\DraftSession;
use App\Models\Player;
use App\Models\Team;
use App\Events\DraftStarted;
use App\Events\PlayerPicked;
use App\Events\DraftTimerExpired;
use App\Events\CategoryChanged;
use App\Events\DraftCompleted;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DraftEngine
{
    private DraftSession $session;

    public function __construct(DraftSession $session)
    {
        $this->session = $session;
    }

    // ── Start the draft ───────────────────────────────────────
    public function startDraft(): DraftSession
    {
        DB::transaction(function () {
            $firstCategory = Category::active()->ordered()->first();

            $this->session->update([
                'status'              => 'active',
                'current_category_id' => $firstCategory->id,
                'current_round'       => 1,
                'started_at'          => now(),
            ]);

            $this->createRoundForCategory($firstCategory);
            AuditLog::record('draft_started', 'Draft session started: ' . $this->session->title, $this->session);
        });

        broadcast(new DraftStarted($this->session->fresh(['currentCategory', 'currentTeam'])));
        return $this->session->fresh();
    }

    // ── Create a draft round for a category ──────────────────
    public function createRoundForCategory(Category $category): DraftRound
    {
        $teams     = Team::approved()->get();
        $teamOrder = $teams->pluck('id')->shuffle()->values()->toArray();

        $round = DraftRound::create([
            'draft_session_id' => $this->session->id,
            'category_id'      => $category->id,
            'round_number'     => $this->session->current_round,
            'team_order'       => $teamOrder,
            'status'           => 'active',
            'started_at'       => now(),
        ]);

        // Build the queue
        foreach ($teamOrder as $position => $teamId) {
            DraftQueue::create([
                'draft_session_id' => $this->session->id,
                'draft_round_id'   => $round->id,
                'team_id'          => $teamId,
                'pick_position'    => $position + 1,
                'status'           => $position === 0 ? 'active' : 'waiting',
                'timer_expires_at' => $position === 0 ? now()->addSeconds($this->session->timer_seconds) : null,
            ]);
        }

        // Set current team turn
        $firstTeamId = $teamOrder[0] ?? null;
        $this->session->update([
            'current_team_turn_id' => $firstTeamId,
            'timer_started_at'     => now(),
        ]);

        AuditLog::record('round_created', "Round created for category: {$category->name}", $round);
        return $round;
    }

    // ── Pick a player ─────────────────────────────────────────
    public function pickPlayer(Team $team, Player $player): array
    {
        // Validate it is this team's turn
        $queueEntry = DraftQueue::where('draft_session_id', $this->session->id)
            ->where('team_id', $team->id)
            ->where('status', 'active')
            ->first();

        if (!$queueEntry) {
            return ['success' => false, 'message' => 'It is not your turn to pick.'];
        }

        // Validate player is available
        if ($player->status !== 'approved') {
            return ['success' => false, 'message' => 'Player is not available for drafting.'];
        }

        if ($player->category_id !== $this->session->current_category_id) {
            return ['success' => false, 'message' => 'Player does not belong to the current draft category.'];
        }

        if (!$team->canPickMorePlayers()) {
            return ['success' => false, 'message' => 'Your team has reached the maximum player limit.'];
        }

        $timeTaken = $this->session->timer_started_at
            ? now()->diffInSeconds($this->session->timer_started_at)
            : null;

        DB::transaction(function () use ($team, $player, $queueEntry, $timeTaken) {
            $activeRound = $this->session->activeRound();

            $pickNumber = DraftPick::where('draft_session_id', $this->session->id)->count() + 1;

            // Record the pick
            $pick = DraftPick::create([
                'draft_session_id' => $this->session->id,
                'draft_round_id'   => $activeRound->id,
                'team_id'          => $team->id,
                'player_id'        => $player->id,
                'category_id'      => $this->session->current_category_id,
                'pick_number'      => $pickNumber,
                'time_taken_seconds' => $timeTaken,
                'picked_at'        => now(),
            ]);

            // Lock the player
            $player->update(['status' => 'drafted', 'team_id' => $team->id]);

            // Update team count
            $team->increment('total_players_drafted');

            // Mark queue entry done
            $queueEntry->update(['status' => 'done']);

            // Advance queue
            $this->advanceQueue($activeRound);

            AuditLog::record('player_picked', "{$team->team_name} picked {$player->name}", $pick);

            broadcast(new PlayerPicked($pick->load(['player', 'team', 'category']), $this->session));
        });

        return ['success' => true, 'message' => 'Player drafted successfully!'];
    }

    // ── Advance to next team in queue ─────────────────────────
    private function advanceQueue(DraftRound $round): void
    {
        $nextEntry = DraftQueue::where('draft_round_id', $round->id)
            ->where('status', 'waiting')
            ->orderBy('pick_position')
            ->first();

        if ($nextEntry) {
            $nextEntry->update([
                'status'          => 'active',
                'timer_expires_at' => now()->addSeconds($this->session->timer_seconds),
            ]);
            $this->session->update([
                'current_team_turn_id' => $nextEntry->team_id,
                'timer_started_at'     => now(),
            ]);
        } else {
            // Round complete — check if we need to go to next category
            $round->update(['status' => 'completed', 'completed_at' => now()]);
            $this->transitionToNextCategory();
        }
    }

    // ── Handle timer expiry (skip team) ──────────────────────
    public function handleTimerExpiry(): void
    {
        $activeQueue = $this->session->currentQueueEntry();
        if (!$activeQueue) return;

        DB::transaction(function () use ($activeQueue) {
            $activeQueue->update(['status' => 'skipped']);

            $activeRound = $this->session->activeRound();
            $this->advanceQueue($activeRound);

            AuditLog::record('turn_skipped', "Timer expired — {$activeQueue->team->team_name} skipped", $activeQueue);
            broadcast(new DraftTimerExpired($this->session->fresh(), $activeQueue->team));
        });
    }

    // ── Move to next category ─────────────────────────────────
    private function transitionToNextCategory(): void
    {
        $currentOrder = $this->session->currentCategory->draft_order;
        $nextCategory = Category::active()
            ->ordered()
            ->where('draft_order', '>', $currentOrder)
            ->first();

        if ($nextCategory) {
            $this->session->update([
                'current_category_id' => $nextCategory->id,
                'current_round'       => $this->session->current_round + 1,
            ]);
            $this->createRoundForCategory($nextCategory);
            AuditLog::record('category_changed', "Draft moved to category: {$nextCategory->name}", $this->session);
            broadcast(new CategoryChanged($nextCategory, $this->session->fresh()));
        } else {
            // All categories done
            $this->completeDraft();
        }
    }

    // ── Complete draft ────────────────────────────────────────
    private function completeDraft(): void
    {
        $this->session->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
        AuditLog::record('draft_completed', 'Draft session completed: ' . $this->session->title, $this->session);
        broadcast(new DraftCompleted($this->session->fresh()));
    }

    // ── Pause / Resume ────────────────────────────────────────
    public function pauseDraft(): void
    {
        $this->session->update(['status' => 'paused']);
        AuditLog::record('draft_paused', 'Draft paused by admin', $this->session);
    }

    public function resumeDraft(): void
    {
        $activeQueue = $this->session->currentQueueEntry();
        if ($activeQueue) {
            $activeQueue->update(['timer_expires_at' => now()->addSeconds($this->session->timer_seconds)]);
        }
        $this->session->update(['status' => 'active', 'timer_started_at' => now()]);
        AuditLog::record('draft_resumed', 'Draft resumed by admin', $this->session);
    }

    // ── Get live state (for broadcast/polling) ────────────────
    public function getLiveState(): array
    {
        $this->session->refresh();
        $activeRound = $this->session->activeRound();
        $currentQueue = $this->session->currentQueueEntry();

        $availablePlayers = Player::where('status', 'approved')
            ->where('category_id', $this->session->current_category_id)
            ->with('category')
            ->get();

        $recentPicks = DraftPick::where('draft_session_id', $this->session->id)
            ->with(['player', 'team', 'category'])
            ->latest('picked_at')
            ->take(10)
            ->get();

        return [
            'session'           => $this->session,
            'current_category'  => $this->session->currentCategory,
            'current_team'      => $currentQueue?->team,
            'timer_remaining'   => $currentQueue?->seconds_remaining ?? 0,
            'active_round'      => $activeRound,
            'available_players' => $availablePlayers,
            'recent_picks'      => $recentPicks,
            'team_counts'       => Team::approved()->withCount('players')->get(),
        ];
    }
}
