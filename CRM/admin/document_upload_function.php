<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  24-april-2025
 * Description: Function to upload and import customer details using Excel sheet
*/

// Add security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CSRF validation function
function validateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        logSecurityEvent('csrf_validation_failed', 'Missing CSRF token');
        return false;
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logSecurityEvent('csrf_validation_failed', 'Invalid CSRF token');
        return false;
    }
    
    return true;
}

// Validate CSRF token for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken()) {
        die('CSRF token validation failed');
    }
}

include("../../config/web_mysqlconnect.php"); // Connection to database

// Input validation functions
function validateInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function validateFileName($filename) {
    // Remove any directory components
    $filename = basename($filename);
    // Remove any non-alphanumeric characters except ._- 
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

function validateFileType($file) {
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    return in_array($mimeType, $allowedTypes);
}

function validateFileSize($file, $maxSize = 5242880) { // 5MB max
    return $file['size'] <= $maxSize;
}

function secureFileUpload($file, $uploadDir, $type) {
    global $link;
    
    // Validate file
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }
    
    // Validate file type
    if (!validateFileType($file)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }
    
    // Validate file size
    if (!validateFileSize($file)) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }
    
    // Generate secure filename
    $originalName = validateFileName($file['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;
    
    // Move file to target directory
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }
    
    // Set secure file permissions
    chmod($targetPath, 0644);
    
    // Log file upload
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');
    
    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }
    
    mysqli_stmt_bind_param($stmt, "siss", $fileName, $uploaded_by, $uploaded_date, $type);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
    
    // Handle different upload types
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload_customer_json':
                if (isset($_POST['customers'])) {
                    $customerList = json_decode($_POST['customers'], true);
                    $upload_result = secureFileUpload($_FILES['customer_excel'], 'uploads/customers/', 'customer');
                    if ($upload_result['success']) {
                        import_customers_from_json($customerList, $upload_result['file_path']);
                    } else {
                        echo json_encode($upload_result);
                    }
                }
                break;
                
            case 'upload_category_json':
                if (isset($_POST['categories'])) {
                    $categoryList = json_decode($_POST['categories'], true);
                    $upload_result = secureFileUpload($_FILES['category_excel'], 'uploads/category/', 'category');
                    if ($upload_result['success']) {
                        import_categories_from_json($categoryList, $upload_result['file_path']);
                    } else {
                        echo json_encode($upload_result);
                    }
                }
                break;
                
            case 'upload_county_json':
                if (isset($_POST['county'])) {
                    $countyList = json_decode($_POST['county'], true);
                    $upload_result = secureFileUpload($_FILES['county_excel'], 'uploads/county/', 'county');
                    if ($upload_result['success']) {
                        import_county_from_json($countyList, $upload_result['file_path']);
                    } else {
                        echo json_encode($upload_result);
                    }
                }
                break;
                
            case 'upload_subcounty_json':
                if (isset($_POST['records'])) {
                    $subcountyList = json_decode($_POST['records'], true);
                    $upload_result = secureFileUpload($_FILES['subcounty_excel'], 'uploads/subcounty/', 'subcounty');
                    if ($upload_result['success']) {
                        import_subcounty_from_json($subcountyList, $upload_result['file_path']);
                    } else {
                        echo json_encode($upload_result);
                    }
                }
                break;
                
            case 'upload_subcategory_data':
                if (isset($_POST['records'])) {
                    $subcategoryList = json_decode($_POST['records'], true);
                    $upload_result = secureFileUpload($_FILES['subcategory_excel'], 'uploads/subcategory/', 'subcategory');
                    if ($upload_result['success']) {
                        import_subcategory_from_json($subcategoryList, $upload_result['file_path']);
                    } else {
                        echo json_encode($upload_result);
                    }
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }
}

/**
 * Function to import customer data from JSON after file upload
 */
