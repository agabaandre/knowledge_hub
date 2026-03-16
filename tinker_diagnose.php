<?php
/**
 * Diagnose: translate_button_filled not saving, and navigation/modal (PHP-side).
 *
 * Run from project root:
 *   php tinker_diagnose.php
 *
 * Or in tinker: paste the body (from $out = function... to the last $out(...);).
 */
if (php_sapi_name() === 'cli' && !defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
}

$out = function ($msg) {
    echo $msg . "\n";
};

$out('=== Settings & Translate button ===');
$settings = \App\Models\Setting::where('status', 'active')->first();
if (!$settings) {
    $out('No active setting row found.');
} else {
    $out('Active setting id: ' . $settings->id);
    $out('translate_button_filled (raw): ' . var_export($settings->getRawOriginal('translate_button_filled') ?? 'column missing', true));
    $out('translate_button_filled (accessor): ' . var_export($settings->translate_button_filled ?? 'null', true));
    if (\Illuminate\Support\Facades\Schema::hasColumn('setting', 'translate_button_filled')) {
        $out('Column translate_button_filled exists.');
    } else {
        $out('Column translate_button_filled MISSING - run migrations.');
    }
}

$out('');
$out('=== Theme settings (Theme1) ===');
if (\Illuminate\Support\Facades\Schema::hasTable('theme_settings')) {
    $themeSettings = \DB::table('theme_settings')->where('theme', 'theme1')->get();
    foreach ($themeSettings as $row) {
        if ($row->key === 'translate_button_filled') {
            $out('theme_settings theme1 translate_button_filled: ' . var_export($row->value, true));
        }
    }
    if ($themeSettings->where('key', 'translate_button_filled')->isEmpty()) {
        $out('No theme_settings row for translate_button_filled.');
    }
} else {
    $out('Table theme_settings does not exist.');
}

$out('');
$out('=== settings() helper (merged) ===');
$merged = settings();
$out('translate_button_filled via settings(): ' . var_export($merged->translate_button_filled ?? 'null', true));

$out('');
$out('=== Routes (search & resource) ===');
try {
    $out('records/search: ' . (function_exists('route') && \Illuminate\Support\Facades\Route::has('records.search') ? route('records.search') : url('records/search')));
    $out('records/resource sample URL: ' . url('records/resource?id=1'));
} catch (\Throwable $e) {
    $out('URL error: ' . $e->getMessage());
}

$out('');
$out('=== Front-end (run in browser) ===');
$out('1. Search page links: Open DevTools (F12) -> Console. Click "Read more" or "Chat with PDF". If nothing happens, check for JS errors and that the link href is correct.');
$out('2. Chat modal: On a resource detail page (with PDF), click "Chat with PDF". If modal does not open, check Console for errors and that openPdfChat is defined (type: typeof openPdfChat).');
$out('3. Bootstrap: In Console type: typeof bootstrap?.Modal  (Theme1) or typeof $.fn.modal  (default) to see if modal API is available.');
