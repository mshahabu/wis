<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_GradeBookIf.php';
include_once 'wis_StudentIf.php';
include_once 'wis_PersonalInfoIf.php';

class Book {

	private $gradeBookIf;
	private $studentIf;
	private $personalInfoIf;
	private $mysqli_h;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->gradeBookIf = new GradeBookIf ( $mysqli_h );
		$this->studentIf = new StudentIf ( $mysqli_h );
		$this->personalInfoIf = new PersonalInfoIf ( $mysqli_h );
	}
	
	/* ^^^^^^^^^^^^^^^^ METHODS called by MENU ^^^^^^^^^^^^^^^^^^^^^^ */
	// Called by wis_menu
	public function add_new_book() {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print "<table>";
		
		print "<tr>";
		print "<td class='normal1'>Book Name </td>";
		print "<td><input type=text name='book_name' size=30 maxlength=40 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Author </td>";
		print "<td><input type=text name='author' size=25 maxlength=40 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Publisher </td>";
		print "<td><input type=text name='publisher' size=30 maxlength=40 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Cost &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp \$ </td>";
		print "<td><input type=text name='dollars' size=3 maxlength=3>";
		print ".";
		print "<input type=text name='cents' size=2 maxlength=2 value='00'></td>";
		print "</tr>";
		
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> Applicable Grades: </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		
		print "<tr> <td> Basic             <input type=checkbox name='grade_BA'> </td></tr>";
		print "<tr> <td> PRE_K             <input type=checkbox name='grade_PK'> </td></tr>";
		print "<tr> <td> KG                <input type=checkbox name='grade_KG'> </td></tr>";
		print "<tr> <td> 1  &nbsp&nbsp&nbsp <input type=checkbox name='grade_1'>  </td></tr>";
		print "<tr> <td> 2  &nbsp&nbsp&nbsp <input type=checkbox name='grade_2'>  </td></tr>";
		print "<tr> <td> 3  &nbsp&nbsp&nbsp <input type=checkbox name='grade_3'>  </td></tr>";
		print "<tr> <td> 4  &nbsp&nbsp&nbsp <input type=checkbox name='grade_4'>  </td></tr>";
		print "<tr> <td> 5  &nbsp&nbsp&nbsp <input type=checkbox name='grade_5'>  </td></tr>";
		print "<tr> <td> 6  &nbsp&nbsp&nbsp <input type=checkbox name='grade_6'>  </td></tr>";
		print "<tr> <td> 7  &nbsp&nbsp&nbsp <input type=checkbox name='grade_7'>  </td></tr>";
		print "<tr> <td> 8  &nbsp&nbsp&nbsp <input type=checkbox name='grade_8'>  </td></tr>";
		print "<tr> <td> YG &nbsp&nbsp&nbsp <input type=checkbox name='grade_YG'>  </td></tr>";
		print "</table>";
		
		print "</FIELDSET>";
		print '</div>';
		
		wis_footer ( TRUE );
		
		setSubmitValue ( "insertBookRecord" );
	}
	
	// Called by wis_menu
	function book_record_update($bid) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$info = $this->gradeBookIf->get_record ( $bid );
		
		list ( $dollars, $cents ) = split ( '[.]', $info ['cost'] );
		
		print "<table>";
		
		print "<tr>";
		print "<td class='normal1'>Book Name </td>";
		print '<td><input type=text name="book_name" value="' . $info ['book_name'] . '" size=30 maxlength=40 ></td>';
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Author </td>";
		print '<td><input type=text name="author" value="' . $info ['author_name'] . '" size=25 maxlength=40 ></td>';
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Publisher </td>";
		print '<td><input type=text name="publisher" value="' . $info ['publisher'] . '" size=30 maxlength=40 ></td>';
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Cost &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp \$ </td>";
		print "<td><input type=text name='dollars' value=" . $dollars . " size=3 maxlength=3>";
		print ".";
		print "<input type=text name='cents' size=2 maxlength=2 value=" . $cents . "></td>";
		print "</tr>";
		
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> Applicable Grades: </td></tr>";
		print "<tr> <td> </td></tr>";
		print "<tr> <td> </td></tr>";
		
		print "<tr><td> Basic <input type=checkbox name='grade_BA'";
		if ($info ['grade'] & WIS_BA) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> PRE_K <input type=checkbox name='grade_PK'";
		if ($info ['grade'] & WIS_PK) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> KG <input type=checkbox name='grade_KG'";
		if ($info ['grade'] & WIS_KG) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 1 &nbsp&nbsp&nbsp <input type=checkbox name='grade_1' ";
		if ($info ['grade'] & WIS_1) {
			print " checked='yes' ";
		}
		print " > </td></tr>";
		print "<tr><td> 2 &nbsp&nbsp&nbsp <input type=checkbox name='grade_2' ";
		if ($info ['grade'] & WIS_2) {
			print " checked='yes' ";
		}
		print " > </td></tr>";
		print "<tr><td> 3 &nbsp&nbsp&nbsp <input type=checkbox name='grade_3' ";
		if ($info ['grade'] & WIS_3) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 4 &nbsp&nbsp&nbsp <input type=checkbox name='grade_4' ";
		if ($info ['grade'] & WIS_4) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 5 &nbsp&nbsp&nbsp <input type=checkbox name='grade_5' ";
		if ($info ['grade'] & WIS_5) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 6 &nbsp&nbsp&nbsp <input type=checkbox name='grade_6' ";
		if ($info ['grade'] & WIS_6) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 7 &nbsp&nbsp&nbsp <input type=checkbox name='grade_7' ";
		if ($info ['grade'] & WIS_7) {
			print " checked='yes' ";
		}
		print " > </td></tr> ";
		print "<tr><td> 8 &nbsp&nbsp&nbsp <input type=checkbox name='grade_8' ";
		if ($info ['grade'] & WIS_8) {
			print " checked='yes' ";
		}
		print " > </td></tr>";
		print "<tr><td> YG &nbsp&nbsp&nbsp <input type=checkbox name='grade_YG' ";
		if ($info ['grade'] & WIS_YG) {
			print " checked='yes' ";
		}
		print " > </td></tr>";
		print "</table>";
		
		print "</FIELDSET>";
		print '</div>';
		
		print "<input type=hidden name='book_id' value='" . $bid . "'>";
		
		wis_footer ( TRUE );
		
		setSubmitValue ( "updateBookRecord" );
	}
	
	// Called by wis_menu
	function book_list($bcl) {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$info = $this->gradeBookIf->get_all_records ();
		usort ( $info, array (
				$this,
				'nameSort' 
		) );
		
		$cnt = 1;
		print "<table border cellpadding=3>";
		// print "<caption> Hello World </caption>";
		print "<caption><B>";
		switch (intval ( $bcl )) {
			case WIS_BA :
				print "Basic ";
				break;
			case WIS_PK :
				print "PRE_K ";
				break;
			case WIS_KG :
				print "KG ";
				break;
			case WIS_1 :
				print "Grade 1 ";
				break;
			case WIS_2 :
				print "Grade 2 ";
				break;
			case WIS_3 :
				print "Grade 3 ";
				break;
			case WIS_4 :
				print "Grade 4 ";
				break;
			case WIS_5 :
				print "Grade 5 ";
				break;
			case WIS_6 :
				print "Grade 6 ";
				break;
			case WIS_7 :
				print "Grade 7 ";
				break;
			case WIS_8 :
				print "Grade 8 ";
				break;
			case WIS_YG :
				print "Youth Group ";
				break;
		}
		print " Book List </B></caption>";
		
		print "<tr>";
		print "<th>Number</th> ";
		print "<th>Book Name</th> ";
		print "<th>Author </th> ";
		print "<th>Publisher </th> ";
		print "<th>Cost</th> ";
		if (intval ( $bcl ) == WIS_ALL) {
			print "<th>Basic</th> ";
			print "<th>PRE_K</th> ";
			print "<th>KG</th> ";
			print "<th>1</th> ";
			print "<th>2</th> ";
			print "<th>3</th> ";
			print "<th>4</th> ";
			print "<th>5</th> ";
			print "<th>6</th> ";
			print "<th>7</th> ";
			print "<th>8</th> ";
			print "<th>YG</th> ";
		}
		print "</tr>";
		
		$bookAssign = FALSE;
		for($i = 0; $i < count ( $info ); $i ++) {
			if ($info [$i] ['grade'] & intval ( $bcl ) || intval ( $bcl ) == WIS_ALL) {
				$bookAssign = TRUE;
				print "<tr>";
				print "<td>" . $cnt ++ . "</td> ";
				print "<td>" . getCell ( $info [$i] ['book_name'] ) . " </td>";
				print "<td>" . getCell ( $info [$i] ['author_name'] ) . " </td>";
				print "<td>" . getCell ( $info [$i] ['publisher'] ) . " </td>";
				print "<td>" . getCell ( $info [$i] ['cost'] ) . " </td>";
				
				if (intval ( $bcl ) == WIS_ALL) {
					$checked = "";
					if ($info [$i] ['grade'] & WIS_BA) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_BA' " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_PK) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_PK' " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_KG) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_KG' " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_1) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_1'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_2) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_2'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_3) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_3'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_4) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_4'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_5) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_5'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_6) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_6'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_7) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_7'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_8) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_8'  " . $checked . " disabled='disabled' </td>";
					
					$checked = "";
					if ($info [$i] ['grade'] & WIS_YG) {
						$checked = "checked='yes'";
					}
					print "<td> <input type=checkbox name='garde_YG'  " . $checked . " disabled='disabled' </td>";
				}
				print "</tr>";
			}
		}
		if ($bookAssign == FALSE) {
			print "<tr>No books are assigned for this grade</tr>";
		}
		print "</table>";
		print '</div>';
		print "</FIELDSET>";
		
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( FALSE );
	}
	
	/* ^^^^^^^^^^^^^^^^ METHODS called by process_request ^^^^^^^^^^^^^^^^^^^^^^ */
	// Called by process_request
	public function insert_book_record() {
		include_once ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		$cost = $_REQUEST ['dollars'] + ($_REQUEST ['cents']) / 100;
		$grade = 0;
		$grade |= (isset ( $_REQUEST ['grade_BA'] )) ? WIS_BA : 0;
		$grade |= (isset ( $_REQUEST ['grade_PK'] )) ? WIS_PK : 0;
		$grade |= (isset ( $_REQUEST ['grade_KG'] )) ? WIS_KG : 0;
		$grade |= (isset ( $_REQUEST ['grade_1'] )) ? WIS_1 : 0;
		$grade |= (isset ( $_REQUEST ['grade_2'] )) ? WIS_2 : 0;
		$grade |= (isset ( $_REQUEST ['grade_3'] )) ? WIS_3 : 0;
		$grade |= (isset ( $_REQUEST ['grade_4'] )) ? WIS_4 : 0;
		$grade |= (isset ( $_REQUEST ['grade_5'] )) ? WIS_5 : 0;
		$grade |= (isset ( $_REQUEST ['grade_6'] )) ? WIS_6 : 0;
		$grade |= (isset ( $_REQUEST ['grade_7'] )) ? WIS_7 : 0;
		$grade |= (isset ( $_REQUEST ['grade_8'] )) ? WIS_8 : 0;
		$grade |= (isset ( $_REQUEST ['grade_YG'] )) ? WIS_YG : 0;
		// print "GRADE is " . $grade . "<BR>";
		
		$this->gradeBookIf->insert_record ( $_REQUEST ['book_name'], $_REQUEST ['author'], $_REQUEST ['publisher'], $cost, $grade );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print '<H4> Book record entered successfully </H4>';
		
		print "</FIELDSET>";
		
		print '</div>';
		
		wis_footer ( FALSE );
	}
	
	// Called by process_request
	public function update_book_record() {
		$cost = $_REQUEST ['dollars'] + ($_REQUEST ['cents']) / 100;
		$grade = 0;
		$grade |= (isset ( $_REQUEST ['grade_BA'] )) ? WIS_BA : 0;
		$grade |= (isset ( $_REQUEST ['grade_PK'] )) ? WIS_PK : 0;
		$grade |= (isset ( $_REQUEST ['grade_KG'] )) ? WIS_KG : 0;
		$grade |= (isset ( $_REQUEST ['grade_1'] )) ? WIS_1 : 0;
		$grade |= (isset ( $_REQUEST ['grade_2'] )) ? WIS_2 : 0;
		$grade |= (isset ( $_REQUEST ['grade_3'] )) ? WIS_3 : 0;
		$grade |= (isset ( $_REQUEST ['grade_4'] )) ? WIS_4 : 0;
		$grade |= (isset ( $_REQUEST ['grade_5'] )) ? WIS_5 : 0;
		$grade |= (isset ( $_REQUEST ['grade_6'] )) ? WIS_6 : 0;
		$grade |= (isset ( $_REQUEST ['grade_7'] )) ? WIS_7 : 0;
		$grade |= (isset ( $_REQUEST ['grade_8'] )) ? WIS_8 : 0;
		$grade |= (isset ( $_REQUEST ['grade_YG'] )) ? WIS_YG : 0;
		
		$this->gradeBookIf->update_record ( $_REQUEST ['book_id'], $_REQUEST ['book_name'], $_REQUEST ['author'], $_REQUEST ['publisher'], $cost, $grade );
		
		wis_main_page ( $this->mysqli_h );
	}
	
	/* ---------- PRIVATE FUNCTIONS ---------------- */
	private function nameSort($a, $b) {
		if ($cmp = strnatcasecmp ( $a ['book_name'], $b ['book_name'] ))
			return $cmp;
		// return strnatcasecmp($a['first_name'], $b['first_name']);
		// return strcmp($a['last_name'], $b['last_name']);
	}
}

?>
