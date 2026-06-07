<?php

namespace App\Services;

use App\Contracts\LearningProviderSync;
use App\Services\Concerns\StoresSyncedCourses;
use App\Support\LearningConfig;
use App\Support\OpenEdxConfig;
use Illuminate\Support\Facades\Http;
use Log;

class OpenEdxService implements LearningProviderSync
{
    use StoresSyncedCourses;

    protected string $lmsUrl;

    protected string $clientId;

    protected string $clientSecret;

    protected string $tokenUrl;

    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->lmsUrl = OpenEdxConfig::lmsUrl();
        $this->clientId = OpenEdxConfig::clientId();
        $this->clientSecret = OpenEdxConfig::clientSecret();
        $this->tokenUrl = OpenEdxConfig::tokenUrl();
    }

    public function providerKey(): string
    {
        return 'openedx';
    }

    public function providerLabel(): string
    {
        return 'Open edX';
    }

    public function isConfigured(): bool
    {
        return OpenEdxConfig::isConfigured();
    }

    public function syncEnabled(): bool
    {
        return OpenEdxConfig::syncEnabled();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function testConnection(array $overrides = []): array
    {
        $lmsUrl = rtrim((string) ($overrides['openedx_lms_url'] ?? $this->lmsUrl), '/');
        $clientId = (string) ($overrides['openedx_client_id'] ?? $this->clientId);
        $clientSecret = (string) ($overrides['openedx_client_secret'] ?? $this->clientSecret);
        $tokenUrl = (string) ($overrides['openedx_token_url'] ?? ($lmsUrl !== '' ? $lmsUrl.'/oauth2/access_token' : ''));

        if ($lmsUrl === '' || $clientId === '' || $clientSecret === '') {
            return ['ok' => false, 'error' => 'LMS URL, client ID, and client secret are required.'];
        }

        try {
            $token = $this->requestAccessToken($clientId, $clientSecret, $tokenUrl);
            if ($token === '') {
                return ['ok' => false, 'error' => 'Could not obtain access token.'];
            }

            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'JWT '.$token,
                    'Accept' => 'application/json',
                ])
                ->get($lmsUrl.'/api/courses/v1/courses/', ['page_size' => 1]);

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status()];
            }

            $count = (int) ($response->json('pagination.count') ?? 0);

            return [
                'ok' => true,
                'courses' => $count,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function fetchAndStoreCourses(?callable $onProgress = null): int
    {
        if (! $this->isConfigured()) {
            Log::warning('Open edX sync skipped: LMS URL, client ID, or client secret is not configured.');

            return 0;
        }

        if (! $this->syncEnabled()) {
            Log::info('Open edX sync skipped: sync is disabled.');

            return 0;
        }

        $this->accessToken = null;
        $url = $this->lmsUrl.'/api/courses/v1/courses/?page_size=100';
        $stored = 0;
        $expectedTotal = 0;

        while ($url !== '') {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'JWT '.$this->getAccessToken(),
                    'Accept' => 'application/json',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::error('Open edX course fetch failed', ['status' => $response->status(), 'body' => $response->body()]);
                break;
            }

            $body = $response->json();
            $results = is_array($body['results'] ?? null) ? $body['results'] : [];
            $expectedTotal = (int) ($body['pagination']['count'] ?? max($expectedTotal, $stored + count($results)));

            foreach ($results as $course) {
                if (! is_array($course) || empty($course['id'])) {
                    continue;
                }
                $this->storeCourse($course);
                $stored++;
                if ($onProgress) {
                    $onProgress($stored, max(1, $expectedTotal), "Imported {$stored} of {$expectedTotal}");
                }
            }

            $url = (string) ($body['pagination']['next'] ?? '');
        }

        return $stored;
    }

    /**
     * @param  array<string, mixed>  $courseData
     */
    protected function storeCourse(array $courseData): void
    {
        $externalId = (string) $courseData['id'];
        $title = (string) ($courseData['name'] ?? $externalId);
        $summary = (string) ($courseData['short_description'] ?? '');

        $this->upsertExternalCourse($this->providerKey(), $externalId, [
            'fullname' => $title,
            'shortname' => (string) ($courseData['number'] ?? $title),
            'category_id' => 0,
            'summary' => $summary !== '' ? $summary : null,
            'cover_image' => $this->resolveImageUrl($courseData),
            'course_url' => OpenEdxConfig::courseViewUrl($externalId),
            'content' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $courseData
     */
    protected function resolveImageUrl(array $courseData): string
    {
        $media = $courseData['media'] ?? null;
        if (is_array($media)) {
            foreach (['course_image', 'image'] as $key) {
                $candidate = $media[$key] ?? null;
                if (is_array($candidate)) {
                    foreach (['uri_absolute', 'url', 'uri'] as $uriKey) {
                        if (! empty($candidate[$uriKey]) && is_string($candidate[$uriKey])) {
                            return $this->absoluteUrl($candidate[$uriKey]);
                        }
                    }
                }
            }
        }

        return LearningConfig::defaultCourseImage();
    }

    protected function absoluteUrl(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return $this->lmsUrl.'/'.ltrim($url, '/');
    }

    protected function getAccessToken(): string
    {
        if ($this->accessToken !== null && $this->accessToken !== '') {
            return $this->accessToken;
        }

        $this->accessToken = $this->requestAccessToken(
            $this->clientId,
            $this->clientSecret,
            $this->tokenUrl
        );

        return $this->accessToken;
    }

    protected function requestAccessToken(string $clientId, string $clientSecret, string $tokenUrl): string
    {
        if ($tokenUrl === '') {
            return '';
        }

        $credential = base64_encode($clientId.':'.$clientSecret);
        $response = Http::asForm()
            ->timeout(20)
            ->withHeaders([
                'Authorization' => 'Basic '.$credential,
                'Cache-Control' => 'no-cache',
            ])
            ->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'token_type' => 'jwt',
            ]);

        if (! $response->successful()) {
            Log::error('Open edX token request failed', ['status' => $response->status(), 'body' => $response->body()]);

            return '';
        }

        return (string) ($response->json('access_token') ?? '');
    }
}
