<?php
/**
 * Check Email Duplicate and Generate Unique Hash
 * Author: Vastvikta
 * Date: 11-12-2024
 * Description: This Script handles email duplicacy by generating a unique hash code 
 *              based on the email's subject and storing it in the table.
 **/

// Database connection details
include_once("../config/web_mysqlconnect.php");
Email_duplicate_Hash();

function Email_duplicate_Hash(){
    global $link, $db;

    $sql = "SELECT * FROM $db.web_email_information_pseudo  ORDER BY d_email_date DESC LIMIT 10";
    $resu = mysqli_query($link, $sql);

    // Step 2: Check if any records exist
    if (mysqli_num_rows($resu) > 0) {
        while ($row = mysqli_fetch_assoc($resu)) {
            // Fetch EMAIL_ID and v_subject
            $EMAIL_ID = $row['EMAIL_ID'];
            $subject = $row['v_subject'];

            // Step 3: Remove spaces from the subject and generate a unique hash
            $cleaned_subject = str_replace(' ', '', $subject); // Remove spaces
            $unique_hash = md5($cleaned_subject); // Generate MD5 hash

            // Step 4: Update the table with the unique hash and a message
            $message = "Hash generated successfully";
            $update_sql = "UPDATE $db.web_email_information_pseudo
                           SET v_subject_hash = '$unique_hash',  v_message = '$message'
                           WHERE EMAIL_ID = '$EMAIL_ID'";

            // Execute the update query
            $result = mysqli_query($link, $update_sql);

            // Step 5: Check for success or failure
            if ($result) {
                echo "Hash generated and updated successfully for EMAIL_ID: $EMAIL_ID<br/>";
            } else {
                echo "Error updating hash for EMAIL_ID: $EMAIL_ID - " . mysqli_error($link) . "<br/>";
            }
        }
    } else {
        echo "No emails found with email_replay_status = '1'.<br/>";
    }
}
?>
