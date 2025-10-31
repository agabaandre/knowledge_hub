<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make sure licenses table exists first
        if (!Schema::hasTable('licenses')) {
            throw new \Exception('Licenses table must be created first. Please run the create_licenses_table migration.');
        }

        Schema::table('publication', function (Blueprint $table) {
            // DOI, ISSN, ISBN
            if (!Schema::hasColumn('publication', 'doi')) {
                $table->string('doi', 255)->nullable()->after('citation_authors');
            }
            if (!Schema::hasColumn('publication', 'issn')) {
                $table->string('issn', 50)->nullable()->after('doi');
            }
            if (!Schema::hasColumn('publication', 'isbn')) {
                $table->string('isbn', 50)->nullable()->after('issn');
            }
            
            // License/Copyright
            if (!Schema::hasColumn('publication', 'license_id')) {
                $table->foreignId('license_id')->nullable()->after('isbn');
                $table->foreign('license_id')->references('id')->on('licenses')->onDelete('set null');
            }
            if (!Schema::hasColumn('publication', 'copyright_info')) {
                $table->text('copyright_info')->nullable()->after('license_id');
            }
            
            // Funder
            if (!Schema::hasColumn('publication', 'funder')) {
                $table->string('funder', 500)->nullable()->after('copyright_info');
            }
            
            // Journal fields (only for Journal Articles)
            if (!Schema::hasColumn('publication', 'journal_name')) {
                $table->string('journal_name', 500)->nullable()->after('funder');
            }
            if (!Schema::hasColumn('publication', 'journal_volume')) {
                $table->string('journal_volume', 50)->nullable()->after('journal_name');
            }
            if (!Schema::hasColumn('publication', 'journal_issue')) {
                $table->string('journal_issue', 50)->nullable()->after('journal_volume');
            }
            if (!Schema::hasColumn('publication', 'journal_pages')) {
                $table->string('journal_pages', 50)->nullable()->after('journal_issue');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'journal_pages')) {
                $table->dropColumn('journal_pages');
            }
            if (Schema::hasColumn('publication', 'journal_issue')) {
                $table->dropColumn('journal_issue');
            }
            if (Schema::hasColumn('publication', 'journal_volume')) {
                $table->dropColumn('journal_volume');
            }
            if (Schema::hasColumn('publication', 'journal_name')) {
                $table->dropColumn('journal_name');
            }
            if (Schema::hasColumn('publication', 'funder')) {
                $table->dropColumn('funder');
            }
            if (Schema::hasColumn('publication', 'copyright_info')) {
                $table->dropColumn('copyright_info');
            }
            if (Schema::hasColumn('publication', 'license_id')) {
                $table->dropForeign(['license_id']);
                $table->dropColumn('license_id');
            }
            if (Schema::hasColumn('publication', 'isbn')) {
                $table->dropColumn('isbn');
            }
            if (Schema::hasColumn('publication', 'issn')) {
                $table->dropColumn('issn');
            }
            if (Schema::hasColumn('publication', 'doi')) {
                $table->dropColumn('doi');
            }
        });
    }
};
