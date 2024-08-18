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
        $coverImage = $this->fetchCourseCoverImage($courseData['id']);
       
        Course::updateOrCreate(
            ['moodle_id' => $courseData['id']],
            [
                'fullname' => $courseData['displayname'],
                'shortname' => $courseData['shortname'],
                'category_id' => $courseData['categoryid'],
                'summary' => $courseData['summary'] ?? null,
                'cover_image' => $coverImage
            ]
        );
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
