<?php
/**
 * Auth:Vastvikta Nishad
 * Description: Sends the Reply and  for Setting the Disposition 
 * Date: sep 2 2024
 */
// Include necessary files and database connection
include("../../config/web_mysqlconnect.php"); 
$phone = $_REQUEST['phone'];
$smsid = $_REQUEST['smsid'];

function get_username($smsid) {
    global $link, $db;
    $query = $link->query("SELECT V_AccountName FROM $db.tbl_smsmessagesin WHERE i_id = '$smsid'");
    $fetch = $query->fetch_assoc();
    return $fetch['V_AccountName'];
}

function get_message_details($phone) {
    global $link, $db;
    $query = $link->query("SELECT v_smsString ,d_timeStamp ,type  FROM $db.tbl_smsmessagesin WHERE v_mobileNo = '$phone' ORDER BY d_timeStamp ASC");
    $messages = [];
    while ($fetch = $query->fetch_assoc()) {
        $messages[] = $fetch;
    }
    return $messages;
}

function get_customername($phone) {
    global $link, $db;
    $query = $link->query("SELECT fname FROM $db.web_accounts WHERE phone LIKE '%$phone%'");
    $fetch = $query->fetch_assoc();
    return $fetch['fname'];
}

$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.tbl_smsmessagesin  WHERE i_id = '$smsid'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';

