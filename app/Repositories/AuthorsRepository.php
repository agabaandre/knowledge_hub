<?php
namespace App\Repositories;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:24;
        
        $forumEngagementSubquery = DB::table('forum_engagements')
            ->select('user_id', DB::raw('SUM(forum_posts + forum_comments) as forum_engagement_total'))
            ->groupBy('user_id');
        $publicationCountSubquery = DB::table('publication')
            ->select('author_id', DB::raw('COUNT(*) as publications_count'))
            ->groupBy('author_id');

        $authors = Author::query()
            ->with(['user.country', 'user.badges.badgeType', 'user.communities', 'publications'])
            ->leftJoin('users as author_users', 'author_users.author_id', '=', 'author.id')
            ->leftJoinSub($publicationCountSubquery, 'publication_totals', function ($join) {
                $join->on('publication_totals.author_id', '=', 'author.id');
            })
            ->leftJoinSub($forumEngagementSubquery, 'forum_totals', function ($join) {
                $join->on('forum_totals.user_id', '=', 'author_users.id');
            })
            ->select('author.*')
            ->selectRaw('COALESCE(publication_totals.publications_count, 0) as publications_count')
            ->selectRaw('COALESCE(forum_totals.forum_engagement_total, 0) as forum_engagement_total')
            ->selectRaw('(COALESCE(publication_totals.publications_count, 0) + COALESCE(forum_totals.forum_engagement_total, 0)) as total_contributions');

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

        return $author;
    }

    public function find($id){

        return Author::find($id);
    }

    public function delete($id){

        return Author::find($id)->delete();
    }

    public function count(){
        return count(Author::all());
    }


}