<select class="form-control js-example-basic-single theme {{ $class ?? 'select2' }}" name="{{$field ?? 'thematic_area_id'}}" {{ $required ?? '' }} select2>
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select' }}</option>
@foreach ($themes as $theme)
    <option value="{{$theme->id}}" {{ (@$selected == $theme->id)?'selected':''}}>
        {{$theme->description}}
    </option>
@endforeach
</select>

<script>

$('.theme').on('change',function(e){

    var themes  =  @json($themes);
    const theme = themes.find((item)=> item.id === parseFloat(e.target.value));
    theme_subs  = theme.subthemes;

    $('.subtheme').html('');

    theme_subs.forEach(item=>{
    $('.subtheme').append(`<option value="${item.id}">${item.description}</option>`);
    });

    });

</script>