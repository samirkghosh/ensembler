<?php
/**
* Auth: Ritu modi 
* Date- 20-03-24
* Description: This file displays the CSAT (Customer Satisfaction) and DSAT (Dissatisfaction) dashboard for agents. It allows users to view CSAT, DSAT, NPS (Net Promoter Score), and customer effort reports within a specified date range.
*/
include("../../config/web_mysqlconnect.php");
include_once("../web_function.php");
include_once("live_chat_connection.php");
if($_POST['Startdate'] !='' && $_POST['Enddate']!='')
{
 extract($_POST);
 $Startdate = date("Y-m-d H:i:s", strtotime($Startdate));
 $Enddate =  date("Y-m-d H:i:s", strtotime($Enddate));

}else
{
 $Startdate   = date('Y-m-d ').'00:00:00';
 $Enddate      = date('Y-m-d ').'23:59:59';
}
function getTimeInFormated($seconds)
{
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
}

////////////////////////////////////CRM/////////////////////////////////////////////////

$data = [];
//Today condition
$today ="AND d_createDate >= '$Startdate' AND d_createDate <= '$Enddate'";

//total cases
$total_cases=mysqli_query($link,"SELECT count(*) as total from $db.web_problemdefination where d_createDate >= '$Startdate' AND d_createDate <= '$Enddate'");
$cases_cnt=mysqli_fetch_assoc($total_cases);

//compalint
$total_comp=mysqli_query($link,"SELECT count(*) as total from $db.web_problemdefination  where vCaseType='complaint' $today");
$comp_cnt=mysqli_fetch_assoc($total_comp);

//inquiry
$total_inq=mysqli_query($link,"SELECT count(*) as total from $db.web_problemdefination  where vCaseType='Inquiry' $today");
$inq_cnt=mysqli_fetch_assoc($total_inq);

//other
$total_other=mysqli_query($link,"SELECT count(*) as total from $db.web_problemdefination  where vCaseType='others' $today");
$other_cnt=mysqli_fetch_assoc($total_other);

//pending
$pending=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where iCaseStatus='1' $today");
$pending_cnt=mysqli_fetch_assoc($pending);

//closed
$closed=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where iCaseStatus='3' $today");
$closed_cnt=mysqli_fetch_assoc($closed);

//resolved
$resolved=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where iCaseStatus='8' $today");
$resolved_cnt=mysqli_fetch_assoc($resolved);


//reopened
$reopened=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where iCaseStatus='5' $today");
$reopened_cnt=mysqli_fetch_assoc($reopened);

//assigned
$assigned=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where iCaseStatus='1' and vCaseType='complaint' and vProjectID > '0' $today ");
$assigned_cnt=mysqli_fetch_assoc($assigned);

//escalated
$escalated=mysqli_query($link,"SELECT count(*) as  total from $db.web_problemdefination  where escalate_status > '0' $today");
$escalated_cnt=mysqli_fetch_assoc($escalated);


//category
$category=mysqli_query($link,"SELECT vCategory, count(*) as catgeory_cnt from $db.web_problemdefination where vCustomerID !='' $today group by vCategory");
$num_complaint=mysqli_num_rows($category);

if($num_complaint > 0 ):
        $arrname=[];
        $ct=[];
        $arrcount=[];
        while($res = mysqli_fetch_array($category)):

        $cat_name=category($res['vCategory']);
        array_push($arrname,$cat_name);
        $cat_cnt=$res['catgeory_cnt'];
        array_push($arrcount,$cat_cnt);

        $arr = array('name' => $cat_name , 'y' => $cat_cnt);
        array_push($ct,$arr);
        $data['category_name']    = $arrname;
        $data['category_count']    = $arrcount;
        endwhile;
else: 
        $data['category_name']    = 'No Data';
        $data['category_count']    = '1';

endif;

//subcategory
$subcategory=mysqli_query($link,"SELECT vSubCategory, count(*) as subcatgeory_cnt from $db.web_problemdefination where vCustomerID !='' $today group by vSubCategory ");
$num_complaint2=mysqli_num_rows($subcategory);

if($num_complaint2 > 0 ):
        $arrname2=[];
        $arrcount2=[];
        $sct=[];
        while($res = mysqli_fetch_array($subcategory)):

        $subcat_name=subcategory($res['vSubCategory']);
        array_push($arrname2,$subcat_name);
        $subcat_cnt=$res['subcatgeory_cnt'];
        array_push($arrcount2,$subcat_cnt);

        $arr = array('name' => $subcat_name , 'y' => $subcat_cnt);
        array_push($sct,$arr);
        $data['subcategory_name']    = $arrname2;
        $data['subcategory_count']    = $arrcount2;
        endwhile;
else: 
        $data['subcategory_name']    = 'No Data';
        $data['subcategory_count']    = '1';
endif;

//priority

