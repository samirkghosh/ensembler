<?php
// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

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

function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]+$/', $username);
}

function validateCompanyId($companyId) {
    return preg_match('/^[a-zA-Z0-9]+$/', $companyId);
}

// Include database connection file
include("config/web_mysqlconnect.php");

// Check if login username cookie is set and assign it to $loginID variable
if (!empty($_COOKIE['loginusername'])) {
    $loginID = filter_var($_COOKIE['loginusername'], FILTER_SANITIZE_STRING);
}

// Check if remember me cookie is set and assign appropriate value to $selremember variable
if (!empty($_COOKIE['logremember_me']) && $_COOKIE['logremember_me'] === '1') {
    $selremember = "checked";
} else {
    $selremember = "";
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
    <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/<?php echo htmlspecialchars($dbtheme, ENT_QUOTES, 'UTF-8'); ?>.css">
</head>
<body style='margin: 0;background-image:url("public/images/<?php echo htmlspecialchars($dblandinglogo, ENT_QUOTES, 'UTF-8'); ?>");background-size: cover;'>
    <div class="topnavlogin">
        <img id="main_logo_login" src="public/images/<?php echo htmlspecialchars($dbheadlogo, ENT_QUOTES, 'UTF-8'); ?>" style="height:62px" alt="Ensembler Logo">
        <div class="login-container" style="margin-top:-26px">
            <!-- Login form -->
            <form id="formlogin" name="frmlogin" method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <!-- Hidden input field for login action -->
                    <input type="hidden" name="action" value="Login_Flow" />
                    <span id="usrn"> 
                        <!-- Username input field -->
                        <input type="text" 
                               placeholder="User Name" 
                               class="inputlogin" 
                               name="loginID" 
                               id="loginID" 
                               onfocus="clearText(this)" 
                               onblur="clearText(this)"
                               required
                               pattern="[a-zA-Z0-9_]+"
                               title="Username can only contain letters, numbers, and underscores"
                               maxlength="50">
                    </span>
                    <!-- Password input field -->
                    <span id="pswd">
                        <input type="password" 
                               placeholder="Password" 
                               class="inputlogin" 
                               id="password" 
                               name="passID" 
                               onkeypress="checkCaps(event)" 
                               autocomplete="off" 
                               onfocus="clearText(this)" 
                               onblur="clearText(this)"
                               required
                               minlength="8"
                               maxlength="128">
                        <input type="hidden" name="output">
                    </span>
                    <!-- Company ID input field -->
                    <span id="cid">
                        <input type="text" 
                               placeholder="Company ID" 
                               class="inputlogin" 
                               id="companyID" 
                               name="companyID" 
                               onkeypress="checkCaps(event)" 
                               autocomplete="off" 
                               onfocus="clearText(this)" 
                               onblur="clearText(this)"
                               required
                               pattern="[a-zA-Z0-9]+"
                               title="Company ID can only contain letters and numbers"
                               maxlength="50">
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
            </form>
        </div>
    </div>
    <!-- Footer -->
    <div class="footerlogin">
        <p>Copyright © <?php echo date('Y'); ?> - <?php echo date('Y', strtotime("+1 year")); ?> Alliance Infotech Pvt Ltd. All Rights Reserved</p>
    </div>
    <!-- JavaScript libraries -->
    <script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="public/js/md5.js"></script>
    <script type="text/javascript" src="public/js/sha1.js"></script>
    <script type="text/javascript" src="public/js/login.js"></script>
</body>
</html>