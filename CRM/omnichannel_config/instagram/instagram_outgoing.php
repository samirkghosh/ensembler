<?php
/**
 * Social Media Channel instagram
 * Author: Aarti Ojha
 * Date: 18-11-2024
 * Description: This file handles Social Media API Data for Outgoing and Incoming Response Store in the database.
 *              It fetches instagram incoming data via cURL hit using instagram APIs and
 *              sends instagram outgoing data via cURL hit using instagram APIs.
 * 
 * Please do not modify this file without permission.
 */

global $webook_instagram_path,$attchment_instagram_path;
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file // Include database connection file 
include_once('/var/www/html/ensembler/CRM/omnichannel_config/script_common_file.php'); // for mail send and curl hit common fun added

// Add code for logging code
include_once("/var/www/html/ensembler/logs/config.php");
include_once("/var/www/html/ensembler/logs/logs.php");

global $log_prefix,$type;
$log_prefix = "[instagram Script]";
$type = 'instagram';

// Master database
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to get the related database name
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0){
    while ($company = $results->fetch_assoc()) {
        $childdb = $company['related_database_name'];

        echo " ############### Company Database Name : ".$childdb; echo"<br/>";

        instagram_incoming_data($childdb); //For outgoing Data 
    }
}else{
    echo "No company databases found."; die;
}

