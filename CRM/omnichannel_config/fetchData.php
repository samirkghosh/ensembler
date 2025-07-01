<?php
/*
auth: Vastvikta Nishad 
Date: 26-04-2024 
Description: TO fetch Data From Database for SMS Twitter Whatsapp and Facebook
Handling all social media Ul datatable design code
*/

// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");
include("omnichannel_function.php");
include_once("../../function/web_function_define.php");
include("date.php");
include_once("../function/classify_function.php"); // Some Common File code handling for web_chat

if(isset($_POST['action']) && $_POST['action'] =='twitter'){
	twitter_data(); // fetching twitter data listing
}
if(isset($_POST['action']) && $_POST['action'] =='sms'){
	sms_data(); // fetching SMS data listing
}
if(isset($_POST['action']) && $_POST['action'] =='facebook'){
	facebook_data(); // fetching facebook data listing
}
if(isset($_POST['action']) && $_POST['action'] =='whatsapp'){
	whatsapp_data(); // fetching whatsapp data listing
}
if(isset($_POST['action']) && $_POST['action'] =='send_sms_reply'){
    send_sms_reply(); // fetching sms data listing
}
if (isset($_POST['action']) && $_POST['action'] == 'web_chat') {
    web_chat();
}
//for messenger datatable code[Aarti][12-08-2024]
if($_POST['action'] =='Messenger_Datatable'){ 
    Messenger_Datatable();
}
// For display Whatsapp Repor
if(isset($_POST['action']) && $_POST['action'] =='Whatsapp_report'){
	Whatsapp_report(); 
}
// For display Email Repor
if(isset($_POST['action']) && $_POST['action'] =='email_queue_report'){
	email_queue_report();
}
// For display messenger Repor
if(isset($_POST['action']) && $_POST['action'] =='messenger_report'){
	messenger_report(); 
}
// For display twitter Report
if(isset($_POST['action']) && $_POST['action'] =='twitter_report'){
	twitter_report(); 
}
// For display SMS Report
if(isset($_POST['action']) && $_POST['action'] =='SMS_report'){
	SMS_report(); 
}
// For display Instagram DM
if (isset($_POST['action']) && $_POST['action'] == 'Instagram_Datatable') {
    Instagram_Datatable();
}
// For display Instagram report
if(isset($_POST['action']) && $_POST['action'] =='insta_report'){
	insta_report(); 
}
// for display Instagram Post details
if (isset($_POST['action']) && $_POST['action'] == 'Instagram_Datatable_Post') {
    Instagram_Datatable_Post();
}
// code for appending message sent via whatsapp [vastvikta][10-03-2025]
if(isset($_POST['action']) && $_POST['action'] =='whatsapp_reply'){
	whatsapp_reply(); 
}
// code for appending message sent via messenger [vastvikta][10-03-2025]
if(isset($_POST['action']) && $_POST['action'] =='messenger_reply'){
	messenger_reply(); 
}
// code for appending message sent via instagram [vastvikta][10-03-2025]
if(isset($_POST['action']) && $_POST['action'] =='instagram_reply'){
	instagram_reply(); 
}
if(isset($_POST['action']) && $_POST['action'] =='WhatsAppDM_Live'){
    WhatsAppDM_Live(); 
}
if(isset($_POST['action']) && $_POST['action'] =='MessengerDM_Live'){
    MessengerDM_Live(); 
}
if(isset($_POST['action']) && $_POST['action'] =='InstagramDM_Live'){
    InstagramDM_Live(); 
}

function web_chat() {
    global $link, $db;

    // Get the first day of the current month
    $default_startdatetime = '';
    // Get the current date and time
    $default_enddatetime = '';

    // Retrieve POST parameters with default values
    $startdatetime = isset($_POST['startdatetime']) && $_POST['startdatetime'] != '' ? $_POST['startdatetime'] : $default_startdatetime;
    $enddatetime = isset($_POST['enddatetime']) && $_POST['enddatetime'] != '' ? $_POST['enddatetime'] : $default_enddatetime;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $chat_type = isset($_POST['chat_type']) ? $_POST['chat_type'] : '';
    $caseid = isset($_POST['caseid']) ? $_POST['caseid'] : '';
    $send_name = isset($_POST['send_name']) ? $_POST['send_name'] : '';
    $iallstatus = isset($_POST['iallstatus']) ? $_POST['iallstatus'] : '';
    $columns = array('id', 'name', 'email', 'phone', 'content_text', 'caseid', 'createdDatetime');

    // Initialize condition for SQL query
    $condition = ""; 

    // Convert start and end datetimes to SQL format
    $from = date('Y-m-d H:i:s', strtotime($startdatetime));
    $to = date('Y-m-d H:i:s', strtotime($enddatetime));

    // Add condition for datetime range
    if ($startdatetime != '' && $enddatetime != '') { 
        $condition .= " AND `createdDatetime` >= '$from' AND `createdDatetime` <= '$to'"; 
    }

    // Add condition for filtering by phone number if provided
    if ($phone) { 
        $condition .= " AND `from` LIKE '%" . mysqli_real_escape_string($link, $phone) . "%'";
    }

    //[vastvikta][03-12-2024] sned name and case created and not created code
    if ($send_name) { 
        $condition .= " AND `name` = '$send_name'";
    }
    if ($iallstatus !== null) { 
        if($iallstatus == 1){
            $condition .= " AND `caseid` != '' ";
        }
        else if($iallstatus == 4){
            $condition .= "";
        }
        else{
            // $condition .= " AND `caseid` = '' ";
        }
    }

    // Add condition for filtering by caseid if provided
    if ($caseid) { 
        $condition .= " AND `caseid` LIKE '%" . mysqli_real_escape_string($link, $caseid) . "%'";
    }
    

    // Build SQL query based on provided parameters
    if ($id != null) {
        // Ensure the id is sanitized
        $id = mysqli_real_escape_string($link, $id); 
        $sql = "SELECT * FROM $db.`overall_bot_chat_session` WHERE id='$id'";
    } else {                        
        $sql = "SELECT * FROM $db.`overall_bot_chat_session` WHERE `chat_session` != '' AND `delete_status` = '0' $condition";
    }

    // Add condition for chat_type if applicable
    if ($chat_type == 1) {
        $sql .= " AND conversation_id IN (SELECT customer_id 
                    FROM $db.`in_out_data` 
                    GROUP BY customer_id
                    HAVING COUNT(CASE WHEN agent_id = '0' THEN 1 END) > 0
                    AND COUNT(CASE WHEN agent_id != '0' THEN 1 END) = 0
                    )";
    }

    
    // Execute query to get the total number of records
    // Default order by column and direction

    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $limit .= " LIMIT $start, $length";
    }
    
        $totalRecordsQuery = mysqli_query($link, $sql);
        $totalRecords = mysqli_num_rows($totalRecordsQuery);
   
        // Apply ORDER BY after query construction
    $sql .= " ORDER BY `createdDatetime` DESC";
    $sql.=$limit;


    // Execute SQL query and get the result set
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));

    $data = array();
    $serial = 1;

    // Process each row in the result set
    while ($row = mysqli_fetch_assoc($res)) {
        $phone_number = $row['from'];
        $customer_id = null;

        // Checking if phone number exists in web_accounts table
        $sqll = checkPhoneNumberExists($phone_number);
        $num = mysqli_num_rows($sqll);
        if ($num > 0) {
            $resdata = mysqli_fetch_array($sqll);
            $customer_id = $resdata['AccountNumber'];
            $customer_name = $resdata['fname'];
        }

        $web_case_detail = base64_encode('web_case_detail');
        $idd = base64_encode($row['caseid']); 

        if (!empty($customer_id)) {
            $web_customer_detail = base64_encode('web_customer_detail');
            $ref = $web_customer_detail . "&CustomerID=" . base64_encode($customer_id);
            $name = '<a style="color: #3974aa; !important" href="customer_index.php?token=' . $ref . '" target="_blank">' . $row['name'] . '</a>';
        } else {
            $name = $row['name'];
        }

        $flag = $row['bot_agent_flag'];
        $clr = "";
        if ($flag == 1) {
            $clr = "red";
        } elseif ($flag == 0) {
            $clr = "yellow";
        }

        $data[] = array(
            $serial,
            $name,
            $row['email'],
            $row['from'],
            '<a  class="ico-interaction2" href="omnichannel_config/chat_history.php?phone=' . $row['from'] . '&caseid=' . $row['caseid'] . '&session_id=' . $row['chat_session'] . '" class="iframe">' . $row['content_text'] . '</a>',
            $row['caseid'] ? '<p>CASE ID:</p><a href="helpdesk_index.php?token=' . base64_encode('web_case_detail') . '&id=' . base64_encode($row['caseid']) . '&mr=5">' . $row['caseid'] . '</a>' : 'Not Created',
            $row['createdDatetime'],
            '<a class="ico-interaction2" href="omnichannel_config/chat_disposition.php?id=' . $row['id'] . '&from=' . urlencode($row['from']) . '&email=' . urlencode($row['email']) . '">Disposition</a>',
            $clr
        );
        $serial++;
    }

    $output = array(
        "sqlchat" => $sql,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    );

    // Output the data in JSON format
    echo json_encode($output);
}
function count_all_data_chat($sql) {
    global $link, $db;

    $case_result = mysqli_query($link, $sql);
    if ($case_result) {
        $total = mysqli_num_rows($case_result);
    } 
    
    return $total;
}

