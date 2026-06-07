<?php

namespace App\Console\Commands;

use App\Services\FederationHubAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class RefreshFederationCentralTokenCommand extends Command
{
    protected $signature = 'federation:refresh-central-token';

    protected $description = 'Refresh the OAuth access token for the central (parent) Knowledge Hub connection';

    public function handle(FederationHubAuthService $auth): int
    {
        if (! function_exists('hub_admin_units_enabled') || ! hub_admin_units_enabled()) {
            $this->info('This hub is not in country mode; no central hub token to refresh.');

            return self::SUCCESS;
        }

        if (! Schema::hasColumn('setting', 'central_hub_refresh_token')) {
            $this->warn('Run migrations to enable federation token refresh.');

            return self::FAILURE;
        }

        try {
            $tokens = $auth->refreshStoredCentralToken();
        } catch (\Throwable $e) {
            $this->error('Token refresh failed: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($tokens === null) {
            $this->info('No central hub refresh token is stored. Connect using a registration token first.');

            return self::SUCCESS;
        }

        $this->info('Central hub access token refreshed (expires in '.$tokens['expires_in'].'s).');

        return self::SUCCESS;
    }
}
