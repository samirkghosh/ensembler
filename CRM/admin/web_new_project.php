<?php
/**
 * Auth: Aarti ojha
 * Modification by AI - 2024-07-22
 * Description: This page is used for adding or editing department details. It has been rewritten to include
 * comprehensive security measures like CSRF protection, prepared statements, and proper input/output handling.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$msg = '';
$id = isset($_GET['id']) ? base64_decode($_GET['id']) : '';

// The getProjectData function is in web_admin_function.php and is now secure
$projectData = getProjectData($link, $id, $db);
$vProjectName = $projectData['vProjectName'];
$category = $projectData['category'];

// The insert_or_update_project is in web_admin_function.php and handles the POST request securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This function now contains all the logic for insert/update, including security checks.
    // It will echo success or an error message.
    insert_or_update_project($link); 
    // We exit here because the function handles the full AJAX response.
    exit();
}

// Fix SQL injection in get_projects function
function get_projects() {
    global $db, $link;
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_project WHERE iStatus = 1 ORDER BY vProjectName");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        $projects = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $projects[] = array(
                'id' => $row['I_ProjectID'],
                'name' => htmlspecialchars($row['vProjectName']),
                'description' => htmlspecialchars($row['vDescription']),
                'start_date' => $row['dStartDate'],
                'end_date' => $row['dEndDate'],
                'status' => $row['iStatus']
            );
        }
        return $projects;
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for project retrieval');
        return array();
    }
}

// Fix SQL injection in get_project_by_id function
function get_project_by_id($id) {
    global $db, $link;
    
    $id = validateNumeric($id);
    
    $stmt = mysqli_prepare($link, "SELECT * FROM $db.web_project WHERE I_ProjectID = ? AND iStatus = 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return array(
                'id' => $row['I_ProjectID'],
                'name' => htmlspecialchars($row['vProjectName']),
                'description' => htmlspecialchars($row['vDescription']),
                'start_date' => $row['dStartDate'],
                'end_date' => $row['dEndDate'],
                'status' => $row['iStatus']
            );
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for project retrieval by ID');
    }
    return null;
}

// Fix SQL injection in insert_project function
function insert_project() {
    global $db, $link;
    
    $project_name = validateInput($_POST['project_name']);
    $description = validateInput($_POST['description']);
    $start_date = validateDate($_POST['start_date']);
    $end_date = validateDate($_POST['end_date']);
    
    $stmt = mysqli_prepare($link, "INSERT INTO $db.web_project (vProjectName, vDescription, dStartDate, dEndDate, iStatus) VALUES (?, ?, ?, ?, 1)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $project_name, $description, $start_date, $end_date);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('project_added', "Project added: $project_name");
            echo "Project Added Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to add project');
            echo "Failed to Add Project!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for project insertion');
        echo "Failed to Add Project!";
    }
}

// Fix SQL injection in update_project function
function update_project() {
    global $db, $link;
    
    $id = validateNumeric($_POST['id']);
    $project_name = validateInput($_POST['project_name']);
    $description = validateInput($_POST['description']);
    $start_date = validateDate($_POST['start_date']);
    $end_date = validateDate($_POST['end_date']);
    $status = validateNumeric($_POST['status']);
    
    $stmt = mysqli_prepare($link, "UPDATE $db.web_project SET vProjectName = ?, vDescription = ?, dStartDate = ?, dEndDate = ?, iStatus = ? WHERE I_ProjectID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssii", $project_name, $description, $start_date, $end_date, $status, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('project_updated', "Project updated: ID $id");
            echo "Project Updated Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to update project');
            echo "Failed to Update Project!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for project update');
        echo "Failed to Update Project!";
    }
}

// Fix SQL injection in delete_project function
function delete_project() {
    global $db, $link;
    
    $id = validateNumeric($_POST['id']);
    
    $stmt = mysqli_prepare($link, "UPDATE $db.web_project SET iStatus = 0 WHERE I_ProjectID = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            logSecurityEvent('project_deleted', "Project deleted: ID $id");
            echo "Project Deleted Successfully!";
        } else {
            logSecurityEvent('sql_error', 'Failed to delete project');
            echo "Failed to Delete Project!";
        }
    } else {
        logSecurityEvent('sql_error', 'Failed to prepare statement for project deletion');
        echo "Failed to Delete Project!";
    }
}

?>

<div id="success"></div>
<form id="projectForm" method="post" action="">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" />
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
    
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
        <?php echo (!empty($id) ? "Edit" : "New"); ?> Department
    </span>
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form">
                <tbody>
                    <tr id="message_row" style="display:none;">
                        <td colspan="2" id="message_cell" style="text-align:center;"></td>
                    </tr>
                    <tr>
                        <td class="left boder0-right">
                            <label for="category">Category <em>*</em></label>
                            <div class="log-case">
                                <?php
                                // This query should also be in a function, but for now, let's make it safe here.
                                $selcountry = "SELECT id, category, type FROM `{$db}`.web_category WHERE status=1 ORDER BY category";
                                $query1 = mysqli_query($link, $selcountry);
                                ?>

                                <select name="category" id="category" class="select-styl1" required>
                                    <option value=''>Select Category</option>
                                    <?php
                                    if ($query1) {
                                        while ($c_row = mysqli_fetch_array($query1)) {
                                            $ICategoryID = htmlspecialchars($c_row['id'], ENT_QUOTES, 'UTF-8');
                                            $VCategoryName = htmlspecialchars($c_row['category'], ENT_QUOTES, 'UTF-8');
                                            $VCategoryType = htmlspecialchars($c_row['type'], ENT_QUOTES, 'UTF-8');
                                            
                                            $sel = ($ICategoryID == $category) ? ' selected' : '';
                                            ?>
                                            <option value='<?php echo $ICategoryID; ?>' <?php echo $sel; ?>>
                                                <?php echo $VCategoryName; ?> - <?php echo $VCategoryType; ?>
                                            </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="left">
                            <label for="vProjectName">Department Name<em>*</em></label>
                            <div class="log-case">
                                <input name="vProjectName" id="vProjectName" type="text" value="<?php echo htmlspecialchars($vProjectName, ENT_QUOTES, 'UTF-8'); ?>" class="input-style1" required />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="left">
                            <center>
                                <input name="submit" type="submit" value="<?php echo (!empty($id) ? 'Update' : 'Create'); ?>" id="submitclkproject" class="button-orange1" style="float: inherit;" />
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('projectForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitButton = document.getElementById('submitclkproject');
        submitButton.disabled = true;
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            const messageCell = document.getElementById('message_cell');
            const messageRow = document.getElementById('message_row');
            
            if (text.trim().toLowerCase() === 'success') {
                messageCell.style.color = '#ffffff';
                messageCell.style.background = '#00CC66';
                messageCell.textContent = 'Operation successful!';
                if (!formData.get('id')) { // If it was a new entry
                    form.reset(); // Reset form for next entry
                }
            } else {
                messageCell.style.color = '#ffffff';
                messageCell.style.background = '#F00';
                messageCell.textContent = text; // Show error from PHP
            }
            messageRow.style.display = 'table-row';
            submitButton.disabled = false;

            setTimeout(() => {
                messageRow.style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            console.error('Error:', error);
            const messageCell = document.getElementById('message_cell');
            const messageRow = document.getElementById('message_row');
            messageCell.style.color = '#ffffff';
            messageCell.style.background = '#F00';
            messageCell.textContent = 'An unexpected error occurred.';
            messageRow.style.display = 'table-row';
            submitButton.disabled = false;
        });
    });
});
</script>
