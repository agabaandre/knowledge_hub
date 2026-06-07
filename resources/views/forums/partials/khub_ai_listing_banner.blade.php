@php
    $pageForumCount = is_countable($forums ?? null) ? count($forums) : 0;
@endphp
<div class="khub-forums-ai-banner" id="khub-forums-ai-banner">
    <div class="khub-forums-ai-banner-inner">
        <div class="khub-forums-ai-banner-icon" aria-hidden="true">
            <i class="fa-solid fa-microchip"></i>
        </div>
        <div class="khub-forums-ai-banner-copy">
            <h2 class="khub-forums-ai-banner-title">Khub AI — Forums assistant</h2>
            <p class="khub-forums-ai-banner-text">
                Ask about trends across these discussions, compare threads, or drill into a specific topic.
                Khub AI uses the discussions shown on this page and loads full thread detail when you ask about one.
            </p>
            @if($pageForumCount > 0)
                <p class="khub-forums-ai-banner-meta">
                    <i class="fa fa-comments me-1"></i>{{ $pageForumCount }} {{ $pageForumCount === 1 ? 'thread' : 'threads' }} on this page
                </p>
            @endif
        </div>
        <div class="khub-forums-ai-banner-actions">
            @auth
                <button type="button" class="btn theme-bg text-white btn-sm px-3" id="btn-open-forums-khub-ai">
                    <i class="fa-solid fa-microchip me-1"></i> Open Khub AI
                </button>
            @else
                <a href="{{ route('login') }}?redirect={{ urlencode(url('forums')) }}" class="btn theme-bg text-white btn-sm px-3">
                    <i class="fa-solid fa-microchip me-1"></i> Log in to use Khub AI
                </a>
            @endauth
        </div>
    </div>
    @auth
    <div class="khub-forums-ai-suggestions-row" aria-label="Suggested prompts">
        <button type="button" class="khub-forums-ai-chip js-forums-ai-chip" data-prompt="What are the main topics being discussed on this page?">Main topics on this page</button>
        <button type="button" class="khub-forums-ai-chip js-forums-ai-chip" data-prompt="Which threads have the most engagement and why?">Most active threads</button>
        <button type="button" class="khub-forums-ai-chip js-forums-ai-chip" data-prompt="Summarize the Ebola-related discussions shown here.">Ebola discussions</button>
        <button type="button" class="khub-forums-ai-chip js-forums-ai-chip" data-prompt="What health sovereignty themes appear across these forums?">Health sovereignty themes</button>
    </div>
    @endauth
</div>
