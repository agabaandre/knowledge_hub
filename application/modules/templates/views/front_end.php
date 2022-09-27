<?php
date_default_timezone_set('Africa/Kampala');

require_once("includes-front/site/header.php");
?>

<div ng-app="zealpesa" ng-controller="SiteController">

  <?php
  $this->load->view($module . "/" . $view);
  ?>

</div>

<?php

require_once("includes-front/site/footer.php");
require_once("includes/angular_scripts.php");
?>

<?php

$cookie_name = "new_visitor";
//if not set, show welcome video and set it
if (!isset($_COOKIE[$cookie_name])) : ?>
  <script>
    //show video after 5 seconds
    // window.setTimeout(function(){
    //     showDemoVideo();
    // },2000);
  </script>
<?php
  //set as knonw
  setcookie($cookie_name, "no", time() + (86400 * 30 * 90), "/");
endif; ?>