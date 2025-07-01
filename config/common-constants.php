<?php 
/**
 * Database and Other Settings
 * Author: Aarti Ojha
 * Date: 11-01-2024
 * This file is responsible for defining database connection settings and other constants defie.
 * This file is responsible for defining database connection settings
 * and other constants used throughout the project.
 *
 * Redirection: This script ensures that requests to the server's IP address
 * are redirected to the proper domain URL to avoid direct IP access.
 * 
 * WARNING: Please do not modify this file without proper authorization.
 **/

// Redirect users accessing the website via IP address to the domain URL
if ($_SERVER['HTTP_HOST'] === '165.232.183.220') {
    // Use a 301 Moved Permanently status code for SEO purposes
    header("Location: https://alliance-infotech.in" . $_SERVER['REQUEST_URI'], true, 301);
    exit(); // Ensure no further code execution after the redirect
}

// Additional constants and settings can be defined below as required

/********************* Database Configuration ************************/
// $configdbname = "ensembler";   // Master DB Name
$configdbhost = "165.232.183.220";  //Database host Name
$configdbuser = "cron";	//Database user Name
$configdbpass = "All1@nc3@1986!";  //Database password Name

/*************** Define database global variables ********************/

// define('configdbname', $configdbname); // Define database name constant
define('configdbhost', $configdbhost); // Define database host constant
define('configdbuser', $configdbuser); // Define database user constant
define('configdbpass', $configdbpass); // Define database password constant

/*********************** END *****************************************/

/****************************** Database Name ************************/
// $db = 'ensembler';  // for Crm module
$db_asterisk = "asterisk";  //for ivr 
$db_chat = "web_chat"; // for webchat module
// $database_name = 'ensembler';
/* Author : Farhan Akhtar
	purpose: For switching database between multi-tanents Dynamically 
	Last Modified On : 23-12-2024
*/
$db_CampaignTracker = 'CampaignTracker';
define('CampaignTracker', $db_CampaignTracker); // Define database name constant
/* End */

define('db_chat', $db_chat); // Define web chat database constant
// define('db', $db); // Define CRM module database constant
define('db_asterisk', $db_asterisk); // Define IVR database constant
// define('database_name', $database_name); // Define database name constant

$serverpath = '165.232.183.220/ensembler';  // server path use in diff files
$unique_ip = '165.232.183.220';
$customer_feedback = false; 

define('serverpath', $serverpath); // Define server path constant
define('unique_ip', $unique_ip); // Define unique IP constant
define('customer_feedback', $customer_feedback); // Define customer feedback constant


/************************** END **************************************/

/**************************** Website URL ****************************/

$SiteURL = 'https://alliance-infotech.in/ensembler/';   // Define site url used in all project
$Link_Login = 'https://alliance-infotech.in/ensembler/web_login.php';
$uc_ip="165.232.183.220";
$base_path = 'https://alliance-infotech.in/';

define('SiteURL', $SiteURL); // Define site URL constant
define('Link_Login', $Link_Login); // Define login link constant
define('uc_ip', $uc_ip); // Define UC IP constant
define('base_path',$base_path);

$centralspoc = "customercare@essilorindia.com";  // customer care email address
$from_email = "rajdubey.alliance@gmail.com"; // email address for send mail
$from_helpdesk_email="uc2000helpdesk@gmail.com"; // helpdesk email address
define('centralspoc', $centralspoc); // Define central spoc constant
define('from_email', $from_email); // Define email sender address constant
define('from_helpdesk_email', $from_helpdesk_email); // Define helpdesk email constant


/**************************** END ************************************/

/*********************************** Group IDs ***********************/

$Agent_groupId = '070000';            // Agent group id
$Backoffice_groupId = '060000';       // Backoffice Officer
$Superviouser_groupId = '080000';     // CRM Admin (Supervisor)
$Admin_groupId = '0000';              // Master Admin
$NonLogin_groupId = '090000';         // Non Login User
$General_groupId = '050000';          // General Report Officer

// Define group and status IDs
define('Agent_groupId', $Agent_groupId); // Define Agent group ID constant
define('Backoffice_groupId', $Backoffice_groupId); // Define Backoffice group ID constant
define('Superviouser_groupId', $Superviouser_groupId); // Define Supervisor group ID constant
define('Admin_groupId', $Admin_groupId); // Define Admin group ID constant
define('NonLogin_groupId', $NonLogin_groupId); // Define Non Login User group ID constant
define('General_groupId', $General_groupId); // Define General Report Officer group ID constant


/******************** Define status IDs For Ticket Module *******************/

