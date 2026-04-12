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
            if (! Schema::hasColumn('pdf_chat_sessions', 'forum_id')) {
                $table->unsignedBigInteger('forum_id')->nullable()->after('publication_id');
                $table->index(['user_id', 'forum_id']);
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if (Schema::hasColumn('pdf_chat_sessions', 'publication_id')) {
            if ($driver === 'mysql') {
                try {
                    DB::statement('ALTER TABLE pdf_chat_sessions MODIFY publication_id BIGINT UNSIGNED NULL');
                } catch (\Throwable $e) {
                    // ignore if already nullable or permissions
                }
            } elseif (in_array($driver, ['pgsql', 'sqlite'], true)) {
                try {
                    Schema::table('pdf_chat_sessions', function (Blueprint $table) {
                        $table->unsignedBigInteger('publication_id')->nullable()->change();
                    });
                } catch (\Throwable $e) {
                    // ignore if already nullable or change unsupported
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('pdf_chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('pdf_chat_sessions', 'forum_id')) {
                try {
                    $table->dropIndex(['user_id', 'forum_id']);
                } catch (\Throwable $e) {
                }
                $table->dropColumn('forum_id');
            }
        });
    }
};
