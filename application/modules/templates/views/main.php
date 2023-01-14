<?php
require_once('includes/header.php');
require_once('includes/preloader.php');
?>
<!-- Page -->
<div class="page">
	<?php
	require("includes/top_bar_main.php");
	require("includes/top_bar_mobile.php");
	require("includes/nav.php");
	?>

	<div class="col-md-12" style="margin-left: 20px;">
		<!-- header-title -->
		<div class="header-title">
			<div class="mb-0 mb-lg-0 mb-xl-0">
				<h4 class="mb-2">Dashboard</h4>
				<div class="main-content-breadcrumb"> <span>Dashboard</span> <span><?php echo $title ?></span> </div>
			</div>
		</div>
	</div>

	<!-- Main Content -->
	<div class="main-content" style="margin-right: 20px; margin-left: 20px; margin-top: 20px;">
		<div class="container-fluid">
			<?php $this->load->view($module . "/" . $view); ?>
		</div>
	</div>
	
</div>
<!-- Page closed -->
<?php require("includes/footer.php"); ?>