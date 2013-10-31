<?php

include("./site_specific.php");


include(INCLUDES . "/returns_submit_top.php");


if(get_return_type($producerid) == "quarterly")

	include(INCLUDES . "/returns_submit.php");

else
	include(INCLUDES . "/monthly_returns_submit.php");


?>
