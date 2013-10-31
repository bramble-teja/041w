<?php
include("admin/site_specific.php");
include("header.php");

if(get_return_type($_SESSION["public_producer_id"]) == "quarterly")

	include(PUBLIC_INCLUDES . "/returns_submit.php");

else
	include(PUBLIC_INCLUDES . "/monthly_returns_submit.php");


//include(PUBLIC_INCLUDES . "returns_submit.php");

?>
