<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1
?>

<!DOCTYPE html>

<html>
<head>
<title>WIS </title>

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

<script>
    var studentBody, asc1 = 1,
    asc2 = 1,
    asc3 = 1,
    asc4 = 1,
    asc5 = 1,
    asc6 = 1,
    asc7 = 1,
    asc8 = 1;

    window.onload = function ()
    {
	studentBody = document.getElementById("studentRecord");
    }

    function sort_table(tbody, col, asc, sort_col1) {
	var rows = tbody.rows,
	    rlen = rows.length,
	    arr = new Array(),
	    row_ctr = 1,
	    i, j, cells, clen;
	// fill the array with values from the table
	for (i = 0; i < rlen; i++) {
	    cells = rows[i].cells;
	    clen = cells.length;
	    arr[i] = new Array();
	    for (j = 0; j < clen; j++) {
		arr[i][j] = cells[j].innerHTML;
	    }
	}
	// sort the array by the specified column number (col) and order (asc)
	arr.sort(function (a, b) {
                return (a[col] == b[col]) ? 0 : ((a[col] > b[col]) ? asc : -1 * asc);
            });
	// replace existing rows with new rows created from the sorted array
	for (i = 0; i < rlen; i++) {
	    if (sort_col1==0) {
		arr[i][0] = row_ctr++;
	    }
	    rows[i].innerHTML = "<td>" + arr[i].join("</td><td>") + "</td>";
	}
    }

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
//<link rel="stylesheet" href="css/print.css" type="text/css" media="print"/>
// <link rel="stylesheet" type="text/css" href="http://localhost/cgi-bin/Site.css">
include ("wis_site.css");
//include ("print.css");
print "</style>";
print "</head>";
print "<body>";

Print "<Form name ='form1' Method ='Post' ACTION ='wis_process_request.php'>";

ini_set ( 'date.timezone', 'America/Los_Angeles' );
?>

