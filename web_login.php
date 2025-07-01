<?php
/***
 * Login Layout
 * Author: Aarti Ojha
 * Date: 11-01-2024
 * This file handles user authentication and login process.
 * 
 * Please do not modify this file without permission.
 **/

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data:;");
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

// Rate limiting
function checkRateLimit($ip) {
    $rateLimitFile = sys_get_temp_dir() . '/rate_limit_' . md5($ip) . '.txt';
    $maxAttempts = 5;
    $timeWindow = 300; // 5 minutes
    
    if (file_exists($rateLimitFile)) {
        $data = json_decode(file_get_contents($rateLimitFile), true);
        if ($data['time'] + $timeWindow > time()) {
            if ($data['attempts'] >= $maxAttempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['attempts' => 1, 'time' => time()];
        }
    } else {
        $data = ['attempts' => 1, 'time' => time()];
    }
    
    file_put_contents($rateLimitFile, json_encode($data));
    return true;
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Secure cookie settings
$cookie_options = array(
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
);

// Input validation functions
function validateInput($input) {
    if (is_array($input)) {
        return array_map('validateInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateCompanyId($companyId) {
    return preg_match('/^[a-zA-Z0-9]{3,50}$/', $companyId);
}

function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9]{3,50}$/', $username);
}

function validatePassword($password) {
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// Include database connection file
require_once("config/web_mysqlconnect.php");

// Get client IP with proxy consideration
$currentIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// Check rate limiting
if (!checkRateLimit($currentIP)) {
    die('Too many login attempts. Please try again later.');
}

// Check if login username cookie is set and assign it to $loginID variable
if (!empty($_COOKIE['loginusername'])) {
   $loginID = validateInput($_COOKIE['loginusername']);
} else {
   $loginID = '';
}

// Check if remember me cookie is set and assign appropriate value to $selremember variable
if ((!empty($_COOKIE['logremember_me'])) && ($_COOKIE['logremember_me'] === '1')) {
   $selremember = "checked";
} else {
   $selremember = "";
}

// Set secure cookies if remember me is checked
if (isset($_POST['remember_me']) && $_POST['remember_me'] === '1') {
    $expiry = time() + 30 * 24 * 60 * 60; // 30 days
    setcookie('loginusername', $loginID, [
        'expires' => $expiry,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    setcookie('logremember_me', '1', [
        'expires' => $expiry,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Log failed login attempts
function logFailedAttempt($username, $ip) {
    global $link;
    $stmt = mysqli_prepare($link, "INSERT INTO login_attempts (username, ip_address, attempt_time) VALUES (?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "ss", $username, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Login_Flow') {
    // Validate CSRF token
    if (!validateCSRFToken()) {
        die('Invalid request');
    }
    
    // Validate inputs
    $loginID = validateInput($_POST['loginID'] ?? '');
    $passID = validateInput($_POST['passID'] ?? '');
    $companyID = validateInput($_POST['companyID'] ?? '');
    
    if (!validateUsername($loginID) || !validateCompanyId($companyID)) {
        logSecurityEvent('login_validation_failed', "Invalid username or company ID format");
        die('Invalid input format');
    }
    
    // Rest of login processing code...
}
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Welcome to Ensembler site | Login Page</title>
   <meta name="format-detection" content="telephone=no">
   <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.min.css">
   <!-- Dynamic css -->
   <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/facebook.css">
</head>

<body style='margin: 0;background-image:url("public/images/test_land.png");background-size: cover;'>
   <div class="topnavlogin">
      <img id="main_logo_login" src="public/images/alliance_logo.png" style="height:62px" alt="Alliance Logo">
      <div class="login-container" style="margin-top:-26px">
         <!-- Login form -->
         <form id="formlogin" name="frmlogin" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div>
               <!-- Hidden input field for login action -->
               <input type="hidden" name="action" value="Login_Flow" />
               <span id="usrn">
                  <!-- Username input field -->
                  <input type="text" placeholder="User Name" class="inputlogin" name="loginID" id="loginID"
                     value="<?php echo htmlspecialchars($loginID, ENT_QUOTES, 'UTF-8'); ?>"
                     onfocus="clearText(this)" onblur="clearText(this)"
                     required
                     pattern="[a-zA-Z0-9]{3,50}"
                     title="Username should be 3-50 characters and contain only letters and numbers">
               </span>
               <!-- Password input field -->
               <span id="pswd">
                  <input type="password" placeholder="Password" class="inputlogin" id="password" name="passID"
                     onkeypress="checkCaps(event)" autocomplete="new-password" onfocus="clearText(this)"
                     onblur="clearText(this)"
                     required
                     minlength="8"
                     pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                     title="Password must be at least 8 characters long and include uppercase, lowercase, and numbers">
                  <input type="hidden" name="output">
               </span>
               <span id="cid">
                  <input type="text" placeholder="Company ID" class="inputlogin" id="companyID" name="companyID"
                     onkeypress="checkCaps(event)" autocomplete="off" onfocus="clearText(this)"
                     onblur="clearText(this)"
                     required
                     pattern="[a-zA-Z0-9]{3,50}"
                     title="Company ID should be 3-50 characters and contain only letters and numbers">
               </span>
               <!-- Login button -->
               <span class="loginbox_container">
                  <input type="submit" value="Login" class="buttonlogin">
               </span>
            </div>
            <!-- Error message container -->
            <span class="errormsglogin" style="color: red"></span>
            <!-- Forgot password link -->
            <span style="float:left">
               <a href="web_forgotpassword.php" class="anchorlogin">Forgot your password ?</a>
            </span>
            <!-- User login link -->
            <span style="float:right">
               <a href="customer_login.php" class="anchorlogin">User Login</a>
            </span>
            <div class="remember-me">
               <input type="checkbox" id="remember_me" name="remember_me" value="1" <?php echo $selremember; ?>>
               <label for="remember_me">Remember me</label>
            </div>
         </form>
      </div>
   </div>
   <!-- Footer -->
   <div class="footerlogin">
      <p>Copyright © <?php echo date('Y'); ?> - <?php echo date('Y', strtotime("+1 year")); ?> Alliance Infotech Pvt Ltd.All Rights
         Reserved
      </p>
   </div>
   <!-- JavaScript libraries -->
   <script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/jquery-1.10.2.min.js"></script>
   <script language='javascript' src='public/js/md5.js'></script>
   <script language='javascript' src='public/js/sha1.js'></script>
   <script language="javascript" src="public/js/login.js"></script>
   <script>
   // Add client-side validation
   document.getElementById('formlogin').addEventListener('submit', function(e) {
       var loginID = document.getElementById('loginID').value;
       var password = document.getElementById('password').value;
       var companyID = document.getElementById('companyID').value;
       var errorMsg = document.querySelector('.errormsglogin');
       
       // Clear previous error
       errorMsg.textContent = '';
       
       // Validate username
       if (!/^[a-zA-Z0-9]{3,50}$/.test(loginID)) {
           e.preventDefault();
           errorMsg.textContent = 'Username should be 3-50 characters and contain only letters and numbers';
           return false;
       }
       
       // Validate password
       if (!/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/.test(password)) {
           e.preventDefault();
           errorMsg.textContent = 'Password must be at least 8 characters long and include uppercase, lowercase, and numbers';
           return false;
       }
       
       // Validate company ID
       if (!/^[a-zA-Z0-9]{3,50}$/.test(companyID)) {
           e.preventDefault();
           errorMsg.textContent = 'Company ID should be 3-50 characters and contain only letters and numbers';
           return false;
       }
       
       // Add delay to prevent brute force
       setTimeout(function() {
           document.getElementById('formlogin').submit();
       }, 1000);
       
       return true;
   });
   </script>
</body>

</html>