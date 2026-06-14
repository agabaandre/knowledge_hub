<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (! Schema::hasColumn('publication', 'publication_language')) {
                $table->string('publication_language', 32)->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'publication_language')) {
                $table->dropColumn('publication_language');
            }
        });
    }
};
