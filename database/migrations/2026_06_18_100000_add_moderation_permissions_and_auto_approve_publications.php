<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting') && ! Schema::hasColumn('setting', 'auto_approve_publications')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('auto_approve_publications')->default(0)->after('auto_approve_comments');
            });
        }

        $permissions = [
            'moderate_cop_participants' => 'Approve or reject community of practice participants',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $moderationPermissions = [
            'moderate_publication',
            'moderate_forum',
            'moderate_cop_participants',
        ];

        foreach (['admin', 'Administrator'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }
            foreach ($moderationPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->where('guard_name', 'web')->first();
                if ($permission && ! $role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting') && Schema::hasColumn('setting', 'auto_approve_publications')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('auto_approve_publications');
            });
        }

        $permission = Permission::where('name', 'moderate_cop_participants')->where('guard_name', 'web')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
