<?php
include_once("../../config/web_mysqlconnect.php");
//Auth: Vastvikta Nishad
//Date: 05 Apr 2024
//Description: Function Defined Used in Email Complaint ,Email Enquiry , SMS , Facebook , Whatsapp and Twitter
/**********************************************************functions for  web_email_complaint.php**********************************************************/
if($_POST['action'] == 'data_delete'){
    data_delete(); // delete data function
}
function changeEmailType($groupid,$str){
	global $db,$link;
	if($groupid!='0000' && $groupid!='080000'){                              
	   $str = " and email_type='IN' "; 
	}else{ 	
	   $str = " and email_type='IN' ";
	}
	return $str;
}
//function to change the status 
function setStatus($iallstatus,$str)
{
	global $db,$link;
	if($iallstatus=='0'){
	   $iallstatus1='and i_Update_status='."'".$iallstatus."' and (ICASEID='' || ICASEID IS NULL) $str"; // email_type='IN' and
	}
	else if($iallstatus=='1')
	{
	$iallstatus1='and (i_Update_status='."'".$iallstatus."' or ICASEID>0 ) $str";
	}
	else if($iallstatus=='2')
	{
	$iallstatus1='and i_DeletedStatus='."'".$iallstatus."' $str";
	}
	else if($iallstatus=='4' || empty($iallstatus))
	{
	$iallstatus1=" $str";
	}
	else if($iallstatus==3)
	{
	$iallstatus1 = " and email_type='IN' and (ICASEID='' || ICASEID IS NULL ) "; $notin = "'$centralspoc',"; 
	}
	return $iallstatus1;

}

//function to get email information  for web_email_complaint.php (omnichannel email complaint )
function getEmailInformationComplaint($email, $iallstatus1,$iallstatus,$changestartdatetime, $changeenddatetime, $delcond, $str) {
    global $db,$link;
    $str = "";
    if(isset($_GET['id'])) {
        $str = "EMAIL_ID=" . $_GET['id'];
        $sql = "select * from $db.web_email_information where $str order by d_email_date desc";
    } elseif ($email == "") {
        if ($iallstatus == '') {
            $iallstatus1 = "and i_Update_status='0' and (ICASEID='' || ICASEID IS NULL) $str";
        }
        if ($changestartdatetime == '-- ') {
            $changestartdatetime = date("Y-m-d") . ' ' . "00:00:00";
        }
        if ($changeenddatetime == '1970-01-01') {
            $changeenddatetime = date("Y-m-d 23:59:59");
        }
        $sql = "select * from $db.web_email_information ";
    } elseif ($email != "") {
        if ($changestartdatetime == '-- ') {
            $changestartdatetime = date("Y-m-d") . ' ' . "00:00:00";
        }
        if ($changeenddatetime == '1970-01-01') {
            $changeenddatetime = date("Y-m-d H:i:s");
        }
		// $sql = "select * from $db.web_email_information where d_email_date >='$changestartdatetime' and d_email_date <='$changeenddatetime' and v_fromemail like '%$email%' $iallstatus1  $delcond  and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com')  order by d_email_date desc";
        //using above query results in no record 
        $sql = "select * from $db.web_email_information where d_email_date >='$changestartdatetime' and d_email_date <='$changeenddatetime' and v_fromemail like '%$email%' $iallstatus1  $delcond  and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com')  order by d_email_date desc ";
       
	}
    // Return the SQL query
    return $sql;
}
//function to update the email information 
function updateEmailInformation($ICASEID, $frommail , $emailid , $Customerid , $where_c, $case_id) {
  global $db,$link;
if(!empty($ICASEID))
	{
	$qq= "select iCaseStatus, ticketid, iPID, vCustomerID,regional  from $db.web_problemdefination where ( $where_c) ";
		//echo "Orginal Case ID  ".$ICASEID."<br>Query".$qq;
		$q1=mysqli_query($link,$qq);
		$numRows=mysqli_num_rows($q1);
		$fetch1=mysqli_fetch_array($q1);
		$case_id=$fetch1['iPID'];
		$caseee=$fetch1['ticketid'];
		$Customerid =$fetch1['vCustomerID'];
		$regional = $fetch1['regional'];
		$regional_stg ='';
		if(!empty($regional)){
			$regional_stg = $regional.'_';
		}

		$qcust=mysqli_fetch_array(mysqli_query($link,"select email from $db.web_accounts where AccountNumber='".$Customerid."'"));
		if( ($qcust['email']==$frommail)  && $qcust['email']!='')
		{
		$q= "update $db.web_email_information set ICASEID='".$caseee."',email_test='web_queue_1' where EMAIL_ID='".$emailid."' ; ";
		mysqli_query($link,$q);
		}//customer mail is equal to from mail
		$ress = mysqli_fetch_array(mysqli_query($link,"select iCaseStatus,ticketid from $db.web_problemdefination where (iPID='$case_id') "));

	
	$rest=mysqli_fetch_array(mysqli_query($link,"select ticketstatus from $db.web_ticketstatus where id='".$ress['iCaseStatus']."' ; "));
	$status = $rest['ticketstatus'];
}
}
//function to get email details  for subjectpopup.php 


