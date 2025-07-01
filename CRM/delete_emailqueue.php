<?php
/***
 * Emails
 * Author: Aarti Ojha
 * Date: 28-10-2024
 * Description: This file handles delete emails in queues
 */

// Include the database connection file
include("../config/web_mysqlconnect.php");  

$del=$_POST['id'];
if(isset($_POST['id']) && (isset($_POST['action']) && $_POST['action'] == 'trash') ){
    foreach ($del as $key => $id):
        $sql_delete="UPDATE $dbname.web_email_information SET i_DeletedStatus='2' WHERE EMAIL_ID='$id'";
        $result = mysqli_query($link,$sql_delete);
        if($result == true) {
            echo "Sucessfully moved to trash";
        }
    endforeach;
}else if(isset($_POST['id']) && (isset($_POST['action']) && $_POST['action'] == 'delete') ){
    foreach ($del as $key => $id):
        $sql_delete="DELETE FROM $dbname.web_email_information WHERE EMAIL_ID='$id'";
        $result = mysqli_query($link,$sql_delete);
        if($result == true) {
            echo "Sucessfully Deleted";
        }
    endforeach;
}else if(isset($_POST['id']) && (isset($_POST['action']) && $_POST['action'] == 'spam') ){
    foreach ($del as $key => $id):
        $sql_delete="UPDATE $dbname.web_email_information SET classification='4' WHERE EMAIL_ID='$id'";
        $result = mysqli_query($link,$sql_delete);
        if($result == true) {
            echo "Sucessfully marked as spam";
        }
    endforeach;
}

?>