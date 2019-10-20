<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';

function year_setup() {
	welcome_banner ( "WIS" );
	$years = array (
			"Make selection",
			"2013-2014",
			"2014-2015",
			"2015-2016",
			"2016-2017",
			"2017-2018",
			"2018-2019",
			"2019-2020" 
	);
	array_push ( $years, "2020-2021", "2021-2022", "2022-2023", "2023-2024", "2024-2025", "2025-2026", "2026-2027", "2027-2028", "2028-2029", "2029-2030", "2030-2031" );
	
	Print "<Form name ='form2' Method ='Post' ACTION ='wis_process_request.php'>";
	
	print '<H4> Weekend Islamic School Year setup</H4>';
	print '<BR>To be updated once each school year <BR><BR><BR>';
	
	print '<label>School Year</label>';
	print "&nbsp&nbsp";
	createDropdown ( $years, "school_year" );
	
	print '<BR>';
	print '<BR>';
	print '<input type=hidden name="SubmitVal" value="schoolYearSetup">';
	
	print '<input type="Submit" name="SubmitVal00" value="Submit" style="background-color:lightgreen;position:absolute;right:20px;botton:0px;">';
	print '<BR>';
	print '<BR>';
}

if (isset ( $_REQUEST ['fwss'] ) && function_exists ( $_REQUEST ['fwss'] )) {
	$_REQUEST ['fwss'] ();
}

?>
