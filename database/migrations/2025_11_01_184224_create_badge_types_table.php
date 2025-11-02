<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Silver Contributor", "Gold Contributor"
            $table->string('slug')->unique(); // e.g., "silver", "gold", "platinum", "diamond"
            $table->text('description')->nullable();
            $table->integer('contribution_threshold'); // 5, 10, 20, 40
            $table->string('badge_color', 50)->default('#C0C0C0'); // Color for styling
            $table->string('image_path')->nullable(); // Path to badge image
            $table->integer('sort_order')->default(0); // For ordering badges
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badge_types');
    }
}
