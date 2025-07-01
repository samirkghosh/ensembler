<?php

/***
 * Author: Aarti Ojha
 * Modification Date: 10/23/24
 * Description: 
 * This script fetches and displays the chat history for a specific phone number. 
 * It retrieves chat sessions from the database, displays them in a list, and includes functionality 
 * for viewing detailed messages of a session and optionally creating a new case from the chat session.
 */
include_once("../../config/web_mysqlconnect.php");
include_once("omnichannel_function.php");

// Get parameters from the URL
$phone = $_GET['phone']; // Phone number of the user
$caseid = $_GET['caseid']; // Case ID (optional)
$session_id = $_GET['session_id']; // Session ID
// updated the function to fetch data according to session id [vastvikta][15-04-2025]
$result = getChatSessions($link, $phone,$session_id); 
$session_array = array();
while($row = mysqli_fetch_assoc($result)){
    $session_array[] = $row;
}

$id = 0 ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/chat_history.css" />
    <link href="<?=$SiteURL?>/public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Chat Session</h4>
                        </div>
                    </div>
                    <div class="inbox_chat">
                         <?php 
                         if (empty($session_array)) {
                                echo "Session array is empty!";
                            } else {
                         foreach ($session_array as $value) { 
                         $id++;
                         $session = $value['chat_session'];}
                         
                         ?>
                              <!-- Active chat -->
                              <div class="chat_list" onclick="get_chat('<?=$session?>')">
                                <div class="chat_people">
                                    <div class="chat_img"> <img src="<?=$SiteURL?>public/images/user-profile-new.png"
                                            alt="img"> </div>
                                    <div class="chat_ib">
                                        <h5>
                                            <a>
                                            <?=$session?> <span class="chat_date"><?=date('M d',strtotime($value['session_start_time']))?></span></h5>
                                            </a>
                                        <p><?=$value['content_text']?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Active chat -->
                       <? } ?> 
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">
                    </div>
                </div>
            </div>
        </div>
        <?php  if(empty($caseid)){ ?>
        <b id='success-msg' style='color:green; font-size:16px; background: rgba(185, 248, 185, 0.99);'></b>
        <form id="create_case" method="post">
            <span style="float:right">
                <input type="submit" value="Create Case" class="button-orange1" id="create" style="display:none">
                <input type="hidden" name="chat_session" id="chat_session">
                <input type="hidden" name="phone" id="phone" value="<?=$phone?>">
            </span>
        </form>
        <?php }?>
    </div>
</body>
<script src="<?=$SiteURL?>/public/bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>/public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
// Function shifted from chat_history.php 
    function get_chat(session) {
        var url = "get_chats.php";
        $("#chat_session").val(session);
        $.ajax({
            method: "POST",
            url: url,
            data: { 'session': session },
            dataType: 'JSON',
            success: function (data) {
                console.log('data')
                console.log(data)
                if (data.chats.length > 0) {
                    $("#create").show();
                    $(".msg_history").empty();
                    $.each(data.chats, function (i, val) {
                        //console.log(val);
                        if (val.direction == 'IN') {
                            var attachments_list = '';
                            if (val.attachment) {
                                attachments_list = '<p class="attachment_images"><a href="../../../chatbox/' + val.attachment + '" target="_blank">attachment</a></p>';
                            }
                            var message_list = '';
                            if (val.message) {
                                message_list = '<p>' + val.message + '</p>';
                            }
                            $(".msg_history").append('<div class="incoming_msg"><div class="received_msg"><div class="received_withd_msg">' + message_list + attachments_list + '<span class="time_date">' + val.create_datetime + '</span></div></div></div>');
                        }else {
                            var content = ''; // Initialize variable for content

                            // Check if there is an attachment
                            if (val.attachment) {
                                content = '<p ><a style="color:white!important" href="../../wapchat/' + val.attachment + '" target="_blank">attachment</a></p>';
                            } 
                            // Check if there is a message
                            else if (val.message) {
                                content = '<p>' + val.message + '</p>';
                            }

                            // Append the content to the message history
                            if (content) {
                                $(".msg_history").append(
                                    '<div class="outgoing_msg">' +
                                        '<div class="sent_msg">' +
                                            content +
                                            '<span class="time_date">' + val.create_datetime + '</span>' +
                                        '</div>' +
                                    '</div>'
                                );
                            }
                        }
                    });
                } else {
                    $("#create").hide();
                    console.log('No chats found')
                }
            },
            error: function (error) {
                console.log('error')
                console.log(error)
            }
        });
    }

    $('.chat_list').click(function () {
        $('.chat_list').removeClass("active");
        $(this).addClass("active");
    });
$(document).ready(function(){
    $('#create_case').submit(function (event) {
        event.preventDefault();
        var phone = $("#phone").val();
        var chat_session = $("#chat_session").val();
        $.ajax({
            url: 'checkMail.php',
            type: 'post',
            data: { 'chat_session': chat_session, 'type': 'chat' },
            success: function (data, status) {
                console.log(data);
                if (data.trim()) {
                    $("#success-msg").html(data);
                    return false;
                } else {
                    var new_case_manual = btoa('new_case_manual');
                    var url_new = '../helpdesk_index.php?token='+ encodeURIComponent(new_case_manual);
                    console.log(url_new);
                    window.open(url_new + '&mr=5&phone_number=' + phone + '&chatid=' + chat_session);
                    parent.$.colorbox.close();
                }
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        }); // end ajax call
    });
});
</script>