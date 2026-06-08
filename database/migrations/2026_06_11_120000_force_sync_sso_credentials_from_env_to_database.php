<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $columnEnvMap = [
        'microsoft_client_id' => 'MICROSOFT_CLIENT_ID',
        'microsoft_client_secret' => 'MICROSOFT_CLIENT_SECRET',
        'microsoft_redirect_uri' => 'MICROSOFT_REDIRECT_URI',
        'microsoft_tenant_id' => 'MICROSOFT_TENANT_ID',
        'google_client_id' => 'GOOGLE_CLIENT_ID',
        'google_client_secret' => 'GOOGLE_CLIENT_SECRET',
        'google_redirect_uri' => 'GOOGLE_REDIRECT_URI',
        'linkedin_client_id' => 'LINKEDIN_CLIENT_ID',
        'linkedin_client_secret' => 'LINKEDIN_CLIENT_SECRET',
        'linkedin_redirect_uri' => 'LINKEDIN_REDIRECT_URI',
    ];

    /** @var array<string, string> */
    private array $redirectProviders = [
        'microsoft_redirect_uri' => 'microsoft',
        'google_redirect_uri' => 'google',
        'linkedin_redirect_uri' => 'linkedin',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $env = $this->readEnvFile(base_path('.env'));
        $appUrl = rtrim((string) ($env['APP_URL'] ?? ''), '/');

        foreach (DB::table('setting')->get() as $row) {
            $updates = [];

            foreach ($this->columnEnvMap as $column => $envKey) {
                if (! Schema::hasColumn('setting', $column)) {
                    continue;
                }

                if (isset($this->redirectProviders[$column])) {
                    $provider = $this->redirectProviders[$column];
                    $resolved = $this->resolveRedirectUri($env, $envKey, $provider, $appUrl);
                    if ($resolved !== '') {
                        $updates[$column] = $resolved;
                    }

                    continue;
                }

                $value = trim((string) ($env[$envKey] ?? ''));
                if ($value !== '') {
                    $updates[$column] = $value;
                }
            }

            if (Schema::hasColumn('setting', 'microsoft_client_id')) {
                $updates = array_merge($updates, $this->microsoftFallbacks($env, $row, $updates));
            }

            foreach (['enable_microsoft_login', 'enable_google_login', 'enable_linkedin_login'] as $toggle) {
                if (Schema::hasColumn('setting', $toggle)) {
                    $updates[$toggle] = true;
                }
            }

            if (Schema::hasColumn('setting', 'sso_use_database_credentials')) {
                $updates['sso_use_database_credentials'] = true;
            }

            if ($updates !== []) {
                DB::table('setting')->where('id', $row->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        // No-op: synced credentials are not removed automatically.
    }

    /**
     * @param  array<string, string>  $env
     * @param  object  $row
     * @param  array<string, mixed>  $updates
     * @return array<string, mixed>
     */
    private function microsoftFallbacks(array $env, object $row, array $updates): array
    {
        $pairs = [
            'microsoft_client_id' => 'EXCHANGE_CLIENT_ID',
            'microsoft_client_secret' => 'EXCHANGE_CLIENT_SECRET',
            'microsoft_tenant_id' => 'EXCHANGE_TENANT_ID',
        ];

        foreach ($pairs as $column => $exchangeKey) {
            if (! Schema::hasColumn('setting', $column)) {
                continue;
            }

            if (! empty($updates[$column]) || trim((string) ($row->{$column} ?? '')) !== '') {
                continue;
            }

            $exchangeValue = trim((string) ($env[$exchangeKey] ?? ''));
            if ($exchangeValue !== '') {
                $updates[$column] = $exchangeValue;
            }
        }

        if (Schema::hasColumn('setting', 'microsoft_tenant_id')
            && empty($updates['microsoft_tenant_id'])
            && trim((string) ($row->microsoft_tenant_id ?? '')) === '') {
            $updates['microsoft_tenant_id'] = 'common';
        }

        return $updates;
    }

    /**
     * @param  array<string, string>  $env
     */
    private function resolveRedirectUri(array $env, string $envKey, string $provider, string $appUrl): string
    {
        $default = $appUrl !== '' ? $appUrl.'/auth/'.$provider.'/callback' : '';
        $fromEnv = trim((string) ($env[$envKey] ?? ''));

        if ($fromEnv === '') {
            return $default;
        }

        if ($appUrl !== '' && $this->isLocalHostUrl($fromEnv) && ! $this->isLocalHostUrl($appUrl)) {
            return $default;
        }

        return $fromEnv;
    }

    private function isLocalHostUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return false;
        }

        $host = strtolower($host);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.test');
    }

    /**
     * @return array<string, string>
     */
    private function readEnvFile(string $path): array
    {
        if (! is_readable($path)) {
            return [];
        }

        $values = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"'))
                || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }

            $values[$key] = $value;
        }

        return $values;
    }
};
