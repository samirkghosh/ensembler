<?php 
   /***
    * Forgot Passowrd Script
    * Author: Ritu Modi
    * Date: 02-04-2024
    * This script handles the forgotten password functionality. 
    * Then it includes necessary files for database connection and some utility functions.
**/
include("../config/web_mysqlconnect.php"); // database file include
include("../CRM/web_function.php");// Include utility functions file
include("MultiTanentDBValidation.php");// Including file for selecting database name on the basis of the company id [vastvikta][28-02-2025]

if ($_POST['action'] == 'forgot_password'){
   Forgot_Password();
}
//Define the function
function checkEmailAndMobileExists($email) {
   global $db,$link;
    // Construct the SQL query
    $sql_check_email = "SELECT * FROM $db.tbl_mst_user_company WHERE (V_EmailID='$email' OR V_MobileNo='$email') AND I_UserStatus='1'";

    // Execute the query
    $result_query1 = mysqli_query($link, $sql_check_email);
    // Check if there are any rows returned
    $num_check_email = mysqli_num_rows($result_query1);    
    // Return the count
    return $num_check_email;
}
// Function to check user name and display name
function checkUserNameAndDisplayName($email) {
      global $db,$link;
    // Construct the SQL query
    $sql_check_name = "SELECT AtxUserName, AtxDisplayName FROM $db.uniuserprofile WHERE AtxEmail = '$email' OR AtxHomePhone = '$email' AND AtxUserStatus='1'";
        // Execute the query
    $result_query_name = mysqli_query($link, $sql_check_name);
    // Fetch the row
    $row_name = mysqli_fetch_array($result_query_name);
    // Return the row
    return $row_name;
}
// This function handling forgot password flow
function Forgot_Password(){
   global $link,$from_email;
   
   // added code for selecting database name on the basis of the company id [vastvikta][28-02-2025]
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
   // database selection code end
   
   if(!empty($_REQUEST['email'])){
      $email=$_REQUEST['email'];
      $sql_check_email="select * from $db.tbl_mst_user_company where V_EmailID='$email' OR V_MobileNo = '$email' AND  I_UserStatus='1'";
      $result_query1=mysqli_query($link,$sql_check_email);
      $num_check_email=mysqli_num_rows($result_query1);
      if($num_check_email > '0')// when record does not exist.
      {
         $msg = "FOUND";
         $row1 = mysqli_fetch_array($result_query1);
         $V_EmailID = $row1['V_EmailID'];
         $V_MobileNo = $row1['V_MobileNo'];
         //AtxEmail, AtxHomePhone
         $sql_check_name="SELECT AtxUserName, AtxDisplayName FROM $db.uniuserprofile WHERE AtxEmail = '$email' OR  AtxHomePhone = '$email' AND  AtxUserStatus='1'";
         $result_query_name=mysqli_query($link,$sql_check_name);
         $row_name = mysqli_fetch_array($result_query_name);
         $AtxUserName = $row_name['AtxUserName'];
         $AtxDisplayName = $row_name['AtxDisplayName'];
         $password=rand(12006,95006);
         $passwd= md5(sha1(md5($password)));

         $sql_customer_user="update $db.tbl_mst_user_company set V_Password = '".$passwd."', I_FirstLogin ='0' where V_EmailId = '".$V_EmailID."' And V_MobileNo='".$V_MobileNo."' ";
         mysqli_query($link,$sql_customer_user);
         $sql_customer_user_pass="update $db.encryp1 set Pwd = '".$passwd."' where UserName = '".$AtxUserName."' ";
         mysqli_query($link,$sql_customer_user_pass);

        
         $from = $from_email; // $from_helpdesk_email variable define in config->common-constants file
         $case_type1 = 'forgot_password';

         // For fetching email template
         $data_array = array();
         $data_array['name'] = $AtxDisplayName;
         $data_array['password_user'] = $password;
         $data_array['email'] = $email;         

         $ress_1 = mail_template($V_MobileNo, $case_type1, $data_array); 


         // For fetching sms template
           $expiry = $ress_1['expiry'];
           $subject = $ress_1['sub'];
           $message_mail = $ress_1['msg'];

         //insert record email out table
         $sql_email="insert into $db.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,module, ICASEID,i_expiry) values ('$email', '$from', '$subject', '$message_mail', 'OUT', 'New Case Call','$V_MobileNo','$expiry')";
         mysqli_query($link,$sql_email) or die("Error In Query23 ".mysqli_error($link));

           
           $res2 = sms_template($V_MobileNo, $case_type1, $data_array);
           $message_text = $res2['msg']; 
           $expiry = $res2['expiry']; 
         // Common function to insert SMS outgoing data into the database.[Aart][05-12-2024]
          $data_sms=array();
          $data_sms['v_mobileNo'] = $V_MobileNo;
          $data_sms['v_smsString']= $message_text;
          $data_sms['V_CreatedBY']='';
          $data_sms['i_status']='0';
          $data_sms['V_AccountName']=$AtxDisplayName;
          $data_sms['i_expiry']=$expiry;
          insert_smsmessages($data_sms);

         echo json_encode("Password Reset Mail Sent Successfully, Please Check Your E-Mail");
      }else{
         // Output error message if email not registered
         echo json_encode("This email address is not registered!");
      }
   }
}
?>