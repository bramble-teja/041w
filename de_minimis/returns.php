<?php

include("admin/site_specific.php");

include("header.php");
include("html_header.php");

if(get_return_type($_SESSION["public_producer_id"]) == "quarterly")

	include(PUBLIC_INCLUDES . "/returns.php");

else
	include(PUBLIC_INCLUDES . "/monthly_returns.php");

//include(PUBLIC_INCLUDES . "returns.php");

include("footer.php");

?>
