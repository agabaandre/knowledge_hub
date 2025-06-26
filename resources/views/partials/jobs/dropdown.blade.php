
<select class="form-control select2 text-left" name="{{ $field ?? 'job' }}" {{ $required ?? '' }} style="padding:10px auto;" >
    <option disabled>Select</option>
   
    @foreach ($jobs as $job)
        <option>{{$job->name}}</option>
    @endforeach
    
</select>