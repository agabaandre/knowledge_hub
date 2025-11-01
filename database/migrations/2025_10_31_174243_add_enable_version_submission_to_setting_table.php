<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'enable_version_submission')) {
                $table->boolean('enable_version_submission')->default(true)->after('publication_required_fields');
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
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'enable_version_submission')) {
                $table->dropColumn('enable_version_submission');
            }
        });
    }
};