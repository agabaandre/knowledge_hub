@php
    $commentCount = (int) ($forum->comments_count ?? count($forum->comments ?? []));
    $likeCount = (int) ($forum->likes_count ?? count($forum->likes ?? []));
    $viewCount = (int) ($forum->views ?? 0);
@endphp
<div class="community-forum-card mb-3">
    <div class="community-forum-card__intro">
        @if(!empty($forum->forum_image))
            <a href="{{ forum_thread_url($forum) }}" class="community-forum-card__thumb-wrap">
                <img src="{{ $forum->forum_image }}" alt="" class="community-forum-card__thumb" loading="lazy">
            </a>
        @else
            <a href="{{ forum_thread_url($forum) }}" class="community-forum-card__thumb-wrap community-forum-card__thumb-wrap--placeholder">
                <i class="fa fa-comments" aria-hidden="true"></i>
            </a>
        @endif
        <div class="community-forum-card__body">
            <h3 class="community-forum-card__title">
                <a href="{{ forum_thread_url($forum) }}">{!! $forum->forum_title !!}</a>
            </h3>
            <p class="community-forum-card__excerpt">
                {!! Str::words(strip_tags($forum->forum_description ?? ''), 35, '...') !!}
            </p>
            @if($forum->tags && $forum->tags->count() > 0)
                <div class="community-forum-card__tags">
                    @foreach($forum->tags->take(6) as $tag)
                        <span class="community-forum-card__tag">#{{ $tag->tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="community-forum-card__footer">
        <div class="community-forum-card__meta">
            <span class="notranslate" translate="no"><i class="fa fa-user mr-1"></i>{{ $forum->user->name ?? 'Unknown' }}</span>
            <span><i class="fa fa-clock-o mr-1"></i>{{ time_ago($forum->created_at) }}</span>
            @if($commentCount > 0)
                <span><i class="fa fa-comments mr-1"></i>{{ $commentCount }}</span>
            @endif
            @if($likeCount > 0)
                <span><i class="fa fa-heart mr-1"></i>{{ $likeCount }}</span>
            @endif
            @if($viewCount > 0)
                <span><i class="fa fa-eye mr-1"></i>{{ format_view_count($viewCount) }}</span>
            @endif
        </div>
        <a href="{{ forum_thread_url($forum) }}" class="btn btn-sm btn-primary community-pub-action-btn--primary">
            <i class="fa fa-comments mr-1"></i> View discussion
        </a>
    </div>
</div>
