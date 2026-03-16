<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'quotes';
        if (!Schema::hasTable($table)) {
            return;
        }
        Schema::table('quotes', function (Blueprint $blueprint) {
            if (!Schema::hasColumn('quotes', 'image')) {
                $blueprint->string('image', 500)->nullable()->after('quote');
            }
            if (!Schema::hasColumn('quotes', 'link_url')) {
                $blueprint->string('link_url', 1000)->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        $table = 'quotes';
        if (!Schema::hasTable($table)) {
            return;
        }
        Schema::table('quotes', function (Blueprint $blueprint) {
            if (Schema::hasColumn('quotes', 'image')) {
                $blueprint->dropColumn('image');
            }
            if (Schema::hasColumn('quotes', 'link_url')) {
                $blueprint->dropColumn('link_url');
            }
        });
    }
};
