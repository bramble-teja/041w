<?php
header("Content-type: text/css; charset=utf-8");
include("rebranding.php");

?>

html {padding:0; margin:0;}
body {padding:0; margin:0; font: 12px Verdana, Arial, Helvetica, sans-serif; color:#333333;}
#frame {position:relative; margin:20px;}

a {color: rgb(139, 198, 62);}
a:visted {color: rgb(139, 198, 62);}
a:hover {color:black;}
strong {color: rgb(139, 198, 62);}
li{
	list-style-type:square;
}
.links li {  line-height:20px;}
.links li a {font-size:10px;color:black; }
.links li a:hover {color:rgb(139, 198, 62);}
/************/

#content { display:block; position:relative; padding-left:245px; padding-top:80px; min-height:800px; height:800px;}
html>body #content {height:auto;}
#content h1
{
	background-color:#d9eac0;
	background-color:#bacba1;
	color: white;
	font:bold 30px Verdana, Arial, Helvetica, sans-serif;
	line-height:50px;
	text-align:right;
	padding-right:20px;
	margin:0 0 20px 0;
}
#content h2 {font: bold 12px Verdana, Arial, Helvetica, sans-serif; color:black;}
#content h3 {font: bold 12px Verdana, Arial, Helvetica, sans-serif; color:black;}

.footnote {font-size:9px; color:#cccccc;}
.bttm {clear:both;}
.section_bottom_menu
{
	padding:0 0 15px 0; margin:15px 0 0 0;
}
.section_bottom_menu li
{
	list-style-type:none; float:left; padding:0 3px; margin:0; color: rgb(139, 198, 62); text-decoration:underline;
}
#calculator td {vertical-align:top;padding-right:10px;}
#calculator input {width:150px;}
#calculator input.radio {width:auto}
#calculator input.submit {width:100px;}
#calculator textarea {width:150px; height:80px;}

.contact  td {vertical-align:top;padding-right:10px;}
/************/
#text_menu
{
	position:relative;
	padding:0;
	margin:20px auto 0 auto;
	width:730px;
}
#text_menu li {position:relative; float:left; padding: 0 10px; margin:0; list-style-type:none;}
/************/

#left_column
{
	display:block;
	position:absolute;
	top:78px;
	left:0px;
}
#menu
{
	padding:0; margin:0;	
}
#menu li {font-size:10px;margin:0; padding:8px 0 0 12px; list-style-type:none; width:202px; overflow:hidden; height:21px; background-image:url(images/menu_button.gif);font-weight: bold;} 
#menu li.selected {background-image:url(images/menu_selected.gif); padding-left:32px; color:white; width:182px;}
#menu li a {font-size:10px;display:block; padding:0 0 0 20px; background-image:url(images/menu_bullett.gif); background-repeat:no-repeat; background-position:left 2px; text-decoration:none; color:black;}
#menu li a:hover {background-image:url(images/menu_hover.gif); color:white;}
#left_column_image { margin-top:10px; padding:5px 0 68px 0; border-top:1px solid #98b572; background-image:url(images/weeeco_compliance.gif); background-repeat:no-repeat; background-position:bottom;}

/*************/

#banner
{
	display:block; position:absolute;
	top:0; left:0;
	background-image:url(images/banner_bg.jpg);
	height:60px; width:100%;
}
#banner p {margin:0; padding:0;background-image:url(images/banner_right.jpg); background-position:right; background-repeat:no-repeat; height:60px;}
#banner a {display:block; height:60px; width:<?php echo $header_width; ?>px; background-image:url(images/<?php echo $header_logo; ?>);}
p.blog_summary {font-weight:bold; font-style:italic;}

/************/
.image_right {display:block; text-align:right; padding-bottom:16px; right:0; }

.next {float:right;}
.prev {float:left;}

.main_table td{
	border:1px solid #003300;
	padding:7px;
	vertical-align:top;
}
.three_steps li {margin-bottom:10px;}



/*********************/

.formtitle{

font: bold 12px Verdana, Arial, Helvetica, sans-serif; color:black;

}
.class_label {
//	float: left;
	padding-top: 0px;
	padding-bottom: 0px;
	margin-top: 0px;
	margin-bottom: 0px;
	text-align: right;
	width: 250px;
	padding-right: 10px;
	font-family: arial; 
	font-size: 10pt;
	font-weight: bold;
	display: inline-block;
}
label {
//	float: left;
	margin-bottom: 4px;
	text-align: right;
	width: 290px;
	padding-right: 10px;
	font-family: arial; 
	font-size: 10pt;
	font-weight: bold;
	display: inline-block;
}
.data_label_return {
//	float: left;
	margin-bottom: 4px;
	text-align: right;
	width: 300px;
	padding-right: 10px;
	padding-top: 4px;
	font-family: arial; 
	font-size: 10pt;
	font-weight: bold;
	display: inline-block;
}
.data_label {
	margin-bottom: 4px;
	text-align: right;
	width: 150px;
	padding-right: 10px;
	font-family: arial; 
	font-size: 10pt;
	font-weight: bold;
	display: inline-block;
}

.data {
	font-family: arial; 
	font-size: 10pt;
	width: 150px;
	margin-left: 10px;
}
.data_return {
	line-height: 24px;
	text-align: right;
	font-family: arial; 
	font-size: 10pt;
}


/*
#card_producer_details{
	float: left;
}
#card_producer_registered{
	float: left;
}
#card_producer_correspondent{
	width: 400px;
}
#card_producer_logos{
	width: 400px;
}
*/
#save_producer{

	width: 150px;
	margin-left: 200px;
	
}
#return_col_1{
    position:absolute;
    left:0;
    width:390px;
}
#return_col_2{
    position:absolute;
    left:384px;
    width:70px;
}
#return_col_3{
    position:absolute;
    left:455px;
    width:70px;
}
#return_col_4{
    position:absolute;
    left:530px;
    width:70px;
}

.return{
   position:relative;
   height: 450px;
   width: 900;
}

.error{
	margin-left: 208px;
	margin-bottom: 4px;
	font-size: 10pt;
	font-family: arial;
	font-weight: bold;
	color: red;
}
