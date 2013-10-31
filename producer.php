<?php

function save_edit_finance($producerid)
{
	global $year;


	//echo "<!-- ".$year."--><!--".$producerid."-->";
	$old_finance_details = get_finance($producerid,$year);

	if(!$old_finance_details)
		die("Error: Couldn't find the old finance");
$enforcement_contact_id=$_POST["enforcementid"];
	$finance_contact_id = $old_finance_details["financecontactid"];
    $financeid = $old_finance_details["fid"];
//   $finance_contact_sql = crm_contact_details_post_to_sql("finance");
	//query("update contact_details set $finance_contact_sql 
		//	where id = $finance_contact_id");
		
			if($_POST["copy_contact_details_f"] == "on" && $finance_contact_id != $enforcement_contact_id)
	{
		query("delete from contact_details where id = $finance_contact_id");
		$finance_contact_id = $enforcement_contact_id;
		query("update crm_finance set contact_type_id=$finance_contact_id
			where finance_id=$financeid and producer_id=$producerid ");
	}
	elseif($_POST["copy_contact_details_f"] != "on" && $finance_contact_id != $enforcement_contact_id)
	{
		
	$finance_contact_sql = crm_contact_details_post_to_sql("finance");

	query("update contact_details set $finance_contact_sql 
			where id = $finance_contact_id");
	} 
	elseif($_POST["copy_contact_details_f"] != "on")
	{
		
			$finance_contact_sql = crm_contact_details_post_to_sql("finance");

		query("insert into contact_details set  $finance_contact_sql");

		 $finance_contact_id = last_id();
		
		query("update crm_finance set contact_type_id=$finance_contact_id
			where finance_id=$financeid and producer_id=$producerid ");
	}

	$finance_main_sql =crm_finance_post_to_sql("finance");
	query("update crm_finance set $finance_main_sql,pcs='".$_POST['pcs']."'
			where finance_id=$financeid and producer_id=$producerid ");

				for($i=1;$i<=13;$i++)
				{
				
 $finance_cat_sql =crm_finance_cat_post_to_sql($i);
	
	$selcat=query("select * from  crm_finance_categories where finance_id = $financeid and cat_id=".$i." and producer_id =$producerid");
$num=num_rows($selcat);
if($num==0)
{
	add_finance_categories($producerid,$financeid);
}
else
{
	query("update crm_finance_categories set $finance_cat_sql 
			where finance_id = $financeid and cat_id=".$i." and producer_id =$producerid");
}
	}

	
	

//	if($old_producer_details["registrationnumber"] != $_POST["registrationnumber"])
//		send_new_producer_email($producerid);
}
function save_edit_producer($producerid)
{
	global $year;

	//echo "<!-- ".$year."--><!--".$producerid."-->";

	$old_producer_details = get_producer($producerid,$year);

	if(!$old_producer_details)
		die("Error: Couldn't find the old producer");

	$daytoday_contact_id = $old_producer_details["daytodaycontactid"];
	$enforcement_contact_id = $old_producer_details["enforcementcontactid"];
	$emergency_contact_id = $old_producer_details["emergencycontactid"];
    
	$old_finance_details = get_finance($producerid,$year);
 $financeid_123 = $old_finance_details["fid"];
 $vat=$_POST['vatregistered'];
 $ann_turn=$_POST['annualturnover'];
if($vat=='on')
{
	if($ann_turn >1000000)
	{
	$eafee="&pound;445";
	$band="More than 1 million pounds";
	}
	else
	{
	$eafee="&pound;220";
	$band="Less than 1 million pounds";
	}
}
else
{
	$eafee="&pound;30";
	$band="Below Vat";
	
}
	query("update crm_finance set purchase_order_ea_fee='$eafee',management_band='$band',management_returns_ea_fee='$eafee',annual_turnover='".$_POST['annualturnover']."'
			where finance_id=$financeid_123 and producer_id=$producerid ");



	
	$enforcement_contact_sql = crm_contact_details_post_to_sql("enforcement");

	query("update contact_details set $enforcement_contact_sql 
			where id = $enforcement_contact_id");

if($_POST["copy_contact_details"] == "on" && $daytoday_contact_id != $enforcement_contact_id)
	{
		query("delete from contact_details where id = $daytoday_contact_id");
		$daytoday_contact_id = $enforcement_contact_id;
	}
	elseif($_POST["copy_contact_details"] != "on" && $daytoday_contact_id != $enforcement_contact_id)
	{
		$daytoday_contact_sql = crm_contact_details_post_to_sql("daytoday");

		query("update contact_details set $daytoday_contact_sql 
				where id = $daytoday_contact_id");
	} 
	elseif($_POST["copy_contact_details"] != "on")
	{
		$daytoday_contact_sql = contact_details_post_to_sql("daytoday");

		query("insert into contact_details set $daytoday_contact_sql");

		$daytoday_contact_id = last_id();
	}
	
	if($_POST["copy_contact_details_em"] == "on" && $emergency_contact_id != $enforcement_contact_id)
	{
		query("delete from contact_details where id = $emergency_contact_id");
		$emergency_contact_id = $enforcement_contact_id;
	}
	elseif($_POST["copy_contact_details_em"] != "on" && $emergency_contact_id != $enforcement_contact_id)
	{
		 $emergency_contact_sql = crm_contact_details_post_to_sql("emergency");

	query("update contact_details set $emergency_contact_sql 
			where id = $emergency_contact_id");
	} 
	elseif($_POST["copy_contact_details_em"] != "on")
	{
			 $emergency_contact_sql = crm_contact_details_post_to_sql("emergency");

		query("insert into contact_details set  $emergency_contact_sql");

		$emergency_contact_id = last_id();
	}

	$producer_sql = producer_post_to_sql();

	query("update producer set $producer_sql, 
				daytoday_contact_detailsid = $daytoday_contact_id, 
				enforcement_contact_detailsid = $enforcement_contact_id,
					emergency_contact_detailsid = $emergency_contact_id

				where id=$producerid");
if(!empty($_FILES["produceridentificationmark"]) && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
    {
        $rs = query("select id,image_name,letter from producer_identification_mark where producerid = $producerid order by id desc ");
        $row = fetch_row($rs);
           
        $charno = (num_rows($rs)==0)?65:($row[2]+1);
        $imagename = $old_producer_details["organisationname"]." logo".chr($charno);
         
        query("insert into producer_identification_mark (producerid,image_name,letter) values($producerid,'{$imagename}',$charno)");
       
       
        //$imageid = last_id();

        move_uploaded_file($_FILES["produceridentificationmark"]["tmp_name"], LOGO_PATH . "/$imagename.jpeg");
           
        resizePhoto(150, 200, "produceridentificationmark", LOGO_PATH . "/$imagename.jpeg");
    }

	if(is_array($old_producer_details["identificationmarks"]))
		foreach($old_producer_details["identificationmarks"] as $identificationmark)
		{
			if($_POST["delete_logo_" . $identificationmark["id"]] == "on")
			{
				query("delete from producer_identification_mark where id=" . $identificationmark["id"]);
				unlink(LOGO_PATH . "/" . $identificationmark["filename"]);
			}
		}


	if($_POST["producertype"] == "company" && $old_producer_details["producertype"] == "company")
	{
	
		update_company($producerid);
	}
			

	elseif($_POST["producertype"] == "company" && $old_producer_details["producertype"] == "partnership")
	{
		add_company($producerid);
		delete_partners($producerid);
	}
	elseif($_POST["producertype"] == "partnership" && $old_producer_details["producertype"] == "company")
	{
		add_partners($producerid);
		delete_company($producerid);
	}
	elseif($_POST["producertype"] == "partnership" && $old_producer_details["producertype"] == "partnership")
		update_partners($producerid);

//	if($old_producer_details["registrationnumber"] != $_POST["registrationnumber"])
//		send_new_producer_email($producerid);

save_edit_tp($producerid);
save_edit_sp($producerid);

crmAssignLevels($producerid);	

}
function save_edit_tp($producerid)
{
	$tndate=date('d/m/y');
	 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$tntime=$hrs .":". $min;
$tnote=$_POST["target_notes"			];
$tempty=explode(">",$tnote);
 $tcnt=count($tempty);
	 $tamt=$tcnt-1;

	  $tll=$tempty[$tamt];
$taddnote=$_POST["old"			];
	$tlen= strlen($tll);
	if($tlen==4)
	{
		
		$tcheck=query("select notes from crm_target_pipeline where producer_id = $producerid and member_status='T' ");
$tcheck_fet=fetch_row($tcheck);
if($tcheck_fet[0]=='')
 $temp_note=$taddnote;
else
 $temp_note=$tcheck_fet[0];

		
	}
	else
	{
	 $temp_note=$tnote;		


	}
	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
$target_sql = target_post_to_sql("target");
query("update  crm_target_pipeline set $target_sql ,notes='".addslashes($temp_note)."', note_tp_date='".addslashes($temp_note)."' where producer_id=$producerid and member_status='T' "); 
	
}
function save_edit_sp($producerid)
{
 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$sntime=$hrs .":". $min;
	$sndate=date('d/m/y');

//$snote="TP - ".$sndate."-".$sntime. " Notes made next day , client still great but talks too much on phone";
	$snote=$_POST["sales_notes"			];
	$saddnote=$_POST["old"			];
	$empty=explode(">",$snote);
 $cnt=count($empty);
	 $amt=$cnt-1;
	  $ll=$empty[$amt];
	
	$len= strlen($ll);
	if($len==4)
	{
		$check=query("select notes from crm_target_pipeline where producer_id = $producerid and member_status='S' ");
$check_fet=fetch_row($check);
if($check_fet[0]=='')
 $emp_note=$saddnote;
else
 $emp_note=$check_fet[0];


		
	}
	else
	{
	 $emp_note=	$snote;		


	}
	$weeestatus=$_POST['sales_status'];
	$tsseltp=query("select note_tp_date from crm_target_pipeline where producer_id = $producerid and member_status='T' ");
$tsfettp=fetch_row($tsseltp);
$tsnote=$tsfettp[0];
	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
$sales_sql = target_post_to_sql("sales");
query("update  crm_target_pipeline set $sales_sql ,notes='".addslashes($emp_note)."' , note_tp_date='".addslashes($tsnote)."' ,note_sp_date='".addslashes($emp_note)."',status='$weeestatus' where producer_id=$producerid and member_status='S' "); 


}
function send_new_producer_email($producerid)
{
return;
	$producer = get_producer($producerid);
	$contact = $producer["enforcementcontactdetails"];

	$to = $contact["email"];

	$subject = SCHEME_NAME . " login details for ". $contact["title"] . " " .  $contact["name"] . " " . $contact["surname"];

	$email  = "Dear " . $contact["title"] . " " .  $contact["name"] . " " . $contact["surname"] . "\r\n";
	$email .= "Please find the link and credentials to login to " . SCHEME_NAME ."\r\n";
	$email .= "Your username is " . $producer["registrationnumber"]  . " and your password is " . $producer["password"] . "\r\n";
	$email .= "Login at " . SCHEME_URL . "\r\n";
	$email .= "Should you have any problems logging on please contact your account manager on 0191 423 6232\r\n";
	$email .= "Barry Groves\r\n";
	$email .= "CEO\r\n";

	mail($to, $subject, $email, "From: " . SCHEME_EMAIL_FROM);
}

function save_add_crm()
{

	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");

	$producer_sql = producer_post_to_sql();
	
	
	$enforcement_contact_sql = crm_contact_details_post_to_sql("enforcement");

	query("insert into contact_details set $enforcement_contact_sql");



	$enforcement_contact_id = last_id();

	if($_POST["copy_contact_details"] == "on")

		$daytoday_contact_id = $enforcement_contact_id;

	else
	{
		 $daytoday_contact_sql = crm_contact_details_post_to_sql("daytoday");

		query("insert into contact_details set $daytoday_contact_sql");

		$daytoday_contact_id = last_id();
	} 
	if($_POST["copy_contact_details_em"] == "on")
	  $emergency_contact_id = $enforcement_contact_id;
	else
	{
	 $emergency_contact_sql = crm_contact_details_post_to_sql("emergency");
	
	query("insert into contact_details set $emergency_contact_sql");
  $emergency_contact_id=last_id();
	}
 
	$sql = "insert into producer set $producer_sql, 
					daytoday_contact_detailsid = $daytoday_contact_id, 
					enforcement_contact_detailsid = $enforcement_contact_id,
					 emergency_contact_detailsid = $emergency_contact_id";


	query($sql);
	

	$producerid = last_id();
	

/*	if(!empty($_FILES["produceridentificationmark"]) && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
	{

		query("insert into producer_identification_mark set producerid = $producerid");
		$imageid = last_id(); 

		//move_uploaded_file($_FILES["produceridentificationmark"]["tmp_name"], LOGO_PATH . "/$imageid.jpeg");
		resizePhoto(150, 200, "produceridentificationmark", LOGO_PATH . "/$imageid.jpeg");
	}*/
if(!empty($_FILES["produceridentificationmark"]) && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
    {
       
       
         $imagename = $old_producer_details["organisationname"]."logoA";
        query("insert into producer_identification_mark (producerid,image_name,letter) values($producerid,'{$imagename}',65)");
        //$imageid = last_id();
        resizePhoto(150, 200, "produceridentificationmark", LOGO_PATH . "/$imagename.jpeg");
        //move_uploaded_file($_FILES["produceridentificationmark"]["tmp_name"], LOGO_PATH . "/$imageid.jpeg");
    }

	if($_POST["producertype"] == "company")
		add_company($producerid);
	else
		add_partners($producerid);
$yearid = get_year_id($year);
	query("insert ignore into year set year=\"$year\"");

	query("insert into producer_year (producerid, yearid) select $producerid, id from year where year=\"$year\"");
	query("insert into year_rate (producerid, yearid) select $producerid, id from year where year=\"$year\"");
    query("update producer_year set return_type=\"" . $_POST["quarterly_monthly"] . "\" where producerid = $producerid and yearid = '$yearid'");
	query("update year_rate set rate=\"" . $_POST["rate"] . "\" where producerid=$producerid and yearid = '$yearid'");



	
// insert into db finance contact information
if($_POST["copy_contact_details_em"] == "on")
	$finance_contact_id = $enforcement_contact_id;
	else
	{
	$finance_contact_sql = crm_contact_details_post_to_sql("finance");

	query("insert into contact_details set $finance_contact_sql");

	$finance_contact_id = last_id();
	}
	
// finance  other information to db

add_finance($producerid,$finance_contact_id );



//insert audit information into db

save_audit_crm($producerid, $year);

crmAssignLevels($producerid);		
}
function save_add_tp($producerid)
{

	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
$target_sql = target_post_to_sql("target");
query("insert  crm_target_pipeline set  producer_id = $producerid ,$target_sql ,member_status='T' "); 
query("update producer set tp_status=1 where id = $producerid "); 
crmAssignLevels($producerid);		
}
function save_add_dm($producerid)
{

	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
$target_sql = target_post_to_sql("dimin");
query("insert  crm_target_pipeline set  producer_id = $producerid ,$dimin_sql ,member_status='D' "); 
query("update producer set dm_status=1 where id = $producerid "); 
crmAssignLevels($producerid);		
}
function save_add_sp($producerid)
{

	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
$sales_sql = target_post_to_sql("sales");

query("insert  crm_target_pipeline set  producer_id = $producerid ,$sales_sql,member_status='S'  "); 
query("update producer set sp_status=1 where id = $producerid "); 
query("update producer set tp_status=0 where id = $producerid "); 
crmAssignLevels($producerid);	
}
function target_post_to_sql($prefix)
{
global $_POST;
$sql =  "  noticestatus		= \"" . _addslashes($_POST[$prefix . "_noticestatus"			]) . "\"";
$sql .=  ",forename 		= \"" . _addslashes($_POST[$prefix . "_forename"			]) . "\"";
$sql .=  ",  current_pcs 		= \"" . _addslashes($_POST[$prefix . "_currentpcs"			]) . "\"";
	$sql .= ", account_manager		= \"" . _addslashes($_POST[$prefix . "_amanager"		]) . "\"";
	$sql .= ", last_contact		= \"" . _addslashes($_POST[$prefix . "_lcdate"			]) . "\"";
		$sql .= ",next_contact 		= \"" . _addslashes($_POST[$prefix . "_ncdate"		]) . "\"";
		$sql .= ",packaging		= \"" . _addslashes($_POST[$prefix . "_package"			]) . "\"";
	$sql .= ",battery		= \"" . _addslashes($_POST[$prefix . "_battery"			]) . "\"";
	

	return($sql);
}
function save_audit_crm($producerid, $year)
{
	query("insert ignore into audit (producerid, yearid) select $producerid, id from year where year = $year");

	$yearid = get_year_id($year);

	$sql = crm_audit_post_to_sql();

	query("update audit set $sql where producerid = $producerid and yearid = $yearid"); 

	return;
}

function crm_audit_post_to_sql()
{
	global $_POST;
	
	$sql .= "part_one_status 		= '" . $_POST["part_one_status"			] . "'";
	$sql .= ",part_two_status 		= '" . $_POST["part_two_status"			] . "'";
	

	$explodee = explode('/', $_POST["audit_date"]);

	$date = $explodee[2] . "-" . $explodee[0] . "-" . $explodee[1];

	$sql .= ",audit_date			= '" . $date . "'";

	if($_POST["hwr_cb"] == "on")
		$sql .= ", hwr_cb=1";
	else
		$sql .= ", hwr_cb=0";

	if($_POST["ep41_cb"] == "on")
		$sql .= ", ep41_cb=1";
	else
		$sql .= ", ep41_cb=0";
	 $sql .= ",general_comments_txt 		= '" . addslashes($_POST["general_comments_txt"	]) . "'";


	

	return($sql);
}
function add_finance($producerid,$finance_contact_id )
{
global $_POST;
 $vat=$_POST['vatregistered'];
 $ann_turn=$_POST['annualturnover'];
if($vat=='on')
{
	if($ann_turn >1000000)
	{
	//$eafee="&pound;445";
	$band="More than 1 million pounds";
	}
	else
	{
	//$eafee="&pound;210";
	$band="Less than 1 million pounds";
	}
}
else
{
	//$eafee="&pound;30";
	$band="Below Vat";
	
}


$sql =  "  department 		= \"" . _addslashes($_POST["finance_department"			]) . "\"";
$sql .=  ",  b2c_obligation 		= \"" . _addslashes($_POST["b2c_obligation"			]) . "\"";
	$sql .= ", purchase_order_management_fee 		= \"" . _addslashes($_POST["purchase_mfee"		]) . "\"";
	$sql .= ", purchase_order_ea_fee 		= \"" . _addslashes($_POST["purchase_eafee"			]) . "\"";
		$sql .= ",management_type 		= \"" . _addslashes($_POST["purchase_type"		]) . "\"";
	$sql .= ", management_band		= \"" . _addslashes($band) . "\"";
	$sql .= ", management_returns_m_fee			= \"" . _addslashes($_POST["b2c_mfee"			]) . "\"";
	$sql .= ", annual_turnover 		= \"" . _addslashes($_POST["annualturnover"			]) . "\"";
	$sql .= ", management_returns_ea_fee		= \"" . _addslashes($_POST["b2c_eafee"				]) . "\"";
	$sql .= ", navision_code	= \"" . _addslashes($_POST["navisioncode"		]) . "\"";
	$sql .= ", pcs	= \"" . _addslashes($_POST["pcs"		]) . "\"";

	query("insert into  crm_finance set  producer_id = $producerid,contact_type_id=$finance_contact_id, $sql"); 
	$f_id=last_id();
	if($_POST['b2b']=='on' && $_POST['b2c']=='')
	{
     return true;
	}
	else
	{
	add_finance_categories($producerid,$f_id);
	}
}
function add_finance_categories($producerid,$finance_contact_id )
{
global $_POST;

for($c=1;$c<=13;$c++)
{
$sql  =  "levypertones 		= \"" . _addslashes($_POST["levytone".$c		]) . "\"";
$sql .=  ", levyperunits 		= \"" . _addslashes($_POST["levunit".$c		]) . "\"";
$sql .=  ", fixedprice 		= \"" . _addslashes($_POST["fixedpr".$c		]) . "\"";
$sql .=  ", opening_rate 		= \"" . _addslashes($_POST["or".$c		]) . "\"";
$sql .=  " , 2009_status 		= \"" . _addslashes($_POST["9cat"	.$c		]) . "\"";
	$sql .= ", 2009_count 		= \"" . _addslashes($_POST["9kg".$c	]) . "\"";
	$sql .= ", 2010_status 		= \"" . _addslashes($_POST["10cat".$c		]) . "\"";
		$sql .= ",	2010_count 		= \"" . _addslashes($_POST["10kg".$c	]) . "\"";

	query("insert into  crm_finance_categories set  finance_id=$finance_contact_id,cat_id =$c, producer_id = $producerid, $sql"); 
}
}

function crm_contact_details_post_to_sql($prefix)
{
	global $_POST;

	$sql =  "  title 		= \"" . _addslashes($_POST[$prefix . "_title"			]) . "\"";
	$sql .= ", forename 		= \"" . _addslashes($_POST[$prefix . "_forename"		]) . "\"";
	$sql .= ", surname 		= \"" . _addslashes($_POST[$prefix . "_surname"			]) . "\"";
	$sql .= ", phone 		= \"" . _addslashes($_POST[$prefix . "_landline"		]) . "\"";
	$sql .= ", mobile 		= \"" . _addslashes($_POST[$prefix . "_mobile"			]) . "\"";
	$sql .= ", fax 			= \"" . _addslashes($_POST[$prefix . "_fax"			]) . "\"";
	$sql .= ", email 		= \"" . _addslashes($_POST[$prefix . "_email"			]) . "\"";
	$sql .= ", position 		= \"" . _addslashes($_POST[$prefix . "_position"			]) . "\"";
	$sql .= ", primary_name 	= \"" . _addslashes($_POST[$prefix . "_address1"		]) . "\"";
	$sql .= ", secondary_name 	= \"" . _addslashes($_POST[$prefix . "_address2"		]) . "\"";
	$sql .= ", street_name 		= \"" . _addslashes($_POST[$prefix . "_address3"		]) . "\"";
	$sql .= ", town 		= \"" . _addslashes($_POST[$prefix . "_town"			]) . "\"";
	$sql .= ", administrative_area	= \"" . _addslashes($_POST[$prefix . "_area"		]) . "\"";
	$sql .= ", countryid 		= " . get_country_id(_addslashes($_POST[$prefix . "_country"]));
	$sql .= ", postcode 		= \"" . _addslashes($_POST[$prefix . "_postcode"		]) . "\"";


	return($sql);
}
function crm_finance_post_to_sql($prefix)
{
	global $_POST;

	$sql =  "  department 		= \"" . _addslashes($_POST["department"			]) . "\"";
	$sql .= ", b2c_obligation		= \"" . _addslashes($_POST["b2c_obligation"		]) . "\"";
	$sql .= ", purchase_order_management_fee 	 		= \"" . _addslashes($_POST["purchase_mfee"			]) . "\"";
	//$sql .= ", purchase_order_ea_fee 		= \"" . _addslashes($_POST["purchase_eafee"		]) . "\"";
	$sql .= ", management_type		= \"" . _addslashes($_POST["purchase_type"			]) . "\"";
	//$sql .= ", management_band 			= \"" . _addslashes($_POST["purchase_band"		]) . "\"";
	$sql .= ", management_returns_m_fee		= \"" . _addslashes($_POST["b2c_mfee"			]) . "\"";
	$sql .= ", annual_turnover 		= \"" . _addslashes($_POST["b2c_annualturnover"			]) . "\"";
	//$sql .= ", management_returns_ea_fee 	= \"" . _addslashes($_POST["b2c_eafee"		]) . "\"";
	$sql .= ", navision_code	= \"" . _addslashes($_POST["navisioncode"		]) . "\"";
	


	return($sql);
}
function crm_finance_cat_post_to_sql($prefix)
{
	global $_POST;
$sql  =  "levypertones 		= \"" . _addslashes($_POST["levytone".$prefix		]) . "\"";
$sql .=  ", levyperunits 		= \"" . _addslashes($_POST["levunit".$prefix		]) . "\"";
$sql .=  ", fixedprice 		= \"" . _addslashes($_POST["fixedpr".$prefix		]) . "\"";
$sql .=  ", opening_rate 		= \"" . _addslashes($_POST["or".$prefix		]) . "\"";
$sql .=  " , 2009_status 		= \"" . _addslashes($_POST["9cat"	.$prefix		]) . "\"";
	$sql .= ", 2009_count 		= \"" . _addslashes($_POST["9kg".$prefix	]) . "\"";
	$sql .= ", 2010_status 		= \"" . _addslashes($_POST["10cat".$prefix		]) . "\"";
		$sql .= ",	2010_count 		= \"" . _addslashes($_POST["10kg".$prefix	]) . "\"";

	return($sql);
}
function crm_contact_details_post_to_email($prefix)
{
	global $_POST;

	$email =  "title 		= " . $_POST[$prefix . "_title"		] . "\n";
	$email .= "forename 		= " . $_POST[$prefix . "_forename"	] . "\n";
	$email .= "surname 		= " . $_POST[$prefix . "_surname"	] . "\n";
	$email .= "phone 		= " . $_POST[$prefix . "_landline"	] . "\n";
	$email .= "mobile 		= " . $_POST[$prefix . "_mobile"	] . "\n";
	$email .= "fax			= " . $_POST[$prefix . "_fax"		] . "\n";
	$email .= "email 		= " . $_POST[$prefix . "_email"		] . "\n";
	$email .= "position 		= " . $_POST[$prefix . "_position "		] . "\n";
	$email .= "primary_name 	= " . $_POST[$prefix . "_address1"	] . "\n";
	$email .= "secondary_name 	= " . $_POST[$prefix . "_address2"	] . "\n";
	$email .= "street_name 		= " . $_POST[$prefix . "_address3"	] . "\n";
	$email .= "town 		= " . $_POST[$prefix . "_town"		] . "\n";
	$email .= "administrative_area	= " . $_POST[$prefix . "_area"	] . "\n";
	$email .= "countryid 		= " . $_POST[$prefix . "_country"	] . "\n";
	$email .= "postcode 		= " . $_POST[$prefix . "_postcode"	] . "\n";

	return($email);
}
function save_add_producer()
{

	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");

	$producer_sql = producer_post_to_sql();
	 $emergency_contact_sql = contact_details_post_to_sql("emergencycontact");
	
	query("insert into contact_details set $emergency_contact_sql");
  $emergency_contact_id=last_id();
	
	$enforcement_contact_sql = contact_details_post_to_sql("enforcement");

	query("insert into contact_details set $enforcement_contact_sql");

	$enforcement_contact_id = last_id();

	if($_POST["copy_contact_details"] == "on")

		$daytoday_contact_id = $enforcement_contact_id;

	else
	{
		$daytoday_contact_sql = contact_details_post_to_sql("daytoday");

		query("insert into contact_details set $daytoday_contact_sql");

		$daytoday_contact_id = last_id();
	} 
 
	$sql = "insert into producer set $producer_sql, 
					daytoday_contact_detailsid = $daytoday_contact_id, 
					enforcement_contact_detailsid = $enforcement_contact_id,
					 emergency_contact_detailsid= $emergency_contact_id";


	query($sql);

	$producerid = last_id();
	

	/*if(!empty($_FILES["produceridentificationmark"]) && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
	{

		query("insert into producer_identification_mark set producerid = $producerid");
		$imageid = last_id(); 

		//move_uploaded_file($_FILES["produceridentificationmark"]["tmp_name"], LOGO_PATH . "/$imageid.jpeg");
		resizePhoto(150, 200, "produceridentificationmark", LOGO_PATH . "/$imageid.jpeg");
	}
*/
if(!empty($_FILES["produceridentificationmark"]) && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
    {
        $rs = query("select id,image_name,letter from producer_identification_mark where producerid = $producerid order by id desc ");
        $row = fetch_row($rs);
           
        $charno = (num_rows($rs)==0)?65:($row[2]+1);
        $imagename = $old_producer_details["organisationname"]." logo".chr($charno);
         
        query("insert into producer_identification_mark (producerid,image_name,letter) values($producerid,'{$imagename}',$charno)");
       
       
        //$imageid = last_id();

    move_uploaded_file($_FILES["produceridentificationmark"]["tmp_name"], LOGO_PATH . "/$imagename.jpeg");
           
       resizePhoto(150, 200, "produceridentificationmark", LOGO_PATH . "/$imagename.jpeg");
    }

	if($_POST["producertype"] == "company")
		add_company($producerid);
	else
		add_partners($producerid);

	query("insert ignore into year set year=\"$year\"");

	query("insert into producer_year (producerid, yearid) select $producerid, id from year where year=\"$year\"");

}

function check_unique_companynumber($number, $id = false)
{
	$query = "select count(*) from company where number=\"$number\" ";

	if($id)
		$query .= " and producerid != $id";

	$result = query($query);

	$row = fetch_row($result);

	if($row[0] > 0)
		return(false);
	else
		return(true);
}

function check_unique_name($name, $id = false)
{
	$query = "select count(*) from producer where name=\"$name\"";

	if($id)
		$query .= " and id != $id";

	$result = query($query);

	$row = fetch_row($result);

	if($row[0] > 0)
		return(false);
	else
		return(true);
}

function check_unique_registration_number($registration_number, $id = false)
{
	$query = "select count(*) from producer where registration_number = \"$registration_number\"";

	if($id)
		$query .= " and id != $id";

	$result = query($query);

	$row = fetch_row($result);

	if($row[0] > 0)
		return(false);
	else
		return(true);
}



function get_finance($id)
{

	//echo "<!-- ".$year." -->";



	$result = query("select * 

			from crm_finance  where producer_id=$id");

	if(num_rows($result) != 1)
		return(false);

	$row = fetch_row($result);

	 $producer["id"					] = $id;
	$producer['fid'] =$row[0];
		$producer['pid'] =$row[1];
	$producer["department"			] = $row[3];
	$producer["b2c_obligation"			] = $row[4];
	
	$producer["purchase_order_management_fee"				] = $row[5];
	$producer["purchase_order_ea_fee"			] = $row[6];
	$producer["management_type"			] = $row[7];
	$producer["management_band"					] = $row[8];
	$producer["management_returns_m_fee"					] = $row[9];
	$producer["annual_turnover"				] = $row[10];
	$producer["management_returns_ea_fee"			] = $row[11];
	$producer["navision_code"	 	] = $row[12];
	$producer["pcs"	 	] = $row[13];
    $producer["financecontactid"] =$row[2];
 
	
$producer["financecontactdetails"] = get_contact_details($row[2]);

	
	
	return($producer);
	
	}

	

function get_producer($id, $year = false)
{

	//echo "<!-- ".$year." -->";

	if(!$year) {
		$year = $_SESSION["year"];

		if($year == date('Y') && date('n') == 1) {
		
		//if(date('n') == 1) {
			$year--;
		}
	}
	//echo "<!-- ".$year." -->";

	$result = query("select name, registration_number, trading_name, vat_registered, annual_turnover, 
				obligation_type_b2b, obligation_type_b2c, sic_code, daytoday_contact_detailsid,
				enforcement_contact_detailsid,emergency_contact_detailsid, password,crm_status,tp_status,sp_status,dm_status ,weee_status,northern_status,
				producer_year.return_type 

			from producer, producer_year, year where producer_year.producerid = producer.id and year.id = producer_year.yearid and year.year = $year and producer.id=$id ");

	if(num_rows($result) != 1)
		return(false);

	$row = fetch_row($result);

	$producer["id"					] = $id;
	$producer["organisationname"			] = $row[0];
	$producer["registrationnumber"			] = $row[1];
	$producer["tradingname"				] = $row[2];
	$producer["vatregistered"			] = $row[3];
	$producer["annualturnover"			] = $row[4];
	$producer["b2b"					] = $row[5];
	$producer["b2c"					] = $row[6];
	$producer["siccode"				] = $row[7];
	$producer["daytodaycontactid"			] = $row[8];
	$producer["enforcementcontactid"	 	] = $row[9];
	$producer["emergencycontactid"	 	] = $row[10];
	$producer["password"			 	] = $row[11];
		$producer["quarterly_monthly"			] = $row[17];
	$producer["crm_status"	 	] = $row[12];
	$producer["tp_status"			 	] = $row[13];
	$producer["sp_status"			] = $row[14];
	$producer["weee_status"			 	] = $row[15];
	$producer["northern_status"			] = $row[16];
	$producer["dm_status"			] = $row[18];
	if(strlen($row[1]) > 0)
		$producer["status"] = "A";
	else
		$producer["status"] = "I";

	if($row[8] == $row[9])
		$producer["copy_contact_details"] = true;
	else
		$producer["copy_contact_details"] = false;
		
	if($row[9] == $row[10])
		$producer["copy_contact_details_em"] = true;
	else
		$producer["copy_contact_details_em"] = false;

	$producer["daytodaycontactdetails"] = get_contact_details($row[8]);
   $producer["targetdetails"]=get_member_details("T",$id);
 
	$producer["salesdetails"]=get_member_details("S",$id);
	$producer["enforcementcontactdetails"] = get_contact_details($row[9]);
		$producer["emergencycontactdetails"] = get_contact_details($row[10]);

	$result = query("select number from company where producerid = $id");

	if(num_rows($result) == 1)
	{
		$row = fetch_row($result);
		$producer["producertype"] = "company";
		$producer["companynumber"] = $row[0];
	}
	else
	{

		$result = query("select id from partnership where producerid = $id");

		if(num_rows($result) == 1)
		{
			$row = fetch_row($result);
			
			$producer["producertype"] = "partnership";

			$result = query("select partner from partnership_list where partnershipid = " . $row[0]);

			while($row = fetch_row($result))
			{
				if($producer["partners"] != "")
				$producer["partners"] .= ", ";
				$producer["partners"] .= $row[0];
			}
		}
	}

	$result = query("select id,image_name from producer_identification_mark where producerid = $id");

	while($row = fetch_row($result))
	{
		$producer["identificationmarks"][$row[0]]["id"] = $row[0];
		$producer["identificationmarks"][$row[0]]["filename"] = $row[1] . ".jpeg";
	}

	return($producer);
}

function get_member_details($status,$producerid)
{

	$result = query("select producer_id,	noticestatus, 
				forename, 
				current_pcs, 
				account_manager, 
				last_contact, 
				next_contact, 
				packaging, 
				battery,
				tp_date,
				sp_date,
				notes,
				note_tp_date,
				note_sp_date,status
			
	
				
			from 	crm_target_pipeline

			where 	producer_id =$producerid and member_status='$status'"

				);

	if(!$result || num_rows($result) != 1)
		return(false);

	$row = fetch_row($result);
     $r["producer_id"		] = $row[0];
	 $r["noticestatus"		] = $row[1];

	$r["forename"		] = $row[2];
	$r["current_pcs"		] = $row[3];
	$r["account_manager"		] = $row[4];
	$r["last_contact"		] = $row[5];
	$r["next_contact"		] = $row[6];
	$r["packaging"		] = $row[7];
	$r["battery"		] = $row[8];
		$r["tp_date"		] = $row[9];
	$r["sp_date"		] = $row[10];
	$r["notes"		] = $row[11];
	$r["note_tp_date"		] = $row[12];
	$r["note_sp_date"		] = $row[13];
	$r["status"		] = $row[14];
	
	
	return($r);
}
function get_contact_details($id)
{
	$result = query("select	title, 
				forename, 
				surname, 
				phone, 
				mobile, 
				fax, 
				email, 
				position,
				primary_name, 
				secondary_name, 
				street_name, 
				town, 
				post_town, 
				locality, 
				administrative_area, 
				country, 
				postcode 

			from 	contact_details, country 

			where 	country.id = contact_details.countryid

				and contact_details.id = $id");

	if(!$result || num_rows($result) != 1)
		return(false);

	$row = fetch_row($result);

	$r["title"		] = $row[0];
	$r["forename"		] = $row[1];
	$r["surname"		] = $row[2];
	$r["landline"		] = $row[3];
	$r["mobile"		] = $row[4];
	$r["fax"		] = $row[5];
	$r["email"		] = $row[6];
	$r["position"		] = $row[7];
	$r["primaryname"	] = $row[8];
	$r["secondaryname"	] = $row[9];
	$r["streetname"		] = $row[10];
	$r["town"		] = $row[11];
	$r["posttown"		] = $row[12];
	$r["locality"		] = $row[13];
	$r["adminarea"		] = $row[14];
	$r["country"		] = $row[15];
	$r["postcode"		] = $row[16];

	return($r);
}

function image_belongs_to_producer($producerid, $imageid)
{
	$result = query("select count(*) from producer_identification_mark where producerid = $producerid and id = $imageid");

	$row = fetch_row($result);

	if($row[0] == 1)
		return(true);
	else
		return(false);
}

function producer_post_to_sql()
{
	global $_POST;

	if($_POST["registrationnumber"] != "")
		$sql .= " registration_number  = \"" .  _addslashes(strtoupper($_POST["registrationnumber"	])) . "\",";
	else
		$sql .=" registration_number = NULL, ";

	$sql .= " name 	 	= \"" .  _addslashes($_POST["organisationname"	]) . "\"";
	$sql .= ", trading_name  	= \"" .  _addslashes($_POST["tradingname"		]) . "\"";
	$sql .= ", sic_code  		= \"" .  _addslashes($_POST["siccode"			]) . "\"";
	$sql .= ", annual_turnover  	= \"" .  _addslashes($_POST["annualturnover"		]) . "\"";
	$sql .= ", password  		= \"" .  _addslashes($_POST["password"			]) . "\"";


	if($_POST["vatregistered"] == "on")
		$sql .= ", vat_registered=1";
	else
		$sql .= ", vat_registered=0";

	if($_POST["b2b"] == "on")
		$sql .= ", obligation_type_b2b=1";
	else
		$sql .= ", obligation_type_b2b=0";

	if($_POST["b2c"] == "on")
		$sql .= ", obligation_type_b2c=1";
	else
		$sql .= ", obligation_type_b2c=0";

	return($sql);
}
function delete_partners($producer_id)
{
	$result = query("select id from partnership where producerid = $producer_id");

	if(num_rows($result) > 0)
	{
		$row = fetch_row($result);
		query("delete from partnership_list where partnershipid = " . $row[0]);
	}

	query("delete from  partnership where producerid = $producer_id");
}

function update_partners($producer_id)
{
	global $_POST;

	query("insert ignore into partnership set producerid = $producer_id");

	$result = query("select id from partnership where producerid = $producer_id");

	if(num_rows($result) > 0)
	{
		$row = fetch_row($result);
		query("delete from partnership_list where partnershipid = " . $row[0]);
	}

	$exploded = explode(",", _addslashes($_POST["partners"]));

	foreach($exploded as $explodee)
	{
		if(!empty($explodee))
		{
			$explodee = _addslashes(ltrim($explodee));

			query(	"insert into partnership_list (partner, partnershipid) " .
				"select \"$explodee\", id from partnership where producerid = $producer_id");
		}
	}
}

function add_partners($producer_id)
{
	global $_POST;

	query("insert ignore into partnership set producerid = $producer_id");

	$exploded = explode(",", _addslashes($_POST["partners"]));

	foreach($exploded as $explodee)
	{
		if(!empty($explodee))
		{
			$explodee = _addslashes(ltrim($explodee));

			query(	"insert into partnership_list (partner, partnershipid) " .
				"select \"$explodee\", id from partnership where producerid = $producer_id");
		}
	}
}

function delete_company($producer_id)
{
	global $_POST;

	query("delete from company where producerid = $producer_id"); 
}

function update_company($producer_id)
{
	global $_POST;

	$companynumber = _addslashes($_POST["companynumber"]);

	query("update company set number =\"$companynumber\" where producerid = $producer_id"); 
}

function add_company($producer_id)
{
	global $_POST;

	$companynumber = _addslashes($_POST["companynumber"]);

	query("insert into company set number = \"$companynumber\", producerid = $producer_id"); 
}

function producer_email($recipient)
{
		//$sel_pro=query("select tp_status,sp_status from producer where id=$producerid");
//$fet_pro=fetch_row($sel_pro);

	$boundry = md5(time());

	$headers = "From: " . SCHEME_EMAIL_FROM . "\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$boundry\"";

	$email = "Multipart Mime Message\n";
	$email .= "--$boundry\n";
	$email .= "Content-Type: text/plain; charset=\"utf-8\"\n";
	$email .= "Content-Transfer-Encoding: 7bit\n";
	$email .= "\n";
	$email .= producer_post_to_email();
	$email .= contact_details_post_to_email("enforcement");
	

	if($_POST["copy_contact_details"] == "on")
		$email .= "enforcement details are the same as the daytoday details";
	else
		$email .= contact_details_post_to_email("daytoday");
$email .= contact_details_post_to_email("emergencycontact");
	$image = producer_post_to_email_images();

	if($image)
	{
		$email .= "A new logo is attached";
		$email .= "\n\n--$boundry\n";
		$email .= $image;
	}
    // if($fet_pro[1]==1)
    $email .= stp_details_post_to_email("sales");
//if($fet_pro[0]==1)
	$email .= stp_details_post_to_email("target");
	$email .= "\n\n--$boundry";

	return(mail('chitti.girija@gmail.com',"Producer details change request", $email, $headers));
}
function stp_details_post_to_email($prefix)
{
global $_POST;

$email .= "Status = " .  $_POST[$prefix . "_status" ] . "\n";

$email .= "Notice Status = " .  $_POST[$prefix . "_noticestatus" ] . "\n";
$email .= "Forename   = " .  $_POST[$prefix . "_forename" ] . "\n";
$email .= "Current PCS = " . $_POST[$prefix . "_currentpcs" ] . "\n";
$email .= "Account Manager   = " .  $_POST[$prefix . "_amanager" ] . "\n";
$email .= "Last contact = " .  $_POST[$prefix . "_lcdate" ] . "\n";
    $email .= "Next contact = " .  $_POST[$prefix . "_ncdate" ] . "\n";
$email .= "Packaging = " .  $_POST[$prefix . "_package" ] . "\n";
    $email .= "Battery = " .  $_POST[$prefix . "_battery" ] . "\n";
$email .= "Notes = " .  $_POST[$prefix . "__notes" ] . "\n";

return($email);
}
function producer_post_to_email_images()
{
	if($_FILES["produceridentificationmark"]["tmp_name"] != '' && $_FILES["produceridentificationmark"]["type"] == "image/jpeg")
	{
		resizePhoto(150, 200, "produceridentificationmark", "/tmp/newimage.jpeg");

		$email .= "Content-Type: image/jpg\n";
		$email .= "Content-Disposition: attachment; filename=\"new_producer_logo.jpeg\"\n";
		$email .= "Content-Transfer-Encoding: base64";
		$email .= "\n\n";
		$email .= base64_encode(file_get_contents("/tmp/newimage.jpeg"));

		unlink("/tmp/newimage.jpeg");

		return($email);
	}
	else
		return("");
}

function producer_post_to_email()
{
	global $_POST;

	$email .= "name 		= " .  $_POST["organisationname"	] . "\n";
	$email .= "trading_name  	= " .  $_POST["tradingname"		] . "\n";
	$email .= "sic_code  		= " .  $_POST["siccode"			] . "\n";
	$email .= "annual_turnover  	= " .  $_POST["annualturnover"		] . "\n";
	$email .= "password  		= " .  $_POST["password"		] . "\n";

	if($_POST["vatregistered"] == "on")
		$email .= "Vat Register  		= "."Vat registered\n";
	else
		$email .= "password  		= ". "Not vat registered\n";

	if($_POST["b2b"] == "on")
		$email .="B2B 		= ". "B2B Return\n";

	if($_POST["b2c"] == "on")
		$email .= "B2C		= "."B2C Return\n";

	if($_POST["producertype"] == "company")
		$email .= "Company Number: " . $_POST["companynumber"] . "\n";
	else
		$email .= "Partners: " . $_POST["partners"] . "\n";

	$email .= "Additional Information: " . $_POST["additional"] . "\n";

	return($email);
}

function contact_details_post_to_email($prefix)
{
	global $_POST;

	$email =  "title 		= " . $_POST[$prefix . "_title"		] . "\n";
	$email .= "forename 		= " . $_POST[$prefix . "_forename"	] . "\n";
	$email .= "surname 		= " . $_POST[$prefix . "_surname"	] . "\n";
	$email .= "phone 		= " . $_POST[$prefix . "_landline"	] . "\n";
	$email .= "mobile 		= " . $_POST[$prefix . "_mobile"	] . "\n";
	$email .= "fax			= " . $_POST[$prefix . "_fax"		] . "\n";
	$email .= "email 		= " . $_POST[$prefix . "_email"		] . "\n";
	$email .= "position 	= " . $_POST[$prefix . "_position"	] . "\n";
	$email .= "Address1 	= " . $_POST[$prefix . "_address1"	] . "\n";
	$email .= "Address2 	= " . $_POST[$prefix . "_address2"	] . "\n";
	$email .= "Adress3		= " . $_POST[$prefix . "_address3"	] . "\n";
	$email .= "town 		= " . $_POST[$prefix . "_town"		] . "\n";
	$email .= "administrative_area	= " . $_POST[$prefix . "_area"	] . "\n";
	$email .= "countryid 		= " . $_POST[$prefix . "_country"	] . "\n";
	$email .= "postcode 		= " . $_POST[$prefix . "_postcode"	] . "\n";

	return($email);
}



function contact_details_post_to_sql($prefix)
{
	global $_POST;

	$sql =  "  title 		= \"" . _addslashes($_POST[$prefix . "_title"			]) . "\"";
	$sql .= ", forename 		= \"" . _addslashes($_POST[$prefix . "_forename"		]) . "\"";
	$sql .= ", surname 		= \"" . _addslashes($_POST[$prefix . "_surname"			]) . "\"";
	$sql .= ", phone 		= \"" . _addslashes($_POST[$prefix . "_landline"		]) . "\"";
	$sql .= ", mobile 		= \"" . _addslashes($_POST[$prefix . "_mobile"			]) . "\"";
	$sql .= ", fax 			= \"" . _addslashes($_POST[$prefix . "_fax"			]) . "\"";
	$sql .= ", email 		= \"" . _addslashes($_POST[$prefix . "_email"			]) . "\"";
	$sql .= ", position 		= \"" . _addslashes($_POST[$prefix . "_position"			]) . "\"";
	$sql .= ", primary_name 	= \"" . _addslashes($_POST[$prefix . "_primaryname"		]) . "\"";
	$sql .= ", secondary_name 	= \"" . _addslashes($_POST[$prefix . "_secondaryname"		]) . "\"";
	$sql .= ", street_name 		= \"" . _addslashes($_POST[$prefix . "_streetname"		]) . "\"";
	$sql .= ", town 		= \"" . _addslashes($_POST[$prefix . "_town"			]) . "\"";
	$sql .= ", post_town 		= \"" . _addslashes($_POST[$prefix . "_posttown"		]) . "\"";
	$sql .= ", locality 		= \"" . _addslashes($_POST[$prefix . "_locality"		]) . "\"";
	$sql .= ", administrative_area	= \"" . _addslashes($_POST[$prefix . "_adminarea"		]) . "\"";
	$sql .= ", countryid 		= " . get_country_id(_addslashes($_POST[$prefix . "_country"]));
	$sql .= ", postcode 		= \"" . _addslashes($_POST[$prefix . "_postcode"		]) . "\"";


	return($sql);
}


function crm_form_errors($public=false, $email = false)
{
	global $_POST;
	$errors = false;

// ******************************************************************* Registration Number 

	if(!empty($_POST["registrationnumber"]))
		if(!ereg("^WEE/[A-Z]{2}[0-9]{4}[A-Z]{2}$", strtoupper($_POST["registrationnumber"])))
			$errors[] = "Registration Number is formatted incorrectly";

// ******************************************************************* Organisation Name 

	if(empty($_POST["organisationname"]) && !$email)
		$errors[] = "Organisation Name must be filled in";
	elseif(strlen($_POST["organisationname"]) > 255 )
		$errors[] = "Organsiation Name is too long";

// ******************************************************************* Organisation Name 

	if(strlen($_POST["tradingname"]) > 255 )
		$errors[] = "Trading Name is too long";

// ******************************************************************* SIC Code 

	if(strlen($_POST["siccode"]) > 8 )
		$errors[] = "SIC code is too long";
	elseif(empty($_POST["siccode"]))
		$errors[] = "SIC code must be set";

// ******************************************************************* Annual Turnover

	if(empty($_POST["annualturnover"]) && !$email)
		$errors[] = "Annual Turnover must be filled in";

	elseif(!is_numeric($_POST["annualturnover"]))
		$errors[] = "Annual Turnover must be a number";

// ******************************************************************* Password

	if((empty($_POST["password"]) || strlen($_POST["password"]) < 8) && !$public)
		$errors[] = "Password must have more than 8 characters";

	if(strlen($_POST["password"]) > 250)
		$errors[] = "Password must have fewer than 250 characters";

// ******************************************************************* Producer Type
	
	if(empty($_POST["producertype"]) && !$email)
		$errors[] = "Producer Type must be filled in";
	elseif($_POST["producertype"] != "company" && $_POST["producertype"] != "partnership" && !$email)
		$errors[] = "Producer Type must be either Company or Partnership";

// ******************************************************************* Partnership List

	if($_POST["producertype"] == "company")
	{
		if(!empty($_POST["companynumber"]) && strlen($_POST["companynumber"]) > 8 )
			$errors[] = "Company number is too long";
	}

	if($_POST["producertype"] == "partnership")
	{
		if(empty($_POST["partners"]) || strlen($_POST["partners"]) < 1 )
			$errors[] = "At least one partner must be listed";
	}

	contact_details_errors("enforcement", "Enforcement", $errors, $public);
	
	if($_POST["copy_contact_details"] != "on")
		contact_details_errors("daytoday", "Day to Day", $errors, $public);
	else
	{
		$_POST['daytoday_title'] = $_POST['enforcement_title'];
		$_POST['daytoday_forename'] = $_POST['enforcement_forename'];
	    $_POST['daytoday_surname'] = $_POST['enforcement_surname'];
		$_POST['daytoday_landline'] = $_POST['enforcement_landline'];
		$_POST['daytoday_mobile'] = $_POST['enforcement_mobile'];
		$_POST['daytoday_fax'] = $_POST['enforcement_fax'];
		$_POST['daytoday_email'] = $_POST['enforcement_email'];
		$_POST['daytoday_position'] = $_POST['enforcement_position'];
		$_POST['daytoday_address1'] = $_POST['enforcement_address1'];
		$_POST['daytoday_address2'] = $_POST['enforcement_address2'];
		$_POST['daytoday_address3'] = $_POST['enforcement_address3'];
		$_POST['daytoday_town'] = $_POST['enforcement_town'];
		$_POST['daytoday_area'] = $_POST['enforcement_area'];
		$_POST['daytoday_postcode'] = $_POST['enforcement_postcode'];
		$_POST['daytoday_country'] = $_POST['enforcement_country'];	
	}
	
if($_POST["copy_contact_details_em"]!= "on")
contact_details_errors("emergency", "Emergency", $errors, $public);
else
	{
		$_POST['emergency_title'] = $_POST['enforcement_title'];
		$_POST['emergency_forename'] = $_POST['enforcement_forename'];
	    $_POST['emergency_surname'] = $_POST['enforcement_surname'];
		$_POST['emergency_landline'] = $_POST['enforcement_landline'];
		$_POST['emergency_mobile'] = $_POST['enforcement_mobile'];
		$_POST['emergency_fax'] = $_POST['enforcement_fax'];
		$_POST['emergency_email'] = $_POST['enforcement_email'];
		$_POST['emergency_position'] = $_POST['enforcement_position'];
		$_POST['emergency_address1'] = $_POST['enforcement_address1'];
		$_POST['emergency_address2'] = $_POST['enforcement_address2'];
		$_POST['emergency_address3'] = $_POST['enforcement_address3'];
		$_POST['emergency_town'] = $_POST['enforcement_town'];
		$_POST['emergency_area'] = $_POST['enforcement_area'];
		$_POST['emergency_postcode'] = $_POST['enforcement_postcode'];
		$_POST['emergency_country'] = $_POST['enforcement_country'];	
	}
    
	if($_FILES["produceridentificationmark"]["name"] != '' && $_FILES["produceridentificationmark"]["type"] != "image/jpeg")
		$errors[] = "Producer Logo must be a jpeg";
			
   	if(	$_POST["part_one_status"] != "Part One Not Issued" && 
		$_POST["part_one_status"] != "Part One Issued" && 
		$_POST["part_one_status"] != "Part One Received")

		$errors[] = "Part One Status was not valid";

	if(	$_POST["part_two_status"] != "Part Two Not Issued" && 
		$_POST["part_two_status"] != "Part Two Issued" && 
		$_POST["part_two_status"] != "Part Two Received")

		$errors[] = "Part Two Status was not valid";
//if(	$_POST["purchase_mfee"])

	//if($_POST["audit_date"]=="" && !is_date_sane($_POST["audit_date"]))
		//$errors[] = "Audit Date should be in the format dd/mm/yyyy and be an existing date";

if($_POST["copy_contact_details_f"] != "on")	
     contact_details_errors("finance", "Finance", $errors, $public);
else
	{
		$_POST['finance_title'] = $_POST['enforcement_title'];
		$_POST['finance_forename'] = $_POST['enforcement_forename'];
	    $_POST['finance_surname'] = $_POST['enforcement_surname'];
		$_POST['finance_landline'] = $_POST['enforcement_landline'];
		$_POST['finance_mobile'] = $_POST['enforcement_mobile'];
		$_POST['finance_fax'] = $_POST['enforcement_fax'];
		$_POST['finance_email'] = $_POST['enforcement_email'];
		$_POST['finance_position'] = $_POST['enforcement_position'];
		$_POST['finance_address1'] = $_POST['enforcement_address1'];
		$_POST['finance_address2'] = $_POST['enforcement_address2'];
		$_POST['finance_address3'] = $_POST['enforcement_address3'];
		$_POST['finance_town'] = $_POST['enforcement_town'];
		$_POST['finance_area'] = $_POST['enforcement_area'];
		$_POST['finance_postcode'] = $_POST['enforcement_postcode'];
		$_POST['finance_country'] = $_POST['enforcement_country'];	
	}
//if(!empty($_POST["pcs"]))   
//if(!ereg("{a-z}{A-Z}{1-9}", $_POST["pcs"]))
	//	$errors[] = "PCS  is formatted incorrectly";

if(($_POST["tpipeline"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["apipeline"]=="on") or ($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with Target pipeline ";
}
if(($_POST["diminimus"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["apipeline"]=="on") or ($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with diminimus pipeline ";
}
if(($_POST["apipeline"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with Sales pipeline ";
}
if($_POST["dcrm"]=="on")
{
if(($_POST["weee"]=="on") or ($_POST["northern"]=="on") or ($_POST["tpipeline"]=="on") or ($_POST["apipeline"]=="on") or ($_POST["diminimus"]=="on"))
$errors[] = "you dont have permission to Upgrade and Downgrade at the same time";
}		

	 
	return($errors);

}

function producer_form_errors($public=false, $email = false)
{
	global $_POST;
	$errors = false;

// ******************************************************************* Registration Number 

	if(!empty($_POST["registrationnumber"]))
		if(!ereg("^WEE/[A-Z]{2}[0-9]{4}[A-Z]{2}$", $_POST["registrationnumber"]))
			$errors[] = "Registration Number is formatted incorrectly";

// ******************************************************************* Organisation Name 

	if(empty($_POST["organisationname"]) && !$email)
		$errors[] = "Organisation Name must be filled in";
	elseif(strlen($_POST["organisationname"]) > 255 )
		$errors[] = "Organsiation Name is too long";

// ******************************************************************* Organisation Name 

	if(strlen($_POST["tradingname"]) > 255 )
		$errors[] = "Trading Name is too long";

// ******************************************************************* SIC Code 

	if(strlen($_POST["siccode"]) > 8 )
		$errors[] = "SIC code is too long";
	elseif(empty($_POST["siccode"]))
		$errors[] = "SIC code must be set";

// ******************************************************************* Annual Turnover

	if(empty($_POST["annualturnover"]) && !$email)
		$errors[] = "Annual Turnover must be filled in";

	elseif(!is_numeric($_POST["annualturnover"]))
		$errors[] = "Annual Turnover must be a number";

// ******************************************************************* Password

	if((empty($_POST["password"]) || strlen($_POST["password"]) < 8) && !$public)
		$errors[] = "Password must have more than 8 characters";

	if(strlen($_POST["password"]) > 250)
		$errors[] = "Password must have fewer than 250 characters";

// ******************************************************************* Producer Type
	
	if(empty($_POST["producertype"]) && !$email)
		$errors[] = "Producer Type must be filled in";
	elseif($_POST["producertype"] != "company" && $_POST["producertype"] != "partnership" && !$email)
		$errors[] = "Producer Type must be either Company or Partnership";

// ******************************************************************* Partnership List

	if($_POST["producertype"] == "company")
	{
		if(!empty($_POST["companynumber"]) && strlen($_POST["companynumber"]) > 8 )
			$errors[] = "Company number is too long";
	}

	if($_POST["producertype"] == "partnership")
	{
		if(empty($_POST["partners"]) || strlen($_POST["partners"]) < 1 )
			$errors[] = "At least one partner must be listed";
	}

	contact_details_errors("enforcement", "Enforcement", $errors, $public);
	
	if($_POST["copy_contact_details"] != "on")
		contact_details_errors("daytoday", "Day to Day", $errors, $public);
		
    if($_POST["copy_contact_details_em"] != "on")
	contact_details_errors("emergency", "Emergency Contact", $errors, $public);

	if($_FILES["produceridentificationmark"]["name"] != '' && $_FILES["produceridentificationmark"]["type"] != "image/jpeg")
		$errors[] = "Producer Logo must be a jpeg";
		
if(($_POST["tpipeline"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["apipeline"]=="on") or ($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with Target pipeline ";
}
if(($_POST["diminimus"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["apipeline"]=="on") or ($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with diminimus pipeline ";
}
if(($_POST["apipeline"]=="on") and ($_POST["dcrm"]!="on"))
{
if(($_POST["weee"]=="on") or ($_POST["northern"]=="on"))
$errors[] = "you dont have permission to adding two or more members with Sales pipeline ";
}
if($_POST["dcrm"]=="on")
{
if(($_POST["weee"]=="on") or ($_POST["northern"]=="on") or ($_POST["tpipeline"]=="on") or ($_POST["apipeline"]=="on") or ($_POST["diminimus"]=="on"))
$errors[] = "you dont have permission to Upgrade and Downgrade at the same time";
}	
	
	return($errors);

}

function contact_details_errors($prefix, $msg, &$errors, $public = false)
{
// ******************************************************************* Title 

	if(empty($_POST[$prefix . "_title"]))
		$errors[] = "$msg Title must be filled in";
	elseif(strlen($_POST[$prefix . "_title"]) > 35)
		$errors[] = "$msg Title is too long";

// ******************************************************************* Forename 

	if(empty($_POST[$prefix . "_forename"]))
		$errors[] = "$msg Forename must be filled in";
	elseif(strlen($_POST[$prefix . "_forename"]) > 35)
		$errors[] = "$msg Forename is too long";

// ******************************************************************* Surname 

	if(empty($_POST[$prefix . "_surname"]))
		$errors[] = "$msg Surname must be filled in";
	elseif(strlen($_POST[$prefix . "_surname"]) > 35)
		$errors[] = "$msg Surame is too long";
		
		// ******************************************************************* Surname 
if($prefix=="finance")
{
	if(empty($_POST[$prefix . "_department"]))
		$errors[] = "$msg department must be filled in";
	} 

// ******************************************************************* Landline 

	if(empty($_POST[$prefix . "_landline"]))
		$errors[] = "$msg Landline must be filled in";
	elseif(!ereg("[0-9]{10,35}", $_POST[$prefix . "_landline"]))
		$errors[] = "$msg Landline contains invalid characters";

// ******************************************************************* Mobile 

	if(!empty($_POST[$prefix . "_mobile"]) && !ereg("[0-9]{10,35}", $_POST[$prefix . "_mobile"]))
		$errors[] = "$msg Mobile contains invalid characters. There must be at least 10 numbers and no letters or spaces.";

// ******************************************************************* Fax 

	if(!empty($_POST[$prefix . "_fax"]) && !ereg("[0-9]{10,35}", $_POST[$prefix . "_fax"]))
		$errors[] = "$msg Fax contains invalid characters. There must be at least 10 numbers and no letters or spaces.";

// ******************************************************************* Email 

	if(empty($_POST[$prefix . "_email"]))
		$errors[] = "$msg Email must be filled in";
	elseif(strlen($_POST[$prefix . "_email"]) > 255)
		$errors[] = "$msg Email is too long";

	elseif(!empty($_POST[$prefix . "_email"]) && !ereg("[a-zA-Z0-9_%-]+(\.[a-zA-Z0-9_%-]+)*@[a-zA-Z0-9_%-]+(\.[a-zA-Z0-9_%-]+)*\.[a-zA-Z]{2,4}", $_POST[$prefix . "_email"]))
		$errors[] = "$msg Email contains invalid characters";

// ******************************************************************* Primary Name (address) 

	if(empty($_POST[$prefix . "_address1"]))
		$errors[] = "$msg Address Line 1 must be filled in";
	elseif(strlen($_POST[$prefix . "_address1"]) > 500)
		$errors[] = "$msg Address Line 1 is too long";

	if(strlen($_POST[$prefix . "_address2"]) > 100)
		$errors[] = "$msg Address Line 2 is too long;";

	if(strlen($_POST[$prefix . "_address3"]) > 100)
		$errors[] = "$msg Address Line 3 is too long";

	if(strlen($_POST[$prefix . "_town"]) > 30)
		$errors[] = "$msg Town is too long";

	if(strlen($_POST[$prefix . "_area"]) > 35)
		$errors[] = "$msg Locality is too long";
	
if($_POST[$prefix . "_country"] == ENGLAND)
		{
    if(empty($_POST[$prefix . "_postcode"]))
		$errors[] = "$msg postcode must be set";
	elseif(!ereg("[A-Z]{1,2}[0-9R][0-9A-Z]? [0-9][A-Z]{2}", $_POST[$prefix . "_postcode"]))
		$errors[] = "$msg postcode is not valid";
		else if($_POST[$prefix . "_country"] != ENGLAND)
		{
		    if(empty($_POST[$prefix . "_postcode"]))
		$errors[] = "$msg postcodefgfdgf must be set";
	elseif(!ereg("^[a-zA-Z0-9]*$", $_POST[$prefix . "_postcode"]))
		$errors[] = "$msg postcode is not valid";
		elseif(strlen($_POST[$prefix . "_postcode"]) > 35)
		$errors[] = "$msg postcode is too long";
		}
        }
      	
		
   
		
	if(!get_country_id(_addslashes($_POST[$prefix . "_country"])))	
		$errors[] = "$msg country not in the approved list";

	
}

function get_country_id($country)
{
	$result = mysql_query("select id from country where country = \"$country\"");

	if(!$result)
		die("Error: Query error");

	if(num_rows($result) != 1)
		return(false);

	$row = fetch_row($result);

	return($row[0]);
}
function target_form($producerid, $action, $defaults = false, $public=false)
{
	global $_POST;


?><form method="post" enctype="multipart/form-data">
<?php if($action) { ?><input type="hidden" name="action" value="<?php echo $action; ?>"><?php } ?>
<input type="hidden" name="id" value="<?php echo $producerid;?>">
<?php if($action=='save_tp')
$name="Target Pipline";
else
$name="Sales Pipeline";
?>
<br /><span class="formtitle"><?php echo $public?"Your":$name; ?> Details</span><br /><br />
<?php

if($public)
{
?>
<input type="hidden" name="registrationnumber" value="<?php echo $producer["registrationnumber"]; ?>">
<input type="hidden" name="organisationname" value="<?php echo $producer["organisationname"]; ?>">
<input type="hidden" name="tradingname" value="<?php echo $producer["tradingname"]; ?>">
<input type="hidden" name="annualturnover" value="<?php echo $producer["annualturnover"]; ?>">
<input type="hidden" name="producertype" value="<?php echo $producer["producertype"]; ?>">

<input type="hidden" name="companynumber" value="<?php echo $producer["companynumber"]; ?>">

<?php
}

?>

<table style="width:800px;border: 0px solid #000000;font-size:12px; font-family:Arial, Helvetica, sans-serif;"  >
  <tr valign="top">
    <td width="781" height="303" align="center"><table width="73%" border="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
      
 
      <?php  if($action=='save_tp'){?>
            <tr>
        <td><?php regform_trs_details("target", $producer?$producer["targetdetails"]:false);
		?></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3"><b><b>Assign Level </b>:</b></td>
          </tr>
          <tr>
            <td width="34%" >3. Sales Pipeline</td>
            <td width="5%" align="center">:</td>
            <td width="61%"><input type="checkbox"  name="apipeline" id="apipeline"   /></td>
          </tr>
          <tr>
            <td >1. CRM Only</td>
            <td width="5%" align="center">:</td>
            <td><input type="checkbox"  name="dcrm" id="dcrm"  /></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td width="5%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <?php }if($action=='save_sp'){?>
            <tr>
        <td><?php regform_trs_details("sales", $producer?$producer["salesdetails"]:false);
		?></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3"><b><b>Assign Level </b>:</b></td>
          </tr>
            <tr>
            <td width="47%" align="left">5. Northern Compliance</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="northern" id="northern"  /></td>
          </tr>
          <tr>
            <td width="47%" align="left">4. WeeeLight</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="weee" id="weee"  /></td>
          </tr>
          <tr>
            <td >1. CRM Only</td>
            <td width="5%" align="center">:</td>
            <td><input type="checkbox" name="dcrm" id="dcrm"  /></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td width="5%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <?php}if($action=='save_dm'){?>
            <tr>
        <td><?php regform_trs_details("dimin", $producer?$producer["dimindetails"]:false);
		?></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3"><b><b>Assign Level </b>:</b></td>
          </tr>
            <tr>
            <td width="47%" align="left">5. Northern Compliance</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="northern" id="northern"  /></td>
          </tr>
          <tr>
            <td width="47%" align="left">4. WeeeLight</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="weee" id="weee"  /></td>
          </tr>
          <tr>
            <td >1. CRM Only</td>
            <td width="5%" align="center">:</td>
            <td><input type="checkbox" name="dcrm" id="dcrm"  /></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td width="5%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <?php } ?>
    </table></td>
    <td width="7">&nbsp;</td>
  </tr>
  <tr>
    <td height="31" colspan="2" align="center"><input type="submit" name="save_tp" id="save_tp" value="Save"  /></td>
  </tr>
</table>		
</form>
<?php
}
function register_form($producer = false, $action, $defaults = false, $public=false)
{
	global $_POST;

	if($producer)
	{
		if($producer["copy_contact_details"] == true)
			$contact_details_identical = true;
		else
			$contact_details_identical = false;
			
		if($producer["copy_contact_details_em"] == true)
			$contact_details_identical_em = true;
		else
			$contact_details_identical_em = false;

		if($producer["producertype"] == "partnership")
			$producer_type = "partnership";
		else
			$producer_type = "company";
	}
	else
	{
		if($_POST["copy_contact_details"] == "on")
			$contact_details_identical = true;
		else
			$contact_details_identical = false;
			
		if($_POST["copy_contact_details_em"] == "on")
			$contact_details_identical_em = true;
		else
			$contact_details_identical_em = false;
			
		if($_POST["copy_contact_details_f"] == "on")
			$contact_details_identical_f = true;
		else
			$contact_details_identical_f = false;


		if($_POST["producertype"] == "partnership")
			$producer_type = "partnership";
		else
			$producer_type = "company";
	}

?>


<form method="post" enctype="multipart/form-data" >
<?php if($action) { ?><input type="hidden" name="action" value="<?php echo $action; ?>"><?php } ?>
<?php if($producer) { ?><input type="hidden" name="id" value="<?php echo $producer["id"]; ?>"><?php 

}elseif(!empty($_POST["id"])){ ?><input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>"><?php } ?> 


<br /><span class="formtitle"><?php echo $public?"Your":"Producer"; ?> Details</span><br /><br />
<?php
if($public)
{
?>
<input type="hidden" name="registrationnumber" value="<?php echo $producer["registrationnumber"]; ?>">
<input type="hidden" name="organisationname" value="<?php echo $producer["organisationname"]; ?>">
<input type="hidden" name="tradingname" value="<?php echo $producer["tradingname"]; ?>">
<input type="hidden" name="annualturnover" value="<?php echo $producer["annualturnover"]; ?>">
<input type="hidden" name="producertype" value="<?php echo $producer["producertype"]; ?>">
<input type="hidden" name="companynumber" value="<?php echo $producer["companynumber"]; ?>">

<?php
}

?>
<table style="width:800px;border: 0px solid #000000;font-size:12px; font-family:Arial, Helvetica, sans-serif;"  >
  <tr valign="top">
    <td width="361" height="702"><table width="99%" border="0">
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3" bgcolor="#CCCCAA"><b>Part One/Two Info </b></td>
          </tr>
          <tr>
            <td colspan="3"><b>Producer Details :</b></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Registration Number</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="registrationnumber" width="100%" value="<?php echo $_POST["registrationnumber"]; ?>" id="registrationnumber" /></td>
          </tr>
          <tr>
            <td align="left">Organisation Name</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="organisationname" width="100%" id="organisationname" value="<?php echo $_POST["organisationname"]; ?>" /></td>
          </tr>
          <tr>
            <td align="left">Trading Name</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="tradingname" width="100%" id="tradingname" value="<?php echo $_POST["tradingname"]; ?>"/></td>
          </tr>
          <tr>
            <td align="left">SIC Code</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="siccode" width="100%" id="siccode" value="<?php echo $_POST["siccode"]; ?>" /></td>
          </tr>
          <tr>
            <td align="left">VAT Registration</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="vatregistration" id="vatregistration" <?php if($_POST["vatregistration"]) echo 'checked="checked"';  ?> onchange="
			var check = document.getElementById('vatregistration'); 
			if(!check.checked)
{
  document.getElementById('b2c_eafee').value='&#163;30';
 document.getElementById('b2c_eafee').readOnly = true;
   document.getElementById('purchase_eafee').value='&#163;30';
  document.getElementById('purchase_eafee').readOnly = true;
  document.getElementById('purchase_band').value='Below Vat';
  document.getElementById('purchase_band').readOnly = true;
  }
  else
  {
  if(document.getElementById('annualturnover').value <= 1000000)
        {
		var check = document.getElementById('vatregistration'); 
		if(check.checked)
		{
		document.getElementById('purchase_eafee').value='&#163;220';
		document.getElementById('b2c_eafee').value='&#163;220';
         document.getElementById('b2c_eafee').readOnly = true;
          document.getElementById('purchase_eafee').readOnly = true;
           document.getElementById('purchase_band').value='Less than 1 million pounds'; 
            document.getElementById('purchase_band').readOnly = true;
          
		}
		}
       else
	   {
	   	var check = document.getElementById('vatregistration'); 
		if(check.checked)
		{
	   document.getElementById('purchase_eafee').value='&#163;445';
	    document.getElementById('b2c_eafee').value='&#163;445';
         document.getElementById('purchase_eafee').readOnly = true;
         document.getElementById('b2c_eafee').readOnly = true;
          document.getElementById('purchase_band').value='More than 1 million pounds';
           document.getElementById('purchase_band').readOnly = true;
		}
	   }
  
  document.getElementById('b2c_eafee').readOnly=false;
  document.getElementById('purchase_eafee').readOnly=false;
  }

			"></td>
          </tr>
          <tr>
            <td align="left">Annual Turnover</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="annualturnover" width="100%" id="annualturnover" value="<?php echo $_POST["annualturnover"]; ?>" onchange="
      document.getElementById('b2c_annualturnover').value=document.getElementById('annualturnover').value;  
       if(document.getElementById('annualturnover').value <= 1000000)
        {
		var check = document.getElementById('vatregistration'); 
		if(check.checked)
		{
		document.getElementById('purchase_eafee').value='&#163;220';
		document.getElementById('b2c_eafee').value='&#163;220';
         document.getElementById('b2c_eafee').readOnly = true;
          document.getElementById('purchase_eafee').readOnly = true;
           document.getElementById('purchase_band').value='Less than 1 million pounds'; 
            document.getElementById('purchase_band').readOnly = true;
		}
		}
       else
	   {
	   	var check = document.getElementById('vatregistration'); 
		if(check.checked)
		{
	   document.getElementById('purchase_eafee').value='&#163;445';
	    document.getElementById('b2c_eafee').value='&#163;445';
         document.getElementById('b2c_eafee').readOnly = true;
          document.getElementById('purchase_eafee').readOnly = true;
          document.getElementById('purchase_band').value='More than 1 million pounds';
           document.getElementById('purchase_band').readOnly = true;
		}
	   }
        "/></td>
          </tr>
          <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		
			
			
<!-- Original:  ataxx@visto.com -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function getRandomNum(lbound, ubound) 
{
return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}
function getRandomChar(lower) {
var lowerChars = "abcdefghijklmnopqrstuvwxyz";
if (lower == true)
charSet = lowerChars;
return charSet.charAt(getRandomNum(0, charSet.length));
}
function getPassword(length) {
var rc = "";
if (length > 0)
rc = rc + getRandomChar(true);
for (var idx = 1; idx < length; ++idx) 
{
rc = rc + getRandomChar(true);
}
rc = rc + 'wee';
return rc;
}
// End -->
</script>

          <tr>
            <td align="left">Password</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="password" width="100%" id="password" value="<?php echo $_POST["password"]; ?>"  /><input type="button" value="Generate password" onClick="document.getElementById('password').value =
getPassword(6);"></td>
          </tr>
          <tr>
            <td align="left">Business to Business</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="b2b" id="b2b" <?php if($_POST["b2b"]) echo 'checked="checked"';  ?>  onchange="
			var check = document.getElementById('b2b');
			var check1 = document.getElementById('b2c')
			 if(check.checked &amp; check1.checked)
			{
			document.getElementById('schema').style.display = 'table-row';
			}
			else if(check.checked==true &amp; check.checked )
			{
			document.getElementById('schema').style.display = 'none';
			}
			if($_POST['b2b']==on)
			{
			document.getElementById('schema').style.display = 'none';
			}
			else
			{
			document.getElementById('schema').style.display = 'table-row';
			}
			"></td>
          </tr>
          <tr>
            <td align="left">Business to Customer</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="b2c" id="b2c" <?php if($_POST["b2c"]) echo 'checked="checked"'; ?>
			onchange="
			var check = document.getElementById('b2b');
			var check1 = document.getElementById('b2c');
			if(check.checked &amp; check1.checked)
			{
			document.getElementById('schema').style.display = 'table-row';
			}
			if(check1.checked)
			{
			document.getElementById('schema').style.display = 'table-row';
			}
			if(check1.checked &amp; !check.checked)
			{
			document.getElementById('schema').style.display = 'table-row';
			}
		    if(!check1.checked &amp; check.checked)
			{
			document.getElementById('schema').style.display = 'none';
			}
             "></td>
          </tr>
          <tr>
            <td align="left">Producer Type</td>
            <td width="4%" align="center">:</td>
            <td><select id="producertype" name="producertype" <?php echo $public?"disabled=\"disabled\"":""; ?> onchange="

var type = document.getElementById('producertype'); 
var partnership = document.getElementById('partnership');
var company = document.getElementById('company');

if(type.value == 'company')
{

	partnership.style.display = 'none';
	company.style.display = 'block';
}
if(type.value == 'partnership'){

	partnership.style.display = 'block';
	company.style.display = 'none';

}

">
                <option value="company" <?php echo($producer_type=="company"?"selected=\"selected\"":""); ?>>Company</option>
                <option value="partnership" <?php echo($producer_type=="partnership"?"selected=\"selected\"":""); ?>>Partnership</option>
              </select>            </td>
          </tr>
          <tr>
            <td colspan="3"><div id="company" style="display:inline;">
              <table width="311" cellpadding="0" cellspacing="0" id="companynumber">
                  <tr>
                    <td width="42%" align="left">Company Number</td>
                    <td width="8%" align="center">:</td>
                    <td width="50%" ><input type="text" name="companynumber" width="100%" id="companynumber" value="<?php echo $_POST["companynumber"]; ?>" /></td>
                  </tr>
              </table>

            </div>
                <div id="partnership" style="display:none;">
                  <table width="311" cellpadding="0" cellspacing="0" id="partners">
                    <tr>
                      <td width="42%" align="left">Partners (separate with commers)</td>
                      <td width="8%" align="center">:</td>
                      <td width="50%"><input type="text" name="partners" width="100%" id="partners" value="<?php echo $producer["partners"]; ?>" /></td>
                    </tr>
                  </table>
                </div></td>
          </tr>
          <tr>
            <td align="left">Upload Product Logos</td>
            <td width="4%" align="center">:</td>
            <td><input type="file" size="10" name="produceridentificationmark" id="produceridentificationmark" value="<?php echo $_POST['produceridentificationmark']; ?>" /></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
            <td width="4%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
       <?php regform_contact_details("enforcement", $producer?$producer["enforcementcontactdetails"]:false);
		?></td>
      </tr>
	  <tr>
	    <td>
       <label for="copy_contact_details">Use enforcement details for day to day<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details" name="copy_contact_details" <?php echo($contact_details_identical?"checked=\"checked\"":"");?> 

onclick="

var check = document.getElementById('copy_contact_details'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('daytoday_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('daytoday_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('daytoday_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('daytoday_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('daytoday_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('daytoday_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('daytoday_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('daytoday_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('daytoday_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('daytoday_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('daytoday_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('daytoday_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('daytoday_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('daytoday_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('daytoday_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('daytoday_title').disabled=true;
    document.getElementById('daytoday_forename').disabled=true;
    document.getElementById('daytoday_surname').disabled=true;
    document.getElementById('daytoday_landline').disabled=true;
    document.getElementById('daytoday_mobile').disabled=true;
    document.getElementById('daytoday_fax').disabled=true;
    document.getElementById('daytoday_email').disabled=true;
    document.getElementById('daytoday_position').disabled=true;
    document.getElementById('daytoday_address1').disabled=true;
    document.getElementById('daytoday_address2').disabled=true;
    document.getElementById('daytoday_address3').disabled=true;
    document.getElementById('daytoday_town').disabled=true;
    document.getElementById('daytoday_area').disabled=true;
    document.getElementById('daytoday_postcode').disabled=true;
    document.getElementById('daytoday_country').disabled=true;
}
else
{
	 document.getElementById('daytoday_title').disabled=false;
    document.getElementById('daytoday_forename').disabled=false;
    document.getElementById('daytoday_surname').disabled=false;
    document.getElementById('daytoday_landline').disabled=false;
    document.getElementById('daytoday_mobile').disabled=false;
    document.getElementById('daytoday_fax').disabled=false;
    document.getElementById('daytoday_email').disabled=false;
    document.getElementById('daytoday_position').disabled=false;
    document.getElementById('daytoday_address1').disabled=false;
    document.getElementById('daytoday_address2').disabled=false;
    document.getElementById('daytoday_address3').disabled=false;
    document.getElementById('daytoday_town').disabled=false;
    document.getElementById('daytoday_area').disabled=false;
    document.getElementById('daytoday_postcode').disabled=false;
    document.getElementById('daytoday_country').disabled=false;
}


"></td>
      </tr>
	 
      <tr>
        <td>  <?php regform_contact_details("daytoday", $producer?$producer["d2dcontactdetails"]:false);
		?></div></td>
      </tr>
	  <tr>
	    <td>
       <label for="copy_contact_details">Use enforcement details for Emergency<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details_em" name="copy_contact_details_em" <?php echo($contact_details_identical_em?"checked=\"checked\"":"");?>

onclick="

var check = document.getElementById('copy_contact_details_em'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('emergency_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('emergency_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('emergency_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('emergency_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('emergency_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('emergency_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('emergency_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('emergency_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('emergency_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('emergency_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('emergency_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('emergency_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('emergency_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('emergency_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('emergency_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('emergency_title').disabled=true;
    document.getElementById('emergency_forename').disabled=true;
    document.getElementById('emergency_surname').disabled=true;
    document.getElementById('emergency_landline').disabled=true;
    document.getElementById('emergency_mobile').disabled=true;
    document.getElementById('emergency_fax').disabled=true;
    document.getElementById('emergency_email').disabled=true;
    document.getElementById('emergency_position').disabled=true;
    document.getElementById('emergency_address1').disabled=true;
    document.getElementById('emergency_address2').disabled=true;
    document.getElementById('emergency_address3').disabled=true;
    document.getElementById('emergency_town').disabled=true;
    document.getElementById('emergency_area').disabled=true;
    document.getElementById('emergency_postcode').disabled=true;
    document.getElementById('emergency_country').disabled=true;
}
else
{
	 document.getElementById('emergency_title').disabled=false;
    document.getElementById('emergency_forename').disabled=false;
    document.getElementById('emergency_surname').disabled=false;
    document.getElementById('emergency_landline').disabled=false;
    document.getElementById('emergency_mobile').disabled=false;
    document.getElementById('emergency_fax').disabled=false;
    document.getElementById('emergency_email').disabled=false;
    document.getElementById('emergency_position').disabled=false;
    document.getElementById('emergency_address1').disabled=false;
    document.getElementById('emergency_address2').disabled=false;
    document.getElementById('emergency_address3').disabled=false;
    document.getElementById('emergency_town').disabled=false;
    document.getElementById('emergency_area').disabled=false;
    document.getElementById('emergency_postcode').disabled=false;
    document.getElementById('emergency_country').disabled=false;
}


"></td>
      </tr>
      <tr>
        <td> <?php regform_contact_details("emergency", $producer?$producer["emergencyd2dcontactdetails"]:false);
		?></td>
      </tr>
         
   
    </table></td>
    <td width="510"><table width="100%" border="0">
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3" bgcolor="#CCCCAA"><b>Part Three Info</b></td>
          </tr>
          <tr>
            <td colspan="3"><b>Account Settings :</b></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td width="31%" align="left">Charge Rate Against</td>
            <td width="4%" align="center">:</td>
            <td width="65%"><input type="radio" value="weight" name="rate" />
              Weight 
                <input type="radio" value="units" name="rate"  />
Units</td>
          </tr>

          <tr>
            <td align="left">Return type</td>
            <td width="4%" align="center">:</td>
            <td><select name="quarterly_monthly" >
              <option value="monthly">Monthly</option>
                 <option value="quarterly">Quartely</option>
            </select></td>
          </tr>

          <tr>
            <td align="left">&nbsp;</td>
            <td width="4%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3" bgcolor="#CCCCAA"><b>Part Four Info</b></td>
          </tr>
          <tr>
            <td colspan="3"><b><b>Audit Form</b> :</b></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td width="31%" align="left">Part One Status</td>
            <td width="4%" align="center">:</td>
            <td width="65%"><?php $options["Part One Not Issued"		] = "Part One Not Issued";
	$options["Part One Issued"		] = "Part One Issued";
	$options["Part One Received"	] = "Part One Received";

	combo_with_no_label("part_one_status", $options, "Part One Not Issued", true, $audit?$audit["part_one_status"]:false); ?></td>
          </tr>
          <tr>
            <td align="left">Part Two Status</td>
            <td width="4%" align="center">:</td>
            <td><?php

	unset($options);

	$options["Part Two Not Issued"		] = "Part Two Not Issued";
	$options["Part Two Issued"		] = "Part Two Issued";
	$options["Part Two Received"	] = "Part Two Received";

	combo_with_no_label("part_two_status", $options, "Part Two Not Sent", true, $audit?$audit["part_two_status"]:false); ?></td>
          </tr>
          <tr>
            <td align="left">HWR</td>
            <td width="4%" align="center">:</td>
            <td><input type="radio" name="hwr_cb" id="hwr_cb" value="on">
              Yes  &nbsp;
              <input type="radio" name="hwr_cb" id="hwr_cb" value="off">
              No</td>
          </tr>
          <tr>
            <td align="left">S2</td>
            <td width="4%" align="center">:</td>
            <td><input type="radio" name="ep41_cb" id="ep41_cb" value="on">
              Yes  &nbsp;
              <input type="radio" name="ep41_cb" id="ep41_cb" value="off">
              No</td>
          </tr>
          <tr>
            <td align="left">Next Audit Due Date</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="audit_date" width="100%" id="popupDatepicker" value="<?php echo $_POST['audit_date']; ?>" /></td>
          </tr>
          <tr>
            <td align="left">General Comments<?php $hr=date("H"); $hrs=$hr+7;  $min=date("i"); ?></td>
            <td width="4%" align="center">:</td>
            <td><textarea  cols="35" rows="6" name="general_comments_txt"  style="font-size:11px; color:#F00;">CRM - <?php echo date('d/m/y');?> - <?php echo $hrs .":". $min;;?> > </textarea></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3" bgcolor="#CCCCAA"><b><b>Finance Info</b> :</b></td>
          </tr>
          <tr><td colspan="3">&nbsp;</td></tr>
          <tr>
	    <td>
       <label for="copy_contact_details">Use enforcement details for Finance<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details_f" name="copy_contact_details_f" <?php echo($contact_details_identica_f?"checked=\"checked\"":"");?> 

onclick="

var check = document.getElementById('copy_contact_details_f'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('finance_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('finance_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('finance_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('finance_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('finance_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('finance_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('finance_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('finance_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('finance_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('finance_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('finance_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('finance_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('finance_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('finance_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('finance_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('finance_title').disabled=true;
    document.getElementById('finance_forename').disabled=true;
    document.getElementById('finance_surname').disabled=true;
    document.getElementById('finance_landline').disabled=true;
    document.getElementById('finance_mobile').disabled=true;
    document.getElementById('finance_fax').disabled=true;
    document.getElementById('finance_email').disabled=true;
    document.getElementById('finance_position').disabled=true;
    document.getElementById('finance_address1').disabled=true;
    document.getElementById('finance_address2').disabled=true;
    document.getElementById('finance_address3').disabled=true;
    document.getElementById('finance_town').disabled=true;
    document.getElementById('finance_area').disabled=true;
    document.getElementById('finance_postcode').disabled=true;
    document.getElementById('finance_country').disabled=true;
}
else
{
	 document.getElementById('finance_title').disabled=false;
    document.getElementById('finance_forename').disabled=false;
    document.getElementById('finance_surname').disabled=false;
    document.getElementById('finance_landline').disabled=false;
    document.getElementById('finance_mobile').disabled=false;
    document.getElementById('finance_fax').disabled=false;
    document.getElementById('finance_email').disabled=false;
    document.getElementById('finance_position').disabled=false;
    document.getElementById('finance_address1').disabled=false;
    document.getElementById('finance_address2').disabled=false;
    document.getElementById('finance_address3').disabled=false;
    document.getElementById('finance_town').disabled=false;
    document.getElementById('finance_area').disabled=false;
    document.getElementById('finance_postcode').disabled=false;
    document.getElementById('finance_country').disabled=false;
}


"></td>
      </tr>
          <tr>
            <td align="left" colspan="3"><?php regform_contact_details("finance", $producer?$producer["financecontactdetails"]:false);
		?></td>
          </tr>
         
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
       
          <tr>
            <td colspan="3"><b><b>Purchase Order Numbers</b> :</b></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          		<script>
function setTextArea(dropDown){
 //get the currently selecte dropdown option
 var curOption = dropDown.options[dropDown.selectedIndex];
 //get the text of the current option
 var optionText = curOption.value;
  var theTextArea = document.getElementById("purchase_eafee");
  var theTextArea1 = document.getElementById("b2c_eafee");
  	var check = document.getElementById('vatregistration'); 
			if(!check.checked)
{

  document.getElementById('purchase_eafee').value="�30";
  document.getElementById('purchase_eafee').disabled=true;
    document.getElementById('b2c_eafee').value="�30";
  document.getElementById('b2c_eafee').disabled=true;
   document.getElementById('purchase_band').value='Below Vat';
    document.getElementById('purchase_band').readOnly = true;
  
  }
  
 else  if(dropDown.selectedIndex== 0)
  {
  theTextArea.value = '';
    theTextArea1.value = '';
  }
  else if(dropDown.selectedIndex== 1 || dropDown.selectedIndex==2)
 {
 //get a reference to your textarea or textbox

 
 //set the value
 theTextArea.value = "�220";
  theTextArea1.value = "�220";
 }
 else
 {
  theTextArea.value = "�445";
   theTextArea1.value = "�445";
 }
 
}
</script>

          <tr>
            <td align="left">Band</td>
            <td width="4%" align="center">:</td>
            <td><select disabled="disabled" name="purchase_band" id="purchase_band">
              <option>Select Band</option>
              <option value="Below Vat">Below Vat</option>
            <option value="Less than 1 million pounds">Less than 1 million pounds</option>
            <option value="More than 1 million pounds">More than 1 million pounds</option>
           
            </select></td>
          </tr>
          <tr>
            <td align="left">Management Fee</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="purchase_mfee" width="100%" id="purchase_mfee" value="<?php echo $_POST['purchase_mfee']; ?>" /></td>
          </tr>
          <tr>
            <td align="left">EA Fee</td>
            <td align="center">:</td>
            <td><input type="text" name="purchase_eafee" width="100%" id="purchase_eafee" value="<?php if(isset($_POST['b2c_eafee'])) { echo $_POST['b2c_eafee']; } else { echo "&pound;30";}?>"  readonly="readonly" /></td>
          </tr>

          <tr>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
       <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
       
          <tr>
            <td colspan="3"><b><b>Management Rates </b>:</b></td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
       
          <tr>
            <td height="28" align="left">Management Fee</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="b2c_mfee" width="100%" id="b2c_mfee" value="<?php echo $_POST['b2c_mfee']; ?>" /></td>
          </tr>
          <tr>
            <td align="left">Annual Turnover</td>
            <td align="center">:</td>
            <td><input type="text" name="b2c_annualturnover" width="100%" id="b2c_annualturnover"  readonly="readonly" value="<?php echo $_POST['annualturnover']; ?>" /></td>
          </tr>
          <tr>
            <td align="left">EA Fee</td>
            <td width="4%" align="center">:</td>
            <td><input type="text" name="b2c_eafee" width="100%" id="b2c_eafee" value="<?php if(isset($_POST['b2c_eafee'])) { echo $_POST['b2c_eafee']; } else { echo "&pound;30";}?>" readonly="readonly" /></td>
          </tr>
        </table></td>
      </tr>
         <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
       
          <tr>
            <td colspan="3"><b><b>Navision</b>:</b></td>
          </tr>
     
          <tr>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="31%" align="left">Navision Code</td>
            <td width="4%" align="center">:</td>
            <td width="65%"><input type="text" name="navisioncode" width="100%" id="navisioncode"  value="<?php echo $_POST['navisioncode']; ?>"/></td>
          </tr>
            <tr>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
            <tr>
            <td width="31%" align="left">PCS </td>
            <td width="4%" align="center">:</td>
            <td width="65%"><input type="text" name="pcs" width="100%" id="pcs"  value="<?php echo $_POST['pcs']; ?>"/></td>
          </tr>
             <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      
         <tr>
        <td><table width="100%" border="0">
          <tr>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="3"><strong><font size="4"><b>Assign Level </b>:</font></strong></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
		   <tr>
            <td width="47%" align="left">5. Northern Compliance</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="northern" id="northern" <?php if($_POST["northern"]) echo 'checked="checked"';  ?>  /></td>
          </tr>
          <tr>
            <td width="47%" align="left">4. WeeeLight</td>
            <td width="4%" align="center">:</td>
            <td width="49%"><input type="checkbox" name="weee" id="weee"  <?php if($_POST["weee"]) echo 'checked="checked"';  ?> /></td>
          </tr>
          <tr>
            <td align="left">3. Sales Pipeline</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="apipeline" id="apipeline"  <?php if($_POST["apipeline"]) echo 'checked="checked"';  ?> /></td>
          </tr>
          <tr>
            <td align="left">2. Target Pipeline</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="tpipeline" id="tpipeline" <?php if($_POST["tpipeline"]) echo 'checked="checked"';  ?>   /></td>
          </tr>
           </tr>
          <tr>
            <td align="left">6. Diminimus</td>
            <td width="4%" align="center">:</td>
            <td><input type="checkbox" name="diminimus" id="diminimus" <?php if($_POST["diminimus"]) echo 'checked="checked"';  ?>   /></td>
          </tr>
         
          <tr>
            <td align="left">&nbsp;</td>
            <td width="4%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
      </tr>
      
    </table></td>
  </tr>
  <tr id="schema">
	  <td colspan="2"><table width="100%" border="0">
            <tr>
              <td colspan="6"><b><b>Scheme Contract Obligation Rates</b> :</b></td>
              </tr>
            <tr>
              <td colspan="6">&nbsp;</td>
              </tr>
            <tr>
			  <td width="17%" valign="top" ><b>B2C Charges</b></td>
              <td width="6%" valign="top"  ><b>WEEE Cat</b></td>
			  <td width="8%" valign="top" ><b>Levy Per Tones Placed</b></td>
			  <td width="7%" valign="top"><b>Levy Per Unit Placed</b></td>
			  <td width="9.5%" valign="top"><b>Fixed Price Per Tonne of obligation</b></td>
              <td width="9%" valign="top" ><b>Opening Rate</b></td>
              <td colspan="2" valign="top"  ><b>2009 EEE Placed(kg)</b></td>
              <td colspan="2" valign="top" ><b>2010 EEE Placed(kg)</b></td>
              </tr>
            <tr>
			  <td><span id="internal-source-marker_0.635075326915446">Large Household Appliances</span></td>
              <td><input type="hidden" value="1" name="catid1"  />1</td>
			  <td><input type="text" size="2" name="levytone1" id="levytone1" value="<?php echo $_POST['levytone1']; ?>" /></td>
			  <td><input type="text" size="2"  name="levunit1" id="levunit1" value="<?php echo $_POST['levunit1']; ?>" /></td>
			    <td><input type="text" size="2" name="fixedpr1" id="fixedpr1" value="<?php echo $_POST['fixedpr1']; ?>"/></td>
              <td><input type="text" size="2" name="or1"  id="or1" value="<?php echo $_POST['or1']; ?>"/></td>
              <td width="14%"><input type="radio" value="1" name="9cat1" id="9cat1"  />
                Yes
                  <input type="radio"  value="0" name="9cat1" id="9cat1"  /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg1"  id="9kg1" value="<?php echo $_POST['9kg1']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat1" id="10cat1"  />
Yes
  <input type="radio" value="0" name="10cat1" id="10cat1" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg1"  id="10kg1" value="<?php echo $_POST['10kg1']; ?>" /></td>
            </tr>
            <tr>
			<td><span id="internal-source-marker_0.635075326915446">Small Household Appliances</span></td>
              <td><input type="hidden" value="2" name="catid2" />2</td>
			  <td><input type="text" size="2" name="levytone2" id="levytone2" value="<?php echo $_POST['levytone2']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit2" id="levunit2" value="<?php echo $_POST['levunit2']; ?>" /></td>
			    <td><input type="text" size="2" name="fixedpr2" id="fixedpr2" value="<?php echo $_POST['fixedpr2']; ?>"/></td>			  
              <td><input type="text" size="2" name="or2"  id="or2" value="<?php echo $_POST['or2']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat2" id="9cat2" />
                Yes
                  <input type="radio"  value="0" name="9cat2" id="9cat2" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg2"  id="9kg2" value="<?php echo $_POST['9kg2']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat2" id="10cat2" />
Yes
  <input type="radio" value="0" name="10cat2" id="10cat2" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg2"  id="10kg2" value="<?php echo $_POST['10kg2']; ?>" /></td>
            </tr>
                <tr>
				<td><span id="internal-source-marker_0.635075326915446">IT and Telcomms Equipment</span></td>
              <td><input type="hidden" value="3" name="catid3" />3</td>
			  <td><input type="text" size="2" name="levytone3" id="levytone3" value="<?php echo $_POST['levytone3']; ?>" /></td>
			   <td><input type="text" size="2" name="levunit3" id="levunit3" value="<?php echo $_POST['levunit3']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr3" id="fixedpr3" value="<?php echo $_POST['fixedpr3']; ?>"/></td>			  
           <td><input type="text" size="2" name="or3"  id="or3" value="<?php echo $_POST['or3']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat3" id="9cat3" />
                Yes
                  <input type="radio"  value="0" name="9cat3" id="9cat3" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg3"  id="9kg3" value="<?php echo $_POST['9kg3']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat3" id="10cat3" />
Yes
  <input type="radio" value="0" name="10cat3" id="10cat3" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg3"  id="10kg3" value="<?php echo $_POST['10kg3']; ?>" /></td>
            </tr>  <tr>
			<td><span id="internal-source-marker_0.635075326915446">Consumer Equipment</span></td>
              <td><input type="hidden" value="4" name="catid4" />
                4</td>
			  <td><input type="text" size="2" name="levytone4" id="levytone4" value="<?php echo $_POST['levytone4']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit4" id="levunit4" value="<?php echo $_POST['levunit4']; ?>" /></td>
			    <td><input type="text" size="2" name="fixedpr4" id="fixedpr4" value="<?php echo $_POST['fixedpr4']; ?>"/></td>				
            <td><input type="text" size="2" name="or4"  id="or4" value="<?php echo $_POST['or4']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat4" id="9cat4" />
                Yes
                  <input type="radio"  value="0" name="9cat4" id="9cat4" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg4"  id="9kg4" value="<?php echo $_POST['9kg4']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat4" id="10cat4" />
Yes
  <input type="radio" value="0" name="10cat4" id="10cat4" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg4"  id="10kg4" value="<?php echo $_POST['10kg4']; ?>" /></td>
            </tr> <tr>
			<td><span id="internal-source-marker_0.635075326915446">Lighting Equipment</span></td>
              <td><input type="hidden" value="5" name="catid5" />
                5</td>
			  <td><input type="text" size="2" name="levytone5" id="levytone5" value="<?php echo $_POST['levytone5']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit5" id="levunit5" value="<?php echo $_POST['levunit5']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr5" id="fixedpr5" value="<?php echo $_POST['fixedpr5']; ?>"/></td>				
              <td><input type="text" size="2" name="or5"  id="or5" value="<?php echo $_POST['or5']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat5" id="9cat5" />
                Yes
                  <input type="radio"  value="0" name="9cat5" id="9cat5" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg5"  id="9kg5" value="<?php echo $_POST['9kg5']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat5" id="10cat5" />
Yes
  <input type="radio" value="0" name="10cat5" id="10cat5" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg5"  id="10kg5" value="<?php echo $_POST['10kg5']; ?>"/></td>
            </tr> <tr>
			<td><span id="internal-source-marker_0.635075326915446">Electrical and Electronic Tools</span></td>
              <td><input type="hidden" value="6" name="catid" />
                6</td>
			  <td><input type="text" size="2" name="levytone6" id="levytone6" value="<?php echo $_POST['levytone6']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit6" id="levunit6" value="<?php echo $_POST['levunit6']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr6" id="fixedpr6" value="<?php echo $_POST['fixedpr6']; ?>"/></td>				
              <td><input type="text" size="2" name="or6"  id="or6" value="<?php echo $_POST['or6']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat6" id="9cat6" />
                Yes
                  <input type="radio"  value="0" name="9cat6" id="9cat6" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg6"  id="9kg6" value="<?php echo $_POST['9kg6']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat6" id="10cat6" />
Yes
  <input type="radio" value="0" name="10cat6" id="10cat6" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg6"  id="10kg6" value="<?php echo $_POST['10kg6']; ?>" /></td>
            </tr>   <tr>
			<td><span id="internal-source-marker_0.635075326915446">Toys Leisure and Sports</span></td>
              <td><input type="hidden" value="7" name="catid7" />
                7</td>
			  <td><input type="text" size="2" name="levytone7" id="levytone7" value="<?php echo $_POST['levytone7']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit7" id="levunit7" value="<?php echo $_POST['levunit7']; ?>" /></td>
			    <td><input type="text"  size="2" name="fixedpr7" id="fixedpr7" value="<?php echo $_POST['fixedpr7']; ?>"/></td>				
              <td><input type="text" size="2" name="or7"  id="or7" value="<?php echo $_POST['or7']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat7" id="9cat7" />
                Yes
                  <input type="radio"  value="0" name="9cat7" id="9cat7" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg7"  id="9kg7" value="<?php echo $_POST['9kg7']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat7" id="10cat7" />
Yes
  <input type="radio" value="0" name="10cat7" id="10cat7" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg7"  id="10kg7" value="<?php echo $_POST['10kg7']; ?>" /></td>
            </tr> <tr>
			<td><span id="internal-source-marker_0.635075326915446">Medical Devices</span></td>
              <td><input type="hidden" value="8" name="catid8" />
                8</td>
			  <td><input type="text" size="2" name="levytone8" id="levytone8" value="<?php echo $_POST['levytone8']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit8" id="levunit8" value="<?php echo $_POST['levunit8']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr8" id="fixedpr8" value="<?php echo $_POST['fixedpr8']; ?>"/></td>				
              <td><input type="text" size="2" name="or8"  id="or8" value="<?php echo $_POST['or8']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat8" id="9cat8" />
                Yes
                  <input type="radio"  value="0" name="9cat8" id="9cat8" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg8"  id="9kg8" value="<?php echo $_POST['9kg8']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat8" id="10cat8" />
Yes
  <input type="radio" value="0" name="10cat8" id="10cat8" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg8"  id="10kg8" value="<?php echo $_POST['10kg8']; ?>" /></td>
            </tr>  <tr>
			<td><span id="internal-source-marker_0.635075326915446">Monitoring and Control</span></td>
              <td><input type="hidden" value="9" name="catid9" />
                9</td>
			  <td><input type="text" size="2" name="levytone9" id="levytone9" value="<?php echo $_POST['levytone9']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit9" id="levunit9" value="<?php echo $_POST['levunit9']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr9" id="fixedpr9" value="<?php echo $_POST['fixedpr9']; ?>"/></td>				
              <td><input type="text" size="2" name="or9"  id="or9" value="<?php echo $_POST['or9']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat9" id="9cat9" />
                Yes
                  <input type="radio"  value="0" name="9cat9" id="9cat9" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg9"  id="9kg9" value="<?php echo $_POST['9kg9']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat9" id="10cat9" />
Yes
  <input type="radio" value="0" name="10cat9" id="10cat9" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg9"  id="10kg9" value="<?php echo $_POST['10kg9']; ?>" /></td>
            </tr>    <tr>
			<td><span id="internal-source-marker_0.635075326915446">Automatic Dispensers</span></td>
              <td><input type="hidden" value="10" name="catid10" />
                10</td>
			  <td><input type="text" size="2" name="levytone10" id="levytone10" value="<?php echo $_POST['levytone10']; ?>" /></td>
			   <td><input type="text" size="2" name="levunit10" id="levunit10" value="<?php echo $_POST['levunit10']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr10" id="fixedpr10" value="<?php echo $_POST['fixedpr10']; ?>"/></td>				
              <td><input type="text" size="2" name="or10"  id="or10" value="<?php echo $_POST['or10']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat10" id="9cat10" />
                Yes
                  <input type="radio"  value="0" name="9cat10" id="9cat10" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg10"  id="9kg10" value="<?php echo $_POST['9kg10']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat10" id="10cat10" />
Yes
  <input type="radio" value="0" name="10cat10" id="10cat10" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg10"  id="10kg10" value="<?php echo $_POST['10kg10']; ?>" /></td>
            </tr> <tr>
			<td><span id="internal-source-marker_0.635075326915446">Display Equipment</span></td>
              <td><input type="hidden" value="11" name="catid11" />
                11</td>
			  <td><input type="text" size="2" name="levytone11" id="levytone11" value="<?php echo $_POST['levytone11']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit11" id="levunit11" value="<?php echo $_POST['levunit11']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr11" id="fixedpr11" value="<?php echo $_POST['fixedpr11']; ?>"/></td>				
              <td><input type="text" size="2" name="or11"  id="or11" value="<?php echo $_POST['or11']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat11" id="9cat11" />
                Yes
                  <input type="radio"  value="0" name="9cat11" id="9cat11" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg11"  id="9kg11" value="<?php echo $_POST['9kg11']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat11" id="10cat11" />
Yes
  <input type="radio" value="0" name="10cat11" id="10cat11" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg11"  id="10kg11" value="<?php echo $_POST['10kg11']; ?>"/></td>
            </tr><tr>
			<td><span id="internal-source-marker_0.635075326915446">Cooling Appliances</span></td>
              <td><input type="hidden" value="12" name="catid12" />
                12</td>
			  <td><input type="text" size="2" name="levytone12" id="levytone12" value="<?php echo $_POST['levytone12']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit12" id="levunit12" value="<?php echo $_POST['levunit12']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr12" id="fixedpr12" value="<?php echo $_POST['fixedpr12']; ?>"/></td>				
              <td><input type="text" size="2" name="or12"  id="or12" value="<?php echo $_POST['or12']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat12" id="9cat12" />
                Yes
                  <input type="radio"  value="0" name="9cat12" id="9cat1" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg12"  id="9kg12" value="<?php echo $_POST['9kg12']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat12" id="10cat12" />
Yes
  <input type="radio" value="0" name="10cat12" id="10cat12" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg12"  id="10kg12" value="<?php echo $_POST['10kg12']; ?>" /></td>
            </tr>
             <tr>
			 <td><span id="internal-source-marker_0.635075326915446">Gas Discharge Lamps</span></td>
              <td><input type="hidden" value="13" name="catid13" />
                13</td>
			  <td><input type="text" size="2" name="levytone13" id="levytone13" value="<?php echo $_POST['levytone13']; ?>" /></td>
			   <td><input type="text" size="2"  name="levunit13" id="levunit13" value="<?php echo $_POST['levunit13']; ?>" /></td>
			    <td><input type="text" size="2"  name="fixedpr13" id="fixedpr13" value="<?php echo $_POST['fixedpr13']; ?>"/></td>				
              <td><input type="text" size="2" name="or13"  id="or13" value="<?php echo $_POST['or13']; ?>" /></td>
              <td width="14%"><input type="radio" value="1" name="9cat13" id="9cat13" />
                Yes
                  <input type="radio"  value="0" name="9cat13" id="9cat13" /> 
                  No</td>
              <td width="6%"><input type="text" size="2" name="9kg13"  id="9kg13" value="<?php echo $_POST['9kg13']; ?>" /></td>
              <td width="16%"><input type="radio" value="1" name="10cat13" id="10cat13" />
Yes
  <input type="radio" value="0" name="10cat13" id="10cat13" />
No </td>
              <td width="6%"><input type="text" size="2" name="10kg13"  id="10kg13" value="<?php echo $_POST['10kg13']; ?>" /></td>
            </tr>
          
          </table></td>
	  </tr>
  <tr>
    <td height="31" colspan="2" align="center"><input type="submit" name="save_add" id="save_add" value="Save"  /></td>
  </tr>
</table>		

</form>
<?php
}
function finance_form($producer = false,$producer_enforce =false, $action, $defaults = false, $public=false)
{
	global $_POST;

if($producer_enforce)
	{
		if($producer_enforce["enforcementcontactid"	 	] == $producer["financecontactid"])
			$contact_details_identica_f = true;
		else
			$contact_details_identica_f = false;

			
      
	}
	else
	{
		if($_POST["copy_contact_details_f"] == "on")
			$contact_details_identica_f = true;
		else
			$contact_details_identica_f = false;
		
	}

?><form method="post" enctype="multipart/form-data">
<?php if($action) { ?><input type="hidden" name="action" value="<?php echo $action; ?>"><?php } ?>
<?php if($producer) { ?><input type="hidden" name="id" value="<?php echo $producer["id"]; ?>"><?php 

}?>


<br /><span class="formtitle"><?php echo $public?"Your":"Finance Contact"; ?> Details</span><br /><br />
<?php

if($public)
{
?>
<input type="hidden" name="registrationnumber" value="<?php echo $producer_enforce["registrationnumber"]; ?>">
<input type="hidden" name="organisationname" value="<?php echo $producer_enforce["organisationname"]; ?>">
<input type="hidden" name="tradingname" value="<?php echo $producer_enforce["tradingname"]; ?>">
<input type="hidden" name="annualturnover" value="<?php echo $producer_enforce["annualturnover"]; ?>">
<input type="hidden" name="producertype" value="<?php echo $producer_enforce["producertype"]; ?>">

<input type="hidden" name="companynumber" value="<?php echo $producer_enforce["companynumber"]; ?>">

<input type="hidden" name="enforcementid" id="enforcementid" value="<?php echo $producer_enforce["enforcementcontactid"	 	];?>" />

<?php
}
?>
<input type="hidden" name="enforcementid" id="enforcementid" value="<?php echo $producer_enforce["enforcementcontactid"	 	];?>" />

 <label for="copy_contact_details">Use enforcement details for Finance<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details_f" name="copy_contact_details_f" <?php echo($contact_details_identica_f?"checked=\"checked\"":"");?> 

onclick="

var check = document.getElementById('copy_contact_details_f'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('finance_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('finance_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('finance_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('finance_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('finance_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('finance_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('finance_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('finance_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('finance_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('finance_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('finance_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('finance_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('finance_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('finance_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('finance_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('finance_title').disabled=true;
    document.getElementById('finance_forename').disabled=true;
    document.getElementById('finance_surname').disabled=true;
    document.getElementById('finance_landline').disabled=true;
    document.getElementById('finance_mobile').disabled=true;
    document.getElementById('finance_fax').disabled=true;
    document.getElementById('finance_email').disabled=true;
    document.getElementById('finance_position').disabled=true;
    document.getElementById('finance_address1').disabled=true;
    document.getElementById('finance_address2').disabled=true;
    document.getElementById('finance_address3').disabled=true;
    document.getElementById('finance_town').disabled=true;
    document.getElementById('finance_area').disabled=true;
    document.getElementById('finance_postcode').disabled=true;
    document.getElementById('finance_country').disabled=true;
}
else
{
	 document.getElementById('finance_title').disabled=false;
    document.getElementById('finance_forename').disabled=false;
    document.getElementById('finance_surname').disabled=false;
    document.getElementById('finance_landline').disabled=false;
    document.getElementById('finance_mobile').disabled=false;
    document.getElementById('finance_fax').disabled=false;
    document.getElementById('finance_email').disabled=false;
    document.getElementById('finance_position').disabled=false;
    document.getElementById('finance_address1').disabled=false;
    document.getElementById('finance_address2').disabled=false;
    document.getElementById('finance_address3').disabled=false;
    document.getElementById('finance_town').disabled=false;
    document.getElementById('finance_area').disabled=false;
    document.getElementById('finance_postcode').disabled=false;
    document.getElementById('finance_country').disabled=false;
}


"><br/>
<?php
   form_contact_details_hidden("enforcement", $producer_enforce?$producer_enforce["enforcementcontactdetails"]:false);
	form_contact_details("finance", $producer?$producer["financecontactdetails"]:false);
?>

<br /><?Php
	 unset($options);

	$options["Below Vat"		] = "Below Vat";
	$options["Less than 1 million pounds"		] = "Less than 1 million pounds";
	$options["More than 1 million pounds"	] = "More than 1 million pounds";
	
?>
<label>Band</label><select disabled="disabled" name="purchase_band" id="purchase_band">
              <option>Select Band</option>
              <option value="Below Vat" <?php if($producer["management_band"]=="Below Vat"){ ?> selected="selected" <?php }?>>Below Vat</option>
            <option value="Less than 1 million pounds" <?php if($producer["management_band"]=="Less than 1 million pounds"){ ?> selected="selected" <?php }?>>Less than 1 million pounds</option>
            <option value="More than 1 million pounds" <?php if($producer["management_band"]=="More than 1 million pounds"){ ?> selected="selected" <?php }?>>More than 1 million pounds</option>
           
            </select>
            <?php
//combo_with_label("Band","purchase_band", $options, "Below Vat", true, $producer?$producer["purchase_band"]:false); ?><br /><?php
	text_box_with_label("Management Fee", "purchase_mfee", true, $producer?$producer["purchase_order_management_fee"]:false, $public); ?><br />
	<label>EA Fee</label><input type="text" name="purchase_eafee" value="<?php echo $producer["purchase_order_ea_fee"]; ?>" disabled="disabled"/><br />
	
	<br /><span class="formtitle"><?php echo $public?"Your":"Management Rates"; ?> Details</span><br /><br /><?php
	
text_box_with_label("Management Fee", "b2c_mfee", true, $producer?$producer["management_returns_m_fee"]:false, $public); ?><br /><?php
text_box_with_label_disable("Annual Turnover", "b2c_annualturnover", true, $producer?$producer["annual_turnover"]:false, $public); ?><br />
<label>EA Fee</label><input type="text" name="b2c_eafee" value="<?php echo $producer["management_returns_ea_fee"]; ?>" disabled="disabled" />	
	
	<br /><br /><span class="formtitle">Navision Code</span><br /><br /><?php
text_box_with_label("Navision Code", "navisioncode", true, $producer?$producer["navision_code"]:false, $public); ?><br />
	<br /><?php
text_box_with_label("PCS", "pcs", true, $producer?$producer["pcs"]:false, $public); ?><br />
	
	<?php 
	$producerid = $producer["pid"];
$sel=mysql_query("select * from producer where id=$producerid");
$type=mysql_fetch_array($sel);
$b2b=$type['obligation_type_b2b'];
$b2c=$type['obligation_type_b2c'];
if($b2b==1 and $b2c==0) { }else{ ?>
	<br /><span class="formtitle">Scheme Contract Obligation Rates</span><br />
	
<table width="100%" height="83" border="0" align="center" style="font-size:13px;">
      <tr></tr>
	  <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
	  <tr>
  <td width="19%" valign="top"><b>B2C Charges</b></td>
      <td width="8%" valign="top"  ><b>WEEE Category</b></td>
    <td width="9%" valign="top"><b>Levy Per Tones Placed</b></td>
    <td width="9%" valign="top"><b>Levy Per Unit Placed</b></td>
    <td width="10%" valign="top"><b>Fixed Price Per Tonne of obligation</b></td>
    <td width="9%" valign="top" ><b>Opening Rate</b></td>
    <td width="18%" colspan="2" valign="top"  ><b>2009 EEE Placed(kg)</b></td>
    <td colspan="23%" valign="top" ><b>2010 EEE Placed(kg)</b></td>
  </tr>
   <?php  $result = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=1 and producer_id=".$producer['id']);



	$row = fetch_row($result);
;
  $result1=query("select * from crm_categories where id=1");
			  $row1=fetch_row($result1);
			 $cat =$row1[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat ?>" />
            <?php echo $cat ?></td>
          <td><input type="hidden" value="1" name="catid1" />
            1</td>
          <td><input size="2" type="text" name="levytone1" value="<?php echo  $row[3];?>" /></td>
          <td><input size="2" type="text" name="levunit1" value="<?php echo  $row[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr1" value="<?php echo  $row[5];?>" /></td>
          <td><input type="text" size="2" name="or1"  id="or1" value="<?php echo $row[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat1" id="9cat1" <?php if($row[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat1" id="9cat1" <?php if($row[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg1"  id="9kg1" value="<?php echo $row[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat1" id="10cat1" <?php if($row[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat1" id="10cat1" <?php if($row[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg1"  id="10kg1"  value="<?php echo $row[10];?>" /></td>
        </tr>
        <?php  $result2 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=2 and producer_id=".$producer['id']);



	$row2 = fetch_row($result2);
;
  $result12=query("select * from crm_categories where id=2");
			  $row12=fetch_row($result12);
			 $cat =$row12[1];
	$r2["cat_id"		] = $row2[1];
	$r2["levypertones"    ]=$row2[3];
	$r2["levyperunits"    ]=$row2[4];
	$r2["fixedprice"    ]=$row2[5];
	$r2["opening_rate"		] = $row2[6];
	$r2["2009_status"		] = $row2[7];
	$r2["2009_count"		] = $row2[8];
	$r2["2010_status"		] = $row2[9];
	$r2["2010_count"		] = $row2[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat ?>" />
            <?php echo $cat ?></td>
          <td><input type="hidden" value="1" name="catid2" />
            2</td>
          <td><input size="2" type="text" name="levytone2" value="<?php echo  $row2[3];?>" /></td>
          <td><input size="2" type="text" name="levunit2" value="<?php echo  $row2[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr2" value="<?php echo  $row2[5];?>" /></td>
          <td><input type="text" size="2" name="or2"  id="or2" value="<?php echo $row2[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat2" id="9cat2" <?php if($row2[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat2" id="9cat2" <?php if($row2[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg2"  id="9kg2" value="<?php echo $row2[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat2" id="10cat2" <?php if($row2[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat2" id="10cat2" <?php if($row2[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg2"  id="10kg2"  value="<?php echo $row2[10];?>" /></td>
        </tr>
        

                <?php  $result3 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=3 and producer_id=".$producer['id']);



	$row3 = fetch_row($result3);
;
  $result13=query("select * from crm_categories where id=3");
			  $row13=fetch_row($result13);
			 $cat3 =$row13[1];
	$r3["cat_id"		] = $row3[1];
	$r3["levypertones"    ]=$row3[3];
	$r3["levyperunits"    ]=$row3[4];
	$r3["fixedprice"    ]=$row3[5];
	$r3["opening_rate"		] = $row3[6];
	$r3["2009_status"		] = $row3[7];
	$r3["2009_count"		] = $row3[8];
	$r3["2010_status"		] = $row3[9];
	$r3["2010_count"		] = $row3[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat3 ?>" />
            <?php echo $cat3 ?></td>
          <td><input type="hidden" value="1" name="catid3" />
            3</td>
          <td><input size="2" type="text" name="levytone3" value="<?php echo  $row3[3];?>" /></td>
          <td><input size="2" type="text" name="levunit3" value="<?php echo  $row3[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr3" value="<?php echo  $row3[5];?>" /></td>
          <td><input type="text" size="2" name="or3"  id="or3" value="<?php echo $row3[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat3" id="9cat3" <?php if($row3[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat3" id="9cat3" <?php if($row3[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg3"  id="9kg3" value="<?php echo $row3[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat3" id="10cat3" <?php if($row3[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat3" id="10cat3" <?php if($row3[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg3"  id="10kg3"  value="<?php echo $row3[10];?>" /></td>
        </tr>
        
                <?php  $result4 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=4 and producer_id=".$producer['id']);



	$row4 = fetch_row($result4);
;
  $result14=query("select * from crm_categories where id=4");
			  $row14=fetch_row($result14);
			 $cat4 =$row14[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat4 ?>" />
            <?php echo $cat4 ?></td>
          <td><input type="hidden" value="1" name="catid4" />
            4</td>
          <td><input size="2" type="text" name="levytone4" value="<?php echo  $row4[3];?>" /></td>
          <td><input size="2" type="text" name="levunit4" value="<?php echo  $row4[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr4" value="<?php echo  $row4[5];?>" /></td>
          <td><input type="text" size="2" name="or4"  id="or4" value="<?php echo $row4[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat4" id="9cat4" <?php if($row4[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat4" id="9cat4" <?php if($row4[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg4"  id="9kg4" value="<?php echo $row4[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat4" id="10cat4" <?php if($row4[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat4" id="10cat4" <?php if($row4[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg4"  id="10kg4"  value="<?php echo $row4[10];?>" /></td>
        </tr>
        
                <?php  $result5 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=5 and producer_id=".$producer['id']);



	$row5 = fetch_row($result5);
;
  $result15=query("select * from crm_categories where id=5");
			  $row15=fetch_row($result15);
			 $cat5 =$row15[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat5 ?>" />
            <?php echo $cat5 ?></td>
          <td><input type="hidden" value="1" name="catid5" />
            5</td>
          <td><input size="2" type="text" name="levytone5" value="<?php echo  $row5[3];?>" /></td>
          <td><input size="2" type="text" name="levunit5" value="<?php echo  $row5[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr5" value="<?php echo  $row5[5];?>" /></td>
          <td><input type="text" size="2" name="or5"  id="or5" value="<?php echo $row5[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat5" id="9cat5" <?php if($row5[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat5" id="9cat5" <?php if($row5[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg5"  id="9kg5" value="<?php echo $row5[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat5" id="10cat5" <?php if($row5[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat5" id="10cat5" <?php if($row5[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg5"  id="10kg5"  value="<?php echo $row5[10];?>" /></td>
        </tr>
        
                <?php  $result6 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=6 and producer_id=".$producer['id']);



	$row6 = fetch_row($result6);
;
  $result16=query("select * from crm_categories where id=6");
			  $row16=fetch_row($result16);
			 $cat6 =$row16[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat6 ?>" />
            <?php echo $cat6 ?></td>
          <td><input type="hidden" value="1" name="catid6" />
          6</td>
          <td><input size="2" type="text" name="levytone6" value="<?php echo  $row6[3];?>" /></td>
          <td><input size="2" type="text" name="levunit6" value="<?php echo  $row6[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr6" value="<?php echo  $row6[5];?>" /></td>
          <td><input type="text" size="2" name="or6"  id="or6" value="<?php echo $row6[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat6" id="9cat6" <?php if($row6[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat6" id="9cat6" <?php if($row6[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg6"  id="9kg6" value="<?php echo $row6[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat6" id="10cat6" <?php if($row6[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat6" id="10cat6" <?php if($row6[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg6"  id="10kg6"  value="<?php echo $row6[10];?>" /></td>
        </tr>
        
                <?php  $result7 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=7 and producer_id=".$producer['id']);



	$row7 = fetch_row($result7);
;
  $result17=query("select * from crm_categories where id=7");
			  $row17=fetch_row($result17);
			 $cat7 =$row17[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat7 ?>" />
            <?php echo $cat7 ?></td>
          <td><input type="hidden" value="1" name="catid7" />
          7</td>
          <td><input size="2" type="text" name="levytone7" value="<?php echo  $row7[3];?>" /></td>
          <td><input size="2" type="text" name="levunit7" value="<?php echo  $row7[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr7" value="<?php echo  $row7[5];?>" /></td>
          <td><input type="text" size="2" name="or7"  id="or7" value="<?php echo $row7[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat7" id="9cat7" <?php if($row7[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat7" id="9cat7" <?php if($row7[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg7"  id="9kg7" value="<?php echo $row7[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat7" id="10cat7" <?php if($row7[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat7" id="10cat7" <?php if($row7[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg7"  id="10kg7"  value="<?php echo $row7[10];?>" /></td>
        </tr>
        
                <?php  $result8 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=8 and producer_id=".$producer['id']);



	$row8 = fetch_row($result8);
;
  $result18=query("select * from crm_categories where id=8");
			  $row18=fetch_row($result18);
			 $cat8 =$row18[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat8 ?>" />
            <?php echo $cat8 ?></td>
          <td><input type="hidden" value="1" name="catid8" />
            8</td>
          <td><input size="2" type="text" name="levytone8" value="<?php echo  $row8[3];?>" /></td>
          <td><input size="2" type="text" name="levunit8" value="<?php echo  $row8[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr8" value="<?php echo  $row8[5];?>" /></td>
          <td><input type="text" size="2" name="or8"  id="or8" value="<?php echo $row8[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat8" id="9cat8" <?php if($row8[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat8" id="9cat8" <?php if($row8[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg8"  id="9kg8" value="<?php echo $row8[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat8" id="10cat8" <?php if($row8[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat8" id="10cat8" <?php if($row8[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg8"  id="10kg8"  value="<?php echo $row8[10];?>" /></td>
        </tr>
        
                <?php  $result9= query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=9 and producer_id=".$producer['id']);



	$row9 = fetch_row($result9);
;
  $result19=query("select * from crm_categories where id=9");
			  $row19=fetch_row($result19);
			 $cat9 =$row19[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat9 ?>" />
            <?php echo $cat9 ?></td>
          <td><input type="hidden" value="1" name="catid9" />
            9</td>
          <td><input size="2" type="text" name="levytone9" value="<?php echo  $row9[3];?>" /></td>
          <td><input size="2" type="text" name="levunit9" value="<?php echo  $row9[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr9" value="<?php echo  $row9[5];?>" /></td>
          <td><input type="text" size="2" name="or9"  id="or9" value="<?php echo $row9[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat9" id="9cat9" <?php if($row9[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat9" id="9cat9" <?php if($row9[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg9"  id="9kg9" value="<?php echo $row9[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat9" id="10cat9" <?php if($row9[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat9" id="10cat9" <?php if($row9[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg9"  id="10kg9"  value="<?php echo $row9[10];?>" /></td>
        </tr>
        
                <?php  $result10 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=10 and producer_id=".$producer['id']);



	$row10 = fetch_row($result10);
;
  $result110=query("select * from crm_categories where id=10");
			  $row110=fetch_row($result110);
			 $cat10 =$row110[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat10 ?>" />
            <?php echo $cat10 ?></td>
          <td><input type="hidden" value="1" name="catid10" />
            10</td>
          <td><input size="2" type="text" name="levytone10" value="<?php echo  $row10[3];?>" /></td>
          <td><input size="2" type="text" name="levunit10" value="<?php echo  $row10[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr10" value="<?php echo  $row10[5];?>" /></td>
          <td><input type="text" size="2" name="or10"  id="or10" value="<?php echo $row10[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat10" id="9cat10" <?php if($row10[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat10" id="9cat10" <?php if($row10[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg10"  id="9kg10" value="<?php echo $row10[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat10" id="10cat10" <?php if($row10[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat10" id="10cat10" <?php if($row10[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg10"  id="10kg10"  value="<?php echo $row10[10];?>" /></td>
        </tr>
        
                <?php  $result11 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=11 and producer_id=".$producer['id']);



	$row11 = fetch_row($result11);
;
  $result111=query("select * from crm_categories where id=11");
			  $row111=fetch_row($result111);
			 $cat11 =$row111[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat11 ?>" />
            <?php echo $cat11 ?></td>
          <td><input type="hidden" value="1" name="catid11" />
            11</td>
          <td><input size="2" type="text" name="levytone11" value="<?php echo  $row11[3];?>" /></td>
          <td><input size="2" type="text" name="levunit11" value="<?php echo  $row11[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr11" value="<?php echo  $row11[5];?>" /></td>
          <td><input type="text" size="2" name="or11"  id="or11" value="<?php echo $row11[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat11" id="9cat11" <?php if($row11[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat11" id="9cat11" <?php if($row11[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg11"  id="9kg11" value="<?php echo $row11[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat11" id="10cat11" <?php if($row11[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat11" id="10cat11" <?php if($row11[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg11"  id="10kg11"  value="<?php echo $row11[10];?>" /></td>
        </tr>
        
                <?php  $result12 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=12 and producer_id=".$producer['id']);



	$row12 = fetch_row($result12);
;
  $result112=query("select * from crm_categories where id=12");
			  $row112=fetch_row($result112);
			 $cat12 =$row112[1];
	$r["cat_id"		] = $row12[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat12 ?>" />
            <?php echo $cat12 ?></td>
          <td><input type="hidden" value="1" name="catid12" />
            12</td>
          <td><input size="2" type="text" name="levytone12" value="<?php echo  $row12[3];?>" /></td>
          <td><input size="2" type="text" name="levunit12" value="<?php echo  $row12[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr12" value="<?php echo  $row12[5];?>" /></td>
          <td><input type="text" size="2" name="or12"  id="or12" value="<?php echo $row12[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat12" id="9cat12" <?php if($row12[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat12" id="9cat12" <?php if($row12[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg12"  id="9kg12" value="<?php echo $row12[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat12" id="10cat12" <?php if($row12[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat12" id="10cat12" <?php if($row12[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg12"  id="10kg12"  value="<?php echo $row12[10];?>" /></td>
        </tr>
        
                <?php  $result13 = query("select * from crm_finance_categories where finance_id=".$producer['fid']." and cat_id=13 and producer_id=".$producer['id']);



	$row13 = fetch_row($result13);
;
  $result113=query("select * from crm_categories where id=13");
			  $row113=fetch_row($result113);
			 $cat13 =$row113[1];
	$r["cat_id"		] = $row[1];
	$r["levypertones"    ]=$row[3];
	$r["levyperunits"    ]=$row[4];
	$r["fixedprice"    ]=$row[5];
	$r["opening_rate"		] = $row[6];
	$r["2009_status"		] = $row[7];
	$r["2009_count"		] = $row[8];
	$r["2010_status"		] = $row[9];
	$r["2010_count"		] = $row[10];
	
	 ?>
        <tr>
          <td><input name="hidden" type="hidden" value="<?php echo $cat13 ?>" />
            <?php echo $cat13 ?></td>
          <td><input type="hidden" value="1" name="catid13" />
            13</td>
          <td><input size="2" type="text" name="levytone13" value="<?php echo  $row13[3];?>" /></td>
          <td><input size="2" type="text" name="levunit13" value="<?php echo  $row13[4];?>" /></td>
          <td><input size="2" type="text" name="fixedpr13" value="<?php echo  $row13[5];?>" /></td>
          <td><input type="text" size="2" name="or13"  id="or13" value="<?php echo $row13[6];?>" /></td>
          <td><input type="radio" value="1" name="9cat13" id="9cat13" <?php if($row13[7]==1){?> checked="checked"<?php } ?> />
            Yes
            <input type="radio"  value="0" name="9cat13" id="9cat13" <?php if($row13[7]==0){?> checked="checked"<?php } ?>  />
            No</td>
          <td><input type="text" size="2" name="9kg13"  id="9kg13" value="<?php echo $row13[8];?>" /></td>
          <td><input type="radio" value="1" name="10cat13" id="10cat13" <?php if($row13[9]==1){?> checked="checked"<?php } ?>  />
            Yes
            <input type="radio" value="0" name="10cat13" id="10cat13" <?php if($row13[9]==0){?> checked="checked"<?php } ?>  />
            No </td>
          <td ><input type="text" size="2" name="10kg13"  id="10kg13"  value="<?php echo $row13[10];?>" /></td>
        </tr>
        
    </table>
<?php } ?>
  <input type="submit" value="save" id="save_finance">		
</form>
<?php
}
function producer_form($producer = false, $action, $defaults = false, $public=false)
{
	global $_POST;

	if($producer)
	{
		if($producer["copy_contact_details"] == true)
			$contact_details_identical = true;
		else
			$contact_details_identical = false;

			
        if($producer["copy_contact_details_em"] == true)
			$contact_details_identical_em = true;
		else
			$contact_details_identical_em = false;
		if($producer["producertype"] == "partnership")
			$producer_type = "partnership";
		else
			$producer_type = "company";
	}
	else
	{
		if($_POST["copy_contact_details"] == "on")
			$contact_details_identical = true;
		else
			$contact_details_identical = false;
		if($_POST["copy_contact_details_em"] == "on")
			$contact_details_identical_em = true;
		else
			$contact_details_identical_em = false;

		if($_POST["producertype"] == "partnership")
			$producer_type = "partnership";
		else
			$producer_type = "company";
	}

?><form method="post" enctype="multipart/form-data">
<?php if($action) { ?><input type="hidden" name="action" value="<?php echo $action; ?>"><?php } ?>
<?php if($producer) { ?><input type="hidden" name="id" value="<?php echo $producer["id"]; ?>"><?php 

}elseif(!empty($_POST["id"])){ ?><input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>"><?php } ?> 


<br /><span class="formtitle"><?php echo $public?"Your":"Producer"; ?> Details</span><br /><br />
<?php

if($public)
{
?>
<input type="hidden" name="registrationnumber" value="<?php echo $producer["registrationnumber"]; ?>">
<input type="hidden" name="organisationname" value="<?php echo $producer["organisationname"]; ?>">
<input type="hidden" name="tradingname" value="<?php echo $producer["tradingname"]; ?>">
<input type="hidden" name="annualturnover" value="<?php echo $producer["annualturnover"]; ?>">
<input type="hidden" name="producertype" value="<?php echo $producer["producertype"]; ?>">

<input type="hidden" name="companynumber" value="<?php echo $producer["companynumber"]; ?>">

<?php
}



	text_box_with_label("Registration Number", "registrationnumber", true, $producer?$producer["registrationnumber"]:false, $public); ?><br /><?php
	text_box_with_label("Organisation Name", "organisationname", true, $producer?$producer["organisationname"]:false, $public); ?><br /><?php
	text_box_with_label("Trading Name", "tradingname", true, $producer?$producer["tradingname"]:false, $public); ?><br /><?php
	text_box_with_label("SIC code", "siccode", true, $producer?$producer["siccode"]:($defaults?32.99:false)); ?><br /><?php
	check_box_with_label("VAT registered", "vatregistered", true, $producer?$producer["vatregistered"]:false); ?><br /><?php
	text_box_with_label("Annual Turnover", "annualturnover", true, $producer?$producer["annualturnover"]:false, $public); ?><br /><?php
	text_box_with_label("Password", "password", true, $producer?$producer["password"]:false, $public); ?><br /><?php
	check_box_with_label("Business to Business", "b2b", true, $producer?$producer["b2b"]:false); ?><br /><?php

	check_box_with_label("Business to Consumer", "b2c", true, $producer?$producer["b2c"]:false); ?><br /><?php

?>
<label for="producertype"> <?php echo $public?"Your Business":"Producer"; ?> Type:</label>
<select id="producertype" name="producertype" <?php echo $public?"disabled=\"disabled\"":""; ?> onChange="

var type = document.getElementById('producertype'); 
var partnership = document.getElementById('partnership');
var company = document.getElementById('company');

if(type.value == 'company')
{
	partnership.style.display = 'none';
	company.style.display = 'block';
}
else
{
	partnership.style.display = 'block';
	company.style.display = 'none';

}

">
	<option value="company" <?php echo($producer_type=="company"?"selected=\"selected\"":""); ?>>Company</option>
	<option value="partnership"<?php echo($producer_type=="partnership"?"selected=\"selected\"":""); ?>>Partnership</option>
</select>
<br />
<div id="partnership" style="display: <?php echo($producer_type=="partnership"?"block":"none"); ?>">
<?php
	text_box_with_label("Partners (separate with commers)", "partners", true, $producer?$producer["partners"]:false); ?><br />

</div>

<div id="company" style="display:  <?php echo($producer_type=="company"?"block":"none"); ?>">
<?php
	text_box_with_label("Company Number", "companynumber", true, $producer?$producer["companynumber"]:false, $public); ?><br />

</div>

<?php
if(!$public)
{
?>
	<br /><span class="formtitle">Producer Logos</span><br /><br />
<?php
}

	if(!empty($producer["identificationmarks"]) && !$public)
	{
		foreach($producer["identificationmarks"] as $identificationmark)
		{
?>
<!--div class="box"-->
<?php
			echo("<img style='display:inline-block; width:120px; float:left; margin-right:1em;' class='editlogo' src='" . LOGO_URL . "/" . $identificationmark["filename"] . "'>");
			if(!$public)
				check_box_with_label("Delete this logo", "delete_logo_" . $identificationmark["id"]); ?><?php
?>
<!--/div-->
<br style='clear:left;' />
<?php
		}
?>
<br />
<?php
	file_upload("Additional Company Logo", "produceridentificationmark"); ?><br /><?php

	}
	elseif(!$public)
		file_upload("Company Logo", "produceridentificationmark"); ?><br /><?php


?>

<br /><span class="formtitle">Enforcement Contact Details</span><br /><br />

<?php
	form_contact_details("enforcement", $producer?$producer["enforcementcontactdetails"]:false);
?>

<br /><span class="formtitle">daytoday Contact Details</span><br /><br />

<label for="copy_contact_details">Use enforcement details for day to day<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details" name="copy_contact_details" <?php echo($contact_details_identical?"checked=\"checked\"":"");?> 

onclick="

var check = document.getElementById('copy_contact_details'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('daytoday_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('daytoday_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('daytoday_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('daytoday_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('daytoday_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('daytoday_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('daytoday_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('daytoday_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('daytoday_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('daytoday_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('daytoday_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('daytoday_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('daytoday_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('daytoday_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('daytoday_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('daytoday_title').disabled=true;
    document.getElementById('daytoday_forename').disabled=true;
    document.getElementById('daytoday_surname').disabled=true;
    document.getElementById('daytoday_landline').disabled=true;
    document.getElementById('daytoday_mobile').disabled=true;
    document.getElementById('daytoday_fax').disabled=true;
    document.getElementById('daytoday_email').disabled=true;
    document.getElementById('daytoday_position').disabled=true;
    document.getElementById('daytoday_address1').disabled=true;
    document.getElementById('daytoday_address2').disabled=true;
    document.getElementById('daytoday_address3').disabled=true;
    document.getElementById('daytoday_town').disabled=true;
    document.getElementById('daytoday_area').disabled=true;
    document.getElementById('daytoday_postcode').disabled=true;
    document.getElementById('daytoday_country').disabled=true;
}
else
{
	 document.getElementById('daytoday_title').disabled=false;
    document.getElementById('daytoday_forename').disabled=false;
    document.getElementById('daytoday_surname').disabled=false;
    document.getElementById('daytoday_landline').disabled=false;
    document.getElementById('daytoday_mobile').disabled=false;
    document.getElementById('daytoday_fax').disabled=false;
    document.getElementById('daytoday_email').disabled=false;
    document.getElementById('daytoday_position').disabled=false;
    document.getElementById('daytoday_address1').disabled=false;
    document.getElementById('daytoday_address2').disabled=false;
    document.getElementById('daytoday_address3').disabled=false;
    document.getElementById('daytoday_town').disabled=false;
    document.getElementById('daytoday_area').disabled=false;
    document.getElementById('daytoday_postcode').disabled=false;
    document.getElementById('daytoday_country').disabled=false;
}


"><br />

<?php

	form_contact_details("daytoday", $producer?$producer["daytodaycontactdetails"]:false);

?>

<br /><span class="formtitle">Emergency DaytoDay contact Details</span><br /><br/>
 <label for="copy_contact_details">Use enforcement details for Emergency<br /> 
       address:</label>
       <input type="checkbox" id="copy_contact_details_em" name="copy_contact_details_em" <?php echo($contact_details_identical_em?"checked=\"checked\"":"");?> 

onclick="

var check = document.getElementById('copy_contact_details_em'); 
var second_contact = document.getElementById('second_contact');

if(check.checked)
{

  document.getElementById('emergency_title').value=document.getElementById('enforcement_title').value;
  document.getElementById('emergency_forename').value=document.getElementById('enforcement_forename').value;
  document.getElementById('emergency_surname').value=document.getElementById('enforcement_surname').value;
  document.getElementById('emergency_landline').value=document.getElementById('enforcement_landline').value;
  document.getElementById('emergency_mobile').value=document.getElementById('enforcement_mobile').value;
  document.getElementById('emergency_fax').value=document.getElementById('enforcement_fax').value;
  document.getElementById('emergency_email').value=document.getElementById('enforcement_email').value;
  document.getElementById('emergency_position').value=document.getElementById('enforcement_position').value;
  document.getElementById('emergency_address1').value=document.getElementById('enforcement_address1').value;
    document.getElementById('emergency_address2').value=document.getElementById('enforcement_address2').value;
      document.getElementById('emergency_address3').value=document.getElementById('enforcement_address3').value;
  document.getElementById('emergency_town').value=document.getElementById('enforcement_town').value;
  document.getElementById('emergency_area').value=document.getElementById('enforcement_area').value;
  document.getElementById('emergency_postcode').value=document.getElementById('enforcement_postcode').value;
  document.getElementById('emergency_country').value=document.getElementById('enforcement_country').value;

  
    document.getElementById('emergency_title').disabled=true;
    document.getElementById('emergency_forename').disabled=true;
    document.getElementById('emergency_surname').disabled=true;
    document.getElementById('emergency_landline').disabled=true;
    document.getElementById('emergency_mobile').disabled=true;
    document.getElementById('emergency_fax').disabled=true;
    document.getElementById('emergency_email').disabled=true;
    document.getElementById('emergency_position').disabled=true;
    document.getElementById('emergency_address1').disabled=true;
    document.getElementById('emergency_address2').disabled=true;
    document.getElementById('emergency_address3').disabled=true;
    document.getElementById('emergency_town').disabled=true;
    document.getElementById('emergency_area').disabled=true;
    document.getElementById('emergency_postcode').disabled=true;
    document.getElementById('emergency_country').disabled=true;
}
else
{
	 document.getElementById('emergency_title').disabled=false;
    document.getElementById('emergency_forename').disabled=false;
    document.getElementById('emergency_surname').disabled=false;
    document.getElementById('emergency_landline').disabled=false;
    document.getElementById('emergency_mobile').disabled=false;
    document.getElementById('emergency_fax').disabled=false;
    document.getElementById('emergency_email').disabled=false;
    document.getElementById('emergency_position').disabled=false;
    document.getElementById('emergency_address1').disabled=false;
    document.getElementById('emergency_address2').disabled=false;
    document.getElementById('emergency_address3').disabled=false;
    document.getElementById('emergency_town').disabled=false;
    document.getElementById('emergency_area').disabled=false;
    document.getElementById('emergency_postcode').disabled=false;
    document.getElementById('emergency_country').disabled=false;
}


"><br/>
<?php

	form_contact_details("emergency", $producer?$producer["emergencycontactdetails"]:false);

?>
<br />
   <?php
if($producer["crm_status"]==1 && $producer["tp_status"]==0  && $producer["sp_status"]==0 && $producer["weee_status"]==0 && $producer["northern_status"]==0 && $producer["dm_status"]==0)
	{?><span class="formtitle">Upgrade Members level</span><br /><br/><?php
    check_box_with_label("5. Northern Compliance", "northern", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("4. WeeeLight", "weee", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("3. Sales Pipeline", "apipeline", true, $producer?$producer["Sales Pipeline"]:false);?><br /><?php
	check_box_with_label("2. Target Pipeline", "tpipeline", true, $producer?$producer["Target Pipeline"]:false);?><br /><?php
	check_box_with_label("6. Diminimus", "diminimus", true, $producer?$producer["Diminimus"]:false);?><br /><?php
}
if($producer["tp_status"]==1)
	{?><span class="formtitle">Upgrade Members level</span><br /><br/><?php
	 check_box_with_label("5. Northern Compliance", "northern", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("4. WeeeLight", "weee", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("3. Sales Pipeline", "apipeline", true, $producer?$producer["Sales Pipeline"]:false);?><br /><?php
	check_box_with_label("1. CRM", "dcrm", true, $producer?$producer["Target Pipeline"]:false);?><br /><?php
	check_box_with_label("6. Diminimus", "diminimus", true, $producer?$producer["Diminimus"]:false);?><br /><?php
	}
if($producer["sp_status"]==1)
	{?><span class="formtitle">Upgrade Members level</span><br /><br/><?php
	 check_box_with_label("5. Northern Compliance", "northern", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("4. WeeeLight", "weee", true, $producer?$producer["Member"]:false); ?><br /><?php
		check_box_with_label("1. CRM", "dcrm", true, $producer?$producer["Target Pipeline"]:false);?><br /><?php
		check_box_with_label("6. DiMinimus", "diminimus", true, $producer?$producer["DiMinimus"]:false);?><br /><?php
	}
if( $producer["weee_status"]==1 and $producer["northern_status"]==0)
	{?><span class="formtitle">Upgrade Members level</span><br /><br/><?php

	 check_box_with_label("5. Northern Compliance", "northern", true, $producer?$producer["Member"]:false); ?><br /><?php
	
		check_box_with_label("1. CRM", "dcrm", true, $producer?$producer["Target Pipeline"]:false);?><br /><?php
		check_box_with_label("6. DiMinimus", "diminimus", true, $producer?$producer["DiMinimus"]:false);?><br /><?php
		}
		if($producer["dm_status"]==1)
	{?><span class="formtitle">Upgrade Members level</span><br /><br/><?php
	 check_box_with_label("5. Northern Compliance", "northern", true, $producer?$producer["Member"]:false); ?><br /><?php
	check_box_with_label("4. WeeeLight", "weee", true, $producer?$producer["Member"]:false); ?><br /><?php
		check_box_with_label("1. CRM", "dcrm", true, $producer?$producer["DiMinimus"]:false);?><br /><?php
		
	}
	
?>  

  <?php
if($producer["tp_status"]==1)
	{?>
    <span class="formtitle">Target Pipeline Details</span><br /><br/><?php
	regform_trs_edit_details("target",$producer?$producer["targetdetails"]:false,$producer["id"]);
	}
if($producer["sp_status"]==1)
	{?>
    <span class="formtitle">Sales Pipeline Details</span><br /><br/><?php
regform_trs_edit_details("sales",$producer?$producer["salesdetails"]:false,$producer["id"]);
	}
	if($producer["dm_status"]==1)
	{?>
    <span class="formtitle">Sales Pipeline Details</span><br /><br/><?php
regform_trs_edit_details("dimin",$producer?$producer["dimindetails"]:false,$producer["id"]);
	}
	
?>  
<?php
if($public)
{
?>
	<br /><span class="formtitle">Your Logos</span><br /><br />
<?php
}


	if(!empty($producer["identificationmarks"]) && $public)
	{
		foreach($producer["identificationmarks"] as $identificationmark)
		{
?>
<!--div class="box"-->s
<?php
			echo("<img style='display:inline-block; width:120px; float:left; margin-right:1em;' class=\"editlogo\" src=\"public_image_access.php?imageid=" . $identificationmark["id"] . "\">");
			if(!$public)
				check_box_with_label("Delete this logo", "delete_logo_" . $identificationmark["id"]); ?><?php
?>
<!--/div-->
<?php
		}
?>
<br />
<?php
	file_upload("Additional Company Logo", "produceridentificationmark"); ?><br /><?php

	}
	elseif($public)
		file_upload("Company Logo", "produceridentificationmark"); ?><br /><?php


?>


<?php
	if($public)
	{
?>
	<label for="additional">Additional Information: </label><textarea cols="45" rows="6"name="additional"><?php echo $_POST["additional"]; ?></textarea><br /><br />
<?php
	}
	
?>

	<input type="submit" value="save" id="save_tp" name="save_tp">		
</form>
<?php
}
function get_target_producers($year, $registration_number = false, $organisation_name = false, $from=0,$charfrom='', $count=999999)
{

	if($registration_number)
		$where = "and registration_number like \"$registration_number%\" ";

	if($organisation_name)
		$where .= "and name like \"%$organisation_name%\" ";

	if(empty($where))
		$where = "";

	$result = query("select producer.id, producer.registration_number, producer.name,
				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type
			from producer, producer_year, year  
							where producer.id = producer_year.producerid and
							producer_year.yearid = year.id and tp_status=1 and sp_status=0 and
							year.year = $year  and 
							producer.name like '$charfrom%'
							$where order by producer.name");

	$r = false;
	$i = 0;

	while($row = fetch_row($result))
	{

		$pagination[$i] = $row[2];

		if($i >= $from && $i < ($from + $count))
		{
			$r["producers"][$i]["id"			] = $row[0];
			$r["producers"][$i]["registration_number"	] = $row[1];
			$r["producers"][$i]["organisation_name"		] = $row[2];
			$r["producers"][$i]["b2b"			] = $row[3];
			$r["producers"][$i]["b2c"			] = $row[4];
			$r["producers"][$i]["quarterly_monthly"		] = $row[5];

		}

		$i++; 
	}



	$_block_start = 0;
	$j = 0;

	while($_block_start < count($pagination))
	{

		if($_block_start + $count >= count($pagination))
			$_block_end = count($pagination) -1;
		else
			$_block_end = $_block_start + $count -1;


		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
		$blocks[$j]["from"		] = $_block_start;

		$j++;

		if(($from+$count) % $count != 0 && $_block_end == $count)
			$_block_start = ($from+$count) % $count;
		else
			$_block_start = $_block_end + 1;
	}

	if(is_array($blocks))
	{
		foreach($blocks as $k => $block)
		{
			if($k != 0)
				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
			else
				$start_down = "";
	
			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
	
			if(strlen($start_down) > strlen($start_up))
				$blocks[$k]["start"	] = strtolower($start_down);
			else
				$blocks[$k]["start"	] = strtolower($start_up);
	
			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
	
			if($k != count($blocks)-1)
				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
			else
				$end_up = "";

			if(strlen($end_down) > strlen($end_up))
				$blocks[$k]["end"	] = strtolower($end_down);
			else
				$blocks[$k]["end"	] = strtolower($end_up);
		}
	}
	else
		$blocks = array();

	$r["total_matched"] = count($pagination);
	$r["pagination"] = $blocks;
	return($r);
}
function get_dimin_producers($year, $registration_number = false, $organisation_name = false, $from=0,$charfrom='', $count=999999)
{

	if($registration_number)
		$where = "and registration_number like \"$registration_number%\" ";

	if($organisation_name)
		$where .= "and name like \"%$organisation_name%\" ";

	if(empty($where))
		$where = "";

	$result = query("select producer.id, producer.registration_number, producer.name,
				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type
			from producer, producer_year, year  
							where producer.id = producer_year.producerid and
							producer_year.yearid = year.id and dm_status=1 and tp_status=0 and sp_status=0 and
							year.year = $year  and 
							producer.name like '$charfrom%'
							$where order by producer.name");

	$r = false;
	$i = 0;

	while($row = fetch_row($result))
	{

		$pagination[$i] = $row[2];

		if($i >= $from && $i < ($from + $count))
		{
			$r["producers"][$i]["id"			] = $row[0];
			$r["producers"][$i]["registration_number"	] = $row[1];
			$r["producers"][$i]["organisation_name"		] = $row[2];
			$r["producers"][$i]["b2b"			] = $row[3];
			$r["producers"][$i]["b2c"			] = $row[4];
			$r["producers"][$i]["quarterly_monthly"		] = $row[5];

		}

		$i++; 
	}



	$_block_start = 0;
	$j = 0;

	while($_block_start < count($pagination))
	{

		if($_block_start + $count >= count($pagination))
			$_block_end = count($pagination) -1;
		else
			$_block_end = $_block_start + $count -1;


		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
		$blocks[$j]["from"		] = $_block_start;

		$j++;

		if(($from+$count) % $count != 0 && $_block_end == $count)
			$_block_start = ($from+$count) % $count;
		else
			$_block_start = $_block_end + 1;
	}

	if(is_array($blocks))
	{
		foreach($blocks as $k => $block)
		{
			if($k != 0)
				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
			else
				$start_down = "";
	
			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
	
			if(strlen($start_down) > strlen($start_up))
				$blocks[$k]["start"	] = strtolower($start_down);
			else
				$blocks[$k]["start"	] = strtolower($start_up);
	
			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
	
			if($k != count($blocks)-1)
				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
			else
				$end_up = "";

			if(strlen($end_down) > strlen($end_up))
				$blocks[$k]["end"	] = strtolower($end_down);
			else
				$blocks[$k]["end"	] = strtolower($end_up);
		}
	}
	else
		$blocks = array();

	$r["total_matched"] = count($pagination);
	$r["pagination"] = $blocks;
	return($r);
}
function get_deminimis_producers($year, $registration_number = false, $organisation_name = false, $from=0,$charfrom='', $count=999999)
{

	if($registration_number)
		$where = "and registration_number like \"$registration_number%\" ";

	if($organisation_name)
		$where .= "and name like \"%$organisation_name%\" ";

	if(empty($where))
		$where = "";

	$result = query("select producer.id, producer.registration_number, producer.name,
				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type
			from producer, producer_year, year  
							where producer.id = producer_year.producerid and
							producer_year.yearid = year.id and dm_status=1 and tp_status=0 and sp_status=0 and
							year.year = $year  and 
							producer.name like '$charfrom%'
							$where order by producer.name");

	$r = false;
	$i = 0;

	while($row = fetch_row($result))
	{

		$pagination[$i] = $row[2];

		if($i >= $from && $i < ($from + $count))
		{
			$r["producers"][$i]["id"			] = $row[0];
			$r["producers"][$i]["registration_number"	] = $row[1];
			$r["producers"][$i]["organisation_name"		] = $row[2];
			$r["producers"][$i]["b2b"			] = $row[3];
			$r["producers"][$i]["b2c"			] = $row[4];
			$r["producers"][$i]["quarterly_monthly"		] = $row[5];

		}

		$i++; 
	}



	$_block_start = 0;
	$j = 0;

	while($_block_start < count($pagination))
	{

		if($_block_start + $count >= count($pagination))
			$_block_end = count($pagination) -1;
		else
			$_block_end = $_block_start + $count -1;


		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
		$blocks[$j]["from"		] = $_block_start;

		$j++;

		if(($from+$count) % $count != 0 && $_block_end == $count)
			$_block_start = ($from+$count) % $count;
		else
			$_block_start = $_block_end + 1;
	}

	if(is_array($blocks))
	{
		foreach($blocks as $k => $block)
		{
			if($k != 0)
				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
			else
				$start_down = "";
	
			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
	
			if(strlen($start_down) > strlen($start_up))
				$blocks[$k]["start"	] = strtolower($start_down);
			else
				$blocks[$k]["start"	] = strtolower($start_up);
	
			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
	
			if($k != count($blocks)-1)
				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
			else
				$end_up = "";

			if(strlen($end_down) > strlen($end_up))
				$blocks[$k]["end"	] = strtolower($end_down);
			else
				$blocks[$k]["end"	] = strtolower($end_up);
		}
	}
	else
		$blocks = array();

	$r["total_matched"] = count($pagination);
	$r["pagination"] = $blocks;
	return($r);
}

function get_sales_producers($year, $registration_number = false, $organisation_name = false, $from=0,$charfrom='', $count=999999)
{

	if($registration_number)
		$where = "and registration_number like \"$registration_number%\" ";

	if($organisation_name)
		$where .= "and name like \"%$organisation_name%\" ";

	if(empty($where))
		$where = "";

	$result = query("select producer.id, producer.registration_number, producer.name,
				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type 
			from producer, producer_year, year  
							where producer.id = producer_year.producerid and
							producer_year.yearid = year.id and sp_status=1 and
							year.year = $year  and 
							producer.name like '$charfrom%'
							$where order by producer.name");

	$r = false;
	$i = 0;

	while($row = fetch_row($result))
	{

		$pagination[$i] = $row[2];

		if($i >= $from && $i < ($from + $count))
		{
			$r["producers"][$i]["id"			] = $row[0];
			$r["producers"][$i]["registration_number"	] = $row[1];
			$r["producers"][$i]["organisation_name"		] = $row[2];
			$r["producers"][$i]["b2b"			] = $row[3];
			$r["producers"][$i]["b2c"			] = $row[4];
			$r["producers"][$i]["quarterly_monthly"		] = $row[5];

		}

		$i++; 
	}

	$_block_start = 0;
	$j = 0;

	while($_block_start < count($pagination))
	{

		if($_block_start + $count >= count($pagination))
			$_block_end = count($pagination) -1;
		else
			$_block_end = $_block_start + $count -1;


		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
		$blocks[$j]["from"		] = $_block_start;

		$j++;

		if(($from+$count) % $count != 0 && $_block_end == $count)
			$_block_start = ($from+$count) % $count;
		else
			$_block_start = $_block_end + 1;
	}

	if(is_array($blocks))
	{
		foreach($blocks as $k => $block)
		{
			if($k != 0)
				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
			else
				$start_down = "";
	
			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
	
			if(strlen($start_down) > strlen($start_up))
				$blocks[$k]["start"	] = strtolower($start_down);
			else
				$blocks[$k]["start"	] = strtolower($start_up);
	
			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
	
			if($k != count($blocks)-1)
				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
			else
				$end_up = "";

			if(strlen($end_down) > strlen($end_up))
				$blocks[$k]["end"	] = strtolower($end_down);
			else
				$blocks[$k]["end"	] = strtolower($end_up);
		}
	}
	else
		$blocks = array();

	$r["total_matched"] = count($pagination);
	$r["pagination"] = $blocks;
	return($r);
}
function get_producers($year, $registration_number = false, $organisation_name = false, $from=0,$charfrom='', $count=999999,$member = false,$quarter = false)
{
if($charfrom==1)
$charfrom='';
	if($registration_number)
		$where = "and registration_number like \"$registration_number%\" ";

	if($organisation_name)
		$where .= "and name like \"%$organisation_name%\" ";
if($quarter!=0)
	{
	if($member=='NC')
		$where .= "and cq".$quarter." =0" ;
		
	if($member=='NA')
		$where .= "and cq".$quarter." =1 and aq".$quarter." =0" ;
		
	if($member=='FA')
		$where .= "and cq".$quarter." =1 and aq".$quarter." =1" ;
	}


	if(empty($where))
		$where = "";

	$result = query("select producer.id, producer.registration_number, producer.name,
				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type ,producer.crm_status,producer.cq1,producer.cq2,producer.cq3,producer.cq4,producer.aq1,producer.aq2,producer.aq3,producer.aq4
			from producer, producer_year, year  
							where producer.id = producer_year.producerid and
							producer_year.yearid = year.id and
							year.year = $year  and 
							producer.name like '$charfrom%'
							$where order by producer.name");

	$r = false;
	$i = 0;

	while($row = fetch_row($result))
	{

		$pagination[$i] = $row[2];

		if($i >= $from && $i < ($from + $count))
		{
			$r["producers"][$i]["id"			] = $row[0];
			$r["producers"][$i]["registration_number"	] = $row[1];
			$r["producers"][$i]["organisation_name"		] = $row[2];
			$r["producers"][$i]["b2b"			] = $row[3];
			$r["producers"][$i]["b2c"			] = $row[4];
			$r["producers"][$i]["quarterly_monthly"		] = $row[5];
			
			$r["producers"][$i]["crm_status"		] = $row[6];


$r["producers"][$i]["cq1"		] = $row[7];
$r["producers"][$i]["cq2"		] = $row[8];
$r["producers"][$i]["cq3"		] = $row[9];
$r["producers"][$i]["cq4"		] = $row[10];
$r["producers"][$i]["aq1"		] = $row[11];
$r["producers"][$i]["aq2"		] = $row[12];
$r["producers"][$i]["aq3"		] = $row[13];
$r["producers"][$i]["aq4"		] = $row[14];

		}

		$i++; 
	}

	$_block_start = 0;
	$j = 0;

	while($_block_start < count($pagination))
	{

		if($_block_start + $count >= count($pagination))
			$_block_end = count($pagination) -1;
		else
			$_block_end = $_block_start + $count -1;


		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
		$blocks[$j]["from"		] = $_block_start;

		$j++;

		if(($from+$count) % $count != 0 && $_block_end == $count)
			$_block_start = ($from+$count) % $count;
		else
			$_block_start = $_block_end + 1;
	}

	if(is_array($blocks))
	{
		foreach($blocks as $k => $block)
		{
			if($k != 0)
				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
			else
				$start_down = "";
	
			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
	
			if(strlen($start_down) > strlen($start_up))
				$blocks[$k]["start"	] = strtolower($start_down);
			else
				$blocks[$k]["start"	] = strtolower($start_up);
	
			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
	
			if($k != count($blocks)-1)
				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
			else
				$end_up = "";

			if(strlen($end_down) > strlen($end_up))
				$blocks[$k]["end"	] = strtolower($end_down);
			else
				$blocks[$k]["end"	] = strtolower($end_up);
		}
	}
	else
		$blocks = array();

	$r["total_matched"] = count($pagination);
	$r["pagination"] = $blocks;
	return($r);
}

//function get_producers($year, $registration_number = false, $organisation_name = false, $from=0, $count=999999)
//{
//
//	if($registration_number)
//		$where = "and registration_number like \"$registration_number%\" ";
//
//	if($organisation_name)
//		$where .= "and name like \"%$organisation_name%\" ";
//
//	if(empty($where))
//		$where = "";
//
//	$result = query("select producer.id, producer.registration_number, producer.name,
//				producer.obligation_type_b2b, producer.obligation_type_b2c, producer_year.return_type
//			from producer, producer_year, year  
//							where producer.id = producer_year.producerid and
//							producer_year.yearid = year.id and
//							year.year = $year 
//							$where order by producer.name");
//
//	$r = false;
//	$i = 0;
//
//	while($row = fetch_row($result))
//	{
//
//		$pagination[$i] = $row[2];
//
//		if($i >= $from && $i < ($from + $count))
//		{
//			$r["producers"][$i]["id"			] = $row[0];
//			$r["producers"][$i]["registration_number"	] = $row[1];
//			$r["producers"][$i]["organisation_name"		] = $row[2];
//			$r["producers"][$i]["b2b"			] = $row[3];
//			$r["producers"][$i]["b2c"			] = $row[4];
//			$r["producers"][$i]["quarterly_monthly"		] = $row[5];
//
//		}
//
//		$i++; 
//	}
//
//	$_block_start = 0;
//	$j = 0;
//
//	while($_block_start < count($pagination))
//	{
//
//		if($_block_start + $count >= count($pagination))
//			$_block_end = count($pagination) -1;
//		else
//			$_block_end = $_block_start + $count -1;
//
//
//		$blocks[$j]["full_start"	] = $pagination[$_block_start	];
//		$blocks[$j]["full_end"		] = $pagination[$_block_end	]; 
//		$blocks[$j]["from"		] = $_block_start;
//
//		$j++;
//
//		if(($from+$count) % $count != 0 && $_block_end == $count)
//			$_block_start = ($from+$count) % $count;
//		else
//			$_block_start = $_block_end + 1;
//	}
//
//	if(is_array($blocks))
//	{
//		foreach($blocks as $k => $block)
//		{
//			if($k != 0)
//				$start_down = first_diff_char($blocks[$k-1]["full_end"], $block["full_start"]);
//			else
//				$start_down = "";
//	
//			$start_up	= first_diff_char($block["full_end"], $block["full_start"]);
//	
//			if(strlen($start_down) > strlen($start_up))
//				$blocks[$k]["start"	] = strtolower($start_down);
//			else
//				$blocks[$k]["start"	] = strtolower($start_up);
//	
//			$end_down	= first_diff_char($block["full_start"], $block["full_end"]);
//	
//			if($k != count($blocks)-1)
//				$end_up	= first_diff_char($blocks[$k+1]["full_start"], $block["full_end"]);
//			else
//				$end_up = "";
//
//			if(strlen($end_down) > strlen($end_up))
//				$blocks[$k]["end"	] = strtolower($end_down);
//			else
//				$blocks[$k]["end"	] = strtolower($end_up);
//		}
//	}
//	else
//		$blocks = array();
//
//	$r["total_matched"] = count($pagination);
//	$r["pagination"] = $blocks;
//	return($r);
//}



//end producer

function first_diff_char($comp, $string)
{
	$i = 0;
	$r = "";
	while($i < strlen($string))	
	{
		$r .= $string[$i];

		if(strlen($comp) < $i)
			break;

		if($string[$i] != $comp[$i])
			break;

		$i++;
	}

	return($r);	
}

function valid_year($year)
{
	$result = query("select year from year where year=$year");

	if(num_rows($result) != 0)
		return(true);
	else
		return(false);

}

function delete_producer_from_year($id, $year)
{
/// Grrr! No InnoDB or multi table deletes on this server.

	$result = query("select id from year where year=$year");

	$row = fetch_row($result);

	if(!is_array($row))
		die("Error: Could not find year");

	$yearid = $row[0];

// If it is the last remaining year for this producer, remove everything
	
	$result = query("select count(*) from producer_year where producerid = $id");

	$row = fetch_row($result);

	if($row[0] < 2)
	{
// Delete the producer and their contact details

		$row = fetch_row(query("select 	daytoday_contact_detailsid, 
						enforcement_contact_detailsid,emergency_contact_detailsid

					from producer where id = $id"));

		if(!$row)
			die("Error: Could not retrieve producer details in order to delete contacts");

		query("delete from contact_details where id = " . $row[0] . " or id = " . $row[1]);
		query("delete from producer where id=$id");

// Delete from the partnership tables

		$row = fetch_row(query("select id from partnership where partnership.producerid = $id"));

		if($row)
		{
			$partnershipid = $row[0];
			query("delete from partnership_list where partnershipid = $partnershipid");
			query("delete from partnership where producerid = $id");
		}

// Delete from company tables
			
		query("delete from company where producerid = $id");

// Delete any identification marks

		$result = query("select id from producer_identification_mark where producerid=$id");

		while($row = fetch_row($result))
			unlink(LOGO_PATH . "/" . $row[0] . ".jpeg");

		query("delete from producer_identification_mark where producerid = $id");
	}

// Delete the producer from the year

	query("delete from producer_year where producerid=$id and yearid=$yearid");

// Delete the producers returns

	query("delete from `return_monthly` where producerid=$id and yearid=$yearid");
	query("delete from `return_quarterly` where producerid=$id and yearid=$yearid");

}

function producer_in_year($producerid, $year)
{
	$result = query("select count(*) from producer, year, producer_year where producer.id = producer_year.producerid and year.id = producer_year.yearid and year.year=$year and producer.id = $producerid");

	$row = fetch_row($result);

	if($row[0] == 1)
		return(true);
	else
		return(false);

}
 /*?>function crmAssignLevels($producerid ){

if($_POST['acrm']=='on')
{
$acrm=1;
}
if($_POST['dcrm']=='on')
{
$dcrm=1;
}
         if($_POST['tpipeline']=='on')
          {
           $tpipeline=1;
	                    if($_POST['apipeline']=='on')
	                     {
	                     $apipeline=1;
	                     $tpipeline=0;
	                                    if($_POST['amember']=='on')
	                                        {
	                                        $amember=1;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
	                                        $amember=0;
	                      }
	                      else
	                       $apipeline=0;
	       }
            else
            $tpipeline=0;
if($_POST['apipeline']=='on')
	                     {
	                     $apipeline=1;
	                     $tpipeline=0;
	                                    if($_POST['amember']=='on')
	                                        {
	                                        $amember=1;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
	                                        $amember=0;

	                      }
	                      else
	                       $apipeline=0;


if($dcrm==1 )
{

}
if($tpipeline==1 )
{
		include(ADMIN_HTDOCS . "/header.php");
?>
	<a href="producers.php">Home</a>
<?php
	target_form($producerid, "save_tp", true);

}

if($apipeline==1 )
{
		include(ADMIN_HTDOCS . "/header.php");
?>
	<a href="producers.php">Home</a>
<?php
	target_form($producerid, "save_sp", true);


}	
	


}<?php */
function crmAssignLevels($producerid ){

if($_POST['acrm']=='on')
{
$acrm=1;
}
if($_POST['dcrm']=='on')
{
$dcrm=1;
}
         if($_POST['tpipeline']=='on')
          {
           $tpipeline=1;
	                    if($_POST['apipeline']=='on')
	                     {
	                     $apipeline=1;
	                     $tpipeline=0;
	                                    if(($_POST['weee']=='on') or ($_POST['northern']=='on'))
	                                        {
																						if($_POST['weee']=='on')
	                                        $weee=1;
											else
	                                        $weee=0;
											 if($_POST['northern']=='on')
	                                        $northern=1;
											else
	                                        $northern=0;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
											{
	                                        $weee=0;
											$northern=0;
											}
	                      }
	                      else
	                       $apipeline=0;
	       }
            else
            $tpipeline=0;
			if($_POST['diminimus']=='on')
          {
           $diminimus=1;
	                    if($_POST['apipeline']=='on')
	                     {
	                     $apipeline=1;
	                     $tpipeline=0;
	                                    if(($_POST['weee']=='on') or ($_POST['northern']=='on'))
	                                        {
																						if($_POST['weee']=='on')
	                                        $weee=1;
											else
	                                        $weee=0;
											 if($_POST['northern']=='on')
	                                        $northern=1;
											else
	                                        $northern=0;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
											{
	                                        $weee=0;
											$northern=0;
											}
	                      }
	                      else
	                       $apipeline=0;
	       }
            else
            $diminimus=0;
if($_POST['apipeline']=='on')
	                     {
	                     $apipeline=1;
	                     $tpipeline=0;
	                                    if($_POST['weee']=='on' or $_POST['northern']=='on' )
	                                        {
											 if($_POST['weee']=='on')
	                                        $weee=1;
											else
	                                        $weee=0;
											 if($_POST['northern']=='on')
	                                        $northern=1;
											else
	                                        $northern=0;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
											{
	                                        $weee=0;
											$northern=0;
											}
	                      }
	                      else
	                       $apipeline=0;
 if(($_POST['weee']=='on') or ($_POST['northern']=='on'))
	                                        {
											
											if($_POST['weee']=='on')
	                                        $weee=1;
											else
	                                        $weee=0;
											 if($_POST['northern']=='on')
	                                        $northern=1;
											else
	                                        $northern=0;
	                                        $apipeline=0;
	                                        $tpipeline=0;
	                                        }
	                                        else
											{
	                                        $weee=0;
											$northern=0;
											}
if($weee==1 or $northern==1)
{ 
$sel_cou=query("select * from  producer_identification_mark where producerid	=$producerid");
$row=num_rows($sel_cou);
if($weee==1 and $northern==0)
{
query("update producer set tp_status=0, sp_status=0, dm_status=0, weee_status=1 where id = $producerid ");

save_add_weee("weee",1,$row,$producerid);
}
if($northern==1 and $weee==0)
{

query("update producer set tp_status=0, sp_status=0, dm_status=0, weee_status=0, northern_status=1 where id = $producerid ");
save_add_weee("northern",1,$row,$producerid);
}
if($northern==1 and $weee==1)
{

query("update producer set tp_status=0, sp_status=0, weee_status=1, northern_status=1 where id = $producerid ");
save_add_weee("northern",2,$row,$producerid);
save_add_weee("weee",2,$row,$producerid);

}
}
if($dcrm==1 )
{
	$seltype=query("select tp_status ,sp_status, dm_status from producer  where id = $producerid ");
$fettype=fetch_row($seltype);
if($fettype[0]==0 and $fettype[1]==1 and $fettype[2]==0)
$type="S";
if($fettype[0]==1 and $fettype[1]==0 and $fettype[2]==0)
$type="T";
if($fettype[0]==0 and $fettype[1]==0 and $fettype[2]==1)
$type="D";

	$ddate=date('d/m/y');
	 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$dtime=$hrs .":". $min;

	$dtotal="CRM-".$ddate." - ".$dtime.">downgraded to CRM";
$dseltp=query("select notes from crm_target_pipeline where producer_id = $producerid and member_status='$type' ");
$dfettp=fetch_row($dseltp);
 $dtpdate=$dfettp[0]."\n".$dtotal;
query("update audit  set general_comments_txt='".addslashes($dtpdate)."' where producerid = $producerid ");


query("update producer set tp_status=0, sp_status=0, dm_status=0 where id = $producerid ");

query("delete from crm_target_pipeline where producer_id = $producerid and member_status='T'");
query("delete from crm_target_pipeline where producer_id = $producerid and member_status='S'");
query("delete from crm_target_pipeline where producer_id = $producerid and member_status='D'");
return true;
}
//if($tpipeline==1 )
//{
//	$year = $_SESSION["year"];
//	if(empty($year))
//		die("Error: Couldn't determine which year to use");
//query("insert  crm_target_pipeline set  producer_id = $producerid ,member_status='T' "); 
//query("update producer set tp_status=1 where id = $producerid "); 
//return(true);
//}
//
//if($apipeline==1 )
//{
//	$year = $_SESSION["year"];
//	if(empty($year))
//		die("Error: Couldn't determine which year to use");
//
//query("insert  crm_target_pipeline set  producer_id = $producerid ,member_status='S'  "); 
//query("update producer set sp_status=1 where id = $producerid "); 
//query("update producer set tp_status=0 where id = $producerid ");
//return(true);
//}	
if($tpipeline==1 )
{
	$date=date('d/m/y');
	 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$time=$hrs .":". $min;

	$total=$date."-".$time;
	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
query("insert  crm_target_pipeline set  producer_id = $producerid ,member_status='T',tp_date='$total' "); 
query("update producer set tp_status=1 where id = $producerid "); 
return(true);
}
if($diminimus==1 )
{
	$date=date('d/m/y');
	 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$time=$hrs .":". $min;

	$total=$date."-".$time;
	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");
query("insert  crm_target_pipeline set  producer_id = $producerid ,member_status='D',tp_date='$total' "); 
query("update producer set dm_status=1 where id = $producerid "); 
return(true);
}

if($apipeline==1 )
{
	$adate=date('d/m/y');
	 $hr=date("H"); $hrs=$hr+7;  $min=date("i"); 
$atime=$hrs .":". $min;

	$atotal=$adate."-".$atime;
	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");

$seltp=query("select noticestatus, 
				forename, 
				current_pcs, 
				account_manager, 
				last_contact, 
				next_contact, 
				packaging, 
				battery,tp_date,note_tp_date,notes from crm_target_pipeline where producer_id = $producerid and member_status='T' ");
$fettp=fetch_row($seltp);
$noticestatus= $fettp[0];
				$forename= $fettp[1];
				$current_pcs= $fettp[2];
				$account_manager= $fettp[3];
				$last_contact= $fettp[4];
				$next_contact= $fettp[5];
				$packaging= $fettp[6];
				$battery=$fettp[7];
$tpdate=$fettp[8];
$tpupdate=$fettp[10];

$seltp_st=query("select tp_status from producer where id = $producerid ");
$fettp_sta=fetch_row($seltp_st);
$tp_status=$fettp_sta[0];
if($tp_status==0)
{
query("insert  crm_target_pipeline set  producer_id = $producerid ,member_status='S', sp_date='$atotal',tp_date='$tpdate' , note_tp_date='$tpupdate'  "); 

}
else
{
query("insert  crm_target_pipeline set  producer_id = $producerid ,noticestatus='".addslashes($noticestatus)."' ,forename='".addslashes($forename)."',current_pcs='".addslashes($current_pcs)."',account_manager='".addslashes($account_manager)."',last_contact='$last_contact',next_contact='$next_contact',packaging='".addslashes($packaging)."',battery='".addslashes($battery)."',member_status='S', sp_date='".addslashes($atotal)."',tp_date='".addslashes($tpdate)."' , note_tp_date='".addslashes($tpupdate)."'"); 

}
query("update producer set sp_status=1 where id = $producerid "); 
query("update producer set tp_status=0 where id = $producerid ");
return(true);
}	


}
function add_partners_weee($producer_id)
{
	global $_POST;

	mysql_query("insert ignore into partnership set producerid = $producer_id");

	$exploded = explode(",", _addslashes($_POST["partners"]));

	foreach($exploded as $explodee)
	{
		if(!empty($explodee))
		{
			$explodee = _addslashes(ltrim($explodee));

			mysql_query(	"insert into partnership_list (partner, partnershipid) " .
				"select \"$explodee\", id from partnership where producerid = $producer_id");
		}
	}
}
function save_add_weee($member,$num,$row,$producer_id)
{

 if($member=="weee")
 {
 mysql_select_db("weeelight_returns");
 if(!check_unique_registration_number(_addslashes($_POST["registrationnumber"]), $_POST["action"]=="save_edit"?$_POST["id"]:false) && $_POST["registrationnumber"] != "")
		{
		$errors[] = "Registration Number must be unique";
		
		}
	if(!check_unique_name(_addslashes($_POST["organisationname"]), $_POST["action"]=="save_edit"?$_POST["id"]:false))
		{
		$errors[] = "Organisation Name must be unique";

		}
		if(is_array($errors))
	{
		 mysql_select_db("crm_master");
		query("update producer set tp_status=0, sp_status=0, dm_status=0, weee_status=0 where id = $producer_id ");

		include(ADMIN_HTDOCS . "/header.php");

		foreach($errors as $error)
		{
?>
	<span class="error">Error: <?php echo $error; ?></span><br />
<?php
		}
		producer_form(false, $_POST["action"], false);
		exit;
	}
 }
  if($member=="northern")
 {
 mysql_select_db("northerncompliance_returns");
if(!check_unique_registration_number(_addslashes($_POST["registrationnumber"]), $_POST["action"]=="save_edit"?$_POST["id"]:false) && $_POST["registrationnumber"] != "")
		{
		$errors[] = "Registration Number must be unique";
        }
	if(!check_unique_name(_addslashes($_POST["organisationname"]), $_POST["action"]=="save_edit"?$_POST["id"]:false))
		{
		$errors[] = "Organisation Name must be unique";
        }
		if(is_array($errors))
	    {
			 mysql_select_db("crm_master");
		query("update producer set tp_status=0, sp_status=0, dm_status=0,  northern_status=0 where id = $producer_id ");

		include(ADMIN_HTDOCS . "/header.php");

		foreach($errors as $error)
		{
?>
	<span class="error">Error: <?php echo $error; ?></span><br />
<?php
		}
		producer_form(false, $_POST["action"], false);
		exit;
	}
 }

 	$year = $_SESSION["year"];
	if(empty($year))
		die("Error: Couldn't determine which year to use");

 $producer_sql = producer_crm_to_wn_sql($member,$num);


	 $enforcement_contact_sql1 = contact_member_post_to_sql("enforcement");

	mysql_query("insert into contact_details set $enforcement_contact_sql1");

	$enforcement_contact_id1 = last_id();

	if($_POST["copy_contact_details"] == "on")

		$daytoday_contact_id1 = $enforcement_contact_id1;

	else
	{
		$daytoday_contact_sql1 =  contact_member_post_to_sql("daytoday");

		query("insert into contact_details set $daytoday_contact_sql1");

		$daytoday_contact_id1 = last_id();
	} 
 
	 $sql = "insert into producer set $producer_sql,
					organisation_contact_detailsid = $daytoday_contact_id1, 
					 correspondence_contact_detailsid= $enforcement_contact_id1";


mysql_query($sql);

$imgarray=Array();
$letarray=Array();

	 $producerid = last_id();
	 if($row>0)
	 {
		  mysql_select_db("crm_master");
		
		$sel_cou1=query("select * from  producer_identification_mark where producerid=$producer_id"); 
	$c=0;
		while($fet_row=fetch_row($sel_cou1))
		{
			$imgarray[$c]=$fet_row[2];
			$letarray[$c]=$fet_row[3];
			$c++;
		}
 $na=implode("abababa",$imgarray);
 $le=implode("abababa",$letarray);
 
 if($member=="weee")
 {
 mysql_select_db("weeelight_returns");
 $exit_name= explode("abababa",$na);
  $exit_let=explode("abababa",$le);
  $count=count($exit_name);
 for($m=0;$m<$count;$m++)
 {
	 $imagename=$exit_name[$m];
	 $charno=$exit_let[$m];
	query("insert into producer_identification_mark (producerid,image_name,letter) values($producerid,'$imagename',$charno)");
 $imageid = last_id(); 

copy("E:/xampp/htdocs/041w-weeeco-tes/clients/crm/admin/logos/$imagename.jpeg", "E:/xampp/htdocs/041w-weeeco-tes/clients/weee_light/admin/logos/$imageid.jpeg");
 
 

 }
 }
  if($member=="northern")
 {
 mysql_select_db("northerncompliance_returns");
 $exit_name= explode("abababa",$na);
  $exit_let=explode("abababa",$le);
  $count=count($exit_name);
 for($m=0;$m<$count;$m++)
 {
	 $imagename=$exit_name[$m];
	 $charno=$exit_let[$m];
	query("insert into producer_identification_mark (producerid,image_name,letter) values($producerid,'$imagename',$charno)");
 $imageid = last_id(); 

copy("E:/xampp/htdocs/041w-weeeco-tes/clients/crm/admin/logos/$imagename.jpeg", "E:/xampp/htdocs/041w-weeeco-tes/clients/northern_compliance/admin/logos/$imageid.jpeg");

 }
 }

	 }
	


	if($_POST["producertype"] == "company")
	{
	$companynumber = _addslashes($_POST["companynumber"]);

	mysql_query("insert into 
	
	company set number = \"$companynumber\", producerid = $producerid"); 
		}
	else
		add_partners_weee($producerid);

	mysql_query("insert ignore into year set year=\"$year\"");

	mysql_query("insert into producer_year (producerid, yearid) select $producerid, id from year where year=\"$year\"");

}




function producer_crm_to_wn_sql($member,$num)
{

	global $_POST;

	if($_POST["registrationnumber"] != "")
		$sql .= " registration_number  = \"" .  _addslashes($_POST["registrationnumber"	]) . "\",";
	else
		$sql .=" registration_number = NULL, ";

	$sql .= " name 	 	= \"" .  _addslashes($_POST["organisationname"	]) . "\"";
	$sql .= ", trading_name  	= \"" .  _addslashes($_POST["tradingname"		]) . "\"";
	$sql .= ", sic_code  		= \"" .  _addslashes($_POST["siccode"			]) . "\"";
	$sql .= ", annual_turnover  	= \"" .  _addslashes($_POST["annualturnover"		]) . "\"";
	$sql .= ", password  		= \"" .  _addslashes($_POST["password"			]) . "\"";

	if($_POST["vatregistered"] == "on")
		$sql .= ", vat_registered=1";
	else
		$sql .= ", vat_registered=0";
if(($member=="northern") and ($num==1))
 {
	
	
	
	 
	if($_POST["b2b"] == "on")

		$sql .= ", obligation_type_b2b=1";
	else
		$sql .= ", obligation_type_b2b=0";

	if($_POST["b2c"] == "on")
		$sql .= ", obligation_type_b2c=1";
	else
		$sql .= ", obligation_type_b2c=0";
		
}
		if(($member=="northern") and ($num==2))
 {
	if($_POST["b2b"] == "on")
		$sql .= ", obligation_type_b2b=1";
	else
		$sql .= ", obligation_type_b2b=0";

		$sql .= ", obligation_type_b2c=0";
}
		if(($member=="weee")  and ($num==1))
 {

	if($_POST["b2b"] == "on")
		$sql .= ", obligation_type_b2b=1";
	else
		$sql .= ", obligation_type_b2b=0";

	if($_POST["b2c"] == "on")
		$sql .= ", obligation_type_b2c=1";
	else
		$sql .= ", obligation_type_b2c=0";
}
		if(($member=="weee")  and ($num==2))
 {
		$sql .= ", obligation_type_b2b=0";

	if($_POST["b2c"] == "on")
		$sql .= ", obligation_type_b2c=1";
	else
		$sql .= ", obligation_type_b2c=0";
}
		if($member=="northern")
 {
	$sql .= " ,crm_status 	 	= \"" .  _addslashes(1) . "\"";
	 if($num==1)
	$sql .= ",status 	= \"" .  _addslashes(0) . "\"";
	 if($num==2)
	$sql .= ",status 	= \"" .  _addslashes(1) . "\"";
	
	
	
	}
	 if($member=="weee")
	 {
	$sql .= ", crm_status 	 	= \"" .  _addslashes(1) . "\"";
		 if($num==1)
	$sql .= ",status 	= \"" .  _addslashes(0) . "\"";
