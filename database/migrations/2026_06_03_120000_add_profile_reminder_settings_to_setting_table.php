<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileReminderSettingsToSettingTable extends Migration
{
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'auto_profile_completion_reminder')) {
                $table->boolean('auto_profile_completion_reminder')->default(0)->after('enable_ai_chat_prune');
            }
            if (!Schema::hasColumn('setting', 'profile_reminder_day_of_month')) {
                $table->unsignedTinyInteger('profile_reminder_day_of_month')->default(1)->after('auto_profile_completion_reminder');
            }
        });
    }

    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'profile_reminder_day_of_month')) {
                $table->dropColumn('profile_reminder_day_of_month');
            }
            if (Schema::hasColumn('setting', 'auto_profile_completion_reminder')) {
                $table->dropColumn('auto_profile_completion_reminder');
            }
        });
    }
}
