@extends('layouts.app')

@section('styles')

@endsection

@section('content')
    <!-- ============================ Blog Detail Start ================================== -->
<section style="padding-top:120px;" class="gray">
			
<div class="container">
           
<div class="row">

<!-- Blog Detail -->
<div class="col-lg-8 col-md-12 col-sm-12 col-12">
@include('forums.partials.forum_details')
</div>

<!-- Single blog Grid -->
<div class="col-lg-4 col-md-12 col-sm-12 col-12">
    
    <!-- Trending Posts -->
    <div class="single_widgets widget_thumb_post">
        <h4 class="title">Recent Forums</h4>
        <ul>

        @foreach($forums as $forum)
            <li>
                <span class="left">
                    @if(is_valid_image(storage_link("uploads/forums/".$forum->forum_image)))
                        <img class="img-fluid" src="{{ storage_link('uploads/forums/'.$forum->forum_image) }}" alt="">
                    @endif
                </span>
                <span class="right">
                    <a class="feed-title" href="{{ url('forums/thread')}}?id={{$forum->id}}">{!! $forum->forum_title !!}</a> 
                    
                    <span class="post-date"><i class="ti-calendar text-success"></i>
                        {{time_ago($forum->created_at)}}
                    </span>

                </span>
            </li>
        @endforeach
            
        </ul>
    </div>
    
    <!-- Tags  -->
    @if(count($forum->tags))
        <div class="single_widgets widget_tags">
            <h4 class="title">Tags</h4>
            <ul>
            @foreach($forum->tags as $tag)
                <li><a href="{{ url('forums')}}?tag={{$tag->tag}}">{{$tag->tag}}</a></li>
            @endforeach
            </ul>
        </div>
    @endif
    
</div>

</div>
                
</div>
                    
</section>
@endsection