function getCaseDetails($ICASEID, $vfromemail, $Customerid, $where_c)
{
	global $db,$link;
    if (!empty($ICASEID)) {
        $where_c = " ticketid ='$ICASEID'";
        $qq = "select iCaseStatus, ticketid, iPID, vCustomerID from $db.web_problemdefination where ( $where_c) ";
        $q1 = mysqli_query($link, $qq);
        $numRows = mysqli_num_rows($q1);
        $fetch1 = mysqli_fetch_array($q1);
        $case_id = $fetch1['iPID'];
        $caseee = $fetch1['ticketid'];
        $Customerid = $fetch1['vCustomerID'];

        $qcust = mysqli_fetch_array(mysqli_query($link, "select email from $db.web_accounts where AccountNumber='" . $Customerid . "'"));

        if (($qcust['email'] == $vfromemail) && $qcust['email'] != '') {
            $q = "update $db.web_email_information set ICASEID='" . $caseee . "',email_test='web_queue_1' where EMAIL_ID='" . $vfromemail . "' ; ";
            mysqli_query($link, $q);
        }

        $ress = mysqli_fetch_array(mysqli_query($link, "select iCaseStatus,ticketid from $db.web_problemdefination where (iPID='$case_id') "));
        $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $ress['iCaseStatus'] . "' ; "));
        $status = $rest['ticketstatus'];
        
        return $status;
    }

    return null;
}
//function  for web_email_information.php

