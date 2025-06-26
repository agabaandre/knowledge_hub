<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorldCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('world_countries', function (Blueprint $table) {
            $table->id();
            $table->char('iso', 2)->unique();
            $table->string('name', 80);
            $table->string('nicename', 80);
            $table->char('iso3', 3)->nullable()->unique();
            $table->integer('numcode')->nullable()->unique();
            $table->char('phonecode', 5)->nullable();
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
        Schema::dropIfExists('world_countries');
    }
}
