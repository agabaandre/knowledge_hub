
<select class="form-control select2 text-left form-select" name="{{ $field ?? 'tags[]' }}" {{ $required ?? '' }} multiple data-placeholder="{{ $allfield ?? 'Select Tags' }}">
    <option disabled value="">{{ $allfield ?? 'Select Tags' }}</option>
    @foreach ($tags as $tag)
        <option 
        {{ (@$selected && is_array(@$selected) && in_array($tag->id, @$selected)) ? 'selected' : '' }}
        
        value="{{$tag->id}}">
            {{$tag->tag_text}}
        </option>
    @endforeach
</select>