<?php

global $included_header;

include("rebranding.php");

if(!$included_header)
{

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo(SCHEME_NAME); ?></title>
		<link rel="stylesheet" href="styles.php"  type="text/css" />
		<link rel="stylesheet" href="/clients/tabber.css" type="text/css" />

	</head>
	<body>
		<div id="frame">
		<div id="content">

<?php
$included_header = true;
}
?>
