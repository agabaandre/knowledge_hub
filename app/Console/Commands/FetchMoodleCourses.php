<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MoodleService;
use App\Support\MoodleConfig;

class FetchMoodleCourses extends Command
{
    protected $signature = 'moodle:fetch-courses';

    protected $description = 'Fetch courses and categories from Moodle and store them in the database';

    public function handle(MoodleService $moodleService): int
    {
        if (! MoodleConfig::syncEnabled()) {
            $this->warn('Moodle course sync is disabled. Skipping.');

            return 0;
        }

        $moodleService->fetchAndStoreCourses();
        $this->info('Moodle courses and categories fetched and stored successfully.');

        return 0;
    }
}
