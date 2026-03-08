<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Contact;
use App\Models\Media;
use App\Models\Opportunity;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use App\Policies\ContactPolicy;
use App\Policies\MediaPolicy;
use App\Policies\OpportunityPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policies
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Opportunity::class, OpportunityPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Rate Limiters
        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower((string) $request->input('email'));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();
            return [Limit::perMinute(10)->by($key)];
        });

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('uploads', function (Request $request) {
            return $request->user()
                ? Limit::perHour(20)->by($request->user()->id)
                : Limit::perHour(5)->by($request->ip());
        });

        RateLimiter::for('reports', function (Request $request) {
            return Limit::perDay(10)->by($request->user()?->id ?? $request->ip());
        });

        RateLimiter::for('messages', function (Request $request) {
            return Limit::perHour(50)->by($request->user()?->id ?? $request->ip());
        });
    }
}
