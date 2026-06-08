<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('thematic_area') && ! Schema::hasColumn('thematic_area', 'slug')) {
            Schema::table('thematic_area', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('description');
                $table->unique('slug', 'thematic_area_slug_unique');
            });
        }

        if (Schema::hasTable('sub_thematic_area') && ! Schema::hasColumn('sub_thematic_area', 'slug')) {
            Schema::table('sub_thematic_area', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('description');
                $table->unique('slug', 'sub_thematic_area_slug_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('thematic_area') && Schema::hasColumn('thematic_area', 'slug')) {
            Schema::table('thematic_area', function (Blueprint $table) {
                $table->dropUnique('thematic_area_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasTable('sub_thematic_area') && Schema::hasColumn('sub_thematic_area', 'slug')) {
            Schema::table('sub_thematic_area', function (Blueprint $table) {
                $table->dropUnique('sub_thematic_area_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }
};
