@if(admin_units_enabled())
    @php
        $current = old('public_availability', isset($row) && isset($row->public_availability)
            ? (int) $row->public_availability
            : (isset($publication) && isset($publication->public_availability)
                ? (int) $publication->public_availability
                : (isset($forum) && isset($forum->public_availability)
                    ? (int) $forum->public_availability
                    : (public_availability_default() ? 1 : 0))));
        $isPublic = (int) $current === 1;
    @endphp
    <div class="col-md-12 mb-2">
        <div class="form-check">
            <input type="hidden" name="public_availability" value="0">
            <input class="form-check-input" type="checkbox" name="public_availability" id="public_availability" value="1" {{ $isPublic ? 'checked' : '' }}>
            <label class="form-check-label" for="public_availability">
                Make available to other Knowledge Hubs (continental federation)
            </label>
        </div>
        <small class="text-muted d-block mt-1">
            Uncheck to limit this content to this country hub only. Continental portals always publish publicly by default.
        </small>
    </div>
@endif
