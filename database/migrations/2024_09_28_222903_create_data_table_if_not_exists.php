<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTableIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the 'data' table exists before creating it
        if (!Schema::hasTable('data')) {
            Schema::create('data', function (Blueprint $table) {
                $table->bigIncrements('id'); // Auto-incrementing BIGINT primary key
                $table->unsignedBigInteger('kpi_id'); // Foreign key for KPI
                $table->unsignedBigInteger('country_id'); // Foreign key for Country
                $table->string('period'); // Period (string, can be changed as per need)
                $table->decimal('value', 15, 2); // Value (with two decimal places)
                $table->timestamps(); // Created at and updated at timestamps
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data');
    }
}
