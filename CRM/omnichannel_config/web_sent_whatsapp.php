<?php 
/***
 * Auth: Aarti Ojha
 * Date:  22-07-2023
 * Description: To Handle Request of Sending reply to the Whatsapp DM's
 */
// <!-- Channel Dispostion Code Added [Aarti][22-07-2024]-->
include("../../config/web_mysqlconnect.php");

$send_from=$_REQUEST['send_from'];
$i_WhatsAppID=$_REQUEST['i_WhatsAppID'];
$send_to = $_REQUEST['send_to'];
$caseid=$_REQUEST['id'];
$messageid=$_REQUEST['messageid'];

$showdiv=$_REQUEST['showdiv'];
// for fetch attachment path [aarti][12-06-2024] 
$sql_cdr= "SELECT * from $db.tbl_whatsapp_connection where status=1 and debug=1 ";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);
$attachment_path = $config['attachment_path'];

$msg="";
$dm_message=""; $json_arry =[];$json_arry["errors"]=array();

$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.whatsapp_in_queue  WHERE id = '$messageid'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';

// get dispostion table details [Aarti][22-07-2024]
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$messageid' and channel_type = 'Whatsapp'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
  $channel_remarks = $query_response['remarks'];
  $disposition_type = $query_response['disposition_type'];
}

// for unread message - [Aarti][01-05-2025]
mysqli_query($link,"update $db.whatsapp_in_queue set flag='1' where send_from='$send_from'");

