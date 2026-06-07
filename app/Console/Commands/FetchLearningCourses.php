<?php

namespace App\Console\Commands;

use App\Services\CourseSyncService;
use Illuminate\Console\Command;

class FetchLearningCourses extends Command
{
    protected $signature = 'learning:fetch-courses {--provider= : Sync only moodle, frappe, or openedx}';

    protected $description = 'Fetch courses from configured learning platforms (Moodle, Frappe LMS, Open edX)';

    public function handle(CourseSyncService $syncService): int
    {
        $provider = $this->option('provider');
        $onlyProvider = is_string($provider) && $provider !== '' ? $provider : null;

        $results = $syncService->syncAll($onlyProvider);
        $synced = array_keys($results['providers'] ?? []);

        if ($synced === []) {
            $this->warn('No learning providers were synced. Configure and enable at least one provider under Admin → Configure → Advanced.');

            return 0;
        }

        $this->info('Synced courses from: '.implode(', ', $synced));

        return 0;
    }
}
