
<select class="form-control {{ $class ?? '' }} text-left form-select" name="{{ $field ?? 'country_id' }}" {{ $required ?? '' }} {{ $multiple ?? '' }} {{ $onclick ?? '' }}>
    <option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Select' }}</option>

    @php
        $selected = $selected ?? false;
    @endphp 
   
    @foreach ($countries as $country)
        <option 
        {{ (((!@$multiple && @$selected ?? null) == $country->id) || ( is_array($selected) && in_array($country->id, $selected)))?'selected':'' }} value="{{$country->id}}">
            {{$country->name}}
        </option>
    @endforeach
</select>