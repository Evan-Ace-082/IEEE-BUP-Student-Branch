<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $roleId = Role::query()->where('slug', 'member')->value('id');

        $user = new User;
        $user->name = $request->string('name')->toString();
        $user->email = mb_strtolower($request->string('email')->toString());
        $user->password = $request->string('password')->toString();
        $user->role_id = $roleId;
        $user->status = 'pending';
        $user->save();

        $user->profile()->create([
            'ieee_membership_status' => 'none',
            'directory_visible' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log('create', 'auth', $user->id, 'Member registered');

        return redirect()->route('verification.notice');
    }
}
