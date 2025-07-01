<?php
/*
Auth: Aarti Ojha
Date: 24-12-2024 
Description: This file handles the SMS, email, and WhatsApp outgoing queue data. 
It provides rescheduling functionality for failed deliveries and an option to forcefully expire SMS that should not be resent.
*/

// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");

// Check the `action` parameter and call the corresponding function
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'sms_reschedule') {
        sms_reschedule(); // Handle SMS rescheduling
    } elseif ($_POST['action'] == 'sms_expire') {
        sms_expire(); // Handle SMS expiration
    } elseif ($_POST['action'] == 'email_reschedule') {
        email_reschedule(); // Handle email rescheduling
    } elseif ($_POST['action'] == 'email_expire') {
        email_expire(); // Handle email expire
    } elseif ($_POST['action'] == 'whatsapp_reschedule') {
        whatsapp_reschedule(); // Handle whatapp_ rescheduling
    } elseif ($_POST['action'] == 'whatsapp_expire') {
        whatsapp_expire(); // Handle whatapp_ expire
    }
}

// Function to handle SMS rescheduling
function sms_reschedule(){
    global $db, $link;

    $reschedule_date = date("Y-m-d H:i:s", strtotime($_POST['DateNew']));
    $update_date = date("Y-m-d H:i:s");
    $ids = $_POST['ids'];
    
    foreach ($ids as $sms_id) {
	    // Step 1: Fetch the old SMS entry that failed
	    $baseSQL = "SELECT * FROM $db.sms_out_queue WHERE id = '{$sms_id}'";
	    $query = mysqli_query($link, $baseSQL);

	    if (!$query || mysqli_num_rows($query) == 0) {
	        die("Error: Failed to fetch the SMS record for rescheduling.");
	    }
	    
	    $row = mysqli_fetch_assoc($query);
	    if($row['status'] == '3'){ // not Delivered sms allow to rescheduling

	    	// Extract the required fields from the fetched record	    	
	    	$send_to = $row['send_to'];
		    $send_from = $row['send_from'];
		    $message = $row['message'];
		    $create_date = $row['create_date'];
		    $status = '0'; // Reset the status for resending
		    $docket_no = $row['ICASEID'];
		    $createdBy = $row['created_by'];
		    $AccountName = $row['AccountName'];
		    $reschedule_id = $row['id'];
		    // Step 2: Insert a new entry into `sms_out_queue` for the rescheduled SMS
		    $insertSQL = "
		        INSERT INTO $db.sms_out_queue
		        (send_to, send_from, message, create_date, status, ICASEID, created_by, AccountName, reschedule_id, rescheduling_date) 
		        VALUES 
		        ('$send_to', '$send_from', '$message', '$create_date', '$status', '$docket_no', '$createdBy', '$AccountName', '$reschedule_id', '$reschedule_date')";

		    $result_sms = mysqli_query($link, $insertSQL);
		    if (!$result_sms) {
		        die("Error: Failed to insert the rescheduled SMS. " . mysqli_error($link));
		    }

		    // Step 3: Update the old SMS entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.sms_out_queue 
		        SET reschedule_flag = '1', update_date = '$update_date',rescheduling_date='$reschedule_date'
		        WHERE id = '{$sms_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old SMS record. " . mysqli_error($link));
		    }

		}else{

			// Step 1: Update the SMS entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.sms_out_queue 
		        SET rescheduling_date = '$reschedule_date' 
		        WHERE id = '{$sms_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old SMS record. " . mysqli_error($link));
		    }
		}
	}
    echo "SMS rescheduling completed successfully.";
}

