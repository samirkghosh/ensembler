<?php
/*
Document: Voice Mails
Author: Ritu Modi
Date: 22-03-2024
This form displays voice mail records with the ability to filter by start and end datetime and read/unread status.
*/

include("../../config/web_mysqlconnect.php"); // database file include
$startdatetime = isset($_POST['startdatetime']) ? date("Y-m-d 00:00:00",strtotime($_POST['startdatetime'])) : date("Y-m-d 00:00:00");
$enddatetime = isset($_POST['enddatetime']) ? date("Y-m-d 23:59:59",strtotime($_POST['enddatetime'])) : date("Y-m-d 23:59:59");

$voice = mysqli_fetch_assoc(mysqli_query($link,"SELECT count(*) as total from asterisk.tbl_cc_voicemails where callerid!='' AND flag1='0' AND voicemailtime BETWEEN '$startdatetime' AND '$enddatetime' "));
echo $voice_mail = $voice['total'];
?>