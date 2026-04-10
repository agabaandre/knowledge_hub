@php
    $showHubCopOptions = $show_hub_cop_options ?? false;
    $tagAllVal = (int) old('tag_all_my_communities', 0);
    $alsoPublicVal = (int) old('also_public_with_communities', isset($also_public_on_hub) ? (int) $also_public_on_hub : 1);
@endphp
@if ($showHubCopOptions && auth()->check())
    <input type="hidden" name="community_targeting_options" value="1">
    <div class="mb-2 p-2 rounded border bg-light community-targeting-options">
        {{-- Match Step 1 (External Link / Embedded / Default): inline label wrapping input; flex centers checkbox with text --}}
        <div class="mb-2" style="margin-left: 15px;">
            <label class="form-check-inline mb-0 d-inline-flex align-items-center" for="tag_all_my_communities">
                <input type="hidden" name="tag_all_my_communities" value="0">
                <input type="checkbox" name="tag_all_my_communities" value="1" class="form-check-input mt-0 me-2 flex-shrink-0" id="tag_all_my_communities"
                    {{ $tagAllVal === 1 ? 'checked' : '' }}>
                <span>Tag all my approved communities</span>
            </label>
        </div>
        <div class="mb-0" style="margin-left: 15px;">
            <label class="form-check-inline mb-0 d-inline-flex align-items-center" for="also_public_with_communities">
                <input type="hidden" name="also_public_with_communities" value="0">
                <input type="checkbox" name="also_public_with_communities" value="1" class="form-check-input mt-0 me-2 flex-shrink-0" id="also_public_with_communities"
                    {{ $alsoPublicVal === 1 ? 'checked' : '' }}>
                <span>Also show on main hub (everyone)</span>
            </label>
        </div>
        <small class="text-muted d-block mt-2">
            <strong>Also show on main hub</strong> is on by default so everyone can find your resource. If you pick specific communities (or use “Tag all my approved communities”) and want it <strong>only</strong> for those members—not listed publicly on the hub—<strong>uncheck</strong> that box. It applies when at least one community is linked.
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
        Default: public on the hub. After tagging communities, <strong>uncheck “Also show on main hub”</strong> if you want community-only visibility (members of those CoPs and you).
    @else
        If you select one or more communities, this content will be visible only to members of those communities.
        Leave as <strong>All</strong> to make it publicly visible.
    @endif
</small>
