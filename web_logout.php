<?php 
/***
 * Logout Page
 * Auth:  Aarti Ojha
 * Date: 16-01-2024
 * Description: this file use for logout user detilas and unset session
 * 
*/

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
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

require_once("config/web_mysqlconnect.php"); // for database connection
require_once("CRM/web_function.php"); // common function related file

/*wfm logout entry*/
require_once("CRM/WFM/wfm_function.php");
$wfm_function = new Wfm_connection;

// Function to securely destroy session
function secureSessionDestroy() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    // Clear all cookies
    if (isset($_COOKIE['loginusername'])) {
        setcookie('loginusername', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    if (isset($_COOKIE['logremember_me'])) {
        setcookie('logremember_me', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    // Destroy the session
    session_destroy();
}

// Function to update user status
function updateUserStatus($link, $db, $userid) {
    $logintime = date("Y-m-d H:i:s");
    $stmt = mysqli_prepare($link, "UPDATE $db.uniuserprofile SET login_status = 'offline', login_datetime = ?, last_activity = ? WHERE AtxUserID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $logintime, $logintime, $userid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Function to update login IP
function updateLoginIP($link, $db, $sessionData) {
    $timep = date("Y-m-d H:i:s");
    $stmt = mysqli_prepare($link, "UPDATE $db.logip SET TimePeriod = ?, logout_ip = ? WHERE SNo = ? AND UserName = ? AND TimePeriod IS NULL");
    if ($stmt) {
        $ip = getClientIP();
        mysqli_stmt_bind_param($stmt, "ssis", $timep, $ip, $sessionData['SNo'], $sessionData['logged']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
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
function logSecurityEvent($link, $db, $userid, $event_type, $details) {
    $timep = date("Y-m-d H:i:s");
    $ip = getClientIP();
    $stmt = mysqli_prepare($link, "INSERT INTO $db.security_logs (user_id, event_type, details, ip_address, event_time) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $userid, $event_type, $details, $ip, $timep);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Main logout logic
if (isset($_SESSION['database'])) {
    $vuserid = filter_var($_SESSION['userid'], FILTER_VALIDATE_INT);
    $name = filter_var($_SESSION['logged'], FILTER_SANITIZE_STRING);
    $final_action = htmlspecialchars("$name User Logged out", ENT_QUOTES, 'UTF-8');

    // Get client IP securely
    $ip = getClientIP();
    $timep = date("Y-m-d H:i:s");
    
    // Update login IP using prepared statement
    $stmt = mysqli_prepare($link, "UPDATE $db.logip SET TimePeriod = ?, logout_ip = ? WHERE SNo = ? AND UserName = ? AND TimePeriod IS NULL");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssis", $timep, $ip, $_SESSION['SNo'], $_SESSION['logged']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    /*------------------------WFM Report related code added aarti 30:03-23--------------------------------*/
    $wfm_function->logout_entry_wfm();
    /*------------------------WFM Report related code END aarti 30:03-23--------------------------------*/

    // Update login IP
    if (isset($_SESSION['SNo']) && isset($_SESSION['logged'])) {
        updateLoginIP($link, $db, $_SESSION);
    }

    // Update user status
    if ($vuserid) {
        updateUserStatus($link, $db, $vuserid);
        add_audit_log($vuserid, 'logout', 'null', 'User Logged Out DateTime: '.$timep, $db);
        logSecurityEvent($link, $db, $vuserid, 'logout', 'User logged out successfully');
    }

    // Securely destroy session
    secureSessionDestroy();

    // Clear any remaining session data
    session_write_close();
    
    // Redirect with security headers
    header("Location: web_login.php?flage1=1"); 
    exit; 
}

// Handle direct logout request
if (isset($_REQUEST['u'])) {
    secureSessionDestroy();
    session_write_close();
    header("Location: web_login.php?flage1=1"); 
    exit; 
}

// Default redirect
header("Location: web_login.php?flage1=1");
exit;
?>
