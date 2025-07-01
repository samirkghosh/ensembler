<?php
/**
 * Controller for all Escalation related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting escalations.
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
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Fetches a single escalation's data.
 * @param int $id
 * @return array|null
 */
function get_escalation_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT escalation_to, escalation_media, escalation_list FROM {$db}.web_escalation WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return [
            'escalation_to' => sanitize_input($data['escalation_to']),
            'escalation_media' => sanitize_input($data['escalation_media']),
            'escalation_list' => array_map('intval', explode(',', $data['escalation_list']))
        ];
    }
    return null;
}

/**
 * Fetches all active users for the escalation list.
 * @return array
 */
function get_all_users() {
    global $db, $link;
    $users = [];
    $sql = "SELECT AtxUserID, AtxUserName FROM {$db}.uniuserprofile WHERE AtxUserStatus='1' ORDER BY AtxUserName";
    $result = mysqli_query($link, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = [
                'id' => (int)$row['AtxUserID'],
                'name' => sanitize_input($row['AtxUserName'])
            ];
        }
    }
    return $users;
}


/**
 * Handles the submission for creating or updating an escalation.
 */
function handle_escalation_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $escalation_to = filter_input(INPUT_POST, 'escalation_to', FILTER_VALIDATE_INT);
    $escalation_media = filter_input(INPUT_POST, 'escalation_media', FILTER_VALIDATE_INT);
    
    $escalation_list_raw = $_POST['escalation_list'] ?? [];
    $escalation_list = implode(',', array_map('intval', $escalation_list_raw));
    
    $vuserid = $_SESSION['userid'];

    if (empty($escalation_to) || empty($escalation_media)) {
        echo json_encode(['success' => false, 'message' => 'Escalation to and media are required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_escalation SET escalation_to=?, escalation_media=?, escalation_list=? WHERE id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'iisi', $escalation_to, $escalation_media, $escalation_list, $id);
        $log_action = 'escalation_update';
        $success_message = 'Escalation updated successfully!';
    } else {
        // There is no "creation" logic in the original file, only update.
        // Assuming we might need to add it one day.
        // For now, we only support updating.
        echo json_encode(['success' => false, 'message' => 'Creation of new escalations is not supported.']);
        return;
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all escalations for the view page.
 * @return array
 */
function get_all_escalations_for_view() {
    global $db, $link;
    $escalations = [];
    $sql = "SELECT e.id, e.d_created_on, p.vProjectName 
            FROM {$db}.web_escalation e
            JOIN {$db}.web_project p ON e.project_id = p.iProjectID
            WHERE e.status = 1 
            ORDER BY p.vProjectName";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $escalations[] = [
                'id' => (int)$row['id'],
                'project_name' => sanitize_input($row['vProjectName']),
                'created_on' => sanitize_input($row['d_created_on']),
            ];
        }
    }
    return $escalations;
}


// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_escalation':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_escalation_data($id)]);
        break;
    case 'get_users':
        echo json_encode(['success' => true, 'data' => get_all_users()]);
        break;
    case 'submit_escalation':
        handle_escalation_submission();
        break;
    case 'get_escalations':
    default:
         echo json_encode(['success' => true, 'data' => get_all_escalations_for_view()]);
        break;
}