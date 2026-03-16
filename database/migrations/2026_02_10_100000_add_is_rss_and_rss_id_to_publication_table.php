<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (!Schema::hasColumn('publication', 'is_rss')) {
                $table->boolean('is_rss')->nullable()->after('is_featured');
            }
            if (!Schema::hasColumn('publication', 'rss_id')) {
                $table->unsignedBigInteger('rss_id')->nullable()->after('is_rss');
            }
        });
    }

    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'rss_id')) {
                $table->dropColumn('rss_id');
            }
            if (Schema::hasColumn('publication', 'is_rss')) {
                $table->dropColumn('is_rss');
            }
        });
    }
};
