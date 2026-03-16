<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (!Schema::hasColumn('setting', 'primary_font')) {
                    $t->string('primary_font', 100)->nullable()->after('logo_scale');
                }
                if (!Schema::hasColumn('setting', 'default_font_color')) {
                    $t->string('default_font_color', 20)->nullable()->after('primary_font');
                }
            });
        }

        if (!Schema::hasTable('custom_fonts')) {
            Schema::create('custom_fonts', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120); // display name in dropdown
                $table->string('font_family', 120); // CSS font-family value
                $table->json('font_files')->nullable(); // {"woff2":"path","woff":"path","ttf":"path"}
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $t) {
                if (Schema::hasColumn('setting', 'primary_font')) {
                    $t->dropColumn('primary_font');
                }
                if (Schema::hasColumn('setting', 'default_font_color')) {
                    $t->dropColumn('default_font_color');
                }
            });
        }
        Schema::dropIfExists('custom_fonts');
    }
};
