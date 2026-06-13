<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Services\InstallerService;
use App\Support\DisposableEmailChecker;
use App\Support\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsRepository
{
    /** Keys that are stored per-theme (Theme1 in theme_settings, default in setting row) */
    private const APPEARANCE_KEYS = [
        'primary_color', 'secondary_color', 'primary_text_color', 'links_active_color', 'icon_font_color',
        'banner_text', 'footer_style', 'nav_style', 'admin_nav_style', 'nav_link_color', 'nav_link_hover_color', 'nav_link_active_color', 'nav_font_weight',
        'gradient_start_color', 'gradient_end_color', 'translate_button_filled', 'translate_button_text_color',
        'header_logo_inverse', 'footer_logo_inverse', 'logo_scale',
        'au_red', 'au_gold', 'au_corporate_green', 'au_green', 'au_plum', 'au_grey_text', 'au_white',
        'logo', 'favicon', 'spotlight_banner', 'spotlight_overlay_color', 'spotlight_overlay_opacity',
    ];

    public function get(Request $request){

        // Always get the active configuration
        $setting = Setting::where('status', 'active')->first();
        
        // If no active setting exists, get the first one (fallback)
        if (!$setting) {
            $setting = Setting::first();
        }
        
        return $setting ? $setting->toArray() : [];
    }
    
    /**
     * Normalize theme value for lookup: empty string and null treated as default.
     */
    private function normalizeThemeForLookup(?string $theme): ?string
    {
        $t = $theme === null ? '' : trim((string) $theme);
        return $t === '' ? null : $t;
    }

    /**
     * Theme key for theme_settings table (theme1. -> theme1, et -> et).
     */
    private function themeSettingsKey(?string $siteTheme): ?string
    {
        $t = $this->normalizeThemeForLookup($siteTheme);
        if ($t === null) {
            return null;
        }
        return $t === 'theme1.' ? 'theme1' : $t;
    }

    /**
     * Default config name for a theme (used when creating a new row and for display).
     */
    private function defaultConfigNameForTheme(?string $siteTheme): string
    {
        $t = $this->normalizeThemeForLookup($siteTheme);
        if ($t === null) {
            return 'Default';
        }
        if ($t === 'theme1.') {
            return 'Theme1';
        }
        if ($t === 'et') {
            return 'ET';
        }
        return \Illuminate\Support\Str::title(str_replace(['.', '_', '-'], ' ', $t));
    }
    
    public function save(Request $request){

        $rawTheme = $request->input('site_theme');
        $rawTheme = $rawTheme === null ? '' : trim((string) $rawTheme);
        $requestedTheme = $rawTheme === '' ? null : $rawTheme;

        // Find a configuration row for this theme (one row per theme; load/update by theme)
        $settings = null;
        if ($requestedTheme === null) {
            $settings = Setting::where(function ($q) {
                $q->whereNull('site_theme')->orWhere('site_theme', '');
            })->first();
        } else {
            $settings = Setting::where('site_theme', $requestedTheme)->first();
        }

        $configName = $request->filled('config_name')
            ? $request->config_name
            : $this->defaultConfigNameForTheme($rawTheme ?: null);

        if (!$settings) {
            // Create a new row for this theme so we don't overwrite other themes' config
            $currentActive = Setting::where('status', 'active')->first();
            $settings = new Setting();
            if ($currentActive) {
                $attrs = $currentActive->getAttributes();
                unset($attrs['id']);
                foreach ($attrs as $key => $value) {
                    $settings->setAttribute($key, $value);
                }
            }
            $settings->site_theme = $rawTheme;
            $settings->config_name = $configName;
            $settings->status = 'active';
            $settings->save();
            Setting::where('id', '!=', $settings->id)->update(['status' => 'inactive']);
        } else {
            Setting::where('id', '!=', $settings->id)->update(['status' => 'inactive']);
            $settings->status = 'active';
            $settings->config_name = $configName;
        }

        // Run updates on this config row (the one whose theme is set)
        $settings->site_name            = $request->site_name;
        $settings->title                = $request->title;
        $settings->site_description     = $request->site_description;
        $settings->seo_keywords         = $request->seo_keywords;
        if (Schema::hasColumn('setting', 'use_seo_friendly_urls')) {
            $settings->use_seo_friendly_urls = (bool) $request->boolean('use_seo_friendly_urls', true);
        }
        $settings->address           = $request->address;
        $settings->phone             = $request->phone;
        $settings->email             = $request->email;
        $settings->timezone          = $request->timezone;
        $settings->analytics_script  = $request->analytics_script;
        $settings->slogan            = $request->slogan;
        $settings->content_disclaimer = $request->content_disclaimer;
        $settings->site_theme         = $request->site_theme ?? '';

        $themeKey = $this->themeSettingsKey($request->site_theme ?? null);
        $hasThemeOverlay = $themeKey !== null;

        // Appearance keys: save to theme_settings when theme has overlay (theme1, et, etc.), else to main row
        if (!$hasThemeOverlay) {
            $settings->primary_color     = $request->primary_color;
            $settings->secondary_color   = $request->secondary_color;
        $settings->primary_text_color = $request->primary_text_color;
        $settings->links_active_color = $request->links_active_color;
        $settings->icon_font_color = $request->icon_font_color;
        $settings->banner_text = $request->banner_text;
        $settings->footer_style = $request->footer_style;
            $settings->gradient_start_color = $request->gradient_start_color;
            $settings->gradient_end_color = $request->gradient_end_color;
            if (Schema::hasColumn('setting', 'translate_button_filled')) {
                $settings->translate_button_filled = (bool)$request->boolean('translate_button_filled', false);
            }
            if (Schema::hasColumn('setting', 'translate_button_text_color')) {
                $settings->translate_button_text_color = $request->input('translate_button_text_color', '#ffffff');
            }
            if (Schema::hasColumn('setting', 'header_logo_inverse')) {
                $settings->header_logo_inverse = (bool)$request->boolean('header_logo_inverse', false);
            }
            if (Schema::hasColumn('setting', 'footer_logo_inverse')) {
                $settings->footer_logo_inverse = (bool)$request->boolean('footer_logo_inverse', false);
            }
            if (Schema::hasColumn('setting', 'nav_style')) {
                $settings->nav_style = $request->input('nav_style', 'colored');
            }
            if (Schema::hasColumn('setting', 'nav_link_color')) {
                $settings->nav_link_color = $request->input('nav_link_color');
            }
            if (Schema::hasColumn('setting', 'nav_link_hover_color')) {
                $settings->nav_link_hover_color = $request->input('nav_link_hover_color');
            }
            if (Schema::hasColumn('setting', 'nav_link_active_color')) {
                $settings->nav_link_active_color = $request->input('nav_link_active_color');
            }
            if (Schema::hasColumn('setting', 'nav_font_weight')) {
                $fw = $request->input('nav_font_weight');
                $settings->nav_font_weight = in_array($fw, ['400', '500', '600', '700'], true) ? $fw : '500';
            }
            if (Schema::hasColumn('setting', 'admin_nav_style')) {
                $settings->admin_nav_style = $request->input('admin_nav_style', 'colored');
            }
            if (Schema::hasColumn('setting', 'logo_scale')) {
                $scale = (int) $request->input('logo_scale', 80);
                $allowed = [40, 50, 60, 70, 80, 100, 120];
                $settings->logo_scale = in_array($scale, $allowed) ? $scale : 80;
            }
            if (Schema::hasColumn('setting', 'primary_font')) {
                $settings->primary_font = $request->input('primary_font');
            }
            if (Schema::hasColumn('setting', 'default_font_color')) {
                $settings->default_font_color = $request->input('default_font_color');
            }
            if (Schema::hasColumn('setting', 'front_body_font_size')) {
                $size = $request->input('front_body_font_size');
                $settings->front_body_font_size = $size !== null && $size !== '' ? (string) $size : '14';
            }
            if (Schema::hasColumn('setting', 'admin_body_font_size')) {
                $size = $request->input('admin_body_font_size');
                $settings->admin_body_font_size = $size !== null && $size !== '' ? (string) $size : '14';
            }
            if (Schema::hasColumn('setting', 'nav_font_size')) {
                $navSize = $request->input('nav_font_size');
                $navSizeInt = is_numeric($navSize) ? (int) $navSize : 11;
                $settings->nav_font_size = (string) max(9, min(16, $navSizeInt));
            }
            if (Schema::hasColumn('setting', 'au_red')) {
                $settings->au_red = $request->au_red ?? '#9F2241';
            }
            if (Schema::hasColumn('setting', 'au_gold')) {
                $settings->au_gold = $request->au_gold ?? '#B4A269';
            }
            if (Schema::hasColumn('setting', 'au_corporate_green')) {
                $settings->au_corporate_green = $request->au_corporate_green ?? '#1A5632';
            }
            if (Schema::hasColumn('setting', 'au_green')) {
                $settings->au_green = $request->au_green ?? '#1A5632';
            }
            if (Schema::hasColumn('setting', 'au_plum')) {
                $settings->au_plum = $request->au_plum ?? '#522B39';
            }
            if (Schema::hasColumn('setting', 'au_grey_text')) {
                $settings->au_grey_text = $request->au_grey_text ?? '#58595B';
            }
            if (Schema::hasColumn('setting', 'au_white')) {
                $settings->au_white = $request->au_white ?? '#FFFFFF';
            }
        }
        // feature flags
        if ($request->has('menu_icons_enabled')) {
            $settings->menu_icons_enabled = (bool)$request->menu_icons_enabled;
        } else {
            // unchecked checkbox doesn't submit; set false
            $settings->menu_icons_enabled = false;
        }

        // Homepage content toggles (default false when unchecked)
        $settings->show_featured = (bool)$request->boolean('show_featured', false);
        $settings->show_events = (bool)$request->boolean('show_events', false);
        $settings->show_top_searches = (bool)$request->boolean('show_top_searches', false);
        $settings->show_tags = (bool)$request->boolean('show_tags', false);
        $settings->show_quotes = (bool)$request->boolean('show_quotes', false);
        $settings->show_quiz = (bool)$request->boolean('show_quiz', false);
        if (Schema::hasColumn('setting', 'show_health_themes')) {
            $settings->show_health_themes = (bool) $request->boolean('show_health_themes', false);
        }
        if (Schema::hasColumn('setting', 'section_title_health_themes')) {
            $settings->section_title_health_themes = $request->input('section_title_health_themes');
        }
        if (Schema::hasColumn('setting', 'section_title_top_searches')) {
            $settings->section_title_top_searches = $request->input('section_title_top_searches');
        }
        if (Schema::hasColumn('setting', 'section_title_recommended')) {
            $settings->section_title_recommended = $request->input('section_title_recommended');
        }
        if (Schema::hasColumn('setting', 'section_title_flagship_initiatives')) {
            $settings->section_title_flagship_initiatives = $request->input('section_title_flagship_initiatives');
        }
        if (Schema::hasColumn('setting', 'theme_card_opacity')) {
            $op = $request->input('theme_card_opacity');
            $settings->theme_card_opacity = $op !== null && $op !== '' ? (string) $op : '1';
        }
        if (Schema::hasColumn('setting', 'spotlight_overlay_color')) {
            $settings->spotlight_overlay_color = $request->input('spotlight_overlay_color') ?: '#000000';
        }
        if (Schema::hasColumn('setting', 'spotlight_overlay_opacity')) {
            $overlayOpacity = (int) $request->input('spotlight_overlay_opacity', 35);
            $settings->spotlight_overlay_opacity = max(0, min(100, $overlayOpacity));
        }
        if (Schema::hasColumn('setting', 'theme_cards_per_row')) {
            $cards = (int) $request->input('theme_cards_per_row', 4);
            $settings->theme_cards_per_row = max(2, min(8, $cards));
        }

        // Search page: show forums and communities in combined results (default true)
        if (Schema::hasColumn('setting', 'search_show_forums')) {
            $settings->search_show_forums = (bool)$request->boolean('search_show_forums', true);
        }
        if (Schema::hasColumn('setting', 'search_show_communities')) {
            $settings->search_show_communities = (bool)$request->boolean('search_show_communities', true);
        }
        if (Schema::hasColumn('setting', 'communities_listing_show_participants')) {
            $settings->communities_listing_show_participants = $request->boolean('communities_listing_show_participants');
        }
        if (Schema::hasColumn('setting', 'communities_listing_max_faces')) {
            $mf = (int) $request->input('communities_listing_max_faces', 8);
            $settings->communities_listing_max_faces = max(1, min(24, $mf));
        }
        if (Schema::hasColumn('setting', 'show_publication_card_file_type_badge')) {
            $settings->show_publication_card_file_type_badge = $request->boolean('show_publication_card_file_type_badge');
        }

        if (Schema::hasColumn('setting', 'allow_email_password_accounts_social_login')) {
            $settings->allow_email_password_accounts_social_login = (bool)$request->boolean('allow_email_password_accounts_social_login', true);
        }
        if (Schema::hasColumn('setting', 'block_disposable_email_registration')) {
            $settings->block_disposable_email_registration = (bool) $request->boolean('block_disposable_email_registration', true);
        }
        if (Schema::hasColumn('setting', 'blocked_email_domains')) {
            $settings->blocked_email_domains = self::normalizeBlockedEmailDomainsInput(
                (string) $request->input('blocked_email_domains', '')
            );
        }

        // Publication form settings
        if (Schema::hasColumn('setting', 'publication_min_words')) {
            $settings->publication_min_words = (int)$request->input('publication_min_words', 150);
        }
        
        if (Schema::hasColumn('setting', 'publication_required_fields')) {
            $requiredFields = $request->input('required_fields', []);
            $settings->publication_required_fields = json_encode($requiredFields);
        }

        // Version submission setting
        if (Schema::hasColumn('setting', 'enable_version_submission')) {
            $settings->enable_version_submission = (bool)$request->boolean('enable_version_submission', true);
        }

        // Auto-approve comments setting
        if (Schema::hasColumn('setting', 'auto_approve_comments')) {
            if ($request->has('auto_approve_comments')) {
                $settings->auto_approve_comments = (bool)$request->boolean('auto_approve_comments', true);
            } else {
                // unchecked checkbox doesn't submit; set false
                $settings->auto_approve_comments = false;
            }
        }

        if (Schema::hasColumn('setting', 'enable_ai_search')) {
            if ($request->has('enable_ai_search')) {
                $settings->enable_ai_search = (bool)$request->boolean('enable_ai_search', true);
            } else {
                // unchecked checkbox doesn't submit; set false
                $settings->enable_ai_search = false;
            }
        }

        if (Schema::hasColumn('setting', 'search_pagination_mode')) {
            $mode = (string) $request->input('search_pagination_mode', 'pagination');
            $settings->search_pagination_mode = in_array($mode, ['pagination', 'infinite_scroll'], true)
                ? $mode
                : 'pagination';
        }

        if (Schema::hasColumn('setting', 'forums_pagination_mode')) {
            $mode = (string) $request->input('forums_pagination_mode', 'pagination');
            $settings->forums_pagination_mode = in_array($mode, ['pagination', 'infinite_scroll'], true)
                ? $mode
                : 'pagination';
        }

        if (Schema::hasColumn('setting', 'enable_ai_chat_prune')) {
            if ($request->has('enable_ai_chat_prune')) {
                $settings->enable_ai_chat_prune = (bool)$request->boolean('enable_ai_chat_prune', true);
            } else {
                // unchecked checkbox doesn't submit; set false
                $settings->enable_ai_chat_prune = false;
            }
        }

        if (Schema::hasColumn('setting', 'auto_profile_completion_reminder')) {
            $settings->auto_profile_completion_reminder = (bool) $request->boolean('auto_profile_completion_reminder', false);
        }
        if (Schema::hasColumn('setting', 'profile_reminder_day_of_month')) {
            $day = (int) $request->input('profile_reminder_day_of_month', 1);
            $settings->profile_reminder_day_of_month = max(1, min(28, $day));
        }

        if (Schema::hasColumn('setting', 'admin_units_enabled')) {
            $settings->admin_units_enabled = (bool) $request->boolean('admin_units_enabled');
        }
        if (Schema::hasColumn('setting', 'default_owner_country_id')) {
            $countryId = $request->input('default_owner_country_id');
            $settings->default_owner_country_id = $countryId !== null && $countryId !== '' ? (int) $countryId : null;
        }
        if (Schema::hasColumn('setting', 'default_owner_region_id')) {
            $regionId = $request->input('default_owner_region_id');
            $settings->default_owner_region_id = $regionId !== null && $regionId !== '' ? (int) $regionId : null;
        }
        if (Schema::hasColumn('setting', 'federation_api_token') && $request->filled('federation_api_token')) {
            $settings->federation_api_token = $request->input('federation_api_token');
        }

        if (Schema::hasColumn('setting', 'email_driver')) {
            $this->applyEmailSettings($settings, $request);
        }

        // Handle status change - if setting a new config as active, deactivate others
        if ($request->has('status') && $request->status === 'active') {
            // Deactivate all other settings
            Setting::where('id', '!=', $settings->id)->update(['status' => 'inactive']);
            $settings->status = 'active';
        }

        // Save appearance to theme_settings when theme has overlay (theme1, et, etc.)
        if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
            $this->saveThemeSettings($themeKey, $request);
        }

        // Save cover / images: upload new or use existing from gallery (per-theme: default → main row, themed → theme_settings)
        if ($request->hasFile('logo')) {
            $logo_filepath = $this->save_attachments($request->file('logo'));
            if (!$hasThemeOverlay) {
                $settings->logo = $logo_filepath;
            }
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'logo', $logo_filepath);
            }
        } elseif ($request->filled('logo_existing')) {
            if (!$hasThemeOverlay) {
                $settings->logo = $request->logo_existing;
            }
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'logo', $request->logo_existing);
            }
        }

        if ($request->hasFile('favicon')) {
            $favicon_filepath = $this->save_attachments($request->file('favicon'));
            if (!$hasThemeOverlay) {
                $settings->favicon = $favicon_filepath;
            }
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'favicon', $favicon_filepath);
            }
        } elseif ($request->filled('favicon_existing')) {
            if (!$hasThemeOverlay) {
                $settings->favicon = $request->favicon_existing;
            }
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'favicon', $request->favicon_existing);
            }
        }

        if ($request->hasFile('spotlight_banner')) {
            $banner_filepath = $this->save_attachments($request->file('spotlight_banner'));
            $settings->spotlight_banner = $banner_filepath;
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'spotlight_banner', $banner_filepath);
            }
        } elseif ($request->filled('spotlight_banner_existing')) {
            $settings->spotlight_banner = $request->spotlight_banner_existing;
            if ($hasThemeOverlay && Schema::hasTable('theme_settings')) {
                $this->upsertThemeSetting($themeKey, 'spotlight_banner', $request->spotlight_banner_existing);
            }
        }

        $settings->save();

        DisposableEmailChecker::forgetCache();
        if (Schema::hasColumn('setting', 'email_driver')) {
            EmailConfig::clearCache();
            EmailConfig::applyRuntimeConfig();
        }
        if (Schema::hasColumn('setting', 'microsoft_client_id')
            || Schema::hasColumn('setting', 'enable_microsoft_login')) {
            \App\Support\SsoConfig::clearCache();
            \App\Support\SsoConfig::applyRuntimeConfig();
        }
        if (Schema::hasColumn('setting', 'ai_openai_api_key')) {
            \App\Support\AiConfig::clearCache();
            \App\Support\AiConfig::applyRuntimeConfig();
        }
        clear_settings_cache();
        clear_cache();

        return $settings;
    }

    public function saveSsoIntegrations(Request $request): ?Setting
    {
        $settings = Setting::where('status', 'active')->first() ?: Setting::query()->first();
        if (! $settings || ! Schema::hasColumn('setting', 'microsoft_client_id')) {
            return null;
        }

        $this->applySsoSettings($settings, $request, true);
        $settings->save();

        \App\Support\SsoConfig::clearCache();
        \App\Support\SsoConfig::applyRuntimeConfig();
        clear_settings_cache();
        clear_cache();

        return $settings;
    }

    public function saveAiIntegrations(Request $request): ?Setting
    {
        $settings = Setting::where('status', 'active')->first() ?: Setting::query()->first();
        if (! $settings || ! Schema::hasColumn('setting', 'ai_openai_api_key')) {
            return null;
        }

        $this->applyAiSettings($settings, $request);
        $settings->save();

        \App\Support\AiConfig::clearCache();
        app(InstallerService::class)->clearAiEnvOverrides();
        \App\Support\AiConfig::applyRuntimeConfig();
        clear_settings_cache();
        clear_cache();

        return $settings;
    }

    public function saveLearningIntegrations(Request $request): ?Setting
    {
        $settings = Setting::where('status', 'active')->first() ?: Setting::query()->first();
        if (! $settings) {
            return null;
        }

        if (Schema::hasColumn('setting', 'moodle_api_url')) {
            $this->applyLearningSettings($settings, $request);
        }

        $settings->save();

        if (Schema::hasColumn('setting', 'moodle_api_url')) {
            \App\Support\LearningConfig::clearAllCaches();
            \App\Support\LearningConfig::applyRuntimeConfig();
        }
        clear_settings_cache();
        clear_cache();

        return $settings;
    }

    private static function parseSubmittedBoolean(Request $request, string $key): bool
    {
        $value = $request->input($key);
        if (is_array($value)) {
            $value = end($value);
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function applyEmailSettings(Setting $settings, Request $request): void
    {
        $envDriver = \App\Support\EmailConfig::envDriverFromEnv();
        $submittedDriver = $request->input('email_driver', 'exchange');
        $submittedDriver = in_array($submittedDriver, ['smtp', 'exchange'], true) ? $submittedDriver : 'exchange';
        $settings->email_driver = $submittedDriver !== $envDriver ? $submittedDriver : null;

        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_host', 'MAIL_HOST', 'mail_host');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_port', 'MAIL_PORT', 'mail_port', false, '587');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_username', 'MAIL_USERNAME', 'mail_username');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_password', 'MAIL_PASSWORD', 'mail_password', true);
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_encryption', 'MAIL_ENCRYPTION', 'mail_encryption', false, 'tls');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_from_address', 'MAIL_FROM_ADDRESS', 'mail_from_address');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'mail_from_name', 'MAIL_FROM_NAME', 'mail_from_name');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'exchange_tenant_id', 'EXCHANGE_TENANT_ID', 'exchange_tenant_id');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'exchange_client_id', 'EXCHANGE_CLIENT_ID', 'exchange_client_id');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'exchange_client_secret', 'EXCHANGE_CLIENT_SECRET', 'exchange_client_secret', true);
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'exchange_redirect_uri', 'EXCHANGE_REDIRECT_URI', 'exchange_redirect_uri');
        \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'exchange_scope', 'EXCHANGE_SCOPE', 'exchange_scope', false, 'https://graph.microsoft.com/.default');

        if ($request->has('exchange_auth_method')) {
            $submittedAuth = $request->input('exchange_auth_method', 'client_credentials');
            $submittedAuth = in_array($submittedAuth, ['client_credentials', 'authorization_code'], true)
                ? $submittedAuth
                : 'client_credentials';
            $envAuth = (string) \App\Support\EnvFirstConfig::envEffective('EXCHANGE_AUTH_METHOD', 'client_credentials');
            $settings->exchange_auth_method = $submittedAuth !== $envAuth ? $submittedAuth : null;
        }
    }

    private function applyLearningSettings(Setting $settings, Request $request): void
    {
        if ($request->has('moodle_api_url')) {
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'moodle_api_url', 'MOODLE_API_URL', 'moodle_api_url');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'moodle_base_url', 'MOODLE_URL', 'moodle_base_url');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'moodle_api_token', 'MOODLE_API_TOKEN', 'moodle_api_token', true);
        }

        if ($request->has('moodle_sync_enabled')) {
            $envDefault = filter_var(\App\Support\EnvFirstConfig::envEffective('MOODLE_SYNC_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
            $submitted = self::parseSubmittedBoolean($request, 'moodle_sync_enabled');
            $settings->moodle_sync_enabled = $submitted !== $envDefault ? ($submitted ? 1 : 0) : null;
        }

        if ($request->has('frappe_base_url')) {
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'frappe_base_url', 'FRAPPE_BASE_URL', 'frappe_base_url');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'frappe_api_key', 'FRAPPE_API_KEY', 'frappe_api_key');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'frappe_course_doctype', 'FRAPPE_COURSE_DOCTYPE', 'frappe_course_doctype', false, 'LMS Course');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'frappe_api_secret', 'FRAPPE_API_SECRET', 'frappe_api_secret', true);
        }

        if ($request->has('frappe_sync_enabled')) {
            $envDefault = filter_var(\App\Support\EnvFirstConfig::envEffective('FRAPPE_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
            $submitted = self::parseSubmittedBoolean($request, 'frappe_sync_enabled');
            $settings->frappe_sync_enabled = $submitted !== $envDefault ? ($submitted ? 1 : 0) : null;
        }

        if ($request->has('openedx_lms_url')) {
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'openedx_lms_url', 'OPENEDX_LMS_URL', 'openedx_lms_url');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'openedx_client_id', 'OPENEDX_CLIENT_ID', 'openedx_client_id');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'openedx_token_url', 'OPENEDX_TOKEN_URL', 'openedx_token_url');
            \App\Support\EnvFirstConfig::applySubmittedOverride($settings, $request, 'openedx_client_secret', 'OPENEDX_CLIENT_SECRET', 'openedx_client_secret', true);
        }

        if ($request->has('openedx_sync_enabled')) {
            $envDefault = filter_var(\App\Support\EnvFirstConfig::envEffective('OPENEDX_SYNC_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
            $submitted = self::parseSubmittedBoolean($request, 'openedx_sync_enabled');
            $settings->openedx_sync_enabled = $submitted !== $envDefault ? ($submitted ? 1 : 0) : null;
        }
    }

    private function applySsoSettings(Setting $settings, Request $request, bool $force = false): void
    {
        if (! $force && ! $request->has('sso_settings_submitted')) {
            return;
        }

        if ($request->has('microsoft_client_id')) {
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'microsoft_client_id', 'microsoft_client_id',
                \App\Support\SsoConfig::resolveFromEnv('MICROSOFT_CLIENT_ID', '')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'microsoft_redirect_uri', 'microsoft_redirect_uri',
                \App\Support\SsoConfig::resolveFromEnv('MICROSOFT_REDIRECT_URI', rtrim((string) config('app.url', ''), '/').'/auth/microsoft/callback')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'microsoft_tenant_id', 'microsoft_tenant_id',
                \App\Support\SsoConfig::resolveFromEnv('MICROSOFT_TENANT_ID', 'common')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'microsoft_client_secret', 'microsoft_client_secret',
                \App\Support\SsoConfig::resolveFromEnv('MICROSOFT_CLIENT_SECRET', ''), true
            );
        }
        if ($request->has('google_client_id')) {
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'google_client_id', 'google_client_id',
                \App\Support\SsoConfig::resolveFromEnv('GOOGLE_CLIENT_ID', '')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'google_redirect_uri', 'google_redirect_uri',
                \App\Support\SsoConfig::resolveFromEnv('GOOGLE_REDIRECT_URI', rtrim((string) config('app.url', ''), '/').'/auth/google/callback')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'google_client_secret', 'google_client_secret',
                \App\Support\SsoConfig::resolveFromEnv('GOOGLE_CLIENT_SECRET', ''), true
            );
        }
        if ($request->has('linkedin_client_id')) {
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'linkedin_client_id', 'linkedin_client_id',
                \App\Support\SsoConfig::resolveFromEnv('LINKEDIN_CLIENT_ID', '')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'linkedin_redirect_uri', 'linkedin_redirect_uri',
                \App\Support\SsoConfig::resolveFromEnv('LINKEDIN_REDIRECT_URI', rtrim((string) config('app.url', ''), '/').'/auth/linkedin/callback')
            );
            \App\Support\EnvFirstConfig::applySubmittedOverrideWithEnvEffective(
                $settings, $request, 'linkedin_client_secret', 'linkedin_client_secret',
                \App\Support\SsoConfig::resolveFromEnv('LINKEDIN_CLIENT_SECRET', ''), true
            );
        }
        if ($request->has('enable_microsoft_login')) {
            $settings->enable_microsoft_login = self::parseSubmittedBoolean($request, 'enable_microsoft_login');
        }
        if ($request->has('enable_google_login')) {
            $settings->enable_google_login = self::parseSubmittedBoolean($request, 'enable_google_login');
        }
        if ($request->has('enable_linkedin_login')) {
            $settings->enable_linkedin_login = self::parseSubmittedBoolean($request, 'enable_linkedin_login');
        }
    }

    private function applyAiSettings(Setting $settings, Request $request): void
    {
        if ($request->has('ai_primary_provider')) {
            $settings->ai_primary_provider = trim((string) $request->input('ai_primary_provider', 'openai')) ?: 'openai';
        }

        foreach ([
            'ai_openai_api_key',
            'ai_chatpdf_api_key',
            'ai_gemini_api_key',
            'ai_deepseek_api_key',
            'ai_custom_api_key',
            'ai_serper_api_key',
        ] as $secretColumn) {
            if ($request->filled($secretColumn)) {
                $settings->{$secretColumn} = $request->input($secretColumn);
            }
        }

        foreach ([
            'ai_openai_model',
            'ai_gemini_model',
            'ai_deepseek_model',
            'ai_custom_base_url',
            'ai_custom_model',
        ] as $column) {
            if ($request->has($column)) {
                $settings->{$column} = trim((string) $request->input($column, ''));
            }
        }

        foreach ([
            'ai_openai_enabled',
            'ai_chatpdf_enabled',
            'ai_gemini_enabled',
            'ai_deepseek_enabled',
            'ai_custom_enabled',
            'ai_serper_enabled',
        ] as $toggle) {
            if ($request->has($toggle)) {
                $settings->{$toggle} = self::parseSubmittedBoolean($request, $toggle);
            }
        }

        if (Schema::hasColumn('setting', 'ai_feature_routing') && $request->has('ai_feature_routing')) {
            $routing = [];
            $features = array_keys(config('ai.features', []));
            foreach ($features as $feature) {
                $value = trim((string) $request->input('ai_feature_routing.'.$feature, ''));
                if ($value !== '') {
                    $routing[$feature] = $value;
                }
            }
            $settings->ai_feature_routing = $routing !== [] ? json_encode($routing) : null;
        }

        if (Schema::hasColumn('setting', 'ai_custom_integrations') && $request->has('custom_integrations_submitted')) {
            $existing = \App\Support\AiConfig::customIntegrations();
            $submitted = $request->input('custom_integrations', []);
            $normalized = \App\Support\AiConfig::normalizeCustomIntegrationsInput(
                is_array($submitted) ? $submitted : [],
                $existing
            );
            $settings->ai_custom_integrations = $normalized !== [] ? json_encode($normalized) : null;
        }

        if (Schema::hasColumn('setting', 'ai_source_priority') && $request->has('ai_source_priority')) {
            $priorities = [];
            foreach (array_merge(
                array_keys(config('ai.providers', [])),
                array_keys(config('ai.web_search_providers', []))
            ) as $provider) {
                $value = $request->input('ai_source_priority.'.$provider);
                if (in_array($value, ['env', 'db'], true)) {
                    $priorities[$provider] = $value;
                }
            }
            $settings->ai_source_priority = $priorities !== [] ? json_encode($priorities) : null;
        }

        if (Schema::hasColumn('setting', 'ai_search_allowed_sites') && $request->has('ai_search_allowed_sites_submitted')) {
            $existing = \App\Support\AiConfig::aiSearchAllowedSites();
            $submitted = $request->input('ai_search_allowed_sites', []);
            $normalized = \App\Support\AiConfig::normalizeAiSearchAllowedSitesInput(
                is_array($submitted) ? $submitted : [],
                $existing
            );
            $settings->ai_search_allowed_sites = json_encode($normalized);
        }

        if (Schema::hasColumn('setting', 'ai_settings_saved_at')) {
            $settings->ai_settings_saved_at = now();
        }
    }

    /**
     * One domain per line for admin textarea storage.
     */
    public static function normalizeBlockedEmailDomainsInput(string $raw): string
    {
        $domains = DisposableEmailChecker::parseDomainList($raw);
        $normalized = [];
        foreach ($domains as $domain) {
            $d = DisposableEmailChecker::normalizeDomain($domain);
            if ($d !== '') {
                $normalized[$d] = true;
            }
        }

        return implode("\n", array_keys($normalized));
    }

    private function save_attachments($files)
    {
        $upfiles   = (!is_array($files)) ? [$files] : $files;
        $file_path = null;
        $configDir = function_exists('hub_storage_path')
            ? hub_storage_path('uploads/config')
            : storage_path('app/public/uploads/config');
        if (! is_dir($configDir)) {
            @mkdir($configDir, 0775, true);
        }
        foreach ($upfiles as $file) {
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name . '.' . $extension;
            $file->move($configDir, $file_path);
        }
       return $file_path;
    }

    private function upsertThemeSetting(string $theme, string $key, ?string $value): void
    {
        DB::table('theme_settings')->updateOrInsert(
            ['theme' => $theme, 'key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    private function saveThemeSettings(string $theme, Request $request): void
    {
        $map = [
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'primary_text_color' => $request->primary_text_color,
            'links_active_color' => $request->links_active_color,
            'icon_font_color' => $request->icon_font_color,
            'banner_text' => $request->banner_text,
            'footer_style' => $request->footer_style,
            'nav_style' => $request->input('nav_style', 'colored'),
            'nav_link_color' => $request->input('nav_link_color'),
            'nav_link_hover_color' => $request->input('nav_link_hover_color'),
            'nav_link_active_color' => $request->input('nav_link_active_color'),
            'nav_font_weight' => in_array($request->input('nav_font_weight'), ['400', '500', '600', '700'], true) ? $request->input('nav_font_weight') : '500',
            'admin_nav_style' => $request->input('admin_nav_style', 'colored'),
            'gradient_start_color' => $request->gradient_start_color,
            'gradient_end_color' => $request->gradient_end_color,
            'translate_button_filled' => $request->boolean('translate_button_filled', false) ? '1' : '0',
            'translate_button_text_color' => $request->input('translate_button_text_color', '#ffffff'),
            'header_logo_inverse' => $request->boolean('header_logo_inverse', false) ? '1' : '0',
            'footer_logo_inverse' => $request->boolean('footer_logo_inverse', false) ? '1' : '0',
            'logo_scale' => (string) (in_array((int)$request->input('logo_scale', 80), [40, 50, 60, 70, 80, 100, 120]) ? (int)$request->input('logo_scale', 80) : 80),
            'au_red' => $request->au_red ?? '#9F2241',
            'au_gold' => $request->au_gold ?? '#B4A269',
            'au_corporate_green' => $request->au_corporate_green ?? '#1A5632',
            'au_green' => $request->au_green ?? '#1A5632',
            'au_plum' => $request->au_plum ?? '#522B39',
            'au_grey_text' => $request->au_grey_text ?? '#58595B',
            'au_white' => $request->au_white ?? '#FFFFFF',
            'primary_font' => $request->input('primary_font'),
            'default_font_color' => $request->input('default_font_color'),
            'front_body_font_size' => $request->input('front_body_font_size') !== null && $request->input('front_body_font_size') !== '' ? (string) $request->input('front_body_font_size') : '14',
            'admin_body_font_size' => $request->input('admin_body_font_size') !== null && $request->input('admin_body_font_size') !== '' ? (string) $request->input('admin_body_font_size') : '14',
            'nav_font_size' => (string) max(9, min(16, (int) ($request->input('nav_font_size') ?: 11))),
            'show_health_themes' => $request->boolean('show_health_themes', false) ? '1' : '0',
            'section_title_health_themes' => $request->input('section_title_health_themes'),
            'section_title_top_searches' => $request->input('section_title_top_searches'),
            'section_title_recommended' => $request->input('section_title_recommended'),
            'section_title_flagship_initiatives' => $request->input('section_title_flagship_initiatives'),
            'theme_card_opacity' => $request->input('theme_card_opacity') !== null && $request->input('theme_card_opacity') !== '' ? (string) $request->input('theme_card_opacity') : '1',
            'theme_cards_per_row' => (string) max(2, min(8, (int) $request->input('theme_cards_per_row', 4))),
            'spotlight_overlay_color' => $request->input('spotlight_overlay_color') ?: '#000000',
            'spotlight_overlay_opacity' => (string) max(0, min(100, (int) $request->input('spotlight_overlay_opacity', 35))),
        ];
        foreach ($map as $key => $value) {
            if ($value !== null) {
                $this->upsertThemeSetting($theme, $key, (string) $value);
            }
        }
    }

    /** Keys excluded from export/import (images – paths only; keep config names) */
    private const IMAGE_KEYS = ['logo', 'favicon', 'spotlight_banner'];

    /**
     * Export current active theme configuration as XML (no images).
     * Includes main setting row (non-image) and, if theme is theme1, theme_settings (non-image).
     */
    public function exportConfigAsXml(): string
    {
        $setting = Setting::where('status', 'active')->first();
        if (!$setting) {
            $setting = Setting::first();
        }
        $activeTheme = $setting ? ($setting->site_theme ?? '') : '';

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $root = $dom->createElement('knowledge_hub_config');
        $root->setAttribute('version', '1');
        $root->setAttribute('exported_at', now()->toIso8601String());
        $root->setAttribute('active_theme', $activeTheme);
        $dom->appendChild($root);

        $main = $dom->createElement('settings');
        $root->appendChild($main);

        if ($setting) {
            $columns = Schema::getColumnListing('setting');
            foreach ($columns as $col) {
                if ($col === 'id' || in_array($col, self::IMAGE_KEYS, true)) {
                    continue;
                }
                $value = $setting->getRawOriginal($col) ?? $setting->getAttribute($col) ?? '';
                if ($value === null) {
                    $value = '';
                }
                $value = (string) $value;
                $el = $dom->createElement('item');
                $el->setAttribute('key', $col);
                $el->appendChild($dom->createCDATASection($value));
                $main->appendChild($el);
            }
        }

        $exportThemeKey = $this->themeSettingsKey($activeTheme);
        if ($exportThemeKey !== null && Schema::hasTable('theme_settings')) {
            $themeNode = $dom->createElement('theme_settings');
            $themeNode->setAttribute('theme', $exportThemeKey);
            $root->appendChild($themeNode);
            $rows = DB::table('theme_settings')->where('theme', $exportThemeKey)->get();
            foreach ($rows as $row) {
                if (in_array($row->key, self::IMAGE_KEYS, true)) {
                    continue;
                }
                $el = $dom->createElement('item');
                $el->setAttribute('key', $row->key);
                $el->appendChild($dom->createCDATASection((string) ($row->value ?? '')));
                $themeNode->appendChild($el);
            }
        }

        return $dom->saveXML();
    }

    /**
     * Import configuration from XML and overwrite active theme config (images are not imported).
     */
    public function importConfigFromXml(string $xml): void
    {
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($xml)) {
            throw new \InvalidArgumentException('Invalid XML.');
        }
        $root = $dom->documentElement;
        if (!$root || $root->nodeName !== 'knowledge_hub_config') {
            throw new \InvalidArgumentException('Invalid config XML: root must be knowledge_hub_config.');
        }

        $setting = Setting::where('status', 'active')->first();
        if (!$setting) {
            $setting = Setting::first();
        }
        if (!$setting) {
            throw new \RuntimeException('No setting record to update.');
        }

        $columns = array_flip(Schema::getColumnListing('setting'));
        $exclude = array_merge(['id'], self::IMAGE_KEYS);

        $main = $root->getElementsByTagName('settings')->item(0);
        if ($main) {
            foreach ($main->getElementsByTagName('item') as $item) {
                $key = $item->getAttribute('key');
                if ($key === '' || in_array($key, $exclude, true) || !isset($columns[$key])) {
                    continue;
                }
                $value = $item->textContent;
                if ($key === 'menu_icons_enabled' || $key === 'show_featured' || $key === 'show_events' || $key === 'show_top_searches' || $key === 'show_tags' || $key === 'show_quotes' || $key === 'show_quiz' || $key === 'show_health_themes' || $key === 'translate_button_filled' || $key === 'header_logo_inverse' || $key === 'footer_logo_inverse' || $key === 'search_show_forums' || $key === 'search_show_communities' || $key === 'show_publication_card_file_type_badge' || $key === 'enable_microsoft_login' || $key === 'enable_google_login' || $key === 'enable_linkedin_login' || $key === 'allow_email_password_accounts_social_login' || $key === 'enable_version_submission' || $key === 'auto_approve_comments' || $key === 'enable_ai_search' || $key === 'enable_ai_chat_prune') {
                    $setting->{$key} = in_array(strtolower($value), ['1', 'true', 'yes'], true);
                } else {
                    $setting->{$key} = $value;
                }
            }
            $setting->save();
        }

        $activeTheme = $setting->site_theme ?? '';
        $importThemeKey = $this->themeSettingsKey($activeTheme);
        if ($importThemeKey !== null && Schema::hasTable('theme_settings')) {
            $themeNode = $root->getElementsByTagName('theme_settings')->item(0);
            if ($themeNode && $themeNode->getAttribute('theme') === $importThemeKey) {
                foreach ($themeNode->getElementsByTagName('item') as $item) {
                    $key = $item->getAttribute('key');
                    if ($key === '' || in_array($key, self::IMAGE_KEYS, true)) {
                        continue;
                    }
                    $value = $item->textContent;
                    $this->upsertThemeSetting($importThemeKey, $key, $value);
                }
            }
        }

        clear_cache();
    }
}
