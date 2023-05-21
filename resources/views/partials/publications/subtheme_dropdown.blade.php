<select class="form-control js-example-basic-single select2" name="{{$field ?? 'sub_thematic_area_id'}}"  {{ $required ?? '' }} select2>
<option disabled selected value="">Select</option>
@foreach ($subthemes as $subtheme)
    <option value="{{$subtheme->id}}">
        {{$subtheme->description}}
    </option>
@endforeach
</select>