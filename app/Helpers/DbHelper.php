<?php
use App\Models\Publication;
use App\Models\PublicationTag;

function get_tag_ublications($tag){
	
	    $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $publications = cache()->remember('tag_pubs'.$tag->id,$minutes, function () use ($tag){
            $taggedpubs = PublicationTag::whereHas('tag', function ($query) use ($tag){
                                $query->where('is_health_emergency', true);
                                $query->where('id', $tag->id);
                            })->pluck('publication_id');

            $publications = Publication::whereIn('id', $taggedpubs)->take(4)->get();
            return   $publications;
        });

       return $publications;
}