<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * Tags that have approved publications, ordered by engagement (views + likes).
     * Only includes tags that have at least one approved, active publication.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function popularByEngagement(int $limit = 20)
    {
        $viewsSub = DB::table('publication_views')
            ->select('publication_id', DB::raw('SUM(views) as total_views'))
            ->groupBy('publication_id');

        $likesSub = DB::table('favourites')
            ->select('publication_id', DB::raw('COUNT(*) as total_likes'))
            ->groupBy('publication_id');

        $tagIdsOrdered = DB::table('publication_tags')
            ->select('publication_tags.tag_id', DB::raw('COALESCE(SUM(pv.total_views), 0) + COALESCE(SUM(fl.total_likes), 0) as engagement'))
            ->join('publication', function ($j) {
                $j->on('publication.id', '=', 'publication_tags.publication_id')
                    ->where('publication.is_active', '=', 'Active')
                    ->where('publication.is_approved', '=', 1);
            })
            ->leftJoinSub($viewsSub, 'pv', 'pv.publication_id', '=', 'publication.id')
            ->leftJoinSub($likesSub, 'fl', 'fl.publication_id', '=', 'publication.id')
            ->groupBy('publication_tags.tag_id')
            ->orderByDesc('engagement')
            ->limit($limit)
            ->pluck('tag_id');

        if ($tagIdsOrdered->isEmpty()) {
            return collect();
        }

        $tags = static::whereIn('id', $tagIdsOrdered)->get();
        $order = $tagIdsOrdered->flip()->all();
        return $tags->sortBy(function ($tag) use ($order) {
            return $order[$tag->id] ?? 999;
        })->values();
    }
}
