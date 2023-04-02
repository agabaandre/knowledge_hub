<select class="form-control js-example-basic-single select2" name="{{$field ?? 'file_type_id'}}" id="file_type" required select2>
<option disabled selected value="">Select</option>
@foreach ($file_types as $filetype)
    <option value="{{$filetype->id}}">
        {{$filetype->name}}
    </option>
@endforeach
</select>