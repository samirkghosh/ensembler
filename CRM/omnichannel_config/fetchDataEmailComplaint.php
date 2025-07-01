<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 22/04/2024
 * Description: To Fetch Data for Emal Complaint and Email Enquiry
 */
// file for database connection and other neccessary files 
include("../../config/web_mysqlconnect.php");
include("omnichannel_function.php");
include_once("function_define.php");
include("date.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if($_POST['action'] =='email_complaint'){
	email_complaint();
}
if($_POST['action'] =='email_enquiry'){
	email_complaint();
}

function email_complaint(){
    global $db,$link;
    $column = array('EMAIL_ID', 'd_email_date', 'v_fromemail', 'v_subject', 'ICASEID', 'iCaseStatus','sentiment', 'classification');
    
    $startdatetime = $_POST['startdatetime'];
    $enddatetime = $_POST['enddatetime'];
    $email = $_POST['email'];
    $iallstatus = $_POST['iallstatus'];
    $serviceable = $_POST['classification'];
    $sentiment = $_POST['sentiment'];
    $action = $_POST['action'] ?? '';

    
    ################### Change the fomats YYYY-mm-dd H:i:s #####################
    $changestartdatetime=datetimeformat($startdatetime);
    $changeenddatetime=datetimeformat($enddatetime);  
    $groupid=$_SESSION['user_group']; 
    $login_email = $_SESSION['login_email']; 

    $str="";
    if($groupid!='0000' && $groupid!='080000'){ 
        $str = " and email_type='IN' "; 
    }else{ 
        $str = " and email_type='IN' ";
    }
    $iallstatus1 = getIallstatusCondition($iallstatus, $str);

 
    ###########################################################################
    if($iallstatus!='2')
    {
        $delcond= " and i_DeletedStatus!=2 ";
    }

    $email_type = $action == 'email_enquiry' ? "and queue_type='inquiry'" : "and queue_type='complain'";
   
    $filter = '';
    $sql = "SELECT * FROM $db.web_email_information WHERE I_Status=1 $email_type  ";

    // Apply filters based on conditions
    if (isset($_GET['id'])) {
        $str = "EMAIL_ID=" . $_GET['id'];
        $sql .= "AND $str ";
    } 
    $sql .= buildEmailFilterSql($email, $sentiment, $changestartdatetime, $changeenddatetime, $iallstatus1, $delcond, $str, $serviceable);

    // Apply order by code
    if (isset($_POST['order'])) {
        // If the order is requested and it's not the first column, apply sorting
        if ($_POST['order'][0]['column'] != 0) {
            $sql .= ' ORDER BY ' . $column[$_POST['order'][0]['column']] . ' ' . $_POST['order'][0]['dir'];
        } else {
            // If it's the first column, don't apply sorting
            $sql .= ' ORDER BY d_email_date DESC';
        }
    } else {
        $sql .= ' ORDER BY d_email_date DESC';
    }
     // updated the code for fetching first 25 records [vastvikta][13-03-2025]

    // Apply limit code
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $limit .= " LIMIT $start, $length";
    }
    
        $totalRecordsQuery = mysqli_query($link, $sql);
        $totalRecords = mysqli_num_rows($totalRecordsQuery);

        $sql.=$limit;
    // print_r($sql);
    $data = array();
    if($totalRecords>0){
        // Execute the final SQL query
        $query = mysqli_query($link, $sql);
        $data = fetchEmailData($query, $link, $db, $column);
    }

    // Prepare the response
    $output = array(
        "sql"=>$sql,
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $totalRecords, 
        "data"            => $data
    );
    // print_r($output);
    // Encode response as JSON and send
    echo json_encode($output);
}

