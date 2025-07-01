<?php
// Include the database connection file
include_once("../config/web_mysqlconnect.php");
include_once("../CRM/web_function.php");

 if ($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_REQUEST["action"]) && $_REQUEST['action'] == 'go') {
    echo adhoc_report_send();
    //Close the database connection
    $link->close();
    exit();
    
 }else {
    $response = json_encode(['status'=> 'error','message'=> 'no response']);
    echo $response;
    //Close the database connection
    $link->close();
    exit();
 }



