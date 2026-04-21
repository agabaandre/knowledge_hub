<?php
namespace App\Repositories;

use App\Models\EventTag;
use App\Models\ForumTag;
use App\Models\PublicationTag;
use App\Models\Tag;
use App\View\Composers\TagsViewComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TagsRepository{

    public function get(Request $request, $return_array = false){
        $rows_count = ($request->rows)?$request->rows:20;
        $tags       = Tag::query()->orderBy('tag_text', 'asc')->orderBy('id', 'asc');

        if($request->term)
        $tags->where('tag_text','like','%'.$request->term.'%');

        return ($return_array)?$tags->get():$tags->paginate($rows_count);
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
        }
        if ($request->has('overview')) {
            $tag->overview = $request->overview;
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


}
