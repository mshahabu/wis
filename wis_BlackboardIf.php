<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class BlackboardIf {
	// property declaration
	private $id;
	private $mysqli;
	
	public function ping() {
		print 'I am Black Board <BR>';
	}
	
	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function file_exists($file_name) {
		$sql = 'SELECT file_name FROM black_board WHERE (file_name = "' . $file_name . '" )';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 1 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		if (empty ( $info ['file_name'] )) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function file_title_exists($file_title) {
		$sql = 'SELECT file_title FROM black_board WHERE (file_title = "' . $file_title . '" )';
		
		$result = $this->mysqli->query ( $sql );
		
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 2 ) . ': ' . $this->mysqli->error );
		}
		
		$info = $result->fetch_assoc ();
		$result->close ();
		
		if (empty ( $info ['file_title'] )) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function get_all_files() {
		$sql = 'SELECT file_name, file_title, comments, grade, section FROM black_board ';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 3 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_file_name_title($grade, $section) {
		$sql = 'SELECT file_name, file_title, comments, student_visible FROM black_board WHERE (grade = "' . $grade . '" && section = "' . $section . '" )';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 3 ) . ': ' . $this->mysqli->error );
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
		$sql = 'SELECT * FROM black_board';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 4 ) . ': ' . $this->mysqli->error );
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
		$sql = "INSERT INTO black_board SET ";
		$sql .= "file_name       = '" . $trans ['file_name'] . "', ";
		$sql .= "file_title      = '" . $trans ['file_title'] . "', ";
		$sql .= "grade           = '" . $trans ['grade'] . "', ";
		$sql .= "student_visible = '" . $trans ['file_visible'] . "', ";
		if (! empty ( $trans ['comments'] )) {
			$sql .= "comments   = '" . $trans ['comments'] . "', ";
		}
		$sql .= "section    = '" . $trans ['section'] . "';";
		// print "SQL: ". $sql . "<BR>";
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 5 ) . ': ' . $this->mysqli->error );
		}
		return $this->mysqli->insert_id;
	}
	
	public function update_file($grade, $section, $orig_title, $modify_title, $comment, $vis) {
		$sql = "UPDATE black_board SET ";
		$sql .= " file_title ='" . $modify_title . "', comments ='" . $comment . "', student_visible = " . $vis;
		$sql .= " WHERE (grade ='" . $grade . "' && section ='" . $section . "' && file_title='" . $orig_title . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 6 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function delete_file($grade, $section, $file_title) {
		$sql = "DELETE from black_board ";
		$sql .= " WHERE (grade ='" . $grade . "' && section ='" . $section . "' && file_title='" . $file_title . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BLACK_BOARD + 7 ) . ': ' . $this->mysqli->error );
		}
	}
}
?>
