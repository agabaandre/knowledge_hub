<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'rcc' }}"
    id="{{ $field ?? 'rcc' }}" {{ $required ?? '' }} {{ $multiple ?? '' }} data-placeholder="{{ $allfield ?? 'Select Region' }}">

    <option {{ @$allfield ? '' : 'disabled' }} value="all">
        {{ $allfield ?? 'Select Region' }}</option>


    @foreach ($regions as $rcc)
        <option
            {{ @$selected == $rcc->id || (is_array(@$selected) && in_array($rcc->id, @$selected)) ? 'selected' : '' }}
            value="{{ $rcc->id }}">
            {{ $rcc->region_name }}
        </option>
    @endforeach
</select>
