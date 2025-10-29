<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'file_type_id'}}" id="file_type" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select File Type' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select File Type' }}</option>
@foreach ($file_types as $filetype)
    <option 
    value="{{$filetype->id}}"
    {{ (@$selected == $filetype->id)?'selected':''}}
    >
        {{$filetype->name}}
    </option>
@endforeach
</select>