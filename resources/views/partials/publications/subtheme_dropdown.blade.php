<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'sub_thematic_area_id'}}"  {{ $required ?? '' }} select2>
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
@foreach ($subthemes as $subtheme)
    <option value="{{$subtheme->id}}"  {{ (@$selected == $subtheme->id)?'selected':''}}>
        {{$subtheme->description}}
    </option>
@endforeach
</select>