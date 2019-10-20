<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_TeacherIf.php';
include_once 'wis_StudentIf.php';
include_once 'wis_StudentRecordIf.php';
include_once 'wis_ParentIf.php';
include_once 'wis_PersonalInfoIf.php';
include_once 'wis_AdministrationIf.php';
include_once 'wis_AttendanceIf.php';

class Teacher {
	
	private $teacherIf;
	private $parentIf;
	private $personalInfoIf;
	private $registrationIf;
	private $administrationIf;
	private $studentIf;
	private $attendanceIf;
	private $mysqli_h;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	function __construct($mysqli_h) {
		$this->mysqli_h         = $mysqli_h;
		$this->teacherIf        = new TeacherIf ( $mysqli_h );
		$this->personalInfoIf   = new PersonalInfoIf ( $mysqli_h );
		$this->registrationIf   = new RegistrationIf ( $mysqli_h );
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
		$this->studentIf        = new StudentIf ( $mysqli_h );
		$this->studentRecordIf  = new StudentRecordIf ( $mysqli_h );
		$this->parentIf         = new ParentIf ( $mysqli_h );
		$this->attendanceIf     = new AttendanceIf( $mysqli_h );
	}
	
	function teacher_class_assignment() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$active_info = $this->teacherIf->get_Qstatus_teacher_id ( 'ACTIVE' );
		
		print "<table class='v1_table'>";
		print "<caption> WIS Teacher Roster </caption>";
		print "<tr >";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Teacher Name</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>ID</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;' colspan='13'>Grade</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Section</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Role</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Room Num</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Cell Phone</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Email</th>";
		print "</tr>";
		
		$ctr = 0;
		for($i = 0; $i < count ( $active_info ); $i ++) {
			$pers_info_id = $this->teacherIf->get_personal_info_id ( $active_info [$i] ['id_teacher'] );
			$info = $this->personalInfoIf->get_record ( $pers_info_id );
			$info_rec = $this->teacherIf->get_record ( $active_info [$i] ['id_teacher'], $this->administrationIf->get_school_year () );
			
			print "<tr >";
			print "<td style='border: 1px solid black;' rowspan='2'>" . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . "</td>";
			print "<td style='border: 1px solid black;' rowspan='2'>" . $active_info [$i] ['id_teacher'] . "</td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='na'";
			if (empty ( $info_rec ['grade'] ) || $info_rec ['grade'] === 'na') {
				print " checked='yes'";
			}
			print " >na </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='Basic'";
			if ($info_rec ['grade'] === 'Basic') {
				print " checked='yes'";
			}
			print " >Basic </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='PRE_K'";
			if ($info_rec ['grade'] === 'PRE_K') {
				print " checked='yes'";
			}
			print " >PRE_K </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='KG'";
			if ($info_rec ['grade'] === 'KG') {
				print " checked='yes'";
			}
			print " >KG </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='1'";
			if ($info_rec ['grade'] == '1') {
				print " checked='yes'";
			}
			print " >1 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='2'";
			if ($info_rec ['grade'] == '2') {
				print " checked='yes'";
			}
			print " >2 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='3'";
			if ($info_rec ['grade'] == '3') {
				print " checked='yes'";
			}
			print " >3 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='4'";
			if ($info_rec ['grade'] == '4') {
				print " checked='yes'";
			}
			print " >4 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='5'";
			if ($info_rec ['grade'] == '5') {
				print " checked='yes'";
			}
			print " >5 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='6'";
			if ($info_rec ['grade'] == '6') {
				print " checked='yes'";
			}
			print " >6 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='7'";
			if ($info_rec ['grade'] == '7') {
				print " checked='yes'";
			}
			print " >7 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='8'";
			if ($info_rec ['grade'] == '8') {
				print " checked='yes'";
			}
			print " >8 </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=radio name='teacher_class_grade_" . $ctr . "' value='YG'";
			if ($info_rec ['grade'] == 'YG') {
				print " checked='yes'";
			}
			print " >YG </td>";
			print "<td style='border: 1px solid black;'><input type=radio name='teacher_class_section_" . $ctr . "'  value='A'";
			if ($info_rec ['section'] == 'A') {
				print " checked='yes'";
			}
			print " >A </td>";
			print "<td style='border: 1px solid black;' rowspan='1'><input type=radio name='teacher_class_role_" . $ctr . "' value='PRIMARY'";
			if ($info_rec ['role'] == 'PRIMARY') {
				print " checked='yes'";
			}
			print " >Primary </td>";
			print "<td style='border: 1px solid black;' rowspan='2'><input type=text name='teacher_room_" . $ctr . "' value='" . $info_rec ['room'] . "' size=5 maxlength=5>" . " </td>";
			print "<td style='border: 1px solid black;' rowspan='2'>" . $info ['cell_phone'] . " </td>";
			print "<td style='border: 1px solid black;' rowspan='2'>" . $info ['email'] . " </td>";
			print "</tr>";
			print "<tr >";
			print "<td style='border: 1px solid black;'><input type=radio name='teacher_class_section_" . $ctr . "' value='B'";
			if ($info_rec ['section'] == 'B') {
				print " checked='yes'";
			}
			print " >B </td>";
			print "<td style='border: 1px solid black;' rowspan='1'><input type=radio name='teacher_class_role_" . $ctr . "' value='SUBSTITUTE'";
			if ($info_rec ['role'] == 'SUBSTITUTE') {
				print " checked='yes'";
			}
			print " >Substitute </td>";
			print "</tr>";
			print "<input type=hidden name='teacher_id_" . $ctr . "' value='" . $active_info [$i] ['id_teacher'] . "'>"; // $info_rec['teacher_id'] . "'>";
			$ctr ++;
		}
		
