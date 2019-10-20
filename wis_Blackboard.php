<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_TeacherIf.php';
include_once 'wis_RegistrationIf.php';
include_once 'wis_AdministrationIf.php';
include_once 'wis_BlackboardIf.php';

class Blackboard {
	private $mysqli_h;
	private $teacherIf;
	private $registrationIf;
	private $blackboardIf;
	private $target_dir;
	private $administrationIf;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	public function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->teacherIf = new TeacherIf ( $mysqli_h );
		$this->registrationIf = new RegistrationIf ( $mysqli_h );
		$this->blackboardIf = new BlackboardIf ( $mysqli_h );
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
		
		$this->target_dir = "uploads/";
	}
	
	public function file_upload($teacher_id) {
		include ("wis_header2.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print "Press 'Browse' to select file to be uploaded and enter 'Submit' to upload file to the black-board <BR><BR>";
		print "<table>";
		print "<tr>";
		print '<td>File Title </td><td><input type="text" name="file_title" size="30" maxlength="45"> &nbsp&nbsp&nbsp</td>';
		print '<td><input type="file" name="file_name" id="fileToUpload"></td>';
		print "</tr><tr>";
		print '<td>Comments </td><td><input type="text" name="file_comments" size="45" maxlength="65"></td>';
		print "</tr>";
		print "<tr>";
		print '<td>File visible to students </td><td><input type="checkbox" name="file_visible" ></td>';
		print "</tr>";
		print "</table>";
		
		print "<input type=hidden name='teacher_id' value='" . $teacher_id . "'>";
		
		print '<P>';
		$files = array_slice ( scandir ( 'uploads/' ), 2 );
		
		print '<BR>';
		print "Black Board files: <BR><BR>";
		
		$info_t = $this->teacherIf->get_record ( $teacher_id );
		$info_b = $this->blackboardIf->get_file_name_title ( $info_t ['grade'], $info_t ['section'] );
		
		$cnt = 1;
		print "<table>";
		print "<tr><th>Number</th><th>Title</th><th>Comments</th></tr>";
		for($i = 0; $i < count ( $files ); $i ++) {
			for($j = 0; $j < count ( $info_b ); $j ++) {
				if ($info_b [$j] ['file_name'] === $files [$i]) {
					print "<tr>";
					print "<td>" . $cnt ++ . "</td>";
					print '<td><a href="uploads/' . $files [$i] . '">';
					print $info_b [$j] ['file_title'] . "</a></td>";
					print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $info_b [$j] ['comments'] . " </td>";
					print "</tr>";
				}
			}
		}
		print "</table>";
		print '</P>';
		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "blackboardFileUpload" );
		wis_footer ( TRUE );
	}
	
	public function view_all_black_board() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<P>';
		$files = array_slice ( scandir ( 'uploads/' ), 2 );
		
		print "Black Board files: <BR><BR>";
		
		$info_b = $this->blackboardIf->get_all_files ();
		
		usort ( $info_b, array (
				$this,
				'gradeSort' 
		) );
		
		print "<table>";
		print "<tr><th>Number</th><th>Grade</th><th>Section</th><th>Title</th><th>Comments</th></tr>";
		
		$cnt = 1;
		for($i = 0; $i < count ( $files ); $i ++) {
			if ($info_b) {
				for($j = 0; $j < count ( $info_b ); $j ++) {
					if ($info_b [$j] ['file_name'] === $files [$i]) {
						print "<tr>";
						print "<td>" . $cnt ++ . "</td>";
						print "<td>" . $info_b [$j] ['grade'] . "</td>";
						print "<td>" . $info_b [$j] ['section'] . "</td>";
						print '<td><a href="uploads/' . $files [$i] . '">';
						print $info_b [$j] ['file_title'] . "</a></td>";
						print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $info_b [$j] ['comments'] . " </td>";
						print "</tr>";
					}
				}
			} else {
				// print $cnt++ . '. ' . $info_b[$j]['file_title'] . "--------" . $files[$i] . "<BR>";
			}
		}
		print "</table>";
		
		print '</P>';
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	public function view_black_board($entity, $entity_id) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<P>';
		$files = array_slice ( scandir ( 'uploads/' ), 2 );
		
		print "Black Board files: <BR><BR>";
		
		$info = NULL;
		$info_b = NULL;
		
		if ($entity == WIS_TEACHER) {
			$info = $this->teacherIf->get_record ( $entity_id );
			$grade = $info ['grade'];
			$section = $info ['section'];
		} else if ($entity == WIS_STUDENT) {
			$info = $this->registrationIf->get_record ( $entity_id, $this->administrationIf->get_school_year () );
			$grade = $info ['wis_grade'];
			$section = $info ['section'];
		}
		
		if ($info) {
			$info_b = $this->blackboardIf->get_file_name_title ( $grade, $section );
		}
		
		print "<table>";
		print "<tr><th>Number</th><th>Title</th><th>Comments</th><th>Student Visibility</tr>";
		
		$cnt = 1;
		for($i = 0; $i < count ( $files ); $i ++) {
			if ($info_b) {
				for($j = 0; $j < count ( $info_b ); $j ++) {
					if ($info_b [$j] ['file_name'] === $files [$i]) {
						print "<tr>";
						print "<td>" . $cnt ++ . "</td>";
						print '<td><a href="uploads/' . $files [$i] . '">';
						print $info_b [$j] ['file_title'] . "</a></td>";
						print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $info_b [$j] ['comments'] . " </td>";
						if ($info_b [$j] ['student_visible']) {
							print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Yes </td>";
						} else {
							print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp No </td>";
						}
						print "</tr>";
					}
				}
			} else {
				// print $cnt++ . '. ' . $info_b[$j]['file_title'] . "--------" . $files[$i] . "<BR>";
			}
		}
		print "</table>";
		
		print '</P>';
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	public function delete_black_board_files($teacher_id) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<BR>';
		print "Select files to be deleted and then press Submit. WARNNING: Files will be permanently deleted<BR><BR>";
		
		print '<P>';
		$files = array_slice ( scandir ( 'uploads/' ), 2 );
		
		print '<BR>';
		print "Black Board files: <BR><BR>";
		
		$info_t = $this->teacherIf->get_record ( $teacher_id );
		$info_b = $this->blackboardIf->get_file_name_title ( $info_t ['grade'], $info_t ['section'] );
		print "<input type=hidden name='grade'   value='" . $info_t ['grade'] . "'>";
		print "<input type=hidden name='section' value='" . $info_t ['section'] . "'>";
		
		$cnt = 1;
		$file_cntr = 0;
		print "<table>";
		print "<tr><th>Number</th><th>Delete File </th><th>Title</th><th>File Name<th>Comments</th></tr>";
		for($i = 0; $i < count ( $files ); $i ++) {
			for($j = 0; $j < count ( $info_b ); $j ++) {
				if ($info_b [$j] ['file_name'] === $files [$i]) {
					print "<tr>";
					print "<td style='width:80px; text-align:center;'>" . $cnt ++ . "</td>";
					print '<td style="width:80px; text-align:center;" >' . '<input type=checkbox name="FileDelete_' . $file_cntr ++ . '"> </td>';
					print '<td>' . $info_b [$j] ['file_title'] . "</td>";
					print '<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' . $info_b [$j] ['file_name'] . "</td>";
					print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp " . $info_b [$j] ['comments'] . " </td>";
					print "</tr>";
					print '<input type=hidden name="delete_title[]" value="' . $info_b [$j] ['file_title'] . '">';
				}
			}
		}
		
		print "</table>";
		print '</P>';
		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "blackboardFileDelete" );
		wis_footer ( TRUE );
	}
	
	public function modify_black_board_files($teacher_id) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<BR>';
		print "Make appropriate changes and select files to be modified and then press Submit. <BR><BR>";
		
		print '<P>';
		$files = array_slice ( scandir ( 'uploads/' ), 2 );
		
		print '<BR>';
		print "Black Board files: <BR><BR>";
		
		$info_t = $this->teacherIf->get_record ( $teacher_id );
		$info_b = $this->blackboardIf->get_file_name_title ( $info_t ['grade'], $info_t ['section'] );
		print "<input type=hidden name='grade'   value='" . $info_t ['grade'] . "'>";
		print "<input type=hidden name='section' value='" . $info_t ['section'] . "'>";
		
		$cnt = 1;
		$file_cntr = 0;
		print "<table>";
		print "<tr><th>Number</th><th>Modify File </th><th>Title</th><th>File Name</th><th>Comments</th><th>Student Visible</th></tr>";
		for($i = 0; $i < count ( $files ); $i ++) {
			for($j = 0; $j < count ( $info_b ); $j ++) {
				if ($info_b [$j] ['file_name'] === $files [$i]) {
					print "<tr>";
					print "<td style='width:80px; text-align:center;'>" . $cnt ++ . "</td>";
					print '<td style="width:80px; text-align:center;">' . '<input type=checkbox name="FileMod_' . $file_cntr . '" ></td>';
					print '<td><input type=text name="modify_title[]" value="' . $info_b [$j] ['file_title'] . '"></td>';
					print '<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' . $info_b [$j] ['file_name'] . "</td>";
					print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <input type=text name='modify_comment[]' value='" . $info_b [$j] ['comments'] . "' ></td>";
					print "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <input type=checkbox name='VisibilityMod_" . $file_cntr ++ . "' ";
					if ($info_b [$j] ['student_visible']) {
						print " checked ";
					}
					print " ></td>";
					print "</tr>";
					print '<input type=hidden name="orig_title[]" value="' . $info_b [$j] ['file_title'] . '">';
				}
			}
		}
		
		print "</table>";
		print '</P>';
		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "blackboardFileModify" );
		wis_footer ( TRUE );
	}
	public function enter_file_upload($teacher_id) {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$info = $this->teacherIf->get_record ( $teacher_id );
		$fileParts = pathinfo ( basename ( $_FILES ["file_name"] ["name"] ) );
		
		if (empty ( $info ['section'] )) {
			$true_file_name = $fileParts ['filename'] . '_' . $info ['grade'] . 'A.' . $fileParts ['extension'];
		} else {
			$true_file_name = $fileParts ['filename'] . '_' . $info ['grade'] . $info ['section'] . '.' . $fileParts ['extension'];
		}
		
		$target_file = $this->target_dir . $true_file_name;
		$uploadOk = 1;
		
		// Check if file already exist in the database
		if ($this->blackboardIf->file_exists ( $true_file_name )) {
			echo "Sorry, file already exists in the database. Change the name and then upload<BR>";
			$uploadOk = 0;
		}
		
		// Check if file title already exist in the database
		if ($this->blackboardIf->file_title_exists ( $_REQUEST ['file_title'] )) {
			echo "Sorry, file title already exists in the database. Change the title and then upload<BR>";
			$uploadOk = 0;
		}
		
		// Check if file already exists on the system
		if (file_exists ( $target_file )) {
			echo "Sorry, file already exists on the disk. Change the name and then upload<BR>";
			$uploadOk = 0;
		}
		
		// Check file size
		if ($_FILES ["file_name"] ["size"] > 10000000) {
			echo "Sorry, your file is too large.<BR>";
			$uploadOk = 0;
		}
		
		// Allow certain file formats
		if ($fileParts ['extension'] != "jpg" && $fileParts ['extension'] != "png" && $fileParts ['extension'] != "jpeg" && $fileParts ['extension'] != "gif" && $fileParts ['extension'] != "pdf" && $fileParts ['extension'] != "doc" && $fileParts ['extension'] != "xls" && $fileParts ['extension'] != "docx" && $fileParts ['extension'] != "txt" && $fileParts ['extension'] != "ppt" && $fileParts ['extension'] != "xlsx") {
			echo "Sorry, only jpg, jpeg, png, gif, pdf, doc, txt, ppt, xls files are allowed to be uploaded.<BR>";
			$uploadOk = 0;
		}
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 1) {
			if (move_uploaded_file ( $_FILES ["file_name"] ["tmp_name"], $target_file )) {
				echo "The file " . $true_file_name . " has been uploaded.";
				
				// Save the name in the database
				$trans ['file_name'] = $true_file_name;
				$trans ['file_title'] = $_REQUEST ['file_title'];
				$trans ['grade'] = $info ['grade'];
				if (empty ( $info ['section'] )) {
					$trans ['section'] = 'A';
				} else {
					$trans ['section'] = $info ['section'];
				}
				if (! empty ( $_REQUEST ['file_comments'] )) {
					$trans ['comments'] = $_REQUEST ['file_comments'];
				}
				if (isset ( $_REQUEST ['file_visible'] )) {
					$trans ['file_visible'] = 1;
				} else {
					$trans ['file_visible'] = 0;
				}
				
				$this->blackboardIf->insert_record ( $trans );
			} else {
				echo "Sorry, there was an error uploading your file.";
			}
		}
		
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	public function enter_file_delete() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$firstDelete = false;
		
		$delete_titles = $_REQUEST ['delete_title'];
		
		for($i = 0; $i < count ( $delete_titles ); $i ++) {
			$file_delete = 'FileDelete_' . $i;
			if (isset ( $_REQUEST [$file_delete] )) {
				if (! $firstDelete) {
					print "Following files are deleted <BR>";
					$firstDelete = true;
				}
				$this->blackboardIf->delete_file ( $_REQUEST ['grade'], $_REQUEST ['section'], $delete_titles [$i] );
				echo "File deleted : " . $delete_titles [$i] . "<br />";
			}
		}
		if (! $firstDelete) {
			print "No files are deleted <BR>";
		}
		
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	public function enter_file_modify() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$firstModify = false;
		
		$orig_titles = $_REQUEST ['orig_title'];
		
		for($i = 0; $i < count ( $orig_titles ); $i ++) {
			$file_mod = 'FileMod_' . $i;
			$file_vis = 'VisibilityMod_' . $i;
			if (isset ( $_REQUEST [$file_mod] )) {
				if (! $firstModify) {
					print "Following files are modified <BR>";
					$firstModify = true;
				}
				if (isset ( $_REQUEST [$file_vis] )) {
					$vis = 1;
				} else {
					$vis = 0;
				}
				$this->blackboardIf->update_file ( $_REQUEST ['grade'], $_REQUEST ['section'], $orig_titles [$i], $_REQUEST ['modify_title'] [$i], $_REQUEST ['modify_comment'] [$i], $vis );
				echo "File modified : " . $orig_titles [$i] . "<br>";
			}
		}
		if (! $firstModify) {
			print "No files are modified <BR>";
		}
		
		print "</FIELDSET>";
		print "</div>";
		
		wis_footer ( FALSE );
	}
	
	/* ---------- PRIVATE FUNCTIONS ---------------- */
	private function gradeSort($a, $b) {
		if ($cmp = strnatcasecmp ( $a ['grade'], $b ['grade'] ))
			return $cmp;
		// return strnatcasecmp($a['first_name'], $b['first_name']);
		// return strcmp($a['last_name'], $b['last_name']);
	}
}

?>
