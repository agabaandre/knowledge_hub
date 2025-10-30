<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'author_id'}}" id="author" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Author' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Author' }}</option>
@php $defaultAuthorId = 1; @endphp
@foreach ($authors as $author)
    @php
        $noExplicitSelection = !isset($selected) || $selected === null || $selected === '';
        $isSelected = isset($selected) && (string)$selected === (string)$author->id;
        $isDefault = $noExplicitSelection && (string)$author->id === (string)$defaultAuthorId;
    @endphp
    <option {{ ($isSelected || $isDefault) ? 'selected' : '' }} value="{{$author->id}}">
        {{$author->name}}
    </option>
@endforeach
</select>