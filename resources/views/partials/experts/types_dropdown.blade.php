<select class="form-control select2 text-left form-select" name="{{ $field ?? 'type_id' }}" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Type' }}">
    <option disabled value="">{{ $allfield ?? 'Select Type' }}</option>
    @foreach ($expert_types as $type)
        <option 
        {{ (@$selected == $type->id)?'selected':'' }} value="{{$type->id}}">
            {{$type->name}}
        </option>
    @endforeach
</select>