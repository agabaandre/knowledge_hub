<select class="form-control select2" name="{{$field ?? 'geo_area_id'}}" required data-placeholder="Select Geographic Area">
<option disabled selected value="">Select Geographic Area</option>
@foreach ($geoareas as $geoarea)
    <option value="{{$geoarea->id}}" {{($geoarea->id == @$selected)?'selected':''}}>
        {{$geoarea->name ?? $geoarea->country_name}}
    </option>
@endforeach
</select>

@php

 //print_r($geoareas)

@endphp