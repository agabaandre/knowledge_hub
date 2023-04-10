<select class="form-control js-example-basic-single select2" name="{{$field ?? 'file_type_id'}}" id="file_type" {{ $required ?? '' }} >
<option disabled selected value="">Select</option>
@foreach ($file_types as $filetype)
    <option 
    value="{{$filetype->id}}"
    {{ (@$selected == $filetype->id)?'selected':''}}
    >
        {{$filetype->name}}
    </option>
@endforeach
</select>