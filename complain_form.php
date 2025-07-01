<?php
/**
 * Author: Ritu Modi
 * Date: 04-04-2024
 The file contains the code of complain_form page
 **/
// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");

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

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateMobile($mobile) {
    return preg_match('/^[0-9]{10}$/', $mobile);
}

function validateCompanyId($companyId) {
    return preg_match('/^[a-zA-Z0-9]+$/', $companyId);
}

// Include necessary files
include("config/web_mysqlconnect.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Stylesheets -->
    <!-- Include Bootstrap CSS -->
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/customer_css.css" rel="stylesheet" type="text/css">
    <link href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/css/customerlogin.css" rel="stylesheet" type="text/css">
    <!-- Title -->
    <title>Registration Form</title>
</head>
<body>
    <!-- Navigation -->
    <header class="text-center">
        <div class="primary_headerr">
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
    <!-- Page Content -->
    <div class="content">
        <div id="main_cont" class="container" style="min-height:450px;">
            <div class="row">
                <!-- Breadcrumb -->
                <div class="col-xs-12">
                    <h4><i class="fa fa-home"></i> <a href="https://alliance-infotech.com/">Home</a> / <a href="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>customer_login.php">Close</a></h4>
                </div>
                <div class="col-xs-12">
                    <div class="post_order_wrap inner_page">    
                        <div class="post_order_info">
                            <!-- Registration Form -->
                            <h5 class="text-center mb-4"><strong>Registration Form</strong></h5>
                            <div class="text-center ticket"></div>
                            <form method="post" class="form-card" id="formsubmit" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="row mb-2">                     
                                    <div class="col-sm-6">
                                        <!-- Full Name Input -->
                                        <label for="full_name" class="col-sm-12 col-form-label">Full Name <font color="red">*</font></label>
                                        <input type="text" class="form-control" name="full_name" id="full_name" required 
                                               pattern="[a-zA-Z\s]+" title="Only letters and spaces allowed"
                                               maxlength="100">
                                        <span class="error_label_name"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <!-- Mobile Input -->
                                        <label for="mobile" class="col-sm-12 col-form-label">Mobile <font color="red">*</font></label>
                                        <input type="tel" class="form-control" name="mobile" id="mobile" required
                                               pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number"
                                               maxlength="10">
                                        <span class="error_label"></span>
                                    </div>  
                                    <!-- Email Input -->
                                    <div class="col-sm-6">
                                        <label for="email" class="col-sm-12 col-form-label">Email <font color="red">*</font></label>
                                        <input type="email" class="form-control" name="email" id="email" required
                                               maxlength="100">
                                        <span class="error_label_email"></span>
                                    </div>
                                    <!-- added company id [vastvikta][04-03-2025] -->
                                    <!-- company id  -->
                                    <div class="col-sm-6">
                                        <label for="companyID" class="col-sm-12 col-form-label">Company ID<font color="red">*</font></label>
                                        <input type="text" class="form-control" name="companyID" id="companyID" required
                                               pattern="[a-zA-Z0-9]+" title="Only letters and numbers allowed"
                                               maxlength="50">
                                    </div>
                                    <!-- TPIN Input -->
                                    <div class="col-sm-6">
                                        <label for="tpin" class="col-sm-12 col-form-label">TPIN</label>
                                        <input type="text" class="form-control" name="tpin" id="tpin"
                                               pattern="[a-zA-Z0-9]+" title="Only letters and numbers allowed"
                                               maxlength="50">
                                    </div>
                                    <!-- NRC Input -->
                                    <div class="col-sm-6">
                                        <label for="nrc" class="col-sm-12 col-form-label">NRC</label>
                                        <input type="text" class="form-control" name="nrc" id="nrc"
                                               pattern="[a-zA-Z0-9]+" title="Only letters and numbers allowed"
                                               maxlength="50">
                                    </div>
                                    <!-- Message Textarea -->
                                    <div class="col-sm-6">
                                        <label for="message" class="col-sm-12 col-form-label">Message</label>
                                        <textarea placeholder="Type message.." name="message" id="message" 
                                                  class="form-control" maxlength="1000"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- Hidden Fields for Action and Submit Button -->
                                        <input type="hidden" name="action" value="send_mail">
                                        <input type="submit" class="btn btn-primary" value="Submit" id="submit_btn">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="footerrlogin">
        <strong><p>Copyright &copy; <?php echo date('Y'); ?> - <?php echo date('Y', strtotime("+1 year")); ?> Alliance Infotech Pvt Ltd. All Rights Reserved</p></strong>
    </div>
<!-- Include jQuery -->
<script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/jquery.min.js"></script>
<!-- Include Custom JavaScript -->
<script type="text/javascript" src="<?php echo htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8'); ?>public/js/customerlogin.js"></script>

<script>
// Add client-side validation
document.getElementById('formsubmit').addEventListener('submit', function(e) {
    var fullName = document.getElementById('full_name').value;
    var mobile = document.getElementById('mobile').value;
    var email = document.getElementById('email').value;
    var companyID = document.getElementById('companyID').value;
    
    // Validate full name
    if (!/^[a-zA-Z\s]+$/.test(fullName)) {
        e.preventDefault();
        document.querySelector('.error_label_name').textContent = 'Name should only contain letters and spaces';
        return false;
    }
    
    // Validate mobile
    if (!/^[0-9]{10}$/.test(mobile)) {
        e.preventDefault();
        document.querySelector('.error_label').textContent = 'Please enter a valid 10-digit mobile number';
        return false;
    }
    
    // Validate email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        document.querySelector('.error_label_email').textContent = 'Please enter a valid email address';
        return false;
    }
    
    // Validate company ID
    if (!/^[a-zA-Z0-9]+$/.test(companyID)) {
        e.preventDefault();
        document.querySelector('.error_label').textContent = 'Company ID should only contain letters and numbers';
        return false;
    }
});
</script>

<?php
// Process complaint form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_mail') {
    // Validate CSRF token
    if (!validateCSRFToken()) {
        die('Invalid request');
    }
    
    // Validate inputs
    $fullName = validateInput($_POST['full_name'] ?? '');
    $mobile = validateInput($_POST['mobile'] ?? '');
    $email = validateInput($_POST['email'] ?? '');
    $companyID = validateInput($_POST['companyID'] ?? '');
    $tpin = validateInput($_POST['tpin'] ?? '');
    $nrc = validateInput($_POST['nrc'] ?? '');
    $message = validateInput($_POST['message'] ?? '');
    
    // Validate required fields
    if (!preg_match('/^[a-zA-Z\s]+$/', $fullName) || 
        !validateMobile($mobile) || 
        !validateEmail($email) || 
        !validateCompanyId($companyID)) {
        logSecurityEvent('complaint_validation_failed', "Invalid input format in complaint form");
        die('Invalid input format');
    }
    
    // Rest of complaint processing code...
}