$Pending_status = '1';
$Resolved_status = '8';
$Closed_status = '3';
// status define
define('Pending_status', $Pending_status); // Define pending status constant
define('Resolved_status', $Resolved_status); // Define resolved status constant
define('Closed_status', $Closed_status); // Define closed status constant

/************************************** END *********************************/
define('VERSION', '1.0.0');
date_default_timezone_set("Asia/Kolkata"); 

/******* This code for customer register and send direct mail ********/
define ("PORTNUM", '587');
define ("EMAIL_SERVER", 'smtp.gmail.com'); 
define ("EMAIL_USER", 'rajdubey.alliance@gmail.com');
define ("EMAIL_PWD", 'syepvwaknagahctq');
define ("EMAIL_TLS", '1');
// End

// for analyze_sentiment define 
$email_analyze_sentiment_url = 'http://165.232.183.220:8084/analyze';
define ("email_analyze_sentiment_url", $email_analyze_sentiment_url);

$email_replay_url = 'http://165.232.183.220:8085/generate_gmail_response';
define ("email_replay_url", $email_replay_url);

$process_audio_url = 'http://165.232.183.220:8084/process_audio';
define("process_audio_url", $process_audio_url);


$DocumentType_WhatsappId = '8'; // for whatsapp docId
$DocumentType_MessengerId = '14'; // for messenger docId
$DocumentType_ChatId = '5'; // for chat docId



// for licence type keyword define
$licence_Concurrent = 'CONCURRENT-LICENCE';
$licence_Named = 'NAMED-LICENCE';


//for gmail imap connection
$usernameIMP = 'infotechalliance76@gmail.com';
$passwordIMP = 'iteyntjosxlpxfjg';  // Hardcoded password for testing (not recommended for production)
define ("usernameIMP", $usernameIMP);
define ("passwordIMP", $passwordIMP);

/*
	* Author :: Farhan Akhtar
	* Modified on :: 24-12-2024
	* Purpose :: To Define BaseStorage Path for Multi-Tenant Using Company ID 
*/ 
	// if (!isset($_SESSION['companyid']) || empty($_SESSION['companyid'])) {
	// 	error_log("Company ID is missing in session.");
	// 	die("An error occurred. Please contact support.");
	// }

	$companyId = $_SESSION['companyid'];

	// Define the base path storage
	$BasePath = "unistorage/$companyId";
	define("BasePathStorage", $BasePath);

/* END */

// for all attachment path define [30-10-2024][Aarti]
$whatsapp_path = 'omnichannel_config/whatsapp_attachemnts/';
$messenger_path = 'omnichannel_config/facebook_attachemnts/';
$instagram_path = 'omnichannel_config/instagram_attachemnts/';
$chat_path = 'https://alliance-infotech.in/chatbox/';

// For attachment path define all channel
$webook_messenger_path = '/var/www/html/'.BasePathStorage.'/omnichannel_config/facebook_attachemnts/'; 
$chat_file_history = '/var/www/html/'.BasePathStorage.'/omnichannel_config/chat_file_history/text_data/'; 
$webook_instagram_path = '/var/www/html/'.BasePathStorage.'/omnichannel_config/instagram_attachemnts/'; 
$messenger_path_out = '/var/www/html/'.BasePathStorage.'/omnichannel_config/facebook_attachemnts/'; 
$whatsapp_path_out = '/var/www/html/'.BasePathStorage.'/omnichannel_config/whatsapp_attachemnts/'; 

// bulk campaign csv file path
$bulkcampaign_path = '/var/www/html/'.BasePathStorage.'/bulkcampaign/';
define("bulkcampaign_path", $bulkcampaign_path);

// for define all channel
define ("messenger_path_out", $messenger_path_out);
define ("whatsapp_path_out", $whatsapp_path_out);
define ("messenger_path", $messenger_path);
define ("whatsapp_path", $whatsapp_path);
define ("instagram_path", $instagram_path);
define ("webook_instagram_path", $webook_instagram_path);

define ("DocumentType_WhatsappId", $DocumentType_WhatsappId);
define("webook_messenger_path", $webook_messenger_path);

// AI Base Knowledge Base API Url :: farhan akhtar [01-02-2025]
$askAPI_url = 'http://165.232.183.220:8093/query';

// Upload Document to Knowledge Base API Url :: farhan akhtar [03-02-2025]
$uploadAPI_url = 'http://165.232.183.220:8093/upload';

// Delete Document of Knowledge Base API Url :: farhan akhtar [11-03-2025]
$deleteDoc_url = 'http://165.232.183.220:8093/delete_document';

// View Document of Knowledge Base API Url :: farhan akhtar [11-03-2025] :: Note here pass category name in endpoint.
$viewDocs_url = 'http://165.232.183.220:8093/get_document_details/';

?>