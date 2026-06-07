<?php

namespace App\Services;

use App\Contracts\LearningProviderSync;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Services\Concerns\StoresSyncedCourses;
use App\Support\LearningConfig;
use App\Support\MoodleConfig;
use Illuminate\Support\Facades\Http;
use Log;

class MoodleService implements LearningProviderSync
{
    use StoresSyncedCourses;

    protected string $apiUrl;

    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = (string) config('moodle.api_url');
        $this->apiToken = (string) config('moodle.api_token');
    }

    public function providerKey(): string
    {
        return 'moodle';
    }

    public function providerLabel(): string
    {
        return 'Moodle';
    }

    public function isConfigured(): bool
    {
        return MoodleConfig::isConfigured();
    }

    public function syncEnabled(): bool
    {
        return MoodleConfig::syncEnabled();
    }

    public function fetchAndStoreCourses(?callable $onProgress = null): int
    {
        if (! $this->isConfigured()) {
            Log::warning('Moodle sync skipped: API URL, token, or base URL is not configured.');

            return 0;
        }

        if (! $this->syncEnabled()) {
            Log::info('Moodle sync skipped: sync is disabled.');

            return 0;
        }

        $this->backfillLegacyMoodleCourses();

        if ($onProgress) {
            $onProgress(0, 1, 'Fetching categories');
        }

        $categories = $this->fetchCategories();
        foreach ($categories as $category) {
            $this->storeCategory($category);
        }

        $courses = array_values(array_filter($this->fetchCourses(), static fn ($course) => is_array($course) && ! empty($course['id'])));
        $total = count($courses);
        $stored = 0;

        foreach ($courses as $course) {
            $this->storeCourse($course);
            $stored++;
            if ($onProgress) {
                $onProgress($stored, max(1, $total), "Imported {$stored} of {$total}");
            }
        }

        return $stored;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function testConnection(array $overrides = []): array
    {
        $apiUrl = (string) ($overrides['moodle_api_url'] ?? $this->apiUrl);
        $apiToken = (string) ($overrides['moodle_api_token'] ?? $this->apiToken);

        if ($apiUrl === '' || $apiToken === '') {
            return ['ok' => false, 'error' => 'API URL and token are required.'];
        }

        try {
            $response = Http::timeout(20)->get($apiUrl, [
                'wstoken' => $apiToken,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json',
            ]);

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status()];
            }

            $body = $response->json();
            if (isset($body['exception'])) {
                return ['ok' => false, 'error' => (string) ($body['message'] ?? 'Moodle API error')];
            }

            return [
                'ok' => true,
                'sitename' => (string) ($body['sitename'] ?? ''),
                'release' => (string) ($body['release'] ?? ''),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    protected function fetchCategories(): array
    {
        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_categories',
            'moodlewsrestformat' => 'json',
        ]);

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    protected function fetchCourses(): array
    {
        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ]);

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    protected function storeCategory(array $categoryData): void
    {
        CourseCategory::updateOrCreate(
            ['moodle_id' => $categoryData['id']],
            [
                'name' => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
            ]
        );
    }

    protected function storeCourse(array $courseData): void
    {
        $externalId = (string) $courseData['id'];
        $fullname = $courseData['displayname'] ?? $courseData['fullname'] ?? '';

        if (config('moodle.exclude_demo_courses', false) && $this->isDemoCourse($fullname)) {
            $existing = Course::query()
                ->where('external_provider', $this->providerKey())
                ->where('external_id', $externalId)
                ->first();
            if ($existing) {
                $existing->update(['is_active' => false]);
            }

            return;
        }

        $coverImage = $this->fetchCourseCoverImage((int) $courseData['id'], $courseData);
        if ($coverImage === null || $coverImage === '') {
            $coverImage = LearningConfig::defaultCourseImage();
        }

        $this->upsertExternalCourse($this->providerKey(), $externalId, [
            'fullname' => $fullname,
            'shortname' => $courseData['shortname'] ?? '',
            'category_id' => $courseData['categoryid'] ?? 0,
            'summary' => $courseData['summary'] ?? null,
            'cover_image' => $coverImage,
            'course_url' => MoodleConfig::courseViewUrl((int) $courseData['id']),
            'content' => null,
        ]);
    }

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

    /**
     * @param  array<string, mixed>  $courseData
     */
    protected function fetchCourseCoverImage(int $courseId, array $courseData = []): ?string
    {
        if (! empty($courseData['overviewfiles']) && is_array($courseData['overviewfiles'])) {
            foreach ($courseData['overviewfiles'] as $file) {
                if (! empty($file['fileurl'])) {
                    return $this->tokenizeMoodleFileUrl((string) $file['fileurl']);
                }
            }
        }

        $response = Http::get($this->apiUrl, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_get_contents',
            'courseid' => $courseId,
            'moodlewsrestformat' => 'json',
        ]);

        $courseContents = $response->json();
        if (! is_array($courseContents)) {
            return null;
        }

        foreach ($courseContents as $section) {
            if (! empty($section['summaryfiles']) && is_array($section['summaryfiles'])) {
                foreach ($section['summaryfiles'] as $file) {
                    if (! empty($file['fileurl'])) {
                        return $this->tokenizeMoodleFileUrl((string) $file['fileurl']);
                    }
                }
            }
        }

        return null;
    }

    protected function tokenizeMoodleFileUrl(string $url): string
    {
        if (str_contains($url, 'token=')) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'token='.$this->apiToken;
    }

    protected function backfillLegacyMoodleCourses(): void
    {
        $defaultImage = LearningConfig::defaultCourseImage();

        Course::query()
            ->where(function ($query) {
                $query->where('moodle_id', '>', 0)
                    ->orWhere('external_provider', $this->providerKey());
            })
            ->chunkById(100, function ($courses) use ($defaultImage) {
                foreach ($courses as $course) {
                    $updates = [];

                    if ($course->external_provider !== $this->providerKey() && ! empty($course->moodle_id)) {
                        $updates['external_provider'] = $this->providerKey();
                        $updates['external_id'] = (string) $course->moodle_id;
                    }

                    if (! $course->is_moodle) {
                        $updates['is_moodle'] = true;
                    }

                    if (empty($course->provider)) {
                        $updates['provider'] = $this->providerLabel();
                    }

                    $storedUrl = $course->getAttributes()['course_url'] ?? null;
                    $moodleId = (int) ($course->moodle_id ?: $course->external_id);
                    $externalUrl = MoodleConfig::courseViewUrl($moodleId);
                    if ($externalUrl && (! is_string($storedUrl) || $storedUrl === '' || ! filter_var($storedUrl, FILTER_VALIDATE_URL))) {
                        $updates['course_url'] = $externalUrl;
                    }

                    $rawCover = $course->getAttributes()['cover_image'] ?? null;
                    if ($rawCover === null || $rawCover === '') {
                        $updates['cover_image'] = $defaultImage;
                    }

                    if ($updates !== []) {
                        $course->update($updates);
                    }
                }
            });
    }
}
