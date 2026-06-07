<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'frappe_base_url')) {
                $table->string('frappe_base_url', 500)->nullable()->after('moodle_sync_enabled');
            }
            if (! Schema::hasColumn('setting', 'frappe_api_key')) {
                $table->string('frappe_api_key', 500)->nullable()->after('frappe_base_url');
            }
            if (! Schema::hasColumn('setting', 'frappe_api_secret')) {
                $table->string('frappe_api_secret', 500)->nullable()->after('frappe_api_key');
            }
            if (! Schema::hasColumn('setting', 'frappe_course_doctype')) {
                $table->string('frappe_course_doctype', 120)->nullable()->after('frappe_api_secret');
            }
            if (! Schema::hasColumn('setting', 'frappe_sync_enabled')) {
                $table->boolean('frappe_sync_enabled')->nullable()->default(false)->after('frappe_course_doctype');
            }
            if (! Schema::hasColumn('setting', 'openedx_lms_url')) {
                $table->string('openedx_lms_url', 500)->nullable()->after('frappe_sync_enabled');
            }
            if (! Schema::hasColumn('setting', 'openedx_client_id')) {
                $table->string('openedx_client_id', 500)->nullable()->after('openedx_lms_url');
            }
            if (! Schema::hasColumn('setting', 'openedx_client_secret')) {
                $table->string('openedx_client_secret', 500)->nullable()->after('openedx_client_id');
            }
            if (! Schema::hasColumn('setting', 'openedx_token_url')) {
                $table->string('openedx_token_url', 500)->nullable()->after('openedx_client_secret');
            }
            if (! Schema::hasColumn('setting', 'openedx_sync_enabled')) {
                $table->boolean('openedx_sync_enabled')->nullable()->default(false)->after('openedx_token_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            foreach ([
                'openedx_sync_enabled',
                'openedx_token_url',
                'openedx_client_secret',
                'openedx_client_id',
                'openedx_lms_url',
                'frappe_sync_enabled',
                'frappe_course_doctype',
                'frappe_api_secret',
                'frappe_api_key',
                'frappe_base_url',
            ] as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
