<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradientColorsToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->string('gradient_start_color', 20)->nullable()->default('#119A48')->after('spotlight_banner');
            $table->string('gradient_end_color', 20)->nullable()->default('#16c653')->after('gradient_start_color');
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
            $table->dropColumn(['gradient_start_color', 'gradient_end_color']);
        });
    }
}
