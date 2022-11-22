<ul class="children">
<li class="article_comments_wrap">
  <article>
    <div class="article_comments_thumb">
      <img src="https://via.placeholder.com/500x500" alt="">
    </div>
    <div class="comment-details">
      <div class="comment-meta">
        <div class="comment-left-meta">
          <h4 class="author-name text-success"><?php echo  $reply->user->name; ?></h4>
          <div class="comment-date"><?php echo time_ago($reply->created_at); ?></div>
        </div>
      </div>
      <div class="comment-text">
        <p><?php echo  $reply->comment; ?></p>
      </div>
    </div>
  </article>
</li>
</ul>