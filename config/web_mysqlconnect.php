<?php 
/***
 * Database Connection
 * Author: Aarti Ojha
 * Date: 11-01-2024
 * This file is responsible for establishing database connections and retrieving theme settings.
 * 
 * Please do not modify this file without permission.
 **/
// Check if session is already started using session_status()
// Report all PHP errors

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

include("common-constants.php");  // Include file containing constants


// Check if a custom database is set in the session and use it if available
if (!empty($_SESSION['database'])) {
    $dbname = $_SESSION['database'];
    $db = $_SESSION['database'];
	$smsdbname = $_SESSION['database'];
	$logged_name = $_SESSION['logged']; // Assign session logged user name
	$companyid = $_SESSION['companyid']; // Assign session company ID
	$userid = $_SESSION['userid']; // Assign session user ID
	$branch = $_SESSION['branch']; // Assign session branch
	$groupid = $_SESSION['user_group']; // Assign session user group
	$company_name = $_SESSION['company_name']; // Assign session company name
}

/* end */

$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass); // Establish database connection
mysqli_select_db($link, $dbname); // Select database


//Check if database connection failed
if(!$link){
    echo "Database connection error: " . mysqli_connect_error();
}

/************ Query to fetch theme settings from crm_setting table ************/

$qu = "SELECT * FROM crm_setting WHERE status_theme='1'";
$querytheme = mysqli_query($link, $qu); // Execute the query
$restheme = mysqli_fetch_array($querytheme); // Fetch theme settings
$dbtheme = $restheme['theme_name']; // Get theme name
$dbheadlogo = $restheme['header_logo']; // Get header logo
$dbfootlogo = $restheme['footer_logo']; // Get footer logo
$dblandinglogo = $restheme['landing_logo']; // Get landing logo

/***************** Establish connection for web chat box *********************/
// $conn_chat = mysqli_connect($configdbhost, $configdbuser, $configdbpass); // Connect to database server
// mysqli_select_db($conn_chat, db_chat); // Select web chat database

?>
