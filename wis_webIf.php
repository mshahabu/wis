<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_connect.php';
include_once 'wis_StudentRecord.php';
include_once 'wis_Book.php';
include_once 'wis_Email.php';
include_once 'wis_Calendar.php';
include_once 'wis_Teacher.php';
include_once 'wis_Administration.php';
include_once 'wis_Blackboard.php';

if (isset ( $_REQUEST ['obj'] ) && isset ( $_REQUEST ['meth'] ) && !empty($_SESSION ['authenticity']) && $_SESSION ['authenticity'] == Authentication::VALID) {
	
	$mysqli_h = wis_connect_to_mysql ();
	
	$method = $_REQUEST ['meth'];
	// print "OBJ: " . $_REQUEST['obj'] . " METHOD " . $_REQUEST['meth'] . " ARG " . $_REQUEST['a1'] . "<BR>";
	
	// Student Record
	if ($_REQUEST ['obj'] === 'studentRecord') {
		$myObj = new StudentRecord ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'book') {
		$myObj = new Book ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'teacher') {
		$myObj = new Teacher ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'administration') {
		$myObj = new Administration ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'calendar') {
		$myObj = new Calendar ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'email') {
		$myObj = new Email ( $mysqli_h );
	} else if ($_REQUEST ['obj'] === 'blackBoard') {
		$myObj = new Blackboard ( $mysqli_h );
	} else {
		// FIX: PROGRAM should die here
		// No function found EXIT
	}
	
	if (method_exists ( $myObj, $method )) {
		if (isset ( $_REQUEST ['a6'] )) {
			$myObj->$method ( $_REQUEST ['a1'], $_REQUEST ['a2'], $_REQUEST ['a3'], $_REQUEST ['a4'], $_REQUEST ['a5'], $_REQUEST ['a6'] );
		} else if (isset ( $_REQUEST ['a5'] )) {
			$myObj->$method ( $_REQUEST ['a1'], $_REQUEST ['a2'], $_REQUEST ['a3'], $_REQUEST ['a4'], $_REQUEST ['a5'] );
		} else if (isset ( $_REQUEST ['a4'] )) {
			$myObj->$method ( $_REQUEST ['a1'], $_REQUEST ['a2'], $_REQUEST ['a3'], $_REQUEST ['a4'] );
		} else if (isset ( $_REQUEST ['a3'] )) {
			$myObj->$method ( $_REQUEST ['a1'], $_REQUEST ['a2'], $_REQUEST ['a3'] );
		} else if (isset ( $_REQUEST ['a2'] )) {
			$myObj->$method ( $_REQUEST ['a1'], $_REQUEST ['a2'] );
		} else if (isset ( $_REQUEST ['a1'] )) {
			$myObj->$method ( $_REQUEST ['a1'] );
		} else {
			$myObj->$method ();
		}
	} else {
		// FIX: PROGRAM should die here
		// No function found EXIT
	}
}

?>
