<?php 
   /***
    * Login Page
    * Author: Aarti
    * Date: 11-01-2023
    * This file handles user authentication and login process. It verifies user credentials,
    * checks for concurrent licenses, password expiration, and other login-related tasks.
    * 
    * Please do not modify this file without permission.
    **/
   session_start();
   include "../config/web_mysqlconnect.php"; // database file include
   include "MultiTanentDBValidation.php"; // database file include :: farhan Akhtar :: 25-12-2024

   if(isset($_GET['userid'])){ 
      $userid=$_GET['userid'];
   }


function add_audit_log($user_id, $actionlog, $ticketid, $comments, $db, $types='')
{
   global $link;
   $created_on=date("Y-m-d H:i:s");
   $ip = getenv('REMOTE_ADDR');
    $sql_ins1="INSERT INTO $db.web_audit_history(user_id, action, created_on, ticket_id, comments, ip_address, case_process_type)
   VALUES('$user_id', '$actionlog', NOW(), '$ticketid', '$comments' , '$ip', '$types')";
   mysqli_query($link,$sql_ins1)or die(mysqli_error().'Err web_audit_history');
}

   function telephony_flag($user,$flag){
      global $link;
      $query = $link->query("UPDATE tbl_mst_user_company SET telephony_flag = '$flag' WHERE V_EmailID = '$user'");
      if($query){
         return true;
      }
   }


   function classify($userid){
      global $link;
      $query = $link->query("SELECT i_classify FROM uniuserprofile WHERE AtxUserID = '$userid' AND AtxUserStatus = 1 ");
      $fetch = $query->fetch_assoc();
      $i_classify = $fetch['i_classify'];
      return $i_classify;
   }

   /* if login with ivr then logout of CRM is disable*/
   switch ($_GET['telephony']) {
      case 1:
         telephony_flag($_GET['loginID'], 1);
         break;
      default:
         telephony_flag($_GET['loginID'], 0);
         break;
   }

   /**
    * *This function check concorant licence flow*
    * */
   function check_concorant_licence($user_id){
      global $database_name ,$link;   
      if($user_id=='1'){   // Skip Licence for admin
         return '1' ;
      }   
      // Query to get user group
      $sql_group="select atxGid from unigroupdetails where ugdContactID='$user_id'";
      $res_group=mysqli_query($link,$sql_group);
      $row_group=mysqli_fetch_array($res_group);
      $user_group=$row_group['atxGid'];  

      // Query to get user license count
      $sql_l="SELECT UserLicence FROM unigroupid WHERE atxGid='$user_group' ";
      $res_licen=mysqli_query($link,$sql_l);
      $row_licen=mysqli_fetch_array($res_licen);
      $UserLicence=$row_licen['UserLicence'];//to get the licence count 

      $response = 1;
      if($UserLicence!=0){  
         $today_date=date("Y-m-d");         
         $sql_logins="SELECT DISTINCT l.IP, l. * , ug.atxGid
         FROM logip AS l
         LEFT JOIN uniuserprofile AS u ON u.AtxUserName = l.UserName
         LEFT JOIN unigroupdetails AS ug ON u.AtxUserID = ug.ugdContactID
         WHERE l.Reason = 'login'
         AND DATE( l.AccessedAt ) = '$today_date'
         AND ug.atxGid= '$user_group'
         AND l.TimePeriod IS NULL
         GROUP BY IP";
         $res_login=mysqli_query($link,$sql_logins);
         $num_user_login=mysqli_num_rows($res_login);
         if($num_user_login !=0){
            if($UserLicence > $num_user_login){
               $response=1;
            }else{
               $response=0;
            } 
         } 
      }
      return $response ;
   }

   /* Function to check if the user is already logged in */
   function check_already_login($user_id){
      global $database_name ,$link;
      $response = '';
      $sql_name="select login_status,login_datetime,id from uniuserprofile where AtxUserID='$user_id'";
      $res_name=mysqli_query($link,$sql_name);
      $num_user_login=mysqli_num_rows($res_name);
      if($num_user_login>0){
         $row_name=mysqli_fetch_array($res_name);
         if($row_name['id'] != '1'){
            $user_status=$row_name['login_status'];
            // $login_datetime=$row_name['login_datetime'];
            // $datemin = date('i',strtotime($login_datetime));
            // $currentmin = date("Y-m-d H:i:s");
            // $from_time = strtotime($login_datetime); 
            // $to_time = strtotime($currentmin); 
            // $diff_minutes = round(abs($from_time - $to_time) / 60,2);
            if($user_status == 'online'){
               // if(round($diff_minutes) == 0){
               //    $response=1;
               //    return $response;
               // }
               // if(round($diff_minutes)<1){
               //    $response=1;
               //    return $response;
               // }
               $response = 1;
            } else {
               $response = 0;
            }  
         }  
      }
      return $response;
   }
   /*// Function to check if the password is expired*/
   //Function to check if the password is expired (more than 3 months old) - aarti
   function isPasswordExpired($user_id) {  
      global $database_name ,$link;
      $response = '';
      $sql_name="select password_change_date from tbl_mst_user_company where I_UserID='$user_id'";
      $res_name=mysqli_query($link,$sql_name);
      $num_user_login=mysqli_num_rows($res_name);
      $response = '';
      if($num_user_login>0){
         $row_name=mysqli_fetch_array($res_name);
             
         if($row_name['password_change_date'] != '0000-00-00 00:00:00'){
            $password_change_date = strtotime($row_name['password_change_date']);
            $expiration_date = strtotime("-3 months");
            if ($password_change_date <= $expiration_date) {
               $response = '3';
            }
            return $response;
         }
         return $response;
      }else{
         return $response;
      }
   }
  // Function to unlock a locked user account after five minutes
   function lockAccount() {    
      global $link,$dbname;
      $response = '';
      $logintime   = date("Y-m-d H:i:s");
      $username   = ($_POST['loginID']);
      $query="SELECT locked_count,last_attemped_time,I_UserID FROM tbl_mst_user_company WHERE V_EmailID='$username' and I_UserStatus='1'";
      $result = mysqli_query($link,$query);
      $num_rows=mysqli_num_rows($result); 
      $locked_count_plus = '';
      if($num_rows==1){
         $row =  mysqli_fetch_array($result);
         $locked_count  =  $row['locked_count'];
         $userid        =  $row['I_UserID'];   
         $locked_time =  $row['last_attemped_time']; 
         $time_last = date("H:i:s",strtotime($locked_time));  
         $difference = time() - strtotime($time_last);
         if ($locked_count>'3' && $difference > 300){
            $sqlrest = "UPDATE $dbname.tbl_mst_user_company SET locked_count = '1' , last_attemped_time = '$logintime' where I_UserID='$userid'";
            mysqli_query($link, $sqlrest);
            return $response;
         }
         if($locked_count>'3'){
            $response = '1';
            return $response;
         }else{
            if($locked_count == 0){
               $locked_count_plus = 1;
            }else if($locked_count>=1){
               $locked_count_plus = $locked_count+1;
            }
            $sqlrest = "UPDATE $dbname.tbl_mst_user_company SET locked_count = '$locked_count_plus' , last_attemped_time = '$logintime' where I_UserID='$userid'";
            mysqli_query($link, $sqlrest);
            return $response;
         }
      }else{
         return $response;
      }
   }
   /**************Licence condition Close*********************/
   /*** Maximum ID from table ***/
   function maximum_id($table_name,$field_name){
      global $link;
      $sql_id = "select max($field_name) as max_id from $table_name";
      $Fetch_Id=mysqli_query($link,$sql_id);
      if($row=mysqli_fetch_array($Fetch_Id)){
         return $max_id=$row['max_id']+1;
      }else{
         return $max_id=1;
      }
   }
   /*** Function to log failed login attempts ***/
   function failed_login($table_name,$usname,$reason_failure,$database_name){
      global $link;
      mysqli_select_db($link,$database_name);
      $modificationtime    =  date("Y-m-d G:i:s");
      $ip =getenv('REMOTE_ADDR');
      $id=maximum_id($table_name,'SNo');
      if(empty($reason_failure)){ $reason_failure="login"; }
      $sql="insert into $table_name(SNo,IP,AccessedAt,UserName,Reason) values('$id','$ip','$modificationtime','$usname','$reason_failure')";
      $res=mysqli_query($link,$sql) or die(mysqli_error($link)) ;
   }

   // Function to check if a user is active :: farhan akhtar [15-03-2025]
   function isUserActive($link, $username) {
    // Use prepared statements for improved security
    $query = "SELECT AtxUserStatus FROM uniuserprofile WHERE AtxEmail = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['AtxUserStatus']; // Adjust condition if status logic changes
    }

    return false; // User not found or inactive
   }

   // get username from email 
   function getUserName($link, $email) {
      // Use prepared statements for improved security
      $query = "SELECT AtxUserName FROM uniuserprofile WHERE AtxEmail = ? AND AtxUserStatus = 1 LIMIT 1";
      $stmt = mysqli_prepare($link, $query);

      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if ($result && mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         return trim($row['AtxUserName']); // Adjust condition if status logic changes
      }

      return false; 
   }


   // FREE SITTING AND FIX SITTING {farhan akhtar :: 03-04-2025}

   function getUserIP($link, $username) {
      // Original prepared statement query
      $query = "SELECT IP_Address FROM encryp1 WHERE UserName = ? AND Status = 1";
  
      // Print the interpolated query for testing
      // $debugQuery = str_replace("?", "'$username'", $query);
      // echo "DEBUG SQL: $debugQuery\n";
  
      // Continue with the prepared statement execution
      $stmt = mysqli_prepare($link, $query);
      mysqli_stmt_bind_param($stmt, "s", $username);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
  
      if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          return $row['IP_Address'];
      }
  
      return false;
  }


   /**Start code for Login flow **/
   if($_POST['action'] == 'Login_Flow' || $_GET['action'] == 'Login_Flow'){
      // Validate CSRF token
      if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
          $info = [
              'status' => false,
              'message' => "Invalid request",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      // Get and validate input
      if($_GET['value'] == '1'){ // for IVR login flow check 
          $username = validateInput($_GET['loginID']);
          $password = validateInput($_GET['output']);
          $companyID = validateInput($_GET['companyID']);
      } else {
          $username = validateInput($_POST['loginID']);
          $password = validateInput($_POST['output']);
          $companyID = validateInput($_POST['companyID']);
      }

      // Validate required fields
      if(empty($username) || empty($password) || empty($companyID)) {
          $info = [
              'status' => false,
              'message' => "All fields are required",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      // Validate company ID format
      if(!validateCompanyId($companyID)) {
          $info = [
              'status' => false,
              'message' => "Invalid company ID format",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      $IP = getenv('REMOTE_ADDR');
      $AccessedAt = date("Y-m-d H:i:s");
      $datetime = date("Y-m-d H:i:s");

      // Get database credentials
      $DBcreds = [
          "configdbhost" => configdbhost,
          "configdbuser" => configdbuser,
          "configdbpass" => configdbpass
      ];

      // Get database for company
      $DBresponse = getDatabaseForCompany($companyID, $DBcreds);

      if (!$DBresponse['status']) {
          $info = [
              'status' => false,
              'message' => "Invalid company ID",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      // Set database constants
      define('db', $DBresponse['database_name']);
      define('database_name', $DBresponse['database_name']);
      define('configdbname', $DBresponse['database_name']);
      define('configcampaign', $DBresponse['campaign_name']);
      
      // Set session variables
      $_SESSION['database'] = $DBresponse['database_name'];
      $_SESSION['campaign'] = $DBresponse['campaign_name'];
      $_SESSION['companyName'] = $DBresponse['company_name'];

      // Connect to database
      $link = mysqli_connect(configdbhost, configdbuser, configdbpass);
      if (!$link) {
          $info = [
              'status' => false,
              'message' => "Database connection failed",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      mysqli_select_db($link, $database_name);

      // Check if user is active
      if (!isUserActive($link, $username)) {
          $info = [
              'status' => false,
              'message' => "User is inactive. Please contact the Administrator.",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      // Check user IP restrictions
      $userName = getUserName($link, $username);
      $userIP = getUserIP($link, $userName);

      if ($userIP !== '0.0.0.0' && $userIP !== $IP && $userName !== 'ADMIN') {
          $info = [
              'status' => false,
              'message' => "User is at Fixed Sitting, Please Contact the Administrator",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      // Prepare and execute login query
      $stmt = mysqli_prepare($link, "SELECT V_Password, I_UserID, I_UserStatus, V_EmailID, I_FirstLogin, I_CompanyID, Datetime_registration FROM tbl_mst_user_company WHERE V_EmailID=? AND V_Password=? AND I_UserStatus='1'");
      if (!$stmt) {
          $info = [
              'status' => false,
              'message' => "Database error",
              'location' => ''
          ];
          echo json_encode($info);
          exit();
      }

      mysqli_stmt_bind_param($stmt, "ss", $username, $password);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if (mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_array($result);
          $userid = $row['I_UserID'];
          $usname = $row['V_EmailID'];
          $userstatus = $row['I_UserStatus'];
          $first_login = $row['I_FirstLogin'];
          $company_id = $row['I_CompanyID'];

          // Get user profile
          $stmt = mysqli_prepare($link, "SELECT AtxUserName, File_up, AtxDesignation, AtxEmail FROM uniuserprofile WHERE AtxUserID=?");
          mysqli_stmt_bind_param($stmt, "i", $userid);
          mysqli_stmt_execute($stmt);
          $res_name = mysqli_stmt_get_result($stmt);
          $row_name = mysqli_fetch_array($res_name);
          
          $user_name = $row_name['AtxUserName'];
          $userimage = $row_name['File_up'];
          $DAtxDesignation = $row_name['AtxDesignation'];
          $AtxEmail = $row_name['AtxEmail'];
          $_SESSION['login_email'] = $AtxEmail;

          // Check password expiration
          $password_expire = isPasswordExpired($userid);
          if ($password_expire == '3') {
              $location = "web_changepass.php?userid=" . $userid . "&comp=" . $company_id;
              $info = [
                  'status' => false,
                  'message' => "Your password has expired. Please reset it.",
                  'location' => $location
              ];
              echo json_encode($info);
              exit();
          }

          // Check user rights
          if ($userid !== 1) {
              $rightsCheck = classify($userid);
              if ($_GET['telephony'] != 1 && $rightsCheck == 0) {
                  $info = [
                      'status' => false,
                      'message' => "This agent doesn't have rights to use Omnichannel. Please contact the administrator."
                  ];
                  echo json_encode($info);
                  exit();
              }
          }

          // Check if user is already logged in
          $login_check = check_already_login($userid);
          if ($login_check == '1') {
              telephony_flag($_GET['loginID'], 0);
              $info = [
                  'status' => false,
                  'message' => "Someone is already logged in. Please logout first.",
                  'location' => ''
              ];
              echo json_encode($info);
              exit();
          }

          // Check concurrent license
          $licence = check_concorant_licence($userid);
          if ($licence == '2') {
              $info = [
                  'status' => false,
                  'message' => "Someone is already logged in. Please logout first.",
                  'location' => ''
              ];
              echo json_encode($info);
              exit();
          } else if ($licence == '0') {
              $info = [
                  'status' => false,
                  'message' => "No more licenses left. Please contact Administrator.",
                  'location' => ''
              ];
              echo json_encode($info);
              exit();
          }

          // Handle successful login
          if ($userstatus == '1') {
              if ($first_login == '0') {
                  $location = "web_changepass.php?userid=" . $userid . "&comp=" . $company_id;
                  $message = "Successfully logged in.";
                  $status = true;
              } else {
                  // Handle remember me
                  if (isset($_POST['remember_me']) && $_POST['remember_me'] == '1') {
                      setcookie("logremember_me", "1", time() + 7600, "/", "", true, true);
                      setcookie("loginusername", $user_name, time() + 7600, "/", "", true, true);
                      setcookie("loginpassword", $password, time() + 7600, "/", "", true, true);
                  } else {
                      setcookie("logremember_me", "", time() - 7600, "/", "", true, true);
                      setcookie("loginusername", "", time() - 7600, "/", "", true, true);
                      setcookie("loginpassword", "", time() - 7600, "/", "", true, true);
                  }

                  // Set session variables
                  $_SESSION['userid'] = $userid;
                  $_SESSION['username'] = $usname;
                  $_SESSION['userstatus'] = $userstatus;
                  $_SESSION['user_name'] = $user_name;
                  $_SESSION['userimage'] = $userimage;
                  $_SESSION['designation'] = $DAtxDesignation;
                  $_SESSION['company_id'] = $company_id;
                  $_SESSION['login_time'] = time();
                  $_SESSION['last_activity'] = time();

                  // Log successful login
                  $stmt = mysqli_prepare($link, "INSERT INTO logip (I_UserID, V_IP, V_AccessedAt) VALUES (?, ?, ?)");
                  mysqli_stmt_bind_param($stmt, "iss", $userid, $IP, $AccessedAt);
                  mysqli_stmt_execute($stmt);

                  $location = "CRM/web_admin_dashboard.php";
                  $message = "Successfully logged in.";
                  $status = true;
              }
          } else {
              $message = "User is inactive.";
              $status = false;
              $location = "";
          }
      } else {
          // Log failed login attempt
          failed_login("tbl_mst_user_company", $username, "Invalid credentials", $database_name);
          
          $message = "Invalid username or password.";
          $status = false;
          $location = "";
      }

      $info = [
          'status' => $status,
          'message' => $message,
          'location' => $location
      ];
      echo json_encode($info);
      exit();
   }


// ===================================
// Agent Login: Send OTP via Email 
// [Date: 11-03-2025][Author: Aarti]
// ===================================
if($_POST['action'] == 'send_otp'){
   global $link,$db;
   // Collect POST data
   $email   = $_POST['email'];

   $data = getCompanyIDOTP($link, $email);
   $companyID = $data['company_id'];
   $name = $data['full_name'];
   $mobile_num = $data['phone_login'];

   // Database configuration credentials
   $DBcreds = [
      "configdbhost" => configdbhost,
      "configdbuser" => configdbuser,
      "configdbpass" => configdbpass
   ];
   // Fetch the specific database name for the company
   $DBresponse = getDatabaseForCompany($companyID,$DBcreds);
   $db=$DBresponse['database_name'];

   // Generate a 6-digit random OTP
   $otp = rand(100000, 999999);
   $date = date('Y-m-d H:i:s'); // Current timestamp

   // Check if the email already exists in the OTP authentication table
   $sql2 = "SELECT * FROM $db.otp_authentication where email = '$email'";
   $stmt2=mysqli_query($link,$sql2);
   $num_rows=mysqli_num_rows($result);
   if($num_rows == 1){
      // Update OTP and timestamp if the record exists
        $updatedate = date('Y-m-d H:i:s');
        $update = "UPDATE $db.otp_authentication SET otp = '$otp', `date` = '$updatedate'WHERE email = '$email'";
        $res_group=mysqli_query($link,$update);
   }else{
       // Insert a new OTP record if it doesn't exist
        $sql_msg = "INSERT INTO $db.otp_authentication (name, email, mobile, otp, `date`) VALUES('$name', '$email', '$mobile_num', '$otp', '$date') ";
         $res_group=mysqli_query($link,$sql_msg);
    } 

    // Prepare the OTP email
   $subject = "Your OTP Code";
   $message = "Your OTP is: $otp";
    // Send OTP email and return the response
   $valuess  = mail_send($subject, $message);
   if($valuess == '1'){
      echo 'otp_sent';
   } else {
      echo 'failed';
   }
    exit();
}
// ================================
// Function to Send Email via SMTP
// 
function mail_send($subject, $message){
   require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';

   // Sender and recipient email configuration
   $fromAddr = 'rajdubey.alliance@gmail.com';

   $toAddr = 'kewal.singh@ensembler.com';

   // Email server configuration
   define ("PORTNUM", '587');
   define ("EMAIL_USER", 'rajdubey.alliance@gmail.com');
   define ("EMAIL_PWD", 'syepvwaknagahctq');
   define ("EMAIL_SERVER", 'smtp.gmail.com');  
   define ("EMAIL_TLS", '1');

    // SMTP settings
   $mail = new PHPMailer;
   $mail->IsSMTP();
   if ( EMAIL_TLS == "1"){
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

   $mail->Host = EMAIL_SERVER;
   $mail->Port = PORTNUM;
   $mail->Username = EMAIL_USER;
   $mail->Password = EMAIL_PWD;
   $mail->From = $fromAddr; 
   $mail->FromName = $fromAddr;
   $mail->AddAddress($toAddr);          
   $mail->WordWrap = 50;
   $mail->IsHTML(true);
   $mail->Subject  =  $subject;
   $mail->Body     =  $message;
   $iRetry = $iRetry +1;

   // Sending the email
   $send=$mail->Send();
   return $send;
}

// ================================
// Agent Login: Verify OTP 
// [Date: 11-03-2025][Author: Aarti]
// 
if($_GET['action'] == 'verify_otp'){
   global $link,$db;

   $emp_otp = $_GET['otp'];
   $email = $_GET['user'];

   $companyID   = $_GET['companyid'];

   // Database configuration credentials
   $DBcreds = [
      "configdbhost" => configdbhost,
      "configdbuser" => configdbuser,
      "configdbpass" => configdbpass
   ];
   // Fetch the specific database name for the company
   $DBresponse = getDatabaseForCompany($companyID,$DBcreds);
   $db=$DBresponse['database_name'];

   // Fetch the OTP record for verification
   $select_sql  = "SELECT * FROM $db.otp_authentication WHERE email ='$email' ORDER BY `otp_authentication`.`id` DESC limit 1";
   $res_otp = $link->query($select_sql);

   if($res_otp->num_rows > 0){
      $rowSms = $res_otp->fetch_assoc();

      // Validate the entered OTP
      if($emp_otp != $rowSms['otp']){
            $info['status'] = 'fail';
            $info['msg'] = "OTP is not valid.";
            echo json_encode($info);die();     
            exit();
      }else{
            $info['status'] = 'success';
            $info['msg'] = "OTP is valid.";
            echo json_encode($info);die();     
            exit();
      }
   }else{
      // If no OTP record is found (possibly expired)
        $info['status'] = 'fail';
        $info['msg'] = "OTP expire.";
        echo json_encode($info);die();     
        exit();
   }
}
/* GET company id AND related database name :: 02-01-2025 ::  Farhan Akhtar*/ 
function getCompanyIDOTP($link, $userid) {
    // Query to retrieve the company ID using the campaign ID
    $query = "SELECT company_id,full_name,phone_login FROM asterisk.autodial_users WHERE user = ?";
    $stmt = mysqli_prepare($link, $query);
    // Bind the campaign ID to the query
    mysqli_stmt_bind_param($stmt, "s", $userid);
    // Execute the query
    mysqli_stmt_execute($stmt);
    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if a company ID was found
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $info['company_id'] = $data['company_id'];
        $info['full_name'] = $data['full_name'];
        $info['phone_login'] = $data['phone_login'];
        return $info; 
    } else {
      return false;
   } 
}
?>