function import_customers_from_json($customers, $file_path) {
    global $db, $link;

    $userid = $_SESSION['userid'];
    $failed_rows = [];
    $existing_mobiles = [];
    $existing_emails = [];

    // Fetch all existing mobiles and emails from the database
    $result = mysqli_query($link, "SELECT mobile, email FROM `$db`.`web_accounts`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_mobiles[] = $row['mobile'];
            $existing_emails[] = $row['email'];
        }
    }

    $batch_mobiles = [];
    $batch_emails = [];

    // First pass: Validate all customers for duplicates
    foreach ($customers as $index => $cust) {
        $mobile = $cust['mobile'] ?? '';
        $email = $cust['email'] ?? '';

        if (empty($mobile)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => 'Mobile number is required'
            ];
            continue;
        }

        if (
            in_array($mobile, $existing_mobiles) || 
            in_array($mobile, $batch_mobiles) ||
            (!empty($email) && (in_array($email, $existing_emails) || in_array($email, $batch_emails)))
        ) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => 'Duplicate mobile or email found '.$mobile.' '.$email
            ];
            continue;
        }

        // Add to batch arrays for further duplicate checks inside batch
        $batch_mobiles[] = $mobile;
        if (!empty($email)) {
            $batch_emails[] = $email;
        }
    }

    // If any duplicates or missing mobiles are found, stop the import
    if (count($failed_rows) > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate records found. Upload failed.',
            'failed_rows' => $failed_rows
        ]);
        return;
    }

    // Second pass: Insert into database (only if all data is clean)
    foreach ($customers as $cust) {
        $county_name = trim($cust['county'] ?? '');
        $sub_county_name = trim($cust['sub_county'] ?? '');
    
        // --- Get or Insert County (web_city) ---
        $county_id = null;
        if (!empty($county_name)) {
            $county_result = mysqli_query($link, "SELECT id FROM $db.web_city WHERE city = '" . mysqli_real_escape_string($link, $county_name) . "'");
            if ($county_result && mysqli_num_rows($county_result) > 0) {
                $county_row = mysqli_fetch_assoc($county_result);
                $county_id = $county_row['id'];
            } else {
                // Insert new county
                $sql = "INSERT INTO $db.`web_city` (`city`, `status`) VALUES ('$county_name', '1')";
                mysqli_query($link, $sql);
                $county_id = mysqli_insert_id($link);
            }
        }
    
        // --- Get or Insert Sub County (web_Village) ---
        $sub_county_id = null;
        if (!empty($sub_county_name)) {
            $sub_county_result = mysqli_query($link, "SELECT id FROM web_Village WHERE vVillage = '" . mysqli_real_escape_string($link, $sub_county_name) . "'");
            if ($sub_county_result && mysqli_num_rows($sub_county_result) > 0) {
                $sub_county_row = mysqli_fetch_assoc($sub_county_result);
                $sub_county_id = $sub_county_row['id'];
            } else {
                $sql = "INSERT INTO $db.`web_Village` (`vVillage`, `iDistrictID`, `status`, `createdby`, `createdon`, `V_Description`) VALUES ('$sub_county_name', '$county_id', '1', '$userid', NOW(), '')";
                mysqli_query($link,$sql);
                $sub_county_id = mysqli_insert_id($link);
            }
        }



        $first_name = $cust['first_name'] ?? '';
        $last_name = $cust['last_name'] ?? '';
        $mobile = $cust['mobile'] ?? '';
        $phone = $cust['alternate_mobile'] ?? '';
        $address = $cust['address'] ?? '';
        $email = $cust['email'] ?? '';
        $fbhandle = $cust['facebook'] ?? '';
        $twitterhandle = $cust['x_handle'] ?? '';
        $gender = $cust['gender'] ?? '';
        $company_registration = $cust['company_registration'] ?? '';
        $sms_number = $cust['sms_number'] ?? '';
        $whatsapp = $cust['whatsapp'] ?? '';
        $instagram = $cust['instagram'] ?? '';
        $messenger = $cust['messenger'] ?? '';
        $regional = $county;
        $nationality = $cust['nationality'] ?? '';
        $company_name = $cust['company_name'] ?? '';
        $priority = $cust['priority'] ?? '';
        $fname = $first_name . ' ' . $last_name;
        $v_Location = '';


        $query = "INSERT INTO `$db`.`web_accounts` 
            (`fname`, `phone`, `mobile`, `address`, `v_Location`, `district`, `v_Village`, 
            `email`, `fbhandle`, `twitterhandle`, `gender`, `customertype`, `company_registration`, 
            `smshandle`, `whatsapphandle`, `instagramhandle`, `messengerhandle`, `regional`, 
            `nationality`, `company_name`, `priority_user`, `v_passwd`) 
        VALUES 
            ('" . mysqli_real_escape_string($link, $fname) . "', 
             '" . mysqli_real_escape_string($link, $mobile) . "', 
             '" . mysqli_real_escape_string($link, $phone) . "', 
             '" . mysqli_real_escape_string($link, $address) . "', 
             '" . mysqli_real_escape_string($link, $v_Location) . "', 
             '" . mysqli_real_escape_string($link, $county_id) . "', 
             '" . mysqli_real_escape_string($link, $sub_county_id) . "', 
             '" . mysqli_real_escape_string($link, $email) . "', 
             '" . mysqli_real_escape_string($link, $fbhandle) . "', 
             '" . mysqli_real_escape_string($link, $twitterhandle) . "', 
             '" . mysqli_real_escape_string($link, $gender) . "', 
             '', 
             '" . mysqli_real_escape_string($link, $company_registration) . "', 
             '" . mysqli_real_escape_string($link, $sms_number) . "', 
             '" . mysqli_real_escape_string($link, $whatsapp) . "', 
             '" . mysqli_real_escape_string($link, $instagram) . "', 
             '" . mysqli_real_escape_string($link, $messenger) . "', 
             '" . mysqli_real_escape_string($link, $regional) . "', 
             '" . mysqli_real_escape_string($link, $nationality) . "', 
             '" . mysqli_real_escape_string($link, $company_name) . "', 
             '" . mysqli_real_escape_string($link, $priority) . "', 
             '')";

           
        mysqli_query($link, $query);
    }

    // Final success message
    echo json_encode([
        'success' => true,
        'message' => 'All data imported successfully.',
        'file_path' => $file_path
    ]);
}
function import_categories_from_json($categoryList, $file_path) {
    global $db, $link;

    $userid = $_SESSION['userid'] ?? 0;
    $failed_rows = [];
    $existing_category = [];

    // Fetch all existing categories from the database
    $result = mysqli_query($link, "SELECT category FROM `$db`.`web_category`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_category[] = strtolower(trim($row['category']));
        }
    }

    $batch_category = [];

    // First pass: Validate for duplicate categories
    foreach ($categoryList as $index => $cat) {
        $category = trim($cat['category'] ?? '');

        if ($category === '') {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => 'Category is required.'
            ];
            continue;
        }

        $lc_category = strtolower($category);
        if (in_array($lc_category, $existing_category) || in_array($lc_category, $batch_category)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Duplicate Category: $category"
            ];
            continue;
        }

        $batch_category[] = $lc_category;
    }

    // If validation failed
    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate or missing data found.',
            'failed_rows' => $failed_rows
        ]);
        return;
    }

    // Second pass: Insert valid data
    foreach ($categoryList as $cat) {
        $category = trim($cat['category'] ?? '');
        $type = trim($cat['complaint_type'] ?? 'None');
        $description = trim($cat['description'] ?? '');
        $createdon = date('Y-m-d H:i:s');

        $query = "INSERT INTO `$db`.`web_category` 
            (`category`, `type`, `status`, `createdby`, `createdon`, `V_Description`)
            VALUES (?, ?, 1, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, "ssiss", $category, $type, $userid, $createdon, $description);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'All data imported successfully.',
        'file_path' => $file_path
    ]);
}
function import_county_from_json($countyList, $file_path) {
    global $db, $link;

    $userid = $_SESSION['userid'] ?? 0;
    $failed_rows = [];
    $existing_county = [];

    // Fetch already existing counties
    $result = mysqli_query($link, "SELECT city FROM `$db`.`web_city`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_county[] = strtolower(trim($row['city']));
        }
    }

    $batch_county = [];

    // First, validate all data before inserting
    foreach ($countyList as $index => $cat) {
        $county = trim($cat['county'] ?? '');

        if ($county === '') {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Empty County Name"
            ];
            continue;
        }

        $lc_county = strtolower($county);
        if (in_array($lc_county, $existing_county)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "County already exists: $county"
            ];
            continue;
        }

        if (in_array($lc_county, $batch_county)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Duplicate County in File: $county"
            ];
            continue;
        }

        $batch_county[] = $lc_county;
    }

    // If any failures found, return them
    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate or invalid data found.',
            'failed_rows' => $failed_rows
        ]);
        return;
    }

    // Proceed with insertions
    foreach ($countyList as $cat) {
        $county = trim($cat['county'] ?? '');

        if ($county === '') continue; // safety check

        $query = "INSERT INTO `$db`.`web_city` (`city`, `status`) VALUES (?, 1)";

        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $county);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            // Optional: log error if insert fails
            $failed_rows[] = [
                'row' => $county,
                'errors' => "Insert failed: " . mysqli_error($link)
            ];
        }
    }

    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Some records could not be inserted.',
            'failed_rows' => $failed_rows
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'All data imported successfully.',
            'file_path' => $file_path
        ]);
    }
}
function import_subcounty_from_json($subcountyList, $file_path) {
    global $db, $link;

    $userid = $_SESSION['userid'] ?? 0;
    $failed_rows = [];
    $existing_subcounties = [];

    // Fetch already existing subcounties (case-insensitive match)
    $result = mysqli_query($link, "SELECT vVillage FROM `$db`.`web_Village`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_subcounties[] = strtolower(trim($row['vVillage']));
        }
    }

    $batch_subcounty = [];

    // Validate data
    foreach ($subcountyList as $index => $row) {
        $county     = trim($row['county'] ?? '');
        $subcounty  = trim($row['subcounty'] ?? '');
        $desc       = trim($row['description'] ?? '');

        if ($county === '' || $subcounty === '') {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "County and Subcounty are required."
            ];
            continue;
        }

        $lc_subcounty = strtolower($subcounty);
        if (in_array($lc_subcounty, $existing_subcounties)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Subcounty already exists: $subcounty"
            ];
            continue;
        }

        if (in_array($lc_subcounty, $batch_subcounty)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Duplicate Subcounty in file: $subcounty"
            ];
            continue;
        }

        $batch_subcounty[] = $lc_subcounty;
    }

    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate or invalid data found.',
            'failed_rows' => $failed_rows
        ]);
        return;
    }

    // Process insertion
    foreach ($subcountyList as $row) {
        $county     = trim($row['county'] ?? '');
        $subcounty  = trim($row['subcounty'] ?? '');
        $desc       = trim($row['description'] ?? '');

        if ($county === '' || $subcounty === '') continue;

        // Check if county (web_city) exists
        $district_id = 0;
        $check = mysqli_query($link, "SELECT id FROM `$db`.`web_city` WHERE LOWER(city) = '" . strtolower($county) . "' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $rowData = mysqli_fetch_assoc($check);
            $district_id = $rowData['id'];
        } else {
            // Insert new county
            $stmt = mysqli_prepare($link, "INSERT INTO `$db`.`web_city` (`city`, `status`) VALUES (?, 1)");
            mysqli_stmt_bind_param($stmt, "s", $county);
            if (mysqli_stmt_execute($stmt)) {
                $district_id = mysqli_insert_id($link);
            }
            mysqli_stmt_close($stmt);
        }

        // Insert subcounty
        $stmt = mysqli_prepare($link, "INSERT INTO `$db`.`web_Village` (`vVillage`, `iDistrictID`, `status`, `createdby`, `createdon`, `V_Description`) VALUES (?, ?, 1, ?, NOW(), ?)");
        mysqli_stmt_bind_param($stmt, "siis", $subcounty, $district_id, $userid, $desc);
        if (!mysqli_stmt_execute($stmt)) {
            $failed_rows[] = [
                'row' => $subcounty,
                'errors' => "Insert failed: " . mysqli_error($link)
            ];
        }
        mysqli_stmt_close($stmt);
    }

    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Some subcounties could not be inserted.',
            'failed_rows' => $failed_rows
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'All subcounties imported successfully.',
            'file_path' => $file_path
        ]);
    }
}
function import_subcategory_from_json($subcategoryList, $file_path) {
    global $db, $link;

    $userid = $_SESSION['userid'] ?? 0;
    $failed_rows = [];
    $existing_subcategories = [];

    // Fetch existing subcategories for duplication check
    $result = mysqli_query($link, "SELECT subcategory FROM `$db`.`web_subcategory`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_subcategories[] = strtolower(trim($row['subcategory']));
        }
    }

    $batch_subcategories = [];

    // Validate each row
    foreach ($subcategoryList as $index => $row) {
        $category   = trim($row['category'] ?? '');
        $subcategory = trim($row['subcategory'] ?? '');
        $description = trim($row['description'] ?? '');
        $res1 = trim($row['escalation_time'] ?? '');
        $res2 = trim($row['second_escalation'] ?? '');
        $res3 = trim($row['third_escalation'] ?? '');
        $lvl1 = trim($row['level1_users'] ?? '');
        $lvl2 = trim($row['level2_users'] ?? '');
        $lvl3 = trim($row['level3_users'] ?? '');

       
        if ($category === '' || $subcategory === '' || $res1 === '') {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Category, Subcategory, and Escalation Time in Hours are required."
            ];
            continue;
        }

        $lc_subcategory = strtolower($subcategory);
        if (in_array($lc_subcategory, $existing_subcategories)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Subcategory already exists: $subcategory"
            ];
            continue;
        }

        if (in_array($lc_subcategory, $batch_subcategories)) {
            $failed_rows[] = [
                'row' => $index + 1,
                'errors' => "Duplicate Subcategory in file: $subcategory"
            ];
            continue;
        }

        $batch_subcategories[] = $lc_subcategory;
    }

    if (!empty($failed_rows)) {
        echo json_encode([
            'success' => false,
            'message' => 'Duplicate or invalid data found.',
            'failed_rows' => $failed_rows
        ]);
        return;
    }

    // Insert into DB
    foreach ($subcategoryList as $index => $row) {
        $category   = trim($row['category'] ?? '');
        $subcategory = trim($row['subcategory'] ?? '');
        $description = trim($row['description'] ?? '');
        $res1 = trim($row['escalation_time'] ?? '');
        $res2 = trim($row['second_escalation'] ?? '');
        $res3 = trim($row['third_escalation'] ?? '');
        $lvl1 = trim($row['level1_users'] ?? '');
        $lvl2 = trim($row['level2_users'] ?? '');
        $lvl3 = trim($row['level3_users'] ?? '');

        if ($category === '' || $subcategory === '' || $res1 === '') {
            continue;
        }

        // Check if category exists
        $category_id = 0;
        $check = mysqli_query($link, "SELECT id FROM `$db`.`web_category` WHERE LOWER(category) = '" . strtolower($category) . "' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $rowData = mysqli_fetch_assoc($check);
            $category_id = $rowData['id'];
        } else {
            // Insert new category
            $stmt = mysqli_prepare($link, "INSERT INTO `$db`.`web_category` (`category`, `type`, `status`, `createdby`, `createdon`) VALUES (?, 'general', 1, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "si", $category, $userid);
            if (mysqli_stmt_execute($stmt)) {
                $category_id = mysqli_insert_id($link);
            } 
            mysqli_stmt_close($stmt);
        }

        // Insert into subcategory
        $query = "INSERT INTO `$db`.`web_subcategory` (`subcategory`, `category`, `resolution_time_in_hours`, `second_resolution_time`, `third_resolution_time`, `level1_users`, `level2_users`, `level3_users`, `V_Description`, `status`, `createdby`, `createdon`) VALUES ('$subcategory', '$category_id', '$res1', '$res2', '$res3', '$lvl1', '$lvl2', '$lvl3', '$description', 1, '$userid', NOW())";

        $stmt = mysqli_prepare($link,$query);
        if (!mysqli_stmt_execute($stmt)) {
            $failed_rows[] = [
                'row' => $subcategory,
                'errors' => "Insert failed: " . mysqli_error($link)
            ];
        } 
        mysqli_stmt_close($stmt);
    }

    // Final output
    if (!empty($failed_rows)) {
       echo json_encode([
            'success' => false,
            'message' => 'Some subcategories could not be inserted.',
            'failed_rows' => $failed_rows
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'All subcategories imported successfully.',
            'file_path' => $file_path
        ]);
    }

}

