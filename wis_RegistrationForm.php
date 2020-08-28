<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_AdministrationIf.php';

class RegistrationForm {
        const FORM = 0;
        const LOGIN = 1;
        const COMPLETE = 2;
        const REENTER = 3;
        const DUPLICATE = 4;
        private $administrationIf;
        private $mysqli_h;
        
        function __construct($mysqli_h) {
                $this->mysqli_h = $mysqli_h;
                $this->administrationIf = new AdministrationIf ( $mysqli_h );
        }
        
        private function input_text_field($form_status, $name, $readonly, $value = NULL, $size = NULL, $maxlen = NULL, $required = false, $class = NULL) {
                print "<input type=text name=" . $name;
                if (isset ( $class )) {
                        print " class=" . $class;
                }
                if (isset ( $size )) {
                        print " size=" . $size;
                }
                if (isset ( $maxlen )) {
                        print " maxlength=" . $maxlen;
                }
                if ($form_status == RegistrationForm::REENTER && ! empty ( $value )) {
                        print " value= '" . $value . "' ";
                }
                if ($form_status == RegistrationForm::REENTER) {
                        $color = ' style="color:blue;" ';
                } else {
                        $color = '';
                }
                if ($required) {
                        print " required ";
                }
                print $color . $readonly . ">";
        }
        
