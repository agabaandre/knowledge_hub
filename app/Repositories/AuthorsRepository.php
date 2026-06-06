<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Publication;
use App\Models\User;
use App\Support\SeoSlugger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuthorsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        
        // Per-user forum totals, then sum those by author_id so one row per author (multiple users may share author_id).
        if (Schema::hasTable('forum_engagements')) {
            $forumEngagementPerUser = DB::table('forum_engagements')
                ->select('user_id', DB::raw('SUM(forum_posts + forum_comments) as forum_engagement_total'))
                ->groupBy('user_id');
            $forumEngagementByAuthor = DB::table('users')
                ->leftJoinSub($forumEngagementPerUser, 'fe_sum', function ($join) {
                    $join->on('fe_sum.user_id', '=', 'users.id');
                })
                ->whereNotNull('users.author_id')
                ->select('users.author_id', DB::raw('SUM(COALESCE(fe_sum.forum_engagement_total, 0)) as forum_engagement_total'))
                ->groupBy('users.author_id');
        } else {
            $forumEngagementByAuthor = DB::table('users')
                ->whereNotNull('users.author_id')
                ->select('users.author_id', DB::raw('0 as forum_engagement_total'))
                ->groupBy('users.author_id');
        }

        $publicationCountSubquery = DB::table('publication')
            ->select('author_id', DB::raw('COUNT(*) as publications_count'))
            ->groupBy('author_id');

        // Do not eager-load `publications` (can load thousands of rows per author and cause timeouts / HTTP 500).
        $authors = Author::query()
            ->with(['user.country', 'user.lifetimeBadge.badgeType', 'user.communities'])
            ->leftJoinSub($publicationCountSubquery, 'publication_totals', function ($join) {
                $join->on('publication_totals.author_id', '=', 'author.id');
            })
            ->leftJoinSub($forumEngagementByAuthor, 'forum_by_author', function ($join) {
                $join->on('forum_by_author.author_id', '=', 'author.id');
            })
            ->select('author.*')
            ->selectRaw('COALESCE(publication_totals.publications_count, 0) as publications_count')
            ->selectRaw('COALESCE(forum_by_author.forum_engagement_total, 0) as forum_engagement_total')
            ->selectRaw('(COALESCE(publication_totals.publications_count, 0) + COALESCE(forum_by_author.forum_engagement_total, 0)) as total_contributions');

        if($request->term) {
            $searchTerm = '%'.$request->term.'%';
            $authorIds = Author::leftJoin('users', 'author.id', '=', 'users.author_id')
                ->where(function($query) use ($searchTerm) {
                    $query->where('author.name', 'like', $searchTerm)
                          ->orWhere('users.name', 'like', $searchTerm)
                          ->orWhere('users.job_title', 'like', $searchTerm)
                          ->orWhere('users.organization_name', 'like', $searchTerm);
                })
                ->distinct()
                ->pluck('author.id');
            
            $authors->whereIn('author.id', $authorIds);
        }

        //Access levels effect to query results
        $this->access_filter($authors);

        $result = $authors
            ->orderByDesc('total_contributions')
            ->orderByDesc('publications_count')
            ->orderBy('author.name', 'asc')
            ->paginate($rows_count);

        return  $result;
    }
    
    public function save($name){
        
        $author = new Author();
        $author->name = $name;
        $author->save();
        $this->ensureSlug($author);

        return $author;
    }

    public function find($id){
        $author = Author::with([
            'user.country',
            'user.lifetimeBadge.badgeType',
        ])->find($id);
        if ($author) {
            $this->ensureSlug($author);
        }

        return $author;
    }

    public function findBySlug(string $slug): ?Author
    {
        return Author::with([
            'user.country',
            'user.lifetimeBadge.badgeType',
        ])
            ->where('slug', $slug)
            ->first();
    }

    public function ensureSlug(Author $author): void
    {
        if (! Schema::hasColumn('author', 'slug') || ! empty($author->slug)) {
            return;
        }

        $author->slug = SeoSlugger::forAuthor((string) ($author->name ?? ''), $author->id ?: null);
        $author->saveQuietly();
    }

    public function delete($id): bool
    {
        $author = Author::find($id);
        if (! $author) {
            return false;
        }

        return (bool) $author->delete();
    }

    /**
     * Merge duplicate author records into one: reassign publications and user links (including forum identity via users.author_id), then delete merged authors.
     *
     * @param  int  $keepId  Author id to retain
     * @param  array<int>  $sourceIds  Author ids to absorb and remove
     */
    public function mergeAuthors(int $keepId, array $sourceIds): void
    {
        $sourceIds = array_values(array_unique(array_map('intval', $sourceIds)));
        $sourceIds = array_values(array_filter($sourceIds, fn ($id) => $id > 0 && $id !== $keepId));

        if ($sourceIds === []) {
            throw new \InvalidArgumentException('No authors to merge.');
        }

        DB::transaction(function () use ($keepId, $sourceIds) {
            Publication::query()->whereIn('author_id', $sourceIds)->update(['author_id' => $keepId]);

            if (Schema::hasTable('publication_summaries')) {
                DB::table('publication_summaries')->whereIn('author_id', $sourceIds)->update(['author_id' => $keepId]);
            }

            if (Schema::hasTable('publications_staging')) {
                DB::table('publications_staging')->whereIn('author_id', $sourceIds)->update(['author_id' => $keepId]);
            }

            User::query()->whereIn('author_id', $sourceIds)->update(['author_id' => $keepId]);

            Author::query()->whereIn('id', $sourceIds)->delete();
        });
    }

    public function count(){
        return count(Author::all());
    }


}