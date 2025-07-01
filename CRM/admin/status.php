<?php
/**
 * Controller for all status related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting statuses.
 * All responses are in JSON format.
 *
 * @author AI
 * @since 2024-07-24
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

function validate_csrf_token() {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return true;
    }
    echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
    return false;
}

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Fetches a single status's data.
 * @param int $id
 * @return array|null
 */
function get_status_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT ticketstatus FROM {$db}.web_ticketstatus WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return ['name' => sanitize_input($data['ticketstatus'])];
    }
    return null;
}

/**
 * Handles the submission for creating or updating a status.
 */
function handle_status_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = sanitize_input($_POST['name']);
    $vuserid = $_SESSION['userid'];

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Status name is required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_ticketstatus SET ticketstatus=? WHERE id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $name, $id);
        $log_action = 'status_update';
        $success_message = 'Status updated successfully!';
    } else {
        $sql = "INSERT INTO {$db}.web_ticketstatus (ticketstatus, status, vcreatedby, dcreatedon) VALUES (?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name, $vuserid);
        $log_action = 'status_creation';
        $success_message = 'Status created successfully!';
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $name, $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all statuses for the view page.
 * @return array
 */
function get_all_statuses_for_view() {
    global $db, $link;
    $statuses = [];
    $sql = "SELECT id, ticketstatus, dcreatedon FROM {$db}.web_ticketstatus WHERE status = 1 ORDER BY ticketstatus";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $statuses[] = [
                'id' => (int)$row['id'],
                'name' => sanitize_input($row['ticketstatus']),
                'created_on' => sanitize_input($row['dcreatedon']),
            ];
        }
    }
    return $statuses;
}

/**
 * Handles the deletion of a status.
 */
function handle_status_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_ticketstatus SET status = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'status_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Status deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_status':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_status_data($id)]);
        break;
    case 'submit_status':
        handle_status_submission();
        break;
    case 'delete_status':
        handle_status_deletion();
        break;
    case 'get_statuses':
    default:
        echo json_encode(['success' => true, 'data' => get_all_statuses_for_view()]);
        break;
}
