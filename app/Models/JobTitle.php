<?php

namespace App\Models;

use App\Repositories\ExpertsRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class JobTitle extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        // Before save: drop old ISCO bucket when the code changes (saved() runs after syncOriginal).
        static::updating(function (JobTitle $jobTitle) {
            if ($jobTitle->isDirty('isco_id')) {
                $previous = $jobTitle->getOriginal('isco_id');
                if ($previous !== null) {
                    Cache::forget(ExpertsRepository::jobTitlesByIscoCacheKey((string) $previous));
                }
            }
        });

        static::saved(function (JobTitle $jobTitle) {
            Cache::forget(ExpertsRepository::CACHE_KEY_JOB_TITLES_ALL);
            if ($jobTitle->isco_id !== null) {
                Cache::forget(ExpertsRepository::jobTitlesByIscoCacheKey((string) $jobTitle->isco_id));
            }
        });

        static::deleted(function (JobTitle $jobTitle) {
            Cache::forget(ExpertsRepository::CACHE_KEY_JOB_TITLES_ALL);
            if ($jobTitle->isco_id !== null) {
                Cache::forget(ExpertsRepository::jobTitlesByIscoCacheKey((string) $jobTitle->isco_id));
            }
        });
    }

    protected $fillable = [
        'job_id',
        'classification_id',
        'isco_id',
        'name'
    ];

    public function iscoClassification()
    {
        return $this->belongsTo(IscoClassification::class, 'isco_id', 'isco_id');
    }

    public function experts()
    {
        return $this->hasMany(Expert::class, 'job_title_id', 'id');
    }
}
