
<select class="form-control select2 text-left form-select" name="{{ $field ?? 'country_id' }}" {{ $required ?? '' }}>
    <option disabled>Select</option>
    @foreach ($countries as $country)
        <option 
        {{ (@$selected == $country->id)?'selected':'' }} value="{{$country->id}}">
            {{$country->name}}
        </option>
    @endforeach
</select>