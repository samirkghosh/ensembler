<?php
/**
 * Auth: Vastvikta Nishad
 * Date :24-12-24
 * Desc:  to handle multi chat  close insert and fetch messages 
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("../../config/web_mysqlconnect.php");

if (isset($_POST['action']) && $_POST['action'] === 'close_chat') {
    close_chat();
}
if (isset($_GET['action']) && $_GET['action'] === 'get_user_list') {
    echo get_user_list();  
}
if (isset($_POST['action']) && $_POST['action'] === 'insert_message') {
    insert_message();
}
if (isset($_POST['action']) && $_POST['action'] === 'setUserId') {
    setUserId();
}
if (isset($_POST['action']) && $_POST['action'] === 'fetch_message') {
    fetch_message();
}
function setUserId(){
    global $db, $link;
    
    $chat_session_id = $_POST['chat_session_id'];
    $senderId = $_POST['senderId'];
    
    // Prepare the SQL query to update the user_id field
    $sql = "UPDATE bot_chat_session 
            SET user_id = '$senderId' 
            WHERE chat_session = '$chat_session_id'";
    
    // Execute the query
    if (mysqli_query($link, $sql)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User ID updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update User ID: ' . mysqli_error($link)
        ]);
    }
}

// function to fetch message and display it on the chat section
function fetch_message() {
    global $db, $link;
    
    // Retrieve the chat session ID from the POST request
    $chat_session_id = $_POST['chat_session_id'];
    
    // Append badge if applicable
    $update = "UPDATE $db.in_out_data SET flag ='1' WHERE chat_session_id ='$chat_session_id' ";
    $link->query($update);

    // Sanitize the chat_session_id input to prevent SQL injection
    $chat_session_id = $link->real_escape_string($chat_session_id);

    // Create the SQL query directly
    $sql = "SELECT * FROM $db.`in_out_data` WHERE `chat_session_id` = '$chat_session_id' ORDER BY `create_datetime` ASC";
    
    // Execute the query
    $result = $link->query($sql);
    
    // Check if the query was successful
    if ($result) {
        // Fetch all rows as an associative array
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        
        // Return the messages as a JSON-encoded string
        echo json_encode($messages);
    } else {
        // In case of an error in the query execution
        $error_message = "Error: " . $link->error . " | SQL: " . $sql;
        echo json_encode(["error" => $error_message]);
    }
}

// functin for inserting message or attatchment 
function insert_message() {
    global $db, $link;
    $agent_id = $_SESSION['userid'];
    // Get message, sender, and receiver from POST
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $senderId = isset($_POST['sender_id']) ? (int)$_POST['sender_id'] : 0;
    $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : 0;
    $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;

    // File upload handling
    $attachmentPath = null;
    $uploadDir = '/home/unistorage/2224/webchat/'; // Absolute path to save files
    $dbPathPrefix = 'unistorage/2224/webchat/'; // Path to store in the database

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = basename($_FILES['attachment']['name']);
        $fileSize = $_FILES['attachment']['size'];
        $fileType = $_FILES['attachment']['type'];
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // For .docx
            'application/vnd.ms-powerpoint', // For .ppt
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' // For .pptx
        ];

        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unsupported file type. Allowed types are: JPEG, PNG, PDF, TXT, DOCX, PPT, PPTX.'
            ]);
            return;
        }

        // Validate file size (e.g., max 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File size exceeds the maximum limit of 5MB.'
            ]);
            return;
        }

        // Generate a unique file name and move the file
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'attachment' . time() . '.' . $fileExtension;
        $SaveattachmentPath = $uploadDir . $newFileName;
        $attachmentPath = $dbPathPrefix . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $SaveattachmentPath)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save the uploaded file. PHP Error: ' . error_get_last()['message']
            ]);
            return;
        }
    }

    if (empty($message) && $attachmentPath === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please provide a valid message or an attachment.'
        ]);
        return;
    }

    // Insert into the database if we have a message, an attachment, or both
    $sql = "INSERT INTO $db.in_out_data (customer_id, message, direction,attachment, create_datetime, send_status, chat_session_id, caseid,agent_id) 
            VALUES ($receiverId, '$message','OUT', '$attachmentPath', NOW(), '1', '$sessionId', '','$agent_id')";

    if (mysqli_query($link, $sql)) {
        $response = [
            'status' => 'success',
            'message' => 'Message sent successfully!',
            'data' => [
                'message_id' => mysqli_insert_id($link),
                'attachment_path' => $attachmentPath
            ]
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Error sending message: ' . mysqli_error($link)
        ];
    }

    echo json_encode($response);

}
function get_where($chat_session_id) {
    global $db, $link; // Ensure $db and $link are globally available

    // Escape the input to prevent SQL injection
    $chat_session_id = mysqli_real_escape_string($link, $chat_session_id);

    // Construct the SQL query
    $query = "SELECT * FROM $db.bot_chat_session WHERE chat_session = '$chat_session_id'";

    // Execute the query
    $result = mysqli_query($link, $query);

    // Check if the query was successful
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Fetch the first row as an associative array
            $row = mysqli_fetch_assoc($result);
            return $row;
        } else {
            return null; // Return null if no rows are found
        }
    } else {
        return null; // Return null if the query fails
    }
}
function get_ticket_id($chat_session_id) {
    global $db, $link; // Ensure $db and $link are globally available

    // Escape the input to prevent SQL injection
    $chat_session_id = mysqli_real_escape_string($link, $chat_session_id);

    $sql = "SELECT caseid FROM $db.overall_bot_chat_session WHERE chat_session = '$chat_session_id' AND caseid != ''";

    $result = mysqli_query($link, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['caseid'] : null; // Return caseid if found, otherwise return null
    } else {
        error_log("MySQL Error: " . mysqli_error($link)); // Log the error
        return null;
    }
}

function create_feedbacklink($data=array()){
    global $db,$link,$SiteURL;
    if(isset($data['createdBy'])){
        $Type = $data['Type'];
        $Call_id = $data['Call_id'];
        $Ticket_id = $data['Ticket_id'];
        $Phone_Number = $data['Phone_Number'];
        $AgentID = $data['AgentID/Name'];
        $Extension_Number = $data['Extension_Number'];
        $customer_email = $data['customer_email'];
        $customer_name = $data['customer_name'];
        $Call_Time = date("Y-m-d H:i:s");
        $d_requestTime = date("Y-m-d H:i:s");
        
        $sql_qry = "INSERT INTO $db.tbl_survey_request (Type, Call_id, Ticket_id, Phone_Number ,Call_Time, AgentID_Name , Extension_Number,d_requestTime,customer_name,customer_email) VALUES ('{$Type}','{$Call_id}','{$Ticket_id}','{$Phone_Number}','{$Call_Time}','{$AgentID}','{$Extension_Number}','{$d_requestTime}','{$customer_name}','{$customer_email}')";
        //  echo $sql_qry;
        mysqli_query($link, $sql_qry);
        $last_id = mysqli_insert_id($link);
        $id = base64_encode($last_id);
    
        return $SiteURL."CRM/feedback.php?".$id;
    }
}
function sms_email_template($case_type, $data_arr=[], $assign_to = 'false'){
    global $link,$db;
    
    $sql_sms="select * from $db.tbl_smsformat where smsstatus=1 AND smstemplatename='$case_type'"; 
    $qsms = mysqli_query($link,$sql_sms)or die(mysqli_error($link));
    $rowSms = mysqli_fetch_array($qsms);
    $header = $rowSms['smsheader'];
    $footer = $rowSms['smsfooter'];
    $body = $rowSms['smsbody'];
    $feedback_link = $data_arr['chat_feedback_link'];
    

	// added code for short url link for sms [vastvikta][26-02-2025]
    $short_url_feedback_link = shortenUrl($feedback_link);

    $fname = $data_arr['name'];
    $caller_id = $data_arr['caller_id'];

    $header = str_replace("%customer%", $fname, $header);
    $body = str_replace("%chat_feedback_link%", $short_url_feedback_link, $body);
    $content = $header.','.$body.$footer;

    $sql_sms_feed="insert into $db.sms_out_queue (send_to,message,create_date,AccountName) values ('$caller_id','$content',NOW(),'$fname')";
    $result = mysqli_query($link,$sql_sms_feed) ;
    if ($result) {
        // echo "Insertion successful!";
    } else {
        echo "Error in Query: " . mysqli_error($link);
    }
     /*----end sms code---------*/
}


