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
            if (! Schema::hasColumn('setting', 'email_driver')) {
                $table->string('email_driver', 20)->default('exchange')->after('email');
            }
            if (! Schema::hasColumn('setting', 'mail_host')) {
                $table->string('mail_host', 255)->nullable()->after('email_driver');
            }
            if (! Schema::hasColumn('setting', 'mail_port')) {
                $table->string('mail_port', 10)->nullable()->after('mail_host');
            }
            if (! Schema::hasColumn('setting', 'mail_username')) {
                $table->string('mail_username', 255)->nullable()->after('mail_port');
            }
            if (! Schema::hasColumn('setting', 'mail_password')) {
                $table->text('mail_password')->nullable()->after('mail_username');
            }
            if (! Schema::hasColumn('setting', 'mail_encryption')) {
                $table->string('mail_encryption', 10)->nullable()->after('mail_password');
            }
            if (! Schema::hasColumn('setting', 'mail_from_address')) {
                $table->string('mail_from_address', 255)->nullable()->after('mail_encryption');
            }
            if (! Schema::hasColumn('setting', 'mail_from_name')) {
                $table->string('mail_from_name', 255)->nullable()->after('mail_from_address');
            }
            if (! Schema::hasColumn('setting', 'exchange_tenant_id')) {
                $table->string('exchange_tenant_id', 255)->nullable()->after('mail_from_name');
            }
            if (! Schema::hasColumn('setting', 'exchange_client_id')) {
                $table->string('exchange_client_id', 255)->nullable()->after('exchange_tenant_id');
            }
            if (! Schema::hasColumn('setting', 'exchange_client_secret')) {
                $table->text('exchange_client_secret')->nullable()->after('exchange_client_id');
            }
            if (! Schema::hasColumn('setting', 'exchange_redirect_uri')) {
                $table->string('exchange_redirect_uri', 500)->nullable()->after('exchange_client_secret');
            }
            if (! Schema::hasColumn('setting', 'exchange_scope')) {
                $table->string('exchange_scope', 500)->nullable()->after('exchange_redirect_uri');
            }
            if (! Schema::hasColumn('setting', 'exchange_auth_method')) {
                $table->string('exchange_auth_method', 40)->nullable()->after('exchange_scope');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $columns = [
            'email_driver',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'exchange_tenant_id',
            'exchange_client_id',
            'exchange_client_secret',
            'exchange_redirect_uri',
            'exchange_scope',
            'exchange_auth_method',
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