// for fetch whatsapp user name updated the code [vastvikta][14-04-2025]
function get_whatsapp_name($send_to,$whatsapphandle){
    global $db,$link;
    $sql_name = "SELECT AccountNumber, fname 
                 FROM $db.web_accounts 
                 WHERE (whatsapphandle = '$whatsapphandle' OR phone = '$whatsapphandle')
                 OR (whatsapphandle = '$send_to' OR phone = '$send_to')";
                
    $result_new =   mysqli_query($link, $sql_name); // Run the query
    $row_new = mysqli_fetch_assoc($result_new);
    return $row_new;
}
function get_whatsapp_name2($send_to){

    global $db,$link;
    $sql = "SELECT user_name FROM $db.whatsapp_in_queue WHERE send_from = '$send_to'";
   
    $result_new = mysqli_query($link, $sql); // Run the query
    $row2 = mysqli_fetch_assoc($result_new);
    return $row2;
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Direct Message</title> 
 <!-- Required meta tags -->
    <meta charset="utf-8">
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
<!-- Font Awesome and Ionicons -->
<link rel="stylesheet" href="https://alliance-infotech.in/ensembler/public/css/font-awesome.min.css">
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

<body>
<div class="popup_area">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="response_area"></div>
                <div class="form_area">
                  <!-- for live data get [Aarti][01-05-2025] -->
                  <input type="hidden" name="send_to" id="send_to" value="<?php echo $send_to;?>">
                    <!---SHOW DM Message-->
                    <?php
                    $last_message_id = 0; // initialize
                   $sqldm="SELECT id, send_to, send_from, message, create_date, channel_type,attachment,user_name FROM $db.whatsapp_out_queue WHERE send_to ='".$send_to."' UNION SELECT id, send_to, send_from, message, create_date, channel_type,attachment,user_name FROM $db.whatsapp_in_queue WHERE send_from ='".$send_to."' ORDER BY create_date asc";

                    $qdm = mysqli_query($link, $sqldm) or die("err" . mysqli_error());
                    $numrec = mysqli_num_rows($qdm);
                    if ($numrec) {

                      // getting incoming lastid 
                      $sqlss = "SELECT id from  whatsapp_in_queue WHERE send_from = '".$send_to."' ORDER BY create_date DESC LIMIT 1";
                      $qdmss = mysqli_query($link, $sqlss);
                      $rmid = mysqli_fetch_assoc($qdmss);
                      $last_message_id = $rmid['id']; // update every loop
                    ?>
                    <div class="row">
                        <form name="dm_form_msg" id="dm_form_msg" enctype="multipart/form-data" method="post">
                            <!-- <input type="hidden" name="type" value="assigndm"> -->
                            <div class="chat_box_wrap">
                                <div class="header_t">
                                   <?php
                                    //  added this condition so that the username is fetched from the webaccounts by default if not  there then fetch from the whatsapp table itself [vastvikta][14-04-2025]
                                    if (!preg_match('/^91\d{10}$/', $send_to) && preg_match('/^\d{10}$/', $send_to)) {
                                        $whatsapphandle = '91' . $send_to;
                                    }
                                    if (preg_match('/^\d{10}$/', $send_to)) {
                                        // Case 1: $send_to is 10 digits
                                        $whatsapphandle = '91' . $send_to;
                                    } elseif (preg_match('/^91(\d{10})$/', $send_to, $matches)) {
                                        // Case 2: $send_to starts with 91 and followed by 10 digits
                                        $whatsapphandle = $matches[1];      // just 10-digit number
                                        $send_to = '91' . $matches[1];             // update send_to to 10-digit number
                                    }
                                
                                   $row_new = get_whatsapp_name($send_to,$whatsapphandle);
                                   // Fetch as an associative array
                         
                                  $fname = $row_new['fname']; // Store the 'fname' value in the variable
                                  $id = $row_new['AccountNumber'];
                                  // Check if fname is empty
                                  if (empty($fname)) {
                                       // Fetch as an associative array
                                      $row2 = get_whatsapp_name2($send_to);
                                      $user_name = $row2['user_name'];
                          
                                      if(empty($user_name)){
                                          $username = $send_to ;
                                      }else{ 
                                          $username = $user_name;
                                      } 
                                  }else{
                                    $username = $fname;
                                  }
                                   
                                   ?>
                                  <span class="contact-name">Direct Messages (<?php echo $username;?>)</span>
                                </div>
                                <div class="chat-box">
                                  <div class="chat-container" id="chat-container">
                                    <?php
                                    if ($numrec) {
                                        while ($rm = mysqli_fetch_assoc($qdm)) {
                                            
                                            if ($rm['send_to'] != $send_to) {
                                                $msg_class = "left-img";
                                                $msg_float = "left";
                                                $imgsrc = '<img src="' . $SiteURL . 'public/images/whatsapp_png.png" alt="" style="height: 30px;width:30px;" title="user name">';
                                                $class ='received';
                                            } else {
                                                $msg_class = "right-img";
                                                $msg_float = "right";
                                                $imgsrc = '<img src="' . $SiteURL . 'public/images/' . $dbheadlogo . '" alt="" style="height: 30px;width:30px;" title="user name">';
                                                $class ='sent';
                                            }
                                            $checkval = ($caseid == $rm['caseid'] && $rm['caseid'] != 0) ? "checked" : "";
                                    ?>
                                    <div class="message-box <?=$msg_class?>">
                                      <div class="picture">
                                        <?php echo $imgsrc;?>
                                      </div>
                                      <div class="message <?=$class?>" style="float: <?=$msg_float?>" >
                                        <p style="padding: 7px 0px 0px 13px;margin: 0px; "><?php if($caseid && $numrec!=0) {
                                        ?><span>
                                          <!-- <input type="checkbox" class="chk_boxes1" name="chkl[ ]" value="<?=$rm['id']?>" <?=$checkval?>>  -->
                                        </span> <? }?><?php echo $rm["message"];?></p>
                                        <p class="message-time" style=" float:right;padding: 0px 9px 3px 0px;margin: 0px;font-size: 12px;"><?php echo $rm["create_date"];?></p>

                                        <?php if(!empty($rm['attachment'])){?><a style="color: #4a90e2;margin-left: 10px;" href="../../../<?=$rm['attachment']?>" target="_blank">attachment</a><?php }?>
                                      </div>
                                    </div>
                                    <?php 
                                        } // end of while
                                    } else { ?>
                                    <div class="enter-message">No message</div>
                                    <?php } ?>
                                  </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php } else { ?>
                    <div class="chat_box_wrap">
                        <div class="header_t" syle="margin:10px;">
                            <h2>Direct Messages</h2>
                        </div>
                        <img src="images/<?= $dbheadlogo ?>" alt="" style="height: 50px;width:100px;" title="user name"> 
                        <div class="" style="text-align: left;"> 
                            No message
                        </div>
                    </div>
                    <?php } ?>
                    <input type="hidden" id="last_message_id" value="<?= $last_message_id ?>">
                    <!---SHOW DM Message-->
                </div>
            </div><!-- end of col-md-9 -->
            <!-- added condition to hide dispose and setiment in case creation[vastvikta][13-03-2025] -->
            <?php if ($showdiv != 1) { ?>
             <div class="col-md-12">
                <div class="response_area">
                    <div id="response_msg"><?= $msg ?></div>
                </div>
                <div class="form_area">
                    <form name="dm_form" id="dm_form" enctype="multipart/form-data" method="post">
                      
                      <div class="message-input" style="position: relative;">
                            <!-- Template Dropdown -->
                            <div id="template-options" style="position: absolute; bottom: 100px; left: 0; background: white; border-radius: 5px; display: none; z-index: 10; width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <ul style="list-style: none; margin: 0; padding: 10px; position: relative;">
                                    <?php
                                    $query = "SELECT temp_name, temp_content FROM $db.whatsapp_template WHERE status = '1'";
                                    $result = mysqli_query($link, $query);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $templateName = htmlspecialchars($row['temp_name']);
                                            $templateContent = htmlspecialchars($row['temp_content']);
                                            echo "
                                            <li 
                                                onmouseover=\"showContent(`$templateContent`)\" 
                                                onmouseout=\"hideContent()\">
                                                <button type='button'  style='background-color:#fff;border:1px #fff;margin:5px;text-align: left; width: 100%; white-space: normal;font-size:12px;' onclick='selectTemplateContent(`$templateContent`)'><b>$templateName</b></button>
                                            </li>";
                                        }
                                    } else {
                                        echo "<li><em>No templates available</em></li>";
                                    }
                                    ?>
                                </ul>

                                <!-- Preview Box -->
                                <div id="template-preview" style="position: absolute; top: 0; left: 230px; width: 260px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; display: none; font-size: 14px; color: #333;"></div>
                            </div>

                            <!-- Template Button -->
                            <button type="button" id="template" class="btn btn-secondary ml-1" 
                                style="background-color:#9782ff; border: 1px solid #9782ff; color:white; 
                                      width:40px; height:85px; border-radius:5px;margin-right:10px;">
                                <img src="../multi_chat/temp.png" alt="User" 
                                    style="width:30px; height:30px; border-radius:1px;">
                            </button>

                            <!-- Textarea -->
                            <textarea name="dm_message" id="dm_message" placeholder="Enter your message.." style="height:85px;" class="form-control mt-1"></textarea> 
                        
                        <button type="submit" class="btn_submit" name="dm_button" id="dm_button" onclick="sendMessage(event)" style="height:85px!important;margin-left:10px;"><i class="fa fa-paper-plane"></i></button>
                        <input type="hidden" name="sendto" id="sendto" value="<?php echo $send_to;?>">
                        <input type="hidden" name="i_WhatsAppID" value="<?= $_REQUEST['i_WhatsAppID'] ?>">
                        <input type="hidden" name="case_id" value="<?= $_REQUEST['id'] ?>">
                        <input type="hidden" name="account_sender_id" value="<?= $_REQUEST['account_sender_id'] ?>">
                        <input type="hidden" name="sendfrom" id="sendfrom" value="<?= $_REQUEST['send_from'] ?>">
                        <span id="responseMessage"></span>
                      </div>
                       <div class="row files" id="files" style="padding: 10px;">
                         <!-- <font color="#000000" face=verdana size=2><b>Attachment</b></font>
                            <span class="btn btn-default btn-file"><input type="file" name="attachments" id="attachment"  multiple />
                            </span>
                            <ul class="fileList"></ul>
                            <input type="hidden" name="liValues" id="liValues"> -->

                            <font color="#000000" face=verdana size=2><b>Attachment</b></font>
                            <input type="file" id="fileInput" multiple name="attachments"><br><br>
                            <ul id="fileList" class="file-list"></ul>
                      </div>
                    </form>
                </div>
                <!-- Channel Disposition [Aarti][22-07-2024] -->
                  <div class="section-wrapper">
                    <div class="section-header toggle-header" onclick="toggleSection(this)">
                      Channel Disposition
                    </div>
                    <div class="section-content">
                      <table class="tableview tableview-2 main-form new-customer">
                        <tbody>
                          <tr>
                            <td>
                              <label>Disposition Type</label>
                              <div class="log-case">
                                <select name="dispostion_type" id="dispostion_type" class="select-styl1" style="width:180px">
                                  <option value="">Please Select</option>
                                  <?php 
                                    $querys1 = "select * from $db.channel_disposition_type";
                                    $disp_query = mysqli_query($link,$querys1);
                                    while ($group_res = mysqli_fetch_array($disp_query)) { ?>
                                      <option value="<?php echo $group_res['name']; ?>" <?php if($group_res['name'] == $disposition_type){ echo 'selected'; } ?>>
                                        <?php echo $group_res['name']; ?>
                                      </option>
                                  <?php } ?>
                                </select>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $messageid; ?>">
                              <input type="hidden" name="channel_type" id="channel_type" value="Whatsapp">
                              <input type="hidden" name="send_from" id="send_from" value="<?php echo $send_to; ?>">

                              <label>Disposition Remark</label>
                              <div class="log-case">
                                <textarea name="email_remark" id="email_remark" class="input-style1" style="margin:0;padding:0.5rem;width:737px;height:100px;">
                                  <?php echo htmlspecialchars_decode(!empty($channel_remarks) ? $channel_remarks : $fact); ?>
                                </textarea>
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
                  <!-- Sentiment Section (Collapsible) -->
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
                              <input type="hidden" name="channel_type" id="channel_type" value="Whatsapp">

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
                    </div>
                  </div>

            </div>
            <?php } ?><!-- end of col-md-3 -->
        </div><!-- end of row -->
    </div><!-- end of col-12 -->
