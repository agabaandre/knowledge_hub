@php
    $rawComment = (string) ($comment->comment ?? '');
    $hasHtml = preg_match('/<[^>]+>/', $rawComment) === 1;
    $safeHtmlComment = sanitize_rich_text_for_display($rawComment);
    $plainPreview = Str::limit(strip_tags($rawComment), 150);
    $photoUrl = null;
    if ($comment->user && !empty($comment->user->photo)) {
        $photoUrl = $comment->user->photo;
        if (strpos($photoUrl, 'http://') !== 0 && strpos($photoUrl, 'https://') !== 0) {
            if (strpos($photoUrl, '/storage/') === 0) {
                $photoUrl = url($photoUrl);
            } elseif (strpos($photoUrl, 'storage/') === 0) {
                $photoUrl = url('/' . $photoUrl);
            }
        }
    }
@endphp
<div class="comment-item-mini community-pub-comment-mini">
    <div class="comment-avatar-mini">
        @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $comment->user->name ?? 'User' }}"
                 onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa fa-user\'></i>';">
        @else
            <i class="fa fa-user" aria-hidden="true"></i>
        @endif
    </div>
    <div class="comment-content-mini">
        <div class="comment-author-mini notranslate" translate="no">{{ $comment->user->name ?? 'Unknown' }}</div>
        <div class="comment-text-mini">{!! $hasHtml ? $safeHtmlComment : nl2br(e($plainPreview)) !!}</div>
        <div class="comment-time-mini">
            <i class="fa fa-clock-o mr-1" aria-hidden="true"></i>{{ time_ago($comment->created_at ?? now()) }}
        </div>
    </div>
</div>
