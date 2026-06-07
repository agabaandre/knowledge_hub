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
            if (! Schema::hasColumn('setting', 'ai_feature_routing')) {
                $table->json('ai_feature_routing')->nullable();
            }
            if (! Schema::hasColumn('setting', 'ai_custom_integrations')) {
                $table->json('ai_custom_integrations')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            foreach (['ai_custom_integrations', 'ai_feature_routing'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
