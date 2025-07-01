<?php
/**
 * Controller for Mail Format Management
 *
 * @author AI
 * @version 1.0
 * @package ensembler
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; form-action 'self';");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer-when-downgrade");

require_once '../../config/web_mysqlconnect.php';
require_once 'web_admin_function.php';

// CSRF Token Management
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'CSRF token mismatch.']);
        exit;
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action.'];

switch ($action) {
    case 'create_mail_format':
    case 'update_mail_format':
        $response = handle_mail_format_submission();
        break;
    case 'delete_mail_format':
        $response = handle_mail_format_deletion();
        break;
    case 'get_mail_formats':
        $response = get_mail_formats_list();
        break;
    case 'get_mail_format':
        $response = get_single_mail_format();
        break;
    default:
        // Response is already set to invalid action
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;

function handle_mail_format_submission() {
    global $link;
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null;
    $template_name = isset($_POST['template_name']) ? htmlspecialchars(trim($_POST['template_name']), ENT_QUOTES, 'UTF-8') : '';
    $type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type']), ENT_QUOTES, 'UTF-8') : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject']), ENT_QUOTES, 'UTF-8') : '';
    $greeting = isset($_POST['greeting']) ? htmlspecialchars(trim($_POST['greeting']), ENT_QUOTES, 'UTF-8') : '';
    $body = isset($_POST['body']) ? htmlspecialchars(trim($_POST['body']), ENT_QUOTES, 'UTF-8') : '';
    $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8') : '';
    $signature = isset($_POST['signature']) ? htmlspecialchars(trim($_POST['signature']), ENT_QUOTES, 'UTF-8') : '';
    $expiry = isset($_POST['expiry']) ? filter_var($_POST['expiry'], FILTER_SANITIZE_NUMBER_INT) : 0;

    if (empty($template_name) || empty($type) || empty($subject) || empty($greeting) || empty($description)) {
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }

    if ($id) {
        $stmt = $link->prepare("UPDATE tbl_mail_type SET MailTemplateName = ?, MailType = ?, MailSubject = ?, MailGreeting = ?, MailBody = ?, MailDescription = ?, MailSignature = ?, MailExpiry = ? WHERE MailTypeID = ?");
        $stmt->bind_param("sssssssii", $template_name, $type, $subject, $greeting, $body, $description, $signature, $expiry, $id);
    } else {
        $stmt = $link->prepare("INSERT INTO tbl_mail_type (MailTemplateName, MailType, MailSubject, MailGreeting, MailBody, MailDescription, MailSignature, MailExpiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $template_name, $type, $subject, $greeting, $body, $description, $signature, $expiry);
    }

    if ($stmt->execute()) {
        $message = $id ? 'Mail format updated successfully.' : 'Mail format created successfully.';
        return ['success' => true, 'message' => $message];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }
}

function handle_mail_format_deletion() {
    // This function will now directly handle the deletion.
    global $link;
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null;
    if (!$id) {
        return ['success' => false, 'message' => 'Invalid ID provided.'];
    }
    $stmt = $link->prepare("DELETE FROM tbl_mail_type WHERE MailTypeID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            return ['success' => true, 'message' => 'Mail format deleted successfully.'];
        } else {
            return ['success' => false, 'message' => 'Mail format not found or could not be deleted.'];
        }
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }
}

function get_mail_formats_list() {
    $result = view_mail_data(); // Assumes this function is secure
    $formats = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formats[] = array_map(fn($item) => htmlspecialchars($item, ENT_QUOTES, 'UTF-8'), $row);
        }
    }
    return ['success' => true, 'formats' => $formats];
}

function get_single_mail_format() {
    global $link;
    $id = isset($_GET['id']) ? filter_var(base64_decode($_GET['id']), FILTER_SANITIZE_NUMBER_INT) : '';

    if (empty($id)) {
        return ['success' => false, 'message' => 'No ID provided.'];
    }

    $data = getMailFormatData($link, $id); // Assumes this function is secure and handles sanitization

    if ($data) {
        return ['success' => true, 'data' => array_map(fn($item) => htmlspecialchars($item, ENT_QUOTES, 'UTF-8'), $data)];
    } else {
        return ['success' => false, 'message' => 'Mail format not found.'];
    }
} 