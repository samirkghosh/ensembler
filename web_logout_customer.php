<?php
/**
 * Customer Logout Script
 * Author: Aarti Ojha
 * Date: 17-12-2024
 * This file handles the customer logout process, clearing session data and redirecting to the login page.
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

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to securely destroy session
function secureSessionDestroy() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/', '', true, true);
    }
    
    // Destroy the session
    session_destroy();
}

// Unset specific session variable for customer view
unset($_SESSION['customer_view']);

// Securely destroy the session
secureSessionDestroy();

// Redirect to the customer login page with a flag to indicate logout status
header("Location: customer_login.php?flage1=1");
exit;
?>
