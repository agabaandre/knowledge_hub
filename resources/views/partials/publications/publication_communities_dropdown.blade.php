@php
    $showHubCopOptions = $show_hub_cop_options ?? false;
    $alsoPublicVal = (int) old('also_public_with_communities', isset($also_public_on_hub) ? (int) $also_public_on_hub : 0);
    $tagAllVal = (int) old('tag_all_my_communities', 0);
@endphp
@if ($showHubCopOptions && auth()->check())
    <input type="hidden" name="community_targeting_options" value="1">
    <div class="mb-2 p-2 rounded border bg-light">
        <div class="form-check mb-2">
            <input type="hidden" name="tag_all_my_communities" value="0">
            <input class="form-check-input" type="checkbox" name="tag_all_my_communities" value="1" id="tag_all_my_communities"
                {{ $tagAllVal === 1 ? 'checked' : '' }}>
            <label class="form-check-label" for="tag_all_my_communities">
                Tag all my communities (every CoP I am an approved member of)
            </label>
        </div>
        <div class="form-check">
            <input type="hidden" name="also_public_with_communities" value="0">
            <input class="form-check-input" type="checkbox" name="also_public_with_communities" value="1" id="also_public_with_communities"
                {{ $alsoPublicVal === 1 ? 'checked' : '' }}>
            <label class="form-check-label" for="also_public_with_communities">
                Also show on the main Knowledge Hub (everyone), not only inside communities
            </label>
        </div>
        <small class="text-muted d-block mt-1">
            Use the second option when you keep <strong>All (visible to everyone)</strong> and add communities, or when you want both public discovery and community pages.
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
        Without “Also show on the main Knowledge Hub”, choosing specific communities limits visibility to members of those communities (plus you as author). With that option checked, the item stays public on the hub and appears in the selected communities.
    @else
        If you select one or more communities, this content will be visible only to members of those communities.
        Leave as <strong>All</strong> to make it publicly visible.
    @endif
</small>
