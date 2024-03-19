<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->string('iso_code', 2)->nullable()->after('name');
            $table->string('iso3_code', 3)->nullable()->after('iso_code');
            $table->smallInteger('numcode')->nullable()->after('iso3_code');
            $table->string('phonecode')->nullable()->after('numcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->dropColumn('iso_code');
            $table->dropColumn('iso3_code');
            $table->dropColumn('numcode');
            $table->dropColumn('phonecode');
        });
    }
}
