
<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  21 Apr 2024
 * Description:To Classify the email into differnt Services 
 * 
*/
include_once("../../config/web_mysqlconnect.php");
$arr=$_POST['id'];
$val=$_POST['value'];

if($val == 1)
{
    foreach ($arr as $key => $id):

        if($id == 'on')
        {
            continue;
        }
        $sql="UPDATE $db.web_email_information SET classification='1' WHERE EMAIL_ID='$id'";
        mysqli_query($link,$sql);
    endforeach;
    echo '1';exit();

}else if($val == 2)
{
    foreach ($arr as $key => $id):

        if($id == 'on')
        {
            continue;
        }
        $sql="UPDATE $db.web_email_information SET classification='2' WHERE EMAIL_ID='$id'";
        mysqli_query($link,$sql);
    endforeach;
    echo '2';exit();

}else if($val == 3)
{
    foreach ($arr as $key => $id):

        if($id == 'on')
        {
            continue;
        }
        $sql="UPDATE $db.web_email_information SET classification='3' WHERE EMAIL_ID='$id'";
        mysqli_query($link,$sql);
    endforeach;
    echo '2';exit();

}
else if($val == 4)
{
    foreach ($arr as $key => $id):

        if($id == 'on')
        {
            continue;
        }
        $sql="UPDATE $db.web_email_information SET classification='4' WHERE EMAIL_ID='$id'";
        mysqli_query($link,$sql);
    endforeach;
    echo '2';exit();

}



?>