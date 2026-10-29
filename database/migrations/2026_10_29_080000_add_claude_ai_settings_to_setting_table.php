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
            if (! Schema::hasColumn('setting', 'ai_claude_api_key')) {
                $table->text('ai_claude_api_key')->nullable();
            }
            if (! Schema::hasColumn('setting', 'ai_claude_model')) {
                $table->string('ai_claude_model', 120)->nullable();
            }
            if (! Schema::hasColumn('setting', 'ai_claude_enabled')) {
                $table->boolean('ai_claude_enabled')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            foreach (['ai_claude_enabled', 'ai_claude_model', 'ai_claude_api_key'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
