<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class AttendanceIf {
	
	// property declaration
	private $id;
	private $personal_info_id;
	private $status;
	private $mysqli;
	
	public function ping() {
		print 'I am AttendanceIf <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_records($student_id, $school_year) {
		$sql = 'SELECT * FROM attendance WHERE (student_id = "' . $student_id . '" && school_year = "' . $school_year . '" ) ORDER BY day_number ASC';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ATTENDANCE + 1 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function insert_record($trans) {
		$sql = "INSERT INTO attendance SET ";
		$sql .= "student_id = '" . $trans ['student_id'] . "', ";
		$sql .= "day_number    = '" . $trans ['day_number'] . "', ";
		//$sql .= "day_month     = '" . $trans ['day_month']  . "', ";
		$sql .= "present       = '" . $trans ['present'] . "', ";
		$sql .= "school_year   = '" . $trans ['school_year'] . "' ";
		//print "SQL: ". $sql . "<BR>";
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ATTENDANCE + 8 ) . ': ' . $this->mysqli->error );
		}
		return $this->mysqli->insert_id;
	}
	
	public function update_record($trans) {
		$sql = "UPDATE attendance SET ";
			$sql .= " day_number='NULL',";
			$sql .= " day_month  ='NULL',";
			$sql .= " present   ='NULL',";
			$sql .= " school_year   ='NULL'";
			$sql .= " WHERE (student_id='" . $trans ['student_id'] . "')";

		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ATTENDANCE + 9 ) . ': ' . $this->mysqli->error );
		}
	}
}
?>        