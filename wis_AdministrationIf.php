<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class AdministrationIf {

	// property declaration
	private $mysqli;
	
	public function ping() {
		print 'I am Administration <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($administration_id) {
		$sql = 'SELECT * FROM administration WHERE (id_admin = "' . $administration_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 1 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	/**
	 *
	 * @return Get tution fee and discounts
	 */
	function get_tution_discounts() {
		$sql = "SELECT tution_fee, sibling_discount, icsgv_mem_discount, payment_plan_fee FROM administration";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 2 ) . ': ' . $this->mysqli->error );
		}
		
		$rv = $result->fetch_assoc ();
		$result->close ();
		
		return $rv;
	}
	
	/**
	 *
	 * @return current school year.
	 */
	public function get_school_year() {
		$years = 0;
		$sql = 'SELECT school_year FROM administration';
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 3 ) . ': ' . $this->mysqli->error );
		}
		
		$year = $result->fetch_assoc ();
		
		if (empty ( $year ['school_year'] )) {
			print '<script type="text/javascript" >alert("Administrator, please setup the school year first ");</script>';
		} else {
			$years = $year ['school_year'];
		}
		return $years;
	}
	
	public function get_id_row_count() {
		$sql = "SELECT COUNT(id_admin) FROM administration";
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 4 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		return $info ['COUNT(id_admin)'];
	}
	
	public function insert_record($trans) {
		$sql = "INSERT INTO administration SET school_year = '" . $trans ['school_year'] . "', ";
		$sql .= "tution_fee = '" . $trans ['tution_fee'] . "', sibling_discount = '" . $trans ['sibling_discount'] . "', ";
		$sql .= "icsgv_mem_discount = '" . $trans ['icsgv_mem_discount'] . "', payment_plan_fee = '" . $trans ['payment_plan_fee'] . "', ";
		$sql .= "application_email = '" . $trans ['application_email'] . "',  approval_email = '" . $trans ['approval_email'] . "'";
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 5 ) . ': ' . $this->mysqli->error );
		}
		
		return $this->mysqli->insert_id;
	}
	
	public function insert_school_year($school_year) {
		$sql = "INSERT INTO administration SET school_year='" . $school_year . "'";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 6 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_school_year($school_year) {
		$sql = "UPDATE administration SET school_year='" . $school_year . "'";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 7 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function delete_all_records() {
		$sql = "DELETE FROM administration";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 8 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function set_tution_fee($tution_fee) {
		$sql = "UPDATE administration SET tution_fee=" . $tution_fee;
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 9 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function set_sibling_discount($sibling_discount) {
		$sql = "UPDATE administration SET sibling_discount=" . $sibling_discount;
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 10 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function set_icsgv_mem_discount($icsgv_mem_discount) {
		$sql = "UPDATE administration SET icsgv_mem_discount=" . $icsgv_mem_discount;
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 11 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function set_payment_plan_fee($payment_plan_fee) {
		$sql = "UPDATE administration SET payment_plan_fee=" . $payment_plan_fee;
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 12 ) . ': ' . $this->mysqli->error );
		}
	}

	public function update_school_days($school_days) {
		$sql = "UPDATE administration SET school_days='" . $school_days . "'";
	
		$result = $this->mysqli->query ( $sql );
	
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 13 ) . ': ' . $this->mysqli->error );
		}
	}

	public function get_school_days() {
		$sql = "SELECT school_days FROM administration ";
	
		$result = $this->mysqli->query ( $sql );
	
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 14 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc();
		$school_days = '';
		
		if (empty ( $info ['school_days'] )) {
			print '<script type="text/javascript" >alert("Administrator, please setup the school year first ");</script>';
		} else {
			$school_days = $info['school_days'];
		}
		return $school_days;
	}
	
	public function get_test_dates_max_points($grade, $section) {
		$sql = "SELECT test_dates, max_grade_points FROM grade_info WHERE (grade_section = '" . $grade . "-" . $section . "')"; 
		
		$result = $this->mysqli->query ( $sql );
	
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 15 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		if ($result->num_rows==1) {
			$info = $result->fetch_assoc ();
			$result->close ();
		} elseif ($result->num_rows>1) {
			die ('Number of rows cannot be greater than 1' . strval ( Errno::ADMINISTRATION + 16 ) . ': ' . $result->num_rows);
		} 

		return $info;
	}
	
	public function update_test_dates_max_points($grade, $section, $test_dates, $max_grade_points) {
		$sql = "SELECT test_dates, max_grade_points FROM grade_info WHERE (grade_section = '" . $grade . "-" . $section . "')"; 
		
		$result = $this->mysqli->query ( $sql );
	
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 17 ) . ': ' . $this->mysqli->error );
		}
		
		if ($result->num_rows==0) {
			$sql1  = "INSERT INTO grade_info SET ";
			$sql1 .= " grade_section='" . $grade . "-" . $section . "', ";
			$sql3  = "";
		} elseif ($result->num_rows==1) {			
			$sql1  = "UPDATE grade_info SET ";
			$sql3  = " WHERE (grade_section = '" . $grade . "-" . $section . "')";
		} else {
			die ('Number of rows cannot be greater than 1' . strval ( Errno::ADMINISTRATION + 18 ) . ': ' . $result->num_rows);
		} 
		
		$sql2  = " test_dates ='" . $test_dates . "', ";
		$sql2 .= " max_grade_points ='" . $max_grade_points . "' ";
		
		$sql = $sql1 . $sql2 . $sql3; 

		$result = $this->mysqli->query ( $sql );
			
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ADMINISTRATION + 16 ) . ': ' . $this->mysqli->error );
		}
		
	}
}

?>
