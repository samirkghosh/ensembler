<?php
/**
 * Controller for all Spam Mail related actions.
 *
 * It handles the logic for creating, viewing, and deleting spam mail entries.
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
 * Handles the submission for creating a new spam mail entry.
 */
function handle_spam_mail_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $vuserid = $_SESSION['userid'];

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address provided.']);
        return;
    }

    $sql = "INSERT INTO {$db}.web_spam_mail (mail, status, created_by, created_on) VALUES (?, 1, ?, NOW())";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $vuserid);
    
    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'spam_mail_creation', '', $email, $db);
        echo json_encode(['success' => true, 'message' => 'Spam mail entry created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all spam mail entries for the view page.
 * @return array
 */
function get_all_spam_mails_for_view() {
    global $db, $link;
    $mails = [];
    $sql = "SELECT id, mail, created_on FROM {$db}.web_spam_mail WHERE status = 1 ORDER BY id DESC";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $mails[] = [
                'id' => (int)$row['id'],
                'mail' => sanitize_input($row['mail']),
                'created_on' => sanitize_input($row['created_on']),
            ];
        }
    }
    return $mails;
}

/**
 * Handles the deletion of a spam mail entry.
 */
function handle_spam_mail_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_spam_mail SET status = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'spam_mail_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Spam mail entry deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'submit_spam_mail':
        handle_spam_mail_submission();
        break;
    case 'delete_spam_mail':
        handle_spam_mail_deletion();
        break;
    case 'get_spam_mails':
    default:
        echo json_encode(['success' => true, 'data' => get_all_spam_mails_for_view()]);
        break;
}