        public function student_registration($form_status, &$info = null) {
                $submitBut = FALSE;
                $printBut = FALSE;
                
                if ($form_status == RegistrationForm::FORM || (isset ( $info ['registration_form'] ) && $info ['registration_form'] == '22')) {
                        global $registrationForm;
                        $registrationForm = 22;
                }
                
                if ($form_status == RegistrationForm::COMPLETE) {
                        $printBut = TRUE;
                        $readonly = 'readonly';
                        
                        print "<label style='background-color:YELLOW; font-size:18; text-align:center'> Registration form for " . $_REQUEST ['first_name1'] . " ";
                        if (isset ( $_REQUEST ['middle_name1'] )) {
                                print $_REQUEST ['middle_name1'] . " ";
                        }
                        print $_REQUEST ['last_name1'] . " has been succefully submitted. <BR>";
                        print " Please PRINT this form and bring it with you with proper identifications to ICSGV WIS office for admission process to complete.</label>";
                        
                        $form_status = RegistrationForm::REENTER;
                } else if ($form_status == RegistrationForm::DUPLICATE) {
                        print "<label style='background-color:YELLOW; font-size:18; text-align:center'> DUPLICATE entry for " . $_REQUEST ['first_name1'] . " ";
                        if (isset ( $_REQUEST ['middle_name1'] )) {
                                print $_REQUEST ['middle_name1'] . " ";
                        }
                        print $_REQUEST ['last_name1'] . ". Student information is already in the database. Entry rejected. <BR> </label>";
                        
                        $submitBut = TRUE;
                        $readonly = '';
                } else {
                        $submitBut = TRUE;
                        $readonly = '';
                }
                
                include ("wis_header.php");
                
                wis_main_menu ( $this->mysqli_h, $printBut );
                
                if ($form_status == RegistrationForm::FORM || (isset ( $info ['registration_form'] ) && $info ['registration_form'] == '22')) {
                        print "<input type=hidden name='registration_form' value='22'>";
                }
                
                if ($form_status == RegistrationForm::REENTER) {
                        $color = ' style="color:blue;" ';
                } else {
                        $color = '';
                }
                $year = date ( 'Y', time () );
                
                $today = today_date ();
                
                if ($form_status == RegistrationForm::REENTER && isset ( $info ['phone'] )) {
                        list ( $c_area, $c_local, $c_number ) = split ( '[-]', $info ['phone'] );
                }
                if ($form_status == RegistrationForm::REENTER && isset ( $info ['phone'] )) {
                        list ( $h_area, $h_local, $h_number ) = split ( '[-]', $info ['phone'] );
                }
                if ($form_status == RegistrationForm::REENTER && isset ( $info ['phone'] )) {
                        list ( $p_area, $p_local, $p_number ) = split ( '[-]', $info ['phone'] );
                }
                
                $info1 = $this->administrationIf->get_tution_discounts ();
                
                print '<div id="printableArea" style="font-size:14px">';
                print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
                print '<LEGEND style="font-size: 14px"></LEGEND>';
                
                list ( $year, $nyear ) = split ( '[-]', $this->administrationIf->get_school_year () );
                if ($year == 0) {
                        return;
                }
                
                /* ------------- SCHOOL YEAR, REGISTRATION DATE, ICSGV MEMBER INFO ----------------- */
                print '<P style="text-align: center;font-size:14px"><i>Registration Form - School Year  <B>' . $year . '-' . $nyear . '</B></i>';
                print "<br>";
                print '<B>Weekend Islamic School - </B>';
                
                print 'Islamic Center San Gabriel Valley';
                print "<br>";
                
                print '<label class="normal1" style="font-size:14px" >19164 E. Walnut Drive North, Rowland Heights, CA 91748 </label></P>';
                
                print "<label  style='font-size:14px'>Date (mm/dd/yyyy)</label>";
                print "<label style='color:red;font-size:11px'> *&nbsp</b></label>";
                
                $this->input_text_field ( RegistrationForm::REENTER, "register_date", "readonly", $today, 10, 10 );
                
                // FIX: Make it read-only for now
                print '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=checkbox name="icsgv_mem" value="1" id="mem_id_check" onClick="memberIdAjax()" ';
                if ($form_status == RegistrationForm::REENTER) {
                        if (isset ( $info ['icsgv_mem'] ) && $info ['icsgv_mem'] == 1) {
                                print ' checked="yes" ';
                        }
                }
                print '><label> ICSGV member </label>';
                
                // FIX: Make it read-only for now
                // print '<input type=text name="mid" id="mem_id" size=5 maxlength=5 onChange="memberIdAjax()" readonly="readonly"';
                // if ($form_status == RegistrationForm::REENTER && isset($info['mem_id'])) {
                // print ' value= "' . $info['mem_id'] . '" readonly="readonly" style="color:blue;" ';
                // }
                // print '>';
                
                print "<label class='normal1' style='color:red;font-size:11px'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp *&nbsp&nbsp</b>";
                print "Required Fields</label>";
                
                /* ------------- STUDENT NAME, CELL PHONE, EMAIL ----------------- */
                print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>1. Student Information</B></LEGEND>";
                
                /* -----------------FIRST CHILD---------------- */
                print "<table>";
                print "<th> </th>";
                print "<th>Name (First, MI, Last) <label style='color:red;font-size:11px'>*</label> </th>";
                // print "<th> Phone <label style='color:red;font-size:11px'>*</label> </th>";
                print "<th> Email  <label style='color:red;font-size:11px'> </label></th>";
                print "<th> Reg. School Grade  <label style='color:red;font-size:11px'>*</label> </th>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 1</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name1', $readonly, $info ['first_name1'], 10, 20, true );
                print "&nbsp";
                $mn = empty ( $info ['middle_name1'] ) ? '' : $info ['middle_name1'];
                $this->input_text_field ( $form_status, 'middle_name1', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name1', $readonly, $info ['last_name1'], 10, 20, true );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email1', $readonly, $info ['email1'], 30, 50 );
                
                print "</td>";
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade1', $readonly, $info ['ps_grade1'], 4, 4, true );
                
                print "</td></tr>";
                
                /* -----------------SECOND CHILD---------------- */
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 2</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name2', $readonly, $info ['first_name2'], 10, 20 );
                print "&nbsp";
                $mn = empty ( $info ['middle_name2'] ) ? '' : $info ['middle_name2'];
                $this->input_text_field ( $form_status, 'middle_name2', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name2', $readonly, $info ['last_name2'], 10, 20 );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email2', $readonly, $info ['email2'], 30, 50 );
                
                print "</td>";
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade2', $readonly, $info ['ps_grade2'], 4, 4 );
                
                print "</td></tr>";
                
                /* -----------------THIRD CHILD---------------- */
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 3</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name3', $readonly, $info ['first_name3'], 10, 20 );
                print "&nbsp";
                $mn = empty ( $info ['middle_name3'] ) ? '' : $info ['middle_name3'];
                $this->input_text_field ( $form_status, 'middle_name3', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name3', $readonly, $info ['last_name3'], 10, 20 );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email3', $readonly, $info ['email3'], 30, 50 );
                
                print "</td>";
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade3', $readonly, $info ['ps_grade3'], 4, 4 );
                
                print "</td></tr>";
                
                /* -----------------FOURTH CHILD---------------- */
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 4</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name4', $readonly, $info ['first_name4'], 10, 20 );
                print "&nbsp";
                $mn = empty ( $info ['middle_name4'] ) ? '' : $info ['middle_name4'];
                $this->input_text_field ( $form_status, 'middle_name4', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name4', $readonly, $info ['last_name4'], 10, 20 );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email4', $readonly, $info ['email4'], 30, 50 );
                
                print "</td>";
                
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade4', $readonly, $info ['ps_grade4'], 4, 4 );
                
                print "</td></tr>";
                
                /* -----------------FIFTH CHILD---------------- */
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 5</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name5', $readonly, $info ['first_name5'], 10, 20 );
                print "&nbsp";
                $mn = empty ( $info ['middle_name5'] ) ? '' : $info ['middle_name5'];
                $this->input_text_field ( $form_status, 'middle_name5', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name5', $readonly, $info ['last_name5'], 10, 20 );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email5', $readonly, $info ['email5'], 30, 50 );
                
                print "</td>";
                
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade5', $readonly, $info ['ps_grade5'], 4, 4 );
                
                print "</td></tr>";
                
                /* -----------------SIXTH CHILD---------------- */
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 6</B>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'first_name6', $readonly, $info ['first_name6'], 10, 20 );
                print "&nbsp";
                $mn = empty ( $info ['middle_name6'] ) ? '' : $info ['middle_name6'];
                $this->input_text_field ( $form_status, 'middle_name6', $readonly, $mn, 1, 2 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'last_name6', $readonly, $info ['last_name6'], 10, 20 );
                
