<?php
/***
 * Content Messaging API For Email,SMS,Whatsapp and Messenger
 * Author: Farhan Akhtar
 * Last Modified On : 18-10-2024
 * Please do not modify this file without permission.
 **/              

// updated code for inserting custoer and case id [vastvikta]04-04-2025
 // Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


 // Include necessary files and database connection
session_start();
require "../../config/web_mysqlconnect.php";
// fetch user details
include_once("../web_function.php");

$userid = $_SESSION['userid'];

$response = ['status' => 'Failed', 'message' => 'Invalid Request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {

    $action = $_POST['action'];
    $message = $link->real_escape_string($_POST['reply']);
    $phone = $_POST['phone'];
    $customerid = $_POST['customerid'];
    $caseid = $_POST['caseid'];
    $sendFrom = $_POST['sendFrom'] ?? null;
    // Get current date to use as the folder name
    $folderName = date('dmy');
    // Handle SMS
    if ($action === "SMS") {
        $name = $_POST['name'];
        $expiry = 3;
        // Common function to insert SMS outgoing data into the database.[Aart][05-12-2024]
          $data_sms=array();
          $data_sms['v_mobileNo'] = $phone;
          $data_sms['v_smsString']= $message;
          $data_sms['V_CreatedBY']=$userid;
          $data_sms['i_status']='0';
          $data_sms['i_expiry']=$expiry; 
          $data_sms['caseid']=$caseid;
          $data_sms['customerid'] = $customerid;
         insert_smsmessages($data_sms);
        
        $response['status'] = 'success';
    }

    // Handle WhatsApp
    elseif ($action === "Whatsapp") {


        $uploadDir = "whatsapp_attachemnts/outgoing/$folderName"; // Define the directory path

        // Check if the folder exists, if not create it
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the folder with full permissions
        }

        // Insert the message entry into the database
        $sql = "INSERT INTO $db.whatsapp_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag, user_name, attachment) 
        VALUES ('$phone', '$sendFrom', '$message', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT', 'Content Messaging', '')";
        $result = $link->query($sql);
        $interact_id = mysqli_insert_id($link);

         // for any interactio coming with any channel when insert data in interactio table
        // [Aarti][07-01-2025]
        $intraction_type = 'Whatsapp';
        $agentid = $_SESSION['userid'];
        // SQL query to insert interaction details into the `interaction` table.
        $sql = "INSERT INTO $db.interaction (caseid,intraction_type,email,mobile,name,interact_id,customer_id,remarks,filename,created_date,type,created_by
                ) VALUES ('$caseid','$intraction_type','','$phone','','$interact_id','$customerid','$message','',NOW(),'OUT','$agentid')";
        $result_sms= mysqli_query($link,$sql) or die("Error In Query24 ".mysqli_error($link));

        if ($result) {
            $response['status'] = 'success';

            // Process each file uploaded
            if (isset($_FILES['attachments']) && $_FILES['attachments']['error'][0] === UPLOAD_ERR_OK) {
                $attachments = $_FILES['attachments']; // Get files from the form

                // Loop through each file
                for ($i = 0; $i < count($attachments['name']); $i++) {
                    // Get file details
                    $fileName = basename($attachments['name'][$i]);
                    $fileTmpPath = $attachments['tmp_name'][$i];
                    $fileSize = $attachments['size'][$i];

                    // Define the file path where the file will be saved
                    $fileDest = "$uploadDir/$fileName";
                    $FileNamewithPath = "/outgoing/$folderName/$fileName";

                    // Move the file to the server folder
                    if (move_uploaded_file($fileTmpPath, $fileDest)) {
                        // Insert each attachment into the whatsapp_out_queue table with a blank message
                        $sql = "INSERT INTO $db.whatsapp_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag, user_name, attachment) 
                                VALUES ('$phone', '$sendFrom', '', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT', 'Content Messaging', '$FileNamewithPath')";
                        $result = $link->query($sql);

                        if (!$result) {
                            $response['status'] = "Failed to insert attachment: $fileName";
                            break; // Stop further execution if any file insertion fails
                        }
                    } else {
                        $response['status'] = "Failed to upload attachment: $fileName";
                        break; // Stop further execution if any file upload fails
                    }
                }
            }
        } else {
            $response['status'] = 'Failed to insert message';
        }

    }

    // Handle Messenger
    elseif ($action === "Messenger") {
        $facebookid = $_POST['facebookid'];
        $uploadDir = "facebook_attachemnts/outgoing/$folderName"; // Define the directory path

        // Check if the folder exists, if not create it
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the folder with full permissions
        }
        $sql = "INSERT INTO $db.messenger_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag,attachment) VALUES ('$facebookid', '$sendFrom', '$message', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT','')";
        $result = $link->query($sql);
        $interact_id = mysqli_insert_id($link);

         // for any interactio coming with any channel when insert data in interactio table
        // [Aarti][07-01-2025]
        $intraction_type = 'messenger';

        $agentid = $_SESSION['userid'];
        // SQL query to insert interaction details into the `interaction` table.
        $sql = "INSERT INTO $db.interaction (caseid,intraction_type,email,mobile,name,interact_id,customer_id,remarks,filename,created_date,type,created_by
                ) VALUES ('$caseid','$intraction_type','','$facebookid','','$interact_id','$customerid','$message','',NOW(),'OUT','$agentid')";
        $result_sms= mysqli_query($link,$sql) or die("Error In Query24 ".mysqli_error($link));

        if ($result) {
            $response['status'] = 'success';

            // Process each file uploaded
            if (isset($_FILES['attachments']) && $_FILES['attachments']['error'][0] === UPLOAD_ERR_OK) {
                $attachments = $_FILES['attachments']; // Get files from the form

                // Loop through each file
                for ($i = 0; $i < count($attachments['name']); $i++) {
                    // Get file details
                    $fileName = basename($attachments['name'][$i]);
                    $fileTmpPath = $attachments['tmp_name'][$i];
                    $fileSize = $attachments['size'][$i];

                    // Define the file path where the file will be saved
                    $fileDest = "$uploadDir/$fileName";
                    $FileNamewithPath = "/outgoing/$folderName/$fileName";

                    // Move the file to the server folder
                    if (move_uploaded_file($fileTmpPath, $fileDest)) {
                        // Insert each attachment into the whatsapp_out_queue table with a blank message
                        $sql = "INSERT INTO $db.messenger_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag,attachment) VALUES ('$facebookid', '$sendFrom', '', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT','$FileNamewithPath')";
                        $result = $link->query($sql);

                        if (!$result) {
                            $response['status'] = "Failed to insert attachment: $fileName";
                            break; // Stop further execution if any file insertion fails
                        }
                    } else {
                        $response['status'] = "Failed to upload attachment: $fileName";
                        break; // Stop further execution if any file upload fails
                    }
                }
            }
        } else {
            $response['status'] = 'Failed to insert message';
        }
    }
    // handle Instagram
    elseif ($action === "instagram") {
        $instagramhandle = $_POST['instagramhandle'];
        $uploadDir = "instagram_attachemnts/outgoing/$folderName"; // Define the directory path

        // Check if the folder exists, if not create it
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the folder with full permissions
        }
        $sql = "INSERT INTO $db.instagram_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag,attachment) VALUES ('$instagramhandle', '$sendFrom', '$message', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT','')";
        $result = $link->query($sql);
        $interact_id = mysqli_insert_id($link);

         // for any interactio coming with any channel when insert data in interactio table
        // [vastvikta][16-04-2025]
        $intraction_type = 'instagram';
        $agentid = $_SESSION['userid'];
        // SQL query to insert interaction details into the `interaction` table.
        $sql = "INSERT INTO $db.interaction (caseid,intraction_type,email,mobile,name,interact_id,customer_id,remarks,filename,created_date,type,created_by
                ) VALUES ('$caseid','$intraction_type','','$instagramhandle','','$interact_id','$customerid','$message','',NOW(),'OUT','$agentid')";
        $result_sms= mysqli_query($link,$sql) or die("Error In Query24 ".mysqli_error($link));

        if ($result) {
            $response['status'] = 'success';

            // Process each file uploaded
            if (isset($_FILES['attachments']) && $_FILES['attachments']['error'][0] === UPLOAD_ERR_OK) {
                $attachments = $_FILES['attachments']; // Get files from the form

                // Loop through each file
                for ($i = 0; $i < count($attachments['name']); $i++) {
                    // Get file details
                    $fileName = basename($attachments['name'][$i]);
                    $fileTmpPath = $attachments['tmp_name'][$i];
                    $fileSize = $attachments['size'][$i];

                    // Define the file path where the file will be saved
                    $fileDest = "$uploadDir/$fileName";
                    $FileNamewithPath = "/outgoing/$folderName/$fileName";

                    // Move the file to the server folder
                    if (move_uploaded_file($fileTmpPath, $fileDest)) {
                        // Insert each attachment into the whatsapp_out_queue table with a blank message
                        $sql = "INSERT INTO $db.instagram_out_queue (send_to, send_from, message, message_type_flag, status, create_date, created_by, channel_type, msg_flag,attachment) VALUES ('$instagramhandle', '$sendFrom', '', '1', '$mesg_flag', NOW(), '$userid', '1', 'OUT','$FileNamewithPath')";
                        $result = $link->query($sql);

                        if (!$result) {
                            $response['status'] = "Failed to insert attachment: $fileName";
                            break; // Stop further execution if any file insertion fails
                        }
                    } else {
                        $response['status'] = "Failed to upload attachment: $fileName";
                        break; // Stop further execution if any file upload fails
                    }
                }
            }
        } else {
            $response['status'] = 'Failed to insert message';
        }
    }
}

echo json_encode($response);
exit();