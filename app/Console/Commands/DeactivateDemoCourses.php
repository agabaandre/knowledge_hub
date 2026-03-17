<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

/**
 * One-time (or on-demand) deactivation of demo/seed courses already in the database.
 * Run on production to hide the four demo courses from https://khub.africacdc.org/courses.
 */
class DeactivateDemoCourses extends Command
{
    protected $signature = 'courses:deactivate-demo
                            {--dry-run : List matching courses without deactivating}';
    protected $description = 'Deactivate demo courses (e.g. Africa CDC eLearning Demo, IT Officer P2, Knowledge Management Portal) so they no longer appear on the courses page';

    public function handle()
    {
        $patterns = config('moodle.exclude_course_name_patterns', [
            'Africa CDC eLearning Demo',
            'IT Officer P2 (French)',
            'IT Officer P2 (English)',
            'Knowledge Management Portal',
            'Leveraging the Knowledge Hub Portal',
        ]);

        $courses = Course::where('is_active', true)
            ->where(function ($q) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $q->orWhere('fullname', 'like', '%' . $pattern . '%');
                }
            })
            ->get();

        if ($courses->isEmpty()) {
            $this->info('No active demo courses found.');
            return 0;
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run – would deactivate ' . $courses->count() . ' course(s):');
            foreach ($courses as $c) {
                $this->line('  - [id=' . $c->id . '] ' . $c->fullname);
            }
            return 0;
        }

        $count = 0;
        foreach ($courses as $course) {
            $course->update(['is_active' => false]);
            $this->line('Deactivated: ' . $course->fullname);
            $count++;
        }
        $this->info('Deactivated ' . $count . ' demo course(s). They will no longer appear on the courses page.');
        return 0;
    }
}
