<?php


	$date=date('d/m/y');

	$cyear=date('Y');

	$cmonth=date('m');

	$con=mysql_connect("localhost","bramble","Bramble041w");

	mysql_select_db("crm_master",$con);

	$getproducer=mysql_query("select * from producer where tp_status=1 and obligation_type_b2b=1 and obligation_type_b2c =0");

	while($gp=mysql_fetch_array($getproducer))

	{

		$pid=$gp['id'];

		$pcid=$gp['organisation_contact_detailsid'];

		$pmail=mysql_query("select email from contact_details where id=$pcid");

		$yearid=mysql_query("select id from year where year=$cyear");

		$year=mysql_fetch_array($yearid);

		$yid=$year['id'];

		$gettype=mysql_query("select * from producer_year where producerid=$pid and yearid=$yid");

			$type1=mysql_fetch_array($gettype);

			$type=$type1['return_type'];

			

	

		while($prodmail=mysql_fetch_array($pmail))

	{

		$mail=$prodmail['email'];

$url="http://avcweeeco.com/clients/target_pipeline/b2b_returns_mail_monthly.php?pid=$pid&y=$yid";

 $to= $mail;
//$to='chitti.girija@gmail.com';




$from ='adminteam@weeeco.com';

$subject = "Environmental Agency returns Information ";

$txt = "<b>Dear Customer!</b><br/>";

$txt .= "Confirmation Mail for Environmental Agency returns Information<br/>";

$txt .="Please click on the below link you can see the your returns information,Please check it once and submit the information to Environmental Agency<br/>";

$txt .="<a href='".$url."'>".$url."</a>";

$headers = "From: '$from'";

$headers = "Date: ".date('r')."\n";

$headers .= "From: '$from'\n";

$headers .= "Message-ID: <".md5(uniqid(time()))."@anotherdomain.xxx>\n";

$headers .= "X-Priority: 3\n";

$headers .= "MIME-Version: 1.0\n";

$headers .= "Content-Transfer-Encoding: 8bit\n";

$headers .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
$headers .= "cc:debbie@weeeco.com\n"; // CC to
//"CC: somebodyelse@example.com";



if(mail($to,$subject,$txt,$headers))

{

$msg="Environmental Agency returnsInformation has been Submitted";

mysql_query("INSERT INTO `mail` (

`producerid` ,

`yearid` ,

`month` ,

`date` ,

`client_accept` ,

`return_type`,`mail_type`

)

VALUES (

 '$pid', '$yid','$cmonth', '$date',0, '$type','Y'

);

");



 }

 else {

echo $msg="Environmental Agency returns Information did n't Submitted";

 }

 

}

}



 ?>

 