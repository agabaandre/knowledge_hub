<?php
namespace App\Repositories;

use App\Models\EventTag;
use App\Models\ForumTag;
use App\Models\PublicationTag;
use App\Models\Tag;
use App\View\Composers\TagsViewComposer;
use App\Support\HealthTopicSourceCatalog;
use App\Support\SeoSlugger;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TagsRepository{

    public function get(Request $request, $return_array = false){
        $rows_count = ($request->rows)?$request->rows:20;
        $tags       = Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc');

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            $tags->where(function ($q) use ($term) {
                $q->where('tag_text', 'like', '%'.$term.'%');
                if (Schema::hasColumn('tags', 'overview')) {
                    $q->orWhere('overview', 'like', '%'.$term.'%');
                }
            });
        }

        if ($return_array || $request->boolean('datatable') || $rows_count === 'all' || (int) $rows_count <= 0) {
            return $tags->get();
        }

        $paginator = $tags->paginate((int) $rows_count);
        $paginator->appends($request->only(['term', 'rows']));

        return $paginator;
    }

    /**
     * All tags for replacement dropdowns (admin delete-with-mapping).
     */
    public function allTagsForMapping(): Collection
    {
        return Tag::query()
            ->orderBy('tag_text', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'tag_text']);
    }

    public function save(Request $request){
        $tag = new Tag();
        $tag->tag_text = $request->name;
        if ($request->has('is_health_topic')) {
            $tag->is_health_topic = $request->is_health_topic ? 1 : 0;
        } else {
            $tag->is_health_topic = 1; // default yes
        }
        if ($request->has('is_health_emergency')) {
            $tag->is_health_emergency = $request->is_health_emergency ? 1 : 0;
        } else {
            $tag->is_health_emergency = 0;
        }
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        if (Schema::hasColumn('tags', 'slug') && empty($tag->slug)) {
            $tag->slug = SeoSlugger::forTag((string) ($tag->tag_text ?? ''), null);
        }
        $tag->save();
        TagsViewComposer::forgetTagListCache();

        return $tag;
    }

    public function find($id){

        return Tag::find($id);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return null; // Or handle the case where tag is not found
        }

        $tag->tag_text = $request->tag_text;
        if ($request->has('is_health_topic')) {
            $tag->is_health_topic = $request->is_health_topic ? 1 : 0;
        }
        if ($request->has('is_health_emergency')) {
            $tag->is_health_emergency = $request->is_health_emergency ? 1 : 0;
        }
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
        }
        if (Schema::hasColumn('tags', 'slug') && empty($tag->slug)) {
            $tag->slug = SeoSlugger::forTag((string) ($tag->tag_text ?? ''), (int) $tag->id);
        }
        $tag->save();
        TagsViewComposer::forgetTagListCache();

        return $tag;
    }

    /**
     * Move all usages from $oldTagId to $replacementTagId, then delete the old tag.
     *
     * @return array{status: string, message: string, data?: array<string, int>}
     */
    public function deleteTagWithMapping(int $oldTagId, int $replacementTagId): array
    {
        if ($oldTagId === $replacementTagId) {
            return ['status' => 'failure', 'message' => 'Please select a different tag to map content to.'];
        }

        $old = Tag::find($oldTagId);
        $new = Tag::find($replacementTagId);
        if (! $old || ! $new) {
            return ['status' => 'failure', 'message' => 'Selected tag was not found.'];
        }

        $oldText = (string) $old->tag_text;
        $newText = (string) $new->tag_text;

        return DB::transaction(function () use ($old, $new, $oldText, $newText) {
            $movedPublicationTags = 0;
            $removedPublicationTagDuplicates = 0;

            foreach (PublicationTag::query()->where('tag_id', $old->id)->cursor() as $row) {
                $exists = PublicationTag::query()
                    ->where('publication_id', $row->publication_id)
                    ->where('tag_id', $new->id)
                    ->exists();
                if ($exists) {
                    $row->delete();
                    $removedPublicationTagDuplicates++;
                } else {
                    $row->tag_id = $new->id;
                    $row->save();
                    $movedPublicationTags++;
                }
            }

            $movedForumTags = 0;
            $removedForumTagDuplicates = 0;
            if (Schema::hasTable('forum_tags')) {
                foreach (ForumTag::query()->where('tag', $oldText)->cursor() as $ft) {
                    $dup = ForumTag::query()
                        ->where('forum_id', $ft->forum_id)
                        ->where('tag', $newText)
                        ->exists();
                    if ($dup) {
                        $ft->delete();
                        $removedForumTagDuplicates++;
                    } else {
                        $ft->tag = $newText;
                        $ft->save();
                        $movedForumTags++;
                    }
                }
            }

            $movedEventTags = 0;
            $removedEventTagDuplicates = 0;
            if (Schema::hasTable('event_tags')) {
                foreach (EventTag::query()->where('tag_id', $old->id)->cursor() as $et) {
                    $dup = EventTag::query()
                        ->where('event_id', $et->event_id)
                        ->where('tag_id', $new->id)
                        ->exists();
                    if ($dup) {
                        $et->delete();
                        $removedEventTagDuplicates++;
                    } else {
                        $et->tag_id = $new->id;
                        $et->save();
                        $movedEventTags++;
                    }
                }
            }

            $movedCommunityTags = 0;
            $removedCommunityTagDuplicates = 0;
            if (Schema::hasTable('community_of_practice_tags')) {
                $rows = DB::table('community_of_practice_tags')->where('tag_id', $old->id)->get();
                foreach ($rows as $pivot) {
                    $dup = DB::table('community_of_practice_tags')
                        ->where('community_of_practice_id', $pivot->community_of_practice_id)
                        ->where('tag_id', $new->id)
                        ->exists();
                    if ($dup) {
                        DB::table('community_of_practice_tags')
                            ->where('community_of_practice_id', $pivot->community_of_practice_id)
                            ->where('tag_id', $old->id)
                            ->delete();
                        $removedCommunityTagDuplicates++;
                    } else {
                        DB::table('community_of_practice_tags')
                            ->where('community_of_practice_id', $pivot->community_of_practice_id)
                            ->where('tag_id', $old->id)
                            ->update(['tag_id' => $new->id]);
                        $movedCommunityTags++;
                    }
                }
            }

            $deletedTagId = (int) $old->id;
            $old->delete();

            TagsViewComposer::forgetTagListCache();

            return [
                'status' => 'success',
                'message' => 'Tag deleted and content mapped successfully.',
                'data' => [
                    'deleted_tag_id' => $deletedTagId,
                    'replacement_tag_id' => (int) $new->id,
                    'moved_publication_tags' => $movedPublicationTags,
                    'removed_publication_tag_duplicates' => $removedPublicationTagDuplicates,
                    'moved_forum_tags' => $movedForumTags,
                    'removed_forum_tag_duplicates' => $removedForumTagDuplicates,
                    'moved_event_tags' => $movedEventTags,
                    'removed_event_tag_duplicates' => $removedEventTagDuplicates,
                    'moved_community_tags' => $movedCommunityTags,
                    'removed_community_tag_duplicates' => $removedCommunityTagDuplicates,
                ],
            ];
        });
    }

    /**
     * @return array<string, true>
     */
    public function existingTagKeyMap(): array
    {
        $map = [];
        foreach (Tag::query()->pluck('tag_text') as $name) {
            $key = HealthTopicSourceCatalog::normalizeTagKey((string) $name);
            if ($key !== '') {
                $map[$key] = true;
            }
        }

        return $map;
    }

    /**
     * @param  list<array{tag_text: string, overview: string, is_health_topic?: int, is_health_emergency?: int}>  $topics
     * @return array{imported: int, skipped_duplicates: int, tags: list<Tag>}
     */
    public function importHealthTopics(array $topics): array
    {
        $existing = $this->existingTagKeyMap();
        $imported = 0;
        $skipped = 0;
        $saved = [];

        foreach ($topics as $row) {
            $tagText = HealthTopicSourceCatalog::normalizeTopicName((string) ($row['tag_text'] ?? ''));
            if ($tagText === '' || mb_strlen($tagText) > 255) {
                continue;
            }
            $key = HealthTopicSourceCatalog::normalizeTagKey($tagText);
            if (isset($existing[$key])) {
                $skipped++;
                continue;
            }

            $tag = new Tag();
            $tag->tag_text = $tagText;
            $tag->overview = (string) ($row['overview'] ?? '');
            $tag->is_health_topic = (int) ($row['is_health_topic'] ?? 1);
            $tag->is_health_emergency = (int) ($row['is_health_emergency'] ?? 0);
            if (Schema::hasColumn('tags', 'slug')) {
                $tag->slug = SeoSlugger::forTag($tagText, null);
            }
            $tag->save();

            $existing[$key] = true;
            $imported++;
            $saved[] = $tag;
        }

        if ($imported > 0) {
            TagsViewComposer::forgetTagListCache();
        }

        return [
            'imported' => $imported,
            'skipped_duplicates' => $skipped,
            'tags' => $saved,
        ];
    }

    /**
     * Merge exact duplicate tag names (case-insensitive) into the oldest tag per group.
     *
     * @return array{status: string, message: string, merged_groups: int, merged_tags: int, details: list<array<string, mixed>>}
     */
    public function deduplicateTags(): array
    {
        $groups = [];
        foreach (Tag::query()->orderBy('id')->get() as $tag) {
            $key = HealthTopicSourceCatalog::normalizeTagKey((string) $tag->tag_text);
            if ($key === '') {
                continue;
            }
            $groups[$key][] = $tag;
        }

        $mergedGroups = 0;
        $mergedTags = 0;
        $details = [];

        foreach ($groups as $key => $tags) {
            if (count($tags) < 2) {
                continue;
            }

            $canonical = $tags[0];
            foreach (array_slice($tags, 1) as $duplicate) {
                $result = $this->deleteTagWithMapping((int) $duplicate->id, (int) $canonical->id);
                if (($result['status'] ?? '') === 'success') {
                    $mergedTags++;
                    $details[] = [
                        'canonical_id' => (int) $canonical->id,
                        'canonical_text' => (string) $canonical->tag_text,
                        'merged_id' => (int) $duplicate->id,
                        'merged_text' => (string) $duplicate->tag_text,
                    ];
                }
            }
            $mergedGroups++;
        }

        $message = $mergedTags > 0
            ? "Merged {$mergedTags} duplicate tag(s) across {$mergedGroups} group(s)."
            : 'No duplicate tags found (case-insensitive exact matches).';

        return [
            'status' => 'success',
            'message' => $message,
            'merged_groups' => $mergedGroups,
            'merged_tags' => $mergedTags,
            'details' => $details,
        ];
    }

    public function overviewPlainLength(?string $overview): int
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $overview)) ?? '');

        return mb_strlen($plain);
    }

    public function tagNeedsOverview(?string $overview, int $minChars = 120): bool
    {
        return $this->overviewPlainLength($overview) < $minChars;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Tag>
     */
    public function tagsNeedingOverview(int $minChars = 120, int $limit = 50): Collection
    {
        return Tag::query()
            ->orderBy('tag_text')
            ->get(['id', 'tag_text', 'overview', 'is_health_topic', 'is_health_emergency'])
            ->filter(function (Tag $tag) use ($minChars) {
                return $this->tagNeedsOverview($tag->overview, $minChars);
            })
            ->take($limit)
            ->values();
    }

    public function countTagsNeedingOverview(int $minChars = 120): int
    {
        return Tag::query()
            ->get(['id', 'overview'])
            ->filter(fn (Tag $tag) => $this->tagNeedsOverview($tag->overview, $minChars))
            ->count();
    }

    public function updateOverviewOnly(int $tagId, string $overview): ?Tag
    {
        $tag = Tag::find($tagId);
        if (! $tag) {
            return null;
        }

        $tag->overview = $overview;
        $tag->save();
        TagsViewComposer::forgetTagListCache();

        return $tag;
    }

    /**
     * @param  list<array{tag_id: int, overview: string, force?: bool}>  $updates
     * @return array{updated: int, skipped: int, details: list<array<string, mixed>>}
     */
    public function applyTagOverviews(array $updates, bool $onlyIfLonger = true): array
    {
        $updated = 0;
        $skipped = 0;
        $details = [];

        foreach ($updates as $row) {
            $tagId = (int) ($row['tag_id'] ?? 0);
            $overview = trim((string) ($row['overview'] ?? ''));
            $force = (bool) ($row['force'] ?? false);

            if ($tagId <= 0 || $overview === '') {
                $skipped++;
                continue;
            }

            $tag = Tag::find($tagId);
            if (! $tag) {
                $skipped++;
                continue;
            }

            $existingLen = $this->overviewPlainLength($tag->overview);
            $newLen = $this->overviewPlainLength($overview);

            if ($onlyIfLonger && ! $force && $existingLen >= 120 && $newLen <= $existingLen) {
                $skipped++;
                $details[] = [
                    'tag_id' => $tagId,
                    'tag_text' => $tag->tag_text,
                    'status' => 'skipped_shorter',
                    'existing_length' => $existingLen,
                    'new_length' => $newLen,
                ];
                continue;
            }

            $tag->overview = $overview;
            $tag->save();
            $updated++;
            $details[] = [
                'tag_id' => $tagId,
                'tag_text' => $tag->tag_text,
                'status' => 'updated',
                'existing_length' => $existingLen,
                'new_length' => $newLen,
            ];
        }

        if ($updated > 0) {
            TagsViewComposer::forgetTagListCache();
        }

        return [
            'updated' => $updated,
            'skipped' => $skipped,
            'details' => $details,
        ];
    }

    /**
     * @param  list<array{tag_text: string, overview: string}>  $topics
     * @return list<array{tag_text: string, overview: string, is_duplicate: bool}>
     */
    public function markDuplicateTopics(array $topics): array
    {
        $existing = $this->existingTagKeyMap();
        $out = [];
        $batchKeys = [];

        foreach ($topics as $topic) {
            $tagText = HealthTopicSourceCatalog::normalizeTopicName((string) ($topic['tag_text'] ?? ''));
            $key = HealthTopicSourceCatalog::normalizeTagKey($tagText);
            $isDuplicate = $tagText === '' || isset($existing[$key]) || isset($batchKeys[$key]);
            if ($key !== '' && ! $isDuplicate) {
                $batchKeys[$key] = true;
            }
            $out[] = [
                'tag_text' => $tagText,
                'overview' => (string) ($topic['overview'] ?? ''),
                'is_duplicate' => $isDuplicate,
            ];
        }

        return $out;
    }
}
