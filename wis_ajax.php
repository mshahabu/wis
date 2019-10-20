<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_RegistrationForm.php';

// Updates the member_tag & spouse_tag in the members table that were selected in quorum_count() function
function member_check($mid) {
	$sql = 'SELECT first_name, last_name, spouse_first_name, spouse_last_name, address, city, zipcode, phone, email FROM personal_info, members WHERE (member_id = ' . $mid . ' AND members.personal_info_id = personal_info.id )';
	
	// print $sql;
	$result = mysql_query ( $sql );
	if (! $result) {
		die ( 'Invalid Query 501: ' . mysql_error () );
	}
	
	$info = mysql_fetch_array ( $result );
	// print "MEM_ID: " . $mid . " first_name: " . $info['first_name'] . " last_name: " . $info['last_name'];
	$info ['mem_id'] = $mid;
	wis_student_registration ( true, false, $info );
}

// echo "Member id: " . $_GET['memberCheck'] . "; who: " . $_GET['who'] . "; check value: " . $_GET['checked'] . " <BR>";
// echo "MEM_ID " . $_GET['memberId'];
// member_check($_GET['memberId']);

?>
