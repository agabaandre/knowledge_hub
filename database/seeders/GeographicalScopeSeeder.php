<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeographicalScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        DB::table('geographical_scope')->insert([
            ['name' => 'Member state', 'description' => 'A specific member state of the organization'],
            ['name' => 'Southern Africa', 'description' => 'Southern region of Africa'],
            ['name' => 'Eastern Africa', 'description' => 'Eastern region of Africa'],
            ['name' => 'Central Africa', 'description' => 'Central region of Africa'],
            ['name' => 'Whole of Africa', 'description' => 'The entire continent of Africa'],
            ['name' => 'Global', 'description' => 'Worldwide or global scope']
        ]);
    }
}
