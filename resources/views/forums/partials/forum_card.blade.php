<div class="forum-card"
     data-forum-id="{{ $forum->id }}"
     data-joined="{{ in_array($forum->id, $my_forums) ? 'true' : 'false' }}"
     data-comments="{{ $forum->total_comments ?? count($forum->comments) }}"
     data-date="{{ $forum->created_at }}">
    <div class="forum-header">
        @if($forum->forum_image)
        <img src="{{ $forum->forum_image }}" alt="{{ $forum->forum_title }} - Forum Discussion" class="forum-image" loading="lazy">
        @else
        <div class="forum-image" style="background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 2rem; border: 1px solid #e2e8f0;">
            <i class="fa fa-comments"></i>
        </div>
        @endif

        <div class="forum-content">
            @php
                $authorPhotoUrl = null;
                if ($forum->user && !empty($forum->user->photo)) {
                    $authorPhotoUrl = $forum->user->photo;
                    $baseUrl = url('/');
                    if (strpos($authorPhotoUrl, 'http://') === 0 || strpos($authorPhotoUrl, 'https://') === 0) {
                        // full URL
                    } elseif (strpos($authorPhotoUrl, $baseUrl) !== false) {
                        // already absolute
                    } elseif (strpos($authorPhotoUrl, '/storage/') === 0) {
                        $authorPhotoUrl = $baseUrl . $authorPhotoUrl;
                    } elseif (strpos($authorPhotoUrl, 'storage/') === 0) {
                        $authorPhotoUrl = $baseUrl . '/' . $authorPhotoUrl;
                    }
                }
            @endphp
            <h2 class="forum-title" itemprop="headline">
                <a href="{{ forum_thread_url($forum)}}">{!! $forum->forum_title !!}</a>
            </h2>
            <p class="forum-description">
                @php
                    $limitedDescription = Str::words(strip_tags($forum->forum_description), 80, '...');
                    $processedDescription = detect_and_embed_video_links($limitedDescription, 180, 180);
                @endphp
                {!! $processedDescription !!}
            </p>

            @if(count($forum->tags) > 0)
            <div class="forum-tags">
                @foreach($forum->tags as $tag)
                <span class="tag">#{{ $tag->tag }}</span>
                @endforeach
            </div>
            @endif

            @php
                $totalComments = $forum->total_comments ?? count($forum->comments);
            @endphp
            <div class="forum-meta-actions-wrap">
                <div class="forum-author-avatar-large">
                    @if($authorPhotoUrl)
                        <img src="{{ $authorPhotoUrl }}" alt="{{ $forum->user->name ?? 'User' }}"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa fa-user\' aria-hidden=\'true\'></i>';">
                    @else
                        <i class="fa fa-user" aria-hidden="true"></i>
                    @endif
                </div>
                <div class="forum-meta-actions-body">
            <div class="forum-meta">
                <div class="meta-item forum-meta-author-text">
                    <div>
                        <span class="forum-meta-author-name notranslate" translate="no">{{ $forum->user->name ?? 'Unknown' }}</span>
                        @if($forum->user && trim((string) ($forum->user->job_title ?? '')) !== '')
                            <span class="forum-meta-author-title notranslate" translate="no">{{ $forum->user->job_title }}</span>
                        @endif
                    </div>
                </div>
                <div class="meta-item">
                    <i class="fa fa-clock"></i>
                    <span>{{ time_ago($forum->created_at) }}</span>
                </div>
                @if($totalComments > 0)
                <div class="meta-item comments-toggle-inline collapsed"
                     data-forum-id="{{ $forum->id }}"
                     onclick="toggleComments({{ $forum->id }})"
                     style="cursor: pointer;">
                    <i class="fa fa-comments"></i>
                    <span>{{ $totalComments }} {{ $totalComments === 1 ? 'Comment' : 'Comments' }}</span>
                </div>
                @endif
                @php
                    $totalLikes = $forum->total_likes ?? count($forum->likes);
                    $isLiked = auth()->check() && $forum->isLikedBy(auth()->id());
                    $totalViews = isset($forum->views) ? (int)$forum->views : 0;
                @endphp
                <div class="meta-item like-forum-btn"
                     data-forum-id="{{ $forum->id }}"
                     onclick="likeForum({{ $forum->id }})"
                     style="cursor: pointer; {{ $isLiked ? 'color: #ef4444;' : '' }}">
                    <i class="fa {{ $isLiked ? 'fa-heart' : 'fa-heart-o' }}" style="color: {{ $isLiked ? '#ef4444' : 'inherit' }};"></i>
                    <span class="like-count-{{ $forum->id }}">{{ $totalLikes }}</span>
                    <span>{{ $totalLikes === 1 ? ' like' : ' likes' }}</span>
                </div>
                <div class="meta-item">
                    <i class="fa fa-eye"></i>
                    <span>{{ format_view_count($totalViews) }} {{ $totalViews === 1 ? 'view' : 'views' }}</span>
                </div>
            </div>

            <div class="forum-actions">
                <a href="{{ forum_thread_url($forum)}}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-info-circle"></i> Details
                </a>
                @include('forums.partials.khub_ai_thread_button', [
                    'forum' => $forum,
                    'redirectUrl' => url('forums'),
                ])
                @auth
                    @if(in_array($forum->id, $my_forums))
                        <a href="{{ forum_thread_url($forum)}}" class="btn btn-sm theme-bg text-white">
                            <i class="fa fa-comments"></i> View Discussion
                        </a>
                        <button type="button" class="btn btn-sm theme-bg text-white"
                                onclick="showInlineCommentForm({{ $forum->id }})"
                                id="show-comment-btn-{{ $forum->id }}">
                            <i class="fa fa-plus-circle me-1"></i> Add Comment
                        </button>
                    @else
                        <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm btn-dark" id="join{{ $forum->id }}">
                            <i class="fa fa-link"></i> Join Discussion
                        </a>
                        <button type="button" class="btn btn-sm theme-bg text-white"
                                onclick="showInlineCommentJoinPanel({{ $forum->id }})"
                                id="show-comment-btn-join-{{ $forum->id }}">
                            <i class="fa fa-plus-circle me-1"></i> Add Comment
                        </button>
                    @endif
                @else
                    <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm btn-dark" id="join{{ $forum->id }}">
                        <i class="fa fa-link"></i> Join Discussion
                    </a>
                @endauth
                @include('forums.partials.share_buttons', ['forum' => $forum, 'variant' => 'inline'])
            </div>
                </div>
            </div>

            @php
                $forumComments = $forum->comments->take(5);
                $commentsToShow = max(2, min(5, min($totalComments ?? 0, 5)));
            @endphp

            @if($totalComments > 0 || auth()->check())
            <div class="comments-panel">
                <div class="comments-list" id="comments-list-{{ $forum->id }}" style="display: none;">
                    @if($totalComments > 0)
                        @foreach($forumComments->take($commentsToShow) as $comment)
                            <div class="comment-item-mini">
                                <div class="comment-avatar-mini">
                                    @if($comment->user && $comment->user->photo)
                                        @php
                                            $photoUrl = $comment->user->photo;
                                            $baseUrl = url('/');
                                            if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                                // Already a full URL
                                            } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                                // Already contains base URL
                                            } elseif (strpos($photoUrl, '/storage/') === 0) {
                                                $photoUrl = $baseUrl . $photoUrl;
                                            } elseif (strpos($photoUrl, 'storage/') === 0) {
                                                $photoUrl = $baseUrl . '/' . $photoUrl;
                                            }
                                        @endphp
                                        <img src="{{ $photoUrl }}" alt="Avatar for {{ $comment->user->name ?? 'User' }}"
                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa fa-user\'></i>';">
                                    @else
                                        <i class="fa fa-user"></i>
                                    @endif
                                </div>
                                <div class="comment-content-mini">
                                    <div class="comment-author-mini notranslate" translate="no">{{ $comment->user->name ?? 'Unknown' }}</div>
                                    <div class="comment-text-mini">{!! Str::limit(strip_tags($comment->comment ?? ''), 150) !!}</div>
                                    <div class="comment-time-mini">
                                        <i class="fa fa-clock me-1"></i>{{ time_ago($comment->created_at ?? now()) }}
                                        @if($comment->likes && count($comment->likes) > 0)
                                            <span class="ms-2">
                                                <i class="fa fa-heart text-danger me-1"></i>{{ count($comment->likes) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-comments">No comments yet. Be the first to comment!</div>
                    @endif
                </div>

                @auth
                @if(in_array($forum->id, $my_forums))
                <div class="inline-comment-form" id="comment-form-{{ $forum->id }}" style="display: none;">
                    <form onsubmit="submitInlineComment(event, {{ $forum->id }})" enctype="multipart/form-data" method="post" action="{{ url('forums/comment') }}">
                        <div class="inline-comment-field">
                            <textarea name="comment" id="inline-comment-{{ $forum->id }}"
                                      class="comment-textarea"
                                      placeholder="Add a comment..." required maxlength="20000" rows="3"></textarea>
                            <div class="comment-char-count" style="font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem;">
                                <span class="char-count">0</span> / 300 words max
                            </div>
                            <div class="inline-forum-upload-widget" data-forum-id="{{ $forum->id }}">
                                <div class="file-upload-area inline-file-upload-area" style="cursor: pointer;">
                                    <div class="file-upload-text">
                                        <i class="fa fa-paperclip me-1"></i>
                                        <span>Attach images, PDF, office, audio, or video (max 2MB per file)</span>
                                    </div>
                                    <div class="file-upload-hint">Images · PDF · Word/Excel/PowerPoint (saved as PDF) · Audio · Video</div>
                                    <input type="file" name="attachments[]" class="inline-forum-attachments-input" multiple
                                           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,audio/*,video/*,.mp3,.m4a,.wav,.aac,.ogg,.oga,.opus,.flac,.wma,.mp4,.webm,.mov,.avi,.mkv,.wmv,.flv,.3gp,.mpeg,.mpg"
                                           style="display: none;">
                                </div>
                                <div class="file-preview inline-forum-file-preview"></div>
                            </div>
                        </div>
                        <div class="inline-comment-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="cancelInlineComment({{ $forum->id }})">Cancel</button>
                            <button type="submit" class="btn btn-sm theme-bg text-white">
                                <i class="fa fa-paper-plane me-1"></i>Post Comment
                            </button>
                        </div>
                        @csrf
                    </form>
                </div>
                @else
                <div class="inline-comment-form" id="comment-form-join-{{ $forum->id }}" style="display: none;">
                    <p class="mb-2 text-muted small">Join this discussion to post a comment from the listing.</p>
                    <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm theme-bg text-white">
                        <i class="fa fa-link me-1"></i>Join discussion
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="cancelInlineCommentJoin({{ $forum->id }})">Cancel</button>
                </div>
                @endif
                @endauth
            </div>
            @endif
        </div>
    </div>
</div>
