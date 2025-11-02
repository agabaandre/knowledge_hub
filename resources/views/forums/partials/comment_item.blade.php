@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $currentUser = $currentUser ?? current_user();
    $my_forums = $my_forums ?? [];
@endphp

<div class="comment-item">
    <div class="comment-avatar" style="position: relative; width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; background: #f8f9fa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        @if($comment->user && $comment->user->photo)
            @php
                // The User model's getPhotoAttribute accessor already handles URL construction
                // Access it directly to trigger the accessor which returns a proper URL via storage_link()
                $photoUrl = $comment->user->photo;
                
                // Double-check: if the accessor didn't return a full URL, construct it manually
                if ($photoUrl && !preg_match('/^https?:\/\//', $photoUrl)) {
                    // Check if it's already a relative path starting with /storage
                    if (strpos($photoUrl, '/storage/') === 0) {
                        $photoUrl = url($photoUrl);
                    } else {
                        // Use the raw photo attribute from database and construct via storage_link
                        $rawPhoto = $comment->user->attributes['photo'] ?? $comment->user->getOriginal('photo') ?? $photoUrl;
                        if ($rawPhoto) {
                            $photoUrl = storage_link('uploads/users/' . $rawPhoto);
                        }
                    }
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
                    $likeCount = $comment->likes ? $comment->likes->count() : 0;
                    $isLiked = auth()->check() ? $comment->isLikedBy() : false;
                @endphp
                <button type="button" class="comment-action-btn like-comment-btn" data-comment-id="{{ $comment->id }}" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: {{ $isLiked ? '#ef4444' : '#64748b' }};">
                    <i class="fa fa-heart{{ $isLiked ? '' : '-o' }}" style="color: {{ $isLiked ? '#ef4444' : 'inherit' }};"></i>
                    <span class="like-count">{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                </button>
                <button type="button" class="comment-action-btn reply-comment-btn" data-comment-id="{{ $comment->id }}" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: #64748b;">
                    <i class="fa fa-reply"></i>
                    <span>Reply</span>
                </button>
                @php 
                    $forumId = isset($forum) && isset($forum->id) ? $forum->id : ($comment->forum_id ?? 0);
                    $commentShareUrl = url('forums/thread').'?id='.$forumId.'#comment-'.$comment->id; 
                @endphp
                <button type="button" class="comment-action-btn share-comment-btn" data-share-url="{{ $commentShareUrl }}" title="Share comment" style="cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.8125rem; background: transparent; border: none; color: #64748b;">
                    <i class="fa fa-share-alt"></i>
                    <span>Share</span>
                </button>
            </div>
            @else
            <div class="comment-header-actions" style="display: inline-flex; gap: 0.5rem; margin-left: 0.5rem;">
                @php $likeCount = $comment->likes ? $comment->likes->count() : 0; @endphp
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
        <div class="comment-text">
            @php
                // First limit to 100 words and strip tags for length calculation
                $limitedText = Str::words(strip_tags($comment->comment), 100, '...');
                // Then detect and embed video links with 180px previews
                $processedText = detect_and_embed_video_links($limitedText, 180, 180);
            @endphp
            {!! cleanHtmlContent($processedText) !!}
        </div>

        @php
            // Get attachments - trigger the accessor if needed
            // Since attachments is in $appends, accessing $comment->attachments should work
            // But let's ensure it's loaded properly
            try {
                // First try to use the accessor (since it's in $appends)
                $commentAttachments = isset($comment->attachments) ? $comment->attachments : null;
                
                // If not available or empty, query directly
                if (!$commentAttachments || (is_object($commentAttachments) && method_exists($commentAttachments, 'count') && $commentAttachments->count() === 0)) {
                    // Direct query to ensure we get the attachments
                    $commentAttachments = \App\Models\CustomAttachment::where('model', 'forum_comments')
                        ->where('record_id', $comment->id ?? 0)
                        ->get();
                }
                
                // Ensure it's a collection
                if (is_array($commentAttachments)) {
                    $commentAttachments = collect($commentAttachments);
                } elseif (!is_object($commentAttachments) || !method_exists($commentAttachments, 'count')) {
                    $commentAttachments = collect();
                }
                
                // Debug logging
                if ($commentAttachments->count() > 0) {
                    \Log::info('Comment attachments found in view', [
                        'comment_id' => $comment->id ?? null,
                        'count' => $commentAttachments->count(),
                        'paths' => $commentAttachments->map(function($a) {
                            return $a->path ?? (isset($a->attributes['path']) ? $a->attributes['path'] : 'no path');
                        })->toArray()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error loading comment attachments in view', [
                    'comment_id' => $comment->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $commentAttachments = collect();
            }
        @endphp
        @if ($commentAttachments && $commentAttachments->count() > 0)
        @php
            $imageAttachments = [];
            $fileAttachments = [];
            foreach ($commentAttachments as $attachment) {
                $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                if ($isImage) {
                    $imageAttachments[] = $attachment;
                } else {
                    $fileAttachments[] = $attachment;
                }
            }
        @endphp
        <div class="comment-attachments">
            @if(count($imageAttachments) > 0)
            <div class="comment-attachments-row">
                <div class="comment-image-attachments" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach ($imageAttachments as $attachment)
                    <div class="comment-attachment-img-wrapper" style="position: relative;">
                        <img src="{{ $attachment->path }}" alt="{{ $attachment->name ?? 'Attachment' }}" class="comment-attachment-img" 
                             onclick="if(typeof openImageModal === 'function') { openImageModal('{{ $attachment->path }}', '{{ $attachment->name ?? 'Attachment' }}'); } else { window.open('{{ $attachment->path }}', '_blank'); }"
                             style="cursor: pointer; width: 180px; height: 180px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                        @if($attachment->name)
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 2px 4px; font-size: 0.625rem; border-radius: 0 0 4px 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ Str::limit($attachment->name, 15) }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if(count($fileAttachments) > 0)
                <div class="comment-file-attachments-summary">
                    @foreach ($fileAttachments as $attachment)
                        @php
                            $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                            $isVideo = in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'ogg', 'ogv']);
                            $isPdf = $extension === 'pdf';
                            $isWord = in_array($extension, ['doc', 'docx']);
                            $isExcel = in_array($extension, ['xls', 'xlsx']);
                            $isPowerpoint = in_array($extension, ['ppt', 'pptx']);
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                            // Use the saved human-readable name, fallback to basename if not available
                            $fileName = $attachment->name ?? basename($attachment->path);
                            $canPreview = $isPdf || $isVideo;
                            
                            // Determine icon class
                            $iconClass = 'fa-file-o';
                            if ($isPdf) $iconClass = 'fa-file-pdf-o';
                            elseif ($isVideo) $iconClass = 'fa-file-video-o';
                            elseif ($isWord) $iconClass = 'fa-file-word-o';
                            elseif ($isExcel) $iconClass = 'fa-file-excel-o';
                            elseif ($isPowerpoint) $iconClass = 'fa-file-powerpoint-o';
                            elseif ($isImage) $iconClass = 'fa-file-image-o';
                        @endphp
                        <a href="{{ $attachment->path }}" target="_blank" class="comment-attachment-file" 
                           style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 4px; text-decoration: none; color: #374151; font-size: 0.8125rem; transition: background 0.2s;"
                           onmouseover="this.style.background='#f3f4f6'" 
                           onmouseout="this.style.background='transparent'"
                           @if($canPreview) onclick="event.preventDefault(); previewFile('{{ $attachment->path }}', '{{ $extension }}'); return false;" @endif>
                            <i class="fa {{ $iconClass }}" style="font-size: 0.875rem; color: {{ $isPdf ? '#dc2626' : ($isVideo ? '#3b82f6' : ($isWord ? '#2563eb' : ($isExcel ? '#16a34a' : ($isPowerpoint ? '#ea580c' : '#6b7280')))) }};"></i>
                            <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $fileName }}</span>
                            @if($canPreview)
                            <i class="fa fa-eye" style="font-size: 0.75rem; opacity: 0.7; margin-left: 4px;" title="Click to preview"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
            @elseif(count($fileAttachments) > 0)
            <div class="comment-file-attachments-only">
                @foreach ($fileAttachments as $attachment)
                    @php
                        $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'ogg', 'ogv']);
                        $isPdf = $extension === 'pdf';
                        $isWord = in_array($extension, ['doc', 'docx']);
                        $isExcel = in_array($extension, ['xls', 'xlsx']);
                        $isPowerpoint = in_array($extension, ['ppt', 'pptx']);
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                        // Use the saved human-readable name, fallback to basename if not available
                        $fileName = $attachment->name ?? basename($attachment->path);
                        $canPreview = $isPdf || $isVideo;
                        
                        // Determine icon class
                        $iconClass = 'fa-file-o';
                        if ($isPdf) $iconClass = 'fa-file-pdf-o';
                        elseif ($isVideo) $iconClass = 'fa-file-video-o';
                        elseif ($isWord) $iconClass = 'fa-file-word-o';
                        elseif ($isExcel) $iconClass = 'fa-file-excel-o';
                        elseif ($isPowerpoint) $iconClass = 'fa-file-powerpoint-o';
                        elseif ($isImage) $iconClass = 'fa-file-image-o';
                    @endphp
                    <a href="{{ $attachment->path }}" target="_blank" class="comment-attachment-file"
                       style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 4px; text-decoration: none; color: #374151; font-size: 0.8125rem; transition: background 0.2s;"
                       onmouseover="this.style.background='#f3f4f6'" 
                       onmouseout="this.style.background='transparent'"
                       @if($canPreview) onclick="event.preventDefault(); previewFile('{{ $attachment->path }}', '{{ $extension }}'); return false;" @endif>
                        <i class="fa {{ $iconClass }}" style="font-size: 0.875rem; color: {{ $isPdf ? '#dc2626' : ($isVideo ? '#3b82f6' : ($isWord ? '#2563eb' : ($isExcel ? '#16a34a' : ($isPowerpoint ? '#ea580c' : '#6b7280')))) }};"></i>
                        <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $fileName }}</span>
                        @if($canPreview)
                        <i class="fa fa-eye" style="font-size: 0.75rem; opacity: 0.7; margin-left: 4px;" title="Click to preview"></i>
                        @endif
                    </a>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Reply Form (Hidden by default) -->
        @auth
        @if (isset($forum) && isset($forum->id) && isset($my_forums) && in_array($forum->id, $my_forums))
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
                        <textarea name="comment" class="comment-textarea" placeholder="Write a reply..." rows="2" maxlength="600" required></textarea>
                        <div class="comment-char-count" style="font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem;">
                            <span class="char-count">0</span>/100 words (max 600 characters)
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

