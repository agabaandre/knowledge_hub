<div class="article_detail_wrapss single_article_wrap format-standard">
    <div class="article_body_wrap">


        @if(is_image($forum->forum_image))
        <div class="article_featured_image mb-3">
            <img class="img-fluid rounded" src="{{ $forum->forum_image }}" alt=""
                 style="display:block; margin:0 auto; max-width:100%; height:auto; max-height:60vh; object-fit: contain;">
        </div>
        @endif

        <h2 class="post-title mb-2">{!! $forum->forum_title !!}</h2>
        <div class="article_top_info d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
            <ul class="article_middle_info">
                <li><span class="text-bold text-red"><i class="lni lni-user mr-1"></i> {{ $forum->user->name }}</span>
                </li><br>
                <li><i class="lni lni-alarm-clock mr-1"></i> {{ time_ago($forum->created_at) }}</li>
                <li><a href="#"><span class="icons"><i class="ti-comment-alt"></i>
                        </span>{{ count($forum->comments) }} Comments</a></li>
            </ul>
            <div class="d-flex align-items-center" style="gap:8px;">
                <a onclick="summarise({{ $forum->id }},1)" class="btn btn-sm btn-success text-white"><i class="fa fa-robot"></i> Summarise</a>
                <div class="btn-group" role="group" aria-label="Share">
                    @php $shareUrl = url('forums/thread').'?id='.$forum->id; $shareText = urlencode(strip_tags($forum->forum_title)); @endphp
                    <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareText }}" title="Share on X"><i class="fab fa-x-twitter"></i></a>
                    <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ urlencode($shareUrl) }}" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyForumLink('{{ $shareUrl }}')" title="Copy link"><i class="fa fa-link"></i></button>
                </div>
            </div>
        </div>

        <div class="mt-2 p-3" style="background:#ffffff; border:1px solid #e5e7eb; border-radius:10px;">
            <p class="mb-0">{!! cleanHtmlContent($forum->forum_description) !!}</p>
        </div>
    </div>


    @if (!@$no_comments)

        <!-- Author Detail -->
        <!-- <div class="article_detail_wrapss single_article_wrap format-standard">
        
        <div class="article_posts_thumb">
            <span class="img">
                    @php
                        $forumUserPhoto = !empty($forum->user->photo) ? storage_link('uploads/users/' . $forum->user->photo) : null;
                    @endphp
                    @if(!empty($forumUserPhoto))
                        <img src="{{ $forumUserPhoto }}" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="user-avatar-fallback" style="display:none; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                    @else
                        <span class="user-avatar-fallback" style="display:flex; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                    @endif
            </span>
            <small class="text-muted">Posted By</small>
            
        </div>
        
    </div>
     -->
        <!-- Blog Comment -->

        <div class="comment-area">
            <div class="all-comments">
                <h3 class="comments-title text-muted mt-3">
                    {{ count($forum->comments) > 0 ? count($forum->comments) : 'No ' }} Engagements</h3>
                <div class="comment-list">
                    <ul>
                        @foreach ($forum->comments as $comment)
                            <li class="article_comments_wrap">

                                <article>
                                    <div class="comment-details">
                                        <div class="comment-meta row">
                                            <div class="comment-left-meta justify-content-center">
                                                @php
                                                    $userPhoto = is_image($comment->user->photo) ? $comment->user->photo : ($forum->user->photo ?? null);
                                                @endphp
                                                @if(!empty($userPhoto))
                                                    <img src="{{ $userPhoto }}" width="30px"
                                                        class="avatar rounded user-avatar-img"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                                    <span class="user-avatar-fallback avatar rounded" style="display:none; width: 30px; height: 30px; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; font-size: 14px;"><i class="fa fa-user"></i></span>
                                                @else
                                                    <span class="user-avatar-fallback avatar rounded" style="display:inline-flex; width: 30px; height: 30px; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; font-size: 14px;"><i class="fa fa-user"></i></span>
                                                @endif
                                                <h4 class="author-name">
                                                    {{ @current_user() && @current_user()->id == $comment->created_by ? 'You' : $comment->user->name }}
                                                </h4>
                                                <div class="comment-date"><small
                                                        class="text-success">{{ time_ago($comment->created_at) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="comment-text">
                                            <p>{!! $comment->comment !!}</p>

                                            @if ($comment->attachments)
                                                @foreach ($comment->attachments as $attachment)
                                                    @if (is_image($attachment->path))
                                                        <img src="{{ $attachment->path }}" class="img-fluid">
                                                    @else
                                                        <a href="{{ $attachment->path }}" target="_blank">Download
                                                            Attachment</a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>
                                </article>
                                <!--replies-->

                                @if ($comment->replies)
                                    @foreach ($comment->replies as $reply)
                                        @include('forums.partials.comment_replies')
                                    @endforeach
                                @endif

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>


            @auth
                @if (in_array($forum->id, $my_forums))
                    <div class="comment-box submit-form mt-4">
                        <h3 class="reply-title">Share your views</h3>
                        <div class="comment-form">
                            <form action="{{ url('forums/comment') }}" method="post">
                                <div class="row">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $forum->id }}" />
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <textarea name="comment" class="form-control summernote" cols="30" rows="6"
                                                placeholder="Type your comment...."></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        @php
                                            $recaptchaSiteKey = config('recaptcha.api_site_key');
                                            $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1']) || 
                                                          app()->environment('local', 'testing');
                                            $showRecaptcha = $recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost;
                                        @endphp
                                        @if($showRecaptcha)
                                            <div class="form-group py-2">
                                                {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlFormSnippet() !!}
                                                @error('g-recaptcha-response')
                                                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group float-right">
                                            <button type="submit" class="btn theme-bg text-white">Submit Now</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <h5 class="text-center text-danger mt-5 mb-5"><i class="fa fa-info-circle"></i> You have to join before
                        you can participate in the discussion</h5>
                    <a class="text-white round btn btn-sm btn-dark mt-1 mb-2" id="join{{ $forum->id }}"
                        href="{{ url('forums/join') }}?id={{ $forum->id }}"><i class="fa fa-link"></i> Join This
                        Discussion</a>
                @endif
            @else
                <h5 class="text-center text-danger mt-5 mb-5"><i class="fa fa-info-circle"></i> Only logged in users
                    participate in discussions</h5>
            @endauth

        </div>

</div>

@endif
