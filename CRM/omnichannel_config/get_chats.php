<?php

include_once("../../config/web_mysqlconnect.php");

$session = $_POST['session'];


// get chats using chat session

$chat = "SELECT * FROM $db. in_out_data WHERE chat_session_id='$session' ORDER BY id ASC";

$result = mysqli_query($link,$chat);

    $chat_array = array();

    while($row = mysqli_fetch_assoc($result)){

        $chat_array[] = $row;

    }


    echo json_encode(array("status" => "success", "msg" => "conversations", "chats" => $chat_array));die();




?>