<?php 
/***
 * USER HOME PAGE
 * Author: Aarti Ojha
 * Date: 14-03-2024
 * Description: Display All User Details List with filter and pagination option
 **/

$companyid	= $_SESSION['companyid'];
$comp_no_users = F_Count_UserID($companyid,$db); // Fetch Count User //0
$comp_no_userlicense = F_CompanyUserLicense($companyid,$db); // Fetch company user license details //null
$count_admin_user=F_Count_Admin_User($companyid,$db); // Fetch admin user license details
$count_agent_user=F_Count_Agent_User($companyid,$db); // Fetch agent user license details
$timeview		=	$_POST['timeview'];



// Close

?>	
<!-- Html code start  -->
<form method="post" name="frmuser" id="frmuser">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">View Userss</span>
	<div class="style2-table">
		<div class="style-title2">
			<div class="row">
				<div class="col-sm-6">
					<p class="mt-1"> Select a view from the drop down list to see a list of related results</p>
				</div>
				<div class="col-sm-6">
					<?php $department = department_list(); 
					 $web_usercreate = base64_encode('web_usercreate');
					 $web_userdetailview = base64_encode('web_userdetailview');
					 ?>
						<select name="department" id="department" class="input-style2" style="width: 130px; height: 30px;">
							<option value="">Select Role</option>
							<?php 
								$sel = "";
								while($row=mysqli_fetch_array($department)){
									$gps=$row['DisplayName'];
									if($gps == $_POST['department']){
										$sel = "selected";
									}else{
										$sel = "";
									}
								?>
								<option value="<?=$gps?>" <?=$sel?>><?=$gps?></option>
							<?php }?>
						</select>
						<select name="timeview" class="input-style2" style="width: 80px; height: 30px;">
							<option value="" <?=(!isset($timeview) || $timeview === '') ? 'selected' : ''?>> ALL </option>
							<option value="1" <?=($timeview == 1) ? 'selected' : ''?>> Active </option>
							<option value="0" <?=( $timeview== 0 && $timeview != '') ?'selected' : ''?>> InActive </option>
						</select>

					<input type="submit" class="button-orange1" value='Go' style="width: 40px;" />
					<? if(($comp_no_users ) < ($comp_no_userlicense)){ ?>
					<input name="reset" id="reset" type="button" value="RESET" class="button-orange1" style="width: 60px;">
					<input type="button" class="button-orange1" value='New User' style="width: 80px;" onClick="window.location.href='user_index.php?token=<?php echo $web_usercreate;?>'" />
					<? }?>
				</div>
			</div>
		</div>
	    <?php 
	    	/** Fetch User list function call **/ 						
			$query_response = user_home_list();
			$numrows = mysqli_num_rows($query_response);
	    ?>
		<div class="table">
			<div class="wrappes">
				<div>
					<input type="hidden" name="count" value="<?=$numrows?>" />
					<!-- Table code start -->
					<table width="595" class="tableview tableview-2" id="userTable">
						<thead>
							<tr class="background">
								<td align="center" valign="middle">User Name(User Id)</td>
								<td align="center" valign="middle">User Role</td>
								<td align="center" valign="middle">Email </td>
								<?php if($groupid=='0000'){?>
								   <td align="center" valign="middle">Reset Password</td >
								<?php } ?>

							</tr>

						</thead>
						<tbody>
					<?php
						$count=0;
						while($row5=mysqli_fetch_array($query_response)){
							$Agents[] = $row5 ;
						}
						if(count($Agents) > 0){
							$count_row = 0 ;
							foreach ($Agents as $akey => $val) {
								$AtxUserName=	$val['AtxDisplayName'];
								$AN			=	$val['AtxUserName'];
								$User		=	$val['AtxUserID'];
								$designation=	$val['AtxDesignation'];
								$mobile		=	$val['AtxMobile'];
								$status		=	$val['AtxUserStatus'];
								$email		=	$val['AtxEmail'];
								$AtxUserCat = 	$val['AtxUserCat'];
								$count		=	$count+1;
								$check		=	'check'.$count;
								// Fetch group name
								$sql3="select a.DisplayName,a.atxGid from $db.unigroupid a, $db.unigroupdetails b where a.atxGid=b.atxGid and b.ugdContactID='$User'";
								$res3=mysqli_query($link,$sql3);
								$row3=mysqli_fetch_array($res3);
								$department=$row3['DisplayName'];
								
								$clr = ($count%2==0) ? 'white' : '#EFEFEF'; ; 
							?>
								<tr>
									<td width="10%">
										<input type="checkbox" name="<?=$check?>" id="<?=$check?>"
											value="<?=$User?>"
											style="margin:2px 5px -10px 0; float:left;" />
										<a href="user_index.php?token=<?php echo $web_userdetailview;?>&id=<?=$User?>"><?=$AN?> (<?=$User?>)
										</a>
										<? if($status==0){ ?>
										<font face="verdana" color="red">&nbsp; *</font>
										<? } ?>
									</td>
									<!--changed department to designation[vastvikta][31-01-2025] -->
									<td width="10%"> <?=$designation?></td>
									<td width="10%"> <?=$email?></td>
									<?if($groupid=='0000'):
									  if($department == 'Non Login User')continue;
									?>
									<td  width="5%"><center><a href="javascript:void(0)" class="reset" id="<?=$email?>"><i class="fa fa-refresh" aria-hidden="true"></i></a></center></td>
						            <?endif;?>
								</tr>
								<?php
							}
						}	
						?>	
						</tbody>						
					</table>
					<!-- Table code close -->
				</div>
			</div>
		</div>
		<?php
		if($numrows>0) { ?>
		<table width="95%" align="center" border="0" class="tableview tableview-2 main-form">					
			<tr>
				<td height="20" align="right">
					<?php if(($timeview=='')||($timeview=='1')){										
						if(($comp_no_users) >= ($comp_no_userlicense)){ ?>
							<p id="display-error2" style="margin-top: -2px;">To increase number of Agent/admin license, Please contact
							<a href='info@alliance-infotech.com'>info@alliance-infotech.com</a></p>
						<? }?>
						<center><a href="#" class="button-orange1 user_delete" style="float:inherit;text-decoration:none;color:black" data-id="<?=$User?>"><ins>Delete</ins></a></center>
					<?}?>
				</td>
			</tr>
		</table>
		<?php }?>
	</div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
<script>
    $(document).ready(function() {
        $('#reset').click(function(event) {
        	var encodedToken = btoa('web_userhome');
			window.location.href = "user_index.php?action=view_category&token=" + encodeURIComponent(encodedToken);
        });
    });
</script>
