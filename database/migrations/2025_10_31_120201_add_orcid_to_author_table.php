<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrcidToAuthorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('author', function (Blueprint $table) {
            if (!Schema::hasColumn('author', 'orcid')) {
                $table->string('orcid', 19)->nullable()->after('email');
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
        Schema::table('author', function (Blueprint $table) {
            if (Schema::hasColumn('author', 'orcid')) {
                $table->dropColumn('orcid');
            }
        });
    }
}
