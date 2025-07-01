<?php
/***
 * CREATE TICKET
 * Author: Aarti Ojha
 * Date: 13-03-2024
 * This file is handling reset password code for update in database
 */

include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
// fetch user details
include_once("../web_function.php");

function get_from_address($id){
    global $db,$link;
    $sql = "SELECT v_username FROM $db.tbl_smtp_connection WHERE id='$id'";
    $res_email=mysqli_query($link,$sql);
    $row_email=mysqli_fetch_array($res_email);
    return $row_email['v_username'];
}
function reset_password($email){
    global $db,$link;
    $from = get_from_address(1);
    if(isset($email) && !empty($email)) 
    {
        $check_email=$link->query("SELECT * FROM $db.tbl_mst_user_company WHERE V_EmailID='$email' AND  I_UserStatus='1'");
        $num_check_email= $check_email->num_rows;
        if($num_check_email > '0')
        {
            $row = $check_email->fetch_assoc();
            $V_EmailID = $row['V_EmailID'];
            $V_MobileNo = $row['V_MobileNo'];

            $check_name= $link->query("SELECT AtxUserName, AtxDisplayName FROM $db.uniuserprofile WHERE AtxEmail = '$email' AND  AtxUserStatus=1 ");
            $row_name = $check_name->fetch_assoc();
            $AtxUserName = $row_name['AtxUserName'];
            $AtxDisplayName = $row_name['AtxDisplayName'];

            $password=rand(12006,95006);
            $encripted_passwd= md5(sha1(md5($password)));

            $update_user= $link->query("UPDATE $db.tbl_mst_user_company SET V_Password = '$encripted_passwd', I_FirstLogin = 0 WHERE V_EmailId = '$V_EmailID' AND V_MobileNo='$V_MobileNo'");

            if($update_user == true)
            {
                $link->query("UPDATE $db.encryp1 SET Pwd = '$password' WHERE UserName = '$AtxUserName'");
            }
                        
            $data = array();
            $data['name'] = $AtxDisplayName;
            $data['password_user'] = $password;
            $data['email'] = $email;

            // fetching mail templete data
            $ress_1 = mail_template($V_MobileNo,'forgot_password', $data);	
            $expiry = $ress_1['expiry'];
            $subject = $ress_1['sub'];
            $message_mail = $ress_1['msg'];

            // insert data in mail out table
            $sql_email="insert into $db.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,module, ICASEID,i_expiry) values ('$email', '$from', '$subject', '$message_mail', 'OUT', 'Password Reset','$V_MobileNo','$expiry')";
            mysqli_query($link,$sql_email);

            // fetchig sms templete
            $res2 = sms_template($V_MobileNo, 'forgot_password', $data);
            $message_text = $res2['msg']; 
            $expiry = $res2['expiry']; 

            // Common function to insert SMS outgoing data into the database.[Aart][05-12-2024]
              $data_sms=array();
              $data_sms['v_mobileNo'] = $V_MobileNo;
              $data_sms['v_smsString']= $message_text;
              $data_sms['V_AccountName']=$AtxDisplayName;
              $data_sms['i_status']='0';
              $data_sms['i_expiry']=$expiry; 
             insert_smsmessages($data_sms);


            $msg = "Password Reset Sucessfully, Temporary Password is Sent to '$email' ";
            
        }
        else
        {
            $msg = "Failed to Reset the password";
        }
    }
    return $msg;
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["email"]) && !empty($_POST['email'])){
    $email = $_POST['email'];
    $message = reset_password($email);
    echo json_encode(['status'=>'success','msg'=>$message]);exit();
}
?>