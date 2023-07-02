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
         \App\Models\AccessLevel::insert([
            ["level_name"=>"Viewer"],
            ["level_name"=>"Country"],
            ["level_name"=>"RCC"],
            ["level_name"=>"Overall"]
         ]);
    }
}
