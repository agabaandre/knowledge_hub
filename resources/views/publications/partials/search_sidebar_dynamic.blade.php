@php
    $forumsUrl = url('forums');
    if (request()->filled('term')) {
        $forumsUrl .= '?term=' . urlencode(request('term'));
    } elseif (request()->filled('tag')) {
        $tagModel = \App\Models\Tag::find((int) request('tag'));
        if ($tagModel) {
            $forumsUrl .= '?term=' . urlencode($tagModel->tag_text);
        }
    }
@endphp

@if(isset($searchForums) && $searchForums->count() > 0)
<div class="records-sidebar-card records-sidebar-card--forums search-sidebar-panel mb-4">
    <div class="records-sidebar-card__head">
        <span class="records-sidebar-card__icon records-sidebar-card__icon--forums" aria-hidden="true">
            <i class="fa fa-comments"></i>
        </span>
        <div>
            <h2 class="records-sidebar-card__title">{{ __('publications.search.related_forums') }}</h2>
            <p class="records-sidebar-card__hint">Discussions related to your search.</p>
        </div>
    </div>
    <ul class="search-sidebar-list list-unstyled mb-0">
        @foreach($searchForums as $forum)
        <li class="search-sidebar-list__item">
            <a href="{{ forum_thread_url($forum) }}" class="search-sidebar-list__link">
                <span class="search-sidebar-list__title">{!! Str::limit(strip_tags($forum->forum_title ?? ''), 90) !!}</span>
                @if(!empty($forum->forum_description))
                <span class="search-sidebar-list__excerpt">{{ Str::limit(strip_tags($forum->forum_description), 110) }}</span>
                @endif
                <span class="search-sidebar-list__meta">
                    @if($forum->user)
                    <span><i class="fa fa-user" aria-hidden="true"></i> {{ $forum->user->name ?? 'Unknown' }}</span>
                    @endif
                    <span><i class="fa fa-clock" aria-hidden="true"></i> {{ time_ago($forum->created_at) }}</span>
                    <span><i class="fa fa-comments" aria-hidden="true"></i> {{ $forum->total_comments ?? 0 }} {{ __('publications.search.comments') }}</span>
                    <span><i class="fa fa-eye" aria-hidden="true"></i> {{ format_view_count($forum->views ?? 0) }} {{ __('publications.search.views') }}</span>
                </span>
            </a>
        </li>
        @endforeach
    </ul>
    <a href="{{ $forumsUrl }}" class="search-sidebar-view-all"><i class="fa fa-arrow-right" aria-hidden="true"></i> {{ __('publications.search.view_all_forums') }}</a>
</div>
@endif

@if(isset($latestPublications) && $latestPublications->count() > 0)
<div class="records-sidebar-card records-sidebar-card--latest search-sidebar-panel mb-4">
    <div class="records-sidebar-card__head">
        <span class="records-sidebar-card__icon records-sidebar-card__icon--latest" aria-hidden="true">
            <i class="fa fa-clock"></i>
        </span>
        <div>
            <h2 class="records-sidebar-card__title">{{ __('publications.search.latest_publications') }}</h2>
            <p class="records-sidebar-card__hint">Recently added resources on the hub.</p>
        </div>
    </div>
    <ul class="search-sidebar-list list-unstyled mb-0">
        @foreach($latestPublications->take(5) as $pub)
        <li class="search-sidebar-list__item">
            <a href="{{ publication_url($pub) }}" class="search-sidebar-list__link">
                <span class="search-sidebar-list__title">{{ Str::limit(strip_tags($pub->title), 90) }}</span>
                <span class="search-sidebar-list__excerpt">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</span>
                <span class="search-sidebar-list__meta">
                    @if($pub->author)
                    <span class="notranslate" translate="no">
                        <i class="fa fa-user" aria-hidden="true"></i> {{ $pub->author->name }}
                    </span>
                    @endif
                    <span><i class="fa fa-calendar" aria-hidden="true"></i> {{ $pub->created_at->format('M Y') }}</span>
                </span>
            </a>
        </li>
        @endforeach
    </ul>
    <a href="{{ url('records') }}" class="search-sidebar-view-all"><i class="fa fa-arrow-right" aria-hidden="true"></i> {{ __('publications.search.view_all_publications') }}</a>
</div>
@endif
