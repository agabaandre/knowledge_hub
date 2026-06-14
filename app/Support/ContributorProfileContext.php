<?php

namespace App\Support;

use App\Models\Author;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Resolves the hub user behind a contributor author profile and scopes their publications
 * (including resources credited to an Associated Corporate Source / Member State).
 */
final class ContributorProfileContext
{
    public function __construct(
        public Author $author,
        public ?User $user,
        public ?Author $corporateAuthor,
    ) {}

    public static function resolve(Author $author): self
    {
        $author->loadMissing(['user.country', 'user.lifetimeBadge.badgeType']);

        $user = $author->user;

        if ($user === null && ! empty($author->email)) {
            $user = User::query()->where('email', $author->email)->first();
        }

        if ($user === null && ! empty($author->name)) {
            $user = User::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim((string) $author->name))])
                ->first();
        }

        $corporateAuthor = null;
        if ($user && (int) $user->author_id > 0 && (int) $user->author_id !== (int) $author->id) {
            $corporateAuthor = Author::query()->find((int) $user->author_id);
        }

        return new self($author, $user, $corporateAuthor);
    }

    public function applyPublicationScope(Builder $query): void
    {
        $profileAuthorId = (int) $this->author->id;
        $userId = (int) ($this->user?->id ?? 0);
        $corporateAuthorId = (int) ($this->corporateAuthor?->id ?? 0);

        $query->where(function (Builder $q) use ($profileAuthorId, $userId, $corporateAuthorId) {
            $q->where('author_id', $profileAuthorId);

            // Corporate account uploads only — not every record this user touched as editor/uploader.
            if ($userId > 0 && $corporateAuthorId > 0) {
                $q->orWhere(function (Builder $sub) use ($userId, $corporateAuthorId) {
                    $sub->where('user_id', $userId)
                        ->where('author_id', $corporateAuthorId);
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
        if ($this->user === null || $this->corporateAuthor === null) {
            return 0;
        }

        return (int) (clone $baseQuery)
            ->where('user_id', (int) $this->user->id)
            ->where('author_id', (int) $this->corporateAuthor->id)
            ->count();
    }

    /**
     * Base publication query with listing visibility (matches contributor profile listing).
     */
    public static function publicationBaseQuery(): Builder
    {
        return Publication::query()->where('is_version', 0);
    }
}