$priority=mysqli_query($link,"SELECT priority, count(*) as priority_cnt from $db.web_problemdefination where priority !='' $today group by priority ");
$num_priority=mysqli_num_rows($priority);

if($num_priority > 0 ):
        $pp=[];
        $arrname3=[];
        $arrcount3=[];
        while($res = mysqli_fetch_array($priority)):
        $arr = array('name' => $res['priority'] , 'y' => $res['priority_cnt']);
        array_push($pp,$arr);

        $priority_name=$res['priority'];
        array_push($arrname3,$priority_name);
        $priority_cnt=$res['priority_cnt'];
        array_push($arrcount3,$priority_cnt);
        $data['priority_name']    = $arrname3;
        $data['priority_count']    = $arrcount3;
        endwhile;
else: 
        $data['name']    = 'No Data';
        $data['y']    = '1';
endif;



$data['total_case_today']=    $cases_cnt['total'];
$data['total_compalint'] =    $comp_cnt['total'];
$data['total_inquiries'] =    $inq_cnt['total'];
$data['total_others']    =    $other_cnt['total'];
$data['pending']    =         $pending_cnt['total'];
$data['closed']    =          $closed_cnt['total'];
$data['resolved']    =          $resolved_cnt['total'];
$data['reopened']    =          $reopened_cnt['total'];
$data['assigned']    =        $assigned_cnt['total'];
$data['escalated']    =       $escalated_cnt['total'];

////////////////////////////////////TELEPHONY////////////////////////////////
//Inbound
$date_cond="   AND  call_date>= '$Startdate' and call_date<= '$Enddate' ";
$inbound=mysqli_query($link,"select count(closecallid) as total from $db_asterisk.autodial_closer_log where call_date>= '$Startdate' and call_date<= '$Enddate'");
$inbound_cnt=mysqli_fetch_assoc($inbound);

/* Total no of inbound connected  */
   // ivr abandon
$begin_date1= $Startdate;
$begin_date2= $Enddate;
$rowt=mysqli_fetch_row(mysqli_query($link,"select count(*) as ivrs_abandonCalls  from $db_asterisk.tbl_cdr cd join $db_asterisk.tbl_cdrlog cdl on cdl.v_SessionId = cd.uniqueid  where d_StartTime>= '$begin_date1' and d_StartTime<= '$begin_date2' and (cdl.status='NO ANSWER' OR cdl.status='MISSED CALL') and cdl.missedcallcause = 'CALL DISCONNECTED' "));
$ivrs_abandonCalls_total=$rowt[0];
$stmt="select DATE(call_date),count(*) as Total_INCOMING_Calls,
sum(case when status in ('DONE','INCALL') then 1 else 0 end) as Converts,
sum(case when status in ('AGENT DROP') then 1 else 0 end) as AGENT_DROP,
sum(case when status in ('QUEUE') then 1 else 0 end) as DROPS,
sum(case when status in ('DROP') then 1 else 0 end) as QueueMaxAchieve,campaign_id
from $db_asterisk.autodial_closer_log
where call_date >= '$begin_date1' and call_date <= '$begin_date2' order by campaign_id asc";
$rslt=mysqli_query($link,$stmt);
$row=mysqli_fetch_row($rslt);
$connected_total=$row[2];//connected
$agentDrops_total=$row[3];//agentdrop
$systenQueueDrops_total=$row[5];//drop
$customerQueueDrops_total=$row[4];//queee
$service ="select count(*) as service  from $db_asterisk.tbl_cdr   where start_time>= '$begin_date1' and end_time<= '$begin_date2' and status IN ('SERVICING') ";
$service=mysqli_query($link,$service);
$service_result  = mysqli_fetch_assoc($service);
$total_service =   $service_result['service'] ;
$Forwarding ="select count(*) as forwarded  from $db_asterisk.tbl_cdr   where start_time>= '$begin_date1' and end_time<= '$begin_date2' and status IN ('FORWARDING') ";
$Forwarding = mysqli_query($link,$Forwarding);
$forwarding_result  = mysqli_fetch_assoc($Forwarding);
$total_forwarding =   $forwarding_result['forwarded'] ;
/*voicemail*/
$stmv = "select count(*) as non_officehour_voicmail from $db_asterisk.tbl_cdrlog where  
d_EndTime>= '$begin_date1' and d_EndTime<= '$begin_date2' AND missedcallcause='VOICEMAIL_nonofficehours' ";
$rowVQuery = mysqli_query($link, $stmv) or die('Err while excecuting non office hour voicemail' . mysqli_error($link));
$rowv = mysqli_fetch_row($rowVQuery);
$non_officehour_total_voicemail = $rowv[0];
$total_abandoned=( ($connected_total+$total_service + $total_forwarding) +$agentDrops_total+$systenQueueDrops_total+$customerQueueDrops_total+$ivrs_abandonCalls_total+$non_officehour_total_voicemail);
/*End */
//outbound
$date_cond_voicelogger="   AND  start_time>= '$Startdate' and start_time<= '$Enddate' ";
// $outbound=mysqli_fetch_row(mysqli_query($link,"select count(sno) from asterisk.voiceloger where typeofcall='OUT' and local<>'1' and status<>'ANSWERED' $date_cond_voicelogger"));
$outbound=mysqli_fetch_row(mysqli_query($link,"select count(recording_id) from $db_asterisk.recording_log where location NOT IN ('Connected','Transfer') and call_type IN('dialpad','c2c') $date_cond_voicelogger"));
$outbound_cnt=$outbound[0];

