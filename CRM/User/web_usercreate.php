<?php
/***
 * USER CREATE PAGE
 * Author: Aarti Ojha
 * Date: 14-03-2024
 * Description: Create User with Multiple Role option Like - Backoffice,Admin,general report,non login user
 **/

$companyid = $_SESSION['companyid'];
$company_name=company_name($companyid); // Fetch company name
// Fetching necessary data
$d = date("F j ,Y,g:i a");
$name =$_GET['username'];
$flag_company=$_GET['company'];
if(empty($flag_company))

$flag_company	=	($_POST['company']);
$action=$_GET['action'];
$username1=$_GET['username1'];
$user_group= $_SESSION['user_group'];

if($action=='add'){
	$countuser=0;
	$sql_user="select count(*) as countuser from uniuserprofile where AtxUserName='$username1'";
	$res_user=mysqli_query($link,$sql_user);
	$num_rows=mysqli_fetch_array($res_user);
	if($num_rows["countuser"]>0){?>
	<script language="JavaScript">
	   alert("User Id Already Exist"); 
	</script>
<?php } 
} 
/* [27-03-2024]	[Aarti] This code for edit user detail fetch in database and display
** 
*/ 
$user=$_GET['id'];
if(!empty($user)){
	// Step-1 Fetching user personal information in uniuserprofile table
	 $sql="select * from $db.uniuserprofile where AtxUserID='$user'";
	$res=mysqli_query($link,$sql);
	while($row=mysqli_fetch_array($res)){
// echo"<pre>"; print_r($row);
		$username1=$row['AtxDisplayName'];
		$atxusernae			=	$row['AtxUserName'];
		// Fetch IP address 
		$sql_ip="select IP_Address from $db.encryp1 where UserName='$atxusernae' AND Status = 1";
	    $res_ip=mysqli_query($link,$sql_ip);
	    $row_ip=mysqli_fetch_array($res_ip);
	    $ip_address=$row_ip['IP_Address'];

		$fname					=	$row['GivenName'];
		$sn						=	$row['sn'];
		$atxusername			=	$row['AtxUserName'];
		$displayname			=	$row['AtxDisplayName'];
		$company				=	$row['AtxCompany'];
		$jobtitle				=	$row['AtxDesignation'];
		$email					=	$row['AtxEmail'];
		$password				=	$row['AtxPassword'];
		$mailpassword			=	$row['AtxEmailPwd'];
		$category				=	$row['AtxUserCat'];
		$bday1					=	$row['AtxBirthDate'];
		$bday					=	view_dateformat1($bday1);	
		$gender					=	$row['AtxGender'];
		$street					=	$row['AtxStreet'];
		$city					=	$row['AtxCity'];
		$state					=	$row['AtxState'];
		$country				=	$row['AtxCountry'];
		$pincode				=	$row['AtxPincode'];
		$phone					=	$row['AtxHomePhone'];
		$contactphone			=	$row['AtxContactPhone'];
		$fax					=	$row['AtxContactFax'];
		$pager					=	$row['AtxPager'];
		$mobile					=	$row['AtxMobile'];
		$desc					=	$row['AtxDescription'];
		$status					=	$row['AtxUserStatus'];
		$level					=	$row['AtxUserCat'];
		//WFM Changes - aarti ojha 29-03-2
		$shift_id				=	$row['i_shiftpref'];
		$skills 				=	$row['i_Skillset'];
		$skill 					=explode(',',$skills);
        $i_classify = $row['i_classify'];
        $job_title=$row['AtxDesignation'];
        $date_leave=$row['LeavingDate'];
		$date_leave1=view_dateformat1($date_leave);
		if($date_leave1 == '--'){
			$date_leave1 = '';
		}
		// if user edit mode then some fields readonly 
		$readonly = 'readonly';
	}
	// Step-2 Fetching user vProjectAssign information in userhead table	
	$reportto='';
	$assignto='';
	$sql2="select HeadID,vProjectAssign from $db.userhead where UserID='$user'";
	$res=mysqli_query($link,$sql2) or die(mysqli_error($link));
	if(mysqli_num_rows($res)){
		$row=mysqli_fetch_array($res);
		$reports=$row['HeadID'];
		$assigns=$row['vProjectAssign'];
		$reportto=explode(',',$reports);
		$assignto=explode(',',$assigns);
	}
	// Step-3 Fetching user unigroupdetails information in unigroupid table			
	$sql3="select a.DisplayName,a.atxGid from $db.unigroupid a, $db.unigroupdetails b where a.atxGid=b.atxGid and b.ugdContactID='$user'";
	// echo $sql3;
	$res3=mysqli_query($link,$sql3);
	$row=mysqli_fetch_array($res3);
	$department=$row['DisplayName'];
	$dept=$row['atxGid'];
}
if((empty($date_leave1))&&(empty($date_leave))&&(empty($user))){
	$date_leave1 = date("d-m-Y");
	}
	// echo "date"$date_leave1;
