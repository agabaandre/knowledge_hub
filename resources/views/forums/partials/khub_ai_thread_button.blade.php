@php
    $threadTitle = e(Str::limit(strip_tags($forum->forum_title ?? 'Forum thread'), 200));
    $btnClass = $btnClass ?? 'btn btn-sm btn-outline-secondary';
    $redirectUrl = $redirectUrl ?? url('forums');
    $isActionBtn = str_contains($btnClass, 'forum-action-btn');
@endphp
@auth
    <button type="button"
            class="{{ $btnClass }} js-open-forum-assistant"
            data-forum-id="{{ $forum->id }}"
            data-thread-title="{{ $threadTitle }}"
            title="Ask Khub AI about this discussion"
            @if($isActionBtn) style="cursor: pointer; border: none; background: none;" @endif>
        <i class="fa-solid fa-microchip"></i>
        <span>Khub AI</span>
    </button>
@else
    <a href="{{ route('login') }}?redirect={{ urlencode($redirectUrl) }}"
       class="{{ $btnClass }}"
       title="Log in to use Khub AI">
        <i class="fa-solid fa-microchip"></i>
        <span>Khub AI</span>
    </a>
@endauth
