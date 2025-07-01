<!--
Document: Voice Mails Form with DataTables
Author: Ritu Modi
Date: 22-03-2024
This form displays voice mail records with the ability to filter by start and end datetime and read/unread status.
-->
<?php
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); //database connection files
if(isset($_POST['id'])){
    $id = $_POST['id'];
    $update = "UPDATE $db_asterisk.tbl_cc_voicemails SET flag1='1' WHERE id='$id'";
    $res = mysqli_query($link,$update) or die("Error in query".mysqli_error($link));
    if($res){
        echo json_encode(array("status"=>"success","message"=>"flag is updated"));die();
    }else{
        echo json_encode(array("status"=>"success","message"=>"failed to open file"));die();
    }
}
?>