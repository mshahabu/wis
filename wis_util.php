<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once ("wis_menu.php");

if (version_compare ( phpversion (), '5.4.0', '>=' )) {
    if (session_status () !== PHP_SESSION_ACTIVE) {
        session_start ();
    }
} else {
    if (session_id () === '') {
        session_start ();
    }
}
?>

<script language="javascript" type="text/javascript">
  //<!-- 
  //Browser Support Code
function ajaxFunction(ev){

  if (ev.keyCode==13) {

    var ajaxRequest;  // The variable that makes Ajax possible!
    //alert("Javascript");
    try{
      // Opera 8.0+, Firefox, Safari
      ajaxRequest = new XMLHttpRequest();
    }catch (e){
      // Internet Explorer Browsers
      try{
    ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
    try{
      ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }catch (e){
      // Something went wrong
      alert("Your browser broke!");
      return false;
    }
      }
    }

    //alert("Administrator ALERT1"); 
    //document.getElementsByName("SubmitLog")[0].value="Test2";
    //document.getElementsByName("Submit")[0].value="";
    document.getElementsById('test1').value = "studentRecSearch";
    //document.getElementsById('SubmitVal')[1].value="studentRecSearch";
    //document.getElementsByName("SubmitVal")[2].value="studentRecSearch";

    window.location = 'http://localhost/cgi-bin/wis_process_request.php';

    //ajaxRequest.open("GET", "http://localhost/cgi-bin/wis_process_request.php?SubmitVal=studentRecSearch", false);
    //ajaxRequest.send(null);

  }
}
</script>

<?php

$access_privilege = 0;

$background_color = '#FFCC99';
$header_color = '#628B61';
$button_color = '#F3D5DB';

$box_color = '#F6E4CC';
$section_color = '#C7E1BA';

$logFileName = 'icsgv_wis.log';

// ICSGV WIS
define ( "WIS_STUDENT", 1 );
define ( "WIS_TEACHER", 2 );
define ( "WIS_STAFF", 4 );

define ( "WIS_BA", 1 );
define ( "WIS_PK", 2 );
define ( "WIS_KG", 4 );
define ( "WIS_1", 8 );
define ( "WIS_2", 16 );
define ( "WIS_3", 32 );
define ( "WIS_4", 64 );
define ( "WIS_5", 128 );
define ( "WIS_6", 256 );
define ( "WIS_7", 512 );
define ( "WIS_8", 1024 );
define ( "WIS_YG", 2048 );
define ( "WIS_ALL", 4095 );

define ( "GRADE_LIST", 5 );
define ( "TUTION", 6 );
define ( "BRIEF_REC", 7 );
define ( "DETAIL_REC", 8 );
define ( "GRADUATE", 9 );
define ( "INACTIVE", 10 );

final class Action {
    private function __construct() {
    }
    const CREATE = 0;
    const MODIFY = 1;
    const RE_ENTER = 2;
    const REENTER_MODIFY = 3;
};

final class Status {
    private function __construct() {
    }
    const PENDING = 0;
    const APPROVED = 1;
    const GRADUATED = 2;
    const INACTIVE = 3;
    const DENIED = 4;
};

final class Authentication {
    private function __construct() {
    }
    const INVALID = 0;
    const SIGNIN = 1;
    const VALID = 99;
}

print '<SCRIPT LANGUAGE="JavaScript">';

print 'function print_div(divName) {';
print '   var headstr = "<html><head><title></title></head><body>";';
print '   var footstr = "</body>";';
print '   var printContents = document.getElementById(divName).innerHTML;';
print '   var originalContents = document.body.innerHTML;';
// print ' print("MESSAGE: %s ",printContents);';
print '  document.body.innerHTML = headstr+printContents+footstr;';

print '  window.print();';

print '  document.body.innerHTML = originalContents;';
print '}';

print "function keyCheck1 (ev1) {";
print "   if (ev1.keyCode==13) {";
// print ' alert("Administrator ALERT1"); ';
// print ' document.getElementsByName("Submit")[0].value="";';
print '      document.getElementsByName("SubmitVal")[0].value="studentRecSearch";';
print '      document.getElementsByName("SubmitVal")[1].value="studentRecSearch";';
print '      document.getElementsByName("SubmitVal")[2].value="studentRecSearch";';
print '   ajaxRequest.open("GET", "wis_process_request.php", true); ';
print '   ajaxRequest.send(null); ';

print "   }";
print "}";

print "function keyCheck2 (ev2) {";
print "   if (ev2.keyCode==13) {";
// print ' alert("Administrator ALERT2"); ';
// print ' document.getElementsByName("Submit")[0].value="";';
print '      document.getElementsByName("SubmitVal")[0].value="studentRecSearch";';
print '      document.getElementsByName("SubmitVal")[1].value="studentRecSearch";';
print '      document.getElementsByName("SubmitVal")[2].value="studentRecSearch";';
print "   }";
print "}";

print "</SCRIPT>";

ini_set ( 'date.timezone', 'America/Los_Angeles' );