function fetchEmailData($query, $link, $db, $column) {
    $data = array();
    $serial = $_POST['start'] + 1; // Adjust serial number for pagination
    
    while ($row = mysqli_fetch_array($query)) {
        // echo "<pre>";print_r($row); die;
        $email_date = $row['d_email_date'];
        $email_id = $row['EMAIL_ID'];
        $from_email = $row['v_fromemail'];
        $classification = $row['classification'];
        $subject = $row['v_subject'];
        // $status = $row['iCaseStatus'];
        $flag = $row['Flag'];
        $ICASEID = $row['ICASEID'];
        $sentiment = $row['sentiment'];

        $subject = subject_decode_string($subject);
        $where_c = "ticketid ='$ICASEID'";

        if (!empty($ICASEID)) {
            $qq = "SELECT iCaseStatus, ticketid, iPID, vCustomerID, regional FROM $db.web_problemdefination WHERE ($where_c)";
            $q1 = mysqli_query($link, $qq);
            $numRows = mysqli_num_rows($q1);
            $fetch1 = mysqli_fetch_array($q1);
            $case_id = $fetch1['iPID'];
            $caseee = $fetch1['ticketid'];
            $Customerid = $fetch1['vCustomerID'];
            $regional = $fetch1['regional'];
            $regional_stg = !empty($regional) ? $regional . '_' : '';

            $qcust = mysqli_fetch_array(mysqli_query($link, "SELECT email FROM $db.web_accounts WHERE AccountNumber='" . $Customerid . "'"));
            if (($qcust['email'] == $from_email) && $qcust['email'] != '') {
                $q = "UPDATE $db.web_email_information SET ICASEID='" . $caseee . "', email_test='web_queue_1' WHERE EMAIL_ID='" . $email_id . "';";
                mysqli_query($link, $q);
            }
            
            $ress = mysqli_fetch_array(mysqli_query($link, "SELECT iCaseStatus, ticketid FROM $db.web_problemdefination WHERE (iPID='$case_id')"));
            $rest = mysqli_fetch_array(mysqli_query($link, "SELECT ticketstatus FROM $db.web_ticketstatus WHERE id='" . $ress['iCaseStatus'] . "';"));
            $status = $rest['ticketstatus'];
        }
        
        $url = 'clearCaseID.php?caseid=' . $row['ICASEID'] . '&queueid=' . $email_id;
        $subject_output = trim($subject) != "" ? $subject : "No subject";
        $description = subject_decode_string($row['v_body']);

        $clr = ($flag == 1) ? "yellow" : (($flag == 0) ? "red" : (($flag == 2) ? "green" : ""));
        $checkbox = '<input type="checkbox" name="email_checkbox[]" value="' . $email_id . '">';

        $sub_array = array();
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . date('d-m-Y H:i', strtotime($email_date)) . '</span>';
        $sub_array[] = '<span>' . $from_email . '</span>';
        // updated code for attachment [vastvikta][05-06-2025]
        $paperclip_icon = !empty($row['V_rule']) ? '<i class="fa fa-paperclip" style="margin-left:5px;color:#888;" title="Has Rule"></i>' : '';
        $caseopentime = $row['case_open_time'];
        $i_reminder = $row['i_reminder'];
        $vuserid = $row['userid']; // assuming this is set in $row too
        
        $caseTime = new DateTime($caseopentime);
        $currentTime = new DateTime();
        $interval = $currentTime->getTimestamp() - $caseTime->getTimestamp(); // in seconds
        
        if ($i_reminder == 1 && $interval <= 30 && $vuserid != $_SESSION['userid']) {
            // Fetch the name of the user currently working on the case
            $userName = 'Someone'; // Default fallback
        
            $userName = getUserNameByAtxUserID($link, $db, $vuserid);
        
            // Show alert with specific user name
            $sub_array[] = '<a title="In Use" href="javascript:void(0)" onclick="alert(\'' . htmlspecialchars($userName) . ' IS WORKING ON IT\')">' . $subject_output . '</a>' . $paperclip_icon;
        }else {
            // Normal popup
            $sub_array[] = '<a title="CreateComplain" href="javascript:void(0)" onclick="Centerss(900,600,50,\'omnichannel_config/subjectpopup.php?id=' . $email_id . '&classification=' . $classification . '\',\'demo_win\');">' . $subject_output . '</a>' . $paperclip_icon;
        }
        if ($ICASEID == '0') {
            $caseee = "";
        } else {
            $w = mysqli_fetch_array(mysqli_query($link, "SELECT ticketid, iCaseStatus FROM $db.web_problemdefination WHERE ticketid='$ICASEID'"));
            $caseee = $w['ticketid'];
            $rest = mysqli_fetch_array(mysqli_query($link, "SELECT ticketstatus FROM $db.web_ticketstatus WHERE id='" . $w['iCaseStatus'] . "';"));
            $status = $rest['ticketstatus'];
        }

        $web_case_detail = base64_encode('web_case_detail');
        $idd = base64_encode($ICASEID); 

        if ($row['ICASEID'] != '') {
            $sub_array[] = 'Case ID : <a href="helpdesk_index.php?token=' . $web_case_detail . '&id=' . $idd . '&mr=">' . $caseee . '</a>';
        } else {
            $sub_array[] = "";
        }

        $rest = mysqli_fetch_array(mysqli_query($link, "SELECT ticketstatus FROM $db.web_ticketstatus WHERE id='" . $row['iCaseStatus'] . "';"));
        $status = '<span>' . $rest['ticketstatus'] . '</span>';
        $sub_array[] = '<span>' . $status . '</span>';
        $sub_array[] = '<span>' . $sentiment . '</span>';
        $sub_array[] = '<span>' . getClassificationLabel($classification) . '</span>';
        $sub_array[] = $clr;
        $data[] = $sub_array;
        $serial++;
        // print_r($data);
    }
    
    return $data;
}
function getUserNameByAtxUserID($link, $db, $atxUserID) {
    $userName = 'Someone'; // Default fallback

    $query = "SELECT AtxUserName FROM {$db}.uniuserprofile WHERE AtxUserID = ?";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $atxUserID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $fetchedName);
        if (mysqli_stmt_fetch($stmt)) {
            $userName = $fetchedName;
        }
        mysqli_stmt_close($stmt);
    }

    return $userName;
}

