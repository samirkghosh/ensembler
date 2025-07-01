<?php 
/***
 * Auth: Vastvikta Nishad
 * Date:  21 Apr 2024
 * Description:To Display Data Related to The Particular Email
 * 
*/
// <!-- Channel Dispostion Code Added [Aarti][22-07-2024]-->
if(!isset($_SESSION)) { 
    session_start(); 
} 
include_once("../../config/web_mysqlconnect.php");
$Mid = ($_GET['id']);

$showdiv=$_REQUEST['showdiv'];
//this function for subject decode content
function subject_decode_string($subject){
	$utf = substr($subject, 0, 10);
	if(strcasecmp($utf, "=?utf-8?B?") == '0'){
		$d = str_ireplace("=?utf-8?B?", "",$subject);//echo  $d."<br>";
		return base64_decode($d);
	}
	return $subject ;
}
//this code for delete email
if(isset($_POST['delete'])){
	mysqli_query($link,"update $db.web_email_information set i_DeletedStatus='2' where EMAIL_ID='$Mid'");
	?>
	<script type="text/javascript">
	window.opener.cfrm.submit();
	window.close();
	</script>
	<?
}
/* This code comment for agent disposition time update flag [Aarti][23-07-2024]*/
// mysqli_query($link,"update $db.web_email_information set Flag='1' where EMAIL_ID='$Mid'; ");

$query5 = mysqli_query($link,"select v_subject,v_fromemail,userid,v_body,ICASEID,case_open_time,i_DeletedStatus,V_rule,i_reminder from $db.web_email_information where EMAIL_ID='$Mid'");
$query5 = mysqli_fetch_array($query5);
$vsubject=$query5['v_subject'];
$vsubject = subject_decode_string($vsubject);
$vfromemail=$query5['v_fromemail'];
$i_reminder=$query5['i_reminder'];//some one is working condition
$caseopentime = $query5['case_open_time'];
$vuserid = $query5['userid'];
$v_body=$query5['v_body'];

if ($vsubject == 'Service Request Form'){ 
}else{
	$v_body=nl2br($v_body);
}
// $v_body = quoted_printable_decode($v_body);
$ICASEID=$query5['ICASEID'];
$i_DeletedStatus=$query5['i_DeletedStatus'];
$q=mysqli_fetch_row(mysqli_query($link,"select AccountNumber from $db.web_accounts where (email like '%$vfromemail%')"));
$customerid = $q[0];
$new_case_manual = base64_encode('new_case_manual');
if ($customerid != '') {
    $link_image = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&customerid=$customerid&emailid=$Mid&mr=6', '_blank');\"";
} else {
    $link_image = "onclick=\"window.open('../helpdesk_index.php?token=$new_case_manual&emailid=$Mid&mr=6', '_blank');\"";
}	

$query_sentiment = mysqli_query($link, "SELECT sentiment FROM $db.web_email_information  WHERE EMAIL_ID = '$Mid'");
$sentiment_data = mysqli_fetch_array($query_sentiment);
$selected_sentiment = $sentiment_data['sentiment'] ?? '';

// get dispostion table details 
$query_dis = mysqli_query($link,"select * from $db.multichannel_disposition where channel_id='$Mid' and channel_type = 'Email'");
$query_response = mysqli_fetch_array($query_dis);
if($query_response){
	$channel_remarks = $query_response['remarks'];
	$disposition_type = $query_response['disposition_type'];
}
function getCaseID_Subject($subject){
    $subject=string_between_two_string($subject,"[","]");   // TICKET # 200056
     if (strpos($subject,'TICKET #') !== false){
       $spos=strpos($subject, "#");
       $epos=strlen($subject);
       $caseID = substr($subject, $spos+1,$epos); 
     }else{
       $caseID = "";
     }
     return $caseID;
   }
    function string_between_two_string($str, $starting_word, $ending_word){ 
       $arr = explode($starting_word, $str); 
        if (isset($arr[1])){ 
           $arr = explode($ending_word, $arr[1]); 
           return $arr[0]; 
        } 
       return ''; 
	}
?>
<html>
<head>
	<title>Email Popup</title>
	<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/facebook.css"/>
