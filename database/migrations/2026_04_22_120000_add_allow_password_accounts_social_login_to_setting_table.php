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
            if (!Schema::hasColumn('setting', 'allow_email_password_accounts_social_login')) {
                if (Schema::hasColumn('setting', 'enable_linkedin_login')) {
                    $table->boolean('allow_email_password_accounts_social_login')
                        ->default(true)
                        ->after('enable_linkedin_login');
                } else {
                    $table->boolean('allow_email_password_accounts_social_login')->default(true);
                }
            }
        });

        if (Schema::hasColumn('setting', 'allow_email_password_accounts_social_login')) {
            DB::table('setting')
                ->whereNull('allow_email_password_accounts_social_login')
                ->update(['allow_email_password_accounts_social_login' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'allow_email_password_accounts_social_login')) {
                $table->dropColumn('allow_email_password_accounts_social_login');
            }
        });
    }
};
