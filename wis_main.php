<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

if (version_compare ( phpversion (), '5.4.0', '>=' )) {
    if (session_status () !== PHP_SESSION_ACTIVE) {
        session_start ();
    }
} else {
    if (session_id () === '') {
        session_start ();
    }
}

include_once "wis_util.php";

// $_SESSION['authenticity'] = Authentication::INVALID;

if (isset ( $_REQUEST ['vl'] ) && $_REQUEST ['vl'] === 'lout') {
    $_SESSION ['authenticity'] = Authentication::INVALID;
    
    session_unset ();
    $_SESSION = array ();
    session_destroy ();
    session_write_close ();
}

include_once ("wis_header.php");

wis_main_menu ( wis_connect_to_mysql (), FALSE );

$wval = "window.open('wis_signin.php?fws=signin_with_banner','', 'width=350, height=450, location=no, menubar=no, status=no,toolbar=no, scrollbars=no, resizable=no'); return false";
print '<a href="" onclick="' . $wval . '">';

print 'New login? Click here to sign-in</a><br><br>';

print '<table>';

print '<tr><td>&nbsp&nbsp User name</td><td colspan=1 height=30><input type=text name=username size=20 maxlength=20></td></tr>';
print '<tr><td>&nbsp&nbsp Password</td><td colspan=1 height=30><input type=password name=password size=20 maxlength=30></td></tr>';

print '<tr> <td colspan="2">Login As</td></tr>';
print '<tr> <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=radio name="access" value="Staff" > Staff </td>';
print '<td> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
print '<input type="Submit" name="SubmitLog" value="Log In" style="background-color:lightgreen;"> </td></tr>';
print '<tr> <td colspan="2">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=radio name="access" value="Teacher"> Teacher          </td></tr>';
print '<tr> <td colspan="2">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type=radio name="access" value="Student" checked="yes"> Student/Parent   </td></tr>';

print '</table>';
print '<P>';

print '<input type=hidden name="login_source" value="office">';

wis_footer ( FALSE );

?>