function customer_excel_file() {
    global $db, $link;

    // Directory to save uploaded file
    $uploadDir = 'uploads/customers/';
    
    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }

    // Validate file upload
    if (!isset($_FILES['customer_excel']) || $_FILES['customer_excel']['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file type
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['customer_excel']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }

    // Validate file size (5MB max)
    if ($_FILES['customer_excel']['size'] > 5242880) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }

    // Generate secure filename
    $originalName = basename($_FILES['customer_excel']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['customer_excel']['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }

    // Set secure file permissions
    chmod($targetPath, 0644);

    // Save file details into the database
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, 'customer')");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sis", $fileName, $uploaded_by, $uploaded_date);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

function category_excel_file() {
    global $db, $link;
    
    // Directory to save uploaded file
    $uploadDir = 'uploads/category/';
    
    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }

    // Validate file upload
    if (!isset($_FILES['category_excel']) || $_FILES['category_excel']['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file type
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['category_excel']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }

    // Validate file size (5MB max)
    if ($_FILES['category_excel']['size'] > 5242880) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }

    // Generate secure filename
    $originalName = basename($_FILES['category_excel']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['category_excel']['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }

    // Set secure file permissions
    chmod($targetPath, 0644);

    // Save file details into the database
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, 'category')");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sis", $fileName, $uploaded_by, $uploaded_date);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

