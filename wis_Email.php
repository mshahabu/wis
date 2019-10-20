<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_AdministrationIf.php';
include_once 'wis_RegistrationIf.php';
include_once 'wis_StudentIf.php';
include_once 'wis_ParentIf.php';
include_once 'wis_PersonalInfoIf.php';

include('Mail.php');
include('Mail/mime.php');
include('Mail/mail.php'); // adds the enhanced send function

class Email {
	private $mysqli_h;
	private $administrationIf;
	private $registrationIf;
	private $parentIf;
	private $studentIf;
	private $personalInfoIf;
	
	function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
		$this->registrationIf = new RegistrationIf ( $mysqli_h );
		$this->parentIf = new ParentIf ( $mysqli_h );
		$this->studentIf = new StudentIf ( $mysqli_h );
		$this->personalInfoIf = new PersonalInfoIf ( $mysqli_h );
	}
	
	function get_email_header() {
		list ( $year, $nyear ) = split ( '[-]', $this->administrationIf->get_school_year () );
		
		$emailHeader = '<P style="text-align: center;font-size:14px">';
		$emailHeader .= '<br>';
		$emailHeader .= '<B>Weekend Islamic School - </B>';
		
		$emailHeader .= 'Islamic Center San Gabriel Valley';
		$emailHeader .= '<br>';
		
		$emailHeader .= '<label class="normal1" style="font-size:14px" >19164 E. Walnut Drive North, Rowland Heights, CA 91748 </label><br>';
		
		$emailHeader .= '<i>School Year  <B>' . $year . '-' . $nyear . '</B></i></P><BR>';
		
		return $emailHeader;
	}
	
	function get_email_list($grade, $section = 'ALL') {
		$info = $this->registrationIf->get_student_grade_ids ( 'ALL', $grade, $section, $this->administrationIf->get_school_year () );
		
		$j = 0;
		$email_list = array ();
		for($i = 0; $i < count ( $info ); $i ++) {
			$stu_email = $this->personalInfoIf->get_email ( $this->studentIf->get_personal_info_id ( $info [$i] ['student_id'] ) );
			if (! empty ( $stu_email )) {
				$email_list [$j ++] = $stu_email;
			}
			
			$par_email = $this->parentIf->get_email ( $this->studentIf->get_parent_id ( $info [$i] ['student_id'] ) );
			if (! empty ( $par_email )) {
				$email_list [$j ++] = $par_email;
			}
		}
		return $email_list;
	}
	
	function format_and_email_old($to, $subject, $message) {
		$body = "<html>\n";
		$body .= "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px;\">\n";
		$body .= $message;
		$body .= "</body>\n";
		$body .= "</html>\n";
		
		$headers = "From: wis@icsgv.com\r\n";
		$headers .= "Reply-To: wis@icsgv.com\r\n";
		$headers .= "Return-Path: wis@icsgv.com\r\n";
		$headers .= "X-Mailer: Drupal\n";
		$headers .= 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		mail ( $to, $subject, $body, $headers );
		
		// echo "Mail " . $subject . " ; " . $to . "<BR>";
	}
	

	function format_and_email($to, $subject, $message) {
		if ($to === "n/a") {
			return;
		}
		
		$crlf = "\n";
		$hdrs = array (
				'From' => 'wis@icsgv.com',
				'Subject' => $subject 
		);
		
		$mime = new Mail_mime ( $crlf );
		
		$html_body = "<html>\n";
		$html_body .= "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px;\">\n";
		$html_body .= $message;
		$html_body .= "</body>\n";
		$html_body .= "</html>\n";
		
		// $mime->setTXTBody($text);
		// FIXME: Remove temp2
		$mime->setHTMLBody ( $html_body );
		
		// print "ERR: " . $_FILES['uploaded_file']['error'] . " To: " . $to . " SUB: " . $subject . " FILE: " . $_FILES['uploaded_file']['name'] . "TMP: " . $_FILES['uploaded_file']['tmp_name'] . " CONTENT: " . $content . "<BR>";
		
		if (file_exists ( $_FILES ['uploaded_file'] ['tmp_name'] )) {
			$ext = pathinfo ( $_FILES ['uploaded_file'] ['name'], PATHINFO_EXTENSION );
			
			if ($ext === 'doc' || $ext === 'docx') {
				$content = "'application/vnd.openxmlformats-officedocument.wordprocessingml.document'";
			} elseif ($ext === 'pdf') {
				$content = "'application/pdf'";
			} elseif ($ext === 'xls' || $ext === 'xlsx' || $ext === 'csv') {
				$content = "'application/vnd.ms-excel'";
			} elseif ($ext === 'gif') {
				$content = "'image/gif'";
			} elseif ($ext === 'jpeg') {
				$content = "'image/jpeg'";
			} elseif ($ext === 'png') {
				$content = "'image/png'";
			} else {
				$content = "'text/plain'";
			}
			
			// print "To: " . $to . " SUB: " . $subject . " FILE: " . $_FILES['uploaded_file']['name'] . " CONTENT: " . $content . "<BR>";
			
			$mime->addAttachment ( $_FILES ['uploaded_file'] ['tmp_name'], $content, $_FILES ['uploaded_file'] ['name'] );
		}
		
		// do not ever try to call these lines in reverse order
		$body = $mime->get ();
		$hdrs = $mime->headers ( $hdrs );
		
		$mail = & Mail::factory ( 'mail', '-f wis@icsgv.com' ); // add the fifth parameter for the PHP mail() function
		$mail->send ( $to, $hdrs, $body );
	}

	
	function gather_email_message($grade, $section) {
		include_once ("wis_header2.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		if ($grade === 'ALL') {
			print '<H3> Send Email to ALL WIS Parents </H3>';
		} else {
			print '<H3> Send Email to ' . $grade . '-' . $section . ' Parents </H3>';
		}
		print "<BR>";
		
		print "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp SUBJECT: <input type=text name=email_subject  size=40 maxlength=40>";
		print "<BR>";
		print "<BR>";
		
		print "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <label for='uploaded_file'>File Upload?</label>";
		print "<input type='file' name='uploaded_file'>";
		print "<BR>";
		print "<BR>";
		
		print "&nbsp&nbsp&nbsp Write email content below <BR>";
		print "<BR>";
		
		print "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <textarea name='email_text'rows='20' cols='50'  >\n";
		print "</textarea>";
		
		print "<br>";
		print "<br>";
		
		// print '<Input type=Submit name = "Submit" value = "Send email">';
		print "<input type=hidden name='grade' value='" . $grade . "'>";
		print "<input type=hidden name='section' value='" . $section . "'>";
		setSubmitValue ( "Send email" );
		
		print "</FIELDSET>";
		print '</div>';
		
		wis_footer ( TRUE );
	}
	
	function send_email() {
		$message = $this->get_email_header () . str_replace ( "\r", '<br>', $_REQUEST ['email_text'] );
		
		if (empty ( $_REQUEST ['section'] )) {
			$email_list = $this->get_email_list ( $_REQUEST ['grade'] );
		} else {
			$email_list = $this->get_email_list ( $_REQUEST ['grade'], $_REQUEST ['section'] );
		}
		
		foreach ( $email_list as $to ) {
			$this->format_and_email($to, $_REQUEST['email_subject'], $message);
			// print "Email recipient " . $to . "<BR>";
		}
		wis_log_event ( "Send email to " . $_REQUEST ['grade'] );
	}
}

?> 
