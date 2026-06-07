<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('publication')) {
            Schema::table('publication', function (Blueprint $table) {
                if (Schema::hasColumn('publication', 'associated_authors')) {
                    $table->text('associated_authors')->nullable()->change();
                }
                if (Schema::hasColumn('publication', 'author_affiliation')) {
                    $table->text('author_affiliation')->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('publications_staging')) {
            Schema::table('publications_staging', function (Blueprint $table) {
                if (Schema::hasColumn('publications_staging', 'associated_authors')) {
                    $table->text('associated_authors')->nullable()->change();
                }
                if (Schema::hasColumn('publications_staging', 'author_affiliation')) {
                    $table->text('author_affiliation')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('publication')) {
            Schema::table('publication', function (Blueprint $table) {
                if (Schema::hasColumn('publication', 'associated_authors')) {
                    $table->string('associated_authors', 200)->nullable()->change();
                }
                if (Schema::hasColumn('publication', 'author_affiliation')) {
                    $table->string('author_affiliation', 500)->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('publications_staging')) {
            Schema::table('publications_staging', function (Blueprint $table) {
                if (Schema::hasColumn('publications_staging', 'associated_authors')) {
                    $table->string('associated_authors')->nullable()->change();
                }
                if (Schema::hasColumn('publications_staging', 'author_affiliation')) {
                    $table->string('author_affiliation')->nullable()->change();
                }
            });
        }
    }
};
