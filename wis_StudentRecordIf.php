<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class StudentRecordIf {
	
	// property declaration
	private $mysqli;
	
	public function ping() {
		print 'I am Student Record <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($student_id) {
		$sql = 'SELECT * FROM record WHERE (student_id = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT_RECORD + 1 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function insert_record($stu_id,$date,$gp) {
		$sql = "INSERT INTO record SET student_id = '" . $stu_id . "', ";
		$sql .= "date = '" . $date . "', points = '" . $gp . "'";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT_RECORD + 2 ) . ': ' . $this->mysqli->error );
		}
		
		return $this->mysqli->insert_id;
	}
	
	public function update_record($stu_id,$date,$gp) {
		$sql = "UPDATE record SET ";
		$sql .= " points='"     . $gp . "'";
		$sql .= " WHERE (student_id='" . $stu_id . "' && date='" . $date . "')";

		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT_RECORD + 3 ) . ': ' . $this->mysqli->error );
		}
	}

	public function isExist($student_id, $date) {

	        $sql = "SELECT * FROM record WHERE (student_id = '" . $student_id . "' && date='" . $date . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT_RECORD + 4 ) . ': ' . $this->mysqli->error );
		}
		
		if ($result->num_rows === 0) {
		    return Null;
		}
		return $result->fetch_assoc();
	}
	
}

?>
