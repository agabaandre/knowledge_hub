<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmUserPermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_user_perms');
        Schema::create('odm_user_perms', function (Blueprint $table) {
            $table->integer('fid')->unsigned()->nullable();
            $table->integer('uid')->unsigned()->default(0);
            $table->tinyInteger('rights')->default(0);
            
            $table->index(['fid', 'uid', 'rights'], 'user_perms_idx');
            $table->index('fid');
            $table->index('uid');
            $table->index('rights');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_user_perms');
    }
}

