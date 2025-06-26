<?php

namespace Database\Seeders;

use App\Models\IscoClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class IscoClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(__DIR__ ."/data/iso_classifications.json");

        $data = json_decode($json);

        foreach ($data as $item) {
            IscoClassification::create([
                'isco_id' => $item->isco_id,
                'name'=> $item->name,
            ]);
        }
    }
}
