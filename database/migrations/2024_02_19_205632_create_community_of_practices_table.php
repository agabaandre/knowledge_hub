<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityOfPracticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_of_practices', function (Blueprint $table) {
            $table->id();
            $table->string("community_name");
            $table->string('description',1000)->nullable();
            $table->foreignId('created_by')
                ->constrained('users','id')
                ->onDelete('cascade');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('community_of_practices');
    }
}