function delete($chat_session_id) {
    global $db, $link;  // assuming $db and $link are your database connection variables
    
    // SQL query to delete the entry
    $sql = "DELETE FROM $db.bot_chat_session WHERE chat_session = '$chat_session_id'";
    mysqli_query($link,$sql) or die("Error In Query23 ".mysqli_error($link));

}

//added the code to  store the attachment in the file [vastvikta nishad][07-12-2024]m
function save_wa_out_queue($conversation_id, $message, $type = 'text', $bot_chat_session = '', $agent_id = '', $file_path = '') {
    global $db;

    if (empty($file_path)) {
        $file_path = '';
    }

    $insert = array(
        'customer_id' => $conversation_id,
        'message' => $message,
        'agent_id' => $agent_id,
        'direction' => 'OUT',
        'create_datetime' => date('Y-m-d H:i:s'),
        'chat_session_id' => $bot_chat_session,
        'attachment' => $file_path,
    );

    

    // This will store the message in the WhatsApp out queue.
    $insert_id = insert_data('in_out_data', $insert);
    return $insert_id;
}
// function for inserting  last message for closing chat 
function insert_data($table, $insert) {
    global $db, $link;  
    // Prepare the SQL query
    $sql = "INSERT INTO $db.$table (customer_id, message, agent_id, direction, create_datetime, chat_session_id, attachment) 
            VALUES (
                '{$insert['customer_id']}', 
                '{$insert['message']}', 
                '{$insert['agent_id']}', 
                '{$insert['direction']}', 
                '{$insert['create_datetime']}', 
                '{$insert['chat_session_id']}', 
                '{$insert['attachment']}'
            )";

    // Execute the query
    if (mysqli_query($link, $sql)) {
        $inserted_id = mysqli_insert_id($link);
        return $inserted_id;  // Return the inserted ID
    } else {
        return false;
    }
}
function get_agent_name($agent_id) {
    global $db, $link;
    
    // Prepare the SQL query
    $query = "SELECT AtxUserName FROM uniuserprofile WHERE AtxUserID = '$agent_id'";
    
    // Execute the query
    $result = mysqli_query($link, $query);
    
    // Check if the query executed successfully
    if ($result) {
        // Fetch the result
        $row = mysqli_fetch_assoc($result);
        
        // Return the agent name if found, otherwise return null
        return $row ? $row['AtxUserName'] : null;
    } else {
        // Log error in case of query failure
        error_log("Query Error: " . mysqli_error($link));
        return null;
    }
}

