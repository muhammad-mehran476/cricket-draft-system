<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('team_name', 'like', "%{$request->search}%"))
            ->withCount('players')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.teams.index', compact('teams'));
    }

    public function show(Team $team)
    {
        $team->load(['user', 'players.category', 'matches', 'draftPicks.player']);
        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'team_name'    => 'required|string|max:255',
            'captain_name' => 'required|string|max:255',
            'status'       => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:500',
            'logo'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('teams', 'public');
            $validated['logo_url'] = \Storage::url($path);
        }

        if ($validated['status'] !== 'rejected') {
            $validated['rejection_reason'] = null;
        }

        $old = $team->toArray();
        $team->update($validated);
        AuditLog::record('team_updated', "Team updated: {$team->team_name}", $team, $old, $team->fresh()->toArray());

        return redirect()->route('admin.teams.show', $team)->with('success', "Team {$team->team_name} has been updated.");
    }

    public function approve(Team $team)
    {
        $old = $team->toArray();
        $team->update(['status' => 'approved']);
        AuditLog::record('team_approved', "Team approved: {$team->team_name}", $team, $old, $team->fresh()->toArray());
        return back()->with('success', "Team {$team->team_name} has been approved.");
    }

    public function reject(Request $request, Team $team)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $old = $team->toArray();
        $team->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);
        AuditLog::record('team_rejected', "Team rejected: {$team->team_name}", $team, $old, $team->fresh()->toArray());
        return back()->with('success', "Team {$team->team_name} has been rejected.");
    }

    public function destroy(Team $team)
    {
        $name = $team->team_name;
        $team->delete();
        AuditLog::record('team_deleted', "Team deleted: {$name}");
        return redirect()->route('admin.teams.index')->with('success', "Team {$name} deleted.");
    }

}