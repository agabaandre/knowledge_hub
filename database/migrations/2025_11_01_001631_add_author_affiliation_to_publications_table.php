<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorAffiliationToPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (!Schema::hasColumn('publication', 'author_affiliation')) {
                $table->string('author_affiliation', 500)->nullable()->after('associated_authors');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'author_affiliation')) {
                $table->dropColumn('author_affiliation');
            }
        });
    }
}
