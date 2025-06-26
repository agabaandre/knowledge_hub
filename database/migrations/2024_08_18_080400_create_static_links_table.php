<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticLinksTable extends Migration
{
    public function up()
    {
        Schema::create('static_links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('order');
            $table->string('link');
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('static_links');
    }
} 