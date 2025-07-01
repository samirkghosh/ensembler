<?php
/***
    * Instagram Post Detail Page
    * Author: Aarti Ojha
    * Date: 09-12-2024
    * This file handles Instagram Chat display
    * To integrate Instagram messaging in your PHP application using the Instagram Business API, you need to follow several steps, including setting up your Instagram Developer account, creating a Instagram Business Account, configuring your webhook, and handling incoming and outgoing messages
    * 
    * Please do not modify this file without permission.
**/

// Fetch Instagram posts and comments from the database
include("../../config/web_mysqlconnect.php");

function getInstagramPosts($postId) {
    global $link;
    // Fetch posts
    $postQuery = "SELECT * FROM instagram_posts where id = '$postId' ORDER BY timestamp DESC";
    $postResult = mysqli_query($link, $postQuery);

    $posts = [];
    while ($post = mysqli_fetch_assoc($postResult)) {
        // Fetch comments for each post
        $postId = $post['id'];
        $commentQuery = "SELECT * FROM instagram_post_comments WHERE post_id = '$postId' ORDER BY timestamp DESC";
        $commentResult = mysqli_query($link, $commentQuery);

        $comments = [];
        while ($comment = mysqli_fetch_assoc($commentResult)) {
            $comments[] = $comment;
        }

        $post['comments'] = $comments;
        $posts[] = $post;
    }

    return $posts;
}
$postId = $_GET['ID'];
$instagramPosts = getInstagramPosts($postId);

// for update unread messege code
$sql="UPDATE $db.instagram_post_comments SET flag='1' WHERE post_id = '$postId'";
mysqli_query($link,$sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Posts</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <!--  CSS code -->
<link href="<?=$SiteURL?>public/css/channel_all_style.css" rel="stylesheet" type="text/css" />
</head>
<style type="text/css">
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    color: #333;
}

#posts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.post-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.post-media img,
.post-media video {
    width: 50%;
    height: 50%;
}

.post-content {
    padding: 15px;
}

.post-content .caption {
    font-size: 16px;
    color: #333;
    margin: 0 0 10px;
}

.post-content a {
    color: #007BFF;
    text-decoration: none;
}

.post-content .timestamp {
    font-size: 12px;
    color: #777;
}

.comments-section {
    padding: 15px;
    border-top: 1px solid #eaeaea;
}

.comments-section h3 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #333;
}

.comment {
    margin-bottom: 10px;
}

.comment strong {
    display: block;
    color: #007BFF;
    font-size: 14px;
}

.comment p {
    margin: 0;
    font-size: 14px;
    color: #333;
}

.comment .timestamp {
    font-size: 12px;
    color: #777;
}
/* General styles for comments section */
.comments-section {
    padding: 15px;
    border-top: 1px solid #eaeaea;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.comments-section h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: #333;
}

/* General comment styles */
.comment {
    display: flex;
    flex-direction: column;
    max-width: 70%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 10px;
    word-wrap: break-word;
    line-height: 1.5;
}

/* Owner's comments (right-aligned) */
.owner-comment {
    align-self: flex-end;
    background-color: #d1f5d3; /* Light green for owner */
    color: #2c5d33;
    text-align: right;
}

/* User's comments (left-aligned) */
.user-comment {
    align-self: flex-start;
    background-color: #f1f1f1; /* Light gray for users */
    color: #333;
    text-align: left;
}

/* Common styles for all comments */
.comment strong {
    font-size: 14px;
    color: #007bff;
}

.comment .timestamp {
    font-size: 12px;
    color: #777;
    margin-top: 5px;
}
</style>
<body>
    <div class="container">
        <h1>Instagram Posts</h1>
        <div id="posts">
            <?php foreach ($instagramPosts as $post): ?>
                <div class="post-card">
                    <div class="post-media">
                        <?php if ($post['media_type'] === 'IMAGE'): ?>
                            <img src="<?= $post['media_url']; ?>" alt="Instagram Post" style="height:50%;">
                        <?php elseif ($post['media_type'] === 'VIDEO'): ?>
                            <video controls>
                                <source src="<?= $post['media_url']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    </div>
                    <div class="post-content">
                        <p class="caption"><?= htmlspecialchars($post['caption']); ?></p>
                        <a href="<?= $post['permalink']; ?>" target="_blank">View on Instagram</a>
                        <p class="timestamp"><?= date("d M Y, h:i A", strtotime($post['timestamp'])); ?></p>
                    </div>
                    <div class="comments-section">
                    <h3>Comments</h3>
                    <?php if (!empty($post['comments'])): ?>
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="comment <?= ($comment['username'] === 'YOUR_INSTAGRAM_USERNAME') ? 'owner-comment' : 'user-comment'; ?>">
                                <p><strong><?= htmlspecialchars($comment['username']); ?></strong></p>
                                <p><?= htmlspecialchars($comment['text']); ?></p>
                                <span class="timestamp"><?= date("d M Y, h:i A", strtotime($comment['timestamp'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
