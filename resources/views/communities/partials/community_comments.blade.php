@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $currentUser = current_user();
    $communityComments = $communityComments ?? collect();
    $commentCount = $communityComments->count();
@endphp

<div class="community-wall-card" id="community-wall">
    <div class="community-wall-card__head">
        <h2 class="community-wall-card__title">
            <i class="fa fa-heart mr-2" style="color: {{ $primaryColor }};"></i>Community wall
        </h2>
        <span class="badge badge-light" style="background:rgba(17,154,72,0.1);color:{{ $primaryColor }};font-weight:600;">
            {{ $commentCount }} {{ $commentCount === 1 ? 'post' : 'posts' }}
        </span>
    </div>

    @auth
        @if($isCommunityMember ?? true)
            <div class="comment-form-card">
                <div class="comment-form-toggle" onclick="toggleCommunityCommentForm()">
                    <h3 class="comments-header mb-0" style="border:none;padding:0;font-size:1rem;">
                        <i class="fa fa-comment mr-2" style="color: {{ $primaryColor }};"></i>Share with your community
                    </h3>
                    <i class="fa fa-chevron-down" id="communityCommentFormToggleIcon" style="color:#64748b;transition:transform 0.2s ease;"></i>
                </div>
                <div id="communityCommentFormContainer" style="display:none;padding:1.25rem 0 0;">
                    <form action="{{ route('community.comment', $community->id) }}" method="post" enctype="multipart/form-data" id="communityCommentForm" class="community-comment-form">
                        @csrf
                        <div class="comment-input-wrapper">
                            <div class="comment-avatar">
                                @if($currentUser && $currentUser->photo)
                                    <img src="{{ $currentUser->photo }}" alt="{{ $currentUser->name }}"
                                         style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;z-index:1;"
                                         onerror="this.style.display='none';">
                                @else
                                    <i class="fa fa-user" style="color:#64748b;"></i>
                                @endif
                            </div>
                            <div class="comment-form-controls" style="flex:1;">
                                <textarea name="comment" id="communityCommentTextarea" class="comment-textarea"
                                          placeholder="What would you like to share with this community?" rows="3" maxlength="20000" required></textarea>
                                <div class="comment-char-count" style="font-size:0.75rem;color:#94a3b8;text-align:right;margin-top:0.25rem;">
                                    <span class="char-count">0</span> / 300 words max
                                </div>
                                <div class="file-upload-area" id="communityFileUploadArea" style="cursor:pointer;">
                                    <div><i class="fa fa-paperclip mr-1"></i> Attach images, PDF, office, audio, or video (max 2MB per file)</div>
                                    <input type="file" name="attachments[]" id="communityCommentFiles" multiple
                                           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,audio/*,video/*,.mp3,.m4a,.wav,.aac,.ogg,.oga,.opus,.flac,.wma,.mp4,.webm,.mov,.avi,.mkv,.wmv,.flv,.3gp,.mpeg,.mpg"
                                           style="display:none;">
                                </div>
                                <div class="file-preview" id="communityFilePreview" style="margin-top:0.75rem;"></div>
                            </div>
                        </div>
                        <div class="comment-form-footer d-flex justify-content-end mt-3">
                            <button type="submit" class="comment-submit-btn">
                                <i class="fa fa-paper-plane"></i> Post to community wall
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info mb-3">
                <i class="fa fa-info-circle mr-1"></i>Join this community to post on the community wall.
            </div>
        @endif
    @else
        <div class="alert alert-light border mb-3 text-center">
            <p class="mb-2">Sign in to join the conversation on the community wall.</p>
            <a href="{{ route('login', ['redirect' => community_detail_url($community)]) }}" class="comment-submit-btn" style="text-decoration:none;display:inline-flex;">
                <i class="fa fa-sign-in-alt"></i> Log in
            </a>
        </div>
    @endauth

    <div class="comments-section">
        <h3 class="comments-header">
            <i class="fa fa-comments mr-2" style="color: {{ $primaryColor }};"></i>
            <span id="communityCommentsCountLabel">{{ $commentCount }}</span> {{ $commentCount === 1 ? 'Comment' : 'Comments' }}
        </h3>

        @if($communityComments->count() > 0)
            <div id="communityCommentsList">
                @foreach ($communityComments as $comment)
                    @include('communities.partials.community_comment_item', [
                        'comment' => $comment,
                        'community' => $community,
                        'isCommunityMember' => $isCommunityMember ?? true,
                    ])
                @endforeach
            </div>
        @else
            <div class="no-comments" id="communityNoComments">
                <i class="fa fa-comments" style="font-size:2rem;margin-bottom:0.5rem;display:block;"></i>
                <p class="mb-0">No posts yet. Be the first to share something with your community!</p>
            </div>
        @endif
    </div>
</div>