</head>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
	function cheackMail(url){
		$.ajax({
	      url: 'checkMail.php',
	      type: 'post',
	      data: {'id': '<?=$Mid?>','type': 'email'},
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
	function deleteMail(){
		if(confirm("Are you sure to delete?")){
			$.ajax({
		      url: '../checkMail.php',
		      type: 'post',
		      data: {'id': '<?=$Mid?>','del': 'del','type': 'email'},
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
	.tableview .left {
	    text-transform: unset;
	}
	table,tr,th,td{border-bottom:1px solid #ddd; font-size:12px; font-family:Arial,Sans Serif;}
</style>
<body>
	<div class="style-title">
	  <h3>Email Info.</h3>
	</div>
	<div class="old-customer-simple-table">
	 <form action="" method="POST" name="myform">
	 <table class="tableview tableview-2 main-form new-customer">
       <tbody>
		<tr>
		<td width="52%" class="left boder0-right"><label style="padding-right:11px;"><strong><span style="text-decoration:underline;">Subject</span> : </strong></label></br></br><?=$vsubject?></td>
		</tr>
		<tr>
			<td width="52%" class="left boder0-right"><label style="padding-right:11px;"><strong>
			<?	
			$multi_attach = explode(",", $query5['V_rule']);
			$ii=0;
			foreach($multi_attach as $attach){ 
				if($attach=='../lead_doc/' || $attach=='' || f=='../uploaded/') { }else{ 
					$i++;
					if(strpos($attach,"imap/")!==false){ 
						$attach = "../".$attach; 
					}
					// updated the correct path [vastvikta][12-05-2025]
					$attach = "../../../" . $BasePath . "/imap/".$attach; // adding imap path - aarti:23-03-2023		
				?>

				<a href='javascript:void(0)' onClick="JavaScript:window.open('<?=$attach?>','_blank','height=350, width=600,scrollbars=0')" class='cptext'>Attachment <?=$i?>
				</a>&nbsp; &nbsp;&nbsp;
				<? } 
				}
				?>
	 		</strong></label>
			</td>		
		</tr>
		<tr>
			<td width="52%" class="left boder0-right"><label style="padding-right:11px;"><strong><span style="text-decoration:underline;">Descriptions</span> :
			 </strong></label></br></br>
			<?php
			$checkdecodeVal=decode_text_check($v_body);
			function decode_text_check($textVal){
				$x = substr($textVal,0, 50);
				$y = strpos($x, ' ');
				//echo "<br>[".$x."]<br>";
				
				if($y == "")
				{
			//		echo "<BR>Not Equal";
					return 1;
				}else{
			//		echo "<BR>Equal";
					return 0;
				}

			}
			//echo "<br>IF val is 1 Decode the string ::".$checkdecodeVal."<br><br>";
			if($checkdecodeVal==1){
				//$v_body_de=base64_decode($v_body,false);
				$x=str_replace('\n',"", $v_body);
					$x=str_replace('\r',"", $x);
			}
			?>
			<?=$v_body?></td>
		</tr>
		<?php if($_GET['classification']==0){?>
		<tr>
			<td>
				<label for="serviceable">Classification</label>
				<select name="serviceable" id="serviceable" class="select-styl1">
					<option value="">Select</option>
					<option value="1">Serviceable</option>
					<option value="2">Non servicable
					</option>
					<option value="3">Spam</option>
					<option value="4">Inquiry</option>
				</select>
			</td>
		<tr>
		<?php } ?>
		<tr>
			<td>
			<? if(($groupid=='080000') || ($groupid=='0000') || ($groupid=='070000')) { ?>
			<? if($ICASEID!='' && $ICASEID!='0'){ 
				echo "<br><br><b style='color:green; font-size:16px;  background: rgba(185, 248, 185, 0.99);'>Case created.</b>";}else if($i_DeletedStatus!=2 ){ ?>
			<?php
			// Calculate time difference
			$caseTime = new DateTime($caseopentime);
			$currentTime = new DateTime();
			$interval = $currentTime->getTimestamp() - $caseTime->getTimestamp();

			if ($i_reminder == 1 && $interval <= 30 && $vuserid != $_SESSION['userid']) { // 300 seconds = 5 minutes
				$userName = 'Someone'; // Default fallback

				$query = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID = ?";
				$stmt = mysqli_prepare($link, $query);
				
				if ($stmt) {
					mysqli_stmt_bind_param($stmt, 's', $vuserid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt, $fetchedName);
					if (mysqli_stmt_fetch($stmt)) {
						$userName = $fetchedName;
					}
					mysqli_stmt_close($stmt);
				}

				$disabled = 'disabled';
				$warningText = "<b style='color:red; font-size:16px;background: rgba(255,191,186);'><u>$userName</u> is working on it</b>";
				}
			?>

			<input type='button' name='sub1' value='Create Case' class='button-orange1' <?=$link_image?> <?= $disabled ?>>
			<?= $warningText ?>

			<?  } if($i_DeletedStatus!=2 && $i_reminder!=1){ ?>
		<!--	<input type='button' name='delete' value='Delete Email' class="button-orange1" onClick="return deleteMail()">  -->
			<? } 
			}
	        if(empty($ICASEID)){
	            $ICASEID=getCaseID_Subject($vsubject);
	               $ICASEID=trim($ICASEID);
	             if(!empty($ICASEID))
	             {
	               $where_c=" ticketid ='$ICASEID'";
	             }//not empty caseid
	         }
	        	$where_c=" ticketid ='$ICASEID'";
	         	$status='';
	        if(!empty($ICASEID)){
				global $link;
	            $qq= "select iCaseStatus, ticketid, iPID, vCustomerID  from $db.web_problemdefination where ( $where_c) ";
	             $q1=mysqli_query($link,$qq);
	             $numRows=mysqli_num_rows($q1);
	             $fetch1=mysqli_fetch_array($q1);
	             $case_id=$fetch1['iPID'];
	             $caseee=$fetch1['ticketid'];
	             $Customerid =$fetch1['vCustomerID'];
	             //echo "select email from $db.web_accounts where AccountNumber='".$Customerid."'";
	             $qcust=mysqli_fetch_array(mysqli_query($link,"select email from $db.web_accounts where AccountNumber='".$Customerid."'"));
	             //echo "<br>Cust Email::".$qcust['email'].'**'.$vfromemail;
	             if( ($qcust['email']==$vfromemail)  && $qcust['email']!='')
	             {
	                $q= "update $db.web_email_information set ICASEID='".$caseee."',email_test='web_queue_1' where EMAIL_ID='".$vfromemail."' ; ";
	                mysqli_query($link,$q);
	             }//customer mail is equal to from mail
	             $ress = mysqli_fetch_array(mysqli_query($link,"select iCaseStatus,ticketid from $db.web_problemdefination where (iPID='$case_id') "));
	          $rest=mysqli_fetch_array(mysqli_query($link,"select ticketstatus from $db.web_ticketstatus where id='".$ress['iCaseStatus']."' ; "));
	           $status = $rest['ticketstatus'];
	        }
	        if ($ICASEID!='' && $case_id!=''){  
	           ?>
				<input type='button'  value='Reply' class="button-orange1" onClick="JavaScript:window.open('web_send_email_reply.php?replyid=<?=$Mid?>&amp;iid=<?=$caseee?>&amp;reply_to=<?=$vfromemail?>','_blank','height=550, width=900,scrollbars=0')">
	           <?}else{?>
				<input type='button'  value='Reply' class="button-orange1" onClick="JavaScript:window.open('web_send_email_reply.php?replyid=<?=$Mid?>&amp;iid=ensembler&amp;reply_to=<?=$vfromemail?>','_blank','height=550, width=900,scrollbars=0')">
	    	<?}?>
			<br><br>
			<b id='success-msg' style='color:green; font-size:16px; background: rgba(185, 248, 185, 0.99);'><? if($i_DeletedStatus==2){ echo "Email is already deleted!"; }?></b>
			</td>
		</tr>
		<!-- added condition to hide dispose and setiment in case creation[vastvikta][13-03-2025] -->
		<?php if ($showdiv != 1) { ?>
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
				<input type="hidden" name="channel_type" id="channel_type" value="Email">
				<label>Dispostion Remark</label>
                <div class="log-case">
                	<?php if(!empty($channel_remarks)){?>
                		<textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 500;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($channel_remarks); ?></textarea>
                	<?php }else{?>
                   		<textarea name="email_remark" id="email_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 500;height: 100px;" class="input-style1"><?php echo htmlspecialchars_decode($fact); ?></textarea>
                   	<?php }?>
                </div>
			</td>
		</tr>
		<tr>
			<td>
				<input name="Submit" type="submit" value="Disposition" class="button-orange1" style="float:inherit;" id="create_disposition"/>
			</td>
		</tr>
	</table>
	<div class="tableview main-form new-customer">
                    <table class="tableview tableview-2 main-form new-customer">
                        <tbody>
                            <tr>
                                <td><h6>Sentiment</h6></td>
                            </tr>
                            <tr>
                                <td>
								<input type="hidden" name="channel_id" id="channel_id" value="<?php echo $Mid;?>">
								<input type="hidden" name="channel_type" id="channel_type" value="Email">
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
							<?php }?>
                        </tbody>
                    </table>
                <!-- </div> -
	</form>
	</div>	
</body>
<!-- Channel Dispostion Code Added [Aarti][22-07-2024]--> 
<script src="<?=$SiteURL?>/public/js/disposition_script.js"></script>