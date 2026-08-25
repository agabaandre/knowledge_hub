<?php

namespace Tests\Unit;

use App\Support\LocaleDirection;
use Tests\TestCase;

class LookupI18nTest extends TestCase
{
    public function test_english_defaults_to_ltr_and_arabic_to_rtl(): void
    {
        $this->assertSame('ltr', LocaleDirection::direction('en'));
        $this->assertSame('ltr', LocaleDirection::direction('fr'));
        $this->assertSame('rtl', LocaleDirection::direction('ar'));
        $this->assertFalse(LocaleDirection::isRtl('en'));
        $this->assertTrue(LocaleDirection::isRtl('ar'));
    }

    public function test_lookup_i18n_route_and_controller_expose_flags_labels_and_ltr(): void
    {
        $api = file_get_contents(base_path('routes/api.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Api/LookupApiController.php'));
        $encrypt = file_get_contents(app_path('Http/Middleware/EncryptCookies.php'));
        $config = file_get_contents(base_path('config/supported_locales.php'));

        $this->assertStringContainsString("lookup/i18n", $api);
        $this->assertStringContainsString('function i18n', $controller);
        $this->assertStringContainsString('selectorMap', $controller);
        $this->assertStringContainsString('LocaleDirection::direction', $controller);
        $this->assertStringContainsString('khub.africacdc.org/docs', $controller);
        $this->assertStringNotContainsString('khub_dir', $controller);
        $this->assertStringNotContainsString('direction_mode', $controller);
        $this->assertStringNotContainsString('ltr_available', $controller);
        $this->assertStringNotContainsString('khub_dir', $encrypt);
        $this->assertStringNotContainsString('direction_cookie', $config);
    }

    public function test_au_locale_files_cover_frontend_groups(): void
    {
        $groups = ['frontend_nav', 'ui_body', 'home_sections'];
        $locales = ['fr', 'ar', 'es', 'pt', 'sw'];

        foreach ($locales as $locale) {
            foreach ($groups as $group) {
                $path = resource_path("lang/{$locale}/{$group}.php");
                $this->assertFileExists($path, "Missing {$locale} {$group} translations");
                $data = require $path;
                $this->assertIsArray($data);
                $this->assertNotSame('', $data['home'] ?? $data['site_title'] ?? $data['search'] ?? '');
            }
        }

        $frNav = require resource_path('lang/fr/frontend_nav.php');
        $this->assertSame('Accueil', $frNav['home']);
        $this->assertArrayHasKey('layout_ltr', $frNav);
        $this->assertArrayHasKey('records', $frNav);
        $this->assertArrayHasKey('read_more', $frNav);

        $arNav = require resource_path('lang/ar/frontend_nav.php');
        $this->assertNotSame('Home', $arNav['home']);
    }

    public function test_language_management_can_copy_english_defaults(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Admin/LanguageManagementController.php'));
        $panel = file_get_contents(resource_path('views/admin/language-management/partials/translation-panel.blade.php'));

        $this->assertStringContainsString('language-management/copy-english', $routes);
        $this->assertStringContainsString('function copyEnglish', $controller);
        $this->assertStringContainsString('lm-copy-english-btn', $panel);
    }
}
