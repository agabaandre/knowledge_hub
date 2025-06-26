<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(__DIR__."/data/job_titles.json");

        $data = json_decode($json);

        foreach ($data as $item) {
            JobTitle::create([
                'job_id' => $item->job_id,
                'classification_id' => $item->classification_id,
                'isco_id' => $item->isco_id,
                'name' => $item->name
            ]);
        }

    }
}