//Missedcall
$date_cond_misscall="   AND  d_StartTime>= '$Startdate' and d_StartTime<= '$Enddate' ";
$missedcall=mysqli_query($link,"SELECT count(v_SessionId) as total from $db_asterisk.tbl_cdrlog where status in ('MISSED CALL','NO ANSWER') and  missedcallcause = 'CALLER DISCONNECTED IN QUEUE' $date_cond_misscall");
$missedcall_cnt=mysqli_fetch_assoc($missedcall);

//voicemail
$date_cond_voicemail="   AND  voicemailtime>= '$Startdate' and voicemailtime<= '$Enddate' ";
$voicemail=mysqli_query($link,"SELECT count(id) as total from $db_asterisk.tbl_cc_voicemails where  flag1='0'  $date_cond_voicemail ");
$voicemail_cnt=mysqli_fetch_assoc($voicemail);

//abandon
$abandon=mysqli_query($link,"SELECT count(closecallid) as total from $db_asterisk.autodial_closer_log where status in ('QUEUE','DROP') $date_cond");
$abandon_cnt=mysqli_fetch_assoc($abandon);

//////////////////////////////////////////////////////////////////////////////////////////////////////
$dt=date("Y-m-d");
$q=mysqli_fetch_row(mysqli_query($link,"select SUM(UNIX_TIMESTAMP(`end_time`)-UNIX_TIMESTAMP(`answered_time`)) , AVG(UNIX_TIMESTAMP(`end_time`)-UNIX_TIMESTAMP(`answered_time`)) FROM $db_asterisk.voiceloger where  local<>'1' $date_cond_voicelogger and status='ANSWERED' and length_in_sec>0 ;"));
$vars=$q[0]; $varss=$q[1];

$q=mysqli_fetch_row(mysqli_query($link,"select SUM(length_in_sec) , AVG(length_in_sec) FROM $db_asterisk.recording_log where length_in_sec>0 $date_cond_voicelogger ;"));
$vars1=$q[0]; 
$varss1=$q[1];
//inbound answered
$qqqq=mysqli_fetch_row(mysqli_query($link,"select count(closecallid) from $db_asterisk.autodial_closer_log where status in ('DONE','INCALL') $date_cond;"));
$var4_4=$qqqq[0];

//total talktime
$var9=gmdate("H:i:s",($vars+$vars1));


//average talktime
$var10=gmdate("H:i:s",($varss+$varss1));


//Average call duration in past hour

$query_avg_duration = "SELECT AVG(ans_duration) FROM $db_asterisk.recording_log WHERE ans_duration > 0 AND answered_time >= NOW() - INTERVAL 1 HOUR";
$bind_avg_duration = $link->query($query_avg_duration);
$fetch_avg_duration = $bind_avg_duration->fetch_row();
$var_avg_dur = gmdate("H:i:s",($fetch_avg_duration[0]));

//Average call wrapup in past hour

$query_avg_wrapup = "SELECT AVG(dispo_sec) FROM $db_asterisk.autodial_agent_log WHERE dispo_sec > 0 AND event_time >= NOW() - INTERVAL 1 HOUR";
$bind_avg_wrapup = $link->query($query_avg_wrapup);
$fetch_avg_wrapup = $bind_avg_wrapup->fetch_row();
$var_avg_wrapup = gmdate("H:i:s",($fetch_avg_wrapup[0]));

//Longest call duration in past hour

$query_lng_duration = "SELECT MAX(ans_duration) FROM $db_asterisk.recording_log WHERE ans_duration > 0 AND answered_time >= NOW() - INTERVAL 1 HOUR";
$bind_lng_duration = $link->query($query_lng_duration);
$fetch_lng_duration = $bind_lng_duration->fetch_row();
$var_lng_dur = gmdate("H:i:s",($fetch_lng_duration[0]));



//wrapup
$wrapup=mysqli_query($link,"select count(*) as total from $db_asterisk.autodial_live_agents where status='WRAPUP';");
$wrapup_cnt=mysqli_fetch_assoc($wrapup);

//productive vs total case
//0=> Not meaningful , 1=> meaningful

$q_p="SELECT ticketid,d_createDate,d_updateTime ,UNIX_TIMESTAMP(`d_updateTime`) - UNIX_TIMESTAMP(`d_createDate`) as DifferenceInSeconds,CASE WHEN UNIX_TIMESTAMP(`d_updateTime`) - UNIX_TIMESTAMP(`d_createDate`) <= 180 THEN '1' ELSE '0' END AS Timediff from $db.web_problemdefination where iCaseStatus=8 $today";

