<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'author_id'}}" id="author" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Author' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Author' }}</option>
@foreach ($authors as $author)
    <option 
    {{ (@$selected)?((@$selected == $author->id)?'selected':''):''}}
     value="{{$author->id}}">
        {{$author->name}}
    </option>
@endforeach
</select>