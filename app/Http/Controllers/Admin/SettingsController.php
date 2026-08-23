<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomFont;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SettingsRepository;
use App\Support\EmailConfig;
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
}
