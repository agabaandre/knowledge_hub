@php
    $shareUrl = forum_thread_url($forum);
    $shareTitle = strip_tags($forum->forum_title ?? '');
    $shareText = rawurlencode($shareTitle);
    $variant = $variant ?? 'default';
    $btnClass = $variant === 'inline' ? 'forum-share-btn' : 'forum-action-btn';
@endphp
<div class="forum-share-actions{{ $variant === 'inline' ? ' forum-share-actions--inline' : '' }}"
     @if($variant !== 'inline') style="display: contents" @endif
     role="group" aria-label="Share discussion">
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($shareUrl) }}"
       title="Share on LinkedIn">
        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
        @if($variant !== 'inline')<span>LinkedIn</span>@endif
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://twitter.com/intent/tweet?url={{ rawurlencode($shareUrl) }}&text={{ $shareText }}"
       title="Share on X">
        <i class="fab fa-x-twitter" aria-hidden="true"></i>
        @if($variant !== 'inline')<span>X</span>@endif
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}"
       title="Share on Facebook">
        <i class="fab fa-facebook-f" aria-hidden="true"></i>
        @if($variant !== 'inline')<span>Facebook</span>@endif
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ rawurlencode($shareUrl) }}"
       title="Share on WhatsApp">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
        @if($variant !== 'inline')<span>WhatsApp</span>@endif
    </a>
    <button type="button" class="{{ $btnClass }} copy-link-btn" data-share-url="{{ $shareUrl }}"
            title="Copy link" aria-label="Copy discussion link">
        <i class="fa fa-link" aria-hidden="true"></i>
        @if($variant !== 'inline')<span>Copy link</span>@endif
    </button>
</div>