// Function to forcefully expire SMS that should not be resent
function sms_expire()
{
    global $db, $link;

    // Get the IDs of SMS to be expired
    $ids = isset($_POST['ids']) ? $_POST['ids'] : []; // Expecting `ids` as an array in the POST request
    $update_date = date("Y-m-d H:i:s");

    if (empty($ids)) {
        die("Error: No SMS IDs provided for expiration.");
    }

    // Step 1: Update each SMS entry to mark it as expired
    foreach ($ids as $sms_id) {
        $updateSQL = "
            UPDATE $db.sms_out_queue
            SET expiry_flag = '1', update_date = '$update_date' ,status='4'
            WHERE id = '{$sms_id}'";

        $update_result = mysqli_query($link, $updateSQL);
        if (!$update_result) {
            die("Error: Failed to update SMS record with ID {$sms_id}. " . mysqli_error($link));
        }
    }

    echo "SMS expiration completed successfully.";
}
// for email report code start
// Function to handle SMS rescheduling
function email_reschedule(){
    global $db, $link;

    $reschedule_date = date("Y-m-d H:i:s", strtotime($_POST['DateNew']));
    $update_date = date("Y-m-d H:i:s");
    $ids = $_POST['ids'];
    
    foreach ($ids as $email_id) {
	    // Step 1: Fetch the old SMS entry that failed
	    $baseSQL = "SELECT * FROM $db.web_email_information_out WHERE EMAIL_ID = '{$email_id}'";
	    $query = mysqli_query($link, $baseSQL);

	    if (!$query || mysqli_num_rows($query) == 0) {
	        die("Error: Failed to fetch the EMAIL record for rescheduling.");
	    }
	    
	    $row = mysqli_fetch_assoc($query);
	    if($row['I_Status'] == '3'){ // not Delivered sms allow to rescheduling

	    	// Extract the required fields from the fetched record	    	
	    	$v_toemail = $row['v_toemail'];
		    $v_fromemail = $row['v_fromemail'];
		    $v_subject = $row['v_subject'];
		    $v_body = $row['v_body'];
		    $d_email_date = $row['d_email_date'];
		    $status = '1'; // Reset the status for resending
		    $ICASEID = $row['ICASEID'];
		    $reschedule_id = $row['EMAIL_ID'];
		    $email_type = 'OUT';
		    $V_rule = $row['V_rule'];

		    // Step 2: Insert a new entry into `sms_out_queue` for the rescheduled SMS
		    $insertSQL = "
		        INSERT INTO $db.web_email_information_out
		        (v_toemail, v_fromemail, v_subject, v_body, d_email_date, I_Status, ICASEID, reschedule_id, scheduling_date,email_type,V_rule) 
		        VALUES 
		        ('$v_toemail', '$v_fromemail','$v_subject', '$v_body', '$update_date', '$status', '$ICASEID', '$reschedule_id', '$reschedule_date', '$email_type', '$V_rule')";
		    $result_sms = mysqli_query($link, $insertSQL);
		    if (!$result_sms) {
		        die("Error: Failed to insert the rescheduled EMAIL. " . mysqli_error($link));
		    }

		    // Step 3: Update the old SMS entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.web_email_information_out 
		        SET schedule_flag = '1', d_RetryTime = '$update_date',scheduling_date='$reschedule_date'
		        WHERE EMAIL_ID = '{$email_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old SMS record. " . mysqli_error($link));
		    }

		}else{

			// Step 1: Update the Email entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.web_email_information_out 
		        SET scheduling_date = '$reschedule_date' 
		        WHERE EMAIL_ID = '{$email_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old Email record. " . mysqli_error($link));
		    }
		}
	}
    echo "Email rescheduling completed successfully.";
}

