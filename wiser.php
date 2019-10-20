<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_connect.php'; 
include 'wis_RegistrationForm.php';

?>

<script language="javascript" type="text/javascript">
<!-- 

//Browser Support Code
function memberIdAjax() {
  //alert("Hello there"); 
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
  // Create a function that will receive data 
  // sent from the server and will update
  // div section in the same page.
  ajaxRequest.onreadystatechange = function(){
    if(ajaxRequest.readyState == 4){
      var ajaxDisplay = document.getElementById('printableArea');
      ajaxDisplay.innerHTML = ajaxRequest.responseText;
    }
  }

 // Now get the value from user and pass it to
 // server script.
 //var mid = document.getElementById("mem_id").value;
 var mid = form2.mid.value;
   //var mid_chk = document.getElementById("mem_id_check").checked;
 var mid_chk = form2.mid_chk.checked;

   //var newstr = "MemberID: " + mid + " : " + mid_chk;
   //alert(newstr); 

 if (mid_chk && mid > 0) {
   //alert("member id: true");
   var queryString = "?memberId=" + mid;

   window.open('wis_xxxx.php?f=signin','', 'height=350, width=320, location=no, menubar=no, status=no,toolbar=no, scrollbars=no, resizable=no'); 

   //ajaxRequest.open("GET", "wis_signin.php?f=signin_with_banner",true);

   //ajaxRequest.open("GET", "wis_ajax.php" + 
   //queryString, true);
   //ajaxRequest.send(null);
 }
}
-->
</script>

<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 11/27/2013
// Version 3.7

$regForm = new RegistrationForm( wis_connect_to_mysql() );

if ( isset($_REQUEST['form']) && $_REQUEST['form'] === 'login') {
    $regForm->student_registration(RegistrationForm::LOGIN);
} else {
    $_SESSION['authenticity'] = Authentication::INVALID;
    $regForm->student_registration(RegistrationForm::FORM);
}

?>

