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

function validateSessionId($sessionId) {
    if (is_array($sessionId)) {
        return array_map('validateSessionId', $sessionId);
    }
    return preg_match('/^[a-zA-Z0-9_-]+$/', $sessionId) ? $sessionId : '';
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
if (!file_exists("../../config/web_mysqlconnect.php")) {
    handleError("Database configuration file not found");
}
require_once("../../config/web_mysqlconnect.php");

function getChatDataInOut($customerID, $chat_session_id) {
    global $db, $link;
    
    try {
        // Validate inputs
        $customerID = validateCustomerId($customerID);
        $chat_session_id = validateSessionId($chat_session_id);
        
        if (!$customerID || !$chat_session_id) {
            throw new Exception("Invalid customer ID or session ID");
        }
        
        if (!mysqli_select_db($link, 'web_chat')) {
            throw new Exception("Failed to select database: " . mysqli_error($link));
        }
        
        // Use prepared statement
        $stmt = mysqli_prepare($link, "SELECT message, direction FROM $db.`in_out_data` WHERE customer_id = ? AND chat_session_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $customerID, $chat_session_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            throw new Exception("Query failed: " . mysqli_error($link));
        }

        $formattedMessages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $direction = strtoupper($row['direction']);
            $message = validateInput($row['message']);
            
            if ($direction === 'OUT') {
                $formattedMessages[] = "Sender: " . $message;
            } elseif ($direction === 'IN') {
                $formattedMessages[] = "Receiver: " . $message;
            } else {
                $formattedMessages[] = "Unknown Direction: " . $message;
            }
        }

        mysqli_stmt_close($stmt);
        mysqli_free_result($result);
        
        logSecurityEvent('chat_data_retrieved', "Customer ID: $customerID, Session ID: $chat_session_id");
        return $formattedMessages;
    } catch (Exception $e) {
        logSecurityEvent('chat_data_error', $e->getMessage());
        return [];
    }
}

function saveMessagesToFile($customerID, $chat_session_id) {
    global $BasePath;
    
    try {
        // Validate inputs
        $customerID = validateCustomerId($customerID);
        $chat_session_id = validateSessionId($chat_session_id);
        
        if (!$customerID || !$chat_session_id) {
            throw new Exception("Invalid customer ID or session ID");
        }
        
        $chat_file_history = "/home/$BasePath/webchat/";
        $database_storage = "/$BasePath/webchat/";
        
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
        $returnFilePath = $database_storage . $fileName;
        
        // Fetch chat data
        $messages = getChatDataInOut($customerID, $chat_session_id);
        
        // Prepare file content
        $fileContent = implode(PHP_EOL, $messages);
        
        // Write file with secure permissions
        $bytesWritten = file_put_contents($filePath, $fileContent);
        if ($bytesWritten === false) {
            throw new Exception("Failed to write file");
        }
        chmod($filePath, 0640);
        
        logSecurityEvent('chat_file_saved', "Customer ID: $customerID, Session ID: $chat_session_id, File: $fileName");
        return $returnFilePath;
    } catch (Exception $e) {
        logSecurityEvent('chat_file_error', $e->getMessage());
        handleError("Failed to save chat messages");
    }
}

function insert_emailinformationout($email, $customerID, $chat_session_id, $data_arr = [], $case_type) {
    global $link, $db;
    
    try {
        // Validate inputs
        $email = validateEmail($email);
        $customerID = validateCustomerId($customerID);
        $chat_session_id = validateSessionId($chat_session_id);
        $case_type = validateInput($case_type);
        
        if (!$email || !$customerID || !$chat_session_id || !$case_type) {
            throw new Exception("Invalid input parameters");
        }
        
        // Get email template using prepared statement
        $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_mailformats WHERE MailStatus = 1 AND MailTemplateName = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "s", $case_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result || !($rowemail = mysqli_fetch_array($result))) {
            throw new Exception("Email template not found");
        }
        
        $subject = $rowemail['MailSubject'];
        $greeting = $rowemail['MailGreeting'];
        $body = $rowemail['MailBody'];
        $signature = $rowemail['MailSignature'];
        $chat_feedback_link = isset($data_arr['chat_feedback_link']) ? validateInput($data_arr['chat_feedback_link']) : '';
        
        $body = str_replace("%chat_feedback_link%", $chat_feedback_link, $body);
        $content = $greeting . $body;
        
        $from_email = "rajdubey.alliance@gmail.com";
        $filePath = saveMessagesToFile($customerID, $chat_session_id);
        
        $todaytime = date("Y-m-d H:i:s");
        $V_Subject = "Chat conversation and " . $subject;
        $V_Content = $content . "<br> The chat history is attached as the text file.<br> " . $signature;
        $V_rule = $filePath;
        
        // Use prepared statement for insert
        $stmt = mysqli_prepare($link, "INSERT INTO $db.web_email_information_out (
            v_toemail, 
            v_fromemail, 
            d_email_date, 
            v_subject, 
            v_body, 
            V_rule,
            email_type
        ) VALUES (?, ?, ?, ?, ?, ?, 'OUT')");
        
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
        
        logSecurityEvent('email_info_saved', "Customer ID: $customerID, Email: $email, Session ID: $chat_session_id");
        return true;
    } catch (Exception $e) {
        logSecurityEvent('email_info_error', $e->getMessage());
        handleError("Failed to save email information");
    }
}
?>