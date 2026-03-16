<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmOdmsysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_odmsys');
        Schema::create('odm_odmsys', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('sys_name', 16)->nullable();
            $table->string('sys_value', 255)->nullable();
        });
        
        // Insert initial data
        DB::table('odm_odmsys')->insert([
            'sys_name' => 'version',
            'sys_value' => '1.4.0',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_odmsys');
    }
}

