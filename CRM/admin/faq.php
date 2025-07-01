<?php
/**
 * Controller for all FAQ (Knowledge Base) related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting FAQs.
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
 * Fetches a single FAQ's data.
 * @param int $id
 * @return array|null
 */
function get_faq_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT v_qus, v_ans FROM {$db}.web_knowledge_base WHERE i_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return [
            'question' => sanitize_input($data['v_qus']),
            'answer' => sanitize_input($data['v_ans'])
        ];
    }
    return null;
}

/**
 * Handles the submission for creating or updating a FAQ.
 */
function handle_faq_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $question = sanitize_input($_POST['question']);
    $answer = sanitize_input($_POST['answer']);
    $vuserid = $_SESSION['userid'];

    if (empty($question) || empty($answer)) {
        echo json_encode(['success' => false, 'message' => 'Question and answer are required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_knowledge_base SET v_qus=?, v_ans=? WHERE i_id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $question, $answer, $id);
        $log_action = 'faq_update';
        $success_message = 'FAQ updated successfully!';
    } else {
        $sql = "INSERT INTO {$db}.web_knowledge_base (v_qus, v_ans, i_status, i_created_by, d_created_on) VALUES (?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $question, $answer, $vuserid);
        $log_action = 'faq_creation';
        $success_message = 'FAQ created successfully!';
    }

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, $log_action, '', $question, $db);
        echo json_encode(['success' => true, 'message' => $success_message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

/**
 * Fetches all FAQs for the view page.
 * @return array
 */
function get_all_faqs_for_view() {
    global $db, $link;
    $faqs = [];
    $sql = "SELECT i_id, v_qus, d_created_on FROM {$db}.web_knowledge_base WHERE i_status = 1 ORDER BY i_id DESC";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $faqs[] = [
                'id' => (int)$row['i_id'],
                'question' => sanitize_input($row['v_qus']),
                'created_on' => sanitize_input($row['d_created_on']),
            ];
        }
    }
    return $faqs;
}

/**
 * Handles the deletion of a FAQ.
 */
function handle_faq_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_knowledge_base SET i_status = 0 WHERE i_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'faq_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'FAQ deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_faq':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_faq_data($id)]);
        break;
    case 'submit_faq':
        handle_faq_submission();
        break;
    case 'delete_faq':
        handle_faq_deletion();
        break;
    case 'get_faqs':
    default:
        echo json_encode(['success' => true, 'data' => get_all_faqs_for_view()]);
        break;
}
