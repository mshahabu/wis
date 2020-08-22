<?php 
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_connect.php'; 
include_once "wis_Teacher.php"; 
include_once "wis_GradeBookIf.php"; 
include_once "wis_StudentIf.php"; 
include_once "wis_AdministrationIf.php"; 
include_once "wis_RegistrationIf.php"; 
include_once "wis_PersonalInfoIf.php"; 

function wis_get_book_list($mysqli_h) 
{
    $gradeBookIf = new GradeBookIf($mysqli_h); 

    $info = $gradeBookIf->get_all_records();
  
    for ($i=0; $i<count($info); $i++) {
        print       '<li style="width:250px;"><a href="wis_webIf.php?obj=book&meth=book_record_update&a1=' . $info[$i]['id_gb'] . '">';
        print  $info[$i]['book_name'] . " by " . $info[$i]['author_name'] . '</a></li>';  
        //print "book " . $info[$i]['book_name'] . "<BR>"; 
    }
}
  
function wis_get_grade_list($mysqli_h, $grade_section, $type, $status='APPROVED') 
{
    $studentIf = new StudentIf($mysqli_h);
    $registrationIf = new RegistrationIf($mysqli_h); 
    $personalInfoIf = new PersonalInfoIf($mysqli_h);
    $administrationIf = new AdministrationIf($mysqli_h);
    $j = 0;
    
    if ($grade_section === 'ALL') {
                //$info2 = $registrationIf->get_student_status_ids('ALL', $administrationIf->get_school_year());
                $info2 = $registrationIf->get_student_status_ids('ALL', 'ALL');
    } else {
                list($grade, $section) = split('[-]', $grade_section);
                $info2 = $registrationIf->get_student_grade_ids('ALL', $grade, $section, $administrationIf->get_school_year());
    }
    
    if ($type == GRADE_LIST) { 
                $func = "view_student_record_n_modify";
                $stu_status = 'ACTIVE';
    } else if ($type == TUTION) {
                $func = "view_tution_plan_n_setup";
                $stu_status = 'ACTIVE';
    } else if ($type == BRIEF_REC) {
                $func = "view_student_record";
                $stu_status = 'ACTIVE';
    } else if ($type == GRADUATE) {
                $func = "view_student_record_n_modify"; 
                $stu_status = 'GRADUATED';
    } else if ($type == INACTIVE) {
                $func = "view_student_record_n_modify"; 
                $stu_status = 'INACTIVE';
    }
    
    for ($i=0; $i<count($info2); $i++) {
            if ($studentIf->isStatus($info2[$i]['student_id'], $stu_status) ) {
                        //$info = $registrationIf->get_records(RegistrationIf::STUDENT_ID, $info2[$i]['student_id'], $administrationIf->get_school_year(), 'ALL');
                        $info = $registrationIf->get_records(RegistrationIf::STUDENT_ID, $info2[$i]['student_id'], 'ALL', 'ALL');
                        $pers_info_id = $studentIf->get_personal_info_id($info2[$i]['student_id']); 
                        $infoP = $personalInfoIf->get_name($pers_info_id); 
                        
                        //print "NAME: first last " . $info2[$i]['first_name'] . " " . $info2[$i]['last_name'] . $info2[$i]['id'] . "<BR>";
        
                        if ($status === 'PENDING') {
                            print  '<li style="width:200px">';
                        } else {
                            print  '<li>';
                        }
                        $hyephen = (!empty($info[0]['wis_grade']) && !empty($info[0]['section'])) ? "-" : "";
                        print  '<a href="wis_webIf.php?obj=studentRecord&meth=' . $func . '&a1=' . $info2[$i]['student_id'] . '">';
                        print  $info2[$i]['student_id'] . " - " . $infoP['first_name'] . " " . $infoP['middle_name'] . " " . $infoP['last_name'] . " (" . $info[0]['wis_grade'] . $hyephen . $info[0]['section'] . ')</a></li>';  
            }
    }
}

