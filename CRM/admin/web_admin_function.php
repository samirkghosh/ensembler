<?php
/**
 * functions file 
 * Auth:AARTI  & VASTVIKTA NISHAD
 * DATE: 16-01-24 
 *  This file is used to fetch the  data
 * Insert Update  or delete the data
 * 
 **/
// Added code for adding audit log on update insert and delete [vastvikta][13-02-2025]
// Add code for logging code
include_once("../../logs/config.php");
include_once("../../logs/logs.php");

include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CSRF validation function
function validateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        logSecurityEvent('csrf_validation_failed', 'Missing CSRF token');
        return false;
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logSecurityEvent('csrf_validation_failed', 'Invalid CSRF token');
        return false;
    }
    
    return true;
}

// Validate CSRF token for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken()) {
        die('CSRF token validation failed');
    }
}

if($_POST['action'] == 'Submit_category'){
    insert_category();
}
if($_POST['action']=='category_delete'){
    category_delete();
}
if($_POST['action']=='subcategory_delete'){
    subcategory_delete();
}
if($_POST['action']=='Submit_subcategory'){
    insert_subcategory();
}
if($_POST['action']=='province_delete'){
    province_delete();
}
if($_POST['action']=='mail_delete'){
    mail_delete();
}
if($_POST['action']=='sms_delete'){
    sms_delete();
}
if($_POST['action']=='delete_bulletin'){
delete_bulletin();
}
if($_POST['action']=='Submit_emailadhoc'){
    $emailadhoc = isset($_POST['emailadhoc']) ? $_POST['emailadhoc'] : '';
    adhoc_mail_update($emailadhoc);
}
if ($_POST['action'] == 'project_delete') {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    if (!empty($id)) {
        project_delete($id);
    }
}
if ($_POST['action'] == 'submit_province' || $_POST['action'] == 'update_province') {
    insert_or_update_province();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'village_delete') {
        village_delete();
    }
}
if ($_POST['action'] == 'submit_village' || $_POST['action'] == 'update_village') {
    insert_or_update_village($link);
}
if ($_POST['action'] == 'submit_mail' || $_POST['action'] == 'update_mail') {
    insert_or_update_mail($link);
}
if ($_POST['action'] == 'submit_sms' || $_POST['action'] == 'update_sms') {
    insert_or_update_sms($link);
}
if (isset($_POST['action']) && ($_POST['action'] == 'submit_project' || $_POST['action'] == 'update_project')) {
    insert_or_update_project($link);
}  
if (isset($_POST['action']) && $_POST['action'] == 'assign_user') {
    assign_user($link, $_POST['user'], $_POST['assignto'], $db);
} elseif (isset($_POST['action']) && $_POST['action'] == 'unassign_user') {
    unassign_user($link, $_POST['user'], $_POST['assignto'], $db);
}
if($_POST['action']=='status_delete'){
    status_delete();
}
if ($_POST['action'] == 'submit_status' || $_POST['action'] == 'update_status') {
    insert_or_update_status();
}
if($_POST['action']=='base_delete'){
    base_delete();
}
if ($_POST['action'] == 'submit_base' || $_POST['action'] == 'update_base') {
    handleBaseSubmit();
}
if($_POST['action']=='Submit_escalation'){
    update_escalation();
}
if($_POST['action']=='update_smtp'){
    update_smtp();
}
if($_POST['action']=='update_imap'){
    update_imap();
}
if($_POST['action']=='disposition_delete'){
    disposition_delete();
}
if ($_POST['action'] == 'submit_disposition' || $_POST['action'] == 'update_disposition') {
    insert_or_update_disposition();
}
if($_POST['action'] == 'submit_callbacks'){
    updateCallbacks();
}
if($_POST['action']=='bulk_delete'){
    bulk_delete();
}
if($_POST['action']=='update_bulk_data'){
    update_bulk_data();
}
if($_POST['action'] == 'update_sent_mail'){
    update_sent_mail();
}
if ($_POST['action'] == 'submit_webchat' || $_POST['action'] == 'update_webchat') {
    insert_or_update_webchat($link);
}
if($_POST['action']=='webchat_delete'){
    webchat_delete();
}
if ($_POST['action'] == 'submit_whatsapp' || $_POST['action'] == 'update_whatsapp') {
    insert_or_update_whatsapp($link);
}
if($_POST['action']=='whatsapp_delete'){
    whatsapp_delete();
}
if($_POST['action']=='spam_mail_delete'){
    spam_mail_delete();
}
if($_POST['action']=='spam_mail'){
    spam_mail();
}
// Call Knowledge Base Upload API for Document Upload :: Farhan Akhtar [04-02-2025]
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Upload_DocumentAPI') {

    header('Content-Type: application/json');

    // Define log file
    $logFile = "UPLOADAPIdebug.log";

	try {
        // Log the received request data for debugging
        // file_put_contents($logFile, "[" . date("Y-m-d H:i:s") . "] Received request: " . print_r($_REQUEST, true) . PHP_EOL, FILE_APPEND);

        // Get description from request :: Farhan Akhtar [04-02-2025]
        $description = isset($_POST['descDoc']) ? trim($_POST['descDoc']) : "";

        // (userid in session) :: Farhan Akhtar [04-02-2025]
        $created_by = $_SESSION['userid']; 
        
		$category = "General";

		// Call the API function
		$response = uploadFilesToAPI($uploadAPI_url, $_FILES, $category, $description, $created_by, $logFile);
		
		// Ensure valid JSON output
		if (!is_array($response) || empty($response)) {
            $errorMsg = "[" . date("Y-m-d H:i:s") . "] Invalid or empty API response: " . print_r($response, true) . PHP_EOL;
            file_put_contents($logFile, $errorMsg, FILE_APPEND);
            throw new Exception("Empty or invalid API response.");
		}

    
        // Encode response as JSON
        $jsonResponse = json_encode($response, JSON_PRETTY_PRINT);
        if ($jsonResponse === false) {
            throw new Exception("JSON encoding error: " . json_last_error_msg());
        }

        echo $jsonResponse;

	} catch (Exception $e) {
        http_response_code(400);
        
        // Log the exception
        $errorMsg = "[" . date("Y-m-d H:i:s") . "] Exception: " . $e->getMessage() . PHP_EOL;
        file_put_contents($logFile, $errorMsg, FILE_APPEND);
        
        // Return error as JSON response
		echo json_encode(["error" => $e->getMessage()]);
	}

	exit();
}
// function to update tbl connection sent mail details [vastvitka][25-04-2025]
function update_sent_mail() {
    global $db, $link;

    $sent_mail = validateInput($_POST["sent_mail"]);
    $case_count = validateNumeric($_POST["case_count"]);
    $status_active = validateNumeric($_POST['status_active']);

    $stmt = mysqli_prepare($link, "UPDATE $db.tbl_connection SET sent_mail = ?, case_count = ?, status_active = ? WHERE I_ID = 2");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sii", $sent_mail, $case_count, $status_active);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            echo "Updated Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to update sent mail settings');
            echo "Failed to Update!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for sent mail update');
        echo "Failed to Update!";
    }
}

// Function to upload files to the API :: Farhan Akhtar [04-02-2025]
function uploadFilesToAPI($uploadAPI_url, $files, $category, $description, $created_by, $logFile) {
    global $db;
    // Initialize an array to store the API responses
    $responses = [];

    // Loop through each file in the $files array
    foreach ($files['file']['name'] as $index => $name) {
        // Prepare the file data for the API request
        $fileData = [
            'file' => new CURLFile($files['file']['tmp_name'][$index], $files['file']['type'][$index], $name),
            'category' => $category,
            'description' => $description,
            'created_by' => $created_by
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $uploadAPI_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Adjust based on your SSL requirements
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Adjust based on your SSL requirements

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $errorMessage = "[" . date("Y-m-d H:i:s") . "] cURL Error: " . curl_error($ch) . PHP_EOL;
            file_put_contents($logFile, $errorMessage, FILE_APPEND);
            throw new Exception("cURL error: " . curl_error($ch));
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $response_data = json_decode($response, true);

        // Check if the response is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errorMessage = "[" . date("Y-m-d H:i:s") . "] JSON Error: " . json_last_error_msg() . " | Raw Response: " . $response . PHP_EOL;
            file_put_contents($logFile, $errorMessage, FILE_APPEND);
            throw new Exception("Invalid JSON response from API.");
        }
            // Log audit only on success
            add_audit_log($created_by, 'document_upload', '', "New Document added: $name", $db);
      
        // Add the response to the responses array
        $responses[] = $response_data;
    }
    
    // Return the decoded responses
    return $responses;
}

// Function to format size of uploaded files :: Farhan Akhtar [11-03-2025]
function formatSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB'; // Convert to MB
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB'; // Convert to KB
    } else {
        return $bytes . ' bytes'; // Keep in bytes if small
    }
}

// code for view uploaded files :: Farhan Akhtar [11-03-2025]
if (isset($_POST['action']) && $_POST['action'] === "view_document") {

    header('Content-Type: application/json'); // Ensure JSON response

    // Retrieve action from POST request
    $category = "General"; // Replace with the actual category or get it from the request
    $fileList = getFilesFromApi($viewDocs_url, $category);

    if (!empty($fileList)) {
        echo json_encode([
            "status" => "success",
            "files" => $fileList
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No records found"
        ]);
    }
}

// Function to get uploaded files :: Farhan Akhtar [11-03-2025]
function getFilesFromApi($viewDocs_url, $category) {
    $apiUrl = $viewDocs_url.$category;
    $files = array();

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        // Handle cURL error
        return $files;
    }

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    $delete_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';

    $view_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-primary"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';


    // Process the API response
    if (isset($data['documents']) && is_array($data['documents'])) {
        foreach ($data['documents'] as $document) {
            $files[] = [
                "name" => $document['filename'], // Filename
                "size" => formatSize($document['file_size_mb'] * 1024 * 1024), // Convert MB to bytes for formatting
                "description" => $document['description'], // Description
                "created_by" => getUserName($document['created_by']), // Created by
                "date_uploaded" => date("d-m-Y H:i:s", strtotime($document['date_uploaded'])), // Convert date_uploaded to timestamp
                "action" =>'
                             <a href="javascript:void(0)" class="view-doc" id="'.$document['filename'].'" data-category="'.$document['category'].'">'.$view_icon.'</a>
                             <a href="javascript:void(0)" class="delete-doc" id="'.$document['filename'].'" data-category="'.$document['category'].'">'.      $delete_icon.'</a>
    
                           '
            ];
        }

        // Sort files by 'modified' field in descending order (latest first)
        usort($files, function($a, $b) {
            return strtotime($b['date_uploaded']) - strtotime($a['date_uploaded']);
        });
        
    }

    return $files;
}


if (isset($_POST['action']) && $_POST['action'] === "delete_document") {
    // Ensure the category is provided
    if (isset($_POST['category'])) {
        $category = $_POST['category'];
        $filename = $_POST['filename'];

        // Call the delete function
        $result = deleteDocument($deleteDoc_url,$category, $filename);

        // Return the result as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        // Handle missing category
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Category is required."
        ]);
    }
}

function deleteDocument($deleteDoc_url, $category, $filename) {
    global $db;

    $vuserid = $_SESSION['userid'];
    // Prepare the raw JSON data
    $rawData = json_encode([
        "category" => $category,
        "filename" => $filename
    ]);

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $deleteDoc_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Use DELETE method
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData); // Send raw JSON data
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Set content type to JSON
        'Content-Length: ' . strlen($rawData) // Set content length
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        // Handle cURL error
        return [
            "status" => "error",
            "message" => "Failed to connect to the API: " . curl_error($ch)
        ];
    }

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Log audit only on success
    if (isset($data['message']) && $data['message'] === "Document deleted successfully") {
    add_audit_log($vuserid, 'document_deleted', '', "Document Deleted: $filename", $db);
    }
    // Check if the deletion was successful
    if (isset($data['message']) && $data['message'] === "Document deleted successfully") {
        return [
            "status" => "success",
            "message" => $data['message']
        ];
    } else {
        return [
            "status" => "error",
            "message" => "Failed to delete the document"
        ];
    }
}


