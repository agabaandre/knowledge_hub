<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdf_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdf_chat_session_id')->constrained('pdf_chat_sessions')->cascadeOnDelete();
            $table->string('role', 20); // user | assistant
            $table->longText('content');
            $table->timestamps();
            $table->index('pdf_chat_session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdf_chat_messages');
    }
}
