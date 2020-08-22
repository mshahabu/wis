<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';

function signin() {
    
    Print "<Form name ='form2' Method ='Post' ACTION ='wis_process_request.php'>";
    
    print '<H4> ICSGV WIS Sign-in </H4>';
    print 'Only WIS can sign-in <BR>';
    print 'Call WIS admin to get your member ID <BR><BR>';
    
    print '<table>';
    print '<tr> <td>ID</td>               <td colspan=5 height=10><input type=text name=signin_id size=5 maxlength=5>      </td> <td></td></tr>';
    print '<tr> <td>Email</td>            <td colspan=5 height=10><input type=text name=email size=20 maxlength=50>        </td> <td></td></tr>';
    print '<tr> <td>First Name</td>       <td colspan=5 height=10><input type=text name=firstname size=20 maxlength=30>    </td> <td></td></tr>';
    print '<tr> <td>Last Name</td>        <td colspan=5 height=10><input type=text name=lastname size=20 maxlength=30>     </td> <td></td></tr>';
    print '<tr> <td>User Name</td>        <td colspan=5 height=10><input type=text name=username size=20 maxlength=30>     </td> <td></td></tr>';
    print '<tr> <td>Password</td>         <td colspan=5 height=10><input type=password name=password1 size=20 maxlength=30></td> <td></td></tr>';
    print '<tr> <td>Re-type Password</td> <td colspan=5 height=10><input type=password name=password2 size=20 maxlength=30></td> <td></td></tr>';
    
    print "<tr> <td><input type=radio name='signin_as'  value=1 checked> Student </td>";
    print "     <td><input type=radio name='signin_as'  value=2 > Teacher </td>";
    print "     <td><input type=radio name='signin_as'  value=3 > Staff   </td></tr>";
    print '</table>';
    
    print '<Input type = "Submit" name = "SubmitLog" Value = "Sign In">';
}

function username_pass_change() {
    print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
    print '<LEGEND></LEGEND>';
    print '<H4 style="text-align: center";> Member User Name / Password Change </H4>';
    
    print '<table >';
    print '<tr> <td>User Name</td>        <td colspan=5 height=10><input type=text name=username size=20 maxlength=30>      </td> </tr>';
    print '<tr> <td>Password</td>         <td colspan=5 height=10><input type=password name=password1 size=20 maxlength=30> </td> </tr>';
    print '<tr> <td>Re-type Password</td> <td colspan=5 height=10><input type=password name=password2 size=20 maxlength=30> </td> </tr>';
    print '</table>';
    
    print "</FIELDSET>";
    
    setSubmitValue ( "change_user_pass" );
}

function signin_with_banner() {
    welcome_banner ( "WIS" );
    signin ();
}

if (isset ( $_REQUEST ['fws'] ) && function_exists ( $_REQUEST ['fws'] )) {
    $_REQUEST ['fws'] ();
}

?>
