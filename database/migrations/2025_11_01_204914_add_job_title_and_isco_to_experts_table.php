<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobTitleAndIscoToExpertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->unsignedBigInteger('job_title_id')->nullable()->after('job_title');
            $table->unsignedBigInteger('isco_classification_id')->nullable()->after('job_title_id');
            
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('set null');
            $table->foreign('isco_classification_id')->references('id')->on('isco_classifications')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->dropForeign(['job_title_id']);
            $table->dropForeign(['isco_classification_id']);
            $table->dropColumn(['job_title_id', 'isco_classification_id']);
        });
    }
}
