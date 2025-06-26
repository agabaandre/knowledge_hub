<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('venue')->nullable();
            $table->timestamp('startdate')->nullable();
            $table->timestamp('enddate')->nullable();
            $table->string('organized_by')->nullable();
            $table->decimal('fee', 8, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('event_link')->nullable();
            $table->string('registration_link')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('contact_person')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->foreignId('country_id')->nullable();
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
        Schema::dropIfExists('events');
    }
}
