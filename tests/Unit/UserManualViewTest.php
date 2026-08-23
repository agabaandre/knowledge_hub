<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserManualViewTest extends TestCase
{
    public function test_user_guide_markdown_covers_current_portal_behaviour(): void
    {
        $guide = file_get_contents(base_path('docs/user-guide.md'));

        $this->assertIsString($guide);
        $this->assertStringContainsString('/records', $guide);
        $this->assertStringContainsString('/federated', $guide);
        $this->assertStringContainsString('/admin/approvals', $guide);
        $this->assertStringContainsString('/adminunits/details', $guide);
        $this->assertStringContainsString('/account/publish', $guide);
        $this->assertStringContainsString('moderate_publication', $guide);
    }

    public function test_administrator_guide_documents_slug_and_approval_commands(): void
    {
        $guide = file_get_contents(base_path('docs/administrator-guide.md'));

        $this->assertIsString($guide);
        $this->assertStringContainsString('php artisan slugs:regenerate', $guide);
        $this->assertStringContainsString('--only-empty', $guide);
        $this->assertStringContainsString('php artisan approvals:daily-summary', $guide);
        $this->assertStringContainsString('/admin/dashboard/list', $guide);
        $this->assertStringContainsString('font-weight: 900', $guide);
    }

    public function test_user_manual_route_and_footer_point_at_the_in_app_guide(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $footer = file_get_contents(resource_path('views/layouts/theme1/partials/footer.blade.php'));
        $i18n = file_get_contents(resource_path('views/layouts/partials/footer_i18n_row.blade.php'));
        $sitemap = file_get_contents(app_path('Services/SitemapService.php'));
        $controller = file_get_contents(app_path('Http/Controllers/CommonController.php'));

        $this->assertStringContainsString("get('/user_manual'", $routes);
        $this->assertStringContainsString('userManual', $routes);
        $this->assertStringContainsString('administratorGuide', $routes);
        $this->assertStringContainsString("url('user_manual')", $footer);
        $this->assertStringContainsString("url('user_manual')", $i18n);
        $this->assertStringNotContainsString('github.com/Africa-cdc-Khub/knowledge_hub/wiki', $i18n);
        $this->assertStringContainsString("'path' => 'user_manual'", $sitemap);
        $this->assertStringContainsString('docs/user-guide.md', $controller);
        $this->assertStringContainsString('GithubFlavoredMarkdownConverter', $controller);
        $this->assertStringContainsString('rewriteGuideImageUrls', $controller);
        $this->assertStringContainsString("asset('manual/'", $controller);
        $this->assertStringContainsString('rewriteGuideLinkUrls', $controller);
        $this->assertStringContainsString("href=\"(/", $controller);
        $nav = file_get_contents(resource_path('views/partials/secondary_navigation.blade.php'));
        $this->assertStringContainsString('$isUserManualPage', $nav);
        $this->assertStringContainsString("request()->is('user_manual')", $nav);
        $this->assertStringContainsString("request()->is('administrator-guide')", $nav);
        $index = file_get_contents(resource_path('views/user_manual/index.blade.php'));
        $admin = file_get_contents(resource_path('views/user_manual/administrator.blade.php'));
        $this->assertStringContainsString("partials.secondary_navigation", $index);
        $this->assertStringContainsString("'forceShow' => true", $index);
        $this->assertStringContainsString("partials.secondary_navigation", $admin);
    }

    public function test_guide_root_relative_links_include_the_app_subdirectory(): void
    {
        $controller = app(\App\Http\Controllers\CommonController::class);
        $method = new \ReflectionMethod($controller, 'rewriteGuideLinkUrls');
        $method->setAccessible(true);

        $html = $method->invoke(
            $controller,
            '<p><a href="/administrator-guide">administrator guide</a></p>'
        );

        $this->assertStringContainsString(url('administrator-guide'), $html);
        $this->assertStringNotContainsString('href="/administrator-guide"', $html);
    }

    public function test_guide_markdown_embeds_every_manual_screenshot(): void
    {
        $userGuide = (string) file_get_contents(base_path('docs/user-guide.md'));
        $adminGuide = (string) file_get_contents(base_path('docs/administrator-guide.md'));
        $combined = $userGuide."\n".$adminGuide;
        preg_match_all('#\(/manual/(user-guide|administrator-guide)/([a-z0-9\-.]+\.png)\)#', $combined, $matches, PREG_SET_ORDER);

        $this->assertGreaterThanOrEqual(30, count($matches), 'Guides should embed captured screenshots');

        foreach ($matches as $match) {
            $relative = 'manual/'.$match[1].'/'.$match[2];
            $this->assertFileExists(public_path($relative), $relative.' is referenced in the guide but missing from public/');
        }
    }

    public function test_documentation_index_lists_the_guides(): void
    {
        $index = file_get_contents(base_path('docs/README.md'));
        $readme = file_get_contents(base_path('README.md'));

        $this->assertStringContainsString('user-guide.md', $index);
        $this->assertStringContainsString('administrator-guide.md', $index);
        $this->assertStringContainsString('features/APPROVALS.md', $index);
        $this->assertStringContainsString('features/SEO_SLUGS.md', $index);
        $this->assertStringContainsString('docs/user-guide.md', $readme);
    }
}
