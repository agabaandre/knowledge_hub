<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministrativeUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrative_units', function (Blueprint $table) {
            $table->id();
            $table->string('name',300);
            $table->string('description',300)->nullable();
            $table->integer('parent_id');
            $table->string('code',50)->nullable();
            $table->string('alternate_code',50)->nullable();
            $table->string('logo',50)->nullable();
            $table->string('icon',50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrative_units');
    }
}
