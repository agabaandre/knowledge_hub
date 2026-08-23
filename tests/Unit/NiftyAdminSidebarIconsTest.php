<?php

namespace Tests\Unit;

use Tests\TestCase;

class NiftyAdminSidebarIconsTest extends TestCase
{
    public function test_admin_sidebar_css_does_not_force_regular_weight_on_font_awesome_icons(): void
    {
        $colors = file_get_contents(resource_path('views/layouts/theme1/partials/theme1_colors.blade.php'));

        $this->assertIsString($colors);

        // FA6 solid glyphs (th-large, book, users, list, cog) vanish when weight is 400.
        $this->assertDoesNotMatchRegularExpression(
            '/#mainnav-container[^{]*\.fs-5[^{]*\{[^}]*font-weight:\s*400\s*!important/',
            $colors
        );
        $this->assertMatchesRegularExpression(
            '/#mainnav-container[\s\S]*i\.fa[\s\S]*font-weight:\s*900\s*!important/',
            $colors
        );
    }

    public function test_nifty_admin_layout_always_shows_sidebar_icons(): void
    {
        $layout = file_get_contents(resource_path('views/admin/layouts/main_nifty.blade.php'));
        $sidebar = file_get_contents(resource_path('views/admin/layouts/partials/nifty_sidebar.blade.php'));

        $this->assertStringContainsString('menu-icons-enabled', $layout);
        $this->assertStringNotContainsString('menu-icons-disabled', $layout);
        $this->assertStringNotContainsString('settings()->menu_icons_enabled', $layout);
        $this->assertStringContainsString('fa fa-th-large', $sidebar);
        $this->assertStringContainsString('fa fa-book', $sidebar);
        $this->assertStringContainsString('fa fa-cog', $sidebar);
    }
}
