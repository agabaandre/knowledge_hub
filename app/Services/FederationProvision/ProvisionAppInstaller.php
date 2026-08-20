<?php

namespace App\Services\FederationProvision;

use RuntimeException;
use Symfony\Component\Process\Process;

class ProvisionAppInstaller
{
    /**
     * @param  array{email: string, password: string, first_name: string, last_name: string}  $admin
     */
    public function install(string $targetPath, string $siteName, array $admin, string $appUrl): void
    {
        $php = (string) config('federation_provision.php_binary', PHP_BINARY);
        $artisan = rtrim($targetPath, '/').'/artisan';

        if (! is_file($artisan)) {
            throw new RuntimeException('Artisan not found in target tree: '.$artisan);
        }

        $this->runArtisan($php, $targetPath, ['config:clear']);

        $this->runArtisan($php, $targetPath, [
            'khub:install',
            '--email='.$admin['email'],
            '--password='.$admin['password'],
            '--first-name='.$admin['first_name'],
            '--last-name='.$admin['last_name'],
            '--site-name='.$siteName,
            '--mail-driver=log',
        ], 3600);

        // Ensure APP_URL / ASSET_URL remain subdirectory-correct after finalize.
        $this->ensureAppUrl($php, $targetPath, $appUrl);
        $this->ensureRewriteBase($targetPath, $appUrl);
    }

    /**
     * Ensure Apache subdirectory aliases (e.g. /ghana) rewrite correctly.
     */
    public function ensureRewriteBase(string $targetPath, string $appUrl): void
    {
        $path = parse_url($appUrl, PHP_URL_PATH);
        $base = is_string($path) ? rtrim($path, '/') : '';
        if ($base === '' || $base === '/') {
            return;
        }

        $htaccess = rtrim($targetPath, '/').'/public/.htaccess';
        if (! is_file($htaccess) && ! is_readable($htaccess)) {
            // Still try via sudo write after reading with cat if needed.
        }

        $contents = is_readable($htaccess) ? (string) file_get_contents($htaccess) : '';
        if ($contents === '') {
            try {
                $contents = app(SudoRunner::class)->run(['cat', $htaccess]);
            } catch (\Throwable) {
                return;
            }
        }

        $line = 'RewriteBase '.$base.'/';
        if (str_contains($contents, 'RewriteBase ')) {
            $contents = preg_replace('/^RewriteBase\s+.*/m', $line, $contents) ?? $contents;
        } elseif (str_contains($contents, 'RewriteEngine On')) {
            $contents = preg_replace(
                '/RewriteEngine On\s*/',
                "RewriteEngine On\n\n    ".$line."\n\n",
                $contents,
                1
            ) ?? $contents;
        } else {
            $contents = $line.PHP_EOL.$contents;
        }

        if (is_writable($htaccess)) {
            file_put_contents($htaccess, $contents);
        } else {
            app(ProvisionFilesystem::class)->writeFileAsRoot($htaccess, $contents);
        }
    }

    public function applyCountryHubSettings(string $targetPath, int $countryId): void
    {
        $php = (string) config('federation_provision.php_binary', PHP_BINARY);

        // Seed the owner country (+ region) so admin UI dropdowns resolve.
        $country = \App\Models\Country::query()->find($countryId);
        $region = $country?->region;
        $seed = [
            'country' => $country ? $country->getAttributes() : null,
            'region' => $region ? $region->getAttributes() : null,
        ];
        $seedPath = rtrim($targetPath, '/').'/storage/app/federation_country_seed.json';
        app(ProvisionFilesystem::class)->writeFileAsRoot($seedPath, json_encode($seed));

        $code = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$countryId = (int) getenv('KHUB_OWNER_COUNTRY_ID');
$seedFile = storage_path('app/federation_country_seed.json');
if (is_file($seedFile)) {
    $seed = json_decode(file_get_contents($seedFile), true) ?: [];
    if (!empty($seed['region']) && Illuminate\Support\Facades\Schema::hasTable('regions')) {
        $region = $seed['region'];
        unset($region['created_at'], $region['updated_at']);
        Illuminate\Support\Facades\DB::table('regions')->updateOrInsert(['id' => $region['id']], $region);
    }
    if (!empty($seed['country']) && Illuminate\Support\Facades\Schema::hasTable('country')) {
        $country = $seed['country'];
        unset($country['created_at'], $country['updated_at']);
        Illuminate\Support\Facades\DB::table('country')->updateOrInsert(['id' => $country['id']], $country);
    }
    @unlink($seedFile);
}
$row = Illuminate\Support\Facades\DB::table('setting')->where('status', 'active')->first()
    ?? Illuminate\Support\Facades\DB::table('setting')->orderBy('id')->first();
if (!$row) { fwrite(STDERR, "No setting row\n"); exit(1); }
$updates = [];
if (Illuminate\Support\Facades\Schema::hasColumn('setting', 'admin_units_enabled')) {
    $updates['admin_units_enabled'] = 1;
}
if (Illuminate\Support\Facades\Schema::hasColumn('setting', 'default_owner_country_id')) {
    $updates['default_owner_country_id'] = $countryId;
}
if ($updates !== []) {
    Illuminate\Support\Facades\DB::table('setting')->where('id', $row->id)->update($updates);
}
Illuminate\Support\Facades\Cache::forget('settings');
echo "ok\n";
PHP;

        $process = new Process(
            [$php, '-r', $code],
            $targetPath,
            array_merge($_ENV, $_SERVER, [
                'KHUB_OWNER_COUNTRY_ID' => (string) $countryId,
            ]),
            null,
            120
        );
        $process->run();
        if (! $process->isSuccessful()) {
            throw new RuntimeException('Failed to apply country hub settings: '.trim($process->getErrorOutput() ?: $process->getOutput()));
        }
    }

    /**
     * @param  list<string>  $args
     */
    protected function runArtisan(string $php, string $cwd, array $args, int $timeout = 600, bool $required = true): void
    {
        $cmd = array_merge([$php, 'artisan'], $args);
        $process = new Process($cmd, $cwd, null, null, $timeout);
        $process->run();

        if (! $process->isSuccessful()) {
            if (! $required) {
                return;
            }
            $out = trim($process->getErrorOutput() ?: $process->getOutput());
            // Redact password option if present.
            $out = preg_replace('/--password=\S+/', '--password=***', $out) ?? $out;
            throw new RuntimeException('artisan '.($args[0] ?? '').' failed: '.$out);
        }
    }

    protected function ensureAppUrl(string $php, string $targetPath, string $appUrl): void
    {
        $envPath = rtrim($targetPath, '/').'/.env';
        if (! is_readable($envPath)) {
            return;
        }

        $contents = (string) file_get_contents($envPath);
        foreach (['APP_URL' => $appUrl, 'ASSET_URL' => $appUrl] as $key => $value) {
            $line = $key.'='.$value;
            $pattern = '/^'.preg_quote($key, '/').'=.*/m';
            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents) ?? $contents;
            } else {
                $contents .= PHP_EOL.$line;
            }
        }

        // Use sudo write via filesystem helper when not writable.
        if (is_writable($envPath)) {
            file_put_contents($envPath, $contents);
        } else {
            app(ProvisionFilesystem::class)->writeFileAsRoot($envPath, $contents);
        }

        $this->runArtisan($php, $targetPath, ['config:clear'], 60, false);
    }
}
