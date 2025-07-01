<?php 
/***
 * Change password Layout
 * Author: Ritu
 * Date: 04-04-2024
 * This file contains all the code for Change password.
 * (If a user change his password then he can recover it by clicking on forgot password and all the code for the same is given in this file.)
 * 
 **/

// Add security headers
header("Content-Security-Policy: default-src 'self' https://code.jquery.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com; style-src 'self' 'unsafe-inline';");
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
function checkRateLimit($userid) {
    $rateLimitFile = sys_get_temp_dir() . '/rate_limit_pass_' . md5($userid) . '.txt';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}

// Input validation functions
function validateUserId($userid) {
    return filter_var($userid, FILTER_VALIDATE_INT);
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate and sanitize inputs
$userid = isset($_GET['userid']) ? validateUserId($_GET['userid']) : null;
if (!$userid) {
    die('Invalid user ID');
}

// Check rate limiting
if (!checkRateLimit($userid)) {
    die('Too many password change attempts. Please try again later.');
}

$value = isset($_GET['value']) ? sanitizeInput($_GET['value']) : '';
$comp = isset($_GET['comp']) ? sanitizeInput($_GET['comp']) : '';
if (!empty($_SESSION['companyid'])) {
    $comp = $_SESSION['companyid'];
}
$c = isset($_GET['c']) ? sanitizeInput($_GET['c']) : '';

// Password handling
$oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';

require_once("config/web_mysqlconnect.php");// for database connection

$compnayid = $_SESSION['companyid'];

// Add password validation function
function validatePassword($password) {
    // Minimum 8 characters, maximum 16 characters
    if (strlen($password) < 8 || strlen($password) > 16) {
        return false;
    }
    
    // At least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // At least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // At least one number
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    // At least one special character
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return false;
    }
    
    // No common patterns
    $commonPatterns = [
        'password', '123456', 'qwerty', 'admin', 'welcome',
        'letmein', 'monkey', 'dragon', 'baseball', 'football'
    ];
    
    foreach ($commonPatterns as $pattern) {
        if (stripos($password, $pattern) !== false) {
            return false;
        }
    }
    
    return true;
}

