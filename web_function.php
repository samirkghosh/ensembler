// CSRF token validation function
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

// Function to regenerate CSRF token
function regenerateCSRFToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
} 