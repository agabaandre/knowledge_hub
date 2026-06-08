<div class="row justify-content-center mt-3">
    @foreach ($sub_themes as $subtheme)
        <a class="badge theme-primary  m-1"
            href="{{ sub_thematic_area_records_url($subtheme) }}">{{ $subtheme->description }}</a>
    @endforeach
</div>
