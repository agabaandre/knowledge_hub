@php
    $showHubCopOptions = $show_hub_cop_options ?? false;
    $tagAllVal = (int) old('tag_all_my_communities', 0);
@endphp
@if ($showHubCopOptions && auth()->check())
    <input type="hidden" name="community_targeting_options" value="1">
    <div class="mb-2 p-2 rounded border bg-light community-targeting-options">
        <div class="d-flex align-items-start w-100">
            <label class="form-check-label mb-0 flex-grow-1 pe-3" for="tag_all_my_communities">
                Tag all my communities (every CoP I am an approved member of)
            </label>
            <div class="ms-auto flex-shrink-0 ps-2 community-targeting-options__toggle">
                <input type="hidden" name="tag_all_my_communities" value="0">
                <input class="form-check-input" type="checkbox" name="tag_all_my_communities" value="1" id="tag_all_my_communities"
                    {{ $tagAllVal === 1 ? 'checked' : '' }}>
            </div>
        </div>
        @once
        <style>
            .community-targeting-options .community-targeting-options__toggle .form-check-input {
                float: none;
                margin-left: 0;
                margin-top: 0.25rem;
            }
        </style>
        @endonce
        <small class="text-muted d-block mt-2">
            Leave the list on <strong>All (visible to everyone)</strong> for a public resource. Choose specific communities (or use this shortcut) to limit visibility to members of those communities (and you as author).
        </small>
    </div>
@endif
<select class="form-control {{ $class ?? 'select2' }} communities-select" name="{{ $field ?? 'communities[]' }}"
    id="communities" {{ $required ?? '' }} multiple data-placeholder="{{ $allfield ?? 'Select Communities (optional)' }}">
    @php
        // Determine if "All" should be selected by default
        $hasSelection = false;
        if (isset($selected)) {
            if (is_array($selected)) {
                $hasSelection = !empty($selected);
            } elseif (is_object($selected) && method_exists($selected, 'isEmpty')) {
                // It's a collection
                $hasSelection = !$selected->isEmpty();
            } elseif (is_object($selected) && method_exists($selected, 'count')) {
                // It's a countable object
                $hasSelection = $selected->count() > 0;
            }
        }
    @endphp
    <option value="" {{ !$hasSelection ? 'selected' : '' }}>All (visible to everyone)</option>
    @foreach ($communities as $comm)
        @php
            $isSelected = false;
            if (isset($selected)) {
                if (is_array($selected)) {
                    $isSelected = in_array($comm->id, $selected);
                } elseif (is_object($selected) && method_exists($selected, 'pluck')) {
                    // It's a collection
                    $isSelected = in_array($comm->id, $selected->pluck('id')->toArray());
                } elseif (is_object($selected) && method_exists($selected, 'contains')) {
                    // It's a collection with contains method
                    $isSelected = $selected->contains('id', $comm->id);
                }
            }
        @endphp
        <option value="{{ $comm->id }}" {{ $isSelected ? 'selected' : '' }}>
            {{ $comm->community_name }}
        </option>
    @endforeach
</select>
<small class="text-muted d-block mt-1">
    @if ($showHubCopOptions)
        Specific communities restrict who can see this on the hub. <strong>All</strong> keeps it visible to everyone.
    @else
        If you select one or more communities, this content will be visible only to members of those communities.
        Leave as <strong>All</strong> to make it publicly visible.
    @endif
</small>
