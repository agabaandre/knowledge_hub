<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentIdToPdfChatSessionsTable extends Migration
{
    public function up()
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('attachment_id')->nullable()->after('publication_id');
            $table->index(['user_id', 'publication_id', 'attachment_id']);
        });
    }

    public function down()
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'publication_id', 'attachment_id']);
            $table->dropColumn('attachment_id');
        });
    }
}
