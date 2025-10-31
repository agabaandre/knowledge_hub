<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'show_featured')) {
                $table->boolean('show_featured')->default(false);
            }
            if (!Schema::hasColumn('setting', 'show_events')) {
                $table->boolean('show_events')->default(false);
            }
            if (!Schema::hasColumn('setting', 'show_top_searches')) {
                $table->boolean('show_top_searches')->default(false);
            }
            if (!Schema::hasColumn('setting', 'show_tags')) {
                $table->boolean('show_tags')->default(false);
            }
            if (!Schema::hasColumn('setting', 'show_quotes')) {
                $table->boolean('show_quotes')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'show_featured')) {
                $table->dropColumn('show_featured');
            }
            if (Schema::hasColumn('setting', 'show_events')) {
                $table->dropColumn('show_events');
            }
            if (Schema::hasColumn('setting', 'show_top_searches')) {
                $table->dropColumn('show_top_searches');
            }
            if (Schema::hasColumn('setting', 'show_tags')) {
                $table->dropColumn('show_tags');
            }
            if (Schema::hasColumn('setting', 'show_quotes')) {
                $table->dropColumn('show_quotes');
            }
        });
    }
};


