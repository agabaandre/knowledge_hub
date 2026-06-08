<?php

namespace App\Console\Commands;

use App\Support\SsoConfig;
use Illuminate\Console\Command;

class SyncSsoFromEnvCommand extends Command
{
    protected $signature = 'sso:sync-from-env
        {--env-file= : Path to .env file (default: project .env)}';

    protected $description = 'Clear database SSO overrides so .env values take effect again';

    public function handle(): int
    {
        $envFile = $this->option('env-file') ?: base_path('.env');

        if (! is_readable($envFile)) {
            $this->error('Env file not readable: '.$envFile);

            return self::FAILURE;
        }

        $result = SsoConfig::syncCredentialsFromEnvToDatabase($envFile);

        if ($result['updated'] === 0) {
            $this->warn('No setting rows were updated.');
        } else {
            $this->info('Updated '.$result['updated'].' setting row(s).');
        }

        foreach ($result['providers'] as $provider => $configured) {
            $redirect = $result['redirects'][$provider] ?? '';
            $status = $configured ? '<info>configured</info>' : '<error>NOT configured</error>';
            $this->line(sprintf('  %s: %s', ucfirst($provider), strip_tags($status)));
            if ($redirect !== '') {
                $this->line('    redirect: '.$redirect);
            }
        }

        if (in_array(false, $result['providers'], true)) {
            $this->newLine();
            $this->warn('Some providers are still missing credentials. Ensure GOOGLE_* / LINKEDIN_* / MICROSOFT_* keys exist in .env, then run this command again.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('SSO env-first reset complete. Run: php artisan config:cache');

        return self::SUCCESS;
    }
}
