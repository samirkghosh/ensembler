<?php
/**
 * Chat History Display Script
 * Author: Aarti
 * Date: 09-04-2024
 * 
 * This script retrieves and displays chat history from the database for a specific phone number.
 * It includes a frontend for listing chat sessions and showing chat messages when a session is selected.
 */
include_once("../../config/web_mysqlconnect.php"); // Database connection file
include_once("omnichannel_function.php"); // Functions related to omnichannel support

// Retrieve GET parameters
$phone = $_GET['phone'];
$caseid = $_GET['caseid'];
$session_id = $_GET['session_id'];

// Get chat sessions for the provided phone number
$result = getChatSessions($link, $phone);
$session_array = array();

// Populate session_array with chat session data from database
while($row = mysqli_fetch_assoc($result)){
    $session_array[] = $row;
}
$id = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/chat_history.css" />
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
/**
 * get_chat - Fetches chat messages for a selected session and displays them in the chat window.
 * @param {string} session - The session ID of the chat to retrieve.
 */
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
                        } else {
                            $(".msg_history").append('<div class="outgoing_msg"><div class="sent_msg"><p>' + val.message + '</p><span class="time_date">' + val.create_datetime + '</span></div></div>');
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

    $('#create_case').submit(function (event) {
        event.preventDefault();
        var phone = $("#phone").val();
        var chat_session = $("#chat_session").val();
        $.ajax({
            url: 'checkMail.php',
            type: 'post',
            data: { 'chat_session': chat_session, 'type': 'chat' },
            success: function (data, status) {
                if (data != '') {
                    $("#success-msg").html(data);
                    return false;
                } else {
                    var new_case_manual = btoa('new_case_manual');
                    var url_new = 'helpdesk_index.php?token='+ encodeURIComponent(new_case_manual);
                    window.open(url_new + '&mr=5&phone=' + phone + '&chatid=' + chat_session);
                    parent.$.colorbox.close();
                }
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        }); // end ajax call
    });
</script>