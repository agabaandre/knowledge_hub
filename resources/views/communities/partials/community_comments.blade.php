@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $currentUser = current_user();
    $communityComments = $communityWallPosts ?? $communityComments ?? collect();
    $commentCount = method_exists($communityComments, 'total') ? $communityComments->total() : $communityComments->count();
    $openWallPostForm = $openWallPostForm ?? false;
    $embeddedInTab = $embeddedInTab ?? true;
@endphp

<div class="community-wall-card{{ $embeddedInTab ? ' community-wall-card--tab' : '' }}" id="community-wall">
    @if($embeddedInTab)
        <div class="community-wall-post-cta">
            <div class="community-wall-post-cta__copy">
                <strong><i class="fa fa-pencil-square-o mr-1"></i>Share an update with members</strong>
                <span class="d-block small text-muted mt-1">Post news, questions, or files on the community wall — no need to leave this page.</span>
            </div>
            @auth
                @if($isCommunityMember ?? true)
                    <button type="button" class="btn btn-primary community-wall-post-cta__btn" onclick="openCommunityWallPost()">
                        <i class="fa fa-plus-circle mr-1"></i> Post on community wall
                    </button>
                @endif
            @endauth
        </div>
    @else
        <div class="community-wall-card__head">
            <h2 class="community-wall-card__title">
                <i class="fa fa-heart mr-2" style="color: {{ $primaryColor }};"></i>Community wall
            </h2>
            <span class="badge badge-light" style="background:rgba(17,154,72,0.1);color:{{ $primaryColor }};font-weight:600;">
                {{ $commentCount }} {{ $commentCount === 1 ? 'post' : 'posts' }}
            </span>
        </div>
    @endif

    @auth
        @if($isCommunityMember ?? true)
            <div class="comment-form-card" id="communityWallPostFormCard">
                <div class="comment-form-toggle" onclick="toggleCommunityCommentForm()">
                    <h3 class="comments-header mb-0" style="border:none;padding:0;font-size:1rem;">
                        <i class="fa fa-comment mr-2" style="color: {{ $primaryColor }};"></i>Write a wall post
                    </h3>
                    <i class="fa fa-chevron-down" id="communityCommentFormToggleIcon" style="color:#64748b;transition:transform 0.2s ease;"></i>
                </div>
                <div id="communityCommentFormContainer" style="display:{{ $openWallPostForm ? 'block' : 'none' }};padding:1.25rem 0 0;">
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
                                          placeholder="What would you like to share with this community?" rows="4" maxlength="20000" required></textarea>
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
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-2" onclick="toggleCommunityCommentForm()">Cancel</button>
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
            <a href="{{ route('login', ['redirect' => community_detail_url($community, true, ['tab' => 'wall'])]) }}" class="comment-submit-btn" style="text-decoration:none;display:inline-flex;">
                <i class="fa fa-sign-in-alt"></i> Log in
            </a>
        </div>
    @endauth

    <div class="comments-section">
        <h3 class="comments-header">
            <i class="fa fa-comments mr-2" style="color: {{ $primaryColor }};"></i>
            <span id="communityCommentsCountLabel">{{ $commentCount }}</span> {{ $commentCount === 1 ? 'Wall post' : 'Wall posts' }}
        </h3>

        @php
            $wallItems = method_exists($communityComments, 'items') ? collect($communityComments->items()) : $communityComments;
        @endphp
        @if($wallItems->count() > 0)
            <div id="communityCommentsList">
                @foreach ($wallItems as $comment)
                    @include('communities.partials.community_comment_item', [
                        'comment' => $comment,
                        'community' => $community,
                        'isCommunityMember' => $isCommunityMember ?? true,
                    ])
                @endforeach
            </div>
            @if(method_exists($communityComments, 'links'))
                <div class="mt-3">{{ $communityComments->links() }}</div>
            @endif
        @else
            <div class="no-comments" id="communityNoComments">
                <i class="fa fa-comments" style="font-size:2rem;margin-bottom:0.5rem;display:block;"></i>
                <p class="mb-0">No posts yet. Be the first to share something with your community!</p>
            </div>
        @endif
    </div>
</div>

@if($openWallPostForm)
<script>
document.addEventListener('DOMContentLoaded', function () {
    var icon = document.getElementById('communityCommentFormToggleIcon');
    if (icon) icon.style.transform = 'rotate(180deg)';
    var ta = document.getElementById('communityCommentTextarea');
    if (ta) ta.focus();
});
</script>
@endif
