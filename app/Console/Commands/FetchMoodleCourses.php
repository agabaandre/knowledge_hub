<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MoodleService;

class FetchMoodleCourses extends Command
{
    protected $signature = 'moodle:fetch-courses';
    protected $description = 'Fetch courses and categories from Moodle and store them in the database';

    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        parent::__construct();
        $this->moodleService = $moodleService;
    }

    public function handle()
    {
        if (!config('moodle.sync_enabled', true)) {
            $this->warn('Moodle course sync is disabled (MOODLE_SYNC_ENABLED=false). Skipping.');
            return 0;
        }
        $this->moodleService->fetchAndStoreCourses();
        $this->info('Courses and categories fetched and stored successfully.');
        return 0;
    }
}
