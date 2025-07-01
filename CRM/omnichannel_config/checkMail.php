<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  25 Apr  2024
 * Description: To Update or Delete the Data of Email Facebook  Chat Twitter and SMS
 * 
*/
// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");

$Mid = $_POST['id'];
$type = $_POST['type'];
//$_SESSION['sess_emailid'] = $Mid;

if($type == 'email'){
	$query5 = mysqli_query($link,"select i_reminder,ICASEID,i_DeletedStatus from $db.web_email_information where EMAIL_ID='$Mid' ; ");
	$query5 = mysqli_fetch_row($query5);
	$working= $query5[0];  $caseid= $query5[1]; $del = $query5[2];
	if($del==0 && $working==0 && isset($_POST['del'])){ 
		mysqli_query($link,"update $db.web_email_information set i_DeletedStatus='2' where EMAIL_ID='$Mid'"); 
		echo ""; 
		exit;
	} 
	if($del==2 && $working==0){ echo "Email is already deleted!"; exit; }
	if($caseid>0){ echo "Case is already created. Refresh your queue."; exit;}
	if($working==1){ 
		echo "Someone is working on this Email."; 
	}else{ 
		mysqli_query($link,"update $db.web_email_information set i_reminder=0 where EMAIL_ID='$Mid' ; "); echo ""; 
	}
}
if($type == 'fb'){
	$query5 = mysqli_query($link,"select i_reminder,ICASEID,i_deletestatus from $db.tbl_facebook where id='$Mid' ; ");
	$query5 = mysqli_fetch_row($query5);
	$working= $query5[0];  $caseid= $query5[1]; $del = $query5[2];
	if($del!=0 && $working==0 && isset($_POST['del'])){ mysqli_query($link,"update $db.tbl_facebook set i_deletestatus='0' where id='$Mid'"); echo ""; exit;} 
	if($del==0 && $working==0){ echo "Comment is already deleted!"; exit; }
	if($caseid>0){ echo "Case is already created. Refresh your queue."; exit;}
	if($working==1)
	{ 
		echo "Someone is working on this Comment."; 
	}
	else{ 
		mysqli_query($link,"update $db.tbl_facebook set i_reminder=1 where id='$Mid' ; "); echo ""; 
	 }


}

if($type == 'chat'){
	$chat_session = $_POST['chat_session'];
	$query = "SELECT `i_reminder` from $db.`overall_bot_chat_session` WHERE `chat_session`='$chat_session' ";;
    $result = mysqli_query($link,$query);
	$fetch = mysqli_fetch_row($result);
	$working = $fetch[0];
	if($working==0){ 
		mysqli_query($link,"update $db.overall_bot_chat_session set i_reminder='1' where chat_session='$chat_session'"); 
		exit;
	}else{ 
		echo "Someone is working on this Chat."; 
	}

}

if($type == 'twitter')
{

	$query5 = mysqli_query($link,"select i_reminder,ICASEID,i_Status from $db.tbl_tweet where i_ID='$Mid' ; ");
	$query5 = mysqli_fetch_row($query5);
	$working= $query5[0];  $caseid= $query5[1]; $del = $query5[2];
	if($caseid<0 && $working==1){ mysqli_query($link,"update $db.tbl_tweet set i_reminder='1' where i_ID='$Mid'");}
	if($del!=0 && $working==0 && isset($_POST['del'])){ mysqli_query($link,"update $db.tbl_tweet set i_Status='0' where i_ID='$Mid'"); echo ""; exit;} 
	if($del==0 && $working==0){ echo "Tweet is already deleted!"; exit; }
	if($caseid>0){ echo "Case is already created. Refresh your queue."; exit;}
	if($working==1)
	{ 
		echo "Someone is working on this Tweet."; 
	}
	else{ 
		mysqli_query($link,"update $db.tbl_tweet set i_reminder=1 where i_ID='$Mid' ; "); echo ""; 
	}

}

if($type == 'sms') 
{
	if(isset($_POST['del'])){ 
		$query = mysqli_query($link,"update $db.sms_out_queue set status='0' where id='$Mid'"); 
		echo "Deleted Successfully.";
		exit();
	} 

	if(isset($_POST['id']))
	{
		$query = mysqli_query($link,"select i_reminder from $db.sms_out_queue where id='$Mid'");
		$query5 = mysqli_fetch_row($query);
		$working= $query5[0];
		if($working==0){ 
			mysqli_query($link,"update $db.sms_out_queue set i_reminder='1' where id='$Mid'"); 
			echo ""; 
			exit;
		}else 
		{ 
			echo "Someone is working on this SMS."; 
		}
		
	}

}
?>