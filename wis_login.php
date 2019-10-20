<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once "wis_util.php";
include_once 'wis_connect.php';
include_once "wis_PersonalInfoIf.php";
include_once 'wis_TeacherIf.php';

function login() {
	$mysqli = wis_connect_to_mysql ();
	$personalInfoIf = new PersonalInfoIf ( $mysqli );
	
	$rv = FALSE;
	$_SESSION ['wis_error'] = '';
	$_SESSION ['wis_error_flag'] = FALSE;
	
	if (empty ( $_REQUEST ['password'] ) || empty ( $_REQUEST ['username'] )) {
		$_SESSION ['wis_error'] = "<font color='red'> Please fill-in all the fields <font color='black'><BR>";
		$_SESSION ['wis_error_flag'] = TRUE;
		return $rv;
	}
	
	// get a new hash for a password
	$hash = wis_get_password_hash ( wis_get_password_salt (), $_REQUEST ['password'] );
	
	$user_hash = $personalInfoIf->get_user_info ( $_REQUEST ['username'] );
	
	// print " <B> ACCESS PRIV is " . $_REQUEST['access'] ;
	if ($_REQUEST ['access'] === "Student") {
		$loging_as = WIS_STUDENT;
	} elseif ($_REQUEST ['access'] === "Teacher") {
		$loging_as = WIS_TEACHER;
	} elseif ($_REQUEST ['access'] === "Staff") {
		$loging_as = WIS_STAFF;
	}
	
	$_SESSION ['authenticity'] = Authentication::INVALID;
	if (wis_compare_password ( $_REQUEST ['password'], $user_hash ['password'] ) == TRUE) {
		
		if ($user_hash ['access'] & $loging_as) {
			$_SESSION ['access_privilege'] = $loging_as;
			$_SESSION ['login_source'] = $_REQUEST ['login_source'];
			$_SESSION ['username'] = $_REQUEST ['username'];
			$_SESSION ['actualUserName'] = $user_hash ['first_name'] . " " . $user_hash ['middle_name'] . " " . $user_hash ['last_name'];
			$_SESSION ['authenticity'] = Authentication::VALID;
			$_SESSION ['last_touch'] = time ();
			
			wis_log_event ( " logged as " . $_REQUEST ['access'] );
			
			// print "<B> LOGIN AUTHENTICATION PASSED </B>";
			if ($loging_as == WIS_TEACHER) {
				$teacherIf = new TeacherIf ( $mysqli );
				$teacher_id = $teacherIf->get_Qpinfo_teacher_id ( $user_hash ['id_pi'] );
				
				$_SESSION ['teacher_id'] = $teacher_id;
				$_SESSION ['first_name'] = $user_hash ['first_name'];
				$_SESSION ['middle_name'] = $user_hash ['middle_name'];
				$_SESSION ['last_name'] = $user_hash ['last_name'];
			} else if ($loging_as == WIS_STUDENT) {
				$studentIf = new StudentIf ( $mysqli );
				$student_id = $studentIf->get_id ( $user_hash ['id_pi'] );
				
				$_SESSION ['student_id'] = $student_id;
			}
			
			$rv = TRUE;
		} else {
			$_SESSION ['wis_error'] = "<font color='red'> LOGIN AUTHENTICATION FAILED, Access not allowed as " . $_REQUEST ['access'] . "<font color='black'><BR>";
			$_SESSION ['wis_error_flag'] = TRUE;
		}
	} else {
		$_SESSION ['wis_error'] = "<font color='red'> LOGIN AUTHENTICATION FAILED, Invalid User Name OR Password <font color='black'><BR>";
		$_SESSION ['wis_error_flag'] = TRUE;
	}
	
	return $rv;
}

?>
