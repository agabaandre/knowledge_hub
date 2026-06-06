@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $auGold = settings()->au_gold ?? '#B4A269';
    $currentUser = current_user();
@endphp
@once('forum_details_author_avatar')
<style>
    /* Thread + admin modal: author avatar size (show.blade.php adds hover/dark tweaks) */
    .forum-post-card .forum-author-avatar-inline {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4.5rem;
        height: 4.5rem;
        min-width: 4.5rem;
        min-height: 4.5rem;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
        background: #f8f9fa;
        margin-right: 0.25rem;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .forum-post-card .forum-author-avatar-inline img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        z-index: 1;
    }
    .forum-post-card .forum-author-avatar-inline i {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 0;
        font-size: 2rem;
        color: {{ $primaryColor }};
        opacity: 0.85;
        line-height: 1;
        background: transparent;
    }
</style>
@endonce

<div class="forum-post-card">
    <div class="forum-header">
        <div class="forum-author-info">
            <div class="forum-author-name-container">
                @if($forum->user && $forum->user->photo)
                    @php
                        $photoUrl = $forum->user->photo;
                        // Fix double path issue - User model already returns full URL via accessor
                        // If it already contains the domain/base URL, use as-is, otherwise check for storage path
                        $baseUrl = url('/');
                        if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                            // Already a full URL - use as-is
                        } elseif (strpos($photoUrl, $baseUrl) !== false) {
                            // Already contains base URL - use as-is
                        } elseif (strpos($photoUrl, '/storage/') === 0) {
                            // Has storage path but not full URL
                            $photoUrl = $baseUrl . $photoUrl;
                        } elseif (strpos($photoUrl, 'storage/') === 0) {
                            // Has storage path without leading slash
                            $photoUrl = $baseUrl . '/' . $photoUrl;
                        }
                    @endphp
                    <div class="forum-author-avatar-inline" style="cursor: pointer;"
                         onclick="openImageModal('{{ $photoUrl }}', '{{ $forum->user->name ?? 'Unknown' }}')">
                        <img src="{{ $photoUrl }}" alt="{{ $forum->user->name }}"
                             onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='block';">
                        <i class="fa fa-user" style="display: none;"></i>
                    </div>
                @else
                    <div class="forum-author-avatar-inline">
                        <i class="fa fa-user" aria-hidden="true"></i>
        </div>
                @endif
                <span class="forum-author-name">{{ $forum->user->name ?? 'Unknown' }}</span>
                <span class="forum-post-time" style="margin-left: 4px; color: #64748b; font-size: 0.875rem;">
                    <i class="fa fa-clock me-1"></i>{{ time_ago($forum->created_at) }}
                </span>
            </div>
            </div>
        </div>

    <div class="forum-content" style="margin-top: 1rem; overflow: hidden;">
        @if($forum->forum_image)
            @php
                $forumImageUrl = $forum->forum_image;
                $baseUrl = url('/');
                if (strpos($forumImageUrl, 'http://') === 0 || strpos($forumImageUrl, 'https://') === 0) {
                    // Already a full URL
                } elseif (strpos($forumImageUrl, $baseUrl) !== false) {
                    // Already contains base URL
                } elseif (strpos($forumImageUrl, '/storage/') === 0) {
                    $forumImageUrl = $baseUrl . $forumImageUrl;
                } elseif (strpos($forumImageUrl, 'storage/') === 0) {
                    $forumImageUrl = $baseUrl . '/' . $forumImageUrl;
                }
            @endphp
            <!-- Forum Image as Dropcap -->
            <a href="javascript:void(0);" onclick="if(typeof openImageModal === 'function') { openImageModal('{{ $forumImageUrl }}', '{{ $forum->forum_title ?? 'Forum Image' }}'); } else { window.open('{{ $forumImageUrl }}', '_blank'); }" 
               style="float: left; display: inline-block; cursor: pointer; margin-right: 12px; margin-bottom: 8px; margin-left: 2px; margin-top: 2px;">
                <img src="{{ $forumImageUrl }}" 
                     alt="{{ $forum->forum_title ?? 'Forum Image' }}" 
                     style="width: 144px; height: 144px; object-fit: contain; transition: transform 0.3s ease; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; display: block;"
                     onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';"
                     onmouseover="this.style.transform='scale(1.05)'"
                     onmouseout="this.style.transform='scale(1)'">
            </a>
        @endif
        
        @php
            // Process forum description to detect and embed video links and convert URLs to clickable links
            $processedDescription = detect_and_embed_video_links($forum->forum_description, 180, 180);
        @endphp
        <div style="text-align: justify; overflow-wrap: break-word;">
            {!! cleanHtmlContent($processedDescription) !!}
        </div>
    </div>

    <div class="forum-actions">
        @auth
        <button type="button" class="forum-action-btn like-forum-btn" data-forum-id="{{ $forum->id }}" style="cursor: pointer; border: none; background: none; padding: 0.5rem;">
            <i class="fa fa-heart{{ $forum->isLikedBy() ? '' : '-o' }}" style="color: {{ $forum->isLikedBy() ? '#ef4444' : 'inherit' }};"></i>
            <span class="like-count">{{ $forum->likes->count() }}</span>
        </button>
