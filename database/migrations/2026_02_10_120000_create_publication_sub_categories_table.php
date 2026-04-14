<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add parent_id to publication_categories so sub-categories live in the same table.
     * Rows with parent_id = null are top-level categories; rows with parent_id set are sub-categories.
     */
    public function up(): void
    {
        Schema::table('publication_categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('publication_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('publication_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
    }
};
