<?php
/**
 * Controller for all village/district related actions.
 *
 * It handles the logic for creating, viewing, updating, and deleting villages.
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
 * Fetches all parent districts (provinces).
 * @return array
 */
function get_all_districts() {
    global $db, $link;
    $districts = [];
    $sql = "SELECT id, city FROM {$db}.web_city WHERE iStatus = 1 ORDER BY city";
    $result = mysqli_query($link, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $districts[] = [
                'id' => (int)$row['id'],
                'name' => sanitize_input($row['city'])
            ];
        }
    }
    return $districts;
}

/**
 * Fetches a single village's data.
 * @param int $id
 * @return array|null
 */
function get_village_data($id) {
    global $db, $link;
    if (!is_numeric($id)) return null;

    $stmt = mysqli_prepare($link, "SELECT iDistrictID, vVillage, V_Description FROM {$db}.web_village WHERE iVillageID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($data) {
        return [
            'district_id' => (int)$data['iDistrictID'],
            'name' => sanitize_input($data['vVillage']),
            'description' => sanitize_input($data['V_Description'])
        ];
    }
    return null;
}

/**
 * Handles the submission for creating or updating a village.
 */
function handle_village_submission() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $district_id = filter_input(INPUT_POST, 'district_id', FILTER_VALIDATE_INT);
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $vuserid = $_SESSION['userid'];

    if (empty($district_id) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'District and village name are required.']);
        return;
    }

    if ($id) {
        $sql = "UPDATE {$db}.web_village SET iDistrictID=?, vVillage=?, V_Description=? WHERE iVillageID=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'issi', $district_id, $name, $description, $id);
        $log_action = 'village_update';
        $success_message = 'Village updated successfully!';
    } else {
        $sql = "INSERT INTO {$db}.web_village (iDistrictID, vVillage, V_Description, iStatus, vcreatedby, dcreatedon) VALUES (?, ?, ?, 1, ?, NOW())";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'isss', $district_id, $name, $description, $vuserid);
        $log_action = 'village_creation';
        $success_message = 'Village created successfully!';
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
 * Fetches all villages for the view page.
 * @return array
 */
function get_all_villages_for_view() {
    global $db, $link;
    $villages = [];
    $sql = "SELECT v.iVillageID, v.vVillage, c.city as district_name, v.dcreatedon 
            FROM {$db}.web_village v
            JOIN {$db}.web_city c ON v.iDistrictID = c.id
            WHERE v.iStatus = 1 
            ORDER BY c.city, v.vVillage";
    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $villages[] = [
                'id' => (int)$row['iVillageID'],
                'name' => sanitize_input($row['vVillage']),
                'district_name' => sanitize_input($row['district_name']),
                'created_on' => sanitize_input($row['dcreatedon']),
            ];
        }
    }
    return $villages;
}

/**
 * Handles the deletion of a village.
 */
function handle_village_deletion() {
    global $db, $link;

    if (!validate_csrf_token()) return;

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $vuserid = $_SESSION['userid'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        return;
    }

    $stmt = mysqli_prepare($link, "UPDATE {$db}.web_village SET iStatus = 0 WHERE iVillageID = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        add_audit_log($vuserid, 'village_delete', '', "ID: {$id}", $db);
        echo json_encode(['success' => true, 'message' => 'Village deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
    mysqli_stmt_close($stmt);
}

// --- [[ Action Router ]] ---
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_districts':
        echo json_encode(['success' => true, 'data' => get_all_districts()]);
        break;
    case 'get_village':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        echo json_encode(['success' => true, 'data' => get_village_data($id)]);
        break;
    case 'submit_village':
        handle_village_submission();
        break;
    case 'delete_village':
        handle_village_deletion();
        break;
    case 'get_villages':
    default:
        echo json_encode(['success' => true, 'data' => get_all_villages_for_view()]);
        break;
}