@else
        <a href="{{ route('login') }}" class="forum-action-btn" title="Login to like">
            <i class="fa fa-heart-o"></i>
            <span>{{ $forum->likes->count() }}</span>
        </a>
        @endauth
        <div class="forum-action-btn">
            <i class="fa fa-comment"></i>
            <span>{{ count($forum->comments) }} {{ count($forum->comments) == 1 ? 'Comment' : 'Comments' }}</span>
        </div>
        @auth
        <button type="button" class="forum-action-btn js-open-forum-assistant" data-forum-id="{{ $forum->id }}" data-thread-title="{{ e(Str::limit(strip_tags($forum->forum_title ?? ''), 140)) }}" style="cursor: pointer; border: none; background: none; padding: 0.5rem;">
            <i class="fa fa-robot"></i>
            <span>Khub AI</span>
        </button>
        @else
        <a href="{{ route('login') }}" class="forum-action-btn" title="Log in to use Khub AI">
            <i class="fa fa-robot"></i>
            <span>Khub AI</span>
        </a>
        @endauth
        @include('forums.partials.share_buttons', ['forum' => $forum])
    </div>
    </div>


    @if (!@$no_comments)
<!-- Comment Form - Above Comments -->
@auth
    @if (in_array($forum->id, $my_forums))
    <div class="comment-form-card" style="margin-bottom: 2rem;">
        <div class="comment-form-toggle" onclick="toggleCommentForm()" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 4px; transition: background 0.2s ease;">
            <h5 class="comment-form-header" style="margin: 0; font-size: 1rem; font-weight: 600; color: #2d3748;">
                <i class="fa fa-comment me-2" style="color: {{ $primaryColor }};"></i>
                Share your views
            </h5>
            <i class="fa fa-chevron-down" id="commentFormToggleIcon" style="color: #64748b; transition: transform 0.2s ease;"></i>
        </div>
        <div id="commentFormContainer" style="display: none; padding: 1.5rem; background: #ffffff; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 4px 4px;">
            <form action="{{ url('forums/comment') }}" method="post" enctype="multipart/form-data" id="forumCommentForm">
                @csrf
                <input type="hidden" name="id" value="{{ $forum->id }}" />
        
                <div class="comment-input-wrapper">
                    <div class="comment-input-avatar" style="position: relative; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; background: #f8f9fa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        @if($currentUser && $currentUser->photo)
                            @php
                                $photoUrl = $currentUser->photo;
                                $baseUrl = url('/');
                                if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                    // Already full URL
                                } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                    // Already contains base URL
                                } elseif (strpos($photoUrl, '/storage/') === 0) {
                                    $photoUrl = $baseUrl . $photoUrl;
                                } elseif (strpos($photoUrl, 'storage/') === 0) {
                                    $photoUrl = $baseUrl . '/' . $photoUrl;
                                }
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ $currentUser->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover; object-position: center; position: absolute; top: 0; left: 0; z-index: 1;"
                                 onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <i class="fa fa-user" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #64748b; font-size: 1rem; z-index: 0;"></i>
@else
                            <i class="fa fa-user" style="color: #64748b; font-size: 1rem;"></i>
