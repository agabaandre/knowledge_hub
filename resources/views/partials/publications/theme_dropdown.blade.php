<select class="form-control js-example-basic-single select2" name="{{$field ?? 'thematic_area_id'}}" required select2>
<option disabled selected value="">Select</option>
@foreach ($themes as $theme)
    <option value="{{$theme->id}}">
        {{$theme->description}}
    </option>
@endforeach
</select>