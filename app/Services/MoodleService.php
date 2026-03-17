<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Course; // Assuming you have a Course model
use App\Models\CourseCategory; // Assuming you have a Category model
use Log;

class MoodleService
{
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->apiUrl   = config('moodle.api_url');
        $this->apiToken = config('moodle.api_token');
    }

    public function fetchAndStoreCourses()
    {
        // Fetch categories
        $categories = $this->fetchCategories();
        foreach ($categories as $category) {
            $this->storeCategory($category);
        }

        // Fetch courses
        $courses = $this->fetchCourses();
        foreach ($courses as $course) {
            $this->storeCourse($course);
        }
    }

    protected function fetchCategories()
    {
        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_categories',
            'moodlewsrestformat' => 'json',
        ]);

        return $response->json();
    }

    protected function fetchCourses()
    {
        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ]);

        return $response->json();
    }

    protected function storeCategory($categoryData)
    {
        CourseCategory::updateOrCreate(
            ['moodle_id' => $categoryData['id']],
            [
                'name' => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
            ]
        );
    }

    protected function storeCourse($courseData)
    {
        $fullname = $courseData['displayname'] ?? $courseData['fullname'] ?? '';

        if (config('moodle.exclude_demo_courses', false) && $this->isDemoCourse($fullname)) {
            $existing = Course::where('moodle_id', $courseData['id'])->first();
            if ($existing) {
                $existing->update(['is_active' => false]);
            }
            return;
        }

        $coverImage = $this->fetchCourseCoverImage($courseData['id']);

        Course::updateOrCreate(
            ['moodle_id' => $courseData['id']],
            [
                'fullname' => $fullname,
                'shortname' => $courseData['shortname'] ?? '',
                'category_id' => $courseData['categoryid'] ?? 0,
                'summary' => $courseData['summary'] ?? null,
                'cover_image' => $coverImage,
                'is_active' => true,
            ]
        );
    }

    /**
     * Check if course fullname matches demo/excluded patterns (e.g. for production).
     */
    protected function isDemoCourse(string $fullname): bool
    {
        $patterns = config('moodle.exclude_course_name_patterns', []);
        foreach ($patterns as $pattern) {
            if (stripos($fullname, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function fetchCourseCoverImage($courseId)
    {
        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_contents',
            'courseid' => $courseId,
            'moodlewsrestformat' => 'json',
        ]);

        $courseContents = $response->json();

        Log::info('Course::'.json_encode($courseContents ));

        foreach ($courseContents as $section) {
            if (!empty($section['summaryfiles'])) {
                foreach ($section['summaryfiles'] as $file) {
                    if (isset($file['fileurl'])) {
                        return $file['fileurl'] . '&token=' . $this->apiToken;
                    }
                }
            }
        }

        return null; // Return null if no cover image is found
    }
}