// get a new salt - 8 hexadecimal characters long
// current PHP installations should not exceed 8 characters
// on dechex( mt_rand() )
// but we future proof it anyway with substr()
function wis_get_password_salt() {
    return substr ( str_pad ( dechex ( mt_rand () ), 8, '0', STR_PAD_LEFT ), - 8 );
}

// calculate the hash from a salt and a password
function wis_get_password_hash($salt, $password) {
    return $salt . (hash ( 'whirlpool', $salt . $password ));
}

// compare a password to a hash
function wis_compare_password($password, $hash) {
    $salt = substr ( $hash, 0, 8 );
    return $hash == wis_get_password_hash ( $salt, $password );
}

function wis_illegal_choice($var) {
    print "ERROR -- " . $var;
    print "<br>";
    print "Please Inform Web Adiministrator";
    
    print "<br>";
    print "<br>";
}

function get_color($type) {
    global $header_color, $button_colcor, $background_color, $box_color, $section_color;
    
    if ($type === 'BACKGROUND') {
        return $background_color;
    } elseif ($type === 'HEADER') {
        return $header_color;
    } elseif ($type === 'BUTTON') {
        return $button_color;
    } elseif ($type === 'BOX') {
        return $box_color;
    } elseif ($type === 'SECTION') {
        return $section_color;
    } else {
        die ( 'Invalid query Color option ' . $type );
    }
}

function createDropdown($arr, $frm, $kv = 0) {
    $html = '<select name="' . $frm . '" id="' . $frm;
    if ($kv == 0) {
        $html .= '" style="font-size: 16px"> <BR>';
    } else {
        $html .= '"> <BR>';
    }
    if (count ( $arr ) == 0) {
        $html .= ' <option value="None"> None </option>';
    } else {
        foreach ( $arr as $key => $value ) {
            if ($kv == 0) {
                $html .= ' <option value="' . $value . '">' . $value . '</option><BR>';
            } else {
                $html .= ' <option value="' . $key . '">' . $value . '</option><BR>';
            }
        }
    }
    $html .= '</select>';
    print $html;
}

function wis_log_event($msg) {
    global $logFileName;
    
    // print 'DOCUMENT_ROOT is ' . $_SERVER['DOCUMENT_ROOT'] . '<BR>';
    $myFile = $_SERVER ['DOCUMENT_ROOT'] . '/' . $logFileName;
    $logFile = fopen ( $myFile, 'a' ) or die ( "can't open file" );
    $stringData = date ( "Y-m-d H:i:s" ) . " ";
    if (! empty ( $_SESSION ['actualUserName'] )) {
        $stringData .= $_SESSION ['actualUserName'];
    }
    $stringData .= " " . $msg . "\n";
    fwrite ( $logFile, $stringData );
    fclose ( $logFile );
}

function today_date() {
    // This gets today date
    $date = time ();
    
    // This puts the day, month, and year in seperate variables
    $day = date ( 'd', $date );
    $month = date ( 'm', $date );
    $year = date ( 'Y', $date );
    
    $today = $month . '/' . $day . '/' . $year;
    
    return $today;
}

function convert_sql_date_to_normal($sql_date) {
    // This gets today date
    if (empty ( $sql_date )) {
        return;
    }
    
    list ( $year, $month, $day ) = split ( '[-]', $sql_date );
    
    $date = $month . '/' . $day . '/' . $year;
    return $date;
}

function convert_normal_date_to_SQL($date) {
    // This gets today date
    $sql_date = null;
    if (! empty ( $date )) {
        list ( $month, $day, $year ) = explode ( '/', $date );
        
        $sql_date = $year . '-' . $month . '-' . $day;
    }
    return $sql_date;
}

function today_date_SQL_format() {
    // This gets today date
    $date = time ();
    
    // This puts the day, month, and year in seperate variables
    $day = date ( 'd', $date );
    $month = date ( 'm', $date );
    $year = date ( 'Y', $date );
    
    $today = $year . '-' . $month . '-' . $day;
    return $today;
}

