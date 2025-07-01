<?php 
/***
    * Instagram Outgoing Message
    * Author: Aarti Ojha
    * Date: 19-11-2024
    * This file handles Instagram Outgoing Message 
    * To integrate Instagram messaging in your PHP application using the Facebook Business API, you need to follow several steps, including setting up your Facebook Developer account, creating a Instagram Business Account, configuring your webhook, and handling incoming and outgoing messages
    * 
    * Please do not modify this file without permission.
**/
include("../../config/web_mysqlconnect.php");
include("../web_function.php");

// Variable handling
$send_from=$_REQUEST['send_from'];
$ID=$_REQUEST['ID'];
$send_to = $_REQUEST['send_to'];
$caseid=$_REQUEST['id'];//caseid
$messageid=$_REQUEST['messageid'];

$showdiv=$_REQUEST['showdiv'];
// get dispostion table details [Aarti][22-07-2024]
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$messageid' and channel_type = 'Instagram Instagram'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
  $channel_remarks = $query_response['remarks'];
  $disposition_type = $query_response['disposition_type'];
}
// for fetch attachment path [aarti][12-06-2024] 
$sql_cdr= "SELECT * from $db.tbl_instagram_connection where status=1 and debug=1 ";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);
$attachment_path = $config['attachment_path'];

function show__date($timestampMs){
  //Divide by 1,0000
  $timestampSeconds = $timestampMs/ 1000;
  //Format it into a human-readable datetime.
  $formatted = date("d-m-Y H:i", $timestampSeconds);
  //Print it out
  return $formatted;
}
// mysqli_query($link,"update $db.instagram_in_queue set flag='1' , status='1' where id='$ID'; ");
$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.instagram_in_queue  WHERE id = '$messageid'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';


?>
<!-- Html Code Start -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Direct Message</title> 
 <!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/font-awesome/4.5.0/css/font-awesome.min.css"/> 
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css"/> 
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2-bootstrap.min.css"> 
 
<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>   
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-ui.min2.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/web_sent_dm.css">
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/channel_all_style.css">
</head>

<style>
  
/*//expand or collapse  code for "Channel Disposition" or "Sentiment"*/
.section-header {
  font-weight: bold;
  background-color: #f2f2f2;
  padding: 10px;
  cursor: pointer;
  border: 1px solid #ccc;
}
.section-content {
  display: none;
  padding: 10px;
  border: 1px solid #ccc;
  border-top: none;
}
.section-header.open::before {
  content: "▼ ";
}
.section-header::before {
  content: "▶ ";
}
/*multile file code*/
.file-list-inline {
  display: flex;
  flex-wrap: wrap; /* wraps only if screen is too narrow */
  gap: 10px;
  margin-top: 8px;
  background-color: #f8f8f8;
  padding: 8px;
  border-radius: 8px;
}

.file-item {
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 16px;
  font-size: 14px;
  white-space: nowrap;
}

.file-name {
  margin-right: 6px;
}

.remove-file {
  cursor: pointer;
  color: red;
  font-weight: bold;
  padding-left: 3px;
}
/*file button layout code*/
input[type="file"]::file-selector-button {
  padding: 6px 12px;
  border: none;
  background: #007bff;
  color: white;
  border-radius: 4px;
  cursor: pointer;
}