$productive=mysqli_query($link,$q_p);

$meaning_full = 0 ;

while($res = mysqli_fetch_array($productive)){
      
        if($res['Timediff']==1){
              $meaning_full++;
        }else
        {
                $meaning_full=0;       
        }
        
}


$productive_cnt = ($cases_cnt['total'] == 0 ) ? 0 : $meaning_full/$cases_cnt['total']*100;


$data['inbound']    =      $total_abandoned;
$data['outbound']    =       $outbound_cnt;
$data['missedcall']    =       $agentDrops_total+$systenQueueDrops_total;
$data['voicemail']    =       $non_officehour_total_voicemail;
$data['abandon']    =       $customerQueueDrops_total+$ivrs_abandonCalls_total;
$data['total_talktime']    =       $var9;
$data['average_talktime']    =       $var10;
$data['average_duration']    =       $var_avg_dur;
$data['longest_duration']    =       $var_lng_dur;
$data['average_wrapup']    =       $var_avg_wrapup;
$data['wrapup']    =       $wrapup_cnt['total'];
$data['productive'] = $productive_cnt;



////////////////////////Social Media and Channels///////////////////////////////

//case created by twitter
$twitter_created=mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='3' GROUP BY p.ticketid");
$twitter_created_cnt=mysqli_num_rows($twitter_created);

//twitter recieved
$twitter_recieved=mysqli_query($link,"SELECT count(*) as total from $db.tbl_tweet where i_Status=1 and d_TweetDateTime>='$Startdate' and d_TweetDateTime<='$Enddate' AND irrelevant_status='0' order by i_ID");
$twitter_recieved_cnt=mysqli_fetch_assoc($twitter_recieved);

//case created by facebook
$facebook_created=mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='4' GROUP BY p.ticketid");
$facebook_created_cnt=mysqli_num_rows($facebook_created);

//facebook recieved
$facebook_recieved=mysqli_query($link,"SELECT count(*) as total from $db.tbl_facebook where i_deletestatus!='0' and createddate>='$Startdate' and createddate<='$Enddate' order by createddate");
$facebook_recieved_cnt=mysqli_fetch_assoc($facebook_recieved);

//instagram recieved
$instagram_recieved=mysqli_query($link,"SELECT count(*) as total FROM $db.instagram_in_queue WHERE create_date BETWEEN '$Startdate' AND '$Enddate' ");
$instagram_recieved_cnt=mysqli_fetch_assoc($instagram_recieved);


//instagram post  recieved[vastvikta][17-12-2024]
$instagram_posts_recieved=mysqli_query($link,"SELECT count(*) as total FROM $db.instagram_posts WHERE `timestamp` BETWEEN '$Startdate' AND '$Enddate' ");
$instagram_posts_recieved_cnt=mysqli_fetch_assoc($instagram_posts_recieved);

// whatsapp bot sessions
// $mysqli = new mysqli('167.71.232.201:3306', 'jipes', '1234', 'bipa_dev');
// $whatsapp_bot = mysqli_query($mysqli,"SELECT count(*) as total from bipa_dev.overall_bot_chat_session where session_start_time>='$Startdate' and session_start_time<='$Enddate'");
// $whatsapp_bot_cnt = mysqli_fetch_assoc($whatsapp_bot);


 //whatsapp count code chnages [Aarti][20-03-2024]
$whatsapp_bot=mysqli_query($link,"SELECT count(*) as total FROM $db.whatsapp_in_queue WHERE status='0' AND create_date BETWEEN '$Startdate' AND '$Enddate' AND flag='0'");
$whatsapp_bot_cnt = mysqli_fetch_assoc($whatsapp_bot);


//messenger count code chnages [Aarti][20-03-2024]
$messenger_botcmd = mysqli_query($link,"SELECT count(*) as total from $db.messenger_in_queue where create_date>='$Startdate' and create_date<='$Enddate' ");
$messenger_bot = mysqli_fetch_assoc($messenger_botcmd);



//webchat 
$chat_bot = mysqli_query($link,"SELECT count(*) as total from $db.overall_bot_chat_session where session_start_time>='$Startdate' and session_start_time<='$Enddate'");
$chat_bot_cnt = mysqli_fetch_assoc($chat_bot);

//case created by chat
$chat_created=mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='5' GROUP BY p.ticketid");
$chat_created_cnt=mysqli_num_rows($chat_created);

//sms recieved
$sms_query = mysqli_query($link,"SELECT count(*) as total FROM $db.`tbl_smsmessagesin` WHERE d_timeStamp>='$Startdate' and d_timeStamp<='$Enddate' ORDER BY `d_timeStamp` DESC");
$sms_cnt = mysqli_fetch_assoc($sms_query);
// case created through sms
$sms_created = mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='13' GROUP BY p.ticketid");
$sms_created_cnt = mysqli_num_rows($sms_created);

