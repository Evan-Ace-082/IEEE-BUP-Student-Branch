<?php

namespace App\Providers;

use App\Models\LeadershipProfile;
use App\Models\MemberProfile;
use App\Models\ResearchPaper;
use App\Models\User;
use App\Policies\LeadershipProfilePolicy;
use App\Policies\MemberProfilePolicy;
use App\Policies\ResearchPaperPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(MemberProfile::class, MemberProfilePolicy::class);
        Gate::policy(LeadershipProfile::class, LeadershipProfilePolicy::class);
        Gate::policy(ResearchPaper::class, ResearchPaperPolicy::class);

        RateLimiter::for('browse', function (Request $request) {
            return Limit::perMinute(12000)->by($request->ip());
        });

        RateLimiter::for('login-post', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('account-register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('event-register', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('event-email:'.$email),
                Limit::perMinute(1500)->by('event-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('contact-form', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        RateLimiter::for('membership-form', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(3)->by('join-email:'.$email),
                Limit::perMinute(60)->by('join-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('password-email', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('reset-email:'.$email),
                Limit::perMinute(30)->by('reset-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(20)->by('upload:'.($request->user()?->id ?: $request->ip()));
        });
    }
}
