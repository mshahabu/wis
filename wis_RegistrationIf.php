<?php
// Developed by Product Line Software (PLS) Inc.
// Date 8/1/2016
// Version 2.1

include_once 'wis_Errno.php';

class RegistrationIf {
        
        const STUDENT_ID = 1;
        const SCHOOL_YEAR = 2;
        const WIS_GRADE = 3;
        const WIS_SECTION = 4;
        const REG_GRADE = 5;
        const APPROVED_BY = 6;
        const ICSGV_MEM = 7;
        const NUM_SIBLINGS = 8;
        const PAYMENT_PLAN = 9;
        const STATUS = 10;
        
        // property declaration
        private $mysqli;
        function __construct($mysqli) {
                $this->mysqli = $mysqli;
        }
        
        public function get_record($sid, $school_year) {
                $sql = 'SELECT * FROM registration WHERE (student_id = "' . $sid . '" && school_year = "' . $school_year . '")';
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 1 ) . ': ' . $this->mysqli->error );
                }
                
                $info = $result->fetch_assoc ();
                $result->close ();
                
                return $info;
        }
        
        public function get_approval_date($sid) {
                $sql = 'SELECT approval_date FROM registration WHERE (student_id = "' . $sid . '")';
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 2 ) . ': ' . $this->mysqli->error );
                }
                
                $info = $result->fetch_assoc ();
                $result->close ();
                
                return $info ['approval_date'];
        }
        
        public function get_records($query_for, $value, $school_year = 'ALL', $status = 'ALL') {
                switch ($query_for) {
                        
                        case RegistrationIf::STUDENT_ID :
                                $sql = 'SELECT * FROM registration WHERE (student_id = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::WIS_GRADE :
                                list ( $grade, $section ) = split ( '[-]', $value );
                                if (empty ( $section )) {
                                        $sql = 'SELECT * FROM registration WHERE (wis_grade = "' . $grade . '"';
                                } else {
                                        $sql = 'SELECT * FROM registration WHERE (wis_grade = "' . $grade . '" && section = "' . $section . '"';
                                }
                                break;
                        
                        case RegistrationIf::REG_GRADE :
                                $sql = 'SELECT * FROM registration WHERE (reg_school_grade = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::APPROVED_BY :
                                $sql = 'SELECT * FROM registration WHERE (approved_by = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::ICSGV_MEM :
                                $sql = 'SELECT * FROM registration WHERE (icsgv_mem = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::NUM_SIBLINGS :
                                $sql = 'SELECT * FROM registration WHERE (num_siblings = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::PAYMENT_PLAN :
                                $sql = 'SELECT * FROM registration WHERE (payment_plan = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::SCHOOL_YEAR :
                                $sql = 'SELECT * FROM registration WHERE (school_year = "' . $value . '"';
                                break;
                        
                        case RegistrationIf::STATUS :
                                $sql = 'SELECT * FROM registration WHERE (reg_status = "' . $value . '"';
                                break;
                        
                        default :
                                break;
                }
                
                if ($query_for != RegistrationIf::SCHOOL_YEAR && $school_year !== 'ALL') {
                        $sql .= ' && school_year = "' . $school_year . '"';
                }
                if ($query_for != RegistrationIf::STATUS && $status !== 'ALL') {
                        $sql .= ' && reg_status = "' . $status . '"';
                }
                $sql .= ')';
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 3 ) . ': ' . $this->mysqli->error );
                }
                
                // Put them in array
                $info = array ();
                for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
                        $info [$i] = $res;
                }
                $result->close ();
                
                return $info;
        }
        
        public function get_student_status_ids($reg_status, $school_year) {
                if ($reg_status === 'ALL' && $school_year == 'ALL' ) {
                        $query = "";
                } else if ($reg_status === 'ALL') {
                        $query = "(school_year='" . $school_year . "')";
                } else {
                        $query = "(reg_status='" . $reg_status . "' && school_year='" . $school_year . "')";
                        // $query = "(reg_school_grade = NULL || reg_school_grade = ' ')";
                }
                //$sql = "SELECT student_id FROM registration WHERE " . $query;
                $sql = "SELECT student_id FROM registration ";
                if (!empty($query)) {
                        $sql .= " WHERE " . $query;
                }
                // print "SQL : " . $sql . "<BR>";
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 4 ) . ': ' . $this->mysqli->error );
                }
                
                $info = array ();
                for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
                        $info [$i] = $res;
                }
                $result->close ();
                
                return $info;
        }
        
        public function get_student_ids($school_year) {
                if ($school_year !== 'ALL') {
                        $query = "WHERE (school_year='" . $school_year . "')";
                }
                
                $sql = "SELECT student_id FROM registration " . $query;
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 5 ) . ': ' . $this->mysqli->error );
                }
                
                $info = array ();
                for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
                        $info [$i] = $res;
                }
                $result->close ();
                
                return $info;
        }
        
        public function get_student_grade_ids($reg_status, $grade, $section, $school_year) {
                if ($reg_status === 'ALL' && $grade === 'ALL') {
                        $query = "(school_year='" . $school_year . "')";
                } else if ($grade === 'ALL') {
                        $query = "(school_year='" . $school_year . "' && reg_status ='" . $reg_status . "')";
                } else if ($reg_status === 'ALL') {
                        if ($section == 'A' || empty ( $section )) {
                                $query = "(wis_grade = '" . $grade . "' && ";
                                $query .= " (section = 'A' or section IS NULL or section = '') && ";
                                $query .= " school_year='" . $school_year . "')";
                        } else {
                                $query = "(wis_grade = '" . $grade . "' && section = '" . $section . "' && school_year='" . $school_year . "')";
                        }
                } else if ($section === 'ALL') {
                    if ($reg_status === 'APP_PENDING') {
                        $query = "(wis_grade = '" . $grade . "' && school_year='" . $school_year . "' && (reg_status='APPROVED' || reg_status='PENDING'))";
                    } else {
                        $query = "(wis_grade = '" . $grade . "' && school_year='" . $school_year . "' && reg_status = '" . $reg_status . "')";
                    }
                } else {
                    if ($reg_status === 'APP_PENDING') {
                        $query = "(wis_grade = '" . $grade . "' && section = '" . $section . "' && school_year='" . $school_year . "' && (reg_status='APPROVED' || reg_status='PENDING'))";
                    } else {
                        $query = "(wis_grade = '" . $grade . "' && section = '" . $section . "' && school_year='" . $school_year . "' && reg_status ='" . $reg_status . "')";
                    }
                }
                
                $sql = "SELECT student_id FROM registration WHERE " . $query;
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 6 ) . ': ' . $this->mysqli->error );
                }
                
                $info = array ();
                for($i = 0; ($res = $result->fetch_assoc ()); $i ++) {
                        $info [$i] = $res;
                }
                $result->close ();
                
                return $info;
        }
        
        public function insert_record($trans) {
                $sql = "INSERT INTO registration SET student_id = '" . $trans ['student_id'] . "', ";
                $sql .= "register_date = '" . $trans ['register_date'] . "', reg_school_grade = '" . $trans ['reg_school_grade'] . "', ";
                // $sql .= "wis_grade = '" . $trans['wis_grade'] . "', section = '" . $trans['section'] . "', ";
                $sql .= "allergies = '" . $trans ['allergies'] . "', medications = '" . $trans ['medications'] . "', ";
                $sql .= "auth_person1 = '" . $trans ['auth_person1'] . "', auth_person2 = '" . $trans ['auth_person2'] . "', ";
                $sql .= "address_ap1 = '" . $trans ['address_ap1'] . "', address_ap2 = '" . $trans ['address_ap2'] . "', ";
                $sql .= "driver_lic_ap1 = '" . $trans ['driver_lic_ap1'] . "', driver_lic_ap2 = '" . $trans ['driver_lic_ap1'] . "', ";
                $sql .= "phone_ap1 = '" . $trans ['phone_ap1'] . "', phone_ap2 = '" . $trans ['phone_ap2'] . "', ";
                // $sql .= "approved_by = '" . $trans['approved_by'] . "', approval_date = '" . $trans['approval_date'] . "', ";
                $sql .= "school_year = '" . $trans ['school_year'] . "',  icsgv_mem = '" . $trans ['icsgv_mem'] . "', ";
                $sql .= "num_siblings = '" . $trans ['num_siblings'] . "',  parent_volun_1= '" . $trans ['parent_volun_1'] . "', ";
                $sql .= "parent_volun_2 = '" . $trans ['parent_volun_2'] . "', ";
                $sql .= "payment_plan = '" . $trans ['payment_plan'] . "', reg_status = '" . $trans ['reg_status'] . "'";
                
                $result = $this->mysqli->query ( $sql );
                
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 7 ) . ': ' . $this->mysqli->error );
                }
                return $this->mysqli->insert_id;
        }
        
        public function update_grade_section($student_id, $grade, $section) {
                $sql = "UPDATE registration SET section='" . $section . "', wis_grade='" . $grade;
                $sql .= "' WHERE (student_id='" . $student_id . "')";
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 8 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function update_miscl_charges_mp_planfee($student_id, $miscl_charges, $mp_planfee) {
                $sql = "UPDATE registration SET miscl_charges='" . $miscl_charges . "', multi_payplan_fee='" . $mp_planfee;
                $sql .= "' WHERE (student_id='" . $student_id . "')";
        
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 9 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function update_approve_grade_section($student_id, $grade, $section) {
                $sql = "UPDATE registration SET reg_status='APPROVED', section='" . $section . "', wis_grade='" . $grade;
                $sql .= "' WHERE (student_id='" . $student_id . "')";
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 10 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function update_record($reg_rec, $student_id, $return_student=false) {
                $sql = "UPDATE registration SET ";
                $sep = true;
                if (isset ( $reg_rec ['icsgv_mem'] )) {
                        $sql .= " icsgv_mem = '" . $reg_rec ['icsgv_mem'] . "'";
                } else {
                        $sql .= " icsgv_mem = '0' ";
                }
		if ($return_student==false) {
		    if (isset ( $reg_rec ['wis_grade'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " wis_grade = '" . $reg_rec ['wis_grade'] . "'";
		    } else {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " wis_grade = NULL ";
		    }
		    if (isset ( $reg_rec ['section'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " section   = '" . $reg_rec ['section'] . "'";
                        $sep = true;
		    } else {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " section   = NULL ";
		    }
		}
                if (isset ( $reg_rec ['tutionWaiver'] )) {
		        if ($sep)
                                $sql .= ",";
                        if($reg_rec ['tutionWaiver']) {
                                $sql .= " tutionWaiver =  1";
                        } else {
                                $sql .= " tutionWaiver =  0";
                        }
		}
                if (! empty ( $reg_rec ['approved_by'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " approved_by = '" . $reg_rec ['approved_by'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['approval_date'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " approval_date='" . $reg_rec ['approval_date'] . "'";
                        $sep = true;
                }
                
                if (! empty ( $reg_rec ['num_siblings'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " num_siblings = '" . $reg_rec ['num_siblings'] . "'";
                        $sep = true;
                }
                
                if (! empty ( $reg_rec ['parent_volun_1'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " parent_volun_1='" . $reg_rec ['parent_volun_1'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['parent_volun_2'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " parent_volun_2='" . $reg_rec ['parent_volun_2'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['allergies'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " allergies='" . $reg_rec ['allergies'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['reg_status'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " reg_status = '" . $reg_rec ['reg_status'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['medications'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " medications='" . $reg_rec ['medications'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['register_date'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " register_date='" . $reg_rec ['register_date'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['auth_person1'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " auth_person1='" . $reg_rec ['auth_person1'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['address_ap1'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " address_ap1='" . $reg_rec ['address_ap1'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['phone_ap1'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " phone_ap1='" . $reg_rec ['phone_ap1'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['driver_lic_ap1'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " driver_lic_ap1='" . $reg_rec ['driver_lic_ap1'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['auth_person2'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " auth_person2='" . $reg_rec ['auth_person2'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['address_ap2'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " address_ap2='" . $reg_rec ['address_ap2'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['phone_ap2'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " phone_ap2='" . $reg_rec ['phone_ap2'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['driver_lic_ap2'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " driver_lic_ap2='" . $reg_rec ['driver_lic_ap2'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['reg_school_grade'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " reg_school_grade='" . $reg_rec ['reg_school_grade'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['waiver_signed_by'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " waiver_signed_by='" . $reg_rec ['waiver_signed_by'] . "'";
                        $sep = true;
                }
                if (! empty ( $reg_rec ['form_signed_by'] )) {
                        if ($sep)
                                $sql .= ",";
                        $sql .= " form_signed_by='" . $reg_rec ['form_signed_by'] . "'";
                        $sep = true;
                }
                $sql .= " WHERE (student_id='" . $student_id . "')";
                // print "SQL: " . $sql;
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 11 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function get_id($student_id) {
                $sql = "SELECT student_id, wis_grade, section ";
                $sql .= " , school_year, registration.reg_status ";
                $sql .= " FROM student, registration WHERE (wis_grade = '" . $grade . "' && section = '" . $section . "' && registration.reg_status = 'APPROVED' && student.id = student_id ) ";
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 12 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function get_wis_grade($student_id) {
                $sql = "SELECT wis_grade FROM registration WHERE (student_id='" . $student_id . "')";

                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 13 ) . ': ' . $this->mysqli->error );
                }
                
                $info = $result->fetch_assoc ();
                return $info['wis_grade'];
        }
        
        public function isIcsgvMember($student_id) {
                $sql = "SELECT icsgv_mem FROM registration WHERE (student_id='" . $student_id . "')";

                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 14 ) . ': ' . $this->mysqli->error );
                }
                
                $info = $result->fetch_assoc ();
                return $info['icsgv_mem'];
        }
        
         public function waiveTution($student_id) {
                $sql = "SELECT tutionWaiver FROM registration WHERE (student_id='" . $student_id . "')";
 
                 $result = $this->mysqli->query ( $sql );
                 if (! $result) {
                         die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 15 ) . ': ' . $this->mysqli->error );
                 }
 
                 $info = $result->fetch_assoc ();
                 return $info['tutionWaiver'];
         }
        
        public function set_num_siblings($student_id, $sib_cnt) {
                $sql = "UPDATE registration SET num_siblings='" . $sib_cnt ;
                $sql .= "' WHERE (student_id='" . $student_id . "')";
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 16 ) . ': ' . $this->mysqli->error );
                }
        }
        
        public function new_school_year($student_id, $wis_grade, $school_year) {
                $sql  = "UPDATE registration SET ";
                $sql .= " approved_by=NULL, approval_date=NULL, ";
                $sql .= " parent_volun_1=NULL, parent_volun_2=NULL, ";
                $sql .= " waiver_signed_by=NULL, form_signed_by=NULL, ";
                $sql .= " register_date='2020-08-24', ";
                $sql .= " school_year='" . $school_year . "', ";
                $sql .= " wis_grade='" . $wis_grade . "', ";
                $sql .= " miscl_charges='0.0', ";
                $sql .= " multi_payplan_fee='0.0', ";
                $sql .= " reg_status='PENDING' ";
                $sql .= " WHERE (student_id='" . $student_id . "') "; 
                
                $result = $this->mysqli->query ( $sql );
                if (! $result) {
                        die ( 'Invalid query ' . strval ( Errno::REGISTRATION + 17 ) . ': ' . $this->mysqli->error );
                }
                
        }
        
}
?>        