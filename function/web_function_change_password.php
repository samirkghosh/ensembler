<?php 
/***
 * Change password Layout
 * Author: Ritu
 * Date: 04-04-2024
 * This file contains all the code for Change password.
 * (If a user change his password then he can recover it by clicking on forgot password and all the code for the same is given in this file.)
 * 
 **/
include("../config/web_mysqlconnect.php");
include("web_function_define.php");
require_once '../PHPMailer-5.2.28/PHPMailerAutoload.php';

if($_POST['action'] == 'change_password_flow'){
    Web_newpassword_change();
}
function getCompanyID($userid, $comp) {
   global $db, $link;
    // Prepare the SQL query
    $sql = "SELECT I_CompanyID FROM $db.tbl_mst_user_company WHERE I_UserID='$userid' AND I_CompanyID='$comp'";
    // Execute the query
    $result = mysqli_query($link, $sql) or die(mysqli_error($link) . "tbl_mst_user_company");    
    // Fetch the result
    $row = mysqli_fetch_array($result);
    // Return the company ID
    return $row['I_CompanyID'];
}
// Fetch use current password
function getUserPasswords($userid){
   global $db, $link;
    // Prepare the SQL query
    $sql = "select V_Password,password_1,password_2,password_3,V_EmailID from $db.tbl_mst_user_company where I_UserID='$userid'";
    // Execute the query
    $result = mysqli_query($link, $sql);
    // Fetch the result
    $row = mysqli_fetch_array($result);
    // Return the company ID
    return $row['I_CompanyID'];
}
// Define a function to update user password
function updateUserPassword($userid, $npassword, $passchangedate, $password_change) {
   global $db, $link;
    // Build the SQL query to update the password
    $sql = "update $db.tbl_mst_user_company set V_Password='$npassword',password_change_date='$passchangedate' $password_change where I_UserID='$userid'";
    //echo $sql;
    // Execute the query
    $result = mysqli_query($link, $sql);
    // Check if the query was successful
    if (!$result) {
        // If query fails, print the error message and terminate the script
        die(mysqli_error());
    }
}
function updatePasswordHistory($userid, $oldp, $npassword, $changedate) {
            global $db, $link;
    // Build the SQL query to update password history
    $sql = "UPDATE $db.changepwd_history SET OldPassword='$oldp', NewPassword='$npassword', CPass_Date='$changedate' WHERE UserID='$userid'";
    
    // Execute the query
    $result = mysqli_query($link, $sql);
    
    // Check if the query was successful
    if (!$result) {
        // If query fails, print the error message and terminate the script
        die(mysqli_error());
    }
}
function updateFirstLoginStatus($userid) {
   global $db, $link;
    // Build the SQL query to update first login status
    $sql = "UPDATE $db.tbl_mst_user_company SET I_FirstLogin='1' WHERE I_UserID='$userid'";
    
    // Execute the query
    $result = mysqli_query($link, $sql);
    
    // Check if the query was successful
    if (!$result) {
        // If query fails, print the error message and terminate the script
        die(mysqli_error());
    }
}
function fetchUsername($userid) {
      global $db, $link;
    // Build the SQL query to fetch the username
    $query = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserStatus='1' AND AtxUserID='$userid'";
    // Execute the query
    $result = mysqli_query($link, $query);
    
    // Check if the query was successful
    if (!$result) {
        // If query fails, print the error message and terminate the script
        die("Error in query: " . mysqli_error($link));
    }
    
    // Fetch the username from the result set
    $row = mysqli_fetch_array($result);
    
    // Check if a username was found
    if ($row) {
        return $row['AtxUserName'];
    } else {
        return null; // Return null if no username was found
    }
}
function updatePassword($npassword, $AtxUserName) {
   global $db, $link;
    // Build the SQL query to update the password
    $query = "UPDATE $db.encryp1 SET Pwd='$npassword' WHERE UserName='$AtxUserName'";
    
    // Execute the query
    $result = mysqli_query($link, $query);
    
    // Check if the query was successful
    if (!$result) {
        // If query fails, print the error message and terminate the script
        die("Error in updating password: " . mysqli_error($link));
    }
}
function updateUserLoginStatus($user_id, $logintime) {
   global $db, $link;
    $sql = "UPDATE $db.uniuserprofile SET login_status = 'online' , login_datetime = '$logintime' WHERE AtxUserID='$user_id'";
    mysqli_query($link, $sql);
}
function updateUserLoginAttempt($user_id, $logintime) {
   global $db, $link;
    $sql = "UPDATE $db.tbl_mst_user_company SET locked_count = '0' , last_attemped_time = '$logintime' WHERE I_UserID='$user_id'";
    mysqli_query($link, $sql);
}
function getUserProfile($user_id) {
    global $db, $link;
    $sql = "SELECT AtxUserName, File_up, AtxDesignation, AtxEmail FROM $db.uniuserprofile WHERE AtxUserID='$user_id'";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($res);
    return $row;
}
function getUserGroup($user_id) {
    global $db, $link;
    $sql_group = "SELECT atxGid FROM $db.unigroupdetails WHERE ugdContactID='$user_id'";
    $res_group = mysqli_query($link, $sql_group);
    $row_group = mysqli_fetch_array($res_group);
    return $row_group;
}
function getUserLicenceDetails($user_group) {
    global $db, $link;
    $sql_l = "SELECT UserLicence, con_lic_cnt, named_lic_cnt FROM $db.unigroupid 
              WHERE atxGid='$user_group' AND atxGid!='0000'";
    $res_licen = mysqli_query($link, $sql_l);
    $row_licen = mysqli_fetch_array($res_licen);
    return $row_licen;
}
/***
 * For change password code flow 
 * I have remove web_newpassword.php and create function add all code here
 * this fun call from file web_changepass.php
 * ****/ 
