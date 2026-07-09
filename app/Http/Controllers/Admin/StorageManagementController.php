<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\MigrateHostStorageJob;
use App\Jobs\MigrateHubStorageJob;
use App\Models\HubStorageSetting;
use App\Services\HubDatabaseBackupService;
use App\Services\HubEnvBackupService;
use App\Services\HubOffsiteBackupService;
use App\Services\HubStorageMetricsService;
use App\Services\HubStoragePublicationIndexService;
use App\Services\HubStorageService;
use App\Services\StaffEcosystemStorageService;
use App\Support\QueueHealth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StorageManagementController extends Controller
{
    public function index(
        HubStorageService $storage,
        HubDatabaseBackupService $backup,
        HubEnvBackupService $envBackup,
        HubStorageMetricsService $metrics,
        StaffEcosystemStorageService $staffStorage
    )
    {
        $settings = $storage->settings();
        $recommended = $storage->recommendedPaths();

        try {
            $storage->ensurePublicStorageSymlink();
        } catch (\Throwable) {
            // Admin page should still load if linking fails (permissions, etc.).
        }

        $freshMetrics = request()->boolean('refresh_metrics');

        return view('admin.storage.index', [
            'settings' => $settings,
            'recommended' => $recommended,
            'drivers' => config('hub_storage.drivers', []),
            'driverPackages' => config('hub_storage.driver_packages', []),
            'driverSetup' => config('hub_storage.driver_setup', []),
            'contentAreas' => config('hub_storage.content_prefixes', []),
            'backups' => $backup->listBackups(),
            'envBackups' => $envBackup->listBackups(),
            'filesRoot' => $storage->filesRoot(),
            'configuredFilesRoot' => $storage->configuredInternalRoot(),
            'legacyFilesRoot' => $storage->legacyInternalRoot(),
            'isUsingLegacyUploadFallback' => $storage->isUsingLegacyUploadFallback(),
            'needsLegacyToHostMigration' => $storage->needsLegacyToHostMigration(),
            'canPurgeLegacyInternalStorage' => $storage->canPurgeLegacyInternalStorage(),
            'legacyPurgePreview' => $storage->previewPurgeLegacyInternalStorage(),
            'publicStorageLinkOk' => $storage->publicStorageLinkOk(),
            'sqlBackupRoot' => $storage->sqlBackupRoot(),
            'usesExternal' => $storage->usesExternalFiles(),
            'siteStorageId' => $storage->siteStorageId(),
            'backupTableGroups' => $backup->backupTableGroups(),
            'kpiExcludedTables' => config('hub_storage.backup_kpi_tables', []),
            'systemMetrics' => $metrics->snapshot($storage, $freshMetrics),
            'staffEcosystemEnabled' => $staffStorage->enabled(),
            'staffEcosystem' => $staffStorage->enabled() ? [
                'site_id' => $staffStorage->siteId(),
                'data_root' => $staffStorage->dataRoot(),
                'repo_root' => $staffStorage->repoRoot(),
                'backup_root' => $staffStorage->filesBackupRoot(),
                'modules' => $staffStorage->moduleMetrics(),
                'backups' => $staffStorage->listFileBackups(),
            ] : null,
        ]);
    }

    public function update(Request $request, HubStorageService $storage)
    {
        $validated = $request->validate([
            'files_driver' => 'required|in:internal,s3,gcs,azure,sharepoint,sftp',
            'local_files_root' => 'nullable|string|max:512',
            'sql_backup_root' => 'nullable|string|max:512',
            'auto_sql_backup' => 'nullable|boolean',
            'sql_backup_retention_days' => 'required|integer|min:1|max:3650',
            'cloud_key' => 'nullable|string|max:255',
            'cloud_secret' => 'nullable|string|max:255',
            'cloud_region' => 'nullable|string|max:64',
            'cloud_bucket' => 'nullable|string|max:255',
            'cloud_endpoint' => 'nullable|string|max:512',
            'cloud_url' => 'nullable|string|max:512',
            'cloud_root_prefix' => 'nullable|string|max:255',
            'cloud_account_name' => 'nullable|string|max:255',
            'cloud_account_key' => 'nullable|string|max:512',
            'cloud_connection_string' => 'nullable|string|max:1024',
            'cloud_container' => 'nullable|string|max:255',
            'gcs_project_id' => 'nullable|string|max:255',
            'gcs_key_file_path' => 'nullable|string|max:512',
            'gcs_bucket' => 'nullable|string|max:255',
            'gcs_storage_api_uri' => 'nullable|string|max:512',
            'cloud_host' => 'nullable|string|max:255',
            'cloud_username' => 'nullable|string|max:255',
            'cloud_password' => 'nullable|string|max:255',
            'cloud_private_key' => 'nullable|string|max:8192',
            'cloud_passphrase' => 'nullable|string|max:255',
            'cloud_port' => 'nullable|integer|min:1|max:65535',
            'sharepoint_tenant_id' => 'nullable|string|max:255',
            'sharepoint_client_id' => 'nullable|string|max:255',
            'sharepoint_client_secret' => 'nullable|string|max:512',
            'sharepoint_site_id' => 'nullable|string|max:255',
            'sharepoint_site_hostname' => 'nullable|string|max:255',
            'sharepoint_site_path' => 'nullable|string|max:255',
            'sharepoint_drive_id' => 'nullable|string|max:255',
            'offsite_backup_enabled' => 'nullable|boolean',
            'offsite_backup_driver' => ['nullable', Rule::requiredIf(fn () => $request->boolean('offsite_backup_enabled')), 'in:s3,gcs,azure,sftp,ftp'],
            'offsite_remote_prefix' => 'nullable|string|max:255',
            'offsite_remote_root' => 'nullable|string|max:512',
            'offsite_key' => 'nullable|string|max:255',
            'offsite_secret' => 'nullable|string|max:255',
            'offsite_region' => 'nullable|string|max:64',
            'offsite_bucket' => 'nullable|string|max:255',
            'offsite_endpoint' => 'nullable|string|max:512',
            'offsite_gcs_project_id' => 'nullable|string|max:255',
            'offsite_gcs_key_file_path' => 'nullable|string|max:512',
            'offsite_gcs_bucket' => 'nullable|string|max:255',
            'offsite_gcs_storage_api_uri' => 'nullable|string|max:512',
            'offsite_connection_string' => 'nullable|string|max:1024',
            'offsite_account_name' => 'nullable|string|max:255',
            'offsite_account_key' => 'nullable|string|max:512',
            'offsite_container' => 'nullable|string|max:255',
            'offsite_host' => 'nullable|string|max:255',
            'offsite_username' => 'nullable|string|max:255',
            'offsite_password' => 'nullable|string|max:255',
            'offsite_private_key' => 'nullable|string|max:8192',
            'offsite_passphrase' => 'nullable|string|max:255',
            'offsite_port' => 'nullable|integer|min:1|max:65535',
            'offsite_ftp_ssl' => 'nullable|boolean',
            'offsite_ftp_passive' => 'nullable|boolean',
        ]);

        if (! Schema::hasTable('hub_storage_settings')) {
            throw ValidationException::withMessages([
                'sql_backup_root' => 'Storage settings table is missing. Run database migrations first.',
            ]);
        }

        $settings = HubStorageSetting::current();
        $existingCloud = $settings->cloud_config ?? [];
        $existingOffsite = $settings->offsite_backup_config ?? [];

        $cloud = array_merge($existingCloud, array_filter([
            'key' => $validated['cloud_key'] ?? null,
            'secret' => $validated['cloud_secret'] ?? null,
            'region' => $validated['cloud_region'] ?? null,
            'endpoint' => $validated['cloud_endpoint'] ?? null,
            'url' => $validated['cloud_url'] ?? null,
            'root_prefix' => $validated['cloud_root_prefix'] ?? ($existingCloud['root_prefix'] ?? 'khub'),
            'account_name' => $validated['cloud_account_name'] ?? null,
            'account_key' => $validated['cloud_account_key'] ?? null,
            'connection_string' => $validated['cloud_connection_string'] ?? null,
            'container' => $validated['cloud_container'] ?? null,
            'project_id' => $validated['gcs_project_id'] ?? null,
            'key_file_path' => $validated['gcs_key_file_path'] ?? null,
            'bucket' => $validated['gcs_bucket'] ?? $validated['cloud_bucket'] ?? null,
            'storage_api_uri' => $validated['gcs_storage_api_uri'] ?? null,
            'host' => $validated['cloud_host'] ?? null,
            'username' => $validated['cloud_username'] ?? null,
            'password' => $validated['cloud_password'] ?? null,
            'private_key' => $validated['cloud_private_key'] ?? null,
            'passphrase' => $validated['cloud_passphrase'] ?? null,
            'port' => $validated['cloud_port'] ?? null,
            'tenant_id' => $validated['sharepoint_tenant_id'] ?? null,
            'client_id' => $validated['sharepoint_client_id'] ?? null,
            'client_secret' => $validated['sharepoint_client_secret'] ?? null,
            'site_id' => $validated['sharepoint_site_id'] ?? null,
            'site_hostname' => $validated['sharepoint_site_hostname'] ?? null,
            'site_path' => $validated['sharepoint_site_path'] ?? null,
            'drive_id' => $validated['sharepoint_drive_id'] ?? null,
        ], fn ($v) => $v !== null && $v !== ''));

        $offsite = $this->mergeOffsiteBackupConfig($request, $validated, $existingOffsite);

        $settings->update([
            'files_driver' => $validated['files_driver'],
            'local_files_root' => $validated['files_driver'] === 'internal'
                ? ($validated['local_files_root'] ?: $storage->defaultInternalRoot())
                : null,
            'sql_backup_root' => $validated['sql_backup_root'] ?: $storage->defaultSqlBackupRoot(),
            'auto_sql_backup' => $request->boolean('auto_sql_backup'),
            'sql_backup_retention_days' => (int) $validated['sql_backup_retention_days'],
            'cloud_config' => $cloud,
            'offsite_backup_config' => $offsite,
        ]);

        $storage->forgetSettingsCache();
        $storage->ensureDirectories();

        return redirect()
            ->route('admin.storage.index')
            ->with('alert-success', 'Storage settings saved.');
    }

    public function testConnection(HubStorageService $storage)
    {
        return response()->json($storage->testConnection());
    }

    public function testOffsiteConnection(HubOffsiteBackupService $offsite)
    {
        return response()->json($offsite->testConnection());
    }

    public function runOffsiteBackup(HubOffsiteBackupService $offsite)
    {
        $result = $offsite->runWeeklyUpload(true);

        if (($result['status'] ?? '') === 'ok') {
            return redirect()
                ->route('admin.storage.index', [], 303)
                ->with('alert-success', $result['message'])
                ->withFragment('storage-config');
        }

        return redirect()
            ->route('admin.storage.index', [], 303)
            ->with('alert-danger', $result['message'] ?? 'Offsite backup failed.')
            ->withFragment('storage-config');
    }

    public function browse(Request $request, HubStorageService $storage, HubStoragePublicationIndexService $publicationIndex)
    {
        $area = (string) $request->input('area', 'publications');
        $path = (string) $request->input('path', '');

        $result = $storage->browse($area, $path);
        $base = config('hub_storage.content_prefixes')[$area] ?? 'uploads/publications';

        foreach ($result['items'] as &$item) {
            if (($item['type'] ?? '') !== 'file') {
                continue;
            }

            $storagePath = trim($base.'/'.($item['path'] ?? $item['name'] ?? ''), '/');
            $item['publications'] = $publicationIndex->referencesForStoragePath($storagePath);
        }
        unset($item);

        return response()->json($result);
    }

    public function publicationReferences(Request $request, HubStoragePublicationIndexService $publicationIndex)
    {
        $path = trim(str_replace(['\\', '..'], ['/', ''], (string) $request->input('path', '')), '/');

        return response()->json([
            'path' => $path,
            'files' => $publicationIndex->referencesInDirectory($path),
        ]);
    }

    public function browseBackups(Request $request, HubDatabaseBackupService $backup)
    {
        $path = (string) $request->input('path', '');
        $storage = app(HubStorageService::class);
        $root = $storage->sqlBackupRoot();
        $absolute = $root.($path ? DIRECTORY_SEPARATOR.str_replace(['..', '/'], '', $path) : '');
        if (! str_starts_with(realpath($absolute) ?: $absolute, realpath($root) ?: $root)) {
            return response()->json(['items' => [], 'path' => ''], 403);
        }

        $items = [];
        if (is_dir($absolute)) {
            foreach (scandir($absolute) ?: [] as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $full = $absolute.DIRECTORY_SEPARATOR.$entry;
                $items[] = [
                    'name' => $entry,
                    'type' => is_dir($full) ? 'dir' : 'file',
                    'path' => trim($path.'/'.$entry, '/'),
                    'size' => is_file($full) ? filesize($full) : null,
                ];
            }
        }

        return response()->json(['items' => $items, 'path' => $path]);
    }

    public function runBackup(Request $request, HubDatabaseBackupService $backup)
    {
        $incremental = $request->boolean('incremental', true);
        $result = $backup->runBackup($incremental);

        $message = "SQL backup completed ({$result['tables']} tables) at {$result['path']}";
        if (! empty($result['env_backup']['name'])) {
            $message .= '. .env snapshot saved as '.$result['env_backup']['name'].'.';
        }

        return redirect()
            ->route('admin.storage.index', [], 303)
            ->with('alert-success', $message)
            ->withFragment('storage-backups');
    }

    public function runEnvBackup(HubEnvBackupService $envBackup)
    {
        try {
            $result = $envBackup->runBackup();
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.storage.index', [], 303)
                ->with('alert-danger', '.env backup failed: '.$e->getMessage())
                ->withFragment('storage-backups');
        }

        return redirect()
            ->route('admin.storage.index', [], 303)
            ->with('alert-success', '.env backup saved as '.$result['name'].'.')
            ->withFragment('storage-backups');
    }

    public function downloadEnvBackup(Request $request, HubEnvBackupService $envBackup)
    {
        $name = (string) $request->query('name', '');
        $path = $envBackup->resolveBackupPath($name);
        if ($path === null) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path, basename($path));
    }

    public function restoreBackup(Request $request, HubDatabaseBackupService $backup)
    {
        $validated = $request->validate([
            'backup_path' => 'required|string|max:1024',
            'only_empty_tables' => 'nullable|boolean',
            'restore_tables' => 'required|array|min:1',
            'restore_tables.*' => ['string', Rule::in($backup->backupTables())],
        ]);

        $path = $validated['backup_path'];
        if (! is_dir($path)) {
            return redirect()
                ->route('admin.storage.index')
                ->with('alert-danger', 'Backup path not found.');
        }

        $result = $backup->restoreFromDirectory(
            $path,
            $request->boolean('only_empty_tables', true),
            $validated['restore_tables']
        );
        $message = count($result['restored']).' table(s) restored.';
        if ($result['skipped']) {
            $message .= ' Skipped (not empty): '.implode(', ', $result['skipped']);
        }
        if ($result['missing']) {
            $message .= ' Not in backup folder: '.implode(', ', $result['missing']);
        }
        if ($result['errors']) {
            return redirect()->route('admin.storage.index')->with('alert-danger', $message.' Errors: '.implode('; ', $result['errors']));
        }

        return redirect()->route('admin.storage.index')->with('alert-success', $message);
    }

    public function backupTablesInDirectory(Request $request, HubDatabaseBackupService $backup)
    {
        $path = (string) $request->input('path', '');
        if ($path === '' || ! is_dir($path)) {
            return response()->json(['tables' => []]);
        }

        $root = app(HubStorageService::class)->sqlBackupRoot();
        if (! str_starts_with(realpath($path) ?: $path, realpath($root) ?: $root)) {
            return response()->json(['tables' => []], 403);
        }

        return response()->json([
            'tables' => $backup->tablesInBackupDirectory($path),
        ]);
    }

    public function migrate(Request $request)
    {
        $mode = $request->input('mode', 'queue');
        if ($mode === 'sync') {
            app(HubStorageService::class)->migrateInternalToExternal();

            return redirect()->route('admin.storage.index')->with('alert-success', 'File migration to external storage completed.');
        }

        MigrateHubStorageJob::dispatch();

        return redirect()->route('admin.storage.index')->with('alert-success', 'External file migration queued.');
    }

    public function migrateHost(Request $request)
    {
        $storage = app(HubStorageService::class);

        if (! $storage->needsLegacyToHostMigration()) {
            return redirect()
                ->route('admin.storage.index')
                ->with('alert-danger', 'No legacy uploads need copying to the host files root.');
        }

        $mode = $request->input('mode', 'queue');
        if ($mode === 'sync') {
            $result = $storage->migrateLegacyToHostPath();
            if (($result['status'] ?? '') === 'completed') {
                return redirect()
                    ->route('admin.storage.index')
                    ->with('alert-success', 'Files copied to host path. public/storage has been relinked.');
            }

            return redirect()
                ->route('admin.storage.index')
                ->with('alert-danger', $result['message'] ?? 'Host path migration did not complete.');
        }

        MigrateHostStorageJob::dispatch();

        return redirect()
            ->route('admin.storage.index')
            ->with('alert-success', 'Host path migration queued.');
    }

    public function purgeLegacy(HubStorageService $storage)
    {
        if (! $storage->canPurgeLegacyInternalStorage()) {
            $preview = $storage->previewPurgeLegacyInternalStorage();

            return redirect()
                ->route('admin.storage.index')
                ->with('alert-danger', $preview['skipped'] > 0
                    ? "Cannot purge legacy storage: {$preview['skipped']} file(s) missing or mismatched on the host path."
                    : 'Legacy uploads cannot be purged yet. Complete host migration and verify files first.');
        }

        $result = $storage->purgeLegacyInternalStorage(false);

        if (($result['status'] ?? '') === 'completed') {
            return redirect()
                ->route('admin.storage.index')
                ->with('alert-success', $result['message'] ?? 'Legacy upload copies removed.');
        }

        return redirect()
            ->route('admin.storage.index')
            ->with('alert-danger', $result['message'] ?? 'Legacy purge did not complete.');
    }

    public function migrationStatus(Request $request)
    {
        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()
                ->route('admin.storage.index', [], 303)
                ->withFragment('storage-migration');
        }

        $settings = HubStorageSetting::current();

        return response()->json([
            'status' => $settings->migration_status,
            'total' => (int) $settings->migration_files_total,
            'done' => (int) $settings->migration_files_done,
            'message' => $settings->migration_message,
        ]);
    }

    public function systemMetrics(Request $request, HubStorageService $storage, HubStorageMetricsService $metrics)
    {
        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()
                ->route('admin.storage.index', [], 303)
                ->withFragment('storage-overview');
        }

        return response()->json($metrics->snapshot($storage, $request->boolean('fresh')));
    }

    public function retryFailedJobs(HubStorageService $storage)
    {
        $result = QueueHealth::retryAllFailed();

        Cache::forget('hub_storage_metrics_'.$storage->siteStorageId());

        if (($result['status'] ?? '') === 'ok') {
            return redirect()
                ->route('admin.storage.index', [], 303)
                ->with('alert-success', $result['message'])
                ->withFragment('storage-overview');
        }

        return redirect()
            ->route('admin.storage.index', [], 303)
            ->with('alert-danger', $result['message'] ?? 'Could not retry failed jobs.')
            ->withFragment('storage-overview');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    private function mergeOffsiteBackupConfig(Request $request, array $validated, array $existing): array
    {
        $driver = $validated['offsite_backup_driver'] ?? ($existing['driver'] ?? null);
        $config = array_merge($existing, array_filter([
            'enabled' => $request->boolean('offsite_backup_enabled'),
            'driver' => $driver,
            'remote_prefix' => $validated['offsite_remote_prefix'] ?? ($existing['remote_prefix'] ?? 'sql-backups'),
            'remote_root' => $validated['offsite_remote_root'] ?? ($existing['remote_root'] ?? '/khub-backups'),
            'key' => $validated['offsite_key'] ?? null,
            'region' => $validated['offsite_region'] ?? null,
            'bucket' => $validated['offsite_bucket'] ?? ($validated['offsite_gcs_bucket'] ?? null),
            'endpoint' => $validated['offsite_endpoint'] ?? null,
            'project_id' => $validated['offsite_gcs_project_id'] ?? null,
            'key_file_path' => $validated['offsite_gcs_key_file_path'] ?? null,
            'storage_api_uri' => $validated['offsite_gcs_storage_api_uri'] ?? null,
            'account_name' => $validated['offsite_account_name'] ?? null,
            'container' => $validated['offsite_container'] ?? null,
            'host' => $validated['offsite_host'] ?? null,
            'username' => $validated['offsite_username'] ?? null,
            'private_key' => $validated['offsite_private_key'] ?? null,
            'port' => $validated['offsite_port'] ?? null,
        ], fn ($value) => $value !== null && $value !== ''));

        if ($driver === 'ftp') {
            $config['ssl'] = $request->boolean('offsite_ftp_ssl');
            $config['passive'] = $request->boolean('offsite_ftp_passive');
        }

        if ($driver === 'gcs' && ! empty($validated['offsite_gcs_bucket'])) {
            $config['bucket'] = $validated['offsite_gcs_bucket'];
        }

        foreach ([
            'secret' => 'offsite_secret',
            'password' => 'offsite_password',
            'account_key' => 'offsite_account_key',
            'connection_string' => 'offsite_connection_string',
            'passphrase' => 'offsite_passphrase',
        ] as $key => $field) {
            $value = $request->input($field);
            if ($value !== null && $value !== '') {
                $config[$key] = $value;
            }
        }

        if (! $request->boolean('offsite_backup_enabled')) {
            $config['enabled'] = false;
        }

        return $config;
    }

    public function staffEcosystemMetrics(StaffEcosystemStorageService $staffStorage)
    {
        if (! $staffStorage->enabled()) {
            return response()->json(['enabled' => false]);
        }

        return response()->json([
            'enabled' => true,
            'site_id' => $staffStorage->siteId(),
            'data_root' => $staffStorage->dataRoot(),
            'modules' => $staffStorage->moduleMetrics(),
            'backups' => $staffStorage->listFileBackups(),
        ]);
    }

    public function staffMigrate(Request $request, StaffEcosystemStorageService $staffStorage)
    {
        if (! $staffStorage->enabled()) {
            return redirect()->route('admin.storage.index')->with('alert-danger', 'Staff ecosystem storage is disabled.');
        }

        $validated = $request->validate([
            'module' => 'required|in:ci,apm,helpdesk,staff-portal,all',
        ]);

        if ($validated['module'] === 'all') {
            $messages = [];
            foreach (array_keys($staffStorage->moduleDefinitions()) as $module) {
                $result = $staffStorage->runMigration($module);
                $messages[] = $result['message'];
            }

            return redirect()
                ->route('admin.storage.index')
                ->withFragment('storage-staff')
                ->with('alert-success', implode(' ', $messages));
        }

        $result = $staffStorage->runMigration($validated['module']);
        $flash = ($result['status'] ?? '') === 'completed' ? 'alert-success' : 'alert-danger';

        return redirect()
            ->route('admin.storage.index')
            ->withFragment('storage-staff')
            ->with($flash, $result['message']);
    }

    public function staffFileBackup(Request $request, StaffEcosystemStorageService $staffStorage)
    {
        if (! $staffStorage->enabled()) {
            return redirect()->route('admin.storage.index')->with('alert-danger', 'Staff ecosystem storage is disabled.');
        }

        $modules = $request->input('modules');
        $moduleList = is_array($modules) ? array_values($modules) : null;
        $result = $staffStorage->runFileBackup($moduleList);
        $flash = ($result['status'] ?? '') === 'completed' ? 'alert-success' : 'alert-danger';

        return redirect()
            ->route('admin.storage.index')
            ->withFragment('storage-staff')
            ->with($flash, ($result['message'] ?? 'Backup finished.').' '.$result['path']);
    }
}
