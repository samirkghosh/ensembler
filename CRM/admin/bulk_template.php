<?php
/**
 * Controller for all Bulk Template related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting templates.
 * All responses are in JSON format.
 *
 * @author AI
 * @since 2024-07-25
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
 * Fetches a single bulk template's data.
 * @param int $id
 * @return array|null
 */
function get_template_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT name, template_content, type, slug FROM {$db}.web_bulk_template WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $data ? array_map('sanitize_input', $data) : null;
}

/**
 * Handles the submission for creating or updating a bulk template.
 */
function handle_template_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = sanitize_input($_POST['name']);
    $content = sanitize_input($_POST['content']);
    $type = sanitize_input($_POST['type']);
    $slug = sanitize_input($_POST['slug']);
    $vuserid = $_SESSION['userid'];

    if (empty($name) || empty($content) || empty($type)) {
        echo json_encode(['success' => false, 'message' => 'Name, content, and type are required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_bulk_template SET name=?, template_content=?, type=?, slug=? WHERE id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssi', $name, $content, $type, $slug, $id);
        $log_action = 'bulk_template_update';
        $success_message = 'Template updated successfully!';
    } else {
        $sql = "INSERT INTO {$db}.web_bulk_template (name, template_content, type, slug, status, created_by, created_on) VALUES (?, ?, ?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $name, $content, $type, $slug, $vuserid);
        $log_action = 'bulk_template_creation';
        $success_message = 'Template created successfully!';
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
 * Fetches all templates for the view page.
 * @return array
 */
function get_all_templates_for_view() {
    global $db, $link;
    $templates = [];
    $sql = "SELECT id, name, type, created_on FROM {$db}.web_bulk_template WHERE status = 1 ORDER BY name";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $templates[] = [
                'id' => (int)$row['id'],
                'name' => sanitize_input($row['name']),
                'type' => sanitize_input($row['type']),
                'created_on' => sanitize_input($row['created_on']),
            ];
        }
    }
    return $templates;
}

/**
 * Handles the deletion of a template.
 */
function handle_template_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_bulk_template SET status = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'bulk_template_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Template deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_template':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_template_data($id)]);
        break;
    case 'submit_template':
        handle_template_submission();
        break;
    case 'delete_template':
        handle_template_deletion();
        break;
    case 'get_templates':
    default:
        echo json_encode(['success' => true, 'data' => get_all_templates_for_view()]);
        break;
}