<?php

namespace App\Services\FederationProvision;

use App\Models\Country;
use App\Models\FederatedHubProvision;
use App\Models\FederatedKnowledgeHub;
use App\Services\FederatedHubService;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FederatedHubProvisionService
{
    public function __construct(
        private readonly ProvisionFilesystem $filesystem,
        private readonly ProvisionDatabase $database,
        private readonly ProvisionApache $apache,
        private readonly ProvisionEnvWriter $envWriter,
        private readonly ProvisionAppInstaller $installer,
        private readonly ProvisionMetadataCopy $metadataCopy,
        private readonly FederatedHubService $federation
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) config('federation_provision.enabled');
    }

    public function assertCanProvision(): void
    {
        if (hub_admin_units_enabled()) {
            throw new RuntimeException('Country hubs cannot provision federated sites. Use the continental portal.');
        }

        if (! $this->isEnabled()) {
            throw new RuntimeException('Federation provision is disabled. Set FEDERATION_PROVISION_ENABLED=true on the continental server.');
        }

        $required = [
            'FEDERATION_PROVISION_SUDO_PASSWORD' => config('federation_provision.sudo_password'),
            'FEDERATION_PROVISION_MYSQL_ADMIN_USER' => config('federation_provision.mysql.admin_user'),
            'FEDERATION_PROVISION_MYSQL_ADMIN_PASSWORD' => config('federation_provision.mysql.admin_password'),
            'FEDERATION_PROVISION_SOURCE_ROOT' => config('federation_provision.source_root'),
            'FEDERATION_PROVISION_APACHE_VHOST' => config('federation_provision.apache_vhost'),
        ];

        foreach ($required as $key => $value) {
            if ($value === null || $value === '') {
                throw new RuntimeException($key.' is not configured.');
            }
        }
    }

    /**
     * @param  array{
     *   country_id: int,
     *   slug: string,
     *   site_name?: string|null,
     *   admin_first_name: string,
     *   admin_last_name: string,
     *   admin_email: string,
     *   admin_password: string,
     *   requested_by?: int|null
     * }  $input
     */
    public function createProvisionRecord(array $input): FederatedHubProvision
    {
        $this->assertCanProvision();

        $slug = ProvisionSlug::normalize($input['slug']);
        ProvisionSlug::assertAllowed($slug);

        $country = Country::query()->findOrFail($input['country_id']);

        if (FederatedKnowledgeHub::query()->where('mapped_country_id', $country->id)->where('is_active', true)->exists()) {
            // Allow if only failed provisions exist without a live hub URL conflict.
        }

        $existingHub = FederatedKnowledgeHub::query()
            ->where('base_url', rtrim((string) config('federation_provision.public_base_url'), '/').'/'.$slug)
            ->first();
        if ($existingHub) {
            throw new RuntimeException('A federated hub is already registered for /'.$slug);
        }

        $active = FederatedHubProvision::query()
            ->where('slug', $slug)
            ->whereIn('status', [FederatedHubProvision::STATUS_QUEUED, FederatedHubProvision::STATUS_RUNNING])
            ->exists();
        if ($active) {
            throw new RuntimeException('A provision job for /'.$slug.' is already in progress.');
        }

        $this->filesystem->assertCopyPossible($slug);
        // Apache Alias uniqueness is checked again inside the job (may need sudo to read vhost).

        $baseUrl = rtrim((string) config('federation_provision.public_base_url'), '/').'/'.$slug;
        $siteName = trim((string) ($input['site_name'] ?? '')) ?: ($country->name.' Knowledge Hub');

        return FederatedHubProvision::query()->create([
            'country_id' => $country->id,
            'slug' => $slug,
            'site_name' => $siteName,
            'base_url' => $baseUrl,
            'target_path' => $this->filesystem->targetPath($slug),
            'admin_email' => $input['admin_email'],
            'admin_first_name' => $input['admin_first_name'],
            'admin_last_name' => $input['admin_last_name'],
            'status' => FederatedHubProvision::STATUS_QUEUED,
            'current_step' => 'queued',
            'progress_percent' => 0,
            'message' => 'Queued for provisioning.',
            'requested_by' => $input['requested_by'] ?? null,
        ]);
    }

    /**
     * @param  array{email: string, password: string, first_name: string, last_name: string}  $admin
     */
    public function run(FederatedHubProvision $provision, array $admin): void
    {
        $slug = $provision->slug;
        $createdDb = false;
        $copiedFiles = false;
        $addedApache = false;
        $dbCreds = null;

        try {
            $provision->markStep('validate', 'Validating provision request…', 5);
            $this->assertCanProvision();
            ProvisionSlug::assertAllowed($slug);
            $this->filesystem->assertCopyPossible($slug);
            $this->apache->assertAliasAvailable($slug);

            $provision->markStep('copying', 'Copying application files…', 15);
            $target = $this->filesystem->copyInstance($slug);
            $copiedFiles = true;
            $provision->markStep('copying', 'Application files copied.', 25, ['target_path' => $target]);

            $provision->markStep('database', 'Creating MySQL database…', 35);
            $dbCreds = $this->database->createForSlug($slug);
            $createdDb = true;
            $provision->forceFill([
                'database_name' => $dbCreds['database'],
                'database_username' => $dbCreds['username'],
            ])->save();
            $provision->markStep('database', 'Database created.', 45);

            $provision->markStep('env', 'Writing country environment…', 50);
            $this->envWriter->writeCountryEnv(
                $target,
                $slug,
                (int) $provision->country_id,
                $dbCreds,
                ['APP_NAME' => (string) $provision->site_name]
            );

            $provision->markStep('apache', 'Adding Apache Alias…', 55);
            $this->apache->addAlias($slug);
            $addedApache = true;
            $provision->markStep('apache', 'Apache Alias active.', 60);

            $provision->markStep('install', 'Running application install…', 65);
            $this->installer->install(
                $target,
                (string) $provision->site_name,
                $admin,
                $provision->base_url
            );
            $this->installer->applyCountryHubSettings($target, (int) $provision->country_id);
            $provision->markStep('install', 'Application installed.', 80);

            $provision->markStep('metadata', 'Copying branding and lookup metadata…', 85);
            $metaSummary = $this->metadataCopy->copyToTargetInstance($target, $dbCreds);
            $provision->markStep('metadata', 'Metadata copied.', 90, ['metadata_summary' => $metaSummary]);

            $provision->markStep('register', 'Registering hub on continental portal…', 95);
            $hub = $this->registerHub($provision);
            try {
                $this->federation->testConnection($hub);
            } catch (\Throwable $e) {
                Log::warning('Federated hub provisioned but connect failed: '.$e->getMessage(), [
                    'slug' => $slug,
                    'hub_id' => $hub->id,
                ]);
            }

            $provision->markCompleted('Country hub ready at '.$provision->base_url, $hub->id);
        } catch (\Throwable $e) {
            Log::error('Federated hub provision failed', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            $this->cleanup($slug, $createdDb, $copiedFiles, $addedApache, $dbCreds);
            $provision->markFailed($this->publicError($e));
            throw $e;
        }
    }

    protected function registerHub(FederatedHubProvision $provision): FederatedKnowledgeHub
    {
        $token = '';
        if (function_exists('settings') && settings()) {
            $token = (string) (settings()->federation_api_token ?? '');
        }
        if ($token === '') {
            $token = (string) env('FEDERATION_API_TOKEN', '');
        }

        return FederatedKnowledgeHub::query()->updateOrCreate(
            ['base_url' => rtrim($provision->base_url, '/')],
            [
                'name' => $provision->site_name ?: ucfirst($provision->slug).' Knowledge Hub',
                'mapped_country_id' => $provision->country_id,
                'api_token' => $token ?: null,
                'is_active' => true,
                'auto_sync' => true,
                'connection_status' => 'pending',
            ]
        );
    }

    /**
     * @param  array{database?: string, username?: string}|null  $dbCreds
     */
    protected function cleanup(string $slug, bool $createdDb, bool $copiedFiles, bool $addedApache, ?array $dbCreds): void
    {
        try {
            if ($addedApache) {
                $this->apache->removeAlias($slug);
            }
        } catch (\Throwable $e) {
            Log::warning('Provision cleanup Apache failed: '.$e->getMessage());
        }

        try {
            if ($createdDb) {
                $this->database->dropForSlug(
                    $slug,
                    $dbCreds['database'] ?? null,
                    $dbCreds['username'] ?? null
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Provision cleanup DB failed: '.$e->getMessage());
        }

        try {
            if ($copiedFiles) {
                $this->filesystem->removeTarget($slug);
            }
        } catch (\Throwable $e) {
            Log::warning('Provision cleanup filesystem failed: '.$e->getMessage());
        }
    }

    protected function publicError(\Throwable $e): string
    {
        $message = $e->getMessage();
        $password = (string) config('federation_provision.sudo_password', '');
        $mysqlPass = (string) config('federation_provision.mysql.admin_password', '');
        if ($password !== '') {
            $message = str_replace($password, '***', $message);
        }
        if ($mysqlPass !== '') {
            $message = str_replace($mysqlPass, '***', $message);
        }

        return $message;
    }
}
