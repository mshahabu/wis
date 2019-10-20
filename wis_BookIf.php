<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class BookIf {
	
	// property declaration
	private $mysqli;
	public function ping() {
		print 'I am Book <BR>';
	}
	
	function __construct($mysqli_h) {
		$this->mysqli = $mysqli_h;
	}
	
	public function get_record($student_id) {
		$sql = "SELECT grade_books_id";
		$sql .= " FROM books WHERE (student_id = '" . $student_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 1 ) . ': ' . $this->mysqli->error );
		}
		// Put them in array
		$info = array ();
		if ($result->num_rows==1) {
			$info = $result->fetch_assoc ();
			$result->close ();
		}
		return $info;
	}
	
	public function insertRecord($student_id, $grade_book_id) {
		$sql = 'INSERT INTO books (grade_books_id, needed, student_id ) ';
		$sql .= ' VALUES ("' . $grade_book_id . '",0, ' . $student_id . ')';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 2 ) . ': ' . $this->mysqli->error );
		}
		
		$parent_id = mysql_insert_id ();
		
		return $this->mysqli->insert_id;
	}
	
	public function updateRecord($grade_book_id, $needed, $student_id) {
		$sql = 'UPDATE books SET grade_book_id="' . $grade_book_id . '", needed="' . $needed . '", student_id="';
		$sql .= $student_id .  '"';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 3 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function updateBookNeeded($student_id, $grade_book_id, $needed) {
		$sql = 'UPDATE books SET needed="' . $needed . '" ';
		$sql .= ' WHERE grade_books_id="' . $grade_book_id . '" &&  student_id="' . $student_id .  '"';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 4 ) . ': ' . $this->mysqli->error );
		}
	}
	
	public function bookNotExist($student_id, $grade_book_id) {
		$rv = true;
		
		$sql = "SELECT id_books";
		$sql .= " FROM books WHERE (student_id = '" . $student_id . "' && grade_books_id= '" . $grade_book_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 5 ) . ': ' . $this->mysqli->error );
		}
		if ($result->num_rows==1) {
			$info = $result->fetch_assoc ();
			$result->close ();
			$rv = false;
		}
		
		return $rv;
	}

	public function isBookNeeded($student_id, $grade_book_id) {
		$rv = false;
		
		$sql = "SELECT needed";
		$sql .= " FROM books WHERE (student_id = '" . $student_id . "' && grade_books_id= '" . $grade_book_id . "')";
		//print "SQL: " . $sql . "<BR>";
		$result = $this->mysqli->query ( $sql );
		if (!$result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 6 ) . ': ' . $this->mysqli->error );
		}

		$info = $result->fetch_assoc ();
		$result->close ();

		if ($info['needed'] != 0) {
		    $rv = true;
		}
		return $rv;
	}

	public function yearEndCleanBooks() {
	        $sql = "TRUNCATE table books";
	    
		$result = $this->mysqli->query ( $sql );
		if (!$result) {
			die ( 'Invalid query ' . strval ( Errno::BOOK + 7 ) . ': ' . $this->mysqli->error );
		}
	}
}

?>
