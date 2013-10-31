<?php 

include(FUNCTIONS_PATH);
session_start();

$year = $_SESSION["year"];

$errors = false;
$url= $_SERVER["REQUEST_URI"];
$ad=split('/',$url);
 $prefix=$ad['3'];
if($_POST["action"] == "save_add" || $_POST["action"] == "save_edit")
{
	$errors = producer_form_errors();

	if(empty($_POST["id"]) && $_POST["action"] == "save_edit")
		die("Error: Edit submit needs an ID");

	if(!empty($_POST["id"]) && $_POST["action"] == "save_add")
		die("Error: ID must not be set when adding");

	if(!empty($_POST["id"]) && !is_numeric($_POST["id"]))
		die("Error: ID must be a number");

	if(!check_unique_registration_number(_addslashes($_POST["registrationnumber"]), $_POST["action"]=="save_edit"?$_POST["id"]:false) && $_POST["registrationnumber"] != "")
		$errors[] = "Registration Number must be unique";

	if(!check_unique_name(_addslashes($_POST["organisationname"]), $_POST["action"]=="save_edit"?$_POST["id"]:false))
		$errors[] = "Organisation Name must be unique";

	if(!check_unique_companynumber(_addslashes($_POST["companynumber"]), $_POST["action"]=="save_edit"?$_POST["id"]:false) && $_POST["companynumber"] != "")
		$errors[] = "Company number must be unique";

	if(is_array($errors))
	{
		include(ADMIN_HTDOCS . "/header.php");

		foreach($errors as $error)
		{
?>
	<span class="error">Error: <?php echo $error; ?></span><br />
<?php
		}
		producer_form($prefix,false, $_POST["action"], false);
	}
	elseif($_POST["action"] == "save_add")
		save_add_producer();
	else
		save_edit_producer($_POST["id"]);

	if(!is_array($errors))
		header("Location: index.php");
}
elseif($_GET["action"] == "new")
{
	include(ADMIN_HTDOCS . "/header.php");
?>
	<a href="producers.php">Home</a>
<?php
	producer_form($prefix,false, "save_add", true);
}
elseif($_GET["action"] == "edit")
{
	if(!is_numeric($_GET["id"]))
		die("Error: ID must be a number");
//$url= $_SERVER["REQUEST_URI"];
//$ad=split('/',$url);
//$ad['3'];

	$producer = get_producer($_GET["id"],$year);

	include(ADMIN_HTDOCS . "/header.php");
?>
<a href="producers.php">Home</a>&nbsp;-&nbsp;
<a href="producer_card.php?id=<?php echo $_GET["id"]; ?>">Back</a>
<?php
	producer_form($prefix,$producer, "save_edit", false);
}
else
	die("Error: No Action");

include(ADMIN_HTDOCS . "/footer.php");
?>
