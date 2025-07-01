<?php
/**
 * Controller for user-project assignment actions.
 *
 * Handles assigning and unassigning projects to users.
 * All responses are in JSON format.
 *
 * @author AI
 * @since 2024-07-23
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
 * Fetches all back-office users.
 * @return array
 */
function get_back_office_users() {
    global $db, $link;
    $users = [];
    $sql = "SELECT AtxUserID, AtxUserName FROM {$db}.web_users WHERE vusertype = 'back_officer' AND iStatus = 1 ORDER BY AtxUserName";
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
 * Fetches all projects and their assignment status for a given user.
 * @param int $user_id
 * @return array
 */
function get_projects_with_assignment_status($user_id) {
    global $db, $link;
    $projects = [];

    // Get all active projects
    $all_projects_sql = "SELECT pId, vProjectName FROM {$db}.web_projects WHERE i_Status='1' ORDER BY vProjectName ASC";
    $all_projects_result = mysqli_query($link, $all_projects_sql);
    $all_projects = mysqli_fetch_all($all_projects_result, MYSQLI_ASSOC);

    // Get projects assigned to the user
    $assigned_projects_sql = "SELECT I_ProjectID FROM {$db}.web_project_users WHERE I_UserID = ?";
    $stmt = mysqli_prepare($link, $assigned_projects_sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $assigned_project_ids = array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'I_ProjectID');
    mysqli_stmt_close($stmt);

    foreach ($all_projects as $project) {
        $projects[] = [
            'id' => (int)$project['pId'],
            'name' => sanitize_input($project['vProjectName']),
            'is_assigned' => in_array($project['pId'], $assigned_project_ids)
        ];
    }

    return $projects;
}


/**
 * Assigns a set of projects to a user.
 */
function handle_project_assignment() {
    global $db, $link;
    if (!validate_csrf_token()) return;

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $project_ids = $_POST['project_ids'] ?? [];
    $vuserid = $_SESSION['userid'];

    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid user selected.']);
        return;
    }

    // First, clear existing assignments for the user
    $delete_sql = "DELETE FROM {$db}.web_project_users WHERE I_UserID = ?";
    $stmt_delete = mysqli_prepare($link, $delete_sql);
    mysqli_stmt_bind_param($stmt_delete, 'i', $user_id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    // Now, insert the new assignments
    if (!empty($project_ids)) {
        $insert_sql = "INSERT INTO {$db}.web_project_users (I_UserID, I_ProjectID) VALUES (?, ?)";
        $stmt_insert = mysqli_prepare($link, $insert_sql);

        foreach ($project_ids as $project_id) {
            $p_id = filter_var($project_id, FILTER_VALIDATE_INT);
            if ($p_id) {
                mysqli_stmt_bind_param($stmt_insert, 'ii', $user_id, $p_id);
                mysqli_stmt_execute($stmt_insert);
            }
        }
        mysqli_stmt_close($stmt_insert);
    }
    
    add_audit_log($vuserid, 'project_assignment_update', '', "User ID: {$user_id}", $db);
    echo json_encode(['success' => true, 'message' => 'Assignments updated successfully!']);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_users':
        echo json_encode(['success' => true, 'data' => get_back_office_users()]);
        break;
    case 'get_projects':
        $user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'User ID is required.']);
            break;
        }
        echo json_encode(['success' => true, 'data' => get_projects_with_assignment_status($user_id)]);
        break;
    case 'assign_projects':
        handle_project_assignment();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
        break;
} 