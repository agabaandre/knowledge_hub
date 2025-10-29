<select class="form-control {{ $class ?? 'select2' }}" name="{{ $field ?? 'communities[]' }}"
    id="communities" {{ $required ?? '' }} multiple data-placeholder="{{ $allfield ?? 'Choose Communities' }}">
    <option {{ @$allfield ? '' : 'disabled' }} value="">{{ $allfield ?? 'Choose Communities' }}</option>
    @foreach ($communities as $comm)
        <option value="{{ $comm->id }}"
            {{ in_array($comm->id, is_array(@$selected) ? $selected : @$selected->pluck('id')->toArray()) ? 'selected' : '' }}>
            {{ $comm->community_name }}
        </option>
    @endforeach
</select>
