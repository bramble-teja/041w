<?php
session_start();
include(FUNCTIONS_PATH);

$filter_reg = $_SESSION["filter_reg"];
$filter_name = $_SESSION["filter_name"];
$year = $_SESSION["year"];
$from = $_SESSION["from"];
$charfrom = $_SESSION["charfrom"];

unset($_SESSION["last_viewed_producers"]);

if(empty($year))
{
	$year = date("Y");
	$_SESSION["year"] = $year;
}

if(empty($from))
	$from = 0;

$count = PRODUCERS_PAGE_COUNT;

$producers_array = get_deminimis_producers($year, $filter_reg, $filter_name, $from,$charfrom, $count);

$producers = $producers_array["producers"];
$matched = $producers_array["total_matched"];
$pagination = $producers_array["pagination"];

include(ADMIN_HTDOCS . "/header_no_box.php");

?>
<div class="container">
<table width="740" style="font-size:13px;" border="1">
  <tr>
    <td width="117"><a href="/041w-weeeco-tes/clients/crm/admin/producers.php">CRM Database</a></td>
    <td width="98"><a href="/041w-weeeco-tes/clients/target_pipeline/admin/producers.php">Target Pipeline</a></td>
    <td width="104"><a href="/041w-weeeco-tes/clients/sales_pipeline/admin/producers.php">Sales Pipeline</a></td>
    <td width="153"><a href="/041w-weeeco-tes/clients/weee_light/admin/producers.php">WEEE Light Members</a></td>
    <td width="234"><a href="/041w-weeeco-tes/clients/northern_compliance/admin/producers.php">Northern Compliance Members</a></td>
   <?php /*?> <td width="117"><a href="/041w-weeeco-tes/clients/deminimus11/admin/producers.php">DiMinimus</a></td>
    <td width="117"><a href="/041w-weeeco-tes/clients/diminimus/admin/producers.php">DiMinimus1</a></td><?php */?>
    <td width="117"><a href="/041w-weeeco-tes/clients/de_minimis/admin/producers.php">De Minimis</a></td>
  </tr>
</table>

<h1><?php echo SCHEME_NAME; ?> <?php echo $_SESSION["year"]?" " . $_SESSION["year"]:""; ?></h1>
<form method="post" action="producers_submit.php" name="producers">
<?php /*?>
<?php year_combo("Year", "year", true, $year); ?>
<input type="submit" value="Change" name="action">
<br /><?php */?><br />
<table>
	<thead class="th_producers"><tr class="tabhead"><th/>
	<th>Company Name</th>
	<th>WEEE  Number</th>
	<th/><th/><th/><th/></tr></thead>
	<tr class="tabhead">
		<td/>
		<td class="td_name"><input type="text" name="filtername" value="<?php echo he_stripslashes($filter_name); ?>"></td>
		<td class="td_reg_number"><input type="text" name="filterreg" value="<?php echo he_stripslashes($filter_reg); ?>"></td>
		<td class="td_action"><input type="submit" name="action" value="Filter"></td>
        <td class="td_action"></td>
     
		<td />
		<td />
	</tr>
	<tr>
		<td colspan="3">
<?php if($from > 0){ ?><input type="image" alt="First" src="<?php echo ICON_URL; ?>first.png" value="First" name="first">
<input type="image" alt="Previous" src="<?php echo ICON_URL; ?>previous.png" value="Previous" name="previous"> <?php } ?>
<?php if($from + $count < $matched){ ?><input type="image" alt="Next" src="<?php echo ICON_URL; ?>next.png" value="Next" name="next">
<input type="image" alt="Last" src="<?php echo ICON_URL; ?>last.png" value="Last" name="last">
<?php } ?>

		</td>
		<td class="td_p1">P1 & 2</td>
	<!--	<td class="td_p2">P3</td>-->
		<td class="td_p4">P4</td>
        <td class="td_p4">Finance</td>
    </tr>
<?php