function fetchBackOfficer($link){
    global $db,$link;
    // changed query  to fetch only backofficer [vastvikta][11-04-2025]
    $sql = "SELECT c.AtxUserID, c.AtxUserName, a.DisplayName, a.atxGid, b.ugdContactID, b.atxGid 
    FROM $db.unigroupid a, $db.unigroupdetails b, $db.uniuserprofile c 
    WHERE a.atxGid = b.atxGid AND b.ugdContactID = c.AtxUserID AND a.atxGid='060000' AND c.AtxUserStatus = '1'
    GROUP BY b.ugdContactID ORDER BY AtxUserName ASC";

    $user_query = mysqli_query($link, $sql);
    if (!$user_query) {
        echo "Error fetching back officers: " . mysqli_error($link);
    }
    return $user_query;
}
/* Fetch customer type list */
function categorytype($id = ''){
    // Access global variables $db and $link for database connection
    global $db, $link;
    // Initialize an empty WHERE clause
    $where = '';
    // Check if $id is not empty, and construct the WHERE clause accordingly
    if (!empty($id)){
        $where = 'WHERE id IN (' . $id . ')';
    }
    // SQL query to select all columns from the 'complaint_type' table with optional WHERE clause
    $sql_type = "SELECT * FROM $db.complaint_type " . $where;
    // Execute the SQL query and store the result in $result_type
    $result_type = mysqli_query($link, $sql_type);
    // Return the result set containing customer types
    return $result_type;
}
/* Insert or update data in the 'web_category' table */
function insert_category() {
    global $db, $link;
    
    $category_name = validateInput($_POST['category_name']);
    $category_desc = validateInput($_POST['category_desc']);
    $category_type = validateInput($_POST['category_type']);
    $status = validateNumeric($_POST['status']);
    
    $stmt = mysqli_prepare($link, "INSERT INTO $db.web_category (vCategoryName, vCategoryDesc, vCategoryType, iStatus) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $category_name, $category_desc, $category_type, $status);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('category_added', "Category added: $category_name");
            echo "Category Added Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to add category');
            echo "Failed to Add Category!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for category insertion');
        echo "Failed to Add Category!";
    }
}

/* Fetch data for a specific category from the 'web_category' table */
function category_data($id){
    // Access global variables $db and $link for database connection
    global $db, $link;

    // SQL query to select all columns from the 'web_category' table for the specified category id
    $sql2 = "SELECT * FROM $db.web_category WHERE id = '" . $id . "'";

    // Execute the SQL query and store the result in $fetch2
    $fetch2 = mysqli_query($link, $sql2);

    // Fetch the associative array representing a row of data from the result set
    $row2 = mysqli_fetch_assoc($fetch2);

    // Create an array to store the fetched category data
    $categorydata = array();

    // Store category-related data in the $categorydata array
    $categorydata['category'] = $row2['category'];
    $categorydata['type'] = $row2['type'];
    $categorydata['VDescription'] = $row2['V_Description'];

    // Return the array containing the fetched category data
    return $categorydata;
}

function test_input($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Function to delete a web category by updating its status to inactive (status=0)
function category_delete() {
    global $db, $link;
    
    $id = validateNumeric($_POST['id']);
    
    $stmt = mysqli_prepare($link, "DELETE FROM $db.web_category WHERE I_CategoryID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('category_deleted', "Category deleted: ID $id");
            echo "Category Deleted Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to delete category');
            echo "Failed to Delete Category!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for category deletion');
        echo "Failed to Delete Category!";
    }
}
/*Fetch category data for view page*/
function category_view(){
    // Access global variables $db and $link for database connection
    global $db, $link;
    // SQL query to select all rows from the 'web_category' table where status is 1 (active), ordered by category ascending
    $sql_document = "SELECT * FROM $db.web_category WHERE status=1 ORDER BY category ASC";
    // Execute the SQL query and store the result in $res
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result set containing active web categories
    return $res;
}

/* functions for view subcategory  Auth: VASTVIKTA NISHAD */
// Function to fetch subcategory data for viewing
function subcategory_view(){
    //connection to the database 
    global $db, $link;
    // SQL query to select data from the web_subcategory table
    $sql_document = "SELECT * FROM $db.web_subcategory WHERE status=1 ORDER BY subcategory ASC";
    // store the result in $res
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result set
    return $res;
}
// fetch category name
function category($cat)
{
    global $db, $link;
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($link, "SELECT category FROM `{$db}`.web_category WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $cat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $res = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $res ? $res['category'] : null;
}
//function to delete the  subcategory 
function subcategory_delete()
{
    global $db, $link;
    // Retrieving the id from the form and validating it
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        echo json_encode(['error' => true, 'error_msg' => 'Invalid ID provided.']);
        exit();
    }
    
    // Updating status of the subcategory on the basis of the id using prepared statement
    $sql_delete = "UPDATE `{$db}`.web_subcategory SET status = 0 WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql_delete);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $res = mysqli_stmt_execute($stmt);
    
    if (!$res){
        if (_DBGLOG_){
                   DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete error subcategory: ". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "Sub Category Database error";
        echo json_encode($response);
        exit();
    }
    
    // Get user ID from the session for audit log
    $vuserid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;

    //fetching subcategory for audit log
    $sql1 = "SELECT subcategory FROM `{$db}`.web_subcategory WHERE id = ?";
    $stmt1 = mysqli_prepare($link, $sql1);
    mysqli_stmt_bind_param($stmt1, 'i', $id);
    mysqli_stmt_execute($stmt1);
    $res1 = mysqli_stmt_get_result($stmt1);
    $row = mysqli_fetch_assoc($res1);
    $subcategory = $row ? $row['subcategory'] : 'N/A';
    mysqli_stmt_close($stmt1);
 
    add_audit_log($vuserid, 'delete_subcategory', '', 'Subcategory deleted '.$subcategory, $db);
    
    echo json_encode(['success' => true]);
    exit();
}
//function to insert/update data in subcategory
function insert_subcategory()
{
    global $db, $link;
    // Getting user ID from the session
    $vuserid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    
    // Retrieving data from the request and sanitizing it
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
    $subcategory = filter_input(INPUT_POST, 'subcategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $time_hours = filter_input(INPUT_POST, 'time_hours', FILTER_VALIDATE_INT);
    $VDescription = filter_input(INPUT_POST, 'V_Description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $second_resolution = filter_input(INPUT_POST, 'second_resolution', FILTER_VALIDATE_INT);
    $third_resolution = filter_input(INPUT_POST, 'third_resolution', FILTER_VALIDATE_INT);
    
    // Sanitize user arrays
    $level1_users_raw = isset($_POST['level1_users']) ? $_POST['level1_users'] : [];
    $level2_users_raw = isset($_POST['level2_users']) ? $_POST['level2_users'] : [];
    $level3_users_raw = isset($_POST['level3_users']) ? $_POST['level3_users'] : [];

    $level1_users = implode(",", array_map('intval', $level1_users_raw));
    $level2_users = implode(",", array_map('intval', $level2_users_raw));
    $level3_users = implode(",", array_map('intval', $level3_users_raw));

    // Getting current date
    $datedk = date("Y-m-d H:i:s");

    if (empty($id)) { // This is an INSERT
        // Checking for duplicate entry using prepared statement
        $sql_check_document = "SELECT id FROM `{$db}`.web_subcategory WHERE subcategory=? AND category=? AND status='1'";
        $stmt_check = mysqli_prepare($link, $sql_check_document);
        mysqli_stmt_bind_param($stmt_check, "si", $subcategory, $category);
        mysqli_stmt_execute($stmt_check);
        $result_query = mysqli_stmt_get_result($stmt_check);
        $num_document = mysqli_num_rows($result_query);
        mysqli_stmt_close($stmt_check);

        if ($num_document == 0) {
            // Inserting new subcategory with prepared statement
            $sql_document = "INSERT INTO `{$db}`.web_subcategory(subcategory, V_Description, createdby, createdon, category, resolution_time_in_hours, second_resolution_time, third_resolution_time, level1_users, level2_users, level3_users) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($link, $sql_document);
            mysqli_stmt_bind_param($stmt_insert, "ssisiiisss", $subcategory, $VDescription, $vuserid, $datedk, $category, $time_hours, $second_resolution, $third_resolution, $level1_users, $level2_users, $level3_users);
            $res = mysqli_stmt_execute($stmt_insert);
            
            if ($res) {
                add_audit_log($vuserid, 'insert_subcategory', '', 'New Subcategory added '.$subcategory, $db);
                echo "Success: Sub Category name added successfully!";
            } else {
                if (_DBGUp_){ DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error subcategory: ". mysqli_error($link)); }
                echo "Error: Could not add subcategory.";
            }
            mysqli_stmt_close($stmt_insert);
            exit;
        } else {
            echo "Error: Duplicate Entry Of Sub Category name";
            exit;
        }
    } else { // This is an UPDATE
        // Checking for duplicate entry during update
        $sql_check_document = "SELECT id FROM `{$db}`.web_subcategory WHERE subcategory=? AND category=? AND status='1' AND id != ?";
        $stmt_check = mysqli_prepare($link, $sql_check_document);
        mysqli_stmt_bind_param($stmt_check, "sii", $subcategory, $category, $id);
        mysqli_stmt_execute($stmt_check);
        $result_query = mysqli_stmt_get_result($stmt_check);
        $num_document = mysqli_num_rows($result_query);
        mysqli_stmt_close($stmt_check);

        if ($num_document == 0) {
            // Updating subcategory with prepared statement
            $sql_update = "UPDATE `{$db}`.web_subcategory SET 
                                subcategory = ?, V_Description = ?, category = ?, 
                                resolution_time_in_hours = ?, second_resolution_time = ?, 
                                third_resolution_time = ?, level1_users = ?, 
                                level2_users = ?, level3_users = ?, createdon = ? 
                                WHERE id = ?";
            $stmt_update = mysqli_prepare($link, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "ssiiiissssi", $subcategory, $VDescription, $category, $time_hours, $second_resolution, $third_resolution, $level1_users, $level2_users, $level3_users, $datedk, $id);
            $res = mysqli_stmt_execute($stmt_update);

            if ($res) {
                add_audit_log($vuserid, 'update_subcategory', '', 'Subcategory updated '.$subcategory, $db);
                echo "Success: Sub Category updated successfully!";
            } else {
                echo "Error: Could not update subcategory.";
            }
            mysqli_stmt_close($stmt_update);
            exit;
        } else {
            echo "Error: Duplicate Entry Of Sub Category name";
            exit;
        }
    }
}

//function to fetch data in subcategory table 
function getSubCategoryData($link, $id) {
    global $db;
    $subcategoryData = array();
    
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        return null; // Invalid ID
    }

    // Query to retrieve user data for three levels
    $sqlagent = "SELECT AtxUserID, AtxUserName FROM `{$db}`.uniuserprofile WHERE AtxUserStatus='1' ORDER BY AtxUserName ASC";
    // Execute the query for each level and store the results in the array
    $subcategoryData['result1'] = mysqli_query($link, $sqlagent);
    $subcategoryData['result2'] = mysqli_query($link, $sqlagent);
    $subcategoryData['result3'] = mysqli_query($link, $sqlagent);
    
    // If an id is provided, fetch data for a specific subcategory
    if ($id) {
        // Query to retrieve subcategory data based on the provided id using prepared statement
        $sql = "SELECT * FROM `{$db}`.web_subcategory WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Process the result and populate the subcategoryData array
        if ($row = mysqli_fetch_assoc($result)) {
            $subcategoryData['category'] = $row['category'];
            $subcategoryData['subcategory'] = $row['subcategory'];
            $subcategoryData['time_hours'] = $row['resolution_time_in_hours'];
            $subcategoryData['second_resolution'] = $row['second_resolution_time'];
            $subcategoryData['third_resolution'] = $row['third_resolution_time'];
            $subcategoryData['level1_users'] = $row['level1_users'];
            $subcategoryData['level2_users'] = $row['level2_users'];
            $subcategoryData['level3_users'] = $row['level3_users'];
            $subcategoryData['VDescription'] = $row['V_Description'];
        }
        mysqli_stmt_close($stmt);
    }
    // Return the array containing subcategory data
    return $subcategoryData;
}

/* functions for view county */
//function for viewing data from web_city for view county page
function view_county(){
    // Security Fix by AI - 2024-07-22: Made the query safe from SQL injection.
    global $db, $link;
    // SQL query to select all records from the web_city table where status is '1'
    $sql_document = "SELECT * FROM `{$db}`.web_city WHERE status='1'";
    // Execute the query
    $res = mysqli_query($link, $sql_document);
    // Return the result set
    return $res;
}
//function to fetch city data from the database 
function getCityData($link, $id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    $cityData = array();
    if (empty($id) || !is_numeric($id)) {
        return $cityData;
    }

    $sql = "SELECT `id`, `city` FROM `web_city` WHERE `id` = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $cityData['id'] = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $cityData['city'] = htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8');
    }
    mysqli_stmt_close($stmt);
    return $cityData;
}

//function to delete  province
function province_delete()
{
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (empty($id)) {
        echo json_encode(['error' => true, 'message' => 'Invalid ID.']);
        return;
    }

    $sql_delete = "UPDATE `{$db}`.web_city SET status='0' WHERE id=?";
    $stmt = mysqli_prepare($link, $sql_delete);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $res = mysqli_stmt_execute($stmt);
    
    if (!$res) {
        if (_DBGLOG_) { DbgLog(_LOG_ERROR, __LINE__, __FILE__, "DB Delete error Province: " . mysqli_error($link)); }
        echo json_encode(['error' => TRUE, 'error_msg' => "Province Database error"]);
        exit();
    }
    
    echo json_encode(['success' => true]);

    $vuserid = $_SESSION['userid'];
    $city = 'N/A';
    $sql1 = "SELECT city FROM `{$db}`.web_city WHERE id = ?";
    $stmt1 = mysqli_prepare($link, $sql1);
    mysqli_stmt_bind_param($stmt1, 'i', $id);
    mysqli_stmt_execute($stmt1);
    $res1 = mysqli_stmt_get_result($stmt1);
    if($row = mysqli_fetch_assoc($res1)) {
        $city = $row['city'];
    }
    mysqli_stmt_close($stmt1);
 
    add_audit_log($vuserid, 'delete_province', '', 'Province deleted '.$city, $db);
    exit();
}
//function to handle the update and insert  request for the county / province
function insert_or_update_province(){
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $vuserid = $_SESSION['userid'];

    if (empty($city)) {
        echo json_encode(['error' => true, 'message' => 'Province name cannot be empty.']);
        return;
    }

    if (empty($id)) {
        $sql = "INSERT INTO `{$db}`.`web_city` (`city`, `status`) VALUES (?, '1')";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $city);
        $log_action = 'insert_province';
        $log_message = 'New Province added '.$city;
    } else {
        $sql = "UPDATE `{$db}`.`web_city` SET `city`=?, `status`='1' WHERE `id`=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $city, $id);
        $log_action = 'update_province';
        $log_message = 'Province updated '.$city;
    }

    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        if (_DBGLOG_) { DbgLog(_LOG_ERROR, __LINE__, __FILE__, "DB insert or update error Province: " . mysqli_error($link)); }
        echo json_encode(['error' => TRUE, 'error_msg' => "Province Database error"]);
        exit();
    }
    
    add_audit_log($vuserid, $log_action, '', $log_message, $db);
    echo json_encode(['success' => true]);
    exit();
}

/*funciton  for view sub county */
//function to load the data from web_Village table 
function view_village() {
    global $db, $link;
    // SQL query to select all records from the web_Village table where status is '1'
    $sql_document = "SELECT * FROM $db.web_Village WHERE status='1'";
    // Execute the query or die with an error message if it fails
    $res = mysqli_query($link, $sql_document) or die("Could not select: " . mysqli_error($link));
    // Return the result set
    return $res;
}

//function to delete the village data
function village_delete(){
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
    // SQL query to update the status of the village to '0' (soft delete)
    $sql_delete = "UPDATE $db.web_Village SET status=0 WHERE id='$id'";
    // Execute the SQL query
    $res = mysqli_query($link, $sql_delete);

    if (!$res){
        if (_DBGLOG_){
                   DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete error District: $sql_delete". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "District Database error";
        echo json_encode($response);
        exit();
    }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error'; // Return an error message to the client
    }
}
//function to get province data on the basis of id  of the village  form web_city for web_new_village 
function getDistrict($id){
    global $db, $link;
    // SQL query to select id and city from web_city where status is '1'
    $sqldistrict = "SELECT id, city FROM web_city WHERE status=1";
    // Execute the SQL query
    $result =  mysqli_query($link, $sqldistrict);
    // Return the result set
    return $result;
}

//function to get district data from the web_village table on the basis of id 
function getVillageData($link, $id){
    global $db;
    $villageData = array();
    // Check if $id is not empty
    if ($id != '') {
        // SQL query to select all columns from web_Village based on the provided id
        $sql = "SELECT * FROM $db.web_Village WHERE id = '$id'";
        // Execute the SQL query
        $result = mysqli_query($link, $sql);
        // Check if the query was successful and if there are rows returned
        if ($result && mysqli_num_rows($result) > 0) {
            // Fetch the associative array representing the result row
            $villageData = mysqli_fetch_assoc($result);
        }
    }
    // Return the villageData array
    return $villageData;
}

//function to  handle insert or update of the   district in the database 
function insert_or_update_village($link){
    global $db, $link;
    // Retrieve data from the POST request and perform proper escaping
    $id = isset($_POST['id']) ? mysqli_real_escape_string($link, $_POST['id']) : '';
    $district = isset($_POST['district']) ? mysqli_real_escape_string($link, $_POST['district']) : '';
    $village = isset($_POST['village']) ? mysqli_real_escape_string($link, $_POST['village']) : '';
    $description = isset($_POST['V_Description']) ? mysqli_real_escape_string($link, $_POST['V_Description']) : '';
    
    // Check if all required fields are available
    if (!$district || !$village) {
        $response = array('error' => TRUE, 'error_msg' => "District or Village cannot be empty.");
        echo json_encode($response);
        exit();
    }
    
    // Determine the SQL query based on whether $id is empty
    if (empty($id)) {
        // SQL query for insertion if ID is empty
        $sql = "INSERT INTO `$db`.`web_Village` (`vVillage`, `iDistrictID`, `status`, `createdby`, `createdon`, `V_Description`)
                VALUES ('$village', '$district', '1', '1', CURRENT_TIMESTAMP, '$description')";
    } else {
        // SQL query for update if ID is not empty
        $sql = "UPDATE `$db`.`web_Village` SET `vVillage`='$village', `iDistrictID`='$district', `V_Description`='$description' WHERE `id`='$id'";
    }
    
    // Log the SQL query for debugging
    error_log("Executing SQL: $sql");

    // Execute the SQL query
    $result = mysqli_query($link, $sql);
    
    // Log any SQL errors
    if (!$result) {
        error_log("SQL Error: " . mysqli_error($link));
        if (_DBGLOG_){
            DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB Update or insert error: $sql - " . mysqli_error($link));
        }
        $response = array('error' => TRUE, 'error_msg' => "District Database error: " . mysqli_error($link));
        echo json_encode($response);
        exit();
    }
    
    // Check if the query was successful
    if ($result) {
        $response = array('error' => FALSE, 'message' => 'success');
    } else {
        $response = array('error' => TRUE, 'error_msg' => "Unexpected error: " . mysqli_error($link));
    }
    
    echo json_encode($response);
}

/* functions for view mail format*/
// Function to retrieve all mail formats with MailStatus set to 1
function view_mail_data() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    // SQL query to select all records from tbl_mailformats where MailStatus is 1
    $sql_document = "SELECT * FROM `{$db}`.tbl_mailformats WHERE MailStatus = 1";
    // Execute the query
    $res = mysqli_query($link, $sql_document);
    // Return the result set
    return $res;
}

