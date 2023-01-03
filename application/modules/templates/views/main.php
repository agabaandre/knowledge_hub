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
	<!-- Main-content -->
	<div class="main-content horizontal-content">

		<!-- Container-fluid -->
		<div class="container-fluid">

			<!-- Main-content-body -->
			<div class="">

				<!-- header-title -->
				<div class="header-title">
					<div class="mb-0 mb-lg-0 mb-xl-0">
						<h4 class="mb-2">Dashboard</h4>
						<div class="main-content-breadcrumb"> <span>Dashboard</span> <span><?php echo $title ?></span> </div>
					</div>
				</div>

				<?php $this->load->view($module . "/" . $view); ?>
			</div>
			<!-- Main-content-body closed -->
		</div>
		<!-- Container-fluid closed -->
	</div>
	<!-- Main-content closed -->
</div>
<!-- Page closed -->
<?php require("includes/footer.php"); ?>