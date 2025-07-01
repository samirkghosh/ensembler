<?php
/***
 * Dashborad page
 * Author: Aarti
 * Date: 16-01-2024
 * Description: This file handles the display all chart, graphs, and bar charts.
 * C-SAT/D-SAT Graph
 * Overall Dashboard
 **/
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// Include necessary functions
include("../web_function.php");
   include_once('header.php');

// User profile details fetch
function uniuserprofile(){
  global $db,$link;
  $sqlagent="select AtxUserID,AtxUserName from $db.uniuserprofile where AtxUserStatus='1' ORDER BY AtxUserName ASC";
    $result=mysqli_query($link,$sqlagent);
    return $result;
}
// for feedback details score
function fetchSatisfactionData($query_str_sat) {
      global $db,$link;
    $str_score_cat="";
    $sql_satifaction1="SELECT count( * ) AS Total_Calls,
                       sum(CASE WHEN Dialed_Digit IN ('1','2','3') THEN 1 ELSE 0 END ) AS GOOD, 
                       sum(CASE WHEN Dialed_Digit IN ('4','5') THEN 1 ELSE 0 END ) AS BAD 
                       FROM $db.tbl_civrs_cdr WHERE 1=1  $query_str_sat " ;
    $query_satisfaction1=mysqli_query($link,$sql_satifaction1);
    $Fetch_satis=mysqli_fetch_assoc($query_satisfaction1);
    return $Fetch_satis; // Returning the result
} 

function fetchSatisfactionData2($startdatetime,$enddatetime,$agentquery) {
      global $db,$link;
    $query_str_satq = " and (Connect_time between '$startdatetime' and '$enddatetime') $agentquery";
    $sql_satifaction2="SELECT count( * ) AS Total_Calls2,
                        sum(CASE WHEN Dialed_Digit IN ('1','2','3') THEN 1 ELSE 0 END ) AS GOOD, 
                        sum(CASE WHEN Dialed_Digit IN ('4','5') THEN 1 ELSE 0 END ) AS BAD ,
                        sum(CASE WHEN Dialed_Digit IN ('1') THEN 1 ELSE 0 END ) AS HAPPY_C,
                        sum(CASE WHEN Dialed_Digit IN ('2') THEN 1 ELSE 0 END ) AS Satisfied_C, 
                        sum(CASE WHEN Dialed_Digit IN ('3') THEN 1 ELSE 0 END ) AS Neutral_C, 
                        sum(CASE WHEN Dialed_Digit IN ('4') THEN 1 ELSE 0 END ) AS Unsatisfied_C,
                        sum(CASE WHEN Dialed_Digit IN ('5') THEN 1 ELSE 0 END ) AS Demotivated_C
                        
                        FROM $db.tbl_civrs_cdr WHERE (Type=1 or Type=2) and Dialed_Digit!='NULL'  $query_str_satq ";
    $query_satisfaction2=mysqli_query($link,$sql_satifaction2)or die(mysqli_error($link)."Error toll free query");
    $Fetch_satis2=mysqli_fetch_assoc($query_satisfaction2);
    return $Fetch_satis2; // Returning the result
}

function fetchSatisfactionData3($query_str_satq) {
    global $db, $link; // Assuming $link is your database connection object
    $sql_satifaction3="SELECT count( * ) AS Total_Calls3,
                        sum(CASE WHEN Dialed_Digit IN ('3','4','5') THEN 1 ELSE 0 END ) AS GOOD, 
                        sum(CASE WHEN Dialed_Digit IN ('1','2') THEN 1 ELSE 0 END ) AS BAD ,
                        sum(CASE WHEN Dialed_Digit IN ('5') THEN 1 ELSE 0 END ) AS HAPPY_C,
                        sum(CASE WHEN Dialed_Digit IN ('4') THEN 1 ELSE 0 END ) AS Satisfied_C,
                        sum(CASE WHEN Dialed_Digit IN ('3') THEN 1 ELSE 0 END ) AS Neutral_C,
                        sum(CASE WHEN Dialed_Digit IN ('2') THEN 1 ELSE 0 END ) AS Unsatisfied_C,
                        sum(CASE WHEN Dialed_Digit IN ('1') THEN 1 ELSE 0 END ) AS Demotivated_C
                        
                        FROM $db.tbl_civrs_cdr WHERE Type=2  $query_str_satq " ;
    // Uncomment below line if you want to echo the query for debugging
    // echo "<br>". $sql_satifaction3;
    $query_satisfaction3=mysqli_query($link,$sql_satifaction3);
    $Fetch_satis3=mysqli_fetch_assoc($query_satisfaction3);
    return $Fetch_satis3; // Returning the result
}

