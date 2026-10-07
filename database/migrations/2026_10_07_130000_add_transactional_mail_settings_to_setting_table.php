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

        // setting is a wide single-row table; VARCHAR additions trip InnoDB's ~8KB
        // in-row limit. Prefer DYNAMIC + TEXT (off-page) before adding more fields.
        try {
            DB::statement('ALTER TABLE `setting` ROW_FORMAT=DYNAMIC');
        } catch (\Throwable $e) {
            // Ignore engines/hosts that reject the ROW_FORMAT clause.
        }

        foreach (['mail_http_base_url', 'mail_http_client_id'] as $column) {
            if (Schema::hasColumn('setting', $column)) {
                DB::statement("ALTER TABLE `setting` MODIFY `{$column}` TEXT NULL");
            }
        }

        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'mail_api_key')) {
                $after = Schema::hasColumn('setting', 'mail_http_client_secret')
                    ? 'mail_http_client_secret'
                    : (Schema::hasColumn('setting', 'exchange_auth_method') ? 'exchange_auth_method' : null);
                $col = $table->text('mail_api_key')->nullable();
                if ($after) {
                    $col->after($after);
                }
            }
            if (! Schema::hasColumn('setting', 'mail_api_secret')) {
                $table->text('mail_api_secret')->nullable()->after('mail_api_key');
            }
            if (! Schema::hasColumn('setting', 'mail_api_domain')) {
                $table->text('mail_api_domain')->nullable()->after('mail_api_secret');
            }
            if (! Schema::hasColumn('setting', 'mail_api_region')) {
                $table->text('mail_api_region')->nullable()->after('mail_api_domain');
            }
            if (! Schema::hasColumn('setting', 'mail_api_base_url')) {
                $table->text('mail_api_base_url')->nullable()->after('mail_api_region');
            }
            if (! Schema::hasColumn('setting', 'mail_api_message_stream')) {
                $table->text('mail_api_message_stream')->nullable()->after('mail_api_base_url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $columns = [
            'mail_api_key',
            'mail_api_secret',
            'mail_api_domain',
            'mail_api_region',
            'mail_api_base_url',
            'mail_api_message_stream',
        ];

        Schema::table('setting', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
