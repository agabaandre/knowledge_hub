
<select class="form-control select2 text-left form-select" name="{{ $field ?? 'type_id' }}" {{ $required ?? '' }}>
    <option disabled>Select</option>
    @foreach ($expert_types as $type)
        <option 
        {{ (@$selected == $type->id)?'selected':'' }} value="{{$type->id}}">
            {{$type->type_name}}
        </option>
    @endforeach
</select>