function fetchNpsData() {
    global $db, $link; // Assuming $link is your database connection object
    $sql_satifactionnps="SELECT count( * ) AS Total_Calls3,
                        sum(CASE WHEN feedback_value IN ('0','1','2','3','4','5','6') THEN 1 ELSE 0 END ) AS Detractors, 
                        sum(CASE WHEN feedback_value IN ('7','8') THEN 1 ELSE 0 END ) AS Passives ,
                        sum(CASE WHEN feedback_value IN ('9','10') THEN 1 ELSE 0 END ) AS Promoters
                        FROM $db.tbl_nps WHERE flag=1 $query_str_nps" ;
    $query_satifactionnps=mysqli_query($link,$sql_satifactionnps);
    $Fetch_satisnps=mysqli_fetch_assoc($query_satifactionnps);
    return $Fetch_satisnps; // Returning the result
}
function fetchCustomerEffortData() {
    global $db, $link; // Assuming $link is your database connection object
    $sql_satifaction5="SELECT count( * ) AS Total_Calls,
                        sum(CASE WHEN feedback_value IN ('1') THEN 1 ELSE 0 END ) AS Very_good, 
                        sum(CASE WHEN feedback_value IN ('2') THEN 1 ELSE 0 END ) AS Low_effort ,
                        sum(CASE WHEN feedback_value IN ('3') THEN 1 ELSE 0 END ) AS Neutral,
                        sum(CASE WHEN feedback_value IN ('4') THEN 1 ELSE 0 END ) AS high_effort,
                        sum(CASE WHEN feedback_value IN ('5') THEN 1 ELSE 0 END ) AS very_high_effort
                        FROM $db.tbl_customer_effort WHERE $query_str_customer";
    // Uncomment below line if you want to echo the query for debugging
    // echo $sql_satifaction5;
    $query_satisfaction5=mysqli_query($link,$sql_satifaction5);
    $Fetch_satis5=mysqli_fetch_assoc($query_satisfaction5);
    return $Fetch_satis5; // Returning the result
}

