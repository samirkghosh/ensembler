<?php

require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';
// for facing error send mail[Aarti][25-09-2024]
function sendErrorEmail($errorMessage,$type) {
	try {
		// 'techsupport@alliance-infotech.com'
		$recipientEmails = ['Aarti.Ojha@ensembler.com']; // Add more emails as needed
        $mail = new PHPMailer(true);
        define ("PORTNUM", '587');
	  	define ("EMAIL_USER", 'rajdubey.alliance@gmail.com');
	  	define ("EMAIL_PWD", 'syepvwaknagahctq');
	  	define ("EMAIL_SERVER", 'smtp.gmail.com');  
	  	define ("EMAIL_TLS", '1');
        $fromAddr = 'rajdubey.alliance@gmail.com';

        $mail = new PHPMailer;
      	$mail->IsSMTP();
      	if ( EMAIL_TLS == "1"){
	        $mail->SMTPAuth = true;
	        $mail->SMTPSecure = "tls";
	    }
	    $mail->SMTPDebug = 0;
	    $mail->SMTPOptions = array(
	        'ssl' => array(
	        'verify_peer' => false,
	        'verify_peer_name' => false,
	        'allow_self_signed' => true
	        )
	    );
  	  	$mail->Host = EMAIL_SERVER;
      	$mail->Port = PORTNUM;
      	$mail->Username = EMAIL_USER;
      	$mail->Password = EMAIL_PWD;

      	// $mail->From = $fromAddr; 
      	$mail->FromName = $fromAddr;

	    // Loop through the recipient emails array and add them
	    foreach ($recipientEmails as $toAddr) {
	        $mail->addAddress($toAddr); // Add each email address
	    }  

	    // Content
		$mail->isHTML(true);                                        // Set email format to HTML
		if($type == 'whatsapp'){
			$mail->Subject = 'Ubuntu Droplet WhatsApp Script Error Notification';
		}else if($type == 'facebook_messenger'){
			$mail->Subject = 'Ubuntu Droplet Facebook Messenger Script Error Notification';
		}else if($type == 'FacebookPost'){
			$mail->Subject = 'Ubuntu Droplet Facebook post Script Error Notification';
		}else{
			$mail->Subject = 'Ubuntu Droplet Droplet IMAP Script Error Notification';
		}
		
		$mail->Body    = '<b>Error:</b> ' . $errorMessage;
	    // $send=$mail->Send();

	    if($send==1){ 
	      $msg = "successfully mail send.";
	    }else{
	      $msg = "something went wrong.";
	    }
    } catch (Exception $e) {
        echo "Error email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
// this code for send message and attachement using curl hit
function curlhit($api_url,$message_array,$bearer_token){
	//Making cURL request to WhatsApp API
	echo"<br/> Url: ";print_r($api_url);echo"<br/>";echo"<br/>";echo"<br/>";
	echo"Request: ";print_r($message_array);echo"<br/>";echo"<br/>";
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $api_url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>$message_array,
	  CURLOPT_HTTPHEADER => array(
	    'Content-Type: application/json',
	    'Authorization: Bearer '.$bearer_token,
	  ),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	echo "i am here";
	print_r($response);
	$response = json_decode($response,true);
	return $response;
}
// Function to check file size (max size in bytes)[Aarti][30-09-2024]
function checkFileSize($filePath, $maxSize = 5242880) { // 5MB limit
    return filesize($filePath) <= $maxSize;
}

// Function to split large messages into chunks and send in loop [Aarti][30-09-2024]
function splitMessage($message, $maxLength = 1500) {
    return str_split($message, $maxLength); // Split the message by WhatsApp limit
}
// Function to handle shortening of file names, handling special characters, and limiting file sizes[Aarti][30-09-2024]
function processFileName($filePath,$id,$type,$childdb) {
    global $link,$db;
    $maxLength = 200;
    // Split the file path into directory and file name
    $path_parts = pathinfo($filePath);
    $directory = $path_parts['dirname'];
    $originalFileName = $path_parts['basename'];  // Get the file name with extension

    // Remove any special characters from the file name
    $cleanedFileName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalFileName);
    
    // Shorten file name if it's too long
    if (strlen($cleanedFileName) > $maxLength) {
        echo "Filename was too long, shortened to: $cleanedFileName\n<br/><br/>";
        $fileExtension = pathinfo($cleanedFileName, PATHINFO_EXTENSION); // Get file extension
        $cleanedFileName = md5($cleanedFileName) . '.' . $fileExtension;  // Create hashed short name
    }
    // If the cleaned file name is the same as the original, no renaming needed
    if ($cleanedFileName === $originalFileName) {
        // No need to rename or update the database
        echo "File name is valid and doesn't need to be changed.";
        return $filePath;  // Return the original path
    }
    // Full path with cleaned file name
    $newFilePath = $directory . '/' . $cleanedFileName;
    // Rename the file in the directory (server)
    if (file_exists($filePath)) {
        rename($filePath, $newFilePath);  // Rename the file on the server
    } else {
        echo "File not found: " . $filePath;
        return $filePath;  // Return the original path if file doesn't exist
    }
    if($type == 'facebook_messenger'){
    	$attachment_string = explode("facebook_attachemnts", $newFilePath); // for store database file name changed
    	$rename_attachment_stringe = $attachment_string['1'];
    }else if($type == 'whatsapp'){
    	$attachment_string = explode("attachments", $newFilePath); // for store database file name changed
		$rename_attachment_stringe = $attachment_string['1'];
    }
    
    if($type == 'facebook_messenger'){
	    // Update the file name in the database (if required)
	    $sql = "UPDATE $childdb.messenger_out_queue 
	            SET attachment = '$rename_attachment_stringe' 
	            WHERE id = '$id'";
	    echo $sql;
	    if (mysqli_query($link, $sql)) {
	        echo "Database updated successfully with new file name.";
	    } else {
	        echo "Error updating record: " . mysqli_error($link);
	    }
	}else if($type == 'whatsapp'){
		$sql = "UPDATE $childdb.whatsapp_out_queue 
            SET attachment = '$rename_attachment_stringe' 
            WHERE id = '$id'";
        echo $sql;
	    if (mysqli_query($link, $sql)) {
	        echo "Database updated successfully with new file name.";
	    } else {
	        echo "Error updating record: " . mysqli_error($link);
	    }
	}
    echo "<br/><br/>Filename after removing special characters: $filename\n<br/><br/>";
    // Return the full path with the modified file name
    return $newFilePath;
}
?>