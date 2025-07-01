<?php
/***
 * Auth: Aarti Ojha
 * Date: 04/03/2024
 * This file is for handling common function used in all modules
 */

// Add security headers
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;");
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

function validateNumeric($input) {
    if (is_array($input)) {
        return array_map('validateNumeric', $input);
    }
    return is_numeric($input) ? (int)$input : 0;
}

function validateAlphanumeric($input) {
    if (is_array($input)) {
        return array_map('validateAlphanumeric', $input);
    }
    return preg_match('/^[a-zA-Z0-9_-]+$/', $input) ? $input : '';
}

function validateEmail($email) {
    if (is_array($email)) {
        return array_map('validateEmail', $email);
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
}

function validatePhone($phone) {
    if (is_array($phone)) {
        return array_map('validatePhone', $phone);
    }
    return preg_match('/^[0-9+\-\s()]{10,15}$/', $phone) ? $phone : '';
}

include_once "../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this

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

// [vastvikta][25-04-2025]
/* If a case is created for a particlar meter number more than 10 times and closed (on same sub category), the case then should be escalated to the supervisor (sebetet@lec.co.ls) the next time it is created */
function checkAndEscalate($customerid, $subCategory) {
    global $link, $db;
    
    // Validate inputs
    $customerid = validateNumeric($customerid);
    $subCategory = validateAlphanumeric($subCategory);
    
    if (!$customerid || !$subCategory) {
        logSecurityEvent('invalid_escalation_input', "Invalid customer ID or subcategory");
        return false;
    }

    try {
        // Use prepared statement for query
        $stmt = mysqli_prepare($link, "SELECT COUNT(*) AS case_count FROM $db.web_problemdefination WHERE vCustomerID = ? AND vSubCategory = ? AND iCaseStatus = 3");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }

        mysqli_stmt_bind_param($stmt, "is", $customerid, $subCategory);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $caseCount = $row['case_count'];

            if ($caseCount >= escalation_case_count()) {
                // Get mail template using prepared statement
                $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_mailformats WHERE MailStatus = 1 AND MailTemplateName = 'multiple_case_creation_alert'");
                if (!$stmt) {
                    throw new Exception("Failed to prepare mail template statement");
                }
                
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                
                if (!$row) {
                    throw new Exception("Mail template not found");
                }

                $subject = validateInput($row['MailSubject']);
                $greeting = validateInput($row['MailGreeting']);
                $body = validateInput($row['MailBody']);
                $signature = validateInput($row['MailSignature']);
                $expiry = validateInput($row['MailExpiry']);

                $phone_no = get_phone_no($customerid);
                if (!$phone_no) {
                    throw new Exception("Phone number not found");
                }

                $subject = str_replace("%phone%", validateInput($phone_no), $subject);
                $body = str_replace("%phone%", validateInput($phone_no), $body);
                $content = "$greeting$body$signature";

                $supervisorEmail = getEscalationSupervisorMail();
                if (!$supervisorEmail) {
                    throw new Exception("Supervisor email not found");
                }

                $status = getMailStatus();
                
                if ($status == '1') {
                    $stmt = mysqli_prepare($link, "INSERT INTO $db.web_email_information(v_toemail, v_fromemail, v_subject, v_body, email_type, module, i_expiry) VALUES (?, 'info@alliance-infotech.com', ?, ?, 'OUT', 'Escalation Mail After 5 Cases Closed', ?)");
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "ssss", $supervisorEmail, $subject, $content, $expiry);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        
                        logSecurityEvent('case_escalated', "Case escalated for customer ID: $customerid");
                        return true;
                    }
                }
            }
        }
        return false;
    } catch (Exception $e) {
        logSecurityEvent('escalation_error', $e->getMessage());
        return false;
    }
}

function getMailStatus() {
    global $db, $link;
    
    try {
        $stmt = mysqli_prepare($link, "SELECT status_active FROM $db.tbl_connection");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            mysqli_stmt_close($stmt);
            return $row[0];
        }
        
        mysqli_stmt_close($stmt);
        return null;
    } catch (Exception $e) {
        logSecurityEvent('mail_status_error', $e->getMessage());
        return null;
    }
}

