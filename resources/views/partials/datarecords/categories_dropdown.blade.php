<select class="form-control text-left form-select data_category select2" name="{{ $field ?? 'data_category_id' }}"
    {{ $required ?? '' }} data-placeholder="Select Category">
    <option disabled {{ (@$selected == '') ? 'selected' : '' }} value="">Select Category</option>
    @foreach ($data_categories as $category)
        @if (!empty($exclude_special))
            @if (empty($category->is_special))
                <option {{ (@$selected == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">
                    {{ $category->category_name }}
                </option>
            @endif
        @else
            <option {{ (@$selected == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">
                {{ $category->category_name }}
            </option>
        @endif
    @endforeach
</select>
