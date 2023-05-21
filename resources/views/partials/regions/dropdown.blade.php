
<select class="form-control select2 text-left form-select" name="{{ $field ?? 'rcc' }}" {{ $required ?? '' }}>
    <option disabled selected value="">Select</option>
    @foreach ($regions as $rcc)
        <option 
        {{ (@$selected == $rcc->id)?'selected':'' }} value="{{$rcc->id}}">
            {{$rcc->region_name}}
        </option>
    @endforeach
</select>