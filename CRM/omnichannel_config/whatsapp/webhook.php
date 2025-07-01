<?php
/***
    * WhatsApp Incoming Message
    * Author: Aarti Ojha
    * Date: 09-05-2024
    * This file handles WhatsApp Incoming Message 
    * To integrate WhatsApp messaging in your PHP application using the Facebook Business API, you need to follow several steps, including setting up your Facebook Developer account, creating a WhatsApp Business Account, configuring your webhook, and handling incoming and outgoing messages
    * 
    * Please do not modify this file without permission.
**/

error_log('########################### WhatsApp WebHook Data Store1 ##################');
$verify_token = "whatsapp_TOKEN"; // Set this to any random string
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['hub_verify_token'] === $verify_token) {
        echo $_GET['hub_challenge'];
    }
}
// $json = '{"object":"whatsapp_business_account","entry":[{"id":"511922062007891","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"919220411572","phone_number_id":"543360945523071"},"contacts":[{"profile":{"name":"Ashu Ankit Singh"},"wa_id":"917678667178"}],"messages":[{"from":"917678667178","id":"wamid.HBgMOTE3Njc4NjY3MTc4FQIAEhgUM0YxOTU4OUFCREI4NUZBMDVERjcA","timestamp":"1746708021","type":"document","document":{"filename":"photo.jpg","mime_type":"image/jpeg","sha256":"BsOyVao1qapckJ25O8048T6igdokjchaZUSZwjitnxY=","id":"1059050459611576"}}]},"field":"messages"}]}]}';

$messages = json_decode(file_get_contents('php://input'),true);
// $messages = json_decode($json,true);


// Define the file path
$filePath = 'logfile.txt'; // Replace with your desired file path

// Convert the data to a JSON string
$jsonData = json_encode($messages, JSON_PRETTY_PRINT);
file_put_contents($filePath, $jsonData . PHP_EOL, FILE_APPEND);

include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file
include_once('/var/www/html/ensembler/CRM/omnichannel_config/script_common_file.php'); // for mail send and curl hit common fun added

// Add code for logging code
include_once("logs/config.php");
include_once("logs/logs.php");

global $log_prefix,$type;

global $whatsapp_path_out;
$whatsapp_path_out = '/var/www/html/unistorage/2224/omnichannel_config/whatsapp_attachemnts/';

$log_prefix = "[WhatsApp Script]";
$type = 'whatsapp';

