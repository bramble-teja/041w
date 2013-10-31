<?php
//$user = $_SERVER['PHP_AUTH_USER'];
//$pw = $_SERVER['PHP_AUTH_PW'];
include("../../userlevel.php");

if($alevel==2 or $alevel==3 or $alevel==4 or $alevel==5 or $alevel==6)
{
include("./site_specific.php");
include(INCLUDES . "/producers_6.php");
}
else
{
include("./site_specific.php");
include(INCLUDES . "/noaccess.php");
}
?>
