<?php

namespace Tests\Unit;

use Tests\TestCase;

class EmailLayoutLogoTest extends TestCase
{
    public function test_email_layout_uses_site_logo_not_hardcoded_africacdc_asset(): void
    {
        $layout = file_get_contents(resource_path('views/emails/layout.blade.php'));

        $this->assertStringNotContainsString(
            'https://africacdc.org/wp-content/uploads/2020/02/AfricaCDC_Logo.png',
            $layout
        );
        $this->assertStringContainsString('settings()->logo', $layout);
        $this->assertStringContainsString("asset('assets/images/logo.png')", $layout);
    }
}
