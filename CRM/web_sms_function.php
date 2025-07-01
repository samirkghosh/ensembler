<?php
/*
* Date: 30-11-2024
* Author: Aarti Ojha
* Purpose: Send SMS using SMPP service, insert SMS into the database, and fetch/update SMS list.
* Note: Centralized functions for insert/update/delete to maintain consistency and ease of future changes.
*/

include_once("../config/web_mysqlconnect.php");

// Function to insert SMS details into the database.
/**
 * Inserts an SMS record into the `sms_out_queue` table.
 *
 * @param string $V_MobileNo  The mobile number to send the SMS to.
 * @param string $smsCustomer The SMS message content.
 * @param string $V_SenderName The sender's name.
 * @return void
 */
function sms_out_queue($V_MobileNo, $smsCustomer,$ticket='',$createdBy) {
    global $db, $link;
    
    // Get the current timestamp for the SMS creation time.
    $todayTime = date("Y-m-d H:i:s");
    $send_from = 'LEC'; // Default sender ID.
    $send_to = '00266'.$V_MobileNo;
    
    // Define the SMS message and status.
    $message = mysqli_real_escape_string($link, $smsCustomer);
    $status = '0'; // Initial status for new SMS entries.0
    // SQL query to insert SMS details into the `sms_out_queue` table.
    $sql = "INSERT INTO $db.sms_out_queue (
                send_to, 
                send_from,
                message, 
                create_date, 
                status,
                ICASEID,
                created_by
            ) VALUES (
                '$send_to',
                '$send_from',
                '$message',
                '$todayTime',
                '$status',
                '$ticket',
                '$createdBy'
            )";
    // Execute the query and handle potential errors.
    if (!mysqli_query($link, $sql)) {
        die("Error inserting SMS into queue: " . mysqli_error($link));
    }
    // echo "SMS inserted successfully!";
}

// Function to fetch SMS details from the database.
/**
 * Retrieves SMS details from the `sms_out_queue` table.
 *
 * @param string $Mid (optional) The ID of the specific SMS record to fetch.
 * @return array|null Returns an associative array of the SMS record, or null if no data is found.
 */
function get_sms_out_queue($Mid) {
    global $db, $link;

    // Construct the WHERE clause if an ID is provided.
    $where = $Mid ? "WHERE id='$Mid'" : '';

    // SQL query to fetch SMS records from the database.
    $sql = "SELECT * FROM $db.sms_out_queue $where";
    $result = mysqli_query($link, $sql);

    if ($result) {
        return $row; // Return the SMS details.
    } else {
        // Handle query errors.
        die("Error fetching SMS data: " . mysqli_error($link));
    }
}

// Function to update SMS status and reminder.
/**
 * Updates the status and reminder fields for an SMS record in the database.
 *
 * @param string $Mid The ID of the SMS record to update.
 * @return void
 */
function update_sms($Mid) {
    global $db, $link;

    // SQL query to update the SMS status to '0' (processed).
    $statusUpdateQuery = "UPDATE $db.sms_out_queue SET status='0' WHERE id='$Mid'";
    if (!mysqli_query($link, $statusUpdateQuery)) {
        die("Error updating SMS status: " . mysqli_error($link));
    }

    // SQL query to set the reminder field to '1' (reminder sent).
    $reminderUpdateQuery = "UPDATE $db.sms_out_queue SET i_reminder='1' WHERE id='$Mid'";
    if (!mysqli_query($link, $reminderUpdateQuery)) {
        die("Error updating SMS reminder: " . mysqli_error($link));
    }

    // echo "SMS status and reminder updated successfully!";
}
?>
