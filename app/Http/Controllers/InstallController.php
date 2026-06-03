<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\InstallerService;
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
        return view('install.database', [
            'defaults' => $this->installer->databaseDefaults(),
            'runtime' => $this->installer->runtimeEnvironment(),
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

        try {
            $this->installer->runDatabaseSetup($db);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Setup failed: '.$e->getMessage());
        }

        $request->session()->put('installer.database_ready', true);

        return redirect()->route('install.site')->with('status', 'Database migrated successfully.');
    }

    public function showSite(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.database_ready')) {
            return redirect()->route('install.database');
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

        return redirect()->route('install.mail')->with('status', 'Site settings saved.');
    }

    public function showMail(Request $request): RedirectResponse|View
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        return view('install.mail', [
            'defaults' => [
                'mail_mailer' => env('MAIL_MAILER', 'log'),
                'mail_host' => env('MAIL_HOST', ''),
                'mail_port' => env('MAIL_PORT', '587'),
                'mail_username' => env('MAIL_USERNAME', ''),
                'mail_password' => env('MAIL_PASSWORD', ''),
                'mail_encryption' => env('MAIL_ENCRYPTION', 'tls') ?: 'none',
                'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@localhost'),
                'mail_from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Knowledge Hub')),
            ],
        ]);
    }

    public function storeMail(Request $request): RedirectResponse
    {
        if (! $request->session()->get('installer.site_ready')) {
            return redirect()->route('install.site');
        }

        $driver = $request->input('mail_mailer', 'log');
        $rules = [
            'mail_mailer' => 'required|in:log,smtp',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ];

        if ($driver === 'smtp') {
            $rules['mail_host'] = 'required|string|max:255';
            $rules['mail_port'] = 'required|integer|min:1|max:65535';
            $rules['mail_username'] = 'nullable|string|max:255';
            $rules['mail_password'] = 'nullable|string|max:255';
            $rules['mail_encryption'] = 'required|in:tls,ssl,none';
        }

        $data = $request->validate($rules);

        try {
            $this->installer->writeMailConfig($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not save mail settings: '.$e->getMessage());
        }

        $request->session()->put('installer.mail_ready', true);

        return redirect()->route('install.admin')->with('status', 'Mail settings saved to .env.');
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

        $request->session()->forget(['installer.database_ready', 'installer.site_ready', 'installer.mail_ready']);
        $request->session()->put('install_show_complete', true);

        return redirect()->route('install.complete');
    }

    public function complete(): View
    {
        return view('install.complete');
    }
}