function getEscalationSupervisorMail() {
    global $link, $db;
    
    try {
        $stmt = mysqli_prepare($link, "SELECT sent_mail FROM $db.tbl_connection");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            mysqli_stmt_close($stmt);
            return validateEmail($row[0]);
        }
        
        mysqli_stmt_close($stmt);
        return null;
    } catch (Exception $e) {
        logSecurityEvent('supervisor_mail_error', $e->getMessage());
        return null;
    }
}

function escalation_case_count() {
    global $link, $db;
    
    try {
        $stmt = mysqli_prepare($link, "SELECT case_count FROM $db.tbl_connection");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            mysqli_stmt_close($stmt);
            return validateNumeric($row[0]);
        }
        
        mysqli_stmt_close($stmt);
        return 0;
    } catch (Exception $e) {
        logSecurityEvent('case_count_error', $e->getMessage());
        return 0;
    }
}

function get_phone_no($customerid) {
    global $db, $link;
    
    try {
        $customerid = validateAlphanumeric($customerid);
        if (!$customerid) {
            throw new Exception("Invalid customer ID format");
        }
        
        $stmt = mysqli_prepare($link, "SELECT phone FROM $db.web_accounts WHERE AccountNumber = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "s", $customerid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return validatePhone($row['phone']);
        }
        
        mysqli_stmt_close($stmt);
        return null;
    } catch (Exception $e) {
        logSecurityEvent('phone_lookup_error', $e->getMessage());
        return null;
    }
}

// get email for sending crm daily report(adhoc)
function get_adhoc_mail() {
    global $link, $dbname;
    
    try {
        $stmt = mysqli_prepare($link, "SELECT adhoc_mail FROM $dbname.tbl_connection");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            mysqli_stmt_close($stmt);
            return validateEmail($row[0]);
        }
        
        mysqli_stmt_close($stmt);
        return '';
    } catch (Exception $e) {
        logSecurityEvent('adhoc_mail_error', $e->getMessage());
        return '';
    }
}

// get from email for sending emails notification
function get_from_mail() {
    global $link, $dbname;
    
    try {
        $stmt = mysqli_prepare($link, "SELECT v_username FROM $dbname.tbl_connection");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            mysqli_stmt_close($stmt);
            return validateEmail($row[0]);
        }
        
        mysqli_stmt_close($stmt);
        return '';
    } catch (Exception $e) {
        logSecurityEvent('from_mail_error', $e->getMessage());
        return '';
    }
}

// Get Customers fullname using vCustomerID : By farhan on 09-04-2021
function getfname($id) {
    global $db, $link;
    
    try {
        $id = validateAlphanumeric($id);
        if (!$id) {
            throw new Exception("Invalid ID format");
        }
        
        $stmt = mysqli_prepare($link, "SELECT CONCAT(vFirstName, ' ', vLastName) as fullname FROM $db.tbl_mst_user_company WHERE I_UserID = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return validateInput($row['fullname']);
        }
        
        mysqli_stmt_close($stmt);
        return '';
    } catch (Exception $e) {
        logSecurityEvent('name_lookup_error', $e->getMessage());
        return '';
    }
}

function get_username($userid) {
    global $db, $link;
    
    $userid = validateAlphanumeric($userid);
    if (!$userid) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $userid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['AtxUserName']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function getFileName($SmartFileName) {
    try {
        $SmartFileName = validateInput($SmartFileName);
        if (empty($SmartFileName)) {
            throw new Exception("Invalid filename");
        }
        
        // Validate file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $extension = strtolower(pathinfo($SmartFileName, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type");
        }
        
        // Generate secure filename
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $newFileName = $timestamp . '_' . $random . '.' . $extension;
        
        return $newFileName;
    } catch (Exception $e) {
        logSecurityEvent('filename_error', $e->getMessage());
        return '';
    }
}

