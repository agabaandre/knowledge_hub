@php
    $__catSelected = isset($selected) && $selected !== null && $selected !== '' ? (string) $selected : '';
@endphp
<select class="form-control text-left form-select data_category select2" name="{{ $field ?? 'data_category_id' }}"
    {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Category' }}">
    @if (!empty($allfield))
        <option value="" {{ $__catSelected === '' ? 'selected' : '' }}>{{ $allfield }}</option>
    @else
        <option disabled {{ $__catSelected === '' ? 'selected' : '' }} value="">Select Category</option>
    @endif
    @foreach ($data_categories as $category)
        @if (!empty($exclude_special))
            @if (empty($category->is_special))
                <option {{ $__catSelected !== '' && $category->id == $__catSelected ? 'selected' : '' }} value="{{ $category->id }}">
                    {{ $category->category_name }}
                </option>
            @endif
        @else
            <option {{ $__catSelected !== '' && $category->id == $__catSelected ? 'selected' : '' }} value="{{ $category->id }}">
                {{ $category->category_name }}
            </option>
        @endif
    @endforeach
</select>
