<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccessLevelsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (['Viewer', 'Country', 'RCC', 'Overall'] as $levelName) {
            \App\Models\AccessLevel::query()->firstOrCreate(
                ['level_name' => $levelName],
                ['level_name' => $levelName]
            );
        }
    }
}
