<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (! Schema::hasColumn('setting', 'block_disposable_email_registration')) {
                if (Schema::hasColumn('setting', 'allow_email_password_accounts_social_login')) {
                    $table->boolean('block_disposable_email_registration')
                        ->default(true)
                        ->after('allow_email_password_accounts_social_login');
                } else {
                    $table->boolean('block_disposable_email_registration')->default(true);
                }
            }
            if (! Schema::hasColumn('setting', 'blocked_email_domains')) {
                $table->text('blocked_email_domains')->nullable();
            }
        });

        if (Schema::hasColumn('setting', 'block_disposable_email_registration')) {
            DB::table('setting')
                ->whereNull('block_disposable_email_registration')
                ->update(['block_disposable_email_registration' => 1]);
        }

        if (Schema::hasTable('setting_key_groups') && Schema::hasColumn('setting', 'blocked_email_domains')) {
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'block_disposable_email_registration'],
                [
                    'group_name' => 'Advanced',
                    'subgroup_name' => 'Authentication',
                    'sort_order' => 74,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            DB::table('setting_key_groups')->updateOrInsert(
                ['setting_key' => 'blocked_email_domains'],
                [
                    'group_name' => 'Advanced',
                    'subgroup_name' => 'Authentication',
                    'sort_order' => 75,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'block_disposable_email_registration')) {
                $table->dropColumn('block_disposable_email_registration');
            }
            if (Schema::hasColumn('setting', 'blocked_email_domains')) {
                $table->dropColumn('blocked_email_domains');
            }
        });

        if (Schema::hasTable('setting_key_groups')) {
            DB::table('setting_key_groups')
                ->whereIn('setting_key', ['block_disposable_email_registration', 'blocked_email_domains'])
                ->delete();
        }
    }
};
