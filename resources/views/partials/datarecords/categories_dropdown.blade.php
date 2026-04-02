@php
    $__catSelected = '';
    if (isset($selected) && $selected !== null && $selected !== '' && $selected !== 'all') {
        if (is_array($selected)) {
            $ids = array_values(array_unique(array_filter(array_map('intval', $selected), function ($id) {
                return $id > 0;
            })));
            // Single-select UI: show first id when multiple are active (e.g. sidebar facets).
            $__catSelected = $ids !== [] ? (string) $ids[0] : '';
        } else {
            $__catSelected = (string) $selected;
        }
    }
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
