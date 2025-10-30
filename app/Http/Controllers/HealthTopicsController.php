<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Publication;
use App\Models\PublicationTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema as DBSchema;

class HealthTopicsController extends Controller
{
    public function index()
    {
        // Get all tags that are marked as health topics
        $tags = Tag::where(function($q){
                    if (DBSchema::hasColumn('tags','is_health_topic')) {
                        $q->where('is_health_topic', true);
                    } else {
                        $q->where('is_health_emergency', true);
                    }
                  })
                  ->orderBy('tag_text')
                  ->get();

        // Group tags by first letter
        $groupedTags = $tags->groupBy(function ($tag) {
            return strtoupper(substr($tag->tag_text, 0, 1));
        });

        return view('health-topics.index', compact('groupedTags'));
    }

    public function show($id)
    {
        // Find the tag (health topic)
        $tag = Tag::where(function($q){
                    if (DBSchema::hasColumn('tags','is_health_topic')) {
                        $q->where('is_health_topic', true);
                    } else {
                        $q->where('is_health_emergency', true);
                    }
                 })
                 ->findOrFail($id);

        // Get publications tagged with this tag
        $publicationIds = PublicationTag::where('tag_id', $id)
                                      ->pluck('publication_id');

        $publications = Publication::whereIn('id', $publicationIds)
                                 ->with(['author', 'file_type'])
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(12);

        return view('health-topics.show', compact('tag', 'publications'));
    }
} 