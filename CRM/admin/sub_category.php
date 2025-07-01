<?php
/**
 * Controller for all sub-category related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting sub-categories.
 * All responses are in JSON format.
 *
 * @author AI
 * @since 2024-07-23
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header('Content-Type: application/json');
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// --- [[ INCLUDES ]] ---
// Include necessary files
// require_once('../db.php'); // Adjust path as needed
// require_once('web_admin_function.php'); // Adjust path as needed


/**
 * Validates the CSRF token.
 * @return bool
 */
function validate_csrf_token() {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return true;
    }
    // In a real app, you'd log this event.
    // log_security_event('csrf_validation_failed', 'Invalid CSRF token');
    echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
    return false;
}

/**
 * Sanitizes user input.
 * @param $input
 * @return string
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Fetches all parent categories for dropdowns.
 * @return array
 */
function get_parent_categories() {
    global $db, $link;
    $categories = [];
    $sql = "SELECT I_CategoryID, category FROM {$db}.web_category WHERE iStatus = 1 ORDER BY category";
    $result = mysqli_query($link, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = [
                'id' => (int)$row['I_CategoryID'],
                'name' => sanitize_input($row['category'])
            ];
        }
    }
    return $categories;
}

/**
 * Fetches a single sub-category's data.
 * @param int $id
 * @return array|null
 */
function get_subcategory_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT I_CategoryID, vSubCategoryName, VDescription FROM {$db}.web_sub_category WHERE I_SubCategoryID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return [
            'category_id' => (int)$data['I_CategoryID'],
            'name' => sanitize_input($data['vSubCategoryName']),
            'description' => sanitize_input($data['VDescription'])
        ];
    }
    return null;
}

/**
 * Handles the submission for creating or updating a sub-category.
 */
function handle_subcategory_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $vuserid = $_SESSION['userid'];

    if (empty($category_id) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category and sub-category name are required.']);
        return;
    }

    if ($id) {
        // Update existing sub-category
        $sql = "UPDATE {$db}.web_sub_category SET I_CategoryID=?, vSubCategoryName=?, VDescription=? WHERE I_SubCategoryID=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'issi', $category_id, $name, $description, $id);
        $log_action = 'subcategory_update';
        $success_message = 'Sub-category updated successfully!';
    } else {
        // Create new sub-category
        $sql = "INSERT INTO {$db}.web_sub_category (I_CategoryID, vSubCategoryName, VDescription, iStatus, vcreatedby, dcreatedon) VALUES (?, ?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'isss', $category_id, $name, $description, $vuserid);
        $log_action = 'subcategory_creation';
        $success_message = 'Sub-category created successfully!';
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $name, $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        // log_security_event('sql_error', 'Failed to save sub-category: ' . mysqli_error($link));
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all sub-categories for the view page.
 * @return array
 */
function get_all_subcategories_for_view() {
    global $db, $link;
    $subcategories = [];
    $sql = "SELECT sc.I_SubCategoryID, sc.vSubCategoryName, c.category as parent_category, sc.dcreatedon 
            FROM {$db}.web_sub_category sc
            JOIN {$db}.web_category c ON sc.I_CategoryID = c.I_CategoryID
            WHERE sc.iStatus = 1 
            ORDER BY c.category, sc.vSubCategoryName";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subcategories[] = [
                'id' => (int)$row['I_SubCategoryID'],
                'name' => sanitize_input($row['vSubCategoryName']),
                'parent_category' => sanitize_input($row['parent_category']),
                'created_on' => sanitize_input($row['dcreatedon']),
            ];
        }
    }
    return $subcategories;
}

/**
 * Handles the deletion of a sub-category.
 */
function handle_subcategory_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_sub_category SET iStatus = 0 WHERE I_SubCategoryID = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'subcategory_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Sub-category deleted successfully!']);
    } else {
        // log_security_event('sql_error', 'Failed to delete sub-category: ' . mysqli_error($link));
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'submit_subcategory':
        handle_subcategory_submission();
        break;
    case 'delete_subcategory':
        handle_subcategory_deletion();
        break;
    case 'get_parent_categories':
        echo json_encode(['success' => true, 'data' => get_parent_categories()]);
        break;
    case 'get_subcategories':
    default:
        echo json_encode(['success' => true, 'data' => get_all_subcategories_for_view()]);
        break;
} 