<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once "wis_util.php";
include_once 'wis_EventIf.php';
include_once 'wis_AdministrationIf.php';

class Calendar {
	
	private $eventIf;
	private $administrationIf;
	private $mysqli_h;
	
	/* ---------- PUBLIC FUNCTIONS ---------------- */
	function __construct($mysqli_h) {
		$this->mysqli_h = $mysqli_h;
		$this->eventIf = new EventIf ( $mysqli_h );
		$this->administrationIf = new AdministrationIf ( $mysqli_h );
	}
	
	public function view_calendar() {
		$submitBut = FALSE;
		
		if (isset ( $_SESSION ['access_privilege'] ) && $_SESSION ['access_privilege'] == WIS_STAFF) {
			$submitBut = TRUE;
		}
		
		include ("wis_header.php");
		
		wis_main_menu ( $this->mysqli_h, TRUE );
		
		print '<div id="printableArea">';
		
		print '<FIELDSET  style="background-color:' . get_color ( 'BOX' ) . ' ; margin:0px auto;">';
		print '<LEGEND style="font-size: 20px"></LEGEND>';
		
		list ( $syear, $snyear ) = $this->administrationIf->get_school_year ();
		if ($syear == 0) {
			return;
		}
		
		print "<P style='text-align: center';><B>Calendar - School Year " . $syear . "-" . $snyear . "</B><BR> ";
		print "<B> ICSGV Weekend Islamic School</B></P> ";
		
		print "<table id='cal_table' border cellpadding=3 >";
		
		print "<tr>";
		print "<th>Month</th> ";
		print "<th colspan='5'>Date - Event - Time </th> ";
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>September " . $syear . " </td>";
		
		$result = $this->get_monthly_events ( "sep" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>October " . $syear . " </td>";
		
		$result = $this->get_monthly_events ( "oct" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>November " . $syear . " </td>";
		
		$result = $this->get_monthly_events ( "nov" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>December " . $syear . " </td>";
		
		$result = $this->get_monthly_events ( "dec" );
		print "</tr>";
		
		print "<tr>";
		
		print "<td id='cal_td'>January " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "jan" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>February " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "feb" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>March " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "mar" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>April " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "apr" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>May " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "may" );
		print "</tr>";
		
		print "<tr>";
		print "<td id='cal_td'>June " . $snyear . " </td>";
		
		$result = $this->get_monthly_events ( "jun" );
		print "</tr>";
		
		print "</table>";
		
		print "</FIELDSET>";
		print '</div>';
		
		setSubmitValue ( "addEvents" );
		
		wis_footer ( $submitBut );
	}
	
	public function add_new_events() {
		$month [0] = 'sep';
		$month [1] = 'oct';
		$month [2] = 'nov';
		$month [3] = 'dec';
		$month [4] = 'jan';
		$month [5] = 'feb';
		$month [6] = 'mar';
		$month [7] = 'apr';
		$month [8] = 'may';
		$month [9] = 'jun';
		
		for($i = 0; $i < 10; $i ++) {
			for($j = 1; $j <= 5; $j ++) {
				$date = $month [$i] . "_dat_" . $j;
				$event = $month [$i] . "_evt_" . $j;
				$ev_time = $month [$i] . "_tim_" . $j;
				
				$eventIf->update_record ( $_REQUEST [$date], $_REQUEST [$event], $_REQUEST [$ev_time], $month [$i], $j );
			}
		}
	}
	
	/* ---------- PRIVATE FUNCTIONS ---------------- */
	private function get_monthly_events($month) {
		$info = $this->eventIf->get_record ( $month );
		
		for($i = 0; $i < count ( $info ); $i ++) {
			
			print "<td><input type=text name='" . $month . "_dat_" . $i . "' ";
			if (empty ( $info [$i] ['date'] ) && $_SESSION ['access_privilege'] == WIS_STAFF) {
				print " class='datepicker2' ";
			} else {
				print " value = '" . $info [$i] ['date'] . "' ";
			}
			print " size=14 maxlength=15 ";
			if ($_SESSION ['access_privilege'] != WIS_STAFF) {
				print " readonly='readonly'";
			}
			print " >";
			print "<BR><input type=text name='" . $month . "_evt_" . $i . "' ";
			if (! empty ( $info [$i] ['event_desc'] )) {
				print " value = '" . $info [$i] ['event_desc'] . "' ";
			}
			print " size=15 maxlength=45 ";
			if ($_SESSION ['access_privilege'] != WIS_STAFF) {
				print " readonly='readonly'";
			}
			print " >";
			print "<BR><input type=text style='font-size: 10px' name='" . $month . "_tim_" . $i . "' ";
			if (! empty ( $info [$i] ['time'] )) {
				print " value = '" . $info [$i] ['time'] . "' ";
			}
			print " size=15 maxlength=30 ";
			if ($_SESSION ['access_privilege'] != WIS_STAFF) {
				print " readonly='readonly'";
			}
			print " >";
			print "</td>";
			
			// print "<BR><input type=text name='sep_t_" . $i . "' style='font-size: 10px' size=15></td>";
		}
	}
}

?>
