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
        if (!Schema::hasColumn('setting', 'show_publication_card_file_type_badge')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->boolean('show_publication_card_file_type_badge')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('setting', 'show_publication_card_file_type_badge')) {
            Schema::table('setting', function (Blueprint $table) {
                $table->dropColumn('show_publication_card_file_type_badge');
            });
        }
    }
};
