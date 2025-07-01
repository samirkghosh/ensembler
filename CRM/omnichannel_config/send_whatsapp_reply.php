<?php
if(!isset($_SESSION)) 
{ 
session_start(); 
} 
// Include necessary files and database connection
include "../../config/web_mysqlconnect.php";

if(isset($_POST['reply']) && $_POST['action']=="reply") 
{
    $userid = $_SESSION['userid'];
    $message = $link->real_escape_string($_POST['reply']);
    $phone= $_POST['phone'];
    $sendFrom= $_POST['sendFrom'];
   
    $sql="INSERT INTO $db.whatsapp_out_queue (send_to,send_from,message,message_type_flag,status,create_date, created_by,channel_type,msg_flag,user_name) values ('$phone','$sendFrom','$message','1','$mesg_flag',NOW(), '$userid','1','OUT','Content Messaging')";
    $result= $link->query($sql);

    if($result == true){
        echo true;
    }

}

?>