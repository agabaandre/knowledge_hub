<?php require_once 'partials/css_files.php';?>

        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
<div id="main-wrapper">

<?php 

require_once 'partials/header.php';


if(!$is_home)
require_once 'partials/page_search.php';

if(get_flash('danger') || get_flash('success'))
require_once 'partials/alerts.php';

$this->load->view($module . "/" . $view);

require_once 'partials/footer.php';



?>
	