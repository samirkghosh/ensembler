<?php
/**
 * Social Media Channel
 * Author: Aarti Ojha
 * Date: 29-04-2024
 * Description: This file handles Social Media API Data for Outgoing and Incoming Response Store in the database.
 *              It fetches WhatsApp incoming data via cURL hit using WhatsApp APIs and
 *              sends WhatsApp outgoing data via cURL hit using WhatsApp APIs.
 * 
 * Please do not modify this file without permission.
 */

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

global $whatsapp_path_out;
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file
include_once('/var/www/html/ensembler/CRM/omnichannel_config/script_common_file.php'); // for mail send and curl hit common fun added

// Add code for logging code
include_once("/var/www/html/ensembler/logs/config.php");
include_once("/var/www/html/ensembler/logs/logs.php");

global $log_prefix,$type;
$log_prefix = "[WhatsApp Script]";
$type = 'whatsapp';

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

        WhatsApp_incoming_data($childdb); //For outgoing Data 
    }
}else{
    echo "No company databases found."; die;
}

/**
 * Fetches WhatsApp outgoing data
**/
function WhatsApp_incoming_data($childdb){
    global $childdb,$link,$base_path,$log_prefix,$type;
    echo"<br/>";echo '================Start whatsapp code======================';echo"<br/>";

    /* whatsapp access token and url form database */
		$sql_cdr= "SELECT * from $childdb.tbl_whatsapp_connection where status=1 and debug=1 ";
		$query=mysqli_query($link,$sql_cdr);
		$config = mysqli_fetch_array($query);

        if (!$query){
            if (__DBGLOG__){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix DB fetching connection error tbl_whatsapp_connection: $sql_cdr". mysqli_error($link),'omichannel');
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "$log_prefix DB fetching connection error tbl_whatsapp_connection: $sql_cdr";
            echo json_encode($response);
            sendErrorEmail($status_response,$type); // for send error mail -script_common.php
        }

		$access_token = $config['access_token']; // Replace with your access token
		$global_url = $config['whatsapp_url']; // get facebook url from table
		$phone_number_id = $config['phone_number_id']; // get phone_number_id from table
		$bearer_token = $access_token; //bearer token 
		$STD = $config['STD'];

		/* getting list of outgoint flag baises in database */
		$sql_cdr = "SELECT * FROM $childdb.whatsapp_out_queue WHERE msg_flag= 'OUT' and status='0'";
		$query=mysqli_query($link,$sql_cdr);
		$count = mysqli_num_rows($query);

		if($count != 0 ){
		  while($data=mysqli_fetch_assoc($query)){
 
	  		$id = $data['id'];
	  		$comment_text = $data['message']; // Your comment text here
	  		$send_to = $data['send_to']; // Your comment text here
	  		$datajsonq = json_encode($datajson);
	  		$template_name = $data['template_name']; // Your comment text here	  		
  			$attachment = $data['attachment'];
		  	$api_url = $global_url.'messages/send';

            // fetcing message template name
            $template_name = $data['template_name'];
            $template_type = $data['type'];
			echo"url---";print_r($api_url); echo"<pre>";
			  
			$message_array = array();

			$message_array['messages'][0]['clientWaNumber'] = $send_to;

			if(empty($comment_text) && empty($attachment) && empty($template_name)){
			  continue;
			}
			//send attachment code 
			if(!empty($attachment)){

				$target_file = $base_path . $attachment;

				echo "Original filename: $target_file\n<br/><br/>";

				// $attachment = processFileName($target_file,$id,$type);

                // Get file information
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $fileName = basename($target_file);
                $fileSize = 0;
                $mimeType = '';

        		// Check file size before uploading
                if (!checkFileSize($target_file)) {
                    echo "File size exceeds the limit.<br/><br/>";
                    continue;
                }

                // Check if the file is local or remote
                if (file_exists($target_file)) {
                    // Local file
                    $fileSize = filesize($target_file); // Get file size in bytes
                    $mimeType = mime_content_type($target_file); // Get MIME type
                } else {
                    // Remote file
                    $headers = get_headers($target_file, 1);
                    if (!empty($headers['Content-Length'])) {
                        $fileSize = $headers['Content-Length']; // Get file size from headers
                    }
                    if (!empty($headers['Content-Type'])) {
                        $mimeType = $headers['Content-Type']; // Get MIME type from headers
                    }
                }

                echo "File name: $fileName\n<br/>";
                echo "File size: $fileSize bytes\n<br/>";
                echo "MIME type: $mimeType\n<br/>";
                echo "Final filename to be saved: $attachment\n<br/><br/>";

				$mediaUrl = $target_file;

				if(!empty($mediaUrl)){

					if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
						$message_array['messages'][0]['mediaType'] = 'document';
					}else{
						$message_array['messages'][0]['mediaType'] = 'image';
					}

                    $message_array['messages'][0]['url'] = $mediaUrl;
                    $message_array['messages'][0]['name'] = $fileName;
                    $message_array['messages'][0]['size'] = $fileSize;
                    $message_array['messages'][0]['mimeType'] = $mimeType;
                    $message_array['messages'][0]['messageType'] = 'media'; 
				}
				$message_array = json_encode($message_array);
				$response = curlhit($api_url,$message_array,$access_token);//this function script_common.php files inside    
			}else{

				if (!empty($comment_text)) {
	                // Split message if needed
                    $message_array['messages'][0]['messageType'] = 'text';
	                $messagess = splitMessage($comment_text);
	                foreach ($messagess as $message_part) {	                	
					    $message_array['messages'][0]['message'] = $message_part;
					    // Convert the message part to UTF-8
					    $message_part = mb_convert_encoding($message_part, 'UTF-8', 'auto');
					    // Set the message part in the array
					    $message_array['messages'][0]['message'] = $message_part;

					    // Convert the whole message array to UTF-8 before encoding to JSON
					    array_walk_recursive($message_array, function(&$item) {
					        $item = mb_convert_encoding($item, 'UTF-8', 'auto');
					    });
					    // Convert to JSON with UTF-8 support
					    $json_message_array = json_encode($message_array, JSON_UNESCAPED_UNICODE);
					    // Send via curl
					    $response = curlhit($api_url, $json_message_array,$access_token);//this function script_common.php files inside    					    
				    }	
				}
                // this code for send template [Aarti][27-03-2025]
                if($template_type == '1'){
                    $message_array = array();
                    $message_array['messages'][0]['clientWaNumber'] = $send_to;
                    $message_array['messages'][0]['templateName'] = $template_name;
                    $message_array['messages'][0]['templateHeader'] = '';
                    $message_array['messages'][0]['languageCode'] = 'en';
                    $message_array['messages'][0]['variables'] = [];
                    $message_array['messages'][0]['messageType'] = 'template';
                   
                    
                    

                    //  $message_array['messageType'] = 'template';
                    // $message_array['clientWaNumber'] = $send_to;
                    //  $message_array['variables'] = [];
                    // $message_array['templateName'] = $template_name;
                    // $message_array['languageCode'] = 'en';


                    echo"<br/>";print_r($message_array);
                    $json_message_array = json_encode($message_array, JSON_UNESCAPED_UNICODE);
                    $response = curlhit($api_url, $json_message_array,$access_token);//this function 
                }
			}
			// close
				echo"<pre> Response: ";print_r($response); echo"<pre>"; 
				if($response["errorType"]) {
					// Comment posting failed, handle the error.
				  	echo "<h3>Sorry, there was a problem.</h3><p>whatsapp returned the following error message:</p><p><em>".$response["errorType"]."</em></p>";
				  	$update_date = date("Y-m-d H:i:s");
					//send replay sucessfully
					$status_response = $response["errorType"];

					$strQrytest ="update $childdb.whatsapp_out_queue set update_date='".$update_date."',status_response='".$status_response."',status='2' where id=".$id; // store commnet_id and send date also send flag 1
					echo $strQrytest;
					mysqli_query($link, $strQrytest);

                    if (__DBGLOG__){
                            DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix whatsapp returned the following error message: $response",'omichannel');
                    }
                    $response['error'] = TRUE;
                    $response['error_msg'] = "$log_prefix whatsapp returned the following error message: $status_response";
                    echo json_encode($response);
                    sendErrorEmail(json_encode($response),$type); // for send error mail -script_common.php
				
				}else{

					// Comment was posted successfully, and $response_data['id'] contains the comment ID.
					$update_date = date("Y-m-d H:i:s");
					$message_unique_id = $response["data"]["success"][0]["message"]["wamid"];
					$message_status = 'success';
					//send replay sucessfully
					$strQrytest ="update $childdb.whatsapp_out_queue set update_date='".$update_date."',status_response='".$message_status."' ,status='1',message_unique_id='".$message_unique_id."',mediaId='".$mediaId."' where id=".$id; // store commnet_id and send date also send flag 1
					echo $strQrytest;
					mysqli_query($link, $strQrytest);

					echo"<pre>";echo "################## Message send Successfully message_unique_id: " . $message_unique_id; echo"<pre>";
				}
			     echo"<br/>";echo"<br/>";echo '================End whatsapp code======================';echo"<br/>";
				// die;
		}
	}
}