// Function to log password change attempts
function logPasswordChangeAttempt($link, $db, $userid, $success, $reason = '') {
    $timep = date("Y-m-d H:i:s");
    $ip = getClientIP();
    $stmt = mysqli_prepare($link, "INSERT INTO $db.password_change_logs (user_id, success, reason, ip_address, attempt_time) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iisss", $userid, $success, $reason, $ip, $timep);
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
?>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/styles.css"/>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script language="javascript" type="text/javascript">
        function callothers() {  
            $('.alert_text').text(''); 
            document.passwordfrm.npassword.value = hex_md5(sha1(hex_md5(document.passwordfrm.newpassword.value)));
            if(event.keyCode == 13) {
                frmsubmit(document.passwordfrm.newpassword.value, document.passwordfrm.confirmpassword.value, document.passwordfrm.oldpassword.value);
            } 
        }
        
        function frmsubmitNew() {
            $('.alert_text').text(''); 
            var pswlen = document.passwordfrm.newpassword.value;
            var confirmp = document.passwordfrm.confirmpassword.value;
            var oldp = document.passwordfrm.password.value;
            var opassword = document.passwordfrm.opassword.value;
            
            if (!validatePasswordStrength(pswlen)) {
                $('.alert_text').text('Password does not meet the requirements');
                return;
            }
            
            if (pswlen !== confirmp) {
                $('.alert_text').text('Passwords do not match');
                return;
            }
            
            if (opassword) {
                var opassword = hex_md5(sha1(hex_md5(passwordfrm.opassword.value)));
            }
            
            var userid = document.passwordfrm.userid.value;
            
            // Add delay to prevent brute force
            setTimeout(function() {
                $.ajax({
                    type: 'post',
                    url: 'CRM/web_function.php',
                    data: {
                        'userid': userid,
                        'password': pswlen,
                        'confirmpassword': confirmp,
                        'oldpassword': opassword,
                        'oldpassdb': oldp,
                        'action': 'PasswordValidation'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == false) {
                            $('.alert_text').text(response.message);
                        } else {
                            $('.alert_text').text(''); 
                            $.ajax({
                                url: 'function/web_function_change_password.php',
                                type: 'POST',
                                data: $('#passwordfrm').serialize(),
                                dataType: 'json',
                                success: function(response) {
                                    if(response.status == false) {
                                        $('.errormsglogin').text(response.message);
                                    } else {
                                        if($('.setting_password').val() == '1') {
                                            $('.change_password_div').html(response.html);
                                            setInterval("refreshParent()", 2000);
                                        } else {
                                            $('.errormsglogin').text(response.message);
                                            window.location.href = response.location;
                                        }
                                    }
                                },
                                error: function(error) {
                                    console.error('Error:', error);
                                    $('.alert_text').text('An error occurred. Please try again.');
                                }
                            });
                        }
                    }
                });
            }, 1000);
        }
        
        function refreshParent() {
            window.parent.location.reload();
        }
        
        function validatePasswordStrength(password) {
            // Minimum 8 characters, maximum 16 characters
            if (password.length < 8 || password.length > 16) {
                return false;
            }
            
            // At least one uppercase letter
            if (!/[A-Z]/.test(password)) {
                return false;
            }
            
            // At least one lowercase letter
            if (!/[a-z]/.test(password)) {
                return false;
            }
            
            // At least one number
            if (!/[0-9]/.test(password)) {
                return false;
            }
            
            // At least one special character
            if (!/[!@#$%^&*()\-_=+{};:,<.>]/.test(password)) {
                return false;
            }
            
            // Check for common patterns
            var commonPatterns = [
                'password', '123456', 'qwerty', 'admin', 'welcome',
                'letmein', 'monkey', 'dragon', 'baseball', 'football'
            ];
            
            for (var i = 0; i < commonPatterns.length; i++) {
                if (password.toLowerCase().indexOf(commonPatterns[i]) !== -1) {
                    return false;
                }
            }
            
            return true;
        }
    </script>
    <script language="javascript" src='<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/md5.js' type="text/javascript"></script>
    <script language="javascript" src='<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/sha1.js' type="text/javascript"></script>
    <style>
        label { width: 188px!important; }
        .addinteraction-popup ul li em { 
            font-style: normal; 
            font-weight: bold; 
            float: none!important; 
            padding: 0; 
            width: auto!important; 
            text-align: left!important; 
        }
        .alert_text {
            font-size: 12px;
            color: red;
            text-align: center;
            margin: 28px;
        }
    </style>
</head>
<body onload="document.passwordfrm.opassword.focus();">
    <div class="change_password_div">
        <form method="post" name="passwordfrm" id="passwordfrm">
            <div class="addinteraction-popup">
                <span class="breadcrumb_head" style="height:17px;padding:9px 16px">Change Password</span>
                <div id="msgview">
                    <?php
                    $res = get_password($userid);
                    while($row = mysqli_fetch_array($res)) {
                        $password = $row['V_Password'];
                    }
                    ?>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="change_password_flow" />
                    <input type="hidden" name="uname" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($password ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                    <input type="hidden" name="oldpassword" />
                    <span class="alert_text"></span>
                    <ul style="padding: 14px;color: crimson;font-style: italic;">
                        <li><em>*</em> Password must be 8-16 characters long</li>
                        <li><em>*</em> Must contain at least one uppercase letter</li>
                        <li><em>*</em> Must contain at least one lowercase letter</li>
                        <li><em>*</em> Must contain at least one number</li>
                        <li><em>*</em> Must contain at least one special character</li>
                        <li><em>*</em> Cannot contain common words or patterns</li>
                    </ul>
                    <ul style="padding:14px;">
                        <li>
                            <label>Please Enter Your Old Password <em>*</em></label>
                            <input type="password" name="opassword" value="" autocomplete="off" size="30" 
                                   onblur='passwordfrm.oldpassword.value=hex_md5(sha1(hex_md5(passwordfrm.opassword.value)));' 
                                   class="input-style1" required />
                        </li>
                        <li>
                            <label>Enter New Password <em>*</em></label>
                            <input type="password" name="newpassword" id="newpassword" autocomplete="off" 
                                   class="input-style1" size="30" required 
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,16}"
                                   title="Password must be 8-16 characters long and include uppercase, lowercase, number, and special character" />
                        </li>
                        <li>
                            <label>Confirm Password <em>*</em></label>
                            <input type="password" name="confirmpassword" value="" autocomplete="off" 
                                   class="input-style1" size="30" required
                                   onblur='passwordfrm.npassword.value=hex_md5(sha1(hex_md5(passwordfrm.newpassword.value)));' 
                                   onkeypress="callothers();" />
                            <input type="hidden" name="npassword" id="npassword" />
                        </li>
                    </ul>
                </div>
                <?php if(($_SESSION['user_group']=='080000') || ($_SESSION['user_group']=='0000')) { ?>
                <div class="button-all" style="padding:11px 0px 0px 211px">
                <?php } else { ?>
                <div class="button-all">
                    <center>
                <?php } ?>
                    <input name="abhipass" type="button" value="Update Password" class="button-orange1" onclick='frmsubmitNew();'>
                    <?php if(($_SESSION['user_group']=='080000') || ($_SESSION['user_group']=='0000')) { ?>
                    <?php } ?>
                    <input type="hidden" name="userid" value='<?php echo htmlspecialchars($userid, ENT_QUOTES, 'UTF-8'); ?>' />
                    <input type="hidden" name="comp" value='<?php echo htmlspecialchars($comp, ENT_QUOTES, 'UTF-8'); ?>' />
                    <input type="hidden" name="value" value='<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>' class="setting_password"/>
                    </center>
                </div>
            </div>
        </form>
    </div>
</body>
<?php
function get_password($userid) {
    global $db, $link;
    $stmt = mysqli_prepare($link, "SELECT V_Password FROM $db.tbl_mst_user_company WHERE I_UserID = ?");
    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
?>