//Retrieves the total number of cases within a specified date range.
function getTotalCases($startdatetime, $enddatetime) {
      global $db, $link; // Assuming $link is your database connection object
    // Prepare query string
    $query_str = " and (d_createDate between '$startdatetime' and '$enddatetime')";
    // Execute query to get total cases
    $total_cases = mysqli_query($link, "SELECT count(*) as total from $db.web_problemdefination where d_createDate >= '$startdatetime' AND d_createDate <= '$enddatetime'");
    return $total_cases ? mysqli_fetch_assoc($total_cases) : "Error executing query: " . mysqli_error($link);
}
//Retrieves the total number of complaints within a specified date range
function getComplaintCount($startdatetime, $enddatetime) {
    global $db, $link; // Assuming $link is your database connection object
    // Prepare query string
    $query_str = " and (d_createDate between '$startdatetime' and '$enddatetime')";
    // Prepare the complaint count query
    $complaint_count_query = "SELECT count(p.iPID) as cnt FROM $db.web_problemdefination p WHERE (p.vCaseType='complaint') $query_str";
    // Execute the complaint count query
    $complaint_count_result = mysqli_query($link, $complaint_count_query);
    return $complaint_count_result ? mysqli_fetch_assoc($complaint_count_result)['cnt'] : "Error executing query: " . mysqli_error($link);
}
//Retrieves the count of inquiries within a specified query string
function getInquiryCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the inquiry count query
    $inquiry_count_query = "SELECT COUNT(p.iPID) AS cnt FROM $db.web_problemdefination p WHERE p.vCaseType='Inquiry' $query_str";    
    // Execute the inquiry count query
    $inquiry_count_result = mysqli_query($link, $inquiry_count_query);
    // Return the count of inquiries if query execution is successful, otherwise return an error message
    return $inquiry_count_result ? mysqli_fetch_assoc($inquiry_count_result)['cnt'] : "Error executing query: " . mysqli_error($link);
}
//Retrieves the count of cases with 'others' case type within a specified query string.
function getOtherCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the count query for 'others' cases
    $other_count_query = "SELECT COUNT(p.iPID) AS cnt FROM $db.web_problemdefination p WHERE p.vCaseType='others' $query_str";    
    // Execute the query to get the count of 'others' cases
    $other_count_result = mysqli_query($link, $other_count_query);
    // Return the count of 'others' cases if query execution is successful, otherwise return an error message
    return $other_count_result ? mysqli_fetch_assoc($other_count_result)['cnt'] : "Error executing query: " . mysqli_error($link);
}
//Retrieves the count of mentions within a specified query string.
function getMentionCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the mention count query
    $mention_count_query = "SELECT COUNT(p.iPID) AS cnt FROM $db.web_problemdefination p WHERE p.vCaseType='mention' $query_str";    
    // Execute the mention count query
    $mention_count_result = mysqli_query($link, $mention_count_query);
    // Return the count of mentions if query execution is successful, otherwise return an error message
    return $mention_count_result ? mysqli_fetch_assoc($mention_count_result)['cnt'] : "Error executing query: " . mysqli_error($link);
}
//Retrieves the count of pending cases within a specified query string.
function getPendingCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count pending cases
    $pending_query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination WHERE iCaseStatus='1' $query_str";
    // Execute the query to count pending cases
    $pending_result = mysqli_query($link, $pending_query);   
    // Return the count of pending cases if query execution is successful, otherwise return an error message
    return mysqli_fetch_assoc($pending_result);
}
//Retrieves the count of cases that are currently in progress within a specified query string. 
function getInProgressCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count cases in progress
    $inProgress_query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination WHERE iCaseStatus='8' $query_str";    
    // Execute the query to count cases in progress
    $inProgress_result = mysqli_query($link, $inProgress_query);
    // Return the count of cases in progress if query execution is successful, otherwise return an error message
    return mysqli_fetch_assoc($inProgress_result);
}
//Retrieves the count of closed cases within a specified query string.
function getClosedCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count closed cases
    $closed_query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination WHERE iCaseStatus='3' $query_str";    
    // Execute the query to count closed cases
    $closed_result = mysqli_query($link, $closed_query);
    // Return the count of closed cases if query execution is successful, otherwise return an error message
    return mysqli_fetch_assoc($closed_result);
}
//Retrieves the count of assigned complaints within a specified query string.
function getAssignedCount($query_str) { 
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count assigned complaints
    $assigned_query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination WHERE iCaseStatus='1' AND vCaseType='complaint' AND vProjectID > '0' $query_str";    
    // Execute the query to count assigned complaints
    $assigned_result = mysqli_query($link, $assigned_query);
    // Return the count of assigned complaints if query execution is successful, otherwise return an error message
    return mysqli_fetch_assoc($assigned_result);
}
//Retrieves the count of escalated cases within a specified query string.
function getEscalatedCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count escalated cases
    $escalated_query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination WHERE iCaseStatus='4' AND escalate_status='1' $query_str"; 
    // Execute the query to count escalated cases
    $escalated_result = mysqli_query($link, $escalated_query);
    // Return the count of escalated cases if query execution is successful, otherwise return an error message
    return mysqli_fetch_assoc($escalated_result);
}
//Retrieves the count of voicemails within a specified date range.
function getVoiceMailCount($startdatetime, $enddatetime) {
    global $link; // Assuming $link is your database connection object
    // Construct the query to count voicemails
    $voice_query = "SELECT COUNT(*) AS total FROM asterisk.tbl_cc_voicemails WHERE callerid != '' AND flag1 = '0' AND voicemailtime BETWEEN '$startdatetime' AND '$enddatetime'";
    // Execute the query to count voicemails
    $voice_result = mysqli_query($link, $voice_query);
    // Fetch the result row
    $voice_row = mysqli_fetch_assoc($voice_result);   
    // Return the count of voicemails
    return $voice_row['total'];
}
//Retrieves the count of cases with First Call Resolution (FCR) within a specified query string.
function getFCRCount($query_str) {
    global $db, $link; // Assuming $link is your database connection object
    // Construct the query to count FCR cases
    $fcr_count_query = "SELECT COUNT(iPID) AS cnt FROM $db.web_problemdefination WHERE d_createDate = d_updateTime AND iCaseStatus='3' $query_str";    
    // Execute the query to count FCR cases
    $fcr_count_result = mysqli_query($link, $fcr_count_query);
    // Fetch the result row
    $fcr_row = mysqli_fetch_assoc($fcr_count_result);
    // Return the count of FCR cases
    return $fcr_row['cnt'];
}
//Executes the query to retrieve recording log data within a specified date range.
function executeRecordingLogQuery($link, $startdatetime, $enddatetime) {
    $query = "SELECT users.v_department, log.campaign_id, users.full_name, users.user, TIMESTAMPDIFF(SECOND, d_createdOn, NOW()) AS TermCheck 
              FROM asterisk.autodial_users AS users 
              INNER JOIN asterisk.recording_log AS log ON users.user = log.extension 
              WHERE log.start_time BETWEEN '$startdatetime' AND '$enddatetime'  
              GROUP BY user";
    return mysqli_query($link, $query);
}
//Retrieves the login time of a user within a specified campaign condition, user, and date.
function getUserLogInTime($link, $campCond, $user, $thisDate) {
    $query = "SELECT event_date FROM asterisk.autodial_user_log WHERE event = 'LOGIN' $campCond AND user = '$user' AND event_date LIKE '%$thisDate%' ORDER BY event_date ASC LIMIT 1";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    $logInData = mysqli_fetch_assoc($result);
    return $logInData ? strtotime($logInData['event_date']) : false;
}
//  Retrieves the logout time of a user within a specified campaign condition, user, and date.
function getUserLogOutTime($link, $campCond, $user, $thisDate) {
    $query = "SELECT event_date FROM asterisk.autodial_user_log WHERE event = 'LOGOUT' $campCond AND user = '$user' AND event_date LIKE '%$thisDate%' ORDER BY event_date DESC LIMIT 1";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    $logOutData = mysqli_fetch_assoc($result);
    return $logOutData ? strtotime($logOutData['event_date']) : false;
}
//Retrieves the total login time of a user within a specified date range.
function getTotalLoginTime($link, $startdatetime, $enddatetime, $user) {
    $query = "SELECT SUM(diff) AS totalLogin FROM (
                    SELECT TIME_TO_SEC(TIMEDIFF(MAX(event_date), MIN(event_date))) AS diff
                    FROM asterisk.autodial_user_log  
                    WHERE event_date BETWEEN '$startdatetime' AND '$enddatetime' AND user = '$user'
                    GROUP BY user, DATE(event_date) 
                    ORDER BY event_date DESC
                ) AS total";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    $fetch = mysqli_fetch_array($result);
    return $fetch ? getTimeInFormated($fetch['totalLogin']) : false;
}
// Retrieves the count of ADR (Automatic Dialer Record) attempts made by a user within a specified date range.
function getADRAttemptedCount($link, $startdatetime, $enddatetime, $user) {
    $query = "SELECT count(*) as totalADRAttempted 
              FROM asterisk.autodial_closer_log 
              WHERE call_date BETWEEN '$startdatetime' AND '$enddatetime' 
              AND user='$user' 
              AND status IN ('DONE','INCALL')";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    $fetch = mysqli_fetch_assoc($result);
    return $fetch ? $fetch['totalADRAttempted'] : false;
}
// Retrieves the total talk time of a user within a specified date range.
function getTotalTalkTime($link, $startdatetime, $enddatetime, $user) {
    $query = "SELECT SUM(talk_sec) as totalTalkTime 
              FROM asterisk.autodial_agent_log 
              WHERE event_time BETWEEN '$startdatetime' AND '$enddatetime' 
              AND user='$user'";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    $fetch = mysqli_fetch_assoc($result);
    return $fetch ? $fetch['totalTalkTime'] : false;
}
// Retrieves the count of calls offered to a user within a specified date range.
function getUserOfferedCount($link, $startdatetime, $enddatetime, $user) {
    $cond_usr = "AND user='$user'";
    $stmt = "SELECT * FROM ( SELECT * FROM asterisk.autodial_closer_log WHERE user!='' AND (call_date >= '$startdatetime' AND call_date <= '$enddatetime' $cond_usr) ORDER BY call_date DESC ) AS sub GROUP BY v_SessionId";
    $result = mysqli_query($link, $stmt);
    if (!$result) {
        return -1;
    }
    return mysqli_num_rows($result);
}
//Retrieves the count of cases grouped by language within a specified date range.
function getLanguageCount($link, $db, $startdatetime, $enddatetime) {
    $query = "SELECT COUNT(*) AS language_cnt, language_id FROM $db.web_problemdefination WHERE d_createDate BETWEEN '$startdatetime' AND '$enddatetime' GROUP BY language_id";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    return $result;
}
// Retrieves the count of cases grouped by subcategory within a specified query string.
function getSubCategoryCount($link, $db, $query_str) {
    $query = "SELECT vSubCategory, COUNT(iPID) AS cnt FROM $db.web_problemdefination WHERE vSubCategory != 0 $query_str GROUP BY vSubCategory";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    return $result;
}
// Retrieves the count of cases grouped by subcategory within a specified query string.
function getSubCategoryCount1($link, $db, $query_str) {
    $query = "SELECT COUNT(iPID) AS cnt FROM $db.web_problemdefination WHERE vSubCategory != 0 $query_str GROUP BY iPID";
    $result = mysqli_query($link, $query);
    if (!$result) {
        return false;
    }
    return $result;
}


