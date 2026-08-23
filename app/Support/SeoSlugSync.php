<?php

namespace App\Support;

use App\Models\Author;
use App\Models\CommunityOfPractice;
use App\Models\Country;
use App\Models\DataCategory;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\SubThemeticArea;
use App\Models\SubjectArea;
use App\Models\Tag;
use App\Models\ThemeticArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SeoSlugSync
{
    /**
     * @return array<string, array{
     *     model: class-string<Model>,
     *     source: string,
     *     column?: string,
     *     generator: callable(string, ?int): string,
     *     constrain?: callable(Builder): Builder
     * }>
     */
    public static function catalog(): array
    {
        return [
            'themes' => [
                'model' => ThemeticArea::class,
                'source' => 'description',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forThematicArea($source, $id),
            ],
            'subthemes' => [
                'model' => SubThemeticArea::class,
                'source' => 'description',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forSubThematicArea($source, $id),
            ],
            'publications' => [
                'model' => Publication::class,
                'source' => 'title',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forPublication($source, $id),
                'constrain' => function (Builder $query) {
                    if (Schema::hasColumn($query->getModel()->getTable(), 'is_version')) {
                        $query->where('is_version', 0);
                    }

                    return $query;
                },
            ],
            'forums' => [
                'model' => Forum::class,
                'source' => 'forum_title',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forForum($source, $id),
            ],
            'tags' => [
                'model' => Tag::class,
                'source' => 'tag_text',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forTag($source, $id),
            ],
            'communities' => [
                'model' => CommunityOfPractice::class,
                'source' => 'community_name',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forCommunity($source, $id),
            ],
            'authors' => [
                'model' => Author::class,
                'source' => 'name',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forAuthor($source, $id),
            ],
            'countries' => [
                'model' => Country::class,
                'source' => 'name',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forCountry($source, $id),
            ],
            'data-categories' => [
                'model' => DataCategory::class,
                'source' => 'category_name',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forDataCategory($source, $id),
            ],
            'subject-areas' => [
                'model' => SubjectArea::class,
                'source' => 'name',
                'generator' => fn (string $source, ?int $id) => SeoSlugger::forSubjectArea($source, $id),
            ],
        ];
    }

    public static function apply(Model $model, string $type, bool $force = false): bool
    {
        $config = self::catalog()[$type] ?? null;
        if ($config === null) {
            return false;
        }

        $table = $model->getTable();
        $column = $config['column'] ?? 'slug';
        $sourceField = $config['source'];

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column) || ! Schema::hasColumn($table, $sourceField)) {
            return false;
        }

        if ($type === 'publications' && Schema::hasColumn($table, 'is_version') && (int) ($model->is_version ?? 0) === 1) {
            return false;
        }

        $current = trim((string) ($model->getAttributes()[$column] ?? $model->{$column} ?? ''));
        $sourceChanged = $model->isDirty($sourceField) || $model->wasChanged($sourceField);

        if (! $force && $current !== '' && ! $sourceChanged) {
            return false;
        }

        $source = trim((string) $model->{$sourceField});
        $id = $model->getKey() ? (int) $model->getKey() : null;
        $next = (string) ($config['generator'])($source, $id);

        if ($next === $current) {
            return false;
        }

        $model->{$column} = $next;

        return true;
    }

    /**
     * @return array{scanned: int, updated: int, skipped: int}
     */
    public static function regenerate(string $type, bool $onlyEmpty = false, bool $dryRun = false): array
    {
        $config = self::catalog()[$type] ?? null;
        if ($config === null) {
            throw new \InvalidArgumentException('Unknown slug type: '.$type);
        }

        /** @var Model $model */
        $model = new $config['model'];
        $table = $model->getTable();
        $column = $config['column'] ?? 'slug';
        $sourceField = $config['source'];

        $scanned = 0;
        $updated = 0;
        $skipped = 0;

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column) || ! Schema::hasColumn($table, $sourceField)) {
            return compact('scanned', 'updated', 'skipped');
        }

        $query = $config['model']::query()->orderBy($model->getKeyName());
        if (isset($config['constrain'])) {
            $query = ($config['constrain'])($query);
        }
        if ($onlyEmpty) {
            $query->where(function ($q) use ($column) {
                $q->whereNull($column)->orWhere($column, '');
            });
        }

        $query->chunkById(200, function ($rows) use ($type, $onlyEmpty, $dryRun, $column, &$scanned, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $scanned++;
                $changed = self::apply($row, $type, ! $onlyEmpty);
                if (! $changed) {
                    $skipped++;
                    continue;
                }
                $updated++;
                if (! $dryRun) {
                    self::persistSlug($row, $column);
                }
            }
        });

        return compact('scanned', 'updated', 'skipped');
    }

    private static function persistSlug(Model $row, string $column): void
    {
        $row->newQueryWithoutScopes()
            ->toBase()
            ->where($row->getKeyName(), $row->getKey())
            ->update([$column => $row->getAttribute($column)]);
    }
}
