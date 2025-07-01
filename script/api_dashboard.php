<?php
require_once "../config/web_mysqlconnect.php";
require_once "../CRM/web_function.php";

function getTimeInFormated($seconds){
    $hours = floor($seconds / 3600);
    $mins = floor($seconds / 60 % 60);
    $secs = floor($seconds % 60);
    return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
}

/*
$fdate should be less than $tdate
 if $fdate < current date - 21 days then return -1; 
minimum 21 days required
 if ( $tdate - today's date < 21) then find the date $todateRange by deducting 21 days from $tdate else $todateRange = $tdate;
 Let $N = $todate Cases 
Calculate number of resolved cases between $fdate and $todateRange say $resolvedCases
 return (resovedCaes/N) * 100;

*/

function getRateOfResolution($fdate, $tdate){

    global $link, $db;
    $msg = '';
    $cdate = date('Y-m-d 00:00:00');
    $dateLess21 = date('Y-m-d 00:00:00', strtotime("-21 days"));

    if ($fdate >= $dateLess21) {
        $msg = "Minimum 21 Days Required";
        return $msg;
    }

    $toDateRange = ($tdate > $dateLess21) ? $dateLess21 : $tdate;
    $N = dateDiffInDays($toDateRange, $fdate) + 1;
    $qry = "SELECT count(*) as total from $db.web_problemdefination where vCaseType = 'complaint' and   d_createDate BETWEEN '$fdate' AND '$toDateRange'";
    $totalRes =  $link->query( $qry);
    $totalRow = $totalRes->fetch_assoc();
    $TotalCases = $totalRow['total'];

    // resolved and closed cases
    $qry = "SELECT count(*) as total from $db.web_problemdefination where  vCaseType = 'complaint' and (iCaseStatus='3' OR iCaseStatus='8') AND d_createDate BETWEEN '$fdate' AND '$toDateRange'";

    $resolvedRes =  $link->query( $qry);

    $resolvedRow = $resolvedRes->fetch_assoc();
    $resolvedCases = $resolvedRow['total'];

    if ($TotalCases > 0) {
        $res = ($resolvedCases / $TotalCases) * 100;
        return (round($res, 2)) . "%";
    } else
        return "No Case";

}