function twitter_data() {
    global $db, $link;

    // Initialize necessary variables
    $columns = array('i_ID', 'd_TweetDateTime', 'v_Screenname', 'v_TweeterDesc', 'ICASEID', 'iCaseStatus', 'i_ID');

    // Initialize SQL query
    $sql = "SELECT * FROM $db.tbl_tweet WHERE I_Status=1";

    
    // Get startdatetime and enddatetime from POST or set defaults
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Date range filter
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND d_TweetDateTime BETWEEN '$from' AND '$to'";
    } 
    // Screenname filter
    if (!empty($_POST['v_Screenname'])) {
        $screenname = mysqli_real_escape_string($link, $_POST['v_Screenname']);
        $res = " AND (v_Screenname = '$screenname' )";
        $sql .= " AND (v_Screenname = '$screenname')";
    }
    if (!empty($_POST['v_name'])) {
        $name = mysqli_real_escape_string($link, $_POST['v_name']);
        $res = " AND (v_name = '$name')";
        $sql .= " AND (v_name = '$name')";
    }
    if (!empty($_POST['ICASEID'])) {
        $ICASEID = mysqli_real_escape_string($link, $_POST['ICASEID']);
        $sql .= " AND (ICASEID = '$ICASEID' OR ICASEID LIKE '%$ICASEID%')";
    }
     // Default order by column and direction

     if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $limit .= " LIMIT $start, $length";
    }
    if(empty($startdatetime)||empty($enddatetime)){
    // Get the total number of records
    $totalRecordsQuery = mysqli_query($link, $sql.$limit);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);
    }else{
        $totalRecordsQuery = mysqli_query($link, $sql);
        $totalRecords = mysqli_num_rows($totalRecordsQuery);
    }
        // Apply ORDER BY after query construction
    $sql.=$limit;
    // Execute the query
    $result = mysqli_query($link, $sql);
    
    // Prepare data array
    $data = array();
    
    // Loop through each row of data
    $serial = $_POST['start'] + 1; // Adjust serial number for pagination
    while ($row = mysqli_fetch_assoc($result)) {
        $ICASEID = $row['ICASEID'];
        if ($ICASEID == '0'){
            $caseee = "";
        }else{
            $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
            $caseee = $w['ticketid'];
            $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
            $status = $rest['ticketstatus'];
        }
        $web_case_detail = base64_encode('web_case_detail');
        $idd = base64_encode($ICASEID);
        // Construct the 'Action' link using values from each row
        
        if ($caseee != ""){
                $row['Case'] = 'Case ID : <a href="helpdesk_index.php?token='.$web_case_detail.'&id='.$idd.'&mr=3">'.$caseee.'</a>';
        }else{
            $row['Case'] = '<a title="CreateComplain" class="" href="javascript:void(0)" onclick="Centerss(900,600,50,\'omnichannel_config/complainpopup_twitter.php?id=' . $row['i_ID'] . '\',\'demo_win\');"><span>Create Case</span></a>&nbsp;&nbsp;';
        }

        $dmLink = '<a style="text-decoration:none;" href="omnichannel_config/web_sent_dm.php?i_TweetID=' . $row['i_TweetID'] . '&receptent_id=' . $row['tuser_id'] . '&id=' . $row['ICASEID'] . '&messageid=' . $row['i_ID'] . '&account_sender_id=' . $acoount_id . '" class="ico-interaction2"> DM &nbsp;</a>';

        $row['Action'] = ' <img src="../../ensembler/public/images/reply.png" width="14" border="0" title="Reply" style="color: white;">&nbsp;' . $dmLink;

        $Flag = $row['Flag'];

        // Determine the background color based on the flag
        if($Flag == 1) {
            $clr = "yellow";
        } elseif($Flag == 0) {
            $clr = "red";
        } elseif($Flag == 2) {
            $clr = "green";
        } else {
            $clr = ""; // Default background color
        }
        
        
        $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
            $caseee = $w['ticketid'];
            $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
            $status = $rest['ticketstatus'];
        // Add row to data array
        $sub_array = array();
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['d_TweetDateTime'] . '</span>';
        $sub_array[] = '<span>' . $row['v_Screenname'] . '</span>';
        $sub_array[] = '<span>' . $row['v_name'] . '</span>';
        $sub_array[] = '<span>' . $row['v_TweeterDesc'] . '</span>';
        $sub_array[] = '<span>' . $row['Case'] . '</span>';
        $sub_array[] = '<span>' . $status . '</span>';
        $sub_array[] ='<span>'. $row['Action'] . '</span>';
        $sub_array[] = $clr;
        $data[] = $sub_array;
        $serial++;
    }
    
    // Prepare the response
    $output = array(
        "sql_twitter"     => $sql.$limit,
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    =>  $totalRecords, 
        "recordsFiltered" => $totalRecords, 
        "data"            => $data
    );
    
    // Encode response as JSON and send
    echo json_encode($output);
 }

 function sms_data(){
    global $db,$link;

    $column = array('i_id', 'v_mobileNo', 'v_smsString', 'd_timeStamp', 'ICASEID', 'v_FromSIM', 'v_msgkey');


    
    // Get startdatetime and enddatetime from POST or set defaults
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }
    $phone = $_POST['phone'];
    $ICASEID = $_POST['ICASEID'];
    $iallstatus = $_POST['iallstatus'];

    // ################### Change the fomats YYYY-mm-dd H:i:s #####################
    // $changestartdatetime=datetimeformat($_POST['startdatetime']);
    // $changeenddatetime=datetimeformat($_POST['enddatetime']); 
    // $changeenddatetime= date('Y-m-d',strtotime($changeenddatetime.'+ 1 days'));
    // ###########################################################################
    // $yest=date("Y-m-d", $yesterday);
    
    // $from=date('Y-m-d H:i:s',strtotime($startdatetime));
    // $to=date('Y-m-d H:i:s',strtotime($enddatetime));
    
    if (!empty($ICASEID)) {
        $ICASEID = mysqli_real_escape_string($link, $ICASEID);
        $condition .= " AND ICASEID = $ICASEID  "; // Apply the filter to SQL query
    }


    // Date range filter
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND d_timeStamp BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $condition .= " AND d_timeStamp >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND d_timeStamp <= '$to'";
    }

   
    if ($iallstatus != '') {
        if ($iallstatus == 1) {
            $condition .= " AND `ICASEID` != ''"; // Fetch records where ICASEID is not empty
        } elseif ($iallstatus == 0) {
            $condition .= " AND (`ICASEID` = '' OR `ICASEID` IS NULL)"; // Fetch records where ICASEID is empty
        }
        // No additional condition for iallstatus = 4, so it will skip this step
    }
    
    /*this code for interaction flow related fetch data
    - aarti-22-11-24*/
    if(!empty($_POST['id'])){
        $strid = "i_id=".$_POST['id'];
        $sql="SELECT * FROM $db.tbl_smsmessagesin where $strid";
    }else{                       
        $sql="SELECT * FROM $db.tbl_smsmessagesin WHERE i_status=1 $condition ";
    }
   
    // updated the code for fetching first 25 records [vastvikta][13-03-2025]

        // Default order by column and direction

        if ($_POST["length"] != -1) {
            $start = $_POST['start'];
            $length = $_POST['length'];
            $limit .= " LIMIT $start, $length";
        }
       
            $totalRecordsQuery = mysqli_query($link, $sql);
            $totalRecords = mysqli_num_rows($totalRecordsQuery);
        
            $sql .= ' ORDER BY `i_id` ASC'; // Apply limit code
            $sql.=$limit;

    // Execute the query
    $result = mysqli_query($link, $sql);

    // Prepare data array
    $data = array();

    // Loop through each row of data
    $serial = $_POST['start'] + 1;
    while ($row = mysqli_fetch_assoc($result)) {
       
        $Mid = $row['i_id'];
        $phone = $row['v_mobileNo'];

        $q=mysqli_fetch_row(mysqli_query($link,"select AccountNumber from $db.web_accounts where (phone like '%$phone%');"));
        $customerid = $q[0];
        $ICASEID = $row['ICASEID'];
        if ($ICASEID == '0'){
            $caseee = "";
        }else{
            $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
            $caseee = $w['ticketid'];
            $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
            $status = $rest['ticketstatus'];
        }

        $web_case_detail = base64_encode('web_case_detail');
        $idd = base64_encode($ICASEID);
        $new_case_manual = base64_encode('new_case_manual');

        if ($caseee != "") {
            // Case ID exists
            $row['Case'] = 'Case ID : <a href="helpdesk_index.php?token='.$web_case_detail.'&id='.$idd.'&mr=13" >'.$caseee.'</a>';
        } else {
            // No Case ID, applying your logic here
            if ($customerid != '') {
                $row['Case'] = '<a href="#" onclick="window.open(\'helpdesk_index.php?token='.$new_case_manual.'&customerid='.$customerid.'&smsid='.$Mid.'&mr=13\', \'_blank\');" >Create Case</a>';
            } else {
                $row['Case'] = '<a href="#" onclick="window.open(\'helpdesk_index.php?token='.$new_case_manual.'&smsid='.$Mid.'&mr=13\', \'_blank\');" >Create Case</a>';
            }
        }
        $row['Reply'] = '<a href="#" onclick="window.open(\'helpdesk_index.php?token='.$new_case_manual.'&smsid='.$Mid.'&mr=13\', \'_blank\');" >Create Case</a>';
        
        $reply_link = '<img src="../public/images/reply.png" width="14" border="0" title="Reply">&nbsp;';
        $reply_link .= '<a style="text-decoration: none;" href="omnichannel_config/sms_reply.php?phone='.$phone.'&smsid='.$Mid.'" target="_blank"  class="ico-interaction2"> Reply &nbsp;</a>';
        //  
        $flag = $row['Flag'];
        if($flag == 1) {
            $clr = "yellow";
        } elseif($flag == 0) {
            $clr = "red";
        } elseif($flag == 2) {
            $clr = "green";
        } else {
            $clr = ""; // Default background color
        }

        $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
        $caseee = $w['ticketid'];
        $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
        $status = $rest['ticketstatus']; 

        // code for fetching name if customer exist in the table [vastvikta][28-02-2025]
        $smsnumber =   $row['v_mobileNo'];
        $sql_name = "SELECT AccountNumber,fname FROM $db.web_accounts 
             WHERE phone LIKE '%$smsnumber%' 
             OR mobile LIKE '%$smsnumber%' 
             OR smshandle LIKE '%$smsnumber%'";
        $result_new = mysqli_query($link, $sql_name); // Run the query
        $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array

        $fname = $row_new['fname']; // Store the 'fname' value in the variable
        $id = $row_new['AccountNumber'];
        // Check if fname is empty
        if (empty($fname)) {
            $username = $smsnumber ; // Set $name to $send_from if fname is empty
        } else {
            $web_customer_detail = base64_encode('web_customer_detail');
            $ref = $web_customer_detail."&CustomerID=".base64_encode($id);
            $username = '<a style="color: #3974aa; !important" href="customer_index.php?token=' . $ref . '" target="_blank">' . $fname . '</a>';   
        }



        $id = $row['i_id'];
        // Add row to data array
        $sub_array = array();
        $sub_array[] = '<span >' .      $serial . '</span>';
        $sub_array[] = '<span>' . $username . '</span>';
        $sub_array[] = '<span>' . $row['v_smsString'] . '</span>';
        $sub_array[] = '<span >' .$row['Case'] . '</span>';
        $sub_array[] = '<span>' . $status . '</span>';
        $sub_array[] = '<span>' . $row['d_timeStamp'] . '</span>';
        $sub_array[] = '<span>' . $reply_link . '</span>';
        $sub_array[] = $clr;
        $data[] = $sub_array;
        $serial++;
    }
    // Prepare the response
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords, 
        "recordsFiltered" => $totalRecords, 
        "sql" => $sql,
        "data"            => $data 
    );

    // Encode response as JSON and send
    echo json_encode($output);
}
function facebook_data(){

    global $db,$link;

    $column = array('id','name', 'msg',  'comment', 'attachment','createdate' , 'post_id');

    $startdatetime = $_POST['startdatetime'];
    $enddatetime = $_POST['enddatetime'];
    $iallstatus = $_POST['allstatus'];

    
    $str = "";

    if(isset($iallstatus)) {
        $st = $iallstatus;
        if($st == 4) $str = "";
        if($st == 0) $str = " and (ICASEID='' || ICASEID=0)";
        if($st == 1) $str = " and ICASEID!='' ";
        if($st == 2) $str = " and i_deletestatus=0";
    } else {
        $str = " and i_deletestatus=1"; 
    }

    if(!isset($_POST['startdatetime']) && !isset($_POST['enddatetime'])) {
        $str .= " and createddate>='".date("Y-m-d 00:00:00")."' and createddate<='".date("Y-m-d H:i:s")."'";
    } /*
    else {
        $str .= " and createddate>='".date("Y-m-d 00:00:00",strtotime($_POST['startdatetime']))."' and createddate<='".date("Y-m-d H:i:s",strtotime($_POST['enddatetime']))."'";
    }
    using the above else condition doesn't fetch any data 
    */ 
    $sql = "SELECT * FROM $db.tbl_facebook WHERE id!=''  and i_deletestatus!='0' $str ";

    if (isset($_POST['order'])) {
        $sql .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
    } else {
        // Default order by column and direction
        $sql .= ' ORDER BY `id` ASC';
    }

    // Apply limit code
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }

    $limit = "";
        if (isset($_POST["length"]) && $_POST["length"] != -1) {
            $start = intval($_POST['start']);
            $length = intval($_POST['length']);
            $limit = " LIMIT $start, $length";
        }
        if(empty($startdatetime)||empty($enddatetime)){
            // Get the total number of records
            $totalRecordsQuery = mysqli_query($link, $sql.$limit);
        }else{
            $totalRecordsQuery = mysqli_query($link, $sql);
        }

        $totalRecords = mysqli_num_rows($totalRecordsQuery);

        $sql.=$limit;

    // Execute the query
    $query = mysqli_query($link, $sql);


    // Get the total number of records
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($query);

    $data = array();
    $serial = $_POST['start'] + 1; 

    while($row = mysqli_fetch_array($query)) {
        
        // Fetching data from the row
        $caseee = $row['ICASEID'];
        $name = $row['name'];
        $post = $row['msg'];
        // Incorporating the provided link into the comment variable
        $comment = '<a style="text-decoration: none;" href="omnichannel_config/web_facebook_sent.php?post_id_2=' . $row['post_id_2'] . '&id=' . $row['id'] . '" class="ico-interaction2">' . $row['comment'] . '</a>';
        
        if (!empty($row['attachment'])) {
            $attachment = '<a href="' . $row['attachment'] . '" target="_blank">Attachment</a>';
        } else {
            $attachment = '';
        }
        $date = $row['createddate'];
        $case = '<a href="javascript:void(0)" onclick="window.open(\'omnichannel_config/facebookpop.php?id=' . $row['id'] . '\',\'Facebook popup\',\'width=500,height=300\');">Create Case</a>';
        $Flag = $row['flag_read_unread'];

        // Determine the background color based on the flag
        if($Flag == 2) {
            $clr = "yellow";
        } elseif($Flag == 1) {
            $clr = "red";
        } elseif($Flag == 0) {
            $clr = "green";
        } else {
            $clr = ""; // Default background color
        }

        // Building sub-array
        $sub_array = array();
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $name . '</span>';
        $sub_array[] = '<span>' . $post . '</span>';
        $sub_array[] = '<span>' . $comment . '</span>';
        $sub_array[] = '<span>' . $attachment . '</span>';
        $sub_array[] = '<span>' . $date . '</span>';
        $sub_array[] = '<span>' . $case . '</span>';
        $sub_array[] = $clr;
        $data[] = $sub_array;
        $serial++;
    }
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => count_all_data_facebook(),
        "recordsFiltered" => $totalRecords, 
        "data"            => $data
    );

    echo json_encode($output);
}
function whatsapp_data() {
    global $db, $link,$whatsapp_path;

    // Initialize necessary variables
    $column = array('id', 'create_date', 'send_from', 'send_to', 'message', 'message_unique_id', 'ICASEID', 'status', 'id');

    // Get startdatetime and enddatetime from POST or set defaults
    $startdatetime = $_POST['startdatetime'] ?? '';
    $enddatetime = $_POST['enddatetime'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $ICASEID = $_POST['ICASEID'] ?? '';
    $send_from = $_POST['send_from'] ?? '';
    $send_name = $_POST['send_name'] ?? '';

    $condition = "1=1"; // Ensure condition is never empty

    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND `create_date` >= '$from' AND `create_date` <= '$to'";
    }
    if (!empty($send_name)) {
        $condition .= " AND `send_from` = '$send_name'";
    }
    if (!empty($phone)) {
        $condition .= " AND `send_from` = '$phone'";
    }
    if (!empty($ICASEID)) {
        $condition .= " AND `ICASEID` = '$ICASEID'";
    }
    if (!empty($send_from)) {
        $condition .= " AND `send_from` = '$send_from'";
    }

    // Ensure LIMIT is properly formatted
    $limit = "";
    if (isset($_POST["length"]) && $_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $limit = " LIMIT $start, $length";
    }

    // Fix SQL Query vastvikta 13-03-2025
    $sql = "SELECT w.*
            FROM $db.`whatsapp_in_queue` w
            INNER JOIN (
                SELECT `send_from`, MAX(`create_date`) as max_create_date
                FROM $db.`whatsapp_in_queue`
                WHERE $condition
                GROUP BY `send_from`
            ) latest ON w.`send_from` = latest.`send_from` 
            AND w.`create_date` = latest.max_create_date
            ORDER BY w.`create_date` DESC";

       
            $totalRecordsQuery = mysqli_query($link, $sql);
       

        $totalRecords = mysqli_num_rows($totalRecordsQuery);

        $sql.=$limit;
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    $data = array();
    $no = 1;

    while ($row = mysqli_fetch_assoc($query)) {

        //[vastvikta][03-12-2024] for fetching name according to the whatsapp handle 
        // Ensure the WhatsApp handle contains only the last 10 digits
        $send_from = $row['send_from'];
        $whatsapphandle = preg_replace('/\D/', '', $row['send_from']); // Remove non-numeric characters
        $whatsapphandle = substr($whatsapphandle, -10); // Keep only the last 10 digits

        $row_new = get_whatsapp_name($send_from,$whatsapphandle);
         // Fetch as an associative array

        $fname = $row_new['fname']; // Store the 'fname' value in the variable
        $id = $row_new['AccountNumber'];
        // Check if fname is empty
        if (empty($fname)) {
             // Fetch as an associative array
            $row2 = get_whatsapp_name2($send_from);
            $user_name = $row2['user_name'];

            if(empty($user_name)){
                $username = $send_from ;
            }else{ 
                $username = $user_name;
            } 
        } else {
            $web_customer_detail = base64_encode('web_customer_detail');
            $ref = $web_customer_detail."&CustomerID=".base64_encode($id);
            $username = '<a style="color: #3974aa; !important" href="customer_index.php?token=' . $ref . '" target="_blank">' . $fname . '</a>';    
        }

        $sub_array = array();
        $sub_array[] = $no;
        $sub_array[] = $row['create_date'];
        $sub_array[] = $username;
        $sub_array[] = $row['send_to'];

       
        $message = !empty($row['message']) ? $row['message'] : "N/A";
        $sub_array[] = $message;

        $ICASEID = $row['ICASEID'];
        if ($ICASEID == '0'){
            $caseee = "";
        }else{
            $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
            $caseee = $w['ticketid'];
            $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
            $status = $rest['ticketstatus'];
        }
        $web_case_detail = base64_encode('web_case_detail');
        $new_case_manual = base64_encode('new_case_manual');

        
        $attachment = $row['attachment'];
        if (!empty($attachment)) {
            $attachment_link = '<a href="'.$whatsapp_path . $attachment . '" target="_blank">attachment</a>';
            $sub_array[] = $attachment_link;
        } else {
            $sub_array[] = ''; // No attachment
        }
        $send_from =  $row['send_from'];
        $sql_send = "select AccountNumber from $db.web_accounts where (phone like '%$send_from%')";
        $q=mysqli_fetch_row(mysqli_query($link,$sql_send));
        $customerid = $q[0];
        if(!empty($customerid)){
            $custome = '&customerid='.$customerid;
        }else{ 
            $custome = '&customerid='.$send_from;
        }
        $messageid = $row['id'];
        $idd = base64_encode($ICASEID);
        if (!empty($caseee)) {
            $sub_array[] = "Case ID : <a href='helpdesk_index.php?token={$web_case_detail}&id={$idd}&mr=8'>{$caseee}</a>";
        } else {
            $case_link = "<a title='CreateComplain' class='' href='helpdesk_index.php?token={$new_case_manual}{$custome}&whatsappid={$messageid}&mr=8'><span>Create Case</span></a>";
            if ($irrelevant_status == 1) {
                $case_link .= "&nbsp;&nbsp;<span>Case is irrelevant</span>";
            }
            $sub_array[] = $case_link;
        }
        // Additional logic for ICASEID and status
       
        $status = '';
        if (!empty($ICASEID)) {
            // Fetch and set $status based on conditions
            $w = mysqli_fetch_array(mysqli_query($link, "SELECT ticketid,iCaseStatus FROM $db.web_problemdefination WHERE ticketid='$ICASEID'"));
            if ($w) {
                $rest = mysqli_fetch_array(mysqli_query($link, "SELECT ticketstatus FROM $db.web_ticketstatus WHERE id='" . $w['iCaseStatus'] . "'"));
                $status = $rest['ticketstatus'];
            }
        } else {
            // Handle other cases
            $status = '';
        }

        // Build reply link with badges
        $reply_link = '<img src="../public/images/reply.png" width="14" border="0" title="Reply">&nbsp;';
        $reply_link .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_whatsapp.php?i_WhatsAppID=' . $row['id'] . '&send_to=' . $row['send_from'] . '&id=' . $ICASEID . '&send_from=' . $row['send_to'] . '&messageid=' . $row['id'] . '&account_sender_id=' . $row['send_from'] . '" class="ico-interaction2"> Reply &nbsp;</a>';

        // Append badge if applicable
        $sqls = "SELECT COUNT(*) AS total FROM $db.`whatsapp_in_queue` WHERE flag='0' AND send_from = '" . $row['send_from'] . "'";
        $response = mysqli_query($link, $sqls) or die(mysqli_error($link));
        $rowCount = mysqli_fetch_array($response);
        if ($rowCount['total'] > 0) {
            $reply_link .= '<span class="badge badge-light">' . $rowCount['total'] . '</span>';
        } else {
            $reply_link .= '<span class="badge badge-light"></span>';
        }

        // Define $clr based on conditions
        $clr = "";
        $flag = $row['flag'];
        if ($flag == 2) {
            $clr = "green";
        } elseif ($flag == 0) {
            $clr = "red";
        } elseif ($flag == 1) {
            $clr = "yellow";
        }

        // Append data to sub_array
        $sub_array[] = $status;
        $sub_array[] = $reply_link;
        $sub_array[] = $clr;

        // Push sub_array to data array
        $data[] = $sub_array;
        $no++;
    }

    // Prepare the response
    $output = array(
        "sql"=>$sql,
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $totalRecords, // Count filtered records
        "data"            => $data
        
    );

    echo json_encode($output);
}
// updated the  condition so that it checks all the possible values against the  phone number with or without 91 [vastvikta][03-04-2025]
function get_whatsapp_name($send_from,$whatsapphandle){
    global $db,$link;
    $sql_name = "SELECT AccountNumber, fname 
                 FROM $db.web_accounts 
                 WHERE (whatsapphandle = '$whatsapphandle' OR phone = '$whatsapphandle')
                 OR (whatsapphandle = '$send_from' OR phone = '$send_from')";
    $result_new =   mysqli_query($link, $sql_name); // Run the query
    $row_new = mysqli_fetch_assoc($result_new);
    return $row_new;
}
function get_whatsapp_name2($send_from){

    global $db,$link;
    $sql = "SELECT user_name FROM $db.whatsapp_in_queue WHERE send_from = '$send_from'";
    $result_new = mysqli_query($link, $sql); // Run the query
    $row2 = mysqli_fetch_assoc($result_new);
    return $row2;
}
function count_all_data_twitter(){
    global $db,$link;
    $query = "SELECT * FROM $db.tbl_tweet";
   
    $case_result = mysqli_query($link, $query); // get status filter option
    $total = mysqli_num_rows($case_result);
    return $total;
}
function count_all_data_sms(){
    global $db,$link;
    $query = "SELECT * FROM $db.tbl_smsmessagesin";
    $case_result = mysqli_query($link, $query); // get status filter option
    $total = mysqli_num_rows($case_result);
    return $total;
}
function count_all_data_facebook(){
    global $db,$link;
    $query = "SELECT * FROM $db.tbl_facebook";
    $case_result = mysqli_query($link, $query); // get status filter option
    $total = mysqli_num_rows($case_result);
    return $total;
}
function count_all_data_whatsapp() {
    global $db, $link;
    $query = "SELECT * FROM $db.whatsapp_in_queue ";
    $case_result = mysqli_query($link, $query);
    $total = mysqli_num_rows($case_result);
    return $total;
}
// for send sms replay option 
function send_sms_reply(){
    global $db,$link;
    if(isset($_POST['reply']) && $_POST['action']=="reply"){
        $userid = $_SESSION['userid'];
        $message = $link->real_escape_string($_POST['reply']);
        $phone= $_POST['phone'];
        $name = $_POST['name'];
        $expiry = 3;
        $sql_sms="INSERT INTO $db.tbl_smsmessagesin (v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status,i_expiry) values ('$phone','$message','Sms','$name','$userid',NOW(),'1','$expiry')";
        $result_sms= $link->query($sql_sms);
        if($result_sms == true){
            echo true;
        }
    }
}
/*
This code for messenger lising display with datatable and filter option
[Aarti][12-08-2024]
*/
function Messenger_Datatable(){
    global $link,$db;
    $startdatetime = $_POST['startdatetime'] ?? '';
    $enddatetime = $_POST['enddatetime'] ?? '';
    $iallstatus = $_POST['iallstatus'] ?? '';
    $ICASEID = $_POST['ICASEID'] ?? '';
    $send_from = $_POST['send_from'] ?? '';
    $send_name = $_POST['send_name'] ?? '';

    $changestartdatetime = datetimeformat($startdatetime);
    $changeenddatetime = datetimeformat($enddatetime);

    $iallstatus1 = '';
    if($iallstatus ==  '0'){
            $iallstatus1 = " and (ICASEID='' OR ICASEID IS NULL)";
    }else if($iallstatus ==  '1'){
        $iallstatus1 = " and ICASEID > 0";
    }
    if($ICASEID){ 
        $iallstatus1 .=" AND `ICASEID`='$ICASEID'"; 
    }
    //[vastvikta nishad][02-12-24] Added the code for send name
    if($send_from){ 
    $iallstatus1 .=" AND `send_from`='$send_from'"; 
    } 
    if($send_name){ 
        $iallstatus1 .=" AND `send_from`= $send_name"; 
    } 
    if($startdatetime!='' && $enddatetime!=''){ 
        $condition .="where `create_date` >='$changestartdatetime' and `create_date` <='$changeenddatetime'  "; 
    }

    $sql = "SELECT w.*
      FROM $db.`messenger_in_queue` w
      INNER JOIN (
          SELECT `send_from`, MAX(`create_date`) as max_create_date
          FROM $db.`messenger_in_queue`
           $condition $iallstatus1
          GROUP BY `send_from`
      ) latest ON w.`send_from` = latest.`send_from` AND w.`create_date` = latest.max_create_date
      ORDER BY w.`create_date` DESC ";
       $limit = "";
       if (isset($_POST["length"]) && $_POST["length"] != -1) {
           $start = intval($_POST['start']);
           $length = intval($_POST['length']);
           $limit = " LIMIT $start, $length";
       }
   
        $totalRecordsQuery = mysqli_query($link, $sql);
    

    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    $sql.=$limit;

    $query = mysqli_query($link, $sql);
    $data = array();
    $serial = $_POST['start'] + 1;
    while ($row = mysqli_fetch_array($query)) {
        
        $data[] = processRowDataMessenger($row, $serial++);
    }

    $output = array(
        "sql"=> $sql,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    );
    echo json_encode($output);
}
//For read,unread flag code set
function getFlagColor($flag) {
    switch ($flag) {
        case 1: return 'yellow';
        case 0: return 'red';
        case 2: return 'green';
        default: return '';
    }
}
// For row data bindling proper flow
function processRowDataMessenger($row, $serial) {
    global $link,$db,$messenger_path;
    $id = $row['id'];
    $ICASEID = $row['ICASEID'];
    $send_from = $row['send_from'];
    $send_to = $row['send_to'];
    $messageid= $row['id'];
    $flag = $row['flag'];
    $attachment = $row['attachment'];
    $create_date = $row['create_date'];

    $customer_id = $row['customer_id'];
    $fullname = '';
    if(!empty($customer_id)){
        $w = mysqli_fetch_array(mysqli_query($link, "select fname,lname from $db.web_accounts where AccountNumber='$customer_id'"));
        $fname = $w['fname'];
        $lname = $w['lname'];
        $fullname = $fname.''.$lname;
    }else{
        $fullname = $send_from;
    }

    $clr = getFlagColor($flag);

    if ($ICASEID == '0'){
        $caseee = "";
    }else{
        $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
        $caseee = $w['ticketid'];
        $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
        $status = $rest['ticketstatus'];
    }

    // [vastvikta nishad][03-12-2024] code for fetching name on  the basis of the  send_from id in the messenger_in_queue tab
    $sql_name = "SELECT AccountNumber,fname FROM $db.web_accounts WHERE messengerhandle = '$send_from'";
    $result_new = mysqli_query($link, $sql_name); // Run the query
    $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array

    $fname = $row_new['fname']; // Store the 'fname' value in the variable
    $id = $row_new['AccountNumber'];
    // Check if fname is empty
    if (empty($fname)) {
        $username = $send_from ; // Set $name to $send_from if fname is empty
    } else {
        $web_customer_detail = base64_encode('web_customer_detail');
        $ref = $web_customer_detail."&CustomerID=".base64_encode($id);
        $username = '<a style="color: #3974aa; !important" href="customer_index.php?token=' . $ref . '" target="_blank">' . $fname . '</a>';   
    }


    $web_case_detail = base64_encode('web_case_detail');

    $ICASEID = base64_encode($ICASEID);
    $new_case_manual = base64_encode('new_case_manual');

    // for fetch attachment path [aarti][12-06-2024] 
    $sql_cdr= "SELECT * from $db.tbl_messenger_connection where status=1 and debug=1 ";
    $query=mysqli_query($link,$sql_cdr);
    $config = mysqli_fetch_array($query);
    $attachment_path = $config['attachment_path'];

    if(!empty($attachment)){
        $attachment_link = '<a href="../../../'. $attachment.'" target="_blank" >attachment</a>';
    }
    $case_page = '';
    if ($caseee != ""){
        $case_page = 'Case ID : <a href="helpdesk_index.php?token='.$web_case_detail.'&id='.$ICASEID.'&mr=14">'.$caseee.'</a>';
    }else{
        $case_page = '<a title="CreateComplain" class="" href="helpdesk_index.php?token='.$new_case_manual.'&messengerid=' . $messageid . '&mr=14"><span">Create Case</span></a>&nbsp;&nbsp;';
    }

    if(!empty($row['message'])){ 
        $message = $row['message']; 
    }else{ 
        $message = "N/A"; 
    }

    if (!empty($ICASEID)){
      $status_list =  $status;
    }

    // Build reply link with badges
    $reply_link = '<img src="../public/images/reply.png" width="14" border="0" title="Reply">&nbsp;';
    $reply_link .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_messanger.php?ID=' . $row['id'] . '&send_to=' . $row['send_from'] . '&id=' . $ICASEID . '&send_from=' . $row['send_to'] . '&messageid=' . $row['id'] . '&account_sender_id=' . $row['send_from'] . '" class="ico-interaction2"> Reply &nbsp;</a>';

    //for fetching new message notification display
    $sqls ="SELECT count(*) as total FROM $db.`messenger_in_queue` WHERE  flag='0' and send_from = '$send_from'";
    $response=mysqli_query($link,$sqls) or die(mysqli_error($link));
    $rowCount=mysqli_fetch_array($response);

    if ($rowCount['total'] > 0) {
        $reply_link .= '<span class="badge badge-light">' . $rowCount['total'] . '</span>';
    } else {
        $reply_link .= '<span class="badge badge-light"></span>';
    }

    return array(
        '<span>' . $serial . '</span>',
        '<span>' . date('d-m-Y H:i', strtotime($create_date)) . '</span>',
        '<span>' . $username . '</span>',
        '<span>' . $send_to . '</span>',
        '<span>' . $message . '</span>',
        $attachment_link,
        $case_page,
        '<span>' . $status_list . '</span>',
        $reply_link,
        $clr
    );
}
//fucntion for whatsapp queue report 
function Whatsapp_report() {
    global $db, $link;

    // Define the columns for the table, corresponding to the database fields
    $column = array('id', 'send_from', 'send_to', 'message', 'status', 'status_response', 'create_date');

// added group by clause [vastvikta][15-02-2025]
    $group_by = isset($_POST['group_by']) ? trim($_POST['group_by']) : '';

    // Get custom startdatetime and enddatetime from POST if provided
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Get the 'send_to' value from POST or set it to an empty string by default
    $send_to = isset($_POST['send_to']) ? $_POST['send_to'] : '';
    
    // Initialize status as null (for filtering purposes)
    $status = null;

    // Check if 'status' is set in POST and not an empty string
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $status = intval($_POST['status']); // Convert the status to an integer for comparison
    }

    // Base SQL query to fetch records from whatsapp_out_queue table
    $sql = "SELECT id, send_to, send_from, status_response,created_by, message, `status`, attachment ,create_date FROM $db.whatsapp_out_queue WHERE 1=1";

    // Apply date range filtering if both start and end datetimes are provided
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND create_date BETWEEN '$from' AND '$to'";
    }

    // Apply filter by 'send_to' if provided
    if (!empty($send_to)) {
        $sql .= " AND send_to = '$send_to'";
    }

    // Apply filter by status if it is not null
    if ($status !== null) { 
        $sql .= " AND status = $status"; 
    }
    if (!empty($group_by)) { 
        $sql .= " GROUP BY `$group_by`"; 
    }
    

  
        $sql .= ' ORDER BY `create_date` desc';
  
    

    // Execute the query to get the total records for pagination
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    if (isset($_POST["length"]) && $_POST["length"] != -1) {
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = intval($_POST['length']);
        $sql .= " LIMIT $start, $length";
    }
    
    // Execute the query to fetch the actual records
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    // Initialize an empty array to store data for the response
    $data = array();
    $serial = $_POST['start'] + 1;  // Start the serial number based on pagination

    // Loop through each row in the query result and prepare the data
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();

        // Determine the status text based on the 'status' field in the row
        switch ($row['status']) {
            case 0:
                $status_text = 'In Queue';
                break;
            case 1:
                $status_text = 'Delivered';
                break;
            case 2:
                $status_text = 'Pending';
                break;
            case 3:
                $status_text = 'Delivered';
                break;
            default:
                $status_text = 'Unknown'; // Fallback for any unhandled status
        }
        $checkbox = '<input type="checkbox" class="row-checkbox" data-id="' . $row['id'] . '" />';
        // Determine reschedule status based on reschedule_flag
            $isRescheduled = ($row['schedule_flag'] == 1) 
        ? '<span class="badge badge-success">Rescheduled</span>' 
        : '<span class="badge badge-warning">Not Rescheduled</span>';
        $agent_id = $row['created_by'];
        $agent_name = get_agent_name($agent_id);
        if(empty($row['schedule_time'])){
            $schedule_time = '0000-00-00 00:00:00';
        }else{
            $schedule_time=$row['schedule_time'];
        }
        // updated attachment logic [vastvikta][03-05-2025]
        if(empty($row['message'])){
            $message = '<a href="../../'.$row['attachment'].'" target="_blank">Attachment</a>';
        }
        else{
            $message = $row['message'];
        }
        // Populate the 'sub_array' with formatted data for each row
        $sub_array[] = '<span>' . $checkbox . '</span>';
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['send_from'] . '</span>';
        $sub_array[] = '<span>' . $row['send_to'] . '</span>';
        $sub_array[] = '<span>' . $row['create_date'] . '</span>';
        $sub_array[] = '<span>' . $message . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $row['status_response'] . '</span>';
        $sub_array[] = '<span>' . $agent_name . '</span>';
        $sub_array[] = '<span>' . $schedule_time . '</span>';
        $sub_array[] = $isRescheduled; // Add reschedule status

        // Add the row's data to the 'data' array
        $data[] = $sub_array;
        $serial++;  // Increment the serial number for each row
    }

    // Prepare the response array to return to the DataTable
    $output = array(
        "sql"=> $sql,
        "draw"            => intval($_POST["draw"]),           
        "recordsTotal"    => $totalRecords, 
        "recordsFiltered" => $totalRecords,                    
        "data"            => $data
        
    );

    // Return the output as a JSON response
    echo json_encode($output);
}
function get_agent_name($agent_id){
    global $link, $db; // Ensure $db is included for the table reference

    $sql = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID = ?";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $agent_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $agent_name);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $agent_name;
    } else {
        return null; // Return null if the query fails
    }
}

