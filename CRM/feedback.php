<?php
/***
 * Feedback page
 * Author: Aarti Ojha
 * Date: 07-10-2024
 * Description: This file handles Feedback form. 
 */

// Add security headers
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;");
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

// Input validation functions
function validateInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function validateCompanyId($companyId) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $companyId);
}

function validateType($type) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $type);
}

function validateId($id) {
    return is_numeric($id) && $id > 0;
}

include_once("../config/web_mysqlconnect.php"); // Connection to the database

// Get the URL and split it
$url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$data = explode('?', $url);

// Split the query string by '&'
$queryParams = explode('&', $data[1]);

// Decode the first parameter (base64 encoded id)
$ids = base64_decode($queryParams[0]);

// Parse the remaining query parameters into an associative array
$params = [];
for ($i = 1; $i < count($queryParams); $i++) {
    parse_str($queryParams[$i], $temp);
    $params = array_merge($params, $temp);
}

$master = 'CampaignTracker';
// Get the type and company_id from the parsed query parameters
$type = isset($params['Type']) ? validateType($params['Type']) : '';
$companyID = isset($params['company_id']) ? validateCompanyId($params['company_id']) : '';

// Query to get the related database name using prepared statement
$stmt = mysqli_prepare($link, "SELECT related_database_name FROM $master.companies WHERE company_id = ?");
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($link));
}

mysqli_stmt_bind_param($stmt, "s", $companyID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $company = mysqli_fetch_assoc($result);
    $relatedDatabaseName = validateInput($company['related_database_name']);
    $db = $relatedDatabaseName;
} else {
    echo 'Invalid company ID. No database found.';
    exit;
}

mysqli_stmt_close($stmt);

if (!empty($ids)) {
    // Use prepared statement for survey request query
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_survey_request WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($link));
    }

    mysqli_stmt_bind_param($stmt, "i", $ids);
    mysqli_stmt_execute($stmt);
    $record = mysqli_stmt_get_result($stmt);

    $numrow = mysqli_num_rows($record);
    $flag = ($numrow == '0' || $numrow == '') ? '1' : '0';

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>    
<html lang="en">    
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback Form</title>
    <style>    
        .container {    
            border-radius: 5px;    
            background-color: #f2f2f2;    
            padding: 20px;    
        }
        .submit_form {
            background-color: #ff1d1e !important;
            border-color: #ff1d1e !important;
            color: #fff !important;
            border-radius: 6px !important;
            width: 97px !important;
            text-align: center;
        }    
        .header {
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
        }
        .message {
            text-align: center;
            font-size: 15px;
            font-weight: 500;
        }     
    </style>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>  
</head>    
<body>
    <div class="header">
        <img class="logo" src="../public/images/HELB.png" alt="Logo" width="278" height="100">
    </div>          
    <div class="container mt-3">
        <?php if ($flag == '1') { ?>
            <p id="thank-you-message" class="message">
                <span>Feedback form is already processed.</span><br/>
                Thank you for contacting us. We will be in touch with you very soon.
            </p>
        <?php } else { ?>
            <p id="thank-you-message" class="message" style="display: none">
                Thank you for contacting us. We will be in touch with you very soon.
            </p>
            <div class="main_div">
                <h4>Customer Feedback Form</h4>
                <p>Hello, Thank you for connecting us. We value your concern. To serve you better, please rate us:</p>
                <form name="feedregistration" id="feedregistration" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="user_id" id="user_id" value="<?php echo validateInput($ids); ?>">
                    <input type="hidden" name="action" id="action" value="insert_rating_feedback">
                    <input type="hidden" name="Type" id="Type" value="<?php echo validateInput($type); ?>">
                    <input type="hidden" name="companyID" id="companyID" value="<?php echo validateInput($companyID); ?>">
                    <div class="form-check">
                        <input type="radio" class="form-check-input radio_check" id="radio1" name="optradio" value="1" required>
                        <label class="form-check-label" for="radio1">Very satisfied</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input radio_check" id="radio2" name="optradio" value="2">
                        <label class="form-check-label" for="radio2">Satisfied</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input radio_check" id="radio3" name="optradio" value="3">
                        <label class="form-check-label" for="radio3">Neutral</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input radio_check" id="radio4" name="optradio" value="4">
                        <label class="form-check-label" for="radio4">Unsatisfied</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input radio_check" id="radio5" name="optradio" value="5">
                        <label class="form-check-label" for="radio5">Very Unsatisfied</label>
                    </div>
                </form>
                <input name="submit_form" type="submit" value="Submit" class="btn btn-primary mt-3 submit_form">
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="../public/js/common.js"></script>
    <script>
    // Add client-side validation
    document.querySelector('.submit_form').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validate CSRF token
        var csrfToken = document.querySelector('input[name="csrf_token"]').value;
        if (!csrfToken) {
            alert('Invalid form submission');
            return false;
        }
        
        // Validate radio selection
        var selectedRating = document.querySelector('input[name="optradio"]:checked');
        if (!selectedRating) {
            alert('Please select a rating');
            return false;
        }
        
        // Submit form if validation passes
        document.getElementById('feedregistration').submit();
    });
    </script>
</body>    
</html>