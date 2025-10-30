<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('publication', 'year_published')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->integer('year_published')->nullable()->index()->after('publication');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('publication', 'year_published')) {
            Schema::table('publication', function (Blueprint $table) {
                $table->dropColumn('year_published');
            });
        }
    }
};

