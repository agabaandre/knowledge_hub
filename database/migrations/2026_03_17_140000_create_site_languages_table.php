<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_languages', function (Blueprint $table) {
            $table->id();
            $table->string('locale_code', 32)->unique()->comment('Laravel locale / users.langauge, e.g. de, zh-cn');
            $table->string('name', 120)->comment('Display name in admin & profile');
            $table->string('google_translate_code', 32)->nullable()->comment('Google Translate language code, e.g. zh-CN');
            $table->string('flag_emoji', 16)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['locale_code' => 'en', 'name' => 'English', 'google_translate_code' => 'en', 'flag_emoji' => '🇺🇸', 'sort_order' => 10, 'is_active' => true],
            ['locale_code' => 'fr', 'name' => 'Français', 'google_translate_code' => 'fr', 'flag_emoji' => '🇫🇷', 'sort_order' => 20, 'is_active' => true],
            ['locale_code' => 'ar', 'name' => 'العربية', 'google_translate_code' => 'ar', 'flag_emoji' => '🇸🇦', 'sort_order' => 30, 'is_active' => true],
            ['locale_code' => 'es', 'name' => 'Español', 'google_translate_code' => 'es', 'flag_emoji' => '🇪🇸', 'sort_order' => 40, 'is_active' => true],
            ['locale_code' => 'pt', 'name' => 'Português', 'google_translate_code' => 'pt', 'flag_emoji' => '🇵🇹', 'sort_order' => 50, 'is_active' => true],
            ['locale_code' => 'sw', 'name' => 'Kiswahili', 'google_translate_code' => 'sw', 'flag_emoji' => '🇰🇪', 'sort_order' => 60, 'is_active' => true],
        ];

        foreach ($rows as &$r) {
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
        }
        unset($r);

        DB::table('site_languages')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_languages');
    }
};
