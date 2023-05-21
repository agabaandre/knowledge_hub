<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'thematic_area_id'}}" {{ $required ?? '' }} select2>
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
@foreach ($themes as $theme)
    <option value="{{$theme->id}}" {{ (@$selected == $theme->id)?'selected':''}}>
        {{$theme->description}}
    </option>
@endforeach
</select>