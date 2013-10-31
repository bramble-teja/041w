<?php

include("admin/site_specific.php");

session_start();

include(FUNCTIONS_PATH);

if(!isset($_SESSION["public_producer_id"]))
	die("Error: Not logged in");

include(PUBLIC_INCLUDES . "assistance_submit.php");

?>
