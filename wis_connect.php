<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

function wis_connect_to_mysql_old() {
	$dbh = mysql_connect ( 'localhost', 'root' ); // Testing
	                                           
	// $dbh = mysql_connect('localhost', 'icsgv_wisuser', 'wI$oifj1*'); //web hosting
	
	if (! $dbh) {
		die ( 'Could not connect: ' . mysql_error () );
	}
	
	// make foo the current db
	$db_selected = mysql_select_db ( 'wis_db', $dbh );
	if (! $db_selected) {
		die ( 'Can\'t use foo : ' . mysql_error () );
	}
}

function wis_connect_to_mysql() {
       
	$mysqli = new mysqli('localhost', 'icsgv_wisuser', 'WIS*ifj16', 'icsgv_wis');
	// $mysqli = new mysqli ( 'localhost', 'root', '', 'wis_db' );
	
	if ($mysqli->connect_errno) {
		die ( 'Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
	}
	return $mysqli;
}

?>
