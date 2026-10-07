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

        // TEXT avoids InnoDB "Row size too large" on the already-wide setting row.
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'mail_http_base_url')) {
                $table->text('mail_http_base_url')->nullable()->after('exchange_auth_method');
            }
            if (! Schema::hasColumn('setting', 'mail_http_client_id')) {
                $table->text('mail_http_client_id')->nullable()->after('mail_http_base_url');
            }
            if (! Schema::hasColumn('setting', 'mail_http_client_secret')) {
                $table->text('mail_http_client_secret')->nullable()->after('mail_http_client_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $columns = [
            'mail_http_base_url',
            'mail_http_client_id',
            'mail_http_client_secret',
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