// Function to soft delete a mail format by updating MailStatus to 0
function mail_delete() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if (empty($id)) {
        echo json_encode(['error' => true, 'message' => 'Invalid ID.']);
        return;
    }

    // SQL query to update MailStatus to 0 for the specified MailTypeID
    $sql_update = "UPDATE `{$db}`.tbl_mailformats SET MailStatus = '0' WHERE MailTypeID = ?";
    $stmt = mysqli_prepare($link, $sql_update);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $res = mysqli_stmt_execute($stmt);

    if (!$res){
        if (_DBGLOG_){
                   DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB Delete error Mail status : ". mysqli_error($link));
        }
        echo json_encode(['error' => TRUE, 'error_msg' => "Mail status  Database error"]);
        exit();
    }
    
    echo json_encode(['success' => true]);
    
     // Get user ID from the session
     $vuserid = $_SESSION['userid'];
     //fetching  mailformat [vastvikta][14-02-2025]
     $sql1 = "SELECT MailTemplateName FROM `{$db}`.tbl_mailformats WHERE MailTypeID = ?";
     $stmt1 = mysqli_prepare($link, $sql1);
     mysqli_stmt_bind_param($stmt1, "i", $id);
     mysqli_stmt_execute($stmt1);
     $res1 = mysqli_stmt_get_result($stmt1);
     $MailTemplateName = "N/A";
     if ($row = mysqli_fetch_assoc($res1)) {
        $MailTemplateName = $row['MailTemplateName'];
     }
     mysqli_stmt_close($stmt1);
  
     //deleting mail format  [vastvikta][14-02-2025]
     add_audit_log($vuserid, 'delete_mailformat', '', 'Mail format deleted '.$MailTemplateName, $db);
    
    exit();
}
// Function to retrieve mail format data based on the provided MailTypeID
function getMailFormatData($link, $id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db;
    $mailFormatData = array();
    if (empty($id) || !is_numeric($id)) {
        return $mailFormatData;
    }

    $sql = "SELECT * FROM `{$db}`.tbl_mailformats WHERE MailTypeID = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        foreach ($row as $key => $value) {
            $mailFormatData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    mysqli_stmt_close($stmt);
    return $mailFormatData;
}
// Function to handle insert and update requests for mail formats
function insert_or_update_mail($link) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection, added input validation.
    global $db;
    $vuserid = $_SESSION['userid'];

    // Retrieve and clean POST data
    $MailTypeID = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $MailTemplateName = filter_input(INPUT_POST, 'template_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $MailType = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $MailSubject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $MailGreeting = filter_input(INPUT_POST, 'greeting', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $MailBody = isset($_POST['body']) ? $_POST['body'] : ''; // Allow HTML, will be handled on output
    $MailSignature = isset($_POST['signature']) ? $_POST['signature'] : ''; // Allow HTML
    $MailDescription = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $MailStatus = 1;
    $MailExpiry = filter_input(INPUT_POST, 'expiry', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);

    if ($_POST['action'] == 'submit_mail' && empty($MailTypeID)) {
        $sql = "INSERT INTO `{$db}`.`tbl_mailformats` 
                (`MailTemplateName`, `MailType`, `MailSubject`, `MailGreeting`, `MailBody`, `MailSignature`, `MailDescription`, `MailStatus`, `MailExpiry`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = mysqli_prepare($link, $sql);
        
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, 'sssssssii', $MailTemplateName, $MailType, $MailSubject, $MailGreeting, $MailBody, $MailSignature, $MailDescription, $MailStatus, $MailExpiry);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                add_audit_log($vuserid, 'insert_mailformat', '', 'New Mail format added '.$MailTemplateName, $db);
                echo 'success';
            } else {
                echo json_encode(['error' => true, 'error_msg' => 'Database error: ' . mysqli_stmt_error($stmt_insert)]);
            }
            mysqli_stmt_close($stmt_insert);
        } else {
            echo json_encode(['error' => true, 'error_msg' => 'Prepare statement error: ' . mysqli_error($link)]);
        }
    } elseif ($_POST['action'] == 'update_mail' && !empty($MailTypeID)) {
        // Update the mail format
        $sql = "UPDATE `{$db}`.`tbl_mailformats` 
                SET `MailTemplateName`=?, `MailType`=?, `MailSubject`=?, `MailGreeting`=?, 
                    `MailBody`=?, `MailSignature`=?, `MailDescription`=?, `MailStatus`=?, `MailExpiry`=?
                WHERE `MailTypeID`=?";
        
        $stmt_update = mysqli_prepare($link, $sql);
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, 'sssssssiis', $MailTemplateName, $MailType, $MailSubject, $MailGreeting, $MailBody, $MailSignature, $MailDescription, $MailStatus, $MailExpiry, $MailTypeID);

            if (mysqli_stmt_execute($stmt_update)) {
                add_audit_log($vuserid, 'update_mailformat', '', 'Mail format updated '.$MailTemplateName, $db);
                echo 'success';
            } else {
                echo json_encode(['error' => true, 'error_msg' => 'Database error: ' . mysqli_stmt_error($stmt_update)]);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            echo json_encode(['error' => true, 'error_msg' => 'Prepare statement error: ' . mysqli_error($link)]);
        }
    }
}

