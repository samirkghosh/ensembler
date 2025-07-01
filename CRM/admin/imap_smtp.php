<?php
/**
 * Controller for all IMAP/SMTP related actions.
 *
 * It handles the logic for creating, viewing, and updating mail server settings.
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
 * Fetches a single IMAP record's data.
 * @param int $id
 * @return array|null
 */
function get_imap_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT * FROM {$db}.web_imap_settings WHERE i_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $data ? array_map('sanitize_input', $data) : null;
}

/**
 * Fetches a single SMTP record's data.
 * @param int $id
 * @return array|null
 */
function get_smtp_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT * FROM {$db}.web_smtp_settings WHERE i_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $data ? array_map('sanitize_input', $data) : null;
}

/**
 * Handles the submission for updating IMAP settings.
 */
function handle_imap_update() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $fields = [
        'v_connectionname' => sanitize_input($_POST['v_connectionname']),
        'v_ipaddress' => sanitize_input($_POST['v_ipaddress']),
        'v_username' => sanitize_input($_POST['v_username']),
        'v_pasowrd' => sanitize_input($_POST['v_pasowrd']),
        'v_type' => sanitize_input($_POST['v_type']),
        'cc_mail_id' => sanitize_input($_POST['cc_mail_id']),
        'v_client_id' => sanitize_input($_POST['v_client_id']),
        'v_client_secret' => sanitize_input($_POST['v_client_secret']),
        'v_tenant' => sanitize_input($_POST['v_tenant'])
    ];
    
    $vuserid = $_SESSION['userid'];

    $sql = "UPDATE {$db}.web_imap_settings SET v_connectionname=?, v_ipaddress=?, v_username=?, v_pasowrd=?, v_type=?, cc_mail_id=?, v_client_id=?, v_client_secret=?, v_tenant=? WHERE i_id=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssssi', 
        $fields['v_connectionname'], $fields['v_ipaddress'], $fields['v_username'], $fields['v_pasowrd'],
        $fields['v_type'], $fields['cc_mail_id'], $fields['v_client_id'], $fields['v_client_secret'],
        $fields['v_tenant'], $id
    );

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'imap_update', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'IMAP settings updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Handles the submission for updating SMTP settings.
 */
function handle_smtp_update() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }
    
    $fields = [
        'v_connectionname' => sanitize_input($_POST['v_connectionname']),
        'v_ipaddress' => sanitize_input($_POST['v_ipaddress']),
        'i_port' => filter_input(INPUT_POST, 'i_port', FILTER_VALIDATE_INT),
        'v_username' => sanitize_input($_POST['v_username']),
        'v_pasowrd' => sanitize_input($_POST['v_pasowrd']),
        'v_type' => sanitize_input($_POST['v_type']),
    ];

    $vuserid = $_SESSION['userid'];

    $sql = "UPDATE {$db}.web_smtp_settings SET v_connectionname=?, v_ipaddress=?, i_port=?, v_username=?, v_pasowrd=?, v_type=? WHERE i_id=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'ssisssi',
        $fields['v_connectionname'], $fields['v_ipaddress'], $fields['i_port'], $fields['v_username'],
        $fields['v_pasowrd'], $fields['v_type'], $id
    );

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'smtp_update', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'SMTP settings updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all IMAP/SMTP settings for the view page.
 * @return array
 */
function get_all_mail_settings() {
    global $db, $link;
    $settings = ['imap' => [], 'smtp' => []];

    // Fetch IMAP
    $sql_imap = "SELECT i_id, v_connectionname, v_ipaddress, v_username FROM {$db}.web_imap_settings WHERE i_status = 1";
    $result_imap = mysqli_query($link, $sql_imap);
    if ($result_imap) {
        while ($row = mysqli_fetch_assoc($result_imap)) {
            $settings['imap'][] = array_map('sanitize_input', $row);
        }
    }

    // Fetch SMTP
    $sql_smtp = "SELECT i_id, v_connectionname, v_ipaddress, v_username FROM {$db}.web_smtp_settings WHERE i_status = 1";
    $result_smtp = mysqli_query($link, $sql_smtp);
    if ($result_smtp) {
        while ($row = mysqli_fetch_assoc($result_smtp)) {
            $settings['smtp'][] = array_map('sanitize_input', $row);
        }
    }
    
    return $settings;
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_imap':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_imap_data($id)]);
        break;
    case 'get_smtp':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_smtp_data($id)]);
        break;
    case 'update_imap':
        handle_imap_update();
        break;
    case 'update_smtp':
        handle_smtp_update();
        break;
    case 'get_all_settings':
    default:
        echo json_encode(['success' => true, 'data' => get_all_mail_settings()]);
        break;
}
