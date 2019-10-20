<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class ParentIf {
	// property declaration
	private $mysqli;
	
	public function ping() {
		print 'I am Parent <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function get_record($parent_id) {
		$sql = 'SELECT * FROM parent WHERE (id_parent = "' . $parent_id . '")';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PARENT + 1 ) . ': ' . $this->mysqli->error );
		}
		
		return $result->fetch_assoc ();
	}
	
	public function get_email($parent_id) {
		$sql = "SELECT par_email FROM parent WHERE (id_parent= '" . $parent_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PARENT + 2 ) . ': ' . $this->mysqli->error );
		}
		$info = $result->fetch_assoc ();
		
		return $info ['par_email'];
	}
	
	public function insert_record($parent_rec) {
		$sql = "INSERT INTO parent (mother_last_name, mother_middle_name, mother_first_name, father_last_name, father_middle_name, father_first_name";
		$sql .= ", par_email, mother_cell_phone, father_cell_phone ) ";
		$sql .= " VALUES ('" . $parent_rec ['mother_last_name'] . "','" . $parent_rec ['mother_middle_name'] . "','" . $parent_rec ['mother_first_name'] . "'";
		$sql .= ", '" . $parent_rec ['father_last_name'] . "','" . $parent_rec ['father_middle_name'] . "','" . $parent_rec ['father_first_name'] . "'";
		$sql .= ", '" . $parent_rec ['par_email'] . "','" . $parent_rec ['mother_cell_phone'] . "','" . $parent_rec ['father_cell_phone'] . "')";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PARENT + 3 ) . ': ' . $this->mysqli->error );
		}
		$parent_id = mysql_insert_id ();
		
		return $this->mysqli->insert_id;
	}
	
	public function update_record($parent_rec, $parent_id) {
		$sql = "UPDATE parent SET mother_last_name='" . $parent_rec ['mother_last_name'] . "', mother_first_name='" . $parent_rec ['mother_first_name'] . "', mother_middle_name='" . $parent_rec ['mother_middle_name'];
		$sql .= "', mother_cell_phone='" . $parent_rec ['mother_cell_phone'] . "', father_last_name='" . $parent_rec ['father_last_name'] . "', father_first_name='" . $parent_rec ['father_first_name'];
		$sql .= "', father_middle_name='" . $parent_rec ['father_middle_name'] . "', father_cell_phone='" . $parent_rec ['father_cell_phone'] . "', par_email='" . $parent_rec ['par_email'] . "' ";
		$sql .= " WHERE id_parent = '" . $parent_id . "'";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PARENT + 4 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function find_parent() {
		$sql = "SELECT id_parent FROM parent WHERE (par_email= '" . $_REQUEST ['par_email'] . "' AND father_first_name= '" . $_REQUEST ['father_first_name'] . "' AND father_last_name= '" . $_REQUEST ['father_last_name'] . "')";
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::PARENT + 5 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		
		return $info;
	}
}

?>
