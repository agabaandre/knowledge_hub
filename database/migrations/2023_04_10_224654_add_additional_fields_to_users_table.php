<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('photo',100)->default('avatar.jpg');
            $table->string('first_name',50)->nullable();
            $table->string('organization_name',150)->nullable();
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->boolean('is_changed')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('verification_token',100)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
