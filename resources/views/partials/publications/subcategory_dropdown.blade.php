<select class="form-control {{ $class ?? 'select2' }}" name="{{ $field ?? 'publication_sub_category_id' }}" id="publication_sub_category_id" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Sub Category' }}">
    <option value="" {{ (@$allfield) ? '' : '' }}>{{ $allfield ?? 'Select Sub Category' }}</option>
    @foreach ($publication_sub_categories ?? [] as $sub)
        <option value="{{ $sub->id }}" {{ (@$selected == $sub->id) ? 'selected' : '' }}>
            {{ $sub->category_name }} @if($sub->parent)({{ $sub->parent->category_name }})@endif
        </option>
    @endforeach
</select>
