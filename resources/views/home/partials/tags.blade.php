
<div class="d-block mb-2 mt-3">
    <span class="text-success">Tags:</span>

    <div class="row px-2 custom-row">
    @php 
     $colors = ['dark','blue','green'];
    @endphp

    @foreach($tags as $tag)
    <div  class="custom-row-item px-3 py-1 mr-1 mt-1 medium bg-{{ $colors[mt_rand(0,2)] }} rounded col-xs-6  col-sm-6  col-md-3  col-lg-3 text-center">
        <a href="{{ url('records')}}?tag={{$tag->tag_text}}" class="text-white ">{{ truncate($tag->tag_text,20) }}</a>
    </div>
    @endforeach

    </div>

</div>