//[Aarti][16-04-2024] for social media channel license flow code
$query = "SELECT * FROM $db.user_channel_assignment WHERE userid='$user'";
$res =mysqli_query($link,$query);
$data_channel = array();
if (mysqli_num_rows($res) > 0) {
   while ($adminrow = mysqli_fetch_assoc($res)) {
   	  $data_channel[$adminrow['channel_type']] = $adminrow['channel_type'];
   }
}
?>
<? $heading="Add: New User"; if($flag_company==1) { $heading = $heading." to a Company"; } 
if(!empty($user)){
	$heading="User Detail";
}

?>
<!-- updated code for adding additional info [vastvikta][31-01-2025] -->
<style>
    .tooltip-icon {
        cursor: pointer;
        color: blue;
        position: relative;
    }

    .tooltip-icon:hover::after {
        content: attr(data-title);
        white-space: pre-line; /* Allows line breaks */
        position: absolute;
        background: #fff;
        color: black;
        padding: 8px;
        border: 2px solid #abaaab; /* ✅ Added border */
    	border-radius: 2px;
        top: 20px;
        left: 0;
        z-index: 100;
        font-size: 12px;
        width: max-content;
        max-width: 200px;
		font-family: -apple-system, BlinkMacSystemFont, "San Francisco", "Helvetica Neue", Arial, sans-serif;

    }