function color($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT color FROM $db.web_category WHERE id = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['color']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function getissuename($issue_id, $db) {
    global $link;
    
    $issue_id = validateNumeric($issue_id);
    if (!$issue_id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT issue_name FROM $db.web_mst_Issues WHERE id = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $issue_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['issue_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function getlanguagename($lang_id) {
    global $db, $link;
    
    $lang_id = validateNumeric($lang_id);
    if (!$lang_id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT lang_Name FROM $db.web_language WHERE id = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $lang_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['lang_Name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function get_age_group($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_age_range WHERE id = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        $age = validateInput($row['age_group']) . "-(" . validateInput($row['age_range']) . ")";
        return $age;
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function get_gender($id) {
    global $db, $link;
    
    $id = validateAlphanumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT `name` FROM $db.web_gender WHERE `value` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function get_mno($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_mno WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['category']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function get_isp($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_isp WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['category']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function comp_type($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.tbl_comp_type WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['category']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function RecordingFileName($SmartFileName) {
    try {
        $SmartFileName = validateInput($SmartFileName);
        if (empty($SmartFileName)) {
            throw new Exception("Invalid filename");
        }
        
        // Validate file extension
        $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
        $extension = strtolower(pathinfo($SmartFileName, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid audio file type");
        }
        
        // Generate secure filename
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        $newFileName = $timestamp . '_' . $random . '.' . $extension;
        
        return $newFileName;
    } catch (Exception $e) {
        logSecurityEvent('recording_filename_error', $e->getMessage());
        return '';
    }
}

function ddmmyydateFormat($date) {
    try {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new Exception("Invalid date format");
        }
        
        return date('d-m-Y', $timestamp);
    } catch (Exception $e) {
        logSecurityEvent('date_format_error', $e->getMessage());
        return '';
    }
}

function ddmmyyHisdateTimeFormat($date) {
    try {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new Exception("Invalid datetime format");
        }
        
        return date('d-m-Y H:i:s', $timestamp);
    } catch (Exception $e) {
        logSecurityEvent('datetime_format_error', $e->getMessage());
        return '';
    }
}

function yymmddDateFormat($date) {
    try {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new Exception("Invalid date format");
        }
        
        return date('Y-m-d', $timestamp);
    } catch (Exception $e) {
        logSecurityEvent('date_format_error', $e->getMessage());
        return '';
    }
}

function randomKey($length) {
    try {
        if (!is_numeric($length) || $length < 1 || $length > 64) {
            throw new Exception("Invalid key length");
        }
        
        // Generate cryptographically secure random key
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    } catch (Exception $e) {
        logSecurityEvent('random_key_error', $e->getMessage());
        return '';
    }
}

function case_type($type) {
    global $db, $link;
    
    $type = validateAlphanumeric($type);
    if (!$type) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_case_type WHERE `value` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function gender($gender) {
    global $db, $link;
    
    $gender = validateAlphanumeric($gender);
    if (!$gender) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_gender WHERE `value` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $gender);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function category($cat) {
    global $db, $link;
    
    $cat = validateNumeric($cat);
    if (!$cat) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_category WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $cat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['category_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function subcategory($subcat) {
    global $db, $link;
    
    $subcat = validateNumeric($subcat);
    if (!$subcat) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_subcategory WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $subcat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['subcategory_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function cat_from_subcat($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    if (!$id) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT category_id FROM $db.web_subcategory WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateNumeric($row['category_id']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function source($source) {
    global $db, $link;
    
    $source = validateNumeric($source);
    if (!$source) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_source WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $source);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['source_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function ticketstatus($ticketstatus) {
    global $db, $link;
    
    $ticketstatus = validateNumeric($ticketstatus);
    if (!$ticketstatus) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_ticketstatus WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $ticketstatus);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['status_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function assignto($assignto) {
    global $db, $link;
    
    $assignto = validateAlphanumeric($assignto);
    if (!$assignto) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.uniuserprofile WHERE `AtxUserID` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $assignto);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['AtxUserName']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function assignfor($assignfor) {
    global $db, $link;
    
    $assignfor = validateAlphanumeric($assignfor);
    if (!$assignfor) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.uniuserprofile WHERE `AtxUserID` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $assignfor);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['AtxUserName']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function project($projectid) {
    global $db, $link;
    
    $projectid = validateNumeric($projectid);
    if (!$projectid) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_project WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $projectid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['project_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function city($city) {
    global $db, $link;
    
    $city = validateNumeric($city);
    if (!$city) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_city WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $city);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['city_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function region($region) {
    global $db, $link;
    
    $region = validateNumeric($region);
    if (!$region) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_region WHERE `id` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $region);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['region_name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function regional_offices($name) {
    global $db, $link;
    
    $name = validateAlphanumeric($name);
    if (!$name) {
        return '';
    }
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_regional_offices WHERE `name` = ?");
    if (!$stmt) {
        return '';
    }
    
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return validateInput($row['name']);
    }
    
    mysqli_stmt_close($stmt);
    return '';
}

function stakeholder($stakeholder) {
    global $db, $link;
    
    $stakeholder = validateNumeric($stakeholder);
    if (!$stakeholder) {
        return '';
    }
    
		return $row; // Returns an associative array like ['id' => 123]
	} else {
		return null; // No matching row found
	}
}

function interaction_history_insert($ticketid, $interact_id, $intraction_type, $customerid) {
    global $link, $db;
    
    try {
        // Validate inputs
        $ticketid = validateNumeric($ticketid);
        $interact_id = validateNumeric($interact_id);
        $intraction_type = validateAlphanumeric($intraction_type);
        $customerid = validateAlphanumeric($customerid);
        
        if (!$ticketid || !$interact_id || !$intraction_type || !$customerid) {
            throw new Exception("Invalid input parameters");
        }
        
        // Get current timestamp
        $timep = date("Y-m-d H:i:s");
        
        // Insert interaction history
        $stmt = mysqli_prepare($link, "INSERT INTO $db.web_interaction_history (ticketid, interact_id, intraction_type, customerid, created_at) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }
        
        mysqli_stmt_bind_param($stmt, "iisss", $ticketid, $interact_id, $intraction_type, $customerid, $timep);
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            throw new Exception("Failed to insert interaction history");
        }
        
        mysqli_stmt_close($stmt);
        
        // Log successful interaction
        logSecurityEvent('interaction_inserted', "Ticket ID: $ticketid, Type: $intraction_type");
        
        return true;
    } catch (Exception $e) {
        logSecurityEvent('interaction_error', $e->getMessage());
        return false;
    }
}

/*  
	* Author : Farhan Akhtar 
	* Last Modified On : 22-04-2025
	* Purpose : Functions to Capture and Calculte the Assign Department of Cases 
*/

	function createCaseDeptTimelinesTable($link) {
		try {
			$query = "CREATE TABLE IF NOT EXISTS case_dept_timelines (
				id INT AUTO_INCREMENT PRIMARY KEY,
				case_id INT NOT NULL,
				department_id INT NOT NULL,
				start_time DATETIME NOT NULL,
				end_time DATETIME,
				duration_seconds INT,
				status VARCHAR(50) NOT NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				INDEX (case_id),
				INDEX (department_id),
				INDEX (status)
			)";
			
			if (!mysqli_query($link, $query)) {
				throw new Exception("Failed to create case_dept_timelines table");
			}
			
			return true;
		} catch (Exception $e) {
			logSecurityEvent('table_creation_error', $e->getMessage());
			return false;
		}
	}

	function calculateDurationInSeconds($start, $end) {
		try {
			$startTime = strtotime($start);
			$endTime = strtotime($end);
			
			if ($startTime === false || $endTime === false) {
				throw new Exception("Invalid date format");
			}
			
			return $endTime - $startTime;
		} catch (Exception $e) {
			logSecurityEvent('duration_calculation_error', $e->getMessage());
			return 0;
		}
	}

	function createNewCase($link, $data) {
		global $db;
		
		try {
			// Validate required fields
			$requiredFields = ['case_id', 'department_id', 'status'];
			foreach ($requiredFields as $field) {
				if (!isset($data[$field]) || empty($data[$field])) {
					throw new Exception("Missing required field: $field");
				}
			}
			
			// Prepare data
			$caseId = validateNumeric($data['case_id']);
			$departmentId = validateNumeric($data['department_id']);
			$status = validateAlphanumeric($data['status']);
			$startTime = date('Y-m-d H:i:s');
			
			// Insert new case timeline
			$stmt = mysqli_prepare($link, "INSERT INTO $db.case_dept_timelines (case_id, department_id, start_time, status) VALUES (?, ?, ?, ?)");
			if (!$stmt) {
				throw new Exception("Failed to prepare statement");
			}
			
			mysqli_stmt_bind_param($stmt, "iiss", $caseId, $departmentId, $startTime, $status);
			$result = mysqli_stmt_execute($stmt);
			
			if (!$result) {
				throw new Exception("Failed to create new case timeline");
			}
			
			mysqli_stmt_close($stmt);
			
			// Log successful case creation
			logSecurityEvent('case_created', "Case ID: $caseId, Department: $departmentId");
			
			return true;
		} catch (Exception $e) {
			logSecurityEvent('case_creation_error', $e->getMessage());
			return false;
		}
	}
	

	function forwardToDept($link, $data) {
		global $db;
		
		try {
			// Validate required fields
			$requiredFields = ['case_id', 'old_dept_id', 'new_dept_id', 'status'];
			foreach ($requiredFields as $field) {
				if (!isset($data[$field]) || empty($data[$field])) {
					throw new Exception("Missing required field: $field");
				}
			}
			
			// Prepare data
			$caseId = validateNumeric($data['case_id']);
			$oldDeptId = validateNumeric($data['old_dept_id']);
			$newDeptId = validateNumeric($data['new_dept_id']);
			$status = validateAlphanumeric($data['status']);
			$currentTime = date('Y-m-d H:i:s');
			
			// Begin transaction
			mysqli_begin_transaction($link);
			
			try {
				// Update old department timeline
				$stmt = mysqli_prepare($link, "UPDATE $db.case_dept_timelines SET end_time = ?, duration_seconds = ? WHERE case_id = ? AND department_id = ? AND end_time IS NULL");
				if (!$stmt) {
					throw new Exception("Failed to prepare update statement");
				}
				
				$duration = calculateDurationInSeconds($data['start_time'], $currentTime);
				mysqli_stmt_bind_param($stmt, "siii", $currentTime, $duration, $caseId, $oldDeptId);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
				
				// Create new department timeline
				$stmt = mysqli_prepare($link, "INSERT INTO $db.case_dept_timelines (case_id, department_id, start_time, status) VALUES (?, ?, ?, ?)");
				if (!$stmt) {
					throw new Exception("Failed to prepare insert statement");
				}
				
				mysqli_stmt_bind_param($stmt, "iiss", $caseId, $newDeptId, $currentTime, $status);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
				
				// Commit transaction
				mysqli_commit($link);
				
				// Log successful department change
				logSecurityEvent('case_forwarded', "Case ID: $caseId, From: $oldDeptId, To: $newDeptId");
				
				return true;
			} catch (Exception $e) {
				mysqli_rollback($link);
				throw $e;
			}
		} catch (Exception $e) {
			logSecurityEvent('case_forward_error', $e->getMessage());
			return false;
		}
	}
	

	function closeCase($link, $data) {
		global $db;
		
		try {
			// Validate required fields
			$requiredFields = ['case_id', 'department_id'];
			foreach ($requiredFields as $field) {
				if (!isset($data[$field]) || empty($data[$field])) {
					throw new Exception("Missing required field: $field");
				}
			}
			
			// Prepare data
			$caseId = validateNumeric($data['case_id']);
			$departmentId = validateNumeric($data['department_id']);
			$currentTime = date('Y-m-d H:i:s');
			
			// Update case timeline
			$stmt = mysqli_prepare($link, "UPDATE $db.case_dept_timelines SET end_time = ?, duration_seconds = ?, status = 'closed' WHERE case_id = ? AND department_id = ? AND end_time IS NULL");
			if (!$stmt) {
				throw new Exception("Failed to prepare statement");
			}
			
			$duration = calculateDurationInSeconds($data['start_time'], $currentTime);
			mysqli_stmt_bind_param($stmt, "siii", $currentTime, $duration, $caseId, $departmentId);
			$result = mysqli_stmt_execute($stmt);
			
			if (!$result) {
				throw new Exception("Failed to close case");
			}
			
			mysqli_stmt_close($stmt);
			
			// Log successful case closure
			logSecurityEvent('case_closed', "Case ID: $caseId, Department: $departmentId");
			
			return true;
		} catch (Exception $e) {
			logSecurityEvent('case_closure_error', $e->getMessage());
			return false;
		}
	}


	function formatDuration($seconds) {
		try {
			if (!is_numeric($seconds) || $seconds < 0) {
				throw new Exception("Invalid duration");
			}
			
			$hours = floor($seconds / 3600);
			$minutes = floor(($seconds % 3600) / 60);
			$seconds = $seconds % 60;
			
			return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		} catch (Exception $e) {
			logSecurityEvent('duration_format_error', $e->getMessage());
			return "00:00:00";
		}
	}

	function whatsapp_template($ticketid, $case_type, $data_arr=[]){
		global $db,$link;
	
		if($case_type!="" || $ticketid!=""){
			$sql= $link->query("SELECT * FROM $db.whatsapp_template WHERE status=1 AND temp_name='$case_type'");
			$row = $sql->fetch_assoc();
			$content =$row['temp_content'];
			return array('msg' => $content) ;
		}
	}
	// function to insert whatsapp message [vastvikta][29-05-2025]
	function insert_whatsapp_message($data) {
		global $link, $db;
	
		$agentid = $_SESSION['userid'];
		$caller_id = $data['send_to'];
		$account_phone_number = $data['send_from'];
		$message = addslashes($data['message']);
		$todaytime = date("Y-m-d H:i:s");
		$template_name = $data['template_name'];
		$ICASEID = $data['ICASEID'];
		$customer_name = $data['customer_name'];
		$customerid = isset($data['customerid']) ? $data['customerid'] : '';

	
		$queue_session = '';
		$mesg_type = '1'; // bulk message
		$mesg_flag = '0'; // msg flag 0
	
		// Insert WhatsApp message into whatsapp_out_queue
		$sql_whatsapp = "INSERT INTO $db.whatsapp_out_queue (
			send_to, send_from, message, message_type_flag, status, create_date,
			bulk_session_id, created_by, channel_type, msg_flag, queue_session,
			user_name, attachment, type, template_name
		) VALUES (
			'$caller_id', '$account_phone_number', '$message', '$mesg_type', '$mesg_flag',
			'$todaytime', '', '$agentid', '1', 'OUT', '$queue_session',
			'$customer_name', '', '1', '$template_name'
		)";
	
		$result = mysqli_query($link, $sql_whatsapp) or die("Error inserting WhatsApp message: " . mysqli_error($link));
		$msgid = mysqli_insert_id($link);
	
		// Interaction log for WhatsApp message
		$intraction_type = 'Whatsapp';
		$sql_find_email = "SELECT AccountNumber FROM $db.web_accounts WHERE phone = '$caller_id'";
		$result_acc = mysqli_query($link, $sql_find_email);
	
		if ($result_acc && mysqli_num_rows($result_acc) > 0) {
			$row = mysqli_fetch_assoc($result_acc);
			if (empty($customerid)) {
				$customerid = $row['AccountNumber'];
			}
		}
	
		$ICASEID = !empty($data['ICASEID']) ? $data['ICASEID'] : (!empty($data['caseid']) ? $data['caseid'] : null);
	
		$sql_interaction = "INSERT INTO $db.interaction (
			caseid, intraction_type, email, mobile, name, interact_id,
			customer_id, remarks, filename, created_date, type, created_by
		) VALUES (
			'$ICASEID', '$intraction_type', '', '$caller_id', '', '$msgid',
			'$customerid', '$message', '', '$todaytime', 'OUT', '$agentid'
		)";
	
		mysqli_query($link, $sql_interaction) or die("Error inserting interaction: " . mysqli_error($link));
	}
	
	

/* End */
?>