<?php
/**
 * Controller for Whatsapp Template Management
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

// Check for CSRF token
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
    case 'create_whatsapp_template':
    case 'update_whatsapp_template':
        $response = handle_whatsapp_template_submission();
        break;
    case 'delete_whatsapp_template':
        $response = handle_whatsapp_template_deletion();
        break;
    case 'get_whatsapp_templates':
        $response = get_whatsapp_templates_list();
        break;
    default:
        // Already handled by the initial response value
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
exit;

function handle_whatsapp_template_submission() {
    global $link;
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null;
    $template_name = isset($_POST['template_name']) ? htmlspecialchars(trim($_POST['template_name']), ENT_QUOTES, 'UTF-8') : '';
    $type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type']), ENT_QUOTES, 'UTF-8') : '';
    $body = isset($_POST['body']) ? htmlspecialchars(trim($_POST['body']), ENT_QUOTES, 'UTF-8') : '';

    if (empty($template_name) || empty($type) || empty($body)) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }

    if ($id) {
        $stmt = $link->prepare("UPDATE tbl_whatsapp_template SET temp_name = ?, temp_type = ?, temp_content = ? WHERE id = ?");
        $stmt->bind_param("sssi", $template_name, $type, $body, $id);
    } else {
        $stmt = $link->prepare("INSERT INTO tbl_whatsapp_template (temp_name, temp_type, temp_content) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $template_name, $type, $body);
    }

    if ($stmt->execute()) {
        $message = $id ? 'Whatsapp template updated successfully.' : 'Whatsapp template created successfully.';
        return ['success' => true, 'message' => $message];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }
}

function handle_whatsapp_template_deletion() {
    global $link;
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT) : null;

    if (!$id) {
        return ['success' => false, 'message' => 'Invalid ID.'];
    }

    $stmt = $link->prepare("DELETE FROM tbl_whatsapp_template WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Whatsapp template deleted successfully.'];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }
}

function get_whatsapp_templates_list() {
    $result = view_whatsapp_template();
    $templates = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $templates[] = [
                'id' => htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'),
                'temp_name' => htmlspecialchars($row['temp_name'], ENT_QUOTES, 'UTF-8'),
                'temp_type' => htmlspecialchars($row['temp_type'], ENT_QUOTES, 'UTF-8'),
                'temp_content' => htmlspecialchars($row['temp_content'], ENT_QUOTES, 'UTF-8'),
            ];
        }
    }
    return ['success' => true, 'templates' => $templates];
} 