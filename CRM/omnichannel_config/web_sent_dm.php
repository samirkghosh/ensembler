<?php 
/***
 * Auth: Vastvikta Nishad
 * Date:  19 Apr  2024
 * Description: To Handle Request of Sending reply to the twitter DM's
 */ 
// <!-- Channel Dispostion Code Added [Aarti][22-07-2024]-->
include("../../config/web_mysqlconnect.php");

$showdiv=$_REQUEST['showdiv'];
$recipient_id = $_REQUEST['receptent_id'];
$tweet_id = $_REQUEST['id']; // caseid
$messageid = $_REQUEST['messageid'];

function show__date($timestampMs) {
    // Divide by 1,0000
    $timestampSeconds = $timestampMs / 1000;
    // Format it into a human-readable datetime.
    $formatted = date("d-m-Y H:i", $timestampSeconds);
    // Print it out
    return $formatted;
}

if ($_REQUEST['receptent_id'] != "") {
    $cond_r = " AND tuser_id='" . $_REQUEST['receptent_id'] . "' ";
    $sqlt = mysqli_query($link, "select distinct tuser_id, v_Screenname, v_name from $dbname.tbl_tweet where i_Status=1 and v_Screenname!='lusaka_water' $cond_r ");
    $rowt = mysqli_fetch_assoc($sqlt);
    $v_Screenname = $rowt['v_Screenname'];
}
//Update red unred flag
 /* This code comment for agent disposition time update flag [Aarti][23-07-2024]*/
// mysqli_query($link,"update $db.tbl_tweet set Flag='1' where i_ID='$messageid'; ");
$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.tbl_tweet  WHERE i_ID = '$messageid'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';

$msg = "";
$dm_message = ""; 
$json_arry = [];
$json_arry["errors"] = array();

