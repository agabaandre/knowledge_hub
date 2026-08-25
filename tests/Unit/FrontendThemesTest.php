<?php

namespace Tests\Unit;

use App\Support\FrontendThemes;
use Tests\TestCase;
use ZipArchive;

class FrontendThemesTest extends TestCase
{
    public function test_builtin_catalog_includes_the_three_design_themes(): void
    {
        $ids = array_column(FrontendThemes::builtinCatalog(), 'id');

        $this->assertContains(FrontendThemes::ID_UNIVERSITY, $ids);
        $this->assertContains(FrontendThemes::ID_LANGUAGE_ACADEMY, $ids);
        $this->assertContains(FrontendThemes::ID_ONLINE_COURSE, $ids);
        $this->assertSame(FrontendThemes::ID_UNIVERSITY, FrontendThemes::DEFAULT);
    }

    public function test_sanitize_id_falls_back_to_university(): void
    {
        $this->assertSame('university', FrontendThemes::sanitizeId(null));
        $this->assertSame('university', FrontendThemes::sanitizeId(''));
        $this->assertSame('university', FrontendThemes::sanitizeId('../evil'));
        $this->assertSame('language-academy', FrontendThemes::sanitizeId('language-academy'));
        $this->assertSame('my-pack', FrontendThemes::sanitizeId('my-pack'));
    }

    public function test_parse_manifest_requires_name_and_a_builtin_base(): void
    {
        $ok = FrontendThemes::parseManifest(json_encode([
            'id' => 'coastal',
            'name' => 'Coastal Hub',
            'extends' => 'university',
            'tokens' => ['primary' => '#0b5fff'],
        ]));

        $this->assertSame('coastal', $ok['id']);
        $this->assertSame('Coastal Hub', $ok['name']);
        $this->assertSame('university', $ok['extends']);
        $this->assertSame('#0b5fff', $ok['tokens']['primary']);

        $this->expectException(\InvalidArgumentException::class);
        FrontendThemes::parseManifest(json_encode(['name' => 'Nope', 'extends' => 'shop']));
    }

    public function test_resolve_prefers_the_active_setting_and_uploaded_pack(): void
    {
        $builtin = FrontendThemes::resolve((object) ['frontend_theme' => 'online-course']);
        $this->assertSame('online-course', $builtin['id']);
        $this->assertSame('builtin', $builtin['source']);

        $uploaded = FrontendThemes::resolve((object) [
            'frontend_theme' => 'coastal',
            'frontend_theme_packs' => json_encode([
                [
                    'id' => 'coastal',
                    'name' => 'Coastal Hub',
                    'extends' => 'university',
                    'css_url' => '/storage/frontend-themes/coastal/tokens.css',
                ],
            ]),
        ]);
        $this->assertSame('coastal', $uploaded['id']);
        $this->assertSame('upload', $uploaded['source']);
        $this->assertSame('university', $uploaded['extends']);
    }

    public function test_extract_pack_rejects_zip_slip_and_accepts_a_valid_manifest(): void
    {
        $dir = sys_get_temp_dir().'/khub-theme-'.uniqid();
        mkdir($dir, 0777, true);

        $slip = $dir.'/slip.zip';
        $zip = new ZipArchive();
        $zip->open($slip, ZipArchive::CREATE);
        $zip->addFromString('../../evil.txt', 'nope');
        $zip->close();

        try {
            FrontendThemes::extractPack($slip, $dir.'/out-slip');
            $this->fail('Zip slip should be rejected');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('Unsafe', $e->getMessage());
        }

        $good = $dir.'/good.zip';
        $zip = new ZipArchive();
        $zip->open($good, ZipArchive::CREATE);
        $zip->addFromString('theme.json', json_encode([
            'id' => 'coastal',
            'name' => 'Coastal Hub',
            'extends' => 'language-academy',
        ]));
        $zip->addFromString('tokens.css', ':root{--bd-primary:#123;}');
        $zip->close();

        $pack = FrontendThemes::extractPack($good, $dir.'/out-good');
        $this->assertSame('coastal', $pack['id']);
        $this->assertFileExists($dir.'/out-good/theme.json');
        $this->assertFileExists($dir.'/out-good/tokens.css');
    }

    public function test_admin_configure_exposes_frontend_theme_controls(): void
    {
        $tab = file_get_contents(resource_path('views/admin/settings/partials/tab_frontend.blade.php'));
        $index = file_get_contents(resource_path('views/admin/settings/index.blade.php'));
        $repository = file_get_contents(app_path('Repositories/SettingsRepository.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $api = file_get_contents(base_path('routes/api.php'));

        $this->assertStringContainsString('name="frontend_theme"', $tab);
        $this->assertStringContainsString('frontend_theme_pack', $tab);
        $this->assertStringContainsString('tab_frontend', $index);
        $this->assertStringContainsString('frontend-tab', $index);
        $this->assertStringContainsString('frontend_theme', $repository);
        $this->assertStringContainsString('storeFrontendThemePack', $routes);
        $this->assertStringContainsString('lookup/frontend-theme', $api);
        $this->assertFileExists(base_path('front_end/src/themes/_starter/theme.json'));
        $this->assertFileExists(base_path('front_end/src/nucleus/theme/registry.ts'));
    }
}
