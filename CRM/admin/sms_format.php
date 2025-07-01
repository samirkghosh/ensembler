<?php
/**
 * Controller for all SMS format related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting SMS formats.
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
 * Fetches a single SMS format's data.
 * @param int $id
 * @return array|null
 */
function get_sms_format_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT template_name, type, header, body, footer, description, expiry FROM {$db}.web_smsformats WHERE id = ?");
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
 * Handles the submission for creating or updating an SMS format.
 */
function handle_sms_format_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $template_name = sanitize_input($_POST['template_name']);
    $type = sanitize_input($_POST['type']);
    $header = sanitize_input($_POST['header']);
    $body = sanitize_input($_POST['body']);
    $footer = sanitize_input($_POST['footer']);
    $description = sanitize_input($_POST['description']);
    $expiry = filter_input(INPUT_POST, 'expiry', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (empty($template_name) || empty($body)) {
        echo json_encode(['success' => false, 'message' => 'Template name and body are required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_smsformats SET template_name=?, type=?, header=?, body=?, footer=?, description=?, expiry=? WHERE id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssssii', $template_name, $type, $header, $body, $footer, $description, $expiry, $id);
        $log_action = 'smsformat_update';
        $success_message = 'SMS format updated successfully!';
    } else {
        $sql = "INSERT INTO {$db}.web_smsformats (template_name, type, header, body, footer, description, expiry, status, createdby, createdon) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssssis', $template_name, $type, $header, $body, $footer, $description, $expiry, $vuserid);
        $log_action = 'smsformat_creation';
        $success_message = 'SMS format created successfully!';
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $template_name, $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all SMS formats for the view page.
 * @return array
 */
function get_all_sms_formats_for_view() {
    global $db, $link;
    $formats = [];
    $sql = "SELECT id, template_name, type, createdon FROM {$db}.web_smsformats WHERE status = 1 ORDER BY template_name";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formats[] = [
                'id' => (int)$row['id'],
                'name' => sanitize_input($row['template_name']),
                'type' => sanitize_input($row['type']),
                'created_on' => sanitize_input($row['createdon']),
            ];
        }
    }
    return $formats;
}

/**
 * Handles the deletion of an SMS format.
 */
function handle_sms_format_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_smsformats SET status = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'smsformat_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'SMS format deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_sms_format':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_sms_format_data($id)]);
        break;
    case 'submit_sms_format':
        handle_sms_format_submission();
        break;
    case 'delete_sms_format':
        handle_sms_format_deletion();
        break;
    case 'get_sms_formats':
    default:
        echo json_encode(['success' => true, 'data' => get_all_sms_formats_for_view()]);
        break;
}
