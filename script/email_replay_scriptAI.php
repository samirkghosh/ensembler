<?php
/**
 * Email Replay from AI
 * Author: Aarti
 * Date: 19-10-2024
 * Description: This Script Handling Email replay suggesting using AI tools
 **/

// Database connection details
include_once("../config/web_mysqlconnect.php");

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

        Email_Replay_AI($childdb);
    }
}else{
    echo "No company databases found."; die;
}

function Email_Replay_AI($childdb){
    global $link,$childdb,$email_replay_url;

    $sql = "SELECT * FROM $childdb.web_email_information where email_replay_status='1' order by d_email_date DESC limit 10 ";
    $resu = mysqli_query($link, $sql);
    $num = mysqli_num_rows($resu);
    if ($num > 0) {
        while($template=mysqli_fetch_array($resu)){

            // Step 2: Prepare the request payload
            $EMAIL_ID = $template['EMAIL_ID'];
            $payload = [
                'subject' => $template['v_subject'],
                'to' => $template['v_toemail'],
                'from_' => $template['v_fromemail'],
                'body' => $template['v_body']
            ];

            // Step 3: Make a cURL request
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

            // Step 4: Handle error or success
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
            // Step 5: Store the response or error in `email_template_replay` table
            $sqlmit="UPDATE $childdb.web_email_information  SET email_replay_status = '2' WHERE EMAIL_ID ='$EMAIL_ID'";
            $resultt = mysqli_query($link, $sqlmit) or die(mysqli_error($link));
            echo $sqlmit; echo"<br/>";echo"<br/>";

            $stmt = " INSERT INTO $childdb.email_template_replay (email_id, response, http_status, error_message) 
                VALUES ('$EMAIL_ID', '$body', $http_status, '$error_message')";
            echo $stmt; echo"<br/>";echo"<br/>";
            $resultt = mysqli_query($link, $stmt) or die(mysqli_error($link));

            // Check if data was inserted successfully
            if ($resultt) {
                echo "Response stored successfully!";
            } else {
                echo "Error storing the response.";
            }
        }
    } else {
        echo "No email template found.";
        echo "<br>......All checked analyze sentiment .........."; echo"<br/>";echo"<br/>";;
        exit;
    }
}
?>