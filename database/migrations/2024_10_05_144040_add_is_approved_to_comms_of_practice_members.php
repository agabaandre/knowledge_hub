<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsApprovedToCommsOfPracticeMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('community_of_practice_members', function (Blueprint $table) {
            
            $table->boolean("is_approved")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('community_of_practice_members', function (Blueprint $table) {
            //
        });
    }
}
