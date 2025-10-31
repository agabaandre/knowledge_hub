<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('short_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert common licenses
        $commonLicenses = [
            ['name' => 'Creative Commons Attribution (CC BY)', 'short_name' => 'CC BY', 'description' => 'This license allows reusers to distribute, remix, adapt, and build upon the material in any medium or format, so long as attribution is given to the creator.', 'url' => 'https://creativecommons.org/licenses/by/4.0/', 'sort_order' => 1],
            ['name' => 'Creative Commons Attribution-ShareAlike (CC BY-SA)', 'short_name' => 'CC BY-SA', 'description' => 'This license allows reusers to distribute, remix, adapt, and build upon the material in any medium or format, so long as attribution is given to the creator and the material is licensed under the same terms.', 'url' => 'https://creativecommons.org/licenses/by-sa/4.0/', 'sort_order' => 2],
            ['name' => 'Creative Commons Attribution-NoDerivs (CC BY-ND)', 'short_name' => 'CC BY-ND', 'description' => 'This license allows reusers to copy and distribute the material in any medium or format in unadapted form only, and only so long as attribution is given to the creator.', 'url' => 'https://creativecommons.org/licenses/by-nd/4.0/', 'sort_order' => 3],
            ['name' => 'Creative Commons Attribution-NonCommercial (CC BY-NC)', 'short_name' => 'CC BY-NC', 'description' => 'This license allows reusers to distribute, remix, adapt, and build upon the material in any medium or format for noncommercial purposes only, and only so long as attribution is given to the creator.', 'url' => 'https://creativecommons.org/licenses/by-nc/4.0/', 'sort_order' => 4],
            ['name' => 'Creative Commons Attribution-NonCommercial-ShareAlike (CC BY-NC-SA)', 'short_name' => 'CC BY-NC-SA', 'description' => 'This license allows reusers to distribute, remix, adapt, and build upon the material in any medium or format for noncommercial purposes only, and only so long as attribution is given to the creator and the material is licensed under the same terms.', 'url' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/', 'sort_order' => 5],
            ['name' => 'Creative Commons Attribution-NonCommercial-NoDerivs (CC BY-NC-ND)', 'short_name' => 'CC BY-NC-ND', 'description' => 'This license allows reusers to copy and distribute the material in any medium or format in unadapted form only, for noncommercial purposes only, and only so long as attribution is given to the creator.', 'url' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/', 'sort_order' => 6],
            ['name' => 'Creative Commons Public Domain (CC0)', 'short_name' => 'CC0', 'description' => 'Dedicate works to the public domain.', 'url' => 'https://creativecommons.org/publicdomain/zero/1.0/', 'sort_order' => 7],
            ['name' => 'Public Domain', 'short_name' => 'Public Domain', 'description' => 'Works in the public domain are not restricted by copyright.', 'sort_order' => 8],
            ['name' => 'All Rights Reserved', 'short_name' => 'All Rights Reserved', 'description' => 'Copyright holder reserves all rights.', 'sort_order' => 9],
            ['name' => 'Open Access', 'short_name' => 'Open Access', 'description' => 'Open access publication that is freely available.', 'sort_order' => 10],
            ['name' => 'Copyright Protected', 'short_name' => 'Copyright', 'description' => 'Material is protected by copyright.', 'sort_order' => 11],
        ];

        foreach ($commonLicenses as $license) {
            DB::table('licenses')->insert(array_merge($license, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
