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

    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $env = $this->readEnvFile(base_path('.env'));

        foreach (DB::table('setting')->get() as $row) {
            $updates = [];

            foreach ($this->columnEnvMap as $column => $envKey) {
                if (! Schema::hasColumn('setting', $column)) {
                    continue;
                }

                $current = trim((string) ($row->{$column} ?? ''));
                if ($current !== '') {
                    continue;
                }

                $value = trim((string) ($env[$envKey] ?? ''));
                if ($value !== '') {
                    $updates[$column] = $value;
                }
            }

            if (Schema::hasColumn('setting', 'microsoft_client_id') && empty($updates['microsoft_client_id'])) {
                $exchangeId = trim((string) ($env['EXCHANGE_CLIENT_ID'] ?? ''));
                if ($exchangeId !== '' && trim((string) ($row->microsoft_client_id ?? '')) === '') {
                    $updates['microsoft_client_id'] = $exchangeId;
                }
            }

            if (Schema::hasColumn('setting', 'microsoft_client_secret') && empty($updates['microsoft_client_secret'])) {
                $exchangeSecret = trim((string) ($env['EXCHANGE_CLIENT_SECRET'] ?? ''));
                if ($exchangeSecret !== '' && trim((string) ($row->microsoft_client_secret ?? '')) === '') {
                    $updates['microsoft_client_secret'] = $exchangeSecret;
                }
            }

            if (Schema::hasColumn('setting', 'microsoft_tenant_id') && empty($updates['microsoft_tenant_id'])) {
                $exchangeTenant = trim((string) ($env['EXCHANGE_TENANT_ID'] ?? ''));
                if ($exchangeTenant !== '' && trim((string) ($row->microsoft_tenant_id ?? '')) === '') {
                    $updates['microsoft_tenant_id'] = $exchangeTenant;
                }
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
