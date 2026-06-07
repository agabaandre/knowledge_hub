<?php

namespace App\Services;

use App\Contracts\LearningProviderSync;
use App\Support\LearningConfig;
use Illuminate\Support\Facades\Log;

class CourseSyncService
{
    /**
     * @param  null|callable(int $progress, string $step, ?string $message, int $coursesFetched, int $coursesTotal): void  $onProgress
     * @return array{providers: array<string, int>, skipped: list<string>, total: int}
     */
    public function syncAll(?string $onlyProvider = null, ?callable $onProgress = null): array
    {
        $activeProviders = [];
        $skipped = [];

        foreach ($this->providers() as $provider) {
            $key = $provider->providerKey();

            if ($onlyProvider !== null && $onlyProvider !== '' && $key !== $onlyProvider) {
                continue;
            }

            if (! $provider->isConfigured()) {
                $skipped[] = $provider->providerLabel().' (not configured)';

                continue;
            }

            if (! $provider->syncEnabled()) {
                $skipped[] = $provider->providerLabel().' (sync disabled)';

                continue;
            }

            $activeProviders[] = $provider;
        }

        $providerCount = count($activeProviders);
        $byProvider = [];
        $totalCourses = 0;
        $runningFetched = 0;

        if ($providerCount === 0) {
            return [
                'providers' => [],
                'skipped' => $skipped,
                'total' => 0,
            ];
        }

        foreach ($activeProviders as $index => $provider) {
            $key = $provider->providerKey();
            $label = $provider->providerLabel();
            $sliceStart = (int) floor(($index / $providerCount) * 90) + 5;
            $coursesBeforeProvider = $totalCourses;

            if ($onProgress) {
                $onProgress($sliceStart, "Connecting to {$label}…", null, $runningFetched, $runningFetched);
            }

            try {
                $count = $provider->fetchAndStoreCourses(function (int $current, int $total, string $stepLabel) use (
                    $onProgress,
                    $sliceStart,
                    $providerCount,
                    $label,
                    $coursesBeforeProvider
                ) {
                    $sliceSize = (int) floor(90 / max(1, $providerCount));
                    $inner = $total > 0 ? (int) round(($current / $total) * $sliceSize) : 0;
                    $progress = min(95, $sliceStart + $inner);
                    $fetched = $coursesBeforeProvider + $current;

                    if ($onProgress) {
                        $onProgress(
                            $progress,
                            "{$label}: {$stepLabel}",
                            "{$current} of {$total} courses",
                            $fetched,
                            max($fetched, $coursesBeforeProvider + $total)
                        );
                    }
                });

                $byProvider[$key] = $count;
                $totalCourses += $count;
                $runningFetched = $totalCourses;
            } catch (\Throwable $e) {
                Log::error('Learning provider sync failed', [
                    'provider' => $key,
                    'error' => $e->getMessage(),
                ]);
                $skipped[] = $label.' ('.$e->getMessage().')';
                $byProvider[$key] = 0;
            }
        }

        if ($onProgress) {
            $onProgress(99, 'Finalizing…', null, $totalCourses, max($totalCourses, 1));
        }

        return [
            'providers' => $byProvider,
            'skipped' => $skipped,
            'total' => $totalCourses,
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function testProvider(string $providerKey, array $overrides = []): array
    {
        foreach ($this->providers() as $provider) {
            if ($provider->providerKey() === $providerKey) {
                return $provider->testConnection($overrides);
            }
        }

        return ['ok' => false, 'error' => 'Unknown learning provider.'];
    }

    /**
     * @return list<LearningProviderSync>
     */
    protected function providers(): array
    {
        $providers = [];

        foreach (LearningConfig::providerClasses() as $class) {
            $instance = app($class);
            if ($instance instanceof LearningProviderSync) {
                $providers[] = $instance;
            }
        }

        return $providers;
    }
}
