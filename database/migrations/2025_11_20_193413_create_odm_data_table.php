<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_data');
        Schema::create('odm_data', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->integer('category')->unsigned()->default(0);
            $table->integer('owner')->unsigned()->nullable();
            $table->string('realname', 255)->default('');
            $table->dateTime('created')->default('1000-01-01 00:00:00');
            $table->string('description', 255)->nullable();
            $table->string('comment', 255)->default('');
            $table->smallInteger('status')->nullable();
            $table->smallInteger('department')->unsigned()->nullable();
            $table->tinyInteger('default_rights')->nullable();
            $table->tinyInteger('publishable')->nullable();
            $table->integer('reviewer')->unsigned()->nullable();
            $table->string('reviewer_comments', 255)->nullable();
            
            $table->index(['id', 'owner'], 'data_idx');
            $table->index('publishable');
            // Note: Original SQL uses prefix index on description(200), 
            // but Laravel doesn't support prefix indexes directly
            $table->index('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_data');
    }
}

