<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityOfPracticeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_of_practice_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_of_practice_id')
            ->constrained('community_of_practices','id')
            ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users','id')
                ->onDelete('cascade');
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
        Schema::dropIfExists('community_of_practice_members');
    }
}
