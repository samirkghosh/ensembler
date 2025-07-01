<?php
/***
 * Auth: Farhan Akhtar
 * Date:  19-10-2024
 * Description: To get AI Generated Reply Response
 * 
*/
include_once "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
function Email_Replay_AI($template){
	global $email_replay_url;

	// Step 1: Prepare the request payload
    // $EMAIL_ID = $template['EMAIL_ID'];
    $payload = [
        'subject' => $template['v_subject'],
        'to' => $template['to'],
        'from_' => $template['from'],
        'body' => $template['body']
    ];



    // Step 2: Make a cURL request
    $url = $email_replay_url; // Replace with your actual API endpoint
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    // Execute cURL and get the response
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
    // Step 3: Handle error or success
    if (curl_errno($ch)) {
        // cURL error
        $error_message = curl_error($ch);
        $response = null;
        $http_status = 500; // Internal Server Error for curl failures
    } elseif ($http_status >= 400) {
        // Handle HTTP errors (e.g., 400 Bad Request)
        $error_message = $response; // Store the response as the error message
    } else {
        $error_message = null;
    }
    $response = json_decode($response,true);
    curl_close($ch);
    $body = addslashes($response['response']['text']);
    return $body;
}

function getEmailData($id) {
	global $link,$db;

    // Build the SQL query
    $query = "SELECT * FROM $db.web_email_information WHERE EMAIL_ID = '$id'";
    // Execute the query
    $result = $link->query($query);
    // Check if the query was successful
    if ($result === false) {
        // Handle query error (log the error or return null)
        return null;
    }
    // Fetch the result
    $data = $result->fetch_assoc();
    return $data;
}

function getJsonResponse($status, $data) {
    return json_encode(["status" => $status, "data" => $data]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Action']) && $_POST['Action'] == 'AI_REPLY') {

    
    // Validate and sanitize the incoming POST data
    $id = isset($_POST['id']) ? htmlspecialchars($_POST['id']) : null;

    if ($id) {
        
        // Get the email data based on the reply ID
        $emailResponse = getEmailData($id);

        if ($emailResponse) {
            // Map the email data to the $template array
            $template = [
                'v_subject' => $emailResponse['v_subject'],  // Assuming 'subject' is a field in the email data
                'to'        => $emailResponse['v_toemail'], // Assuming 'to_email' is a field in the email data
                'from'      => $emailResponse['v_fromemail'], // Assuming 'from_email' is a field in the email data
                'body'      => $emailResponse['v_body']        // Assuming 'body' is a field in the email data
            ];



            // Call the Email_Replay_AI function with the populated template
            $aiResponse = Email_Replay_AI($template);

            if(!empty($aiResponse)){
                // Return a successful JSON response
                echo getJsonResponse("success", $aiResponse);
                
            }else{     
                // Return a successful JSON response
                echo getJsonResponse("error", "No Response From API");
            }

        } else {
            // No email data found for the provided ID
            echo getJsonResponse("error", "No email data found for the provided ID.");
        }
    } else {
        // ID is not provided or is invalid
        echo getJsonResponse("error", "Invalid or missing ID.");
    }

} else {
    // Invalid request method or missing Action parameter
    echo getJsonResponse("error", "Invalid request method or missing required parameters.");
}





