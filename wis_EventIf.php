<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class EventIf {
	// property declaration
	private $mysqli;

	public function ping() {
		print 'I am Event <BR>';
	}

	function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}

	public function get_record($month) {
		$sql = 'SELECT * FROM events WHERE (month = "' . $month . '")';
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::EVENTS + 1 ) . ': ' . $this->mysqli->error );
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
		$sql = "INSERT INTO events SET date = '" . $trans ['date'] . "', ";
		$sql .= "event_desc = '" . $trans ['event_desc'] . "', month = '" . $trans ['month'] . "', ";
		$sql .= "time = '" . $trans ['time'] . "', event_cnt = '" . $trans ['event_cnt'] . "', ";
		$sql .= "school_year = '" . $trans ['school_year'] . "'";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::EVENTS + 2 ) . ': ' . $this->mysqli->error );
		}
		
		return $this->mysqli->insert_id;
	}

	public function update_record($date, $event, $ev_time, $month, $ev_cnt) {
		$sql = "UPDATE events SET date = '" . $date . "', event_desc = '" . $event . "', time = '" . $ev_time . "' ";
		$sql .= " WHERE (month = '" . $month . "' && event_cnt = " . $ev_cnt . ")";
		
		$result = $this->mysqli->query ( $sql );
		if (! $result) {
			die ( 'Invalid query ' . strval ( Errno::EVENTS + 3 ) . ': ' . $this->mysqli->error );
		}
	}
}
?>        