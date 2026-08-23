<?php

namespace Tests\Unit;

use App\Support\DataCategoryAccess;
use Tests\TestCase;

class DataCategoryAccessTest extends TestCase
{
    public function test_restricted_flag_defaults_to_workforce_permission(): void
    {
        $category = (object) ['is_restricted' => 1, 'required_permission' => null];

        $this->assertSame('view_workforce', DataCategoryAccess::requiredPermission($category));
        $this->assertFalse(DataCategoryAccess::userCanView($category, null));
    }

    public function test_named_permission_is_used_when_present(): void
    {
        $category = (object) [
            'is_restricted' => 1,
            'required_permission' => 'view_workforce',
        ];

        $user = new class {
            public function can($permission): bool
            {
                return $permission === 'view_workforce';
            }
        };

        $this->assertTrue(DataCategoryAccess::userCanView($category, $user));
    }

    public function test_public_categories_remain_visible(): void
    {
        $category = (object) ['is_restricted' => 0, 'required_permission' => '', 'is_special' => 0];

        $this->assertNull(DataCategoryAccess::requiredPermission($category));
        $this->assertTrue(DataCategoryAccess::userCanView($category, null));
    }
}
