<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';

$access_privilege = 0;
function wis_create_new_event() {
	print "<input type=text name=wis_ev_date><BR>";
	print "<input type=text name=wis_ev><BR>";
}

function wis_reset_user_password() {
	print "<br>";
	print "<br>";
	
	print "<H2 align=left> Reset User Name/Password </H2>";
	
	if ($logging_as === 'STUDENT') {
		$info = $studentIf->get_all_ids ();
		$id = 'id_student';
	} else {
		$info = $studentIf->get_all_ids ();
		$id = 'id_staff';
	}
	
	for($i = 0; $i < count ( $info ); $i ++) {
		$pinfo = $personal_info->get_name ();
		
		$str = $info [$i] [$id] . '  ' . $pinfo ['first_name'] . '  ' . $pinfo ['middle_name'] . '  ' . $pinfo ['last_name'];
		$names [$i + 1] = $str;
	}
	
	print '<label for="accessee">Select Member whose User name/password need be reset</label>';
	print "<em  style='color:red;'> *&nbsp&nbsp</b></em>";
	createDropdown ( $names, 'member_reset' );
	
	print "<br>";
	print "<br>";
	
	setSubmitValue ( "reset_user_password" );
}

function wis_change_access_privileges() {
	print "<br>";
	print "<br>";
	
	print "<H2 align=left> Change/View Access Privilege </H2>";
	$id = '';
	$table = '';
	
	if ($logging_as === 'STUDENT') {
		$info = $studentIf->get_all_ids ();
		$id = 'id_student';
	} else {
		$info = $studentIf->get_all_ids ();
		$id = 'id_staff';
	}
	
	for($i = 0; $i < count ( $info ); $i ++) {
		// FIX get name in alphabetical order
		$pinfo = $personal_info->get_name ();
		
		$str = $info [$i] [$id] . '  ' . $pinfo ['first_name'] . '  ' . $pinfo ['middle_name'] . '  ' . $pinfo ['last_name'];
		$names [$i + 1] = $str;
	}
	
	print '<label for="accessee">Select Member whose privileges need be changed:</label>';
	print "<em  style='color:red;'> *&nbsp&nbsp</b></em>";
	createDropdown ( $names, 'member_priv' );
	
	print "<br>";
	print "<br>";
	
	// print "<p><em>Grant Access (Check all that apply)</em><br>";
	print '<input type=radio name="access_select" value="View Access" checked="yes"> View Access';
	print "<br>";
	print "<br>";
	print '<input type=radio name="access_select" value="Change Access" > Change Access (Check all that apply)';
	print "<br>";
	// print "<br>";
	print "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
	
	print '<input type=checkbox name="access_student"   value="Student"> Student';
	print '<input type=checkbox name="access_parent"    value="Parent"> Parent';
	print '<input type=checkbox name="access_teacher"   value="Teacher"> Teacher';
	print '<input type=checkbox name="access_office"    value="Staff"> Staff';
	
	print "<br>";
	print "<br>";
	
	setSubmitValue ( "change_access_privilege" );
}

function wis_enter_user_password_reset() {
	
	// print "MEM " . $_REQUEST['member_reset'];
	// print "<BR>";
	list ( $member_id, $first_name, $middle_name, $last_name ) = explode ( "  ", $_REQUEST ['member_reset'] );
	
	if ($logging_as === 'STUDENT') {
		$pers_info_id = $studentIf->get_personal_info_id ( $member_id );
	} else {
		$pers_info_id = $staffIf->get_personal_info_id ( $member_id );
	}
	
	$personal_info->reset_user_passwd ( $pers_info_id );
	
	wis_log_event ( " reseted PASSWORD id: " . $member_id );
	
	print "<br>";
	print "<br>";
	print "<em><b>USER password reset Succefully</em>";
	print "<br>";
	print "<br>";
}

function wis_enter_access_privilege() {
	
	// print "MEM " . $_REQUEST['member_priv'];
	// print "<BR>";
	list ( $member_id, $first_name, $middle_name, $last_name ) = explode ( "  ", $_REQUEST ['member_priv'] );
	print "MEM " . $member_id . " <BR>";
	if ($logging_as === 'STUDENT') {
		$pers_info_id = $studentIf->get_personal_info_id ( $member_id );
	} else {
		$pers_info_id = $staffIf->get_personal_info_id ( $member_id );
	}
	
	$access = 0;
	if (isset ( $_REQUEST ['access_student'] )) {
		$access |= WIS_STUDENT;
	}
	if (isset ( $_REQUEST ['access_teacher'] )) {
		$access |= WIS_TEACHER;
	}
	if (isset ( $_REQUEST ['access_accounts'] )) {
		$access |= WIS_STAFF;
	}
	
	$personal_info->update_access ( $access, $pers_info_id );
	
	wis_log_event ( " changed ACCESS_PRIV id: " . $member_id );
	
	print "<br>";
	print "<br>";
	print "<em><b>Data Accepted Succefully</em>";
	print "<br>";
	print "<br>";
}

?>
