<?php

namespace App\Services;

use App\Contracts\LearningProviderSync;
use App\Services\Concerns\StoresSyncedCourses;
use App\Support\FrappeConfig;
use App\Support\LearningConfig;
use Illuminate\Support\Facades\Http;
use Log;

class FrappeLmsService implements LearningProviderSync
{
    use StoresSyncedCourses;

    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiSecret;

    protected string $courseDoctype;

    public function __construct()
    {
        $this->baseUrl = FrappeConfig::baseUrl();
        $this->apiKey = FrappeConfig::apiKey();
        $this->apiSecret = FrappeConfig::apiSecret();
        $this->courseDoctype = FrappeConfig::courseDoctype();
    }

    public function providerKey(): string
    {
        return 'frappe';
    }

    public function providerLabel(): string
    {
        return 'Frappe LMS';
    }

    public function isConfigured(): bool
    {
        return FrappeConfig::isConfigured();
    }

    public function syncEnabled(): bool
    {
        return FrappeConfig::syncEnabled();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function testConnection(array $overrides = []): array
    {
        $baseUrl = rtrim((string) ($overrides['frappe_base_url'] ?? $this->baseUrl), '/');
        $apiKey = (string) ($overrides['frappe_api_key'] ?? $this->apiKey);
        $apiSecret = (string) ($overrides['frappe_api_secret'] ?? $this->apiSecret);

        if ($baseUrl === '' || $apiKey === '' || $apiSecret === '') {
            return ['ok' => false, 'error' => 'Base URL, API key, and API secret are required.'];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders($this->authHeaders($apiKey, $apiSecret))
                ->get($baseUrl.'/api/method/frappe.auth.get_logged_user');

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status()];
            }

            $body = $response->json();
            if (isset($body['exc_type'])) {
                return ['ok' => false, 'error' => (string) ($body['exception'] ?? $body['exc_type'])];
            }

            return [
                'ok' => true,
                'user' => (string) ($body['message'] ?? ''),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAndStoreCourses(?callable $onProgress = null): int
    {
        if (! $this->isConfigured()) {
            Log::warning('Frappe LMS sync skipped: base URL, API key, or API secret is not configured.');

            return 0;
        }

        if (! $this->syncEnabled()) {
            Log::info('Frappe LMS sync skipped: sync is disabled.');

            return 0;
        }

        $limit = 100;
        $start = 0;
        $stored = 0;
        $page = 0;

        do {
            $courses = $this->fetchCoursePage($start, $limit);
            $page++;
            foreach ($courses as $course) {
                if (! is_array($course) || empty($course['name'])) {
                    continue;
                }
                $this->storeCourse($course);
                $stored++;
                if ($onProgress) {
                    $onProgress($stored, max($stored, $limit * $page), "Imported {$stored} course(s)");
                }
            }
            $start += $limit;
        } while (count($courses) === $limit);

        return $stored;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function fetchCoursePage(int $start, int $limit): array
    {
        $doctype = $this->courseDoctype;
        $response = Http::timeout(30)
            ->withHeaders($this->authHeaders())
            ->get($this->baseUrl.'/api/resource/'.rawurlencode($doctype), [
                'fields' => json_encode(['name', 'title', 'short_introduction', 'description', 'image', 'category', 'published']),
                'filters' => json_encode([['published', '=', 1]]),
                'limit_start' => $start,
                'limit_page_length' => $limit,
            ]);

        if (! $response->successful()) {
            Log::error('Frappe LMS course fetch failed', ['status' => $response->status(), 'body' => $response->body()]);

            return [];
        }

        $data = $response->json('data');

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $courseData
     */
    protected function storeCourse(array $courseData): void
    {
        $externalId = (string) $courseData['name'];
        $title = (string) ($courseData['title'] ?? $externalId);
        $summary = (string) ($courseData['short_introduction'] ?? $courseData['description'] ?? '');

        $this->upsertExternalCourse($this->providerKey(), $externalId, [
            'fullname' => $title,
            'shortname' => $title,
            'category_id' => 0,
            'summary' => $summary !== '' ? $summary : null,
            'cover_image' => $this->resolveImageUrl($courseData['image'] ?? null),
            'course_url' => FrappeConfig::courseViewUrl($externalId),
            'content' => null,
        ]);
    }

    protected function resolveImageUrl(mixed $image): ?string
    {
        if (! is_string($image) || $image === '') {
            return LearningConfig::defaultCourseImage();
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return $this->baseUrl.'/'.ltrim($image, '/');
    }

    /**
     * @return array<string, string>
     */
    protected function authHeaders(?string $apiKey = null, ?string $apiSecret = null): array
    {
        $key = $apiKey ?? $this->apiKey;
        $secret = $apiSecret ?? $this->apiSecret;

        return [
            'Authorization' => 'token '.$key.':'.$secret,
            'Accept' => 'application/json',
        ];
    }
}