</div><!-- end of pop_area -->
   
<script>
// Call JavaScript function with escaped PHP variables
// get_template('<?=$templateid?>', '<?=$v_Screenname?>', '<?=$messageid?>');
</script>
<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script src="<?=$SiteURL?>/public/js/disposition_script.js"></script>
<!-- Jquery Code start -->
<script type="text/javascript">
const templateBtn = document.getElementById('template');
const optionsBox = document.getElementById('template-options');

// Toggle template list on button click
templateBtn.addEventListener('click', function () {
    optionsBox.style.display = optionsBox.style.display === 'none' ? 'block' : 'none';
});

// Close dropdown if clicked outside
document.addEventListener('click', function (event) {
    if (!templateBtn.contains(event.target) && !optionsBox.contains(event.target)) {
        optionsBox.style.display = 'none';
    }
});

// Fill template content into the textarea
function selectTemplateContent(content) {
    document.getElementById('dm_message').value = content;
    document.getElementById('template-options').style.display = 'none';
}

// Show preview box on hover
function showContent(content) {
    const previewBox = document.getElementById('template-preview');
    previewBox.textContent = content;
    previewBox.style.display = 'block';
}

// Hide preview box
function hideContent() {
    document.getElementById('template-preview').style.display = 'none';
}

// Function to scroll the chat to the bottom [Aarti][17-08-2024]
// updated auto scroll code as previous one was not working [vastvikta][07-03-2025]
document.addEventListener("DOMContentLoaded", function () {
    smoothScrollToBottom(); // Scroll on page load
});