error_log('########################### WhatsApp WebHook Data Store2 ##################');
error_log($messages);
// $messages = json_decode($messages,true);
error_log('########################### WhatsApp WebHook Data Store3 ##################');
// Check if JSON decoding was successful
if ($messages === null && json_last_error() !== JSON_ERROR_NONE) {
    // JSON decoding failed
    error_log("Error decoding JSON: " . json_last_error_msg());
} else {
    // Assuming the rest of your code for handling and storing the data goes here
    
    // // Check connection
    if(!$link){
        echo "Database connection error".mysqli_connect_error();
        DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Database connection error: $sql_cdr". mysqli_connect_error(),'omichannel');
    }
     $db = 'ensembler';  // for Crm module
     $agentid = $_SESSION['userid'];
    // Assuming $input contains an array of WhatsApp messages
    if (!empty($messages['entry'])) {
        foreach ($messages['entry'] as $entry) {

            $messages_value =  $entry['changes'][0]['value'];
            $send_to = $messages_value['metadata']['display_phone_number'];

            foreach ($messages_value['messages'] as $messaging) {
                // Extract message details
                $send_from = $messaging['from'];
                $message_id = $messaging['id'];
                if(isset($messaging['image']['sha256']) || isset($messaging['document']['sha256'])){
                    // Assuming the media ID and SHA-256 hash are included in the webhook data
                    if(isset($messaging['image'])){
                        $mediaId = $messaging['image']['id'];
                        $imageHash = $messaging['image']['sha256'];
                         $file_orignal_name = ''; // for image name not getting 
                    }else{
                        $mediaId = $messaging['document']['id'];
                        $imageHash = $messaging['document']['sha256'];
                         $file_orignal_name = $messaging['document']['filename'];
                    }
                   
                    if(!empty($mediaId) && !empty($imageHash)){
                        echo "<pre>";
                        /* whatsapp access token and url form database */
                        $sql_cdr= "SELECT * from $db.tbl_whatsapp_connection where status=1 and debug=1 ";
                        $query=mysqli_query($link,$sql_cdr);
                        $config = mysqli_fetch_array($query);
                         // Replace with your access token
                        $accessToken = $config['access_token'];
                        // Create date-wise directory
                        $directoryPath = createDateWiseDirectory(); 
                        // Download and save the image
                        echo"<br/>";
                        print_r($mediaId);
                        echo"<br/>";
                        print_r($file_orignal_name);
                        echo"<br/>";
                         print_r($directoryPath);
                         echo"<br/>";
                        $filename = downloadImage($mediaId, $accessToken,$file_orignal_name,$directoryPath);
                         print_r($filename);
                        echo "i am here";
                        $filenamepath = $whatsapp_path_out.'/'.$whatsapp_path.$filename;
                        print_r($filenamepath);
                        echo "Image received and saved successfully.";
                    }
                    if(isset($messaging['image']['caption'])){
                        $message_text = $messaging['image']['caption'];
                    }
                }else{
                    $message_text = $messaging['text']['body'];
                }

                $user_name = addslashes($messages_value['contacts'][0]['profile']['name']); // for fetch username and store in table
                $timestamp = date('Y-m-d H:i:s', $messaging['timestamp']);

                print_r($filenamepath);
                if(!empty($message_text) || !empty($filename)){ // for check message or file condition data added in table [Aarti][30-07-2024]
                    // Insert message into database
                    $message_text = addslashes($message_text);
                    $sql = "INSERT INTO $db.whatsapp_in_queue (send_to,send_from,message, create_date,message_unique_id,status,flag,attachment,user_name) VALUES ('$send_to','$send_from','$message_text', '$timestamp','$message_id','0','0','$filenamepath','$user_name')";
                   echo $sql;
                    $result = $link->query($sql);
                    $interact_id = mysqli_insert_id($link);
            
                    // updated code by [vastivkta][14-04-2025]    for insertion in  interaction table                
                    
                    // check against all number with or without 91 
                    if (preg_match('/^91(\d{10})$/', $send_from, $matches)) {
                        // Case 1: 12-digit number starting with 91
                        $send_from_new = $matches[1]; // Just the 10-digit number
                    } elseif (preg_match('/^\d{10}$/', $send_from)) {
                        // Case 2: Only 10-digit number
                        $send_from_new = '91' . $send_from;
                    } 
                    
                    // FUNCTION to check if the user exists in the  web accounts table or not 
                    $isexist = get_user_list($send_from, $send_from_new);

                    
                    if (!empty($isexist)) {
                        $customerid = $isexist['AccountNumber']; // Get customer_id from the result

                        $sql = "INSERT INTO $db.interaction (
                                    caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type,created_by
                                ) VALUES (
                                    '', 'Whatsapp', '', '$send_from', '', '$interact_id', '$customerid', '$message_text', '', NOW(), 'IN' , '$agentid'
                                )";

                        $result_sms = mysqli_query($link, $sql) or die("Error In Query of interaction insertion " . mysqli_error($link));
                    }
                    //  code ends here 
                    if ($result === TRUE) {
                        echo "Message inserted successfully";
                        error_log("Message inserted successfully");
                    } else {
                        echo "Error inserting message";
                        error_log("Error inserting message: " . $link->error);
                        if (__DBGLOG__){
                            DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix DB fetching connection error tbl_whatsapp_connection: $sql". mysqli_error($link),'omichannel');
                        }
                        $response['error'] = TRUE;
                        $response['error_msg'] = "$log_prefix DB connection error whatsapp_in_queue: $sql";
                        // sendErrorEmail(json_encode($response),$type); // for send error mail -script_common.php
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
// function defined for fetching the data of the existing user [vastvikta][14-04-2025]
function get_user_list($send_from, $send_from_new) {
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

error_log('########################### WhatsApp WebHook Data Store End  ##################');
// Function to download and save the image, handling redirections
function downloadImage($mediaId, $accessToken,$file_orignal_name,$directoryPath) {
    global $path,$log_prefix,$type;
    $url = "https://graph.facebook.com/v21.0/{$mediaId}";
    // Initialize a cURL session
    print_r($url);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$accessToken}"
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    // Execute the cURL request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // Debug output

    echo"<br/>";print_r($accessToken);
    echo "HTTP Code: $http_code<br/>";
    echo "Response: <br/>";
    print_r(json_decode($response, true));
    // The response from Facebook Graph API contains a URL to the media
    $res = json_decode($response, true);
    if ($http_code == 200 && isset($res['url'])) {
        // Download the actual media content
        $mediaUrl = $res['url'];
        echo "<br/>"; print_r($mediaUrl); echo "<br/>";
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $mediaUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$accessToken,
            'User-Agent: PostmanRuntime/7.28.4',
            'Accept: */*',
            'Cache-Control: no-cache',
            'Postman-Token: some-token', // Replace with actual Postman token if available
            'Host: lookaside.fbsbx.com',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive'
          ),
        ));
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
            $headers = curl_getinfo($curl);
            curl_close($curl);

            if(!empty($file_orignal_name)){
                $filename = 'whatsapp_'.$mediaId.'_'.$file_orignal_name;  // for doc file
            }else{ 
                $filename = 'whatsapp_'.$mediaId; // for jpg,png file           
                $extension = '';
                if (isset($headers['content_type'])) {
                    $content_type = $headers['content_type'];
                    // Determine file extension based on content type
                    // added all type doc and image handling [Aarti][08-08-2024]
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
                // Fallback to a default filename with the determined extension
                $filename .= $extension;
            }           
            
            // Check if the request was successful and content type is an image
            if ($http_code == 200) {
                // Define the path to save the image
                $imagePath = $directoryPath . '/' . $filename;
                // Save the response to a file
                if (file_put_contents($imagePath, $response)) {
                    echo "Image saved successfully to " . $imagePath;
                    chmod($imagePath, 0666); // Set file permissions to 0666
                    $date = date('dmy');
                    $filename = $date.'/'.$filename;
                    return $filename; exit();
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
                echo"<br/>";echo $textt;
                if (__DBGLOG__){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Failed to download media. HTTP Code: $http_code",'omichannel');
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
    global $whatsapp_path_out;
    $date = date('dmy'); // Get the current date in ddmmyy format
    $directoryPath = $whatsapp_path_out . $date; // Directory path
    // Check if the directory already exists
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0777, true); // Create the directory with 0777 permissions
        chmod($directoryPath, 0777); // Ensure the directory has 0777 permissions
    }
    print_r($directoryPath);
    return $directoryPath;
}
?>
