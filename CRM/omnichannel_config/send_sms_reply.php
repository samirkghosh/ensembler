<?php
if(!isset($_SESSION)) 
{ 
session_start(); 
} 
// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");
include("../web_function.php");

if(isset($_POST['reply']) && $_POST['action']=="reply") 
{
    $userid = $_SESSION['userid'];
    $message = $link->real_escape_string($_POST['reply']);
    $phone= $_POST['phone'];
    $name = $_POST['name'];
    $i_id = $_POST['i_id'];
    $expiry = 3;
   
    // Common function to insert SMS outgoing data into the database.[Aart][05-12-2024]
    $data_sms=array();
    $data_sms['i_id'] = $i_id;
    $data_sms['v_mobileNo'] = $phone;
    $data_sms['v_smsString']= $message;
    $data_sms['V_CreatedBY']=$userid;
    $data_sms['i_status']='0';
    $data_sms['i_expiry']=$expiry; 
    insert_smsmessages($data_sms);
    echo true;
}
?>