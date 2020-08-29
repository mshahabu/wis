<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class StaffIf {
    
    // property declaration
    private $mysqli;
    function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    public function get_record($staff_id) {
        $sql = 'SELECT * FROM staff WHERE (id = "' . $staff_id . '")';
        
        $result = $this->mysqli->query ( $sql );
        if (! $result) {
            die ( 'Invalid query ' . strval ( Errno::STAFF + 1 ) . ': ' . $this->mysqli->error );
        }
        
        return $result->fetch_assoc ();
    }
    
    public function get_personal_info_id($staff_id) {
        $sql = 'SELECT personal_info_id FROM staff WHERE (id_staff = "' . $staff_id . '")';
        
        $result = $this->mysqli->query ( $sql );
        if (! $result) {
            die ( 'Invalid query ' . strval ( Errno::STAFF + 2 ) . ': ' . $this->mysqli->error );
        }
        
        $info = $result->fetch_assoc ();
        
        return $info ['personal_info_id'];
    }

    public function get_all_staff_ids() {
        $sql = "SELECT id_staff FROM staff ";
        
        $result = $this->mysqli->query ( $sql );
        if (! $result) {
            die ( 'Invalid query ' . strval ( Errno::STAFF + 3 ) . ': ' . $this->mysqli->error );
        }
        
        // Put them in array
        $info = array ();
        for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
            $info [$i] = $res ['id_staff'];
        }
        $result->close ();
        
        return $info;
    }
    
    public function get_staff_id($pers_info_id) {
        $sql = "SELECT id_staff FROM staff WHERE ( personal_info_id = '" . $pers_info_id . "' )";
        
        $result = $this->mysqli->query ( $sql );
        if (! $result) {
            die ( 'Invalid query ' . strval ( Errno::STAFF + 4 ) . ': ' . $this->mysqli->error );
        }
        $info = $result->fetch_assoc ();
        
        return $info ['id_staff'];
    }
    
    public function insert_record($trans) {
        $sql = "INSERT INTO staff SET personal_info_id = '" . $trans ['personal_info_id'] . "', ";
        $sql .= "staff_status = '" . $trans ['staff_status'] . "', hire_date = '" . $trans ['hire_date'] . "', ";
        $sql .= "termination_date = '" . $trans ['termination_date'] . "'";
        
        $result = $this->mysqli->query ( $sql );
        if (! $result) {
            die ( 'Invalid query ' . strval ( Errno::STAFF + 5 ) . ': ' . $this->mysqli->error );
        }
        return $this->mysqli->insert_id;
    }
}
?>
