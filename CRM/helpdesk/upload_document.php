<?php
/***
 * Document Upload
 * Author: Aarti
 * Date: 04-04-2024
 *  This code is used in a web application to Document Upload 
-->
 **/
include("../../config/web_mysqlconnect.php"); // Connection to database
$uploadDir = '../document/' . $db . '/'; 
//updated code to allow file in audio and  video format [vastvikta][21-03-2025]
$allowTypes = array('jpg', 'png', 'jpeg', 'wav', 'mp4'); // Added 'wav' and 'mp4'

$response = array( 
    'status' => 0, 
    'message' => 'Form submission failed, please try again.' 
);

// Define max file size (5 MB)
$maxFileSize = 5 * 1024 * 1024; // 5 MB
$maxFileCount = 5; // Maximum number of files to upload

// If form is submitted 
$errMsg = ''; 
$uploadStatus = 0; 
$valid = 1;

if(isset($_POST['V_Doc_Name']) || isset($_POST['V_DOC_Description']) || isset($_FILES['files'])){ 
    // Get the submitted form data 
    $name = $_POST['V_Doc_Name']; 
    $V_DOC_Description = $_POST['V_DOC_Description']; 
    $filesArr = $_FILES["files"];
    $I_DocumentType = $_POST['I_DocumentType']; 

    if(empty($name)){ 
        $valid = 0; 
        $errMsg .= '<br/>Please enter Document name.'; 
    } 

    // Check whether submitted data is not empty 
    if($valid == 1){ 
        $fileNames = array_filter($filesArr['name']); 
        $fileCount = count($fileNames); // Count number of files

        // Check if the number of files exceeds the limit
        if ($fileCount > $maxFileCount) {
            $response['message'] = "You can upload a maximum of $maxFileCount files.";
            echo json_encode($response);
            exit; // Stop further processing
        }

        // Upload file 
        $uploadedFile = ''; 
        if(!empty($fileNames)){  
            foreach($filesArr['name'] as $key=>$val){  
                // File upload path  
                $fileName = time() . '_' . str_replace(" ", "", strtolower(basename($filesArr['name'][$key])));  
                $targetFilePath = $uploadDir . $fileName;  

                // Check whether file type is valid  
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));  
                $fileSize = $filesArr["size"][$key]; // Get file size

                if(in_array($fileType, $allowTypes)){  
                    // Check file size
                    if ($fileSize > $maxFileSize) {
                        $response['message'] = "File size of " . $fileName . " exceeds 5 MB.";
                        echo json_encode($response);
                        exit; // Stop further processing
                    }
                    
                    // Upload file to server  
                    if(move_uploaded_file($filesArr["tmp_name"][$key], $targetFilePath)){  
                        $uploadedFile .= $fileName . ','; 
                        $uploadStatus = 1; 
                    } else {  
                        $uploadStatus = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                        $response['message1'] = $targetFilePath . ' ' . $fileType; 
                    }  
                } else {  
                    $uploadStatus = 0; 
                    $response['message'] = 'Sorry, only JPG, JPEG, & PNG files are allowed to upload.'; 
                    $response['message1'] = $targetFilePath . ' type ' . $fileType;  
                }  
            }  
        } 

        if($uploadStatus == 1){ 
            // Insert form data in the database 
            $uploadedFileStr = trim($uploadedFile, ','); 
            $I_UploadedBY = $_SESSION['logged'];
            $I_PP = isset($_REQUEST['I_PP']) ? 1 : 0; // Simplified check for I_PP
            $opportunityid = $_REQUEST['opportunityid'];

            $insert_sql = "INSERT INTO $db.web_documents (I_DocumentType, V_Doc_Name, v_uploadedFile, V_DOC_Description, I_PP, I_UploadedON, I_UploadedBY, I_Doc_Status, I_section_ID) 
            VALUES ('$I_DocumentType', '$name', '$uploadedFileStr', '$V_DOC_Description', '$I_PP', NOW(), '$I_UploadedBY', '1', '$opportunityid')";

            mysqli_query($link, $insert_sql) or die(mysqli_error($link));
            if($insert_sql){ 
                $response['status'] = 1; 
                $response['message'] = 'Submitted successfully!'; 
            } 
        } else {
            $response['status'] = 2; 
            $response['message'] = 'Please upload documents up to 5MB only !!!'; 
        } 
    } else { 
        $response['status'] = 2; 
        $response['message'] = 'Please fill mandatory fields and upload at least one file!' . $errMsg; 
    } 
} 

// Return response 
echo json_encode($response);
?>
