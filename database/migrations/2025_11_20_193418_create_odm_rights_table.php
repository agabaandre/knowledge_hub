<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_rights');
        Schema::create('odm_rights', function (Blueprint $table) {
            $table->tinyInteger('RightId')->nullable();
            $table->string('Description', 255)->nullable();
        });
        
        // Insert initial data
        DB::table('odm_rights')->insert([
            ['RightId' => 0, 'Description' => 'none'],
            ['RightId' => 1, 'Description' => 'view'],
            ['RightId' => -1, 'Description' => 'forbidden'],
            ['RightId' => 2, 'Description' => 'read'],
            ['RightId' => 3, 'Description' => 'write'],
            ['RightId' => 4, 'Description' => 'admin'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_rights');
    }
}