if($producers)
{
	$a=1;
	foreach($producers as $producer)
	{
		$_SESSION["last_viewed_producers"][] = $producer["id"];
	
?>
	<tr <?php echo ++$a%2?$alternate="class=\"alternate\"":"";
 ?>>
		<td class="td_select"><input type="checkbox" name="checked_<?php echo $producer["id"]; ?>"
			<?php echo $_SESSION["checked"][$producer["id"]]?"checked=\"checked\"":""; ?>></td>

		<td class="td_name"><?php echo $producer["organisation_name"]; ?></td>
		<td class="td_reg_number"><?php echo $producer["registration_number"]; ?></td>
		<td class="td_p1">
			<a href="producer_card.php?id=<?php echo $producer["id"]; ?>" title="Details"><img border="0" src="<?php echo ICON_URL; ?>details.png"></a>
			<a href="producers_submit.php?action=deldimin&id=<?php echo $producer["id"]; ?>&deleteyear=<?php echo $year; ?>"
				onclick="return confirm('Are you sure you want to delete <?php echo $producer['organisation_name']; ?> from the year <?php echo $year; ?>? Clicking OK will irrevocably remove all the producers return data for the year.');" title="Delete"><img border="0" src="<?php echo ICON_URL; ?>delete.png"></a>

		</td>
		<!--<td class="td_p2">
<?php
		if(($producer["b2b"] || $producer["b2c"])  && ($producer["weee_status"]==1 || $producer["northern_status"]==1))
		{
?>
			<!--<a href="returns_submit.php?id=<?php echo $producer["id"]; ?>&title=<?php echo $producer["organisation_name"]; ?>" title="Environment Agency returns">Returns</a>-->
             <a>Returns</a>
<?php
		}
		else
		{?>
		<a><del>Returns</del></a> <?php
		}
?>
		</td>-->
		<td class="td_p4">
			<a href="audit.php?id=<?php echo $producer["id"]; ?>" title="Audit"><img border="0" src="<?php echo ICON_URL; ?>audit.png"></a>

		</td>
        	<td class="td_p4" align="center">
			<a href="finance_card.php?id=<?php echo $producer["id"]; ?>" title="Finance"><img border="0" src="<?php echo ICON_URL; ?>finance.png"></a>

		</td>
        
	</tr>
<?php
	}
}

?>
</table>

<table>
<tr><td width="717">
<?php
	foreach($pagination as $block)
	{
?>
		<a onClick="document.producers.paginationfrom.value='<?php echo $block["from"]; ?>'; document.producers.submit();" onMouseOver="this.style.cursor='pointer';">
<?php
		echo("( " . $block["start"] . " - " . $block["end"] . " ) ");
?>
		</a>
<?php
	}

?><br/>
   </td></tr></table> 
<?php if($from > 0){ ?><input type="image" alt="First" src="<?php echo ICON_URL; ?>first.png" value="First" name="first">
<input type="image" alt="Previous" src="<?php echo ICON_URL; ?>previous.png" value="Previous" name="previous"> <?php } ?>
<?php if($from + $count < $matched){ ?><input type="image" alt="Next" src="<?php echo ICON_URL; ?>next.png" value="Next" name="next">
<input type="image" alt="Last" src="<?php echo ICON_URL; ?>last.png" value="Last" name="last">
<br />
<br />
<?php } ?>
<input type="hidden" name="paginationfrom" value="unset">
<input type="hidden" name="paginationfrom1" value="unset">
<br />

