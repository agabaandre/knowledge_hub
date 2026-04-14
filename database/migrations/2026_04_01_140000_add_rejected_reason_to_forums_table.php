<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (! Schema::hasColumn('forums', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
        });
    }
};
