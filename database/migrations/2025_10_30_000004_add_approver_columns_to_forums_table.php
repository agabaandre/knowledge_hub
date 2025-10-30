<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApproverColumnsToForumsTable extends Migration
{
    public function up()
    {
        Schema::table('forums', function (Blueprint $table) {
            if (!Schema::hasColumn('forums', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('is_approved');
            }
            if (!Schema::hasColumn('forums', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
            }
        });
    }

    public function down()
    {
        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('forums', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
        });
    }
}


