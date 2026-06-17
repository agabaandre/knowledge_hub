<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_chat_sessions', 'pdf_selection_key')) {
                $table->string('pdf_selection_key', 255)->nullable()->after('attachment_id');
                $table->index(['user_id', 'publication_id', 'pdf_selection_key'], 'pdf_chat_sessions_user_pub_selection_idx');
            }
            if (! Schema::hasColumn('pdf_chat_sessions', 'chatpdf_meta')) {
                $table->json('chatpdf_meta')->nullable()->after('pdf_selection_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('pdf_chat_sessions', 'pdf_selection_key')) {
                $table->dropIndex('pdf_chat_sessions_user_pub_selection_idx');
                $table->dropColumn('pdf_selection_key');
            }
            if (Schema::hasColumn('pdf_chat_sessions', 'chatpdf_meta')) {
                $table->dropColumn('chatpdf_meta');
            }
        });
    }
};
