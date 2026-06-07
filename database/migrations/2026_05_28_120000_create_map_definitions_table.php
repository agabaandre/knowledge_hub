<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('map_definitions')) {
            return;
        }

        Schema::create('map_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('provider', 40)->default('highcharts');
            $table->string('source_type', 40)->default('topojson_url');
            $table->string('topology_preset', 80)->nullable();
            $table->string('topology_url', 500)->nullable();
            $table->string('collection_version', 20)->default('2.3.3');
            $table->string('map_key', 120)->nullable();
            $table->string('script_path', 255)->nullable();
            $table->string('join_by', 40)->default('iso-a3');
            $table->string('iso_property', 40)->nullable();
            $table->string('scope', 40)->default('custom');
            $table->string('country_iso2', 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_definitions');
    }
};
