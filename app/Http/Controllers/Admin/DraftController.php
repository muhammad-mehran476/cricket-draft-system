<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DraftPick;
use App\Models\DraftSession;
use App\Models\Player;
use App\Models\Team;
use App\Services\DraftEngine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DraftController extends Controller
{
    // ── Admin draft control panel ─────────────────────────────
    public function index()
    {
        $session    = DraftSession::with(['currentCategory', 'currentTeam', 'picks.player', 'picks.team'])->latest()->first();
        $categories = Category::active()->ordered()->withCount(['players as available' => fn($q) => $q->where('status', 'approved')])->get();
        $teams      = Team::approved()->withCount('players')->get();

        return view('admin.draft.index', compact('session', 'categories', 'teams'));
    }

    // ── Create a new draft session ────────────────────────────
    public function create()
    {
        $approvedTeams   = Team::approved()->count();
        $approvedPlayers = Player::approved()->count();
        $categories      = Category::active()->ordered()->get();

        return view('admin.draft.create', compact('approvedTeams', 'approvedPlayers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:191', 'timer_seconds' => 'required|integer|min:30|max:600']);

        $session = DraftSession::create([
            'title'         => $request->title,
            'timer_seconds' => $request->timer_seconds,
            'status'        => 'pending',
            'created_by'    => auth()->id(),
        ]);

        AuditLog::record('draft_created', "Draft session created: {$session->title}", $session);
        return redirect()->route('admin.draft.show', $session)->with('success', 'Draft session created.');
    }

    public function show(DraftSession $draft)
    {
        $draft->load(['currentCategory', 'currentTeam', 'rounds.category', 'picks' => fn($q) => $q->latest('picked_at')->with(['player', 'team', 'category'])->take(20)]);

        $engine        = new DraftEngine($draft);
        $liveState     = $engine->getLiveState();
        $teams         = Team::approved()->withCount('players')->get();
        $allCategories = Category::active()->ordered()->get();

        return view('admin.draft.show', compact('draft', 'liveState', 'teams', 'allCategories'));
    }

    // ── Start draft ───────────────────────────────────────────
    public function start(DraftSession $draft)
    {
        if ($draft->status !== 'pending') {
            return back()->with('error', 'Draft cannot be started in its current state.');
        }

        if (Team::approved()->count() < 2) {
            return back()->with('error', 'At least 2 approved teams are required.');
        }

        $engine = new DraftEngine($draft);
        $engine->startDraft();

        return redirect()->route('admin.draft.show', $draft)->with('success', 'Draft started!');
    }

    // ── Pause draft ───────────────────────────────────────────
    public function pause(DraftSession $draft)
    {
        $engine = new DraftEngine($draft);
        $engine->pauseDraft();
        return back()->with('success', 'Draft paused.');
    }

    // ── Resume draft ──────────────────────────────────────────
    public function resume(DraftSession $draft)
    {
        $engine = new DraftEngine($draft);
        $engine->resumeDraft();
        return back()->with('success', 'Draft resumed.');
    }

    // ── Ajax: get live state ──────────────────────────────────
    public function liveState(DraftSession $draft): JsonResponse
    {
        $engine = new DraftEngine($draft);
        return response()->json($engine->getLiveState());
    }

    // ── Ajax: admin force pick ────────────────────────────────
    public function forcePick(Request $request, DraftSession $draft): JsonResponse
    {
        $request->validate([
            'team_id'   => 'required|exists:teams,id',
            'player_id' => 'required|exists:players,id',
        ]);

        $engine = new DraftEngine($draft);
        $result = $engine->pickPlayer(
            Team::findOrFail($request->team_id),
            Player::findOrFail($request->player_id)
        );

        return response()->json($result);
    }

    // ── Ajax: skip current team turn ──────────────────────────
    public function skipTurn(DraftSession $draft): JsonResponse
    {
        $engine = new DraftEngine($draft);
        $engine->handleTimerExpiry();
        return response()->json(['success' => true, 'message' => 'Turn skipped.']);
    }

    // ── Draft analytics ───────────────────────────────────────
    public function analytics(DraftSession $draft)
    {
        $picks = DraftPick::where('draft_session_id', $draft->id)
            ->with(['player', 'team', 'category'])
            ->get();

        $picksPerTeam     = $picks->groupBy('team.team_name')->map->count();
        $picksPerCategory = $picks->groupBy('category.name')->map->count();
        $avgPickTime      = $picks->avg('time_taken_seconds');

        return view('admin.draft.analytics', compact('draft', 'picks', 'picksPerTeam', 'picksPerCategory', 'avgPickTime'));
    }
}
