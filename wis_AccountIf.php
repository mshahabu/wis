<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class AccountIf {

	// property declaration
	private $mysqli;

	public function ping() {
		print 'I am Account <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($pers_info_id, $school_year) {
		$sql = 'SELECT amount, trans_date FROM accounts WHERE (personal_info_id = "' . $pers_info_id;
		
		if ($school_year === 'ALL') {
			$sql .= '")';
		} else {
			$sql .= '" && school_year = "' . $school_year . '")';
		}
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ACCOUNT + 1 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_all_records() {
		$sql = 'SELECT * FROM accounts';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ACCOUNT + 2 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	function get_amount_paid($pers_info_id, $school_year) {
		$sql = "SELECT amount FROM accounts WHERE (school_year='" . $school_year . "' && personal_info_id='" . $pers_info_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ACCOUNT + 3 ) . ': ' . $this->mysqli->error );
		}
		
		$tpaid = 0;
		while ( $info = $result->fetch_assoc () ) {
			$tpaid += $info ['amount'];
		}
		return $tpaid;
	}
	
	public function insert_record($trans) {
		$sql = "INSERT INTO accounts SET personal_info_id = '" . $trans ['personal_info_id'] . "', ";
		$sql .= "amount = '" . $trans ['amount'] . "', trans_date = '" . $trans ['trans_date'] . "', ";
		$sql .= "check_number = '" . $trans ['check_number'] . "', payment_type = '" . $trans ['payment_type'] . "', ";
		$sql .= "paid_for = '" . $trans ['paid_for'] . "', other_description = '" . $trans ['other_description'] . "', ";
		$sql .= "school_year = '" . $trans ['school_year'] . "';";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ACCOUNT + 4 ) . ': ' . $this->mysqli->error );
		}
		
		return $this->mysqli->insert_id;
	}
	
	public function get_account_info($school_year, $pers_info_id) {
		$sql = "SELECT trans_date, amount, payment_type, check_number, paid_for, other_description FROM accounts ";
		$sql .= " WHERE (personal_info_id='" . $pers_info_id;
		
		if ($school_year === 'ALL') {
			$sql .= "')";
		} else {
			$sql .= "' && school_year = '" . $school_year . "')";
		}

		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::ACCOUNT + 5 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
}

?>
