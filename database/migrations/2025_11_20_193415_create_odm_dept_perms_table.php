<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmDeptPermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_dept_perms');
        Schema::create('odm_dept_perms', function (Blueprint $table) {
            $table->integer('fid')->unsigned()->nullable();
            $table->integer('dept_id')->unsigned()->nullable();
            $table->tinyInteger('rights')->default(0);
            
            $table->index('rights');
            $table->index('dept_id');
            $table->index('fid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_dept_perms');
    }
}

