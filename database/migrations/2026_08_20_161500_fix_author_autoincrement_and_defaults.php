<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Country hubs sometimes lose AUTO_INCREMENT on author.id, and older schemas
 * require address/telephone/email without defaults — which breaks register-time
 * Author::firstOrCreate() and leaves users.author_id null.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('author')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            try {
                DB::statement('ALTER TABLE `author` MODIFY `id` INT NOT NULL AUTO_INCREMENT');
            } catch (\Throwable $e) {
                // Already auto-increment or non-int PK — ignore.
            }

            try {
                DB::statement("ALTER TABLE `author` MODIFY `address` VARCHAR(255) NOT NULL DEFAULT ''");
            } catch (\Throwable $e) {
            }
            try {
                DB::statement("ALTER TABLE `author` MODIFY `telephone` VARCHAR(50) NOT NULL DEFAULT ''");
            } catch (\Throwable $e) {
            }
            try {
                DB::statement("ALTER TABLE `author` MODIFY `email` VARCHAR(255) NOT NULL DEFAULT ''");
            } catch (\Throwable $e) {
            }
            try {
                DB::statement("ALTER TABLE `author` MODIFY `is_organsiation` VARCHAR(10) NOT NULL DEFAULT 'No'");
            } catch (\Throwable $e) {
            }
        } else {
            Schema::table('author', function (Blueprint $table) {
                // SQLite/tests: best-effort nullable-friendly changes are limited.
            });
        }
    }

    public function down(): void
    {
        // Non-destructive; intentionally left blank.
    }
};
