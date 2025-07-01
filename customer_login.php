<?php 
/**
 * Author: Ritu Modi
 * Date: 04-04-2024
 Desc: The file contains the code of customer_login page
 **/
// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

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

function validateMobile($mobile) {
    return preg_match('/^[0-9]{10}$/', $mobile);
}

function validateCompanyId($companyId) {
    return preg_match('/^[a-zA-Z0-9]+$/', $companyId);
}

function validateOTP($otp) {
    return preg_match('/^[0-9]{6}$/', $otp);
}

// Include necessary files
include("config/web_mysqlconnect.php");

// Function to get current time
function current_date() {
    return date('H:i:s');
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    if (!validateCSRFToken()) {
        die('Invalid request');
    }
    
    if ($_POST['action'] === 'login_check') {
        // Validate inputs
        $loginID = validateInput($_POST['loginID'] ?? '');
        $companyID = validateInput($_POST['companyID'] ?? '');
        
        if (!validateMobile($loginID) || !validateCompanyId($companyID)) {
            logSecurityEvent('customer_login_validation_failed', "Invalid mobile or company ID format");
            die('Invalid input format');
        }
        
        // Rest of login processing code...
    } elseif ($_POST['action'] === 'otp_check') {
        // Validate inputs
        $otp = validateInput($_POST['emp_otp'] ?? '');
        $companyID = validateInput($_POST['companyID'] ?? '');
        
        if (!validateOTP($otp) || !validateCompanyId($companyID)) {
            logSecurityEvent('otp_validation_failed', "Invalid OTP or company ID format");
            die('Invalid input format');
        }
        
        // Rest of OTP verification code...
    }
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <meta name="format-detection" content="telephone=no">
    <!-- Include Font Awesome CSS -->
    <!-- Include Bootstrap CSS -->
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Include Custom CSS -->
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/customer_css.css" rel="stylesheet" type="text/css">
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/customerlogin.css" rel="stylesheet" type="text/css">
</head>
<body>
<header class="text-center">
    <div class="primary_header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">                
                    <!-- Logo -->
                    <img id="main_logo_login" src="public/images/ensembler-logo.png" style="height:90px;background:#fff" alt="Ensembler Logo">           
                </div>
            </div>
        </div>
    </div>
</header>
<div>
    <div class="content">
        <div id="main_cont" class="container" style="min-height:450px;">
            <div class="row">
                <div class="col-xs-12">
                    <!-- Breadcrumb -->
                    <h4><i class="fa fa-home"></i> <a href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>customer_login.php">Home</a> / <a href="https://alliance-infotech.com/">Close</a></h1>
                </div>
                <div class="col-xs-12">
                    <div class="post_order_wrap inner_page">    
                        <div class="post_order_info">
                            <!-- Title Heading -->
                            <span class="title">
                                <h3 class="title_heading">Track and Report complaint</h3>
                            </span>
                            <!-- Login Form -->
                            <form id="formlogin" name="frmlogin" method="post" style="display: none" autocomplete="off">                                   
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="row">
                                    <div class="contact_input col-lg-6 col-xs-12">
                                        <!-- Mobile Number Input -->
                                        <input type="tel" 
                                               class="form-control" 
                                               placeholder="Enter Register Mobile Number" 
                                               name="loginID" 
                                               id="loginID" 
                                               required
                                               pattern="[0-9]{10}"
                                               title="Please enter a valid 10-digit mobile number"
                                               maxlength="10">
                                        <!-- Error Message for Mobile Number -->
                                        <span class="errormsglogin" style="color: red;text-align: center;"></span>
                                    </div>
                                    <!-- added company id [vastvikta][04-03-2025] -->
                                    <div class="contact_input col-lg-6 col-xs-12">
                                        <!-- Mobile Number Input -->
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Enter Company ID" 
                                               name="companyID" 
                                               id="companyID" 
                                               required
                                               pattern="[a-zA-Z0-9]+"
                                               title="Only letters and numbers allowed"
                                               maxlength="50">
                                        <!-- Error Message for Mobile Number -->
                                        <span class="errormsglogin" style="color: red;text-align: center;"></span>
                                    </div>
                                </div>                                 
                                <div class="contact_input col-xs-12">   
                                    <!-- Submit Button -->
                                    <button type="submit" class=" btn bg-dark-blue button submit_btn buttonlogin" id="Login-button" name="Login">Track</button>
                                </div>
                                <!-- Hidden Field for Action -->
                                <input type="hidden" name="action" value="login_check">  
                            </form>
                            <!-- OTP Form -->
                            <div class="login-container otp_form" style="display: none; margin-right: 55px;">
                                <form id="formlogin_otp" name="formlogin_otp" method="post" autocomplete="off">                                   
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="row">
                                        <div class="contact_input col-lg-6 col-xs-12">
                                            <!-- OTP Input -->
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="OTP" 
                                                   name="emp_otp" 
                                                   id="emp_otp" 
                                                   required
                                                   pattern="[0-9]{6}"
                                                   title="Please enter a valid 6-digit OTP"
                                                   maxlength="6">
                                            <!-- Error Message for OTP -->
                                            <span class="error_label" style="color: red;text-align: center;"></span>
                                        </div>
                                         <!-- added company id [vastvikta][04-03-2025] -->
                                        <div class="contact_input col-lg-6 col-xs-12">
                                            <!-- Mobile Number Input -->
                                            <input type="text" 
                                                   class="form-control" 
                                                   placeholder="Enter Company ID" 
                                                   name="companyID" 
                                                   id="companyID" 
                                                   required
                                                   pattern="[a-zA-Z0-9]+"
                                                   title="Only letters and numbers allowed"
                                                   maxlength="50">
                                            <!-- Error Message for Mobile Number -->
                                            <span class="errormsglogin" style="color: red;text-align: center;"></span>
                                        </div>
                                    </div>
                                    <!-- Resend OTP Link -->
                                    <div class="d-flex justify-content-center align-items-center"><span>Didn't get the code </span> 
                                        <a href="#" class="text-decoration-none ms-3" id="create_otp"> Resend </a> 
                                    </div>                                 
                                    <div class="contact_input col-xs-12">   
                                        <!-- Submit Button -->
                                        <button type="submit" class=" btn bg-dark-blue button submit_btn buttonlogin" id="Login-button" name="otp_submit">Submit</button>
                                    </div>
                                    <!-- Hidden Field for Mobile Number -->
                                    <input type="hidden" name="vh_mobile" value="" id="vh_mobile">
                                    <!-- Hidden Field for Action -->
                                    <input type="hidden" name="action" value="otp_check">  
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="padding-bottom: 10px;padding-right: 34px;padding-left: 36px;">
                    <!-- Links to New Complaints Form and Ticket Tracking -->
                    <a href="complain_form.php" target="_blank" class="btn" id="report_page" name="Login" style="float: left;">New Complaints or queries</a>
                    <a href="javascript:void(0);" class="btn" id="report_page_ticket" name="Login" style="float: right;">Track status of your ticket</a>
                </div>
            </div>
        </div>
    </div>
</div>   
<div class="footerlogin">
    <!-- Footer -->
    <strong><p>Copyright © <?php echo date('Y'); ?> - <?php echo date('Y', strtotime("+1 year")); ?> Alliance Infotech Pvt Ltd. All Rights Reserved</p></strong>
</div>
</body>
<!-- Include jQuery -->
<script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/jquery.min.js"></script>
<!-- Include Custom JavaScript -->
<script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/customerlogin.js"></script>

<script>
// Add client-side validation
document.getElementById('formlogin').addEventListener('submit', function(e) {
    var mobile = document.getElementById('loginID').value;
    var companyID = document.getElementById('companyID').value;
    
    if (!/^[0-9]{10}$/.test(mobile)) {
        e.preventDefault();
        document.querySelector('.errormsglogin').textContent = 'Please enter a valid 10-digit mobile number';
        return false;
    }
    
    if (!/^[a-zA-Z0-9]+$/.test(companyID)) {
        e.preventDefault();
        document.querySelector('.errormsglogin').textContent = 'Company ID should only contain letters and numbers';
        return false;
    }
});

document.getElementById('formlogin_otp').addEventListener('submit', function(e) {
    var otp = document.getElementById('emp_otp').value;
    var companyID = document.getElementById('companyID').value;
    
    if (!/^[0-9]{6}$/.test(otp)) {
        e.preventDefault();
        document.querySelector('.error_label').textContent = 'Please enter a valid 6-digit OTP';
        return false;
    }
    
    if (!/^[a-zA-Z0-9]+$/.test(companyID)) {
        e.preventDefault();
        document.querySelector('.errormsglogin').textContent = 'Company ID should only contain letters and numbers';
        return false;
    }
});
</script>
</html>