/**
 * Fetches Facebook Messager outgoing data
*/
function instagram_incoming_data($childdb){
    global $link,$childdb,$log_prefix,$type,$base_path;
    /* facebook access token and url form database */
    $sql_cdr= "SELECT * from $childdb.tbl_instagram_connection where status=1 and debug=1 ";
    $query=mysqli_query($link,$sql_cdr);
    $config = mysqli_fetch_array($query);

    if (!$query){
        if (__DBGLOG__){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix DB fetching connection error tbl_instagram_connection: $sql_cdr". mysqli_error($link),'omichannel');
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "$log_prefix DB fetching connection error tbl_instagram_connection: $sql_cdr";
        echo json_encode($response);
        sendErrorEmail($status_response,$type); // for send error mail -script_common.php
    }

    $page_access_token = $config['access_token']; // Replace with your access token
    $global_url = $config['instagram_url']; // get facebook url from table
    $page_id = $config['app_id'];

    echo"<br/>";echo '================Start Messager code======================';echo"<br/>";
    print_r($page_access_token);echo"<br/>";
    
    /* getting list of outgoint flag baises in database */
    $sql_cdr = "SELECT * FROM $childdb.instagram_out_queue WHERE status= '0'";
    $query=mysqli_query($link,$sql_cdr);
    $count = mysqli_num_rows($query);
print_r($sql_cdr);
    if($count != 0 ){
      while($data=mysqli_fetch_assoc($query)){
            echo"<br/>";echo '================  Message SEND START ================';echo"<br/>";

            $recipientId = $data['send_to']; // Replace with the actual post comment ID
            $messageText = $data['message']; // Your comment text here

            $attachmentUrl = $data['attachment']; // Attachment URL (if any)
            $id = $data['id'];
            $url = $global_url.$page_id.'/messages?access_token='.$page_access_token;

            if ($attachmentUrl) {

                $attachmentUrl = $base_path . $attachmentUrl; // URL to the image file
                echo $attachmentUrl;
                $attachmentUrl = processFileName($attachmentUrl,$id,$type,$childdb);
                // Check file size before uploading
                if (!checkFileSize($attachmentUrl)) {
                    echo "File size exceeds the limit.<br/><br/>";
                    continue;
                }

                // Upload the attachment and get the attachment ID
                $attachmentType = GetFileType($attachmentUrl);               
                // $attachmentId = uploadAttachmentToFacebook($attachmentUrl, $attachmentType, $page_access_token,$page_id,$global_url);

                // if ($attachmentUrl) {
                    $messageData['message'] = [
                        'attachment' => [
                            'type' => $attachmentType,  // Attachment type (image, video, file)
                            'payload' => [
                                // 'url' => 'https://ensembler.com/logo.png',
                                'url' => $attachmentUrl,
                            ]
                        ]

                    ];
                    $messageData['recipient']['id'] = $recipientId;
                    if ($messageText) {
                        $messageData['message']['text'] = $messageText;
                    }
                    // $messageData['messaging_type'] = 'MESSAGE_TAG';
                    // $messageData['tag'] = 'POST_PURCHASE_UPDATE';
                // }
                $json_message_array = json_encode($messageData);
                $response = curlhit($url, $json_message_array,$page_access_token); //this function script_common.php files inside    
               print_r($messageData);echo"<br/>";echo"<br/>";
            } else {
                $messageData = [
                    'recipient' => [
                        'id' => $recipientId
                    ],
                    // 'messaging_type'=> 'MESSAGE_TAG',
                    // 'tag'=> 'POST_PURCHASE_UPDATE'
                ];
                print_r($messageData);echo"<br/>";echo"<br/>";
                if (!empty($messageText)) {
                    // Split message if needed
                    $messagess = splitMessage($messageText);
                    foreach ($messagess as $message_part) {    
                    // echo"<br/><br/><br/>";print_r($message_part);    echo"<br/><br/><br/>";             
                        $messageData['message']['text'] = $message_part;
                        // Convert the message part to UTF-8
                        $message_part = mb_convert_encoding($message_part, 'UTF-8', 'auto');
                        // Set the message part in the array
                        $messageData['message']['text'] = $message_part;
                        // Convert the whole message array to UTF-8 before encoding to JSON
                        array_walk_recursive($messageData, function(&$item) {
                            $item = mb_convert_encoding($item, 'UTF-8', 'auto');
                        });
                        // Convert to JSON with UTF-8 support
                        $json_message_array = json_encode($messageData, JSON_UNESCAPED_UNICODE);
                        // Send via curl
                        $response = curlhit($url, $json_message_array,$page_access_token); //this function script_common.php files inside                  
            }
                }
            
            }
            echo"<br/>";echo"<pre> Response: ";print_r($response); echo"<pre>";

            if($response["error"][0]["message"] = "") {
                // Comment posting failed, handle the error.
                echo"<br/>";echo "Error posting comment: " . $response;
                echo"<br/>";echo "<h3>Sorry, there was a problem.</h3><p>facebook returned the following error message:</p><p><em>".$response["error"][0]["message"]."</em></p>";
              // exit();
            }else{
                // Comment was posted successfully, and $response_data['id'] contains the comment ID.
                echo"<br/>";echo "Comment posted with ID: " . $response['message_id'];
                $comment_id = $response["message_id"];
            }

            // die;
            if(!empty($comment_id)){
                $sent_date = date("Y-m-d H:i:s");
                /*send replay sucessfully*/
                $strQrytest ="update $childdb.instagram_out_queue set message_unique_id='".$comment_id."',update_date='".$sent_date."',status='1' where id=".$id; // store commnet_id and send date also send flag 1
                echo"<br/>";echo"<br/>";echo $strQrytest;echo"<br/>";echo"<br/>";

                mysqli_query($link, $strQrytest);
            }else{
                $sent_date = date("Y-m-d H:i:s");
                // Change for store error response[Aarti][13-08-2024]
                $status_response = $response["error"]['message'];
                echo"<br/>";echo "<h3>Sorry, there was a problem.</h3><p>facebook returned the following error message:</p><p><em>".$response["error"][0]["message"]."</em></p>";
                $status_response = addslashes($status_response);
                $strQrytest ="update $childdb.instagram_out_queue set update_date='".$sent_date."',status_response='".$status_response."',status='2' where id=".$id;
                echo"<br/>";echo"<br/>";echo $strQrytest;echo"<br/>";echo"<br/>";

                mysqli_query($link, $strQrytest);
                if (__DBGLOG__){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix facebook returned the following error message: $response",'omichannel');
                }
                $response['error'] = TRUE;
                $response['error_msg'] = "$log_prefix facebook returned the following error message: $status_response";
                echo json_encode($response);
                // sendErrorEmail(json_encode($response),$type); // for send error mail -script_common.php
            }
            // die;
            echo"<br/>";echo '================End facebook code======================';echo"<br/>";
        }
    }
}
// Messenger Attachement Upload and get attachment url assign with message curl hit 
function uploadAttachmentToFacebook($image_url, $attachmentType, $page_access_token,$page_id,$global_url) {
    global $log_prefix,$type;
    $url = $global_url.$page_id.'/message_attachments/';
    // Download the image to a temporary file

    $temp_image_path = '/tmp/' . basename($image_url);
    file_put_contents($temp_image_path, file_get_contents($image_url));
     // Check if the file has been downloaded
    if (!file_exists($temp_image_path)) {
        echo "Failed to download the file.";
        return;
    }
    // Determine the MIME type of the file manually
    $file_extension = pathinfo($temp_image_path, PATHINFO_EXTENSION);
    $mime_type = 'application/octet-stream'; // Default MIME type
    switch ($file_extension) {
        case 'pdf':
            $mime_type = 'application/pdf';
            break;
        case 'jpg':
        case 'jpeg':
            $mime_type = 'image/jpeg';
            break;
        case 'png':
            $mime_type = 'image/png';
            break;
        case 'PNG':
            $mime_type = 'image/png';
            break;
        case 'webp':
            $mime_type = 'image/webp';
            break;
        case 'xml':
            $mime_type = 'application/xml';
            break;
        case 'csv':
            $mime_type = 'text/csv';
            break;
        case 'xlsx':
            $mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            break;
        case 'txt':
            $mime_type = 'text/plain';
            break;
        case 'doc':
            $mime_type = 'application/msword';
            break;
        case 'docx':
            $mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            break;
        case 'ppt':
            $mime_type = 'application/vnd.ms-powerpoint';
            break;
        case 'pptx':
            $mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            break;
        // Add other cases as needed
    }
    $curl = curl_init();
    // Prepare the data for the CURL request
    $data = [
        'message' => json_encode([
            'attachment' => [
                'type' => $attachmentType,
                'payload' => [
                    'is_reusable' => true
                ]
            ]
        ]),
        'filedata' => new CURLFile($temp_image_path, $mime_type, basename($image_url))
    ];
    echo 'image temp_image_path'.$temp_image_path;
    echo"<pre>";print_r(json_encode($data)); echo"<br/><br/>"; 

    // Prepare the data for the CURL request
   

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data, // Do not encode as JSON, send as multipart/form-data
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $page_access_token,
            'Content-Type: multipart/form-data' // Ensure correct Content-Type
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo"<br/>";print_r($response); echo"<br/>";
    // Remove the temporary file
    unlink($temp_image_path);
    $response = json_decode($response, true);
    if(isset($response['attachment_id'])) {
        return $response['attachment_id'];
    } else {
        echo "Error uploading attachment: " . $response['error']['message'];
        // For log create and send error mail notification
        if (__DBGLOG__){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Failed to upload media.: $response",'omichannel');
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "$log_prefix Failed to upload media.: $response";
        echo json_encode($response);
        // sendErrorEmail($response,$type); // for send error mail -script_common.php
        return false;
    }
}
// This function fetches the attachment type based on the file extension
function GetFileType($attachmentUrl){
    // Determine the file extension
    $file_extension = strtolower(pathinfo($attachmentUrl, PATHINFO_EXTENSION));
    $attachment_type = 'file'; // Default type for unknown files

    switch ($file_extension) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'webp':
        case 'gif': // Added gif as well
            $attachment_type = 'image';
            break;
        case 'mp4':
        case 'mov':
        case 'avi':
        case 'wmv':
            $attachment_type = 'video';
            break;
        case 'pdf':
        case 'doc':
        case 'docx':
        case 'ppt':
        case 'pptx':
        case 'xlsx':
        case 'csv':
        case 'txt':
        case 'xml':
            $attachment_type = 'file';
            break;
        // Add more cases as needed for other types
    }
    return $attachment_type;
}
?>