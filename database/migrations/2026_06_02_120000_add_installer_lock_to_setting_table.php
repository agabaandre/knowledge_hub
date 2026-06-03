<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'installer_locked')) {
                $table->boolean('installer_locked')->default(false)->after('status');
            }
            if (! Schema::hasColumn('setting', 'installer_completed_at')) {
                $table->timestamp('installer_completed_at')->nullable()->after('installer_locked');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'installer_completed_at')) {
                $table->dropColumn('installer_completed_at');
            }
            if (Schema::hasColumn('setting', 'installer_locked')) {
                $table->dropColumn('installer_locked');
            }
        });
    }
};