input[type="file"] {
  color: transparent; /* hides the file name */
}
</style>
<body onload=" ">
<div class="popup_area">
    <div class="popheading">
    </div>
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12">
          <div class="response_area"></div>
          <div class="form_area">         
            <!---SHOW DM Message-->
            <input type="hidden" name="send_to" id="send_to" value="<?php echo $send_to;?>">
            <?php

          // $sqldm="SELECT id, send_to, send_from, message, create_date, channel_type,attachment FROM $db.Instagram_out_queue WHERE send_to ='".$send_to."' UNION SELECT id, send_to, send_from, message, create_date, channel_type,attachment FROM $db.Instagram_in_queue WHERE send_from ='".$send_to."' ORDER BY create_date asc";
          $last_message_id = 0; // initialize
           $sqldm = "
           SELECT 
               id, 
               send_to, 
               send_from, 
               message, 
               create_date, 
               channel_type, 
               attachment 
           FROM (
               SELECT 
                   id, 
                   send_to, 
                   send_from, 
                   message, 
                   create_date, 
                   channel_type, 
                   attachment 
               FROM $db.instagram_out_queue 
               WHERE send_to = '".$send_to."'
               
               UNION ALL
               
               SELECT 
                   id, 
                   send_to, 
                   send_from, 
                   message, 
                   create_date, 
                   channel_type, 
                   attachment 
               FROM $db.instagram_in_queue 
               WHERE send_from = '".$send_to."'
           ) AS combined_messages 
           ORDER BY create_date ASC";
       // echo $sqldm;
            $qdm=mysqli_query($link,$sqldm)or die("err".mysqli_error());
            $numrec=mysqli_num_rows($qdm);
            if($numrec){
              $sqlss = "SELECT id from 
              instagram_in_queue WHERE send_from = '".$send_to."' ORDER BY create_date DESC LIMIT 1";
              $qdmss = mysqli_query($link, $sqlss);
              $rmid = mysqli_fetch_assoc($qdmss);
              $last_message_id = $rmid['id']; // update every loop
              ?>
                <div class="row">
                  <form name="dm_form_msg" id="dm_form_msg"   enctype="multipart/form-data" method="post"  >
                      <input type="hidden" name="type" value="assigndm">
                      <div class="chat_box_wrap">
                        <div class="header_t">
                          <!-- function to display name of already created users [vastvikta][25-04-2025] -->
                          <?php
                          function getInstagramUsername($send_to) {
                            global $db,$link;
                            $sql = "SELECT fname FROM $db.web_accounts WHERE instagramhandle = '$send_to'";
                            $result = mysqli_query($link, $sql);
                        
                            if ($result && $row = mysqli_fetch_assoc($result)) {
                                if (!empty($row['fname'])) {
                                    return $row['fname'];
                                }
                            }
                        
                            return null; // Return null if no username found
                          }
                          $username = getInstagramUsername($send_to);
                          ?>
                          <span class="contact-name">Direct Messages(<?php echo $username;?>)</span>
                        </div>
                        <div class="chat-box">
                          <div class="chat-container" id="chat-container">
                            <?php
                            if($numrec){
                              while($rm=mysqli_fetch_assoc($qdm)){
                                if($rm['send_to']!=$send_to){
                                    $msg_class="left-img";
                                    $msg_float="left";
                                    $symbol=">>>";
                                    $imgsrc='<img src="'.$SiteURL.'public/images/instagram.png" alt="" style="height: 30px;width:30px;" title="user name">';
                                    $class ='received';
                                }else{
                                    $symbol=">>>";
                                      $msg_class="right-img";
                                      $msg_float="right";
                                      $imgsrc='<img src="'.$SiteURL.'public/images/'.$dbheadlogo.'" alt="" style="height: 30px;width:30px;" title="user name">';
                                      $class ='sent';
                                }
                                if($caseid==$rm['caseid'] && $rm['caseid']!=0){
                                  $checkval="  checked ";
                                }else{
                                  $checkval="   ";
                                }
                                ?>
                                <div class="message-box <?=$msg_class?>">
                                  <div class="picture">
                                    <?php echo $imgsrc;?>
                                  </div>
                                  <div class="message <?=$class?>" style="float: <?=$msg_float?>" >
                                    <p style="padding: 7px 0px 0px 13px;margin: 0px; "><?php if($caseid && $numrec!=0) {
                                    ?><span>
                                    </span> <? }?><?php echo $rm["message"];?></p>
                                    <p class="message-time" style=" float:right;padding: 0px 9px 3px 0px;margin: 0px;font-size: 12px;"><?php echo $rm["create_date"];?></p>
                                    <?php if(!empty($rm['attachment'])){?><a style="color: #4a90e2;margin-left: 10px;" href="../../../<?=$rm['attachment']?>" target="_blank">attachment</a><?php }?>
                                  </div>
                                </div>
                                <?php }//end of while
                            }else{?>
                                  <div class="enter-message">No message</div>
                            <?php }?>
                          </div>
                        </div>
                      </div>
                  </form>
                </div>
            <? }else{?>
              <div class="chat_box_wrap">
                <div class="header_t" syle="margin:10px;"><h2>Direct Messages</h2></div>
                <img src="images/<?php echo $dbheadlogo;?>" alt="" style="height: 50px;width:100px;" title="user name"> 
                <div class="" style="text-align: left;"> 
                  No message
                </div>
              </div>
            <? }?>
            <input type="hidden" id="last_message_id" value="<?= $last_message_id ?>">
            <!---SHOW DM Message-->
          </div>
         
          <div>
             <!-- added condition to hide dispose and setiment in case creation[vastvikta][13-03-2025] -->
             <?php if ($showdiv != 1) { ?>
 
          <div class="response_area"><div id="response_msg"><?=$msg?></div></div>
            <div class="form_area">
              <form name="dm_form" id="dm_form" enctype="multipart/form-data" method="post">
                <?php
                $sql="select * from $db.web_template_whatsapp where i_type=1 and i_status =1";
                $query=mysqli_query($link,$sql);
                ?>
                <select name="template"  class="select-styl1" onclick="get_template(this.value)" style="display: none;"><option value="" style="display: none;">Select Template </option>
                    <?php while($row=mysqli_fetch_assoc($query)){
                    if($_REQUEST['template']==$row['i_id'])
                    {
                      $sel="selected";
                    }else{
                      $sel="";
                    }
                    ?>
                    <option value="<?=$row['template_tag']?>" <?=$sel?>><?=$row['v_templateName']?> </option>
                <?php } ?>
                </select>
                <div class="message-input">
                  <!-- updated design for send button and hidden values [vastvikta][02-05-2025] -->
                    <textarea  name="dm_message" id="dm_message" placeholder="Enter your message.." style="height:85px;margin-top: 5px;"></textarea>
                    <button type="submit" class="btn_submit" name="dm_button" id="dm_button" onclick="sendMessage(event)" style="width:35px;margin-top:5px;"><svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" style="width:12px; height:12px; margin-left:-4px;" viewBox="0 0 512 512"><path d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480l0-83.6c0-4 1.5-7.8 4.2-10.8L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/></svg></button>
                    <input type="hidden" name="ID" value="<?=$_REQUEST['ID']?>">
                    <input type="hidden" name="case_id" value="<?=$_REQUEST['id']?>">
                    <input type="hidden" name="account_sender_id" value="<?=$_REQUEST['account_sender_id']?>">
                    <input type="hidden" name="sendfrom" id="sendfrom" value="<?= $_REQUEST['send_from'] ?>">
                    <input type="hidden" name="sendto" id="sendto" value="<?php echo $send_to;?>">
                  <span id="responseMessage"></span>
                </div>
                  <div class="row files" id="files" style="padding: 10px;">
                    <font color="#000000" face=verdana size=2><b>Attachment</b></font>
                      <input type="file" id="fileInput" multiple name="attachments"><br><br>
                      <ul id="fileList" class="file-list"></ul>
                  </div>  
               </form> 
            </div>
          </div><!--end of col-md-3-->
            <!-- Channel Dispostion Code Added [Aarti][22-07-2024] -->
          <div class="section-wrapper">
            <div class="section-header toggle-header" onclick="toggleSection(this)">Channel Disposition</div>
            <div class="section-content">
              <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                  <tr>
                    <td>
                      <label>Dispostion Type</label>
                        <div class="log-case">
                          <select name="dispostion_type" id="dispostion_type" class="select-styl1" style="width:180px">
                            <option value="">Please Select</option>
                            <?php 
                              $querys1 = "select * from $db.channel_disposition_type";
                              $disp_query = mysqli_query($link,$querys1);
                              while ($group_res = mysqli_fetch_array($disp_query)){?>
                                  <option value="<?php echo $group_res['name']; ?>" <?php if($group_res['name'] == $disposition_type){ echo 'selected';}?>>
                                    <?php echo $group_res['name']; ?>
                                  </option>
                            <?php } ?>
                          </select>
                        </div>
                        </td>     
                      </tr>
                      <tr>
                      <td>
                        <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $messageid;?>">
                        <input type="hidden" name="channel_type" id="channel_type" value="Instagram Instagram">
                        <input type="hidden" name="send_from" id="send_from" value="<?php echo $send_to;?>">
                        <label>Dispostion Remark</label>
                        <div class="log-case">
                          <?php if(!empty($channel_remarks)){?>
                            <textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 737px;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($channel_remarks); ?></textarea>
                          <?php }else{?>
                              <textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 737px;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($fact); ?></textarea>
                            <?php }?>
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
                </div>
              </div>
              <div class="section-wrapper">
                <div class="section-header toggle-header" onclick="toggleSection(this)">
                  Sentiment
                </div>
                <div class="section-content">
                <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                    <tr>
                      <td>
                        <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $messageid;?>">
                        <input type="hidden" name="channel_type" id="channel_type" value="Instagram Instagram">
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
          </div><!--end of col-md-12-->
      </div><!--end of col-md-9-->
      <?php }?>
    </div><!--end of row-->
  </div><!--end of col-12-->
