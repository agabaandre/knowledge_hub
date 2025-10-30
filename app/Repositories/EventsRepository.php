<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\EventTag;

class EventsRepository
{
    public function getAll()
    {
        return Event::all();
    }

    public function find($id)
    {
        return Event::find($id);
    }

    public function create(array $data)
    {
        // Extract tags if present
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $event = Event::create($data);

        // Save tags if provided
        if (!empty($tags)) {
            $this->saveTags($tags, $event->id);
        }

        return $event;
    }

    public function update(Event $event, array $data)
    {
        // Extract tags if present
        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        $updated = $event->update($data);

        // Update tags if provided
        if ($tags !== null) {
            // Delete existing tags
            EventTag::where('event_id', $event->id)->delete();
            // Save new tags
            if (!empty($tags)) {
                $this->saveTags($tags, $event->id);
            }
        }

        return $updated;
    }

    public function delete(Event $event)
    {
        return $event->delete();
    }

    public function saveTags($tags, $event_id)
    {
        // Optimized: Use bulk insert instead of individual inserts in a loop
        $tagData = [];
        foreach($tags as $tag_id) {
            $tagData[] = [
                'tag_id' => $tag_id,
                'event_id' => $event_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        if (!empty($tagData)) {
            EventTag::insert($tagData);
        }
    }
}
