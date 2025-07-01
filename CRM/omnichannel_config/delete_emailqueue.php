<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  05 Apr 2024
 * Description: To Delete Email for Email complaint and Email Enquiry
 * 
*/
include_once("../../config/web_mysqlconnect.php");

$del=$_POST['id'];
//print_r($id);
if($del):
foreach ($del as $key => $id):
    $sql_delete="UPDATE $db.web_email_information SET i_DeletedStatus='2' WHERE EMAIL_ID='$id'";
    mysql_query($sql_delete);
    echo '1';
endforeach;
endif;

?>