<input type="submit" value="Check None" name="action">
<input type="submit" value="Check All" name="action"><br /><br />
<a href="producer_csv.php">All Producers CSV</a><br />
<br />
</div>
<?php /*?><div class="container">
<b>Part 3</b><br />

<b>Scheme returns</b><br />

<input type="submit" value="Registrations XML" name="action"><br /><br />
<input type="submit" value="Registrations Validate" name="action"><br /><br />
<input type="submit" value="Registrations XML on screen" name="action"><br /><br />

<fieldset class='version13'>
<legend>Version 1.3</legend>

<?php

$scheme_return_types = get_scheme_return_types();

foreach($scheme_return_types as $scheme_return_type) {
?>
<a href="scheme_returns.php?type=<?php echo($scheme_return_type["id"]); ?>&title=<?php echo($scheme_return_type["name"]); ?> <?php echo($scheme_return_type["type"]); ?>">
	<?php echo($scheme_return_type["name"]); ?>
	<?php echo($scheme_return_type["type"]); ?></a>
<br />
<?php

}
?>
<br />
<!--input type="submit" value="Registrations XML" name="action"><br /><br />
<input type="submit" value="Registrations Validate" name="action"><br /><br />
<input type="submit" value="Registrations XML on screen" name="action"><br /><br /-->

<input type="submit" value="Returns XML Q1" name="action"> 
<input type="submit" value="Returns Validate Q1" name="action">  
<input type="submit" value="Returns XML on screen Q1" name="action"><br /><br />

<input type="submit" value="Returns XML Q2" name="action"> 
<input type="submit" value="Returns Validate Q2" name="action">  
<input type="submit" value="Returns XML on screen Q2" name="action"><br /><br />

<input type="submit" value="Returns XML Q3" name="action"> 
<input type="submit" value="Returns Validate Q3" name="action">  
<input type="submit" value="Returns XML on screen Q3" name="action"><br /><br />

<input type="submit" value="Returns XML Q4" name="action"> 
<input type="submit" value="Returns Validate Q4" name="action">  
<input type="submit" value="Returns XML on screen Q4" name="action"><br /><br />

</fieldset>

<!--fieldset class='version20'>
<legend>Version 2.0</legend>
<p style='font-size:150%; font-weight:bold; color:green;'>In Testing - Please use caution with 'v2.0'</p>

<?php

$scheme_return_types = get_scheme_return_types("2.0");

foreach($scheme_return_types as $scheme_return_type)
{
?>
<a href="scheme_returns2.php?type=<?php echo($scheme_return_type["id"]); ?>&title=<?php echo($scheme_return_type["name"]); ?> <?php echo($scheme_return_type["type"]); ?>">
	<?php echo($scheme_return_type["name"]); ?>
	<?php echo($scheme_return_type["type"]); ?></a>
<br />
<?php

}
?>
<br />

<input type="submit" value="Returns XML Q1 v2.0" name="action"> 
<input type="submit" value="Returns Validate Q1 v2.0" name="action">  
<input type="submit" value="Returns XML on screen Q1 v2.0" name="action"><br /><br />

<input type="submit" value="Returns XML Q2 v2.0" name="action"> 
<input type="submit" value="Returns Validate Q2 v2.0" name="action">  
<input type="submit" value="Returns XML on screen Q2 v2.0" name="action"><br /><br />

<input type="submit" value="Returns XML Q3 v2.0" name="action"> 
<input type="submit" value="Returns Validate Q3 v2.0" name="action">  
<input type="submit" value="Returns XML on screen Q3 v2.0" name="action"><br /><br />

<input type="submit" value="Returns XML Q4 v2.0" name="action"> 
<input type="submit" value="Returns Validate Q4 v2.0" name="action">  
<input type="submit" value="Returns XML on screen Q4 v2.0" name="action"><br /><br />

</fieldset-->

<fieldset class='version20'>
<legend>Version 2.0</legend>
<p style='font-size:150%; font-weight:bold; color:green;'>In Testing - Please use caution with 'v2.0'</p>

<?php

$scheme_return_types = get_scheme_return_types("2.0");

foreach($scheme_return_types as $scheme_return_type)
{

	if(strpos($scheme_return_type["name"],'ATF') > -1 || strpos($scheme_return_type["name"],'AE') > -1) {
		continue;
	}

	?>
	<a href="scheme_returns2.php?type=<?php echo($scheme_return_type["id"]); ?>&title=<?php echo($scheme_return_type["name"]); ?> <?php echo($scheme_return_type["type"]); ?>">
		<?php echo($scheme_return_type["name"]); ?>
		<?php echo($scheme_return_type["type"]); ?></a>
	<br />
	<?php

}
?>
<br />
<!--input type="submit" value="Registrations XML" name="action"><br /><br />
<input type="submit" value="Registrations Validate" name="action"><br /><br />
<input type="submit" value="Registrations XML on screen" name="action"><br /><br /-->

<input type="submit" value="Returns XML Q1 v2.0" name="action"><br /><br />
<input type="submit" value="Returns XML Q2 v2.0" name="action"><br /><br />
<input type="submit" value="Returns XML Q3 v2.0" name="action"> <br /><br />
<input type="submit" value="Returns XML Q4 v2.0" name="action"> <br /><br />


</fieldset>
<br/>
<table width="421" border="1">
  <tr>
    <td><b>Quarterly Invoicing Report</b><br />
    <br />
  <span style="width: 50px; margin-left: 50px;float: left">From:</span>
  <select name="quarter_start">
    <option value="1" selected="selected">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4">Quarter 4</option>
  </select>
  <br />
  <span style="width: 50px; margin-left: 50px; float: left;">Until:</span>
  <select name="quarter_end">
    <option value="1">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4" selected="selected">Quarter 4</option>
  </select>
  <br />
  <br />
  <input type="radio" name="invoice_type_filter" value="both" checked="checked"> 
  Show b2b and b2c<br />
  <input type="radio" name="invoice_type_filter" value="b2b"> 
  Show b2b only<br />
  <input type="radio" name="invoice_type_filter" value="b2c"> 
  Show b2c only<br />
  <br />
  <input type="submit" value="Download Quarterly Invoicing CSV" name="action">
</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
    <td>     <b>Monthly Invoicing Report</b><br />
  <br />
  <span style="width: 50px; margin-left: 50px;float: left">From:</span>
  <select name="month_start">
        <?php
	for($i=1; $i<13; $i++)
		if($i !== 1)
			echo("<option value=\"$i\">" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
		else
			echo("<option value=\"$i\" selected>" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
?>
    </select>
  <br />
  <span style="width: 50px; margin-left: 50px; float: left;">Until:</span>
  <select name="month_end">
        <?php
	for($i=1; $i<13; $i++)
		if($i !== 12)
			echo("<option value=\"$i\">" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
		else
			echo("<option value=\"$i\" selected>" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
?>
    </select>
  <br />
  <br />
  <input type="radio" name="invoice_type_filter" value="both" checked="checked"> 
  Show b2b and b2c<br />
  <input type="radio" name="invoice_type_filter" value="b2b"> 
  Show b2b only<br />
  <input type="radio" name="invoice_type_filter" value="b2c"> 
  Show b2c only<br />
  <br />
  <input type="submit" value="Download Monthly Invoicing CSV" name="action">
  
  
  
</td>
  </tr>
</table>

<br/>
<table width="305" border="1">
  <tr>
    <td width="295"><b>Quarterly &amp; Monthly Invoicing Report</b><br />
    <br />
  <span style="width: 50px; margin-left: 50px;float: left">From:</span>
  <select name="quarter_start">
    <option value="1" selected="selected">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4">Quarter 4</option>
  </select>
  <br />
  <span style="width: 50px; margin-left: 50px; float: left;">Until:</span>
  <select name="quarter_end">
    <option value="1">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4" selected="selected">Quarter 4</option>
  </select>
  <br />
  <br />
  <input type="radio" name="invoice_type_filter" value="both" checked="checked"> 
  Show b2b and b2c<br />
  <input type="radio" name="invoice_type_filter" value="b2b"> 
  Show b2b only<br />
  <input type="radio" name="invoice_type_filter" value="b2c"> 
  Show b2c only<br />
  <br />
  <input type="submit" value="Download Quarterly and Monthly Invoicing CSV" name="action">
</td></tr>
</table>



</div><?php */?>
<!--<div class="container">
<b>Part 3</b><br />

