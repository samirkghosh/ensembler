<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 18/11/2024
 * This file is for handling common functions used in all modules
 */

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_lifetime', 0);
    session_start();
}

// Error handling
function handleError($message, $code = 500) {
    error_log($message);
    http_response_code($code);
    die(json_encode(['status' => false, 'message' => 'An error occurred']));
}

// Input validation functions
function validateInput($input) {
    if (is_array($input)) {
        return array_map('validateInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    if (is_array($email)) {
        return array_map('validateEmail', $email);
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
}

function validateCustomerId($customerId) {
    if (is_array($customerId)) {
        return array_map('validateCustomerId', $customerId);
    }
    return preg_match('/^[a-zA-Z0-9_-]+$/', $customerId) ? $customerId : '';
}

function validatePhoneNumber($phone) {
    if (is_array($phone)) {
        return array_map('validatePhoneNumber', $phone);
    }
    return preg_match('/^[0-9]{10}$/', $phone) ? $phone : '';
}

// Function to get client IP securely
function getClientIP() {
    $ip = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP);
    }
    if (!$ip) {
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }
    return $ip ?: '0.0.0.0';
}

// Function to log security events
function logSecurityEvent($event, $details = '') {
    global $link, $db;
    $timep = date("Y-m-d H:i:s");
    $ip = getClientIP();
    $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    
    $stmt = mysqli_prepare($link, "INSERT INTO $db.security_logs (user_id, event, details, ip_address, event_time) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $userid, $event, $details, $ip, $timep);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Connection to database
if (!file_exists("../config/web_mysqlconnect.php")) {
    handleError("Database configuration file not found");
}
require_once("../config/web_mysqlconnect.php");
global $links, $dbname, $SiteURLs;

$dbname = $db;
$SiteURLs = $SiteURL;
$links = $link;

function getChatDataInOut1($customerID) {
    global $links;
    
    try {
        // Validate customer ID
        $customerID = validateCustomerId($customerID);
        if (!$customerID) {
            throw new Exception("Invalid customer ID format");
        }
        
        if (!mysqli_select_db($links, 'web_chat')) {
            throw new Exception("Failed to select database: " . mysqli_error($links));
        }
        
        // Use prepared statement
        $stmt = mysqli_prepare($links, "SELECT message, direction FROM `in_out_data` WHERE customer_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "s", $customerID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            throw new Exception("Query failed: " . mysqli_error($links));
        }

        $formattedMessages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $direction = strtolower($row['direction']);
            $message = validateInput($row['message']);
            
            if ($direction === 'out') {
                $formattedMessages[] = "Sender: " . $message;
            } elseif ($direction === 'in') {
                $formattedMessages[] = "Receiver: " . $message;
            } else {
                $formattedMessages[] = "Unknown Direction: " . $message;
            }
        }

        mysqli_stmt_close($stmt);
        mysqli_free_result($result);
        
        logSecurityEvent('chat_data_retrieved', "Customer ID: $customerID");
        return $formattedMessages;
    } catch (Exception $e) {
        logSecurityEvent('chat_data_error', $e->getMessage());
        return [];
    }
}

function saveMessagesToFile($customerID) {
    try {
        // Validate customer ID
        $customerID = validateCustomerId($customerID);
        if (!$customerID) {
            throw new Exception("Invalid customer ID format");
        }

        $chat_file_history = "/var/www/html/ensembler/CRM/text_data/";

        // Create directory with secure permissions
        if (!is_dir($chat_file_history)) {
            if (!mkdir($chat_file_history, 0750, true)) {
                throw new Exception("Failed to create directory");
            }
            chmod($chat_file_history, 0750);
        }

        // Generate secure filename
        $fileName = "messages_" . time() . "_" . bin2hex(random_bytes(8)) . ".txt";
        $filePath = $chat_file_history . $fileName;

        $messages = getChatDataInOut1($customerID);
        $fileContent = implode(PHP_EOL, $messages);

        // Write file with secure permissions
        $bytesWritten = file_put_contents($filePath, $fileContent);
        if ($bytesWritten === false) {
            throw new Exception("Failed to write file");
        }
        chmod($filePath, 0640);

        logSecurityEvent('chat_file_saved', "Customer ID: $customerID, File: $fileName");
        return $filePath;
    } catch (Exception $e) {
        logSecurityEvent('chat_file_error', $e->getMessage());
        handleError("Failed to save chat messages");
    }
}

function insert_emailinformationout($email, $customerID) {
    global $links, $dbname;
    
    try {
        // Validate inputs
        $email = validateEmail($email);
        $customerID = validateCustomerId($customerID);
        
        if (!$email || !$customerID) {
            throw new Exception("Invalid email or customer ID");
        }
        
        $from_email = "rajdubey.alliance@gmail.com"; 
        $filePath = saveMessagesToFile($customerID);

        $todaytime = date("Y-m-d H:i:s");
        $V_Subject = "Chat conversation and Feedback";
        $V_Content = "The chat history is attached as the text file.";
        $V_rule = $filePath;

        // Use prepared statement
        $stmt = mysqli_prepare($links, "INSERT INTO $dbname.web_email_information_out (
            v_toemail, 
            v_fromemail, 
            d_email_date, 
            v_subject, 
            v_body, 
            V_rule
        ) VALUES (?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "ssssss", 
            $email,
            $from_email,
            $todaytime,
            $V_Subject,
            $V_Content,
            $V_rule
        );
        
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            throw new Exception("Failed to insert email information");
        }
        
        mysqli_stmt_close($stmt);
        
        logSecurityEvent('email_info_saved', "Customer ID: $customerID, Email: $email");
        return true;
    } catch (Exception $e) {
        logSecurityEvent('email_info_error', $e->getMessage());
        handleError("Failed to save email information");
    }
}

