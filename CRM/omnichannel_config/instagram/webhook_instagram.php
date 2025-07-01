<?php
/***
    * instagram Incoming Message
    * Author: Aarti Ojha
    * Date: 09-05-2024
    * This file handles instagram Incoming Message 
    * To integrate instagram messaging in your PHP application using the Facebook Business API, you need to follow several steps, including setting up your Facebook Developer account, creating a instagram Business Account, configuring your webhook, and handling incoming and outgoing messages
    * 
    * Please do not modify this file without permission.
**/
global $webook_instagram_path;

error_log('########################### instagram WebHook Data Store1 ##################');
$verify_token = "ensembler_TOKEN"; // Set this to any random string
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['hub_verify_token'] === $verify_token) {
        echo $_GET['hub_challenge'];
    }
}

// $json = '{"object":"instagram","entry":[{"time":1744789255994,"id":"17841470945980483","messaging":[{"sender":{"id":"613556994653795"},"recipient":{"id":"17841470945980483"},"timestamp":1744789255335,"message":{"mid":"aWdfZAG1faXRlbToxOklHTWVzc2FnZAUlEOjE3ODQxNDcwOTQ1OTgwNDgzOjM0MDI4MjM2Njg0MTcxMDMwMTI0NDI3NjAyOTk1MTcyODQ1MTg0MzozMjE4NTY4MDg1NTcyOTI0NzkxNDY1NDI5OTY5OTgwNjIwOAZDZD","text":"good afternoon mam"}}]}]}';

$messages = json_decode(file_get_contents('php://input'),true);
// $messages = json_decode($json,true);

// Define the file path
$filePath = 'logfile.txt'; // Replace with your desired file path

// Convert the data to a JSON string
$jsonData = json_encode($messages, JSON_PRETTY_PRINT);
file_put_contents($filePath, $jsonData . PHP_EOL, FILE_APPEND);


// // Handle incoming webhook data
// $messages = json_decode($messages, true);

include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file
include_once('/var/www/html/ensembler/CRM/omnichannel_config/script_common_file.php'); // for mail send and curl hit common fun addedand curl hit common fun added

// Add code for logging code
include_once("logs/config.php");
include_once("logs/logs.php");

global $log_prefix,$type;
$log_prefix = "[instagram Script]";
$type = 'instagram';

error_log('########################### instagram WebHook Data Store2 ##################');
// $messages = json_decode($messages,true);

error_log('########################### instagram WebHook Data Store3 ##################');

// Check if JSON decoding was successful
if ($messages === null && json_last_error() !== JSON_ERROR_NONE) {
    // JSON decoding failed
    error_log("Error decoding JSON: " . json_last_error_msg());
} else {
    // Assuming the rest of your code for handling and storing the data goes here
    date_default_timezone_set("Africa/Lusaka");

    // // Check connection
    if(!$link){
        echo "Database connection error".mysqli_connect_error();
        DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Database connection error: $sql_cdr". mysqli_connect_error(),'omichannel');
    }
    // Assuming $input contains an array of WhatsApp messages
    if (isset($messages['object']) && $messages['object'] == 'instagram') {
        foreach ($messages['entry'] as $entry) {
            foreach ($entry['messaging'] as $messaging) {

                if (isset($messaging['message'])) {

                    // Extract message details
                    $send_from =  $messaging['sender']['id'];

                    $recipient_id = $messaging['recipient']['id'];
                    if(isset($messaging['message']['text'])){
                        $message_text = $messaging['message']['text'] ?? '';
                    }else{
                        $message_text = '';
                    }

                    $mid = $messaging['message']['mid'];

                    $timestamp = $messaging['timestamp'];

                    $mediaId = $entry['time'];

                    if(isset($messaging['message']['attachments'])){

                        foreach ($messaging['message']['attachments'] as $attachment) {
                            // Assuming the media ID and SHA-256 hash are included in the webhook data
                           
                            $attachment_type = $attachment['type'];
                            $attachment_url = $attachment['payload']['url'];

                            storeIncomingMessage($recipient_id,$send_from,$message_text,$timestamp, $attachment_url, $attachment_type,$mid,$mediaId);
                        }
                    }else{
                         storeIncomingMessage($recipient_id,$send_from, $message_text,$timestamp, null, null,$mid,$mediaId);
                    }                        
                }
            }
        }
    }else{
        error_log(" No record found ");
        echo "<br/> No record found";
    }
    // Close database connection
}

error_log('########################### WhatsApp WebHook Data Store End  ##################');

