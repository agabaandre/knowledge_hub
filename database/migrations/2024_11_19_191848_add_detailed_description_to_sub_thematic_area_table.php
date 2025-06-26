<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailedDescriptionToSubThematicAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_thematic_area', function (Blueprint $table) {
            $table->text('detailed_description')->nullable()->after('description')->comment('Detailed description of the thematic area');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_thematic_area', function (Blueprint $table) {
            $table->dropColumn('detailed_description');
        });
    }
}
