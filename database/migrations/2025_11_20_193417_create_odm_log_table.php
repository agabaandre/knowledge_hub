<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_log');
        Schema::create('odm_log', function (Blueprint $table) {
            $table->integer('id')->unsigned()->default(0);
            $table->dateTime('modified_on')->default('1000-01-01 00:00:00');
            $table->string('modified_by', 25)->nullable();
            $table->text('note')->nullable();
            $table->string('revision', 255)->nullable();
            
            $table->index('id');
            $table->index('modified_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_log');
    }
}

