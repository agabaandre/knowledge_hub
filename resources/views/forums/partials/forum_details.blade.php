<div class="article_detail_wrapss single_article_wrap format-standard">
    <div class="article_body_wrap">


        <div class="article_featured_image">
            <img class="img-fluid" src="{{ $forum->forum_image }}" alt="">
        </div>

        <h2 class="post-title mb-2">{!! $forum->forum_title !!}</h2>
        <div class="article_top_info">
            <ul class="article_middle_info">
                <li><span class="text-bold text-red"><i class="lni lni-user mr-1"></i> {{ $forum->user->name }}</span>
                </li><br>
                <li><i class="lni lni-alarm-clock mr-1"></i> {{ time_ago($forum->created_at) }}</li>
                <li><a href="#"><span class="icons"><i class="ti-comment-alt"></i>
                        </span>{{ count($forum->comments) }} Comments</a></li>
            </ul>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <a onclick="summarise({{ $forum->id }},1)" class="btn btn-md btn-success rounded fs-sm ft-medium"
                    style="min-width:100%; color:white;">
                    <i class="fa fa-robot"></i> Summarise this for me</a>
            </div>
        </div>

        <p>{!! cleanHtmlContent($forum->forum_description) !!}</p>
    </div>


    @if (!@$no_comments)

        <!-- Author Detail -->
        <!-- <div class="article_detail_wrapss single_article_wrap format-standard">
        
        <div class="article_posts_thumb">
            <span class="img">
                    @if (is_valid_image(storage_link('uploads/users/' . $forum->user->photo)))
<img src="{{ storage_link('uploads/users/' . $forum->user->photo) }}" >
@else
<img src="{{ storage_link('uploads/users/' . $forum->user->photo) }}">
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
                                                @if (is_image($comment->user->photo))
                                                    <img src="{{ $comment->user->photo }}" width="30px"
                                                        class="avatar rounded">
                                                @else
                                                    <img src="{{ $forum->user->photo }}" width="30px"
                                                        class="avatar rounded">
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

                                    <div class="col-lg-12">
                                        <label class="form-label" for="attachments">Attachments</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="attachments"
                                                id="attachments">
                                            <label class="custom-file-label" for="validatedCustomFile">Choose
                                                file.s..</label>
                                            <div class="preview py-2"></div>
                                        </div>
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