// Function to count all records in the whatsapp_out_queue table (for total records calculation)
function count_all_data_whatsapp_report() {
    global $db, $link;
    
    // Simple query to get the total number of records
    $query = "SELECT * FROM $db.whatsapp_out_queue ";
    $case_result = mysqli_query($link, $query);
    
    // Return the total record count
    $total = mysqli_num_rows($case_result);
    return $total;
}
//function for email  queue  report
function email_queue_report(){
    global $db, $link;

    // Define the columns to be used in the report
    $column = array('EMAILID', 'v_toemail', 'v_fromemail',  'd_email_date', 'I_Status', 'v_LastError');

    // Get startdatetime and enddatetime from POST if available, otherwise keep defaults
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Initialize other filter variables from POST data
    $I_Status = isset($_POST['I_Status']) ? $_POST['I_Status'] : '';
    $to_email = isset($_POST['to_email']) ? $_POST['to_email'] : '';
    $from_email = isset($_POST['from_email']) ? $_POST['from_email'] : '';
    $status = null; // Initialize status as null

    // Check if 'status' is set in POST and not an empty string
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $status = intval($_POST['status']); // Convert status to integer
    }

    // Base SQL query to fetch email records
    $sql = "SELECT v_toemail, v_fromemail, d_email_date, I_Status, v_LastError,scheduling_date,schedule_flag,reschedule_id,EMAIL_ID
            FROM $db.web_email_information_out 
            WHERE email_type='OUT' AND v_toemail!='' ";

    // Date range filtering
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND d_email_date BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $sql .= " AND d_email_date >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND d_email_date <= '$to'";
    }

    // Additional filters based on status and email fields
    if ($I_Status !== "" && $I_Status != '5') {
        $sql .= " AND I_Status = '$I_Status'";
    }

    if($I_Status == '5'){
        // this code for expire,resuchdule,all sms
        $sql .= " AND `reschedule_id` != ''";
    }

    if ($to_email !== "") {
        $sql .= " AND v_toemail = '$to_email'";
    }
    if ($from_email !== "") {
        $sql .= " AND v_fromemail = '$from_email'";
    }

    // Ordering the results
    if (isset($_POST['order'])) {
        $sql .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
    } else {
        // Default order by email date and email ID in descending order
        $sql .= ' ORDER BY d_email_date DESC, EMAIL_ID DESC';
    }

    // Get the total number of records matching the query
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    // Apply pagination (limit) based on the 'start' and 'length' POST parameters
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }

    // Execute the query to get the data
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    // Prepare the data for the response
    $data = array();
    $serial = $_POST['start'] + 1; // Start serial number for pagination

    // Fetch each row of the query result
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();
        
        // Translate status codes into human-readable text
        switch ($row['I_Status']) {
            case 1:
                $status_text = 'In Queue';
                break;
            case 2:
                $status_text = 'Submitted';
                break;
            case 3:
                $status_text = 'Not Delivered';
                break;
            default:
                $status_text = 'Expire'; // Fallback for any unhandled status
        }

        // Set the remark for errors, defaulting to 'N/A' if no error
        $remark = empty($row['v_LastError']) ? 'N/A' : $row['v_LastError'];

        $checkbox = '<input type="checkbox" class="row-checkbox" data-id="' . $row['EMAIL_ID'] . '" />';
        // Determine reschedule status based on reschedule_flag
            $isRescheduled = ($row['schedule_flag'] == 1) 
        ? '<span class="badge badge-success">Rescheduled</span>' 
        : '<span class="badge badge-warning">Not Rescheduled</span>';

        // Add data to the sub-array for each column
        $sub_array[] = $checkbox;
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['v_toemail'] . '</span>';
        $sub_array[] = '<span>' . $row['v_fromemail'] . '</span>';
        $sub_array[] = '<span>' . $row['d_email_date'] . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $remark . '</span>';
        $sub_array[] = '<span>' . $row['scheduling_date'] . '</span>';
        $sub_array[] = $isRescheduled; // Add reschedule status
        
        // Add the row data to the final data array
        $data[] = $sub_array;
        $serial++; // Increment serial number
    }

    // Prepare the final output as a JSON response
    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    );

    // Send the output as JSON
    echo json_encode($output);
}

