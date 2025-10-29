<select class="form-control subtheme {{ $class ?? 'select2' }}" name="{{$field ?? 'sub_thematic_area_id'}}" {{ $multiple ?? '' }}  {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Sub-Theme' }}">
@if(!@$multiple)
    <option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Sub-Theme' }}</option>
@endif
@foreach ($subthemes as $subtheme)
    <option value="{{$subtheme->id}}"  {{ ( (!is_array(@$selected) && @$selected == $subtheme->id) || (is_array(@$selected) && in_array($subtheme->id,@$selected)))?'selected':''}}>
        {{$subtheme->description}}
    </option>
@endforeach
</select>