<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:160'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $emailKey = 'login:'.Str::lower((string) $this->input('email'));
        $ipKey = 'login-ip:'.$this->ip();

        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            event(new Lockout($this));
            $seconds = RateLimiter::availableIn($emailKey);

            throw ValidationException::withMessages([
                'email' => "Too many attempts for this account. Try again in {$seconds} seconds.",
            ]);
        }

        if (RateLimiter::tooManyAttempts($ipKey, 60)) {
            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts from this network. Please wait a minute and try again.',
            ]);
        }

        $user = User::query()->where('email', $this->input('email'))->first();
        if ($user && in_array($user->status, ['suspended', 'inactive'], true)) {
            throw ValidationException::withMessages([
                'email' => 'This account cannot sign in.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($emailKey, 600);
            RateLimiter::hit($ipKey, 60);
            ActivityLogger::log('failed_login', 'auth', null, 'Failed login attempt');

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($emailKey);
    }
}
