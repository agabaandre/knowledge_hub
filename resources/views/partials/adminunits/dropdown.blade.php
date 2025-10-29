<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'administrative_unit_id'}}" id="administrative_unit" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Administrative Unit' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Administrative Unit' }}</option>
@foreach ($adminunits as $adminunit)
    <option 
    {{ (@$selected)?((@$selected == $adminunit->id)?'selected':''):''}}
     value="{{$adminunit->id}}">
        {{$adminunit->name}}
    </option>
@endforeach
</select>