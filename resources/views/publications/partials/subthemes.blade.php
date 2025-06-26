<div class="row justify-content-center mt-3">
    @foreach ($sub_themes as $subtheme)
        <a class="badge theme-primary  m-1"
            href="{{ url('/records') }}?sub_thematic_area_id={{ $subtheme->id }}">{{ $subtheme->description }}</a>
    @endforeach
</div>
