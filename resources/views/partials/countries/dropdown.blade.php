
<select class="form-control {{ $class ?? 'select' }} text-left form-select" name="{{ $field ?? 'country_id' }}" {{ $required ?? '' }}>
    <option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
    @foreach ($countries as $country)
        <option 
        {{ (@$selected == $country->id)?'selected':'' }} value="{{$country->id}}">
            {{$country->name}}
        </option>
    @endforeach
</select>