/*FUNCTION  FOR VIEW SMS FORMAT*/
// Function to retrieve all SMS formats with smsstatus set to 1
function view_sms_data(){
    global $db, $link;
    // SQL query to select all records from tbl_smsformat where smsstatus is 1
    $sql_document = "SELECT * FROM $db.tbl_smsformat WHERE smsstatus = 1";
    // Execute the query or die with an error message if it fails
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result set
    return $res;
}
// Function to soft delete an SMS format by updating smsstatus to 0
function sms_delete() {
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
    // SQL query to update smsstatus to 0 for the specified id
    $sql_update = "UPDATE $db.tbl_smsformat SET smsstatus = '0' WHERE id = '$id'";
    // Execute the SQL query
    $res = mysqli_query($link, $sql_update);
        if (!$res){
            if (_DBGLOG_){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error SMS format : $sql_update". mysqli_error($link));
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "SMS format Database error";
            echo json_encode($response);
            exit();
        }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
    // Get user ID from the session
    $vuserid = $_SESSION['userid'];
    //fetching  smsformat [vastvikta][14-02-2025]
    $sql1 = "SELECT smstemplatename FROM $db.tbl_smsformat WHERE id = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $smstemplatename = $row['smstemplatename'];
 
    //deleting SMS format  [vastvikta][14-02-2025]
    add_audit_log($vuserid, 'delete_smsformat', '', 'SMS format deleted '.$smstemplatename, $db);
   
}

function delete_bulletin() {
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
    // SQL query to delete_bulletin for the specified id
    $sql_update = "delete from $db.tbl_bulletinboard where id = $id";
     // Execute the SQL query
    $res = mysqli_query($link, $sql_update);
        if (!$res){
            if (_DBGLOG_){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete error bulletin board : $sql_update". mysqli_error($link));
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "Bulletin board  Database error";
            echo json_encode($response);
            exit();
        }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
}

// Function to get SMS template data from tbl_smsformat based on the provided id
function getSmsData($link, $id) {
    global $db;
    $smsData = array();
    // Check if $id is not empty
    if ($id) {
        // Use prepared statements to prevent SQL injection
        $sql = "SELECT * FROM $db.tbl_smsformat WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);
        // Bind the parameters and execute the statement
        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);
        // Iterate through the result set and populate the $smsData array
        while ($row = mysqli_fetch_assoc($result)) {
            $smsData['template_name'] = $row['smstemplatename'];
            $smsData['type'] = $row['smstype'];
            $smsData['header'] = $row['smsheader'];
            $smsData['body'] = $row['smsbody'];
            $smsData['footer'] = $row['smsfooter'];
            $smsData['description'] = $row['smsdescription'];
            $smsData['expiry'] = $row['smsexpiry'];
        }
    }
    // Return the $smsData array
    return $smsData;
}
// Function for updating and inserting data in tbl_smsformat
function insert_or_update_sms($link){
    global $db,$link;
    $vuserid = $_SESSION['userid'];
    // Retrieve data from the POST request and perform proper escaping
    $SmsID = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $SmsTemplateName = isset($_POST['template_name']) ? mysqli_real_escape_string($link, $_POST['template_name']) : '';
    $SmsType = isset($_POST['type']) ? mysqli_real_escape_string($link, $_POST['type']) : '';
    $SmsHeader = isset($_POST['header']) ? mysqli_real_escape_string($link, $_POST['header']) : '';
    $SmsBody = isset($_POST['body']) ? mysqli_real_escape_string($link, $_POST['body']) : '';
    $SmsFooter = isset($_POST['footer']) ? mysqli_real_escape_string($link, $_POST['footer']) : '';
    $SmsDescription = isset($_POST['description']) ? mysqli_real_escape_string($link, $_POST['description']) : '';
    $SmsStatus = 1; // Assuming '1' is the default status
    $SmsExpiry = isset($_POST['expiry']) ? mysqli_real_escape_string($link, $_POST['expiry']) : 0;
    // Check the action type (submit or update)
    if ($_POST['action'] == 'submit_sms') {
        $sql = "INSERT INTO `$db`.`tbl_smsformat` 
                (`smstemplatename`, `smstype`, `smsheader`, `smsbody`, `smsfooter`, `smsdescription`, `smsstatus`, `smsexpiry`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = mysqli_prepare($link, $sql);
        
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, 'ssssssii', 
                $SmsTemplateName, $SmsType, $SmsHeader, $SmsBody, $SmsFooter, $SmsDescription, $SmsStatus, $SmsExpiry);
            
            $SmsTemplateName = $_POST['template_name'];
            $SmsType = $_POST['type'];
            $SmsHeader = $_POST['header'];
            $SmsBody = $_POST['body'];
            $SmsFooter = $_POST['footer'];
            $SmsDescription = $_POST['description'];
            $SmsStatus = 1; // Assuming '1' is the default status
            $SmsExpiry = $_POST['expiry'];
            
            $insert_success = mysqli_stmt_execute($stmt_insert);
            
            if ($insert_success) {
                $SmsID = mysqli_insert_id($link); // Get the auto-generated SmsID
                echo 'success'; // Return success message to the client
            } else {
                $response['error'] = TRUE;
                $response['error_msg'] = 'Database error: ' . mysqli_stmt_error($stmt_insert);
                echo json_encode($response);
            }
            
            mysqli_stmt_close($stmt_insert);
        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = 'Prepare statement error: ' . mysqli_error($link);
            echo json_encode($response);
        }
          
			add_audit_log($vuserid, 'insert_smsformat', '', 'New SMS format added '.$SmsTemplateName, $db);
		
    }  elseif ($_POST['action'] == 'update_sms') {
        $sql = "UPDATE `$db`.`tbl_smsformat` 
                SET `smstemplatename`=?, 
                    `smstype`=?, 
                    `smsheader`=?, 
                    `smsbody`=?, 
                    `smsfooter`=?, 
                    `smsdescription`=?, 
                    `smsstatus`=?, 
                    `smsexpiry`=?
                WHERE `id`=?";
        
        $stmt_update = mysqli_prepare($link, $sql);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, 'ssssssiii', 
                $SmsTemplateName, $SmsType, $SmsHeader, $SmsBody, $SmsFooter, $SmsDescription, $SmsStatus, $SmsExpiry, $SmsID);
            
            $SmsTemplateName = $_POST['template_name'];
            $SmsType = $_POST['type'];
            $SmsHeader = $_POST['header'];
            $SmsBody = $_POST['body'];
            $SmsFooter = $_POST['footer'];
            $SmsDescription = $_POST['description'];
            $SmsStatus = 1; // Assuming '1' is the default status
            $SmsExpiry = $_POST['expiry'];
            $SmsID = $_POST['id'];
            
            $update_success = mysqli_stmt_execute($stmt_update);
            
            if ($update_success) {
                echo 'success'; // Return success message to the client
            } else {
                $response['error'] = TRUE;
                $response['error_msg'] = 'Database error: ' . mysqli_stmt_error($stmt_update);
                echo json_encode($response);
            }
            
            mysqli_stmt_close($stmt_update);
        } else {
            $response['error'] = TRUE;
            $response['error_msg'] = 'Prepare statement error: ' . mysqli_error($link);
            echo json_encode($response);
        }

			add_audit_log($vuserid, 'update_smsformat', '', 'SMS format updated '.$SmsTemplateName, $db);
		
    }
    
}
// Function to retrieve active projects from web_projects table
function view_project() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    // SQL query to select all records from the web_projects table
    $sql_document = "SELECT * FROM `{$db}`.web_projects WHERE iStatus=1 ORDER BY vProjectName ASC";
    // Execute the query
    $res = mysqli_query($link, $sql_document);
    // Return the result set
    return $res;
}
// function to delete the project [vastvikta][08-02-2025]
function project_delete($id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    
    if (!is_numeric($id)) {
        echo json_encode(['error' => true, 'message' => 'Invalid ID.']);
        return;
    }

    $id = (int)$id;

    // Updating status of the project on the basis of the id  
    $sql_delete = "UPDATE `{$db}`.web_projects SET iStatus=0 WHERE pId=?";
    $stmt = mysqli_prepare($link, $sql_delete);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!$res) {
        // Error handling
        echo json_encode(['error' => true, 'message' => 'DB delete error.']);
        exit();
    }
    
    echo json_encode(['success' => true]);

    // Get user ID from the session
    $vuserid = $_SESSION['userid'];
   
    $vProjectName = project($id); // Re-use secured function
 
    add_audit_log($vuserid, 'delete_department', '', 'Department deleted '.$vProjectName, $db);
   
    exit(); // Terminate the script
}
// Function to retrieve project data based on the provided id in the new department form
function getProjectData($link, $id, $db) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    $data = array('vProjectName' => '', 'category' => '');
    if (empty($id) || !is_numeric($id)) {
        return $data;
    }

    $sql = "SELECT vProjectName, Type FROM `{$db}`.web_projects WHERE pId = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $data['vProjectName'] = htmlspecialchars($row['vProjectName']);
        $data['category'] = htmlspecialchars($row['Type']);
    }
    mysqli_stmt_close($stmt);
    return $data;
}
// Function to insert or update data in the web_projects table
function insert_or_update_project($link) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection, added CSRF protection and input validation.
    global $db;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['error' => true, 'message' => 'Error: Invalid CSRF token.']);
        return;
    }

    $vuserid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    $createdby = isset($_SESSION['logged']) ? $_SESSION['logged'] : '';
    $createdon = date("Y-m-d H:i:s");

    $category_id = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
    if ($category_id === null) {
        echo json_encode(['error' => true, 'message' => 'Error: Category not selected.']);
        return;
    }

    $vProjectName = filter_input(INPUT_POST, 'vProjectName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $project_code = substr(preg_replace('/[^a-z]/', '', strtolower(str_replace(' ', '', $vProjectName))), 0, 10);

    if (empty($id)) {
        // INSERT logic
        $sql = "INSERT INTO `{$db}`.web_projects (vProjectName, v_CreateBY, d_datetime, Type, project_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $vProjectName, $createdby, $createdon, $category_id, $project_code);
        $log_action = 'insert_department';
        $log_message = 'New Department added  '.$vProjectName;
    } else {
        // UPDATE logic
        $sql = "UPDATE `{$db}`.web_projects SET vProjectName = ?, Type = ?, project_code = ? WHERE pId = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sssi', $vProjectName, $category_id, $project_code, $id);
        $log_action = 'update_department';
        $log_message = 'Department updated '.$vProjectName;
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $log_message, $db);
        echo 'success';
    } else {
        echo 'Error: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

//FUNCTION TO SLEECT USER 
function select_user(){
    global $db,$link;
    $sql = "SELECT c.AtxUserID, c.AtxUserName, a.DisplayName, a.atxGid, b.ugdContactID, b.atxGid FROM $db.unigroupid a, $db.unigroupdetails b, $db.uniuserprofile c WHERE a.atxGid=b.atxGid AND b.ugdContactID = c.AtxUserID AND a.DisplayName='Backoffice Officer' GROUP BY b.ugdContactID ORDER BY AtxUserName ASC";
    return $sql;    
}
//functions  used in  web_user_proj_assign.php file Auth: VASTVIKTA NISHAD 06-02-2024
// Function to get the username based on the provided user ID
function getUserName($id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    if (!is_numeric($id)) return " ";
    
    $q = "SELECT AtxUserName FROM `{$db}`.uniuserprofile WHERE AtxUserID=?";
    $stmt = mysqli_prepare($link, $q);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    $output = " ";
    if ($res && $rs = mysqli_fetch_assoc($res)) {
        $output = htmlspecialchars($rs['AtxUserName']);
    }
    mysqli_stmt_close($stmt);
    return $output;
}

// Function to get the username based on the provided user ID and return it
function assignto($assignto) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    if (!is_numeric($assignto)) return null;

    $stmt = mysqli_prepare($link, "SELECT AtxUserName FROM `{$db}`.uniuserprofile WHERE AtxUserID=?");
    mysqli_stmt_bind_param($stmt, 'i', $assignto);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? htmlspecialchars($row['AtxUserName']) : null;
}
// Function to add an entry to the web_audit_history table for audit logging
function add_audit_log($user_id, $actionlog, $ticketid, $comments, $db, $types = ''){
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link; // Removed global $db as it is passed as a parameter
    $ip = getenv('REMOTE_ADDR');
    
    // Sanitize inputs
    $user_id_clean = filter_var($user_id, FILTER_VALIDATE_INT, array("options" => array("min_range"=>0)));
    $actionlog_clean = filter_var($actionlog, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $ticketid_clean = filter_var($ticketid, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $comments_clean = filter_var($comments, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $ip_clean = filter_var($ip, FILTER_VALIDATE_IP);
    $types_clean = filter_var($types, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $sql_ins1 = "INSERT INTO `{$db}`.web_audit_history(user_id, action, created_on, ticket_id, comments, ip_address, case_process_type) VALUES(?, ?, NOW(), ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql_ins1);
    mysqli_stmt_bind_param($stmt, 'isssss', $user_id_clean, $actionlog_clean, $ticketid_clean, $comments_clean, $ip_clean, $types_clean);
    
    $result = mysqli_stmt_execute($stmt);
    if (!$result){
        if (_DBGLOG_){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert  error Audit: ". mysqli_error($link));
        }
        // Avoid echoing directly in a function like this
    }
    mysqli_stmt_close($stmt);
}
// Function to retrieve the project name based on the provided project ID
function project($projectid) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $db, $link;
    if (!is_numeric($projectid)) return null;

    $stmt = mysqli_prepare($link, "SELECT vProjectName FROM `{$db}`.web_projects WHERE pId=?");
    mysqli_stmt_bind_param($stmt, 'i', $projectid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? htmlspecialchars($row['vProjectName']) : null;
}

/*FUNCTIONS USED IN  ASSIGNING DEPARTMENT*/
//functios to assign the department to the user Auth: VASTVIKTA NISHAD 06-02-2024
function assignProjectsToUser($link, $user, $assignto_arr, $db) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerabilities.
    $vuserid = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $user = (int)$user;

    foreach ($assignto_arr as $val) {
        $val = (int)$val;
        // Check if the assignment already exists in the web_project_assigne table
        $query = "SELECT project_id FROM `{$db}`.web_project_assigne WHERE user_id = ? AND project_id = ?";
        $stmt_check = mysqli_prepare($link, $query);
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "ii", $user, $val);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            
            if (mysqli_stmt_num_rows($stmt_check) === 0) {
                mysqli_stmt_close($stmt_check);
                
                // Prepare the insertion query
                $queryInsert = "INSERT INTO `{$db}`.web_project_assigne (project_id, user_id) VALUES (?, ?)";
                $stmtInsert = mysqli_prepare($link, $queryInsert);
                if ($stmtInsert) {
                    mysqli_stmt_bind_param($stmtInsert, "ii", $val, $user);
                    mysqli_stmt_execute($stmtInsert);
                    mysqli_stmt_close($stmtInsert);
                }
                
                $vProjectName = project($val); // Re-use the secured function
                $name = getUserName($user);   // Re-use the secured function
             
                add_audit_log($vuserid, 'department_assign', '', $vProjectName.' Department assigned to '.$name, $db);
            } else {
                 mysqli_stmt_close($stmt_check);
            }
        }
    }
}
// function to handle  unassigning the department to the user  Auth: VASTVIKTA NISHAD 06-02-2024
function unassignProjectsFromUser($link, $user, $assignto_arr, $db) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerabilities.
    $vuserid = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $user = (int)$user;
    // Loop through each project ID in the $assignto_arr array
    for ($i = 0; $i < count($assignto_arr); $i++) {
        $val = $assignto_arr[$i];
        // Prepare and execute the DELETE query
        $queryDelete = "DELETE FROM $db.web_project_assigne WHERE project_id = ? AND user_id = ?";
        $stmtDelete = mysqli_prepare($link, $queryDelete);
        

        $sql1 = "SELECT vProjectName FROM $db.web_projects WHERE pId = '$val'";
        $res1 = mysqli_query($link, $sql1);
        $row = mysqli_fetch_assoc($res1);
        $vProjectName = $row['vProjectName'];
     
        $sql1 = "SELECT c.AtxUserName 
        FROM $db.unigroupid a
        JOIN $db.unigroupdetails b ON a.atxGid = b.atxGid
        JOIN $db.uniuserprofile c ON b.ugdContactID = c.AtxUserID
        WHERE c.AtxUserID = '$user'";
        $res1 = mysqli_query($link, $sql1);
        $row = mysqli_fetch_assoc($res1);
        $name = $row['AtxUserName'];
     
        add_audit_log($vuserid, 'department_unassign', '', $name.' unassigned from department '.$vProjectName, $db);
       
        // Check if the statement was prepared successfully
        if ($stmtDelete) {
            // Bind parameters and execute the statement
            mysqli_stmt_bind_param($stmtDelete, "ii", $val, $user);
            mysqli_stmt_execute($stmtDelete);
            // Close the statement
            mysqli_stmt_close($stmtDelete);
        }
    }
}
/*FUNCTION FOR VIEW STATUS */
// Function to retrieve and view ticket statuses from the database  Auth: VASTVIKTA NISHAD 07-02-2024
function view_ticketstatus(){
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all rows from the 'web_ticketstatus' table where status is '1'
    $sql_document = "SELECT * FROM $db.web_ticketstatus WHERE status='1'";  
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql_document) or die("Could not select: " . mysqli_error($link));
    // Return the result, which contains the retrieved ticket statuses
    return $result;
}
// Function to display agent name based on the provided user ID Auth: VASTVIKTA NISHAD 07-02-2024
function displayagentname($userid){
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select the agent's display name from the 'uniuserprofile' table based on the provided user ID
    $sql_user = "SELECT AtxDisplayName FROM $db.uniuserprofile WHERE AtxUserID='$userid'";
    // Execute the SQL query and store the result in the $res_user variable
    $res_user = mysqli_query($link, $sql_user) or die(mysqli_error($link));
    // Fetch the result as an associative array
    $row_user = mysqli_fetch_assoc($res_user);
    // Extract the 'AtxDisplayName' from the fetched associative array
    $AtxDisplayName = $row_user['AtxDisplayName'];
    // Return the agent's display name
    return $AtxDisplayName;
}
// Function to delete a status from the 'web_ticketstatus' table Auth: VASTVIKTA NISHAD 07-02-2024
function status_delete(){
    // Global variables for the database connection
    global $db, $link;
    // Get the 'id' parameter from the POST request
    $id = $_POST['id'];
    // SQL query to update the 'status' column to '0' for the specified 'id' in the 'web_ticketstatus' table
    $sql_update = "UPDATE $db.web_ticketstatus SET status = '0' WHERE id = '$id'";
    // Execute the SQL query and store the result in the $res variable
    $res = mysqli_query($link, $sql_update);
    if (!$res){
        if (_DBGLOG_){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB status delete  error: $sql_update". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "status Delete Database error";
        echo json_encode($response);
        exit();
    }
    // Check if the query was successful
    if ($res) {
        // If successful, echo 'success'
        echo 'success';
    } else {
        // If there's an error, echo 'error' along with the specific error message
        echo 'error: ' . mysqli_error($link);
    }
    $vuserid = $_SESSION['userid'];
   
    $sql1 = "SELECT ticketstatus FROM $db.web_ticketstatus WHERE id = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $ticketstatus = $row['ticketstatus'];
 
    add_audit_log($vuserid, 'delete_ticketstatus', '', 'Ticket status deleted '.$ticketstatus, $db);
   
    
}

//function to  get status on the basis of id in the edit form  Auth: VASTVIKTA NISHAD 07-02-2024
// Function to retrieve status data based on the provided ID
function getStatusData($link, $id) {
    // Initialize an empty array to store status data
    $statusData = array();
    // Check if the provided ID is not empty
    if (!empty($id)) {
        // SQL query to select the 'ticketstatus' column from 'web_ticketstatus' where the 'id' matches the provided ID
        $query = "SELECT `ticketstatus` FROM `web_ticketstatus` WHERE `id` = $id";
        // Execute the SQL query and store the result in the $result variable
        $result = mysqli_query($link, $query);
        // Check if the query was successful
        if ($result) {
            // Fetch the result as an associative array and store it in $statusData
            $statusData = mysqli_fetch_assoc($result);
            // Free up the memory associated with the result set
            mysqli_free_result($result);
        } 
    }
    // Return the status data, which may be an empty array or contain the fetched status information
    return $statusData;
}

//function to update or insert the data in the web_ticketstatus table  Auth: VASTVIKTA NISHAD 07-02-2024
// Function to insert or update status records in the 'web_ticketstatus' table
function insert_or_update_status(){
    // Global variable for the database connection
    global $link,$db;
    // Get the 'id' and 'status' parameters from the POST request
    $id = $_POST['id'];
    $status = $_POST['status'];
    $vuserid = $_SESSION['userid'];
    // Check if 'id' is empty, indicating a new record should be added
    if (empty($id)) {
        // Insert new record with provided 'status', setting default values for other columns
        $sql = "INSERT INTO `web_ticketstatus` (`ticketstatus`, `status`, `createdby`, `createdon`, `bgcolor`) VALUES ('$status', 1, 1, NOW(), NULL)";
        add_audit_log($vuserid, 'insert_ticketstatus', '', 'New Ticket status added '.$status, $db);
   
    } else {
        // Update existing record with the provided 'status' for the specified 'id'
        $sql = "UPDATE `web_ticketstatus` SET `status` = 1, `ticketstatus` = '$status' WHERE `id` = $id";
        add_audit_log($vuserid, 'update_ticketstatus', '', 'Ticket status updated '.$status, $db);
   
    }
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql);
    if (!$result){
        if (_DBGLOG_){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB Status update or insert  error  : $sql". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "Database error";
        echo json_encode($response);
        exit();
    }
    // Check if the query was successful
    if ($result) {
        // If successful, echo 'success'
        echo 'success';
    } else {
        // If there's an error, echo 'Error' along with the specific error message
        echo 'Error: ' . mysqli_error($link);
    }
}

//function to fetch the data  for view knowledge base Auth: VASTVIKTA NISHAD 08-02-2024
// Function to retrieve base data from the 'tbl_mst_faq' table
function getBaseData() {
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from 'tbl_mst_faq' where 'i_status' is '1'
    $query = "SELECT * FROM $db.tbl_mst_faq WHERE i_status = '1'";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $query);
    // Return the result, which contains the retrieved base data
    return $result;
}

