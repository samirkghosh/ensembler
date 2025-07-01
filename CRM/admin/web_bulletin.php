<?php 
/**
 * Bulletin edit Page
 * Author: Ritu modi
 * Date: 13-03-2024
 * This page is used for managing bulletins. It allows users, particularly administrators, to create new bulletins or edit existing ones.
 */
// updated code for datetimepicker [vastvikta][11-06-2025]
// Encode 'Edit_Bulletin' to base64
$Edit_Bulletin = base64_encode('Edit_Bulletin'); 
// Decode 'id' from the GET parameter
$id = base64_decode($_GET['id']);

// Include common functions
include_once("common_function.php");

// Initialize userId
$userId = '';

// Check if user is admin
if($_SESSION['user_group'] == '0000' && $_SESSION['logged'] == 'Admin'){
    // Set userId if user is admin
    $userId = $_SESSION['userid'];
}

// Initialize data variable
$data = '';

// Check if id is set
if($id){
    // Get bulletin record
    $Bulletin = new common_function;
    $data = $Bulletin->get_bulletin_record($id);
}

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
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

// CSRF validation function
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

// Validate CSRF token for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken()) {
        die('CSRF token validation failed');
    }
}

// Fix SQL injection in get_bulletins function
function get_bulletins() {
    global $db, $link;
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_bulletin WHERE iStatus = 1 ORDER BY dCreatedOn DESC");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        $bulletins = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $bulletins[] = array(
                'id' => $row['I_BulletinID'],
                'title' => htmlspecialchars($row['vTitle']),
                'content' => htmlspecialchars($row['vContent']),
                'created_on' => $row['dCreatedOn'],
                'created_by' => htmlspecialchars($row['vCreatedBy'])
            );
        }
        return $bulletins;
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for bulletin retrieval');
        return array();
    }
}

// Fix SQL injection in get_bulletin_by_id function
function get_bulletin_by_id($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_bulletin WHERE I_BulletinID = ? AND iStatus = 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return array(
                'id' => $row['I_BulletinID'],
                'title' => htmlspecialchars($row['vTitle']),
                'content' => htmlspecialchars($row['vContent']),
                'created_on' => $row['dCreatedOn'],
                'created_by' => htmlspecialchars($row['vCreatedBy']),
                'status' => $row['iStatus']
            );
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for bulletin retrieval by ID');
    }
    return null;
}

// Fix SQL injection in insert_bulletin function
function insert_bulletin() {
    global $db, $link;
    
    $title = validateInput($_POST['title']);
    $content = validateInput($_POST['content']);
    $created_by = validateInput($_SESSION['username']);
    
    $stmt = mysqli_prepare($link, "INSERT INTO $db.web_bulletin (vTitle, vContent, vCreatedBy, dCreatedOn, iStatus) VALUES (?, ?, ?, NOW(), 1)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $title, $content, $created_by);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('bulletin_added', "Bulletin added: $title");
            echo "Bulletin Added Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to add bulletin');
            echo "Failed to Add Bulletin!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for bulletin insertion');
        echo "Failed to Add Bulletin!";
    }
}

// Fix SQL injection in update_bulletin function
function update_bulletin() {
    global $db, $link;
    
    $id = validateNumeric($_POST['id']);
    $title = validateInput($_POST['title']);
    $content = validateInput($_POST['content']);
    $status = validateNumeric($_POST['status']);
    
    $stmt = mysqli_prepare($link, "UPDATE $db.web_bulletin SET vTitle = ?, vContent = ?, iStatus = ? WHERE I_BulletinID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $status, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('bulletin_updated', "Bulletin updated: ID $id");
            echo "Bulletin Updated Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to update bulletin');
            echo "Failed to Update Bulletin!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for bulletin update');
        echo "Failed to Update Bulletin!";
    }
}

