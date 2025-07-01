<?php 
/***
 * Auth: Vastvikta Nishad
 * Date:  20 Apr 2024
 * Description:To Create Delete and Reply to the SMS 
 * 
*/
// <!-- Channel Dispostion Code Added [Aarti][22-07-2024]-->
if(!isset($_SESSION)) 
{ 
session_start(); 
} 

// Include necessary files and database connection
include("../../config/web_mysqlconnect.php"); 

$Mid = $_GET['id'];
$phone =$_GET['phone'];

$query = mysqli_query($link,"select * from $db.sms_out_queue where id='$Mid'");
$fetch = $query->fetch_assoc();
$ICASEID = $fetch['ICASEID'];

$q=mysqli_fetch_row(mysqli_query($link,"select AccountNumber from $db.web_accounts where (phone like '%$phone%');"));
$customerid = $q[0];

$new_case_manual = base64_encode('new_case_manual');
if ($customerid != '') {
    $href = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&customerid=$customerid&smsid=$Mid&mr=13', '_blank');\"";
} else {
    $href = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&smsid=$Mid&mr=13', '_blank');\"";
}
 /* This code comment for agent disposition time update flag [Aarti][23-07-2024]*/
// mysqli_query($link,"update $db.tbl_smsmessages set Flag	='1' where i_id='$Mid'; ");
// get dispostion table details 
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$Mid' and channel_type='SMS'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
	$channel_remarks = $query_response['remarks'];
	$disposition_type = $query_response['disposition_type'];
}
?>
<html>
<head>
	<title>SMS Popup</title>
	<link rel="stylesheet" type="text/css" href="../../public/css/<?=$dbtheme?>.css"/>
</head>
<script type="text/javascript" src="../../public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
</script>
<style>
	body{background-color: #f5f5f5}
	table,tr,th,td{border-bottom:1px solid #ddd; font-size:12px; font-family:Arial,Sans Serif;}
</style>
<body>
	<div class="style-title">
	  <h3>SMS Info.</h3>
	</div>
	<div class="old-customer-simple-table">
	 <form action="" method="POST" name="myform">
	 <table class="tableview tableview-2 main-form new-customer">
       <tbody>
		<tr>
		<td width="52%" class="left boder0-right"><label style="padding-right:11px;"><strong><span style="text-decoration:underline;">Message</span> :
		 </strong></label></br></br>
		 <?=$fetch['v_smsString']?>
			</td>
		</tr>
		<tr>
			<td>
			<? if(($groupid=='080000') || ($groupid=='0000') || ($groupid=='070000')) { ?>
			<? if($ICASEID!=''){ 
				echo "<br><br><b style='color:green; font-size:16px;  background: rgba(185, 248, 185, 0.99);'>Case created.</b>";}else { ?>
			<input type='button' name='sub1' value='Create Case' class="button-orange1" <?=$href?>>
			<!-- <input type='button' name='delete' value='Delete SMS' class="button-orange1" onClick="return deleteSms()"> -->
			<input type='button'  value='Reply' class="button-orange1" onClick="JavaScript:window.open('sms_reply.php?phone=<?=$phone?>','_blank','height=350, width=400,scrollbars=0')">
			<? } 

			} ?>
			<br><br>
			<b id='success-msg' style='color:green; font-size:16px; background: rgba(185, 248, 185, 0.99);'></b>
			</td>
		</tr>
		<!-- Channel Dispostion Code Added [Aarti][22-07-2024] -->
		<tr><td><h6> Channel Disposition</h6></td></tr>
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
				<input type="hidden" name="channel_id" id="channel_id" value="<?php echo $Mid;?>">
				<input type="hidden" name="channel_type" id="channel_type" value="SMS">
				<label>Dispostion Remark</label>
                <div class="log-case">
                	<?php if(!empty($channel_remarks)){?>
                		<textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 500;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($channel_remarks); ?></textarea>
                	<?php }else{?>
                   		<textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 500;height: 100px;" class="input-style1"></textarea>
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
	</form>
	</div>	
</body>
	<script>

	function checkSms(url)
	{
		$.ajax({
			url: 'checkMail.php',
			type: 'post',
			data: {'id': '<?=$Mid?>','type': 'sms'},
			success: function(data, status) 
			{
				if(data!='') 
				{ 
					$("#success-msg").html(data);
					return false; 
				} 
				else 
				{ 
					window.opener.location = url; window.close(); 
				}
			},
			error: function(xhr, desc, err) {
			console.log(xhr);
			console.log("Details: " + desc + "\nError:" + err);
			}
		}); // end ajax call
	}


	function deleteSms()
	{
		if(confirm("Are you sure to delete SMS?"))
		{
			$.ajax({
				url: 'checkMail.php',
				type: 'post',
				data: {'id': '<?=$Mid?>','del': 'del','type': 'sms'},
				success: function(data, status) {
					
					if(data!='') 
					{ 
						$("#success-msg").html(data);
						return false; 
					} 
					else 
					{ 
						window.opener.cfrm.submit(); window.close();
					}
				},
				error: function(xhr, desc, err) {
					console.log(xhr);
					console.log("Details: " + desc + "\nError:" + err);
				}
			}); // end ajax call
		}
	}

	</script>

</html>
<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script src="<?=$SiteURL?>/public/js/disposition_script.js"></script>