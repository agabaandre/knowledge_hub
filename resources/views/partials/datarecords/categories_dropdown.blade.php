
<select class="form-control select2 text-left form-select data_category" name="{{ $field ?? 'data_category_id' }}" {{ $required ?? '' }}>
    <option disabled selected>Select</option>
    @foreach ($data_categories as $category)

        @if(@$exclude_special)

        @if(!$category->is_special)
            <option 
            {{ (@$selected == $category->id)?'selected':'' }} value="{{$category->id}}">
                {{$category->category_name}}
            </option>
        @endif

        @else
        <option 
        {{ (@$selected == $category->id)?'selected':'' }} value="{{$category->id}}">
            {{$category->category_name}}
        </option>
        @endif
    @endforeach
</select>