                print "</td><td style='font-size:13px'>";
                $this->input_text_field ( $form_status, 'email6', $readonly, $info ['email6'], 30, 50 );
                
                print "</td>";
                print "<td>";
                
                $this->input_text_field ( $form_status, 'ps_grade6', $readonly, $info ['ps_grade6'], 4, 4 );
                
                print "</td></tr>";
                print "</table>";
                
                print "</FIELDSET>";
                
                print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>2. Parent Information</B></LEGEND>";
                
                print "<table>";
                
                /* ------------- MOTHER NAME, CELL PHONE ----------------- */
                print "<tr>";
                print "<td><B style='font-size:13px'>Mother's Name </B><label style='font-size:12px'>(First,MI,Last)</label>";
                print "<em style='color:red;font-size:11px'> *</em>";
                print "</td>";
                print "<td> ";
                
                $this->input_text_field ( $form_status, 'mother_first_name', $readonly, $info ['mother_first_name'], 10, 20, true );
                print "</td>";
                print "<td> ";
                
                $this->input_text_field ( $form_status, 'mother_middle_name', $readonly, $info ['mother_middle_name'], 1, 2 );
                print "</td>";
                print "<td> ";
                
                $this->input_text_field ( $form_status, 'mother_last_name', $readonly, $info ['mother_last_name'], 10, 20, true );
                print "</td>";
                print "<td style='font-size:13px'>Cell";
                print "<em style='color:red;font-size:11px'> *</em>";
                
