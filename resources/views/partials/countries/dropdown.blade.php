
<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'country_id' }}" {{ $required ?? '' }} {{ $multiple ?? '' }} {{ $onclick ?? '' }} data-placeholder="{{ $allfield ?? 'Select Country' }}">
    <option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Select Country' }}</option>

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
