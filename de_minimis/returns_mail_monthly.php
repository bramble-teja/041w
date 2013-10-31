<?php
include("admin/site_specific.php");
include("header.php");
include("html_header.php");
?>
<script type="text/javascript" src="/clients/tabber.js"></script>
<?php
if(get_return_type($_SESSION["public_producer_id"]) == "quarterly")

	include(PUBLIC_INCLUDES . "/returns_mail_quartly.php");

else
include(PUBLIC_INCLUDES . "returns_mail_monthly.php");

include("footer.php");

?>