//function to delete  the data for view knowledge base Auth: VASTVIKTA NISHAD 08-02-2024
// hello  Function to delete a record from the 'tbl_mst_faq' table by updating the 'i_status' column to '0'
function base_delete() {
    // Global variables for the database connection
    global $db, $link;
    
    // Get the 'i_id' parameter from the POST request
    $id = $_POST['i_id'];
    
    // SQL query to update the 'i_status' column to '0' for the specified 'i_id' in the 'tbl_mst_faq' table
    $sql_update = "UPDATE $db.tbl_mst_faq SET i_status = '0' WHERE i_id = ?";
    
    // Prepare the SQL statement and store it in the $stmt variable
    $stmt = mysqli_prepare($link, $sql_update);
    if ($stmt === false) {
        // Log error if preparation fails
        if (_DBGLOG_) {
            DbgLog(_LOG_ERROR, __LINE__, __FILE__, "Base delete error in Knowledge Base (prepare): " . mysqli_error($link));
        }
        echo json_encode(['error' => true, 'error_msg' => 'Knowledge Base Database error (prepare)']);
        exit();
    }
    
    // Bind the 'i_id' parameter to the prepared statement
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    // Execute the prepared statement and store the result in the $res variable
    $res = mysqli_stmt_execute($stmt);
    if ($res === false) {
        // Log error if execution fails
        if (_DBGLOG_) {
            DbgLog(_LOG_ERROR, __LINE__, __FILE__, "Base delete error in Knowledge Base (execute): " . mysqli_error($link));
        }
        echo json_encode(['error' => true, 'error_msg' => 'Knowledge Base Database error (execute)']);
        exit();
    }
    
    // Check if the query was successful
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // If successful, echo 'success'
        echo 'success';
    } else {
        // If no rows were affected, echo an error message
        echo json_encode(['error' => true, 'error_msg' => 'No rows affected']);
    }
    
    $vuserid = $_SESSION['userid'];
   
    $sql1 = "SELECT v_qus FROM $db.tbl_mst_faq WHERE i_id = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $v_qus = $row['v_qus'];
 
    add_audit_log($vuserid, 'delete_knowledgebase', '', 'Knowledge Base deleted '.$v_qus, $db);
   

    // Close the prepared statement
    mysqli_stmt_close($stmt);
    
    // Return the result of the deletion operation (true or false)
    return $res;
}
// Function to retrieve a specific record from the 'tbl_mst_faq' table based on the provided 'i_id'
function getBaseDataset($link, $id) {
    // Initialize an empty array to store base data
    $baseData = array();
    // Check if the provided 'i_id' is not empty
    if (!empty($id)) {
        // SQL query to select all columns from 'tbl_mst_faq' where 'i_id' matches the provided 'id'
        $query = "SELECT * FROM `tbl_mst_faq` WHERE `i_id` = $id";
        // Execute the SQL query and store the result in the $result variable
        $result = mysqli_query($link, $query);
        // Check if the query was successful
        if ($result) {
            // Fetch the result as an associative array and store it in $baseData
            $baseData = mysqli_fetch_assoc($result);
            // Free up the memory associated with the result set
            mysqli_free_result($result);
        } 
    }
    // Return the base data, which may be an empty array or contain the fetched record information
    return $baseData;
}

