<select class="form-control js-example-basic-single {{ $class ?? 'select2' }}" name="{{$field ?? 'accessgroups[]'}}" id="accessgroups" {{ $required ?? '' }} multiple aria-placeholder="Choose" >
<option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Choose One or More' }}</option>>
@foreach ($accessgroups as $grp)
    <option 
    value="{{$grp->id}}"
    {{ (in_array($grp->id,@$selected))?'selected':''}}
    >
        {{$grp->group_name}}
    </option>
@endforeach
</select>