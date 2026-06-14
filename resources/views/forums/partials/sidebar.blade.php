@php
    $primary = settings()->primary_color ?? '#119A48';
    $categories = collect($forumSidebarCategories ?? []);
    $topForums = collect($forumTopByEngagement ?? []);
    $activeTag = request('tag');
@endphp

<aside class="forums-sidebar" aria-label="Forum navigation">
    @if($categories->isNotEmpty())
    <div class="forums-sidebar-card">
        <h2 class="forums-sidebar-card__title">
            <i class="fa fa-folder-open me-2" aria-hidden="true"></i>Topics
        </h2>
        <p class="forums-sidebar-card__hint">Browse discussions by tag, similar to community categories.</p>
        <div class="forums-sidebar-table-wrap">
            <table class="forums-sidebar-table">
                <thead>
                    <tr>
                        <th scope="col">Category</th>
                        <th scope="col" class="text-end">Threads</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $row)
                        @php
                            $tag = (string) ($row->tag ?? '');
                            $isActive = $activeTag !== null && strcasecmp((string) $activeTag, $tag) === 0;
                        @endphp
                        <tr class="{{ $isActive ? 'is-active' : '' }}">
                            <td>
                                <a href="{{ url('forums') }}?tag={{ urlencode($tag) }}" class="forums-sidebar-table__link">
                                    #{{ $tag }}
                                </a>
                            </td>
                            <td class="text-end forums-sidebar-table__count">{{ number_format((int) ($row->topics_count ?? 0)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($activeTag)
            <a href="{{ url('forums') }}" class="forums-sidebar-clear small">Clear topic filter</a>
        @endif
    </div>
    @endif

    @if($topForums->isNotEmpty())
    <div class="forums-sidebar-card">
        <h2 class="forums-sidebar-card__title">
            <i class="fa fa-fire me-2" aria-hidden="true"></i>Most engaged
        </h2>
        <ol class="forums-sidebar-ranked list-unstyled mb-0">
            @foreach($topForums as $rank => $forum)
                @php
                    $score = (int) ($forum->engagement_score ?? 0);
                @endphp
                <li class="forums-sidebar-ranked__item">
                    <span class="forums-sidebar-ranked__num">#{{ $rank + 1 }}</span>
                    <div class="forums-sidebar-ranked__body">
                        <a href="{{ forum_thread_url($forum) }}" class="forums-sidebar-ranked__title">
                            {{ Str::limit(strip_tags($forum->forum_title ?? ''), 72) }}
                        </a>
                        <span class="forums-sidebar-ranked__meta">
                            {{ number_format($score) }} engagements
                            · {{ format_view_count($forum->views ?? 0) }} views
                        </span>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
    @endif

    <div class="forums-sidebar-card">
        <h2 class="forums-sidebar-card__title">
            <i class="fa fa-compass me-2" aria-hidden="true"></i>Explore
        </h2>
        <ul class="forums-sidebar-links list-unstyled mb-0">
            <li><a href="{{ url('communities') }}"><i class="fa fa-users me-2"></i>Communities of Practice</a></li>
            <li><a href="{{ url('forums/create') }}"><i class="fa fa-plus-circle me-2"></i>Start a discussion</a></li>
            <li><a href="{{ url('faqs') }}"><i class="fa fa-life-ring me-2"></i>FAQs &amp; guidelines</a></li>
            <li><a href="{{ url('records/search') }}"><i class="fa fa-book me-2"></i>Browse resources</a></li>
        </ul>
    </div>
</aside>