function close_chat() {
    global $db,$link; // Ensure $db is globally available

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $agent_id = $_SESSION['userid'];
   
    $agent_name = get_agent_name($agent_id);
    
    $chat_session_id = isset($_POST['chat_session_id']) ? $_POST['chat_session_id'] : 0;

    // Fetch the conversation from the database using $db
    $res = get_where($chat_session_id);
    // function to fetch ticket id if case created during chat 
    $Ticket_id = get_ticket_id($chat_session_id);
    $email = $res['email'];

    $phone_new = $res['from'];
  
    if (!empty($email)) {
        include("../chat_function.php");

        $feedback_data = array(
            'createdBy' => 'feedback',
            'Type' => '4', // Changed type for feedback by chat
            'Call_id' => $phone_new,
            'Ticket_id' => $Ticket_id,
            'Phone_Number' => $phone_new,
            'AgentID/Name' => $agent_name,
            'Extension_Number' => '',
            'customer_email' => $email,
            'customer_name' => $res['conversation_id']
        );

        $feedback_link = create_feedbacklink($feedback_data);
        $companyId = $_SESSION['companyid'];
        $data_array = array(
            
            'chat_feedback_link' => $feedback_link . '&Type=4&company_id=' . $companyId ,
            'email' => $res['email'],
            'name' => $res['name'],
            'caller_id' => $res['from']
        );

        $case_type = 'chat_close_feedback';
   
        sms_email_template($case_type, $data_array, false);
        // added code for sending the chat in txt format to the customer  as well as feedback through email
        $insertResult = insert_emailinformationout($email, $id,$chat_session_id,$data_array,$case_type);
        
    }
    if (!empty($id)) {
    delete($chat_session_id);

        save_wa_out_queue($id, 'Agent has closed this session, Thank you  and have a nice day', 'OUT', $chat_session_id, $agent_id);

        echo json_encode(array("status" => "success", "msg" => "chat closed"));
        die();
    } else {
        echo json_encode(array("status" => "fail", "msg" => "failed to close"));
        die();
    }
}
// function to  fetch the  active users 
function get_user_list() {
    global $db, $link, $chat_user_id;

    // Prepare SQL query to fetch the list of chat sessions
    $sql = "SELECT * FROM $db.bot_chat_session WHERE chat_session != '' ORDER BY session_start_time, id DESC";
    
    // Execute the query
    $result = $link->query($sql);
    
    // Check if query was successful and there are results
    if ($result->num_rows > 0) {
        $output = '<ul id="userList" class="users">';
        
        // Loop through the result set
        while ($row = $result->fetch_assoc()) {
            $isSelected = ($chat_user_id == $row['id']) ? 'selected' : '';
            $statusClass = ''; // Add logic here if you have a specific status for users

            // Generate list item HTML for each user
            $output .= '<li class="person ' . $isSelected . '" data-chat="person" data-phone="' . $row['phone'] . '" data-user-id="' . $row['conversation_id'] . '" data-session-id="' . $row['chat_session'] . '" data-user-name="' . htmlspecialchars($row['name']) . '">
                            <div class="user">
                                <img src="multi_chat/user2.png" alt="' . htmlspecialchars($row['name']) . '">
                                <span class="' . $statusClass . '"></span>
                            </div>
                            <p class="name-time">
                                <span class="name">' . htmlspecialchars($row['name']) . '</span>
                            </p>';

                            // Append badge if applicable
                            $sqls = "SELECT COUNT(*) AS total FROM $db.`in_out_data` WHERE direction='IN' and flag='0' AND chat_session_id = '" . $row['chat_session'] . "'";
                            $response = mysqli_query($link, $sqls) or die(mysqli_error($link));
                            $rowCount = mysqli_fetch_array($response);
                            if ($rowCount['total'] > 0) {
                                $output .= '<span class="badge badge-light" style="margin: 10px;">' . $rowCount['total'] . '</span>';
                            } else {
                                $output .= '<span class="badge badge-light" style="margin: 10px;"></span>';
                            }
                            $output .= '</li>';
        }
        
        $output .= '</ul>';
    } else {
        $output = '<p style="margin-left: 120px;">No Active Users</p>';
    }

    // Return the generated HTML
    return $output;
}

// added code for short url link for sms [vastvikta][26-02-2025]
function shortenUrl($longUrl) {
    $apiUrl = "https://tinyurl.com/api-create.php?url=" . urlencode($longUrl);
    $shortUrl = file_get_contents($apiUrl);
    return $shortUrl;
}
?>