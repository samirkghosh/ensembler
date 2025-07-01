<?php
/**
 * Controller for all category-related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting categories.
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

/**
 * Validates the CSRF token.
 * @return bool
 */
function validate_csrf_token() {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return true;
    }
    log_security_event('csrf_validation_failed', 'Invalid CSRF token');
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
 * Fetches all category types.
 * @return array
 */
function get_category_types() {
    global $db, $link;
    $types = [];
    $sql = "SELECT complaint_name FROM {$db}.web_complaint_type ORDER BY complaint_name";
    $result = mysqli_query($link, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $types[] = sanitize_input($row['complaint_name']);
        }
    }
    return $types;
}

/**
 * Fetches a single category's data.
 * @param int $id
 * @return array|null
 */
function get_category_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT type, category, VDescription FROM {$db}.web_category WHERE I_CategoryID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return array_map('sanitize_input', $data);
    }
    return null;
}

/**
 * Handles the submission for creating or updating a category.
 */
function handle_category_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $type = sanitize_input($_POST['type']);
    $category_name = sanitize_input($_POST['category']);
    $description = sanitize_input($_POST['V_Description']);
    $vuserid = $_SESSION['userid'];

    if (empty($type) || empty($category_name)) {
        echo json_encode(['success' => false, 'message' => 'Category type and name are required.']);
        return;
    }

    if ($id) {
        // Update existing category
        $sql = "UPDATE {$db}.web_category SET `type`=?, `category`=?, `VDescription`=? WHERE I_CategoryID=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sssi', $type, $category_name, $description, $id);
        $log_action = 'category_update';
        $success_message = 'Category updated successfully!';
    } else {
        // Create new category
        $sql = "INSERT INTO {$db}.web_category (`type`, `category`, `VDescription`, `iStatus`, `vcreatedby`, `dcreatedon`) VALUES (?, ?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $type, $category_name, $description, $vuserid);
        $log_action = 'category_creation';
        $success_message = 'Category created successfully!';
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $category_name, $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        log_security_event('sql_error', 'Failed to save category: ' . mysqli_error($link));
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all categories for display.
 * @return array
 */
function get_all_categories_for_view() {
    global $db, $link;
    $categories = [];
    $sql = "SELECT I_CategoryID, category, type, dcreatedon FROM {$db}.web_category WHERE iStatus = 1 ORDER BY category";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = [
                'id' => (int)$row['I_CategoryID'],
                'name' => sanitize_input($row['category']),
                'type' => sanitize_input($row['type']),
                'created_on' => sanitize_input($row['dcreatedon']),
            ];
        }
    }
    return $categories;
}

/**
 * Handles the deletion of a category.
 */
function handle_category_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_category SET iStatus = 0 WHERE I_CategoryID = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'category_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully!']);
    } else {
        log_security_event('sql_error', 'Failed to delete category: ' . mysqli_error($link));
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// Action router
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'submit_category':
        handle_category_submission();
        break;
    case 'delete_category':
        handle_category_deletion();
        break;
    // Default case can be to get all categories for the view page
    case 'get_categories':
    default:
        echo json_encode(['success' => true, 'data' => get_all_categories_for_view()]);
        break;
} 