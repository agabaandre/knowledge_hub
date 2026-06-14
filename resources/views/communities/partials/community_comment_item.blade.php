@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $currentUser = current_user();
    $communityComments = $communityComments ?? collect();
    $commentCount = $communityComments->count();
    $isReply = (bool) ($isReply ?? false);
    $likeCount = $comment->likes ? $comment->likes->count() : 0;
    $isLiked = auth()->check() ? $comment->isLikedBy() : false;
    $commentShareUrl = community_detail_url($community) . '#community-comment-' . $comment->id;
@endphp

<div class="comment-item {{ $isReply ? 'reply-comment' : '' }}" id="community-comment-{{ $comment->id }}">
    <div class="comment-avatar">
        @if($comment->user && $comment->user->photo)
            @php
                $photoUrl = $comment->user->photo;
                if ($photoUrl && ! preg_match('/^https?:\/\//', $photoUrl)) {
                    if (strpos($photoUrl, '/storage/') === 0) {
                        $photoUrl = url($photoUrl);
                    } elseif (strpos($photoUrl, 'storage/') === 0) {
                        $photoUrl = url('/' . $photoUrl);
                    }
                }
            @endphp
            <img src="{{ $photoUrl }}" alt="{{ $comment->user->name }}"
                 style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;z-index:1;"
                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
            <i class="fa fa-user" style="display:none;"></i>
        @else
            <i class="fa fa-user" style="color:#64748b;"></i>
        @endif
    </div>
    <div class="comment-content">
        <div class="comment-header">
            <span class="comment-author notranslate" translate="no">
                {{ @current_user() && @current_user()->id == $comment->created_by ? 'You' : ($comment->user->name ?? 'Unknown') }}
            </span>
            <span class="comment-time">· {{ time_ago($comment->created_at) }}</span>
            @auth
                <div class="comment-header-actions" style="display:inline-flex;gap:0.5rem;margin-left:0.5rem;">
                    <button type="button" class="comment-action-btn like-community-comment-btn" data-comment-id="{{ $comment->id }}"
                            style="color: {{ $isLiked ? '#ef4444' : '#64748b' }};">
                        <i class="fa fa-heart{{ $isLiked ? '' : '-o' }}" style="color: {{ $isLiked ? '#ef4444' : 'inherit' }};"></i>
                        <span class="like-count">{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                    </button>
                    @if(! $isReply)
                        <button type="button" class="comment-action-btn reply-comment-btn" data-comment-id="{{ $comment->id }}">
                            <i class="fa fa-reply"></i><span>Reply</span>
                        </button>
                    @endif
                    <button type="button" class="comment-action-btn share-comment-btn" data-share-url="{{ $commentShareUrl }}">
                        <i class="fa fa-share-alt"></i><span>Share</span>
                    </button>
                </div>
            @else
                <div class="comment-header-actions" style="display:inline-flex;gap:0.5rem;margin-left:0.5rem;">
                    <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration:none;color:#64748b;">
                        <i class="fa fa-heart-o"></i><span>{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                    </a>
                </div>
            @endauth
        </div>
        <div class="comment-text">
            @php
                $commentAttachments = $comment->attachments ?? collect();
                $imageAttachments = [];
                $fileAttachments = [];
                foreach ($commentAttachments as $attachment) {
                    $extension = forum_comment_attachment_raw_extension($attachment);
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $imageAttachments[] = $attachment;
                    } else {
                        $fileAttachments[] = $attachment;
                    }
                }
            @endphp
            @if(count($imageAttachments) > 0)
                @foreach ($imageAttachments as $attachment)
                    @php $attDisplay = forum_attachment_display_name($attachment); @endphp
                    <a href="javascript:void(0);" onclick="if(typeof openImageModal==='function'){openImageModal('{{ $attachment->path }}', {{ json_encode($attDisplay) }});}else{window.open('{{ $attachment->path }}','_blank');}"
                       style="float:left;display:inline-block;margin:0 12px 8px 2px;">
                        <img src="{{ $attachment->path }}" alt="{{ $attDisplay }}"
                             style="width:144px;height:144px;object-fit:contain;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:4px;padding:2px;">
                    </a>
                @endforeach
            @endif
            @php
                $limitedText = Str::words(strip_tags($comment->comment), 300, '...');
                $processedText = detect_and_embed_video_links($limitedText, 180, 180);
            @endphp
            <div style="text-align:justify;overflow-wrap:break-word;clear:left;">
                {!! cleanHtmlContent($processedText) !!}
            </div>
            @if(count($fileAttachments) > 0)
                <div class="comment-file-attachments-summary mt-2">
                    @foreach ($fileAttachments as $attachment)
                        @include('forums.partials.comment_file_attachment_link', ['attachment' => $attachment])
                    @endforeach
                </div>
            @endif
        </div>

        @auth
            @if(! $isReply && ($isCommunityMember ?? true))
                <div class="reply-form-container" id="reply-form-{{ $comment->id }}" style="display:none;margin-top:1rem;padding-top:1rem;border-top:1px solid #e2e8f0;">
                    <form action="{{ route('community.comment', $community->id) }}" method="post" enctype="multipart/form-data" class="reply-form community-reply-form">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="comment-input-wrapper">
                            <div class="comment-avatar" style="width:36px;height:36px;">
                                @if($currentUser && $currentUser->photo)
                                    <img src="{{ $currentUser->photo }}" alt="" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;">
                                @else
                                    <i class="fa fa-user" style="color:#64748b;"></i>
                                @endif
                            </div>
                            <div class="comment-form-controls" style="flex:1;">
                                <textarea name="comment" class="comment-textarea" placeholder="Write a reply..." rows="3" maxlength="20000" required></textarea>
                                <div class="comment-char-count" style="font-size:0.75rem;color:#94a3b8;text-align:right;margin-top:0.25rem;">
                                    <span class="char-count">0</span> / 300 words max
                                </div>
                                <div style="margin-top:0.5rem;">
                                    <button type="submit" class="comment-submit-btn"><i class="fa fa-paper-plane"></i> Reply</button>
                                    <button type="button" class="cancel-reply-btn" data-comment-id="{{ $comment->id }}"
                                            style="margin-left:0.5rem;padding:0.5rem 1rem;background:#e2e8f0;border:none;border-radius:4px;color:#64748b;cursor:pointer;">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>

@if(! $isReply && $comment->replies && $comment->replies->count() > 0)
    @foreach ($comment->replies as $reply)
        @include('communities.partials.community_comment_item', [
            'comment' => $reply,
            'community' => $community,
            'isCommunityMember' => $isCommunityMember ?? true,
            'isReply' => true,
        ])
    @endforeach
@endif
