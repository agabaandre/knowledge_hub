<select class="form-control js-example-basic-single select2" name="{{$field ?? 'author_id'}}" id="author" {{ $required ?? '' }} >
<option disabled selected value="">Select</option>
@foreach ($authors as $author)
    <option 
    {{ (@$selected)?((@$selected == $author->id)?'selected':''):''}}
     value="{{$author->id}}">
        {{$author->name}}
    </option>
@endforeach
</select>