<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManualFieldsToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('summary');
            $table->text('content')->nullable()->after('provider');
            $table->string('course_url')->after('content');
            $table->boolean('is_moodle')->default(false)->after('course_url');
            $table->boolean('is_active')->default(true)->after('is_moodle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['provider', 'content', 'course_url', 'is_moodle', 'is_active']);
        });
    }
} 