@php
    $recentForums = collect($forumSidebarRecent ?? []);
    $topForums = collect($forumTopByEngagement ?? []);
    $categories = collect($forumSidebarCategories ?? []);
    $activeTag = request('tag');
@endphp

<aside class="forums-sidebar" aria-label="Forum navigation">
    @if($recentForums->isNotEmpty())
    <div class="forums-sidebar-card forums-sidebar-card--recent">
        <div class="forums-sidebar-card__head">
            <span class="forums-sidebar-card__icon forums-sidebar-card__icon--recent" aria-hidden="true">
                <i class="fa fa-clock"></i>
            </span>
            <div>
                <h2 class="forums-sidebar-card__title mb-0">Recent forums</h2>
                <p class="forums-sidebar-card__hint mb-0">Latest discussions from the community.</p>
            </div>
        </div>
        <ul class="forums-sidebar-recent list-unstyled mb-0">
            @foreach($recentForums as $forum)
                <li>
                    <a href="{{ forum_thread_url($forum) }}" class="forums-sidebar-recent__item">
                        @if(!empty($forum->forum_image) && is_image($forum->forum_image))
                            <img class="forums-sidebar-recent__thumb" src="{{ $forum->forum_image }}" alt="" loading="lazy">
                        @else
                            <span class="forums-sidebar-recent__thumb forums-sidebar-recent__thumb--placeholder" aria-hidden="true">
                                <i class="fa fa-comments"></i>
                            </span>
                        @endif
                        <span class="forums-sidebar-recent__body">
                            <span class="forums-sidebar-recent__title">{!! Str::limit(strip_tags($forum->forum_title ?? ''), 72) !!}</span>
                            <span class="forums-sidebar-recent__meta">
                                <i class="fa fa-clock" aria-hidden="true"></i>
                                {{ time_ago($forum->created_at) }}
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($topForums->isNotEmpty())
    <div class="forums-sidebar-card forums-sidebar-card--engaged">
        <div class="forums-sidebar-card__head">
            <span class="forums-sidebar-card__icon forums-sidebar-card__icon--engaged" aria-hidden="true">
                <i class="fa fa-fire"></i>
            </span>
            <div>
                <h2 class="forums-sidebar-card__title mb-0">Most engaged</h2>
                <p class="forums-sidebar-card__hint mb-0">Ranked by views, comments, and likes.</p>
            </div>
        </div>
        <ol class="forums-sidebar-ranked list-unstyled mb-0">
            @foreach($topForums as $rank => $forum)
                @php
                    $score = (int) ($forum->engagement_score ?? 0);
                    $rankClass = $rank === 0 ? 'is-gold' : ($rank === 1 ? 'is-silver' : ($rank === 2 ? 'is-bronze' : ''));
                @endphp
                <li class="forums-sidebar-ranked__item">
                    <span class="forums-sidebar-ranked__num {{ $rankClass }}">#{{ $rank + 1 }}</span>
                    <div class="forums-sidebar-ranked__body">
                        <a href="{{ forum_thread_url($forum) }}" class="forums-sidebar-ranked__title">
                            {{ Str::limit(strip_tags($forum->forum_title ?? ''), 72) }}
                        </a>
                        <span class="forums-sidebar-ranked__meta">
                            <span class="forums-sidebar-stat"><i class="fa fa-chart-line" aria-hidden="true"></i>{{ number_format($score) }}</span>
                            <span class="forums-sidebar-stat"><i class="fa fa-eye" aria-hidden="true"></i>{{ format_view_count($forum->views ?? 0) }}</span>
                        </span>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
    @endif

    @if($categories->isNotEmpty())
    <div class="forums-sidebar-card forums-sidebar-card--topics">
        <div class="forums-sidebar-card__head">
            <span class="forums-sidebar-card__icon forums-sidebar-card__icon--topics" aria-hidden="true">
                <i class="fa fa-folder-open"></i>
            </span>
            <div>
                <h2 class="forums-sidebar-card__title mb-0">Topics</h2>
                <p class="forums-sidebar-card__hint mb-0">Browse discussions by health topic.</p>
            </div>
        </div>
        <ul class="forums-sidebar-topics list-unstyled mb-0">
            @foreach($categories as $row)
                @php
                    $tag = (string) ($row->tag ?? '');
                    $isActive = $activeTag !== null && strcasecmp((string) $activeTag, $tag) === 0;
                @endphp
                <li>
                    <a href="{{ url('forums') }}?tag={{ urlencode($tag) }}"
                       class="forums-sidebar-topics__link {{ $isActive ? 'is-active' : '' }}">
                        <span class="forums-sidebar-topics__label">#{{ $tag }}</span>
                        <span class="forums-sidebar-topics__count">{{ number_format((int) ($row->topics_count ?? 0)) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @if($activeTag)
            <a href="{{ url('forums') }}" class="forums-sidebar-clear small">Clear topic filter</a>
        @endif
    </div>
    @endif

    <div class="forums-sidebar-card forums-sidebar-card--explore">
        <div class="forums-sidebar-card__head">
            <span class="forums-sidebar-card__icon forums-sidebar-card__icon--explore" aria-hidden="true">
                <i class="fa fa-compass"></i>
            </span>
            <div>
                <h2 class="forums-sidebar-card__title mb-0">Explore</h2>
            </div>
        </div>
        <ul class="forums-sidebar-links list-unstyled mb-0">
            <li><a href="{{ url('communities') }}"><i class="fa fa-users" aria-hidden="true"></i>Communities of Practice</a></li>
            <li><a href="{{ url('forums/create') }}"><i class="fa fa-plus-circle" aria-hidden="true"></i>Start a discussion</a></li>
            <li><a href="{{ url('faqs') }}"><i class="fa fa-life-ring" aria-hidden="true"></i>FAQs &amp; guidelines</a></li>
            <li><a href="{{ url('records/search') }}"><i class="fa fa-book" aria-hidden="true"></i>Browse resources</a></li>
        </ul>
    </div>
</aside>
