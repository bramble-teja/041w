</div>
<div id="banner"><p><a href="/index.html"></a></p></div>
<div id="left_column">
<ul id="menu">
<?php

$menu_items["Home"]="index.php";
$menu_items["Change Details"]="producer_details_ae.php";
//$menu_items["Returns"]="returns_submit.php";
//$menu_items["Completed Returns"] = "returns_report.php";

//if(isset($_SESSION["public_producer_id"]) && !empty($year))
//{
//	$past_return_types = producer_return_types($_SESSION["public_producer_id"], $year);
//
//	if(isset($past_return_types["quarterly"]))
//		$menu_items["Completed Quarterly Returns"] = "returns_report_quarterly.php";
//
//	if(isset($past_return_types["monthly"]))
//		$menu_items["Completed Monthly Returns"] = "returns_report_monthly.php";
//
//        if(isset($past_return_types["quarterly_with_rates"]))
//                $menu_items["Completed Quarterly w/ Rates"] = "returns_report_monthly.php";
//
//
//}

//if(isset($_SESSION["public_producer_id"]) && !empty($year) && get_producer_audits($_SESSION["public_producer_id"]))
	$menu_items["Compliance Status"] = "audit_card.php";

$menu_items["Assistance"]="assistance.php";
$menu_items["Back to " . $back_to_text]="/index.html";
$menu_items["Collection Request"] = "/request/index.html";
$menu_items["Log Out"] = "logout.php";

echo "<li class='selected'>Free Advice: 0845 257 7024</li>\r\n";

foreach($menu_items as $anchor=>$link)
{
	if ($link==$_SERVER['PHP_SELF'])
	{
		echo "<li class='selected'>$anchor</li>\r\n";
	}
	else{
		echo "<li><a href='$link'>$anchor</a></li>\r\n";
	}
}

?>
</ul>
<div id="left_column_image">
<!-- InstanceBeginEditable name="left_col_image" -->
<img src="images/main5.jpg" alt="A solitary leaf" width="214" height="339" />
<!-- InstanceEndEditable -->
</div></div>
</div>


<?php

include(PUBLIC_INCLUDES . "footer.php");

?>
