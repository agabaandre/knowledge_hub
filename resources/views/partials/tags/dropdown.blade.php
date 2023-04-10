
<select class="form-control select2" name="{{ $field ?? 'tags[]' }}" {{ $required ?? '' }} multiple>
    <option disabled>Select</option>
    @foreach ($tags as $tag)
        <option 
        {{ in_array($tag->id,@$selected)?'selected':'' }}
        
        value="{{$tag->id}}">
            {{$tag->tag_text}}
        </option>
    @endforeach
</select>