// this function for upload media files
function Upload_Media($global_url, $PHONE_NUMBER_ID, $token, $image_url) {
    global $whatsapp_path_out,$log_prefix,$type;

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
    // Log for debugging
    echo "MIME Type: " . $mime_type;

    $url = $global_url . $PHONE_NUMBER_ID . '/media';

    echo"<br/>";print_r($url);echo"<br/>";

    $curl = curl_init();
    $data = [
        'file' => new CURLFile($temp_image_path, $mime_type, basename($temp_image_path)),
        'messaging_product' => 'whatsapp'
    ];
    echo 'image temp_image_path'.$temp_image_path;
    print_r($data);

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/form-data' // Ensure correct Content-Type
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    // Remove the temporary file
    unlink($temp_image_path);
    $responseData = json_decode($response, true);
    if (isset($responseData['id'])) {
        $mediaId = $responseData['id']; // Get the media ID
        echo "<br/>Media ID: " . $mediaId;
        return $mediaId;
    } else {
        echo "<br/>Failed to upload media.";
        // For log create and send error mail notification
        if (__DBGLOG__){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"$log_prefix Failed to upload media.: $responseData",'omichannel');
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "$log_prefix Failed to upload media.: $responseData";
        echo json_encode($response);
        sendErrorEmail($response,$type); // for send error mail -script_common.php
        return null;        
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