<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\FederatedHubLookupService;
use App\Services\InstallerService;
use App\Services\MailConfigTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function __construct(private InstallerService $installer)
    {
    }

    public function index(): View
    {
        $requirements = $this->installer->requirements();
        $dbDefaults = $this->installer->databaseDefaults();

        return view('install.index', compact('requirements', 'dbDefaults'));
    }

    public function showDatabase(): View
    {
        $defaults = $this->installer->databaseDefaults();

        return view('install.database', [
            'defaults' => $defaults,
            'runtime' => $this->installer->runtimeEnvironment(),
            'hasExistingTables' => $this->installer->databaseHasExistingTables($defaults),
            'hasExistingData' => $this->installer->databaseHasExistingData($defaults),
        ]);
    }

    public function storeDatabase(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:64',
            'db_username' => 'required|string|max:64',
            'db_password' => 'nullable|string|max:255',
            'skip_migrations' => 'nullable|boolean',
        ]);

        $db = [
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
            'password' => $data['db_password'] ?? '',
        ];

        $test = $this->installer->testDatabaseConnection($db);
        if (! $test['ok']) {
            return back()->withInput()->with('error', 'Database connection failed: '.$test['message']);
        }

        $skipMigrations = $request->boolean('skip_migrations');

        try {
            $this->installer->runDatabaseSetup($db, ! $skipMigrations);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Setup failed: '.$e->getMessage());
        }

        $request->session()->put('installer.database_ready', true);

        $status = $skipMigrations
            ? 'Database connection saved. Existing schema and data were left unchanged.'
            : 'Database migrated successfully.';

        return redirect()->route('install.storage')->with('status', $status);
    }

    public function showStorage(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.database_ready')) {
            return redirect()->route('install.database');
        }

        $defaults = $this->installer->storageDefaults();
        $runtime = $this->installer->runtimeEnvironment();
        $storage = app(\App\Services\HubStorageService::class);

        return view('install.storage', [
            'defaults' => $defaults,
            'siteStorageId' => $storage->siteStorageId(),
            'runtime' => $runtime,
            'drivers' => config('hub_storage.drivers', []),
            'driverSetup' => config('hub_storage.driver_setup', []),
            'driverPackages' => config('hub_storage.driver_packages', []),
        ]);
    }

    public function storeStorage(Request $request): RedirectResponse
    {
        if (! $request->session()->get('installer.database_ready')) {
            return redirect()->route('install.database');
        }

        $driver = $request->input('files_driver', 'internal');
        $rules = [
            'files_driver' => 'required|in:internal,s3,gcs,azure,sharepoint,sftp',
            'local_files_root' => 'nullable|string|max:512',
            'sql_backup_root' => 'nullable|string|max:512',
            'auto_sql_backup' => 'nullable|boolean',
        ];

        $rules['sql_backup_root'] = 'required|string|max:512';
        if ($driver === 'internal') {
            $rules['local_files_root'] = 'required|string|max:512';
        }

        $data = $request->validate($rules);

        try {
            $this->installer->configureStorage($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not configure storage: '.$e->getMessage());
        }

        $request->session()->put('installer.storage_ready', true);

        return redirect()->route('install.site')->with('status', 'Storage paths configured.');
    }

    public function showSite(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.database_ready')) {
            return redirect()->route('install.database');
        }

        if (! $request->session()->get('installer.storage_ready')) {
            return redirect()->route('install.storage');
        }

        $active = null;
        try {
            $active = Setting::query()->where('status', 'active')->first()
                ?? Setting::query()->orderBy('id')->first();
        } catch (\Throwable) {
            $active = null;
        }

        return view('install.site', [
            'timezones' => config('install.timezones', ['UTC']),
            'defaults' => [
                'site_name' => old('site_name', $active->site_name ?? env('APP_NAME', 'Knowledge Hub')),
                'title' => old('title', $active->title ?? env('APP_NAME', 'Knowledge Hub')),
                'slogan' => old('slogan', $active->slogan ?? 'Informing Health Decisions and Actions'),
                'site_description' => old('site_description', $active->site_description ?? 'Welcome to the Knowledge Hub — your central platform for health knowledge and collaboration.'),
                'seo_keywords' => old('seo_keywords', $active->seo_keywords ?? 'Knowledge Management Hub, Public Health'),
                'contact_email' => old('contact_email', $active->email ?? 'admin@localhost'),
                'phone' => old('phone', $active->phone ?? ''),
                'address' => old('address', $active->address ?? ''),
                'timezone' => old('timezone', $active->timezone ?? 'Africa/Nairobi'),
            ],
        ]);
    }

    public function storeSite(Request $request): RedirectResponse
    {
        if (! $request->session()->get('installer.database_ready')) {
            return redirect()->route('install.database');
        }

        $data = $request->validate([
            'site_name' => 'required|string|max:100',
            'title' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'site_description' => 'required|string|max:5000',
            'seo_keywords' => 'nullable|string|max:2000',
            'contact_email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|string|max:150',
        ]);

        try {
            $this->installer->saveSiteSettings($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not save site settings: '.$e->getMessage());
        }

        $request->session()->put('installer.site_ready', true);

        return redirect()->route('install.central')->with('status', 'Site settings saved.');
    }

    public function showCentralHub(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        return view('install.central', [
            'defaults' => [
                'central_hub_url' => old('central_hub_url', env('CENTRAL_HUB_URL', 'https://khub.africacdc.org')),
                'central_hub_api_token' => old('central_hub_api_token', env('CENTRAL_HUB_API_TOKEN', '')),
            ],
        ]);
    }

    public function testCentralHub(Request $request, FederatedHubLookupService $lookup)
    {
        if (! $request->session()->get('installer.site_ready')) {
            return response()->json(['ok' => false, 'error' => 'Complete site settings first.'], 422);
        }

        $data = $request->validate([
            'central_hub_url' => 'required|url|max:500',
            'central_hub_api_token' => 'nullable|string|max:255',
        ]);

        $result = $lookup->testCentralConnection(
            $data['central_hub_url'],
            $data['central_hub_api_token'] ?: null
        );

        return response()->json($result);
    }

    public function storeCentralHub(Request $request, FederatedHubLookupService $lookup): RedirectResponse
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        if ($request->boolean('skip_central')) {
            $request->session()->put('installer.central_ready', true);

            return redirect()->route('install.mail')->with('status', 'Skipped central hub connection.');
        }

        $data = $request->validate([
            'central_hub_url' => 'required|url|max:500',
            'central_hub_api_token' => 'nullable|string|max:255',
            'import_branding' => 'nullable|boolean',
            'import_metadata' => 'nullable|boolean',
        ]);

        try {
            $summary = $lookup->importFromCentral(
                $data['central_hub_url'],
                $data['central_hub_api_token'] ?: null,
                $request->boolean('import_branding', true),
                $request->boolean('import_metadata', true)
            );

            $this->installer->writeEnvValues([
                'CENTRAL_HUB_URL' => rtrim($data['central_hub_url'], '/'),
                'CENTRAL_HUB_API_TOKEN' => $data['central_hub_api_token'] ?? '',
            ]);

            $request->session()->put('installer.central_ready', true);
            $request->session()->put('installer.central_import_summary', $summary);

            $metaTotal = array_sum($summary['metadata'] ?? []);
            $brandTotal = array_sum($summary['branding'] ?? []);

            return redirect()->route('install.mail')->with(
                'status',
                'Connected to central hub. Imported '.$brandTotal.' branding field(s) and '.$metaTotal.' metadata row(s).'
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Central hub connection failed: '.$e->getMessage());
        }
    }

    public function showMail(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        $active = null;
        try {
            $active = Setting::query()->where('status', 'active')->first()
                ?? Setting::query()->orderBy('id')->first();
        } catch (\Throwable) {
            $active = null;
        }

        $mailer = old('mail_mailer');
        if ($mailer === null) {
            $mailer = env('MAIL_MAILER') === 'log' ? 'log' : ($active?->email_driver ?? 'exchange');
        }

        return view('install.mail', [
            'defaults' => [
                'mail_mailer' => $mailer,
                'mail_host' => old('mail_host', $active?->mail_host ?? env('MAIL_HOST', '')),
                'mail_port' => old('mail_port', $active?->mail_port ?? env('MAIL_PORT', '587')),
                'mail_username' => old('mail_username', $active?->mail_username ?? env('MAIL_USERNAME', '')),
                'mail_password' => old('mail_password', $active?->mail_password ?? env('MAIL_PASSWORD', '')),
                'mail_encryption' => old('mail_encryption', $active?->mail_encryption ?? env('MAIL_ENCRYPTION', 'tls')) ?: 'none',
                'mail_from_address' => old('mail_from_address', $active?->mail_from_address ?? env('MAIL_FROM_ADDRESS', 'noreply@localhost')),
                'mail_from_name' => old('mail_from_name', $active?->mail_from_name ?? env('APP_NAME', 'Knowledge Hub')),
                'exchange_tenant_id' => old('exchange_tenant_id', $active?->exchange_tenant_id ?? env('EXCHANGE_TENANT_ID', '')),
                'exchange_client_id' => old('exchange_client_id', $active?->exchange_client_id ?? env('EXCHANGE_CLIENT_ID', '')),
                'exchange_client_secret' => old('exchange_client_secret', $active?->exchange_client_secret ?? ''),
                'exchange_auth_method' => old('exchange_auth_method', $active?->exchange_auth_method ?? env('EXCHANGE_AUTH_METHOD', 'client_credentials')),
                'exchange_redirect_uri' => old('exchange_redirect_uri', $active?->exchange_redirect_uri ?? env('EXCHANGE_REDIRECT_URI', '')),
                'exchange_scope' => old('exchange_scope', $active?->exchange_scope ?? env('EXCHANGE_SCOPE', 'https://graph.microsoft.com/.default')),
            ],
        ]);
    }

    public function testMail(Request $request, MailConfigTestService $mailTest): \Illuminate\Http\JsonResponse
    {
        if (! $request->session()->get('installer.site_ready')) {
            return response()->json(['ok' => false, 'error' => 'Complete site settings first.'], 422);
        }

        $data = $this->validateMailInput($request);
        $recipient = $request->input('test_email');

        $result = $mailTest->testAndSend($data, is_string($recipient) ? $recipient : null);

        return response()->json($result);
    }

    public function storeMail(Request $request): RedirectResponse
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        $data = $this->validateMailInput($request);

        try {
            $this->installer->saveMailSettings($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not save mail settings: '.$e->getMessage());
        }

        $request->session()->put('installer.mail_ready', true);

        return redirect()->route('install.admin')->with('status', 'Mail settings saved. You can change them later under Admin → Configure.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMailInput(Request $request): array
    {
        $driver = $request->input('mail_mailer', 'exchange');
        $rules = [
            'mail_mailer' => 'required|in:log,smtp,exchange',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'test_email' => 'nullable|email|max:255',
        ];

        if ($driver === 'smtp') {
            $rules['mail_host'] = 'required|string|max:255';
            $rules['mail_port'] = 'required|integer|min:1|max:65535';
            $rules['mail_username'] = 'nullable|string|max:255';
            $rules['mail_password'] = 'nullable|string|max:255';
            $rules['mail_encryption'] = 'required|in:tls,ssl,none';
        }

        if ($driver === 'exchange') {
            $rules['exchange_tenant_id'] = 'required|string|max:255';
            $rules['exchange_client_id'] = 'required|string|max:255';
            $rules['exchange_client_secret'] = 'required|string|max:2000';
            $rules['exchange_auth_method'] = 'required|in:client_credentials,authorization_code';
            $rules['exchange_redirect_uri'] = 'nullable|string|max:500';
            $rules['exchange_scope'] = 'nullable|string|max:500';
        }

        return $request->validate($rules);
    }

    public function showAdmin(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        return view('install.admin');
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'app_url' => 'nullable|url|max:255',
        ]);

        try {
            $this->installer->createAdminUser($data);
            $this->installer->finalize($data['app_url'] ?? (string) $request->getSchemeAndHttpHost());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not create admin account: '.$e->getMessage());
        }

        $request->session()->forget([
            'installer.database_ready',
            'installer.storage_ready',
            'installer.site_ready',
            'installer.central_ready',
            'installer.central_import_summary',
            'installer.mail_ready',
        ]);
        $request->session()->put('install_show_complete', true);

        return redirect()->route('install.complete');
    }

    public function complete(): View
    {
        return view('install.complete');
    }
}