                print "&nbsp";
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'mother_cell_area', $readonly, $info ['mother_cell_area'], 3, 3, true );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'mother_cell_local', $readonly, $info ['mother_cell_local'], 3, 3, true );
                print "-";
                
                $this->input_text_field ( $form_status, 'mother_cell_number', $readonly, $info ['mother_cell_number'], 4, 4, true );
                print "</td>";
                print "</tr>";
                
                /* ------------- FATHER NAME, CELL PHONE ----------------- */
                print "<tr>";
                
                print "<td><B style='font-size:13px'>Father's Name </B><label style='font-size:12px'>(First,MI,Last)</label>";
                print "<label style='color:red;font-size:11px'> *</label>";
                print "</td>";
                
                print "<td> ";
                $this->input_text_field ( $form_status, 'father_first_name', $readonly, $info ['father_first_name'], 10, 20, true );
                print "</td>";
                
                print "<td> ";
                $this->input_text_field ( $form_status, 'father_middle_name', $readonly, $info ['father_middle_name'], 1, 2 );
                print "</td>";
                
                print "<td> ";
                $this->input_text_field ( $form_status, 'father_last_name', $readonly, $info ['father_last_name'], 10, 20, true );
                print "</td>";
                
                print "<td style='font-size:13px'> Cell";
                print "<em style='color:red;font-size:11px'> </em>";
                print "&nbsp&nbsp&nbsp";
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'father_cell_area', $readonly, $info ['father_cell_area'], 3, 3 );
                print "&nbsp";
                
                $this->input_text_field ( $form_status, 'father_cell_local', $readonly, $info ['father_cell_local'], 3, 3 );
                print "-";
                
                $this->input_text_field ( $form_status, 'father_cell_number', $readonly, $info ['father_cell_number'], 4, 4 );
                print "</td>";
                
                print "</tr>";
                print "</table>";
                
                /* ------------- ADDRESS, EMAIL, HOME PHONE, PARENT EMAIL ----------------- */
                print "<table>";
                print "<tr><td>";
                print "<label  style='font-size:14px' >Address </label>";
                print "<label  style='color:red; font-size:12px'>*</label>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'address', $readonly, $info ['address'], 22, 55, true );
                print "</td><td>";
                print "<label style='font-size:14px'>City</label>";
                print "<label style='color:red;font-size:11px'>*</label>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'city', $readonly, $info ['city'], 15, 30, true );
                print "</td><td>";
                print "<label style='font-size:14px' >Home</label>";
                print "<label  style='color:red;font-size:11px'>*</label>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'home_area', $readonly, $info ['home_area'], 3, 3, true );
                
                print "&nbsp";
                $this->input_text_field ( $form_status, 'home_local', $readonly, $info ['home_local'], 3, 3, true );
                print "-";
                
                $this->input_text_field ( $form_status, 'home_number', $readonly, $info ['home_number'], 4, 4, true );
                print "<br>";
                print "</td></tr>";
                
                print "<tr><td>";
                print "<label style='font-size:14px'>&nbsp&nbsp State </label>";
                print "<em style='color:red;font-size:11px'> *&nbsp</b></em>";
                print "</td><td>";
                
                $this->input_text_field ( RegistrationForm::REENTER, 'state', $readonly, 'California', 11, 15 );
                print "</td><td>";
                
                print "<label style='font-size:14px'>Zipcode </label>";
                print "<em style='color:red;font-size:11px'> *&nbsp</b></em>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'zipcode', $readonly, $info ['zipcode'], 5, 5, true );
                print "</td><td>";
                
                print "<label style='font-size:14px'>Email</label>";
                print "<em style='color:red;font-size:11px'> *&nbsp</b></em>";
                print "</td><td>";
                
                $this->input_text_field ( $form_status, 'par_email', $readonly, $info ['par_email'], 30, 50, true );
                print "</td></tr>";
                print "</table>";
                // print "<br>";
                
                /* ------------- PARENT VOLUNTEER INFORMATION ----------------- */
                print "<em style='color:blue;font-size:12px'><B>All parents are encouraged to volunteer. It is recommended that parents volunteer one Sunday during the school year </B></em>";
                print "<br>";
                print "<label  style='font-size:14px'>Date - 1 (mm/dd/yyyy)</label>";
                print "<label class='normal1' style='color:red;'> &nbsp</b></label>";
                
                $this->input_text_field ( $form_status, "parent_volun_date1", $readonly, $info ['parent_volun_date1'], 10, 10, false, 'datepicker' );
                
                print "<label  style='font-size:14px'>Date - 2 (mm/dd/yyyy)</label>";
                print "<em style='color:red;'> &nbsp</b></em>";
                
                $this->input_text_field ( $form_status, "parent_volun_date2", $readonly, $info ['parent_volun_date2'], 10, 10, false, 'datepicker' );
                
                print "</FIELDSET>";
                
                /* ------------- REGISTRATION FEE/DISCOUNTS etc. ----------------- */
                print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>3. Registration Fee</B></LEGEND>";
                
                print " Tution fee 1st child: $" . $info1 ['tution_fee'] . " per year; $50 discount for each suucessive child ";
                
                print "<br>";
                print "<label style='font-size:14px'>ICSGV member gets $" . $info1 ['icsgv_mem_discount'] . " discount for 1st child; Payment plan available with $" . $info1 ['payment_plan_fee'] . " additional fee</label>";
                
                print "</FIELDSET>";
                
                /* ------------- PARENT PERMISSION, WAIVERS ----------------- */
                print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>4. Parent Permission and Waiver</B></LEGEND>";
                print '<label  style="font-size:14px">Your signature below will give permission for your child to participate in all school activities within the premises of Islamic Center of San Gabriel Valley (ICSGV) or outside activities held in conjunction with Weekend Islamic School. It will also waive all the claims against ICSGV for injury, accident, illness, or death during any school activities. </label>';
                print '<BR>';
                // print '<BR>';
                print '<label style="font-size:14px"><I><B>Signature of parent, guardian or student 18 years of age or older</I></B></label>';
                
                print '<input type="text" size=28 style="color:blue;" readonly="readonly">';
                print "</FIELDSET>";
                
                /* ------------- STUDENT MEDICAL INFO ----------------- */
                print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>5. Parent Consent for Medical Treatment</B></LEGEND>";
                print '<label style="font-size:14px">Please list any allergy and medications used by your child on a regular basis</label>';
                print "<table>";
                print "<th> </th>";
                print "<th>Allergies </th>";
                print "<th>Medications </th>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 1</B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies1", $readonly, $info ['allergies1'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications1", $readonly, $info ['medications1'], 25, 40 );
                print "</td></tr>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 2</B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies2", $readonly, $info ['allergies2'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications2", $readonly, $info ['medications2'], 25, 40 );
                print "</td></tr>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 3 </B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies3", $readonly, $info ['allergies3'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications3", $readonly, $info ['medications3'], 25, 40 );
                print "</td></tr>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 4</B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies4", $readonly, $info ['allergies4'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications4", $readonly, $info ['medications4'], 25, 40 );
                print "</td></tr>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 5 </B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies5", $readonly, $info ['allergies5'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications5", $readonly, $info ['medications5'], 25, 40 );
                print "</td></tr>";
                
                print "<tr>";
                print "<td><B style='font-size:13px'> Child 6 </B>";
                print "</td><td>";
                $this->input_text_field ( $form_status, "allergies6", $readonly, $info ['allergies6'], 25, 40 );
                print "</td><td>";
                $this->input_text_field ( $form_status, "medications6", $readonly, $info ['medications6'], 25, 40 );
                print "</td></tr>";
                print "</table>";
                
                print "<label style='font-size:14px'> Your signature below will grant consent to ICSGV to provide all Emergency dental or medical care prescribed by a duly licensed physician (MD), Osteopathy (DO), or Dentist (DDS) for the above named student. This care may be given under whatever condition necessary to preserve life, limb or well being of the student. </label>";
                print '<BR>';
                print '<BR>';
                print '<label style="font-size:14px"><I><B>Signature of parent, guardian or student 18 years of age or older </B></I></label>';
                print '<input type="text" size=28 style="color:blue;" readonly="readonly">';
                print "</FIELDSET>";
                
                /* ------------- PERSONS AUTHORIZED FOR STUDENT PICKUP ----------------- */
                print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
                print "<LEGEND style='font-size:14px'><B>6. Names of persons authorized to pickup child(ren) in Emergency</B></LEGEND>";
                
                print "<table>";
                print "<tr>";
                print "<th><span style='font-weight: normal; font-size:14px'>Name</span></th> ";
                print "<th><span style='font-weight: normal; font-size:14px'>Address</span></th> ";
                print "<th><span style='font-weight: normal; font-size:14px'>Phone</span></th> ";
                print "<th><span style='font-weight: normal; font-size:14px'>Driver Lic.</span></th> ";
                print "</tr>";
                
                print "<tr>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "auth_person1", $readonly, $info ['auth_person1'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "address_ap1", $readonly, $info ['address_ap1'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "phone_ap1", $readonly, $info ['phone_ap1'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "driver_lic_ap1", $readonly, $info ['driver_lic_ap1'], 15, 24 );
                print "</td>";
                print "</tr>";
                
                print "<tr>";
                print "<td>";
                $this->input_text_field ( $form_status, "auth_person2", $readonly, $info ['auth_person2'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "address_ap2", $readonly, $info ['address_ap2'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "phone_ap2", $readonly, $info ['phone_ap2'], 15, 24 );
                print "</td>";
                
                print "<td>";
                $this->input_text_field ( $form_status, "driver_lic_ap2", $readonly, $info ['driver_lic_ap2'], 15, 24 );
                print "</td>";
                
                print "</tr>";
                
                print "</table>";
                // print '<input type="Submit" name="Submit" value="Submit" style="background-color:lightgreen; float:right;">';
                
                print '</FIELDSET >';
                
                print '</div>';
                
                print "</FIELDSET>";
                
                setSubmitValue ( "studentRegistration" );
                print "</FORM>";
                
?>

<SCRIPT TYPE="text/javascript">
<!--
autojump('cell_area', 'cell_local', 3);
autojump('cell_local', 'cell_number', 3);

autojump('mother_cell_area', 'mother_cell_local', 3);
autojump('mother_cell_local', 'mother_cell_number', 3);

autojump('father_cell_area', 'father_cell_local', 3);
autojump('father_cell_local', 'father_cell_number', 3);

autojump('home_area', 'home_local', 3);
autojump('home_local', 'home_number', 3);
//-->
</SCRIPT>

<?php
                wis_footer ( $submitBut );
        }
}

?>