</div><!--end of poparea-->

<!-- Jquery Code start -->

<script src="<?=$SiteURL?>public/js/disposition_script.js"></script>
<script type="text/javascript">
// Function to scroll the chat to the bottom [Aarti][17-08-2024]
    function scrollToBottom() {
        $(".chat-box").stop().animate({ scrollTop: $(".chat-box")[0].scrollHeight}, 1000);
    }

    // Automatically scroll down when the page loads
    window.onload = scrollToBottom;

    
    var SiteURL = "<?php echo $SiteURL; ?>";
    var dbheadlogo = "<?php echo $dbheadlogo; ?>";
    
  
    function sendMessage(e) {
      e.preventDefault();
      // updated code  for sending and displaying message and attachment [vastvitkta][02-05-2025]
      var messageInput = $("#dm_message");
      var messageText = messageInput.val().trim();
      var validationMessage = $("#response_msg");
      var sendFrom = $('#sendfrom').val();
      var sendTo = $('#sendto').val();

      if (messageText === "" && filesArray.length === 0) {
          validationMessage.text("Message cannot be blank!").show();
          return;
      } else {
          validationMessage.hide();
      }

      var formData = new FormData();
      formData.append("action", "instagram_reply");
      formData.append("message", messageText);
      formData.append("send_from", sendFrom);
      formData.append("send_to", sendTo);
      // old code
      // var attachmentInput = $("#attachment")[0].files[0]; // Get file input
      // if (attachmentInput) {
      //     formData.append("attachments", attachmentInput);
      // }

      // New code for upload files
      filesArray.forEach(file => {
          formData.append('attachments[]', file);
      });


      $.ajax({
        url: "fetchData.php",
        type: "POST",
        data: formData,
        contentType: false, // Required for file upload
        processData: false, // Required for file upload
        dataType: "json", // Ensure the response is treated as JSON
        success: function (response) {
          if (response.error) {
            console.log("Error:", response.error);
            alert("Error sending message.");
            return;
          }

          // Loop through each message/attachment item
          $.each(response, function (index, item) {
            // Always align messages to the right
              var msgClass = "right-img sent";
              var msgFloat = "right";
              var imgSrc = '<img src="' + SiteURL + 'public/images/' + dbheadlogo + '" alt="" style="height: 30px;width:30px;" title="user name">';

              // Prepare message bubble dynamically
              var messageBubble = '<div class="message-box ' + msgClass + '" style="float: ' + msgFloat + ';">' +
                  '<div class="picture">' + imgSrc + '</div>' +
                  '<div class="message" style="float: ' + msgFloat + '; background-color:#d1f1c0; padding:10px; border-radius:4px;">' +
                  '<p style="margin: 0px;">' + item.message + '</p>' +
                  '<p class="message-time" style="float:right; padding: 3px 9px; margin: 0px; font-size: 12px; background-color:#d1f1c0;">' + item.timestamp + '</p>';

              if (item.attachment) {
                  messageBubble += '<a style="color: #4a90e2; margin-left: 10px; display: block; background-color:#d1f1c0; padding: 3px 5px; border-radius: 4px;" href="../../../' + item.attachment + '" target="_blank">attachment</a>';
              }

              messageBubble += '</div></div>';

              $("#chat-container").append(messageBubble); // Append message to chat
          });

          // Clear form inputs after sending
          $("#dm_message").val("");
          $("#attachment").val("");
          filesArray = [];
          renderFileList();
          scrollToBottom();
        },
        error: function () {
            alert("Error sending message. Please try again.");
        }
      });
    }
    
    
