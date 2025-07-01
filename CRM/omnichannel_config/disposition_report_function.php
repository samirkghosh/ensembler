<?php
/***
 * Disposition Report Function Page
 * Author: Aarti Ojha
 * Date: 23-07-2024
 * This file is handling Channel Disposition Report
 * Email,sms,twitter,chat,whatsapp,facebook handling multiple channel Disposition Report
 */
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// fetch user details
include("../web_function.php");
// Check if the action is to view an Disposition report
if($_POST['action'] == 'Disposition_Report'){
  Disposition_Report(); // Call function to view Disposition report
}

// Functions defined for fetching the field value to populate the table [vastvikta][11-12-2024]
function get_email_channel_detail($channel_id){
    global $db, $link;
    // Construct the SQL query to fetch web_email_information report with the specified conditions
    $query = "SELECT `v_fromemail`, `sentiment` FROM $db.web_email_information WHERE EMAIL_ID ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    $v_fromemail = $row['v_fromemail'];
    $sentiment = $row['sentiment'];
    return ['v_fromemail' => $v_fromemail, 'sentiment' => $sentiment];
}

function get_sms_channel_detail($channel_id){
    global $db, $link;
    // Construct the SQL query to fetch tbl_smsmessagesin report with the specified conditions
    $query = "SELECT `v_mobileNo`, `V_AccountName`, `sentiment` FROM $db.tbl_smsmessagesin WHERE i_id ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['v_mobileNo' => $row['v_mobileNo'], 'V_AccountName' => $row['V_AccountName'], 'sentiment' => $row['sentiment']];
}

function get_whatsapp_channel_detail($channel_id){
    global $db, $link;
    // Construct the SQL query to fetch whatsapp_in_queue with the specified conditions
    $query = "SELECT `send_from`, `sentiment` FROM $db.whatsapp_in_queue WHERE id ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['send_from' => $row['send_from'], 'sentiment' => $row['sentiment']];
}

function get_web_chat_detail($channel_id){
    global $db, $link;
    $query = "SELECT `from`, `name`, `email`, `sentiment` FROM $db.overall_bot_chat_session WHERE id ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['name' => $row['name'], 'from' => $row['from'], 'email' => $row['email'], 'sentiment' => $row['sentiment']];
}

function get_tweet_detail($channel_id){
    global $db, $link;
    $query = "SELECT `v_Screenname`, `sentiment` FROM $db.tbl_tweet WHERE i_ID ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['v_Screenname' => $row['v_Screenname'], 'sentiment' => $row['sentiment']];
}

function get_Messenger_detail($channel_id){
    global $db, $link;
    $query = "SELECT `send_from`, `sentiment` FROM $db.messenger_in_queue WHERE id ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['send_from' => $row['send_from'], 'sentiment' => $row['sentiment']];
}

function get_Instagram_detail($channel_id){
    global $db, $link;
    $query = "SELECT `send_from`, `customer_id`, `sentiment` FROM $db.instagram_in_queue WHERE id ='{$channel_id}'";
    // Execute the query
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    return ['send_from' => $row['send_from'], 'customer_id' => $row['customer_id'], 'sentiment' => $row['sentiment']];
}

/*
 * Fetches Disposition report with filter options.
 *
 * This function retrieves Disposition report from the database based on specified filter options such as startdatetime, enddatetime, source, mode, category, and subcategory.
 * It constructs a SQL query to fetch NPS report with the provided filters and returns the result.
 *
 * @return mixed Returns the result of the SQL query containing NPS report based on the provided filters.
 */
