<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Repositories\PublicationsRepository;

class EventsController extends Controller
{
    private $publicationsRepo;

    public function __construct(PublicationsRepository $publicationsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
    }

    public function show($id, Request $request)
    {
        $event = Event::with('tags')->findOrFail($id);
        
        // Get event tag IDs for filtering related events
        $eventTagIds = $event->tags->pluck('tag_id')->toArray();
        
        // Handle search (using 'term' parameter to match records page style)
        $searchTerm = $request->get('term') ?? $request->get('search');
        $searchResults = null;
        
        if ($searchTerm) {
            $searchQuery = Event::where('id', '!=', $event->id)
                ->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('venue', 'like', '%' . $searchTerm . '%')
                      ->orWhere('organized_by', 'like', '%' . $searchTerm . '%');
                });
            
            // If current event has tags, prioritize events with matching tags
            if (!empty($eventTagIds)) {
                $taggedEventIds = \App\Models\EventTag::whereIn('tag_id', $eventTagIds)
                    ->where('event_id', '!=', $event->id)
                    ->pluck('event_id')
                    ->unique()
                    ->toArray();
                
                if (!empty($taggedEventIds)) {
                    $searchQuery->orderByRaw('CASE WHEN id IN (' . implode(',', $taggedEventIds) . ') THEN 0 ELSE 1 END')
                                ->orderBy('startdate', 'desc');
                } else {
                    $searchQuery->orderBy('startdate', 'desc');
                }
            } else {
                $searchQuery->orderBy('startdate', 'desc');
            }
            
            $searchResults = $searchQuery->paginate(20);
        }
        
        // Get related past events - prioritize by organizer, fallback to all past events
        $query = Event::where('id', '!=', $event->id)
            ->where(function($q) {
                // Get past events
                $q->where(function($w) {
                    $w->whereNotNull('enddate')
                      ->where('enddate', '<', now());
                })
                ->orWhere(function($w) {
                    $w->whereNull('enddate')
                      ->whereNotNull('startdate')
                      ->where('startdate', '<', now());
                });
            });
        
        // Try to match by organizer first if available
        if (!empty($event->organized_by)) {
            $relatedEvents = (clone $query)
                ->where('organized_by', $event->organized_by)
                ->orderBy('startdate', 'desc')
                ->take(10)
                ->get();
        } else {
            $relatedEvents = collect();
        }
        
        // If no related events by organizer, get any past events
        if ($relatedEvents->isEmpty()) {
            $relatedEvents = $query
                ->orderBy('startdate', 'desc')
                ->take(10)
                ->get();
        }
        
        // Get latest publications (5 per category) - similar to home page
        $latestRequest = clone $request;
        $latestRequest->merge(['rows' => 5]);
        $latestPublications = $this->publicationsRepo->get($latestRequest);
        
        // Get related publications - you can customize this based on event tags, category, etc.
        $relatedRequest = clone $request;
        $relatedRequest->merge(['rows' => 5]);
        // For now, just get recent publications as related
        // You can enhance this to match by event tags, themes, etc.
        $relatedPublications = $this->publicationsRepo->get($relatedRequest);
        
        return view('events.show', compact('event', 'relatedEvents', 'latestPublications', 'relatedPublications', 'searchTerm', 'searchResults'));
    }
}


