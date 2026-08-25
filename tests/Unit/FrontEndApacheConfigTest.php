<?php

namespace Tests\Unit;

use Tests\TestCase;

class FrontEndApacheConfigTest extends TestCase
{
    public function test_next_app_uses_knowledge_hub_front_end_base_path(): void
    {
        $config = file_get_contents(base_path('front_end/next.config.ts'));

        $this->assertStringContainsString("/knowledge_hub/front_end", $config);
        $this->assertStringContainsString('basePath', $config);
        $this->assertStringContainsString('trailingSlash', $config);
    }

    public function test_laravel_htaccess_skips_the_next_frontend_prefix(): void
    {
        $htaccess = file_get_contents(base_path('.htaccess'));

        $this->assertStringContainsString('front_end', $htaccess);
        $this->assertStringContainsString('RewriteRule ^front_end', $htaccess);
    }

    public function test_apache_conf_does_not_proxy_the_whole_frontend_so_static_files_can_load(): void
    {
        $conf = file_get_contents(base_path('front_end/apache-front-end.conf'));

        $this->assertStringContainsString('Alias /assets', $conf);
        $this->assertStringContainsString('webpack-hmr', $conf);
        $this->assertDoesNotMatchRegularExpression(
            '/^ProxyPass\s+\/knowledge_hub\/front_end\s+http/m',
            $conf,
            'Catch-all ProxyPass bypasses published static files and 502s when Node is down.'
        );
        $this->assertStringContainsString('front-end-proxy.php', file_get_contents(base_path('front_end/.htaccess')));
        $this->assertStringContainsString('public-spa', file_get_contents(base_path('front_end/front-end-proxy.php')));
    }

    public function test_next_and_eslint_config_are_on_the_latest_16_3_release(): void
    {
        $pkg = json_decode((string) file_get_contents(base_path('front_end/package.json')), true, 512, JSON_THROW_ON_ERROR);
        $next = $pkg['dependencies']['next'];
        $eslint = $pkg['devDependencies']['eslint-config-next'];

        $this->assertSame($next, $eslint, 'next and eslint-config-next must stay on the same version');
        $this->assertGreaterThanOrEqual(
            0,
            version_compare($next, '16.3.2'),
            'front_end must use Next.js 16.3.2 or newer, found '.$next
        );
    }

    public function test_next_production_build_is_a_static_export(): void
    {
        $config = file_get_contents(base_path('front_end/next.config.ts'));

        $this->assertStringContainsString("output: 'export'", $config);
        $this->assertStringContainsString('unoptimized: true', $config);
    }

    public function test_setup_production_script_builds_and_publishes_static_files(): void
    {
        $script = file_get_contents(base_path('front_end/setup-production.sh'));
        $publish = file_get_contents(base_path('front_end/scripts/publish-static.sh'));

        $this->assertStringContainsString('--skip-build', $script);
        $this->assertStringContainsString('npm run build', $script);
        $this->assertStringContainsString('legacy-peer-deps', $script);
        $this->assertStringContainsString('publish-static.sh', $script);
        $this->assertStringContainsString('knowledge_hub/front_end', $script);
        $this->assertStringContainsString('public-spa', $publish);
        $this->assertStringContainsString('RewriteEngine Off', $publish);
    }

    public function test_dynamic_detail_pages_declare_static_params_for_export(): void
    {
        $pages = [
            'front_end/src/app/(courses-inner-pages)/courses/course-details/[courseId]/page.tsx',
            'front_end/src/app/(blog)/blog/blog-details/[blogId]/page.tsx',
            'front_end/src/app/(pages)/(page-layout-four)/shop/shop-details/[id]/page.tsx',
            'front_end/src/app/(pages)/(page-layout-three)/kindergarten-program-details/[id]/page.tsx',
            'front_end/src/app/(pages)/(page-layout-three)/instructor/instructor-details/[id]/page.tsx',
            'front_end/src/app/(pages)/(page-layout-three)/event/event-details/[id]/page.tsx',
            'front_end/src/app/(courses-inner-pages)/program-details/[programId]/page.tsx',
        ];

        foreach ($pages as $page) {
            $this->assertStringContainsString(
                'generateStaticParams',
                file_get_contents(base_path($page)),
                $page.' must export generateStaticParams for next static export'
            );
        }
    }

    public function test_published_static_resolver_maps_subdirectory_uris_to_files(): void
    {
        require_once base_path('front_end/front-end-static.php');

        $spa = sys_get_temp_dir().'/khub-front-end-spa-'.uniqid();
        mkdir($spa.'/courses', 0777, true);
        file_put_contents($spa.'/index.html', 'home');
        file_put_contents($spa.'/courses/index.html', 'courses');
        mkdir($spa.'/_next/static', 0777, true);
        file_put_contents($spa.'/_next/static/app.js', 'js');

        $this->assertSame('/', khub_front_end_request_relpath('/knowledge_hub/front_end/'));
        $this->assertSame('/courses', khub_front_end_request_relpath('/knowledge_hub/front_end/courses/'));
        $this->assertSame(
            $spa.'/index.html',
            khub_front_end_resolve_file('/', $spa)
        );
        $this->assertSame(
            $spa.'/courses/index.html',
            khub_front_end_resolve_file('/courses', $spa)
        );
        $this->assertSame(
            $spa.'/_next/static/app.js',
            khub_front_end_resolve_file('/_next/static/app.js', $spa)
        );
        $this->assertNull(khub_front_end_resolve_file('/../secret', $spa));

        $this->rmdirRecursive($spa);
    }

    private function rmdirRecursive(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->rmdirRecursive($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
