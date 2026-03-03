<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_category');
        Schema::create('odm_category', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->string('name', 255)->default('');
        });
        
        // Insert initial data
        DB::table('odm_category')->insert([
            ['name' => 'SOP'],
            ['name' => 'Training Manual'],
            ['name' => 'Letter'],
            ['name' => 'Presentation'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_category');
    }
}

