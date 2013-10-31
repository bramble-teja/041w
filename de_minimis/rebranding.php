<?php

session_start();

$logo_files[0]["file"] = "wl_lamp.jpg";
$logo_files[0]["width"] = 149;
$logo_files[0]["backtolink"] = "http://www.weeecorecycle.co.uk";
$logo_files[0]["backtotext"] = "Weeeco";

$logo_files[1]["file"] = "lighting_association.jpg";
$logo_files[1]["width"] = 108;
$logo_files[1]["backtolink"] = "http://www.lightingassociation.com/";
$logo_files[1]["backtotext"] = "Lighting Association";

if(isset($_GET["logo_id"]) && is_numeric($_GET["logo_id"]))
	$_SESSION["logo_id"] = $_GET["logo_id"];

if(!isset($_SESSION["logo_id"]))
	$_SESSION["logo_id"] = 0;

if(!isset($logo_files[$_SESSION["logo_id"]]))
	$_SESSION["logo_id"] = 0;

$logo_id = $_SESSION["logo_id"];

$header_logo = $logo_files[$logo_id]["file"];
$header_width = $logo_files[$logo_id]["width"];
$back_to_link = $logo_files[$logo_id]["backtolink"];
$back_to_text = $logo_files[$logo_id]["backtotext"];

?>
