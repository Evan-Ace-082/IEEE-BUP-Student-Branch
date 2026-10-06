<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PendingAccountController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isActiveAccount()) {
            return $user->hasAdminAccess()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.dashboard');
        }

        return view('auth.pending');
    }
}
