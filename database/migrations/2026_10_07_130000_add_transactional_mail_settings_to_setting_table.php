<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        // The setting row is already near InnoDB's ~8KB in-row limit. Six TEXT
        // columns still fail on Antelope/COMPACT (large in-row prefixes). Use one
        // JSON/TEXT blob after forcing DYNAMIC and shrinking wide VARCHARs.
        $this->prepareWideSettingTable();

        if (Schema::hasColumn('setting', 'mail_api_config')) {
            return;
        }

        // Legacy path: environments that already got per-field columns.
        if (Schema::hasColumn('setting', 'mail_api_key')) {
            return;
        }

        Schema::table('setting', function (Blueprint $table) {
            $after = Schema::hasColumn('setting', 'mail_http_client_secret')
                ? 'mail_http_client_secret'
                : (Schema::hasColumn('setting', 'exchange_auth_method') ? 'exchange_auth_method' : null);
            $col = $table->mediumText('mail_api_config')->nullable();
            if ($after) {
                $col->after($after);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        if (Schema::hasColumn('setting', 'mail_api_config')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('mail_api_config');
            });
        }
    }

    private function prepareWideSettingTable(): void
    {
        try {
            DB::statement('ALTER TABLE `setting` ROW_FORMAT=DYNAMIC');
        } catch (\Throwable $e) {
            // Ignore hosts that reject ROW_FORMAT.
        }

        $toText = [
            'mail_http_base_url',
            'mail_http_client_id',
            'mail_host',
            'mail_username',
            'mail_from_address',
            'mail_from_name',
            'exchange_tenant_id',
            'exchange_client_id',
            'exchange_redirect_uri',
            'exchange_scope',
            'logo',
            'favicon',
            'moodle_api_url',
            'moodle_api_token',
            'moodle_base_url',
            'frappe_base_url',
            'frappe_api_key',
            'frappe_api_secret',
            'frappe_course_doctype',
            'openedx_lms_url',
            'openedx_client_id',
            'openedx_client_secret',
            'openedx_token_url',
            'central_hub_url',
            'central_hub_api_token',
            'content_disclaimer',
        ];

        foreach ($toText as $column) {
            if (! Schema::hasColumn('setting', $column)) {
                continue;
            }

            try {
                DB::statement("ALTER TABLE `setting` MODIFY `{$column}` TEXT NULL");
            } catch (\Throwable $e) {
                // Keep going; some columns may already be TEXT or non-nullable oddly.
            }
        }
    }
};