// multi-file upload with file name display and a close (remove) icon [Vastvikta][14-05-2025]
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');

let filesArray = [];

fileInput.addEventListener('change', (event) => {
  for (let file of event.target.files) {
    filesArray.push(file);
  }
  renderFileList();
  fileInput.value = ''; // allow same file reselect
});

function renderFileList() {
  fileList.innerHTML = '';
  filesArray.forEach((file, index) => {
    const item = document.createElement('div');
    item.className = 'file-item';
    item.innerHTML = `
      <span class="file-name">${file.name}</span>
      <span class="remove-file" onclick="removeFile(${index})">&times;</span>
    `;
    fileList.appendChild(item);
  });
}

function removeFile(index) {
  filesArray.splice(index, 1);
  renderFileList();
}

//expand or collapse  code for "Channel Disposition" or "Sentiment" [vastvikta][14-05-2025]
function toggleSection(header) {
  $(header).toggleClass("open");
  $(header).next(".section-content").slideToggle();
}


// Instagram chat UI refreshes all messages every 4 seconds  [vastvikta][14-05-2025]
setInterval(function () {
  InstagramDM_Live();
}, 4000);

function InstagramDM_Live() {
  var lastId = $('#last_message_id').val(); // or use `lastMessageId` if in JS variable
  console.log('InstagramDM_Live');
  console.log("lastid");
  console.log(lastId);
  var send_to = $('#send_to').val();
  $.ajax({
    url: "fetchData.php",
    type: 'post',
    dataType: 'json', // ✅ Important: expect JSON response
    data: {
      action: 'InstagramDM_Live',
      send_to: send_to,
      last_id: lastId  // send last seen ID
    },
    success: function (data) {
        $('#chat-container').append(data.html);
        lastMessageId = data.last_id;

        // optionally update hidden last ID
        if (lastMessageId) {
            $('#last_message_id').val(lastMessageId);
        }
      // auto-scroll to bottom
      scrollToBottom();
      }
  });
}
</script>
</body>
</html>
