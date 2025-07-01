<?php
/***
 * Status Update Page
 * Author: Aarti Ojha
 * Date: 07-11-2024
 * Description: This file handles updating the user’s status (e.g., Available, Away, Busy) in the database.
 */

// Include the database connection file
// Make sure that the file '../config/web_mysqlconnect.php' establishes a connection to your MySQL database.
// Do not remove this line, as it is essential for accessing the database.
include_once("../config/web_mysqlconnect.php");

// Check if the 'action' in the POST request is 'change_status' to execute the status change.
if($_POST['action'] == 'change_status'){
    change_status(); // Call the function to change the user status
}

/**
 * Function to change the user status in the database.
 *
 * @param string $value (optional) Unused parameter; can be removed or used if needed in the future.
 */
function change_status(){
    global $link,$db;
    // Check if the AJAX request contains both 'user_id' and 'status' fields
    if (isset($_POST['user_id']) && isset($_POST['status'])) {
    
        // Retrieve the 'user_id' and 'status' values from the POST request
        $user_id = $_POST['user_id'];
        $status = $_POST['status'];

        // Prepare the SQL statement to update the user status in the database
        $sql = "UPDATE $db.uniuserprofile SET login_status = '{$status}' WHERE AtxUserID = '{$user_id}'";
        // Execute the SQL query and check if the update was successful
        if(mysqli_query($link, $sql)){
            // If the update was successful, return a success response in JSON format
            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            // If there was an error, return a failure response with an error message
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);;
        }
    }else{
        // If 'user_id' or 'status' is missing in the request, return an error response
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
}
?>
