<?php
/**
 * Author: Vastvikta Nishad
 * Date: 26-03-2025
 * This page handles saving and fetching filters.
 */
include("../../config/web_mysqlconnect.php"); // Database connection
header('Content-Type: application/json');

// Read raw JSON input from AJAX
$jsonInput = file_get_contents("php://input");
error_log("Received JSON: " . $jsonInput); // Debugging

$data = json_decode($jsonInput, true);


error_log("Request Method: " . $_SERVER["REQUEST_METHOD"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);  // Read JSON payload correctly

    if (!isset($data['action'])) {
        error_log("No action found in request");
        exit(json_encode(["status" => "error", "message" => "No action specified"]));
    }

    global $db, $link;
    error_log("Action Received: " . $data['action']);

    if ($data['action'] === "save_filter") {
        save_filter($data);
    } elseif ($data['action'] === "get_filter" && isset($data['filter_id'])) {
        $filter_data = get_filter_data($data['filter_id']);
        if ($filter_data) {
            exit(json_encode(["status" => "success", "filters" => $filter_data]));
        } else {
            exit(json_encode(["status" => "error", "message" => "Filter not found"]));
        }
    } elseif ($data['action'] === "delete_filter" && isset($data['filter_id'])) {
        $response = delete_filter($data['filter_id']);
        exit(json_encode($response));  // Return the response from the delete function
    } elseif ($data['action'] === "update_filter") {
        save_filter($data);
    } elseif ($data['action'] === "update_dropdown") {
        // Fix: Use `$data` instead of `$input`
        $status = isset($data['status']) ? $data['status'] : '1';
        $agent_id = $_SESSION['userid'];

        $result = get_filter_list($status, $agent_id);
        $filters = "";

        while ($row = mysqli_fetch_array($result)) {
            $filters .= "<option value='" . $row['id'] . "'>" . $row['filter_name'] . "</option>";
        }

        exit(json_encode(["status" => "success", "filters" => $filters]));
    } elseif ($data['action'] === "restore_filter" && isset($data['filter_id'])) {
        $response = restore_filter($data['filter_id']);
        exit(json_encode($response));  // Return the response from the delete function
    }

    exit(json_encode(["status" => "error", "message" => "Invalid request action"]));
}

// Function to get filter list
function get_filter_list($status, $agent_id) {
    global $db, $link;
    if ($agent_id == '1') {
        $sql = "SELECT * FROM $db.user_filters WHERE filter_page = 'login_user_report' AND status = '$status'";
    } else {
        $sql = "SELECT * FROM $db.user_filters WHERE filter_page = 'login_user_report' AND created_by = '$agent_id' AND status = '$status'"; 
    }
    return mysqli_query($link, $sql);
}

function delete_filter($filter_id){
    global $db, $link;

    $filter_id = mysqli_real_escape_string($link, $filter_id);
    $sql = "UPDATE $db.`user_filters` SET `status` = '0' WHERE `id` = '$filter_id' AND `status` != '0';";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        error_log("SQL Error: " . mysqli_error($link));
        return ["status" => "error", "message" => "Failed to delete the filter."];
    }

    if (mysqli_affected_rows($link) > 0) {
        return ["status" => "success", "message" => "Filter deleted successfully."];
    } else {
        return ["status" => "error", "message" => "Filter not found or already deleted."];
    }
}
function restore_filter($filter_id){
    global $db, $link;

    $filter_id = mysqli_real_escape_string($link, $filter_id);
    $sql = "UPDATE $db.`user_filters` SET `status` = '1' WHERE `id` = '$filter_id' AND `status` != '1';";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        error_log("SQL Error: " . mysqli_error($link));
        return ["status" => "error", "message" => "Failed to restore the filter."];
    }

    if (mysqli_affected_rows($link) > 0) {
        return ["status" => "success", "message" => "Filter restored successfully."];
    } else {
        return ["status" => "error", "message" => "Filter not found or already restored."];
    }
}
// Function to save filter
function save_filter($data) {
    global $db, $link;

    $agent_id = $_SESSION['userid'];
    if (!isset($data['filter_name']) || !isset($data['filters'])) {
        exit(json_encode(["status" => "error", "message" => "Invalid data"]));
    }

    $filter_id = mysqli_real_escape_string($link, $data['filter_id']);
    $filterName = mysqli_real_escape_string($link, $data['filter_name']);
    $filter_page = mysqli_real_escape_string($link, $data['filter_page'] ?? '');
    $filters = json_encode($data['filters'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

    // Debugging logs
    error_log("Saving Filter: Name - $filterName, Page - $filter_page, Filters - $filters");

    if(empty($filter_id)){
    $sql = "INSERT INTO $db.user_filters (filter_name, filter_page, filters,created_by,status) 
            VALUES ('$filterName', '$filter_page', '$filters','$agent_id','1')";
    }else{
        $sql = "UPDATE $db.user_filters SET filter_name = '$filterName',  filters = '$filters', created_by = '$agent_id'  WHERE id = '$filter_id' ";
    }
    if(empty($filter_id)){
        $message = 'Filter saved successfully';
    }else{
        $message = 'Filter updated successfully';
    }
    if (mysqli_query($link, $sql)) {
        exit(json_encode(["status" => "success", "message" => $message]));
    } else {
        error_log("SQL Error: " . mysqli_error($link)); // Log error
        if(mysqli_error($link) == "Duplicate entry '$filterName' for key 'filter_name'"){
            $error = "Filter Name cannot be duplicate";
        }else{
            $error =  "Failed to save filter: " . mysqli_error($link);
        }
        exit(json_encode(["status" => "error", "message" => $error]));
    }
}

// Function to fetch filter data
function get_filter_data($filter_id) {
    global $db, $link;

    $filter_id = mysqli_real_escape_string($link, $filter_id);
    $sql = "SELECT filters FROM $db.user_filters WHERE id = '$filter_id' LIMIT 1";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        error_log("SQL Error: " . mysqli_error($link));
        return null;
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $json_data = trim($row['filters']);  

        error_log("Raw JSON from DB: " . $json_data);

        $decoded_data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        return $decoded_data;
    }

    return null; // No matching filter found
}

?>
