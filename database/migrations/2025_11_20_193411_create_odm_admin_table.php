<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_admin');
        Schema::create('odm_admin', function (Blueprint $table) {
            $table->integer('id')->unsigned()->nullable();
            $table->tinyInteger('admin')->nullable();
        });
        
        // Insert initial data
        DB::table('odm_admin')->insert([
            'id' => 1,
            'admin' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_admin');
    }
}

