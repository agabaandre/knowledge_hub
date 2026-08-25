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

    public function test_apache_proxy_conf_matches_staff_portal_subdirectory_style(): void
    {
        $conf = file_get_contents(base_path('front_end/apache-front-end.conf'));

        $this->assertStringContainsString('/knowledge_hub/front_end http://127.0.0.1:3001/knowledge_hub/front_end', $conf);
        $this->assertStringContainsString('127.0.0.1:3001', $conf);
        $this->assertStringContainsString('front-end-proxy.php', file_get_contents(base_path('front_end/.htaccess')));
        $this->assertStringContainsString('127.0.0.1:3001', file_get_contents(base_path('front_end/front-end-proxy.php')));
    }
}
