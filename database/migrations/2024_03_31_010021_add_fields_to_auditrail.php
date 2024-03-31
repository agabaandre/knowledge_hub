<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAuditrail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_trails', function (Blueprint $table) {
            
            $table->dropColumn('start');
            $table->dropColumn('end');

            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auditrail', function (Blueprint $table) {
            //
        });
    }
}
