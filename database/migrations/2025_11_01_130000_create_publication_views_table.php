<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('publication_views')) {
            Schema::create('publication_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('publication_id');
                $table->year('year');
                $table->unsignedTinyInteger('month'); // 1-12
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();

                $table->unique(['publication_id', 'year', 'month'], 'publication_year_month_unique');
                $table->index(['publication_id', 'year', 'month']);
            });

            // Add foreign key constraint separately - handle type mismatch gracefully
            // Check if publication table exists and has compatible id column
            if (Schema::hasTable('publication')) {
                try {
                    DB::statement('ALTER TABLE publication_views ADD CONSTRAINT publication_views_publication_id_foreign FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE');
                } catch (\Exception $e) {
                    // If foreign key fails due to type mismatch, continue without it
                    // Data integrity will be maintained at application level
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_views');
    }
};