function getEmailInformationEnquiry( $email, $iallstatus1,$iallstatus,$changestartdatetime, $changeenddatetime, $delcond) {
	global $db,$link;
    if ($email == "") {
        if ($iallstatus == '') {
            $iallstatus1 = 'and i_Update_status=' . "'0' and (ICASEID='' || ICASEID IS NULL) $str";
        }

        if ($changestartdatetime == '-- ') {
            $changestartdatetime = date("Y-m-d") . ' ' . "00:00:00";
        }

        if ($changeenddatetime == '1970-01-01') {
            $changeenddatetime = date("Y-m-d H:i:s");
        }

        $sql = "select * from $db.web_email_information";
    } else {
        if ($changestartdatetime == '-- ') {
            $changestartdatetime = date("Y-m-d") . ' ' . "00:00:00";
        }
        if ($changeenddatetime == '1970-01-01') {
            $changeenddatetime = date("Y-m-d H:i:s");
        }

        $sql = "select * from $db.web_email_information where d_email_date >='$changestartdatetime' and d_email_date <='$changeenddatetime' and v_fromemail like '%$email%' $iallstatus1  $delcond  and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com')  order by d_email_date desc";
    }

    $res = mysqli_query($link, $sql) or die(mysqli_error($link));

    return $res;
}
//function to show the classification category 
function getClassificationLabel($classification) {
    if ($classification == '1') { //if classification vlaue  1 show servicable 
        return "Servicable";
    } else if ($classification == '2') { //if classification value 2 show non servicable
        return "Non Servicable";
    } else if ($classification == '3') { //if classification value 3 show Spam
        return "Spam";
    } else if ($classification == '4') {//if classification value 4 show Inquiry 
        return "Inquiry";
    } else { //if classification not defined  then return empty string 
        return " ";
    }
}
//function to  change the  background color of the fields on the basis of the Flag Value 
function getFlagColorStyle($flag) {
    if ($flag == 1) {//yellow color 
        return "background:#f1a00bd4; color:#fff;";
    } else if ($flag == 2) {//green color 
        return "background:#228b22de; color:#fff;";
    } else  {//by defalut red color 
        return "background:#e34234db; color:#fff;";
    }
}
//function for subjectpopup.php 
function getCustomerIdByEmail($vfromemail) {
	global $db,$link;
    $query = "SELECT AccountNumber FROM $db.web_accounts WHERE (email LIKE '%$vfromemail%')";
    $result = mysqli_query($link, $query);
    if ($result) {
        $row = mysqli_fetch_row($result);
        if ($row) {
            return $row[0]; // Return AccountNumber
        } else {
            return null; // No matching records found
        }
    } else {
        return null; // Query execution failed
    }
}
//function to return the link to reply mail 
function generateLink($customerid, $vfromemail, $Mid) {
    if ($customerid != '') {
        return "onclick=\"cheackMail('new_case_manual.php?customerid=$customerid&emailid=$Mid&mr=6');\"";
    } else {
        return "onclick=\"cheackMail('new_case_manual.php?email=$vfromemail&emailid=$Mid&mr=6');\"";
    }
}
//function to set status  for web_twitter.php page
function setStatusTwitter($iallstatus)
{
	if ($iallstatus == '0')
    {
        $iallstatus1 = 'and i_Update_status=' . "'" . $iallstatus . "'";
    }
    else if ($iallstatus == '1')
    {
        $iallstatus1 = 'and i_Update_status=' . "'" . $iallstatus . "'";
    }
    else if ($iallstatus == '2')
    {
        $iallstatus1 = 'and i_DeletedStatus=' . "'" . $iallstatus . "'";
    }
    else 
    {
        $iallstatus1 = '';
    }
    return $iallstatus1;
} 
// Function to check the number of unread direct messages for a Twitter user.
function check_unreadDM($tw_usrID, $db)
{
    global $link, $db; // Accessing global variables $link and $db
    // SQL query to count unread direct messages for the specified user
    $slq = "SELECT COUNT(*) AS cnt_status FROM $db.web_twitter_directmsg WHERE read_status = 0 
            AND (recipient_id = '" . $tw_usrID . "' OR sender_id = '" . $tw_usrID . "')";
    // Executing the SQL query
    $query = mysqli_query($link, $slq);
    // Fetching the result row as an array
    $fetch_st = mysqli_fetch_row($query);
    // Returning the count of unread direct messages
    return $fetch_st[0];
}
// Function to get the total number of direct messages for a Twitter user.
function get_twuserDm($tw_usrID, $db)
{
    global $link, $db; // Accessing global variables $link and $db
    // SQL query to count all direct messages involving the specified user (as either recipient or sender)
    $slq = "SELECT COUNT(*) AS cnt_status FROM $db.web_twitter_directmsg WHERE (recipient_id = '" . $tw_usrID . "' OR sender_id = '" . $tw_usrID . "')";
    // Executing the SQL query
    $query = mysqli_query($link, $slq);
    // Fetching the result row as an array
    $fetch_st = mysqli_fetch_row($query);
    // Returning the total count of direct messages
    return $fetch_st[0];
}
function fetchTweetId(){
	// Accessing global variables $link and $db
	global $db,$link;
	// SQL query to select distinct screen names
    $sql = "SELECT v_Screenname FROM $db.tbl_tweet WHERE v_Screenname != '' GROUP BY v_Screenname ORDER BY v_Screenname ASC";
    // Executing the SQL query
    $query = mysqli_query($link, $sql);
    // Returning the result set
    return $query;
}  
function update_case_twitterID($in_reply_to_status_id, $link, $db, $caseid)
{
    if (!empty($caseid) && !empty($in_reply_to_status_id))
    {
        $sqlt = "select i_ID from $db.tbl_tweet where i_TweetID='" . $in_reply_to_status_id . "' and ICASEID=0 AND irrelevant_status=0 ";
        $query = mysqli_query($link, $sqlt) or die(mysqli_error($link));
        $numrows = mysqli_num_rows($query);
        if ($numrows)
        {
            $fetch_r = mysqli_fetch_row($query);
            $i_ID = $fetch_r[0];
            $update_t = "UPDATE $db.tbl_tweet SET  ICASEID='" . $caseid . "' 
                                        where i_ID='" . $i_ID . "' ";
            mysqli_query($link, $update_t) or die(mysqli_error($link));
        }
    }

    if (!empty($in_reply_to_status_id) && empty($caseid))
    {
        $sqlt = "select ICASEID from $db.tbl_tweet where i_TweetID='" . $in_reply_to_status_id . "' and ICASEID!=0 AND irrelevant_status=0   ";
        $query = mysqli_query($link, $sqlt) or die(mysqli_error($link));
        if (mysqli_num_rows($query))
        {
            $rowt = mysqli_fetch_row($query);
            if ($rowt[0])
            {
                $update_t = "UPDATE $db.tbl_tweet SET  ICASEID='" . $rowt[0] . "' 
                                        where in_reply_to_status_id='" . $in_reply_to_status_id . "' ";
                mysqli_query($link, $update_t) or die(mysqli_error($link));

            }
        } //END OF NUM ROWS

    }
}