<b>Scheme returns</b><br /><?php

$scheme_return_types = get_scheme_return_types();

foreach($scheme_return_types as $scheme_return_type)
{
?>
<a href="scheme_returns.php?type=<?php echo($scheme_return_type["id"]); ?>&title=<?php echo($scheme_return_type["name"]); ?> <?php echo($scheme_return_type["type"]); ?>">
	<?php echo($scheme_return_type["name"]); ?>
	<?php echo($scheme_return_type["type"]); ?></a>
<br />
<?php

}
?>
<br />
<input type="submit" value="Registrations XML" name="action"><br /><br />
<input type="submit" value="Registrations Validate" name="action"><br /><br />
<input type="submit" value="Registrations XML on screen" name="action"><br /><br />

<input type="submit" value="Returns XML Q1" name="action"> 
<input type="submit" value="Returns Validate Q1" name="action">  
<input type="submit" value="Returns XML on screen Q1" name="action"><br /><br />

<input type="submit" value="Returns XML Q2" name="action"> 
<input type="submit" value="Returns Validate Q2" name="action">  
<input type="submit" value="Returns XML on screen Q2" name="action"><br /><br />

<input type="submit" value="Returns XML Q3" name="action"> 
<input type="submit" value="Returns Validate Q3" name="action">  
<input type="submit" value="Returns XML on screen Q3" name="action"><br /><br />

<input type="submit" value="Returns XML Q4" name="action"> 
<input type="submit" value="Returns Validate Q4" name="action">  
<input type="submit" value="Returns XML on screen Q4" name="action"><br /><br />

