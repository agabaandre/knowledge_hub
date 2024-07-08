<?php

$db_conf = array();
$db_conf["type"]     = "mysqli";
$db_conf["server"]   = env('DB_HOST', 'localhost'); // or you mysql ip
$db_conf["user"]     = env('DB_USERNAME'); // username
$db_conf["password"] = env('DB_PASSWORD'); // password
$db_conf["database"] = env('DB_DATABASE');

define("PHPGRID_LIBPATH",base_path("libs/phpgrid/"));

return [
    "db_config"=>$db_conf,
    "js_asset_path"=>"assets/phpgrid/js/"
];

?>