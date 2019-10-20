<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class GradeBookIf {
	
	// property declaration
	private $first_name;
	private $middle_name;
	private $last_name;
	private $cell_phone;
	private $home_phode;
	private $address;
	private $city;
	private $state;
	private $email;
	private $password;
	private $user_name;
	private $access;
	private $member;
	private $parent_id;
	private $mysqli;
	public function ping() {
		print 'I am Book <BR>';
	}
	
	function __construct($mysqli_h) {
		$this->mysqli = $mysqli_h;
	}
	
	public function get_record($book_id) {
		$sql = "SELECT book_name, author_name, publisher, cost, grade";
		$sql .= " FROM grade_books WHERE (id_gb = '" . $book_id . "')";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::GRADE_BOOK + 1 ) . ': ' . $this->mysqli->error );
		}
		return $result->fetch_assoc ();
	}
	
	public function get_Qgrade_book_list($grade) {
		$sql = "SELECT book_name, cost, grade, id_gb FROM grade_books ";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::GRADE_BOOK + 1 ) . ': ' . $this->mysqli->error );
		}
		
		// Put them in array
		$info = array ();
		$j = 0;
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
		        //print 'Grade to look at is ' . $grade . ' GRADE is ' . $res['grade'] . '<BR>';
			//print 'BK_NM ' . $res['book_name'] . ' ; cost ' . $res['cost'] . '<BR>';
			if ($res ['grade'] & wis_convert_grade_to_num ( $grade )) {
				$info [$j] = $res;
				$j ++;
			}
		}
		$result->close ();
		
		return $info;
	}
	
	public function get_all_records() {
		$sql = "SELECT * FROM grade_books ";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::GRADE_BOOK + 2 ) . ': ' . $this->mysqli->error );
		}
		// Put them in array
		$info = array ();
		for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
			$info [$i] = $res;
		}
		$result->close ();
		
		return $info;
	}
	
	public function insert_record($book_name, $author, $publisher, $cost, $grade) {
		$sql = 'INSERT INTO grade_books (book_name, author_name, publisher, cost, grade ) ';
		$sql .= ' VALUES ("' . $book_name . '","' . $author . '","' . $publisher . '","' . $cost . '", ' . $grade . ')';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::GRADE_BOOK + 3 ) . ': ' . $this->mysqli->error );
		}
		
		$parent_id = mysql_insert_id ();
		
		return $this->mysqli->insert_id;
	}
	
	public function update_record($book_id, $book_name, $author, $publisher, $cost, $grade) {
		$sql = 'UPDATE grade_books SET book_name="' . $book_name . '", author_name="' . $author . '", publisher="';
		$sql .= $_REQUEST ['publisher'] . '", cost=' . $cost . ', grade=' . $grade . ' WHERE id_gb="' . $book_id . '"';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::GRADE_BOOK + 4 ) . ': ' . $this->mysqli->error );
		}
	}
}

?>
