@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $messages = $contentRequest->referralMessages ?? collect();
@endphp

<div class="cr-track-messages">
    @forelse($messages as $msg)
        @php
            $isRequester = (bool) $msg->posted_via_track;
            $authorName = $msg->authorLabel();
            $initial = mb_strtoupper(mb_substr($authorName, 0, 1));
        @endphp
        <article class="cr-track-message {{ $isRequester ? 'cr-track-message--requester' : 'cr-track-message--team' }}">
            <div class="cr-track-message__avatar" aria-hidden="true">
                @if(!$isRequester && $msg->user && $msg->user->photo)
                    @php
                        $photoUrl = $msg->user->photo;
                        if ($photoUrl && !preg_match('/^https?:\/\//', $photoUrl)) {
                            $photoUrl = strpos($photoUrl, '/storage/') === 0
                                ? url($photoUrl)
                                : storage_link('uploads/users/' . ($msg->user->getOriginal('photo') ?? $photoUrl));
                        }
                    @endphp
                    <img src="{{ $photoUrl }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="cr-track-message__avatar-fallback" style="display:none;">{{ $initial }}</span>
                @else
                    <span class="cr-track-message__avatar-fallback">{{ $isRequester ? 'R' : $initial }}</span>
                @endif
            </div>
            <div class="cr-track-message__bubble">
                <header class="cr-track-message__meta">
                    <strong class="cr-track-message__author">{{ $authorName }}</strong>
                    @if($isRequester)
                        <span class="cr-track-message__badge">Requester</span>
                    @else
                        <span class="cr-track-message__badge cr-track-message__badge--team">Hub team</span>
                    @endif
                    <time class="cr-track-message__time" datetime="{{ $msg->created_at->toIso8601String() }}">
                        {{ $msg->created_at->format('M j, Y g:i a') }}
                    </time>
                </header>
                <div class="cr-track-message__body rich-text-content">
                    {!! cleanHtmlContent($msg->body) !!}
                </div>
            </div>
        </article>
    @empty
        <div class="cr-track-empty">
            <div class="cr-track-empty__icon"><i class="fa fa-comments-o"></i></div>
            <p class="mb-0">No private messages yet. Use the form below to send an update to the team.</p>
        </div>
    @endforelse
</div>
