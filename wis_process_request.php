<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_connect.php';
include_once 'wis_login.php';
include_once 'wis_Email.php';
include_once 'wis_signin.php';

include_once 'wis_RegistrationForm.php';
include_once 'wis_StudentRecord.php';
include_once 'wis_AdministrationIf.php';
include_once 'wis_Administration.php';
include_once 'wis_EventIf.php';
include_once 'wis_PersonalInfoIf.php';
include_once 'wis_StaffIf.php';
include_once 'wis_StudentIf.php';
include_once 'wis_TeacherIf.php';
include_once 'wis_Teacher.php';
include_once 'wis_Book.php';
include_once 'wis_BookIf.php';
include_once 'wis_Blackboard.php';
include_once 'wis_Calendar.php';

function wis_new_registration($mysqli_h) {
        if (empty ( $_REQUEST ['signin_id'] ) || empty ( $_REQUEST ['email'] ) || empty ( $_REQUEST ['firstname'] ) || empty ( $_REQUEST ['lastname'] ) || empty ( $_REQUEST ['password1'] ) || empty ( $_REQUEST ['password2'] ) || empty ( $_REQUEST ['username'] )) {
                print "<font color='red'> Please fill-in all the fields <font color='black'>";
                signin_with_banner ();
                return;
        }
        if (! (filter_var ( $_REQUEST ['email'], FILTER_VALIDATE_EMAIL ))) {
                print "<font color='red'> Invalid email; Please provide valid email <font color='black'>";
                signin_with_banner ();
                return;
        }
        $pers_info_id='';
        switch ($_REQUEST ['signin_as']) {
                case 1 :
                        $studentIf = new StudentIf ( $mysqli_h );
                        $access = WIS_STUDENT;
                        $pers_info_id = $studentIf->get_personal_info_id ( $_REQUEST ['signin_id'] );
                        break;
                case 2 :
                        $teacherIf = new TeacherIf ( $mysqli_h );
                        $pers_info_id = $teacherIf->get_personal_info_id ( $_REQUEST ['signin_id'] );
                        $access = WIS_TEACHER;
                        break;
                case 3 :
                        $staffIf = new StaffIf ( $mysqli_h );
                        $pers_info_id = $staffIf->get_personal_info_id ( $_REQUEST ['signin_id'] );
                        $access = WIS_STAFF;
                        break;
                default :
                        die ( "No user name or ID match, please contact WIS administration" );
        }
        if (empty ( $pers_info_id )) {
                print "<font color='red'> No personal identification exist in the data base; please call WIS admin to get member name and ID <font color='black'>";
                signin_with_banner ();
                return;
        }
        
        $personalInfoIf = new PersonalInfoIf ( $mysqli_h );
        $info = $personalInfoIf->get_record ( $pers_info_id );
        
        if (! empty ( $info ['user'] )) { // USER is already registered so REJECT the registration
                print "<font color='red'> User already registered. If you forgot your password, please call WIS admin to reset the password <font color='black'>";
                signin_with_banner ();
                return;
        }
        
        if ($_REQUEST ['password1'] !== $_REQUEST ['password2']) {
                print "Passwords don't match. Please retype passwords ";
                signin_with_banner ();
                return;
        }
        
        // print "firstName " . $info[0] . " lastName " . $info[1];
        if (($info ['first_name'] === $_REQUEST ['firstname']) && ($info ['last_name'] === $_REQUEST ['lastname'])) {
                
                $users = $personalInfoIf->get_all_users ();
                
                for($i = 0; $i < count ( $users ); $i ++) {
                        if ($users [$i] ['user'] === $_REQUEST ['username']) {
                                print "<font color='red'> User name already exists. Please pick-up another name.  <font color='black'>";
                                signin_with_banner ();
                                return;
                        }
                }
        } else {
                print "<font color='red'> Invalid Member name or ID; please call WIS admin to get member name and ID <font color='black'>";
                signin_with_banner ();
                return;
        }
        
        $hash = wis_get_password_hash ( wis_get_password_salt (), $_REQUEST ['password1'] );
        
        $personalInfoIf->update_user_email_passwd ( $_REQUEST ['username'], $_REQUEST ['email'], $hash, $access, $info ['id_pi'] );
        
        print "<BR><font color='green'> Successful Student Registration.  Please close this window & login with your username and password <font color='black'><BR>";
}

