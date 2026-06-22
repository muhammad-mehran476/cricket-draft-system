<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['player', 'team'])
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['player', 'team', 'auditLogs' => fn($q) => $q->latest()->take(20)]);
        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot suspend an admin account.');
        }
        $user->update(['status' => 'suspended']);
        AuditLog::record('user_suspended', "User suspended: {$user->name}", $user);
        return back()->with('success', "User '{$user->name}' has been suspended.");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        AuditLog::record('user_activated', "User activated: {$user->name}", $user);
        return back()->with('success', "User '{$user->name}' has been activated.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        AuditLog::record('password_reset', "Password reset for: {$user->name}", $user);
        return back()->with('success', "Password reset for '{$user->name}'.");
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete an admin account.');
        }
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->delete();
        AuditLog::record('user_deleted', "User deleted: {$name}");
        return redirect()->route('admin.users.index')->with('success', "User '{$name}' deleted.");
    }
}
