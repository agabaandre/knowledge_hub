<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomFont;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SettingsRepository;
use App\Services\MailConfigTestService;
use App\Support\EmailConfig;
use App\Support\EmailDrivers;
use App\Support\FrontendThemes;
use App\Support\SsoConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    private $settingsRepo;

    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepo = $settingsRepo;
    }

    public function index(Request $request){
        // Use merged settings (per-theme: Theme1 overlay when active)
        $data['settings'] = settings();
        $data['badgeTypes'] = \App\Models\BadgeType::getAllBadgesInOrder();
        // Config gallery: list image filenames from active + legacy config dirs
        $configImages = [];
        $configPaths = array_unique(array_filter([
            function_exists('hub_storage_path') ? hub_storage_path('uploads/config') : null,
            storage_path('app/public/uploads/config'),
        ]));
        foreach ($configPaths as $configPath) {
            if (! is_dir($configPath)) {
                continue;
            }
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg'] as $ext) {
                foreach (glob($configPath.'/*.'.$ext) ?: [] as $path) {
                    $configImages[] = basename($path);
                }
            }
        }
        $data['configGalleryImages'] = array_unique($configImages);
        $data['customFonts'] = \Illuminate\Support\Facades\Schema::hasTable('custom_fonts')
            ? CustomFont::orderBy('name')->get()
            : collect();
        $data['settingKeyGroups'] = Schema::hasTable('setting_key_groups')
            ? DB::table('setting_key_groups')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('group_name')
            : collect();
        $data['emailFields'] = Schema::hasColumn('setting', 'email_driver')
            ? EmailConfig::fieldsForAdmin()
            : [];
        $data['ssoFields'] = (Schema::hasColumn('setting', 'microsoft_client_id')
            || Schema::hasColumn('setting', 'enable_microsoft_login'))
            ? SsoConfig::fieldsForAdmin()
            : [];
        $data['hubCountries'] = Schema::hasTable('country')
            ? \App\Models\Country::orderBy('name')->get()
            : collect();
        $data['hubRegions'] = Schema::hasTable('region')
            ? \App\Models\Region::orderBy('region_name')->get()
            : collect();
        $data['frontendThemes'] = FrontendThemes::builtinCatalog();
        $data['frontendThemePacks'] = FrontendThemes::packsFromSettings(settings());
        $data['activeFrontendTheme'] = FrontendThemes::resolve(settings());
        return view('admin.settings.index', $data);
    }
  
    public function store(Request $request){

        $saved = $this->settingsRepo->save($request);

        // Update badge configurations if provided
        if ($request->has('badge_ids') && is_array($request->badge_ids)) {
            foreach ($request->badge_ids as $badgeId) {
                $badgeType = \App\Models\BadgeType::find($badgeId);
                if ($badgeType) {
                    // Update threshold
                    if (isset($request->badge_thresholds[$badgeId])) {
                        $badgeType->contribution_threshold = (int)$request->badge_thresholds[$badgeId];
                    }
                    
                    // Update name
                    if (isset($request->badge_names[$badgeId])) {
                        $badgeType->name = $request->badge_names[$badgeId];
                    }
                    
                    // Update description
                    if (isset($request->badge_descriptions[$badgeId])) {
                        $badgeType->description = $request->badge_descriptions[$badgeId];
                    }
                    
                    // Update color
                    if (isset($request->badge_colors[$badgeId])) {
                        $badgeType->badge_color = $request->badge_colors[$badgeId];
                    }
                    
                    // Update active status
                    $badgeType->is_active = isset($request->badge_active[$badgeId]) && $request->badge_active[$badgeId] == '1';
                    
                    $badgeType->save();
                }
            }
        }

        if($saved):
            $data = ['alert-success'=>'Settings saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['alert-danger'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }

    public function generateFederationToken(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'Sign in as an administrator to generate a federation token.');
        }

        try {
            $result = $this->settingsRepo->generateFederationApiToken($user);
        } catch (\Throwable $e) {
            $payload = [
                'status' => 'failure',
                'alert-danger' => $e->getMessage(),
            ];

            return $request->expectsJson()
                ? response()->json($payload, 422)
                : back()->with($payload);
        }

        $payload = [
            'status' => 'success',
            'token' => $result['token'],
            'generated_by' => $result['generated_by'],
            'alert-success' => 'Federation API token generated as '.$result['generated_by'].'. Copy it for other hubs. Remote requests must send Authorization: Bearer <token>.',
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with($payload);
    }

    public function storeSso(Request $request)
    {
        $saved = $this->settingsRepo->saveSsoIntegrations($request);

        if ($saved) {
            $data = ['alert-success' => 'Social login settings saved successfully', 'status' => 'success'];
        } else {
            $data = ['alert-danger' => 'Social login settings could not be saved', 'status' => 'failure'];
        }

        if ($request->ajax()) {
            return response($data, $saved ? 200 : 422);
        }

        return back()->with($data);
    }

    public function sendProfileReminders(Request $request)
    {
        try {
            \Artisan::call('profiles:remind-incomplete');
            $output = trim((string) \Artisan::output());
            $message = $output !== '' ? $output : 'Profile completion reminders have been queued.';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'alert-success' => $message,
                    'status' => 'success',
                    'output' => $output,
                ], 200);
            }

            return back()->with('alert-success', $message);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to send profile reminders: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'alert-danger' => $errorMessage,
                    'status' => 'error',
                ], 500);
            }

            return back()->with('alert-danger', $errorMessage);
        }
    }

    public function clearCache(Request $request){
        
        try {
            $output = [];
            $commands = [
                'cache:clear' => 'Clearing application cache...',
                'config:clear' => 'Clearing configuration cache...',
                'view:clear' => 'Clearing view cache...',
                'route:clear' => 'Clearing route cache...',
            ];
            
            $allOutput = [];
            $allOutput[] = "=== Cache Clear Operation Started ===" . PHP_EOL;
            $allOutput[] = date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
            
            foreach ($commands as $command => $description) {
                $allOutput[] = $description . PHP_EOL;
                try {
                    \Artisan::call($command);
                    $commandOutput = \Artisan::output();
                    if (!empty(trim($commandOutput))) {
                        $allOutput[] = $commandOutput . PHP_EOL;
                    } else {
                        $allOutput[] = "✓ Completed successfully" . PHP_EOL;
                    }
                } catch (\Exception $e) {
                    $allOutput[] = "✗ Error: " . $e->getMessage() . PHP_EOL;
                }
                $allOutput[] = PHP_EOL;
            }
            
            // Also clear the settings and theme_settings caches (all theme overlay caches)
            try {
                cache()->forget('settings');
                cache()->forget('theme_settings_theme1');
                cache()->forget('theme_settings_et');
                $allOutput[] = "Clearing settings cache..." . PHP_EOL;
                $allOutput[] = "✓ Settings cache cleared successfully" . PHP_EOL . PHP_EOL;
            } catch (\Exception $e) {
                $allOutput[] = "✗ Error clearing settings cache: " . $e->getMessage() . PHP_EOL . PHP_EOL;
            }
            
            $allOutput[] = "=== Cache Clear Operation Completed ===" . PHP_EOL;
            $allOutput[] = date('Y-m-d H:i:s') . PHP_EOL;
            
            $outputText = implode('', $allOutput);
            
            $message = 'Cache cleared successfully!';
            
            if($request->ajax() || $request->expectsJson()){
                return response()->json([
                    'alert-success' => $message, 
                    'status' => 'success',
                    'output' => $outputText
                ], 200);
            }
            
            return back()->with([
                'alert-success' => $message,
                'cache_output' => $outputText
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error clearing cache: ' . $e->getMessage();
            $errorOutput = "=== Error ===" . PHP_EOL . $e->getMessage() . PHP_EOL . PHP_EOL . $e->getTraceAsString();
            
            if($request->ajax() || $request->expectsJson()){
                return response()->json([
                    'alert-danger' => $errorMessage, 
                    'status' => 'error',
                    'output' => $errorOutput
                ], 500);
            }
            
            return back()->with([
                'alert-danger' => $errorMessage,
                'cache_output' => $errorOutput
            ]);
        }
    }

    public function storeCustomFont(Request $request)
    {
        $request->validate([
            'font_name' => 'nullable|string|max:120',
            'font_family' => 'nullable|string|max:120',
            'font_files' => 'nullable|array',
            'font_files.*' => 'file|mimes:woff,woff2,ttf,otf|max:5120',
        ]);

        $baseName = null;
        if ($request->hasFile('font_files')) {
            $first = collect($request->file('font_files'))->first();
            if ($first) {
                $baseName = pathinfo($first->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }
        $displayName = trim((string) $request->font_name);
        $fontFamily = trim((string) $request->font_family);
        if ($displayName === '') {
            $displayName = $baseName ?: 'Custom font';
        }
        if ($fontFamily === '') {
            $fontFamily = $baseName ? \Illuminate\Support\Str::title(str_replace(['-', '_'], ' ', $baseName)) : 'Custom Font';
        }

        $font = new CustomFont();
        $font->name = $displayName;
        $font->font_family = $fontFamily;
        $font->font_files = null;
        $font->save();

        $dir = storage_path('app/public/uploads/fonts');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $idDir = $dir . '/' . $font->id;
        $files = [];
        if ($request->hasFile('font_files')) {
            if (!is_dir($idDir)) {
                @mkdir($idDir, 0755, true);
            }
            foreach ($request->file('font_files') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['woff', 'woff2', 'ttf', 'otf'])) {
                    continue;
                }
                $filename = \Illuminate\Support\Str::slug($font->name) . '.' . $ext;
                $file->move($idDir, $filename);
                $files[$ext] = $font->id . '/' . $filename;
            }
            $font->font_files = $files ?: null;
            $font->save();
        }
        clear_cache();
        return back()->with('alert-success', 'Custom font added. Select it from the Primary font dropdown and save.');
    }

    public function deleteCustomFont($id)
    {
        $font = CustomFont::findOrFail($id);
        $dir = storage_path('app/public/uploads/fonts');
        $fontDir = $dir . '/' . $font->id;
        if (is_dir($fontDir)) {
            foreach (glob($fontDir . '/*') ?: [] as $path) {
                @unlink($path);
            }
            @rmdir($fontDir);
        }
        $font->delete();
        clear_cache();
        return back()->with('alert-success', 'Custom font removed.');
    }

    /**
     * Export current active theme configuration as XML (excludes images).
     */
    public function exportConfig()
    {
        $xml = $this->settingsRepo->exportConfigAsXml();
        $filename = 'knowledge_hub_config_' . date('Y-m-d_His') . '.xml';
        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import configuration from XML and overwrite active theme config (images are not imported).
     */
    public function importConfig(Request $request)
    {
        $request->validate([
            'config_file' => 'required|file|max:2048',
        ], [
            'config_file.required' => 'Please select a file to import.',
            'config_file.file' => 'The uploaded value must be a file.',
            'config_file.max' => 'The file may not be larger than 2 MB.',
        ]);
        $file = $request->file('config_file');
        $xml = file_get_contents($file->getRealPath());
        if ($xml === false || trim($xml) === '') {
            return back()->with('alert-danger', 'Could not read the config file or file is empty.');
        }
        try {
            $this->settingsRepo->importConfigFromXml($xml);
        } catch (\InvalidArgumentException $e) {
            return back()->with('alert-danger', 'Invalid config file: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            return back()->with('alert-danger', 'Import failed: ' . $e->getMessage());
        }
        return back()->with('alert-success', 'Configuration imported successfully. Active theme settings have been updated (images were not changed).');
    }

    public function storeFrontendThemePack(Request $request)
    {
        $file = $request->file('frontend_theme_pack');
        if (! $file || strtolower((string) $file->getClientOriginalExtension()) !== 'zip') {
            return back()->with(['alert-danger' => 'Upload a .zip theme pack that contains theme.json.', 'status' => 'failure']);
        }

        if (! Schema::hasColumn('setting', 'frontend_theme_packs')) {
            return back()->with(['alert-danger' => 'Run migrations to enable frontend theme packs.', 'status' => 'failure']);
        }

        $tmpDir = storage_path('app/theme-upload-'.uniqid('', true));
        try {
            $manifest = FrontendThemes::extractPack($file->getRealPath(), $tmpDir);
        } catch (\InvalidArgumentException $e) {
            return back()->with(['alert-danger' => $e->getMessage(), 'status' => 'failure']);
        }

        $id = $manifest['id'];
        if (FrontendThemes::isBuiltin($id)) {
            $id = $id.'-'.substr(sha1(uniqid('', true)), 0, 6);
            $manifest['id'] = $id;
        }

        $dest = FrontendThemes::storagePath($id);
        if (is_dir($dest)) {
            \Illuminate\Support\Facades\File::deleteDirectory($dest);
        }
        if (! @rename($tmpDir, $dest)) {
            \Illuminate\Support\Facades\File::copyDirectory($tmpDir, $dest);
            \Illuminate\Support\Facades\File::deleteDirectory($tmpDir);
        }

        $cssRel = 'frontend-themes/'.$id.'/tokens.css';
        $cssUrl = is_file($dest.DIRECTORY_SEPARATOR.'tokens.css')
            ? (function_exists('storage_link') ? storage_link($cssRel) : asset('storage/'.$cssRel))
            : '';

        $settings = \App\Models\Setting::where('status', 'active')->first();
        if (! $settings) {
            return back()->with(['alert-danger' => 'No active settings row found.', 'status' => 'failure']);
        }

        $packs = FrontendThemes::packsFromSettings($settings);
        $packs = array_values(array_filter($packs, static fn ($pack) => ($pack['id'] ?? '') !== $id));
        $packs[] = [
            'id' => $id,
            'name' => $manifest['name'],
            'extends' => $manifest['extends'],
            'css_url' => $cssUrl,
            'source' => 'upload',
        ];
        $settings->frontend_theme_packs = json_encode($packs);
        if ($request->boolean('activate_frontend_theme') && Schema::hasColumn('setting', 'frontend_theme')) {
            $settings->frontend_theme = $id;
        }
        $settings->save();
        clear_settings_cache();
        clear_cache();

        return back()->with(['alert-success' => 'Theme pack “'.$manifest['name'].'” uploaded.', 'status' => 'success']);
    }

    public function destroyFrontendThemePack(Request $request, string $slug)
    {
        $id = FrontendThemes::sanitizeId($slug);
        if (FrontendThemes::isBuiltin($id)) {
            return back()->with(['alert-danger' => 'Built-in themes cannot be deleted.', 'status' => 'failure']);
        }

        $settings = \App\Models\Setting::where('status', 'active')->first();
        if (! $settings || ! Schema::hasColumn('setting', 'frontend_theme_packs')) {
            return back()->with(['alert-danger' => 'No theme packs to delete.', 'status' => 'failure']);
        }

        $packs = array_values(array_filter(
            FrontendThemes::packsFromSettings($settings),
            static fn ($pack) => ($pack['id'] ?? '') !== $id
        ));
        $settings->frontend_theme_packs = json_encode($packs);
        if (Schema::hasColumn('setting', 'frontend_theme') && FrontendThemes::sanitizeId($settings->frontend_theme ?? null) === $id) {
            $settings->frontend_theme = FrontendThemes::DEFAULT;
        }
        $settings->save();

        $dest = FrontendThemes::storagePath($id);
        if (is_dir($dest)) {
            \Illuminate\Support\Facades\File::deleteDirectory($dest);
        }
        clear_settings_cache();
        clear_cache();

        return back()->with(['alert-success' => 'Theme pack removed.', 'status' => 'success']);
    }

    public function testMail(Request $request, MailConfigTestService $mailTest)
    {
        $request->validate([
            'email_driver' => 'nullable|string|in:'.implode(',', EmailDrivers::SUPPORTED),
            'test_email' => 'nullable|email|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:2000',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
            'mail_http_base_url' => 'nullable|string|max:500',
            'mail_http_client_id' => 'nullable|string|max:500',
            'mail_http_client_secret' => 'nullable|string|max:2000',
            'mail_api_key' => 'nullable|string|max:2000',
            'mail_api_secret' => 'nullable|string|max:2000',
            'mail_api_domain' => 'nullable|string|max:255',
            'mail_api_region' => 'nullable|in:us,eu',
            'mail_api_base_url' => 'nullable|string|max:500',
            'mail_api_message_stream' => 'nullable|string|max:100',
            'exchange_tenant_id' => 'nullable|string|max:255',
            'exchange_client_id' => 'nullable|string|max:255',
            'exchange_client_secret' => 'nullable|string|max:2000',
            'exchange_auth_method' => 'nullable|in:client_credentials,authorization_code',
            'exchange_redirect_uri' => 'nullable|string|max:500',
            'exchange_scope' => 'nullable|string|max:500',
        ]);

        EmailConfig::clearCache();

        $payload = $this->mailTestPayloadFromRequest($request);
        $recipient = $request->input('test_email');
        if (! is_string($recipient) || trim($recipient) === '') {
            $recipient = auth()->user()->email ?? null;
        }

        $result = $mailTest->testAndSend($payload, is_string($recipient) ? $recipient : null);

        return response()->json($result, ($result['ok'] ?? false) ? 200 : 422);
    }

    /**
     * Build a mail-test payload from the configure form, falling back to effective
     * env/DB values when secret fields are left blank.
     *
     * @return array<string, mixed>
     */
    private function mailTestPayloadFromRequest(Request $request): array
    {
        $driver = EmailDrivers::normalize((string) (
            $request->input('email_driver') ?: EmailConfig::driver()
        ));

        $value = static function (Request $request, string $key, string $envKey, ?string $column = null, $default = '') {
            if ($request->filled($key)) {
                return $request->input($key);
            }

            return EmailConfig::resolve($envKey, $column ?? strtolower($envKey), $default);
        };

        return [
            'email_driver' => $driver,
            'mail_mailer' => $driver,
            'mail_from_address' => $value($request, 'mail_from_address', 'MAIL_FROM_ADDRESS', 'mail_from_address'),
            'mail_from_name' => $value($request, 'mail_from_name', 'MAIL_FROM_NAME', 'mail_from_name', config('app.name')),
            'mail_host' => $value($request, 'mail_host', 'MAIL_HOST', 'mail_host'),
            'mail_port' => $value($request, 'mail_port', 'MAIL_PORT', 'mail_port', '587'),
            'mail_username' => $value($request, 'mail_username', 'MAIL_USERNAME', 'mail_username'),
            'mail_password' => $value($request, 'mail_password', 'MAIL_PASSWORD', 'mail_password'),
            'mail_encryption' => $value($request, 'mail_encryption', 'MAIL_ENCRYPTION', 'mail_encryption', 'tls') ?: 'tls',
            'mail_http_base_url' => $value(
                $request,
                'mail_http_base_url',
                'MAIL_HTTP_BASE_URL',
                'mail_http_base_url',
                EmailConfig::httpBaseUrlDefault()
            ),
            'mail_http_client_id' => $value($request, 'mail_http_client_id', 'MAIL_HTTP_CLIENT_ID', 'mail_http_client_id'),
            'mail_http_client_secret' => $value($request, 'mail_http_client_secret', 'MAIL_HTTP_CLIENT_SECRET', 'mail_http_client_secret'),
            'mail_api_key' => $value($request, 'mail_api_key', 'MAIL_API_KEY', 'mail_api_key'),
            'mail_api_secret' => $value($request, 'mail_api_secret', 'MAIL_API_SECRET', 'mail_api_secret'),
            'mail_api_domain' => $value($request, 'mail_api_domain', 'MAIL_API_DOMAIN', 'mail_api_domain'),
            'mail_api_region' => $value($request, 'mail_api_region', 'MAIL_API_REGION', 'mail_api_region', 'us') ?: 'us',
            'mail_api_base_url' => $value($request, 'mail_api_base_url', 'MAIL_API_BASE_URL', 'mail_api_base_url'),
            'mail_api_message_stream' => $value(
                $request,
                'mail_api_message_stream',
                'MAIL_API_MESSAGE_STREAM',
                'mail_api_message_stream',
                'outbound'
            ) ?: 'outbound',
            'exchange_tenant_id' => $value($request, 'exchange_tenant_id', 'EXCHANGE_TENANT_ID', 'exchange_tenant_id'),
            'exchange_client_id' => $value($request, 'exchange_client_id', 'EXCHANGE_CLIENT_ID', 'exchange_client_id'),
            'exchange_client_secret' => $value($request, 'exchange_client_secret', 'EXCHANGE_CLIENT_SECRET', 'exchange_client_secret'),
            'exchange_auth_method' => $value(
                $request,
                'exchange_auth_method',
                'EXCHANGE_AUTH_METHOD',
                'exchange_auth_method',
                'client_credentials'
            ) ?: 'client_credentials',
            'exchange_redirect_uri' => $value($request, 'exchange_redirect_uri', 'EXCHANGE_REDIRECT_URI', 'exchange_redirect_uri'),
            'exchange_scope' => $value(
                $request,
                'exchange_scope',
                'EXCHANGE_SCOPE',
                'exchange_scope',
                'https://graph.microsoft.com/.default'
            ),
        ];
    }
}