// Function to forcefully expire SMS that should not be resent
function email_expire(){
    global $db, $link;

    // Get the IDs of SMS to be expired
    $ids = isset($_POST['ids']) ? $_POST['ids'] : []; // Expecting `ids` as an array in the POST request
    $update_date = date("Y-m-d H:i:s");

    if (empty($ids)) {
        die("Error: No Email IDs provided for expiration.");
    }

    // Step 1: Update each Email entry to mark it as expired
    foreach ($ids as $email_id) {
        $updateSQL = "
            UPDATE $db.web_email_information_out
            SET expiry_flag = '1', d_RetryTime = '$update_date' ,I_Status='4'
            WHERE EMAIL_ID = '{$email_id}'";

        $update_result = mysqli_query($link, $updateSQL);
        if (!$update_result) {
            die("Error: Failed to update Email record with ID {$email_id}. " . mysqli_error($link));
        }
    }

    echo "Email expiration completed successfully.";
}
// Function to handle whatsapp rescheduling
function whatsapp_reschedule(){
    global $db, $link;

    $reschedule_date = date("Y-m-d H:i:s", strtotime($_POST['DateNew']));
    $update_date = date("Y-m-d H:i:s");
    $ids = $_POST['ids'];
    
    foreach ($ids as $email_id) {
	    // Step 1: Fetch the old SMS entry that failed
	    $baseSQL = "SELECT * FROM $db.whatsapp_out_queue WHERE id = '{$email_id}'";
	    $query = mysqli_query($link, $baseSQL);

	    if (!$query || mysqli_num_rows($query) == 0) {
	        die("Error: Failed to fetch the EMAIL record for rescheduling.");
	    }
	    
	    $row = mysqli_fetch_assoc($query);
	    if($row['status'] == '3'){ // not Delivered sms allow to rescheduling

	    	// Extract the required fields from the fetched record	    	
	    	$send_to = $row['send_to'];
		    $send_from = $row['send_from'];
		    $message = $row['message'];
		    $status = '1'; // Reset the status for resending
		    $ICASEID = $row['ICASEID'];
		    $reschedule_id = $row['id'];
		    $mediaId = $row['mediaId'];
		    $attachment = $row['attachment'];

		    // Step 2: Insert a new entry into `sms_out_queue` for the rescheduled SMS
		    $insertSQL = "
		        INSERT INTO $db.whatsapp_out_queue
		        (send_to, send_from, message, create_date, status, ICASEID, reschedule_id, schedule_time,attachment,mediaId) 
		        VALUES 
		        ('$send_to', '$send_from', '$message', '$update_date', '$status', '$ICASEID', '$reschedule_id', '$reschedule_date', '$attachment','$mediaId')";
		    $result_sms = mysqli_query($link, $insertSQL);
		    if (!$result_sms) {
		        die("Error: Failed to insert the rescheduled EMAIL. " . mysqli_error($link));
		    }

		    // Step 3: Update the old SMS entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.whatsapp_out_queue 
		        SET schedule_flag = '1', update_date = '$update_date',schedule_time='$reschedule_date'
		        WHERE id = '{$email_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old SMS record. " . mysqli_error($link));
		    }

		}else{

			// Step 1: Update the Email entry to mark it as rescheduled
		    $updateSQL = "
		        UPDATE $db.whatsapp_out_queue 
		        SET schedule_time = '$reschedule_date' 
		        WHERE id = '{$email_id}'";

		    $update_result = mysqli_query($link, $updateSQL);
		    if (!$update_result) {
		        die("Error: Failed to update the old Email record. " . mysqli_error($link));
		    }
		}
	}
    echo "Email rescheduling completed successfully.";
}

// Function to forcefully expire whatsapp that should not be resent
function whatsapp_expire(){
    global $db, $link;

    // Get the IDs of SMS to be expired
    $ids = isset($_POST['ids']) ? $_POST['ids'] : []; // Expecting `ids` as an array in the POST request
    $update_date = date("Y-m-d H:i:s");

    if (empty($ids)) {
        die("Error: No whatsapp IDs provided for expiration.");
    }

    // Step 1: Update each whatsapp entry to mark it as expired
    foreach ($ids as $email_id) {
        $updateSQL = "
            UPDATE $db.whatsapp_out_queue
            SET expiry_flag = '1', update_date = '$update_date' ,status='4'
            WHERE id = '{$email_id}'";

        $update_result = mysqli_query($link, $updateSQL);
        if (!$update_result) {
            die("Error: Failed to update whatsapp record with ID {$email_id}. " . mysqli_error($link));
        }
    }

    echo "whatsapp expiration completed successfully.";
}
?>
