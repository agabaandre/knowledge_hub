<div class="article_detail_wrapss single_article_wrap format-standard">
        <div class="article_body_wrap">
            
            @if(is_valid_image(storage_link('uploads/forums/'.$forum->forum_image)))
            <div class="article_featured_image">
                <img class="img-fluid" src="{{ storage_link('uploads/forums/'.$forum->forum_image) }}" alt="">
            </div>
            @endif

            <h2 class="post-title">{!! $forum->forum_title !!}</h2>
            
            <div class="article_top_info">
                <ul class="article_middle_info">
                    <li><i class="lni lni-alarm-clock mr-1"></i> {{ time_ago($forum->created_at) }}</li>
                    <li><a href="#"><span class="icons"><i class="ti-comment-alt"></i> </span>{{count($forum->comments)}} Comments</a></li>
                </ul>
            </div>

                <p>{!! $forum->forum_description !!}</p>
        </div>
    </div>
    
    
    @if(!@$no_comments)

    <!-- Author Detail -->
    <div class="article_detail_wrapss single_article_wrap format-standard">
        
        <div class="article_posts_thumb">
            <span class="img">
                    @if(is_valid_image(storage_link('uploads/users/'.$forum->user->photo)))
                        <img src="{{ storage_link('uploads/users/'.$forum->user->photo)}}" >
                    @else
                        <img src="{{ storage_link('uploads/users/'.$forum->user->photo)}}">
                    @endif
            </span>
            <small class="text-muted">Posted By</small>
            <h3 class="text-muted">{{ $forum->user->name }}</h3>

            
        </div>
        
    </div>
    
    <!-- Blog Comment -->
    <div class="article_detail_wrapss single_article_wrap format-standard">
        
        <div class="comment-area">
            <div class="all-comments">
                <h3 class="comments-title text-muted">{{ (count($forum->comments)>0)?count($forum->comments):'No '}} Comments</h3>
                <div class="comment-list">
                    <ul>
                        
                    @foreach($forum->comments as $comment)
                        <li class="article_comments_wrap">

                            <article>
                                <div class="comment-details">
                                    <div class="comment-meta">
                                        <div class="comment-left-meta">
                                            @if(is_valid_image(storage_link('uploads/users/'.$comment->user->photo)))
                                            <img src="{{ storage_link('uploads/users/'.$comment->user->photo)}}" width="30px" class="avatar rounded" >
                                            @else
                                                <img src="{{ storage_link('uploads/users/'.$forum->user->photo)}}"  width="30px" class="avatar rounded">
                                            @endif
                                            <h4 class="author-name">{{  (@current_user() && @current_user()->id == $comment->created_by )?'You':$comment->user->name }}</h4>
                                            <div class="comment-date"><small class="text-success">{{ time_ago($comment->created_at)}}</small></div>
                                        </div>

                                        <!--<div class="comment-reply">
                                            <a href="#" class="reply text-success"><span class="icona"><i class="ti-back-left"></i></span> Reply</a>
                                        </div>-->
                                    </div>
                                    <div class="comment-text">
                                        <p>{{$comment->comment}}</p>
                                    </div>
                                    
                                </div>
                            </article>
                            <!--replies-->

                            @if($comment->replies)

                                @foreach($comment->replies as $reply)
                                        @include('forums.partials.comment_replies') 
                                @endforeach

                            @endif
                            
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            
            @auth
            <div class="comment-box submit-form mt-4">
                <h3 class="reply-title">Post Comment</h3>
                <div class="comment-form">
                    <form action="{{ url('forums/comment')}}" method="post">
                        <div class="row">
                            @csrf
                            <input type="hidden" name="id" value="{{$forum->id}}"/>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <textarea name="comment" class="form-control" cols="30" rows="6" placeholder="Type your comment...."></textarea>
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
                <h5 class="text-center text-danger mt-5 mb-5"><i class="fa fa-info-circle"></i> Only logged in users can comment</h5>
            @endauth
            
        </div>
        
    </div>
    
    @endif

    
    