if (isset($_REQUEST['dm_button'])) {
    if ($_POST['receptent_id'] != "" && trim($_POST["dm_message"]) != "") {
        $receptent_id = $_POST['receptent_id']; // sumamathew10
        $case_id = $_POST['case_id'];
        //$receptent_id='128861993';//@samkghosh
        $message = trim($_POST["dm_message"]);
        $i_TweetID = $_POST['i_TweetID'];
        $dm_id = $_POST["id"];
        $recipient_id = $receptent_id;
        $sender_id = $_POST['account_sender_id'];
        $time_stamp = '';
        $message_data = $message;
        $created_date = date("Y-m-d H:i:s");
        $msg_flag = 'OUT';
        $sent_flag = '0';
        
        $sql_insert_information = "INSERT INTO $dbname.web_twitter_directmsg (dm_id, recipient_id, sender_id, message_data, time_stamp, i_TweetID, page_source, read_status, created_date, msg_flag, sent_flag)
            VALUES('" . $dm_id . "','" . $recipient_id . "','" . $sender_id . "','" . $message_data . "','" . $time_stamp . "','" . $i_TweetID . "','TWITTER QUEUE SENT DM',1,'" . $created_date . "','" . $msg_flag . "','" . $sent_flag . "') ";

        mysqli_query($link, $sql_insert_information) or die(mysqli_error($link));
        //added code for updating flag if   reply is send[vastvikta][17-12-2024]
        mysqli_query($link,"update $db.instagram_in_queue set flag='1' , status='1' where i_ID='$tweet_id'; ");
        $msg = "Message sent successfully !!";
    } else {
        $msg = "Please enter Message !!! ";
    }
}
// get dispostion table details [Aarti][22-07-2024]
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$messageid' and channel_type = 'Twitter'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
  $channel_remarks = $query_response['remarks'];
  $disposition_type = $query_response['disposition_type'];
}
?>

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
.chat-window{
  height:50% !important;
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
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Direct Message</title> 
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/font-awesome/4.5.0/css/font-awesome.min.css"/> 
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css"/> 
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2-bootstrap.min.css"> 
  
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>    
<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script> 
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-ui.min2.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/web_sent_dm.css">
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/channel_all_style.css">
</head>
  <div class="popup_area">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12">
          <div class="response_area"></div>
          <div class="form_area">
            <!-- SHOW DM Message -->
            <?php
$sqldm = "SELECT * FROM $dbname.web_twitter_directmsg 
          WHERE recipient_id = '$recipient_id' OR sender_id = '$recipient_id' 
          ORDER BY dm_id DESC LIMIT 0,50";

$qdm = mysqli_query($link, $sqldm) or die("err" . mysqli_error($link));
$numrec = mysqli_num_rows($qdm);
?>
<div class="chat_box_wrap">
  <div class="header_t">
    <span class="contact-name">Direct Messages</span>
  </div>
  <?php if ($numrec) { ?>
    <div class="row">
      <form name="dm_form_msg" id="dm_form_msg" enctype="multipart/form-data" method="post">
        <input type="hidden" name="type" value="assigndm">

        <div class="chat-box">
          <div class="chat-container" id="chat-container">
            <?php
            while ($rm = mysqli_fetch_assoc($qdm)) {
              $isSender = ($rm['sender_id'] == $recipient_id);

              $msg_class = $isSender ? "left-img" : "right-img";
              $msg_float = $isSender ? "left" : "right";
              $userName = $isSender ? "@$v_Screenname" : "@luska_water";
              $imgsrc = $isSender 
                        ? '<img src="../../public/images/x_logo.png" alt="" style="height: 30px;width:30px;" title="user name">' 
                        : '<img src="../../public/images/' . $dbheadlogo . '" alt="" style="height: 30px;width:30px;" title="user name">';
              $class = $isSender ? "received" : "sent";

              // Update read status
              $upd_dm_readstatus = "UPDATE $dbname.web_twitter_directmsg SET read_status = 1 WHERE id = '{$rm['id']}'";
              mysqli_query($link, $upd_dm_readstatus);

              $checkval = ($tweet_id == $rm['caseid'] && $rm['caseid'] != 0) ? "checked" : "";
              ?>
              
              <div class="message-box <?=$msg_class?>">
                <div class="picture">
                  <?=$imgsrc?>
                </div>
                <div class="message <?=$class?>" style="float: <?=$msg_float?>">
                  <p style="padding: 7px 0 0 13px; margin: 0;">
                    <?=$rm["message_data"]?>
                  </p>
                  <p class="message-time" style="float:right; padding: 0 9px 3px 0; margin: 0; font-size: 12px;">
                    <?=show__date($rm["time_stamp"]);?>
                  </p>
                  <?php if (!empty($rm['attachment'])) { ?>
                    <a style="color: #4a90e2; margin-left: 10px;" href="<?=$rm['attachment']?>" target="_blank">attachment</a>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>

    <?php } else { ?>
      <div class="chat-window">
        <div class="no-message">
          <i class="fa fa-inbox"></i>
          <p>No messages yet</p>
          <small>Start a conversation by sending a message!</small>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="col-md-12">
  <div class="response_area">
    <div id="response_msg"><?= $msg ?></div>
  </div>

  <div class="form_area">
    <form name="dm_form" id="dm_form" enctype="multipart/form-data" method="post">
      <div class="message-input" style="position: relative; display: flex; align-items: center;">

        <!-- Template Dropdown Area -->
        <div id="template-options" style="position: absolute; bottom: 100px; left: 0; background: white; border-radius: 5px; display: none; z-index: 10; width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
          <ul style="list-style: none; margin: 0; padding: 10px; position: relative;">
            <?php
            $query = "SELECT v_templateName, t_templateContent FROM $dbname.web_template_master WHERE i_type = 1 AND i_status = 1";
            $result = mysqli_query($link, $query);

            if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $templateName = htmlspecialchars($row['v_templateName']);
                $templateContent = htmlspecialchars($row['t_templateContent']);
                echo "
                <li onmouseover=\"showContent(`$templateContent`)\" onmouseout=\"hideContent()\">
                  <button type='button' style='background-color:#fff;border:1px #fff;margin:5px;text-align: left; width: 100%; white-space: normal;font-size:12px;' onclick='selectTemplateContent(`$templateContent`)'><b>$templateName</b></button>
                </li>";
              }
            } else {
              echo "<li><em>No templates available</em></li>";
            }
            ?>
          </ul>

          <!-- Template Preview -->
          <div id="template-preview" style="position: absolute; top: 0; left: 230px; width: 260px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; display: none; font-size: 14px; color: #333;"></div>
        </div>

        <!-- Template Button -->
        <button type="button" id="template" class="btn btn-secondary ml-1"
          style="background-color:#9782ff; border: 1px solid #9782ff; color:white; width:40px; height:85px; border-radius:5px; margin-right:10px;">
          <img src="../multi_chat/temp.png" alt="Template" style="width:30px; height:30px;">
        </button>

        <!-- Textarea -->
        <textarea name="dm_message" id="dm_message" placeholder="Enter your message.." style="height:85px;" class="form-control mt-1"></textarea>

        <!-- Send Button -->
        <button type="submit" class="btn_submit" name="dm_button" id="dm_button" onclick="sendMessage(event)" style="height:85px!important;margin-left:10px;width:40px;"><svg xmlns="http://www.w3.org/2000/svg"  style="height:90%;width:90%;"  viewBox="0 0 512 512"><path fill="#ffffff" d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480l0-83.6c0-4 1.5-7.8 4.2-10.8L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/></svg></button>
                        

        <!-- Hidden Fields -->
        <input type="hidden" name="i_TweetID" value="<?= $_REQUEST['i_TweetID'] ?>">
        <input type="hidden" name="case_id" value="<?= $_REQUEST['id'] ?>">
        <input type="hidden" name="account_sender_id" value="<?= $_REQUEST['account_sender_id'] ?>">
        <input type="hidden" name="receptent_id" value="<?= $_REQUEST['receptent_id'] ?>">

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
      </div>
    </form>
  </div>
</div>
          <!-- Channel Disposition Section (Collapsible) -->
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
                  $querys1 = "SELECT * FROM $db.channel_disposition_type";
                  $disp_query = mysqli_query($link, $querys1);
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
            <input type="hidden" name="channel_type" id="channel_type" value="Twitter">
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
            <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $messageid; ?>">
            <input type="hidden" name="channel_type" id="channel_type" value="Twitter">

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

            <!-- </div> -->
            </div>   
        </div><!--end of col-md-3-->
      </div><!--end of row-->
    </div><!--end of col-12-->
  </div><!--end of poparea-->
<!-- <script src="<?=$SiteURL?>CRM/omnichannel_config/js/omni_config.js"></script>   -->
<script type="text/javascript">
// Function to scroll the chat to the bottom [Aarti][17-08-2024]
    function scrollToBottom() {
        $(".chat-box").stop().animate({ scrollTop: $(".chat-box")[0].scrollHeight}, 1000);
    }

    // Automatically scroll down when the page loads
    window.onload = scrollToBottom;

    function sendMessage(e) {
      e.preventDefault();
      // Get the input message value and trim any whitespace
      var messageInput = document.getElementById("dm_message");
      var messageText = messageInput.value.trim();
        var validationMessage = document.getElementById("response_msg");
        var attachments = $('#attachment').val();

         if (messageText == "" && attachments == "") {
            validationMessage.textContent = "Message cannot be blank!";
            validationMessage.style.display = "block";
            return; // Do not send the message if the input is empty
        } else {
            validationMessage.style.display = "none"; // Hide validation message if input is valid
            // Finally, submit the form using jQuery
            document.dm_form.submit();
        }       
    }
    
</script>
<script>
const templateBtn = document.getElementById('template');
const optionsBox = document.getElementById('template-options');

// Toggle template list on button click
templateBtn.addEventListener('click', function () {
    optionsBox.style.display = optionsBox.style.display === 'none' || optionsBox.style.display === '' ? 'block' : 'none';
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
    optionsBox.style.display = 'none';
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
function toggleSection(header) {
  $(header).toggleClass("open");
  $(header).next(".section-content").slideToggle();
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
</script>


<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script src="<?=$SiteURL?>/public/js/disposition_script.js"></script>