// Helper function to count all email records
function count_all_data_email_report(){
    global $db, $link;
    $query = "SELECT * FROM $db.web_email_information_out";
    $case_result = mysqli_query($link, $query);
    $total = mysqli_num_rows($case_result);
    return $total;
}
//function for  facebook messenger report 
function messenger_report(){
    global $db, $link;

    // Define columns to be used in the report
    $column = array('id', 'send_from', 'send_to', 'message', 'status', 'status_response', 'create_date');


    // Get startdatetime and enddatetime from POST data if provided, else use default
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Get filter values for 'send_to' and 'status' from POST
    $send_to = isset($_POST['send_to']) ? $_POST['send_to'] : '';
    $status = null; // Initialize status as null

    // Check if 'status' is set in POST and convert it to integer if not empty
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $status = intval($_POST['status']);
    }

    // Base SQL query for fetching messenger report data
    $sql = "SELECT id, send_to, send_from, status_response, message, status,attachment, create_date FROM $db.messenger_out_queue WHERE 1=1";

    // Add date range filtering to SQL if dates are provided
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND create_date BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $sql .= " AND create_date >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND create_date <= '$to'";
    }

    // Add filtering by 'send_to' if provided
    if (!empty($send_to)) {
        $sql .= " AND send_to = '$send_to'";
    }

    // Add filtering by 'status' if it's not null
    if ($status !== null) {
        $sql .= " AND status = $status"; 
    }

    // Handle sorting if 'order' is provided in POST
    if (isset($_POST['order'])) {
        $sql .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
    } else {
        // Default order by 'id' in ascending order
        $sql .= 'ORDER BY `create_date` desc';
    }

    // Get the total number of records (before pagination)
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    // Apply limit based on the requested pagination length and starting index
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }

    // Execute the query to fetch the filtered data
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    $data = array();  // Array to store the report data
    $serial = $_POST['start'] + 1; // Serial number for the report entries

    // Loop through the query result and format the data
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();
        
        // Determine the status text based on the 'status' field
         // Determine the status text based on the 'status' field
        switch ($row['status']) {
           case 0:
                $status_text = 'In Queue';
                break;
            case 1:
                $status_text = 'Submitted';
                break;
            case 2:
                $status_text = 'Not Delivered';
                break;
            case 3:
                $status_text = 'Delivered';
                break;
            default:
                $status_text = 'Expire'; // Fallback for any unhandled status
        }

        // Check if 'status_response' is empty, if so set it to 'N/A'
        if (empty($row['status_response'])) {
            $status_response = 'N/A';
        } else {
            $status_response = $row['status_response'];
        }

         // updated attachment logic [vastvikta][03-05-2025]
         if(empty($row['message'])){
            $message = '<a href="../../'.$row['attachment'].'" target="_blank">Attachment</a>';
        }
        else{
            $message = $row['message'];
        }

        // Prepare data for each row to be displayed in the report
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['send_from'] . '</span>';
        $sub_array[] = '<span>' . $row['send_to'] . '</span>';
        $sub_array[] = '<span>' . $message . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $status_response . '</span>';
        $sub_array[] = '<span>' . $row['create_date'] . '</span>';

        // Add this row to the data array
        $data[] = $sub_array;
        $serial++;
    }

    // Prepare the final output array for DataTables
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $totalRecords, 
        "data"            => $data
    );

    // Return the output as JSON
    echo json_encode($output);
}