function get_teacher_class_list($mysqli_h)
{
    $registrationIf = new RegistrationIf($mysqli_h); 
    $personalInfoIf = new PersonalInfoIf($mysqli_h); 
    $teacherIf = new TeacherIf($mysqli_h); 
    
    $ainfo = $teacherIf->get_all_records(); 
    
    for ($i=0; $i<count($ainfo); $i++) {
                $pers_info_id = $teacherIf->get_personal_info_id($ainfo[$i]['teacher_id']);
                $info  = $personalInfoIf->get_name($pers_info_id); 
                $tinfo = $teacherIf->get_record($ainfo[$i]['teacher_id']);
        
                //print "NAME: first last " . $info['first_name'] . " " . $info['last_name'] . "<BR>";
                print       "<li style='width:200px'><a href='wis_webIf.php?obj=teacher&meth=get_class_roster&a1=" . $ainfo[$i]['teacher_id'] . "&a2=" . $info['first_name'] . "&a3=" . $info['middle_name'] . "&a4=" . $info['last_name'] .  "' >";
                print  $tinfo['grade'] . "-" . $tinfo['section'] . " : " . $info['first_name'] . " " . $info['middle_name'] . " " . $info['last_name'] . '</a></li>';  
    }
}

function wis_main_menu($mysqli_h, $printBut)
{
    global $registrationForm;

    $teacher = new Teacher($mysqli_h);
    $registrationIf = new RegistrationIf($mysqli_h);
    $administrationIf = new AdministrationIf($mysqli_h);
    
    print '<div id="nav">';
    print '<ul>';
    
    if (empty($registrationForm)) {
        print '<li><a href="wis.php">Home</a></li>';
    }
//print "AUTH VALUE : " . $_SESSION['authenticity'] . "<BR>";
    if (isset($_SESSION['authenticity']) && ($_SESSION['authenticity'] == Authentication::VALID)) {
        if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print '<li><a href="#">Registration</a>';
            print    '<ul>';
            print       '<li><a href="wiser.php?form=login">New registration</a></li>';
            print       '<li><a href="#">Pay tution fee</a>';
            print         '<ul>';
            print             '<li>';
            print                '<a href="#">ALL</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'ALL', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">Basic-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">Basic-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-A', TUTION);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-B', TUTION);
            print                  '</ul>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="#">Approve registration</a>';
            print         '<ul style="max-height: 200px; overflow-y: auto;">';
            print           '<li style="width:200px"><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list_n_assign_grade">ALL</a></li>'; 
            wis_get_grade_list($mysqli_h, 'ALL', GRADE_LIST, 'PENDING');
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="#">Modify student information</a>';
            print         '<ul>';
            print             '<li>';
            print                '<a href="#">ALL</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'ALL', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">Basic-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">Basic-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-A', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-B', GRADE_LIST);
            print                  '</ul>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
            print    '</ul>';
            print '</li>';
            print '<li><a href="#">Student Record</a>';
            print    '<ul>';
            print       '<li><a href="#">Individual Record</a>';
            print         '<ul>';
            print             '<li>';
            print                '<a href="#">Basic-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">Basic-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'Basic-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">PRE_K-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'PRE_K-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">KG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'KG-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">1-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '1-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">2-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '2-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">3-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '3-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">4-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '4-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">5-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '5-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">6-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '6-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">7-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '7-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">8-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, '8-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-A</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-A', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="#">YG-B</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            wis_get_grade_list($mysqli_h, 'YG-B', BRIEF_REC);
            print                  '</ul>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="#">Record Summary </a>';
            print         '<ul>'; 
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list">All Records </a></li>';
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=ALL&a2=APPROVED">Approved Records </a></li>';
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=ALL&a2=IN_REVIEW">In Reivew Applications</a></li>';
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=ALL&a2=NEW_APPL">New Applications</a></li>';
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_account_summary_list">Account Summary</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_account_list">Account Details</a></li>';
            /*
            print         '<ul>'; 
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list">Default</a></li>';
            print           '<li><a href="wis_webIf.php?obj=studentRecord&meth=selectStudentView">Selection</a></li>';
            print         '</ul>';
            */
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=Basic">Basic</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=PRE_K">PRE_K</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=KG">KG</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=1">1</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=2">2</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=3">3</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=4">4</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=5">5</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=6">6</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=7">7</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=8">8</a></li>';
            print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_student_list&a1=YG">YG</a></li>';
            print    '</ul>';
            print '</li>';
        }
        if ($_SESSION['access_privilege'] == WIS_STUDENT) {
            print "<li><a href='wis_webIf.php?obj=studentRecord&meth=get_profile&a1=" . $_SESSION['student_id'] .  "' >My Profile</a></li>";

            $infoRegistration = $registrationIf->get_record ( $_SESSION['student_id'], $administrationIf->get_school_year (), 'ACTIVE' );

            //if($infoRegistration['reg_status'] == 'PENDING')
            {
                print "<li><a href='wis_webIf.php?obj=studentRecord&meth=studentRegistrationApproval&a1=" . $_SESSION['student_id'] .  "' >Registration</a></li>";
            }
            
            print "<li><a href='wis_webIf.php?obj=studentRecord&meth=view_tution_plan_n_setup&a1=" . $_SESSION['student_id'] . "' >Tution</a></li>";
        }
        if ($_SESSION['access_privilege'] == WIS_TEACHER) {
            print "<li><a href='wis_webIf.php?obj=teacher&meth=get_class_roster&a1=" . $_SESSION['teacher_id'] . "&a2=" . $_SESSION['first_name'] . "&a3=" . $_SESSION['middle_name'] . "&a4=" . $_SESSION['last_name'] .  "' >My Class Roster</a></li>";
            print "<li><a href='wis_webIf.php?obj=teacher&meth=get_profile&a1=" . $_SESSION['teacher_id'] . "&a2=" . $_SESSION['first_name'] . "&a3=" . $_SESSION['middle_name'] . "&a4=" . $_SESSION['last_name'] .  "' >My Profile</a></li>";
            //print       '<li style="width:200px">;
        }
        print '<li><a href="#">Attendance</a>';
        print    '<ul>';
        if ($_SESSION['access_privilege'] == WIS_TEACHER) { 
                $teacher_name = $_SESSION['first_name'] . " " . $_SESSION['middle_name'] . " " . $_SESSION['last_name']; 
            print       "<li><a href='wis_webIf.php?obj=teacher&meth=view_n_update_attendance&a1=" . $_SESSION['teacher_id'] . "&a2=" . $teacher_name . "&a3=0" .  "'>Record Attendance</a></li>";
        }
        if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print       '<li><a href="#">View Attendance</a>';
            print         '<ul>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=Basic&a2=A">Basic-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=Basic&a2=B">Basic-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=PRE_K&a2=A">PRE_K-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=PRE_K&a2=B">PRE_K-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=KG&a2=A">KG-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=KG&a2=B">KG-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=1&a2=A">1-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=1&a2=B">1-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=2&a2=A">2-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=2&a2=B">2-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=3&a2=A">3-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=3&a2=B">3-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=4&a2=A">4-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=4&a2=B">4-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=5&a2=A">5-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=5&a2=B">5-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=6&a2=A">6-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=6&a2=B">6-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=7&a2=A">7-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=7&a2=B">7-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=8&a2=A">8-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=8&a2=B">8-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=YG&a2=A">YG-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=wis_class_attendance&a1=YG&a2=B">YG-B</a>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
        }
        print    '</ul>';
        print '</li>';
        print '<li><a href="#">Grades</a>';
        print    '<ul>';
        if ($_SESSION['access_privilege'] == WIS_TEACHER) {
            print       "<li><a href='wis_webIf.php?obj=teacher&meth=view_Qteacher_student_grades&a1=" . $_SESSION['teacher_id'] . "&a2=" . $_SESSION['first_name'] . "&a3=" . $_SESSION['middle_name'] . "&a4=" . $_SESSION['last_name'] . "'>Record Grades</a></li>";
        }
        if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print       '<li><a href="#">View Grades</a>';
            print         '<ul>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=Basic&a2=A">Basic-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=Basic&a2=B">Basic-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=PRE_K&a2=A">PRE_K-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=PRE_K&a2=B">PRE_K-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=KG&a2=A">KG-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=KG&a2=B">KG-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=1&a2=A">1-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=1&a2=B">1-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=2&a2=A">2-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=2&a2=B">2-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=3&a2=A">3-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=3&a2=B">3-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=4&a2=A">4-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=4&a2=B">4-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=5&a2=A">5-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=5&a2=B">5-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=6&a2=A">6-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=6&a2=B">6-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=7&a2=A">7-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=7&a2=B">7-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=8&a2=A">8-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=8&a2=B">8-B</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=YG&a2=A">YG-A</a>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=view_Qgrade_student_grades&a1=YG&a2=B">YG-B</a>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
        }
        print    '</ul>';
        print '</li>';
        print '<li><a href="#">Black Board</a>';
        print    '<ul>';
        print       '<li>';
        if ($_SESSION['access_privilege'] == WIS_TEACHER) {
            print        '<a href="wis_webIf.php?obj=blackBoard&meth=view_black_board&a1=' . $_SESSION['access_privilege'] . '&a2=' . $_SESSION['teacher_id'] . '">View Black Board</a>';
        } else if ($_SESSION['access_privilege'] == WIS_STUDENT) {
            print        '<a href="wis_webIf.php?obj=blackBoard&meth=view_black_board&a1=' . $_SESSION['access_privilege'] . '&a2=' . $_SESSION['student_id'] . '">View Black Board</a>';
        } else if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print        '<a href="wis_webIf.php?obj=blackBoard&meth=view_all_black_board">View Black Board</a>';
        }
        print       '</li>';
        if ($_SESSION['access_privilege'] == WIS_TEACHER) {
          print     '<li>';
          print          '<a href="wis_webIf.php?obj=blackBoard&meth=file_upload&a1=' . $_SESSION['teacher_id'] . '">Upload Files</a>';
          print     '</li>';
          print     '<li>';
          print          '<a href="wis_webIf.php?obj=blackBoard&meth=delete_black_board_files&a1=' . $_SESSION['teacher_id'] . '">Delete Files</a>';
          print     '</li>';
          print     '<li>';
          print          '<a href="wis_webIf.php?obj=blackBoard&meth=modify_black_board_files&a1=' . $_SESSION['teacher_id'] . '">Modify Files</a>';
          print     '</li>';
        }
        print    '</ul>';
        print '</li>';
        print '<li><a href="#">Books</a>';
        print    '<ul>';
        print       '<li><a href="#">Book List</a>';
        print         '<ul>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_ALL . '">ALL</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_BA . '">Basic</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_PK . '">PRE_K</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_KG . '">KG</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_1 . '">1</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_2 . '">2</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_3 . '">3</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_4 . '">4</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_5 . '">5</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_6 . '">6</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_7 . '">7</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_8 . '">8</a>';
        print             '</li>';
        print             '<li>';
        print                '<a href="wis_webIf.php?obj=book&meth=book_list&a1=' . WIS_YG . '">YG</a>';
        print             '</li>';
        print         '</ul>';
        print       '</li>';
        if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print       '<li><a href="wis_webIf.php?obj=book&meth=add_new_book">New Books</a></li>';
            print       '<li><a href="#">Update Books</a>';
            print         '<ul>';
            wis_get_book_list($mysqli_h);
            print         '</ul>';
            print       '</li>';
        }
        print    '</ul>';
        print '</li>';
        
        if ($_SESSION['access_privilege'] == WIS_STAFF) {
            print '<li><a href="#">Admin Tasks</a>';
            print    '<ul>';
            //print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_print_all_records1">View Print all Records1</a></li>';
            //print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=view_print_all_records2">View Print all Records</a></li>';
            print       '<li><a href="#">Close school year</a></li>';
            //print       '<li><a href="wis_webIf.php?obj=studentRecord&meth=close_school_year">Close school year</a></li>';
            //$wval = "window.open('wis_admin_win.php?fwss=year_setup','', 'width=350, height=300, location=no, menubar=no, status=no,toolbar=no, scrollbars=no, resizable=no'); return false";
            print       '<li><a href="#">Start new school year</a>';
            print         '<ul>';
            //print             '<li><a href="" onClick="' . $wval . '">Setup school year</a></li>';
            print             '<li><a href="#">Setup school year</a></li>';
            print             "<li><a href='wis_webIf.php?obj=administration&meth=setup_school_days&a1=0'>Setup school days</a></li>";
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="#">Teacher</a>';
            print         '<ul>';
            print             '<li><a href="wis_webIf.php?obj=teacher&meth=new_teacher_record">New Teacher</a></li>';
            print             '<li>';
            print                '<a href="#">Modify Info</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
                                       $teacher->get_teacher_list();
            print                  '</ul>';
            print             '</li>';
            print             '<li>';
            print                '<a href="wis_webIf.php?obj=teacher&meth=teacher_class_assignment">Grade/Room Assignment</a>';
            print                  '<ul style="max-height: 200px; overflow-y: auto;">';
            //wis_get_teacher_list();
            print                  '</ul>';
            print             '</li>';
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="#">Roster</a>';
            print       '<ul>';
            print           '<li><a href="wis_webIf.php?obj=teacher&meth=teacher_roster">Teacher roster</a></li>';
            print           '<li><a href="#">Class roster</a>';
            print              '<ul>';
                                   get_teacher_class_list($mysqli_h);
            print              '</ul>'; 
            print           '</li>'; 
            print         '</ul>';
            print       '</li>';
            print       '<li><a href="wis_webIf.php?obj=administration&meth=allocate_fees">Set Tution and Discounts</a>';
            print       '</li>';
            print       '<li><a href="#">Graduate Students</a>';
            print          '<ul style="max-height: 200px; overflow-y: auto;">';
                               wis_get_grade_list($mysqli_h, 'ALL', GRADUATE);
            print          '</ul>';
            print       '</li>';
            print       '<li><a href="#">Inactive Students</a>';
            print          '<ul style="max-height: 200px; overflow-y: auto;">';
                               wis_get_grade_list($mysqli_h, 'ALL', INACTIVE);
            print          '</ul>';
            print       '</li>';
            print       '<li><a href="#">Reset Password</a>';
            print       '</li>';
            print    '</ul>';
            print '</li>';
            
            print '<li><a href="#">Email</a>';
            print         '<ul>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=ALL&a2=">ALL</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=Basic&a2=A">Basic-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=Basic&a2=B">Basic-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=PRE_K&a2=A">PRE_K-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=PRE_K&a2=B">PRE_K-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=KG&a2=A">KG-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=KG&a2=B">KG-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=1&a2=A">1-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=1&a2=B">1-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=2&a2=A">2-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=2&a2=B">2-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=3&a2=A">3-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=3&a2=B">3-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=4&a2=A">4-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=4&a2=B">4-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=5&a2=A">5-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=5&a2=B">5-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=6&a2=A">6-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=6&a2=B">6-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=7&a2=A">7-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=7&a2=B">7-B</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=8&a2=A">8-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=8&a2=B">8-B</a>';
            print             '</li style="width:80px;">';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=YG&a2=A">YG-A</a>';
            print             '</li>';
            print             '<li style="width:80px;">';
            print                '<a href="wis_webIf.php?obj=email&meth=gather_email_message&a1=YG&a2=B">YG-B</a>';
            print             '</li style="width:80px;">';
            print         '</ul>';
            print '</li>';
            
        } 
    } else {
        if (empty($registrationForm)) {
            print '<li><label id="sub1"></li>';
            print '<li><li><a href="wis_main.php">Sign In</a></li>';
            print '<li><a href="#">Find Us</a>';
            print    '<ul>';
            print       '<li><a href="#">Contact Us</a></li>';
            print       '<li><a href="#">Our Location</a></li>';
            print       '<li><a href="#">Facebook <span class="right-arrow">&#9658;</span></a>';
            print          '<ul>';
            print             '<li><a href="#">Like Us</a></li>';
            print             '<li><a href="#">Favorite Us</a></li>';
            print          '</ul>';
            print       '</li>';
            print       '<li><a href="#">Twitter <span class="right-arrow">&#9658;</span></a>';
            print          '<ul>';
            print             '<li><a href="#">Follow Us</a></li>';
            print             '<li><a href="#">Tweet About Us</a></li>';
            print          '</ul>';
            print       '</li>';
            print       '<li><a href="#">Google+</a></li>';
            print    '</ul>';
            print '</li>';
        }
    }
    
    if (isset($printBut) && $printBut) {
        print '<li><a href="#" onClick="print_div(' . "'printableArea'" . ')">Print</a></li>';
    }
    
    if (isset($_SESSION['authenticity']) && ($_SESSION['authenticity'] == Authentication::VALID)) {
        //print '<li><label id="sub1"></li>';
        print '<li><a = href="wis_main.php?vl=lout"> Log Out </a></li>';
        //  print '<li><input type="Submit" id="sub1" name="SubmitLog" value="Log Out"></li>';
    }
    print '</ul>';
    print '<br class="clearboth"/>';
    
    print '</div>';
    
    print '<div id="main">';
    print '<h2>Welcome to ICSGV Weekend Islamic School (WIS) </h2>';
    
    
    if ( isset($_SESSION['wis_error_flag']) && $_SESSION['wis_error_flag'] == TRUE) {
        print $_SESSION['wis_error'];
        $_SESSION['wis_error_flag'] = FALSE;
        $_SESSION['wis_error'] = '';
    }
}

function wis_footer($submitBut) {

    ini_set('date.timezone', 'America/Los_Angeles');

    $year = date("Y");
    if (isset($submitBut) && $submitBut) {
        print '<div style="text-align:center;" >';
        print '<input type="Submit"  name="Submit" style="background-color:orange; width:120px; height:24px;" value="Submit">';
        print '</div>';
    }
    print '<footer>';

    print '<em style="font-size: 10px; text-align:right;">&copy' . $year . ' Product Line Software (PLS) Inc. All rights reserved.</em>';
    
    print '</footer>';
    print '</div>';
    print '</body>';

    print '</html>';
}

?>
