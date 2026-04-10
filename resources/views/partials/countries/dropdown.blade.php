
@php
    $selected = $selected ?? null;
    $isMultiple = !empty($multiple);
    $noCountryChosen = $isMultiple
        ? (empty($selected) || (is_array($selected) && count($selected) === 0))
        : ($selected === null || $selected === '' || ! is_numeric($selected));
@endphp
<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'country_id' }}" id="{{ $id ?? ($field ?? 'country_id') }}" {{ $required ?? '' }} {{ $multiple ?? '' }} {{ $onclick ?? '' }} data-placeholder="{{ $allfield ?? 'Select Country' }}">
    <option value="" {{ $noCountryChosen ? 'selected' : '' }}>{{ $allfield ?? 'Select Country' }}</option>

    @foreach ($countries as $country)
        @php
            $isSelected = false;
            if ($isMultiple) {
                $selectedValues = is_array($selected) ? $selected : [];
                $isSelected = in_array((int) $country->id, array_map('intval', $selectedValues), true);
            } else {
                $isSelected = $selected !== null && $selected !== '' && is_numeric($selected) && (int) $selected === (int) $country->id;
            }
        @endphp
        <option {{ $isSelected ? 'selected' : '' }} value="{{$country->id}}">
            {{$country->name}}
        </option>
    @endforeach
</select>
