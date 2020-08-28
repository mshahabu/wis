<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class StudentIf {
	
	// property declaration
	private $mysqli;
	
	public function ping() {
		print 'I am Student <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($student_id) {
		$sql = 'SELECT * FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 1 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_admission_date($student_id) {
		$sql = 'SELECT admission_date FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 2 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['admission_date'];
	}
	
	public function get_exit_date($student_id) {
		$sql = 'SELECT exit_date FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 3 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['exit_date'];
	}
	
	public function get_Qgrade_record($grade, $section, $status) {
		$sql = 'SELECT * FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 4 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_all_records() {
		$sql = 'SELECT * FROM student';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 5 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_id($personal_info_id) {
		$sql = 'SELECT id_student FROM student WHERE (personal_info_id = "' . $personal_info_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 6 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		if ($result->num_rows === 0) {
		    return 0;
		}
		
		return $info ['id_student'];
	}
	
	public function get_personal_info_id($student_id) {
		$sql = 'SELECT personal_info_id FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 7 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		return $info ['personal_info_id'];
	}
	
	public function get_parent_id($student_id) {
		$sql = 'SELECT parent_id FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 8 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		return $info ['parent_id'];
	}
	
	public function get_teacher_id($student_id) {
		$sql = 'SELECT teacher_id FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 9 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		return $info ['teacher_id'];
	}
	
	public function get_student_id($pers_info_id) {
		$sql = "SELECT id_student FROM student WHERE ( personal_info_id = '" . $pers_info_id . "' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 10 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['id_student'];
	}
	
	public function get_status($student_id) {
		$sql = 'SELECT status FROM student WHERE (id_student = "' . $student_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 11 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		return $info ['status'];
	}
	
	public function get_all_student_ids() {
		$sql = 'SELECT id_student FROM student';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 12 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res ['id_student'];
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_all_active_student_ids() {
		$sql = "SELECT id_student FROM student WHERE (status='ACTIVE')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 13 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res ['id_student'];
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_ids($personal_info_id) {
		$sql = 'SELECT id_student, parent_id, teacher_id FROM student WHERE (personal_info_id = "' . $personal_info_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 14 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_children_ids($parent_id) {
		$sql = 'SELECT id_student FROM student WHERE (parent_id = "' . $parent_id . '" && status = "ACTIVE" )';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 15 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res ['id_student'];
		}
		$result->close ();
		
		return $info;
	}
	
	public function insert_record($trans) {
		$sql = "INSERT INTO student SET personal_info_id = '" . $trans ['personal_info_id'] . "', ";
		$sql .= "teacher_id = '" . $trans ['teacher_id'] . "', parent_id = '" . $trans ['parent_id'] . "', ";
		$sql .= "status = '" . $trans ['status'] . "'";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 16 ) . ': ' . $this->mysqli->error );
		}
		
		return $this->mysqli->insert_id;
	}
	
	public function update_teacher_id($teacher_id, $student_id) {
		$sql = "UPDATE student SET ";
		$sql .= " teacher_id = '" . $teacher_id . "' ";
		$sql .= " WHERE (id_student = '" . $student_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 17 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_admission_date($admission_date, $student_id) {
		$sql = "UPDATE student SET ";
		$sql .= " admission_date = '" . $admission_date . "' ";
		$sql .= " WHERE (id_student = '" . $student_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 18 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_exit_date($exit_date, $student_id) {
		$sql = "UPDATE student SET ";
		$sql .= " exit_date = '" . $exit_date . "' ";
		$sql .= " WHERE (id_student = '" . $student_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 19 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_status($status, $student_id) {
		$sql = "UPDATE student SET ";
		$sql .= " status = '" . $status . "' WHERE (id_student = '" . $student_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 20 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function new_change($personal_info_id, $new_home_phone, $new_address, $new_city, $new_zipcode) {
		$sql = "SELECT home_phone, address, city, zipcode, state FROM personal_info WHERE (id_pi = '$personal_info_id')";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::STUDENT + 21 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		if ($info ['phone_number'] === $new_home_phone && $info ['address'] === $new_address && $info ['city'] === $new_city && $info ['zipcode'] === $new_zipcode) {
			return false;
		} else {
			return true;
		}
	}
	
	public function isStatus($sid, $status) {
		$rv = true;
		
		$sql = "SELECT status FROM student WHERE (id_student='" . $sid . "' && status='" . $status . "')";
		
		$result = $this->mysqli->query ( $sql );
		
		if ($result->num_rows === 0) {
			$rv = false;
		}
		return $rv;
	}
}

?>
