
<select class="form-control select2 text-left" name="{{ $field ?? 'job' }}" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Job' }}">
    <option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Job' }}</option>
   
    @foreach ($jobs as $job)
        <option value="{{$job->id}}" {{ (@$selected == $job->id)?'selected':''}}>{{$job->name}}</option>
    @endforeach
    
</select>