</style>
<!-- HTML Code Start -->
	<input type="hidden" name="mode" id="mode" />
	<span class="breadcrumb_head" style="height:37px;padding:9px 16px"><?=$heading?></span>
	<div class="style2-table">
		<form method="post" name="adduserfrm" id="adduserfrm" enctype="multipart/form-data">	
			<!-- this condition for edit and create user if user edit mode action edit and create mode action create -->
			<?php if(!empty($user)){?>
				<input type="hidden" name="action" value="edit_user" id="edit_user">
				<input type="hidden" name="userid" value="<?php echo $user;?>" id="userid">
			<?php }else{?>
				<input type="hidden" name="action" value="create_user" id="create_user">
			<?php }?>
			<table cellspacing="0" cellpadding="1" width="98%" border="0" align="left" class="tableview tableview-2 main-form">				
				<tr>
				<td align="left" valign="middle" width="18%" class="boder0-left boder0-right">
					First&nbsp; Name<em> * 
						<span style="cursor: pointer; color: blue;" title="(First name of the user)">
							<i class="fas fa-info-circle" style="color: black;"></i>
						</span>
					</em>
				</td>

					<td width="32%" align="left" valign="middle" class="boder0-left boder0-right">
						<input type="text" name="fname" id="fname" placeholder="Please enter First Name" 
							class="input-style1 CustomInput" onblur='User.callchange()' value='<?=$fname?>'>
					</td>
					<td align="left" valign="middle" width="17%" class="boder0-right">
						Last&nbsp; Name <em>* 
							<span style="cursor: pointer; color: blue;" title="(Last name of the user)">
							<i class="fas fa-info-circle" style="color: black;"></i></span>
						</em>
					</td>
					<td width="33%" align="left" valign="middle" class="boder0-left">
						<input type="text" name="lastname" id="lastname" placeholder="Please enter Last Name" 
							class="input-style1 CustomInput" onblur='User.callchangelast()' 
							value='<?=$sn?>' onChange="this.value=this.value.trim();">
					</td>
				</tr>

				<tr>
					<td align="left" valign="middle" class="boder0-left boder0-right">
						Gender<em> * 
							
						</em>
					</td> 
					<td align="left" valign="middle" class="boder0-left boder0-right">
						<span class="radio-box-area"> <input type="radio" name="gender" id="gender" value="1" checked="checked" <? if($gender=='1' ){echo checked;}?>/>Male </span>
						<span class="radio-box-area"> <input type="radio" name="gender" id="gender" value="0" <? if($gender=='0' ){echo checked;}?>/>Female </span>
					</td>
					<td align="left" valign="middle" class="boder0-right">
						<!--Full&nbsp; Name-->User&nbsp; Name<em>*
						<span style="cursor: pointer; color: blue;" title="(Display name during user login)"><i class="fas fa-info-circle" style="color: black;"></i></span>
						</em>
					</td>
					<td align="left" valign="middle" class="boder0-left">
						<?php if(!empty($user)){?>
							<input type="text" name="username_full" id="username_full" readonly class="input-style1 CustomInput" value='<?=$atxusername?>'>
						<?php }else{ ?>
							<input
							type="text"
							name="username_full"
							id="username_full"
							class="input-style1"
							value="<?php echo $displayname;?>"
							onBlur="User.OnCheckAvailabilityUser(this.value);"
							onKeyPress="return User.ValidateOnKeyPress(this,event,'Please Fill your valid User Name!');"
							 <?php echo $readonly;?> />
							<input type="hidden" name="username" id="username" class="input-style1 CustomInput" value="<?php echo $displayname;?>" readonly />
						<div id="divAvailableUser"></div>
						<?php }?>
					</td>
				</tr>
				<tr>
					<?php if(!empty($user)){ ?>
						<td align="left" valign="middle" class="boder0-left boder0-right">Date of Leave</td>
					<?php }else{ ?>
						<td align="left" valign="middle" class="boder0-left boder0-right">Date of Joining</td>
					<?php } ?>
					<td align="left" valign="middle" class="boder0-left">
						<input type="text" name="dateof_join" class="dob CustomInput" id="startdate1" readonly="readonly" value="<?=$date_leave1?>" /> &nbsp;
					</td>
					<?php if(!empty($user)){?>
					<!-- <td class="boder0-right">Assign To <em> *</em> </td>
					<td class="boder0-left">
						<select name="assign[]" id="assign" class="select-styl1 CustomInput"
							multiple="multiple">
							<?
						$sql="select vProjectName,pId from $db.web_projects WHERE i_Status='1'";
						$res=mysqli_query($link,$sql);
						while($row=mysqli_fetch_array($res))
						{
						$assign=$row['vProjectName'];
						$assignid=$row['pId'];
						?>
						<option value="<?=$assignid?>" <? if(in_array($assignid, $assignto))
						echo "selected" ; ?>><?=$assign?></option>
						<? }?>
					</select>
					</td> -->
				<?php }else{?>
					<td align="left" valign="middle" class="boder0-right">
						Password<em> 
							<span style="cursor: pointer; color: blue;" title="(Password for login)"><i class="fas fa-info-circle" style="color: black;"></i></span>
						</em>
					</td>

					<td align="left" valign="middle" class="boder0-left boder0-right">
						<input type="password" name="password" id="password" class="input-style1 CustomInput" 
							value='<?=($_POST["password"] ?? ""); ?>' readonly>
					</td>

				<?php }?>
				</tr>
				<tr>
					<td align="left" valign="middle" class="boder0-left boder0-right">Company</td>
					<td align="left" valign="middle" class="boder0-left">
						<? if($flag_company!=1){ //echo $company_name; ?>
						<input type="text" name="AtxCompany" id="AtxCompany" class="input-style1 CustomInput" value="<?=$_SESSION['companyName']?>" readonly />
						<? }?>
						<? if($flag_company==1){ ?>
						<input type="text" name="companynm" id="companynm" class="input-style1 CustomInput" value='<?=$company?>' readonly>
						<a href="#" onClick="javascript: window.open('getcompanybranch.php','view','height=400, width=400,scrollbars=1,status=1')"> <img src="images\popup.jpg" title="select company" border="0" /></a>
						<input type="text" name="branch" readonly class="input-style1 CustomInput" value='<?=($_POST[' branch ']);?>'>
						<? }?>
					</td>
					<td align="left" valign="middle" class="boder0-left boder0-right">Job Title<em> 
							<span style="cursor: pointer; color: blue;" title="(Select the type of the user from the drop down)"><i class="fas fa-info-circle" style="color: black;"></i></span>
						</em>
					<td align="left" valign="middle" class="boder0-left">
						<select name="jobtitle" id="jobtitle" class="select-styl1 CustomInput">
						<option value="" disabled selected>Select Job Title</option>
						<option value="0">None</option>
						<?php
							$sql_jobtitle = "SELECT * FROM $db.uniuserprofile WHERE AtxUserID='$user'";
                            $query_jobtitle=mysqli_query($link,$sql_jobtitle);
                            $row_jobtitle=mysqli_fetch_array($query_jobtitle);
                            $job_title=$row_jobtitle['AtxDesignation'];

							$res=department_list();
							while($row=mysqli_fetch_array($res)){							
								$atxGid=$row['atxGid'];
								$gps=$row['DisplayName'];
							?>
							<option value="<?=$atxGid?>" <?php if($job_title == $gps){ echo "selected";}?>><?=$gps?></option>
						<? }?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="32" align="left" valign="middle" c class="boder0-right">Email<em> *
					<span style="cursor: pointer; color: blue;" title="(Email ID of the user. This is required for login in CRM and forget password)"><i class="fas fa-info-circle" style="color: black;"></i></span>
					</em></td>
					<td align="left" valign="middle" class="boder0-left">
						<input type="email" name="email" id="email" placeholder="Please enter Email" class="input-style1 CustomInput"
						onBlur="User.OnCheckAvailabilityEmail(this.value);"
						 onKeyPress="return User.ValidateOnKeyPress(this,event,'Please Fill your valid Email Id!');" value="<?=$email?>" />
						<font face="Tahoma" color="#FF3333" size="1">
						<!-- Author :: Farhan Akhtar 26-09-2024 -->
						<div id="divAvailableEmail"></div>
						</font>
					</td>
					<td class="boder0-left boder0-right">Status<em> 
						<span class="tooltip-icon" data-title="Active: User can login&#10;Inactive: User can't login"><i class="fas fa-info-circle" style="color: black;" ></i></span>
					</em></td>
					<td class="boder0-left boder0-right">
						<?php if (empty($user)) { ?>
							Active <input type="radio" name="status" value="1" checked>
						<?php } else { ?>
							Active <input type="radio" name="status" value="1" <?= $status == '1' || empty($status) ? 'checked' : '' ?>>
							Inactive <input type="radio" name="status" value="0" <?= $status == '0' ? 'checked' : '' ?>>
						<?php } ?>
					</td>
					<!-- one col is not showen because of rowspan -->
				</tr>
				<tr>
					<td align="left" valign="middle" class="boder0-left boder0-right">Contact No<em> *
					<span style="cursor: pointer; color: blue;" title="Mobile Number of the user. This is required for OTP"><i class="fas fa-info-circle" style="color: black;"></i></span>
					
					</em></td>
					<td align="left" valign="middle" class="boder0-left">
						<input type="text" name="phone" id="phone" placeholder="Please enter Contact No." class="input-style1 CustomInput" value="<?=$phone?>" maxlength="12"/>
					</td>
					<td align="left" valign="middle" class="boder0-right">Upload Image<em> 
					<span style="cursor: pointer; color: blue;" title="Passport size photo of user"><i class="fas fa-info-circle" style="color: black;"></i></span>
					
					</em></td>
						<td align="left" valign="middle" class="boder0-left boder0-right">
						    <input type="file" name="fileup" id="fileup" class="textbox" />
						    <span style="color: gray; margin-left: 75px;">(JPEG, PNG, or JPG format only)</span>
						</td>

				</tr>
				<tr>
				   
				</tr>
				<tr>
					<td align="left" valign="middle" class="boder0-left boder0-right">Description<em> 
					<span style="cursor: pointer; color: blue;" title="A small description of the user up to 255 characters"><i class="fas fa-info-circle" style="color: black;"></i></span>
					
					</em></td>
					<td align="left" valign="middle" class="boder0-left">
						<textarea name="desc" id="desc" style="height:100px;width:250px;border-radius: 4px;"placeholder = "Enter Description" class="text-area1 CustomInput" value='<?=($_POST[' desc ']);?>' onKeyDown="User.textCounter(document.adduserfrm.desc,document.adduserfrm.remLen1,255)" onKeyUp="User.textCounter(document.adduserfrm.desc,document.adduserfrm.remLen1,255)"><?=$desc?></textarea>
						<br />
						<div style="float: left; width: 100%; padding: 5px 0 0 0px;">
						<input readonly type="text" name="remLen1" size="3" maxlength="3" value="255" style="width: 30px; text-align: center; margin-right: 5px;" class="input-style1" />
						<span style="padding-top: 10px; display: inline-block;">characters left</span>
						</div>
					</td>
					<td align="left" valign="middle" class="boder0-right">User Desktop IP<em> 
					<span style="cursor: pointer; color: blue;" title="If fixed user, enter login desktop IP address"><i class="fas fa-info-circle" style="color: black;"></i></span>
					
					</em></td>
					<!-- FREE SITTING OR FIX SITTING [Farhan Akhtar][04-03-2025] -->
					<?php
						$localIP = $_SERVER['REMOTE_ADDR'];
						$ip_address = $ip_address ?? ''; // Ensure variable exists
					?>

					<td align="left" valign="middle" class="boder0-left boder0-right">
						<!-- <select name="ip_address" id="ip_address" class="input-style1 CustomInput">
							<option value="0.0.0.0" <?php if($ip_address == '0.0.0.0') echo 'selected="selected"'; ?>>0.0.0.0</option>
							<option value="<?php echo $localIP; ?>" <?php if($ip_address == $localIP) echo 'selected="selected"'; ?>><?php echo $localIP; ?></option>
						</select> -->
						<input type="text" name="ip_address" id="ip_address" class="input-style1" pattern="^(\d{1,3}\.){3}\d{1,3}$" value="<?=$ip_address?>" placeholder="e.g. 192.168.0.1" required>
					</td>

				</tr>
				<tr>
					<!-- Active Inactive Status on the basis of the WFM [vastvikta][06-01-2025] -->
					<?php $module_flag = module_license('WFM');
                	if($module_flag == '1'){?>
					<td align="left" valign="middle"   class=" boder0-left boder0-right">Shift<em>
					<span style="cursor: pointer; color: blue;" title="(Admin can select the shift of the user Required for Work Force Management)"><i class="fas fa-info-circle" style="color: black;"></i></span>
					</em></td>
				    <td align="left" valign="middle" valign="middle" class="boder0-left">
				      <select name="ddl_shift" id="ddl_shift" class="select-styl1 CustomInput">
				        <option value="">Select Shifts</option>
				        <?php
				        $resshift = shift_list();
						while($rowshift=mysqli_fetch_array($resshift)){
							$shiftid=$rowshift['i_shiftID'];
							$shiftname=$rowshift['v_shiftName'];
						?>
				        <option value="<?=$shiftid?>" <? if($shift_id==$shiftid)  echo "selected"; ?> ><?=$shiftname?></option><? }?>
				        </select>
					</td>
					<?php }?>
					<td align="left" valign="middle" class="boder0-left  boder0-right">Department<em>*
					<span style="cursor: pointer; color: blue;" title="(Select the department of the user from the drop down)"><i class="fas fa-info-circle" style="color: black;"></i></span>
					</em></td>
					<td align="left" valign="middle" class="boder0-left">
						<select name="department" id="department" class="select-styl1 CustomInput">
						<option value="" disabled selected>Select Department</option>
						<?php 
							$department=department_list();
							while($row=mysqli_fetch_array($department)){							
								$atxGid=$row['atxGid'];
								$gps=$row['DisplayName'];
								 
							?>
						<option value="<?=$atxGid?>" <?php if($dept == $atxGid){ echo "selected";}?>><?=$gps?></option>
						<? }?>
						</select>
					</td>	
					</td>
						</tr>
				<tr class="classify" style="display:none">
					<td align="left" valign="middle"  class="boder0-right">Classify Agent</td>
					<td align="left" valign="middle"   class="boder0-right">
					<span class="radio-box-area"> <input type="radio" name="classify" value="0" >Telephony </span>
						<span class="radio-box-area"> <input type="radio" name="classify" value="1" >Ominchannel </span>
						<span class="radio-box-area"> <input type="radio" name="classify" value="2" >Both </span>
					</td>
					<td align="left" valign="middle" class="boder0-left boder0-right"></td>
					<td align="left" valign="middle" class="boder0-left">
						
					</td>
				</tr>
				<!-- This flow only for admin access -->
			<?php if($user_group == $Admin_groupId){?> 
				<?php if($job_title == 'Agent'){?>
                <tr>
                    <td align="left" valign="middle" class="boder0-right">Classify Agent<em>
					<span style="cursor: pointer; color: blue;" title="(Admin can assign channels to the agent from the dropdown)"><i class="fa-sharp fa-regular fa-circle-info"></i></span>
					</em></td>
					<td align="left" valign="middle"   class="boder0-left">
                        <span class="radio-box-area"> <input type="radio" name="classify" value="0" <? if($i_classify==0){ echo "checked"; }?> >Telephony </span>
                        <span class="radio-box-area"> <input type="radio" name="classify" value="1" <? if($i_classify==1){ echo "checked"; }?>>Ominchannel </span>
                        <span class="radio-box-area"> <input type="radio" name="classify" value="2" <? if($i_classify==2){ echo "checked"; }?>>Both </span>
                    </td>
					<td align="left" valign="middle" class="boder0-left boder0-right"></td>
					<td align="left" valign="middle" class="boder0-left">
						
					</td>
                </tr>
            	<?php } ?>
            	<tr id="channel_div">
					<td align="left" valign="middle" class="boder0-right">Channel Assign<em>
					<span style="cursor: pointer; color: blue;" title="(Admin can assign channels to the agent from the dropdown)"><i class="fas fa-info-circle" style="color: black;"></i></span>
					</em></td>
					<td align="left" valign="middle" class="boder0-left">
						
						<select name="channel_license[]" id="channel_license" class="select-styl1 CustomInput" multiple>
						<option value=""  disabled>Select Channels</option>
						<!-- <option value="">Select channel license</option> -->
						<?php
						$count_list = "SELECT channel_type, COUNT(*) AS entry_count FROM $db.user_channel_assignment GROUP BY channel_type";
						$respose=mysqli_query($link,$count_list);
						$data_channelrow = array();
						while($rows=mysqli_fetch_array($respose)){
							$data_channelrow[$rows['channel_type']] = $rows['entry_count'];
						}
						$sql="select * from $db.channel_license";
						$res=mysqli_query($link,$sql);
						while($row=mysqli_fetch_array($res)){
							$name=$row['name'];
							$count=$row['count'];
							$idchannel=$row['id'];
						?>
						<option value="<?=$name?>" <?php if($data_channelrow[$name] == $count){?><?php echo 'disabled'; }?> <?php if($data_channel[$name] == $name){ echo 'selected'; }?> ><?=$name ?></option>
						<?php }
						?>
						</select>
					</td>
				</tr>
			<?php }?>
			<!-- Close -->
			</table>
			<div class="button-all2">
				<?php  $web_userhome = base64_encode('web_userhome'); ?>
				<input type="button" onClick="User.validateform(this,1);" class="button-orange1" value="Submit" style="float: none;" />
				<a href="javascript:history.go(-1);" class="button-orange1" style="float:inherit;padding: 6px;color: black;">Back</a>
			</div>
			<input type="hidden" name="company" value="<?=$flag_company?>" />
		</form>				
		<!-- table right panel End -->
	</div>
	<!-- Close Html Code -->
	<script src="../public/js/select2.min.js"></script>
	<script type="text/javascript">
		$('#channel_license').select2({});
	</script>
	<script>
    document.getElementById('fileup').addEventListener('change', function(event) {
        var fileInput = event.target;
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        // Check the file extension
        if (!allowedExtensions.exec(filePath)) {
            alert('You can only upload files in JPEG, PNG, or JPG format.');
            fileInput.value = ''; // Clear the file input field
            return false;
        }
    });
	document.getElementById("ip_address").addEventListener("keyup", function (e) {
		const value = this.value;
		const validChars = value.replace(/[^0-9.]/g, ''); // Remove invalid chars
		if (value !== validChars) {
			this.value = validChars; // Update value without invalid chars
		}
	});
</script>