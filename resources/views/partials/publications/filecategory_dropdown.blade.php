<select
    class="form-control {{ $class ?? 'select2' }}"
    name="{{ $field ?? 'publication_catgory_id' }}"
    id="{{ $id ?? ($field ?? 'publication_catgory_id') }}"
    {{ $required ?? '' }}
    data-placeholder="{{ $allfield ?? 'Select Category' }}"
>
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Category' }}</option>
@foreach ($file_categories as $filecategory)
    <option 
    value="{{$filecategory->id}}"
    data-linked-data-categories="{{ ($filecategory->linkedDataCategories ?? collect())->pluck('id')->implode(',') }}"
    {{ (@$selected == $filecategory->id)?'selected':''}}
    >
        {{$filecategory->category_name}}
    </option>
@endforeach
</select>