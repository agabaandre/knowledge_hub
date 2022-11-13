<?php require_once 'partials/css_files.php';?>

        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
<div id="main-wrapper">

<?php 

require_once 'partials/header.php';

$this->load->view($module . "/" . $view);

require_once 'partials/footer.php';

?>
	