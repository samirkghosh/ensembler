<?php
/**
 * Instagram Post and Comments Handling
 * Author: Aarti Ojha
 * Date: 07-12-2024
 * Description: This file handles the Social Media API data for Instagram posts and comments, 
 *              fetching the incoming data via Instagram APIs and storing it in the database.
 *              This file is designed to store both Instagram posts and their associated comments.
 * Please do not modify this file without permission.
 */

// Include the database connection file
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file

/**
 * Main Function to Handle Instagram Incoming Data
 * Fetches posts and associated comments from the Instagram API
 * and stores them in the database.
 */

// Master database
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to get the related database name
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0){
    while ($company = $results->fetch_assoc()) {
        $childdb = $company['related_database_name'];

        echo " ############### Company Database Name : ".$childdb; echo"<br/>";

        instagram_incoming_data($childdb); //For outgoing Data 
    }
}else{
    echo "No company databases found."; die;
}

/**
 * Function: instagram_incoming_data
 * Description: Fetches Instagram posts and comments using the Facebook Graph API and stores the data 
 *              in the database (`instagram_posts` and `instagram_post_comments` tables).
 */
/**
 * Function: instagram_incoming_data
 * Description: Fetches Instagram posts and comments using the Facebook Graph API, stores the data
 *              in the database (`instagram_posts` and `instagram_post_comments` tables), and optionally
 *              saves media files locally if required.
 */
/**
 * Handles Instagram incoming data: posts and comments.
 */
function instagram_incoming_data($childdb) {
    global $link, $childdb, $webook_instagram_path;

    // Fetch Instagram connection configuration
    $config = getInstagramConfig($childdb, $link);
    if (!$config) {
        echo "No valid Instagram configuration found.<br>";
        return;
    }

    // Fetch posts from the Instagram API
    $posts = fetchInstagramPosts($config);
    if (!$posts) {
        echo "No posts found.<br>";
        return;
    }
   echo"<br/>"; print_r($posts); echo"<br/>";
    // Save posts and comments
    foreach ($posts as $post) {
        $postId = $post['id'];

        // Save post
        if (!saveInstagramPost($post, $config, $link, $webook_instagram_path,$childdb)) {
            continue;
        }

        // Fetch and save comments for the post
        $comments = fetchInstagramComments($postId, $config);
        if ($comments) {
            saveInstagramComments($postId, $comments, $link,$childdb);
        }
    }
}

/**
 * Fetches Instagram API configuration from the database.
 */
function getInstagramConfig($childdb, $link) {
    $sql = "SELECT * FROM $childdb.tbl_instagram_connection WHERE status = 1 AND debug = 1";
    $query = mysqli_query($link, $sql);
    return mysqli_fetch_assoc($query);
}

/**
 * Fetches Instagram posts from the API.
 */
function fetchInstagramPosts($config) {
    $url = "{$config['instagram_url']}{$config['app_id']}/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,username,timestamp&access_token={$config['access_token']}";
    echo "Post URL: $url<br><br>";

     // Step 3: Initialize cURL to fetch Instagram posts
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Step 4: Execute the cURL request
    $response = curl_exec($ch);
    curl_close($ch);

    // Step 5: Decode the JSON response
    $responseData = json_decode($response, true);
    return $responseData['data'];
}

/**
 * Fetches Instagram comments for a specific post.
 */
function fetchInstagramComments($postId, $config) {
    $url = "{$config['instagram_url']}{$postId}/comments?fields=id,text,username,timestamp&access_token={$config['access_token']}";
    echo "Comment URL: $url<br>";

    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['data'] ?? null;
}

/**
 * Saves an Instagram post to the database.
 */
function saveInstagramPost($post, $config, $link, $mediaPath,$childdb) {
    global $link,$db;

    $postId = $post['id'];

    // Check if post exists
    $checkSql = "SELECT COUNT(*) AS cnt FROM $childdb.instagram_posts WHERE id = '$postId'";
    $exists = mysqli_fetch_assoc(mysqli_query($link, $checkSql))['cnt'] > 0;
    if ($exists) {
        echo "Post ID $postId already exists. Skipping.<br>";
        return false;
    }

    // Save media locally (optional)
    $localPath = null;
    if ($post['media_type'] === 'IMAGE') {
        $localPath = saveMediaLocally($post['media_url'], $mediaPath);
    }

    // Insert post into the database
    $caption = mysqli_real_escape_string($link, $post['caption'] ?? '');
    $mediaType = $post['media_type'];
    $mediaUrl = $post['media_url'];
    $thumbnailUrl = $post['thumbnail_url'] ?? null;
    $permalink = $post['permalink'];
    $timestamp = date("Y-m-d H:i:s", strtotime($post['timestamp']));
    $username = $post['username'];
    $sql = "INSERT INTO $childdb.instagram_posts (id, caption, media_type, media_url, local_media_path, thumbnail_url, permalink, timestamp,send_from)
            VALUES ('$postId', '$caption', '$mediaType', '$mediaUrl', '$localPath', '$thumbnailUrl', '$permalink', '$timestamp','$username')";
    echo $sql;
    return mysqli_query($link, $sql);
}

/**
 * Saves Instagram comments to the database.
 */
function saveInstagramComments($postId, $comments, $link,$childdb) {
    global $link,$db;
    foreach ($comments as $comment) {
        $commentId = $comment['id'];
        $text = mysqli_real_escape_string($link, $comment['text']);
        $username = $comment['username'];
        $timestamp = date("Y-m-d H:i:s", strtotime($comment['timestamp']));

        $sql = "INSERT INTO $childdb.instagram_post_comments (id, post_id, text, username, timestamp)
                VALUES ('$commentId', '$postId', '$text', '$username', '$timestamp')
                ON DUPLICATE KEY UPDATE 
                    text = '$text', 
                    username = '$username',
                    timestamp = '$timestamp'";
        mysqli_query($link, $sql);
    }
}

/**
 * Saves media locally and returns the local file path.
 */
function saveMediaLocally($url, $basePath) {
    $date = date('dmy');
    $path = $basePath . $date;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $fileName = getFileNameFromUrl($url);
    $localPath = "$path/$fileName";
    file_put_contents($localPath, file_get_contents($url));
    return $localPath;
}
/**
 * Function to extract the filename from a media URL
 * @param string $mediaUrl The media URL
 * @return string|null The extracted filename or null if not found
 */
function getFileNameFromUrl($mediaUrl) {
    // Parse the URL to get the path
    $parsedUrl = parse_url($mediaUrl);

    if (isset($parsedUrl['path'])) {
        // Extract the filename from the path
        $path = $parsedUrl['path'];
        $fileName = basename($path);

        // Handle cases where Instagram adds `.webp` extensions but media is `.jpg`
        if (strpos($fileName, '?') !== false) {
            $fileName = explode('?', $fileName)[0]; // Remove query parameters
        }

        // Optional: Change the extension to `.jpg` if `dst-jpg` is in the URL
        if (strpos($mediaUrl, 'dst-jpg') !== false) {
            $fileName = preg_replace('/\.[a-z]+$/', '.jpg', $fileName); // Replace extension
        }

        return $fileName;
    }

    return null; // Return null if no path is found
}

?>
