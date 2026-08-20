<?php

namespace App\Services\FederationProvision;

use Illuminate\Support\Str;

class ProvisionEnvWriter
{
    public function __construct(private readonly ProvisionFilesystem $filesystem)
    {
    }

    /**
     * @param  array{database: string, username: string, password: string, host: string, port: int}  $db
     * @param  array<string, string>  $extra
     */
    public function writeCountryEnv(string $targetPath, string $slug, int $countryId, array $db, array $extra = []): void
    {
        $baseUrl = rtrim((string) config('federation_provision.public_base_url'), '/').'/'.$slug;
        $continentalUrl = rtrim((string) config('app.url'), '/');
        // Prefer public base without path for central when continental is at root.
        $centralUrl = rtrim((string) config('federation_provision.public_base_url'), '/') ?: $continentalUrl;

        $federationToken = '';
        if (function_exists('settings') && settings()) {
            $federationToken = (string) (settings()->federation_api_token ?? '');
        }
        if ($federationToken === '') {
            $federationToken = (string) env('FEDERATION_API_TOKEN', '');
        }

        $appKey = 'base64:'.base64_encode(random_bytes(32));

        $values = array_merge([
            'APP_NAME' => $extra['APP_NAME'] ?? (ucfirst($slug).' Knowledge Hub'),
            'APP_ENV' => 'production',
            'APP_KEY' => $appKey,
            'APP_DEBUG' => 'false',
            'APP_URL' => $baseUrl,
            'ASSET_URL' => $baseUrl,
            'APP_INSTALLED' => 'false',
            'INSTALLER_DISABLED' => 'false',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $db['host'],
            'DB_PORT' => (string) $db['port'],
            'DB_DATABASE' => $db['database'],
            'DB_USERNAME' => $db['username'],
            'DB_PASSWORD' => $db['password'],
            'STATES_ENABLED' => 'false',
            'ADMIN_UNITS_ENABLED' => 'true',
            'HUB_OWNER_COUNTRY_ID' => (string) $countryId,
            'HUB_SITE_ID' => $slug,
            'CENTRAL_HUB_URL' => $centralUrl,
            'CENTRAL_HUB_API_TOKEN' => $federationToken,
            'FEDERATION_API_TOKEN' => Str::random(40),
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION', 'database'),
            'CACHE_DRIVER' => env('CACHE_DRIVER', 'file'),
            'SESSION_DRIVER' => env('SESSION_DRIVER', 'file'),
            'MAIL_MAILER' => 'log',
            'LOG_CHANNEL' => 'stack',
            'LOG_LEVEL' => 'error',
        ], $extra);

        $sourceEnv = $this->filesystem->sourceRoot().'/.env';
        $lines = [];
        if (is_readable($sourceEnv)) {
            $lines = file($sourceEnv, FILE_IGNORE_NEW_LINES) ?: [];
        } else {
            $example = $this->filesystem->sourceRoot().'/.env.docker.example';
            if (is_readable($example)) {
                $lines = file($example, FILE_IGNORE_NEW_LINES) ?: [];
            }
        }

        $map = $this->parseEnvLines($lines);
        foreach ($values as $key => $value) {
            $map[$key] = (string) $value;
        }

        // Never inherit continental DB / install / provision secrets.
        unset(
            $map['FEDERATION_PROVISION_SUDO_PASSWORD'],
            $map['FEDERATION_PROVISION_MYSQL_ADMIN_PASSWORD'],
            $map['FEDERATION_PROVISION_ENABLED']
        );

        $contents = '';
        foreach ($map as $key => $value) {
            $contents .= $key.'='.$this->escape($value).PHP_EOL;
        }

        $envPath = rtrim($targetPath, '/').'/.env';
        $this->filesystem->writeFileAsRoot($envPath, $contents);
    }

    /**
     * @param  list<string>  $lines
     * @return array<string, string>
     */
    protected function parseEnvLines(array $lines): array
    {
        $map = [];
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '' || str_starts_with($trim, '#')) {
                continue;
            }
            if (! str_contains($trim, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $trim, 2);
            $key = trim($key);
            if ($key === '') {
                continue;
            }
            $map[$key] = $value;
        }

        return $map;
    }

    protected function escape(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'|\\\\/', $value)) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
        }

        return $value;
    }
}
