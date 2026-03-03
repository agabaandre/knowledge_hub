<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmAccessLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_access_log');
        Schema::create('odm_access_log', function (Blueprint $table) {
            $table->integer('file_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamp('timestamp')->useCurrent()->useCurrentOnUpdate();
            $table->enum('action', ['A', 'B', 'C', 'V', 'D', 'M', 'X', 'I', 'O', 'Y', 'R']);
            
            $table->index(['file_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_access_log');
    }
}