function Web_newpassword_change(){
    global $link,$db,$uc_ip;
    /*** basic variable use ***/
    $value          =   $_POST['value'];
    $opassword      =   $_POST['oldpassword'];
    $npassword      =   md5($_POST['newpassword']);
    $npassword      =   $_POST['npassword'];
    $confirmpassword=   $_POST['confirmpassword'];

    $userid     =   $_POST['userid'];
    $comp       =   $_POST['comp'];

    $row1 = getCompanyID($userid, $comp); // get company id 
    $company_id = $row1['I_CompanyID'];
    $changedate = date("Y-m-d");

    // Code start for new and old password change
    if(!empty($npassword)){
        $sql = "select V_Password,password_1,password_2,password_3,V_EmailID from $db.tbl_mst_user_company where I_UserID='$userid'";
        // Execute the query
        $result = mysqli_query($link, $sql);
        // Fetch the result
        $row1 = mysqli_fetch_array($result);
        $oldp = $row1['V_Password'];
        $V_EmailID = $row1['V_EmailID'];

        $jobtitle=getjobTitle($userid,$db); // fetch job title - in function/web_function_define file
        // Change password astric database - in function/web_function_define file
        
        $response = get_web_page("http://$uc_ip/agc/executequery.php?mode=modify&user=$V_EmailID&pass=$confirmpassword");
        if($response=='Error'){
            $info['status'] ='false';
            $info['message'] = 'Something went wrong. Please Check';
            $info['location'] = '';
            echo json_encode($info); exit();
        }
        /*** This code for replace password1,password2,password3 for not use 3 time same password ***/
        $password_change = '';
        if(empty($row1['password_1'])){
            $password_change = ",password_1='".$oldp."'";
        }
        if(!empty($row1['password_1'])){
            $pass_1 = $row1['password_1'];
            $password_change = ",password_1='".$oldp."', password_2='".$pass_1."'";
        }
        if(!empty($row1['password_2'])){
            $pass_2 = $row1['password_2'];
            $pass_1 = $row1['password_1'];
            $password_change = ",password_1='".$oldp."', password_2='".$pass_1."',password_3='".$pass_2."'";
        }
        $passchangedate = date('Y-m-d H:i:s');
        updateUserPassword($userid, $npassword, $passchangedate, $password_change); // for update password tbl_mst_user_company table
        updatePasswordHistory($userid, $oldp, $npassword, $changedate); // for update password changepwd_history table
        updateFirstLoginStatus($userid); // update password tbl_mst_user_company table
        $ress=fetchUsername($userid); // this code for fetch username
        $AtxUserName=$ress['AtxUserName'];
        updatePassword($npassword, $AtxUserName); // update password encryp1 table
        // add_audit_log($userid, 'password_change', 'null', 'Changed the Password', $db);
        if($value==1){
            // when use click setting to change password 
            /*** For send mail change password **/ 
            $to = $V_EmailID;
            $subject = "CRM Changed Password Message";
            $message = "<style>
                    p{
                    font-family:arial;
                    font-size:12px;
                    color:#000000;
                    }
                </style>
                <html>
                <head>
                <title>HTML email</title>
                </head>
                <body>
                <p>Hi,</p>
                <p>Your password is Changed !<br>New Password : <strong>".$confirmpassword."</strong></p>
                <p>Thanx <br>Admin</p>
                </body>
                </html>
                ";
             $sql_email="insert into $db.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,module, ICASEID,i_expiry) values ('$V_EmailID', '$from', '$subject', '$message', 'OUT', 'Password Reset','','')";
            mysqli_query($link,$sql_email);

            //Close mail send code
            $html = '<table width=80% align=center>
                <tr>
                    <td class=ttext align=center>Your Password has been successfully changed.</td>
                </tr>
                <tr>
                    <td class=ttext align=center>Wait 2 second this window will be closed automatically.</td>
                </tr>
            </table>';  
            $info['status'] = 'true';
            $info['message'] = '';
            $info['location'] = '';
            $info['html'] = $html;
            echo json_encode($info);
        }else{
            // when user first time login then change password flow active
            /*** This function for check password and set session and redirect CRM Dashbord page ***/
            changepassword_logindatabase($userid,$company_id);
        }
    }
}
function changepassword_logindatabase($userid,$company_id){
  global $link,$db;
  $user_id = $_POST['userid'];
  $row=getUserName($user_id);
  $user_name=$row['AtxUserName'];
  // WFM add login details for wfmlogin report - aarti:30-03-23
  include("../CRM/WFM/wfm_function.php");
  $wfm_function = new Wfm_connection;
  $wfm_function->login_entry_wfm($userid,$database_name);

   ############### CALLING FUNCTION TO INSERT VALUES IN logip TABLE IF SUCCESSFUL LOGIN OCCURS
  $q=mysqli_fetch_array(mysqli_query($link,"select SNo FROM $db.logip order by SNo desc limit 0,1"));
  $_SESSION['SNo']=$q['SNo'];

  //user login details update in table 
  $logintime   = date("Y-m-d H:i:s");
  $sql = updateUserLoginStatus($user_id, $logintime);
  $sqlrest = updateUserLoginAttempt($user_id, $logintime);

  /*****************using function to get the database of companyid********************/ 
    $company_name=company_name($company_id);
    $row=getUserProfile($user_id);
    $username=$row['AtxUserName'];
    $userimage=$row['File_up'];
    $DAtxDesignation=$row['AtxDesignation'];
    $AtxEmail=$row['AtxEmail'];
    $_SESSION['login_email']=$AtxEmail;

    ############## CODE TO FIND THE GROUP ID OF A PARTICULAR USER ID ################33
    $row_group=getUserGroup($user_id);
    $user_group=$row_group['atxGid'];

    $row_licen=getUserLicenceDetails($user_group);
    $user_group_licence=$row_licen['UserLicence'];//to get the licence count 
    $con_lic_cnt=$row_licen['con_lic_cnt'];

    ################ SESSION VARIABLES ########################3 
    $_SESSION['database']       =$db;
    $_SESSION['logged']         =ucfirst(ucwords(strtolower($username)));
    $_SESSION['branch']         ='1459';
    $_SESSION['companyid']      =$company_id;
    $_SESSION['userid']         =$user_id;
    $_SESSION['user_group']     =$user_group;
    $_SESSION['company_name']   =$company_name;
    $_SESSION['user_image']     =$userimage;
    $_SESSION['reginoal_spoc']  =$DAtxDesignation;

    if(($user_group=='080000') || ($user_group=='0000'))   // ADmin Supervisour ADmin
    {
        $web_admin_dashboard = base64_encode('web_admin_dashboard');
         $message = "Sucessfully login.";
         $status = true; 
         $location = "CRM/dashboard_index.php?token=$web_admin_dashboard";
    }else if($user_group=='060000' || $user_group=='050000' || $user_group=='070000' || $user_group=='090000'  ) // Agent, B1, B2, B3, B4
    {
         $web_helpdesk = base64_encode('web_helpdesk');
        $location ="CRM/helpdesk_index.php?token=$web_helpdesk";
         $message = "Sucessfully login.";
         $status = true;
    }else{
         //$location = "CRM/index.php";
          $location ="CRM/web_admin_dashboard.php";
         $message = "Sucessfully login.";
         $status = true;
    }
    $info['status'] = $status;
    $info['message'] = $message;
    $info['location'] = $location;
    echo json_encode($info); exit();
}
?>