// Helper function to count total number of records in the messenger report table
function count_all_data_messenger_report(){
    global $db, $link;
    $query = "SELECT * FROM $db.messenger_out_queue ";
    $case_result = mysqli_query($link, $query);
    $total = mysqli_num_rows($case_result);
    return $total;
}

//function for twitter queue report 
function twitter_report(){
    global $db, $link;
    
    // Define columns for sorting and display
    $column = array('id', 'recipient_id', 'sender_id', 'message_data', 'sent_flag', 'error_message', 'created_date');
    
    // Get startdatetime and enddatetime from POST request, or keep defaults
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Get recipient_id and status from POST request
    $recipient_id = isset($_POST['recipient_id']) ? $_POST['recipient_id'] : '';
    $status = isset($_POST['status']) && $_POST['status'] !== '' ? intval($_POST['status']) : null;

    // Base SQL query to fetch Twitter direct message data
    $sql = "SELECT * FROM $db.web_twitter_directmsg WHERE msg_flag = 'OUT'"; // Only outgoing messages

    // Add recipient_id filter if provided
    if (!empty($recipient_id)) {
        $sql .= " AND recipient_id = '$recipient_id'";
    }

    // Apply date range filtering if start and/or end datetime is provided
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND created_date BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $sql .= " AND created_date >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND created_date <= '$to'";
    }

    // Filter by sent status if provided
    if ($status !== null) { 
        $sql .= " AND sent_flag = '$status'"; 
    }

    // Order results based on column and direction from POST data, or default by ID
    if (isset($_POST['order'])) {
        $sql .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
    } else {
        $sql .=  'ORDER BY `create_date` desc'; // Default order
    }

    // Fetch total number of records
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    // Apply limit for pagination
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }

    // Execute the query to get the required data
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    $data = array();
    $serial = $_POST['start'] + 1; // Start serial number from the current page's starting index

    // Loop through each row from the query result and format the data
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();
        
        // Determine the status text based on sent_flag value
        switch ($row['sent_flag']) {
            case 0:
                $status_text = 'In Queue';
                break;
            case 1:
                $status_text = 'Delivered';
                break;
            case 2:
                $status_text = 'Pending';
                break;
            default:
                $status_text = 'Unknown'; // Fallback for unhandled statuses
        }

        // If error_message is empty, set status response to 'N/A'
        if(empty($row['error_message'])){
            $status_response = 'N/A';
        }
        else{
            $status_response = $row['error_message'];
        }

        // Add formatted data to the sub_array for the row
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['recipient_id'] . '</span>';
        $sub_array[] = '<span>' . $row['sender_id'] . '</span>';
        $sub_array[] = '<span>' . $row['message_data'] . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $status_response . '</span>';
        $sub_array[] = '<span>' . $row['created_date'] . '</span>';
        
        // Add the row data to the final output
        $data[] = $sub_array;
        $serial++; // Increment the serial number for the next row
    }

    // Prepare the output data for DataTables
    $output = array(
        "sql"=> $sql,
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords, // Get the total number of records
        "recordsFiltered" => $totalRecords,
        "data"            => $data
    );

    // Output the data in JSON format
    echo json_encode($output);
}