//function getCategoryCounts($query_str) { 
//    global $link, $db;
//    $cat_sql = "SELECT vCategory, COUNT(*) AS category_cnt FROM $db.web_problemdefination WHERE vCustomerID != '' $query_str GROUP BY vCategory";
//    $result = mysqli_query($link, $cat_sql);
//   if (!$result) {
//     return false;
//   }
//   return $result;
//} 

//function getCategoryCountss($query_str) {
  //  global $db, $link;
  //  $cat_sql = "SELECT COUNT(*) AS category_cnt FROM $db.web_problemdefination WHERE vCustomerID != '' $query_str GROUP BY vCategory";
  //  $result = mysqli_query($link, $cat_sql);
  //  if (!$result) {
    //    return false;
   // }
   // return $result;
//}

// get seconds in time format start
function getTimeInFormated($seconds)
{
  $hours = floor($seconds / 3600);
  $mins = floor($seconds / 60 % 60);
  $secs = floor($seconds % 60);
  return $timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
}
//Calculates the rate of resolution for complaints within a specified date range.
function getRateOfResolution($fdate,$tdate)
{
  global $link,$db;
  $msg = '';
      // Current date
    $cdate = date('Y-m-d 00:00:00');
    // Date 21 days ago
    $dateLess21 = date('Y-m-d 00:00:00', strtotime("-21 days"));
    // Check if the start date is at least 21 days ago
    if ($fdate >= $dateLess21) {
        $msg = "Minimum 21 Days Required";
        return $msg;
    }
    // Determine the end date of the effective date range
    if ($tdate > $dateLess21)
        $toDateRange = $dateLess21;
    else 
        $toDateRange = $tdate;
    // Calculate the number of days in the date range
    $N = dateDiffInDays($toDateRange, $fdate) + 1;
    // Retrieve the total number of complaint cases within the date range
    $qry = "SELECT count(*) as total from $db.web_problemdefination where vCaseType = 'complaint' and d_createDate BETWEEN '$fdate' AND '$toDateRange'";   
    $totalRes = mysqli_query($link, $qry);
    $totalRow = mysqli_fetch_assoc($totalRes);
    $TotalCases = $totalRow['total'];    
    // Retrieve the total number of resolved and closed complaint cases within the date range
    $qry = "SELECT count(*) as total from $db.web_problemdefination where vCaseType = 'complaint' and (iCaseStatus='3' OR iCaseStatus='8') AND d_createDate BETWEEN '$fdate' AND '$toDateRange'";
    $resolvedRes = mysqli_query($link, $qry);
    $resolvedRow = mysqli_fetch_assoc($resolvedRes);
    $resolvedCases = $resolvedRow['total'];
    
    // Calculate the rate of resolution
    if ($TotalCases > 0) {
        $res = ($resolvedCases / $TotalCases) * 100;
        return (round($res, 2)) . "%";
    } else {
        return "No Case";
    }
}


/*********Overall dashboard page functions********/

function getLiveAgentsCount($link, $today_date) {
    $sql_agent = mysqli_query($link, "SELECT user, status, campaign_id FROM $db_asterisk.autodial_live_agents WHERE DATE(last_update_time)='$today_date'");
    $result= mysqli_query($sql_agent);
    return $result;
}

?>