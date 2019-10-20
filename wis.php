<?php
// Developed by Product Line Software (PLS) Inc. 
// Date 8/1/2016
// Version 2.1

include_once "wis_util.php"; 
include_once 'wis_connect.php';

$mysqli_h = wis_connect_to_mysql();
    
wis_main_page($mysqli_h); 

?>
