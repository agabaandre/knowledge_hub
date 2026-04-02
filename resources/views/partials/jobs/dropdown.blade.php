
<select class="form-control select2 text-left" name="{{ $field ?? 'job' }}" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Job' }}">
    <option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Job' }}</option>
   
    @foreach ($jobs as $job)
        @php
            $valueField = $valueField ?? 'id';
            $optionValue = $valueField === 'name' ? $job->name : $job->id;
        @endphp
        <option value="{{ $optionValue }}" {{ ((string)@$selected === (string)$optionValue)?'selected':''}}>{{ $job->name }}</option>
    @endforeach
    
</select>