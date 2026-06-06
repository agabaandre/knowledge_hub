<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('country') && ! Schema::hasColumn('country', 'slug')) {
            Schema::table('country', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('name');
                $table->unique('slug', 'country_slug_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('country') && Schema::hasColumn('country', 'slug')) {
            Schema::table('country', function (Blueprint $table) {
                $table->dropUnique('country_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }
};
