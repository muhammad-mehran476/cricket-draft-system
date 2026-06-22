<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DraftSession;
use App\Models\Player;
use App\Models\Team;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_players'  => Player::approved()->count(),
            'total_teams'    => Team::approved()->count(),
            'categories'     => Category::active()->count(),
        ];

        $activeDraft = DraftSession::where('status', 'active')->latest()->first();
        $teams       = Team::approved()->take(8)->get();

        return view('public.landing', compact('stats', 'activeDraft', 'teams'));
    }

    public function rules()
    {
        return view('public.rules');
    }

    public function leaderboard()
    {
        $topBatsmen = Player::drafted()
            ->whereHas('stats')
            ->with(['stats', 'team'])
            ->get()
            ->sortByDesc(fn($p) => $p->stats->sum('runs_scored'))
            ->take(10);

        $topBowlers = Player::drafted()
            ->whereHas('stats')
            ->with(['stats', 'team'])
            ->get()
            ->sortByDesc(fn($p) => $p->stats->sum('wickets_taken'))
            ->take(10);

        $teams = Team::approved()->withCount('players')->get();

        return view('public.leaderboard', compact('topBatsmen', 'topBowlers', 'teams'));
    }
}