function build_irrelevant_status_query($post)
{
    if (!empty($post['irrelevant_status'])) {
        $str_i = " AND irrelevant_status='" . $post['irrelevant_status'] . "' ";
    } else {
        $str_i = "";
    }
    return $str_i;
}

function build_allstatus_query($post)
{
    if (isset($post['allstatus'])) {
        $st = $post['allstatus'];
        if ($st == 4) return "";
        if ($st == 0) return " (ICASEID='' || ICASEID=0)";
        if ($st == 1) return " ICASEID>0";
        if ($st == 2) return " i_Status=0";
    }
    return " i_Status=1";
}

function build_v_screenname_query($post)
{
    $str = "";
    if (!empty($post['v_Screenname'])) {
        $str .= " and (v_Screenname='" . $post['v_Screenname'] . "' OR v_TweeterDesc like '%" . $post['v_Screenname'] . "%' )";
    }
    return $str;
}

function build_twitter_handlers_query($post)
{
    $str = "";
    if ($post['twitter_handlers'] == 'Y') {
        $str .= " and (v_Screenname !='lusaka_water' )";
    } else if ($post['twitter_handlers'] == 'N') {
        $str .= " and (v_Screenname ='lusaka_water' )";
    }
    return $str;
}

function build_datetime_query($post)
{
    $str = "";
    if (!isset($post['startdatetime']) && !isset($post['enddatetime'])) {
        $str .= " and d_TweetDateTime>='" . date("Y-m-01 00:00:00") . "' and d_TweetDateTime<='" . date("Y-m-d 23:59:59") . "'";
    } else {
        $str .= " and d_TweetDateTime>='" . date("Y-m-d 00:00:00", strtotime($post['startdatetime'])) . "' and d_TweetDateTime<='" . date("Y-m-d 23:59:59", strtotime($post['enddatetime'])) . "'";
    }
    return $str;
}
function getTwitterId(){
    global $db,$link;
    $sql_cdr = "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1";
    $query = mysqli_query($link,$sql_cdr);
    return $query;
}
function selectTweets($id){
    global $db,$link;
    if(isset($id)){
    $strid = "i_ID=".$id;
    $sql = "select * from $db.tbl_tweet where $strid order by d_TweetDateTime desc ";
    }else{          
    $sql = "select * from $db.tbl_tweet";
    }
  
    $res = mysqli_query($link, $sql) or die('MYSQL Error : '. mysqli_error($link));
    return $res;
}

function subject_decode_string($subject)
{

  $utf = substr($subject, 0, 10);
  if(strcasecmp($utf, "=?utf-8?B?") == '0')
  {
    // $subject = base64_decode(str_ireplace("=?UTF-8?", "",$subject));
    $d = str_ireplace("=?utf-8?B?", "",$subject);//echo  $d."<br>";
    return base64_decode($d);
  }
  return $subject;


}
//function used in email complaint and enquiry
function getCaseID_Subject($subject)
   {
   
     $subject=string_between_two_string($subject,"[","]");   // TICKET # 200056
   
     // if (strpos($subject,'LWSC/') !== false)    // this will work for another formate so dont remove it. : 29-12-2020 vijay
   
     if (strpos($subject,'TICKET #') !== false) 
     {
         
       $spos=strpos($subject, "#");
       $epos=strlen($subject);
       //echo $subject."<br>poc is avail  --Start pos".$spos." end pos". $epos."<br>";
       $caseID = substr($subject, $spos+1,$epos); 
      
     }else{
       $caseID = "";
     }
   
     return $caseID;
   }
   