function get_dashboard_records($Startdate,$Enddate){

    global $link,$db,$db_asterisk;
    
    $query_str = " AND d_createDate BETWEEN '$Startdate' AND '$Enddate'";
    $data = [];

    /* CRM Starts */

        $total_cases = $link->query("SELECT count(*) AS total FROM $db.web_problemdefination WHERE d_createDate >= '$Startdate' AND d_createDate <= '$Enddate'");
        $row_total = $total_cases->fetch_row();
        $data['total_cases'] = $row_total[0];

        $complaint_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE vCaseType='complaint' $query_str ");
        $row_complaint = $complaint_cases->fetch_row();
        $complaints = $row_complaint[0];
        $data['complaints'] = $complaints;

        $inquiry_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination p WHERE vCaseType='Inquiry' $query_str ");
        $row_inquiry = $inquiry_cases->fetch_row();
        $inquiries = $row_inquiry[0];
        $data['inquiries'] = $inquiries;


        $other_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination p WHERE vCaseType='others' $query_str ");
        $row_other = $other_cases->fetch_row();
        $others = $row_other[0];
        $data['others'] = $others;

        $pending_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE iCaseStatus='1' $query_str ");
        $row_pending = $pending_cases->fetch_row();
        $pending = $row_pending[0];
        $data['pending'] = $pending;

        $inprogress_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE iCaseStatus='8' $query_str ");
        $row_inprogress = $inprogress_cases->fetch_row();
        $inprogress = $row_inprogress[0];
        $data['inprogress'] = $inprogress;

        $closed_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE iCaseStatus='3' $query_str ");
        $row_closed = $closed_cases->fetch_row();
        $closed = $row_closed[0];
        $data['closed'] = $closed;

        $assigned_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE iCaseStatus='1'  AND vCaseType='complaint' AND vProjectID > '0' $query_str ");
        $row_assigned = $assigned_cases->fetch_row();
        $assigned = $row_assigned[0];
        $data['assigned'] = $assigned;

        // updated the query for the escalated cases [ vastvikta][13-05-2025]
        $escalated_cases = $link->query("SELECT count(iPID) AS cnt FROM $db.web_problemdefination  WHERE escalate_status > '0' $query_str ");
        $row_escalated = $escalated_cases->fetch_row();
        $escalated = $row_escalated[0];
        $data['escalated'] = $escalated;

        $resolution_rate= getRateOfResolution($Startdate,$Enddate);
        $data['resolution_rate'] = $resolution_rate;


        /* Category starts */

            // Initialize an empty array
            $str_category_array = [];

            // Fetch the category data
            $qq2 = $link->query("SELECT vCategory, COUNT(*) AS cnt FROM $db.web_problemdefination WHERE vCustomerID !='' $query_str GROUP BY vCategory");

            // Fetch the total count data
            $qq3 = $link->query("SELECT COUNT(*) AS cnt FROM $db.web_problemdefination WHERE vCustomerID !='' $query_str GROUP BY vCategory");

            $num_cat = $qq2->num_rows;

            if ($num_cat > 0) {
                $cnt2 = 0;

                // Calculate the total count
                while ($resss = $qq3->fetch_assoc()) {
                    $cnt2 += $resss['cnt'];
                }

                // Process each subcategory
                while ($res = $qq2->fetch_assoc()) {
                    $vCategory = $res['vCategory'];
                    $CatgeoryName = category($vCategory);
                    $cnt = $res['cnt'];
                    $category_cnt_per = $cnt / $cnt2 * 100;
                    
                    // Append the processed data to the array
                    $str_category_array[] = [
                        "y" => (int)$cnt,
                        "label" => $CatgeoryName,
                        "indexLabel" => $CatgeoryName . ' ' .$cnt . '(' . round($category_cnt_per, 2) . ')%',
                        "indexLabelFontSize" => 13,
                        "indexLabelFontWeight" => "bold"
                    ];
                }
            } else {
                // Handle the case when there are no subcategories
                $str_category_array[] = [
                    "y" => 1,
                    "label" => "Complaint category",
                    "indexLabel" => "No Data"
                ];
            }

            // Encode the array to JSON
            $str_category_json = json_encode($str_category_array);

        /* Category end */


        /* Subcategory starts */
        
            // Initialize an empty array
            $str_subcategory_array = [];

            // Fetch the subcategory data
            $qq = $link->query("SELECT vSubCategory, COUNT(iPID) AS cnt FROM $db.web_problemdefination WHERE vSubCategory != 0 $query_str GROUP BY vSubCategory");

            // Fetch the total count data
            $qq2 = $link->query("SELECT COUNT(iPID) AS cnt FROM $db.web_problemdefination WHERE vSubCategory != 0 $query_str GROUP BY iPID");

            $num_subcat = $qq->num_rows;

            if ($num_subcat > 0) {
                $cnt2 = 0;

                // Calculate the total count
                while ($resss = $qq2->fetch_assoc()) {
                    $cnt2 += $resss['cnt'];
                }

                // Process each subcategory
                while ($res = $qq->fetch_assoc()) {
                    $vSubCategory = $res['vSubCategory'];
                    $cat_from_subcat = cat_from_subcat($vSubCategory);
                    $color_subcat = color($cat_from_subcat);
                    $SubcatgeoryName = subcategory($vSubCategory);
                    $cnt = $res['cnt'];
                    $subcatgeory_cnt_per = $cnt / $cnt2 * 100;

                    // Append the processed data to the array
                    $str_subcategory_array[] = [
                        "y" => (int)$cnt,
                        "label" => $SubcatgeoryName,
                        "indexLabel" => $SubcatgeoryName . '-' . $cnt . '(' . round($subcatgeory_cnt_per, 2) . ')%',
                        // "color" => $color_subcat,
                        "indexLabelFontSize" => 13,
                        "indexLabelFontWeight" => "bold"
                    ];
                }
            } else {
                // Handle the case when there are no subcategories
                $str_subcategory_array[] = [
                    "y" => 1,
                    "label" => "Sub Category-wise",
                    "indexLabel" => "No Data(0)",
                    "indexLabelFontSize" => 13,
                    "indexLabelFontWeight" => "bold"
                ];
            }

            $str_subcategory_json = json_encode($str_subcategory_array);

        /* Subcategory end */

            /* Language starts */

            // Initialize an empty array
            $str_language_array = [];

            // Fetch the category data
            $qq4 = $link->query("SELECT count(*) AS cnt,language_id FROM $db.web_problemdefination WHERE language_id != 0  $query_str GROUP BY language_id");

            $num_lang = $qq4->num_rows;

            if ($num_lang > 0) {
            
                // Process each subcategory
                while ($res = $qq4->fetch_assoc()) {
                    $language_id = $res['language_id'];
                    $LanguageName = getlanguagename($language_id);
                    $cnt = $res['cnt'];
                    
                    // Append the processed data to the array
                    $str_language_array[] = [
                        "y" => (int)$cnt,
                        "label" => $LanguageName,
                        "indexLabel" => $cnt,
                        "indexLabelFontSize" => 13,
                        "indexLabelFontWeight" => "bold"
                    ];
                }
            } else {
                // Handle the case when there are no subcategories
                $str_language_array[] = [
                    "y" => 1,
                    "label" => "No Data",
                    "indexLabel" => "Language"
                ];
            }

            // Encode the array to JSON
            $str_language_json = json_encode($str_language_array);

        /* Language end */


        ////////////////////////Social Media and Channels///////////////////////////////
      
        //case created by twitter
        $twitter_created= $link->query("SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='3' GROUP BY p.ticketid");
        $twitter_created_cnt= $twitter_created->num_rows;

        // //twitter recieved
        $twitter_recieved= $link->query("SELECT * from $db.tbl_tweet where i_Status=1 and d_TweetDateTime>='$Startdate' and d_TweetDateTime<='$Enddate' AND irrelevant_status='0' order by i_ID");
        $twitter_recieved_cnt= $twitter_recieved->num_rows;

        // //case created by facebook
        $facebook_created= $link->query("SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='4' GROUP BY p.ticketid");
        $facebook_created_cnt= $facebook_created->num_rows;

        // //facebook recieved
        $facebook_recieved= $link->query("SELECT * from $db.tbl_facebook where i_deletestatus!='0' and createddate>='$Startdate' and createddate<='$Enddate' order by createddate");
        $facebook_recieved_cnt= $facebook_recieved->num_rows;

        // //whatsapp
        $whatsapp_bot =  $link->query("SELECT * from $db.whatsapp_in_queue where create_date>='$Startdate' and create_date<='$Enddate'");
        $whatsapp_bot_cnt = $whatsapp_bot->num_rows;

        // //webchat 
        $chat_bot =  $link->query("SELECT * from $db.overall_bot_chat_session where session_start_time>='$Startdate' and session_start_time<='$Enddate'");
        $chat_bot_cnt= $chat_bot->num_rows;

        // //case created by chat
        $chat_created= $link->query("SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='5' GROUP BY p.ticketid");
        $chat_created_cnt=$chat_created->num_rows;

        // //sms recieved
        $sms_query =  $link->query("SELECT * FROM $db.`tbl_smsmessagesin` WHERE d_timeStamp>='$Startdate' and d_timeStamp<='$Enddate' ORDER BY `d_timeStamp` DESC");
        $sms_cnt= $sms_query->num_rows;

        // // case created through sms
        $sms_created =  $link->query("SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='13' GROUP BY p.ticketid");
        $sms_created_cnt = $sms_created->num_rows;

        // //email recieved
        $email =  $link->query("SELECT * from $db.web_email_information where d_email_date >='$Startdate' and d_email_date <='$Enddate' and email_type='IN' and i_DeletedStatus!=2 and v_fromemail not in ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com') order by d_email_date");
        $email_cnt = $email->num_rows;

        // //case created by email
        $email_created= $link->query("SELECT p.* from $db.web_problemdefination as p left join $db.web_accounts a on a.AccountNumber = p.vCustomerID  where p.ticketid!='' and p.d_createDate>='$Startdate' and p.d_createDate<='$Enddate' AND i_source ='6' GROUP BY p.ticketid");
        $email_created_cnt= $email_created->num_rows;

        $data['facebook_c']    =       $facebook_created_cnt;
        $data['facebook_r']    =       $facebook_recieved_cnt;
        $data['twitter_c']    =       $twitter_created_cnt ;
        $data['twitter_r']    =       $twitter_recieved_cnt;
        $data['whatsapp_r']    =       $whatsapp_bot_cnt;
        $data['whatsapp_c']    =      0 ; // pass value here when available
        $data['chat_r']    =       $chat_bot_cnt;
        $data['chat_c']    =       $chat_created_cnt ;
        $data['sms_r']    =       $sms_cnt;
        $data['sms_c']    =       $sms_created_cnt ;
        $data['email_r']    =       $email_cnt;
        $data['email_c']    =       $email_created_cnt;
        


    /* CRM End */

    /* Telephony Starts */

        $live_agents= $link->query("SELECT user,status,campaign_id from $db_asterisk.autodial_live_agents where DATE(last_update_time)='$Startdate'");
        $live_agents_cnt= $live_agents->num_rows;
        
        $data['live_agents_count']    =       $live_agents_cnt;

        $query =  $link->query("SELECT users.v_department,log.campaign_id,users.full_name,users.user,TIMESTAMPDIFF(SECOND,d_createdOn,NOW()) as TermCheck from $db_asterisk.autodial_users as users inner join $db_asterisk.recording_log as log on users.user=log.extension where log.start_time between '$Startdate' and '$Enddate' group by user");
       
        $highestCallAnswered = 0;
        $longLoginTime=0;
        $longCallAns=0;
        $longTalkTime=0;
        $totalcalloffered = 0 ;
        $totalcallanswered = 0 ;
        $no = 0;

        while ($row = $query->fetch_assoc()) {
            $no++;

            // call offered
            $query01Data = $link->query("SELECT count(*) as totalCallOffered from $db_asterisk.autodial_closer_log where call_date between '" . $Startdate . "' and '" . $Enddate . "' and user='" . $row['user'] . "'");
            $query01Row = $query01Data->fetch_assoc();
        
            // call answered
            $query02Data1 = $link->query("SELECT count(*) as totalADRAttempted from $db_asterisk.autodial_closer_log where call_date between '" . $Startdate . "' and '" . $Enddate . "' and user='" . $row['user'] . "' and status in ('DONE','INCALL')");
            $query02Row1 = $query02Data1->fetch_assoc();

            //overall talktime
            $query03Data = $link->query("SELECT sum(talk_sec) as totalTalkTime from $db_asterisk.autodial_agent_log where event_time between '" . $Startdate . "' and '" . $Enddate . "'  and user='" . $row['user'] . "' and talk_sec < 1200");
            $query03Row = $query03Data->fetch_assoc();

            // login time
            $query1= $link->query("SELECT SUM( diff ) AS totalLogin FROM (SELECT TIME_TO_SEC( TIMEDIFF( MAX( event_date ) , MIN( event_date ) ) ) AS diff
            FROM  $db_asterisk.autodial_user_log  WHERE event_date  BETWEEN  '" . $Startdate . "' and '" . $Enddate . "'	AND user = '" . $row['user'] . "' GROUP BY user, DATE( event_date ) 
            ORDER BY event_date DESC) AS total");
            $fetch1 = $query1->fetch_assoc();

            //answered calls
            $highestCallAnswered = $query02Row1['totalADRAttempted'];

            $totalcalloffered = $query01Row['totalCallOffered'];
            $totalcallanswered = $highestCallAnswered;

            //Highest Login Time
            if ( $fetch1['totalLogin'] > $longLoginTime)
            {
                $longLoginTime = $fetch1['totalLogin'];
                $longLoginName = $row['full_name'];
            }
            //Highest Call Answered
            if ( $highestCallAnswered > $longCallAns)
            {
                $longCallAns = $highestCallAnswered;
                $longCallAnsName = $row['full_name'];
            }
            //Highest Talk Time
            if ( $query03Row['totalTalkTime'] > $longTalkTime)
            {
                $longTalkTime = $query03Row['totalTalkTime'];
                $longTalkTimeName = $row['full_name'];
            }

        }

        $data['longCallAnsName'] = $longCallAnsName;
        $data['longCallAns'] = $longCallAns;
        $data['highestTalkTimeName'] = $longTalkTimeName;
        $data['highestTalkTime'] = getTimeInFormated($longTalkTime);
        $data['highestLoginTimeName'] = getTimeInFormated($longLoginTime);
        $data['highestLoginTime'] = $longLoginName;
        $data['percentage_answeredcall'] = round(($totalcallanswered / $totalcalloffered) * 100, 2).'%';

        



        /* Incoming calls */
        $stmt =
        "SELECT 
            DATE(call_date) AS call_date,
            COUNT(*) AS Total_INCOMING_Calls,
            SUM(CASE WHEN status IN ('DONE', 'INCALL') THEN 1 ELSE 0 END) AS Converts,
            SUM(CASE WHEN status = 'AGENT DROP' THEN 1 ELSE 0 END) AS AGENT_DROP,
            SUM(CASE WHEN status = 'QUEUE' THEN 1 ELSE 0 END) AS DROPS,
            SUM(CASE WHEN status = 'DROP' THEN 1 ELSE 0 END) AS QueueMaxAchieve,
            campaign_id
        FROM 
            $db_asterisk.autodial_closer_log
        WHERE 
            call_date >= '$Startdate' AND call_date <= '$Enddate'
        ORDER BY 
            campaign_id ASC
        ";

        $result_stmt = $link->query($stmt);
        $row_stmt = $result_stmt->fetch_row();
        $connected_total = $row_stmt[2];//connected
        $agentDrops_total = $row_stmt[3];//agentdrop
        $systenQueueDrops_total = $row_stmt[5];//drop
        $customerQueueDrops_total = $row_stmt[4];//queee

        /* Service calls */
        $service =
        "SELECT 
            COUNT(*) AS service 
        FROM 
            $db_asterisk.tbl_cdr 
        WHERE 
            start_time >= '$Startdate' 
            AND end_time <= '$Enddate' 
            AND status IN ('SERVICING')
        ";

        $result_service = $link->query($service);
        $service_result = $result_service->fetch_assoc();
        $total_service = $service_result['service'];

        /* Forwarding calls */
        $Forwarding =
        "SELECT 
                COUNT(*) AS forwarded 
            FROM 
                $db_asterisk.tbl_cdr 
            WHERE 
                start_time >= '$Startdate' 
                AND end_time <= '$Enddate' 
                AND status IN ('FORWARDING')
        ";

        $result_forwarding = $link->query($Forwarding);
        $row_result = $result_forwarding->fetch_assoc();
        $total_forwarding = $row_result['forwarded'];


        /* IVR abandoned calls */
        $query_ivr =
        "SELECT 
                COUNT(*) AS ivrs_abandonCalls  
            FROM 
                $db_asterisk.tbl_cdr cd 
            JOIN 
                $db_asterisk.tbl_cdrlog cdl 
            ON 
                cdl.v_SessionId = cd.uniqueid  
            WHERE 
                d_StartTime >= '$Startdate' 
                AND d_StartTime <= '$Enddate' 
                AND (cdl.status = 'NO ANSWER' OR cdl.status = 'MISSED CALL') 
                AND cdl.missedcallcause LIKE '%CALL DISCONNECTED%'
        ";

        $result_ivr = $link->query($query_ivr);
        $rowt_ivr = $result_ivr->fetch_row();
        $ivrs_abandonCalls_total = $rowt_ivr[0];


        /* Voicemails */
        $stmv =
        "SELECT 
             COUNT(*) AS non_officehour_voicemail 
        FROM 
            $db_asterisk.tbl_cc_voicemails 
        WHERE  
            voicemailtime >= '$Startdate' 
        AND voicemailtime <= '$Enddate' 
        ";

        $result_stmv = $link->query($stmv);
        $rowv = $result_stmv->fetch_row();
        $non_officehour_total_voicemail = $rowv[0];

        /* TOTAL INBOUND CALLS  */
        $total_inbound = $connected_total + $total_service + $total_forwarding + $agentDrops_total + $systenQueueDrops_total + $customerQueueDrops_total + $ivrs_abandonCalls_total + $non_officehour_total_voicemail;

        /* Outbound Answer */
        $outbound =
        "SELECT 
                COUNT(recording_id) 
            FROM 
                $db_asterisk.recording_log 
            WHERE 
                start_time >= '$Startdate' AND start_time <= '$Enddate'
                AND location IN ('Connected', 'Transfer') 
                AND call_type IN ('dialpad', 'c2c')
        ";

        $result_outbound = $link->query($outbound);
        $row_outbound = $result_outbound->fetch_row();
        $outboundanswered = $row_outbound[0];

        /* Outbound No Answer/ Busy */
        $query_outbound =
        "SELECT 
            COUNT(recording_id) 
        FROM 
            $db_asterisk.recording_log 
        WHERE 
            start_time >= '$Startdate' AND start_time <= '$Enddate'
            AND location NOT IN ('Connected', 'Transfer') 
            AND call_type IN ('dialpad', 'c2c')
        ";

        $result_outbound = $link->query($query_outbound);
        $row_oubound = $result_outbound->fetch_row();
        $ouboundans = $row_oubound[0];

        /* TOTAL OUTBOUND CALLS  */
        $total_outbound = $outboundanswered + $ouboundans;


        /* Talktime */
        $query_talktime =
        "SELECT 
            SUM(talk_sec)
        FROM 
            $db_asterisk.autodial_agent_log 
        WHERE 
            event_time >= '$Startdate' AND event_time <= '$Enddate'
            AND talk_sec < 1200
        ";

        $result_talktime = $link->query($query_talktime);
        $row_talktime = $result_talktime->fetch_assoc();
        $talktime = getTimeInFormated($row_talktime['SUM(talk_sec)']);

        /* Average Talktime */
        $avgtlk = getTimeInFormated($row_talktime['SUM(talk_sec)']/$totalcallanswered);


        /* Wrapup Time */
        $query_wrapuptime =
        "SELECT 
            SUM(dispo_sec)
        FROM 
            $db_asterisk.autodial_agent_log 
        WHERE 
            event_time >= '$Startdate' AND event_time <= '$Enddate'
            AND talk_sec < 1200
        ";

        $result_wrapuptime = $link->query($query_wrapuptime);
        $row_wrapuptime = $result_wrapuptime->fetch_assoc();
        $wrapuptime = getTimeInFormated($row_wrapuptime['SUM(dispo_sec)']);

        /* TOTAL CALLS INBOUND + OUTBOUND  */
        $total_calls = $total_inbound + $total_outbound;

        /* Inbound missedcalls  */
        $inbound_missedcall = $agentDrops_total + $systenQueueDrops_total;

        $data['total_calls'] = $total_calls;
        $data['total_inbound'] = $total_inbound;
        $data['total_outbound'] = $total_outbound;
        $data['talktime'] = $talktime;
        $data['average_talktime'] = $avgtlk;
        $data['wrapuptime'] = $wrapuptime;
        $data['voicemail'] = $non_officehour_total_voicemail;
        $data['abandoned'] = $ivrs_abandonCalls_total ;
        $data['missedcall'] = $inbound_missedcall ;


        $sql_agent = $link->query("SELECT user, status, campaign_id FROM $db_asterisk.autodial_live_agents WHERE last_update_time BETWEEN '$Startdate' AND '$Enddate'");
        $live_agents = '';
        if( $sql_agent->num_rows > 0) {
            while ($row_agent = $sql_agent->fetch_assoc()) {
                $user = $row_agent['user'];
                $status = $row_agent['status'];
                $class = [
                    'INCALL' => "bg-danger",
                    'PAUSED' => "bg-warning",
                    'WRAPUP' => "bg-success",
                    'QUEUE' => "bg-blue",
                    'READY' => "bg-primary",
                ];
                $class = $class[$status];
                $live_agents .= "<span class='liveusers'><img src='../public/images/agent.png' alt='User Image' class='liveuserimage'>&nbsp;$user<br><span class=\"custom-badge $class text-white mx-3\">$status </span></span>";
            }
        }else{
             
             $live_agents .= '<span class="liveusers"><img src="../public/images/agent.png" alt="User Image" class="liveuserimage">&nbsp;';
             $live_agents .= 'No Agent<br><span class="custom-badge bg-danger text-white mx-3">LIVE</span>';
             $live_agents .= '</span>';
         }
       
    /* Telephony END */
        $json_data = json_encode(['status' => 'success', 'info' => $data, 'cat' => $str_category_json, 'subcat' => $str_subcategory_json, 'lang' => $str_language_json, 'live_agents' => $live_agents]);

        return $json_data;

}


if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_REQUEST["action"]) && $_REQUEST['action'] == 'dashboard') {

    if (isset($_POST['Startdate']) && !empty($_POST['Startdate'])) {
        $Startdate = date("Y-m-d H:i:s", strtotime($_POST['Startdate']));
    }
    
    if (isset($_POST['Enddate']) && !empty($_POST['Enddate'])) {
        $Enddate = date("Y-m-d H:i:s", strtotime($_POST['Enddate']));
    }

   echo get_dashboard_records($Startdate,$Enddate);

}else{

    echo json_encode(['status' => 'failed', 'info' => 'No data found']);

}
exit();