// Helper function to count all data in the Twitter report table
function count_all_data_twitter_report(){
    global $db, $link;
    $query = "SELECT * FROM $db.web_twitter_directmsg WHERE msg_flag = 'OUT'"; // Only outgoing messages
    $case_result = mysqli_query($link, $query);
    $total = mysqli_num_rows($case_result); // Count total rows
    return $total; // Return the total count
}
//function for  SMS queue  report
function SMS_report() {
    //changed the table name from tbl_smsmessages to sms_out_queue [vastvikta][05-12-2024]
    global $db, $link; // Ensure global variables are accessible
    $column = array('id','send_to', 'send_from', 'message',  'status', 'status_response', 'create_date');
    
    // Set default date values
    $startdatetime = date("Y-m-01 00:00:00");
    $enddatetime = date("Y-m-d 23:59:59");

    // Get datetime values from POST
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }
    
    // Validate date format
    $from = date('Y-m-d H:i:s', strtotime($startdatetime));
    $to = date('Y-m-d H:i:s', strtotime($enddatetime));
    if (!$from || !$to) {
        echo json_encode(['error' => 'Invalid date format']);
        return;
    }

    // Initialize status if set in POST
    $status = null;
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $status = intval($_POST['status']);
    }
    $v_mobileNo = isset($_POST['v_mobileNo']) ? $_POST['v_mobileNo'] : '';
    

    // Base SQL without SELECT * and LIMIT for counting total records
    $baseSQL = "FROM $db.`sms_out_queue` WHERE create_date BETWEEN '$from' AND '$to'";
    
    if ($status !== null && $status != '5') {
        $baseSQL .= " AND `status` = '$status'";
    }
    if($status == '5'){
        // this code for expire,resuchdule,all sms
        $baseSQL .= " AND `reschedule_id` != ''";
    }

    if (!empty($v_mobileNo)) {
        $baseSQL.= " AND send_to = '$v_mobileNo'";
    }
    
    
    // Count query
    $countSQL = "SELECT COUNT(*) as totalCount $baseSQL";
    $totalRecordsQuery = mysqli_query($link, $countSQL);
    if (!$totalRecordsQuery) {
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }
    $totalRecordsRow = mysqli_fetch_assoc($totalRecordsQuery);
    $totalRecords = $totalRecordsRow['totalCount'];

    // Main query to fetch data with pagination
    $dataSQL = "SELECT * $baseSQL";

    // Handle ordering
    if (isset($_POST['order'])) {
        $dataSQL .= ' ORDER BY ' . $column[$_POST['order'][0]['column']] . ' ' . $_POST['order'][0]['dir'];
    } else {
        $dataSQL .= ' ORDER BY `create_date` desc';
    }

    // Apply limit for pagination
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $dataSQL .= " LIMIT $start, $length";
    }

    // Execute main query
    $query = mysqli_query($link, $dataSQL);
    if (!$query) {
        echo json_encode(['error' => mysqli_error($link)]);
        return;
    }

    // Process query results
    $data = array();
    $serial = $_POST['start'] + 1; 
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();

        switch ($row['status']) {
            case 0:
                $status_text = 'In Queue';
                break;
            case 1:
                $status_text = 'Submitted';
                break;
            case 2:
                $status_text = 'Delivered';
                break;
            case 3:
                $status_text = 'Not Delivered';
                break;
            default:
                $status_text = 'Expire'; // Fallback for any unhandled status
        }
        $id = $row['id'];
        $checkbox = '<input type="checkbox" class="row-checkbox" data-id="' . $row['id'] . '" />';
        // Determine reschedule status based on reschedule_flag
            $isRescheduled = ($row['reschedule_flag'] == 1) 
        ? '<span class="badge badge-success">Rescheduled</span>' 
        : '<span class="badge badge-warning">Not Rescheduled</span>';


        $status_response = empty($row['status_response']) ? 'N/A' : $row['status_response'];
        $sub_array[] = $checkbox;
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['send_to'] . '</span>';
        $sub_array[] = '<span>' . $row['send_from'] . '</span>';
        $sub_array[] = '<span>' . $row['message'] . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $status_response . '</span>';
        $sub_array[] = '<span>' . $row['create_date'] . '</span>';
        $sub_array[] = '<span>' . $row['rescheduling_date'] . '</span>';
        $sub_array[] = $isRescheduled; // Add reschedule status
        $data[] = $sub_array;
        $serial++;
    }
    // Prepare the output
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data"            => $data,
        "sql"=>  $dataSQL
    );
    echo json_encode($output);
}
/*
This code for Instagram_Datatable lising display with datatable and filter option
[Aarti][19-11-2024]
*/
function Instagram_Datatable(){
    global $link,$db;
    $startdatetime = $_POST['startdatetime'] ?? '';
    $enddatetime = $_POST['enddatetime'] ?? '';
    $iallstatus = $_POST['iallstatus'] ?? '';
    $ICASEID = $_POST['ICASEID'] ?? '';
    $send_from = $_POST['send_from'] ?? '';

    $changestartdatetime = datetimeformat($startdatetime);
    $changeenddatetime = datetimeformat($enddatetime);

    $iallstatus1 = '';
    if($iallstatus ==  '0'){
            $iallstatus1 = " and (ICASEID='' OR ICASEID IS NULL)";
    }else if($iallstatus ==  '1'){
        $iallstatus1 = " and ICASEID > 0";
    }
    if($ICASEID){ 
        $iallstatus1 .=" AND `ICASEID`='$ICASEID'"; 
    }
    if($send_from){ 
        $iallstatus1 .=" AND `send_from`='$send_from'"; 
    } 

    //[vastvikta nishad][02-12-24] Added the code for send name
    $send_name = $_POST['send_name'] ?? '';
    if($send_name){ 
        $iallstatus1 .=" AND `send_from`= $send_name"; 
    } 
    if($startdatetime!='' && $enddatetime!=''){ 
        $condition .="WHERE `create_date` >='$changestartdatetime' and `create_date` <='$changeenddatetime'  "; 
    }

    

    $sql = "SELECT w.*
      FROM $db.`instagram_in_queue` w
      INNER JOIN (
          SELECT `send_from`, MAX(`create_date`) as max_create_date
          FROM $db.`instagram_in_queue`
           $condition $iallstatus1
          GROUP BY `send_from`
      ) latest ON w.`send_from` = latest.`send_from` AND w.`create_date` = latest.max_create_date
      ORDER BY w.`create_date` DESC ";

    $limit = "";
    if (isset($_POST["length"]) && $_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $limit = " LIMIT $start, $length";
    }

    
            $totalRecordsQuery = mysqli_query($link, $sql);
        
    
        $totalRecords = mysqli_num_rows($totalRecordsQuery);
    
        $sql.=$limit;
    

    $query = mysqli_query($link, $sql);
    $data = array();
    $serial = $_POST['start'] + 1;
    while ($row = mysqli_fetch_array($query)) {

        $data[] = processRowDataInstagram($row, $serial++);
    }

    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data,
        "sql" =>$sql
    );
    echo json_encode($output);
}
// For row data bindling proper flow
function processRowDataInstagram($row, $serial) {
    global $link,$db,$messenger_path;

    
    $id = $row['id'];
    $ICASEID = $row['ICASEID'];
    $send_from = $row['send_from'];
    $send_to = $row['send_to'];
    $messageid= $row['id'];
    $flag = $row['flag'];
    $attachment = $row['attachment'];
    $create_date = $row['create_date'];

    $customer_id = $row['customer_id'];
    $fullname = '';
    if(!empty($customer_id)){
        $w = mysqli_fetch_array(mysqli_query($link, "select fname,lname from $db.web_accounts where AccountNumber='$customer_id'"));
        $fname = $w['fname'];
        $lname = $w['lname'];
        $fullname = $fname.''.$lname;
    }else{
        $fullname = $send_from;
    }

    $clr = getFlagColor($flag);

    if ($ICASEID == '0'){
        $caseee = "";
    }else{
        $w = mysqli_fetch_array(mysqli_query($link, "select ticketid,iCaseStatus from $db.web_problemdefination where ticketid='$ICASEID'"));
        $caseee = $w['ticketid'];
        $rest = mysqli_fetch_array(mysqli_query($link, "select ticketstatus from $db.web_ticketstatus where id='" . $w['iCaseStatus'] . "' ; "));
        $status = $rest['ticketstatus'];
    }

    $web_case_detail = base64_encode('web_case_detail');

    $ICASEID = base64_encode($ICASEID);
    $new_case_manual = base64_encode('new_case_manual');

    // [vastvikta nishad][03-12-2024]
    $instagramhandle =   $send_from;

    $result_new = get_instausername($instagramhandle); // Run the query
    $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array

    $fname = $row_new['fname']; // Store the 'fname' value in the variable
    $AccountNumber = $row_new['AccountNumber'];
    // Check if fname is empty
    if (empty($fname)) {
        $username = $instagramhandle ; // Set $name to $send_from if fname is empty
    } else {
        $web_customer_detail = base64_encode('web_customer_detail');
        $ref = $web_customer_detail."&CustomerID=".base64_encode($AccountNumber);
        $username = '<a style="color: #3974aa; !important" href="customer_index.php?token=' . $ref . '" target="_blank">' . $fname . '</a>';
    }




    // for fetch attachment path [aarti][12-06-2024] 
    $sql_cdr= "SELECT * from $db.tbl_instagram_connection where status=1 and debug=1 ";
    $query=mysqli_query($link,$sql_cdr);
    $config = mysqli_fetch_array($query);
    $attachment_path = $config['attachment_path'];

    if(!empty($attachment)){
        $attachment_link = '<a href="../../../'.$attachment.'" target="_blank" >attachment</a>';
    }
    $case_page = '';
    if ($caseee != ""){
        $case_page = 'Case ID : <a href="helpdesk_index.php?token='.$web_case_detail.'&id='.$ICASEID.'&mr=15">'.$caseee.'</a>';
    }else{
        $case_page = '<a title="CreateComplain" class="" href="helpdesk_index.php?token='.$new_case_manual.'&instagramid=' . $messageid . '&mr=15"><span >Create Case</span></a>&nbsp;&nbsp;';
    }

    if(!empty($row['message'])){ 
        $message = $row['message']; 
    }else{ 
        $message = "N/A"; 
    }

    if (!empty($ICASEID)){
      $status_list =  $status;
    }

    // Build reply link with badges
    $reply_link = '<img src="../public/images/reply.png" width="14" border="0" title="Reply">&nbsp;';
    $reply_link .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_instagram.php?ID=' . $row['id'] . '&send_to=' . $row['send_from'] . '&id=' . $ICASEID . '&send_from=' . $row['send_to'] . '&messageid=' . $row['id'] . '&account_sender_id=' . $row['send_from'] . '" class="ico-interaction2"> Reply &nbsp;</a>';

    //for fetching new message notification display
    $sqls ="SELECT count(*) as total FROM $db.`instagram_in_queue` WHERE  flag='0' and send_from = '$send_from'";
    $response=mysqli_query($link,$sqls) or die(mysqli_error($link));
    $rowCount=mysqli_fetch_array($response);

    if ($rowCount['total'] > 0) {
        $reply_link .= '<span class="badge badge-light">' . $rowCount['total'] . '</span>';
    } else {
        $reply_link .= '<span class="badge badge-light"></span>';
    }

    return array(
        '<span>' . $serial . '</span>',
        '<span>' . date('d-m-Y H:i', strtotime($create_date)) . '</span>',
        '<span>' . $username . '</span>',
        '<span>' . $send_to . '</span>',
        '<span>' . $message . '</span>',
        $attachment_link,
        $case_page,
        '<span>' . $status_list . '</span>',
        $reply_link,
        $clr
    );
}
function get_instausername($instagramhandle){
    global $db,$link;
    $sql_name = "SELECT AccountNumber,fname FROM $db.web_accounts WHERE instagramhandle = '$instagramhandle'";
    $result_new = mysqli_query($link, $sql_name); // Run the query
   return $result_new;
}
//function for  instagram report 
function insta_report(){
    global $db, $link;

    // Define columns to be used in the report
    $column = array('id', 'send_from', 'send_to', 'message', 'status', 'status_response', 'create_date');

    // Get startdatetime and enddatetime from POST data if provided, else use default
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    // Get filter values for 'send_to' and 'status' from POST
    $send_to = isset($_POST['send_to']) ? $_POST['send_to'] : '';
    $status = null; // Initialize status as null

    // Check if 'status' is set in POST and convert it to integer if not empty
    if (isset($_POST['status']) && $_POST['status'] !== '') {
        $status = intval($_POST['status']);
    }

    // Base SQL query for fetching messenger report data
    $sql = "SELECT id, send_to, send_from, status_response, message, status, attachment, create_date FROM $db.instagram_out_queue WHERE 1=1";

    // Add date range filtering to SQL if dates are provided
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND create_date BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $sql .= " AND create_date >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND create_date <= '$to'";
    }

    // Add filtering by 'send_to' if provided
    if (!empty($send_to)) {
        $sql .= " AND send_to = '$send_to'";
    }

    // Add filtering by 'status' if it's not null
    if ($status !== null) {
        $sql .= " AND status = $status"; 
    }

    // Handle sorting if 'order' is provided in POST
    if (isset($_POST['order'])) {
        $sql .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
    } else {
        // Default order by 'id' in ascending order
        $sql .= '  ORDER BY `create_date` desc';
    }

    // Get the total number of records (before pagination)
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    // Apply limit based on the requested pagination length and starting index
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }

    // Execute the query to fetch the filtered data
    $query = mysqli_query($link, $sql) or die(mysqli_error($link));

    $data = array();  // Array to store the report data
    $serial = $_POST['start'] + 1; // Serial number for the report entries

    // Loop through the query result and format the data
    while ($row = mysqli_fetch_array($query)) {
        $sub_array = array();
        
        // Determine the status text based on the 'status' field
        switch ($row['status']) {
           case 0:
                $status_text = 'In Queue';
                break;
            case 1:
                $status_text = 'Submitted';
                break;
            case 2:
                $status_text = 'Not Delivered';
                break;
            case 3:
                $status_text = 'Delivered';
                break;
            default:
                $status_text = 'Expire'; // Fallback for any unhandled status
        }

        // Check if 'status_response' is empty, if so set it to 'N/A'
        if (empty($row['status_response'])) {
            $status_response = 'N/A';
        } else {
            $status_response = $row['status_response'];
        }

        if (empty($row['message'])){
            // updated link to attchment [vastvikta][03-05-2025]
            $message =  '<a title="Attachments" class="" href="../../'.$row['attachment'].'"><span>Attachment</span></a>&nbsp;&nbsp;';
  
        }else{
            $message = $row['message'];
        }
        // Prepare data for each row to be displayed in the report
        $sub_array[] = '<span>' . $serial . '</span>';
        $sub_array[] = '<span>' . $row['send_from'] . '</span>';
        $sub_array[] = '<span>' . $row['send_to'] . '</span>';
        $sub_array[] = '<span>' . $message . '</span>';
        $sub_array[] = '<span>' . $status_text . '</span>';
        $sub_array[] = '<span>' . $status_response . '</span>';
        $sub_array[] = '<span>' . $row['create_date'] . '</span>';

        // Add this row to the data array
        $data[] = $sub_array;
        $serial++;
    }

    // Prepare the final output array for DataTables
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $totalRecords, 
        "data"            => $data,
        "sql" => $sql
    );

    // Return the output as JSON
    echo json_encode($output);
}

// Helper function to count total number of records in the instagram report table
function count_all_data_insta_report(){
    global $db, $link;
    $query = "SELECT * FROM $db.instagram_out_queue ";
    $case_result = mysqli_query($link, $query);
    $total = mysqli_num_rows($case_result);
    return $total;
}

/*
This function processes Instagram posts for a DataTable display
and supports filtering and pagination.
Author: Aarti
Date: 19-11-2024
*/
function Instagram_Datatable_Post() {
    global $link, $db;

    // Fetch POST request parameters
    $startdatetime = $_POST['startdatetime'] ?? '';  // Start date for filtering
    $enddatetime = $_POST['enddatetime'] ?? '';      // End date for filtering
    $iallstatus = $_POST['iallstatus'] ?? '';        // Status filter (e.g., open/closed)
    $ICASEID = $_POST['ICASEID'] ?? '';              // Specific case ID for filtering
    $send_from = $_POST['send_from'] ?? '';          // Instagram handle of the sender
    $send_name = $_POST['send_name'] ?? '';          // Sender's name for filtering

    // Convert date formats for database queries
    $changestartdatetime = datetimeformat($startdatetime);  // Format start date
    $changeenddatetime = date('Y-m-d', strtotime($enddatetime . '+1 days')); // End date + 1 day for inclusivity

    // Default condition to ensure valid WHERE clause
    $condition = "1=1";  

    // Filter by status
    if ($iallstatus == '0') {
        $condition .= " AND (ICASEID = '' OR ICASEID IS NULL)";  // Unassigned cases
    } elseif ($iallstatus == '1') {
        $condition .= " AND ICASEID > 0";  // Assigned cases
    }

    // Filter by specific case ID
    if ($ICASEID) {
        $condition .= " AND `ICASEID` = '" . mysqli_real_escape_string($link, $ICASEID) . "'";
    }

    // Filter by sender's Instagram handle
    if ($send_from) {
        $condition .= " AND `send_from` = '" . mysqli_real_escape_string($link, $send_from) . "'";
    }

    // Filter by sender's name
    if ($send_name) {
        $condition .= " AND `send_from` = '" . mysqli_real_escape_string($link, $send_name) . "'";
    }

    // Filter by date range
    if ($startdatetime && $enddatetime) {
        $condition .= " AND `timestamp` >= '$changestartdatetime' AND `timestamp` <= '$changeenddatetime'";
    }

    // SQL query to fetch posts based on the filters
    $sql = "SELECT * FROM $db.instagram_posts WHERE $condition ORDER BY timestamp DESC";
    $totalRecordsQuery = mysqli_query($link, $sql);
    
    // Error handling for the query
    if (!$totalRecordsQuery) {
        die('Query Error: ' . mysqli_error($link));
    }

    // Count the total number of records
    $totalRecords = mysqli_num_rows($totalRecordsQuery);

    // Add pagination if the DataTable requested a specific range
    if ($_POST["length"] != -1) {
        $start = (int) $_POST['start'];  // Starting index for pagination
        $length = (int) $_POST['length'];  // Number of records to fetch
        $sql .= " LIMIT $start, $length";
    }

    // Execute the query with pagination
    $query = mysqli_query($link, $sql);
    
    // Error handling for the paginated query
    if (!$query) {
        die('Query Error: ' . mysqli_error($link));
    }

    // Initialize the data array for DataTable
    $data = [];
    $serial = $_POST['start'] + 1;  // Serial number for display

    // Process each row of the query result
    while ($row = mysqli_fetch_array($query)) {
        $data[] = processRowDataInstagramPost($row, $serial++);
    }

    // Prepare the output array for DataTable
    $output = [
        "draw" => intval($_POST["draw"]),          // Draw counter for DataTable
        "recordsTotal" => $totalRecords,          // Total number of records in the table
        "recordsFiltered" => $totalRecords,       // Total number of records after filtering
        "data" => $data,                          // Processed data rows
        "sql" => $sql                             // Debug: SQL query (optional, remove in production)
    ];

    // Return the JSON response to the DataTable
    echo json_encode($output);
}