function county_excel_file() {
    global $db, $link;
    
    // Directory to save uploaded file
    $uploadDir = 'uploads/county/';
    
    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }

    // Validate file upload
    if (!isset($_FILES['county_excel']) || $_FILES['county_excel']['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file type
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['county_excel']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }

    // Validate file size (5MB max)
    if ($_FILES['county_excel']['size'] > 5242880) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }

    // Generate secure filename
    $originalName = basename($_FILES['county_excel']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['county_excel']['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }

    // Set secure file permissions
    chmod($targetPath, 0644);

    // Save file details into the database
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, 'county')");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sis", $fileName, $uploaded_by, $uploaded_date);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

function subcounty_excel_file() {
    global $db, $link;
    
    // Directory to save uploaded file
    $uploadDir = 'uploads/subcounty/';
    
    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }

    // Validate file upload
    if (!isset($_FILES['subcounty_excel']) || $_FILES['subcounty_excel']['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file type
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['subcounty_excel']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }

    // Validate file size (5MB max)
    if ($_FILES['subcounty_excel']['size'] > 5242880) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }

    // Generate secure filename
    $originalName = basename($_FILES['subcounty_excel']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['subcounty_excel']['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }

    // Set secure file permissions
    chmod($targetPath, 0644);

    // Save file details into the database
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, 'subcounty')");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sis", $fileName, $uploaded_by, $uploaded_date);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

