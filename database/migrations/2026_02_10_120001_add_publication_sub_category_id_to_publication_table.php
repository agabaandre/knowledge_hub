<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('publication', 'publication_sub_category_id')) {
            return;
        }
        Schema::table('publication', function (Blueprint $table) {
            $table->foreignId('publication_sub_category_id')->nullable()->after('publication_catgory_id')->constrained('publication_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            $table->dropForeign(['publication_sub_category_id']);
        });
    }
};
