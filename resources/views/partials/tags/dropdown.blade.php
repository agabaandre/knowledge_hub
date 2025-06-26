
<select class="form-control select2 text-left form-select" name="{{ $field ?? 'tags[]' }}" {{ $required ?? '' }} multiple>
    <option disabled>Select</option>
    @foreach ($tags as $tag)
        <option 
        {{ (@$selected)?(in_array($tag->id,@$selected)?'selected':''):'' }}
        
        value="{{$tag->id}}">
            {{$tag->tag_text}}
        </option>
    @endforeach
</select>