// Fix SQL injection in delete_bulletin function
function delete_bulletin() {
    global $db, $link;
    
    $id = validateNumeric($_POST['id']);
    
    $stmt = mysqli_prepare($link, "UPDATE $db.web_bulletin SET iStatus = 0 WHERE I_BulletinID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('bulletin_deleted', "Bulletin deleted: ID $id");
            echo "Bulletin Deleted Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to delete bulletin');
            echo "Failed to Delete Bulletin!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for bulletin deletion');
        echo "Failed to Delete Bulletin!";
    }
}
?>
<style>
/* Hide other calendar libraries */
.ui-datepicker,
.ui-timepicker-div,
.flatpickr-calendar,
.datepicker-dropdown {
    display: none !important;
}

/* DO NOT block pointer-events */
</style>


<!-- Bulletin Form -->
<form name="registration" method="post" id="registration">
    <!-- Hidden inputs for userId and bulletin_id -->
    <input type="hidden" name="userid" value="<?php echo $userId;?>" id="userid">
    <input type="hidden" name="bulletin_id" class="bulletin_id" value="<?php echo $data['id'];?>" id="<?php $data['id']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <!-- Bulletin Header -->
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px"><?= ($data['id'] != '') ? "Edit" : "New" ?> Bulletin </span>

    <!-- Bulletin Form Content -->
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form" width="100%">
                <tbody>
                    <!-- Message Input -->
                    <tr>
                        <td><label>Message<em>*</em></label></td>
                        <td  class="left boder0-right">
                            <div class="log-case">
                                <textarea name="M_Description" id="M_Description" cols="" rows="4" class="text-area1"><?php if(isset($data['id'])){ echo $data['Message']; }?></textarea>
                            </div>
                        </td>
                    </tr>

                    <?php
$startdate = !empty($data['d_startDate']) 
    ? date("d-m-Y H:i:s", strtotime($data['d_startDate'])) 
    : date("d-m-Y H:i:s");

$enddate = !empty($data['d_endDate']) 
    ? date("d-m-Y H:i:s", strtotime($data['d_endDate'])) 
    : date("d-m-Y H:i:s");
?>

<tr>
    <td><label>Start Date<em>*</em></label></td>
    <td class="left boder0-right">
        <div class="log-case">
            <input type="text" id="startdatetime" name="startdatetime"
                   class="date_class dob1" value="<?= $startdate ?>" autocomplete="off" />
        </div>
    </td>
</tr>

<tr>
    <td><label>End Date<em>*</em></label></td>
    <td class="left boder0-right">
        <div class="log-case">
            <input type="text" id="enddatetime" name="enddatetime"
                   class="date_class dob1" value="<?= $enddate ?>" autocomplete="off" />
        </div>
    </td>
</tr>

                    <!-- Message Type Select -->
                    <tr>
                        <td><label>Message Type<em>*</em></label></td>
                        <td class="left boder0-right">
                            <div class="log-case">
                                <select name="msg_type" class="select-styl1" id="msg_type">
                                    <option value=''>Select Type</option>
                                    <option value='0' <?php if(isset($data['id']) && $data['i_msgType'] == '0'){ echo 'selected'; }?>>Normal</option>
                                    <option value='1' <?php if(isset($data['id']) && $data['i_msgType'] == '1'){ echo 'selected'; }?>>Important</option>
                                </select>
                            </div>
                        </td>
                    </tr>

                    <!-- Submit Button -->
                    <tr>
                        <td class="left boder0-right" colspan="4">
                            <center> 
                                <?php if ($data['id'] != '') { ?>
                                    <!-- Update Button -->
                                    <input name="Update_bulletin" type="submit" value="Update" class="button-orange1 Update_bulletin" style="float:inherit">
                                <?php } else { ?>
                                    <!-- Create Button -->
                                    <input name="addbtn" type="submit" value="Create" class="button-orange1 Update_bulletin" style="float:inherit">
                                <?php } ?>
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Include common JavaScript file -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="<?=$SiteURL?>public/js/common.js"></script>

<script>
$(document).ready(function () {
    $('#startdatetime, #enddatetime').datetimepicker({
        format: 'd-m-Y H:i:s',
        step: 1,
        timepicker: true,
        datepicker: true,
        scrollMonth: false,
        scrollInput: false
    });
});
</script>
