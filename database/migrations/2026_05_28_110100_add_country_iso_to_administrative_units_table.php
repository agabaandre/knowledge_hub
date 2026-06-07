<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('administrative_units')) {
            return;
        }

        Schema::table('administrative_units', function (Blueprint $table) {
            if (! Schema::hasColumn('administrative_units', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('parent_id');
            }
            if (! Schema::hasColumn('administrative_units', 'iso_code')) {
                $table->string('iso_code', 2)->nullable()->after('country_id');
            }
            if (! Schema::hasColumn('administrative_units', 'iso3_code')) {
                $table->string('iso3_code', 3)->nullable()->after('iso_code');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('administrative_units')) {
            return;
        }

        Schema::table('administrative_units', function (Blueprint $table) {
            foreach (['iso3_code', 'iso_code', 'country_id'] as $column) {
                if (Schema::hasColumn('administrative_units', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
