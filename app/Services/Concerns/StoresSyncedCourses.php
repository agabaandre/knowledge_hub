<?php

namespace App\Services\Concerns;

use App\Models\Course;
use App\Support\LearningConfig;

trait StoresSyncedCourses
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function upsertExternalCourse(string $provider, string $externalId, array $attributes): Course
    {
        $payload = array_merge($attributes, [
            'external_provider' => $provider,
            'external_id' => $externalId,
            'provider' => $attributes['provider'] ?? $this->providerLabel(),
            'is_moodle' => $provider === 'moodle',
            'is_active' => $attributes['is_active'] ?? true,
        ]);

        if ($provider === 'moodle' && is_numeric($externalId)) {
            $payload['moodle_id'] = (int) $externalId;
        }

        if (empty($payload['cover_image'])) {
            $payload['cover_image'] = LearningConfig::defaultCourseImage();
        }

        return Course::updateOrCreate(
            [
                'external_provider' => $provider,
                'external_id' => $externalId,
            ],
            $payload
        );
    }
}
