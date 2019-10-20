<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1
?>

<!DOCTYPE html>

<html>
<head>
<title>WIS</title>

<link rel="stylesheet"
	href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />

<script>
$(function() {
$( ".datepicker" ).datepicker();
});
$(function() {
$( ".datepicker2" ).datepicker();
$( ".datepicker2" ).datepicker( "option", "dateFormat", "DD, d M");
});
$(function() {
$( ".datepicker3" ).datepicker();
$( ".datepicker3" ).datepicker( "option", "dateFormat", "mm/dd");
});
</script>

<SCRIPT LANGUAGE="JavaScript">

function print_div(divName) {
   var headstr = "<html><head><title></title></head><body>";
   var footstr = "</body>";
   var printContents = document.getElementById(divName).innerHTML;
   var originalContents = document.body.innerHTML;

  document.body.innerHTML = headstr+printContents+footstr;

  window.print();
  
  document.body.innerHTML = originalContents;
}

</SCRIPT>

<SCRIPT TYPE="text/javascript">
<!--
var downStrokeField;
function autojump(fieldName,nextFieldName,fakeMaxLength)
{
var myForm=document.forms[document.forms.length - 1];
var myField=myForm.elements[fieldName];
myField.nextField=myForm.elements[nextFieldName];

if (myField.maxLength == null)
   myField.maxLength=fakeMaxLength;

myField.onkeydown=autojump_keyDown;
myField.onkeyup=autojump_keyUp;
}

function autojump_keyDown()
{
this.beforeLength=this.value.length;
downStrokeField=this;
}

function autojump_keyUp()
{
if (
   (this == downStrokeField) && 
   (this.value.length > this.beforeLength) && 
   (this.value.length >= this.maxLength)
   )
   this.nextField.focus();
downStrokeField=null;
}
//-->
</SCRIPT>

<?php
print "<style>";
// <link rel="stylesheet" type="text/css" href="http://localhost/cgi-bin/Site.css">
include ("wis_site.css");
print "</style>";
print "</head>";
print "<body>";

print '<Form name ="form2" Method ="Post" ACTION ="wis_process_request.php" enctype="multipart/form-data">';

ini_set ( 'date.timezone', 'America/Los_Angeles' );
?>