<table width="421" border="1">
  <tr>
    <td><b>Quarterly Invoicing Report</b><br />
    <br />
  <span style="width: 50px; margin-left: 50px;float: left">From:</span>
  <select name="quarter_start">
    <option value="1" selected="selected">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4">Quarter 4</option>
  </select>
  <br />
  <span style="width: 50px; margin-left: 50px; float: left;">Until:</span>
  <select name="quarter_end">
    <option value="1">Quarter 1</option>
    <option value="2">Quarter 2</option>
    <option value="3">Quarter 3</option>
    <option value="4" selected="selected">Quarter 4</option>
  </select>
  <br />
  <br />
  <input type="radio" name="invoice_type_filter" value="both" checked="checked"> 
  Show b2b and b2c<br />
  <input type="radio" name="invoice_type_filter" value="b2b"> 
  Show b2b only<br />
  <input type="radio" name="invoice_type_filter" value="b2c"> 
  Show b2c only<br />
  <br />
  <input type="submit" value="Download Quarterly Invoicing CSV" name="action">
</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
<td  width="20%">&nbsp;</td>
    <td>     <b>Monthly Invoicing Report</b><br />
  <br />
  <span style="width: 50px; margin-left: 50px;float: left">From:</span>
  <select name="month_start">
        <?php
	for($i=1; $i<13; $i++)
		if($i !== 1)
			echo("<option value=\"$i\">" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
		else
			echo("<option value=\"$i\" selected>" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
?>
    </select>
  <br />
  <span style="width: 50px; margin-left: 50px; float: left;">Until:</span>
  <select name="month_end">
        <?php
	for($i=1; $i<13; $i++)
		if($i !== 12)
			echo("<option value=\"$i\">" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
		else
			echo("<option value=\"$i\" selected>" . date("F", mktime(0,0,0,$i, 1, 1970)) . "</option>");
?>
    </select>
  <br />
  <br />
  <input type="radio" name="invoice_type_filter" value="both" checked="checked"> 
  Show b2b and b2c<br />
  <input type="radio" name="invoice_type_filter" value="b2b"> 
  Show b2b only<br />
  <input type="radio" name="invoice_type_filter" value="b2c"> 
  Show b2c only<br />
  <br />
  <input type="submit" value="Download Monthly Invoicing CSV" name="action">
  
  
  
</td>
  </tr>
</table>






</div>-->
</form>
<?php /*?><div class="container">
<b>Part 3 Audit - quarterly producers</b><br />
<form action="quarterly_returns_report_submit.php" method="post">
Completed <input type="radio" name="completed" value="completed" checked="checked"><br />
Uncompleted <input type="radio" name="completed" value="uncompleted"><br />
Both <input type="radio" name="completed" value="both"><br />
<select name="quarter">
	<option value="1" selected="selected">Quarter 1</option>
	<option value="2">Quarter 2</option>
	<option value="3">Quarter 3</option>
	<option value="4">Quarter 4</option>
</select>
<input type="submit" value="Get Report">
</form>
<b>Part 3 Audit - monthly producers</b><br />
<form action="monthly_returns_report_submit.php" method="post">
Completed <input type="radio" name="completed" value="completed" checked="checked"><br />
Uncompleted <input type="radio" name="completed" value="uncompleted"><br />
Both <input type="radio" name="completed" value="both"><br />

<select name="month">
<?php
for($i = 1; $i < 13; $i++)
{
?>
	<option value="<?php echo $i; ?>" <?php echo $month==$i?"selected=\"selected\"":""; ?>><?php echo date("F", mktime(0,0,0,$i,1,1970)); ?></option>
<?php
}
?>
</select>

<input type="submit" value="Get Report">
</form>
<b>Part 3 Audit - quarterly producers with rates</b><br />
<form action="quarterly_returns_report_rates_submit.php" method="post">
Completed <input type="radio" name="completed" value="completed" checked="checked"><br />
Uncompleted <input type="radio" name="completed" value="uncompleted"><br />
Both <input type="radio" name="completed" value="both"><br />
<select name="month">
	<option value="3" selected="selected">Quarter 1</option>
	<option value="6">Quarter 2</option>
	<option value="9">Quarter 3</option>
	<option value="12">Quarter 4</option>
</select>
<input type="submit" value="Get Report">
</form>

</div>

<div class="container">
<b>Part 4 Audit</b><br /><br />

<a href="audit.php?csv=true">CSV of all audits</a><br />

</div><?php */?>

<?php

include(ADMIN_HTDOCS . "/footer.php");

?>
