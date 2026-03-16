<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmDeptReviewerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_dept_reviewer');
        Schema::create('odm_dept_reviewer', function (Blueprint $table) {
            $table->integer('dept_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
        });
        
        // Insert initial data
        DB::table('odm_dept_reviewer')->insert([
            'dept_id' => 1,
            'user_id' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_dept_reviewer');
    }
}

