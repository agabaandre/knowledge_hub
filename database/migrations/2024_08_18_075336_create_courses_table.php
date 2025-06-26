<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_id')->unique();
            $table->string('fullname');
            $table->string('shortname');
            $table->string('cover_image')->nullable();
            $table->integer('category_id');
            $table->text('summary')->nullable();
            $table->string('provider')->nullable();
            $table->text('content')->nullable();
            $table->string('course_url');
            $table->boolean('is_moodle')->default(false);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('courses');
    }
}
