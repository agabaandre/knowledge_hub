<?php
date_default_timezone_set('Africa/Kampala');

require_once("includes/header.php");
require_once("includes/preloader.php");
require_once("includes/sidenav.php");
require_once("includes/top_bar_main.php");
require_once("includes/top_bar_chat_user.php");
require_once("includes/top_bar_chat_messages.php");


?>
<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ Main Content ] start -->

                        <?php
                        require_once('includes/breadcrumb.php');

                        $this->load->view($module . "/" . $view); ?>
                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
<?php
require_once("includes/footer.php");
?>