/*
This function processes a single row of data from the Instagram posts query
and formats it for display in the DataTable.
*/
function processRowDataInstagramPost($row, $serial) {
    global $link, $db, $messenger_path;

    // Extract data from the row
    $id = $row['id'];
    $ICASEID = $row['ICASEID'];
    $messageid = $row['id'];
    $attachment = $row['media_url'];
    $create_date = $row['timestamp'];
    $username = $row['send_from'];
    $send_to = $row['id'];
    $flag = $row['flag'];

    $clr = getFlagColor($flag);

    // Fetch user-friendly sender name
    // $sql_name = "SELECT fname FROM $db.web_accounts WHERE instagramhandle = '$send_from'";
    // $result_new = mysqli_query($link, $sql_name);
    // $row_new = mysqli_fetch_assoc($result_new);
    // $username = empty($row_new['fname']) ? $send_from : $row_new['fname'];  // Default to handle if name is empty

    // Fetch attachment path for media files
    $sql_cdr = "SELECT * FROM $db.tbl_instagram_connection WHERE status = 1 AND debug = 1";
    $query = mysqli_query($link, $sql_cdr);
    $config = mysqli_fetch_array($query);
    $attachment_path = $config['attachment_path'];

    // Display post media (image or video)
    if ($row['media_type'] === 'IMAGE') {
        $attachment_link = '<img src="' . $row['media_url'] . '" alt="Instagram Post" style="height:150px;width:150px;">';
    } elseif ($row['media_type'] === 'VIDEO') {
        $attachment_link = '<video controls>
            <source src="' . $row['media_url'] . '" type="video/mp4">
            Your browser does not support the video tag.
        </video>';
    } else {
        $attachment_link = "No Media";  // Fallback if media type is not found
    }

    // Build the case ID link or "Create Case" option
    // if (!empty($ICASEID)) {
    //     $case_page = 'Case ID: <a href="helpdesk_index.php?token=' . base64_encode('web_case_detail') .
    //         '&id=' . base64_encode($ICASEID) . '&mr=15">' . $ICASEID . '</a>';
    // } else {
    //     $case_page = '<a title="CreateComplain" href="helpdesk_index.php?token=' . base64_encode('new_case_manual') .
    //         '&instagramid=' . $messageid . '&mr=15">Create Case</a>';
    // }

    // Prepare the reply link
    $reply_link = '<a href="omnichannel_config/web_instagram_post_comment.php?ID=' . $row['id'] .
        '&send_to=' . $row['send_from'] . '&id=' . $ICASEID . '&send_from=' . $row['send_to'] .
        '&messageid=' . $row['id'] . '&account_sender_id=' . $row['send_from'] . '" class="ico-interaction2">View</a>';

    // Fetch new message notification count
    $sqls = "SELECT count(*) as total FROM $db.instagram_post_comments WHERE flag = '0' AND post_id = '$send_to'";
    $response = mysqli_query($link, $sqls);
    $rowCount = mysqli_fetch_array($response);

    $reply_link .= $rowCount['total'] > 0 ? '<span class="badge badge-light">' . $rowCount['total'] . '</span>' : '';

    // Return the formatted row data for DataTable
    return [
        '<span>' . $serial . '</span>',
        '<span>' . date('d-m-Y H:i', strtotime($create_date)) . '</span>',
        '<span>' . $username . '</span>',
        // '<span>' . $send_to . '</span>',
        '<span>' . ($row['caption'] ?: 'N/A') . '</span>',
        $attachment_link,
        // $case_page,
        $reply_link,
        $clr
    ];
}

// code for appending message sent via whatsapp [vastvikta][10-03-2025]
function whatsapp_reply(){
    global $db,$link,$whatsapp_path,$BasePath,$whatsapp_path_out;
    $message=trim($_POST["message"]);
    $message=addslashes($message);
    $todaytime=date("Y-m-d H:i:s");
    $caller_id = $_POST['send_to'];
    $message_text = $message;
    $V_CreatedBY = $_SESSION['userid'];
    $todaytime = $todaytime;
    $queue_session = '';
    $mesg_type = '1'; // 1 means bulk message type
    $mesg_flag = '0'; //msg flag 0 
    $send_from = $_POST['send_from'];
    /** Updated PHP Code for Multiple File Uploads [Aarti][01-05-2025]**/ 
    $attachments = []; // To store file paths
    if (isset($_FILES['attachments'])) {
        $date = date('dmy');
        $directoryPath = $whatsapp_path_out . 'outgoing/' . $date;

        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            chmod($directoryPath, 0777);
        }

        foreach ($_FILES['attachments']['name'] as $index => $name) {
            if ($_FILES['attachments']['error'][$index] === 0) {
                $tmpName = $_FILES['attachments']['tmp_name'][$index];
                $targetFile = $directoryPath . '/' . basename($name);

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $attachmentUrl = $BasePath . '/' . $whatsapp_path . 'outgoing/' . $date . '/' . basename($name);
                    $attachments[] = $attachmentUrl;
                }
            }
        }
    }
    /** If each attachment should be a separate row: [Aarti][01-05-2025]**/ 
    // for send outgoing message and attchemnt single entry facebook not accept both same time
    if (!empty($attachments) && empty($message_text)) {
        foreach ($attachments as $attachment) {
            $sql_sms_feed = "INSERT INTO $db.whatsapp_out_queue (
                send_to, send_from, message, message_type_flag, status, create_date,
                bulk_session_id, created_by, channel_type, msg_flag, queue_session, user_name, attachment
            ) VALUES (
                '$caller_id', '$send_from', '', '$mesg_type', '$mesg_flag', '$todaytime',
                '', '$V_CreatedBY', '1', 'OUT', '$queue_session', 'ensembler', '$attachment'
            )";
            $result_sms = mysqli_query($link, $sql_sms_feed) or die("Error In whatsapp_out_queue: " . mysqli_error($link));
        }
        mysqli_query($link, "UPDATE $db.whatsapp_in_queue SET flag='1' WHERE send_from='$send_to'");
    }

    // if message text not emty insert data [Aarti][12-08-2024]
    if(!empty($message_text)){
      $sql_sms_feed="insert into $db.whatsapp_out_queue (send_to,send_from,message,message_type_flag,status,create_date,bulk_session_id, created_by,channel_type,msg_flag,queue_session,user_name,attachment) values ('$caller_id','$send_from','$message_text','$mesg_type','$mesg_flag','$todaytime','', '$V_CreatedBY','1','OUT','$queue_session','ensembler','')";
      $result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In whatsapp_out_queue ".mysqli_error($link));
      $interact_id = mysqli_insert_id($link);
            
      mysqli_query($link,"update $db.whatsapp_in_queue set flag='1' where send_from='$send_to'");
    }
    // updated code by [vastivkta][16-04-2025]    for insertion in  interaction table                
    // check against all number with or without 91 
    if (preg_match('/^91(\d{10})$/', $caller_id, $matches)) {
        // Case 1: 12-digit number starting with 91
        $caller_id_new = $matches[1]; // Just the 10-digit number
    } elseif (preg_match('/^\d{10}$/', $caller_id)) {
        // Case 2: Only 10-digit number
        $caller_id_new = '91' . $caller_id;
    }

    $isexist = get_user_list_whatsapp($caller_id, $caller_id_new);

    $agentid = $_SESSION['userid'];
    if (!empty($isexist)) {
        $customerid = $isexist['AccountNumber']; // Get customer_id from the result
        $sql = "INSERT INTO $db.interaction (
                    caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type,created_by
                ) VALUES (
                    '', 'Whatsapp', '', '$caller_id', '', '$interact_id', '$customerid', '$message_text', '$attachment', NOW(), 'OUT','$agentid'
                )";

        $result_sms = mysqli_query($link, $sql) or die("Error In Query of interaction insertion " . mysqli_error($link));
    }

    // Fetch the latest inserted message
    $response = [];
    if (!empty($message_text)) {
        $response[] = [
            "message" => htmlspecialchars($message_text),
            "attachment" => '',
            "send_from" => $send_from,
            "send_to" => $caller_id,
            "timestamp" => $todaytime
        ];
    }
    // Fetch the latest inserted attachment
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            $response[] = [
                "message" => '',
                "attachment" => $attachment,
                "send_from" => $send_from,
                "send_to" => $caller_id,
                "timestamp" => $todaytime
            ];
        }
    }
    echo json_encode($response);
    exit;
}
function get_user_list_whatsapp($send_from, $send_from_new) {
    global $db, $link;

    // Sanitize input to prevent SQL injection (prepared statements are best practice)
    $send_from = mysqli_real_escape_string($link, $send_from);
    $send_from_new = mysqli_real_escape_string($link, $send_from_new);

    $sql = "SELECT * FROM $db.web_accounts 
            WHERE phone = '$send_from' OR phone = '$send_from_new' 
            OR mobile = '$send_from' OR mobile = '$send_from_new' 
             
            LIMIT 1";

    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // Return the first matching row
    } else {
        return null; // No match found
    }
}
// code for appending message sent via messenger [vastvikta][10-03-2025]
function messenger_reply(){
    global $db,$link,$messenger_path,$messenger_path_out, $BasePath;

    $message=trim($_POST["message"]);
    $message=addslashes($message);
    $todaytime=date("Y-m-d H:i:s");
    $caller_id = $_POST['send_to'];
    $message_text = $message;
    $V_CreatedBY = $_SESSION['userid'];
    $todaytime = $todaytime;
    $queue_session = '';
    $mesg_type = '1'; // 1 means bulk message type
    $mesg_flag = '0'; //msg flag 0 
    $send_from = $_POST['send_from'];

    // updated the attachment logic  reference from whatsapp reply [vastvikta][02-05-2025]
    $attachments = []; // To store file paths

    if (isset($_FILES['attachments'])) {
        $date = date('dmy');
        $directoryPath = $messenger_path_out . 'outgoing/' . $date;

        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            chmod($directoryPath, 0777);
        }

        foreach ($_FILES['attachments']['name'] as $index => $name) {
            if ($_FILES['attachments']['error'][$index] === 0) {
                $tmpName = $_FILES['attachments']['tmp_name'][$index];
                $targetFile = $directoryPath . '/' . basename($name);

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $attachmentUrl = $BasePath . '/' . $messenger_path . 'outgoing/' . $date . '/' . basename($name);
                    $attachments[] = $attachmentUrl;
                }
            }
        }
    }
    //go through all attachments to insert them one by one [vastvikta][02-05-2025]
    if(!empty($attachments)){
        foreach ($attachments as $attachment) {
      $sql_sms_feed="insert into $db.messenger_out_queue (send_to,send_from,message,message_type_flag,status,create_date,bulk_session_id, created_by,channel_type,msg_flag,queue_session,attachment) values ('$caller_id','$send_from','','$mesg_type','$mesg_flag','$todaytime','', '$V_CreatedBY','1','OUT','$queue_session','$attachment')";
      $result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In whatsapp_out_queue ".mysqli_error($link));
      $interact_id = mysqli_insert_id($link);
      mysqli_query($link,"update $db.messenger_in_queue set flag='1' where send_from='$send_to'");
    }
    }
    // if message text not emty insert data [Aarti][12-08-2024]
    if(!empty($message_text)){
      $sql_sms_feed="insert into $db.messenger_out_queue (send_to,send_from,message,message_type_flag,status,create_date,bulk_session_id, created_by,channel_type,msg_flag,queue_session,attachment) values ('$caller_id','$send_from','$message_text','$mesg_type','$mesg_flag','$todaytime','', '$V_CreatedBY','1','OUT','$queue_session','')";
    
      $result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In whatsapp_out_queue ".mysqli_error($link));
      $interact_id = mysqli_insert_id($link);
      mysqli_query($link,"update $db.messenger_in_queue set flag='1' where send_from='$send_to'");
    }
    $isexist = get_user_list_messenger($caller_id);

    if (!empty($isexist)) {
        $customerid = $isexist['AccountNumber']; // Get customer_id from the result

        $sql_new = "INSERT INTO $db.interaction (
                    caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type,created_by
                ) VALUES (
                    '', 'messenger', '', '$send_from', '', '$interact_id', '$customerid', '$message_text', '$attachment', NOW(), 'OUT' ,'$agentid'
                )";

        $result_mess = mysqli_query($link, $sql_new) or die("Error In Query of interaction insertion " . mysqli_error($link));
    }
  
     // Fetch the latest inserted message [vastvitka][02-05-2025]
     $response = [];
     if (!empty($message_text)) {
         $response[] = [
             "message" => htmlspecialchars($message_text),
             "attachment" => '',
             "send_from" => $send_from,
             "send_to" => $caller_id,
             "timestamp" => $todaytime
         ];
     }
     // Fetch the latest inserted attachment[vastvitka][02-05-2025]
     if (!empty($attachments)) {
         foreach ($attachments as $attachment) {
             $response[] = [
                 "message" => '',
                 "attachment" => $attachment,
                 "send_from" => $send_from,
                 "send_to" => $caller_id,
                 "timestamp" => $todaytime
             ];
         }
     }
     echo json_encode($response);
     exit;
}
// function to fetch user details from the  web accounts [vastvikta][15-04-2025]
function get_user_list_messenger($send_from){
    global $db,$link;
    $send_from = mysqli_real_escape_string($link, $send_from);
   
    $sql = "SELECT * FROM $db.web_accounts 
            WHERE messengerhandle = '$send_from' LIMIT 1";

    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // Return the first matching row
    } else {
        return null; // No match found
    }
}
// code for appending message sent via instagram [vastvikta][10-03-2025]
function instagram_reply(){
    global $db,$link,$webook_instagram_path,$instagram_path,$BasePath;

    $message=trim($_POST["message"]);
    $message=addslashes($message);
    $todaytime=date("Y-m-d H:i:s");
    $caller_id = $_POST['send_to'];
    $message_text = $message;
    $V_CreatedBY = $_SESSION['userid'];
    $todaytime = $todaytime;
    $queue_session = '';
    $mesg_type = '1'; // 1 means bulk message type
    $mesg_flag = '0'; //msg flag 0 
    $send_from = $_POST['send_from'];
    // updated the attachment logic  reference from whatsapp reply [vastvikta][02-05-2025] 
    $attachments = []; // To store file paths

    if (isset($_FILES['attachments'])) {
        $date = date('dmy');
        $directoryPath = $webook_instagram_path . 'outgoing/' . $date;

        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            chmod($directoryPath, 0777);
        }

        foreach ($_FILES['attachments']['name'] as $index => $name) {
            if ($_FILES['attachments']['error'][$index] === 0) {
                $tmpName = $_FILES['attachments']['tmp_name'][$index];
                $targetFile = $directoryPath . '/' . basename($name);

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $attachmentUrl = $BasePath . '/' . $instagram_path . 'outgoing/' . $date . '/' . basename($name);
                    $attachments[] = $attachmentUrl;
                }
            }
        }
    }
    //go through all attachments to insert them one by one [vastvikta][02-05-2025]
    if(!empty($attachments)){
        foreach ($attachments as $attachment) {
      $sql_sms_feed="insert into $db.instagram_out_queue (send_to,send_from,message,message_type_flag,status,create_date,bulk_session_id, created_by,channel_type,msg_flag,queue_session,attachment) values ('$caller_id','$send_from','','$mesg_type','$mesg_flag','$todaytime','', '$V_CreatedBY','1','OUT','$queue_session','$attachment')";
      $result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In whatsapp_out_queue ".mysqli_error($link));
      $interact_id = mysqli_insert_id($link);
      
      mysqli_query($link,"update $db.instagram_in_queue set flag='1' , status='1' where send_from='$send_to'; ");
      // echo $sql_sms_feed;
        }
    }
    // if message text not emty insert data [Aarti][12-08-2024]
    if(!empty($message_text)){
      $sql_sms_feed="insert into $db.instagram_out_queue (send_to,send_from,message,message_type_flag,status,create_date,bulk_session_id, created_by,channel_type,msg_flag,queue_session,attachment) values ('$caller_id','$send_from','$message_text','$mesg_type','$mesg_flag','$todaytime','', '$V_CreatedBY','1','OUT','$queue_session','')";
      $result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In whatsapp_out_queue ".mysqli_error($link));
      $interact_id = mysqli_insert_id($link);
      
      mysqli_query($link,"update $db.instagram_in_queue set flag='1' , status='1' where send_from='$send_to'; ");
    }
    $isexist = get_user_list_instagram($caller_id);

    if (!empty($isexist)) {
        $customerid = $isexist['AccountNumber']; // Get customer_id from the result
        $agentid = $_SESSION['userid'];
        $sql_new = "INSERT INTO $db.interaction (
                    caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type,created_by
                ) VALUES (
                    '', 'instagram', '', '$send_from', '', '$interact_id', '$customerid', '$message_text', '$attachment', NOW(), 'OUT' , '$agentid'
                )";

        $result_mess = mysqli_query($link, $sql_new) or die("Error In Query of interaction insertion " . mysqli_error($link));
    }

    //  // Fetch the latest inserted message [vastvitka][02-05-2025]
    $response = [];
    if (!empty($message_text)) {
        $response[] = [
            "message" => htmlspecialchars($message_text),
            "attachment" => '',
            "send_from" => $send_from,
            "send_to" => $caller_id,
            "timestamp" => $todaytime
        ];
    }
    // Fetch the latest inserted attachment[vastvitka][02-05-2025]
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            $response[] = [
                "message" => '',
                "attachment" => $attachment,
                "send_from" => $send_from,
                "send_to" => $caller_id,
                "timestamp" => $todaytime
            ];
        }
    }
    echo json_encode($response);
    exit;
}
function get_user_list_instagram($send_from){
    global $db,$link;
    $send_from = mysqli_real_escape_string($link, $send_from);
   
    $sql = "SELECT * FROM $db.web_accounts 
    WHERE instagramhandle = '$send_from' LIMIT 1";

    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result); // Return the first matching row
    } else {
    return null; // No match found
    }
}


