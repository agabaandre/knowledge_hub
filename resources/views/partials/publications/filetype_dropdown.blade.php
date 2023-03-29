<select class="form-control js-example-basic-single select2" name="sub_thematic_area_id" required select2>
<option disabled>Select</option>
@foreach ($file_types as $filetype)
    <option value="{{$filetype->id}}">
        {{$filetype->name}}
    </option>
@endforeach
</select>