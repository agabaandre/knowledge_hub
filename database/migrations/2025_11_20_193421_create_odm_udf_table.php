<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdmUdfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_udf');
        Schema::create('odm_udf', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('table_name', 50)->nullable();
            $table->string('display_name', 16)->nullable();
            $table->integer('field_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_udf');
    }
}

