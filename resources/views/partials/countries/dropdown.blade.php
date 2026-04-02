
<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'country_id' }}" id="{{ $id ?? ($field ?? 'country_id') }}" {{ $required ?? '' }} {{ $multiple ?? '' }} {{ $onclick ?? '' }} data-placeholder="{{ $allfield ?? 'Select Country' }}">
    <option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Select Country' }}</option>

    @php
        $selected = $selected ?? null;
        $isMultiple = !empty($multiple);
    @endphp 
   
    @foreach ($countries as $country)
        @php
            $isSelected = false;
            if ($isMultiple) {
                $selectedValues = is_array($selected) ? $selected : [];
                $isSelected = in_array((int) $country->id, array_map('intval', $selectedValues), true);
            } else {
                $isSelected = (string) $selected !== '' && (int) $selected === (int) $country->id;
            }
        @endphp
        <option {{ $isSelected ? 'selected' : '' }} value="{{$country->id}}">
            {{$country->name}}
        </option>
    @endforeach
</select>