// Get disposition table details 
$query_dis = mysqli_query($link, "SELECT * FROM $db.multichannel_disposition WHERE channel_id = '$smsid' AND channel_type = 'SMS'");
$query_response = mysqli_fetch_array($query_dis);
if ($query_response) {
    $channel_remarks = $query_response['remarks'];
    $disposition_type = $query_response['disposition_type'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Reply</title>
    <link rel="stylesheet" href="../../public/css/<?=$dbtheme?>.css"/>
    <link href="<?=$SiteURL?>public/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/web_sent_dm.css">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/channel_all_style.css">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/font-awesome/4.5.0/css/font-awesome.min.css"/> 
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css"/> 
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2-bootstrap.min.css"> 
</head>
<style>
    .sms-text {
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: pre-wrap; /* Ensures that any long continuous text without spaces wraps properly */
    }
</style>
<body>
    
<div class="popup_area">
    <div class="col-md-12">
        <div class="tableview main-form new-customer">
            <div class="alert alert-primary" id="alert_success" role="alert" style="display:none"></div>
            <div class="alert alert-danger" id="alert_fail" role="alert" style="display:none"></div>
        <form id="dm_form_msg" method="post">
            <div class="chat_box_wrap">
                    <div class="header_t">
                        <?php $username = get_username($smsid); ?>
                        <h5><span class="contact-name text-center" style="color:black;">Direct SMS REPLY (<?php echo $username; ?>)</span></h5>
                    </div>
                    <div class="chat-box">
                        <div class="chat-container" id="chat-container">
                            <?php
                            $messages = get_message_details($phone);
                            if ($messages) {
                                foreach ($messages as $rm) {
                                    // Determine message position and styling based on type
                                    if ($rm['type'] == 'IN') {
                                        // Incoming message (left)
                                        $imgsrc = '<img src="' . $SiteURL . 'public/images/sms.png" alt="" style="height: 30px;width:30px;" title="user name">';
                                        $msg_float = "left";
                                        $msg_class = "left-img";
                                        $class = 'received';
                                    } else {
                                        // Outgoing message (right)
                                        $imgsrc = '<img src="' . $SiteURL . 'public/images/sms.png" alt="" style="height: 30px;width:30px;" title="user name">';
                                        $msg_float = "right";
                                        $msg_class = "right-img";
                                        $class = 'sent';
                                    }
                            ?>
                            <div class="message-box <?=$msg_class?>">
                                <div class="picture" style="float: <?=$msg_float?>">
                                    <?php echo $imgsrc; ?>
                                </div>
                                <div class="message <?=$class?>" style="float: <?=$msg_float?>">
                                    <p style="padding: 7px 0px 0px 13px; margin: 0px;" class='sms-text'><?php echo $rm['v_smsString']; ?></p>
                                    <p class="message-time" style=" float:<?=$msg_float == 'left' ? 'right' : 'left'?>;padding: 0px 9px 3px 0px;margin: 0px;font-size: 12px;">
                                        <?php echo $rm["d_timeStamp"];?>
                                    </p>
                                </div>
                            </div>
                            <?php 
                                } // end of foreach
                            } else { ?>
                            <div class="enter-message">No message</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="message-input">
                    <textarea class="form-control" name="reply" id="reply"  style= "font-size:13px;height:100px;border-radius:0"placeholder="Enter your message.." ></textarea>
                    <button type="submit" class="btn_submit" name="dm_button" id="dm_button" onclick="sendMessage(event)">
                        <i class="fa fa-paper-plane"></i> 
                    </button>
                    <input type="hidden" name="phone" id="phone" value="<?= $phone ?>">
                    <input type="hidden" name="i_id" id="i_id" value="<?= $smsid ?>">
                    <input type="hidden" name="name" value="<?= get_customername($phone) ?>">
                    <input type="hidden" name="action" value="reply">
                </div>
                <p><code><span id="charCount">Characters remaining: 160</span></code></p>

            </form>

            <!-- Add the Disposition Table Section -->
            
            <!-- <div class="col-sm-12">
            <div class="col-md-6"> -->
                <div class="tableview main-form new-customer">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <tr>
                        <td><h6> Channel Disposition</h6></td>
                    </tr>
                    <tr>
                        <td>
                            <label>Disposition Type</label>
                            <div class="log-case">
                                <select name="dispostion_type" id="dispostion_type" class="select-styl1" style="width:180px">
                                    <option value="">Please Select</option>
                                    <?php 
                                    $querys1 = "SELECT * FROM $db.channel_disposition_type";
                                    $disp_query = mysqli_query($link,$querys1);
                                    while ($group_res = mysqli_fetch_array($disp_query)) { ?>
                                        <option value="<?php echo $group_res['name']; ?>" <?php if($group_res['name'] == $disposition_type) { echo 'selected'; } ?>>
                                            <?php echo $group_res['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </td>            
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $smsid;?>">
                            <input type="hidden" name="channel_type" id="channel_type" value="SMS">
                            <label>Disposition Remark</label>
                            <div class="log-case">
                                <?php if (!empty($channel_remarks)) { ?>
                                    <textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 737px;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($channel_remarks); ?></textarea>
                                <?php } else { ?>
                                    <textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 737px;height: 100px;" class="input-style1"></textarea>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="Submit" type="submit" value="Disposition" class="button-orange1" style="float:inherit;" id="create_disposition"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- </div>
            </div>    -->
            <!-- <div class="col-md-6"> -->
                <div class="tableview main-form new-customer">
                    <table class="tableview tableview-2 main-form new-customer">
                        <tbody>
                            <tr>
                                <td><h6>Sentiment</h6></td>
                            </tr>
                            <tr>
                                <td>
                                <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $smsid;?>">
                                <input type="hidden" name="channel_type" id="channel_type" value="SMS">
                            <label>Select Sentiment</label>
                                    <div class="log-case">
                                        <select name="sentiment" id="sentiment" class="select-styl1" style="width:180px;">
                                            <option value="">Select</option>
                                            <option value="negative" <?php echo $selected_sentiment === 'negative' ? 'selected' : ''; ?>>Negative</option>
                                            <option value="positive" <?php echo $selected_sentiment === 'positive' ? 'selected' : ''; ?>>Positive</option>
                                            <option value="neutral" <?php echo $selected_sentiment === 'neutral' ? 'selected' : ''; ?>>Neutral</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="Submit" type="submit" value="Sentiment" class="button-orange1" id="submit_sentiment"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <!-- </div> -->
                </div>   
              </div>           
        </div>
    </div>
</div>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-1.10.2.min.js"></script>

<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script src="<?=$SiteURL?>/public/js/disposition_script.js">
</script><script type="text/javascript">
     function scrollToBottom() {
        $(".chat-box").stop().animate({ scrollTop: $(".chat-box")[0].scrollHeight}, 1000);
    }
    
    // Automatically scroll down when the page loads
    window.onload = scrollToBottom;
    $(document).ready(function () {
    // Disable resizing only for #reply
    $("#reply").css("resize", "none");

    var maxLength = 160; // Set character limit for #reply
    var textArea = $('#reply'); // Only target #reply text area
    var charCount = $('#charCount');

    // Add keyup event listener only to #reply text area
    textArea.keyup(function () {
        var text = textArea.val();
        var remaining = maxLength - text.length;
        charCount.text('Characters remaining: ' + remaining);
        if (remaining < 0) {
            textArea.val(text.substring(0, maxLength)); // Trim extra characters
            charCount.text('Characters remaining: 0');
        }
    });

   // Form submission handling
$('form').on('submit', function (e) {
    var phone = $("#phone").val();
    var reply = $("#reply").val();
    var i_id = $('#reply').val();
    if (reply == "") {
        var msg = "Please Enter Message";
        $("#alert_fail").show();
        $("#alert_fail").text(msg);
        setTimeout(function() {
            $("#alert_fail").hide();
        }, 2000);
        return false; // Stop form submission
    }

    e.preventDefault(); // Prevent default form submission

    // AJAX call to send SMS reply
    $.ajax({
        type: 'post',
        url: 'send_sms_reply.php',
        data: $('form').serialize(),
        beforeSend: function() {
            // Log the serialized form data
            console.log('Sending Data:', $('form').serialize());
        },
            success: function (data) {
                if (data == true) {
                    var msg = "Message sent sucessfully !!";
                    $("#alert_success").show();
                    $("#alert_success").text(msg);
                    setTimeout(function() {
                        $("#alert_success").hide();
                    }, 3000);

                    // Get the current time
                    var currentDateTime = new Date();
                    var formattedTime = currentDateTime.toLocaleString();

                    // Append the new message to the chat container with updated classes and values
                    var newMessage = `
                        <div class="message-box right-img">
                            <div class="picture">
                                <img src="<?=$SiteURL?>public/images/<?=$dbheadlogo?>" alt="" style="height: 30px;width:30px;" title="user name">
                            </div>
                            <div class="message sent" style="float: right;">
                                <p style="padding: 7px 0px 0px 13px; margin: 0px;">${reply}</p>
                                <p class="message-time" style="float: right; padding: 0px 9px 3px 0px; margin: 0px; font-size: 12px;">${formattedTime}</p>
                            </div>
                        </div>
                    `;
                    $("#chat-container").append(newMessage);  // Append the new message

                    // Clear the reply input field
                    $("#reply").val('');

                    // Scroll to the bottom of the chat container
                    var chatContainer = document.getElementById("chat-container");
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            }
        });
    });
});
</script>
</body>
</html>