function storeIncomingMessage($recipient_id, $send_from, $message_text,$timestamp,$attachment_url, $attachment_type,$mid,$mediaId){
    global $db,$link,$log_prefix,$type,$instagram_path;
    $db = 'ensembler';  // for Crm module
    if(!empty($attachment_url)){
        $directoryPath = createDateWiseDirectory(); 
        // Download and save the image
        $filename = downloadImage($directoryPath,$attachment_url,$mediaId);
        $filenamepath = BasePathStorage.'/'.$instagram_path.$filename;
        echo "Image received and saved successfully.: ".$filenamepath;
    }else{
        $filename = '';
    }
    print_r($timestamp);
    $timestamp = date('Y-m-d H:i:s', $timestamp/1000); // Convert Unix timestamp to MySQL datetime
echo "<br/>";
    print_r($timestamp);
    $message_id = $mid;
    $message_text = addslashes($message_text);
    
    if(!empty($message_text) || !empty($filename)){
        // Insert message into database
        $sql = "INSERT INTO $db.instagram_in_queue (send_to,send_from,message, create_date,message_unique_id,status,flag,attachment) VALUES ('$recipient_id','$send_from','$message_text', '$timestamp','$message_id','0','0','$filenamepath')";
      
        echo "<br/>"; echo $sql; echo "<br/>"; 
        if (mysqli_query($link,$sql) === TRUE) {

            $interact_id = mysqli_insert_id($link);
             // updated code for insertion in interaction table [vastvikta][15-04-2025]
            $isexist = get_user_list($send_from);
            if (!empty($isexist)) {
                $customerid = $isexist['AccountNumber']; // Get customer_id from the result
                $sql_new = "INSERT INTO $db.interaction (
                            caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type
                        ) VALUES (
                            '', 'instagram', '', '$send_from', '', '$interact_id', '$customerid', '$message_text', '', NOW(), 'IN'
                        )";
                $result_mess = mysqli_query($link, $sql_new) or die("Error In Query of interaction insertion " . mysqli_error($link));
            }
            // code ends here

            echo "Message inserted successfully";
            error_log("Message inserted successfully");
        } else {
            echo "Error inserting message";
            error_log("Error inserting message: " . $link->error);

            if (__DBGLOG__){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix DB fetching connection error instagram_in_queue: $sql". mysqli_error($link),'omichannel');
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "$log_prefix DB connection error instagram_in_queue: $sql";
            // sendErrorEmail(json_encode($response),$type); // for send error mail -script_common.php
        }
    }
}
function get_user_list($send_from){
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
// Function to download and save the image, handling redirections
function downloadImage($directoryPath,$attachment_url,$mediaId) {
    global $log_prefix,$type;
    // Download the actual media content
    if (isset($attachment_url)) {
        echo "<br/>"; print_r($attachment_url); echo "<br/>";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $attachment_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HEADER => true, // Include headers in the output
          CURLOPT_HTTPHEADER => array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive'
          ),
        ));
        // Execute the cURL request and get the response
            // Execute the cURL request and get the response
        $response = curl_exec($curl);
        // Check if any error occurred
        if(curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
            error_log('cURL error: ' . curl_error($curl));
            curl_close($curl);
            exit;
        }
        // Get the HTTP response code
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Get the content type
        $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        // Get content disposition header if available
        $content_disposition = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($curl);

        print_r($header);
        // Check if the request was successful and content type is an image
        $filename = 'messenger_' . $mediaId;
        $extension = '';

        if (preg_match('/^Content-Type:\s*(.*)$/mi', $header, $matches)) {
            // Determine file extension based on content type
            switch ($content_type) {
                case 'image/jpeg':
                    $extension = '.jpg';
                    break;
                case 'image/png':
                    $extension = '.png';
                    break;
                case 'image/gif':
                    $extension = '.gif';
                    break;
                case 'image/bmp':
                    $extension = '.bmp';
                    break;
                case 'image/webp':
                    $extension = '.webp';
                    break;
                case 'video/mp4':
                    $extension = '.mp4';
                    break;
                case 'video/mpeg':
                    $extension = '.mpeg';
                    break;
                case 'audio/mpeg':
                    $extension = '.mp3';
                    break;
                case 'audio/ogg':
                    $extension = '.ogg';
                    break;
                case 'application/pdf':
                    $extension = '.pdf';
                    break;
                case 'application/msword':
                    $extension = '.doc';
                    break;
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    $extension = '.docx';
                    break;
                case 'application/vnd.ms-excel':
                    $extension = '.xls';
                    break;
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    $extension = '.xlsx';
                    break;
                case 'application/vnd.ms-powerpoint':
                    $extension = '.ppt';
                    break;
                case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                    $extension = '.pptx';
                    break;
                case 'text/plain':
                    $extension = '.txt';
                    break;
                case 'text/html':
                    $extension = '.html';
                    break;
                case 'application/zip':
                    $extension = '.zip';
                    break;
                case 'application/x-rar-compressed':
                    $extension = '.rar';
                    break;
                    // Add more cases as needed
            }
        }
        
        $filename .= $extension;
        
        // Define the path to save the image
        $imagePath = $directoryPath . '/' . $filename;
            // Save the response to a file
            print_r($filename);;echo"</br></br></br>";
            print_r($imagePath);

            if (file_put_contents($imagePath, $body)) {
                echo "Image saved successfully to " . $imagePath;
                chmod($imagePath, 0666); // Set file permissions to 0666
                $date = date('dmy');
                $filename = $date.'/'.$filename;
                return $filename; 
                exit();
            } else {
                $textt = "Failed to save the file: $http_code";
                echo $textt;
                if (__DBGLOG__){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Failed to save the file: $http_code",'omichannel');
                }
                // sendErrorEmail($textt,$type); // for send error mail -script_common.php
                return null;
            }
    } else {
        $textt = "Failed to download media. HTTP Code: $http_code";
        echo $textt;
        if (__DBGLOG__){
            DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Failed to download media. HTTP Code: $http_code",'omichannel');
        }
        // sendErrorEmail($textt,$type); // for send error mail -script_common.php
        return null;
    }
}
// Function to create a date-wise directory and return the path
function createDateWiseDirectory() {
    global $webook_instagram_path;
    $date = date('dmy'); // Get the current date in ddmmyy format
    $directoryPath = $webook_instagram_path . $date; // Directory path
    // Check if the directory already exists
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0777, true); // Create the directory with 0777 permissions
        chmod($directoryPath, 0777); // Ensure the directory has 0777 permissions
    }
    print_r($directoryPath);
    return $directoryPath;
}
?>
