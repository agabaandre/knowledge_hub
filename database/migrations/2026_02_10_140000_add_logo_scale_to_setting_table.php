<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('setting', 'logo_scale')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->unsignedSmallInteger('logo_scale')->default(80)->after('site_theme');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('setting', 'logo_scale')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('logo_scale');
            });
        }
    }
};