// case created through instagram
$instagram_created = mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='15' GROUP BY p.ticketid");
$instagram_created_cnt = mysqli_num_rows($instagram_created);

//email recieved
$email = mysqli_query($link,"SELECT count(*) as total from $db.web_email_information where d_email_date >='$Startdate' and d_email_date <='$Enddate' and email_type='IN' and i_DeletedStatus!=2 and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com') order by d_email_date");
$email_cnt = mysqli_fetch_assoc($email);

//case created by email
$email_created=mysqli_query($link,"SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='6' GROUP BY p.ticketid");
$email_created_cnt=mysqli_num_rows($email_created);

//live agents
//        $live_agents=mysqli_query($link,"SELECT * from $db_asterisk.autodial_live_agents a , $db.uniuserprofile u where a.user =u.AtxUserName and u.AtxDesignation='Agent'");
$live_agents=mysqli_query($link,"SELECT user,status,campaign_id from $db_asterisk.autodial_live_agents where DATE(last_update_time)='$Startdate'");
$live_agents_cnt=mysqli_num_rows($live_agents);

//live Backofficer
//        $live_backofficers=mysqli_query($link,"SELECT * from asterisk.autodial_live_agents a , poc.uniuserprofile u where a.user =u.AtxUserName and u.AtxDesignation='Backoffice Officer'");
$live_backofficers=mysqli_query($link,"SELECT * from $db_asterisk.autodial_live_agents a , $db.uniuserprofile u where a.user =u.AtxUserName and u.AtxDesignation='Backoffice Officer' and DATE(a.last_update_time)='$Startdate'");
$live_backofficers_cnt=mysqli_num_rows($live_backofficers);

// to calculate the  twitter unread message
$twitter_unread=mysqli_query($link,"SELECT count(*) as total from $db.tbl_tweet where flag=0 and d_TweetDateTime>='$Startdate' and d_TweetDateTime<='$Enddate' AND irrelevant_status='0' order by i_ID");
$twitter_unread_cnt=mysqli_fetch_assoc($twitter_unread);

// to calculate the twitter read message 
$twitter_read=mysqli_query($link,"SELECT count(*) as total from $db.tbl_tweet where flag=1 and d_TweetDateTime>='$Startdate' and d_TweetDateTime<='$Enddate' AND irrelevant_status='0' order by i_ID");
$twitter_read_cnt=mysqli_fetch_assoc($twitter_read);

// to calculate the  instagram unread message
$instagram_unread=mysqli_query($link,"SELECT count(*) as total from $db.instagram_in_queue where flag='0' and create_date>='$Startdate' and create_date<='$Enddate'  order by id");
$instagram_bot_unread=mysqli_fetch_assoc($instagram_unread);

// to calculate the  instagram read message
$instagram_read=mysqli_query($link,"SELECT count(*) as total from $db.instagram_in_queue where flag=1 and create_date>='$Startdate' and create_date<='$Enddate'  order by id");
$instagram_bot_read=mysqli_fetch_assoc($instagram_read);

// to calculate the  instagram posts read message
$instagram_posts_read=mysqli_query($link,"SELECT count(*) as total from $db.instagram_posts where flag='1' and timestamp>='$Startdate' and timestamp<='$Enddate'  order by id");
$instagram_posts_read=mysqli_fetch_assoc($instagram_posts_read);

// to calculate the  instagram unread message
$instagram_post_unread=mysqli_query($link,"SELECT count(*) as total from $db.instagram_posts where flag='0' and `timestamp`>='$Startdate' and `timestamp`<='$Enddate'  order by id");
$instagram_posts_unread=mysqli_fetch_assoc($instagram_post_unread);

// to calculate whatsapp read message 
$whatsapp_read=mysqli_query($link,"SELECT count(*) as total FROM $db.whatsapp_in_queue WHERE status='0' AND create_date BETWEEN '$Startdate' AND '$Enddate' AND flag='0'");
$whatsapp_read_cnt = mysqli_fetch_assoc($whatsapp_read);

//to calulate whatsapp unread message 
$whatsapp_unread=mysqli_query($link,"SELECT count(*) as total FROM $db.whatsapp_in_queue WHERE status='0' AND create_date BETWEEN '$Startdate' AND '$Enddate' AND flag='1'");
$whatsapp_unreas_cnt = mysqli_fetch_assoc($whatsapp_unread);

//facebook unread message
$facebook_read=mysqli_query($link,"SELECT count(*) as total from $db.tbl_facebook where i_deletestatus!='0' and createddate>='$Startdate' and createddate<='$Enddate' AND flag_read_unread='3'");
$facebook_read_cnt=mysqli_fetch_assoc($facebook_read);