//Auth: Ritu Modi
//Date: 06-04-2024
// Function to fetch chat sessions based on provided parameters
function fetchChatSessions($startdatetime, $enddatetime, $phone = null, $id = null) {
    global $link,$db; // Global database connection object
    // Check if start datetime is provided in request, otherwise use parameter value
    if (!empty($_REQUEST['sttartdatetime'])) { 
        $startdatetime = $_REQUEST['sttartdatetime']; 
    } else {  
        $startdatetime = $startdatetime; 
    }
    // Check if end datetime is provided in request, otherwise use parameter value
    if (!empty($_REQUEST['enddatetime'])) { 
        $enddatetime = $_REQUEST['enddatetime']; 
    } else {  
        $enddatetime = $enddatetime; 
    }
    $condition = ""; // Initialize condition for SQL query
    $from = date('Y-m-d H:i:s', strtotime($startdatetime)); // Convert start datetime to SQL format
    $to = date('Y-m-d H:i:s', strtotime($enddatetime)); // Convert end datetime to SQL format
    // Add condition for datetime range if both start and end datetimes are provided
    if ($startdatetime != '' && $enddatetime != '') { 
        $condition .= " AND `createdDatetime` >='$from' AND `createdDatetime` <='$to'  "; 
    }
    // Add condition for filtering by phone number if provided
    if ($phone) { 
        $condition .= " AND `from`='$phone'"; 
    }
    // Build SQL query based on provided parameters
    if ($id !== null) {
        $str = "id=$id";
        $sql = "SELECT * FROM $db.`overall_bot_chat_session`  ORDER BY `createdDatetime` DESC";
    } else {                        
        $sql = "SELECT * FROM $db.`overall_bot_chat_session` WHERE `chat_session`!='' AND `delete_status`='0' $condition ORDER BY `createdDatetime` DESC";
               // echo $sql; die;
    }
    // Execute SQL query and return result
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));
    return $res;
}
//Auth: Ritu Modi
//Date: 06-04-2024
// Function to check if phone number exists in web_accounts table
function checkPhoneNumberExists($phone_number) {
    global $db, $link; // Global database name and connection object
    // Construct SQL query to check if phone number exists in web_accounts table
    $sql = "SELECT * FROM $db.web_accounts WHERE phone ='$phone_number'";
    // Execute SQL query
    $result = mysqli_query($link, $sql);
    return $result; // Return query result
}

//Auth: Ritu Modi
//Date: 08-04-2024
// updated the function to fetch the data according to the chat session id of the user [vastvikta][15-04-2025]
function getChatSessions($link,$phone,$session_id) {
    global $link,$db;
    $chatSessions = array(); // Initialize empty array
    $chat_session_query = "SELECT chat_session, session_start_time, content_text FROM $db.overall_bot_chat_session WHERE `from` ='$phone' AND `chat_session` = '$session_id' ORDER BY session_start_time";
    $chatSessions=mysqli_query($link, $chat_session_query);
    return $chatSessions; // Return the array of chat sessions
}
// Function to delete data
function data_delete() {
    global $link,$db; // Global database connection object
    $id = $_POST['id'];
    // Define SQL query
    $sql_delete = "UPDATE $db.`overall_bot_chat_session` SET `delete_status`='1' WHERE id='$id'";
    // Execute SQL query
    mysqli_query($link, $sql_delete) or die("Invalid:overall_bot_chat_session " . mysqli_error($link));
    echo json_encode("Sucessfuly delete");
}
// Function to get SQL condition based on status
function getStatusQuery($iallstatus) {
    if ($iallstatus == '0' || $iallstatus == '1') {
        $iallstatus1 = 'and i_Update_status=' . "'" . $iallstatus . "'";
    } else if ($iallstatus == '2') {
        $iallstatus1 = 'and i_DeletedStatus=' . "'" . $iallstatus . "'";
    } else if ($iallstatus == '4') {
        $iallstatus1 = '';
    }
    return $iallstatus1;
}
// Function to get the selected view based on user's selection
function getSelectedView($selection1) {
    switch ($selection1) {
        case 1:
            return 'Previous Month';
            break;
        case 2:
            return 'This Month';
            break;
        case 3:
            return 'This Week';
            break;
        case 4:
            return 'Last Week';
            break;
        case 6:
            return 'ALL';
            break;
        case 7:
            return 'Today';
            break;
        default:
            return 'Unknown';
            break;
    }
}
// Function to get chat session data based on filters
function getChatSessionData() {
    global $link,$db;
    $count=0;
    if(isset($_POST['allstatus']))
    {
     $st = $_POST['allstatus'];
     if($st==4) $str = "";
     if($st==0) $str = " and (ICASEID='' || ICASEID=0)";
     if($st==1) $str = " and ICASEID>0";
     if($st==2) $str = " and i_deletestatus=0";
    }
    else{ $str = " and i_deletestatus=1"; }
     if(!isset($_POST['startdatetime']) && !isset($_POST['enddatetime'])) 
     $str .= " and createddate>='".date("Y-m-d 00:00:00")."' and createddate<='".date("Y-m-d H:i:s")."'";
    else
     $str .= " and createddate>='".date("Y-m-d 00:00:00",strtotime($_POST['startdatetime']))."' and createddate<='".date("Y-m-d H:i:s",strtotime($_POST['enddatetime']))."'";
    // Execute the query
    $query_data = "SELECT * FROM $db.bot_chat_session WHERE agent_forworded = '1' ORDER BY id DESC";
       // echo $query_data; die;
    $res = mysqli_query($link, $query_data) or die(mysqli_error($link));

    return $res;
    }

