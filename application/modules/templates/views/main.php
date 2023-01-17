<?php
require_once('includes/header.php');
require_once('includes/preloader.php');
?>
<!-- Page -->
<div class="page">
	<?php

	// Get Pending Forum Comments and limit to latest 4
	// $pending_forum_comments = $this->db->select('*');
	// $pending_forum_comments = $this->db->from('forum_comments');
	$pending_forum_comments = $this->db->where('status', 'pending');
	$pending_forum_comments = $this->db->limit(4);
	$pending_forum_comments = $this->db->get('forum_comments');
	$pending_forum_comments = $pending_forum_comments->result();

	// for each comment get user
	foreach ($pending_forum_comments as $key => $comment) {
		$pending_forum_comments[$key]->created_by = $this->db->get_where('user', array('user_id' => $comment->created_by))->row();
		// created_at to time ago
		$pending_forum_comments[$key]->created_at = time_ago($comment->created_at);

		// Truncate comment
		$pending_forum_comments[$key]->comment = truncate($comment->comment, 50);
	}
	$pending_forum_comments_count = $pending_forum_comments ? count($pending_forum_comments) : 0;

	// Get Pending publication comments and limit to latest 4
	$pending_publication_comments = $this->db->select('*');
	$pending_publication_comments = $this->db->from('publication_comments');
	$pending_publication_comments = $this->db->where('status', 'pending');
	$pending_publication_comments = $this->db->get();
	$pending_publication_comments = $pending_publication_comments->result();

	// for each comment get user
	foreach ($pending_publication_comments as $key => $comment) {
		$pending_publication_comments[$key]->created_by = $this->db->get_where('user', array('user_id' => $comment->user_id))->row();
		// created_at to time ago
		$pending_publication_comments[$key]->created_at = time_ago($comment->created_at);

		// Truncate comment
		$pending_publication_comments[$key]->comment = truncate($comment->comment, 50);
	}
	$pending_publication_comments_count = $pending_publication_comments ? count($pending_publication_comments) : 0;

	require("includes/top_bar_main.php");
	require("includes/top_bar_mobile.php");
	require("includes/nav.php");
	?>

	<div class="col-md-12" style="margin-left: 20px;">
		<!-- header-title -->
		
		<div class="header-title">
			<div class="mb-0 mb-lg-0 mb-xl-0">
				<h3 class="mb-2">Dashboard</h3>
				<div class="main-content-breadcrumb"> <span>Dashboard</span> <span><?php echo $title ?></span> </div>
			</div>
		</div>
	</div>
	<?php //} ?>
<?php //print_r ($this->session->userdata()) ?>
	<!-- Main Content -->
	<div class="main-content" style="margin-right: 20px; margin-left: 20px; margin-top: 20px;">
		<div class="container-fluid">
			<?php $this->load->view($module . "/" . $view); ?>
		</div>
	</div>
	
</div>
<!-- Page closed -->
<?php require("includes/footer.php"); ?>