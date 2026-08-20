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

        // Inject PEM into config before ResourceServer/CryptKey resolve. File writes may fail
        // on country hubs (storage owned by root); config PEM still lets Passport work.
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
