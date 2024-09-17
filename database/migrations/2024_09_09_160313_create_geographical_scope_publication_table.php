<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeographicalScopePublicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geographical_scope_publication', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publication_id'); // Foreign key to publications table
            $table->unsignedBigInteger('geographical_scope_id'); // Foreign key to geographical_scope table

            // Foreign key constraints
            $table->foreign('publication_id')
                ->references('id')
                ->on('publications')
                ->onDelete('cascade');

            $table->foreign('geographical_scope_id')
                ->references('id')
                ->on('geographical_scope')
                ->onDelete('cascade');

            // Index to improve performance on queries
            $table->unique(['publication_id', 'geographical_scope_id']); // Ensure uniqueness of the pair
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
        Schema::dropIfExists('geographical_scope_publication');
    }
}
