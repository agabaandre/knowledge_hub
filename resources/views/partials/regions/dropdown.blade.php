
<select class="form-control {{ $class ?? 'select2' }} text-left form-select" name="{{ $field ?? 'rcc' }}" id="{{ $field ?? 'rcc' }}" {{ $required ?? '' }}>
    <option {{ (@$allfield) ? '' : 'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
    @foreach ($regions as $rcc)
        <option 
        {{ (@$selected == $rcc->id) ? 'selected' : '' }} value="{{$rcc->id}}">
            {{$rcc->region_name}}
        </option>
    @endforeach
</select>