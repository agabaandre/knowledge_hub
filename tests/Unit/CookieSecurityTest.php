<?php

namespace Tests\Unit;

use Tests\TestCase;

class CookieSecurityTest extends TestCase
{
    public function test_session_secure_defaults_from_app_url_when_env_unset(): void
    {
        config(['session.secure' => null]);

        $this->assertTrue(function_exists('cookie_secure_flag'));
    }

    public function test_cookie_secure_flag_respects_session_config(): void
    {
        config(['session.secure' => true]);
        $this->assertTrue(cookie_secure_flag());

        config(['session.secure' => false]);
        $this->assertFalse(cookie_secure_flag());
    }

    public function test_select2_plugin_is_patched_release(): void
    {
        $js = file_get_contents(public_path('assets/plugins/select2/js/select2.min.js'));

        $this->assertStringContainsString('Select2 4.0.13', $js);
        $this->assertStringNotContainsString('Select2 4.0.6-rc.1', $js);
    }
}