function smoothScrollToBottom() {
    var chatBox = document.querySelector(".chat-box");
    if (chatBox) {
        var scrollHeight = chatBox.scrollHeight;
        var duration = 800; // Scroll duration in milliseconds
        var startTime = null;
        var currentScroll = chatBox.scrollTop;

        function animateScroll(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = timestamp - startTime;
            var easeInOut = progress / duration;

            chatBox.scrollTop = currentScroll + ((scrollHeight - currentScroll) * easeInOut);

            if (progress < duration) {
                requestAnimationFrame(animateScroll);
            } else {
                chatBox.scrollTop = scrollHeight; // Ensure it reaches the exact bottom
            }
        }

        requestAnimationFrame(animateScroll);
    }
}

// Function to be called after a new message is added
function onNewMessage() {
    var chatBox = document.querySelector(".chat-box");
    if (chatBox) {
        var isScrolledToBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 50;
        if (isScrolledToBottom) {
            smoothScrollToBottom();
        } else {
            // Optionally, show a "New messages" notification
        }
    }
}

// multi-file upload with file name display and a close (remove) icon [Aarti][01-05-2025]
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


var SiteURL = "<?php echo $SiteURL; ?>";
var dbheadlogo = "<?php echo $dbheadlogo; ?>";
function sendMessage(e) {
    e.preventDefault();

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
    formData.append("action", "whatsapp_reply");
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
                  '<div class="message" style="float: ' + msgFloat + '; background-color:#d1f1c0; padding:10px; border-radius:8px;">' +
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
            smoothScrollToBottom();
        },
        error: function () {
            alert("Error sending message. Please try again.");
        }
    });
}

// WhatsApp chat UI refreshes all messages every 4 seconds  [Aarti][01-05-2025]
setInterval(function () {
  WhatsAppDM_Live();
}, 4000);

function WhatsAppDM_Live() {
  var lastId = $('#last_message_id').val(); // or use `lastMessageId` if in JS variable
  console.log('WhatsAppDM_Live');
  var send_to = $('#send_to').val();
  $.ajax({
    url: "fetchData.php",
    type: 'post',
    dataType: 'json', // ✅ Important: expect JSON response
    data: {
      action: 'WhatsAppDM_Live',
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
        smoothScrollToBottom();
      }
  });
}
//expand or collapse  code for "Channel Disposition" or "Sentiment" [Aarti][01-05-2025]
function toggleSection(header) {
  $(header).toggleClass("open");
  $(header).next(".section-content").slideToggle();
}
</script>