		print "</table>";
		print "<input type=hidden name='num_teachers' value='" . $ctr . "'>";
		
		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "updateTeacherClass" );
		wis_footer ( TRUE );
	}
	
	public function get_profile($id, $teacher_f_name, $teacher_m_name, $teacher_l_name) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		$pers_info_id = $this->teacherIf->get_personal_info_id ( $id );
		$info = $this->personalInfoIf->get_record ( $pers_info_id );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<table>';
		print '<tr><td>Teacher ID</td> <td>' . $id . '</td> </tr>';
		print '<tr><td>Name</td> <td>' . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . '</td> </tr>';
		print '<tr><td>Address</td> <td>' . $info ['address'] . " " . $info ['city'] . " " . $info ['state'] . " " . $info ['zipcode'] . '</td> </tr>';
		print '<tr><td>Cell Phone</td> <td>' . $info ['cell_phone'] . '</td> </tr>';
		print '<tr><td>Home Phone</td> <td>' . $info ['home_phone'] . '</td> </tr>';
		print '<tr><td>Email</td> <td>' . $info ['email'] . '</td> </tr>';
		
		print '</table>';
		
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	public function get_class_roster($id, $teacher_f_name, $teacher_m_name, $teacher_l_name) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		$info0 = $this->teacherIf->get_record ( $id );
		
		if (! empty ( $info0 ['grade'] )) {
			
			print '<div id="printableArea">';
			
			print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
			print '<LEGEND style="font-size: 20px"></LEGEND>';
			
			$sinfo = $this->registrationIf->get_records ( RegistrationIf::WIS_GRADE, $info0 ['grade'] . '-' . $info0 ['section'] );
			// $sinfo = $this->studentIf->get_Qgrade_record($info0['grade'], $info0['section'], 'APPROVED');
			
			$teacher_id = $this->teacherIf->get_Qgrade_teacher_id ( $info0 ['grade'], $info0 ['section'] );
			$tpers_info_sec = $this->personalInfoIf->get_name ( $teacher_id );
			
			print "<caption style='text-align:center'><B style='font-size:20px'> Class Roster </B><BR>";
			print "Grade: <B>" . $info0 ['grade'] . "-" . $info0 ['section'] . "<BR> </B>Primary Teacher: <B>" . $teacher_f_name . " " . $teacher_m_name . " " . $teacher_l_name . "</B><BR>";
			print "Secondary Teacher: " . $tpers_info_sec ['first_name'] . $tpers_info_sec ['middle_name'] . $tpers_info_sec ['last_name'] . " <BR>Room: " . $info0 ['room'] . " </caption>";
			print "<table >";
			print "<tr >";
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'> Number </th>";
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>First Name </th>";
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Last Name </th>";
			if ($_SESSION ['access_privilege'] == WIS_TEACHER) {
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Student Cell</th>";
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Mother Cell</th>";
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Father Cell</th>";
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Student Email</th>";
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Parent Email</th>";
			}
			print "</tr>";
			$ctr = 1;
			for($i = 0; $i < count ( $sinfo ); $i ++) {
			    if ($this->studentIf->isStatus ( $sinfo [$i] ['student_id'], 'ACTIVE') ) {
					$pers_info_id = $this->studentIf->get_personal_info_id ( $sinfo [$i] ['student_id'] );
					$parent_id = $this->studentIf->get_parent_id ( $sinfo [$i] ['student_id'] );
					$info = $this->personalInfoIf->get_record ( $pers_info_id );
					$info_p = $this->parentIf->get_record ( $parent_id );
					
					if ($info) {
						print "<tr >";
						print "<td style='border: 1px solid black;'>" . $ctr ++ . "</td>";
						print "<td style='border: 1px solid black;'>" . getCell ( $info ['first_name'] ) . "</td>";
						print "<td style='border: 1px solid black;'>" . $info ['middle_name'] . " " . getCell ( $info ['last_name'] ) . "</td>";
						if ($_SESSION ['access_privilege'] == WIS_TEACHER) {
							print "<td style='border: 1px solid black;'>" . getCell ( $info ['cell_phone'] ) . "</td>";
							print "<td style='border: 1px solid black;'>" . getCell ( $info_p ['mother_cell_phone'] ) . "</td>";
							print "<td style='border: 1px solid black;'>" . getCell ( $info_p ['father_cell_phone'] ) . "</td>";
							print "<td style='border: 1px solid black;'>" . getCell ( $info ['email'] ) . "</td>";
							print "<td style='border: 1px solid black;'>" . getCell ( $info_p ['par_email'] ) . "</td>";
						}
						print "</tr >";
					}
				}
			}
			
			print "</table>";
			print "</FIELDSET>";
			print "</div>";
		}
		wis_footer ( FALSE );
	}
	
	function teacher_roster() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$active_info = $this->teacherIf->get_Qstatus_teacher_id ( 'ACTIVE' );
		
		print "<table class='v1_table'>";
		print "<caption> WIS Teacher Roster </caption>";
		print "<tr >";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Teacher Name</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Assigned Class</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Role</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Room Number</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Cell Phone</th>";
		print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Email</th>";
		print "</tr>";
		
		for($i = 0; $i < count ( $active_info ); $i ++) {
			
			$pers_info_id = $this->teacherIf->get_personal_info_id ( $active_info [$i] ['id_teacher'] );
			$info = $this->personalInfoIf->get_record ( $pers_info_id );
			$info_rec = $this->teacherIf->get_record ( $active_info [$i] ['id_teacher'], $this->administrationIf->get_school_year () );
			
			print "<tr >";
			print "<td style='border: 1px solid black;'>" . $info ['first_name'] . " " . $info ['middle_name'] . " " . $info ['last_name'] . "</td>";
			print "<td style='border: 1px solid black;'>" . $info_rec ['grade'] . " " . $info_rec ['section'] . " </td>";
			print "<td style='border: 1px solid black;'>" . $info_rec ['role'] . " </td>";
			print "<td style='border: 1px solid black;'>" . $info_rec ['room'] . " </td>";
			print "<td style='border: 1px solid black;'>" . $info ['cell_phone'] . " </td>";
			print "<td style='border: 1px solid black;'>" . $info ['email'] . " </td>";
			print "</tr>";
		}
		print "</table>";
		
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	function update_teacher_class() {
		for($i = 0; $i < $_REQUEST ['num_teachers']; $i ++) {
			if (isset ( $_REQUEST ['teacher_class_section_' . $i] ) || isset ( $_REQUEST ['teacher_class_grade_' . $i] ) || isset ( $_REQUEST ['teacher_class_role_' . $i] ) || isset ( $_REQUEST ['teacher_room_' . $i] )) {
				$trans ['section'] = NULL;
				$trans ['grade'] = NULL;
				$trans ['role'] = NULL;
				$trans ['room'] = NULL;
				
				if (isset ( $_REQUEST ['teacher_class_section_' . $i] )) {
					$trans ['section'] = $_REQUEST ['teacher_class_section_' . $i];
				}
				if (isset ( $_REQUEST ['teacher_class_grade_' . $i] )) {
					$trans ['grade'] = $_REQUEST ['teacher_class_grade_' . $i];
				}
				if (isset ( $_REQUEST ['teacher_class_role_' . $i] )) {
					$trans ['role'] = $_REQUEST ['teacher_class_role_' . $i];
				}
				if (isset ( $_REQUEST ['teacher_room_' . $i] )) {
					$trans ['room'] = $_REQUEST ['teacher_room_' . $i];
				}
				$trans ['teacher_id'] = $_REQUEST ['teacher_id_' . $i];
				// print "INDEX: " . $i . " ; id: " . $trans['teacher_id'] . " GRADE: " . $trans['grade'] . "<BR>";
				if ($this->teacherIf->is_record ( $trans ['teacher_id'] )) {
					$this->teacherIf->update_record ( $trans );
				} else {
					$this->teacherIf->insert_record ( $trans );
				}
			}
		}
		$this->teacher_roster ();
	}
	
	function insert_teacher_record() {
		$today = today_date_SQL_format ();
		
		$pers_rec ['middle_name'] = NULL;
		$pers_rec ['cell_phone'] = NULL;
		$pers_rec ['home_phone'] = NULL;
		$pers_rec ['first_name'] = $_REQUEST ['first_name'];
		$pers_rec ['last_name'] = $_REQUEST ['last_name'];
		$pers_rec ['address'] = $_REQUEST ['address'];
		$pers_rec ['city'] = $_REQUEST ['city'];
		$pers_rec ['state'] = $_REQUEST ['state'];
		$pers_rec ['zipcode'] = $_REQUEST ['zipcode'];
		$pers_rec ['email'] = $_REQUEST ['email'];
		$pers_rec ['date'] = $today;
		
		if (isset ( $_REQUEST ['middle_name'] )) {
			$pers_rec ['middle_name'] = $_REQUEST ['middle_name'];
		}
		
		if (isset ( $_REQUEST ['cell_area'] )) {
			$pers_rec ['cell_phone'] = $_REQUEST ['cell_area'] . '-' . $_REQUEST ['cell_local'] . '-' . $_REQUEST ['cell_number'];
		}
		if (isset ( $_REQUEST ['home_area'] )) {
			$pers_rec ['home_phone'] = $_REQUEST ['home_area'] . '-' . $_REQUEST ['home_local'] . '-' . $_REQUEST ['home_number'];
		}
		
		$personal_info_id = $this->personalInfoIf->insert_record ( $pers_rec );
		
		$pi_rec ['personal_info_id'] = $personal_info_id;
		$pi_rec ['status'] = 'ACTIVE';
		$pi_rec ['hire_date'] = $today;
		$pi_rec ['termination_date'] = NULL;
		
		$teacher_id = $this->teacherIf->insert_pi_record ( $pi_rec );
		
		wis_main_page ( $this->mysqli_h );
	}
	
	function update_teacher_record() {
		$today = today_date_SQL_format ();
		
		$state = $_REQUEST ['state'];
		
		$pers_info_id = $this->teacherIf->get_personal_info_id ( $_REQUEST ['tid'] );
		
		$pers_rec ['middle_name'] = NULL;
		$pers_rec ['cell_phone'] = NULL;
		$pers_rec ['home_phone'] = NULL;
		
		$pers_rec ['last_name'] = $_REQUEST ['last_name'];
		$pers_rec ['first_name'] = $_REQUEST ['first_name'];
		
		if (isset ( $_REQUEST ['middle_name'] )) {
			$pers_rec ['midlle_name'] = $_REQUEST ['middle_name'];
		}
		
		if (isset ( $_REQUEST ['cell_area'] )) {
			$pers_rec ['cell_phone'] = $_REQUEST ['cell_area'] . '-' . $_REQUEST ['cell_local'] . '-' . $_REQUEST ['cell_number'];
		}
		if (isset ( $_REQUEST ['home_area'] )) {
			$pers_rec ['home_phone'] = $_REQUEST ['home_area'] . '-' . $_REQUEST ['home_local'] . '-' . $_REQUEST ['home_number'];
		}
		
		$pers_rec ['address'] = $_REQUEST ['address'];
		$pers_rec ['city'] = $_REQUEST ['city'];
		$pers_rec ['state'] = $_REQUEST ['state'];
		$pers_rec ['zipcode'] = $_REQUEST ['zipcode'];
		$pers_rec ['email'] = $_REQUEST ['email'];
		$pers_rec ['date'] = $today;
		
		$this->personalInfoIf->update_record ( $pers_rec, $pers_info_id );
		
		// print "SQL: " . $sql;
		
		wis_main_page ( $this->mysqli_h );
	}
	
	function get_teacher_list() {
		$tinfo = $this->teacherIf->get_Qstatus_teacher_id ( 'ACTIVE' );
		
		for($i = 0; $i < count ( $tinfo ); $i ++) {
			$pers_info_id = $this->teacherIf->get_personal_info_id ( $tinfo [$i] ['id_teacher'] );
			
			// FIX: Sort name by alphabatical order
			$info = $this->personalInfoIf->get_name ( $pers_info_id );
			
			// print "NAME: first last " . $info[$i]['first_name'] . " " . $info[$i]['last_name'] . $tinfo[$i]['id_teacher'] . "<BR>";
			print '<li style="width:200px"><a href="wis_webIf.php?obj=teacher&meth=modify_teacher_record&a1=' . $tinfo [$i] ['id_teacher'] . '">';
			print $tinfo [$i] ['id_teacher'] . " - " . $info ['first_name'] . " ";
			if (isset ( $info ['middle_name'] )) {
				print $info ['middle_name'] . " ";
			}
			print $info ['last_name'] . '</a></li>';
		}
	}
	
	function reenter_teacher_record(&$record) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<div id="printableArea">';
		
		$this->teacher_record ( Action::RE_ENTER, $record );
		
		print "</FIELDSET>";
		
		wis_footer ( TRUE );
	}
	
	function modify_teacher_record($sid) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<div id="printableArea">';
		
		$this->teacher_record ( Action::MODIFY, $sid );
		
		print "</FIELDSET>";
		
		wis_footer ( TRUE );
	}
	
	function reenter_modify_teacher_record(&$record) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<div id="printableArea">';
		
		$this->teacher_record ( Action::REENTER_MODIFY, $record );
		
		print "</FIELDSET>";
		
		wis_footer ( TRUE );
	}
	
	function new_teacher_record() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<div id="printableArea">';
		
		$this->teacher_record ( Action::CREATE );
		
		print "</FIELDSET>";
		
		wis_footer ( TRUE );
	}
	
	public function view_Qteacher_student_grades($teacher_id, $teacher_f_name, $teacher_m_name, $teacher_l_name, $submitButton=true) {
		$start = 1;
		$stop = 8;
		
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		$info0 = $this->teacherIf->get_record ( $teacher_id );
		
		print "<H3> Test, Quiz, Midterm, Final Exam Grade Points (GP)</H3>";
		
		if (! empty ( $info0 ['grade'] )) {
			$test_dates_array      = array();
			$max_grade_point_array = array();
			
			$rv_array_strings = $this->administrationIf->get_test_dates_max_points($info0 ['grade'], $info0 ['section']);
			if (count($rv_array_strings)>1) {
				$test_dates_array= unserialize($rv_array_strings['test_dates']);
				$max_grade_point_array= unserialize($rv_array_strings['max_grade_points']);
			}
			print '<div id="printableArea">';

			print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
			print '<LEGEND style="font-size: 20px"></LEGEND>';
			
			$info = $this->registrationIf->get_student_grade_ids ( 'ALL', $info0 ['grade'], $info0 ['section'], $this->administrationIf->get_school_year () );
			
			print "<table class='v1_table'>";
			print "<caption> Student Grade Points: " . $info0 ['grade'] . "-" . $info0 ['section'] . " Teacher: " . $teacher_f_name . " " . $teacher_m_name . " " . $teacher_l_name . " </caption>";
			print "<tr>";
			
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'></th>";
			for($i = $start; $i <= $stop; $i++) {
				print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>GP_" . $i . "</th>";
			}
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'> Total </th>";
			print "</tr>";
			print "<tr>";
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Date </th>";
			for($i = $start; $i <= $stop; $i++) {
				print "<td style='border: 1px solid black;'><input type=text  name='date_" . $i . "' size=10 maxlength=10 ";
				if (count($test_dates_array)!=0 && !empty($test_dates_array[$i-1])) {
				    print " value = '" . $test_dates_array[$i-1] . "' "; 
				}
				print " placeholder='mm/dd/yyyy' > </td>";
			}
			print "</tr>";
			print "<tr>";
			print "<th style='border: 1px solid black; background-color: #CCCC99; color :#330000;'>Max Grade Points </th>";
			$total = 0; 
			for($i = $start; $i <= $stop; $i++) {
				print "<td style='border: 1px solid black;'><input type=text name='max_gp_" . $i . "' size=8 maxlength=8 ";
				if (count($max_grade_point_array)!=0 && !empty($max_grade_point_array[$i-1])) {
					print " value = '" . $max_grade_point_array[$i-1] . "' ";
					$total += $max_grade_point_array[$i-1];
				}
				print " > </td>";
			}
			print "<td style='border: 1px solid black;'>" . $total . "</td>";
			print "</tr>";
				
			for($i = 0; $i < count ( $info ); $i ++) {
				$pers_info_id = $this->studentIf->get_personal_info_id ( $info [$i] ['student_id'] );
				
				$info2 = $this->personalInfoIf->get_name ( $pers_info_id );
				$total = 0; 
				print "<tr>";
				print "<td style='border: 1px solid black;'>" . $info2 ['first_name'] . " " . $info2 ['middle_name'] . " " . $info2 ['last_name'] . "</td>";
				for($j = $start; $j <= $stop; $j ++) {
				    print "<td style='border: 1px solid black;'> <input type=text size=8 maxlength=8 name='gp_" . $info[$i]['student_id'] . "_" . $j . "'";
				    if (count($test_dates_array)!=0 && !empty($test_dates_array[$j-1])) {
					$rv_info = $this->studentRecordIf->isExist($info[$i]['student_id'],convert_normal_date_to_SQL($test_dates_array[$j-1]) );
					if ($rv_info != Null && isset($rv_info['points'])) {
					    print " value = '" . $rv_info['points'] . "' ";  
					    $total += $rv_info['points'];  
					}
				    }
				    print " > </td>";
				}
				print "<td style='border: 1px solid black;'>" . $total . "</td>";
				//print "<td style='border: 1px solid black;'><input type=text name='test_points'  size=20 maxlength=20> </td>";
				print "</tr >";
			}
			print "</table>";
			
			print "</FIELDSET>";
			print "</div>";
			
			print "<input type=hidden name='stu_grade'      value='" . $info0['grade'] . "'> ";
			print "<input type=hidden name='stu_section'    value='" . $info0['section'] . "'> ";
			print "<input type=hidden name='teacher_id'     value='" . $teacher_id . "'> ";
			print "<input type=hidden name='teacher_f_name' value='" . $teacher_f_name . "'> ";
			print "<input type=hidden name='teacher_m_name' value='" . $teacher_m_name . "'> ";
			print "<input type=hidden name='teacher_l_name' value='" . $teacher_l_name . "'> ";
				
			setSubmitValue ( "recordStudentGrades" );
		}
		
		wis_footer ( $submitButton );
	}
	
	public function view_Qgrade_student_grades($grade, $section) {
		$teacher_id = $this->teacherIf->get_Qgrade_teacher_id ( $grade, $section );
		$pers_info_id = $this->teacherIf->get_personal_info_id ( $teacher_id );
		
		$info = $this->personalInfoIf->get_name ( $pers_info_id );
		
		$this->view_Qteacher_student_grades ( $teacher_id, $info ['first_name'], $info ['middle_name'], $info ['last_name'], false);
	}
	
	public function wis_attendance($grade, $section, $teacher_name, $si) {
		$start = 1 + $si;
		$stop = 15 + $si;
		$school_year = $this->administrationIf->get_school_year(); 
				
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print "<input type=hidden name='stu_grade' value='" . $grade . "'> ";
		print "<input type=hidden name='stu_section' value='" . $section . "'> ";
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$info_array = $this->registrationIf->get_records ( RegistrationIf::WIS_GRADE, $grade . '-' . $section, $school_year, 'ALL' );
		
		print "<table class=sample>";
		print "<caption> Class Attendance: " . $grade . "-" . $section . " Teacher: " . $teacher_name . " </caption>";
		print "<col width='45'>";
		print "<tr>";
		print "<td> </td>";
		// print "<th style='border: 1px single black; background-color: #CCCC99; color :#330000;'>Student ID </th>";
		print "<th col width='85'>Student Name </th>";
		for($i = $start; $i <= $stop; $i ++) {
			print "<th>" . $i . "</th>";
		}
		print "<td> </td>";
		print "</tr>";
		print "<tr>";
		print "<td> </td>";
		print "<td>&nbsp</td>";
		
		$rv_array_string = $this->administrationIf->get_school_days();
		$days_array= unserialize($rv_array_string);
		
		for($i = $start; $i <= $stop; $i ++) {
			print "<td><input type=text name='date_" . $i . "' value='" . $days_array[($i-1)] . "' style='font-size:10px;' size=5 maxlength=5 readonly> </td>";
		}
		print "<td> </td>";
		print "</tr>";
		
		// while ($info = $info_array) {
		for ($i = 0; $i < count ( $info_array ); $i ++) {
			// print "STU ID " . $this->studentIf->get_personal_info_id($info_array[$i]['student_id']) . "<BR>";
			$personal_info_id = $this->studentIf->get_personal_info_id ( $info_array [$i] ['student_id'] );
			$info2 = $this->personalInfoIf->get_name ( $personal_info_id );
			$info3 = $this->attendanceIf->get_records($info_array[$i]['student_id'], $school_year);
			
			print "<tr>";
			print "<td></td>";
			print "<td>" . $info2 ['first_name'] . " " . $info2 ['middle_name'] . " " . $info2 ['last_name'] . "</td>";
			for($j = $start; $j <= $stop; $j ++) {
				$checked = false; 
				for ($k=0; $k<count($info3); $k++) {
					if ( ($info3[$k]['day_number'] == $j) && $info3[$k]['present'] ) {
						$checked = true; 
					}
				}
				
				print "<td> <input type=checkbox style='border: thin single black collapse; font-size: 12px' name='att_wk_" . $info_array [$i] ['student_id'] . "_" . $j . "' ";
				if ($checked) {
					print " checked ";
				} 
				print " > </td>";
			}
			print "</tr >";
		}
		print "<tr>";
		if ($start > 1) {
			print "<td><a href='wis_webIf.php?obj=teacher&meth=wis_attendance&a1=" . $grade . "&a2=" . $section . "&a3=" . $teacher_name . "&a4=" . ($si - 15) . "'>Prev </a></td>";
		} else {
			print "<td></td>";
		}
		
		for ($i=$start; $i<=$stop+2; $i++) {
			if ($i==($stop+2) && ($stop<=32)) {
		 		print "<td ><a href='wis_webIf.php?obj=teacher&meth=wis_attendance&a1=" . $grade . "&a2=" . $section . "&a3=" . $teacher_name . "&a4=" . (15+$si) . "'>Next</a></td>";
		 	} else {
		 		print "<td></td>";
		 	}
		}
				
		print "</tr >";
		print "</table>";
		
		print "</FIELDSET>";
		print "</div>";
		
		print "<input type=hidden name='start_rec' value='" . $start   . "'> ";
		print "<input type=hidden name='stop_rec' value='"  . $stop   . "'> ";
		print "<input type=hidden name='teacher_name' value='"  . $teacher_name   . "'> ";
		
		setSubmitValue ( "recordStudentAttendance" );
		
		wis_footer ( TRUE );
	}
	
	public function view_n_update_attendance($teacher_id, $teacher_name, $si) {
		$info = $this->teacherIf->get_record ( $teacher_id );
		
		if (empty ( $info ['section'] )) {
			$info ['section'] = 'A';
		}
		
		$this->wis_attendance ( $info ['grade'], $info ['section'], $teacher_name, $si );

	}
	
	public function record_student_attendance() {
		$school_year = $this->administrationIf->get_school_year();

		$info_array = $this->registrationIf->get_records ( RegistrationIf::WIS_GRADE, $_REQUEST['stu_grade'] . '-' . $_REQUEST['stu_section'], $school_year, 'ALL' );
		$incomplete = false; 

		for($i = 0; $i < count($info_array); $i ++) {
			//print "STU ID " . $info_array[$i]['student_id'] . "<BR>";
			$info3 = $this->attendanceIf->get_records($info_array[$i]['student_id'], $school_year);
			
			for ($j=$_REQUEST['start_rec']; $j<=$_REQUEST['stop_rec']; $j++) {
				$wk = "att_wk_" . $info_array [$i] ['student_id'] . "_" . $j ;
				
				$trans['day_number'] = $j;
				//$trans['day_month'] = $_REQUEST[$date];
				$trans['student_id'] = $info_array[$i]['student_id'];
				$trans['school_year'] = $school_year;
				
				//print "STU-ID: " . $info_array[$i]['student_id'] . "  PRE-PRESENT-" . $j . " " . $info3[$j]['present'] . "<BR>";
				if ( isset($_REQUEST[$wk]) ) {
					if (!empty($info3[$j-1]['present'])) {
						//print "STU-ID: " . $info_array[$i]['student_id'] . "  SET " . $info3[$j-1]['present'] . "<BR>";
					}
					$trans['present'] = '1';
					$this->attendanceIf->insert_record($trans);
				} else {
					if (!empty($info3[$j-1]['present'])) {
						//print "STU-ID: " . $info_array[$i]['student_id'] . "  RESET " . $info3[$j-1]['present'] . "<BR>";
					}
					if (!empty($info3[$j-1]['present']) && $info3[$j-1]['present']=='1') {
						//print "STU-ID: " . $info_array[$i]['student_id'] . "  RESET " . $info3[$j]['present'] . "<BR>";
						$trans['present'] = '0';
						$this->attendanceIf->insert_record($trans);
					}
				}
			}
		}
		$this->wis_attendance($_REQUEST['stu_grade'], $_REQUEST['stu_section'], $_REQUEST['teacher_name'], 0);
		
	}
	
	public function record_student_grades() {
		
		$max_gps = array();
		$test_dates = array();
		for($i = 1; $i <= 8; $i++) {
		    if (!empty($_REQUEST['date_' . $i])) {
			array_push($max_gps,    $_REQUEST['max_gp_' . $i]);
			array_push($test_dates, $_REQUEST['date_' . $i]);
		    }
		}

		$info = $this->registrationIf->get_student_grade_ids('ALL',$_REQUEST['stu_grade'],$_REQUEST['stu_section'],$this->administrationIf->get_school_year());

		for($i = 0; $i < count($info); $i++) {
		    for($j = 1; $j <= 8; $j++) {
			if (!empty($_REQUEST['date_' . $j])) {
			    if ($this->studentRecordIf->isExist($info[$i]['student_id'],convert_normal_date_to_SQL($_REQUEST['date_' . $j]))) {
				$this->studentRecordIf->update_record($info[$i]['student_id'],convert_normal_date_to_SQL($_REQUEST['date_' . $j]), $_REQUEST['gp_' . $info[$i]['student_id'] . '_' . $j]);
			    } else {
				$this->studentRecordIf->insert_record($info[$i]['student_id'],convert_normal_date_to_SQL($_REQUEST['date_' . $j]), $_REQUEST['gp_' . $info[$i]['student_id'] . '_' . $j]);
			    }
			}
		    }
		}	
			    
		$max_gps_string    = $this->mysqli_h->real_escape_string(serialize($max_gps));
		$test_dates_string = $this->mysqli_h->real_escape_string(serialize($test_dates));

		$this->administrationIf->update_test_dates_max_points($_REQUEST['stu_grade'], $_REQUEST['stu_section'], $test_dates_string, $max_gps_string);
		$this->view_Qteacher_student_grades($_REQUEST['teacher_id'], $_REQUEST['teacher_f_name'], $_REQUEST['teacher_m_name'], $_REQUEST['teacher_l_name']);
	}
	
	public function wis_class_attendance($grade, $section) {
		$teacher_id = $this->teacherIf->get_Qgrade_teacher_id ( $grade, $section );
		
		$personal_info_id = $this->teacherIf->get_personal_info_id ( $teacher_id );
		
		$info = $this->personalInfoIf->get_name ( $personal_info_id );

		$teacher_name = $info['first_name'] . " " . $info['middle_name'] . " " . $info['last_name'];
		
		$this->wis_attendance ( $grade, $section, $teacher_name, 0 );
	}
	
	/* ---------- PRIVATE FUNCTIONS ---------------- */
	private function teacher_record($action, &$record = 0) {
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>New Teacher Record</B></LEGEND>";
		
		if ($action == Action::MODIFY) {
			$sid = $record;
			$pers_info_id = $this->teacherIf->get_personal_info_id ( $record );
			
			$info = $this->personalInfoIf->get_record ( $pers_info_id );
			
			list ( $cell_area, $cell_local, $cell_number ) = split ( '[-]', $info ['cell_phone'] );
			list ( $home_area, $home_local, $home_number ) = split ( '[-]', $info ['home_phone'] );
		} else {
			if (isset ( $record ['cell_area'] ) && isset ( $record ['cell_local'] ) && isset ( $record ['cell_number'] )) {
				$cell_area = $record ['cell_area'];
				$cell_local = $record ['cell_local'];
				$cell_number = $record ['cell_number'];
			}
			if (isset ( $record ['home_area'] ) && isset ( $record ['home_local'] ) && isset ( $record ['home_number'] )) {
				$home_area = $record ['home_area'];
				$home_local = $record ['home_local'];
				$home_number = $record ['home_number'];
			}
			$info = $record;
			if ($action == Action::REENTER_MODIFY) {
				$sid = $record ['tid'];
			}
		}
		
		print "<table>";
		
		if ($action == Action::MODIFY || $action == Action::REENTER_MODIFY) {
			print "<tr>";
			print "<td class='normal1'>Teacher ID</td>";
			print "<td><input type=text name='tid' size=12 maxlength=20 value= '" . $sid . "' readonly='readonly' ></td>";
			print "</tr>";
		}
		
		print "<tr>";
		print "<td class='normal1'>First Name</td>";
		print "<td><input type=text name='first_name' size=12 maxlength=20 value= '";
		if (isset ( $info ['first_name'] )) {
			print $info ['first_name'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td  class='normal1' style='size=10'>Middle Name</td>";
		print "<td><input type=text name='middle_name' size=4 maxlength=15 value= '";
		if (isset ( $info ['middle_name'] )) {
			print $info ['middle_name'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1' style='size=55'>Last Name</td>";
		print "<td><input type=text name='last_name' size=12 maxlength=20 value= '";
		if (isset ( $info ['last_name'] )) {
			print $info ['last_name'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Cell Phone </td>";
		
		print "<td><input type=text name='cell_area' size=3 maxlength=3 value= '";
		if (isset ( $cell_area )) {
			print $cell_area;
		}
		print "' >";
		
		print "<input type=text name='cell_local' size=3 maxlength=3 value= '";
		if (isset ( $cell_local )) {
			print $cell_local;
		}
		print "' >";
		print "-";
		print "<input type=text name='cell_number' size=4 maxlength=4 value= '";
		if (isset ( $cell_number )) {
			print $cell_number;
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Home Phone</td>";
		
		print "<td><input type=text name='home_area' size=3 maxlength=3 value= '";
		if (isset ( $home_area )) {
			print $home_area;
		}
		print "' >";
		print "<input type=text name='home_local' size=3 maxlength=3 value= '";
		if (isset ( $home_local )) {
			print $home_local;
		}
		print "' >";
		print "-";
		print "<input type=text name='home_number' size=4 maxlength=4 value= '";
		if (isset ( $home_number )) {
			print $home_number;
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Email </td>";
		print "<td><input type=text name='email' size=30 value= '";
		if (isset ( $info ['email'] )) {
			print $info ['email'];
		}
		print "' ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Address </td>";
		print "<td><input type=text name='address' size=24 maxlength=55 value= '";
		if (isset ( $info ['address'] )) {
			print $info ['address'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>City </td>";
		print "<td><input type=text name='city' size=15 maxlength=30 value= '";
		if (isset ( $info ['city'] )) {
			print $info ['city'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "<tr>";
		print '<td>State:</td>';
		print "<td>";
		print "<input type=text name='state' size=7 maxlength=10 value= 'California' readonly='readonly' style='color:blue;' ";
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Zipcode </td>";
		print "<td><input type=text name='zipcode' size=5 maxlength=5 value= '";
		if (isset ( $info ['zipcode'] )) {
			print $info ['zipcode'];
		}
		print "' ></td>";
		print "</tr>";
		
		print "</table>";
		
		print "</FIELDSET>";
		
		if ($action == Action::MODIFY) {
			setSubmitValue ( "updateTeacherRecord" );
		} else {
			setSubmitValue ( "insertTeacherRecord" );
		}
	}
}

?>
