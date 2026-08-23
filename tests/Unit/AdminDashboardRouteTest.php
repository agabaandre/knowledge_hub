<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminDashboardRouteTest extends TestCase
{
    public function test_admin_dashboard_url_serves_the_stats_home(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/admin/dashboard', 'GET'));

        $this->assertSame('index', $route->getActionMethod());
        $this->assertSame(AdminController::class.'@index', $route->getActionName());
        $this->assertSame('admin.dashboard', $route->getName());
    }

    public function test_admin_only_dashboard_publications_use_a_list_url(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/admin/dashboard/list', 'GET'));

        $this->assertSame('dashboards', $route->getActionMethod());
        $this->assertSame(AdminController::class.'@dashboards', $route->getActionName());
        $this->assertSame('admin.dashboard.list', $route->getName());
    }

    public function test_admin_chrome_sends_dashboard_home_to_the_stats_page(): void
    {
        $sidebar = file_get_contents(resource_path('views/admin/layouts/partials/nifty_sidebar.blade.php'));
        $nav = file_get_contents(resource_path('views/admin/layouts/partials/nav.blade.php'));
        $header = file_get_contents(resource_path('views/admin/layouts/main_nifty.blade.php'));

        $this->assertStringContainsString("url('admin/dashboard')", $header);
        $this->assertStringContainsString("url('admin/dashboard')", $sidebar);
        $this->assertStringContainsString("url('admin/dashboard/list')", $sidebar);
        $this->assertStringContainsString("url('admin/dashboard/list')", $nav);
        $this->assertStringContainsString("__('admin_nav.overview')", $sidebar);
        $this->assertStringContainsString("__('admin_nav.view_all_dashboards')", $sidebar);
    }

    public function test_average_daily_visits_is_zero_when_access_logs_are_empty(): void
    {
        $this->assertSame(0, AdminController::averageDailyVisits(0, null));
        $this->assertSame(0, AdminController::averageDailyVisits(12, null));
        $this->assertSame(0, AdminController::averageDailyVisits(12, ''));
    }

    public function test_admin_unit_and_static_link_caches_do_not_share_a_key(): void
    {
        $adminUnitsComposer = file_get_contents(app_path('View/Composers/AdminUnitsViewComposer.php'));
        $viewComposers = file_get_contents(app_path('Providers/ViewComposerServiceProvider.php'));
        $localeSwitch = file_get_contents(app_path('Http/Controllers/LocaleSwitchController.php'));

        $this->assertStringContainsString("remember('administrative_units.all'", $adminUnitsComposer);
        $this->assertStringContainsString("remember('static_links.ordered'", $viewComposers);
        $this->assertStringContainsString("remember('static_links.ordered'", $localeSwitch);
        $this->assertStringNotContainsString("remember('adminunits'", $viewComposers);
        $this->assertStringNotContainsString("remember('adminunits'", $localeSwitch);
    }
}
