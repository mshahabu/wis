<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_TeacherIf.php';
include_once 'wis_StaffIf.php';
include_once 'wis_StudentIf.php';
include_once 'wis_PersonalInfoIf.php';
include_once 'wis_AdministrationIf.php';

class Administration {

    private $administrationIf;
    private $personalInfoIf;
    private $staffIf;
    private $studentIf;
    private $teacherIf;
    private $mysqli_h;
    
    /* ---------- PUBLIC FUNCTIONS ---------------- */
    function __construct($mysqli_h) {
        $this->mysqli_h         = $mysqli_h;
        $this->personalInfoIf   = new PersonalInfoIf ( $mysqli_h );
        $this->staffIf          = new StaffIf ( $mysqli_h );
        $this->studentIf        = new StudentIf ( $mysqli_h );
        $this->teacherIf        = new TeacherIf ( $mysqli_h );
        $this->administrationIf = new AdministrationIf ( $mysqli_h );
    }
    
    public function setup_school_days($si) {
    
        include ("wis_header.php");
    
        wis_main_menu ( $this->mysqli_h, TRUE );
        
        print '<div id="printableArea">';
    
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"></LEGEND>';
        
        print "<H4> Setup School Days " . $this->administrationIf->get_school_year() . " </H4>";
        
        $this->days_table(0);
        print "<BR>";
        $this->days_table(15);
        print "<BR>";
        $this->days_table(30);

        print "</FIELDSET>";
        print "</div>";
        
        setSubmitValue ( "recordSchoolDays" );
        
        wis_footer ( TRUE );
    }
    
    public function record_school_days() {
        
        $days = array();
        for($i = 1; $i <= 45; $i++) {
            array_push($days, $_REQUEST['date_' . $i]);
            //print "DAY: " . $_REQUEST['date_' . $i] . "<BR>";
        }

        $array_string = $this->mysqli_h->real_escape_string(serialize($days));
        
        $id_count = $this->administrationIf->get_id_row_count ();
        
        if ($id_count == 1) {
            $this->administrationIf->update_school_days($array_string);
        } else {
            die("Something is wrong, contact program administrator");
        }
        
        $rv_array_string = $this->administrationIf->get_school_days();
        
        $array= unserialize($rv_array_string);
        //print_r($array);
    }
    
    private function days_table($si) {
        $start = 1 + $si;
        $stop = 15 + $si;
        
        print "<table class=sample>";
        
        print "<col width='45'>";
        print "<tr>";
        //print "<td> </td>";
        // print "<th style='border: 1px single black; background-color: #CCCC99; color :#330000;'>Student ID </th>";
        for($i = $start; $i <= $stop; $i++) {
            print "<th>" . $i . "</th>";
        }
        //print "<td> </td>";
        print "</tr>";
        print "<tr>";
        //print "<td> </td>";
        //print "<td>&nbsp</td>";
        
        $rv_array_string = $this->administrationIf->get_school_days();
        $days_array= unserialize($rv_array_string);
        
        for($i = $start; $i <= $stop; $i++) {
            $val = $days_array[($i-1)];
            print "<td><input type=text class='datepicker3' name='date_" . $i . "' ";
            //print "<td><input type=text name=date_'" . $i . "' ";
            if (empty($days_array[($i-1)])) {
                print " placeholder='mm/dd' ";
            } else {
                print " value='" . $val . "' ";
            }
            print " style='font-size:10px;' size=5 maxlength=5 ></td>";
        }
        //print "<td> </td>";
        print "</tr>";

        print "</table>";
    
    }
    
    function allocate_fees() {
        include ("wis_header.php");
        
        wis_main_menu ( $this->mysqli_h, FALSE );
        
        print '<div id="printableArea">';
        
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"></LEGEND>';
        
        $info = $this->administrationIf->get_tution_discounts ();
        
        print "<table>";
        
        print "<tr>";
        print "<td class='normal1'>Tution Fee </td>";
        print "<td>$<input type=text name='tution_fee' ";
        if (isset ( $info ['tution_fee'] )) {
            print " value=" . $info ['tution_fee'];
        }
        print " size=5 maxlength=7 ></td>";
        print "</tr>";
        print "<tr>";
        print "<td class='normal1'>Sibling Discount </td>";
        print "<td>$<input type=text name='sibling_discount' ";
        if (isset ( $info ['sibling_discount'] )) {
            print " value=" . $info ['sibling_discount'];
        }
        print " size=5 maxlength=7 ></td>";
        print "</tr>";
        print "<tr>";
        print "<td class='normal1'>Member Discount </td>";
        print "<td>$<input type=text name='member_discount' ";
        if (isset ( $info ['icsgv_mem_discount'] )) {
            print " value=" . $info ['icsgv_mem_discount'];
        }
        print " size=5 maxlength=7 ></td>";
        print "</tr>";
        print "<tr>";
        print "<td class='normal1'>Payment plan fee </td>";
        print "<td>$<input type=text name='pay_plan_fee' ";
        if (isset ( $info ['payment_plan_fee'] )) {
            print " value=" . $info ['payment_plan_fee'];
        }
        print " size=5 maxlength=7 ></td>";
        print "</tr>";
        
        print "</table>";
        
        print "</FIELDSET>";
        print "</div>";
        
        setSubmitValue ( "setupFees" );
        
        wis_footer ( TRUE );
    }

