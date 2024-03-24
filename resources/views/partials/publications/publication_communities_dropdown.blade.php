<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'communities[]'}}" id="communities" {{ $required ?? '' }} multiple placeholder="Choose" >
<option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Choose One or More' }}</option>>
@foreach ($communities as $comm)
    <option 
    value="{{$comm->id}}"
    {{ (in_array($comm->id,@$selected))?'selected':''}}
    >
        {{$comm->community_name}}
    </option>
@endforeach
</select>