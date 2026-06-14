@php
    $publication = $row ?? $publication ?? null;
    $shareUrl = $publication ? publication_url($publication) : url()->current();
    $shareTitle = strip_tags($publication->title ?? '');
    $shareText = rawurlencode($shareTitle);
    $variant = $variant ?? 'inline';
    $btnClass = $variant === 'inline' ? 'community-pub-share-btn' : 'btn btn-sm btn-outline-secondary';
@endphp
<div class="community-pub-share-actions{{ $variant === 'inline' ? ' community-pub-share-actions--inline' : '' }}"
     role="group" aria-label="Share publication">
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($shareUrl) }}"
       title="Share on LinkedIn">
        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://twitter.com/intent/tweet?url={{ rawurlencode($shareUrl) }}&text={{ $shareText }}"
       title="Share on X">
        <i class="fab fa-x-twitter" aria-hidden="true"></i>
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}"
       title="Share on Facebook">
        <i class="fab fa-facebook-f" aria-hidden="true"></i>
    </a>
    <a class="{{ $btnClass }}" target="_blank" rel="noopener noreferrer"
       href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ rawurlencode($shareUrl) }}"
       title="Share on WhatsApp">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
    </a>
    <button type="button" class="{{ $btnClass }} copy-link-btn" data-share-url="{{ $shareUrl }}"
            title="Copy link" aria-label="Copy publication link">
        <i class="fa fa-link" aria-hidden="true"></i>
    </button>
</div>
