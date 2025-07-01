<?php
/**
 * Auth:Vastvikta Nishad
 * Description: Sends the Reply and  for Setting the Disposition 
 * Date: sep 2 2024
 */
// Include necessary files and database connection
include("../../config/web_mysqlconnect.php"); 
$web_chat_id = $_REQUEST['id'];
$phone = $_REQUEST['from'];


// Get disposition table details 
$query_dis = mysqli_query($link, "SELECT * FROM $db.multichannel_disposition WHERE channel_id = '$web_chat_id' AND channel_type = 'web_chat'");
$query_response = mysqli_fetch_array($query_dis);
if ($query_response) {
    $channel_remarks = $query_response['remarks'];
    $disposition_type = $query_response['disposition_type'];
}
// Get sentiment value from the database
$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.overall_bot_chat_session WHERE id = '$web_chat_id'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Disposition</title>
    <link rel="stylesheet" href="../../public/css/<?=$dbtheme?>.css"/>
    <link href="<?=$SiteURL?>public/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/web_sent_dm.css">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/channel_all_style.css">
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/font-awesome/4.5.0/css/font-awesome.min.css"/> 
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css"/> 
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/select2-bootstrap.min.css"> 
</head>
<body>
    <style>
.log-case{
width: 475px;
}

h6 {
    font-weight: bold;
    margin-bottom: 15px;
}

.input-style1 {
   
    border: 1px solid #ccc;
    border-radius: 5px;
}



        </style>
<div class="popup_area">
    <div class="row">
        <!-- Channel Disposition Section -->
        <div class="col-md-6">
            <div class="tableview main-form new-customer">
                <table class="tableview tableview-2 main-form new-customer">
                    <tbody>
                        <tr>
                            <td><h6>Channel Disposition</h6></td>
                        </tr>
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
                                            <option value="<?php echo $group_res['name']; ?>" <?php if ($group_res['name'] == $disposition_type) { echo 'selected'; } ?>>
                                                <?php echo $group_res['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $web_chat_id; ?>">
                                <input type="hidden" name="channel_type" id="channel_type" value="web_chat">
                                <label>Disposition Remark</label>
                                <div class="log-case">
                                    <?php if (!empty($channel_remarks)) { ?>
                                        <textarea name="email_remark" id="email_remark" type="text" class="input-style1" style="width:100%; height:100px;"><?php echo htmlspecialchars_decode($channel_remarks); ?></textarea>
                                    <?php } else { ?>
                                        <textarea name="email_remark" id="email_remark" type="text" class="input-style1" style="width:100%; height:100px;"></textarea>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input name="Submit" type="submit" value="Disposition" class="button-orange1" id="create_disposition"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sentiment Section -->
        <div class="col-md-6">
    <div class="tableview main-form new-customer">
        <table class="tableview tableview-2 main-form new-customer">
            <tbody>
                <tr>
                    <td><h6>Sentiment</h6></td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="channel_id" id="channel_id" value="<?php echo $web_chat_id; ?>">
                        <input type="hidden" name="channel_type" id="channel_type" value="web_chat">
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script type="text/javascript">
   $('#create_disposition').on('click',function(e){
	var dispostion_type = $('#dispostion_type').val();
	var remarks= $('#email_remark').val();
	var channel_id= $('#channel_id').val();
	var channel_type = $('#channel_type').val();
	e.preventDefault();
	$.ajax({
		url: 'servicable_emailqueue_popup.php',
		method: 'POST',
		data: {
			dispostion_type: dispostion_type,action :'dispostion_channel_insert',
			channel_id:channel_id,remarks:remarks,channel_type:channel_type
		},
		success: function(data) {
			location.reload();
		}
	});	
});
$('#submit_sentiment').on('click', function (e) {
    var sentiment = $('#sentiment').val();
    var channel_type = $('#channel_type').val();
    var channel_id = $('#channel_id').val();

    e.preventDefault();
    $.ajax({
        url: 'servicable_emailqueue_popup.php',
        method: 'POST',
        data: {
            sentiment: sentiment,
            channel_type: channel_type,
            channel_id: channel_id,
            action: 'sentiment'
        },
        success: function (data) {
            location.reload();
        }
    });
});

</script>
</body>
</html>
