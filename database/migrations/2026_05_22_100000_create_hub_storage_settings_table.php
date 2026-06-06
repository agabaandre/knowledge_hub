<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubStorageSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hub_storage_settings')) {
            return;
        }

        Schema::create('hub_storage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('files_driver', 32)->default('internal');
            $table->string('local_files_root', 512)->nullable();
            $table->string('sql_backup_root', 512)->nullable();
            $table->json('cloud_config')->nullable();
            $table->boolean('auto_sql_backup')->default(true);
            $table->unsignedSmallInteger('sql_backup_retention_days')->default(30);
            $table->timestamp('last_sql_backup_at')->nullable();
            $table->string('last_sql_backup_path', 512)->nullable();
            $table->string('migration_status', 32)->nullable();
            $table->unsignedInteger('migration_files_total')->default(0);
            $table->unsignedInteger('migration_files_done')->default(0);
            $table->text('migration_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hub_storage_settings');
    }
}
