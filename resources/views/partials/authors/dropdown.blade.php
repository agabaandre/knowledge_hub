<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'author_id'}}" id="{{ $id ?? ($field ?? 'author_id') }}" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Author' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Author' }}</option>
@foreach ($authors as $author)
    @php
        $isSelected = isset($selected) && (string)$selected === (string)$author->id;
    @endphp
    <option {{ $isSelected ? 'selected' : '' }} value="{{$author->id}}">
        {{$author->name}}
    </option>
@endforeach
</select>