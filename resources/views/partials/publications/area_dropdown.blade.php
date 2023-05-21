<select class="form-control js-example-basic-single select2" name="{{$field ?? 'geo_area_id'}}" required select2>
<option disabled selected value="">Select</option>
@foreach ($geoareas as $geoarea)
    <option value="{{$geoarea->id}}">
        {{$geoarea->name}}
    </option>
@endforeach
</select>