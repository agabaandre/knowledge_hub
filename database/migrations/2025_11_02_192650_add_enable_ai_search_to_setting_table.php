<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnableAiSearchToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'enable_ai_search')) {
                $table->boolean('enable_ai_search')->default(1)->after('auto_approve_comments');
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
            if (Schema::hasColumn('setting', 'enable_ai_search')) {
                $table->dropColumn('enable_ai_search');
            }
        });
    }
}
