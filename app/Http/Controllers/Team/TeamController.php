<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CricketMatch as MatchModel;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function showRegisterForm()
    {
        $existing = auth()->user()->team;
        if ($existing) return redirect()->route('team.dashboard');
        return view('team.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name'     => 'required|string|max:191|unique:teams',
            'captain_name'  => 'required|string|max:191',
            'phone'         => 'required|string|max:20',
            'address'       => 'required|string',
            'team_logo'     => 'required|image|max:2048',
            'captain_image' => 'required|image|max:2048',
            'payment_slip'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $logoPath    = $request->file('team_logo')->store('teams/logos', 'public');
        $captainPath = $request->file('captain_image')->store('teams/captains', 'public');
        $slipPath    = $request->file('payment_slip')->store('teams/slips', 'public');

        $team = Team::create([
            'user_id'       => auth()->id(),
            'team_name'     => $request->team_name,
            'captain_name'  => $request->captain_name,
            'email'         => auth()->user()->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'team_logo'     => $logoPath,
            'captain_image' => $captainPath,
            'payment_slip'  => $slipPath,
            'status'        => 'pending',
        ]);

        AuditLog::record('team_registered', "Team registered: {$team->team_name}", $team);
        return redirect()->route('team.dashboard')->with('success', 'Team registered! Awaiting admin approval.');
    }

    public function dashboard()
    {
        $team = auth()->user()->team;
        if (!$team) return redirect()->route('team.register');

        $team->load(['players.category', 'players.stats', 'matches', 'draftPicks.player.category']);

        $categoryBreakdown = $team->players->groupBy('category.name');
        $matchRecord       = $team->getMatchRecord();
        $draftSession      = \App\Models\DraftSession::where('status', 'active')->latest()->first();

        return view('team.dashboard', compact('team', 'categoryBreakdown', 'matchRecord', 'draftSession'));
    }

    public function updateProfile(Request $request)
    {
        $team = auth()->user()->team;
        $request->validate([
            'captain_name' => 'required|string|max:191',
            'phone'        => 'required|string|max:20',
            'address'      => 'required|string',
            'team_banner'  => 'nullable|image|max:3072',
        ]);

        $data = $request->only('captain_name', 'phone', 'address');

        if ($request->hasFile('team_banner')) {
            if ($team->team_banner) Storage::disk('public')->delete($team->team_banner);
            $data['team_banner'] = $request->file('team_banner')->store('teams/banners', 'public');
        }

        $team->update($data);
        return back()->with('success', 'Team profile updated.');
    }

    // ── Draft live room ───────────────────────────────────────
    public function draftRoom()
    {
        $team    = auth()->user()->team;
        if (!$team || !$team->isApproved()) abort(403, 'Team not approved for draft.');

        $session = \App\Models\DraftSession::where('status', 'active')->latest()->first();
        if (!$session) return view('team.draft-waiting', compact('team'));

        $session->load(['currentCategory', 'currentTeam']);
        $myPicks = \App\Models\DraftPick::where('draft_session_id', $session->id)
            ->where('team_id', $team->id)
            ->with(['player.category'])
            ->get();

        $availablePlayers = Player::where('status', 'approved')
            ->where('category_id', $session->current_category_id)
            ->with('category')
            ->get();

        $isMyTurn = $session->current_team_turn_id === $team->id;

        return view('team.draft-room', compact('team', 'session', 'myPicks', 'availablePlayers', 'isMyTurn'));
    }

    // ── Pick a player (Ajax) ──────────────────────────────────
    public function pickPlayer(Request $request)
    {
        $team = auth()->user()->team;
        $request->validate(['player_id' => 'required|exists:players,id', 'draft_session_id' => 'required|exists:draft_sessions,id']);

        $session = \App\Models\DraftSession::findOrFail($request->draft_session_id);
        if ($session->current_team_turn_id !== $team->id) {
            return response()->json(['success' => false, 'message' => 'It is not your turn.'], 422);
        }

        $engine = new \App\Services\DraftEngine($session);
        $result = $engine->pickPlayer($team, Player::findOrFail($request->player_id));

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    // ── Match management ──────────────────────────────────────
    public function matches()
    {
        $team    = auth()->user()->team;
        $matches = MatchModel::where('home_team_id', $team->id)
            ->orWhere('away_team_id', $team->id)
            ->latest('match_date')
            ->paginate(15);
        return view('team.matches', compact('team', 'matches'));
    }

    public function createMatch()
    {
        $team = auth()->user()->team;
        return view('team.create-match', compact('team'));
    }

    public function storeMatch(Request $request)
    {
        $team = auth()->user()->team;
        $request->validate([
            'opponent_name' => 'required|string|max:191',
            'match_date'    => 'required|date',
            'venue'         => 'nullable|string|max:191',
            'match_type'    => 'required|in:league,knockout,friendly,final',
            'home_runs'     => 'required|integer|min:0',
            'home_wickets'  => 'required|integer|min:0|max:10',
            'home_overs'    => 'required|numeric|min:0',
            'away_runs'     => 'required|integer|min:0',
            'away_wickets'  => 'required|integer|min:0|max:10',
            'away_overs'    => 'required|numeric|min:0',
            'result'        => 'required|in:win,loss,draw,no_result',
            'notes'         => 'nullable|string',
        ]);

        MatchModel::create(array_merge($request->all(), [
            'home_team_id' => $team->id,
            'created_by'   => auth()->id(),
        ]));

        return redirect()->route('team.matches')->with('success', 'Match result added.');
    }

    public function players()
    {
        $team = auth()->user()->team;
        $team->load(['players' => fn($q) => $q->with(['category', 'stats'])]);
        return view('team.players', compact('team'));
    }
}
