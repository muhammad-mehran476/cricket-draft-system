<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\DraftPick;
use App\Models\DraftSession;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_players'    => Player::count(),
            'pending_players'  => Player::pending()->count(),
            'approved_players' => Player::approved()->count(),
            'drafted_players'  => Player::drafted()->count(),
            'total_teams'      => Team::count(),
            'pending_teams'    => Team::pending()->count(),
            'approved_teams'   => Team::approved()->count(),
            'total_users'      => User::count(),
        ];

        $draft = DraftSession::latest()->first();

        $categoryStats = Category::withCount([
            'players as total'    => fn($q) => $q->whereIn('status', ['approved', 'drafted']),
            'players as drafted'  => fn($q) => $q->where('status', 'drafted'),
            'players as available'=> fn($q) => $q->where('status', 'approved'),
        ])->orderBy('draft_order')->get();

        $recentActivity = AuditLog::with('user')
            ->latest()
            ->take(15)
            ->get();

        $teamDraftProgress = Team::approved()
            ->withCount('players')
            ->get()
            ->map(fn($t) => [
                'name'    => $t->team_name,
                'count'   => $t->players_count,
                'percent' => round(($t->players_count / 17) * 100),
            ]);

        // Chart data
        $playerRoleChart = Player::approved()
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        $playerSkillChart = Player::approved()
            ->selectRaw('skill_level, COUNT(*) as count')
            ->groupBy('skill_level')
            ->pluck('count', 'skill_level');

        return view('admin.dashboard', compact(
            'stats', 'draft', 'categoryStats', 'recentActivity',
            'teamDraftProgress', 'playerRoleChart', 'playerSkillChart'
        ));
    }

    public function auditLogs(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->latest()
            ->paginate(30);

        $actions = AuditLog::distinct()->pluck('action');

        return view('admin.audit-logs', compact('logs', 'actions'));
    }
}
