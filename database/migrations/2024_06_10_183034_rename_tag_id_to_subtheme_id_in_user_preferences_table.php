<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTagIdToSubthemeIdInUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            //
            $table->renameColumn("tag_id","subtheme_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subtheme_id_in_user_preferences', function (Blueprint $table) {
            //
        });
    }
}
