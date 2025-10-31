<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionFieldsToPublicationTable extends Migration
{
    public function up()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (!Schema::hasColumn('publication', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('is_rejected');
            }
            if (!Schema::hasColumn('publication', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_reason');
            }
            if (!Schema::hasColumn('publication', 'appealed_at')) {
                $table->timestamp('appealed_at')->nullable()->after('rejected_at');
            }
        });
    }

    public function down()
    {
        Schema::table('publication', function (Blueprint $table) {
            if (Schema::hasColumn('publication', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
            if (Schema::hasColumn('publication', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('publication', 'appealed_at')) {
                $table->dropColumn('appealed_at');
            }
        });
    }
}


