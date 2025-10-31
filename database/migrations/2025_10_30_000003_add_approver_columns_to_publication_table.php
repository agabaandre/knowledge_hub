<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApproverColumnsToPublicationTable extends Migration
{
    public function up()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (!Schema::hasColumn('publication', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('publication', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
            }
        });
    }

    public function down()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('publication', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
        });
    }
}


