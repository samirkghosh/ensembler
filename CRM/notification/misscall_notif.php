<?php
/***
Document: Misscall
Author: Ritu Modi
Date: 22-03-2024
This form displays Misscall mail records with the ability to filter by start and end datetime and read/unread status.
**/

include("../../config/web_mysqlconnect.php"); // database file include

$startdatetime = isset($_POST['startdatetime']) ? date("Y-m-d 00:00:00",strtotime($_POST['startdatetime'])) : date("Y-m-d 00:00:00");
$enddatetime = isset($_POST['enddatetime']) ? date("Y-m-d 23:59:59",strtotime($_POST['enddatetime'])) : date("Y-m-d 23:59:59");

$missed = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS total from asterisk.autodial_closer_log WHERE call_date >='$startdatetime' AND call_date <= '$enddatetime' AND status IN ('AGENT DROP','DROP') AND `call_back` IN 0"));

//echo "SELECT COUNT(*) AS total from asterisk.tbl_cdrlog WHERE d_StartTime >='$startdatetime' AND d_EndTime <= '$enddatetime' AND (status='NO ANSWER' OR status='MISSED CALL') AND missedcallcause ='CALLER DISCONNECTED IN QUEUE' ";

echo $missed_call = $missed['total'];
 



?>