//function to insert or update data in the tbl_mst_faq table Auth: VASTVIKTA NISHAD 08-02-2024
// Function to handle the submission of base data (insert or update)
function handleBaseSubmit()
{
    global $db, $link;
    $vuserid = $_SESSION['userid'];
    extract($_POST);
    
    $qus = addslashes($v_qus);
    $ans = addslashes($v_ans);

    if ($_POST['action'] == 'submit_base') {
        $query = "INSERT INTO $db.tbl_mst_faq (v_qus, v_ans) VALUES ('$qus', '$ans')";

    add_audit_log($vuserid, 'insert_knowledgebase', '', 'New Knowledge Base added '.$qus, $db);
   
    } elseif ($_POST['action'] == 'update_base') {
        $id = (int)$i_id;
        $query = "UPDATE $db.tbl_mst_faq SET v_qus='$qus', v_ans='$ans' WHERE i_id=$id";

    add_audit_log($vuserid, 'update_knowledgebase', '', 'Knowledge Base updated '.$qus, $db);

    }

    if (mysqli_query($link, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        if (_DBGLOG_) {
            DbgLog(_LOG_ERROR, __LINE__, __FILE__, "DB Insert or Update error in Knowledge Base: $query " . mysqli_error($link));
        }
        echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
    }
}
// Function to generate a date string based on key ('from' or 'to') and value (1 to 8)
function getFromDate($key, $val)
{
    // Get the current date and extract the year and month
    $cdt = date('m-Y');
    $dt = explode("-", $cdt);
    $current_year = $dt[1];
    $current_month = $dt[0];
    // Initialize the output variable
    $output = "";
    // Check if the key is 'from'
    if ($key == 'from') {
        // Handle different cases based on the provided value
        if ($val == 1) {
            $output = "01-" . $cdt;
        }if ($val == 2) {
            $cdt = date("m-Y", strtotime("-1 months"));
            $output = "01-" . $cdt;
        }if($val==3){
            $output="01-04-".($current_year);
        }if($val==4) {
            $output="01-04-".($current_year-1);
        }if($val==5){
            $time = strtotime(date("Y-m-d H:i:s"));
            $output = date('Y-m-d H:i:s',strtotime("last Monday", $time));
        } if($val==6) {
            $output=date("d-m-Y", strtotime("-1 day"));
        }if($val==7){
            $monday = strtotime("last monday");
            $monday = date('W', $monday)==date('W') ? $monday-7*86400 : $monday;
            $output = date("Y-m-d",$monday);
        }if($val==8){
            $output=date("d-m-Y", strtotime("-2 day"));
        }
    }
    else if ($key == 'to') {
        // Handle different cases based on the provided value for 'to'
        if ($val == 1) {
            $output = date('d') . "-" . $cdt;
        }if ($val == 2) {
            $cdt = date("m-Y", strtotime("-1 months"));
            $output = date('d', strtotime('last day of previous month')) . "-" . $cdt;
        }if($val==3){
            $output="31-03-".($current_year+1);
        }if($val==4){
            $output="31-03-".($current_year);
        }if($val==5) {
            $output = date("Y-m-d H:i:s");
        }if($val==6) {
            $output=date("d-m-Y", strtotime("-1 day"));
        }if($val==7){
            //$output=date("d-m-Y", strtotime("-1 day"));
            $monday = strtotime("last monday");
            $monday = date('W', $monday)==date('W') ? $monday-7*86400 : $monday;
            $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
            $output = date("Y-m-d",$sunday);
        }if($val==8){
            $output = date("Y-m-d H:i:s");
        }
    }
// Return the generated date string
return $output;
}
// Function to retrieve email information for IN emails (changed email_type from OUT to IN)
function getToEmailInformation() {
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from 'web_email_information' where email_type is 'IN' and v_toemail is not empty
    $sql = "SELECT * FROM $db.web_email_information_out WHERE email_type='OUT' AND v_toemail!='' GROUP BY v_toemail";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql);
    // Return the result, which contains the retrieved email information
    return $result;
}
// Function to retrieve email information for IN emails as the sender (changed email_type from OUT to IN)
function getFromEmailInformation() {
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from 'web_email_information' where email_type is 'IN' and v_fromemail is not empty
    $sql = "SELECT * FROM $db.web_email_information_out WHERE email_type='OUT' AND v_fromemail!='' GROUP BY v_fromemail";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql);
    // Return the result, which contains the retrieved email information
    return $result;
}
// Function to retrieve all email information for IN emails with optional date filtering (changed email_type from OUT to IN)
function getAllEmailInformation($datefilter) {
    // Global variables for the database connection
    global $link, $db;
    // SQL query to select all columns from 'web_email_information' where email_type is 'IN', v_toemail is not empty, and optional date filtering
    $qq = "SELECT * FROM $db.web_email_information_out WHERE email_type='OUT' AND v_toemail!='' $datefilter";
    // Execute the SQL query with optional date filtering and order the results by email date and ID
    $result  = mysqli_query($link, "$qq ORDER BY d_email_date DESC, EMAIL_ID DESC;");
    // Return the result, which contains the retrieved email information
    return $result;
}

// Function to fetch user login data from the database for the 'web_live_ip_log' table
// Auth: VASTVIKTA NISHAD 12-02-2024
function getUserLogin($datefilter){
    // Global variables for the database connection
    global $db, $link;
    $qq = "SELECT * FROM $db.logip AS l 
    LEFT JOIN $db.uniuserprofile AS u ON u.AtxUserName=l.UserName 
    LEFT JOIN $db.unigroupdetails AS ug ON u.AtxUserID=ug.ugdContactID
    WHERE l.Reason='login' AND atxGid != '0000'  $datefilter GROUP BY AtxUserName, DATE( AccessedAt )   ORDER BY AccessedAt DESC";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $qq);
    return $result;
}
// Function to fetch data for viewing escalations from the 'escalationdays' table
// Auth: VASTVIKTA NISHAD 12-02-2024
function view_escalations()
{
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from the 'escalationdays' table
    $sql = "SELECT * FROM $db.escalationdays";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql) or die(mysqli_error($link));
    // Return the result, which contains the retrieved data for viewing escalations
    return $result;
}
// Function used in web_view_escalation.php to convert Escalation To
// Auth: VASTVIKTA NISHAD 12-02-2024
function escalation_to($id)
{
    // Check the value of $id and assign the corresponding label to $res
    if ($id == 1) {
        $res = 'Customer';
    } else if ($id == 2) {
        $res = 'Department';
    } else {
        $res = 'Both';
    }
    // Return the result, which represents the converted Escalation To value
    return $res;
}
// Function to convert Escalation Media ID into a corresponding label
function escalation_media($id)
{
    // Check the value of $id and assign the corresponding label to $res
    if ($id == 1) {
        $res = 'Email';
    } else if ($id == 2) {
        $res = 'SMS';
    } else {
        $res = 'Both';
    }
    // Return the result, which represents the converted Escalation Media value
    return $res;
}
// Function to fetch data of escalation page based on the provided ID
function getEscalationData($link, $id)
{
    // Global variable for the database connection
    global $db;
    // SQL query to select all columns from 'escalationdays' where 'eid' matches the provided 'id'
    $sql_check_document = "SELECT * FROM $db.escalationdays WHERE eid='$id'";
    // Execute the SQL query and store the result in the $result_query variable
    $result_query = mysqli_query($link, $sql_check_document);
    // Fetch the result as an associative array and store it in $res
    $res = mysqli_fetch_assoc($result_query);
    // Return the result, which contains the retrieved escalation data
    return $res;
}
// Function to update data in the 'escalationdays' table
function update_escalation() {
    // Global variables for the database connection
    global $db, $link;
    
    $vuserid = $_SESSION['userid'];
    // Retrieve and sanitize the ID from the POST request
    $id = $_POST['id'];
    if (!isset($id)) {
        echo 'Error: ID is not set.';
        return;
    }
    
    // Log received ID
    error_log('Received ID: ' . $id);
    
    // Convert POST variables to appropriate data types
    $escalation_to = (int)$_POST['escalation_to'];
    $escalation_media = (int)$_POST['escalation_media'];
    $escalation_list = implode(",", $_POST['escalation_list']);

    // Use prepared statements to prevent SQL injection
    $sql_document = "UPDATE $db.escalationdays SET escalation_to=?, escalation_media=?, escalation_list=? WHERE eid=?";
    
    $stmt = mysqli_prepare($link, $sql_document);
    mysqli_stmt_bind_param($stmt, "iisi", $escalation_to, $escalation_media, $escalation_list, $id);
    
    // Execute the prepared statement and check for success
    $update_success = mysqli_stmt_execute($stmt);
    
    // Provide appropriate response based on the update result
    if ($update_success) {
        echo 'success';
    } else {
        // If there's an error, display the error message and log it to the server error log
        $error_message = 'Error updating escalation: ' . mysqli_stmt_error($stmt);
        echo $error_message;
        error_log($error_message);
    }

    $sql1 = "SELECT `level` FROM $db.escalationdays WHERE eid = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $level = $row['level'];
 
    add_audit_log($vuserid, 'escalation_update', '', 'Escalation Level ' .$level .' updated', $db);
          
    
    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Function to fetch data for the 'imap' table in 'web_view_imap_smtp'
function getIMAPData()
{
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from 'tbl_connection' with a limit of 10 rows
    $sql = "SELECT * FROM $db.tbl_connection LIMIT 10";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql) or die(mysqli_error($link));
    // Return the result, which contains the retrieved data for the 'imap' table
    return $result;
}
// Function to fetch data for the 'smtp' table in 'web_view_imap_smtp'
function getSMTPData()
{
    // Global variables for the database connection
    global $db, $link;
    // SQL query to select all columns from 'tbl_smtp_connection' with a limit of 10 rows
    $sql = "SELECT * FROM $db.tbl_smtp_connection LIMIT 10";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $sql) or die(mysqli_error($link));
    // Return the result, which contains the retrieved data for the 'smtp' table
    return $result;
}
// Function to fetch data for the 'smtp' table based on the provided ID
function SMTPData($link, $id)
{
    // Global variable for the database connection
    global $db;
    // SQL query to select all columns from 'tbl_smtp_connection' where 'id' matches the provided 'id'
    $sql_check_document = "SELECT * FROM $db.tbl_smtp_connection WHERE id='$id'";
    // Execute the SQL query and store the result in the $result_query variable
    $result_query = mysqli_query($link, $sql_check_document);
    // Fetch the result as an associative array and store it in $res
    $res = mysqli_fetch_assoc($result_query);
    // Free the result set
    mysqli_free_result($result_query);
    // Return the result, which contains the retrieved data for the 'smtp' table
    return $res;
}

// Function to fetch data for the 'imap' table based on the provided ID
function IMAPData($link, $id)
{
    // Global variable for the database connection
    global $db;
    // SQL query to select all columns from 'tbl_connection' where 'I_ID' matches the provided 'id'
    $sql_check_document = "SELECT * FROM $db.tbl_connection WHERE I_ID='$id'";
    // Execute the SQL query and store the result in the $result_query variable
    $result_query = mysqli_query($link, $sql_check_document);
    // Fetch the result as an associative array and store it in $res using mysqli_fetch_assoc
    $res = mysqli_fetch_assoc($result_query);
    // Return the result, which contains the retrieved data for the 'imap' table
    return $res;
}

