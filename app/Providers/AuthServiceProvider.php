<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Artisan;
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

        // Passport::routes() resolves CryptKey immediately; missing/invalid keys crash artisan
        // (e.g. route:list) on provisioned hubs where oauth_clients were copied but key files were not.
        $this->ensurePassportKeyFiles();

        Passport::routes();

        Passport::tokensExpireIn(now()->addHours(24));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }

    /**
     * Ensure oauth-*.key files exist and are readable PEM material.
     */
    protected function ensurePassportKeyFiles(): void
    {
        $private = storage_path('oauth-private.key');
        $public = storage_path('oauth-public.key');

        $privateOk = is_file($private) && is_readable($private) && filesize($private) > 0
            && $this->looksLikePem((string) @file_get_contents($private));
        $publicOk = is_file($public) && is_readable($public) && filesize($public) > 0
            && $this->looksLikePem((string) @file_get_contents($public));

        if ($privateOk && $publicOk) {
            @chmod($private, 0600);
            @chmod($public, 0600);

            return;
        }

        try {
            Artisan::call('passport:keys', ['--force' => true]);
            if (is_file($private)) {
                @chmod($private, 0600);
            }
            if (is_file($public)) {
                @chmod($public, 0600);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to generate Passport OAuth keys: '.$e->getMessage());
        }
    }

    protected function looksLikePem(string $contents): bool
    {
        return str_contains($contents, 'BEGIN') && str_contains($contents, 'KEY');
    }
}
