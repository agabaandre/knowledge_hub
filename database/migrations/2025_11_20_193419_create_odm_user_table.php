<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOdmUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('odm_user');
        Schema::create('odm_user', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement();
            $table->string('username', 25)->default('');
            $table->string('password', 50)->default('');
            $table->integer('department')->unsigned()->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('Email', 50)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->char('pw_reset_code', 32)->nullable();
            $table->tinyInteger('can_add')->nullable()->default(1);
            $table->tinyInteger('can_checkin')->nullable()->default(1);
        });
        
        // Insert initial data (admin/admin)
        DB::table('odm_user')->insert([
            'username' => 'admin',
            'password' => md5('admin'),
            'department' => 1,
            'phone' => '5555551212',
            'Email' => 'odm-test@mailinator.com',
            'last_name' => 'User',
            'first_name' => 'Admin',
            'pw_reset_code' => '',
            'can_add' => 1,
            'can_checkin' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('odm_user');
    }
}

