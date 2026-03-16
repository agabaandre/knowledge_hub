<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameColumnToCustomAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_attachments', function (Blueprint $table) {
            $table->string('name')->nullable()->after('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_attachments', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
