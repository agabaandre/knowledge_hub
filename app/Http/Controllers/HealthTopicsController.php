<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Publication;
use App\Models\PublicationTag;
use Illuminate\Http\Request;

class HealthTopicsController extends Controller
{
    public function index()
    {
        // Get all tags that are health emergencies
        $tags = Tag::where('is_health_emergency', true)
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
        // Find the tag
        $tag = Tag::where('is_health_emergency', true)
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