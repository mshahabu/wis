<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once 'wis_util.php';
include_once 'wis_AdministrationIf.php';

class Administration {

	private $administrationIf;
	private $mysqli_h;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
	}
	
	public function setup_school_days($si) {
	
		include ("wis_header.php");
	
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
	
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		print "<H4> Setup School Days " . $this->administrationIf->get_school_year() . " </H4>";
		
		$this->days_table(0);
		print "<BR>";
		$this->days_table(15);
		print "<BR>";
		$this->days_table(30);

		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "recordSchoolDays" );
		
		wis_footer ( TRUE );
	}
	
	public function record_school_days() {
		
		$days = array();
		for($i = 1; $i <= 45; $i++) {
			array_push($days, $_REQUEST['date_' . $i]);
			//print "DAY: " . $_REQUEST['date_' . $i] . "<BR>";
		}

		$array_string = $this->mysqli_h->real_escape_string(serialize($days));
		
		$id_count = $this->administrationIf->get_id_row_count ();
		
		if ($id_count == 1) {
			$this->administrationIf->update_school_days($array_string);
		} else {
			die("Something is wrong, contact program administrator");
		}
		
		$rv_array_string = $this->administrationIf->get_school_days();
		
		$array= unserialize($rv_array_string);
		//print_r($array);
	}
	
	private function days_table($si) {
		$start = 1 + $si;
		$stop = 15 + $si;
		
		print "<table class=sample>";
		
		print "<col width='45'>";
		print "<tr>";
		//print "<td> </td>";
		// print "<th style='border: 1px single black; background-color: #CCCC99; color :#330000;'>Student ID </th>";
		for($i = $start; $i <= $stop; $i++) {
			print "<th>" . $i . "</th>";
		}
		//print "<td> </td>";
		print "</tr>";
		print "<tr>";
		//print "<td> </td>";
		//print "<td>&nbsp</td>";
		
		$rv_array_string = $this->administrationIf->get_school_days();
		$days_array= unserialize($rv_array_string);
		
		for($i = $start; $i <= $stop; $i++) {
			$val = $days_array[($i-1)];
			print "<td><input type=text class='datepicker3' name='date_" . $i . "' ";
			//print "<td><input type=text name=date_'" . $i . "' ";
			if (empty($days_array[($i-1)])) {
				print " placeholder='mm/dd' ";
			} else {
				print " value='" . $val . "' ";
			}
			print " style='font-size:10px;' size=5 maxlength=5 ></td>";
		}
		//print "<td> </td>";
		print "</tr>";

		print "</table>";
	
	}
	
	function allocate_fees() {
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, FALSE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		$info = $this->administrationIf->get_tution_discounts ();
		
		print "<table>";
		
		print "<tr>";
		print "<td class='normal1'>Tution Fee </td>";
		print "<td>$<input type=text name='tution_fee' ";
		if (isset ( $info ['tution_fee'] )) {
			print " value=" . $info ['tution_fee'];
		}
		print " size=5 maxlength=7 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Sibling Discount </td>";
		print "<td>$<input type=text name='sibling_discount' ";
		if (isset ( $info ['sibling_discount'] )) {
			print " value=" . $info ['sibling_discount'];
		}
		print " size=5 maxlength=7 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Member Discount </td>";
		print "<td>$<input type=text name='member_discount' ";
		if (isset ( $info ['icsgv_mem_discount'] )) {
			print " value=" . $info ['icsgv_mem_discount'];
		}
		print " size=5 maxlength=7 ></td>";
		print "</tr>";
		print "<tr>";
		print "<td class='normal1'>Payment plan fee </td>";
		print "<td>$<input type=text name='pay_plan_fee' ";
		if (isset ( $info ['payment_plan_fee'] )) {
			print " value=" . $info ['payment_plan_fee'];
		}
		print " size=5 maxlength=7 ></td>";
		print "</tr>";
		
		print "</table>";
		
		print "</FIELDSET>";
		print "</div>";
		
		setSubmitValue ( "setupFees" );
		
		wis_footer ( TRUE );
	}
	function enter_fee_allocation() {
		$id_row_count = $this->administrationIf->get_id_row_count ();
		
		$this->administrationIf->set_tution_fee ( $_REQUEST ['tution_fee'] );
		$this->administrationIf->set_sibling_discount ( $_REQUEST ['sibling_discount'] );
		$this->administrationIf->set_icsgv_mem_discount ( $_REQUEST ['member_discount'] );
		$this->administrationIf->set_payment_plan_fee ( $_REQUEST ['pay_plan_fee'] );
		
		if ($id_row_count > 1 || $id_row_count == 0) {
			print '<label style="color:red;"> Administrator, please setup the school year first </label><BR>';
			return;
		}
	}
}

?>
