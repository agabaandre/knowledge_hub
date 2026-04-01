<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (! Schema::hasColumn('forums', 'is_resubmission_pending')) {
                $table->boolean('is_resubmission_pending')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            if (Schema::hasColumn('forums', 'is_resubmission_pending')) {
                $table->dropColumn('is_resubmission_pending');
            }
        });
    }
};