/**
This code for fetching whatsapp live data for display in live chat UI
[Aarti][01-05-2025]
**/ 
function WhatsAppDM_Live(){
    global $db,$link,$SiteURL,$dbheadlogo;
    $send_to = $_POST['send_to'];

    $last_id = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;

    // for unread message - [Aarti][01-05-2025]
    mysqli_query($link,"update $db.whatsapp_in_queue set flag='1' where send_from='$send_to'");
    $sql = "SELECT id, send_to, send_from, message, create_date, channel_type, attachment, user_name from whatsapp_in_queue
            WHERE send_from = '$send_to' and id > $last_id ORDER BY create_date ASC";

    $qdm = mysqli_query($link, $sql) or die("err" . mysqli_error());
    $htmlwhast = '';
    $newLastId = $last_id;

    while ($rm = mysqli_fetch_assoc($qdm)) {
        $newLastId = max($newLastId, $rm['id']); // track latest ID

        if ($rm['send_to'] != $send_to) {
            $msg_class = "left-img";
            $msg_float = "left";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/whatsapp_png.png" style="height:30px;width:30px;">';
            $class = 'received';
        } else {
            $msg_class = "right-img";
            $msg_float = "right";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/' . $dbheadlogo . '" style="height:30px;width:30px;">';
            $class = 'sent';
        }

        $htmlwhast .= '<div class="message-box '.$msg_class.'"><div class="picture">'
                    . $imgsrc . '</div><div class="message '.$class.'" style="float: '.$msg_float.'"><p>'
                    . htmlspecialchars($rm["message"]) . '</p><p class="message-time" style="text-align: right; margin: 0;">'
                    . $rm["create_date"] . '</p>';

        if (!empty($rm['attachment'])) {
            $htmlwhast .= '<a href="../../../'.$rm['attachment'].'" target="_blank">attachment</a>';
        }

        $htmlwhast .= '</div></div>';
    }

    echo json_encode([
        'html' => $htmlwhast,
        'last_id' => $newLastId
    ]);
}

/**
This code for fetching instagram live data for display in live chat UI
[vastvikta][14-05-2025]
**/ 
function InstagramDM_Live(){
    global $db,$link,$SiteURL,$dbheadlogo;;
    $send_to = $_POST['send_to'];

    $last_id = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;

    
    mysqli_query($link,"update $db.instagram_in_queue set flag='1' where send_from='$send_to'");
    $sql = "SELECT id, send_to, send_from, message, create_date, channel_type, attachment, user_name from instagram_in_queue
            WHERE send_from = '$send_to' and id > $last_id ORDER BY create_date ASC";

    $qdm = mysqli_query($link, $sql) or die("err" . mysqli_error());
    $htmlwhast = '';
    $newLastId = $last_id;

    while ($rm = mysqli_fetch_assoc($qdm)) {
        $newLastId = max($newLastId, $rm['id']); // track latest ID

        if ($rm['send_to'] != $send_to) {
            $msg_class = "left-img";
            $msg_float = "left";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/instagram.png" style="height:30px;width:30px;">';
            $class = 'received';
        } else {
            $msg_class = "right-img";
            $msg_float = "right";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/' . $dbheadlogo . '" style="height:30px;width:30px;">';
            $class = 'sent';
        }

        $htmlwhast .= '<div class="message-box '.$msg_class.'"><div class="picture">'
                    . $imgsrc . '</div><div class="message '.$class.'" style="float: '.$msg_float.'"><p>'
                    . htmlspecialchars($rm["message"]) . '</p><p class="message-time" style="text-align: right; margin: 0;">'
                    . $rm["create_date"] . '</p>';

        if (!empty($rm['attachment'])) {
            $htmlwhast .= '<a href="../../../'.$rm['attachment'].'" target="_blank">attachment</a>';
        }

        $htmlwhast .= '</div></div>';
    }

    echo json_encode([
        'html' => $htmlwhast,
        'last_id' => $newLastId
    ]);
}

/**
This code for fetching messenger live data for display in live chat UI
[vastvikta][14-05-2025]
**/
function MessengerDM_Live(){
    global $db,$link,$SiteURL,$dbheadlogo;
    $send_to = $_POST['send_to'];

    $last_id = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;

    mysqli_query($link,"update $db.messenger_in_queue set flag='1' where send_from='$send_to'");
    $sql = "SELECT id, send_to, send_from, message, create_date, channel_type, attachment from $db.messenger_in_queue
            WHERE send_from = '$send_to' and id > $last_id ORDER BY create_date ASC";

    $qdm = mysqli_query($link, $sql) or die("err" . mysqli_error());
    $htmlwhast = '';
    $newLastId = $last_id;

    while ($rm = mysqli_fetch_assoc($qdm)) {
        $newLastId = max($newLastId, $rm['id']); // track latest ID

        if ($rm['send_to'] != $send_to) {
            $msg_class = "left-img";
            $msg_float = "left";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/facebook-messenger-icone.png" style="height:30px;width:30px;">';
            $class = 'received';
        } else {
            $msg_class = "right-img";
            $msg_float = "right";
            $imgsrc = '<img src="' . $SiteURL . 'public/images/' . $dbheadlogo . '" style="height:30px;width:30px;">';
            $class = 'sent';
        }

        $htmlwhast .= '<div class="message-box '.$msg_class.'"><div class="picture">'
                    . $imgsrc . '</div><div class="message '.$class.'" style="float: '.$msg_float.'"><p>'
                    . htmlspecialchars($rm["message"]) . '<p class="message-time" style="text-align: right; margin: 0;">' . $rm["create_date"] . '</p>';

        if (!empty($rm['attachment'])) {
            $htmlwhast .= '<a href="../../../'.$rm['attachment'].'" target="_blank">attachment</a>';
        }

        $htmlwhast .= '</div></div>';
    }

    echo json_encode([
        'html' => $htmlwhast,
        'last_id' => $newLastId
    ]);
}
?>