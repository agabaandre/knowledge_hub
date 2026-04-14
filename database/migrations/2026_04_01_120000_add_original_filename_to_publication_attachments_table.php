<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_attachments', function (Blueprint $table) {
            $table->string('original_filename', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('publication_attachments', function (Blueprint $table) {
            $table->dropColumn('original_filename');
        });
    }
};
