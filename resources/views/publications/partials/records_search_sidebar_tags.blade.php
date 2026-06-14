@php
    $recordsSearchTagQuery = request()->except('page');
    $sidebarTagsList = (isset($tags) && count($tags) > 0)
        ? $tags->take(12)
        : \App\Models\Tag::query()->orderBy('tag_text', 'asc')->limit(12)->get();
    $activeTagId = request('tag');
    $tagResourceCounts = collect();
    if ($sidebarTagsList->isNotEmpty()) {
        $tagResourceCounts = \Illuminate\Support\Facades\DB::table('publication_tags')
            ->join('publication', 'publication.id', '=', 'publication_tags.publication_id')
            ->whereIn('publication_tags.tag_id', $sidebarTagsList->pluck('id'))
            ->where('publication.is_version', 0)
            ->where('publication.is_active', 'Active')
            ->where('publication.is_approved', 1)
            ->groupBy('publication_tags.tag_id')
            ->select('publication_tags.tag_id', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT publication.id) as total'))
            ->pluck('total', 'tag_id');
    }
@endphp

@if($sidebarTagsList->count() > 0)
<div class="records-sidebar-card records-sidebar-card--tags">
    <div class="records-sidebar-card__head">
        <span class="records-sidebar-card__icon records-sidebar-card__icon--tags" aria-hidden="true">
            <i class="fa fa-hashtag"></i>
        </span>
        <div>
            <h2 class="records-sidebar-card__title">{{ __('ui_body.footer_popular_tags') }}</h2>
            <p class="records-sidebar-card__hint">Browse resources by popular health topics.</p>
        </div>
    </div>
    <div class="records-sidebar-tags">
        @foreach($sidebarTagsList as $tag)
            @php
                $tagHref = tag_records_url($tag, true, $recordsSearchTagQuery);
                $tagActive = $activeTagId !== null && $activeTagId !== '' && (string) $activeTagId === (string) $tag->id;
                $tagCount = (int) ($tagResourceCounts[$tag->id] ?? 0);
            @endphp
            <a href="{{ $tagHref }}"
               class="records-sidebar-tags__link js-records-search-ajax{{ $tagActive ? ' is-active' : '' }}"
               title="{{ $tag->tag_text }}">
                <span class="records-sidebar-tags__label">
                    <span class="records-sidebar-tags__label-hash">#</span>{{ truncate($tag->tag_text, 28) }}
                </span>
                @if($tagCount > 0)
                    <span class="records-sidebar-tags__count">{{ number_format($tagCount) }}</span>
                @endif
            </a>
        @endforeach
    </div>
    @if($activeTagId)
        <a href="{{ url('records/search') . (request()->except('tag', 'page') ? '?' . http_build_query(request()->except('tag', 'page')) : '') }}"
           class="records-sidebar-tags__clear js-records-search-ajax">Clear tag filter</a>
    @endif
</div>
@endif
