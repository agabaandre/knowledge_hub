<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('data_category_publication_category')) {
            Schema::create('data_category_publication_category', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('data_category_id');
                $table->unsignedBigInteger('publication_category_id');
                $table->timestamps();

                $table->index('data_category_id', 'dc_pc_data_category_idx');
                $table->index('publication_category_id', 'dc_pc_publication_category_idx');
                $table->unique(['data_category_id', 'publication_category_id'], 'dc_pc_unique');
            });
        }

        // Default behavior: each publication category is linked to all data categories.
        if (Schema::hasTable('data_categories') && Schema::hasTable('publication_categories')) {
            $dataCategoryIds = DB::table('data_categories')->pluck('id');
            $publicationCategoryIds = DB::table('publication_categories')->whereNull('parent_id')->pluck('id');

            foreach ($dataCategoryIds as $dataCategoryId) {
                foreach ($publicationCategoryIds as $publicationCategoryId) {
                    DB::table('data_category_publication_category')->updateOrInsert(
                        [
                            'data_category_id' => $dataCategoryId,
                            'publication_category_id' => $publicationCategoryId,
                        ],
                        [
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('data_category_publication_category');
    }
};

