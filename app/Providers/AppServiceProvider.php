<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Contact;
use App\Models\Media;
use App\Policies\ApplicationPolicy;
use App\Policies\ContactPolicy;
use App\Policies\MediaPolicy;
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
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);

        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower((string) $request->input('email'));
            $key = $email !== '' ? $email.'|'.$request->ip() : $request->ip();

            return [Limit::perMinute(10)->by($key)];
        });
    }
}