//facebook read message
$facebook_unread=mysqli_query($link,"SELECT count(*) as total from $db.tbl_facebook where i_deletestatus!='0' and createddate>='$Startdate' and createddate<='$Enddate' AND flag_read_unread='1'");
$facebook_unread_cnt=mysqli_fetch_assoc($facebook_unread);

//email unread message
$email_unread = mysqli_query($link,"SELECT count(*) as total from $db.web_email_information where d_email_date >='$Startdate' and d_email_date <='$Enddate' and email_type='IN' and i_DeletedStatus!=2 and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com') AND flag='0'");
$email_unread_cnt = mysqli_fetch_assoc($email_unread);
//email read message 
$email_read = mysqli_query($link,"SELECT count(*) as total from $db.web_email_information where d_email_date >='$Startdate' and d_email_date <='$Enddate' and email_type='IN' and i_DeletedStatus!=2 and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com') AND flag='1'");
$email_read_cnt = mysqli_fetch_assoc($email_read);

//sms read 
$sms_read = mysqli_query($link,"SELECT count(*) as total FROM $db.`tbl_smsmessagesin` WHERE d_timeStamp>='$Startdate' and d_timeStamp<='$Enddate'  AND Flag = '1'");
$sms_read_cnt = mysqli_fetch_assoc($sms_read);
//sms unread 
$sms_unread = mysqli_query($link,"SELECT count(*) as total FROM $db.`tbl_smsmessagesin` WHERE d_timeStamp>='$Startdate' and d_timeStamp<='$Enddate' AND Flag = '0' ");
$sms_unread_cnt = mysqli_fetch_assoc($sms_unread);

//webchat read
$chat_bot_read = mysqli_query($link,"SELECT count(*) as total from $db.overall_bot_chat_session where session_start_time>='$Startdate' and session_start_time<='$Enddate' and flag = '0'");
$chat_bot_read_cnt = mysqli_fetch_assoc($chat_bot_read);
//webchat read
$chat_bot_unread = mysqli_query($link,"SELECT count(*) as total from $db.overall_bot_chat_session where session_start_time>='$Startdate' and session_start_time<='$Enddate' and flag = '1'");
$chat_bot_unread_cnt = mysqli_fetch_assoc($chat_bot_unread);

//messenger read
$messenger_botcmd_read = mysqli_query($link,"SELECT count(*) as total from $db.messenger_in_queue where create_date>='$Startdate' and create_date<='$Enddate' AND flag='1'");
$messenger_bot_read = mysqli_fetch_assoc($messenger_botcmd_read);
//messenger unread
$messenger_botcmd_unread = mysqli_query($link,"SELECT count(*) as total from $db.messenger_in_queue where create_date>='$Startdate' and create_date<='$Enddate' AND flag='0'");
$messenger_bot_unread = mysqli_fetch_assoc($messenger_botcmd_unread);

// $data['total_calls']    =       $inbound_cnt['total']+$outbound_cnt;
$data['total_calls']    =       $total_abandoned+$outbound_cnt;
$data['total_outbound']    =       $outbound_cnt;
$data['out_answer']    =       $outbound_cnt;
$data['out_noanswer']    =       $outbound_cnt;
$data['inbound_answered']    =       $var4_4;
$data['facebook_created']    =       $facebook_created_cnt;
$data['facebook_recieved']    =       $facebook_recieved_cnt['total'];
$data['instagram_recieved']    =       $instagram_recieved_cnt['total'];
$data['instagram_posts_recieved']    =       $instagram_posts_recieved_cnt['total'];//instagram posts [vastvikta][17-12-2024]
$data['twitter_created']    =       $twitter_created_cnt;
$data['twitter_recieved']    =       $twitter_recieved_cnt['total'];
$data['whatsapp_bot']    =       $whatsapp_bot_cnt['total']; 
$data['messenger_bot']    =       $messenger_bot['total']; //messenger count dislay[Aarti][20-08-2024]
$data['chat_bot']    =       $chat_bot_cnt['total'];
$data['chat_created']    =       $chat_created_cnt;
$data['sms_cnt']    =       $sms_cnt['total'];
$data['sms_created_cnt']    =       $sms_created_cnt;
$data['instagram_created_cnt']    =     $instagram_created_cnt;
$data['email']    =       $email_cnt['total'];
$data['email_created']    =       $email_created_cnt;
$data['live_agents']    =       $live_agents_cnt;
$data['live_backofficers']    =       $live_backofficers_cnt;
$data['twitter_read_cnt']    =       $twitter_read_cnt['total'];
$data['twitter_unread_cnt']    =       $twitter_unread_cnt['total'];
$data['instagram_bot_read']    =       $instagram_bot_read['total'];
$data['instagram_posts_read']    =       $instagram_posts_read['total'];//[vastvikta][17-12-2024]
$data['instagram_bot_unread']    =       $instagram_bot_unread['total'];
$data['instagram_posts_unread']    =       $instagram_posts_unread['total'];
$data['whatsapp_read_cnt']    =       $whatsapp_read_cnt['total'];
$data['whatsapp_unread_cnt']    =       $whatsapp_unread_cnt['total'];
$data['facebook_read_cnt']    =       $facebook_read_cnt['total'];
$data['facebook_unread_cnt']    =       $facebook_unread_cnt['total'];
$data['email_read_cnt']    =       $email_read_cnt['total'];
$data['email_unread_cnt']    =       $email_unread_cnt['total'];
$data['sms_read_cnt']    =       $sms_read_cnt['total'];
$data['sms_unread_cnt']    =       $sms_unread_cnt['total'];
$data['chat_bot_read_cnt']    =       $chat_bot_read_cnt['total'];
$data['chat_bot_unread_cnt']    =       $chat_bot_unread_cnt['total'];
$data['messenger_bot_read']   =     $messenger_bot_read['total'];       
$data['messenger_bot_unread']   =     $messenger_bot_unread['total'];       