function wis_intro() {
    print '<div id="home1">';
    print '<P>';
    
    print 'The Weekend Islamic School program seeks to provide quality Islamic education by qualified, well resourced teachers for students in a safe, enjoyable, Islamic environment through continuously striving to improve the effectiveness of the Islamic educational program to build Islamic personalities by collaborating with parents, teachers, and students. <BR><BR>';
    
    print 'Weekend Islamic School classes start from Pre-KG to 8th and the ages are from 4 to 13 years old. The enrollment in the current year is 350 students. The two and half curriculum consist of basic concepts of Tawheed, Quran, Islamic History, etiquettes. There are about 21 classes.<BR>';
    
    print '<H4> Administration </H4>';
    
    print '<UL>';
    print '<LI>Rashed Mohammadi (Principal)</LI>';
    print '<LI>Salma Ansari (Vice Principal)</LI>';
    print '<LI>Nadir Soofi (Registrar)</LI>';
    print '<LI>Carla Abu Lashin (Administrator)</LI>';
    print '<LI>Rashed Mohammadi (Library & Media)</LI>';
    print '<BR>';
    
    print '</div>';
    
    print '<div id="home2" style="text-align:center;">';
    
    if ((isset ( $_SESSION ['authenticity'] ) && $_SESSION ['authenticity'] == Authentication::VALID) && ($_SESSION['access_privilege'] == WIS_STAFF) ) {
        print "<BR><B style='text-align:center;'> Student record search <BR>(Enter name or student id)";
        print "<Table>";
        print "<tr>";
        print "<td>Name (First Last) </td><td><input type=text name='SearchName' size=25 maxlength=40 ></td>";
        print "</tr><tr>";
        print "<td>Student Id        </td><td style='text-align:center;'><input type=text name='SearchId' size=4 maxlength=4'></td>";
        print "<tr></tr>";
        print "<td></td><td style='text-align:center;'><input type='Submit' name='SubmitSearch' value='Search' style='background-color:lightgreen;'></td>";
        print "</tr>";
        print "</Table>";
        print "<BR>";
    } else {
        print "<BR>";
        print "<H4> School Timing </H4>";
        print "<H5> 10:30 AM - 1:00 PM Sundays </H5>";
        print "<BR>";
    }
    
    print '</div>';
    
    print '<div id="home3" style="text-align:center;"> ';
    
    //print '<pre>    <a href="wis_webIf.php?obj=calendar&meth=view_calendar" ><img src="http://www.icsgv.com/images/Cal.jpg" vertical-align:"middle" height="75" width="75"></a>';
    //print '<BR><B> School Calendar</B></pre>';
    
    //print '<pre>     <a href="wiser.php?form=login" ><img src="http://www.icsgv.com/images/registration.jpg" height="75" width="75"></a>';
    //print '<BR><B> Student Registration </B></pre>';
    
    //print '<pre>     <img src="http://www.icsgv.com/images/student.jpg" height="75" width="75">';
    //print '<BR><B> Student of the month </B></pre>';
    print '<BR>';
    
    print '</div>';
}

function wis_main_page($mysqli_h) {
    include_once ("wis_header.php");
    wis_main_menu ( $mysqli_h, FALSE );
    wis_intro ();
    wis_footer ( FALSE );
}

function convert_date_SQL_format($date) {
    
    // This puts the day, month, and year in seperate variables
    list ( $year, $month, $day ) = explode ( '-', $date );
    
    $new = $month . '-' . $day . '-' . $year;
    return $new;
}

function download_b() {
    $wval = "window.open('icsgv_mem_download.php','', 'height=300, location=no, menubar=no, status=no,toolbar=no, scrollbars=no, resizable=no'); return false";
    print '<Input type="button" value="Download Excel format" onclick="' . $wval . ' style="background-color:' . get_color ( 'BUTTON' ) . '; margin-left:150px; border-radius:20px/30px; height:25;">';
    // print '<Input type=Submit name = ' . $name . ' value = ' . $value . ' style="background-color:' . get_color('BUTTON') . '; margin-left:150px; border-radius:20px/30px; width:' . $width . '; height:25;">';
}

function setSubmitValue($value) {
    print '<input type=hidden id="test1" name="SubmitVal" value="' . $value . '">';
}

function getSubmitValue() {
    global $submitValue;
    return $submitValue;
}

function wis_convert_grade_to_num($grade) {
    $val = 0;
    if ($grade === 'Basic') {
        $val = WIS_BA;
    } else if ($grade === 'PRE_K') {
        $val = WIS_PK;
    } else if ($grade === 'KG') {
        $val = WIS_KG;
    } else if ($grade === '1') {
        $val = WIS_1;
    } else if ($grade === '2') {
        $val = WIS_2;
    } else if ($grade === '3') {
        $val = WIS_3;
    } else if ($grade === '4') {
        $val = WIS_4;
    } else if ($grade === '5') {
        $val = WIS_5;
    } else if ($grade === '6') {
        $val = WIS_6;
    } else if ($grade === '7') {
        $val = WIS_7;
    } else if ($grade === '8') {
        $val = WIS_8;
    } else if ($grade === 'YG') {
        $val = WIS_YG;
    }
    
    return $val;
}

function print_page() {
    print '<input type="button" value="Print" onClick="print_div(' . "'printableArea'" . ' )" style="background-color:' . get_color ( 'BUTTON' ) . '; color:green;margin-left:300px; " />';
    // $str = '<form> <input type="button" value="Print" onClick="window.print()" style="background-color:' . get_color('BUTTON') . '; color:green;margin-left:300px; border-radius:20px/30px; width:80; height:25;" /> </form>';
    
    // print 'if (window.print) {';
    // print "document.write('" . $str . "')";
    // print "}";
}

function welcome_banner($title) {
    print '<FIELDSET style="background-color:' . get_color ( 'BACKGROUND' ) . ' ;">';
    print '<H2 style="text-align: center";>Welcome to ' . $title . '</H2>';
    print '<LEGEND></LEGEND>';
}

function getCell($value) {
    return empty ( $value ) ? "&nbsp;" : $value;
}

/**
 *
 * @return bool
 */
function wis_is_session_started() {
    if (php_sapi_name () !== 'cli') {
        if (version_compare ( phpversion (), '5.4.0', '>=' )) {
            return session_status () === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id () === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

?>

