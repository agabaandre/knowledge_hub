<?php

namespace Tests\Unit;

use Tests\TestCase;

class AdminUsersCountryAccessLevelTest extends TestCase
{
    public function test_add_and_edit_user_forms_always_expose_access_level_and_country(): void
    {
        $add = file_get_contents(resource_path('views/admin/permissions/partials/add_user_modal.blade.php'));
        $edit = file_get_contents(resource_path('views/admin/permissions/partials/global_user_modals.blade.php'));
        $list = file_get_contents(resource_path('views/admin/permissions/users.blade.php'));

        $this->assertStringContainsString('js-access-level-select', $add);
        $this->assertStringContainsString('name="level_id"', $add);
        $this->assertStringContainsString('country_id', $add);
        $this->assertStringContainsString('hub_owner_country', $add);

        $this->assertStringContainsString('js-access-level-select', $edit);
        $this->assertStringContainsString('name="level_id"', $edit);
        $this->assertStringContainsString('edit_country_id', $edit);
        $this->assertStringContainsString('hub_owner_country', $edit);

        $this->assertStringContainsString('access_level_name', $list);
        $this->assertStringContainsString('country_name', $list);
    }

    public function test_permission_controller_forces_hub_country_and_preserves_access_level(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/PermissionController.php'));

        $this->assertStringContainsString('resolveUserCountryIdForSave', $controller);
        $this->assertStringContainsString('resolveAccessLevelIdForSave', $controller);
        $this->assertStringContainsString('hub_owner_country_id', $controller);
    }

    public function test_resolve_user_country_id_uses_hub_owner_on_country_portal(): void
    {
        config(['deployment.states_enabled' => false, 'deployment.admin_units_enabled' => true]);
        config(['deployment.hub_owner_country_id' => 88]);

        $this->assertTrue(hub_is_country_portal());
        $this->assertSame(88, \App\Http\Controllers\Admin\PermissionController::resolveUserCountryIdForSave(null));
        $this->assertSame(88, \App\Http\Controllers\Admin\PermissionController::resolveUserCountryIdForSave(12));
    }

    public function test_resolve_access_level_id_preserves_existing_when_missing_from_request(): void
    {
        $request = \Illuminate\Http\Request::create('/permissions/saveuser', 'POST', [
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => 'a@example.com',
            'mobile' => '123',
        ]);

        $this->assertSame(4, \App\Http\Controllers\Admin\PermissionController::resolveAccessLevelIdForSave($request, 4));

        $requestWithLevel = \Illuminate\Http\Request::create('/permissions/saveuser', 'POST', [
            'level_id' => 3,
        ]);
        $this->assertSame(3, \App\Http\Controllers\Admin\PermissionController::resolveAccessLevelIdForSave($requestWithLevel, 4));
    }
}
