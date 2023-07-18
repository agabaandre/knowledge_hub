<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'publication_catgory_id'}}" id="publication_catgory_id" {{ $required ?? '' }} >
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>>
@foreach ($file_categories as $filecategory)
    <option 
    value="{{$filecategory->id}}"
    {{ (@$selected == $filecategory->id)?'selected':''}}
    >
        {{$filecategory->category_name}}
    </option>
@endforeach
</select>