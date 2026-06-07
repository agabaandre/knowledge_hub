<?php

namespace App\Models;

use App\Support\FrappeConfig;
use App\Support\LearningConfig;
use App\Support\MoodleConfig;
use App\Support\OpenEdxConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'moodle_id', 'external_provider', 'external_id', 'fullname', 'shortname', 'cover_image', 'category_id', 'summary',
        'provider', 'content', 'course_url', 'is_moodle', 'is_active', 'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'is_moodle' => 'boolean',
        'is_active' => 'boolean',
    ];

    public $appends = ['course_link', 'moodle_course_url', 'external_course_url'];

    public function getExternalCourseUrlAttribute(): ?string
    {
        $stored = $this->attributes['course_url'] ?? null;
        if (is_string($stored) && $stored !== '' && filter_var($stored, FILTER_VALIDATE_URL)) {
            return $stored;
        }

        $provider = (string) ($this->external_provider ?? '');
        $externalId = (string) ($this->external_id ?? '');

        if ($provider === 'frappe' && $externalId !== '') {
            return FrappeConfig::courseViewUrl($externalId);
        }

        if ($provider === 'openedx' && $externalId !== '') {
            return OpenEdxConfig::courseViewUrl($externalId);
        }

        if (($provider === 'moodle' || $this->is_moodle || ! empty($this->moodle_id)) && $externalId !== '') {
            return MoodleConfig::courseViewUrl((int) $externalId);
        }

        if (! empty($this->moodle_id)) {
            return MoodleConfig::courseViewUrl((int) $this->moodle_id);
        }

        return null;
    }

    public function getMoodleCourseUrlAttribute(): ?string
    {
        return $this->external_course_url;
    }

    public function getCourseLinkAttribute(): ?string
    {
        if ($this->isExternalCourse()) {
            return $this->external_course_url;
        }

        $stored = $this->attributes['course_url'] ?? null;

        return is_string($stored) && $stored !== '' ? $stored : null;
    }

    public function isExternalCourse(): bool
    {
        if (! empty($this->external_provider) && ! empty($this->external_id)) {
            return true;
        }

        return $this->isMoodleCourse();
    }

    public function isMoodleCourse(): bool
    {
        return (bool) ($this->is_moodle ?? false)
            || ! empty($this->moodle_id)
            || $this->external_provider === 'moodle';
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id', 'id');
    }

    public function getCoverImageAttribute($value)
    {
        if ($value === null || $value === '') {
            return LearningConfig::defaultCourseImage();
        }

        return filter_var($value, FILTER_VALIDATE_URL) ? $value : asset('storage/uploads/courses/'.$value);
    }
}