@endif
                    </div>
                    <div class="comment-form-controls">
                        <textarea name="comment" id="forumCommentTextarea" class="comment-textarea" 
                                  placeholder="What are your thoughts?" rows="3" maxlength="20000" required></textarea>
                        <div class="comment-char-count" style="font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem;">
                            <span class="char-count">0</span> / 300 words max
                        </div>
                        
                        <div class="file-upload-area" id="fileUploadArea" style="cursor: pointer;">
                            <div class="file-upload-text">
                                <i class="fa fa-paperclip me-1"></i>
                                <span>Attach images, PDF, office, audio, or video (max 2MB per file)</span>
                            </div>
                            <div class="file-upload-hint">Images · PDF · Word/Excel/PowerPoint (saved as PDF) · Audio · Video</div>
                            <input type="file" name="attachments[]" id="forumCommentFiles" 
                                   multiple accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,audio/*,video/*,.mp3,.m4a,.wav,.aac,.ogg,.oga,.opus,.flac,.wma,.mp4,.webm,.mov,.avi,.mkv,.wmv,.flv,.3gp,.mpeg,.mpg" 
                                   style="display: none;">
                        </div>

                        <div class="file-preview" id="filePreview" style="margin-top: 0.75rem;"></div>
                    </div>
                </div>

                <div class="comment-form-footer" style="justify-content: flex-end;">
                    <button type="submit" class="comment-submit-btn">
                        <i class="fa fa-paper-plane"></i>
                        Post Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="comment-form-card" style="text-align: center; padding: 2rem; margin-bottom: 2rem;">
        <i class="fa fa-info-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
        <h5 style="color: #2d3748; margin-bottom: 0.5rem;">Join to Participate</h5>
        <p style="color: #64748b; margin-bottom: 1rem;">You need to join this discussion before you can comment.</p>
        <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="comment-submit-btn" style="text-decoration: none; display: inline-flex;">
            <i class="fa fa-link"></i>
            Join Discussion
        </a>
    </div>
    @endif
@else
    <div class="comment-form-card" style="text-align: center; padding: 2rem; margin-bottom: 2rem;">
        <i class="fa fa-lock" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
        <h5 style="color: #2d3748; margin-bottom: 0.5rem;">Login Required</h5>
        <p style="color: #64748b; margin-bottom: 1rem;">Please log in to participate in discussions.</p>
        <a href="{{ route('login') }}" class="comment-submit-btn" style="text-decoration: none; display: inline-flex;">
            <i class="fa fa-sign-in-alt"></i>
            Login
        </a>
    </div>
@endauth

<!-- Comments Section -->
<div class="comments-section">
    <h3 class="comments-header">
        <i class="fa fa-comments me-2" style="color: {{ $primaryColor }};"></i>
        {{ count($forum->comments) }} {{ count($forum->comments) == 1 ? 'Comment' : 'Comments' }}
    </h3>

    @if($forum->comments && $forum->comments->count() > 0)
                        @foreach ($forum->comments as $comment)
            <div class="comment-item">
                <div class="comment-avatar" style="position: relative; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; background: #f8f9fa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($comment->user && $comment->user->photo)
                        @php
                            $photoUrl = $comment->user->photo;
                            $baseUrl = url('/');
                            if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                // Already full URL
                            } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                // Already contains base URL
                            } elseif (strpos($photoUrl, '/storage/') === 0) {
                                $photoUrl = $baseUrl . $photoUrl;
                            } elseif (strpos($photoUrl, 'storage/') === 0) {
                                $photoUrl = $baseUrl . '/' . $photoUrl;
                            }
                        @endphp
                        <img src="{{ $photoUrl }}" alt="{{ $comment->user->name }}"
                             style="width: 100%; height: 100%; object-fit: cover; object-position: center; position: absolute; top: 0; left: 0; z-index: 1;"
                             onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <i class="fa fa-user" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #64748b; font-size: 1rem; z-index: 0;"></i>
                                                @else
                        <i class="fa fa-user" style="color: #64748b; font-size: 1rem;"></i>
                                                @endif
                </div>
                <div class="comment-content">
                    <div class="comment-header">
                        <span class="comment-author">
                            {{ @current_user() && @current_user()->id == $comment->created_by ? 'You' : ($comment->user->name ?? 'Unknown') }}
                        </span>
                        <span class="comment-time">· {{ time_ago($comment->created_at) }}</span>
                        @auth
                        <div class="comment-header-actions" style="display: inline-flex; gap: 0.5rem; margin-left: 0.5rem;">
                            @php 
                                $likeCount = $comment->likes->count();
                                $isLiked = $comment->isLikedBy();
                            @endphp
                            <button type="button" class="comment-action-btn like-comment-btn" data-comment-id="{{ $comment->id }}" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: {{ $isLiked ? '#ef4444' : '#64748b' }};">
                                <i class="fa fa-heart{{ $isLiked ? '' : '-o' }}" style="color: {{ $isLiked ? '#ef4444' : 'inherit' }};"></i>
                                <span class="like-count">{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                            </button>
                            <button type="button" class="comment-action-btn reply-comment-btn" data-comment-id="{{ $comment->id }}" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: #64748b;">
                                <i class="fa fa-reply"></i>
                                <span>Reply</span>
                            </button>
                            @php $commentShareUrl = forum_thread_url($forum).'#comment-'.$comment->id; @endphp
                            <button type="button" class="comment-action-btn share-comment-btn" data-share-url="{{ $commentShareUrl }}" title="Share comment" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: #64748b;">
                                <i class="fa fa-share-alt"></i>
                                <span>Share</span>
                            </button>
                                                </div>
                        @else
                        <div class="comment-header-actions" style="display: inline-flex; gap: 0.5rem; margin-left: 0.5rem;">
                            @php $likeCount = $comment->likes->count(); @endphp
                            <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration: none; padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #64748b;">
                                <i class="fa fa-heart-o"></i>
                                <span>{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                            </a>
                            <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration: none; padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #64748b;">
                                <i class="fa fa-reply"></i>
                                <span>Reply</span>
                            </a>
                            <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration: none; padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #64748b;">
                                <i class="fa fa-share-alt"></i>
                                <span>Share</span>
                            </a>
                                            </div>
                        @endauth
                                        </div>
                                        <div class="comment-text" style="overflow: hidden;">
                        @if ($comment->attachments && $comment->attachments->count() > 0)
                            @php
                                $imageAttachments = [];
                                $fileAttachments = [];
                                foreach ($comment->attachments as $attachment) {
                                    $extension = forum_comment_attachment_raw_extension($attachment);
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    if ($isImage) {
                                        $imageAttachments[] = $attachment;
                                    } else {
                                        $fileAttachments[] = $attachment;
                                    }
                                }
                            @endphp
                            
                            <!-- Image Attachments as Dropcaps -->
                            @if(count($imageAttachments) > 0)
                                @foreach ($imageAttachments as $attachment)
                                    @php $forumAttDisplay = forum_attachment_display_name($attachment); @endphp
                                    <a href="javascript:void(0);" onclick="if(typeof openImageModal === 'function') { openImageModal('{{ $attachment->path }}', {{ json_encode($forumAttDisplay) }}); } else { window.open('{{ $attachment->path }}', '_blank'); }" 
                                       style="float: left; display: inline-block; cursor: pointer; margin-right: 12px; margin-bottom: 8px; margin-left: 2px; margin-top: 2px;">
                                        <img src="{{ $attachment->path }}" 
                                             alt="{{ $forumAttDisplay }}" 
                                             style="width: 144px; height: 144px; object-fit: contain; transition: transform 0.3s ease; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; display: block;"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';"
                                             onmouseover="this.style.transform='scale(1.05)'"
                                             onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @endforeach
                            @endif
                        @endif
                        
                        @php
                            // First limit to 100 words and strip tags for length calculation
                            $limitedText = Str::words(strip_tags($comment->comment), 300, '...');
                            // Then detect and embed video links with 180px previews
                            $processedText = detect_and_embed_video_links($limitedText, 180, 180);
                        @endphp
                        <div style="text-align: justify; overflow-wrap: break-word;">
                            {!! cleanHtmlContent($processedText) !!}
                        </div>
                    </div>

                    @if ($comment->attachments && $comment->attachments->count() > 0 && count($fileAttachments) > 0)
                    @php
                        $fileAttachmentsList = [];
                        foreach ($comment->attachments as $attachment) {
                            $extension = forum_comment_attachment_raw_extension($attachment);
                            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                $fileAttachmentsList[] = $attachment;
                            }
                        }
                    @endphp
                    <div class="comment-attachments" style="clear: left; margin-top: 1rem;">
                        @if(count($fileAttachmentsList) > 0)
                            <div class="comment-file-attachments-summary">
                                @foreach ($fileAttachmentsList as $attachment)
                                    @include('forums.partials.comment_file_attachment_link', ['attachment' => $attachment])
                                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif


                    <!-- Reply Form (Hidden by default) -->
                    @auth
                    @if (in_array($forum->id, $my_forums))
                    <div class="reply-form-container" id="reply-form-{{ $comment->id }}" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                        <form action="{{ url('forums/comment') }}" method="post" enctype="multipart/form-data" class="reply-form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $forum->id }}" />
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                            
                            <div class="comment-input-wrapper">
                        <div class="comment-input-avatar" style="position: relative; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; background: #f8f9fa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            @if($currentUser && $currentUser->photo)
                                @php
                                    $photoUrl = $currentUser->photo;
                                    $baseUrl = url('/');
                                    if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                        // Already full URL
                                    } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                        // Already contains base URL
                                    } elseif (strpos($photoUrl, '/storage/') === 0) {
                                        $photoUrl = $baseUrl . $photoUrl;
                                    } elseif (strpos($photoUrl, 'storage/') === 0) {
                                        $photoUrl = $baseUrl . '/' . $photoUrl;
                                    }
                                @endphp
                                <img src="{{ $photoUrl }}" alt="{{ $currentUser->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover; object-position: center; position: absolute; top: 0; left: 0; z-index: 1;"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <i class="fa fa-user" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #64748b; font-size: 1rem; z-index: 0;"></i>
                            @else
                                <i class="fa fa-user" style="color: #64748b; font-size: 1rem;"></i>
                                            @endif
                        </div>
                                <div class="comment-form-controls">
                                    <textarea name="comment" class="comment-textarea" placeholder="Write a reply..." rows="3" maxlength="20000" required></textarea>
                                    <div class="comment-char-count" style="font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem;">
                                        <span class="char-count">0</span> / 300 words max
                                    </div>
                                    <button type="submit" class="comment-submit-btn" style="margin-top: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        <i class="fa fa-paper-plane"></i>
                                        Reply
                                    </button>
                                    <button type="button" class="cancel-reply-btn" data-comment-id="{{ $comment->id }}" style="margin-top: 0.5rem; margin-left: 0.5rem; padding: 0.5rem 1rem; background: #e2e8f0; border: none; border-radius: 4px; color: #64748b; font-size: 0.875rem; cursor: pointer;">
                                        Cancel
                                    </button>
                                </div>
                                        </div>
                        </form>
                                    </div>
                                @endif
                    @endauth
                </div>
            </div>

            <!-- Replies -->
            @if ($comment->replies && $comment->replies->count() > 0)
                @foreach ($comment->replies as $reply)
                    <div class="comment-item reply-comment">
                        <div class="comment-avatar" style="position: relative; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; background: #f8f9fa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            @if($reply->user && $reply->user->photo)
                                @php
                                    $photoUrl = $reply->user->photo;
                                    $baseUrl = url('/');
                                    if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                        // Already full URL
                                    } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                        // Already contains base URL
                                    } elseif (strpos($photoUrl, '/storage/') === 0) {
                                        $photoUrl = $baseUrl . $photoUrl;
                                    } elseif (strpos($photoUrl, 'storage/') === 0) {
                                        $photoUrl = $baseUrl . '/' . $photoUrl;
                                    }
                                @endphp
                                <img src="{{ $photoUrl }}" alt="{{ $reply->user->name }}"
                                     style="width: 100%; height: 100%; object-fit: cover; object-position: center; position: absolute; top: 0; left: 0; z-index: 1;"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <i class="fa fa-user" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #64748b; font-size: 1rem; z-index: 0;"></i>
                            @else
                                <i class="fa fa-user" style="color: #64748b; font-size: 1rem;"></i>
                            @endif
                        </div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">
                                    {{ @current_user() && @current_user()->id == $reply->created_by ? 'You' : ($reply->user->name ?? 'Unknown') }}
                                </span>
                                <span class="comment-time">· {{ time_ago($reply->created_at) }}</span>
            @auth
                                <div class="comment-header-actions" style="display: inline-flex; gap: 0.5rem; margin-left: 0.5rem;">
                                    @php 
                                        $replyLikeCount = $reply->likes ? $reply->likes->count() : 0;
                                        $replyIsLiked = auth()->check() ? $reply->isLikedBy() : false;
                                    @endphp
                                    <button type="button" class="comment-action-btn like-comment-btn" data-comment-id="{{ $reply->id }}" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: {{ $replyIsLiked ? '#ef4444' : '#64748b' }};">
                                        <i class="fa fa-heart{{ $replyIsLiked ? '' : '-o' }}" style="color: {{ $replyIsLiked ? '#ef4444' : 'inherit' }};"></i>
                                        <span class="like-count">{{ $replyLikeCount }} {{ $replyLikeCount === 1 ? 'like' : 'likes' }}</span>
                                    </button>
                                    @php $replyShareUrl = forum_thread_url($forum).'#reply-'.$reply->id; @endphp
                                    <button type="button" class="comment-action-btn share-comment-btn" data-share-url="{{ $replyShareUrl }}" title="Share reply" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: #64748b;">
                                        <i class="fa fa-share-alt"></i>
                                        <span>Share</span>
                                    </button>
                                </div>
                                @else
                                <div class="comment-header-actions" style="display: inline-flex; gap: 0.5rem; margin-left: 0.5rem;">
                                    @php $replyLikeCount = $reply->likes ? $reply->likes->count() : 0; @endphp
                                    <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration: none; padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #64748b;">
                                        <i class="fa fa-heart-o"></i>
                                        <span>{{ $replyLikeCount }} {{ $replyLikeCount === 1 ? 'like' : 'likes' }}</span>
                                    </a>
                                    <a href="{{ route('login') }}" class="comment-action-btn" style="text-decoration: none; padding: 0.25rem 0.5rem; font-size: 0.8125rem; color: #64748b;">
                                        <i class="fa fa-share-alt"></i>
                                        <span>Share</span>
                                    </a>
                                </div>
                                @endauth
                                        </div>
                                    <div class="comment-text" style="overflow: hidden;">
                                @if ($reply->attachments && $reply->attachments->count() > 0)
                                    @php
                                        $replyImageAttachments = [];
                                        $replyFileAttachments = [];
                                        foreach ($reply->attachments as $attachment) {
                                            $extension = forum_comment_attachment_raw_extension($attachment);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            if ($isImage) {
                                                $replyImageAttachments[] = $attachment;
                                            } else {
                                                $replyFileAttachments[] = $attachment;
                                            }
                                        }
                                    @endphp
                                    
                                    <!-- Image Attachments as Dropcaps -->
                                    @if(count($replyImageAttachments) > 0)
                                        @foreach ($replyImageAttachments as $attachment)
                                            @php $forumAttDisplayReply = forum_attachment_display_name($attachment); @endphp
                                            <a href="javascript:void(0);" onclick="if(typeof openImageModal === 'function') { openImageModal('{{ $attachment->path }}', {{ json_encode($forumAttDisplayReply) }}); } else { window.open('{{ $attachment->path }}', '_blank'); }" 
                                               style="float: left; display: inline-block; cursor: pointer; margin-right: 12px; margin-bottom: 8px; margin-left: 2px; margin-top: 2px;">
                                                <img src="{{ $attachment->path }}" 
                                                     alt="{{ $forumAttDisplayReply }}" 
                                                     style="width: 144px; height: 144px; object-fit: contain; transition: transform 0.3s ease; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; display: block;"
                                                     onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';"
                                                     onmouseover="this.style.transform='scale(1.05)'"
                                                     onmouseout="this.style.transform='scale(1)'">
                                            </a>
                                        @endforeach
                                    @endif
                                @endif
                                
                                @php
                                    // First limit to 100 words and strip tags for length calculation
                                    $replyLimitedText = Str::words(strip_tags($reply->comment), 300, '...');
                                    // Then detect and embed video links with 180px previews
                                    $replyProcessedText = detect_and_embed_video_links($replyLimitedText, 180, 180);
                                @endphp
                                <div style="text-align: justify; overflow-wrap: break-word;">
                                    {!! cleanHtmlContent($replyProcessedText) !!}
                                        </div>
                                    </div>

                            @if ($reply->attachments && $reply->attachments->count() > 0 && count($replyFileAttachments) > 0)
                            @php
                                $replyFileAttachmentsList = [];
                                foreach ($reply->attachments as $attachment) {
                                    $extension = forum_comment_attachment_raw_extension($attachment);
                                    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                        $replyFileAttachmentsList[] = $attachment;
                                    }
                                }
                            @endphp
                            <div class="comment-attachments" style="clear: left; margin-top: 1rem;">
                                @if(count($replyFileAttachmentsList) > 0)
                                    <div class="comment-file-attachments-summary">
                                        @foreach ($replyFileAttachmentsList as $attachment)
                                            @include('forums.partials.comment_file_attachment_link', ['attachment' => $attachment])
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                @endif
        @endforeach
            @else
        <div class="no-comments">
            <i class="fa fa-comments" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            <p>No comments yet. Be the first to share your thoughts!</p>
        </div>
    @endif
</div>
@endif
