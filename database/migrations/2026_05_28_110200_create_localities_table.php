<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('localities') && Schema::hasTable('administrative_units')) {
            Schema::create('localities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('administrative_unit_id');
                $table->string('name', 300);
                $table->string('code', 50)->nullable();
                $table->string('iso3_code', 3)->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();

                $table->foreign('administrative_unit_id')
                    ->references('id')
                    ->on('administrative_units')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('localities');
    }
};
