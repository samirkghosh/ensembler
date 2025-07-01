<?php
include("../../config/web_mysqlconnect.php"); // database file include
include_once("live_chat_connection.php");

$Startdate   = date('Y-m-d ').'00:00:00';
$Enddate      = date('Y-m-d ').'23:59:59';

########################################################  (Notifications)  ################################################################################

// facebook
$querytfb="SELECT count(*) as totalfb FROM $db.tbl_facebook WHERE ICASEID='0' and i_deletestatus !='2' and createddate between '$Startdate' and '$Enddate'";
$queryOrderfb=mysqli_fetch_assoc(mysqli_query($link,$querytfb));
$fb_count=$queryOrderfb['totalfb'];


// email
$querytmail="SELECT count(*) as total FROM $db.web_email_information WHERE I_Status='1' and i_DeletedStatus !='1' and d_email_date between '$Startdate' and '$Enddate' and Flag=0 and email_type='IN'";
$queryOrdermail=mysqli_fetch_assoc(mysqli_query($link,$querytmail));
$mail_count=$queryOrdermail['total'];

// twitter
$querytwit= "SELECT count(*) as totaltwit from $db.tbl_tweet where i_Status=1 and d_TweetDateTime>='$Startdate' and d_TweetDateTime<='$Enddate' order by i_ID";
$queryOrdertwitt=mysqli_fetch_assoc(mysqli_query($link,$querytwit));
$tweet_count=$queryOrdertwitt['totaltwit'];

// chat
$querychat="SELECT count(*) as total FROM $db.bot_chat_session WHERE agent_forworded ='1' order by id desc";
$chat=mysqli_fetch_assoc(mysqli_query($link,$querychat));
$chat_count= $chat['total'];
// sms
$querysms="SELECT count(*) as total FROM $db.tbl_smsmessagesin WHERE i_status='1' and d_timeStamp between '$Startdate' and '$Enddate' and Flag=0 ";
$queryOrdersms=mysqli_fetch_assoc(mysqli_query($link,$querysms));
$sms_count=$queryOrdersms['total'];


// whatsapp code for notification [Aarti][04-06-2024] 
$querywhatsapp="SELECT count(*) as total FROM $db.whatsapp_in_queue WHERE status='0' AND create_date BETWEEN
            '$Startdate' AND '$Enddate' AND flag='0'";
$queryOrderwhatsapp=mysqli_fetch_assoc(mysqli_query($link,$querywhatsapp));
$whatsapp_count=$queryOrderwhatsapp['total'];

// messenger code for notification [Aarti][20-08-2024] 
$querymessenger="SELECT count(*) as total FROM $db.messenger_in_queue WHERE status='0' AND create_date BETWEEN
            '$Startdate' AND '$Enddate' AND flag='0'";
$queryOrdemes=mysqli_fetch_assoc(mysqli_query($link,$querymessenger));
$messenger_count=$queryOrdemes['total'];

// Instagram code for notification [Aarti][19-10-2024] 
$querymessenger="SELECT count(*) as total FROM $db.instagram_in_queue WHERE status='0' AND create_date BETWEEN
            '$Startdate' AND '$Enddate' AND flag='0'";
$queryOrdemes=mysqli_fetch_assoc(mysqli_query($link,$querymessenger));
$instagram_count=$queryOrdemes['total'];
//Misscall count code for notification  updated [vastvikta][03-02-2025]

$startdatetime = isset($_POST['startdatetime']) ? date("Y-m-d 00:00:00",strtotime($_POST['startdatetime'])) : date("Y-m-d 00:00:00");
$enddatetime = isset($_POST['enddatetime']) ? date("Y-m-d 23:59:59",strtotime($_POST['enddatetime'])) : date("Y-m-d 23:59:59");

$querymisscall = "SELECT COUNT(*) AS total FROM $db_asterisk.autodial_closer_log  WHERE call_date >='$startdatetime' AND call_date <= '$enddatetime' AND (status = 'AGENT DROP' OR status = 'DROP')  AND call_back = '0'";
// echo $querymisscall;
$queryOrdemes=mysqli_fetch_assoc(mysqli_query($link,$querymisscall));
$misscall_count=$queryOrdemes['total'];

############################################################################################################################################################

$data['fbcount']=$fb_count;
$data['mailcount']=$mail_count;
$data['tweetcount']=$tweet_count;
$data['chatcount']=$chat_count;
$data['smscount']=$sms_count;
$data['whatsapp_count'] = $whatsapp_count; // for whatsapp changes[Aarti][21-08-2024]
$data['fbMcount']=$messenger_count; // for messenger changes[Aarti][21-08-2024] 
$data['instagram_count'] = $instagram_count; // for whatsapp changes[Aarti][19-10-2024]
$data['misscall_count'] = $misscall_count; //for missedcall count [vastvikta][16-12-2024]
echo json_encode(array('status' => 'success', 'info' => $data));die();


?>