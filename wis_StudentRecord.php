<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_StudentIf.php';
include_once 'wis_ParentIf.php';
include_once 'wis_RegistrationIf.php';
include_once 'wis_RegistrationForm.php';
include_once 'wis_TeacherIf.php';
include_once 'wis_AccountIf.php';
include_once 'wis_GradeBookIf.php';
include_once 'wis_BookIf.php';

class StudentRecord {

	const NAME_ID_SEARCH = 1;
	const NAME_SEARCH = 2;
	const ID_SEARCH = 3;
	private $personalInfoIf;
	private $studentIf;
	private $parentIf;
	private $registrationIf;
	private $teacherIf;
	private $administrationIf;
	private $accountIf;
	private $bookIf;
	private $gradeBookIf;
	private $infoRegistration;
	private $infoParent;
	private $infoStudent;
	private $infoPersonalInfo;
	private $studentId;
	private $personalInfoId;
	private $regForm;
	private $mysqli_h;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->parentIf = new ParentIf ( $mysqli_h );
		$this->registrationIf = new RegistrationIf ( $mysqli_h );
		$this->studentIf = new StudentIf ( $mysqli_h );
		$this->teacherIf = new TeacherIf ( $mysqli_h );
		$this->personalInfoIf = new PersonalInfoIf ( $mysqli_h );
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
		$this->accountIf = new AccountIf ( $mysqli_h );
		$this->bookIf = new BookIf ( $mysqli_h );
		$this->gradeBookIf = new GradeBookIf ( $mysqli_h );
		$this->regForm = new RegistrationForm ( $mysqli_h );
	}
	
	function get_records($sid) {
		$this->studentId = $sid;
		$this->personalInfoId = $this->studentIf->get_personal_info_id ( $sid );
		$this->infoPersonalInfo = $this->personalInfoIf->get_record ( $this->personalInfoId );
		$this->infoRegistration = $this->registrationIf->get_record ( $sid, $this->administrationIf->get_school_year (), 'ACTIVE' );
		$parent_id = $this->studentIf->get_parent_id ( $sid );
		$this->infoParent = $this->parentIf->get_record ( $parent_id );
		$this->infoStudent = $this->studentIf->get_record ( $sid );
	}
	
	/**
	 *
	 * @return Get tution fee and discounts
	 */
	function get_tution_discounts() {
		return $this->administrationIf->get_tution_discounts ();
	}
	
	/* ^^^^^^^^^^^^^^^^ METHODS called by MENU ^^^^^^^^^^^^^^^^^^^^^^ */
	public function view_student_record($sid) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		if (! empty ( $sid )) {
			$this->get_records ( $sid );
		}
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<B>ICSGV WIS Registration Form ' . $this->administrationIf->get_school_year() . "</B>";
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		if (! empty ( $sid )) {
			$this->show_individual_rec ();
		}
		
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( FALSE );
	}
	
	public function get_profile($sid) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$this->get_records ( $sid );
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Student Information</B></LEGEND>";
		
		$this->show_student_info ();
		
		print "</FIELDSET>";
		
		print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Parent Information</B></LEGEND>";
		
		$this->show_parent_info ();
		print "</FIELDSET>";
		
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( FALSE );
	}
	
	public function view_student_record_n_modify($sid) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$this->get_records ( $sid );
		
		$this->modify_student_data ( FALSE );
		
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( TRUE );
	}
	
	public function view_student_list($grade = 'ALL', $reg_status = 'ALL') {
		include_once ("wis_header.php");
		
		$school_year = $this->administrationIf->get_school_year ();
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		
		if ($grade === 'ALL' && $reg_status === 'ALL') {
			$stu_ids = $this->registrationIf->get_student_ids ( $school_year );
			print '<LEGEND style="font-size: 20px">All Records - ' . count($stu_ids) . ' students </LEGEND>';
		} elseif ($grade === 'ALL' && $reg_status === 'APPROVED') {
			$stu_ids = $this->registrationIf->get_student_grade_ids ( 'APPROVED', 'ALL', 'ALL', $school_year );
			print '<LEGEND style="font-size: 20px">Approved Records - ' . count($stu_ids) . ' students </LEGEND>';
		} else {
			$stu_ids = $this->registrationIf->get_student_grade_ids ( 'APPROVED', $grade, 'ALL', $school_year );
			print '<LEGEND style="font-size: 20px">Grade Records - ' . count($stu_ids) . ' students </LEGEND>';
		}
		
		$cnt = 1;
		// print "<table border cellpadding=3 id='myTable' class='tablesorter'>";
		print "<table id='myTable' class='tablesorter' border cellpadding=3>";
		
		print "<thead>";
		print "<tr>";
		print "<th>Num</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 1, asc1, 0);  asc1 *= -1;'>Stu ID</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 2, asc2, 0);  asc2 *= -1;'>First Name, M</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 3, asc3, 0);  asc3 *= -1;'>Last Name</th> ";
		print "<th>Father Name</th> ";
		print "<th>Mother Name</th> ";
		print "<th>Email</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 7, asc4, 0);  asc4 *= -1;'>Grade</th> ";
		// print "<th>Regular School Grade</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 8, asc5, 0);  asc5 *= -1;'>Teacher Name</th> ";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 9, asc6, 0);  asc6 *= -1;'>Status</th> ";
		print "<th>Billed</th>";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 11, asc7, 0); asc7 *= -1;'>Paid</th>";
		print "<th style='background-color:#beff33;' onclick='sort_table(studentBody, 12, asc8, 0); asc8 *= -1;'>Bal Due</th>";
		print "</tr>";
		print "</thead>";
		print "<tbody id='studentRecord'>";
				
		// FIX: Sort by last_name, first_name
		$studentName = array ();
		$j = 0;
		for($i = 0; $i < count ( $stu_ids ); $i++) {
		        if ($this->studentIf->isStatus($stu_ids [$i] ['student_id'], 'ACTIVE') ) {
				$pers_info_id = $this->studentIf->get_personal_info_id ( $stu_ids [$i] ['student_id'] );
				$studentName [$j] = $this->personalInfoIf->get_name ( $pers_info_id );
				$studentName [$j] ['student_id'] = $stu_ids [$i] ['student_id'];
				$studentName [$j] ['pers_info_id'] = $pers_info_id;
				$j++;
			}
		}
		usort ( $studentName, array ($this, 'nameSort') );
		
		for($i = 0; $i < count ( $studentName ); $i ++) {
		        $student_id = $studentName [$i] ['student_id'];
		        //$teacher_id = $this->studentIf->get_teacher_id ($student_id);
			$parent_id = $this->studentIf->get_parent_id ($student_id);
			
			$infop = $this->parentIf->get_record ( $parent_id );
			// $studentName = $this->personalInfoIf->get_name($pers_info_id);
			$regInfo = $this->registrationIf->get_record ($student_id, $school_year );
			$book_cost = 0;
		        if (! empty ( $regInfo ['wis_grade'] ) && ! empty ( $regInfo ['section'] )) {
			    $teacher_id = $this->teacherIf->get_Qgrade_teacher_id($regInfo ['wis_grade'], $regInfo ['section'] );
			    $tpid  = $this->teacherIf->get_personal_info_id($teacher_id);
			    $tinfo = $this->personalInfoIf->get_name($tpid);
			} else {
			    $tinfo['first_name'] = "";
			    $tinfo['last_name']  = "";
			}
			if (! empty ( $regInfo ['wis_grade'] )) {
				
				$book_info = $this->gradeBookIf->get_Qgrade_book_list ( $regInfo ['wis_grade'] );
				for($k = 0; $k < count ( $book_info ); $k ++) {
				    if ($this->bookIf->isBookNeeded($student_id, $book_info[$k]['id_gb'])) {
					$book_cost += $book_info [$k] ['cost'];
				    }
				}
			}
			
			$ainfo = $this->get_tution_discounts ();
			// print "TUTION FEE " . $ainfo['tution_fee'] . "<BR>";
			$tfee = $ainfo ['tution_fee'] + $book_cost + $regInfo ['miscl_charges'];
			$tfee -= ($regInfo ['icsgv_mem'] * $ainfo ['icsgv_mem_discount']);
			$tfee -= ($regInfo ['num_siblings'] * $ainfo ['sibling_discount']);
			if ($regInfo ['payment_plan'] > 1) {
				$tfee += $ainfo ['payment_plan_fee'];
			}
			
			$apaid = $this->accountIf->get_amount_paid ( $studentName [$i] ['pers_info_id'], $school_year );
			
			print "<tr>";
			
			print "<td>" . $cnt ++ . "</td> ";
			print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $student_id . "'>";
			print $student_id . "</td>";
			print "<td>" . getCell ( $studentName [$i] ['first_name'] );
			if (! empty ( $studentName [$i] ['middle_name'] )) {
				print ", " . getCell ( $studentName [$i] ['middle_name'] );
			}
			print "</td>";
			print "<td>" . getCell ( $studentName [$i] ['last_name'] ) . " </td>";
			// print "<td>" . getCell(convert_sql_date_to_normal($regInfo['register_date'])) . " </td>";
			print "<td>" . getCell ( $infop ['father_first_name'] ) . " " . getCell ( $infop ['father_last_name'] ) . " </td>";
			print "<td>" . getCell ( $infop ['mother_first_name'] ) . " " . getCell ( $infop ['mother_last_name'] ) . " </td>";
			print "<td>" . getCell ( $infop ['par_email'] ) . " </td>";
			
			print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $student_id . "'>";
			print getCell ( $regInfo ['wis_grade'] );
			if (! empty ( $regInfo ['wis_grade'] ) && ! empty ( $regInfo ['section'] )) {
				print "-";
			}
			print getCell ( $regInfo ['section'] ) . " </td>";
			// print "<td>". getCell($regInfo['reg_school_grade']) . " </td>";
			print "<td>" . getCell ( $tinfo ['first_name'] ) . " " . getCell ( $tinfo ['last_name'] ) . " </td>";
			print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $student_id . "'>";
			print getCell ( $regInfo ['reg_status'] ) . " </td>";
			print "<td><pre>" . sprintf("%' 6.2f", $tfee) . "</pre></td>";
			print "<td><pre>" . sprintf("%' 6.2f", $apaid) . "</pre></td>";

			print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_tution_plan_n_setup&a1=" . $student_id . "'>";
			print  "<pre>" . sprintf("%' 6.2f", ($tfee - $apaid)) . "</pre></td>";
			
			print "</tr>";
		}
		print "</tbody>";
		print "</table>";
		wis_footer ( FALSE );
	}

	public function view_account_list($grade = 'ALL', $reg_status = 'APPROVED') {
	    include_once ("wis_header.php");
	    
	    wis_main_menu ( $this->mysqli_h, TRUE );
	    
	    print '<div id="printableArea">';
	    
	    print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
	    print '<LEGEND style="font-size: 20px"></LEGEND>';

	    $ainfo = $this->get_tution_discounts();
	    print "<H4> Tution Fee " . $ainfo ['tution_fee'] . " </H4>";

	    $cnt = 1;
	    // print "<table border cellpadding=3 id='myTable' class='tablesorter'>";
	    print "<table id='myTable' class='tablesorter' border cellpadding=3>";
	    
	    print "<tr>";
	    print "<th>Num</th> ";
	    print "<th>Stu ID</th> ";
	    print "<th>First Name, M</th> ";
	    print "<th>Last Name</th> ";
	    print "<th>Book Fee</th> ";
	    print "<th>Multi pay fee</th> ";
	    print "<th>Miscl. Charges</th> ";
	    print "<th>ICSGV Disc</th> ";
	    print "<th>Sibling Disc</th> ";
	    print "<th>Billed</th>";
	    print "<th>Paid</th>";
	    print "<th>Bal Due</th>";
	    print "</tr>";
	    
	    $school_year = $this->administrationIf->get_school_year ();
	    
	    if ($grade === 'ALL') {
		$stu_ids = $this->registrationIf->get_student_ids ( $school_year );
	    } else {
		$stu_ids = $this->registrationIf->get_student_grade_ids ( 'APPROVED', $grade, 'ALL', $school_year );
	    }
	    
	    // FIX: Sort by last_name, first_name
	    $studentName = array ();
	    $j = 0;
	    for($i = 0; $i < count ( $stu_ids ); $i++) {
		if ($this->studentIf->isStatus($stu_ids [$i] ['student_id'], 'ACTIVE') ) {
		    $pers_info_id = $this->studentIf->get_personal_info_id ( $stu_ids [$i] ['student_id'] );
		    $studentName [$j] = $this->personalInfoIf->get_name ( $pers_info_id );
		    $studentName [$j] ['student_id'] = $stu_ids [$i] ['student_id'];
		    $studentName [$j] ['pers_info_id'] = $pers_info_id;
		    $j++;
		}
	    }
	    usort ( $studentName, array ($this, 'nameSort') );
	    
	    for($i = 0; $i < count ( $studentName ); $i ++) {
		$student_id = $studentName [$i] ['student_id'];
		$parent_id  = $this->studentIf->get_parent_id ($student_id);
		
		$infop = $this->parentIf->get_record ( $parent_id );
		// $studentName = $this->personalInfoIf->get_name($pers_info_id);
		$regInfo = $this->registrationIf->get_record ($student_id, $school_year );
		$book_cost = 0;
		if (! empty ( $regInfo ['wis_grade'] )) {
		    $book_info = $this->gradeBookIf->get_Qgrade_book_list ( $regInfo ['wis_grade'] );
		    for($k = 0; $k < count ( $book_info ); $k ++) {
			if ($this->bookIf->isBookNeeded($student_id, $book_info[$k]['id_gb'])) {
			    $book_cost += $book_info [$k] ['cost'];
			}
		    }
		}
		
		// print "TUTION FEE " . $ainfo['tution_fee'] . "<BR>";
		$tfee = $ainfo ['tution_fee'] + $book_cost + $regInfo ['miscl_charges'];
		$tfee -= ($regInfo ['icsgv_mem'] * $ainfo ['icsgv_mem_discount']);
		$tfee -= ($regInfo ['num_siblings'] * $ainfo ['sibling_discount']);
		if ($regInfo ['payment_plan'] > 1) {
		    $tfee += $ainfo ['payment_plan_fee'];
		}
		
		$apaid = $this->accountIf->get_amount_paid ( $studentName [$i] ['pers_info_id'], $school_year );
		
		print "<tr>";
		
		print "<td>" . $cnt ++ . "</td> ";
		print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $student_id . "'>";
		print $student_id . "</td>";
		print "<td>" . getCell ( $studentName [$i] ['first_name'] );
		if (! empty ( $studentName [$i] ['middle_name'] )) {
		    print ", " . getCell ( $studentName [$i] ['middle_name'] );
		}
		print "</td>";
		print "<td>" . getCell ( $studentName [$i] ['last_name'] ) . " </td>";
		// print "<td>" . getCell(convert_sql_date_to_normal($regInfo['register_date'])) . " </td>";
		print "<td>" . number_format($book_cost,2) . " </td>";
		print "<td>";
 		if ($regInfo ['payment_plan'] > 1) {
		    print $ainfo ['payment_plan_fee'];
		} else {
		    print "0";
		}
		print " </td>";
		
		print "<td>" . number_format($regInfo ['miscl_charges'],2)  . " </td>";
		print "<td>" . number_format( ($regInfo ['icsgv_mem'] * $ainfo ['icsgv_mem_discount']),2) . " </td>";
		print "<td>" . number_format( ($regInfo ['num_siblings'] * $ainfo ['sibling_discount']),2) . " </td>";
		print "<td>" . number_format($tfee,2) . "</td>";
		print "<td>" . number_format($apaid,2) . " </td>";
		print "<td class='stu_status' onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_tution_plan_n_setup&a1=" . $student_id . "'>";
		print number_format(($tfee - $apaid),2) . "</td>";
		
		print "</tr>";
	    }
	    print "</table>";
	    wis_footer ( FALSE );
	}
	
	public function view_account_summary_list($reg_status = 'APPROVED') {
	    include_once ("wis_header.php");
	    
	    wis_main_menu ( $this->mysqli_h, TRUE );
	    
	    print '<div id="printableArea">';
	    
	    print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
	    print '<LEGEND style="font-size: 20px"></LEGEND>';

	    $ainfo = $this->get_tution_discounts();
	    print "<H4> WIS Account Summary Report " . today_date() . " </H4>";

	    $cnt = 1;
	    // print "<table border cellpadding=3 id='myTable' class='tablesorter'>";
	    print "<table id='myTable' class='tablesorter' border cellpadding=3>";
	    
	    print "<tr>";
	    print "<th>Grade</th> ";
	    print "<th>Students</th> ";
	    print "<th>Amount Billed</th> ";
	    print "<th>Amount Received</th> ";
	    print "<th>Balance Due </th> ";
	    print "</tr>";
	    
	    $school_year = $this->administrationIf->get_school_year ();
	    
	    $class_arr = array('BASIC', 'PRE_K', 'K', '1', '2', '3', '4', '5', '6', '7', '8', 'YG');
	    $total_num_stu = 0;
	    $total_billed  = 0;
	    $total_recvd   = 0;
	    $total_bal_due = 0;
	    
	    foreach ($class_arr as &$grade) {
		$stu_ids = $this->registrationIf->get_student_grade_ids ( 'APP_PENDING', $grade, 'ALL', $school_year );

		$tfee = 0;
		$apaid = 0; 
		$stu_grade_tution_fee_inv  = 0; 
		$stu_grade_tution_fee_paid = 0; 
		$stu_grade_tution_fee_bal  = 0;
		
		$stu_grade_bal_due = 0; 
		for($i = 0; $i < count ( $stu_ids ); $i++) {

		    $regInfo = $this->registrationIf->get_record ( $stu_ids[$i]['student_id'], $school_year );
		    $book_cost = 0;

		    if ( !($this->studentIf->isStatus($stu_ids[$i]['student_id'], 'ACTIVE')) ) {
			continue; 
		    }
		    $iReg = $this->registrationIf->get_record ( $stu_ids[$i]['student_id'], $this->administrationIf->get_school_year (), 'ACTIVE' );
		    
		    $book_info = $this->gradeBookIf->get_Qgrade_book_list( $iReg['wis_grade'] );
		    for($j = 0; $j < count ( $book_info ); $j++) {
			if ( $this->bookIf->isBookNeeded($stu_ids[$i]['student_id'],  $book_info [$j] ['id_gb']) ) {
			    $book_cost += $book_info [$j] ['cost'];
			}
		    }
		    
		    // print "TUTION FEE " . $ainfo['tution_fee'] . "<BR>";
		    $tfee = $ainfo ['tution_fee'] + $book_cost + $regInfo ['miscl_charges'];
		    $tfee -= ($regInfo ['icsgv_mem'] * $ainfo ['icsgv_mem_discount']);
		    $tfee -= ($regInfo ['num_siblings'] * $ainfo ['sibling_discount']);
		    if ($regInfo ['payment_plan'] > 1) {
			$tfee += $ainfo ['payment_plan_fee'];
		    }
		    
		    $apaid = $this->accountIf->get_amount_paid ( $this->studentIf->get_personal_info_id($stu_ids[$i]['student_id']), $school_year );
		    
		    $stu_grade_tution_fee_inv  += $tfee;
		    $stu_grade_tution_fee_paid += $apaid;
		    $stu_grade_tution_fee_bal  += ($tfee - $apaid);
		}

		print "<tr>";
		print "<td>" . $grade . "</td>";
		print "<td>" . count ( $stu_ids ) . "</td> ";

		print "<td>" . number_format($stu_grade_tution_fee_inv,2) . "</td>";
		print "<td>" . number_format($stu_grade_tution_fee_paid,2) . " </td>";
		print "<td>" . number_format($stu_grade_tution_fee_bal,2) . "</td>";

		$total_num_stu += count ( $stu_ids );
		$total_billed  += $stu_grade_tution_fee_inv; 
		$total_recvd   += $stu_grade_tution_fee_paid;
		$total_bal_due += $stu_grade_tution_fee_bal;
		print "</tr>";
	    }
	    print "<tr>";
	    print "<td></td>";
	    print "<td>" . number_format($total_num_stu,2) . "</td>";
	    print "<td>" . number_format($total_billed,2)  . "</td>";
	    print "<td>" . number_format($total_recvd,2)   . "</td>";
	    print "<td>" . number_format($total_bal_due,2) . "</td>";
	    print "</tr>";

	    print "</table>";
	    wis_footer ( FALSE );
	}

	public function view_student_list_n_assign_grade() {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		$school_year = $this->administrationIf->get_school_year ();
		$stu_ids = $this->registrationIf->get_student_status_ids ( 'PENDING', $school_year );
		
		$cnt = 1;
		print "<table border cellpadding=3>";
		
		print "<tr>";
		print "<th style='background-color: #CCCC99;'>Num</th> ";
		print "<th style='background-color: #CCCC99;'>Id</th> ";
		print "<th style='background-color: #CCCC99;'>Full Name </th> ";
		// print "<th style='background-color: #CCCC99;'>Register Date</th> ";
		print "<th style='background-color: #CCCC99;'>RS Grade</th> ";
		print "<th style='background-color: #CCCC99;' colspan='13'>Grade / Section</th> ";
		// print "<th style='background-color: #CCCC99;'>Status</th> ";
		print "</tr>";
		$ctr = 0;
		$j = 0;
		$info = array ();
		
		for($i = 0; $i < count ( $stu_ids ); $i ++) {
		        if ($this->studentIf->isStatus ($stu_ids [$i] ['student_id'], 'ACTIVE') ) {
				$pers_info_id = $this->studentIf->get_personal_info_id ( $stu_ids [$i] ['student_id'] );
				$info [$j] = $this->personalInfoIf->get_name ( $pers_info_id );
				$info [$j] ['student_id'] = $stu_ids [$i] ['student_id'];
				$info [$j] ['pers_info_id'] = $pers_info_id;
				$j++;
			}
		}
		
		usort ( $info, array ($this, 'nameSort') );
		
		for($i = 0; $i < count ( $info ); $i ++) {
			$infor = $this->registrationIf->get_record ( $info [$i] ['student_id'], $school_year );
			print "<tr>";
			print "<td rowspan='3'>" . $cnt ++ . "</td> ";
			print "<td rowspan='3'>" . $info [$i] ['student_id'] . " </td>";
			print "<td rowspan='3'>" . getCell ( $info [$i] ['first_name'] );
			if ($info [$i] ['middle_name']) {
				print " " . $info [$i] ['middle_name'];
			}
			print " " . getCell ( $info [$i] ['last_name'] ) . " </td>";
			
			// print "<td rowspan='2'>". getCell(convert_sql_date_to_normal($info[$i]['register_date'])) . " </td>";
			print "<td rowspan='3'>" . getCell ( $infor ['reg_school_grade'] ) . " </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='na'";
			if (empty ( $infor ['wis_grade'] )) {
				print " checked='yes'";
			}
			print " >na </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='Basic'";
			if ($infor ['wis_grade'] === 'Basic') {
				print " checked='yes'";
			}
			print " >Basic </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='PRE_K'";
			if ($infor ['wis_grade'] === 'PRE_K') {
				print " checked='yes'";
			}
			print " >PRE_K </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='KG'";
			if ($infor ['wis_grade'] === 'KG') {
				print " checked='yes'";
			}
			print " >KG </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='1'";
			if ($infor ['wis_grade'] == '1') {
				print " checked='yes'";
			}
			print " >1 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='2'";
			if ($infor ['wis_grade'] == '2') {
				print " checked='yes'";
			}
			print " >2 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='3'";
			if ($infor ['wis_grade'] == '3') {
				print " checked='yes'";
			}
			print " >3 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='4'";
			if ($infor ['wis_grade'] == '4') {
				print " checked='yes'";
			}
			print " >4 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='5'";
			if ($infor ['wis_grade'] == '5') {
				print " checked='yes'";
			}
			print " >5 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='6'";
			if ($infor ['wis_grade'] == '6') {
				print " checked='yes'";
			}
			print " >6 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='7'";
			if ($infor ['wis_grade'] == '7') {
				print " checked='yes'";
			}
			print " >7 </td>";
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='8'";
			if ($infor ['wis_grade'] == '8') {
				print " checked='yes'";
			}
			print " >8 </td>";
			
			print "<td style='border: 1px solid black;' rowspan='3'><input type=radio name='student_grade_" . $ctr . "' value='YG'";
			if ($infor ['wis_grade'] == 'YG') {
				print " checked='yes'";
			}
			print " >YG </td>";
			
			print "<td style='border:1px solid black;'><input type=radio name='student_section_" . $ctr . "'  value='na'";
			if (empty ( $infor ['section'] )) {
				print " checked='yes'";
			}
			print " >na </td>";
			print "</tr>";
			print "<tr>";
			
			print "<td style='border:1px solid black;'><input type=radio name='student_section_" . $ctr . "'  value='A'";
			if ($infor ['section'] == 'A') {
				print " checked='yes'";
			}
			print " >A </td>";
			
			// print "<td>". getCell($infor['wis_grade']) . "-" . getCell($info[$i]['section']) . " </td>";
			
			// print "<td>". getCell($infor['status']) . " </td>";
			print "</tr>";
			print "<tr>";
			print "<td style='border: 1px solid black;'><input type=radio name='student_section_" . $ctr . "'  value='B'";
			if ($infor ['section'] == 'B') {
				print " checked='yes'";
			}
			print " >B </td>";
			print "</tr>";
			print "<input type=hidden name='student_id_" . $ctr . "' value='" . $info [$i] ['student_id'] . "'>";
			$ctr ++;
		}
		print "</table>";
		print "<input type=hidden name='num_students' value='" . $ctr . "'>";
		print '</div>';
		print "</FIELDSET>";
		
		print "</FIELDSET>";
		
		print '</div>';
		
		setSubmitValue ( "approveStudentRegistrations" );
		
		wis_footer ( TRUE );
	}
	
	public function view_tution_plan_n_setup($sid, $readonly = TRUE, $rc_info=NULL) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		$this->get_records ( $sid );
		
		$accountInfo = $this->accountIf->get_record ( $this->personalInfoId, $this->administrationIf->get_school_year() );
		
		$today = today_date ();
		
		$ainfo = $this->get_tution_discounts ();
		
		$sibling_disc = $this->infoRegistration ['num_siblings'] * $ainfo ['sibling_discount'];
		$mem_disc = $this->infoRegistration ['icsgv_mem'] * $ainfo ['icsgv_mem_discount'];
		$tfee = $ainfo ['tution_fee'];
		
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		}
		$book_cost = 0;
		$prev_pay = 0;
		print '<div id="printableArea">';
		
		print '<div style="width:40%; height:80%; float:left;" >';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; ">';
		print "<LEGEND><B>Amount Previously Paid</B></LEGEND>";

		print '<table>';
		for($i = 0; $i < count ( $accountInfo ); $i ++) {
			print '<tr>';
			print "<td> " . convert_sql_date_to_normal ( $accountInfo [$i] ['trans_date'] ) . "</td><td><input type=text size=6 value='" . $accountInfo [$i] ['amount'] . "' style='text-align:right;color:blue;' readonly></td>";
			print '</tr>';
			$prev_pay += $accountInfo [$i] ['amount'];
		}
		if ($prev_pay > 0.0) {
			print '<tr>';
			print "<td>Total</td><td><input type=text size=6 value='" . number_format ( $prev_pay, 2 ) . "' style='text-align:right;color:blue;' readonly></td>";
			print '</tr>';
		}
		print '</table>';
		
		print "</FIELDSET>";
		
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<BR>';
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; ">';
		print "<LEGEND><B>Book Cost</B></LEGEND>";
		if (! empty ( $this->infoRegistration ['wis_grade'] )) {
			
			$book_info = $this->gradeBookIf->get_Qgrade_book_list ( $this->infoRegistration ['wis_grade'] );
			print '<table>';
			for($i = 0; $i < count ( $book_info ); $i ++) {

			    if ( $this->bookIf->bookNotExist($sid,  $book_info [$i] ['id_gb']) ) {
				$this->bookIf->insertRecord($sid,  $book_info [$i] ['id_gb']);
			    }
			    
			    print '<tr>';
			    $bid = "'book-" . $sid . "-" . $i . "'"; 
			    
			    print '<input type=hidden name="gbook_id-' . $i . '" value="' . $book_info [$i] ['id_gb'] . '">';
			    
			    print '<td><input type=checkbox name="' . $bid . '"';
			    
			    if (empty($rc_info)) {
				if ( $this->bookIf->isBookNeeded($sid,  $book_info [$i] ['id_gb']) ) {
				    print ' checked ';
				    $book_cost += $book_info [$i] ['cost'];
				}
			    } else {
				if (!empty($rc_info[$bid]) && $rc_info[$bid]) { 
				    print ' checked ';
				    $book_cost += $book_info [$i] ['cost'];
				}
			    }
			    print '></td>';
			    print '<td>' . $book_info [$i] ['book_name'] . '</td>';
			    print '<td><input type=text size=4 value="' . $book_info [$i] ['cost'] . '" style="text-align:right;color:blue;" readonly> </td>';
			    print '</tr>';
			
			}
			print '<tr>';
			print '<td></td>';
			print '<td>Total</td>';
			print '<td><input type=text size=4 value="' . number_format ( $book_cost, 2 ) . '" style="text-align:right;color:blue;" readonly></td>';
			print '</tr>';

			print '</table>';
		} else {
			print 'No books as grade is not assigned for this student<BR>';
		}
		
		print "</FIELDSET>";
		print "<BR><BR>";
		print '<input type=hidden id="test1" name="rc_student_id" value="' . $sid . '">';
		print '<input type=hidden id="test1" name="rc_readonly" value="' . $readonly . '">';
		print '<input type="Submit"  name="Recalculate" style="background-color:green; width:120px; height:24px;" value="Recalculate">';
		
		print '</div>';
		
		print '<div style="width:60%; height:80%; float:right;" >';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; ">';
		print "<LEGEND><B>Tution and Book Fee</B></LEGEND>";
		
		print "<table>";
		print "<tr><td><B>Student Name </B></td>" . "<td> " . $this->infoPersonalInfo ['first_name'] . " " . $this->infoPersonalInfo ['middle_name'] . " " . $this->infoPersonalInfo ['last_name'] . " </td></tr>";
		print "<tr><td><B> Student Id  </B></td>" . "<td> " . $sid . "&nbsp&nbsp";
		print "<input type='text' class='stu_status' size=20 style='color:blue;' value='Student-Record-link' readonly";
		print " onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $sid . "' >";
		
		print "</td></tr>";
		print "<tr><td><B> Grade </B></td>" . "<td> " . $this->infoRegistration ['wis_grade'] . "-" . $this->infoRegistration ['section'] . " </td></tr>";
		print "</table>";
		// print "<BR>";
		
		print "<class='normal1'>Date </em>";
		print "<input type=text class='datepicker' name='pay_date' value= '" . $today . "' size=10 maxlength=10 >";
		
		print "<BR>";
		$tfee += $book_cost;
		print "<table>";
		print "<tr><td><B>Charges</td><td>Tution Fee</td><td> <input type=text name='tution_fee' size=6 maxlength=10 value='" . $ainfo ['tution_fee'] . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td></td><td>Books </td><td><input type=text name='book_fee' size=6 maxlength=10 value='" . number_format ( $book_cost, 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td></td><td>Multi-payment plan fee </td><td><input type=text name='multi_pay_plan_fee' size=6 maxlength=10 style='text-align:right;' ";
		if (empty($rc_info)) {
			if (empty($this->infoRegistration ['multi_payplan_fee'])) {
				print "value = '0.0' ";
			} else {
				print "value = '" . number_format ($this->infoRegistration ['multi_payplan_fee']) . "' ";
				$tfee += $this->infoRegistration ['multi_payplan_fee'];
			}
		} else {
			$tmp = 0.0;
			if (!empty($rc_info['multi_pay_plan_fee'])) {
				$tfee += $rc_info['multi_pay_plan_fee'];
				$tmp = $rc_info['multi_pay_plan_fee'];
			}
			print "value = '" . number_format ($tmp,2) . "'"; 
		}
		print " ></td></tr>";
		print "<tr><td></td><td>Miscl. Charges</td><td><input type=text name='miscl_charges' size=6 maxlength=10 style='text-align:right;' ";
		if (empty($rc_info)) {
			if (empty($this->infoRegistration ['miscl_charges'])) {
				print "value = '0.0' ";
			} else {
				print "value = '" . number_format ($this->infoRegistration ['miscl_charges']) . "' ";
				$tfee += $this->infoRegistration ['miscl_charges'];
			}
		} else {
			$tmp = 0.0;
			if (!empty($rc_info['miscl_charges'])) {
				$tfee += $rc_info['miscl_charges'];
				$tmp = $rc_info['miscl_charges'];
			}
			print "value = '" . number_format ($tmp,2) . "'"; 
		}
		print "  ></td></tr>";
		
		print "<tr><td><B>Sub-total </td><td><hr></td><td> ";
		print "<input type=text name='subtotal' size=6 maxlength=10 value='" . number_format ($tfee,2) . "' style='text-align:right;color:blue;' readonly>";
		print " </td></tr>";
		$tfee -= ($mem_disc + $sibling_disc);
		
		print "<tr><td><B>Discounts</td><td>ICSGV Member Discount </td><td><input type=text name='mem_discount' size=6 maxlength=10 value='" . number_format ( $mem_disc, 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td></td><td>Sibling Discount </td><td><input type=text name='num_siblings' size=6 maxlength=10 value='" . number_format ( $sibling_disc, 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td><hr></td><td><hr></td><td><hr></td></tr>";
		print "<tr><td><B>Total Amount </td><td><input type=text name='total_amount' size=10 maxlength=10 value='" . number_format ( $tfee, 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td><B>Payment Made </td><td><input type=text name='payment_made' size=10 maxlength=10 value='" . number_format ( $prev_pay, 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "<tr><td><B>Balance Due </td><td><input type=text  name='balance_due'  size=10 maxlength=10 value='" . number_format ( ($tfee - $prev_pay), 2 ) . "' style='text-align:right;color:blue;' readonly></td></tr>";
		print "</table>";
		
		print "<p><em>Select Payment Method</em><br>";
		print '<input type=radio name="pay_method" value="Cash"' . ' > Cash ';
		print "<input type=radio name='pay_method' value='Check' checked > Check Check number<input type=text name='check_number' size=10 maxlength=10><BR><BR>";
		
		print "<class='normal1'>Amount Paid";
		print "<input type=text name='amount_paid' size=10 maxlength=10 value= '' style='color:blue;' >";
		print "<input type=hidden name='personal_info_id' value='" . $this->personalInfoId . "'>";
		print "<input type=hidden name='school_year' value='" . $this->administrationIf->get_school_year() . "'>";
		print "</FIELDSET>";
		
		print '</div>';
		
		print '</div>';
		
		setSubmitValue ( "enterTutionPlan" );
		
		wis_footer ( TRUE );
	}
	
	/* ^^^^^^^^^^^^^^^^ METHODS called by process_request ^^^^^^^^^^^^^^^^^^^^^^ */
	public function view_student_list_n_modify($name, $id, $search) {
		$split_name = explode ( " ", $name );
		$first_name = $split_name [0];
		$last_name = NULL;
		$rv = TRUE;
		
		if (! empty ( $split_name [1] )) {
			$last_name = $split_name [1];
		}
		
		include_once ("wis_header.php");
		
		$stu_ids = array ();
		
		if ($search == StudentRecord::NAME_ID_SEARCH || $search == StudentRecord::NAME_SEARCH) {
			$info = $this->personalInfoIf->get_QflName_all_ids ( $first_name, $last_name );
			for($i = 0; $i < count ( $info ); $i ++) {
				$stu_id = $this->studentIf->get_id ( $info [$i] ['id_pi'] );
				if (! empty ( $stu_id )) {
					array_push ( $stu_ids, $stu_id );
				}
			}
			
			if (count ( $stu_ids ) == 0) {
				wis_main_menu ( $this->mysqli_h, FALSE );
				
				print "<H4> No record found with that student name <BR><BR><BR></H4>";
				$rv = FALSE;
			}
		} else if ($search == StudentRecord::ID_SEARCH) {
			$this->get_records ( $id );
			
			if (empty ( $this->personalInfoId )) {
				wis_main_menu ( $this->mysqli_h, FALSE );
				
				print "<H4> No record found with that student ID <BR><BR><BR></H4>";
				$rv = FALSE;
			} else {
				array_push ( $stu_ids, $id );
			}
		}
		
		if ($rv) {
			wis_main_menu ( $this->mysqli_h, TRUE );
			print '<div id="printableArea">';
			
			for($i = 0; $i < count ( $stu_ids ); $i ++) {
				
				$this->get_records ( $stu_ids [$i] );
				
				print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
				print '<LEGEND> STUDENT ' . ($i + 1) . ' </LEGEND>';
				
				$this->modify_student_data ();
				
				print "</FIELDSET>";
			}
			
			print '</div>';
		}
		
		wis_footer ( FALSE );
	}

	public function get_all_student_records() {

	    $pers_info = $this->personalInfoIf->get_all_records(); 

	    $students = array ();
	    
	    for ($i=0; $i<count($pers_info); $i++) {
		$stu_id = $this->studentIf->get_id($pers_info[$i]['id_pi']);
		
		if ($stu_id != 0) {
		    array_push($students, $stu_id); 
		}
	    }
	    //print "Total records: " . count($pers_info) . " Active student records " . count($students) . "<BR>";
	    return $students; 
	}
	
/* TODO - Temporary to print all records */	
	public function view_print_all_records1() {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
		
		//$stu_ids = $this->studentIf->get_all_student_ids();
		$stu_ids = $this->get_all_student_records(); 

		for ($i=0; $i<100; $i++) {
		       if ($this->studentIf->isStatus($stu_ids[$i], 'ACTIVE') ) { 
		           //print '<DIV class="page-break">';
				print '<FIELDSET  class="page-break"; style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
				print '<LEGEND style="font-size: 15px"></LEGEND>';
				$this->get_records ( $stu_ids[$i] );
				$this->show_individual_rec (FALSE);
				print "</FIELDSET>";
				//print '</DIV>';
			}
		}
		print '</div>';
		wis_footer ( FALSE );
	}
	
/* TODO - Temporary to print all records */	
	public function view_print_all_records2() {
		include_once ("wis_header.php");
	
		wis_main_menu ( $this->mysqli_h, TRUE );
	
		print '<div id="printableArea">';
	
		$stu_ids = $this->get_all_student_records();
		//$stu_ids = $this->studentIf->get_all_student_ids();
	
		for ($i=100; $i<200; $i++) {
		        if ($this->studentIf->isStatus($stu_ids[$i], 'ACTIVE') ) {
				print '<FIELDSET class="page-break"; style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
				print '<LEGEND style="font-size: 15px"></LEGEND>';
				$this->get_records ( $stu_ids[$i] );
				$this->show_individual_rec (FALSE);
				print "</FIELDSET>";
			}
		}
		print '</div>';
		wis_footer ( FALSE );
	}
		
/* TODO - Temporary to print all records */	
	public function view_print_all_records3() {
		include_once ("wis_header.php");
	
		wis_main_menu ( $this->mysqli_h, TRUE );
	
		print '<div id="printableArea">';
	
		$stu_ids = $this->get_all_student_records();
		//$stu_ids = $this->studentIf->get_all_student_ids();
	
		for ($i=200; $i<300; $i++) {
		        if ($this->studentIf->isStatus($stu_ids[$i], 'ACTIVE') ) {
				print '<FIELDSET  class="page-break"; style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
				print '<LEGEND style="font-size: 15px"></LEGEND>';
				$this->get_records ( $stu_ids[$i] );
				$this->show_individual_rec (FALSE);
				print "</FIELDSET>";
			}
		}
		print '</div>';
		wis_footer ( FALSE );
	}
		
/* TODO - Temporary to print all records */	
	public function view_print_all_records4() {
		include_once ("wis_header.php");
	
		wis_main_menu ( $this->mysqli_h, TRUE );
	
		print '<div id="printableArea">';
	
		$stu_ids = $this->get_all_student_records();
		//$stu_ids = $this->studentIf->get_all_student_ids();
	
		for ($i=300; $i<count($stu_ids); $i++) {
		        if ($this->studentIf->isStatus($stu_ids[$i], 'ACTIVE') ) {
				print '<FIELDSET  class="page-break"; style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
				print '<LEGEND style="font-size: 15px"></LEGEND>';
				$this->get_records ( $stu_ids[$i] );
				$this->show_individual_rec (FALSE);
				print "</FIELDSET>";
			}
		}
		print '</div>';
		wis_footer ( FALSE );
	}
		
	public function enter_tution_plan($pay_type = 'REGISTRATION') {
		if (isset ( $_REQUEST ['amount_paid'] ) && $_REQUEST ['amount_paid'] > 0.0) {
			$trans ['personal_info_id'] = $_REQUEST ['personal_info_id'];
			$trans ['amount'] = $_REQUEST ['amount_paid'];
			$trans ['trans_date'] = convert_normal_date_to_SQL ( $_REQUEST ['pay_date'] );
			$trans ['payment_type'] = $_REQUEST ['pay_method'];
			$trans ['other_description'] = NULL;
			$trans ['check_number'] = NULL;
			
			if ($_REQUEST ['pay_method'] === 'Check') {
				$trans ['check_number'] = $_REQUEST ['check_number'];
			}
			if ($pay_type === 'OTHER') {
				$trans ['other_description'] = $_REQUEST ['other_description'];
			}
			$trans ['paid_for'] = $pay_type;
			$trans ['school_year'] = $_REQUEST ['school_year'];
			
			$this->accountIf->insert_record ( $trans );
		}
		$this->registrationIf->update_miscl_charges_mp_planfee($_REQUEST ['rc_student_id'], $_REQUEST ['miscl_charges'], $_REQUEST ['multi_pay_plan_fee']);
		$this->view_tution_plan_n_setup($_REQUEST ['rc_student_id']);
	}
	
	public function enter_student_registration() {
		$info_s = array ();
		$info_s ['first_name'] = $_REQUEST ['first_name1'];
		$info_s ['middle_name'] = $_REQUEST ['middle_name1'];
		$info_s ['last_name'] = $_REQUEST ['last_name1'];
		$info_s ['cell_area'] = '';
		$info_s ['cell_local'] = '';
		$info_s ['cell_number'] = '';
		$info_s ['email'] = $_REQUEST ['email1'];
		$info_s ['ps_grade'] = $_REQUEST ['ps_grade1'];
		$info_s ['medications'] = $_REQUEST ['medications1'];
		$info_s ['allergies'] = $_REQUEST ['allergies1'];
		$info_s ['num_siblings'] = 0;
		
		$this->enter_student_info ( $info_s );
		
		if (! empty ( $_REQUEST ['first_name2'] )) {
			$info_s ['first_name'] = $_REQUEST ['first_name2'];
			$info_s ['middle_name'] = $_REQUEST ['middle_name2'];
			$info_s ['last_name'] = $_REQUEST ['last_name2'];
			$info_s ['email'] = $_REQUEST ['email2'];
			$info_s ['ps_grade'] = $_REQUEST ['ps_grade2'];
			$info_s ['medications'] = $_REQUEST ['medications2'];
			$info_s ['allergies'] = $_REQUEST ['allergies2'];
			$info_s ['num_siblings'] = 1;
			
			$this->enter_student_info ( $info_s );
			
			if (! empty ( $_REQUEST ['first_name3'] )) {
				$info_s ['first_name'] = $_REQUEST ['first_name3'];
				$info_s ['middle_name'] = $_REQUEST ['middle_name3'];
				$info_s ['last_name'] = $_REQUEST ['last_name3'];
				$info_s ['email'] = $_REQUEST ['email3'];
				$info_s ['ps_grade'] = $_REQUEST ['ps_grade3'];
				$info_s ['medications'] = $_REQUEST ['medications3'];
				$info_s ['allergies'] = $_REQUEST ['allergies3'];
				$info_s ['num_siblings'] = 2;
				
				$this->enter_student_info ( $info_s );
				
				if (! empty ( $_REQUEST ['first_name4'] )) {
					$info_s ['first_name'] = $_REQUEST ['first_name4'];
					$info_s ['middle_name'] = $_REQUEST ['middle_name4'];
					$info_s ['last_name'] = $_REQUEST ['last_name4'];
					$info_s ['email'] = $_REQUEST ['email4'];
					$info_s ['ps_grade'] = $_REQUEST ['ps_grade4'];
					$info_s ['medications'] = $_REQUEST ['medications4'];
					$info_s ['allergies'] = $_REQUEST ['allergies4'];
					$info_s ['num_siblings'] = 3;
					
					$this->enter_student_info ( $info_s );
					
					if (! empty ( $_REQUEST ['first_name5'] )) {
						$info_s ['first_name'] = $_REQUEST ['first_name5'];
						$info_s ['middle_name'] = $_REQUEST ['middle_name5'];
						$info_s ['last_name'] = $_REQUEST ['last_name5'];
						$info_s ['email'] = $_REQUEST ['email5'];
						$info_s ['ps_grade'] = $_REQUEST ['ps_grade5'];
						$info_s ['medications'] = $_REQUEST ['medications5'];
						$info_s ['allergies'] = $_REQUEST ['allergies5'];
						$info_s ['num_siblings'] = 4;
						
						$this->enter_student_info ( $info_s );
						
						if (! empty ( $_REQUEST ['first_name6'] )) {
							$info_s ['first_name'] = $_REQUEST ['first_name6'];
							$info_s ['middle_name'] = $_REQUEST ['middle_name6'];
							$info_s ['last_name'] = $_REQUEST ['last_name6'];
							$info_s ['email'] = $_REQUEST ['email6'];
							$info_s ['ps_grade'] = $_REQUEST ['ps_grade6'];
							$info_s ['medications'] = $_REQUEST ['medications6'];
							$info_s ['allergies'] = $_REQUEST ['allergies6'];
							$info_s ['num_siblings'] = 5;
							
							$this->enter_student_info ( $info_s );
						}
					}
				}
			}
		}
		
		// New registration enter into DB, ask user to print
		$this->regForm->student_registration ( RegistrationForm::COMPLETE, $_REQUEST );
	}
	
	public function enter_registration_info($student_id, $app_date, $info_s) {
		$reg_info ['student_id'] = $student_id;
		$reg_info ['register_date'] = $app_date;
		$reg_info ['reg_school_grade'] = $info_s ['ps_grade'];
		$reg_info ['allergies'] = $info_s ['allergies'];
		$reg_info ['medications'] = $info_s ['medications'];
		$reg_info ['num_siblings'] = $info_s ['num_siblings'];
		
		$reg_info ['school_year'] = $this->administrationIf->get_school_year ();
		
		if (! empty ( $_REQUEST ['parent_volun_date1'] )) {
			$reg_info ['parent_volun_1'] = convert_normal_date_to_SQL ( $_REQUEST ['parent_volun_date1'] );
		} else {
			$reg_info ['parent_volun_1'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['parent_volun_date2'] )) {
			$reg_info ['parent_volun_2'] = convert_normal_date_to_SQL ( $_REQUEST ['parent_volun_date2'] );
		} else {
			$reg_info ['parent_volun_2'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['auth_person1'] )) {
			$reg_info ['auth_person1'] = $_REQUEST ['auth_person1'];
		} else {
			$reg_info ['auth_person1'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['address_ap1'] )) {
			$reg_info ['address_ap1'] = $_REQUEST ['address_ap1'];
		} else {
			$reg_info ['address_ap1'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['driver_lic_ap1'] )) {
			$reg_info ['driver_lic_ap1'] = $_REQUEST ['driver_lic_ap1'];
		} else {
			$reg_info ['driver_lic_ap1'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['phone_ap1'] )) {
			$reg_info ['phone_ap1'] = $_REQUEST ['phone_ap1'];
		} else {
			$reg_info ['phone_ap1'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['auth_person2'] )) {
			$reg_info ['auth_person2'] = $_REQUEST ['auth_person2'];
		} else {
			$reg_info ['auth_person2'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['address_ap2'] )) {
			$reg_info ['address_ap2'] = $_REQUEST ['address_ap2'];
		} else {
			$reg_info ['address_ap2'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['driver_lic_ap2'] )) {
			$reg_info ['driver_lic_ap2'] = $_REQUEST ['driver_lic_ap2'];
		} else {
			$reg_info ['driver_lic_ap2'] = NULL;
		}
		
		if (! empty ( $_REQUEST ['phone_ap2'] )) {
			$reg_info ['phone_ap2'] = $_REQUEST ['phone_ap2'];
		} else {
			$reg_info ['phone_ap2'] = NULL;
		}
		
		$reg_info ['reg_status'] = 'PENDING';
		if (! empty ( $_REQUEST ['icsgv_mem'] )) {
			$reg_info ['icsgv_mem'] = '1';
		} else {
			$reg_info ['icsgv_mem'] = '0';
		}
		$reg_info ['payment_plan'] = '1';
		
		$this->registrationIf->insert_record ( $reg_info );
		wis_log_event ( " added new PEDNING student with id: " . $student_id );
	}
	
	public function enter_student_info($info_s) {
		$today = today_date_SQL_format ();
		
		list ( $month, $day, $year ) = split ( '[/.-]', $_REQUEST ['registration_date'] );
		$app_date = $year . '-' . $month . '-' . $day;
		
		$pers_info ['last_name'] = $info_s ['last_name'];
		$pers_info ['first_name'] = $info_s ['first_name'];
		/*
		 * if ($_REQUEST['gender'] == '1') {
		 * $pers_info['gender'] = 'MALE';
		 * } else {
		 * $pers_info['gender'] = 'FEMALE';
		 * }
		 */
		// FIX: Experiment with NULL
		$pers_info ['middle_name'] = '';
		$pers_info ['cell_phone'] = '';
		$pers_info ['home_phone'] = '';
		$pers_info ['state'] = 'California';
		
		if (! empty ( $_REQUEST ['middle_name'] )) {
			$pers_info ['middle_name'] = $info_s ['last_name'];
		}
		
		if (! empty ( $_REQUEST ['cell_area'] )) {
			$pers_info ['cell_phone'] = $info_s ['cell_area'] . '-' . $info_s ['cell_local'] . '-' . $info_s ['cell_number'];
		}
		
		if (! empty ( $_REQUEST ['home_area'] )) {
			$pers_info ['home_phone'] = $_REQUEST ['home_area'] . '-' . $_REQUEST ['home_local'] . '-' . $_REQUEST ['home_number'];
		}
		
		if (! empty ( $_REQUEST ['state'] )) {
			$pers_info ['state'] = $_REQUEST ['state'];
		}
		
		$pers_info ['address'] = $_REQUEST ['address'];
		$pers_info ['city'] = $_REQUEST ['city'];
		$pers_info ['zipcode'] = $_REQUEST ['zipcode'];
		$pers_info ['email'] = $info_s ['email'];
		
		$personal_info_id = $this->personalInfoIf->insert_record ( $pers_info );
		if ($personal_info_id == - 1) {
			$this->regForm->student_registration ( RegistrationForm::DUPLICATE, $_REQUEST );
			return;
		}
		
		$parent_id = $this->enter_parent_info ();
		
		$student_info ['personal_info_id'] = $personal_info_id;
		$student_info ['parent_id'] = $parent_id;
		$student_info ['teacher_id'] = '';
		$student_info ['status'] = 'ACTIVE';
		
		$student_id = $this->studentIf->insert_record ( $student_info );
		
		list ( $year, $nyear ) = $this->administrationIf->get_school_year ();
		
		$this->enter_registration_info ( $student_id, $app_date, $info_s );
	}
	
	public function enter_parent_info() {
		$parent_info ['mother_middle_name'] = '';
		$parent_info ['father_middle_name'] = '';
		$parent_info ['mother_cell_phone'] = '';
		$parent_info ['father_cell_phone'] = '';
		
		$parent_info ['mother_last_name'] = $_REQUEST ['mother_last_name'];
		$parent_info ['mother_first_name'] = $_REQUEST ['mother_first_name'];
		$parent_info ['father_last_name'] = $_REQUEST ['father_last_name'];
		$parent_info ['father_first_name'] = $_REQUEST ['father_first_name'];
		
		if (! empty ( $_REQUEST ['mother_middle_name'] )) {
			$parent_info ['mother_middle_name'] = $_REQUEST ['mother_middle_name'];
		}
		
		if (! empty ( $_REQUEST ['father_middle_name'] )) {
			$parent_info ['father_middle_name'] = $_REQUEST ['father_middle_name'];
		}
		
		$parent_info ['par_email'] = $_REQUEST ['par_email'];
		
		if (! empty ( $_REQUEST ['mother_cell_area'] )) {
			$parent_info ['mother_cell_phone'] = $_REQUEST ['mother_cell_area'] . '-' . $_REQUEST ['mother_cell_local'] . '-' . $_REQUEST ['mother_cell_number'];
		}
		if (! empty ( $_REQUEST ['father_cell_area'] )) {
			$parent_info ['father_cell_phone'] = $_REQUEST ['father_cell_area'] . '-' . $_REQUEST ['father_cell_local'] . '-' . $_REQUEST ['father_cell_number'];
		}
		
		$info_p = $this->parentIf->find_parent ();
		if (empty ( $info_p )) {
			$parent_id = $this->parentIf->insert_record ( $parent_info );
		} 

		else {
			$parent_id = $info_p ['id_parent'];
		}
		
		return $parent_id;
	}
	
	function selectStudentView() {
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		print '<H4 style="text-align: center";>Search Options for the Students</H4>';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND>Student List Selection</LEGEND>";
		
		print "<br><br>";
		print "<B>Check the desired options<br></B>";
		print '<input type="checkbox" name="address_list"    > Include Addresses';
		print "<br>";
		print '<input type="checkbox" name="email_list"      > Include Emails';
		print "<br>";
		print '<input type="checkbox" name="parent_info"     > Include Parent Info';
		print "<br>";
		print '<input type="checkbox" name="mem_type_list"   > Include Membership type';
		print "<br>";
		print '<input type="checkbox" name="mem_status_list" > Include Membership status';
		print "<br>";
		print '<input type="checkbox" name="mem_id_list"     > Include Student ID';
		print "<br>";
		print '<input type="checkbox" name="appl_date_list"  > Include Membership Date';
		print "<br>";
		print '<input type="checkbox" name="mem_sponsor_list" > Include Member Sponsors';
		print "<br>";
		
		print "<br>";
		
		print "</FIELDSET>";
		
		print '<input type=hidden name="back_search_page" value="unset">';
		
		setSubmitValue ( "select_member_view" );
		
		wis_footer ( TRUE );
	}
	
	public function update_student_record() {
		$cphone = $_REQUEST ['cell_area'] . '-' . $_REQUEST ['cell_local'] . '-' . $_REQUEST ['cell_number'];
		$hphone = $_REQUEST ['home_area'] . '-' . $_REQUEST ['home_local'] . '-' . $_REQUEST ['home_number'];
		
		$pers_rec ['first_name'] = $_REQUEST ['first_name'];
		$pers_rec ['last_name'] = $_REQUEST ['last_name'];
		$pers_rec ['middle_name'] = $_REQUEST ['middle_name'];
		$pers_rec ['cell_phone'] = $cphone;
		$pers_rec ['home_phone'] = $hphone;
		$pers_rec ['email'] = $_REQUEST ['email'];
		// $pers_rec['icsgv_member'] = $_REQUEST[''];
		$pers_rec ['address'] = $_REQUEST ['address'];
		$pers_rec ['city'] = $_REQUEST ['city'];
		$pers_rec ['zipcode'] = $_REQUEST ['zipcode'];
		$pers_rec ['personal_info.id'] = $_REQUEST ['personal_info_id'];
		
		$this->personalInfoIf->update_record ( $pers_rec, $_REQUEST ['personal_info_id'] );
		
		$change_sib_add = $this->studentIf->new_change( $this->personalInfoId, $hphone, $_REQUEST ['address'], $_REQUEST ['city'], $_REQUEST ['zipcode'] );
		$parent_id = $this->studentIf->get_parent_id ( $this->studentIf->get_id ( $_REQUEST ['personal_info_id'] ) );
			
		$stat = 'ACTIVE';
		$recalculate_num_siblings = false;
		if (! empty ( $_REQUEST ['student_status'] )) {
			if ($_REQUEST ['student_status'] === 'inactive') {
				$stat = 'INACTIVE';
			} else if ($_REQUEST ['student_status'] === 'active') {
				$stat = 'ACTIVE';
			} else if ($_REQUEST ['student_status'] === 'graduated') {
				$stat = 'GRADUATED';
			}
			$prev_status = $this->studentIf->get_status ($_REQUEST ['student_id'] );
			if ($stat != $prev_status) {
				$recalculate_num_siblings = true;
			}
			$this->studentIf->update_status ( $stat, $_REQUEST ['student_id'] );
		}
		
		// $siblings = array();
		$mem_disc = false;
		$sibling_ids = $this->studentIf->get_children_ids ( $parent_id );
		if ($recalculate_num_siblings && ($stat === 'ACTIVE' || $stat = 'GRADUATED')) {
			$sib_cnt=0;
		} else {
			$sib_cnt=1;
		}
		if (count ( $sibling_ids ) > 1) {
			for ($i = 0; $i < count ( $sibling_ids ); $i ++) {
				if ($sibling_ids [$i] != $this->studentIf->get_id ( $_REQUEST ['personal_info_id'] )) {
					if ($this->registrationIf->isIcsgvMember($sibling_ids[$i])) {
						$mem_disc = true;
					}
					if ($change_sib_add) {
						$personal_id = $this->studentIf->get_personal_info_id ( $sibling_ids [$i] );
						$this->personalInfoIf->update_address_phone ( $personal_id, $hphone, $_REQUEST ['address'], $_REQUEST ['city'], $_REQUEST ['zipcode'] );
						if ($recalculate_num_siblings) {
							$this->registrationIf->set_num_siblings($sibling_ids [$i],$sib_cnt++);
						}
					}
				} else {
					if ($recalculate_num_siblings) {
						$this->registrationIf->set_num_siblings($sibling_ids [$i],0);
					}
				}
			}
		}
		
		$reg_stat = 'PENDING';
		if (! empty ( $_REQUEST ['reg_status'] )) {
			if ($_REQUEST ['reg_status'] === 'pending') {
				$reg_stat = 'PENDING';
			} else if ($_REQUEST ['reg_status'] === 'approved') {
				$reg_stat = 'APPROVED';
				$admission_date = $this->studentIf->get_admission_date ( $_REQUEST ['student_id'] );
				$approval_date = convert_normal_date_to_SQL ( $_REQUEST ['approval_date'] );
				if (empty ( $admission_date )) {
					$this->studentIf->update_admission_date ( $approval_date, $_REQUEST ['student_id'] );
				}
			} else if ($_REQUEST ['reg_status'] === 'denied') {
				$reg_stat = 'DENIED';
			}
		}
		
		if ( !empty($_REQUEST ['icsgv_mem']) && !$mem_disc) {
			$reg_rec ['icsgv_mem'] = 1;
		} else {
			$reg_rec ['icsgv_mem'] = 0;
		}
		
		if (! empty ( $_REQUEST ['student_grade'] )) {
			if ($_REQUEST ['student_grade'] === 'na') {
				$reg_rec ['wis_grade'] = NULL;
			} else {
				$reg_rec ['wis_grade'] = $_REQUEST ['student_grade'];
			}
		}
		if (! empty ( $_REQUEST ['student_section'] )) {
			if ($_REQUEST ['student_section'] === 'na') {
				$reg_rec ['section'] = NULL;
			} else {
				$reg_rec ['section'] = $_REQUEST ['student_section'];
			}
		}
		if (! empty ( $_REQUEST ['approved_by'] )) {
			$reg_rec ['approved_by'] = $_REQUEST ['approved_by'];
		}
		if (! empty ( $_REQUEST ['approval_date'] )) {
			$reg_rec ['approval_date'] = convert_normal_date_to_SQL ( $_REQUEST ['approval_date'] );
		}
		
		if (! empty ( $_REQUEST ['num_siblings'] )) {
			$reg_rec ['num_siblings'] = $_REQUEST ['num_siblings'];
		}
		if (! empty ( $_REQUEST ['parent_volun_date1'] )) {
			$reg_rec ['parent_volun_1'] = convert_normal_date_to_SQL ( $_REQUEST ['parent_volun_date1'] );
		}
		if (! empty ( $_REQUEST ['parent_volun_date2'] )) {
			$reg_rec ['parent_volun_2'] = convert_normal_date_to_SQL ( $_REQUEST ['parent_volun_date2'] );
		}
		if (! empty ( $_REQUEST ['allergies'] )) {
			$reg_rec ['allergies'] = $_REQUEST ['allergies'];
		}
		if (! empty ( $reg_stat )) {
			$reg_rec ['reg_status'] = $reg_stat;
		}
		if (! empty ( $_REQUEST ['medications'] )) {
			$reg_rec ['medications'] = $_REQUEST ['medications'];
		}
		if (! empty ( $_REQUEST ['registration_date'] )) {
			$reg_rec ['register_date'] = convert_normal_date_to_SQL ( $_REQUEST ['registration_date'] ) . "',";
		}
		if (! empty ( $_REQUEST ['auth_person1'] )) {
			$reg_rec ['auth_person1'] = $_REQUEST ['auth_person1'];
		}
		if (! empty ( $_REQUEST ['address_ap1'] )) {
			$reg_rec ['address_ap1'] = $_REQUEST ['address_ap1'];
		}
		if (! empty ( $_REQUEST ['phone_ap1'] )) {
			$reg_rec ['phone_ap1'] = $_REQUEST ['phone_ap1'];
		}
		if (! empty ( $_REQUEST ['driver_lic_ap1'] )) {
			$reg_rec ['driver_lic_ap1'] = $_REQUEST ['driver_lic_ap1'];
		}
		if (! empty ( $_REQUEST ['auth_person2'] )) {
			$reg_rec ['auth_person2'] = $_REQUEST ['auth_person2'];
		}
		if (! empty ( $_REQUEST ['address_ap2'] )) {
			$reg_rec ['address_ap2'] = $_REQUEST ['address_ap2'];
		}
		if (! empty ( $_REQUEST ['phone_ap2'] )) {
			$reg_rec ['phone_ap2'] = $_REQUEST ['phone_ap2'];
		}
		if (! empty ( $_REQUEST ['driver_lic_ap2'] )) {
			$reg_rec ['driver_lic_ap2'] = $_REQUEST ['driver_lic_ap2'];
		}
		if (! empty ( $_REQUEST ['reg_school_grade'] )) {
			$reg_rec ['reg_school_grade'] = $_REQUEST ['reg_school_grade'];
		}
		
		$this->registrationIf->update_record ( $reg_rec, $_REQUEST ['student_id'] );
		
		$parent_rec ['mother_cell_phone'] = $_REQUEST ['mother_cell_area'] . '-' . $_REQUEST ['mother_cell_local'] . '-' . $_REQUEST ['mother_cell_number'];
		$parent_rec ['father_cell_phone'] = $_REQUEST ['father_cell_area'] . '-' . $_REQUEST ['father_cell_local'] . '-' . $_REQUEST ['father_cell_number'];
		
		$parent_rec ['mother_last_name'] = $_REQUEST ['mother_last_name'];
		$parent_rec ['mother_first_name'] = $_REQUEST ['mother_first_name'];
		$parent_rec ['mother_middle_name'] = $_REQUEST ['mother_middle_name'];
		$parent_rec ['father_last_name'] = $_REQUEST ['father_last_name'];
		$parent_rec ['father_first_name'] = $_REQUEST ['father_first_name'];
		$parent_rec ['father_middle_name'] = $_REQUEST ['father_middle_name'];
		$parent_rec ['par_email'] = $_REQUEST ['par_email'];
		
		$this->parentIf->update_record ( $parent_rec, $_REQUEST ['parent_id'] );
		
		$grade_set = false;
		if (! empty ( $_REQUEST ['student_grade'] ) && ! empty ( $_REQUEST ['student_section'] )) {
			$teacher_id = $this->teacherIf->get_Qgrade_teacher_id ( $_REQUEST ['student_grade'], $_REQUEST ['student_section'] );
			$this->studentIf->update_teacher_id ( $teacher_id, $_REQUEST ['student_id'] );
		}
		
		wis_log_event ( " updated STUDENT_PROFILE id: " . $_REQUEST ['student_id'] );
		
		$this->view_student_record_n_modify ( $_REQUEST ['student_id'] );
	}
	
	public function approve_student_registrations() {
		for($i = 0; $i < $_REQUEST ['num_students']; $i ++) {
			$grade = $_REQUEST ['student_grade_' . $i];
			$section = ($_REQUEST ['student_section_' . $i] === "na") ? NULL : $_REQUEST ['student_section_' . $i];
			if ($grade === "na") {
				$this->registrationIf->update_grade_section ( $_REQUEST ['student_id_' . $i], NULL, $section );
			} else {
				$this->registrationIf->update_approve_grade_section ( $_REQUEST ['student_id_' . $i], $grade, $section );
			}
		}
		$this->view_student_list_n_assign_grade ();
	}
	
	public function close_school_year() {
		$school_year = $this->administrationIf->get_school_year();
		$stu_ids = $this->studentIf->get_all_active_student_ids();
		$this->bookIf->yearEndCleanBooks();
		for ($i=0; $i<count($stu_ids); $i++) {
			$wis_grade = trim($this->registrationIf->get_wis_grade($stu_ids[$i])); 
			if (!empty($wis_grade)) {
				$wis_grade = $this->update_wis_grade($wis_grade);
			}
			$this->registrationIf->new_school_year($stu_ids[$i],$wis_grade,$school_year);
		}

		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		print 'Following YEAR close operations were performed<BR>';
		print '---- All student grades are bumped-up by one grade<BR>';
		print '---- Grade books are cleaned<BR>';
		print '---- Set the new school year<BR>';
		print '---- Parent Volunteer dates set to null<BR>';
		print '---- Student Approver and Approval date set to null<BR>';
		print '---- All student registration status set to PENDING<BR>';
		print '---- Miscllaneous charges and payment plan fee set to 0<BR>';
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( FALSE );
	}
	
	/* ---------- PRIVATE FUNCTIONS ---------------- */
	private function get_parent_data() {
		return $this->parentIf->get_record ( $parent_id );
	}
	
	private function get_registration_data() {
		return $this->registrationIf->get_record ( $student_id );
	}
	
	private function get_student_data() {
		return $this->studentIf->get_record ( $personal_info_id );
	}
	
	private function update_wis_grade($wis_grade) {
		
		switch ($wis_grade) {
			case "Basic": 
				$rv = "PRE_K";
				break;
			case "PRE_K":
				$rv = "KG";
				break;		
			case "KG":
				$rv = "1";
				break;
			case "1":
				$rv = "2";
				break;
			case "2":
				$rv = "3";
				break;
			case "3":
				$rv = "4";
				break;
			case "4":
				$rv = "5";
				break;
			case "5":
				$rv = "6";
				break;
			case "6":
				$rv = "7";
				break;
			case "7":
				$rv = "8";
	  			break;
			case "8":
				$rv = "YG";
				break;
			case "YG":
				$rv = "YG";
				break;
			case "NULL":
				$rv = "NULL";
				break;
			default:
				die ("Unrecognised Grade " . $wis_grade);
		}
		return $rv;
	}
	
	private function show_student_info() {
		if (! empty ( $this->infoRegistration ['register_date'] )) {
			list ( $year, $month, $day ) = split ( '[-]', $this->infoRegistration ['register_date'] );
			$reg_date = $month . '/' . $day . '/' . $year;
		}
		if (! empty ( $this->infoPersonalInfo ['cell_phone'] )) {
			list ( $cell_area, $cell_local, $cell_number ) = split ( '[-]', $this->infoPersonalInfo ['cell_phone'] );
		}
		if (! empty ( $this->infoPersonalInfo ['home_phone'] )) {
			list ( $home_area, $home_local, $home_number ) = split ( '[-]', $this->infoPersonalInfo ['home_phone'] );
		}
		
		print "<table>";
		print "<tr>";
		print "<td>Student Id </em></td>";
		print "<td style='color:blue; background-color:white;'>" . $this->studentId . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Student Name (First, Middle, Last) </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoPersonalInfo ['first_name'] . " " . $this->infoPersonalInfo ['middle_name'] . " " . $this->infoPersonalInfo ['last_name'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Cell Phone </td>";
		
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $cell_area )) {
			print "(" . $cell_area . ") " . $cell_local . "-" . $cell_number;
		}
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Home Phone</td>";
		
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $cell_area )) {
			print "(" . $home_area . ") " . $home_local . "-" . $home_number;
		}
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Email </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoPersonalInfo ['email'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Address </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoPersonalInfo ['address'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>City, Sate zipcode </td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoPersonalInfo ['city'] )) {
			print $this->infoPersonalInfo ['city'] . ", ";
		}
		print " CA " . $this->infoPersonalInfo ['zipcode'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td>Registration Date (mm/dd/yyyy) </em></td>";
		print "<td style='color:blue; background-color:white;'>" . convert_sql_date_to_normal($this->infoRegistration['register_date']) . "</td>";
		print "</tr>";
		print "</table>";
	}
	
	private function show_parent_info($registration=TRUE) {
		list ( $father_cell_area, $father_cell_local, $father_cell_number ) = split ( '[-]', $this->infoParent ['father_cell_phone'] );
		list ( $mother_cell_area, $mother_cell_local, $mother_cell_number ) = split ( '[-]', $this->infoParent ['mother_cell_phone'] );
		
		print "<table>";
		print "<tr>";
		print "<td class='normal1'>Mother's Name (First, Middle, Last) </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoParent ['mother_first_name'] . " " . $this->infoParent ['mother_middle_name'] . " " . $this->infoParent ['mother_last_name'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Mother's Cell</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $mother_cell_area )) {
			print "(" . $mother_cell_area . ") " . $mother_cell_local . "-" . $mother_cell_number;
		}
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Father's Name (First, Middle, Last) </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoParent ['father_first_name'] . " " . $this->infoParent ['father_middle_name'] . " " . $this->infoParent ['father_last_name'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Father's Cell</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $father_cell_area )) {
			print "(" . $father_cell_area . ") " . $father_cell_local . "-" . $father_cell_number;
		}
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Email </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoParent ['par_email'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Parent volunteer (Sunday) Date - 1 </td>";
		print "<td style='color:blue; background-color:white;'>";
		if ($registration) {
		    print convert_sql_date_to_normal ( $this->infoRegistration ['parent_volun_1'] );
		}
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Parent volunteer (Sunday) Date - 2 </em>";
		print "<td style='color:blue; background-color:white;'>";
		if ($registration) {
		    print convert_sql_date_to_normal ( $this->infoRegistration ['parent_volun_2'] );
		}
		print "</td>";
		print "</tr>";
		
		print "</table>";
	}
	
	private function show_payment_info() {
		$book_cost = 0;
		$prev_pay = 0;
		
		$accountInfo = $this->accountIf->get_record ( $this->personalInfoId, $this->administrationIf->get_school_year() );
		
		$ainfo = $this->get_tution_discounts ();
		$tfee = $ainfo ['tution_fee'];
		$tfee -= ($this->infoRegistration ['icsgv_mem'] * $ainfo ['icsgv_mem_discount']);
		$tfee -= ($this->infoRegistration ['num_siblings'] * $ainfo ['sibling_discount']);
		
		print "<div style='height: auto; width: 25%; float: left; margin-right: 1%; '>";
		
		print 'Tution Fee: <label style="color:blue; background-color:white; text-align:right;" >$&nbsp' . number_format ( $ainfo ['tution_fee'], 2 ) . "</label><BR>";

		//print '</table>';

		if (! empty ( $this->infoRegistration ['wis_grade'] )) {
				
			$book_info = $this->gradeBookIf->get_Qgrade_book_list ( $this->infoRegistration ['wis_grade'] );
			print '<table>';
			for($i = 0; $i < count ( $book_info ); $i ++) {
			    if ($this->bookIf->isBookNeeded($this->studentId, $book_info[$i]['id_gb'])) {
				print '<tr>';
				print "<td></td>";
				//print '<td><input type=checkbox name="' . $this->infoRegistration ['wis_grade'] . '" checked ></td>';
				print '<td>' . $book_info [$i] ['book_name'] . '</td>';
				print '<td style="color:blue; background-color:white; text-align:right;" >$&nbsp' . number_format ($book_info [$i]['cost'], 2) .  '</td>';
				print '</tr>';
				$book_cost += $book_info [$i] ['cost'];
			    }
			}
			if ($book_cost > 0.0) {
				print '<tr>';
				print '<td></td>';
				print '<td>Total Books</td>';
				print '<td style="color:blue; background-color:white; text-align:right;" >$&nbsp' . number_format ( $book_cost, 2 ) . '</td>';
				print '</tr>';
			}
			print '</table>';
		} else {
			print 'No books as grade is not assigned for this student<BR>';
		}
		/*
		print "<tr>";
		print "<td class='normal1'>Multi-payment plan fee</td>";
		print "<td style='color:blue; background-color:white; text-align:right;'>$&nbsp";
		if ($this->infoRegistration ['payment_plan'] == 1) {
			print number_format ( 0, 2 ) . "</td>";
		} else {
			print number_format ( $ainfo ['payment_plan_fee'], 2 ) . "</td>";
			$tfee += $ainfo ['payment_plan_fee'];
		}
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Miscl. Charges</td>";
		print "<td style='color:blue; background-color:white; text-align:right;'>$&nbsp" . number_format ( 50, 2 ) . "</td>";
		print "</tr>";
		*/
		print "</div>";
		
		print "<div style='height: auto; width: 25%; margin-right: 1%; display: inline;'>";
		
		//$info = $this->accountIf->get_account_info ( $this->administrationIf->get_school_year(), $this->personalInfoId );
		$info = $this->accountIf->get_account_info ( 'ALL', $this->personalInfoId );
		
		print "<table>";

		print "<tr>";
		print "<th class='normal1'>Date </th>";
		print "<th class='normal1'>Amount </th>";
		print "<th class='normal1'>Type </th>";
		print "<th class='normal1'>Check Number </th>";
		print "<th class='normal1'>Paid For</th>";
		print "</tr>";
		
		$tpaid = 0;
		for($i = 0; $i < count ( $info ); $i ++) {
			
			print "<tr>";
			print "<td style='color:blue; background-color:white; width:80px;'>" . convert_sql_date_to_normal ( $info[$i]['trans_date'] ) . "</td>";
			print "<td style='color:blue; background-color:white; width:80px;'>$&nbsp" . number_format ( $info[$i]['amount'], 2 ) . "</td>";
			$tpaid += $info[$i]['amount'];
			print "<td style='color:blue; background-color:white; width:80px;'>" . $info[$i]['payment_type'] . "</td>";
			print "<td style='color:blue; background-color:white;'>" . $info[$i]['check_number'] . "</td>";
			if ($info[$i]['paid_for'] === 'OTHER') {
				print "<td style='color:blue; background-color:white;'>" . $info[$i]['other_discription'] . "</td>";
			} else {
				print "<td style='color:blue; background-color:white;'>" . $info[$i]['paid_for'] . "</td>";
			}
			print "</tr>";
		}
		print "</table>";
		print "<table>";
		print "<tr>";
		print "<td>ICSGV Member Discount </td>";
		print "<td style='color:blue; background-color:white; text-align:right;'>$&nbsp" . number_format ( $this->infoRegistration ['icsgv_mem'] * $ainfo ['icsgv_mem_discount'], 2 ) . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td>Sibling Discount </td>";
		print "<td style='color:blue; background-color:white; text-align:right;'>$&nbsp" . number_format ( $this->infoRegistration ['num_siblings'] * $ainfo ['sibling_discount'], 2 ) . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td>Total Amount </em>";
		$tfee += $book_cost;
		print "<td style='color:blue; background-color:white; text-align:right;'>$&nbsp" . number_format ( $tfee, 2 ) . "</td>";
		print "</tr>";
		
		print "</table>";
		//"<BR>";
		//print "Balance Due <label style='color:blue; background-color:white;'>$&nbsp" . number_format ( ($tfee - $tpaid), 2 ) . "<BR>";
		
		print "</div>";
	}

	private function show_parent_waiver() {

		print '<label  style="font-size:14px">Your signature below will give permission for your child to participate in all school activities within the premises of Islamic Center of San Gabriel Valley (ICSGV) or outside activities held in conjunction with Weekend Islamic School. It will also waive all the claims against ICSGV for injury, accident, illness, or death during any school activities. </label>';
		print '<BR>';
		// print '<BR>';
		print '<label style="font-size:14px"><I><B>Signature of parent, guardian or student 18 years of age or older</I></B></label>';
		
		print '<input type="text" size=28 style="color:blue;border-color:black;" readonly="readonly">';
		
	}
	private function show_medical_consent($readonly = TRUE) {
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		
		print "<table>";
		print "<tr>";
		print "<td class='normal1'>Allergies </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoRegistration ['allergies'] . "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Medications </td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoRegistration ['medications'] . "</td>";
		print "</tr>";
		print "</table>";

		print "<label style='font-size:14px'> Your signature below will grant consent to ICSGV to provide all Emergency dental or medical care prescribed by a duly licensed physician (MD), Osteopathy (DO), or Dentist (DDS) for the above named student. This care may be given under whatever condition necessary to preserve life, limb or well being of the student. </label>";
		print '<BR>';
		print '<BR>';
		print '<label style="font-size:14px"><I><B>Signature of parent, guardian or student 18 years of age or older </B></I></label>';
		print '<input type="text" size=28 style="color:blue;border-color:black;" readonly="readonly">';
	}
	
	private function show_pickup_auth($readonly) {
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		
		print "<table>";
		print "<tr>";
		print "<td colspan='3'> Authorized person name and other info to Pickup  in case of emergency</td>";
		print "</tr>";
		print "<tr>";
		print "<td></td><td>Person 1</td><td>Person 2</td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Name</span></td> ";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['auth_person1'] )) {
			print $this->infoRegistration ['auth_person1'];
		}
		print "</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['auth_person2'] )) {
			print $this->infoRegistration ['auth_person2'];
		}
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Address</span></td> ";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['address_ap1'] )) {
			print $this->infoRegistration ['address_ap1'];
		}
		print "</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['address_ap2'] )) {
			print $this->infoRegistration ['address_ap2'];
		}
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Phone</span></td> ";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['phone_ap1'] )) {
			print $this->infoRegistration ['phone_ap1'];
		}
		print "</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['phone_ap2'] )) {
			print $this->infoRegistration ['phone_ap2'];
		}
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Driver Lic.</span></td> ";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['driver_lic_ap1'] )) {
			print $this->infoRegistration ['driver_lic_ap1'];
		}
		print "</td>";
		print "<td style='color:blue; background-color:white;'>";
		if (! empty ( $this->infoRegistration ['driver_lic_ap2'] )) {
			print $this->infoRegistration ['driver_lic_ap2'];
		}
		print "</td>";
		print "</tr>";
		
		print "</table>";
	}
	
	private function show_student_approval($registration = TRUE) {
		if (! empty ( $this->infoRegistration ['approval_date'] )) {
			$app_date = convert_sql_date_to_normal ( $this->infoRegistration ['approval_date'] );
		} else {
			$app_date = '';
		}
		
		print "<table>";
		print "<tr>";
		print "<td>Grade-Section </em></td>";
		print "<td style='color:blue; background-color:white;'>" . $this->infoRegistration ['wis_grade'] . "-" . $this->infoRegistration ['section'] . "</td>";
		print "</tr>";
		print "<tr>";
		print "<td>Approval Date </em></td>";
		print "<td style='color:blue; background-color:white;'>";
		if ($registration) {
		    print $app_date;
		}
		print "</td>";
		print "</tr>";
		print "<tr>";
		print "<td>Approved By </em></td>";
		print "<td style='color:blue; background-color:white;'>";
		if ($registration) {
		    print $this->infoRegistration ['approved_by'];
		}
		print "</td>";
		print "</tr>";
		
		print "</table>";
	}
	
	private function show_individual_rec($registration=TRUE) {
 	        $num=1;
		print "<input type=hidden name='student_id' value='" . $this->studentId . "'>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Student Information</B></LEGEND>";
		
		$this->show_student_info ();
		
		print "</FIELDSET>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Approve Student Registration</B></LEGEND>";
		
		$this->show_student_approval ($registration);
		
		print "</FIELDSET>";
		
		print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Parent Information</B></LEGEND>";
		
		$this->show_parent_info ($registration);
		print "</FIELDSET>";

		if ($registration) {
		    print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		    print "<LEGEND><B>" . $num++ . ". Payment Information</B></LEGEND>";
		
		    $this->show_payment_info ();
		    print "</FIELDSET>";
		}
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Parent Permission and Waiver</B></LEGEND>";
		$this->show_parent_waiver();
		print "</FIELDSET>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Parent Consent for Medical Treatment</B></LEGEND>";
		
		$this->show_medical_consent ( true );
		
		print "</FIELDSET>";
		
		print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>" . $num++ . ". Names of persons authorized to pickup child(ren) in Emergency</B></LEGEND>";
		
		$this->show_pickup_auth ( true );
		
		print "</FIELDSET>";

		// setSubmitValue("updateStudentRecord");
	}
	
	private function modify_student_pers_info($readonly = TRUE) {
		$cell_area = '';
		$cell_local = '';
		$cell_number = '';
		$home_area = '';
		$home_local = '';
		$home_number = '';
		$reg_date = '';
		
		if (! empty ( $this->infoRegistration ['register_date'] )) {
			list ( $year, $month, $day ) = split ( '[-]', $this->infoRegistration ['register_date'] );
			$reg_date = $month . '/' . $day . '/' . $year;
		}
		
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		if (! empty ( $this->infoPersonalInfo ['cell_phone'] )) {
			list ( $cell_area, $cell_local, $cell_number ) = split ( '[-]', $this->infoPersonalInfo ['cell_phone'] );
		}
		if (! empty ( $this->infoPersonalInfo ['home_phone'] )) {
			list ( $home_area, $home_local, $home_number ) = split ( '[-]', $this->infoPersonalInfo ['home_phone'] );
		}
		
		print "<table>";
		print "<tr>";
		print "<td>Student Id </em></td>";
		print "<td><input type=text name='sid' value='" . $this->studentId . "' size=6 maxlength=8 readonly='readonly' >&nbsp&nbsp";
		
		print "<input type='text' class='stu_status' size=12 style='color:blue;' value='Tuition-link' readonly";
		print " onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_tution_plan_n_setup&a1=" . $this->studentId . "' >";
				
		print "</td>";
		print "<td>ICSGV Member </td><td><input type=checkbox name='icsgv_mem'";
		if (! empty ( $this->infoRegistration ['icsgv_mem'] ) && $this->infoRegistration ['icsgv_mem'] == 1) {
			print " checked ";
		}
		print " ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Student Name (First, Middle, Last) </td>";
		print "<td><input type=text name='first_name' size=12 maxlength=20 value= '" . $this->infoPersonalInfo ['first_name'] . "' " . $read_attr . ">&nbsp&nbsp&nbsp";
		
		print "<input type=text name='middle_name' size=4 maxlength=15 value= '" . $this->infoPersonalInfo ['middle_name'] . "' " . $read_attr . ">&nbsp&nbsp&nbsp";
		
		print "<input type=text name='last_name' size=12 maxlength=20 value= '" . $this->infoPersonalInfo ['last_name'] . "' " . $read_attr . "></td>";
		print "</tr>";
		
		print "<td class='normal1'>Regular school grade </td>";
		print "<td><input type=text name='reg_school_grade' size=2 maxlength=2 value= '" . $this->infoRegistration ['reg_school_grade'] . "' " . $read_attr . "></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Cell Phone </td>";
		
		print "<td><input type=text name='cell_area' size=3 maxlength=3 value= '" . $cell_area . "' " . $read_attr . ">";
		
		print "<input type=text name='cell_local' size=3 maxlength=3 value= '" . $cell_local . "' " . $read_attr . ">";
		print "-";
		print "<input type=text name='cell_number' size=4 maxlength=4 value= '" . $cell_number . "' " . $read_attr . "></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Home Phone</td>";
		
		print "<td><input type=text name='home_area' size=3 maxlength=3 value= '" . $home_area . "' " . $read_attr . " style='color:blue;' >";
		print "<input type=text name='home_local' size=3 maxlength=3 value= '" . $home_local . "' " . $read_attr . " style='color:blue;' >";
		print "-";
		print "<input type=text name='home_number' size=4 maxlength=4 value= '" . $home_number . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Email </td>";
		print "<td><input type=text name='email' size=30 value= '" . $this->infoPersonalInfo ['email'] . "' " . $read_attr . "></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Address </td>";
		print "<td><input type=text name='address' size=24 maxlength=55 value= '" . $this->infoPersonalInfo ['address'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>City </td>";
		print "<td><input type=text name='city' size=15 maxlength=30 value= '" . $this->infoPersonalInfo ['city'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print '<td>State:</td>';
		print "<td>California";
		
		print "<td><input type=radio name='student_status' value='active'";
		if (! empty ( $this->infoStudent ['status'] ) && $this->infoStudent ['status'] === 'ACTIVE') {
			print " checked='yes' ";
		}
		print " > ACTIVE </td>";
		
		print "</td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Zipcode </td>";
		print "<td><input type=text name='zipcode' size=5 maxlength=5 value= '" . $this->infoPersonalInfo ['zipcode'] . "' " . $read_attr . " style='color:blue;' ></td>";
		
		print "<td><input type=radio name='student_status' value='inactive'";
		if (! empty ( $this->infoStudent ['status'] ) && $this->infoStudent ['status'] === 'INACTIVE') {
			print " checked='yes' ";
		}
		print " > INACTIVE </td>";
		
		print "</tr>";
		
		print "<tr>";
		print "<td>Registration Date (mm/dd/yyyy) </em></td>";
		print "<td><input type=text name='registration_date' value='" . $reg_date . "' " . $read_attr . " ></td>";
		
		print "<td><input type=radio name='student_status' value='graduated'";
		if (! empty ( $this->infoStudent ['status'] ) && $this->infoStudent ['status'] === 'GRADUATED') {
			print " checked='yes' ";
		}
		print " > GRADUATED </td>";
		
		print "</tr>";
		print "</table>";
	}
	
	private function modify_parent_pers_info($readonly = TRUE) {
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		$father_cell_area='';
		$father_cell_local=''; 
		$father_cell_number='';
		$mother_cell_area='';
		$mother_cell_local=''; 
		$mother_cell_number='';
		if (!empty($this->infoParent ['father_cell_phone'])) {
			list ( $father_cell_area, $father_cell_local, $father_cell_number ) = split ( '[-]', $this->infoParent ['father_cell_phone'] );
		}
		if (!empty($this->infoParent ['mother_cell_phone'])) {
			list ( $mother_cell_area, $mother_cell_local, $mother_cell_number ) = split ( '[-]', $this->infoParent ['mother_cell_phone'] );
		}
		print "<table>";
		print "<tr>";
		print "<td class='normal1'>Mother's Name (First, Middle, Last) </td>";
		print "<td><input type=text name='mother_first_name' size=12 maxlength=20 value= '" . $this->infoParent ['mother_first_name'] . "' " . $read_attr . " style='color:blue;' >&nbsp&nbsp&nbsp";
		
		print "<input type=text name='mother_middle_name' size=3 maxlength=20 value= '" . $this->infoParent ['mother_middle_name'] . "' " . $read_attr . "  style='color:blue;' >&nbsp&nbsp&nbsp";
		
		print "<input type=text name='mother_last_name' size=12 maxlength=20 value= '" . $this->infoParent ['mother_last_name'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Mother's Cell</td>";
		print "<td><input type=text name='mother_cell_area' size=3 maxlength=3 value= '" . $mother_cell_area . "' " . $read_attr . " style='color:blue;'>";
		
		print "<input type=text name='mother_cell_local' size=3 maxlength=3 value= '" . $mother_cell_local . "' " . $read_attr . " style='color:blue;' >";
		print "-";
		print "<input type=text name='mother_cell_number' size=4 maxlength=4 value= '" . $mother_cell_number . "' " . $read_attr . " style='color:blue;'></td>";
		
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Father's Name (First, Middle, Last) </td>";
		print "<td><input type=text name='father_first_name' size=12 maxlength=20 value= '" . $this->infoParent ['father_first_name'] . "' " . $read_attr . " style='color:blue;' >&nbsp&nbsp&nbsp";
		
		print "<input type=text name='father_middle_name' size=3 maxlength=20 value= '" . $this->infoParent ['father_middle_name'] . "' " . $read_attr . "  style='color:blue;' >&nbsp&nbsp&nbsp";
		
		print "<input type=text name='father_last_name' size=12 maxlength=20 value= '" . $this->infoParent ['father_last_name'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Father's Cell</td>";
		print "<td><input type=text name='father_cell_area' size=3 maxlength=3 value= '" . $father_cell_area . "' " . $read_attr . " style='color:blue;'>";
		
		print "<input type=text name='father_cell_local' size=3 maxlength=3 value= '" . $father_cell_local . "' " . $read_attr . " style='color:blue;' >";
		print "-";
		print "<input type=text name='father_cell_number' size=4 maxlength=4 value= '" . $father_cell_number . "' " . $read_attr . " style='color:blue;'></td>";
		
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Email </td>";
		print "<td><input type=text name='par_email' size=30 value= '" . $this->infoParent ['par_email'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Parent volunteer (Sunday) Date - 1 </td>";
		print "<td><input type=text class='datepicker' name='parent_volun_date1' size=10 maxlength=10 value= '" . convert_sql_date_to_normal ( $this->infoRegistration ['parent_volun_1'] ) . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Parent volunteer (Sunday) Date - 2 </em>";
		print "<td><input type=text class='datepicker' name='parent_volun_date2' size=10 maxlength=10 value= '" . convert_sql_date_to_normal ( $this->infoRegistration ['parent_volun_2'] ) . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "</table>";
		
		print "<input type=hidden name='parent_id' value='" . $this->infoStudent ['parent_id'] . "'>";
		print "<input type=hidden name='personal_info_id' value='" . $this->infoStudent ['personal_info_id'] . "'>";
	}
	private function modify_student_medical_consent($readonly = TRUE) {
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		
		print "<table>";
		print "<tr>";
		print "<td class='normal1'>Allergies </td>";
		print "<td><input type=text name='allergies' size=35 maxlength=40  value= '" . $this->infoRegistration ['allergies'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		
		print "<tr>";
		print "<td class='normal1'>Medications </td>";
		print "<td><input type=text name='medications' size=35 maxlength=40 value= '" . $this->infoRegistration ['medications'] . "' " . $read_attr . " style='color:blue;' ></td>";
		print "</tr>";
		print "</table>";
	}
	
	private function modify_student_pickup_auth($readonly = TRUE) {
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		
		print "<table>";
		print "<tr>";
		print "<td colspan='3'> Authorized person name and other info to Pickup  in case of emergency</td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Name</span></td> ";
		print "<td><input type=text name='auth_person1' size=15 maxlength=24 value= '" . $this->infoRegistration ['auth_person1'] . "' " . $read_attr . " ></td>";
		print "<td><input type=text name='auth_person2' size=15 maxlength=24 value= '" . $this->infoRegistration ['auth_person2'] . "' " . $read_attr . " ></td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Address</span></td> ";
		print "<td><input type=text name='address_ap1' size=15 maxlength=24 value= '" . $this->infoRegistration ['address_ap1'] . "'  " . $read_attr . " ></td>";
		print "<td><input type=text name='address_ap2' size=15 maxlength=24 value= '" . $this->infoRegistration ['address_ap2'] . "'  " . $read_attr . " ></td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Phone</span></td> ";
		print "<td><input type=text name='phone_ap1' size=15 maxlength=24 value= '" . $this->infoRegistration ['phone_ap1'] . "'  " . $read_attr . " ></td>";
		print "<td><input type=text name='phone_ap2' size=15 maxlength=24 value= '" . $this->infoRegistration ['phone_ap2'] . "'  " . $read_attr . " ></td>";
		print "</tr>";
		print "<tr>";
		print "<td><span style='font-weight: normal;'>Driver Lic.</span></td> ";
		print "<td><input type=text name='driver_lic_ap1' size=15 maxlength=24 value= '" . $this->infoRegistration ['driver_lic_ap1'] . "'  " . $read_attr . " ></td>";
		print "<td><input type=text name='driver_lic_ap2' size=15 maxlength=24 value= '" . $this->infoRegistration ['driver_lic_ap2'] . "'  " . $read_attr . " ></td>";
		print "</tr>";
		
		print "</table>";
	}
	
	private function modify_student_data($readonly = TRUE) {
		print "<input type=hidden name='student_id' value='" . $this->studentId . "'>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Student Information</B></LEGEND>";
		
		$this->modify_student_pers_info ( $readonly );
		
		print "</FIELDSET>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Approve Student Registration</B></LEGEND>";
		
		$this->approve_student_grade ( $readonly );
		
		print "</FIELDSET>";
		
		print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Parent Information</B></LEGEND>";
		
		$this->modify_parent_pers_info ( $readonly );
		print "</FIELDSET>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Parent Permission and Waiver</B></LEGEND>";
		
		print '<label class="normal1">Parent Permission Waiver signed </label>';
		print '<input type="text" size=40 style="color:blue;" readonly="readonly">';
		print "</FIELDSET>";
		
		print '<FIELDSET  style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Parent Consent for Medical Treatment</B></LEGEND>";
		
		$this->modify_student_medical_consent ( $readonly );
		print '<label class="normal1">Parent Consent for Emergency Medical Treatment signed </label>';
		
		print "</FIELDSET>";
		
		print '<FIELDSET style="background-color:' . get_color ( 'SECTION' ) . ' ; ">';
		print "<LEGEND><B>Names of persons authorized to pickup child(ren) in Emergency</B></LEGEND>";
		
		$this->modify_student_pickup_auth ( $readonly );
		
		print "</FIELDSET>";
		
		setSubmitValue ( "updateStudentRecord" );
	}
	
	private function approve_student_grade($readonly = TRUE) {
		$pid = $this->studentIf->get_parent_id($this->infoRegistration ['student_id']);
		$sib_ids = $this->studentIf->get_children_ids($pid);
		
		if ($readonly) {
			$read_attr = " readonly='readonly' ";
		} else {
			$read_attr = '';
		}
		
		if (! empty ( $this->infoRegistration ['approval_date'] )) {
			$app_date = convert_sql_date_to_normal ( $this->infoRegistration ['approval_date'] );
		}
		
		print "<table>";
		print "<tr>";
		print "<td>Number of Siblings  </td>";
		print "<td>";
		if (count($sib_ids)>0) {
			print "<input type='text' size=2 style='color:blue;' value='" . (count($sib_ids) - 1) . "' readonly>";
			$sib_cnt=1;
			print "<label> Sibling Student Ids </label>";
			for ($j=0;$j<count($sib_ids);$j++) {
				if ($sib_ids[$j] != $this->infoRegistration ['student_id']) {
					print "SIB-" . $sib_cnt++ . "<input type='text' class='stu_status' size=4 style='color:blue;' value='" . $sib_ids[$j] . "' readonly";
					print " onclick=location.href='wis_webIf.php?obj=studentRecord&meth=view_student_record_n_modify&a1=" . $sib_ids[$j] . "' >";
				}
			}
		} else { 
			print "0";
		}
		print "</td>";
		
		print "</tr>";
		
		print "<tr>";
		print "<td>Grade </td>";
		print "<td colspan='2'>";
		print "<table>";
		print "<tr>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='na'";
		if (empty ( $this->infoRegistration ['wis_grade'] )) {
			print " checked='yes'";
		}
		print " >na </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='Basic'";
		if ($this->infoRegistration ['wis_grade'] == 'Basic') {
			print " checked='yes'";
		}
		print " >Basic </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='PRE_K'";
		if ($this->infoRegistration ['wis_grade'] == 'PRE_K') {
			print " checked='yes'";
		}
		print " >PRE_K </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='KG'";
		if ($this->infoRegistration ['wis_grade'] == 'KG') {
			print " checked='yes'";
		}
		print " >KG </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='1'";
		if ($this->infoRegistration ['wis_grade'] == '1') {
			print " checked='yes'";
		}
		print " >1 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='2'";
		if ($this->infoRegistration ['wis_grade'] == '2') {
			print " checked='yes'";
		}
		print " >2 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='3'";
		if ($this->infoRegistration ['wis_grade'] == '3') {
			print " checked='yes'";
		}
		print " >3 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='4'";
		if ($this->infoRegistration ['wis_grade'] == '4') {
			print " checked='yes'";
		}
		print " >4 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='5'";
		if ($this->infoRegistration ['wis_grade'] == '5') {
			print " checked='yes'";
		}
		print " >5 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='6'";
		if ($this->infoRegistration ['wis_grade'] == '6') {
			print " checked='yes'";
		}
		print " >6 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='7'";
		if ($this->infoRegistration ['wis_grade'] == '7') {
			print " checked='yes'";
		}
		print " >7 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='8'";
		if ($this->infoRegistration ['wis_grade'] == '8') {
			print " checked='yes'";
		}
		print " >8 </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_grade'  value='YG'";
		if ($this->infoRegistration ['wis_grade'] == 'YG') {
			print " checked='yes'";
		}
		print " >YG </td>";
		print "</tr>";
		print "</table>";
		print "</td>";
		print "</tr>";
		print "<tr>";
		
		print "<td>Section </em></td>";
		
		print "<td>";
		print "<table>";
		print "<tr>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_section'  value='na'";
		if (empty ( $this->infoRegistration ['section'] )) {
			print " checked='yes'";
		}
		print " >na </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_section'  value='A'";
		if ($this->infoRegistration ['section'] == 'A') {
			print " checked='yes'";
		}
		print " >A </td>";
		print "<td style='border: 1px solid black;'><input type=radio name='student_section'  value='B'";
		if ($this->infoRegistration ['section'] == 'B') {
			print " checked='yes'";
		}
		print " >B </td>";
		print "</tr>";
		print "</table>";
		print "</td>";
		
		print "<td><input type=radio name='reg_status' value='pending'";
		if (! empty ( $this->infoRegistration ['reg_status'] ) && $this->infoRegistration ['reg_status'] === 'PENDING') {
			print " checked";
		}
		print " > PENDING </td>";
		
		print "</tr>";
		print "<tr>";
		print "<td>Approval Date </em></td>";
		print "<td><input type=text name='approval_date' class='datepicker' size=10 maxlength=10 ";
		if (! empty ( $app_date )) {
			print " value='" . $app_date . "' ";
		} else {
			print " value='" . today_date () . "' ";
		}
		print $read_attr . " >";
		
		print "<td><input type=radio name='reg_status' value='approved'";
		if (! empty ( $this->infoRegistration ['reg_status'] ) && $this->infoRegistration ['reg_status'] === 'APPROVED') {
			print " checked='yes' ";
		}
		print " > APROVED </td>";
		
		print "</tr>";
		print "<tr>";
		print "<td>Approved By </em></td>";
		print "<td><input type=text name='approved_by' size=25 maxlength=40 value='" . $this->infoRegistration ['approved_by'] . "' " . $read_attr . " >";
		
		print "<td><input type=radio name='reg_status' value='denied'";
		if (! empty ( $this->infoRegistration ['reg_status'] ) && $this->infoRegistration ['reg_status'] === 'DENIED') {
			print " checked='yes' ";
		}
		print " > DENIED </td>";
		
		print "<td> </td>";
		print "</tr>";
		
		print "</table>";
		
		if (! empty ( $this->infoRegistration ['id_regis'] )) {
			print "<input type=hidden name='registration_id' value='" . $this->infoRegistration ['id_regis'] . "'>";
		}
	}
	
	private function nameSort($a, $b) {
		if ($cmp = strnatcasecmp ( $a ['last_name'], $b ['last_name'] ))
			return $cmp;
		
		return strnatcasecmp ( $a ['first_name'], $b ['first_name'] );
		// return strcmp($a['last_name'], $b['last_name']);
	}
}
?>