function Disposition_Report() {
    global $db, $link;
    
    // Define column mappings for order (if necessary)
    $columns = ['id', 'createdby', 'caller_name', 'v_mobileNo', 'email', 'channel_type', 'disposition_type','', 'remarks', 'created_date'];
    
    $startdatetime = !empty($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : '';
    $enddatetime = !empty($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : '';
    $phone_number_filter = !empty($_POST['phone_number']) ? $_POST['phone_number'] : '';
    $caller_name_filter = !empty($_POST['caller_name']) ? $_POST['caller_name'] : '';
    $channel_type_filter = !empty($_POST['channel_type']) ? $_POST['channel_type'] : '';
    $disposition_filter = !empty($_POST['disposition']) ? $_POST['disposition'] : '';
    $sentiment_filter = !empty($_POST['sentiment']) ? $_POST['sentiment'] : '';

    // Base query condition
    $condition = " WHERE channel_id != '' ";
    
    // Date range filter
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND created_date BETWEEN '$from' AND '$to'";
    } elseif (!empty($startdatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $condition .= " AND created_date >= '$from'";
    } elseif (!empty($enddatetime)) {
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND created_date <= '$to'";
    }

    // Disposition filter
    if (!empty($disposition_filter)) {
        $disposition_filter = mysqli_real_escape_string($link, $disposition_filter);
        $condition .= " AND disposition_type = '$disposition_filter'";
    }
    

    // Phone number filter
    if (!empty($phone_number_filter)) {
        $phone_number_filter = mysqli_real_escape_string($link, $phone_number_filter);
        $condition .= " AND (v_mobileNo LIKE '%$phone_number_filter%')";
    }

    // Caller name filter
    if (!empty($caller_name_filter)) {
        $caller_name_filter = mysqli_real_escape_string($link, $caller_name_filter);
        $condition .= " AND caller_name LIKE '%$caller_name_filter%'";
    }

    // Channel type filter
    if (!empty($channel_type_filter)) {
        $channel_type_filter = mysqli_real_escape_string($link, $channel_type_filter);
        $condition .= " AND channel_type = '$channel_type_filter'";
    }

    // Ordering and pagination
    if (isset($_POST['order'])) {
        $order_column_index = $_POST['order']['0']['column'];
        $order_column = $columns[$order_column_index];
        $order_dir = $_POST['order']['0']['dir'];
        $condition .= " ORDER BY $order_column $order_dir";
    } else {
        $condition .= " ORDER BY created_date DESC"; // Default ordering
    }

    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $condition .= " LIMIT $start, $length";
    }

    // Total record count for pagination
    $total_query = "SELECT COUNT(*) AS total FROM $db.multichannel_disposition $condition";
    $total_result = mysqli_query($link, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $recordsTotal = $total_row['total'];

    // Filtered record count for pagination
    $filtered_query = "SELECT COUNT(*) AS filtered FROM $db.multichannel_disposition $condition";
    $filtered_result = mysqli_query($link, $filtered_query);
    $filtered_row = mysqli_fetch_assoc($filtered_result);
    $recordsFiltered = $filtered_row['filtered'];

    // Fetch data with applied conditions
    $query = "SELECT * FROM $db.multichannel_disposition $condition";
    $result = mysqli_query($link, $query);

    // Prepare the data for the response
    $data = array();
    $sno = 0;

    while ($row = mysqli_fetch_array($result)) {
        $sno++;
        $AgentName = agentname($row['createdby']);
        $CallerName = 'N/A';
        $phone_number = 'N/A';
        $Channel_type = $row['channel_type'];
        $Disposition = $row['disposition_type'];
        $Remark = $row['remarks'];
        $email = 'N/A';
        $EntryDate = $row['created_date'];
        $channel_id = $row['channel_id'];
        $sentiment = 'N/A';
    
        // Handle different channel types Field values [vastvikta][11-12-2024]
        //updated the code for   updating sentiment in the respective  channel[vastvikta][12-12-2024]
        if ($Channel_type == 'Email') {
            $email_data = get_email_channel_detail($channel_id);
            $email = $email_data['v_fromemail'];
            $sentiment = $email_data['sentiment'];
        } elseif ($Channel_type == 'SMS') {
            $sms_data = get_sms_channel_detail($channel_id);
            $phone_number = $sms_data['v_mobileNo'];
            $CallerName = $sms_data['V_AccountName'];
            $sentiment = $sms_data['sentiment'];
        } elseif ($Channel_type == 'Whatsapp') {
            $whats_data = get_whatsapp_channel_detail($channel_id);
           
           
            $sentiment = $whats_data['sentiment'];

           
            $whatsapphandle =  $whats_data['send_from'];
            $sql_name = "SELECT fname FROM $db.web_accounts WHERE whatsapphandle = '$whatsapphandle'";
            $result_new = mysqli_query($link, $sql_name); // Run the query
            $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array

            $fname = $row_new['fname']; // Store the 'fname' value in the variable
            if(empty($fname)){
                $CallerName = $whats_data['send_from'];
            }
            else {
                $CallerName = $fname;
            }


        } elseif ($Channel_type == 'web_chat') {
            $chat_data = get_web_chat_detail($channel_id);
            $phone_number = $chat_data['from'];
            $CallerName = $chat_data['name'];
            $email = $chat_data['email'];
            $sentiment = $chat_data['sentiment'];
        } elseif ($Channel_type == 'Twitter') {
            $Tweet_data = get_tweet_detail($channel_id);
            $CallerName = $Tweet_data['v_Screenname'];
            $sentiment = $Tweet_data['sentiment'];
        } elseif ($Channel_type == 'Facebook Messenger') {
            $Messenger_data = get_Messenger_detail($channel_id);
            $CallerName = $Messenger_data['send_from'];
            $sentiment = $Messenger_data['sentiment'];

            $sql_name = "SELECT fname FROM $db.web_accounts WHERE messengerhandle = '$CallerName'";
            $result_new = mysqli_query($link, $sql_name); // Run the query
            $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array
        
            $fname = $row_new['fname']; // Store the 'fname' value in the variable
        
            // Check if fname is empty
            if (empty($fname)) {
                $CallerName = $CallerName ; // Set $name to $send_from if fname is empty
            } else {
                $CallerName = $fname; // Set $name to $fname otherwise
            }

        } elseif ($Channel_type == 'Instagram Messenger') {
            $insta_data = get_Instagram_detail($channel_id);
            $customer_id = $insta_data['customer_id'];
            $sentiment = $insta_data['sentiment'];
    
            $instagramhandle =   $insta_data['send_from'];
            $sql_name = "SELECT fname FROM $db.web_accounts WHERE instagramhandle = '$instagramhandle'";
            $result_new = mysqli_query($link, $sql_name); // Run the query
            $row_new = mysqli_fetch_assoc($result_new); // Fetch as an associative array

            $fname = $row_new['fname']; // Store the 'fname' value in the variable

            // Check if fname is empty
            if (empty($fname)) {
                $CallerName = $instagramhandle ; // Set $name to $send_from if fname is empty
            } else {
                $CallerName = $fname; // Set $name to $fname otherwise
            }
        }
        if($Channel_type=='')

        $sentiment ="Undefined";
        // Prepare the row data
        $sub_array = array();
        $sub_array[] = $sno;
        $sub_array[] = $AgentName;
        $sub_array[] = $CallerName;
        $sub_array[] = $phone_number;
        $sub_array[] = $email;
        $sub_array[] = $Channel_type;
        $sub_array[] = $Disposition;
        $sub_array[] = $sentiment;
        $sub_array[] = $Remark;
        $sub_array[] = $EntryDate;
        $data[] = $sub_array;
    }

    // Return JSON response
    $output = array(
        "sql"=>$query ,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $data
    );
    echo json_encode($output);
}

// close Disposition_Report
// email data fetch
?>