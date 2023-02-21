	<!-- ============================ Blog Detail Start ================================== -->
			<section style="padding-top:120px;" class="gray">
			
				<div class="container">
				
					<!-- row Start -->
					<div class="row">
					
						<!-- Blog Detail -->
						<div class="col-lg-8 col-md-12 col-sm-12 col-12">
							<div class="article_detail_wrapss single_article_wrap format-standard">
								<div class="article_body_wrap">
                                    
                                    <?php if(is_valid_image($forum->forum_image,"./uploads/forums/")){ ?>
									<div class="article_featured_image">
										<img class="img-fluid" src="<?php echo base_url();?>uploads/forums/<?php echo $forum->forum_image; ?>" alt="">
									</div>
                                    <?php }; ?>
									

									<h2 class="post-title"><?php echo $forum->forum_title; ?></h2>
									
									<div class="article_top_info">
										<ul class="article_middle_info">
                                            <li><i class="lni lni-alarm-clock mr-1"></i> <?php echo time_ago($forum->created_at); ?></li>
											<li><a href="#"><span class="icons"><i class="ti-comment-alt"></i></span><?php echo count($forum->comments); ?> Comments</a></li>
										</ul>
									</div>

                                     <p><?php echo $forum->forum_description; ?></p>
								</div>
							</div>
							
							<!-- Author Detail -->
							<div class="article_detail_wrapss single_article_wrap format-standard">
								
								<div class="article_posts_thumb">
									<span class="img">
										<?php if(is_valid_image($forum->user->photo,"./uploads/authors/")): ?>
						          	  	  <img src="<?php echo base_url();?>uploads/authors/<?php echo $forum->user->photo; ?>" >
						          	  	<?php else: ?>
						          	  		<img src="<?php echo base_url();?>uploads/authors/author.png">
						          	  	<?php endif; ?>
									</span>
								    <small class="text-muted">Posted By</small>
									<h3 class="pa-name"><?php echo $forum->user->name; ?></h3>
									
								</div>
								
							</div>
							
							<!-- Blog Comment -->
							<div class="article_detail_wrapss single_article_wrap format-standard">
								
								<div class="comment-area">
									<div class="all-comments">
										<h3 class="comments-title"><?php echo count($forum->comments); ?> Comments</h3>
										<div class="comment-list">
											<ul>
												
											<?php foreach($forum->comments as $comment): ?>
												<li class="article_comments_wrap">

													<article>
														<!-- <div class="article_comments_thumb">
															<img src="https://via.placeholder.com/500x500" alt="">
														</div> -->
														<div class="comment-details">
															<div class="comment-meta">
																<div class="comment-left-meta">
																	<h4 class="author-name"><?php echo  $comment->user->name; ?></h4>
																	<div class="comment-date"><?php echo time_ago($comment->created_at); ?></div>
																</div>
																<div class="comment-reply">
																	<a href="#" class="reply text-success"><span class="icona"><i class="ti-back-left"></i></span> Reply</a>
																</div>
															</div>
															<div class="comment-text">
																<p><?php echo $comment->comment; ?></p>
															</div>
															
														</div>
													</article>
													<!--replies-->
													<?php 
													    foreach($comment->replies as $reply): 
															include 'includes/comment_replies.php'; 
														endforeach; 
													?>
													
												</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
									<div class="comment-box submit-form mt-4">
										<h3 class="reply-title">Post Comment</h3>
										<div class="comment-form">
											<form action="<?php echo base_url("forums/index"); ?>">
												<div class="row">
													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="form-group">
															<input type="text" class="form-control" placeholder="Your Name">
														</div>
													</div>
													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="form-group">
															<input type="text" class="form-control" placeholder="Your Email">
														</div>
													</div>
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
								</div>
								
							</div>
							
							
						</div>
						
						<!-- Single blog Grid -->
						<div class="col-lg-4 col-md-12 col-sm-12 col-12">
							
							<!-- Searchbard -->
							<div class="single_widgets widget_search">
								<h4 class="title">Search</h4>
								<form action="#" class="sidebar-search-form">
									<input type="search" name="search" placeholder="Search..">
									<button type="submit"><i class="ti-search"></i></button>
								</form>
							</div>

							<!-- Trending Posts -->
							<div class="single_widgets widget_thumb_post">
								<h4 class="title">Recent Forums</h4>
								<ul>

								<?php foreach($forums as $forum): ?>
									<li>
										<span class="left">
											<?php if(is_valid_image($forum->forum_image,"./uploads/forums/")){ ?>
												<img class="img-fluid" src="<?php echo base_url();?>uploads/forums/<?php echo $forum->forum_image; ?>" alt="">
		                                    <?php }; ?>
										</span>
										<span class="right">
											<a class="feed-title" href="#"><?php echo $forum->forum_title; ?></a> 
											
											<span class="post-date"><i class="ti-calendar text-success"></i>
											   <?php echo time_ago($forum->created_at); ?>
										    </span>

										</span>
									</li>
								<?php endforeach; ?>
									
								</ul>
							</div>
							
							<!-- Tags  -->
							<?php if(count($forum->tags)): ?>
								<div class="single_widgets widget_tags">
									<h4 class="title">Tags</h4>
									<ul>
									<?php foreach($forum->tags as $tag): ?>
										<li><a href="#"><?php echo $tag->tag; ?></a></li>
									<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
							
						</div>
						
					</div>
					<!-- /row -->					
					
				</div>
						
			</section>