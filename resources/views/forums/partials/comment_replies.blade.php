<ul class="children">
<li class="article_comments_wrap">
  <article>
    <div class="article_comments_thumb">
      <img src="https://via.placeholder.com/500x500" width="120px" alt="">
    </div>
    <div class="comment-details">
      <div class="comment-meta">
        <div class="comment-left-meta">
          <h4 class="author-name text-success">{{$reply->user->name}}</h4>
          <div class="comment-date">{{ time_ago($reply->created_at) }}</div>
        </div>
      </div>
      <div class="comment-text">
        <p><{!! $reply->comment !!}</p>
      </div>
    </div>
  </article>
</li>
</ul>