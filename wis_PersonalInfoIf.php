<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class PersonalInfoIf {
	// property declaration
	private $mysqli;
	
	public function ping() {
		print 'I am Personal Info <BR>';
	}
	
	function __construct(&$mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($personal_info_id) {
		$sql = 'SELECT * FROM personal_info WHERE (id_pi = "' . $personal_info_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 1 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_all_records() {
		$sql = 'SELECT id_pi, first_name, last_name FROM personal_info ORDER BY last_name ASC, first_name ASC';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 2 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_all_users() {
		$sql = "SELECT user FROM personal_info";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 3 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_name($personal_info_id) {
		$sql = "SELECT first_name, middle_name, last_name ";
		$sql .= " FROM personal_info WHERE (id_pi= '" . $personal_info_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 4 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_email($personal_info_id) {
		$sql = "SELECT email FROM personal_info WHERE (id_pi= '" . $personal_info_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 5 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['email'];
	}
	
	public function get_user_info($user_name) {
		$sql = "SELECT password, access, first_name, middle_name, last_name, id_pi FROM personal_info WHERE (user = '" . $user_name . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 6 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_id($first_name, $middle_name, $last_name, $email) {
		$sql = 'SELECT id_pi FROM personal_info WHERE ';
		$sql .= ' (first_name = "' . $first_name . '" && last_name = "' . $last_name . '" && middle_name = "' . $middle_name . '" && email = "' . $email . '")';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 7 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
			die ( 'Duplicate Entry found ' . strval ( Errno::PERSONAL_INFO + 8 ) . ' for Personal Info ID: ' . $info [$i] ['id_pi'] );
		}
		
		$result->close ();
		
		return $info [0] ['id_pi'];
	}
	
	public function get_QflName_all_ids($first_name, $last_name) {
		if (empty ( $last_name )) {
			$sql = 'SELECT id_pi FROM personal_info WHERE ';
			$sql .= ' (first_name = "' . $first_name . '" || last_name = "' . $first_name . '")';
		} else {
			$sql = 'SELECT id_pi FROM personal_info WHERE ';
			$sql .= ' (first_name = "' . $first_name . '" && last_name = "' . $last_name . '")';
		}
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 9 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function insert_record($pers_rec) {
		$rv = 0;
		$sql = "INSERT INTO personal_info (last_name, middle_name, first_name, cell_phone, home_phone, address, city, state, zipcode, email )";
		$sql .= " VALUES ('" . $pers_rec ['last_name'] . "','" . $pers_rec ['middle_name'] . "','" . $pers_rec ['first_name'] . "','" . $pers_rec ['cell_phone'] . "'";
		$sql .= ", '" . $pers_rec ['home_phone'] . "','" . $pers_rec ['address'] . "','" . $pers_rec ['city'] . "','" . $pers_rec ['state'] . "'";
		$sql .= ", '" . $pers_rec ['zipcode'] . "','" . $pers_rec ['email'] . "')";
		
		// print "SQL: " . $sql;
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			if ($this->mysqli->errno == 1062) {
				$rv = - 1;
			} else {
				die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 10 ) . ': ' . $this->mysqli->error );
			}
		} else {
			$rv = $this->mysqli->insert_id;
		}
		
		return $rv;
	}
	
	public function update_record($pers_rec, $personal_info_id) {
		$sql = "UPDATE personal_info SET first_name='" . $pers_rec ['first_name'] . "', last_name='" . $pers_rec ['last_name'] . "', middle_name='" . $pers_rec ['middle_name'];
		$sql .= "', cell_phone='" . $pers_rec ['cell_phone'] . "', home_phone='" . $pers_rec ['home_phone'] . "', email='" . $pers_rec ['email']; // . "', icsgv_member'" . $pers_rec[''];
		$sql .= "', address='" . $pers_rec ['address'] . "', city='" . $pers_rec ['city'] . "', zipcode='" . $pers_rec ['zipcode'] . "'";
		// $sql .= "', address='" . $pers_rec['address'] . "', city='" . $pers_rec['city'] . "', state='" . $pers_rec['state'] . "', zipcode='" . $pers_rec['zipcode'] . "'";
		$sql .= " WHERE personal_info.id_pi='" . $personal_info_id . "'";
		
		// print $sql . "<BR>";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 11 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_user_email_passwd($user, $email, $passwd, $access, $personal_info_id) {
		$sql = "UPDATE personal_info SET user='" . $user . "', email='" . $email . "', password='" . $passwd . "', access='" . $access . "' WHERE ( personal_info.id_pi = '" . $personal_info_id . "' )";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 12 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_access($access, $personal_info_id) {
		$sql = 'UPDATE personal_info SET access="' . $access . '" WHERE  (id = "' . $personal_info_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 13 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function reset_user_passwd($personal_info_id) {
		$sql = 'UPDATE personal_info SET user="", password="" WHERE  (id = "' . $personal_info_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 14 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function update_address_phone($personal_info_id, $new_home_phone, $new_address, $new_city, $new_zipcode) {
		$sql = "UPDATE personal_info SET  home_phone = '$new_home_phone', address='$new_address', city='$new_city', zipcode = '$new_zipcode'";
		$sql .= " WHERE id_pi = '" . $personal_info_id . "'";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PERSONAL_INFO + 15 ) . ': ' . $this->mysqli->error );
		}
	}
}

?>
