<?php
/**
* Auth: vastvikta nishad
* Date- 20-03-2025
* Description: this file contains function for the sentiment dashboard
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../config/web_mysqlconnect.php");
/* get sentiment related data from autodial agent log */
function get_sentiments_recording($start_date, $end_date) {
    global $db_asterisk, $link;
    $table = "$db_asterisk.autodial_agent_log";

    /* SQL query to count different sentiment  types and total records*/
    $query = "
        SELECT 
            SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
            SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
            SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
            COUNT(sentiment) AS total 
        FROM $table 
        WHERE event_time BETWEEN '$start_date' AND '$end_date'";  

    /* Execute the query and fetch the result as an associative array*/
    $result = mysqli_fetch_assoc(mysqli_query($link, $query));

    /* Return the counts, ensuring NULL values are replaced with 0*/
    return [
        'positive' => $result['positive'] ?? 0,
        'negative' => $result['negative'] ?? 0,
        'neutral' => $result['neutral'] ?? 0,
        'total' => $result['total'] ?? 0
    ];
}
/* get sentiment related data from email */
function get_sentiments_email($start_date, $end_date) {
    global $db, $link;
    $table = "$db.web_email_information";    
    /* SQL query to count different sentiment  types and total records*/
    $query = "
        SELECT 
            SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
            SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
            SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
            COUNT(sentiment) AS total 
        FROM $table 
        WHERE sentiment != '' AND d_email_date BETWEEN '$start_date' AND '$end_date'";  

        /* Execute the query and fetch the result as an associative array*/
        $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    
        /* Return the counts, ensuring NULL values are replaced with 0*/
        return [
            'positive' => $result['positive'] ?? 0,
            'negative' => $result['negative'] ?? 0,
            'neutral' => $result['neutral'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
}
/* get sentiment related data for whatsapp */
function get_sentiments_whatsapp($start_date, $end_date) {
    global $db, $link;
    $table = "$db.whatsapp_in_queue";
     /* SQL query to count different sentiment  types and total records*/
        $query = "
        SELECT 
            SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
            SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
            SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
            COUNT(sentiment) AS total 
        FROM $table 
        WHERE sentiment != '' AND create_date BETWEEN '$start_date' AND '$end_date'";  

        /* Execute the query and fetch the result as an associative array*/
        $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    
        /* Return the counts, ensuring NULL values are replaced with 0*/
        return [
            'positive' => $result['positive'] ?? 0,
            'negative' => $result['negative'] ?? 0,
            'neutral' => $result['neutral'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
}
/* get sentiment related data from instagram table */
function get_sentiments_instagram($start_date, $end_date) {
    global $db, $link;
    $table = "$db.instagram_in_queue";
     /* SQL query to count different sentiment  types and total records*/
    $query = "
    SELECT 
        SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
        SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
        SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
        COUNT(sentiment) AS total 
    FROM $table 
    WHERE sentiment != '' AND create_date BETWEEN '$start_date' AND '$end_date'";  

    /* Execute the query and fetch the result as an associative array*/
    $result = mysqli_fetch_assoc(mysqli_query($link, $query));

    /* Return the counts, ensuring NULL values are replaced with 0*/
    return [
        'positive' => $result['positive'] ?? 0,
        'negative' => $result['negative'] ?? 0,
        'neutral' => $result['neutral'] ?? 0,
        'total' => $result['total'] ?? 0
    ];
}
/* get sentiment related data from facebook messenger */
function get_sentiments_messenger($start_date, $end_date) {
    global $db, $link;
    $table = "$db.messenger_in_queue";
     /* SQL query to count different sentiment  types and total records*/
    $query = "
    SELECT 
        SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
        SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
        SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
        COUNT(sentiment) AS total 
    FROM $table 
    WHERE sentiment != ''  create_date BETWEEN '$start_date' AND '$end_date'";  

    /* Execute the query and fetch the result as an associative array*/
    $result = mysqli_fetch_assoc(mysqli_query($link, $query));

    /* Return the counts, ensuring NULL values are replaced with 0*/
    return [
        'positive' => $result['positive'] ?? 0,
        'negative' => $result['negative'] ?? 0,
        'neutral' => $result['neutral'] ?? 0,
        'total' => $result['total'] ?? 0
    ];
}
/* get sentiment related data from twitter */
function get_sentiments_twitter($start_date, $end_date) {
    global $db, $link;
    $table = "$db.tbl_tweet";
     /* SQL query to count different sentiment  types and total records*/
    $query = "
    SELECT 
        SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
        SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
        SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
        COUNT(sentiment) AS total 
        FROM $table 
        WHERE sentiment != '' AND d_TweetDateTime BETWEEN '$start_date' AND '$end_date'";  

        /* Execute the query and fetch the result as an associative array*/
        $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    
        /* Return the counts, ensuring NULL values are replaced with 0*/
        return [
            'positive' => $result['positive'] ?? 0,
            'negative' => $result['negative'] ?? 0,
            'neutral' => $result['neutral'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
}
/* get sentiment related data from facebook table */
function get_sentiments_facebook($start_date, $end_date) {
    global $db, $link;
    $table = "$db.tbl_facebook";
     /* SQL query to count different sentiment  types and total records*/
    $query = "
    SELECT 
        SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
        SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
        SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
        COUNT(sentiment) AS total 
        FROM $table 
        WHERE sentiment != '' AND createddate BETWEEN '$start_date' AND '$end_date'";  

        /* Execute the query and fetch the result as an associative array*/
        $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    
        /* Return the counts, ensuring NULL values are replaced with 0*/
        return [
            'positive' => $result['positive'] ?? 0,
            'negative' => $result['negative'] ?? 0,
            'neutral' => $result['neutral'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
}
/* get sentiment related data from overall bot chat session (web_chatting) */
function get_sentiments_chat($start_date, $end_date) {
    global $db, $link;
    $table = "$db.overall_bot_chat_session";
     /* SQL query to count different sentiment  types and total records*/
    $query = "
    SELECT 
        SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) AS positive, 
        SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) AS negative, 
        SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) AS neutral,  
        COUNT(sentiment) AS total 
        FROM $table 
        WHERE sentiment != '' AND createdDatetime BETWEEN '$start_date' AND '$end_date'";  

        /* Execute the query and fetch the result as an associative array*/
        $result = mysqli_fetch_assoc(mysqli_query($link, $query));
    
        /* Return the counts, ensuring NULL values are replaced with 0*/
        return [
            'positive' => $result['positive'] ?? 0,
            'negative' => $result['negative'] ?? 0,
            'neutral' => $result['neutral'] ?? 0,
            'total' => $result['total'] ?? 0
        ];
}
/* sending data in json encoded form for the sentiment doughnut chart */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'dashboard') {
    /*  setting start and  end date*/ 
    $start_date = isset($_POST['startdatetime']) ? date("Y-m-d H:i:s", strtotime($_POST['startdatetime'])) : date("Y-m-01 00:00:00");
    $end_date = isset($_POST['enddatetime']) ? date("Y-m-d H:i:s", strtotime($_POST['enddatetime'])) : date("Y-m-d 23:59:59");
    /*  getting data on the basis of the start and end date */ 
    $sentiments_recording = get_sentiments_recording($start_date, $end_date);
    $sentiments_email = get_sentiments_email($start_date, $end_date);
    $sentiments_facebook = get_sentiments_facebook($start_date, $end_date);
    $sentiments_twitter = get_sentiments_twitter($start_date, $end_date);
    $sentiments_whatsapp = get_sentiments_whatsapp($start_date, $end_date);
    $sentiments_messenger = get_sentiments_messenger($start_date, $end_date);
    $sentiments_chat = get_sentiments_chat($start_date, $end_date);
    $sentiments_instagram = get_sentiments_instagram($start_date, $end_date);
    /* sending data in json format*/
    echo json_encode([
        'recording' => $sentiments_recording,
        'email' => $sentiments_email,
        'facebook' => $sentiments_facebook,
        'twitter' => $sentiments_twitter,
        'whatsapp' => $sentiments_whatsapp,
        'messenger' => $sentiments_messenger,
        'chat' => $sentiments_chat,
        'instagram' => $sentiments_instagram
    ]);
}
/* sending data in the json format for the channelwise sentiment  for the pie chart */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'dashboard_pie') {
    /*  setting start and  end date*/ 
    $start_date = isset($_POST['startdatetime']) ? date("Y-m-d H:i:s", strtotime($_POST['startdatetime'])) : date("Y-m-01 00:00:00");
    $end_date = isset($_POST['enddatetime']) ? date("Y-m-d H:i:s", strtotime($_POST['enddatetime'])) : date("Y-m-d 23:59:59");
    /*  getting data on the basis of the start and end date */ 
    $sentiments_recording = get_sentiments_recording($start_date, $end_date);
    $sentiments_email = get_sentiments_email($start_date, $end_date);
    $sentiments_facebook = get_sentiments_facebook($start_date, $end_date);
    $sentiments_twitter = get_sentiments_twitter($start_date, $end_date);
    $sentiments_whatsapp = get_sentiments_whatsapp($start_date, $end_date);
    $sentiments_messenger = get_sentiments_messenger($start_date, $end_date);
    $sentiments_chat = get_sentiments_chat($start_date, $end_date);
    $sentiments_instagram = get_sentiments_instagram($start_date, $end_date);
    /* sending data in json format*/
    echo json_encode([
        'recording' => $sentiments_recording,
        'email' => $sentiments_email,
        'facebook' => $sentiments_facebook,
        'twitter' => $sentiments_twitter,
        'whatsapp' => $sentiments_whatsapp,
        'messenger' => $sentiments_messenger,
        'chat' => $sentiments_chat,
        'instagram' => $sentiments_instagram
    ]);
}

?>