////////////////////////Ageing(7days ,15days ,20days ,30days)///////////////////////////////
// orignal query "Select count(*) as total from $db.web_problemdefination where iCaseStatus='1' and d_createDate >= '$Startdate' - INTERVAL 7 DAY" 
// chnaged to below one to match the records number from the crm reports cases [vastvikta][19-03-2025]
$Todaydate = date('Y-m-d'); // Get today's date

$ageing_7 = mysqli_query($link,"Select count(*) as total from $db.web_problemdefination where iCaseStatus='1' and d_createDate >= '$Todaydate' - INTERVAL 7 DAY");

$ageing_7_cnt=mysqli_fetch_assoc($ageing_7);

$ageing_15 = mysqli_query($link,"Select count(*) as total from $db.web_problemdefination where iCaseStatus='1' and d_createDate >= '$Todaydate' - INTERVAL 15 DAY");
$ageing_15_cnt=mysqli_fetch_assoc($ageing_15);

$ageing_20 = mysqli_query($link,"Select count(*) as total from $db.web_problemdefination where iCaseStatus='1' and d_createDate >= '$Todaydate' - INTERVAL 20 DAY");
$ageing_20_cnt=mysqli_fetch_assoc($ageing_20);

$ageing_30 = mysqli_query($link,"Select count(*) as total from $db.web_problemdefination where iCaseStatus='1' and d_createDate >= '$Todaydate' - INTERVAL 30 DAY");
$ageing_30_cnt=mysqli_fetch_assoc($ageing_30);


$data['ageing_7']=$ageing_7_cnt['total'];
$data['ageing_15']=$ageing_15_cnt['total'];
$data['ageing_20']=$ageing_20_cnt['total'];
$data['ageing_30']=$ageing_30_cnt['total'];

