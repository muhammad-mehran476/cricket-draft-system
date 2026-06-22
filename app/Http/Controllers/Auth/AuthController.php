<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) return $this->redirectByRole();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        if (auth()->user()->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is not active. Contact admin.']);
        }

        AuditLog::record('user_login', auth()->user()->name . ' logged in');
        return $this->redirectByRole();
    }

    public function showRegister()
    {
        if (auth()->check()) return $this->redirectByRole();
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:player,team_captain',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => 'active',
        ]);

        Auth::login($user);
        AuditLog::record('user_registered', "New {$user->role} account: {$user->name}", $user);

        return $user->role === 'player'
            ? redirect()->route('player.register')
            : redirect()->route('team.register');
    }

    public function logout(Request $request)
    {
        AuditLog::record('user_logout', auth()->user()->name . ' logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function redirectByRole()
    {
        $user = auth()->user();
        return match($user->role) {
            'admin'         => redirect()->route('admin.dashboard'),
            'team_captain'  => redirect()->route('team.dashboard'),
            default         => redirect()->route('player.dashboard'),
        };
    }
}