function subcategory_excel_file() {
    global $db, $link;
    
    // Directory to save uploaded file
    $uploadDir = 'uploads/subcategory/';
    
    // Check if the directory exists, if not, create it
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return [
                'success' => false,
                'message' => 'Error creating upload directory'
            ];
        }
    }

    // Validate file upload
    if (!isset($_FILES['subcategory_excel']) || $_FILES['subcategory_excel']['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }

    // Validate file type
    $allowedTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['subcategory_excel']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Invalid file type. Only Excel and CSV files are allowed'
        ];
    }

    // Validate file size (5MB max)
    if ($_FILES['subcategory_excel']['size'] > 5242880) {
        return [
            'success' => false,
            'message' => 'File size exceeds limit'
        ];
    }

    // Generate secure filename
    $originalName = basename($_FILES['subcategory_excel']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $timestamp = date('YmdHis');
    $randomString = bin2hex(random_bytes(8));
    $fileName = $nameWithoutExt . '_' . $timestamp . '_' . $randomString . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['subcategory_excel']['tmp_name'], $targetPath)) {
        return [
            'success' => false,
            'message' => 'Error moving uploaded file'
        ];
    }

    // Set secure file permissions
    chmod($targetPath, 0644);

    // Save file details into the database
    $uploaded_by = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $uploaded_date = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($link, "INSERT INTO uploaded_file (document_name, uploaded_by, uploaded_date, type) VALUES (?, ?, ?, 'subcategory')");
    if (!$stmt) {
        unlink($targetPath); // Delete file if database insert fails
        return [
            'success' => false,
            'message' => 'Database error'
        ];
    }

    mysqli_stmt_bind_param($stmt, "sis", $fileName, $uploaded_by, $uploaded_date);
    if (!mysqli_stmt_execute($stmt)) {
        unlink($targetPath); // Delete file if database insert fails
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'message' => 'Error saving file details'
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'file_path' => $targetPath
    ];
}

?>
