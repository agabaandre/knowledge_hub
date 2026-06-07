<?php

namespace App\Support;

class LearningConfig
{
    public static function defaultCourseImage(): string
    {
        return (string) config('learning.default_course_image', config('moodle.default_course_image'));
    }

    public static function applyRuntimeConfig(): void
    {
        MoodleConfig::applyRuntimeConfig();
        FrappeConfig::applyRuntimeConfig();
        OpenEdxConfig::applyRuntimeConfig();
    }

    public static function clearAllCaches(): void
    {
        MoodleConfig::clearCache();
        FrappeConfig::clearCache();
        OpenEdxConfig::clearCache();
    }

    /**
     * @return list<class-string<LearningProviderSync>>
     */
    public static function providerClasses(): array
    {
        return array_values(array_filter(
            config('learning.providers', []),
            static fn ($class) => is_string($class) && class_exists($class)
        ));
    }
}
