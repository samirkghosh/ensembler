<?php
/**
 * Author: Ritu Modi
 * Date: 04-04-2024
 * This file handles user authentication and email sending in a web application.
**/
// Set the default timezone
date_default_timezone_set('Asia/Kolkata');
// Include necessary files
require_once '../PHPMailer-5.2.28/PHPMailerAutoload.php';
include("../config/web_mysqlconnect.php"); // Include database connection file
include("MultiTanentDBValidation.php");// Including file for selecting database name on the basis of the company id [vastvikta][04-03-2025]

// Print the contents of $_POST for debugging

// Check if action is set and it's a login_check action
if(isset($_POST['action']) && $_POST['action'] == 'login_check'){
    // Check if loginID is empty
     // added code for selecting database name on the basis of the company id [vastvikta][04-03-2025]
   $companyID   = $_REQUEST['companyID'];
   
   $DBcreds = [
      "configdbhost" => configdbhost,
      "configdbuser" => configdbuser,
      "configdbpass" => configdbpass
   ];
   $DBresponse = getDatabaseForCompany($companyID,$DBcreds);
   if ($DBresponse['status']) {
            
      define('db', $DBresponse['database_name']);
      define('database_name', $DBresponse['database_name']);
      define('configdbname', $DBresponse['database_name']); // Define database name constant
      define('configcampaign', $DBresponse['campaign_name']); // Define database name constant
      $_SESSION['database'] = $DBresponse['database_name'];
      $_SESSION['campaign'] = $DBresponse['campaign_name']; 
      $_SESSION['companyName'] = $DBresponse['company_name']; 
     // Assign to variables
     $db = $DBresponse['database_name'];
     $database_name = $DBresponse['database_name'];
     $configdbname = $database_name;
   
     $link = mysqli_connect(configdbhost, configdbuser, configdbpass); // Establish database connection
     mysqli_select_db($link, $database_name); // Select database
     
     
   } else {
      echo json_encode($DBresponse);
      exit(); // Stop further execution
   }
   // db code ends

    if(empty($_POST['loginID'])){
        $info['status']  = false;
        $info['message']  = 'Please enter Mobile No.';
    } else {
        $empid = $_POST['loginID'];   
        if(!empty($empid)){
            // Query to select data from database based on phone number
            $sql = "SELECT * FROM $db.web_accounts WHERE phone='$empid'";  
        }
        // Execute the query
        $result = mysqli_query($link, $sql);     
        // Fetch the result as an associative array
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
        // Get the number of rows returned
        $count = mysqli_num_rows($result);  
        // If exactly one row is returned
        if($count == 1){ 
            // Set session variables for account details
            $_SESSION['AccountNumber'] = $row['AccountNumber'];
            $_SESSION['name'] = $row['fname']." ".$row['lname'];
            $_SESSION['phone'] = $row['phone'];
            $_SESSION['customer_view'] = 1;           
        // $otp = rand(6,1000000);
        // $date = date('Y-m-d H:i:s');
        // $sql2 = "SELECT * FROM $db.web_accounts where phone = '$empid'";
        // $stmt2 =  mysqli_query($link, $sql2); 
        // $num = mysqli_num_rows($stmt2);   
        // if($num > 0){
        //     $rowss = mysqli_fetch_array($stmt2, MYSQLI_ASSOC);
        //     $name = $rowss['fname'];
        //     $updatedate = date('Y-m-d H:i:s');
        //     $update = "UPDATE $db.web_accounts SET v_otp = '$otp', d_otpTime = '$updatedate'WHERE phone = '$empid'";
        //     mysqli_query($link, $update);

        //     $case_type = 'otp_web_chat';
        //     $sql_sms="select * from $db.tbl_smsformat where smsstatus=1 AND smstemplatename='$case_type'"; 
        //     $qsms = mysqli_query($link, $sql_sms);
        //     $rowSms = mysqli_fetch_array($qsms, MYSQLI_ASSOC);
        //     $header = $rowSms['smsheader'];
        //     $footer = $rowSms['smsfooter'];
        //     $body = $rowSms['smsbody'];
        //     $min = '5';
        //     $header = str_replace("%name%", $name, $header);
        //     $body = str_replace("%min%", $min, $body);
        //     $body = str_replace("%otp%", $otp, $body);
        //     $content = $header.','.$body.$footer;
        //     $sql_sms_feed="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status) values ('$empid','$empid','$content','Sms','$name','',NOW(), '1')";
        //     mysqli_query($link, $sql_sms_feed);
        // }
         $info['status']  = true;
         $info['message']  = '';
      }else{  
         $info['status']  = false;
         $info['message']  = 'User Not Found!!';  
      }  
  }
  echo json_encode($info);die();
}
// Check if action is set and it's an otp_check action
if(isset($_POST['action']) && $_POST['action'] == 'otp_check'){
    // Trim leading zeros from vh_mobile
     // added code for selecting database name on the basis of the company id [vastvikta][04-03-2025]
   $companyID   = $_REQUEST['companyID'];
   
   $DBcreds = [
      "configdbhost" => configdbhost,
      "configdbuser" => configdbuser,
      "configdbpass" => configdbpass
   ];
   $DBresponse = getDatabaseForCompany($companyID,$DBcreds);
   if ($DBresponse['status']) {
            
      define('db', $DBresponse['database_name']);
      define('database_name', $DBresponse['database_name']);
      define('configdbname', $DBresponse['database_name']); // Define database name constant
      define('configcampaign', $DBresponse['campaign_name']); // Define database name constant
      $_SESSION['database'] = $DBresponse['database_name'];
      $_SESSION['campaign'] = $DBresponse['campaign_name']; 
      $_SESSION['companyName'] = $DBresponse['company_name']; 
     // Assign to variables
     $db = $DBresponse['database_name'];
     $database_name = $DBresponse['database_name'];
     $configdbname = $database_name;
   
     $link = mysqli_connect(configdbhost, configdbuser, configdbpass); // Establish database connection
     mysqli_select_db($link, $database_name); // Select database
     
     
   } else {
      echo json_encode($DBresponse);
      exit(); // Stop further execution
   }
   // db code ends

    $_POST['vh_mobile'] = ltrim($_POST['vh_mobile'], '0');
    $mobile_new = $_POST['vh_mobile'];
    $emp_otp = $_POST['emp_otp'];    
    // Query to select data from database where phone number matches and v_otp is not empty
    $select_sql = "SELECT * FROM $db.web_accounts WHERE phone ='$mobile_new' AND v_otp != '' LIMIT 1";
    // Execute the query
    $res_otp = mysqli_query($link, $select_sql);
    $num = mysqli_num_rows($res_otp);   
    // Check if any row is returned
    if($num > 0){
        // Fetch the result as an associative array
        $rowSms = mysqli_fetch_array($res_otp, MYSQLI_ASSOC);
        // Verify if the entered OTP matches the stored v_otp
        if($emp_otp != $rowSms['v_otp']){
            $info['status'] = 'false';
            $info['msg'] = "OTP is not valid.";
            echo json_encode($info);
            die();
        } else {
            // Set session variables upon successful OTP verification
            $_SESSION['AccountNumber'] = $rowSms['AccountNumber'];
            $_SESSION['name'] = $rowSms['fname']." ".$rowSms['lname'];
            $_SESSION['phone'] = $rowSms['phone'];
            $_SESSION['customer_view'] = 1;   
            $info['status'] = 'true';
            $info['msg'] = "OTP is valid.";
            echo json_encode($info);
            die();
        }
    } else {
        // No valid OTP found
        $info['status'] = 'false';
        $info['msg'] = "OTP has expired or is invalid.";
        echo json_encode($info);
        die();
    }
}
// Check if action is set and it's a send_mail action
if(isset($_POST['action']) && $_POST['action'] == 'send_mail'){
    // echo "Inside send_mail condition.<br>";
    error_log("Inside send_mail condition."); 

    // Assuming these variables are defined somewhere in your code
    global $PORTNUM, $EMAIL_SERVER, $EMAIL_USER, $EMAIL_PWD, $EMAIL_TLS, $db, $link;
   
    // added code for selecting database name on the basis of the company id [vastvikta][04-03-2025]
   $companyID   = $_REQUEST['companyID'];
   
   $DBcreds = [
      "configdbhost" => configdbhost,
      "configdbuser" => configdbuser,
      "configdbpass" => configdbpass
   ];
   $DBresponse = getDatabaseForCompany($companyID,$DBcreds);
   if ($DBresponse['status']) {
            
      define('db', $DBresponse['database_name']);
      define('database_name', $DBresponse['database_name']);
      define('configdbname', $DBresponse['database_name']); // Define database name constant
      define('configcampaign', $DBresponse['campaign_name']); // Define database name constant
      $_SESSION['database'] = $DBresponse['database_name'];
      $_SESSION['campaign'] = $DBresponse['campaign_name']; 
      $_SESSION['companyName'] = $DBresponse['company_name']; 
     // Assign to variables
     $db = $DBresponse['database_name'];
     $database_name = $DBresponse['database_name'];
     $configdbname = $database_name;
   
     $link = mysqli_connect(configdbhost, configdbuser, configdbpass); // Establish database connection
     mysqli_select_db($link, $database_name); // Select database
     
     
   } else {
      echo json_encode($DBresponse);
      exit(); // Stop further execution
   }
   // db code ends

    // Fetching POST data
    $toAddr1 = $_POST['email'];
    $toAddr = 'rajdubey.alliance@gmail.com'; // Replace with actual recipient email address
    $FullName = $_POST['full_name'];
    $tpin = $_POST['tpin'];
    $nrc = $_POST['nrc'];
    $mobile = $_POST['mobile'];
    $message = $_POST['message'];
    $subject = 'Service Request Form:'.$toAddr1;
    $case_type = 'service_request';
    // Query to fetch email template from database
    $sql_sms = "SELECT * FROM $db.tbl_mailformats WHERE MailStatus=1 AND MailTemplateName='$case_type'"; 
    $qsms = mysqli_query($link, $sql_sms);
    $rowSms = mysqli_fetch_array($qsms, MYSQLI_ASSOC);
    // Replace placeholders in email template with actual values
    $subject = $rowSms['MailSubject'];
    $subject = str_replace("%email%", $toAddr1, $subject);
    $body = $rowSms['MailBody'];
    $body = str_replace("%fullname%", $FullName, $body);
    $body = str_replace("%mobile%", $mobile, $body);
    $body = str_replace("%email%", $toAddr1, $body);
    $body = str_replace("%tpin%", $tpin, $body);
    $body = str_replace("%nrc%", $nrc, $body);
    $body = str_replace("%message%", $message, $body);
    $V_Body = $body;
    // Configure PHPMailer
    $fromAddr = $EMAIL_USER;
    $mail = new PHPMailer;
    $mail->IsSMTP();
    // Set SMTP settings based on $EMAIL_TLS value
    if ($EMAIL_TLS == "1"){
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
    }
    $mail->SMTPDebug = 0;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Host = $EMAIL_SERVER;
    $mail->Port = $PORTNUM;
    $mail->Username = $EMAIL_USER;
    $mail->Password = $EMAIL_PWD;
    $mail->From = $fromAddr; 
    $mail->FromName = $fromAddr;
    $mail->AddAddress($toAddr);          
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $V_Body;
    // Attempt to send the email
    $send = $mail->Send();    
    // Check if the email was sent successfully
    if($send){
        $msg = "Successfully sent the email.";
        echo json_encode($msg);
    } else {
        $msg = "Failed to send the email.";
        echo json_encode($msg);
    }
}
?>