// Function to update data in the 'imap' table
function update_imap() {
    global $db, $link;
    $response = array();

    $vuserid = $_SESSION['userid'];
    // Retrieve and sanitize input data from the POST request
    $id = $_POST['id'];
    $v_connectionname = mysqli_real_escape_string($link, $_POST['v_connectionname']);
    $v_ipaddress = mysqli_real_escape_string($link, $_POST['v_ipaddress']);
    $v_username = mysqli_real_escape_string($link, $_POST['v_username']);
    $v_pasowrd = $_POST['v_pasowrd'];
    $v_type = mysqli_real_escape_string($link, $_POST['v_type']);
    $v_client_id = mysqli_real_escape_string($link, $_POST['v_client_id']);
    $v_client_secret = mysqli_real_escape_string($link, $_POST['v_client_secret']);
    $v_tenant = mysqli_real_escape_string($link, $_POST['v_tenant']);

    // Use prepared statements to prevent SQL injection
    $sql_document = "UPDATE `$db`.`tbl_connection` 
                     SET 
                        `v_connectionname`=?, 
                        `v_ipaddress`=?, 
                        `v_username`=?, 
                        `v_pasowrd`=?, 
                        `v_type`=?, 
                        `v_client_id`=?, 
                        `v_client_secret`=?, 
                        `v_tenant`=?
                     WHERE `I_ID`=?";

    $stmt = mysqli_prepare($link, $sql_document);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssi", 
                               $v_connectionname, 
                               $v_ipaddress, 
                               $v_username, 
                               $v_pasowrd, 
                               $v_type, 
                               $v_client_id, 
                               $v_client_secret, 
                               $v_tenant, 
                               $id);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 'success';
            $response['message'] = 'Update successful';

    
            add_audit_log($vuserid, 'imap_settings_update', '', 'IMAP settings updated', $db);
           
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Execute error: ' . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Prepare statement error: ' . mysqli_error($link);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Function to update data in the 'smtp' table
function update_smtp()
{
    // Global variables for the database connection
    global $db, $link;
    $vuserid = $_SESSION['userid'];
    // Initialize response array
    $response = array();

    // Check if required POST parameters are set
    if (isset($_POST['id'], $_POST['i_port'], $_POST['v_username'], $_POST['v_password'], $_POST['v_server'], $_POST['i_tls'], $_POST['i_debug'])) {
        // Retrieve and sanitize input data from the POST request
        $id = $_POST['id'];
        $i_port = $_POST['i_port'];
        $v_username = mysqli_real_escape_string($link, $_POST['v_username']); // Sanitize input
        $v_password = $_POST['v_password'];
        $v_server = $_POST['v_server'];
        $i_tls = $_POST['i_tls'];
        $i_debug = $_POST['i_debug'];

        // Use prepared statements to prevent SQL injection
        $sql_document = "UPDATE $db.tbl_smtp_connection SET i_port=?, v_username=?, v_password=?, v_server=?, i_tls=?, i_debug=? WHERE id=?";

        // Prepare the SQL statement
        $stmt = mysqli_prepare($link, $sql_document);

        // Check if the prepared statement was successfully created
        if ($stmt) {
            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, 'isssiii', $i_port, $v_username, $v_password, $v_server, $i_tls, $i_debug, $id);

            // Execute the prepared statement and check for success
            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = 'SMTP Settings Updated Successfully';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Execute error: ' . mysqli_error($link);
            }

            add_audit_log($vuserid, 'smtp_settings_update', '', 'SMTP settings updated', $db);
          
            // Close the prepared statement
            mysqli_stmt_close($stmt);
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Prepare statement error: ' . mysqli_error($link);
        }
    
   
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Missing required parameters';
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Function to fetch data for the 'tbl_disposition' table in 'web_view_dispostion'
// Auth: VASTVIKTA NISHAD 15-02-2024
function getDisposition() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link;
    $sql_document = "SELECT * FROM asterisk.tbl_disposition WHERE I_Status=1 ORDER BY `V_DISPO` ASC";
    $res = mysqli_query($link, $sql_document);
    return $res;
}
// Function to fetch data for the 'tbl_disposition' table based on the provided ID
// Auth: VASTVIKTA NISHAD 15-02-2024
function getDispositionID($id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link;
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    
    $sql = "SELECT * FROM asterisk.tbl_disposition WHERE I_ID = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $row = false;
    if ($result) {
        $row_data = mysqli_fetch_assoc($result);
        if($row_data) {
            foreach ($row_data as $key => $value) {
                $row[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
    }
    mysqli_stmt_close($stmt);
    return $row;
}
// Function to delete a record in the 'tbl_disposition' table
// Auth: VASTVIKTA NISHAD 15-02-2024
function disposition_delete() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link,$db;
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if(empty($id)) {
        echo json_encode(['error' => TRUE, 'error_msg' => "Invalid ID."]);
        exit();
    }
    $vuserid = $_SESSION['userid'];

    $sql_delete = "UPDATE asterisk.tbl_disposition SET I_Status=0, I_Type=0 WHERE I_ID=?";
    $stmt = mysqli_prepare($link, $sql_delete);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $res = mysqli_stmt_execute($stmt);

    if (!$res){
        if (_DBGLOG_){ DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Disposition Delete error : ". mysqli_error($link)); }
        echo json_encode(['error' => TRUE, 'error_msg' => "Disposition Database error"]);
        exit();
    }
    
    echo json_encode(['success' => true]);
   
    add_audit_log($vuserid, 'disposition_deleted', '', 'Disposition deleted', $db);
    exit();
}
// Function to update or insert data in the 'tbl_disposition' table
// Auth: VASTVIKTA NISHAD 15-02-2024
function insert_or_update_disposition() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link,$db;
    $vuserid = $_SESSION['userid'];

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $disposition = filter_input(INPUT_POST, 'disposition', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if (empty($disposition)) {
        echo json_encode(['error' => TRUE, 'error_msg' => "Disposition name cannot be empty."]);
        exit();
    }

    if (empty($id)) {
        $sql_document = "INSERT INTO asterisk.tbl_disposition(V_DISPO, V_DISPOSITION, I_Status, I_Type) VALUES (?, ?, 1, 1)";
        $stmt = mysqli_prepare($link, $sql_document);
        mysqli_stmt_bind_param($stmt, 'ss', $disposition, $disposition);
        $log_action = 'dispposition_insert';
        $log_message = 'New Disposition added '. $disposition;
    } else {
        $sql_document = "UPDATE asterisk.tbl_disposition SET V_DISPO = ?, V_DISPOSITION = ? WHERE I_ID = ?";
        $stmt = mysqli_prepare($link, $sql_document);
        mysqli_stmt_bind_param($stmt, 'ssi', $disposition, $disposition, $id);
        $log_action = 'disposition_updated';
        $log_message = 'Dispostion updated '. $disposition;
    }
    
    $result = mysqli_stmt_execute($stmt);

    if (!$result){
        if (_DBGLOG_){ DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Insert or Update Disposition error : ". mysqli_error($link)); }
        echo json_encode(['error' => TRUE, 'error_msg' => "Disposition Database error"]);
        exit();
    }
    
    add_audit_log($vuserid, $log_action, '', $log_message, $db);
    echo json_encode(['success' => true]);
    exit();
}

// Function to fetch data for callbacks from the 'autodial_callbacks' table
function getCallbacks() {
    // Global variable for the database connection
    global $link;
    // SQL query to select all columns from 'asterisk.autodial_callbacks' where 'comments' is 'CALL BACK'
    $sql_document = "SELECT * FROM asterisk.autodial_callbacks WHERE comments='CALL BACK' ORDER BY `callback_id` DESC";
    // Execute the SQL query and store the result in the $res variable
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result, which contains the retrieved data for callbacks
    return $res;
}
// Function to fetch callback data from the 'autodial_callbacks' table based on the provided id
function get_callbacks($id) {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection vulnerability.
    global $link;
    if (empty($id) || !is_numeric($id)) {
        return false;
    }

    $sql_document = "SELECT * FROM asterisk.autodial_callbacks WHERE callback_id=?";
    $stmt = mysqli_prepare($link, $sql_document);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $row = false;
    if ($result) {
        $row_data = mysqli_fetch_assoc($result);
        if($row_data) {
             foreach ($row_data as $key => $value) {
                $row[htmlspecialchars($key, ENT_QUOTES, 'UTF-8')] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
    }
    mysqli_stmt_close($stmt);
    return $row;
}
// Function to update callback values in the 'autodial_callbacks' table
function updateCallbacks() {
    // Security Fix by AI - 2024-07-22: Patched SQL Injection and added input validation.
    global $db, $link;
    
    header('Content-Type: application/json');

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // 1. CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
        exit();
    }

    $vuserid = $_SESSION['userid'];

    // 2. Input Sanitization and Validation
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $callback_time_str = filter_input(INPUT_POST, 'callback_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $callback_alert_hour = filter_input(INPUT_POST, 'callback_alert_hour', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 23]]);
    $callback_alert_minute = filter_input(INPUT_POST, 'callback_alert_minute', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 59]]);
    $callback_remark = filter_input(INPUT_POST, 'callback_remark', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($id) || empty($callback_time_str) || $callback_alert_hour === false || $callback_alert_minute === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid input provided. Please check the fields and try again.']);
        return;
    }
    
    // 3. Data Processing
    $callback_alert_time = str_pad($callback_alert_hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($callback_alert_minute, 2, '0', STR_PAD_LEFT) . ":00";
    $formatted_callback_time = date('Y-m-d H:i:s', strtotime($callback_time_str));

    // 4. Prepared Statement
    $sql_document = "UPDATE asterisk.autodial_callbacks 
                    SET callback_time = ?, callback_alert_time = ?, remark = ?
                    WHERE callback_id = ?";
    
    $stmt = mysqli_prepare($link, $sql_document);
    mysqli_stmt_bind_param($stmt, 'sssi', $formatted_callback_time, $callback_alert_time, $callback_remark, $id);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        if (_DBGLOG_) { 
            DbgLog(_LOG_ERROR, __LINE__, __FILE__, "Update callback time error: " . mysqli_error($link)); 
        }
        echo json_encode(['success' => false, 'message' => "Database error occurred while updating callback."]);
        exit();
    }
   
    // 5. Audit Log and Success Response
    add_audit_log($vuserid, 'callback_update', '', 'Callback time updated', $db);
    echo json_encode(['success' => true, 'message' => 'Callback updated successfully!']);
    exit();
}
// [vastvikta nishad][22-12-2025]
// this function is for teching data of  bulk email sms template [vastvikta][22-01-2025]
function view_bulk_data(){
     // Global variables for the database connection
    global $db,$link;
    $query = "SELECT * FROM $db.bulksms_templates";
     // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link,$query);
     // Return the result, which contains the retrieved base data
    return $result;
}
function getbulkData($link, $id) {
    // Initialize an empty array to store status data
    $bulkData = array();
    // Check if the provided ID is not empty
    if (!empty($id)) {
        // SQL query to select the 'ticketstatus' column from 'web_ticketstatus' where the 'id' matches the provided ID
        $query = "SELECT * FROM bulksms_templates WHERE `id` = $id";
        // Execute the SQL query and store the result in the $result variable
        $result = mysqli_query($link, $query);
        // Check if the query was successful
        if ($result) {
            // Fetch the result as an associative array and store it in $statusData
            $bulkData = mysqli_fetch_assoc($result);
            // Free up the memory associated with the result set
            mysqli_free_result($result);
        } 
    }
    // Return the status data, which may be an empty array or contain the fetched status information
    return $bulkData;
}
function bulk_delete() {
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
  
    $sql_update = "DELETE From $db.bulksms_templates WHERE id = '$id'";
   
    // Execute the SQL query
    $res = mysqli_query($link, $sql_update);
        if (!$res){
            if (_DBGLOG_){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error BULK format : $sql_update". mysqli_error($link));
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "BULK TEMPLATE DELETE format Database error";
            echo json_encode($response);
            exit();
        }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
   
     $vuserid = $_SESSION['userid'];
    
     $sql1 = "SELECT `name`  FROM $db.bulksms_templates WHERE id = '$id'";
     $res1 = mysqli_query($link, $sql1);
     $row = mysqli_fetch_assoc($res1);
     $name = $row['name'];
  
     add_audit_log($vuserid, 'delete_bulk_sms_email_template', '', 'Bulk SMS EMAIL template deleted '.$name, $db);
    
}
function update_bulk_data() {
    global $db, $link;

    $vuserid = $_SESSION['userid'];
    // Sanitize inputs to prevent SQL injection
    $name = mysqli_real_escape_string($link, $_REQUEST['template_name'] ?? '');
    $template_content = mysqli_real_escape_string($link, $_REQUEST['template_content'] ?? '');
    $type = mysqli_real_escape_string($link, $_REQUEST['type'] ?? '');
    $slug = mysqli_real_escape_string($link, $_REQUEST['slug'] ?? '');
    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    // Initialize the response array
    $response = [
        'status' => 'error',
        'message' => ''
    ];

    // Check if the ID is empty (indicating a new category insertion)
    if ($id == 0) {
        // New entry - insert query
        $sql_document = "INSERT INTO $db.bulksms_templates (name, template_content, type, slug) VALUES ('$name', '$template_content', '$type', '$slug')";
        
        // Execute the query
        $res = mysqli_query($link, $sql_document);

        if (!$res) {
            if (_DBGLOG_) {
                DbgLog(_LOG_ERROR, __LINE__, __FILE__, "DB insert error bulksms templates: $sql_document" . mysqli_error($link));
            }
            $response['message'] = "Database insert error";
            echo json_encode($response); // Return the response as JSON
            exit();
        }
        $response['status'] = 'success';
        $response['message'] = "Successfully added!";
        add_audit_log($vuserid, 'insert_bulk_sms_email_template', '', 'New Bulk SMS EMAIL template added '.$name, $db);
    
    } else {
        // Update entry
        $sql3 = "UPDATE $db.bulksms_templates SET name = '$name', template_content = '$template_content', type = '$type', slug = '$slug' WHERE id = $id";
      
        // Execute the query
        $res1 = mysqli_query($link, $sql3);

        if (!$res1) {
            if (_DBGLOG_) {
                DbgLog(_LOG_ERROR, __LINE__, __FILE__, "DB Update error template: $sql3" . mysqli_error($link));
            }
            $response['message'] = "Database update error";
            echo json_encode($response); // Return the response as JSON
            exit();
        }
        $response['status'] = 'success';
        $response['message'] = "Successfully updated!";
        add_audit_log($vuserid, 'update_bulk_sms_email_template', '', 'Bulk SMS EMAIL template updated '.$name, $db);
    
    }

    // Output the result message as JSON
    echo json_encode($response);
}



/******************** This code for Bullitin Module *************************/ 
function edit_bullitin() {
    // Global variables for the database connection
    global $link,$db;
    // SQL query to select all columns from 'tbl_mst_faq' where 'i_status' is '1'
    // $query = "SELECT city FROM $db.web_city WHERE id='" . $res['iDistrictID'] . "'";
    $query = "SELECT city FROM $db.web_city";
    // Execute the SQL query and store the result in the $result variable
    $result = mysqli_query($link, $query);
    // Return the result, which contains the retrieved base data
    return $result;
}
/************************ END ********************************/ 
/*********** This code for Licence Modules ***************/ 
function View_licence_info(){
    global $link, $db;
    $sql="SELECT DisplayName, UserLicence,user_type, atxGid FROM $db.unigroupid WHERE atxGid != '0000' AND status ='1' ORDER BY DisplayName ";
    $res= mysqli_query($link, $sql);
    return $res;
}
function View_licence_company_detail(){
    global $dbname, $link;
    $sql="SELECT V_CompanyName, V_CompanyAddress, D_DateofRegistration, Website, V_PhoneNo FROM $dbname.tbl_mst_company";
    $rs = mysqli_query($link, $sql);
    return $rs;
}
function F_Count_User($companyid,$db,$usertype){
   global $link,$dbname;
    $sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
    tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation = '$usertype' AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
    $Fetch_USERID=mysqli_query($link,$sql_user_ID);
    $row=mysqli_fetch_array($Fetch_USERID);
    return $USERID=$row['ID'];
}
// This code for check which type of licence and display used licence
function F_Count_User_Licence($companyid,$db,$usertype,$licence_type){
   global $link,$dbname,$licence_Concurrent,$db_asterisk;
   if($licence_type == $licence_Concurrent && $usertype == 'Agent'){
        $sql_user_ID = "SELECT count(*) as total FROM $db_asterisk.`autodial_live_agents`";
        $Fetch_USERID=mysqli_query($link,$sql_user_ID);
        $row=mysqli_fetch_array($Fetch_USERID);
        return $USERID=$row['total'];
   }else{
        $sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
        tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation = '$usertype' AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
        $Fetch_USERID=mysqli_query($link,$sql_user_ID);
        $row=mysqli_fetch_array($Fetch_USERID);
        return $USERID=$row['ID'];
   }
    
}
// get email for sending crm daily report(adhoc)
function get_adhoc_mail($id) 
{
 global $link,$dbname;
 $query = $link->query("SELECT adhoc_mail FROM $dbname.tbl_connection WHERE I_ID IN('$id')");
 $row = $query->fetch_row();
 return $row[0];
}

/******************** This code for Adhoc Email Update *************************/ 
function adhoc_mail_update($emailadhoc) {
    // Global variables for the database connection
    global $link,$dbname;
    $result = $link->query("UPDATE $dbname.tbl_connection SET adhoc_mail='$emailadhoc' WHERE I_ID IN (1) ");
    // Provide appropriate response based on the execution result
    if ($result) {
        echo 'success';
    } else {
        echo 'Error: ' . mysqli_error($link);
    }
   
}
function view_webchat_template(){
    global $db, $link;
    // SQL query to select all records 
    $sql_document = "SELECT * FROM $db.webchat_template WHERE status = '1'";
    // Execute the query or die with an error message if it fails
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result set
    return $res;
}
function view_whatsapp_template(){
    global $db, $link;
    // SQL query to select all records 
    $sql_document = "SELECT * FROM $db.whatsapp_template WHERE status = '1'";
    // Execute the query or die with an error message if it fails
    $res = mysqli_query($link, $sql_document) or die("Could not select");
    // Return the result set
    return $res;
}
// Function to get SMS template data from tbl_smsformat based on the provided id
function getwebchatData($link, $id) {
    global $db;
    $Data = array();

    // Check if $id is not empty
    if ($id) {
        // Escape the input to prevent SQL injection
        $safe_id = mysqli_real_escape_string($link, $id);

        // Construct the query
        $sql = "SELECT temp_name, temp_type, temp_content FROM {$db}.webchat_template WHERE id = '$safe_id'";

        // Execute the query
        $result = mysqli_query($link, $sql);

        // Check if query was successful
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $Data['temp_name'] = $row['temp_name'];
            $Data['temp_type'] = $row['temp_type'];
            $Data['temp_content'] = $row['temp_content'];
        }

        // Free result set
        if ($result) {
            mysqli_free_result($result);
        }
    }

    return $Data;
}
function webchat_delete() {
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
    $sql_update = "UPDATE $db.webchat_template SET status = '0' WHERE id = '$id'";
    // Execute the SQL query
    $res = mysqli_query($link, $sql_update);
        if (!$res){
            if (_DBGLOG_){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error webchat template: $sql_update". mysqli_error($link));
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "webchat template Database error";
            echo json_encode($response);
            exit();
        }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
    // Get user ID from the session
    $vuserid = $_SESSION['userid'];
    $sql1 = "SELECT temp_content FROM $db.webchat_template WHERE id = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $smstemplatename = $row['temp_content'];
 
    add_audit_log($vuserid, 'delete_webchattemplate', '', 'WebChat Template deleted '.$smstemplatename, $db);
   
}
function insert_or_update_webchat($link){
    global $db;

    $vuserid = $_SESSION['userid'];
    $SmsID = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $SmsTemplateName = isset($_POST['template_name']) ? mysqli_real_escape_string($link, $_POST['template_name']) : '';
    $SmsType = isset($_POST['type']) ? mysqli_real_escape_string($link, $_POST['type']) : '';
    $SmsBody = isset($_POST['body']) ? mysqli_real_escape_string($link, $_POST['body']) : '';
    $SmsStatus = 1;

    if ($_POST['action'] == 'submit_webchat') {
        $create_date = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `{$db}`.`webchat_template` 
                (`temp_name`, `temp_type`, `temp_content`, `status`, `created_by`, `create_date`) 
                VALUES ('$SmsTemplateName', '$SmsType', '$SmsBody', '$SmsStatus', $vuserid, '$create_date')";

        if (mysqli_query($link, $sql)) {
            $SmsID = mysqli_insert_id($link);
            echo 'success';
            add_audit_log($vuserid, 'insert_webchat_template', '', 'New WebChat template added: '.$SmsTemplateName, $db);
        } else {
            echo json_encode([
                'error' => true,
                'error_msg' => 'Database error: ' . mysqli_error($link)
            ]);
        }

    } elseif ($_POST['action'] == 'update_webchat') {
        $SmsStatus = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        $sql = "UPDATE `{$db}`.`webchat_template` 
                SET `temp_name` = '$SmsTemplateName',
                    `temp_type` = '$SmsType',
                    `temp_content` = '$SmsBody',
                    `status` = '$SmsStatus'
                WHERE `id` = $SmsID";

        if (mysqli_query($link, $sql)) {
            echo 'success';
            add_audit_log($vuserid, 'update_webchat_template', '', 'WebChat template updated: '.$SmsTemplateName, $db);
        } else {
            echo json_encode([
                'error' => true,
                'error_msg' => 'Database error: ' . mysqli_error($link)
            ]);
        }
    }
}
// Function to get SMS template data from tbl_smsformat based on the provided id
function getwhatsappData($link, $id) {
    global $db;
    $Data = array();

    // Check if $id is not empty
    if ($id) {
        // Escape the input to prevent SQL injection
        $safe_id = mysqli_real_escape_string($link, $id);

        // Construct the query
        $sql = "SELECT temp_name, temp_type, temp_content FROM {$db}.whatsapp_template WHERE id = '$safe_id'";

        // Execute the query
        $result = mysqli_query($link, $sql);

        // Check if query was successful
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $Data['temp_name'] = $row['temp_name'];
            $Data['temp_type'] = $row['temp_type'];
            $Data['temp_content'] = $row['temp_content'];
        }

        // Free result set
        if ($result) {
            mysqli_free_result($result);
        }
    }

    return $Data;
}
function whatsapp_delete() {
    global $db, $link;
    // Retrieve the ID from the POST request
    $id = $_POST['id'];
    $sql_update = "UPDATE $db.whatsapp_template SET status = '0' WHERE id = '$id'";
    // Execute the SQL query
    $res = mysqli_query($link, $sql_update);
        if (!$res){
            if (_DBGLOG_){
                    DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error whatsapp template: $sql_update". mysqli_error($link));
            }
            $response['error'] = TRUE;
            $response['error_msg'] = "whatsapp template Database error";
            echo json_encode($response);
            exit();
        }
    // Check if the query was successful
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
    // Get user ID from the session
    $vuserid = $_SESSION['userid'];
    $sql1 = "SELECT temp_content FROM $db.whatsapp_template WHERE id = '$id'";
    $res1 = mysqli_query($link, $sql1);
    $row = mysqli_fetch_assoc($res1);
    $smstemplatename = $row['temp_content'];
 
    add_audit_log($vuserid, 'delete_whatsapptemplate', '', 'WhatsaApp Template deleted '.$smstemplatename, $db);
   
}
function insert_or_update_whatsapp($link){
    global $db;

    $vuserid = $_SESSION['userid'];
    $SmsID = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $SmsTemplateName = isset($_POST['template_name']) ? mysqli_real_escape_string($link, $_POST['template_name']) : '';
    $SmsType = isset($_POST['type']) ? mysqli_real_escape_string($link, $_POST['type']) : '';
    $SmsBody = isset($_POST['body']) ? mysqli_real_escape_string($link, $_POST['body']) : '';
    $SmsStatus = 1;

    if ($_POST['action'] == 'submit_whatsapp') {
        $create_date = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `{$db}`.`whatsapp_template` 
                (`temp_name`, `temp_type`, `temp_content`, `status`, `created_by`, `create_date`) 
                VALUES ('$SmsTemplateName', '$SmsType', '$SmsBody', '$SmsStatus', $vuserid, '$create_date')";

        if (mysqli_query($link, $sql)) {
            $SmsID = mysqli_insert_id($link);
            echo 'success';
            add_audit_log($vuserid, 'insert_whatsapp_template', '', 'New WhatsApp template added: '.$SmsTemplateName, $db);
        } else {
            echo json_encode([
                'error' => true,
                'error_msg' => 'Database error: ' . mysqli_error($link)
            ]);
        }

    } elseif ($_POST['action'] == 'update_whatsapp') {
        $SmsStatus = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        $sql = "UPDATE `{$db}`.`whatsapp_template` 
                SET `temp_name` = '$SmsTemplateName',
                    `temp_type` = '$SmsType',
                    `temp_content` = '$SmsBody',
                    `status` = '$SmsStatus'
                WHERE `id` = $SmsID";

        if (mysqli_query($link, $sql)) {
            echo 'success';
            add_audit_log($vuserid, 'update_whatsapp_template', '', 'WhatsApp template updated: '.$SmsTemplateName, $db);
        } else {
            echo json_encode([
                'error' => true,
                'error_msg' => 'Database error: ' . mysqli_error($link)
            ]);
        }
    }
}
function spam_mail_delete(){
    global $db,$link;
    $mail = $_POST['id'];
    $sql = "UPDATE $db.web_email_information SET classification = '0' WHERE v_fromemail = '$mail'";
    $res = mysqli_query($link, $sql);
    if (!$res){
        if (_DBGLOG_){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error : $sql". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "Database error";
        echo json_encode($response);
        exit();
    }
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }

    $vuserid = $_SESSION['userid'];
    add_audit_log($vuserid, 'spam_mail_delete', '', 'Spam Mail Deleted '.$mail, $db);
   
}
function spam_mail(){
    global $db,$link;
    $mail = $_POST['mail'];
    $sql = "UPDATE $db.web_email_information SET classification = '3' WHERE v_fromemail = '$mail'";
    $res = mysqli_query($link, $sql);
    if (!$res){
        if (_DBGLOG_){
                DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB delete  error : $sql". mysqli_error($link));
        }
        $response['error'] = TRUE;
        $response['error_msg'] = "Database error";
        echo json_encode($response);
        exit();
    }
    if ($res) {
        echo 'success'; // Return success message to the client
    } else {
        echo 'error: ' . mysqli_error($link); // Return an error message with details to the client
    }
    
    $vuserid = $_SESSION['userid'];
    add_audit_log($vuserid, 'spam_mail_add', '', 'Spam Mail Added '.$mail, $db);
}
function view_spam(){
    global $db,$link;
    $sql = "SELECT DISTINCT(v_fromemail) FROM $db.web_email_information WHERE classification='3' order by v_fromemail";
    $res = mysqli_query($link, $sql) or die("Could not select");
    // Return the result set
    return $res;
}
/************************ END ********************************/ 

?>