// overall disposition List
$table = "<table class='table table-bordered table-striped' >
                <thead>
                    <tr>
                            <th>Agent Name</th>
                            <th>Offered Calls</th>
                            <th>Answered Calls</th>
                            <th>Total Talktime</th>
                            <th>Average Talktime</th>
                            <th>Total Wrapuptime</th>
                            <th>Average Wrapuptime</th>
                            <th>Total Case Created</th>
                            <th>Others</th>
                            <th>Complaints</th>
                            <th>Enquires</th>
                            <th>Transfer</th>
                            <th>Total Break</th>
                        </tr>
                </thead>
                <tbody>";
            $query = $link->query("SELECT users.v_department,log.campaign_id,users.full_name,users.transfer,users.user,TIMESTAMPDIFF(SECOND,d_createdOn,NOW()) AS TermCheck FROM $db_asterisk.autodial_users AS users INNER JOIN $db_asterisk.recording_log AS `log` ON users.user=log.extension WHERE log.start_time BETWEEN '$Startdate' AND ' $Enddate' GROUP BY user");
            $dispo_count = $query->num_rows;

            if(empty($dispo_count) || $dispo_count==0)
            {
              $table .= '<tr><td colspan="12" class="text-center">No Data Found<td></tr>';
            }else
            {
            while ($row = $query->fetch_assoc()) 
            {
                    $query021 = "SELECT count(*) as totalADRAttempted from $db_asterisk.autodial_closer_log where call_date between '" . $Startdate . "' and '" . $Enddate . "' and user='" . $row['user'] . "' and status in ('DONE','INCALL')";
                    $query02Data1 = mysqli_query($link, $query021) or die(mysqli_error($link) . "<11");
                    $query02Row1 = mysqli_fetch_assoc($query02Data1);
                    //modified on 03-06-2023 for talktime matching with talktime 
                    $query03 = "SELECT SUM(length_in_sec) AS totalTalkTime FROM $db_asterisk.recording_log WHERE start_time between '$Startdate' AND ' $Enddate'   AND extension='" . $row['user'] . "'";
                    $query03Data = mysqli_query($link, $query03) or die(mysqli_error($link) . "<21111111111");
                    $query03Row = mysqli_fetch_assoc($query03Data);
                    $query06 = "select sum(dispo_sec) as totalWrapUpTime from $db_asterisk.autodial_agent_log where event_time between '" . $Startdate . "' and '" . $Enddate . "'  and user='" . $row['user'] . "'";
                    $query06Data = mysqli_query($link, $query06) or die(mysqli_error($link) . "<5");
                    $query06Row = mysqli_fetch_assoc($query06Data);
                    // get wrap time end
                    

                    //offered calls
                    $cond_usr = "  and user='" . $row['user'] . "'";
                    $stmt = "SELECT * FROM ( select * from $db_asterisk.autodial_closer_log where user!='' and (call_date >= '$Startdate' and call_date <= '$Enddate' $cond_usr) order by call_date desc ) AS sub group by v_SessionId";
                    $q1 = mysqli_query($link, $stmt);
                    $stmtqr = mysqli_fetch_assoc($q1);
                    $num_userOffered = mysqli_num_rows($q1);

                    //answered calls
                    $highestCallAnswered = $query02Row1['totalADRAttempted'];
                    //Average call duration 
                    $agent = $row['full_name'];
                    $transfer = $row['transfer'];
                    $query_avg_duration = "SELECT AVG(ans_duration) FROM $db_asterisk.recording_log WHERE ans_duration > 0 AND extension='$agent' AND start_time BETWEEN '$Startdate' AND '$Enddate' ";
                    $bind_avg_duration = $link->query($query_avg_duration);
                    $fetch_avg_duration = $bind_avg_duration->fetch_row();
                    $var_avg_duration = gmdate("H:i:s",($fetch_avg_duration[0]));

                    $var_avg_wrapup = $query06Row['totalWrapUpTime']/$highestCallAnswered;

                    $mobile = $stmtqr['phone_number'];
                    $webaccountsql = "SELECT AccountNumber from $db.web_accounts where (phone = '$mobile' || mobile = '$mobile' )";
                    // echo $webaccountsql;
                    $webaccount_q1 = mysqli_query($link, $webaccountsql);
                    $webaccount_row = mysqli_num_rows($webaccount_q1);

                    if($webaccount_row>0){
                        $webaccount=mysqli_fetch_assoc($webaccount_q1);
                        $vCustomerID = $webaccount['AccountNumber'];
                        // this code for fetching total case details
                        $total_ticketid=mysqli_query($link,"SELECT count(*) as ticketid from $db.web_problemdefination where vCustomerID = '$vCustomerID' AND d_createDate BETWEEN '$Startdate' AND '$Enddate'");
                        $ticketids=mysqli_fetch_assoc($total_ticketid);

                        // this code for fetching total other cases
                        $total_other=mysqli_query($link,"SELECT count(vCaseType) as others from $db.web_problemdefination where vCustomerID = '$vCustomerID' AND d_createDate BETWEEN '$Startdate' AND '$Enddate' and vCaseType = 'others'");
                        $total_otherid=mysqli_fetch_assoc($total_other);

                        // this code for fetching total complaint cases
                        $total_complaints=mysqli_query($link,"SELECT count(vCaseType) as Complaints from $db.web_problemdefination where vCustomerID = '$vCustomerID' AND d_createDate BETWEEN '$Startdate' AND '$Enddate' and vCaseType = 'complaint'");
                        $total_complaintsid=mysqli_fetch_assoc($total_complaints);

                        // this code for fetching total Inquiry cases
                        $total_Inquiry=mysqli_query($link,"SELECT count(vCaseType) as Inquiry from $db.web_problemdefination where vCustomerID = '$vCustomerID' AND d_createDate BETWEEN '$Startdate' AND '$Enddate' and vCaseType = 'Inquiry'");
                        $total_Inquiryid=mysqli_fetch_assoc($total_Inquiry);
                        
                    }
                    $table .='<tr>
                              <td>'.$agent.'</td>
                              <td>'.$num_userOffered.'</td>
                              <td>'.$highestCallAnswered.'</td>
                              <td>'.getTimeInFormated($query03Row['totalTalkTime']).'</td>
                              <td>'.$var_avg_duration.'</td>
                              <td>'.getTimeInFormated($query06Row['totalWrapUpTime']).'</td>
                              <td>'.getTimeInFormated($var_avg_wrapup).'</td>
                              <td>'.$ticketids['ticketid'].'</td>
                              <td>'.$total_otherid['others'].'</td>
                              <td>'.$total_complaintsid['Complaints'].'</td>
                              <td>'.$total_Inquiryid['Inquiry'].'</td>
                               <td>'.$transfer.'</td>
                              <td>'.getTimeInFormated($fetch1['totalWorkBreak']).'</td>
                          </tr>';    
            }
}
$table .='</tbody><table>';
echo json_encode(array('status' => 'success', 'info' => $data,'category'=>$ct,'subcategory'=>$sct, 'priority'=>$pp ,'dispostion'=>$table ));die();

?>