// Fetching facbook details 
function Fecebooki_listing(){
    global $db, $link; 
    $count=0;
   if(isset($_POST['allstatus']))
   {
       $st = $_POST['allstatus'];
       if($st==4) $str = "";
       if($st==0) $str = " and (ICASEID='' || ICASEID=0)";
       if($st==1) $str = " and ICASEID!='' ";
       if($st==2) $str = " and i_deletestatus=0";
   }
   else{ $str = " and i_deletestatus=1"; }
   
   if(!isset($_POST['startdatetime']) && !isset($_POST['enddatetime'])) 
       $str .= " and createddate>='".date("Y-m-01 00:00:00")."' and createddate<='".date("Y-m-d H:i:s")."'";
   else
       $str .= " and createddate>='".date("Y-m-01 00:00:00",strtotime($_POST['startdatetime']))."' and createddate<='".date("Y-m-d H:i:s",strtotime($_POST['enddatetime']))."'";
   
   $select_qry = mysqli_query($link,"select * from $db.tbl_facebook where id!='' and comment_type IS null  and userid!='' $str order by createddate desc");
   $facebook_data = array();
   $i=0;
   while($row = mysqli_fetch_array($select_qry))
   {
       $caseee = $row['ICASEID'];
       $count=$count+1; 
       $check="check".$count;
       if($count%2==0) $clr="#efefef"; else $clr="#ffffff";

        $facebook_data['parent'][$i]['id'] = $row['id'];        
        $facebook_data['parent'][$i]['name'] = $row['name'];
        $facebook_data['parent'][$i]['comment'] = $row['comment'];
        $facebook_data['parent'][$i]['createddate'] = $row['createddate'];
        $facebook_data['parent'][$i]['comment_id'] = $row['comment_id'];
        $facebook_data['parent'][$i]['post_id'] = $row['post_id'];
        $facebook_data['parent'][$i]['status'] = $row['status'];
        $facebook_data['parent'][$i]['post'] = $row['post'];
        $facebook_data['parent'][$i]['attachment'] = $row['attachment'];
        $facebook_data['parent'][$i]['flag_read_unread'] = $row['flag_read_unread'];
        $post_id = $row['post_id'];
        $select_qry2 = mysqli_query($link,"select * from $db.tbl_facebook where id!='' and i_deletestatus!='0' and comment_type IS null  and post_id='{$post_id}' and userid='' and parent_comment_id='' $str order by createddate desc");
        $num_rows=mysqli_num_rows($select_qry2);
        if($num_rows>0){
            $k=0;
            while($childlist = mysqli_fetch_array($select_qry2)){
                $facebook_data['parent'][$i]['child'][$k]['id'] = $childlist['id'];     
                $facebook_data['parent'][$i]['child'][$k]['name'] = $childlist['name'];
                $facebook_data['parent'][$i]['child'][$k]['comment'] = $childlist['comment'];
                $facebook_data['parent'][$i]['child'][$k]['createddate'] = $childlist['createddate'];
                $facebook_data['parent'][$i]['child'][$k]['comment_id'] = $childlist['comment_id'];
                $facebook_data['parent'][$i]['child'][$k]['post_id'] = $childlist['post_id'];
                $facebook_data['parent'][$i]['child'][$k]['status'] = $childlist['status'];
                $facebook_data['parent'][$i]['child'][$k]['post'] = $childlist['post'];
                $facebook_data['parent'][$i]['child'][$k]['attachment'] = $childlist['attachment'];
                $facebook_data['parent'][$i]['child'][$k]['flag_read_unread'] = $childlist['flag_read_unread'];
                $k++;
            }
        }
        $i++;
    }
    return $facebook_data;
}
?>
