<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::with(['user', 'category', 'team'])
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->role,     fn($q) => $q->where('role', $request->role))
            ->when($request->search,   fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::active()->ordered()->get();

        return view('admin.players.index', compact('players', 'categories'));
    }

    public function show(Player $player)
    {
        $player->load(['user', 'category', 'team', 'stats', 'draftPick.draftSession']);
        $categories = Category::active()->ordered()->get();
        return view('admin.players.show', compact('player', 'categories'));
    }

    public function edit(Player $player)
    {
        $categories = Category::active()->ordered()->get();
        $teams = Team::orderBy('team_name')->get();
        return view('admin.players.edit', compact('player', 'categories', 'teams'));
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'role'          => 'required|string|max:50',
            'category_id'   => 'nullable|exists:categories,id',
            'team_id'       => 'nullable|exists:teams,id',
            // 'skill_level'   => 'required|string|max:50',
            'status'        => 'required|in:pending,approved,rejected,drafted',
            'rejection_reason' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('players', 'public');
            $validated['profile_picture_url'] = \Storage::url($path);
        }

        if ($validated['status'] !== 'rejected') {
            $validated['rejection_reason'] = null;
        }

        $old = $player->toArray();
        $player->update($validated);
        AuditLog::record('player_updated', "Player updated: {$player->name}", $player, $old, $player->fresh()->toArray());

        return redirect()->route('admin.players.show', $player)->with('success', "Player {$player->name} has been updated.");
    }

    public function approve(Player $player)
    {
        $old = $player->toArray();
        $player->update(['status' => 'approved', 'rejection_reason' => null]);
        AuditLog::record('player_approved', "Player approved: {$player->name}", $player, $old, $player->fresh()->toArray());

        return back()->with('success', "Player {$player->name} has been approved.");
    }

    public function reject(Request $request, Player $player)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $old = $player->toArray();
        $player->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);
        AuditLog::record('player_rejected', "Player rejected: {$player->name}", $player, $old, $player->fresh()->toArray());

        return back()->with('success', "Player {$player->name} has been rejected.");
    }

    public function assignCategory(Request $request, Player $player)
    {
        $request->validate(['category_id' => 'required|exists:categories,id']);
        $old = $player->toArray();
        $player->update(['category_id' => $request->category_id]);
        AuditLog::record('category_assigned', "Category assigned to player: {$player->name}", $player, $old, $player->fresh()->toArray());

        return back()->with('success', 'Category assigned successfully.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:players,id']);
        Player::whereIn('id', $request->ids)->where('status', 'pending')->update(['status' => 'approved']);
        AuditLog::record('bulk_approve_players', 'Bulk approved ' . count($request->ids) . ' players');

        return back()->with('success', count($request->ids) . ' players approved.');
    }

    public function destroy(Player $player)
    {
        $name = $player->name;
        $player->delete();
        AuditLog::record('player_deleted', "Player deleted: {$name}");
        return redirect()->route('admin.players.index')->with('success', "Player {$name} deleted.");
    }
}