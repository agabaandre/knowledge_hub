<select class="form-control {{ $class ?? 'select2' }}" name="{{ $field ?? 'communities[]' }}"
    id="communities" {{ $required ?? '' }} multiple data-placeholder="{{ $allfield ?? 'All' }}">
    <option value="" selected>All (visible to everyone)</option>
    @foreach ($communities as $comm)
        <option value="{{ $comm->id }}"
            {{ in_array($comm->id, is_array(@$selected) ? $selected : @$selected->pluck('id')->toArray()) ? 'selected' : '' }}>
            {{ $comm->community_name }}
        </option>
    @endforeach
</select>
<small class="text-muted d-block mt-1">
    If you select one or more communities, this content will be visible only to members of those communities.
    Leave as <strong>All</strong> to make it publicly visible.
</small>
