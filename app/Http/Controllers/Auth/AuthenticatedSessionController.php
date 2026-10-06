<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        ActivityLogger::log('login', 'auth', $user->id, 'Logged in');

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (! $user->isActiveAccount()) {
            return redirect()->route('account.pending');
        }

        if ($user->hasAdminAccess()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('member.dashboard'));
    }

    public function destroy(Request $request)
    {
        $userId = $request->user()?->id;
        if ($userId) {
            ActivityLogger::log('logout', 'auth', $userId, 'Logged out');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
