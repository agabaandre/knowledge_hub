<?php

namespace App\Support;

use App\Models\Author;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Resolves the hub user behind a contributor author profile and scopes their publications
 * (including resources credited to an Associated Corporate Source / Member State).
 */
final class ContributorProfileContext
{
    /**
     * @param  list<int>  $linkedUserIds
     * @param  list<int>  $aliasAuthorIds
     * @param  list<int>  $corporateCreditAuthorIds
     */
    public function __construct(
        public Author $author,
        public ?User $user,
        public ?Author $corporateAuthor,
        public array $linkedUserIds = [],
        public array $aliasAuthorIds = [],
        public array $corporateCreditAuthorIds = [],
    ) {}

    public static function resolve(Author $author): self
    {
        $author->loadMissing(['user.country', 'user.lifetimeBadge.badgeType']);

        $aliasAuthorIds = static::resolveAliasAuthorIds($author);
        $user = static::resolvePrimaryUser($author, $aliasAuthorIds);
        $linkedUsers = static::resolveLinkedUsers($author, $user, $aliasAuthorIds);
        $linkedUserIds = $linkedUsers
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $profileAuthorId = (int) $author->id;
        $corporateCreditAuthorIds = $linkedUsers
            ->pluck('author_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0 && $id !== $profileAuthorId)
            ->unique()
            ->values()
            ->all();

        $corporateAuthor = null;
        if ($user !== null && (int) $user->author_id > 0 && (int) $user->author_id !== $profileAuthorId) {
            $corporateAuthor = Author::query()->find((int) $user->author_id);
        } elseif ($corporateCreditAuthorIds !== []) {
            $corporateAuthor = Author::query()->find($corporateCreditAuthorIds[0]);
        }

        return new self(
            $author,
            $user,
            $corporateAuthor,
            $linkedUserIds,
            $aliasAuthorIds,
            $corporateCreditAuthorIds,
        );
    }

    public function applyPublicationScope(Builder $query): void
    {
        $profileAuthorId = (int) $this->author->id;
        $linkedUserIds = $this->linkedUserIds;
        $creditAuthorIds = $this->corporateCreditAuthorIds;

        $query->where(function (Builder $q) use ($profileAuthorId, $linkedUserIds, $creditAuthorIds) {
            $q->where('author_id', $profileAuthorId);

            if ($linkedUserIds !== [] && $creditAuthorIds !== []) {
                $q->orWhere(function (Builder $sub) use ($linkedUserIds, $creditAuthorIds) {
                    $sub->whereIn('user_id', $linkedUserIds)
                        ->whereIn('author_id', $creditAuthorIds);
                });
            }
        });
    }

    public function countDirectPublications(Builder $baseQuery): int
    {
        return (int) (clone $baseQuery)
            ->where('author_id', (int) $this->author->id)
            ->count();
    }

    public function countCorporatePublications(Builder $baseQuery): int
    {
        if ($this->linkedUserIds === [] || $this->corporateCreditAuthorIds === []) {
            return 0;
        }

        return (int) (clone $baseQuery)
            ->whereIn('user_id', $this->linkedUserIds)
            ->whereIn('author_id', $this->corporateCreditAuthorIds)
            ->count();
    }

    /**
     * Base publication query with listing visibility (matches contributor profile listing).
     */
    public static function publicationBaseQuery(): Builder
    {
        return Publication::query()->where('is_version', 0);
    }

    /**
     * Author records that represent the same person as the profile (e.g. "Andrew Agaba" vs "Agaba Andrew").
     *
     * @return list<int>
     */
    private static function resolveAliasAuthorIds(Author $author): array
    {
        $ids = collect([(int) $author->id]);

        if (empty($author->name)) {
            return $ids->unique()->values()->all();
        }

        $variants = static::nameMatchVariants((string) $author->name);
        $aliasAuthors = Author::query()
            ->where(function (Builder $q) use ($variants) {
                foreach ($variants as $variant) {
                    $q->orWhereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($variant))]);
                }
            })
            ->pluck('id');

        return $ids
            ->merge($aliasAuthors)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private static function resolvePrimaryUser(Author $author, array $aliasAuthorIds): ?User
    {
        $user = $author->user;

        if ($user === null && ! empty($author->email)) {
            $user = User::query()->where('email', $author->email)->first();
        }

        if ($user === null && $aliasAuthorIds !== []) {
            $user = User::query()
                ->whereIn('author_id', $aliasAuthorIds)
                ->orderByDesc('id')
                ->first();
        }

        if ($user === null && ! empty($author->name)) {
            $variants = static::nameMatchVariants((string) $author->name);
            $matches = User::query()
                ->where(function (Builder $q) use ($variants) {
                    foreach ($variants as $variant) {
                        $q->orWhereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($variant))]);
                    }
                })
                ->get();

            $profileAuthorId = (int) $author->id;
            $user = $matches->first(fn (User $candidate) => (int) $candidate->author_id === $profileAuthorId)
                ?? $matches->first(fn (User $candidate) => in_array((int) $candidate->author_id, $aliasAuthorIds, true))
                ?? $matches->first();
        }

        return $user;
    }

    /**
     * @return Collection<int, User>
     */
    private static function resolveLinkedUsers(Author $author, ?User $primaryUser, array $aliasAuthorIds): Collection
    {
        $users = collect();

        if ($primaryUser !== null) {
            $users->push($primaryUser);
        }

        if ($aliasAuthorIds !== []) {
            $users = $users->merge(
                User::query()->whereIn('author_id', $aliasAuthorIds)->get()
            );
        }

        if (! empty($author->email)) {
            $users = $users->merge(
                User::query()->where('email', $author->email)->get()
            );
        }

        return $users->unique('id');
    }

    /**
     * @return list<string>
     */
    private static function nameMatchVariants(string $name): array
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return [];
        }

        $parts = preg_split('/\s+/', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || count($parts) < 2) {
            return [$trimmed];
        }

        return array_values(array_unique([
            $trimmed,
            implode(' ', array_reverse($parts)),
        ]));
    }
}
