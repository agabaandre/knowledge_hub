
<div class="d-block mb-2 mt-3">
    <span class="text-success">Tags:</span>

    <div class="row">
    @php 
     $colors = ['dark','blue','green'];
    @endphp

    @foreach($tags as $tag)
        <a href="{{ url('records')}}?tag={{$tag->tag_text}}" class="px-3 py-1 mr-1 mt-1 medium bg-{{ $colors[mt_rand(0,2)] }} text-white rounded col-lg-3 text-center">{{ truncate($tag->tag_text,50) }}</a>
    @endforeach

    </div>

</div>