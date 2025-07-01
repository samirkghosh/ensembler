<?php
// Author: Ritu Modi
// Date: 22-03-2024
//This form displays voice mail records with the ability to filter by start and end datetime and read/unread status.

include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// Include necessary functions
include("../web_function.php");
  
// Function to retrieve complaint category based on ID
function comp_cat($val){
    global $db, $link;
    // Query to fetch category from database based on ID
    $query = mysqli_query($link, "SELECT category FROM $db.tbl_complaint_category WHERE id='$val'");
    // Fetch the result row
    $row = mysqli_fetch_assoc($query);
    // Return the category
    return $row['category'];
}
// Function to retrieve location (building type) based on ID
function location($val){
    global $db, $link;
    // Query to fetch building type from database based on ID
    $query = mysqli_query($link, "SELECT building_type FROM $db.tbl_complaint_type WHERE id='$val'");
    // Fetch the result row
    $row = mysqli_fetch_assoc($query);
    // Return the building type
    return $row['building_type'];
}

// Function to retrieve sub-complaint type based on ID
function subcomp($val)
{
    global $db, $link;
    // Query to fetch sub-complaint type from database based on ID
    $query = mysqli_query($link, "SELECT sub_type FROM $db.tbl_sub_complaint_type WHERE id='$val'");
    // Fetch the result row
    $row = mysqli_fetch_assoc($query);
    // Return the sub-complaint type
    return $row['sub_type'];
}
// Function to retrieve mode (source) based on ID
function mode($val)
{
    global $db, $link;
    // Query to fetch source from database based on ID
    $query = mysqli_query($link, "SELECT source FROM $db.web_source WHERE id='$val'");
    // Fetch the result row
    $row = mysqli_fetch_assoc($query);
    // Return the source
    return $row['source'];
}
//Fetches complaint types from the database.
function fetchComplaintTypes() {
    global $db, $link;
    // Execute the query to fetch complaint types
    $query = mysqli_query($link, "SELECT * FROM $db.tbl_complaint_type WHERE status=1 ORDER BY id ASC");
    // Return the result set of the query
    return $query;
}
// Fetches complaint categories from the database.
// This function retrieves complaint categories from the specified table in the database where the status is 1, ordered by ID in ascending order.
function fetchComplaintCategories() {
    // Access global variables for database connection and name
    global $db, $link;
    // Execute the query to fetch complaint categories
    $query = mysqli_query($link, "SELECT * FROM $db.tbl_complaint_category WHERE status=1 ORDER BY id ASC");
    // Return the result set of the query
    return $query;
}
//Fetches web sources from the database.
//This function retrieves web sources from the specified table in the database where the status is 1, ordered by ID in ascending order.
function fetchWebSources() {
    // Access global variables for database connection and name
    global $db, $link;
    // Execute the query to fetch web sources
    $query = mysqli_query($link, "SELECT * FROM $db.web_source WHERE status=1 ORDER BY id ASC");
    return $query;
}
/**
 * Generates a processed voice file.
 * This function takes a SmartFileName as input and generates a processed voice file.
 * It searches for the SmartFileName in the specified directory and checks if the file exists.
 * If the file exists, it converts the file to a specified format using the Sox utility.
 * The processed file is then stored in a temporary directory. 
 * ***/
function SmartFileName_voice($SmartFileName){       
    // Define the path to the original voice file
    $path='/var/www/html/voicemail/IVR/DROP/'.$SmartFileName.'.wav';
    $pathWithoutExtention='/var/www/html/voicemail/IVR/DROP/'.$SmartFileName;
    // Check if the file exists with both .wav and .WAV extensions
    if (file_exists($path)) {
        $recFile= $SmartFileName.".wav";
        $pathWithoutExtention=$pathWithoutExtention.".wav";
    } else {
        $pathWithoutExtention=$pathWithoutExtention.".WAV";
        $recFile= $SmartFileName.".WAV";
    }
    // Replace slashes in the filename
    $recFile = str_replace('/','_', $recFile);
    // Define the destination path for the processed file
    $destFile = "../tmp/" . $recFile;
    // Define the command to convert the file using Sox
    $cmd = "/usr/bin/sox $pathWithoutExtention -b 8 $destFile";
    // Execute the command to convert the file
    system($cmd);
    // Return the path to the processed voice file
    return $destFile;
}

// Function to fetch voicemail reports based on specified criteria
function view_voicemailreport(){
  global $db_asterisk,$link;
    if(!empty($_REQUEST['sttartdatetime'])){ $startdatetime=$_REQUEST['sttartdatetime']; }
    // else{  $startdatetime=$startdatetime; }
    if(!empty($_REQUEST['enddatetime'])){ $enddatetime=$_REQUEST['enddatetime']; }
    // else{  $enddatetime=$enddatetime; }
    $condition="";
    // echo $status;
    // if($status!=""){
    // $condition .='AND flag1 ='."'".$status."'";
    // }
    if($startdatetime!='' && $enddatetime!='')
    { 
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .="AND voicemailtime BETWEEN '$from' AND '$to'  "; 
    }
    $voice_query="SELECT * from $db_asterisk.tbl_cc_voicemails where callerid!='' $condition order by id DESC";
    // echo  $voice_query;
    //echo $voice_query;
    $query=mysqli_query($link,$voice_query) or die("Error in query" . mysqli_error($link));
    return $query;
}
// Date: 03-04-2024
// Function to fetch missed calls based on specified criteria
function fetchMissedCalls() {
    global $db_asterisk,$link;
    $startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");       
    $enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");
    //added code for handling the  status and phone number in the misscall report [vastvikta][16-12-2024]
    $status = ($_REQUEST['status']!='') ? ($_REQUEST['status']) :'';   
    $phone_number = ($_REQUEST['phone_number']!='') ? ($_REQUEST['phone_number']) :'';   

    if($startdatetime!='' && $enddatetime!=''){ 
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
    }
    $condition= '';
    // if($status!=""){
    //     $condition .=" and `status`='$status' ";
    // }

    if($phone_number!=""){

     $condition .=" and `phone_number` like '%".$phone_number."%'";
    }
    // Construct the query to fetch missed calls
    $query = "SELECT `call_date`, `phone_number`,`user`, `status`, `did_name`,`filename`,count(phone_number) AS misscalls FROM  $db_asterisk.`autodial_closer_log`  WHERE `call_date` >= '$from' AND `call_date` <= '$to' AND `phone_number`<>'' AND `status` IN ('AGENT DROP','DROP') AND `call_back` IN (0) $condition GROUP BY `phone_number` ORDER BY `campaign_id` ASC";
    // echo $query; die;
    $result = mysqli_query($link, $query) or die("Error in query" . mysqli_error($link));   
    return $result; // Return the result set
}
function functionusername($phone){
	global $db, $link;
	
	$sql_name = "SELECT * FROM $db.web_accounts WHERE phone = '$phone'";
	
	$result_new = mysqli_query($link, $sql_name);
	if (!$result_new) {
		// echo "MySQL Error: " . mysqli_error($link); // Show DB error
	}
	return $result_new;
}
?>