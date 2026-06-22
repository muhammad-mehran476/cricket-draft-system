<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function showForm()
    {
        $existing = auth()->user()->player;
        if ($existing) return redirect()->route('player.dashboard');
        return view('player.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:191',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string',
            'city'            => 'required|string|max:100',
            'role'            => 'required|in:batsman,bowler,all_rounder,wicket_keeper',
            'skill_level'     => 'required|in:good,better,best',
            'bowling_type'    => 'required|in:fast,medium,spin,none',
            'batting_style'   => 'required|in:right_hand,left_hand',
            'profile_picture' => 'required|image|max:2048',
            'payment_slip'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'rules_accepted'  => 'accepted',
        ]);

        $photoPath   = $request->file('profile_picture')->store('players/photos', 'public');
        $slipPath    = $request->file('payment_slip')->store('players/slips', 'public');

        $player = Player::create([
            'user_id'         => auth()->id(),
            'name'            => $request->name,
            'email'           => auth()->user()->email,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'city'            => $request->city,
            'role'            => $request->role,
            'skill_level'     => $request->skill_level,
            'bowling_type'    => $request->bowling_type,
            'batting_style'   => $request->batting_style,
            'profile_picture' => $photoPath,
            'payment_slip'    => $slipPath,
            'status'          => 'pending',
            'rules_accepted'  => true,
        ]);

        AuditLog::record('player_registered', "Player registered: {$player->name}", $player);
        return redirect()->route('player.dashboard')->with('success', 'Registration submitted! Awaiting admin approval.');
    }

    public function dashboard()
    {
        $player = auth()->user()->player;
        if (!$player) return redirect()->route('player.register');
        $player->load(['category', 'team', 'stats', 'draftPick']);
        return view('player.dashboard', compact('player'));
    }

    public function editProfile()
    {
        $player = auth()->user()->player;
        if (!$player) return redirect()->route('player.register');
        return view('player.edit-profile', compact('player'));
    }

    public function updateProfile(Request $request)
    {
        $player = auth()->user()->player;

        $request->validate([
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string',
            'city'            => 'required|string|max:100',
            'bowling_type'    => 'required|in:fast,medium,spin,none',
            'batting_style'   => 'required|in:right_hand,left_hand',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('phone', 'address', 'city', 'bowling_type', 'batting_style');

        if ($request->hasFile('profile_picture')) {
            if ($player->profile_picture) Storage::disk('public')->delete($player->profile_picture);
            $data['profile_picture'] = $request->file('profile_picture')->store('players/photos', 'public');
        }

        $player->update($data);
        AuditLog::record('player_profile_updated', "Player updated profile: {$player->name}", $player);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function stats()
    {
        $player = auth()->user()->player;
        $stats  = $player->stats()->with('match')->latest()->paginate(15);
        return view('player.stats', compact('player', 'stats'));
    }
}
