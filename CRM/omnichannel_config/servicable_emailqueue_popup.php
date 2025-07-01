<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  21 Apr 2024
 * Description:To Classify the email into differnt Services  in  case of POP up 
 * 
*/
include_once("../../config/web_mysqlconnect.php");
$id=$_POST['id'];
$val=$_POST['value'];
if($val == 1){   
    $sql="UPDATE $db.web_email_information SET classification='1' WHERE EMAIL_ID='$id'";
    mysqli_query($link,$sql);
    echo '1';exit();
}else if($val == 2){
    $sql="UPDATE $db.web_email_information SET classification='2' WHERE EMAIL_ID='$id'";
    mysqli_query($link,$sql);
    echo '2';exit();

}else if($val == 3){
    $sql="UPDATE $db.web_email_information SET classification='3' WHERE EMAIL_ID='$id'";
    mysqli_query($link,$sql);
    echo '2';exit();

}else if($val == 4){
    $sql="UPDATE $db.web_email_information SET classification='4' WHERE EMAIL_ID='$id'";
    mysqli_query($link,$sql);
    echo '2';exit();

}
// For all channel disposition add
if($_POST['action'] == 'dispostion_channel_insert'){
    $createdby        =  $_SESSION['userid'];
    $created_date = date("Y-m-d H:i:s");
    $remarks        =  $_POST['remarks'];
    $channel_id        =  $_POST['channel_id'];
    $send_from        =  $_POST['send_from'];
    $disposition_type        =  $_POST['dispostion_type'];
    $channel_type = $_POST['channel_type'];
    //disposition channel insert data
    // get dispostion table details 
    $query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$channel_id' and channel_type = '$channel_type'");
    if(mysqli_num_rows($query_dis) > 0){
        $update_customer = "update $db.multichannel_disposition set disposition_type='$disposition_type' ,remarks='$remarks' where channel_id='$channel_id' and channel_type = '$channel_type'";
        mysqli_query($link, $update_customer) or die("Error In Query2 " . mysqli_error($link));
    }else{  
        $insert_customer_query = "insert into $db.multichannel_disposition(channel_type, disposition_type, remarks,created_date, channel_id, createdby) values('$channel_type','$disposition_type','$remarks','$created_date','$channel_id','$createdby')  ";
        mysqli_query($link, $insert_customer_query) or die("Error In Query2 " . mysqli_error($link));
    }
    // mail read status update
     //[vastvikta][03-12-2024]changed the condition so that all flags get marked 
    if($channel_type == 'Email'){
        mysqli_query($link,"update $db.web_email_information set Flag='1' where EMAIL_ID='$channel_id'");
    }else if($channel_type == 'SMS'){
        mysqli_query($link,"update $db.tbl_smsmessagesin set Flag='1' WHERE i_id='$channel_id'; ");
    }else if($channel_type == 'Twitter'){
        mysqli_query($link,"update $db.tbl_tweet set Flag='1' where i_ID='$channel_id'");
    }else if($channel_type == 'Whatsapp'){
        mysqli_query($link,"update $db.whatsapp_in_queue set flag='1' where send_from='$send_from'");
    }else if($channel_type == 'Facebook Messenger'){
        mysqli_query($link,"update $db.messenger_in_queue set flag='1' where send_from='$send_from'");
    }else if($channel_type == 'Facebook'){  
        mysqli_query($link,"update $db.tbl_facebook set Flag='1' where id='$channel_id'");
    }else if($channel_type == 'web_chat'){  
        mysqli_query($link,"update $db.overall_bot_chat_session set bot_agent_flag='0' where id='$channel_id'");
    }else if($channel_type == 'Instagram Messenger'){  
        mysqli_query($link,"update $db.instagram_in_queue set flag='1' , status='1' where send_from='$send_from'; ");
    }
}
//[vastvikta nishad][23-11-2024] code for setting sentiment 
if($_POST['action'] == 'sentiment'){
    $channel_type = $_POST['channel_type'];
    $channel_id        =  $_POST['channel_id'];
    $sentiment = $_POST['sentiment'];
    echo $channel_id;
    echo $channel_type;
    echo $sentiment;
    if ($channel_type == 'web_chat') {
        $query = "UPDATE $db.overall_bot_chat_session 
                  SET sentiment = '$sentiment' 
                  WHERE id = '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }if ($channel_type == 'Facebook Messenger') {
        $query = "UPDATE $db.messenger_in_queue 
                  SET sentiment = '$sentiment' 
                  WHERE id = '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }
    if ($channel_type == 'Instagram Messenger') {
        $query = "UPDATE $db.instagram_in_queue 
                  SET sentiment = '$sentiment' 
                  WHERE id = '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }
    if ($channel_type == 'SMS') {
        $query = "UPDATE $db.tbl_smsmessagesin
                  SET sentiment = '$sentiment' 
                  WHERE i_id = '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }
    if ($channel_type == 'Whatsapp') {
        $query = "UPDATE $db.whatsapp_in_queue
                  SET sentiment = '$sentiment' 
                  WHERE id = '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }
    if ($channel_type == 'Twitter') {
        $query = "UPDATE $db.tbl_tweet 
                  SET sentiment = '$sentiment' 
                  WHERE i_ID= '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }
    if ($channel_type == 'Email') {
        $query = "UPDATE $db.web_email_information 
                  SET sentiment = '$sentiment' 
                  WHERE EMAIL_ID= '$channel_id'";
    
        if (mysqli_query($link, $query)) {
            echo "Query executed successfully.";
        } else {
            echo "Error executing query: " . mysqli_error($link);
        }
    }


}
?>