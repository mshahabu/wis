<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';
include_once 'wis_AdministrationIf.php';

class TeacherIf {
	
	// property declaration
	private $id;
	private $personal_info_id;
	private $status;
	private $hire_date;
	private $termination_date;
	private $administrationIf;
	private $mysqli;
	
	public function ping() {
		print 'I am Teacher <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
		$this->administrationIf = new AdministrationIf ( $mysqli );
	}
	
	public function get_record($teacher_id) {
		$sql = 'SELECT * FROM trecord WHERE (teacher_id = "' . $teacher_id . '" && school_year = "' . $this->administrationIf->get_school_year () . '" )';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 1 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		return $info;
	}
	
	public function is_record($teacher_id) {
		$sql = 'SELECT * FROM trecord WHERE (teacher_id = "' . $teacher_id . '" && school_year = "' . $this->administrationIf->get_school_year () . '" )';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 1 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		if (empty ( $info ['teacher_id'] )) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function get_all_records() {
		$sql = 'SELECT * FROM trecord WHERE (school_year = "' . $this->administrationIf->get_school_year () . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 2 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_Qgrade_teacher_id($grade, $section) {
		$sql = "SELECT teacher_id FROM trecord WHERE ( grade = '" . $grade . "' && ";
		$sql .= " section = '" . $section . "' && school_year = '" . $this->administrationIf->get_school_year () . "' && role = 'PRIMARY' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 4 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		return $info ['teacher_id'];
	}
	
	public function get_Qstatus_teacher_id($status) {
		$sql = "SELECT id_teacher FROM teacher WHERE ( status = '" . $status . "' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 5 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_Qpinfo_teacher_id($pers_info_id) {
		$sql = "SELECT id_teacher FROM teacher WHERE ( personal_info_id = '" . $pers_info_id . "' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 6 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['id_teacher'];
	}
	
	public function get_personal_info_id($teacher_id) {
		$sql = "SELECT personal_info_id FROM teacher WHERE ( id_teacher = '" . $teacher_id . "' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 7 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		return $info ['personal_info_id'];
	}
	public function insert_pi_record($trans) {
		$sql = "INSERT INTO teacher SET personal_info_id = '" . $trans ['personal_info_id'] . "', ";
		$sql .= "status = '" . $trans ['status'] . "', hire_date = '" . $trans ['hire_date'] . "', ";
		$sql .= "termination_date = '" . $trans ['termination_date'] . "';";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 8 ) . ': ' . $this->mysqli->error );
		}
		return $this->mysqli->insert_id;
	}
	
	public function insert_record($trans) {
		$sql = "INSERT INTO trecord SET teacher_personal_info_id = '" . $this->get_personal_info_id ( $trans ['teacher_id'] ) . "', ";
		$sql .= "teacher_id   = '" . $trans ['teacher_id'] . "', ";
		$sql .= "school_year   = '" . $this->administrationIf->get_school_year () . "', ";
		$sql .= "grade   = '" . $trans ['grade'] . "', ";
		$sql .= "section = '" . $trans ['section'] . "', ";
		$sql .= "role    = '" . $trans ['role'] . "', ";
		$sql .= "room    = '" . $trans ['room'] . "';";
		// print "SQL: ". $sql . "<BR>";
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 8 ) . ': ' . $this->mysqli->error );
		}
		return $this->mysqli->insert_id;
	}
	
	public function update_record($trans) {
		$sql = "UPDATE trecord SET ";
		if (empty ( $trans ['grade'] ) || ($trans ['grade'] === 'na')) {
			$sql .= " section='NULL',";
			$sql .= " grade  ='NULL',";
			$sql .= " role   ='NULL',";
			$sql .= " room   ='NULL'";
			$sql .= " WHERE (teacher_id='" . $trans ['teacher_id'] . "')";
		} else {
			$sql .= " section='" . $trans ['section'] . "',";
			$sql .= " grade='" . $trans ['grade'] . "',";
			$sql .= " role='" . $trans ['role'] . "',";
			$sql .= " room='" . $trans ['room'] . "'";
			$sql .= " WHERE (teacher_id='" . $trans ['teacher_id'] . "')";
		}
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::TEACHER + 9 ) . ': ' . $this->mysqli->error );
		}
	}
}
?>        