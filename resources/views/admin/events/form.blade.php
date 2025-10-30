@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" placeholder="Event title" value="{{ old('title', $event->title ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="event_description" class="form-control summernote" rows="8" placeholder="Describe the agenda, speakers and purpose" required>{{ old('description', $event->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Schedule</label>
            <div class="form-row">
                <div class="col-12 mb-2">
                    <input type="datetime-local" name="startdate" class="form-control" value="{{ old('startdate', isset($event->startdate)?\Carbon\Carbon::parse($event->startdate)->format('Y-m-d\\TH:i'):'') }}" required>
                </div>
                <div class="col-12">
                    <input type="datetime-local" name="enddate" class="form-control" value="{{ old('enddate', isset($event->enddate)?\Carbon\Carbon::parse($event->enddate)->format('Y-m-d\\TH:i'):'') }}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Delivery Mode</label>
            <select name="is_online" id="is_online" class="form-control" required>
                <option value="1" {{ old('is_online', $event->is_online ?? 0)==1?'selected':'' }}>Online</option>
                <option value="0" {{ old('is_online', $event->is_online ?? 0)==0?'selected':'' }}>In-person</option>
            </select>
        </div>
        <div class="form-group" id="venue_group">
            <label>Venue</label>
            <input type="text" name="venue" class="form-control" placeholder="e.g., AU HQ, Addis Ababa" value="{{ old('venue', $event->venue ?? '') }}">
        </div>
        <div class="form-group">
            <label>Organizer</label>
            <input type="text" name="organized_by" class="form-control" placeholder="Organizing entity" value="{{ old('organized_by', $event->organized_by ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Tags/Health Topics</label>
            @php
                $selectedTags = old('tags', isset($event) && $event->tags ? $event->tags->pluck('tag_id')->toArray() : []);
            @endphp
            @include('partials.tags.dropdown', [
                'field' => 'tags[]',
                'selected' => $selectedTags
            ])
            <small class="form-text text-muted">Select relevant tags to help categorize this event</small>
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    @foreach(['active','pending','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $event->status ?? '')==$s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label>Contact Person</label>
                <input type="text" name="contact_person" class="form-control" placeholder="Name / email / phone" value="{{ old('contact_person', $event->contact_person ?? '') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label>Links</label>
            <input type="url" name="event_link" class="form-control mb-2" placeholder="Event/Agenda link (optional)" value="{{ old('event_link', $event->event_link ?? '') }}">
            <input type="url" name="registration_link" class="form-control" placeholder="Registration link (optional)" value="{{ old('registration_link', $event->registration_link ?? '') }}">
        </div>
        <div class="form-group">
            <label>Banner Image URL</label>
            <input type="text" name="banner_image" class="form-control" placeholder="https://.../banner.jpg" value="{{ old('banner_image', $event->banner_image ?? '') }}">
        </div>
    </div>
</div>

<div class="text-right">
    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">Save</button>
    @isset($event)
    <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline-primary">Preview</a>
    @endisset
    
</div>


