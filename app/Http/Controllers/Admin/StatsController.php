<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CricketMatch as MatchModel;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Team;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function matchIndex()
    {
        $matches = MatchModel::with(['homeTeam', 'awayTeam', 'createdBy'])
            ->latest('match_date')
            ->paginate(20);

        return view('admin.stats.matches', compact('matches'));
    }

    public function matchShow(MatchModel $match)
    {
        $match->load(['homeTeam', 'awayTeam', 'playerStats.player']);
        $availablePlayers = collect();

        if ($match->homeTeam) {
            $availablePlayers = $match->homeTeam->players;
        }

        return view('admin.stats.match-show', compact('match', 'availablePlayers'));
    }

    public function storePlayerStat(Request $request, MatchModel $match)
    {
        $request->validate([
            'player_id'      => 'required|exists:players,id',
            'runs_scored'    => 'required|integer|min:0',
            'balls_faced'    => 'required|integer|min:0',
            'fours'          => 'required|integer|min:0',
            'sixes'          => 'required|integer|min:0',
            'wickets_taken'  => 'required|integer|min:0',
            'overs_bowled'   => 'required|numeric|min:0',
            'runs_conceded'  => 'required|integer|min:0',
            'catches'        => 'required|integer|min:0',
            'is_not_out'     => 'boolean',
        ]);

        PlayerStat::updateOrCreate(
            ['player_id' => $request->player_id, 'match_id' => $match->id],
            array_merge($request->except('_token'), ['match_id' => $match->id])
        );

        AuditLog::record('stat_recorded', "Stats recorded for player in match #{$match->id}");
        return back()->with('success', 'Player stats saved.');
    }

    public function leaderboard()
    {
        $topBatsmen = Player::drafted()
            ->with(['stats', 'team'])
            ->get()
            ->map(function ($p) {
                return [
                    'name'    => $p->name,
                    'team'    => $p->team?->team_name,
                    'runs'    => $p->stats->sum('runs_scored'),
                    'avg'     => $p->batting_average,
                    'sixes'   => $p->stats->sum('sixes'),
                    'fours'   => $p->stats->sum('fours'),
                ];
            })
            ->sortByDesc('runs')
            ->take(20)
            ->values();

        $topBowlers = Player::drafted()
            ->with(['stats', 'team'])
            ->get()
            ->map(function ($p) {
                return [
                    'name'    => $p->name,
                    'team'    => $p->team?->team_name,
                    'wickets' => $p->stats->sum('wickets_taken'),
                    'avg'     => $p->bowling_average,
                    'econ'    => $p->stats->avg('overs_bowled') > 0
                        ? round($p->stats->sum('runs_conceded') / $p->stats->sum('overs_bowled'), 2)
                        : 0,
                ];
            })
            ->sortByDesc('wickets')
            ->take(20)
            ->values();

        return view('admin.stats.leaderboard', compact('topBatsmen', 'topBowlers'));
    }
}
