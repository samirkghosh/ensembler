<?php 
/*****************************************************************************************************
AUTHOR ::Vastvikta Nishad
Date of creation:17 Apr 2024
Purpose :: To Create Case of Twitter and Delete The Twitter 

*****************************************************************************************************/
// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");
$Mid = $_GET['id'];

######################   Get The EMAIL TABLE VALUE  ########################
//echo "select v_smsString,v_FromSIM from $dbname.tbl_smsmessagesin where i_id='$Mid'";

if(isset($_POST['delete']))
{
	mysqli_query($link,"update $dbname.tbl_tweet set i_Status='2' where i_ID='$Mid'");
	?>
	<script type="text/javascript">
	window.opener.cfrm.submit();
	window.close();
	</script>
	<?
}

mysqli_query($link,"update $db.tbl_tweet set Flag='1' where i_ID='$Mid'; ");

$query5 = mysqli_query($link,"select v_Screenname,i_TweetID,v_TweeterDesc,ICASEID,i_Status,i_reminder from $dbname.tbl_tweet where i_ID='$Mid'");
$query5 = mysqli_fetch_array($query5);
$vfromemail=$query5['i_TweetID'];
$v_body=$query5['v_TweeterDesc'];
$ICASEID=$query5['ICASEID'];
$i_DeletedStatus=$query5['i_Status'];
$v_Screenname=$query5['v_Screenname'];
$i_reminder=$query5['i_reminder'];

$q=mysqli_fetch_row(mysqli_query($link,"select AccountNumber from $db.web_accounts where (fname = '$v_Screenname' OR (twitterhandle = '$v_Screenname' and twitterhandle!='') );"));
$customerid = $q[0];


$new_case_manual = base64_encode('new_case_manual');
if ($customerid != '') {
    $link_images = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&customerid=$customerid&twitterid=$Mid&mr=3', '_blank');\"";
} else {
    $link_images = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&twitterid=$Mid&mr=3', '_blank');\"";
}
// get dispostion table details 
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$Mid' and channel_type='Twitter'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
	$channel_remarks = $query_response['remarks'];
	$disposition_type = $query_response['disposition_type'];
}
?>
<html>
<head>
	<title>X Popup</title>
	<!-- Dynamic css -->
	<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css"/>
</head>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
	
	function cheackMail(url)
	{
		$.ajax({
	      url: 'checkMail.php',
	      type: 'post',
	      data: {'id': '<?=$Mid?>','type': 'twitter'},
	      success: function(data, status) {
	      	$("#success-msg").html(data);
	      	if(data!='') { return false; } 
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

	function deleteMail()
	{
		if(confirm("Are you sure to delete?"))
	{
		$.ajax({
	      url: 'checkMail.php',
	      type: 'post',
	      data: {'id': '<?=$Mid?>','del': 'del','type': 'twitter'},
	      success: function(data, status) {
	      	//alert(data); //return false;
	      	$("#success-msg").html(data);
	      	if(data!='') { return false; } 
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
<style>

body{background-color: #f5f5f5}
table,tr,th,td{border-bottom:1px solid #ddd; font-size:12px; font-family:Arial,Sans Serif;}
</style>
<body>
	<div class="style-title">
	  <h3>X Info</h3>
	</div>
	<div class="old-customer-simple-table">
		<form action="" method="POST" name="myform">
			<table class="tableview tableview-2 main-form new-customer">
		       <tbody>	
					<tr>
						<td width="52%" class="left boder0-right"><label style="padding-right:11px;"><strong><span style="text-decoration:underline;">Message</span> : </strong></label></br></br><?=$v_body?></td>
					</tr>
					<tr>
						<td>
						<? if(($groupid=='080000') || ($groupid=='0000') || ($groupid=='070000')) { ?>
						<? if($ICASEID!='' && $ICASEID!='0'){ echo "<br><br><b style='color:green; font-size:16px;  background: rgba(185, 248, 185, 0.99);'>Case created.</b>";}else if($i_DeletedStatus!=0){ ?>
						<input type='button' name='sub1' value='Create Case' class="button-orange1" <?=$link_images?>>
						<?  } if($i_DeletedStatus!=2 && $i_reminder!=1){ ?>
						<!-- <input type='button' name='delete' value='Delete' class="button-orange1" onClick="return deleteMail()"> -->
						<? }

						 } ?>
						<br><br>
						<b id='success-msg' style='color:green; font-size:16px; background: rgba(185, 248, 185, 0.99);'><? if($i_DeletedStatus==0){ echo "Message is already deleted!"; }?></b>
						</td>
					</tr>
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
							<input type="hidden" name="channel_type" id="channel_type" value="Twitter">
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
							<input name="Submit" type="submit" value="Submit" class="button-orange1" style="float:inherit;" id="create_disposition"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>	
</body>
</html>
<script src="<?=$SiteURL?>/public/js/disposition_script.js"></script>