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

                            @foreach ($forums as $other)
                                <li>
                                    <span class="left">
                                        @if (is_image($other->forum_image))
                                            <img class="img-fluid" src="{{ $other->forum_image }}" alt="">
                                        @endif
                                    </span>
                                    <span class="right">
                                        <a class="feed-title"
                                            href="{{ url('forums/thread') }}?id={{ $other->id }}">{!! $other->forum_title !!}</a>

                                        <span class="post-date"><i class="ti-calendar text-success"></i>
                                            {{ time_ago($other->created_at) }}
                                        </span>

                                    </span>
                                </li>
                            @endforeach

                        </ul>
                    </div>

                    <!-- Tags  -->
                    @if (count($forum->tags))
                        <div class="single_widgets widget_tags">
                            <h4 class="title">Tags</h4>
                            <ul>
                                @foreach ($forum->tags as $tag)
                                    <li><a href="{{ url('forums') }}?tag={{ $tag->tag }}">{{ $tag->tag }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </section>

    @include('common.ai-summary')
    @include('common.attachment_js')

@endsection

@section('scripts')
    @include('partials.general.summernote')
    <script>
        function copyForumLink(url){
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function(){
                    alert('Link copied to clipboard');
                });
            } else {
                const el = document.createElement('textarea');
                el.value = url; document.body.appendChild(el); el.select();
                try { document.execCommand('copy'); alert('Link copied to clipboard'); } finally { document.body.removeChild(el); }
            }
        }
        // Ensure Summernote initializes on forum comment box with CDN fallback
        $(function(){
            function loadSN(cb){
                if ($.fn && $.fn.summernote) { cb(); return; }
                if (!$('link[href*="summernote"]').length) {
                    $('<link>',{rel:'stylesheet',href:'{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}'}).appendTo('head');
                }
                var s=document.createElement('script');
                s.src='{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}';
                s.onload=cb;
                s.onerror=function(){
                    var cdn=document.createElement('script');
                    cdn.src='https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js';
                    cdn.onload=function(){
                        $('<link>',{rel:'stylesheet',href:'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css'}).appendTo('head');
                        cb();
                    };
                    document.body.appendChild(cdn);
                };
                document.body.appendChild(s);
            }
            loadSN(function(){
                var $el = $('textarea.summernote, textarea.summernote-sm, textarea.summernote-lg');
                if ($el.length && !$el.eq(0).data('summernote')) {
                    try { $el.summernote({height: 150}); } catch(e) {}
                }
            });
        });
    </script>
@endsection

