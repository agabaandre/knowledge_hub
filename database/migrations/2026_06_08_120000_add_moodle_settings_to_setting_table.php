<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'moodle_api_url')) {
                $table->string('moodle_api_url', 500)->nullable()->after('federation_api_token');
            }
            if (! Schema::hasColumn('setting', 'moodle_api_token')) {
                $table->string('moodle_api_token', 500)->nullable()->after('moodle_api_url');
            }
            if (! Schema::hasColumn('setting', 'moodle_base_url')) {
                $table->string('moodle_base_url', 500)->nullable()->after('moodle_api_token');
            }
            if (! Schema::hasColumn('setting', 'moodle_sync_enabled')) {
                $table->boolean('moodle_sync_enabled')->nullable()->default(true)->after('moodle_base_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach (['moodle_sync_enabled', 'moodle_base_url', 'moodle_api_token', 'moodle_api_url'] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
