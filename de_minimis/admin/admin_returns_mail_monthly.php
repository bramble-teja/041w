<?php
include("../../userlevel.php");
if($alevel==5 or $alevel==6)
{
include("./site_specific.php");
include(INCLUDES . "/returns_top.php");

$producerid=$_GET['id'];
if(get_return_type($producerid) == "quarterly")

	include(INCLUDES . "/admin_returns_mail_quartly.php");

else
include(INCLUDES . "/admin_returns_mail_monthly.php");
}
else
{
include("./site_specific.php");
include(INCLUDES . "/noaccess.php");
}
?>