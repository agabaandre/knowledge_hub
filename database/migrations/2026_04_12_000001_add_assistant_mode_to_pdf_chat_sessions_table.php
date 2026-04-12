<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_chat_sessions', 'assistant_mode')) {
                $table->string('assistant_mode', 32)->default('chatpdf')->after('source_id');
            }
        });
        if (Schema::hasColumn('pdf_chat_sessions', 'assistant_mode')) {
            DB::table('pdf_chat_sessions')->whereNull('assistant_mode')->update(['assistant_mode' => 'chatpdf']);
        }
    }

    public function down(): void
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('pdf_chat_sessions', 'assistant_mode')) {
                $table->dropColumn('assistant_mode');
            }
        });
    }
};
