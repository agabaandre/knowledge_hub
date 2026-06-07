<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'external_provider')) {
                $table->string('external_provider', 50)->nullable()->after('moodle_id');
            }
            if (! Schema::hasColumn('courses', 'external_id')) {
                $table->string('external_id', 191)->nullable()->after('external_provider');
            }
        });

        if (Schema::hasColumn('courses', 'moodle_id')) {
            try {
                Schema::table('courses', function (Blueprint $table) {
                    $table->integer('moodle_id')->nullable()->change();
                });
            } catch (\Throwable) {
                // doctrine/dbal may be unavailable; column may already be nullable.
            }

            $indexes = collect(DB::select('SHOW INDEX FROM courses WHERE Key_name = ?', ['courses_moodle_id_unique']))
                ->pluck('Key_name')
                ->unique();
            if ($indexes->contains('courses_moodle_id_unique')) {
                Schema::table('courses', function (Blueprint $table) {
                    $table->dropUnique('courses_moodle_id_unique');
                });
            }
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'external_provider') && Schema::hasColumn('courses', 'external_id')) {
                $table->unique(['external_provider', 'external_id'], 'courses_external_provider_id_unique');
            }
        });

        DB::table('courses')
            ->where('moodle_id', '>', 0)
            ->whereNull('external_provider')
            ->update([
                'external_provider' => 'moodle',
                'external_id' => DB::raw('CAST(moodle_id AS CHAR)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'external_provider')) {
                $table->dropUnique('courses_external_provider_id_unique');
                $table->dropColumn(['external_provider', 'external_id']);
            }
        });
    }
};
