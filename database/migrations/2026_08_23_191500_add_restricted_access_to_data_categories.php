<?php

use App\Support\DataCategoryAccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('data_categories') && ! Schema::hasColumn('data_categories', 'is_restricted')) {
            Schema::table('data_categories', function (Blueprint $table) {
                $table->boolean('is_restricted')->default(false);
            });
        }

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        $permission = Permission::findOrCreate(DataCategoryAccess::DEFAULT_PERMISSION, 'web');

        foreach (['Admin', 'RCC Admin'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        if (! Schema::hasColumn('data_categories', 'required_permission')) {
            return;
        }

        $query = DB::table('data_categories')
            ->where(function ($q) {
                $q->where('category_name', 'like', '%workforce%')
                    ->orWhere('slug', 'like', '%workforce%');
            });

        $values = ['required_permission' => DataCategoryAccess::DEFAULT_PERMISSION];
        if (Schema::hasColumn('data_categories', 'is_restricted')) {
            $values['is_restricted'] = 1;
        }
        $query->update($values);
    }

    public function down(): void
    {
        if (Schema::hasTable('data_categories') && Schema::hasColumn('data_categories', 'is_restricted')) {
            Schema::table('data_categories', function (Blueprint $table) {
                $table->dropColumn('is_restricted');
            });
        }
    }
};
