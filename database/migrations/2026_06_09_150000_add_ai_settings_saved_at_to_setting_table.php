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
            if (! Schema::hasColumn('setting', 'ai_settings_saved_at')) {
                $table->timestamp('ai_settings_saved_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'ai_settings_saved_at')) {
                $table->dropColumn('ai_settings_saved_at');
            }
        });
    }
};
