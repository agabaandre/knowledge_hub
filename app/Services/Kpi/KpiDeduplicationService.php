<?php

namespace App\Services\Kpi;

use App\Models\Kpi;
use App\Models\KpiDataRecord;
use App\Models\KpiNarration;
use App\Models\SubjectArea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KpiDeduplicationService
{
    public function normalizeKey(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    /**
     * @return array<int, array{reason: string, label: string, keep_id: int, members: array<int, array<string, mixed>>}>
     */
    public function findIndicatorDuplicateGroups(): array
    {
        $kpis = Kpi::query()->with('subjectArea')->withCount(['dataRecords', 'narrations'])->orderBy('id')->get();
        $groups = [];

        $this->collectGroups($groups, $kpis, 'owid_chart_slug', function (Kpi $kpi) {
            $slug = strtolower(trim((string) $kpi->owid_chart_slug));

            return $slug !== '' ? $slug : null;
        }, 'Same OWID chart slug');

        $this->collectGroups($groups, $kpis, 'normalized_name', function (Kpi $kpi) {
            $key = $this->normalizeKey($kpi->name);

            return $key !== '' ? $key : null;
        }, 'Same indicator name');

        $this->collectGroups($groups, $kpis, 'owid_url', function (Kpi $kpi) {
            $url = strtolower(trim((string) $kpi->owid_url));
            if ($url === '') {
                return null;
            }

            return preg_replace('#^https?://(www\.)?ourworldindata\.org/grapher/#', '', $url) ?: $url;
        }, 'Same OWID chart URL');

        $this->collectGroups($groups, $kpis, 'name_subject_area', function (Kpi $kpi) {
            $name = $this->normalizeKey($kpi->name);
            if ($name === '' || ! $kpi->subject_area) {
                return null;
            }

            return $name.'|'.(int) $kpi->subject_area;
        }, 'Same name in subject area');

        return array_values($groups);
    }

    /**
     * @return array<int, array{reason: string, label: string, keep_id: int, members: array<int, array<string, mixed>>}>
     */
    public function findSubjectAreaDuplicateGroups(): array
    {
        $areas = SubjectArea::query()->withCount('kpis')->orderBy('id')->get();
        $groups = [];

        $this->collectGroups($groups, $areas, 'slug', function (SubjectArea $area) {
            $slug = strtolower(trim((string) $area->slug));

            return $slug !== '' ? $slug : null;
        }, 'Same slug');

        $this->collectGroups($groups, $areas, 'normalized_name', function (SubjectArea $area) {
            $key = $this->normalizeKey($area->name);

            return $key !== '' ? $key : null;
        }, 'Same name');

        $this->collectGroups($groups, $areas, 'owid_search', function (SubjectArea $area) {
            $topic = $this->normalizeKey($area->owid_topic);
            $query = $this->normalizeKey($area->owid_search_query);
            if ($topic === '' && $query === '') {
                return null;
            }

            return $topic.'|'.$query;
        }, 'Same OWID topic/search query');

        return array_values($groups);
    }

    /**
     * @return array{groups: int, merged: int, removed: int, errors: array<int, string>}
     */
    public function autoDedupeIndicators(?callable $onProgress = null): array
    {
        $groups = $this->findIndicatorDuplicateGroups();
        $merged = 0;
        $removed = 0;
        $errors = [];
        $total = max(1, count($groups));

        foreach ($groups as $index => $group) {
            if ($onProgress) {
                $onProgress($index + 1, $total, 'Merging: '.$group['label']);
            }

            $keepId = (int) $group['keep_id'];
            $duplicateIds = array_values(array_filter(
                array_map(fn ($member) => (int) $member['id'], $group['members']),
                fn ($id) => $id !== $keepId
            ));

            if ($duplicateIds === []) {
                continue;
            }

            try {
                $result = $this->mergeIndicators($keepId, $duplicateIds);
                $merged++;
                $removed += $result['removed'];
            } catch (\Throwable $e) {
                $errors[] = $group['label'].': '.$e->getMessage();
            }
        }

        return [
            'groups' => count($groups),
            'merged' => $merged,
            'removed' => $removed,
            'errors' => $errors,
        ];
    }

    /**
     * @return array{groups: int, merged: int, removed: int, errors: array<int, string>}
     */
    public function autoDedupeSubjectAreas(?callable $onProgress = null): array
    {
        $groups = $this->findSubjectAreaDuplicateGroups();
        $merged = 0;
        $removed = 0;
        $errors = [];
        $total = max(1, count($groups));

        foreach ($groups as $index => $group) {
            if ($onProgress) {
                $onProgress($index + 1, $total, 'Merging: '.$group['label']);
            }

            $keepId = (int) $group['keep_id'];
            $duplicateIds = array_values(array_filter(
                array_map(fn ($member) => (int) $member['id'], $group['members']),
                fn ($id) => $id !== $keepId
            ));

            if ($duplicateIds === []) {
                continue;
            }

            try {
                $result = $this->mergeSubjectAreas($keepId, $duplicateIds);
                $merged++;
                $removed += $result['removed'];
            } catch (\Throwable $e) {
                $errors[] = $group['label'].': '.$e->getMessage();
            }
        }

        return [
            'groups' => count($groups),
            'merged' => $merged,
            'removed' => $removed,
            'errors' => $errors,
        ];
    }

    /**
     * @return array{removed: int, moved_data: int, moved_narrations: int}
     */
    public function mergeIndicators(int $keepId, array $duplicateIds): array
    {
        $duplicateIds = array_values(array_unique(array_filter(array_map('intval', $duplicateIds), fn ($id) => $id > 0 && $id !== $keepId)));
        if ($duplicateIds === []) {
            return ['removed' => 0, 'moved_data' => 0, 'moved_narrations' => 0];
        }

        $keep = Kpi::query()->findOrFail($keepId);
        $movedData = 0;
        $movedNarrations = 0;

        DB::transaction(function () use ($keep, $duplicateIds, &$movedData, &$movedNarrations) {
            foreach ($duplicateIds as $duplicateId) {
                $duplicate = Kpi::query()->find($duplicateId);
                if (! $duplicate) {
                    continue;
                }

                if (empty($keep->owid_chart_slug) && ! empty($duplicate->owid_chart_slug)) {
                    $keep->owid_chart_slug = $duplicate->owid_chart_slug;
                }
                if (empty($keep->owid_url) && ! empty($duplicate->owid_url)) {
                    $keep->owid_url = $duplicate->owid_url;
                }
                if (empty($keep->unit_label) && ! empty($duplicate->unit_label)) {
                    $keep->unit_label = $duplicate->unit_label;
                }
                if (($keep->status ?? 'draft') !== 'published' && ($duplicate->status ?? '') === 'published') {
                    $keep->status = 'published';
                    $keep->approved_by = $duplicate->approved_by;
                    $keep->approved_at = $duplicate->approved_at;
                }
                if (empty($keep->last_synced_at) && ! empty($duplicate->last_synced_at)) {
                    $keep->last_synced_at = $duplicate->last_synced_at;
                }

                foreach ($duplicate->dataRecords()->get() as $record) {
                    $exists = KpiDataRecord::query()
                        ->where('kpi_id', $keep->id)
                        ->where('country_id', $record->country_id)
                        ->where('period', $record->period)
                        ->exists();
                    if ($exists) {
                        $record->delete();
                    } else {
                        $record->kpi_id = $keep->id;
                        $record->save();
                        $movedData++;
                    }
                }

                foreach ($duplicate->narrations()->get() as $narration) {
                    $exists = KpiNarration::query()
                        ->where('kpi_id', $keep->id)
                        ->where('country_id', $narration->country_id)
                        ->where('period', $narration->period)
                        ->exists();
                    if ($exists) {
                        $narration->delete();
                    } else {
                        $narration->kpi_id = $keep->id;
                        $narration->save();
                        $movedNarrations++;
                    }
                }

                $duplicate->delete();
            }

            $keep->save();
        });

        return [
            'removed' => count($duplicateIds),
            'moved_data' => $movedData,
            'moved_narrations' => $movedNarrations,
        ];
    }

    /**
     * @return array{removed: int, moved_indicators: int}
     */
    public function mergeSubjectAreas(int $keepId, array $duplicateIds): array
    {
        $duplicateIds = array_values(array_unique(array_filter(array_map('intval', $duplicateIds), fn ($id) => $id > 0 && $id !== $keepId)));
        if ($duplicateIds === []) {
            return ['removed' => 0, 'moved_indicators' => 0];
        }

        SubjectArea::query()->findOrFail($keepId);
        $movedIndicators = 0;

        DB::transaction(function () use ($keepId, $duplicateIds, &$movedIndicators) {
            foreach ($duplicateIds as $duplicateId) {
                $duplicate = SubjectArea::query()->find($duplicateId);
                if (! $duplicate) {
                    continue;
                }

                $movedIndicators += Kpi::query()
                    ->where('subject_area', $duplicateId)
                    ->update(['subject_area' => $keepId]);

                $duplicate->delete();
            }
        });

        return [
            'removed' => count($duplicateIds),
            'moved_indicators' => $movedIndicators,
        ];
    }

    public function findExistingIndicatorForChart(array $chart): ?Kpi
    {
        $slug = strtolower(trim((string) ($chart['slug'] ?? '')));
        if ($slug !== '') {
            $bySlug = Kpi::query()->whereRaw('LOWER(owid_chart_slug) = ?', [$slug])->first();
            if ($bySlug) {
                return $bySlug;
            }
        }

        $title = $this->normalizeKey((string) ($chart['title'] ?? $slug));
        if ($title !== '') {
            $candidates = Kpi::query()->get()->filter(function (Kpi $kpi) use ($title) {
                return $this->normalizeKey($kpi->name) === $title;
            });
            if ($candidates->count() === 1) {
                return $candidates->first();
            }
        }

        $url = strtolower(trim((string) ($chart['url'] ?? '')));
        if ($url !== '') {
            return Kpi::query()->whereRaw('LOWER(owid_url) = ?', [$url])->first();
        }

        return null;
    }

    public function ensureUniqueSubjectAreaSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'subject-area';
        $slug = $base;
        $suffix = 2;

        while ($this->subjectAreaSlugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    protected function subjectAreaSlugExists(string $slug, ?int $ignoreId = null): bool
    {
        return SubjectArea::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
            ->exists();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Kpi|SubjectArea>  $items
     * @param  array<string, array<string, mixed>>  $groups
     */
    protected function collectGroups(array &$groups, $items, string $type, callable $keyResolver, string $reason): void
    {
        $buckets = [];

        foreach ($items as $item) {
            $key = $keyResolver($item);
            if ($key === null || $key === '') {
                continue;
            }
            $buckets[$key][] = $item;
        }

        foreach ($buckets as $key => $members) {
            if (count($members) < 2) {
                continue;
            }

            $sorted = collect($members)->sort(function ($a, $b) {
                $scoreA = $this->scoreRecord($a);
                $scoreB = $this->scoreRecord($b);
                if ($scoreA === $scoreB) {
                    return $a->id <=> $b->id;
                }

                return $scoreB <=> $scoreA;
            })->values();

            $keep = $sorted->first();
            $groupKey = $type.':'.$key;
            if (isset($groups[$groupKey])) {
                continue;
            }

            $groups[$groupKey] = [
                'reason' => $reason,
                'label' => $reason.' — '.$this->groupLabel($sorted),
                'keep_id' => (int) $keep->id,
                'members' => $sorted->map(fn ($member) => $this->serializeMember($member))->all(),
            ];
        }
    }

    protected function scoreRecord(Kpi|SubjectArea $record): int
    {
        if ($record instanceof Kpi) {
            $score = 0;
            if ($record->status === 'published') {
                $score += 1000;
            } elseif ($record->status === 'draft') {
                $score += 100;
            }
            if (! empty($record->owid_chart_slug)) {
                $score += 50;
            }
            $score += (int) ($record->data_records_count ?? $record->dataRecords()->count());
            $score += ((int) ($record->narrations_count ?? $record->narrations()->count())) * 2;
            if (! empty($record->last_synced_at)) {
                $score += 10;
            }

            return $score;
        }

        $score = 0;
        if ($record->is_active) {
            $score += 100;
        }
        $score += (int) ($record->kpis_count ?? $record->kpis()->count()) * 5;
        if (! empty($record->owid_topic) || ! empty($record->owid_search_query)) {
            $score += 10;
        }

        return $score;
    }

    protected function serializeMember(Kpi|SubjectArea $member): array
    {
        if ($member instanceof Kpi) {
            return [
                'id' => (int) $member->id,
                'name' => (string) $member->name,
                'status' => (string) ($member->status ?? 'draft'),
                'source' => (string) ($member->source ?? 'manual'),
                'owid_chart_slug' => (string) ($member->owid_chart_slug ?? ''),
                'subject_area' => $member->subjectArea?->name,
                'data_records' => (int) ($member->data_records_count ?? 0),
            ];
        }

        return [
            'id' => (int) $member->id,
            'name' => (string) $member->name,
            'slug' => (string) ($member->slug ?? ''),
            'owid_topic' => (string) ($member->owid_topic ?? ''),
            'owid_search_query' => (string) ($member->owid_search_query ?? ''),
            'indicators' => (int) ($member->kpis_count ?? 0),
            'is_active' => (bool) $member->is_active,
        ];
    }

    protected function groupLabel($members): string
    {
        $first = $members->first();

        return $first instanceof Kpi
            ? (string) $first->name
            : (string) $first->name;
    }
}
