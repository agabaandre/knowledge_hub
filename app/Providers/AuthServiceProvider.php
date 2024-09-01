<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::routes();

        // Set token expiry to 1 hour
        Passport::tokensExpireIn(now()->addHours(24));

        // Set refresh token expiry to 30 days
        Passport::refreshTokensExpireIn(now()->addDays(30));

        // Set personal access token expiry to 6 months
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
