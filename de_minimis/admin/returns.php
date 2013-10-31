<?php
//$user = $_SERVER['PHP_AUTH_USER'];
//$pw = $_SERVER['PHP_AUTH_PW'];
include("../../userlevel.php");

if($alevel==5 or $alevel==6 or $alevel==2 or $alevel==3)
{
include("./site_specific.php");


include(INCLUDES . "/returns_top.php");

if(get_return_type($producerid) == "quarterly")

	include(INCLUDES . "/returns.php");

else
	include(INCLUDES . "/monthly_returns.php");


}
else
{
include("./site_specific.php");
include(INCLUDES . "/noaccess.php");
}
?>