    function enter_fee_allocation() {
        $id_row_count = $this->administrationIf->get_id_row_count ();
        
        $this->administrationIf->set_tution_fee ( $_REQUEST ['tution_fee'] );
        $this->administrationIf->set_sibling_discount ( $_REQUEST ['sibling_discount'] );
        $this->administrationIf->set_icsgv_mem_discount ( $_REQUEST ['member_discount'] );
        $this->administrationIf->set_payment_plan_fee ( $_REQUEST ['pay_plan_fee'] );
        
        if ($id_row_count > 1 || $id_row_count == 0) {
            print '<label style="color:red;"> Administrator, please setup the school year first </label><BR>';
            return;
        }
    }

    function reset_staff_password($sid) {
        include ("wis_header.php");
        
        wis_main_menu ( $this->mysqli_h, FALSE );
        
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"></LEGEND>';
        
        print '<div id="printableArea">';
        
        $this->staff_info ( $sid );
        
        print "</FIELDSET>";
        
        wis_footer ( TRUE );
    }
    
    function reset_student_password($sid) {
        include ("wis_header.php");
        
        wis_main_menu ( $this->mysqli_h, FALSE );
        
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"></LEGEND>';
        
        print '<div id="printableArea">';
        
        $this->student_info ( $sid );
        
        print "</FIELDSET>";
        
        wis_footer ( TRUE );
    }
    
    function reset_teacher_password($tid) {
        include ("wis_header.php");
        
        wis_main_menu ( $this->mysqli_h, FALSE );
        
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"></LEGEND>';
        
        print '<div id="printableArea">';
        
        $this->teacher_info ( $tid );
        
        print "</FIELDSET>";
        
        wis_footer ( TRUE );
    }
    
    function get_staff_list() {
        $sinfo = $this->staffIf->get_all_staff_ids();
        
        for($i = 0; $i < count ( $sinfo ); $i ++) {
            $pers_info_id = $this->staffIf->get_personal_info_id ( $sinfo [$i] );
            
            // FIX: Sort name by alphabatical order
            $info = $this->personalInfoIf->get_name ( $pers_info_id );
            
            // print "NAME: first last " . $info[$i]['first_name'] . " " . $info[$i]['last_name'] . $sinfo[$i] . "<BR>";
            print '<li style="width:200px"><a href="wis_webIf.php?obj=administration&meth=reset_staff_password&a1=' . $sinfo [$i] . '">';
            print $sinfo [$i] . " - " . $info ['first_name'] . " ";
            if (isset ( $info ['middle_name'] )) {
                print $info ['middle_name'] . " ";
            }
            print $info ['last_name'] . '</a></li>';
        }
    }
    
    function get_student_list() {
        $sinfo = $this->studentIf->get_all_active_student_ids();
        
        for($i = 0; $i < count ( $sinfo ); $i ++) {
            $pers_info_id = $this->studentIf->get_personal_info_id ( $sinfo [$i] );
            
            // FIX: Sort name by alphabatical order
            $info = $this->personalInfoIf->get_name ( $pers_info_id );
            
            // print "NAME: first last " . $info[$i]['first_name'] . " " . $info[$i]['last_name'] . $sinfo[$i]['id_teacher'] . "<BR>";
            print '<li style="width:200px"><a href="wis_webIf.php?obj=administration&meth=reset_student_password&a1=' . $sinfo [$i] . '">';
            print $sinfo [$i] . " - " . $info ['first_name'] . " ";
            if (isset ( $info ['middle_name'] )) {
                print $info ['middle_name'] . " ";
            }
            print $info ['last_name'] . '</a></li>';
        }
    }
    
    function get_teacher_list() {
        $tinfo = $this->teacherIf->get_Qstatus_teacher_id ( 'ACTIVE' );
        
        for($i = 0; $i < count ( $tinfo ); $i ++) {
            $pers_info_id = $this->teacherIf->get_personal_info_id ( $tinfo [$i] ['id_teacher'] );
            
            // FIX: Sort name by alphabatical order
            $info = $this->personalInfoIf->get_name ( $pers_info_id );
            
            // print "NAME: first last " . $info[$i]['first_name'] . " " . $info[$i]['last_name'] . $tinfo[$i]['id_teacher'] . "<BR>";
            print '<li style="width:200px"><a href="wis_webIf.php?obj=administration&meth=reset_teacher_password&a1=' . $tinfo [$i] ['id_teacher'] . '">';
            print $tinfo [$i] ['id_teacher'] . " - " . $info ['first_name'] . " ";
            if (isset ( $info ['middle_name'] )) {
                print $info ['middle_name'] . " ";
            }
            print $info ['last_name'] . '</a></li>';
        }
    }
    
