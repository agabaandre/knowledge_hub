@php $homePopularTags = $popular_tags ?? collect(); @endphp
@if($homePopularTags->isNotEmpty())
<div class="d-block mb-2 mt-3" id="tags">
    <span class="text-white">Tags:</span>

    <div class="row px-2 custom-row">
    @php 
     $colors = [settings()->primary_color,settings()->primary_text_color,settings()->icon_font_color];
    @endphp

    @foreach($homePopularTags->take(40) as $tag)
    <div  class="custom-row-item px-3 py-1 mr-1 mt-1 medium rounded col-xs-6  col-sm-6  col-md-3  col-lg-3 text-center text-truncate" title="{{$tag->tag_text}}"
    style="background-color: {{ $colors[mt_rand(0,2)] }}";>
        <a href="{{ tag_records_url($tag) }}" class="text-white ">{{ truncate($tag->tag_text,20) }}</a>
    </div>
    @endforeach

    </div>

</div>
@endif
