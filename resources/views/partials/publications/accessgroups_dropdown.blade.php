<select class="form-control {{ $class ?? 'select2' }}" name="{{$field ?? 'accessgroups[]'}}" id="accessgroups" {{ $required ?? '' }} multiple data-placeholder="{{ $allfield ?? 'Choose Access Groups' }}" >
<option {{ (@$allfield)?'':'disabled' }}  value="">{{ $allfield ?? 'Choose Access Groups' }}</option>
@foreach ($accessgroups as $grp)
    <option 
    value="{{$grp->id}}"
    {{ (in_array($grp->id,@$selected))?'selected':''}}
    >
        {{$grp->group_name}}
    </option>
@endforeach
</select>