<?php
// view_document.php

// Get category and filename from query parameters
$category = $_GET['category'];
$filename = $_GET['filename'];

// Construct the file path
$filePath = "/var/www/html/ThirdPartyApi/new_knowledgebase_faq/uploads/$category/$filename";

// Check if the file exists
if (file_exists($filePath)) {
    // Set the appropriate headers based on the file type
    $mimeType = mime_content_type($filePath);
    header("Content-Type: $mimeType");
    header("Content-Disposition: inline; filename=\"$filename\"");
    readfile($filePath); // Output the file
} else {
    // File not found
    header("HTTP/1.0 404 Not Found");
    echo "File not found.";
}
?>