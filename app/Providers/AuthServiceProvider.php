<?php

namespace App\Providers;

use App\Support\PassportKeyGenerator;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
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

        // Passport::routes() resolves CryptKey immediately; missing keys crash every request
        // (including publication submit) on hubs where oauth_clients exist but key files do not.
        // Do not use Artisan::call('passport:keys') here — the command is often not registered yet.
        if (! PassportKeyGenerator::ensureKeysExist()) {
            Log::error('Passport OAuth keys are missing; skipping Passport::routes() to avoid crashing the app.');

            return;
        }

        Passport::routes();

        Passport::tokensExpireIn(now()->addHours(24));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
