<?php 
/***
 * Auth: Vastvikta Nishad
 * Date:  07 Mar  2024
 * Description: Debug the Data Related to Different channels 
 * Mainly SMTP IMAP Twitter Facebook and SMS
 * 
*/
include_once('config_function.php');
$ID = base64_decode($_GET['id']);
$channel = base64_decode($_GET['channel']);
?>
<style type="text/css">
	.error{
		color: red;
	}
</style>
<div class="col-sm-9 mt-3" style="padding-left:0">
    <div class="rightpanels"> 
	<?php if($channel == 'Smtp'){ ?>
	<form name="testconfig" action="" id="testconfig" method="post">
	    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel SMTP Configuration Panel</span>
	    <div class="style2-table">
	      		<input type="hidden" name="action" value="test_channel">
	      		<input type="hidden" name="id" value="<?php echo $ID;?>">
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form" id="table_display" style="margin-bottom: 25px;">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Email To<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="email" name="email_to" class="input-style1" value="">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Subject<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="subject" class="input-style1" value="">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Body<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<textarea name="body" class="input-style1" value="">Dear Customer, Test messages from omni channel team, Thank you </textarea>
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Attactment</td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="checkbox" name="attctment_check" id="attctment_check" class="attctment_check" value="1">
								<input type="file" name="file" class="input-style1 file" value="" style="display: none">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="output_display tableview tableview-2 main-form" style="font-weight: 600;text-align: center;display: none;">
				</div>
			<div class="button-all2"><input type="button" class="button-orange1 test_channel" value="Testing" style="float: none;">
				<input type="button" class="button-orange1 Refresh" value="Refresh" style="float: none;"> 
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

		</div>
	</form>
	<?php }else if($channel == 'Twitter'){?>
	<form name="twitterconfig" action="" id="twitterconfig" method="post">
	    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel Twitter Configuration Panel</span>
	    <div class="style2-table">
	      		<input type="hidden" name="action" value="twitter_debug_channel">
	      		<input type="hidden" name="id" value="<?php echo $ID;?>">
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form" id="table_display" style="margin-bottom: 25px;">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Method<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="method" class="method_type">
								<option value="GET">GET</option>
								<option value="POST">POST</option>
							</select>
							</td>
						</tr>
						<tr class="show_post" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Recipient Id<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="recipient_id" class="input-style1" value="">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Message<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<textarea name="message" class="input-style1" value="">Dear Customer, Thanks for the tweet</textarea>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="output_display_twitter tableview tableview-2 main-form" style="font-weight: 600;text-align: center;display: none;">
				</div>
			<div class="button-all2"><input type="button" class="button-orange1 twitter_debug_channel" value="Testing" style="float: none;">
				<input type="button" class="button-orange1 Refresh" value="Refresh" style="float: none;"> 
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

		</div>
	</form>
	<?php }else if($channel == 'SMS'){?>
	<form name="smsconfig" action="" id="smsconfig" method="post">
	    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel SMS Configuration Panel</span>
	    <div class="style2-table">
	      		<input type="hidden" name="action" value="sms_debug_channel">
	      		<input type="hidden" name="id" value="<?php echo $ID;?>">
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form" id="table_display" style="margin-bottom: 25px;">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Number<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="number" class="input-style1" value="">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Message<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<textarea name="sms_message" class="input-style1" value="">Dear Customer, Thanks for the sms</textarea>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="output_display_sms tableview tableview-2 main-form" style="font-weight: 600;text-align: center;display: none;">
				</div>
			<div class="button-all2"><input type="button" class="button-orange1 sms_debug_channel" value="Testing" style="float: none;">
				<input type="button" class="button-orange1 Refresh" value="Refresh" style="float: none;"> 
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

		</div>
	</form>
	<?php }else if($channel == 'Facebook'){?>
		<form name="facebookconfig" action="" id="facebookconfig" method="post">
	    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel SMS Configuration Panel</span>
	    <div class="style2-table">
	      		<input type="hidden" name="action" value="facebook_debug_channel">
	      		<input type="hidden" name="id" value="<?php echo $ID;?>">
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form" id="table_display" style="margin-bottom: 25px;">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Method<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="facebook_method" class="method_type">
								<option value="GET">GET</option>
								<option value="POST">POST</option>
							</select>
							</td>
						</tr>
						<tr class="show_post" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">PostId<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="postid" class="input-style1" value="">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="output_display_facebook tableview tableview-2 main-form" style="font-weight: 600;text-align: center;display: none;">
				</div>
			<div class="button-all2"><input type="button" class="button-orange1 facebook_debug_channel" value="Testing" style="float: none;">
				<input type="button" class="button-orange1 Refresh" value="Refresh" style="float: none;"> 
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

		</div>
	</form>

		<?php }else if($channel == 'Whatsapp'){?>
		<form name="Whatsappconfig" action="" id="whatsappconfig" method="post">
	    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel Whatsapp Configuration Panel</span>
	    <div class="style2-table">
	      		<input type="hidden" name="action" value="whatsapp_debug_channel">
	      		<input type="hidden" name="id" value="<?php echo $ID;?>">
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form" id="table_display" style="margin-bottom: 25px;">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Method<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="whatsapp_method" class="method_type">
								<option value="GET">GET</option>
								<option value="POST">POST</option>
							</select>
							</td>
						</tr>
						<tr class="show_post" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">PostId<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="postid" class="input-style1" value="">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="output_display_whatsapp tableview tableview-2 main-form" style="font-weight: 600;text-align: center;display: none;">
				</div>
			<div class="button-all2"><input type="button" class="button-orange1 whatsapp_debug_channel" value="Testing" style="float: none;">
				<input type="button" class="button-orange1 Refresh" value="Refresh" style="float: none;"> 
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

		</div>
	</form>

	<?php }?>
</div>
</div>
<script src="omnichannel_config/js/omnichannel.js"></script>