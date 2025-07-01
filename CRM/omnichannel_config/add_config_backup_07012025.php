<?php 
/***
 * Auth: Vastvikta Nishad
 * Date: 07 Mar 2024
 * Description: To add channels such as SMTP IMAP TWITTER FACEBOOK and SMS
 * 
*/
include_once('config_function.php');

$ID = base64_decode($_GET['id']);
$channel = base64_decode($_GET['channel']);

if(!empty($ID) && $channel != ''){
	$list = new OMNI_CLASS();
	if($channel == 'Smtp'){
		$list_data = $list->get_smt_list($ID);
	}else if($channel == 'Imap'){
		$list_data = $list->get_imap_list($ID);
	}else if($channel == 'Facebook'){
		$list_data = $list->get_facebook_list($ID);
	}else if($channel == 'Whatsapp'){
		$list_data = $list->get_whatsapp_list($ID);
	}else if($channel == 'Messenger'){
		$list_data = $list->get_messenger_list($ID);
	}else if($channel == 'Instagram'){
		$list_data = $list->get_instagram_list($ID);
	}else if($channel == 'Twitter'){
		$list_data = $list->get_twitter_list($ID);
	}else if($channel == 'SMS'){
		$list_data = $list->get_sms_list($ID);

	}
	$config = mysqli_fetch_array($list_data);
	$sms_types  = $config['sms_type'];

	if(isset($config['token_expire_date'])){
	 	$startdate = ($config['token_expire_date']!='') ? $config['token_expire_date'] : date("01-m-Y 00:00:00");
	}else{
	 	$startdate = ($_REQUEST['startdatetime']!='') ? $_REQUEST['startdatetime'] : date("01-m-Y 00:00:00");
	}
}
?>
<div class="col-sm-9 mt-3" style="padding-left:0">
    <div class="rightpanels"> 
		<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel Configuration Panel</span>
		<div class="style2-table">
			<!-- <form method="post" id="addconfig" name="addconfig" enctype="multipart/form-data">							 -->				
			<div>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">
					<tbody>
						<tr>
							<td align="left" valign="middle" width="7%" class="boder0-left boder0-right"><strong>Channel Option</strong></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="channel_name" class="channel_name">
									<?php if($channel == 'Smtp'){
										?><option value="Smtp" <?php if($channel == 'Smtp'){echo 'selected';}?>>SMTP Channel</option>
									<?php }?>
									<?php if($channel == 'Imap'){?>
										<option value="Imap" <?php if($channel == 'Imap'){echo 'selected';}?>>Imap Channel</option>
									<?php }?>
									<?php if($channel == 'Facebook'){?>
										<option value="Facebook" <?php if($channel == 'Facebook'){echo 'selected';}?>>Facebook Channel</option>
									<?php }?>
									<?php if($channel == 'Twitter'){?>
										<option value="Twitter" <?php if($channel == 'Twitter'){echo 'selected';}?>>Twitter Channel</option>
									<?php }?>
									<?php if($channel == 'SMS'){?>
										<option value="SMS" <?php if($channel == 'SMS'){echo 'selected';}?>>SMS Channel</option>
									<?php }?>
									<?php if($channel == 'Whatsapp'){?>
										<option value="Whatsapp" <?php if($channel == 'Whatsapp'){echo 'selected';}?>>WhatsApp Channel</option>
									<?php }?>
									<?php if($channel == 'Messenger'){?>
										<option value="Messenger" <?php if($channel == 'Messenger'){echo 'selected';}?>>Messenger Channel</option>
									<?php }?>
									<?php if($channel == 'Instagram'){?>
										<option value="Instagram" <?php if($channel == 'Instagram'){echo 'selected';}?>>Instagram Channel</option>
									<?php }?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- --------------------------------------------------------------------------- SMTP FORM ----------------------------------------------------------------------------------->
			<form name="addconfig" action="" id="addconfig" method="post" class="Smtp_form">
		  		<?php if(isset($config['v_name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="name" class="input-style1" value="<?php if(isset($config['v_name'])){ echo $config['v_name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Server IP<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="serverip" class="input-style1" value="<?php if(isset($config['v_server'])){ echo $config['v_server'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Port<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="port" class="input-style1" value="<?php if(isset($config['i_port'])){ echo $config['i_port'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">UserID <em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="userid" class="input-style1" value="<?php if(isset($config['v_username'])){ echo $config['v_username'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Password<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="password" class="input-style1" value="<?php if(isset($config['v_password'])){ echo $config['v_password'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">TLS/non-TLS <em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="tls" class="input-style1" value="<?php if(isset($config['i_tls'])){ echo $config['i_tls'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="smtp_status" class="">
									<option value="1" <?php if(isset($config['i_status']) && $config['i_status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['i_status']) && $config['i_status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 smtp_submit_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;">
			</div>
			</form>
			<!-- --------------------------------------------------------------------------- IMAP FORM ----------------------------------------------------------------------------------->
			
			<form name="imapconfig" action="" id="imapconfig" method="post" class="Imap_form" style="display: none">
					<?php if(isset($config['v_connectionname'])){ ?>
						<input type="hidden" name="action" value="edit_config">
						<input type="hidden" name="id" value="<?php echo $config['I_ID'];?>">
					<?php }else{ ?>
						<input type="hidden" name="action" value="submit_config">
					<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="imap_name" class="input-style1" value="<?php if(isset($config['v_connectionname'])){ echo $config['v_connectionname'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Server IP<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="imap_serverip" class="input-style1" value="<?php if(isset($config['v_ipaddress'])){ echo $config['v_ipaddress'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">UserID<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="imap_userid" class="input-style1" value="<?php if(isset($config['v_username'])){ echo $config['v_username'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Password<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="imap_password" class="input-style1" value="<?php if(isset($config['v_pasowrd'])){ echo $config['v_pasowrd'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Status<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="imap_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">OAuth Version<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="type_auth" class="type_auth">
									<option value="OAuth" <?php if(isset($config['v_type']) && $config['v_type'] == '1'){echo 'selected';} ?>>OAuth</option>
									<option value="OAuth2" <?php if(isset($config['v_type']) && $config['v_type'] == '0'){echo 'selected';} ?>>OAuth2</option>
								</select>
							</td>
						</tr>
						<tr class="auth2" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">ClientId<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="clientId" class="input-style1" value="<?php if(isset($config['v_client_id'])){ echo $config['v_client_id'];} ?>"/>
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Client Secret<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="clientsecret" class="input-style1" value="<?php if(isset($config['v_client_secret'])){ echo $config['v_client_secret'];} ?>"/>
							</td>
						</tr>
						<tr class="auth2" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Tenant<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="tenant" class="input-style1" value="<?php if(isset($config['v_tenant'])){ echo $config['v_tenant'];} ?>"/>
							</td>
						</tr>
				<tr class="auth">
					<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Token Expire Date<em> *</em></td>
					<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
						<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
					</td>
				</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 Imapsubmit_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;">
				</div>
			</form>
			<!-- --------------------------------------------------------------------------- TWITTER FORM ----------------------------------------------------------------------------------->
			
			<form name="twitterconfig" action="" id="twitterconfig" method="post" class="twitter_form" style="display: none">
		  		<?php if(isset($config['name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="facebook_name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Access Token<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="access_token" class="input-style1" value="<?php if(isset($config['access_token'])){ echo $config['access_token'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Access Token Secret<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="access_token_secret" class="input-style1" value="<?php if(isset($config['access_token_secret'])){ echo $config['access_token_secret'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Consumer Key <em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="consumer_key" class="input-style1" value="<?php if(isset($config['consumer_key'])){ echo $config['consumer_key'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Consumer Secret<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="consumer_secret" class="input-style1" value="<?php if(isset($config['consumer_secret'])){ echo $config['consumer_secret'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">OAuth Version<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="oauth_type" class="oauth_type_twitter">
									<option value="1" <?php if(isset($config['oauth_type']) && $config['oauth_type'] == '1'){echo 'selected';} ?>>Oauth V1</option>
									<option value="0" <?php if(isset($config['oauth_type']) && $config['oauth_type'] == '0'){echo 'selected';} ?>>Oauth V2</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="twitter_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Twitter Account Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="account_name" class="input-style1" value="<?php if(isset($config['account_name'])){ echo $config['account_name'];} ?>">
							</td>				
						</tr>
				<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right bearer_token" style="display: none;">Bearer Token<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right bearer_token" style="display: none;">
								<input type="text" name="bearer_token" class="input-style1" value="<?php if(isset($config['bearer_token'])){ echo $config['bearer_token'];} ?>">
							</td>
					<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Token Expire Date<em> *</em></td>
					<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
						<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
					</td>
						</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 submit_twitter_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>
			</form>
			<!-- --------------------------------------------------------------------------- SMS FORM ----------------------------------------------------------------------------------->
			
			<form name="smsconfig" action="" id="smsconfig" method="post" class="sms_form" style="display: none">
		  		<?php if(isset($config['name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">SMS Type<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<select name="sms_type" class="sms_type">
									<option value="onfonmedia" <?php if($config['sms_type'] == 'onfonmedia'){echo 'selected';}?>>onfonmedia</option>
									<option value="url_based" <?php if($config['sms_type'] == 'url_based'){echo 'selected';}?>>url based</option>
									<option value="exotel" <?php if($config['sms_type'] == 'exotel'){echo 'selected';}?>>exotel</option>
								</select>
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Name<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="sms_name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
						</tr>
						<tr class="sms_onfonmedia">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Api Key<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="api_key" class="input-style1" value="<?php if(isset($config['apikey'])){ echo $config['apikey'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">ClientId<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="client_id" class="input-style1" value="<?php if(isset($config['clientId'])){ echo $config['clientId'];} ?>">
							</td>
						</tr>				
						<tr class="sms_zambia" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Domain Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="domainname" class="input-style1" value="<?php if(isset($config['domain_name'])){ echo $config['domain_name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">UserID<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="userid" class="input-style1" value="<?php if(isset($config['userid'])){ echo $config['userid'];} ?>">
							</td>
						</tr>
						<tr class="sms_common">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">SenderId<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="senderId" class="input-style1" value="<?php if(isset($config['senderId'])){ echo $config['senderId'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="sms_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
						</tr>
						<tr class="sms_zambia" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Password<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="sms_password" class="input-style1" value="<?php if(isset($config['password'])){ echo $config['password'];} ?>">
							</td>
						</tr>
						<tr class="exotel" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Api Key<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="sms_apikey" class="input-style1" value="<?php if(isset($config['apikey'])){ echo $config['apikey'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Api Token<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="sms_apitoken" class="input-style1" value="<?php if(isset($config['api_token'])){ echo $config['api_token'];} ?>">
							</td>
						</tr>
						<tr class="exotel" style="display: none">
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Domain<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="sms_domain" class="input-style1" value="<?php if(isset($config['domain_name'])){ echo $config['domain_name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">SID<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="sms_sid" class="input-style1" value="<?php if(isset($config['sid'])){ echo $config['sid'];} ?>">
							</td>
						</tr>
						<tr class="exotel" style="display: none">
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="sms_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 submit_sms_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>

			</form>
			<!-- --------------------------------------------------------------------------- FACEBOOK FORM ----------------------------------------------------------------------------------->
			<form name="facebookconfig" action="" id="facebookconfig" method="post" class="facebook_form" style="display: none">
		  		<?php if(isset($config['name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Namess<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="facebook_name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">App Id<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="app_id" class="input-style1" value="<?php if(isset($config['app_id'])){ echo $config['app_id'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">App Token<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="app_token" class="input-style1" value="<?php if(isset($config['app_token'])){ echo $config['app_token'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Access Token<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="access_token_facebook" class="input-style1" value="<?php if(isset($config['access_token'])){ echo $config['access_token'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">App Secret<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="app_secret" class="input-style1" value="<?php if(isset($config['app_secret'])){ echo $config['app_secret'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="facebook_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">
						Token Expire Date<em> *</em></td>
					<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
						<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 submit_facebook_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>
			</form>
			<!-- --------------------------------------------------------------------------- WHATSAPP FORM ----------------------------------------------------------------------------------->

			<form name="whatsappconfig" action="" id="whatsappconfig" method="post" class="whatsapp_form" style="display: none">
		  		<?php if(isset($config['channel_name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">App Id<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="app_id" class="input-style1" value="<?php if(isset($config['app_id'])){ echo $config['app_id'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">App Token<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="app_token" class="input-style1" value="<?php if(isset($config['app_token'])){ echo $config['app_token'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Whatsapp URL<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="whatsapp_url" class="input-style1" value="<?php if(isset($config['whatsapp_url'])){ echo $config['whatsapp_url'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">STD<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="STD" class="input-style1" value="<?php if(isset($config['STD'])){ echo $config['STD'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="whatsapp_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
					</td>
				</tr>
				<tr>
					<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">
						Token Expire Date<em> *</em></td>
					<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
						<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="button-all2"><input type="button" class="button-orange1 submit_whatsapp_config" value="Submit" style="float: none;">
				<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>
			</form>
			<!-- --------------------------------------------------------------------------- Meesengger FORM ----------------------------------------------------------------------------------->

			<form name="massengerconfig" action="" id="messengerconfig" method="post" class="messenger_form" style="display: none">
		  		<?php if(isset($config['channel_name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">App Id<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="app_id" class="input-style1" value="<?php if(isset($config['app_id'])){ echo $config['app_id'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Access Token<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="access_token" class="input-style1" value="<?php if(isset($config['access_token'])){ echo $config['access_token'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Facebook URL<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="facebook_url" class="input-style1" value="<?php if(isset($config['facebook_url'])){ echo $config['facebook_url'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="messenger_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">
								Token Expire Date<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="button-all2"><input type="button" class="button-orange1 submit_messenger_config" value="Submit" style="float: none;">
						<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>
					</form>

								<!-- --------------------------------------------------------------------------- Instagram FORM ----------------------------------------------------------------------------------->

			<form name="instagramconfig" action="" id="instagramconfig" method="post" class="instagram_form" style="display: none">
		  		<?php if(isset($config['channel_name'])){ ?>
					<input type="hidden" name="action" value="edit_config">
					<input type="hidden" name="id" value="<?php echo $config['id'];?>">
				<?php }else{ ?>
					<input type="hidden" name="action" value="submit_config">
				<?php }?>
				<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">		
					<tbody>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Name<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="name" class="input-style1" value="<?php if(isset($config['name'])){ echo $config['name'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">App Id<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="app_id" class="input-style1" value="<?php if(isset($config['app_id'])){ echo $config['app_id'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">Access Token<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="access_token" class="input-style1" value="<?php if(isset($config['access_token'])){ echo $config['access_token'];} ?>">
							</td>
							<td align="left" valign="middle" width="17%" class="boder0-right">Instagram URL<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<input type="text" name="instagram_url" class="input-style1" value="<?php if(isset($config['instagram_url'])){ echo $config['instagram_url'];} ?>">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" width="17%" class="boder0-right">Status<em>*</em></td>
							<td width="33%" align="left" valign="middle" class="boder0-left">
								<select name="instagram_status" class="">
									<option value="1" <?php if(isset($config['status']) && $config['status'] == '1'){echo 'selected';} ?>>Active</option>
									<option value="0" <?php if(isset($config['status']) && $config['status'] == '0'){echo 'selected';} ?>>Inactive</option>
								</select>
							</td>
							<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">
								Token Expire Date<em> *</em></td>
							<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
								<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="button-all2"><input type="button" class="button-orange1 submit_instagram_config" value="Submit" style="float: none;">
						<input type="button" class="button-orange1 Cancel" value="Cancel" style="float: none;"></div>
					</form>

			<!-- </form> -->
		</div>
	</div>
</div>
<script src="<?=$SiteURL?>public/js/omnichannel.js"></script>
<?php if($channel){ ?>
<script type="text/javascript">
		setTimeout(function () {
			$('.channel_name').val('<?=$channel?>').trigger('change');
		},1000);		
</script>
<?php 
}?>
<?php if($sms_types){ ?>
<script type="text/javascript">
		setTimeout(function () {
			$('.sms_type').val('<?=$sms_types?>').trigger('change');
		},100);		
</script>
<?php 
}?>