    /* ---------- PRIVATE FUNCTIONS ---------------- */
    private function staff_info($student_id) {
        print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
        print "<LEGEND><B>Student Record - Password Reset </B></LEGEND>";
        
        $pers_info_id = $this->staffIf->get_personal_info_id ( $student_id );
        print "<input type=hidden name='user_personal_info_id' value='" . $pers_info_id . "'>";
        
        $info = $this->personalInfoIf->get_record ( $pers_info_id );
            
        print "<table>";
        
        print "<tr>";
        print "<td class='normal1'>Staff ID</td>";
        print "<td> " . $student_id . " </td>";
        print "</tr>";
        
        print "<tr>";
        print "<td class='normal1'>Name</td>";
        print "<td> " . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . " </td>";
        print "</tr>";
        
        //if (isset ( $info ['last_name'] )) {
        //    print $info ['last_name'];
        //}
        
        print "<tr>";
        print "<td class='normal1'>Email </td>";
        print "<td> ";
        if (isset ( $info ['email'] )) {
            print $info ['email'];
        }
        print " </td>";
        print "</tr>";
        
        print "</table>";
        print " <BR> Are you sure you want password reset ? <BR>";
        print "</FIELDSET>";
        
        setSubmitValue ( "resetPassword" );
    }

    private function student_info($student_id) {
        print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
        print "<LEGEND><B>Student Record - Password Reset </B></LEGEND>";
        
        $pers_info_id = $this->studentIf->get_personal_info_id ( $student_id );
        print "<input type=hidden name='user_personal_info_id' value='" . $pers_info_id . "'>";
        
        $info = $this->personalInfoIf->get_record ( $pers_info_id );
            
        print "<table>";
        
        print "<tr>";
        print "<td class='normal1'>Student ID</td>";
        print "<td> " . $student_id . " </td>";
        print "</tr>";
        
        print "<tr>";
        print "<td class='normal1'>Name</td>";
        print "<td> " . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . " </td>";
        print "</tr>";
        
        //if (isset ( $info ['last_name'] )) {
        //    print $info ['last_name'];
        //}
        
        print "<tr>";
        print "<td class='normal1'>Email </td>";
        print "<td> ";
        if (isset ( $info ['email'] )) {
            print $info ['email'];
        }
        print " </td>";
        print "</tr>";
        
        print "</table>";
        print " <BR> Are you sure you want password reset ? <BR>";
        print "</FIELDSET>";
        
        setSubmitValue ( "resetPassword" );
    }

    private function teacher_info($teacher_id) {
        print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
        print "<LEGEND><B>Teacher Record - Password Reset </B></LEGEND>";
        
        $pers_info_id = $this->teacherIf->get_personal_info_id ( $teacher_id );
        print "<input type=hidden name='user_personal_info_id' value='" . $pers_info_id . "'>";
        
        $info = $this->personalInfoIf->get_record ( $pers_info_id );
            
        print "<table>";
        
        print "<tr>";
        print "<td class='normal1'>Teacher ID</td>";
        print "<td> " . $teacher_id . " </td>";
        print "</tr>";
        
        print "<tr>";
        print "<td class='normal1'>Name</td>";
        print "<td> " . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . " </td>";
        print "</tr>";
        
        //if (isset ( $info ['last_name'] )) {
        //    print $info ['last_name'];
        //}
        
        print "<tr>";
        print "<td class='normal1'>Email </td>";
        print "<td> ";
        if (isset ( $info ['email'] )) {
            print $info ['email'];
        }
        print " </td>";
        print "</tr>";
        
        print "</table>";
        print " <BR> Are you sure you want password reset ? <BR>";
        print "</FIELDSET>";
        
        setSubmitValue ( "resetPassword" );
    }

    public function reset_user_password($pid) {

        include ("wis_header.php");
    
        wis_main_menu ( $this->mysqli_h, TRUE );
        
        print '<div id="printableArea">';
    
        print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
        print '<LEGEND style="font-size: 20px"><B>Password Reset </B></LEGEND>';
        
        $this->personalInfoIf->reset_user_passwd($pid);

        $info = $this->personalInfoIf->get_record ( $pid );

        
        print "<label> Successfully reset password for " . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . " </label>";
        
        print "</FIELDSET>";
        wis_footer ( FALSE);
    }
}

?>
