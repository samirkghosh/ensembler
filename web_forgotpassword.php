<?php 
/***
 * forgot password Layout
 * Author: Ritu
 * Date: 01-04-2024
 * This file contains all the code for forgotten password.
 * (If a user forgot his password then he can recover it by clicking on forgot password and all the code for the same is given in this file.)
 * 
 **/

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

// Rate limiting
function checkRateLimit($ip) {
    $rateLimitFile = sys_get_temp_dir() . '/rate_limit_forgot_' . md5($ip) . '.txt';
    $maxAttempts = 3;
    $timeWindow = 3600; // 1 hour
    
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

// Input validation functions
function validateInput($input) {
    if (is_array($input)) {
        return array_map('validateInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && 
           preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
}

function validateCompanyId($companyId) {
    return preg_match('/^[0-9]{3,20}$/', $companyId);
}

// Get client IP securely
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

// Check rate limiting
$currentIP = getClientIP();
if (!checkRateLimit($currentIP)) {
    die('Too many password reset attempts. Please try again later.');
}

require_once("config/web_mysqlconnect.php"); // for database connection 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>Welcome to Eviop site | Forgot Password</title>
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/facebook.css">
</head>
<body style='margin: 0;background-image: url("public/images/test_land.png");background-size: cover;'>
    <div class="topnavlogin">
        <img id="main_logo_login" src="public/images/alliance_logo.png" style="height:62px" alt="Alliance Logo">
        <div class="login-container" style="margin-top:-26px">
            <form id="formlogin" name="frmlogin" method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" id="hidden_email" name="hidden_email">
                <input type="hidden" id="action" name="action" value="forgot_password">
                <div id="usrn">
                    <input type="email" 
                           name="email" 
                           id="email" 
                           size="20"  
                           class="inputlogin" 
                           placeholder="Enter Email ID"
                           required
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                           title="Please enter a valid email address"
                           maxlength="100">
                    <span id="cid">
                        <input type="text" 
                               placeholder="Company ID" 
                               class="inputlogin" 
                               id="companyID" 
                               name="companyID"
                               required
                               pattern="[0-9]{3,20}"
                               title="Company ID must be 3-20 digits"
                               maxlength="20"
                               minlength="3">
                    </span>
                    <input type="button" 
                           value="Submit" 
                           class="buttonlogin" 
                           id="Login-button" 
                           name="Submit" 
                           onClick="frmsubmit();">
                    <span id="errortag" class="errortag" style="font-size:11px;display:none;"></span>
                    <div>
                        <a href="web_login.php" class="anchorlogin">&nbsp;Back to login page</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="footerlogin">
        <p>Copyright © <?php echo date('Y'); ?> - <?php echo date('Y', strtotime("+1 year")); ?> Alliance Infotech Pvt Ltd. All Rights Reserved</p>
    </div>

    <script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/jquery-1.10.2.min.js"></script>
    <script>
    function frmsubmit() {
        $('.errortag').text('');
        var emailExp = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var mail = document.frmlogin.email.value.match(emailExp);
        var companyIDField = document.frmlogin.companyID;
        var companyID = companyIDField.value.trim();
        
        // Validate company ID
        if (companyID === '') {
            alert("Please enter Company ID");
            companyIDField.focus();
            return false;
        }
        
        if (!/^[0-9]{3,20}$/.test(companyID)) {
            alert("Company ID must be 3-20 digits!");
            companyIDField.focus();
            return false;
        }
        
        // Validate email
        if (document.frmlogin.email.value == '') {
            alert("Please enter your email id");
            document.frmlogin.email.focus();
            return false;
        }
        
        if (!mail) {
            alert("Please enter a valid email address!");
            document.frmlogin.email.focus();
            return false;
        }

        // Add delay to prevent brute force
        setTimeout(function() {
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                url: "function/web_function_forgot_password.php",
                data: $("#formlogin").serialize(),
                success: function(response) {
                    $('.errortag').show();
                    $('.errortag').text(response);
                    setTimeout(function(){
                        $('.errortag').hide();
                        $('.errortag').text('');
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('.errortag').show();
                    $('.errortag').text('An error occurred. Please try again.');
                    setTimeout(function(){
                        $('.errortag').hide();
                        $('.errortag').text('');
                    }, 2000);
                }
            });
        }, 1000);
    }
    </script>
</body>
</html>