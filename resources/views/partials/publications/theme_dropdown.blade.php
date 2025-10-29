<select class="form-control theme {{ $class ?? 'select2' }}" name="{{$field ?? 'thematic_area_id'}}" {{ $required ?? '' }} data-placeholder="{{ $allfield ?? 'Select Theme' }}">
<option {{ (@$allfield)?'':'disabled' }} selected value="">{{ $allfield ?? 'Select Theme' }}</option>
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
    
    // Re-initialize Select2 after updating options
    $('.subtheme').trigger('change.select2');

    });

</script>
