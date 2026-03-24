<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnableAiChatPruneToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'enable_ai_chat_prune')) {
                $table->boolean('enable_ai_chat_prune')->default(1)->after('enable_ai_search');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'enable_ai_chat_prune')) {
                $table->dropColumn('enable_ai_chat_prune');
            }
        });
    }
}
