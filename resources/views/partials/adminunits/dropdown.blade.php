<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'administrative_unit_id'}}" id="administrative_unit" {{ $required ?? '' }} >
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
@foreach ($adminunits as $adminunit)
    <option 
    {{ (@$selected)?((@$selected == $adminunit->id)?'selected':''):''}}
     value="{{$adminunit->id}}">
        {{$adminunit->name}}
    </option>
@endforeach
</select>