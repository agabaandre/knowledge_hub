<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuColorsToSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->string('au_red', 20)->nullable()->default('#9F2241')->after('gradient_end_color');
            $table->string('au_gold', 20)->nullable()->default('#B4A269')->after('au_red');
            $table->string('au_corporate_green', 20)->nullable()->default('#1A5632')->after('au_gold');
            $table->string('au_green', 20)->nullable()->default('#1A5632')->after('au_corporate_green');
            $table->string('au_plum', 20)->nullable()->default('#522B39')->after('au_green');
            $table->string('au_grey_text', 20)->nullable()->default('#58595B')->after('au_plum');
            $table->string('au_white', 20)->nullable()->default('#FFFFFF')->after('au_grey_text');
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
            $table->dropColumn(['au_red', 'au_gold', 'au_corporate_green', 'au_green', 'au_plum', 'au_grey_text', 'au_white']);
        });
    }
}