function buildEmailFilterSql($email, $sentiment, $changestartdatetime, $changeenddatetime, $iallstatus1, $delcond, $str, $serviceable) {
    $sql = '';
    
    if (isset($_GET['id'])) {
        $sql .= "AND EMAIL_ID=" . $_GET['id'] . " ";
    } 
    else if ($sentiment != "") {
        $sql .= "AND sentiment = '$sentiment' ";
    }  
    
    else if ($email == "") {
       
        $sql .= "  $iallstatus1 $delcond AND v_fromemail NOT IN ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com')";
    } 
    else if ($email != "") {
       
        $sql .= " AND v_fromemail LIKE '%$email%' $iallstatus1 $delcond AND v_fromemail NOT IN ('no-reply@accounts.google.com', 'forwarding-noreply@google.com', 'mail-noreply@google.com', 'mailer-daemon@googlemail.com')";
    }
    // updated the code to fetch the data according to date always [vastvikta][17-03-2025]
    if (!empty($changestartdatetime) || !empty($changeenddatetime)) {

        $sql.="AND d_email_date >='$changestartdatetime' AND d_email_date <='$changeenddatetime'";
        $sql .= "  $iallstatus1 $delcond AND v_fromemail NOT IN ('no-reply@accounts.google.com','forwarding-noreply@google.com','mail-noreply@google.com','mailer-daemon@googlemail.com')";
    }
    if (!empty($serviceable)) {
        // Assuming $serviceable values correspond to classification values
        $serviceableCondition = "AND classification = '$serviceable'";
        $sql .= $serviceableCondition;
    }else{//added this for showingnon spam email 
        $serviceableCondition = "AND classification != '3'";
        $sql .= $serviceableCondition;
    }
    
    return $sql;
}
function getIallstatusCondition($iallstatus, $str){
    
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
    }else if($iallstatus==3){ 
        $iallstatus1 = " and email_type='IN' and (ICASEID='' || ICASEID IS NULL ) "; $notin = "'$centralspoc',"; 
    }
    
}
function count_all_data($action){
    global $db,$link;

    if($action=='email_complaint'){
    $query = "SELECT * FROM $db.web_email_information  where  queue_type = 'complain' ";
    }
    else{
        $query = "SELECT * FROM $db.web_email_information  where queue_type = 'inquiry' ";
    }
    $case_result = mysqli_query($link, $query); // get status filter option
    $total = mysqli_num_rows($case_result);
    return $total;
   }
?>
