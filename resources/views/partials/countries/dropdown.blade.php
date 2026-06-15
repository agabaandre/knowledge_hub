
@php
    $selected = $selected ?? null;
    $isMultiple = !empty($multiple);
    $noCountryChosen = $isMultiple
        ? (empty($selected) || (is_array($selected) && count($selected) === 0))
        : ($selected === null || $selected === '' || ! is_numeric($selected));
@endphp
@php
    $selectedValues = $isMultiple && is_array($selected) ? $selected : [];
    $hasAllSelected = ! empty($all_option) && (
        (is_array($selected) && in_array('all', array_map('strval', $selectedValues), true))
        || $selected === 'all'
    );
@endphp
<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'country_id' }}" id="{{ $id ?? ($field ?? 'country_id') }}" {{ $required ?? '' }} {{ $multiple ?? '' }} {{ $onclick ?? '' }} data-placeholder="{{ $allfield ?? 'Select Country' }}">
    @if (!empty($all_option))
        <option value="all" {{ $hasAllSelected ? 'selected' : '' }}>{{ $allfield ?? 'All' }}</option>
    @else
        <option value="" {{ $noCountryChosen ? 'selected' : '' }}>{{ $allfield ?? 'Select Country' }}</option>
    @endif

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
        <option {{ $isSelected ? 'selected' : '' }} value="{{$country->id}}" data-region-id="{{ (int) ($country->region_id ?? 0) }}">
            {{$country->name}}
        </option>
    @endforeach
</select>