function create_feedbacklink($data = array()) {
    global $links, $dbname, $SiteURLs;
    
    try {
        if (!isset($data['createdBy'])) {
            throw new Exception("Missing required field: createdBy");
        }
        
        // Validate and sanitize inputs
        $Type = validateInput($data['Type']);
        $Call_id = validateInput($data['Call_id']);
        $Ticket_id = validateInput($data['Ticket_id']);
        $Phone_Number = validatePhoneNumber($data['Phone_Number']);
        $AgentID = validateInput($data['AgentID/Name']);
        $Extension_Number = validateInput($data['Extension_Number']);
        $customer_email = validateEmail($data['customer_email']);
        $customer_name = validateInput($data['customer_name']);
        $Call_Time = date("Y-m-d H:i:s");
        $d_requestTime = date("Y-m-d H:i:s");
        
        if (!$Phone_Number || !$customer_email) {
            throw new Exception("Invalid phone number or email");
        }
        
        // Use prepared statement
        $stmt = mysqli_prepare($links, "INSERT INTO $dbname.tbl_survey_request (
            Type, Call_id, Ticket_id, Phone_Number, Call_Time, 
            AgentID_Name, Extension_Number, d_requestTime, 
            customer_name, customer_email
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "ssssssssss",
            $Type, $Call_id, $Ticket_id, $Phone_Number, $Call_Time,
            $AgentID, $Extension_Number, $d_requestTime,
            $customer_name, $customer_email
        );
        
        mysqli_stmt_execute($stmt);
        $last_id = mysqli_insert_id($links);
        mysqli_stmt_close($stmt);
        
        $id = base64_encode($last_id);
        
        logSecurityEvent('feedback_link_created', "Customer ID: $customer_name, Email: $customer_email");
        return $SiteURLs . "CRM/feedback.php?" . $id;
    } catch (Exception $e) {
        logSecurityEvent('feedback_link_error', $e->getMessage());
        handleError("Failed to create feedback link");
    }
}
?>
