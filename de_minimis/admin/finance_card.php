<?php
//$user = $_SERVER['PHP_AUTH_USER'];
//$pw = $_SERVER['PHP_AUTH_PW'];
include("../../userlevel.php");

if($alevel==5 or $alevel==6 or $alevel==4  )
{
include("./site_specific.php");
include(INCLUDES . "/finance_card.php");
}
else
{
include("./site_specific.php");
include(INCLUDES . "/noaccess.php");
}
?>