function wis_get_new_request() {
        $request = TRUE;
        
        $mysqli_h = wis_connect_to_mysql ();
        
        $studentRecord = new StudentRecord ( $mysqli_h );
        $administrationIf = new AdministrationIf ( $mysqli_h );
        $administration = new Administration ( $mysqli_h );
        $calendar = new Calendar ( $mysqli_h );
        $teacher = new Teacher ( $mysqli_h );
        $eventIf = new EventIf ( $mysqli_h );
        $book = new Book ( $mysqli_h );
        $bookIf = new BookIf( $mysqli_h );
        $regForm = new RegistrationForm ( $mysqli_h );
        $wemail = new Email ( $mysqli_h );
        $studentRecord = new StudentRecord ( $mysqli_h );
        
        // print "SubmitVal: " . $_REQUEST['SubmitVal'] . "; Submit: " . $_REQUEST['Submit'] . "; SubmitLog: " . $_REQUEST['SubmitLog'] . "; Auth: " . $_SESSION['authenticity'] . " <BR>";
        
        if (isset ( $_REQUEST ['SubmitLog'] ) && ($_REQUEST ['SubmitLog'] === 'Log In')) {
                if (login ()) {
                        wis_main_page ( $mysqli_h );
                } else {
                        include ("wis_main.php");
                }
                return $request;
        }
        
        if (isset ( $_REQUEST ['SubmitLog'] ) && $_REQUEST ['SubmitLog'] === 'Sign In') {
                $_SESSION ['authenticity'] = Authentication::INVALID;
                wis_new_registration ( $mysqli_h );
                return $request;
        }
        
        if (isset ( $_REQUEST ['SubmitSearch'] ) && ($_REQUEST ['SubmitSearch'] === 'Search')) {
                if (! empty ( $_REQUEST ['SearchName'] ) && ! empty ( $_REQUEST ['SearchId'] )) {
                        $studentRecord->view_student_list_n_modify ( $_REQUEST ['SearchName'], $_REQUEST ['SearchId'], StudentRecord::NAME_ID_SEARCH );
                        return TRUE;
                } else if (! empty ( $_REQUEST ['SearchName'] )) {
                        $studentRecord->view_student_list_n_modify ( $_REQUEST ['SearchName'], NULL, StudentRecord::NAME_SEARCH );
                        return TRUE;
                } else if (! empty ( $_REQUEST ['SearchId'] )) {
                        $studentRecord->view_student_list_n_modify ( NULL, $_REQUEST ['SearchId'], StudentRecord::ID_SEARCH );
                        return TRUE;
                }
        }
        
        if (isset ( $_REQUEST ['SubmitVal'] ) && isset ( $_SESSION ['authenticity'] )) {
                if ($_REQUEST ['SubmitVal'] !== "studentRegistration" && $_SESSION ['authenticity'] == Authentication::INVALID) {
                        include ("wis_main.php");
                        return FALSE;
                }
        }
        if (isset ( $_REQUEST ['Recalculate']) && $_REQUEST ['Recalculate']==='Recalculate') {
                //print "RECALCULATE please <BR>";
                $rc_info['dummy'] = true;
                for ($i=0; $i<10;$i++) { //FIX: Assuming 10 books MAX
                        $bid = "'book-" . $_REQUEST['rc_student_id'] . "-" . $i . "'";        
                        $gb_id_name = "gbook_id-" . $i;
                        if (empty($_REQUEST[$gb_id_name])) {
                                break;
                        }
                        if (isset($_REQUEST[$bid])) {
                                $rc_info[$bid] = true;
                                $bookIf->updateBookNeeded($_REQUEST['rc_student_id'], $_REQUEST[$gb_id_name], 1);
                        } else {
                                $rc_info[$bid] = false;
                                $bookIf->updateBookNeeded($_REQUEST['rc_student_id'], $_REQUEST[$gb_id_name], 0);
                        }
                }
                $rc_info['multi_pay_plan_fee'] = $_REQUEST['multi_pay_plan_fee'];
                $rc_info['miscl_charges'] = $_REQUEST['miscl_charges'];
                $studentRecord->view_tution_plan_n_setup($_REQUEST['rc_student_id'], $_REQUEST['rc_readonly'],$rc_info);
                return FALSE;
        }
        
        // welcome_banner();
        if (! (isset ( $_REQUEST ['SubmitVal'] ))) {
                wis_main_page ( $mysqli_h );
                return FALSE;
        }
        switch ($_REQUEST ['SubmitVal']) {
                
                case 'Send email' :
                        
                        // print "EMAIL MESSAGE to be sent " . str_replace("\r",'<br>',$_REQUEST['email_text']) . " <BR>";
                        $wemail->send_email ();
                        wis_main_page ( $mysqli_h );
                        print "<input type=hidden name='regulated' value='1'>";
                        break;
                
                case 'studentRegistration' :
                        
                        $_REQUEST ['register_date']      = trim ( $_REQUEST ['register_date'] );
                        $_REQUEST ['first_name1']        = trim ( $_REQUEST ['first_name1'] );
                        $_REQUEST ['last_name1']         = trim ( $_REQUEST ['last_name1'] );
                        $_REQUEST ['address']            = trim ( $_REQUEST ['address'] );
                        $_REQUEST ['city']               = trim ( $_REQUEST ['city'] );
                        $_REQUEST ['zipcode']            = trim ( $_REQUEST ['zipcode'] );
                        $_REQUEST ['mother_last_name']   = trim ( $_REQUEST ['mother_last_name'] );
                        $_REQUEST ['mother_first_name']  = trim ( $_REQUEST ['mother_first_name'] );
                        $_REQUEST ['father_last_name']   = trim ( $_REQUEST ['father_last_name'] );
                        $_REQUEST ['father_first_name']  = trim ( $_REQUEST ['father_first_name'] );
                        $_REQUEST ['ps_grade1']          = trim ( $_REQUEST ['ps_grade1'] );
                        $_REQUEST ['home_area']          = trim ( $_REQUEST ['home_area'] );
                        $_REQUEST ['home_local']         = trim ( $_REQUEST ['home_local'] );
                        $_REQUEST ['home_number']        = trim ( $_REQUEST ['home_number'] );
                        $_REQUEST ['mother_cell_area']   = trim ( $_REQUEST ['mother_cell_area'] );
                        $_REQUEST ['mother_cell_local']  = trim ( $_REQUEST ['mother_cell_local'] );
                        $_REQUEST ['mother_cell_number'] = trim ( $_REQUEST ['mother_cell_number'] );
                        $_REQUEST ['father_cell_area']   = trim ( $_REQUEST ['father_cell_area'] );
                        $_REQUEST ['father_cell_local']  = trim ( $_REQUEST ['father_cell_local'] );
                        $_REQUEST ['father_cell_number'] = trim ( $_REQUEST ['father_cell_number'] );

                        if (empty ( $_REQUEST ['register_date'] ) ||
                            empty ( $_REQUEST ['first_name1'] ) ||
                            empty ( $_REQUEST ['last_name1'] ) ||
                            empty ( $_REQUEST ['address'] ) ||
                            empty ( $_REQUEST ['city'] ) ||
                            empty ( $_REQUEST ['zipcode'] ) ||
                            empty ( $_REQUEST ['mother_last_name'] ) ||
                            empty ( $_REQUEST ['mother_first_name'] ) ||
                            empty ( $_REQUEST ['father_last_name'] ) ||
                            empty ( $_REQUEST ['father_first_name'] ) ||
                            empty ( $_REQUEST ['ps_grade1'] ) ||
                            empty ( $_REQUEST ['home_area'] ) ||
                            empty ( $_REQUEST ['home_local'] ) ||
                            empty ( $_REQUEST ['home_number'] ) ||
                            empty ( $_REQUEST ['mother_cell_area'] ) ||
                            empty ( $_REQUEST ['mother_cell_local'] ) ||
                            empty ( $_REQUEST ['mother_cell_number'] ) ||
                            empty ( $_REQUEST ['father_cell_area'] ) ||
                            empty ( $_REQUEST ['father_cell_local'] ) ||
                            empty ( $_REQUEST ['father_cell_number'] )
                            ) {
                            print "EMPTY <BR>";
                                $_SESSION ['wis_error_flag'] = TRUE;
                                $_SESSION ['wis_error'] = "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                // print "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                $regForm->student_registration ( RegistrationForm::REENTER, $_REQUEST );
                        } elseif (($_REQUEST ['par_email'] !== "n/a") && ! (filter_var ( $_REQUEST ['par_email'], FILTER_VALIDATE_EMAIL ))) {
                            print "PAR_EMAIL <BR>";
                                // print "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                $_SESSION ['wis_error_flag'] = TRUE;
                                $_SESSION ['wis_error'] = "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                $regForm->student_registration ( RegistrationForm::REENTER, $_REQUEST );
                        } else {
                                $studentRecord->enter_student_registration ();
                        }
                        break;
                
                case 'studentReturnRegistrationSubmit':

                        $info['first_name1']        = trim ( $_REQUEST ['first_name1'] );
                        $info['middle_name1']       = trim ( $_REQUEST ['middle_name1'] );
                        $info['last_name1']         = trim ( $_REQUEST ['last_name1'] );
                        $info['email1']             = trim ( $_REQUEST ['email1'] );
                        $info['ps_grade1']          = trim ( $_REQUEST ['ps_grade1'] );
                        $info['allergies1']         = trim ( $_REQUEST ['allergies1'] );
                        $info['medications1']       = trim ( $_REQUEST ['medications1'] );
                        $info['register_date']      = trim ( $_REQUEST ['register_date'] );
                        $info['waiver_signed_by']   = trim ( $_REQUEST ['waiver_signed_by'] );
                        $info['form_signed_by']     = trim ( $_REQUEST ['form_signed_by'] );

                        $info['address']            = trim ( $_REQUEST ['address']);
                        $info['city']               = trim ( $_REQUEST ['city'] );
                        $info['zipcode']            = trim ( $_REQUEST ['zipcode'] );
                        $info['state']              = trim ( $_REQUEST ['state'] );
                        $info['home_area']          = trim ( $_REQUEST ['home_area'] );
                        $info['home_local']         = trim ( $_REQUEST ['home_local'] );
                        $info['home_number']        = trim ( $_REQUEST ['home_number'] );

                        $info['mother_last_name']   = trim ( $_REQUEST ['mother_last_name'] );
                        $info['mother_first_name']  = trim ( $_REQUEST ['mother_first_name'] );
                        $info['mother_middle_name'] = trim ( $_REQUEST ['mother_middle_name'] );
                        $info['mother_cell_area']   = trim ( $_REQUEST ['mother_cell_area'] );
                        $info['mother_cell_local']  = trim ( $_REQUEST ['mother_cell_local'] );
                        $info['mother_cell_number'] = trim ( $_REQUEST ['mother_cell_number'] );

                        $info['father_last_name']   = trim ( $_REQUEST ['father_last_name'] );
                        $info['father_first_name']  = trim ( $_REQUEST ['father_first_name'] );
                        $info['father_middle_name'] = trim ( $_REQUEST ['father_middle_name'] );
                        $info['father_cell_area']   = trim ( $_REQUEST ['father_cell_area'] );
                        $info['father_cell_local']  = trim ( $_REQUEST ['father_cell_local'] );
                        $info['father_cell_number'] = trim ( $_REQUEST ['father_cell_number'] );

                        $info['par_email']          = trim ( $_REQUEST ['par_email'] );
                        $info['parent_volun_date1'] = trim ( $_REQUEST ['parent_volun_date1'] );
                        $info['parent_volun_date2'] = trim ( $_REQUEST ['parent_volun_date2'] );

                        $info['auth_person1']       = trim ( $_REQUEST ['auth_person1'] );
                        $info['address_ap1']        = trim ( $_REQUEST ['address_ap1'] );
                        $info['phone_ap1']          = trim ( $_REQUEST ['phone_ap1'] );
                        $info['driver_lic_ap1']     = trim ( $_REQUEST ['driver_lic_ap1'] );

                        $info['auth_person2']       = trim ( $_REQUEST ['auth_person2'] );
                        $info['address_ap2']        = trim ( $_REQUEST ['address_ap2'] );
                        $info['phone_ap2']          = trim ( $_REQUEST ['phone_ap2'] );
                        $info['driver_lic_ap2']     = trim ( $_REQUEST ['driver_lic_ap2'] );


                        if (empty ( $info ['register_date'] ) ||
                            empty ( $info ['first_name1'] ) ||
                            empty ( $info ['last_name1'] ) ||
                            empty ( $info ['address'] ) ||
                            empty ( $info ['city'] ) ||
                            empty ( $info ['zipcode'] ) ||
                            empty ( $info ['mother_last_name'] ) ||
                            empty ( $info ['mother_first_name'] ) ||
                            empty ( $info ['father_last_name'] ) ||
                            empty ( $info ['father_first_name'] ) ||
                            empty ( $info ['ps_grade1'] ) ||
                            empty ( $info ['home_area'] ) ||
                            empty ( $info ['home_local'] ) ||
                            empty ( $info ['home_number'] ) ||
                            empty ( $info ['mother_cell_area'] ) ||
                            empty ( $info ['mother_cell_local'] ) ||
                            empty ( $info ['mother_cell_number'] ) ||
                            empty ( $info ['father_cell_area'] ) ||
                            empty ( $info ['father_cell_local'] ) ||
                            empty ( $info ['father_cell_number'] )
                            )
                        {
                                $_SESSION ['wis_error_flag'] = TRUE;
                                $_SESSION ['wis_error'] = "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                // print "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                $studentRecord->studentReturnRegistration( $_REQUEST ['student_id'], $info );

                        } elseif (($info ['par_email'] !== "n/a") && ! (filter_var ( $info ['par_email'], FILTER_VALIDATE_EMAIL ))) {

                                // print "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                $_SESSION ['wis_error_flag'] = TRUE;
                                $_SESSION ['wis_error'] = "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                $studentRecord->studentReturnRegistration( $_REQUEST ['student_id'], $info );

                        } else {
                                $studentRecord->studentReturnRegistrationDb($_REQUEST ['student_id'], $info);
                        }
                        
                        break;
                        
                case 'blackboardFileUpload' :
                        $blackboard = new Blackboard ( $mysqli_h );
                        $_FILES ['file_name'] ['name'] = trim ( $_FILES ['file_name'] ['name'] );
                        $_REQUEST ['file_title'] = trim ( $_REQUEST ['file_title'] );
                        
                        if (empty ( $_FILES ['file_name'] ['name'] ) || empty ( $_REQUEST ['file_title'] )) {
                                $_SESSION ['wis_error_flag'] = TRUE;
                                $_SESSION ['wis_error'] = "<font color='red'> File Title and file selection is required; Please provide<font color='black'>";
                                $blackboard->file_upload ( $_REQUEST ['teacher_id'] );
                        } else {
                                $blackboard->enter_file_upload ( $_REQUEST ['teacher_id'] );
                        }
                        break;
                
                case 'blackboardFileDelete' :
                        $blackboard = new Blackboard ( $mysqli_h );
                        $blackboard->enter_file_delete ();
                        
                        break;
                
                case 'blackboardFileModify' :
                        $blackboard = new Blackboard ( $mysqli_h );
                        $blackboard->enter_file_modify ();
                        
                        break;
                
                case 'setupFees' :
                        $rv = true;
                        $_REQUEST ['tution_fee'] = trim ( $_REQUEST ['tution_fee'] );
                        $_REQUEST ['sibling_discount'] = trim ( $_REQUEST ['sibling_discount'] );
                        $_REQUEST ['member_discount'] = trim ( $_REQUEST ['member_discount'] );
                        $_REQUEST ['pay_plan_fee'] = trim ( $_REQUEST ['pay_plan_fee'] );
                        
                        if (! empty ( $_REQUEST ['tution_fee'] )) {
                                if (! is_numeric ( $_REQUEST ['tution_fee'] ) || $_REQUEST ['tution_fee'] < 300) {
                                        print "<font color='red'>Invalid value of tution fee; Must be a numeric value >= 300 <BR></font>";
                                        $rv = false;
                                }
                        } else {
                                print "<font color='red'>Must have tution fee with value > 300 <BR></font>";
                                $rv = false;
                        }
                        if (! empty ( $_REQUEST ['sibling_discount'] )) {
                                if (! is_numeric ( $_REQUEST ['sibling_discount'] )) {
                                        print "<font color='red'>Invalid value of sibling discount; Must be a numeric value  <BR></font>";
                                        $rv = false;
                                }
                        } else {
                                $_REQUEST ['sibling_discount'] = 0;
                        }
                        if (! empty ( $_REQUEST ['member_discount'] )) {
                                if (! is_numeric ( $_REQUEST ['member_discount'] )) {
                                        print "<font color='red'>Invalid value of member discount; Must be a numeric value  <BR></font>";
                                        $rv = false;
                                }
                        } else {
                                $_REQUEST ['member_discount'] = 0;
                        }
                        if (! empty ( $_REQUEST ['pay_plan_fee'] )) {
                                if (! is_numeric ( $_REQUEST ['pay_plan_fee'] )) {
                                        print "<font color='red'>Invalid value of member discount; Must be a numeric value  <BR></font>";
                                        $rv = false;
                                }
                        } else {
                                $_REQUEST ['pay_plan_fee'] = 0;
                        }
                        if ($rv) {
                                $administration->enter_fee_allocation ();
                        }
                        wis_main_page ( $mysqli_h );
                        
                        break;
                
                case 'enterTutionPlan' :
                        $studentRecord->enter_tution_plan ( 'REGISTRATION' );
                        //wis_main_page ( $mysqli_h );
                        
                        break;
                
                case 'schoolYearSetup' :
                        list ( $s_year, $e_year ) = split ( '[-]', $_REQUEST ['school_year'] );
                        if (intval ( $s_year ) >= 2010 && intval ( $s_year ) <= 2030) {
                                
                                $rownull = false;
                                $id_count = $administrationIf->get_id_row_count ();
                                
                                if ($id_count > 1) {
                                        $administrationIf->delete_all_records ();
                                        $rownull = true;
                                }
                                
                                if ($id_count == 0 || $rownull) {
                                        $administrationIf->insert_school_year ( $_REQUEST ['school_year'] );
                                } else {
                                        $administrationIf->update_school_year ( $_REQUEST ['school_year'] );
                                }
                                
                                $month [0] = 'sep';
                                $month [1] = 'oct';
                                $month [2] = 'nov';
                                $month [3] = 'dec';
                                $month [4] = 'jan';
                                $month [5] = 'feb';
                                $month [6] = 'mar';
                                $month [7] = 'apr';
                                $month [8] = 'may';
                                $month [9] = 'jun';
                                
                                $info = $administrationIf->get_school_year ();
                                
                                if (empty ( $info ['school_year'] )) {
                                        for($i = 0; $i < 10; $i ++) {
                                                for($j = 1; $j <= 5; $j ++) {
                                                        $rec ['month'] = $month [$i];
                                                        $rec ['event_cnt'] = $j;
                                                        $rec ['school_year'] = $_REQUEST ['school_year'];
                                                        $rec ['date'] = NULL;
                                                        $rec ['event_desc'] = NULL;
                                                        $rec ['time'] = NULL;
                                                        
                                                        $eventIf->insert_record ( $rec );
                                                }
                                        }
                                }
                                print "Got it; Please close this window<BR>";
                        } else {
                                print "ILLEGAL selection; Please close this window and make proper selection<BR>";
                        }
                        break;
                case 'recordSchoolDays' :
                        $administration->record_school_days();
                        break;
                        
                case 'addCalendarEvent' :
                        if ($_REQUEST ['event_type'] === 'Create_event') {
                                print "Create event <BR>";
                                wis_create_new_event ();
                        } elseif ($_REQUEST ['event_type'] === 'Modify_event') {
                                print "Modify event: " . $_REQUEST ['wis_event'] . " <BR>";
                        } else {
                                print "Illegal choice  <BR>";
                        }
                        break;
                
                case 'insertTeacherRecord' :
                        
                        $_REQUEST ['last_name'] = trim ( $_REQUEST ['last_name'] );
                        $_REQUEST ['first_name'] = trim ( $_REQUEST ['first_name'] );
                        $_REQUEST ['home_area'] = trim ( $_REQUEST ['home_area'] );
                        $_REQUEST ['home_local'] = trim ( $_REQUEST ['home_local'] );
                        $_REQUEST ['home_number'] = trim ( $_REQUEST ['home_number'] );
                        $_REQUEST ['cell_area'] = trim ( $_REQUEST ['cell_area'] );
                        $_REQUEST ['cell_local'] = trim ( $_REQUEST ['cell_local'] );
                        $_REQUEST ['cell_number'] = trim ( $_REQUEST ['cell_number'] );
                        
                        if (empty ( $_REQUEST ['last_name'] ) || empty ( $_REQUEST ['first_name'] ) || empty ( $_REQUEST ['home_area'] ) || empty ( $_REQUEST ['home_local'] ) || empty ( $_REQUEST ['home_number'] ) || empty ( $_REQUEST ['cell_area'] ) || empty ( $_REQUEST ['cell_local'] ) || empty ( $_REQUEST ['cell_number'] )) {
                                
                                print "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                $teacher->reenter_teacher_record ( $_REQUEST );
                        } elseif ((($_REQUEST ['email'] !== "n/a") && ! (filter_var ( $_REQUEST ['email'], FILTER_VALIDATE_EMAIL )))) {
                                
                                print "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                wis_reenter_teacher_record ( $_REQUEST );
                        } else {
                                $teacher->insert_teacher_record ();
                        }
                        break;
                
                case 'insertBookRecord' :
                        
                        $book->insert_book_record ();
                        break;
                
                case 'updateTeacherClass' :
                        $teacher->update_teacher_class ();
                        break;
                case 'updateTeacherRecord' :
                        
                        $_REQUEST ['last_name'] = trim ( $_REQUEST ['last_name'] );
                        $_REQUEST ['first_name'] = trim ( $_REQUEST ['first_name'] );
                        $_REQUEST ['home_area'] = trim ( $_REQUEST ['home_area'] );
                        $_REQUEST ['home_local'] = trim ( $_REQUEST ['home_local'] );
                        $_REQUEST ['home_number'] = trim ( $_REQUEST ['home_number'] );
                        $_REQUEST ['cell_area'] = trim ( $_REQUEST ['cell_area'] );
                        $_REQUEST ['cell_local'] = trim ( $_REQUEST ['cell_local'] );
                        $_REQUEST ['cell_number'] = trim ( $_REQUEST ['cell_number'] );
                        
                        if ((($_REQUEST ['email'] !== "n/a") && ! (filter_var ( $_REQUEST ['email'], FILTER_VALIDATE_EMAIL )))) {
                                
                                print "<font color='red'> Invalid email; Please provide valid email or enter n/a <font color='black'>";
                                wis_reenter_modify_teacher_record ( $_REQUEST );
                        } elseif (empty ( $_REQUEST ['last_name'] ) || empty ( $_REQUEST ['first_name'] ) || empty ( $_REQUEST ['home_area'] ) || empty ( $_REQUEST ['home_local'] ) || empty ( $_REQUEST ['home_number'] ) || empty ( $_REQUEST ['cell_area'] ) || empty ( $_REQUEST ['cell_local'] ) || empty ( $_REQUEST ['cell_number'] )) {
                                
                                print "<font color='red'>Please fill in all required (*) fields <font color='black'> <BR>";
                                wis_reenter_modify_teacher_record ( $_REQUEST );
                        } else {
                                $teacher->update_teacher_record ();
                        }
                        break;
                
                case 'updateStudentRecord' :
                        // FIX: Check for all required entries to be filled.
                        $studentRecord->update_student_record ();
                        break;
                
                case 'approveStudentRegistrations' :
                        $studentRecord->approve_student_registrations ();
                        break;
                
                case 'updateBookRecord' :
                        // FIX: Check for all required entries to be filled.
                        $book->update_book_record ();
                        break;
                
                case 'recordStudentAttendance':
                        $teacher->record_student_attendance();        
                        break;
                        
                case 'recordStudentGrades':
                        $illegal_date = false;
                        for ($i=1; $i<=8; $i++) {
                            if (!empty($_REQUEST['date_' . $i])) {
                                $mydate = explode ( '/', $_REQUEST['date_' . $i]);
                                if (count($mydate)!=3 || $mydate[0]<1 || $mydate[0]>12 || $mydate[1]<1 || $mydate[1]>31 || $mydate[2]<2000 || $mydate[2]>2070) {
                                    $illegal_date = true;
                                    $_SESSION ['wis_error_flag'] = TRUE;
                                    $_SESSION ['wis_error'] = "<font color='red'>Please use correct date format <font color='black'> <BR>";
                                    break;
                                }
                            }
                        }
                        if ($illegal_date) {
                            $teacher->view_Qteacher_student_grades($_REQUEST['teacher_id'],$_REQUEST['teacher_f_name'],$_REQUEST['teacher_m_name'],$_REQUEST['teacher_l_name']);
                        } else { 
                            $teacher->record_student_grades();
                        }
                        break;
                                
                case 'addEvents' :
                        $info = $administrationIf->get_school_year ();
                        
                        if (empty ( $info ['school_year'] )) {
                                print '<label style="color:red;"> Administrator, please setup the school year first </label><BR>';
                        } else {
                                $calendar->add_new_events ();
                        }
                        wis_main_page ( $mysqli_h );
                        break;
                
                default :
                        wis_main_page ( $mysqli_h );
                        $request = FALSE;
        } // end switch
        
        return $request;
}

wis_get_new_request ();

?>
