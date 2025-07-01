<?php 
   /***
    * USER DETAIL PAGE
    * Author: Aarti Ojha
    * Date: 27-03-2024
    * This file is handling fetch user record in database and display user record
   */
   /**Fetching necessary data ***/
   $username	=	$_SESSION['logged'];
   $usergroup = $_SESSION['user_group'];
   $user=$_GET['id'];
   /*** Fetching user detail in uniuserprofile table**/ 
   $sql="select * from $db.uniuserprofile where AtxUserID='$user'";
   $res=mysqli_query($link,$sql);
   while($row=mysqli_fetch_array($res)){	

	$user_name=$row['AtxUserName'];
	$sql_ip="select IP_Address from $db.encryp1 where UserName='$user_name' AND Status = 1";
	$res_ip=mysqli_query($link,$sql_ip);
	$row_ip=mysqli_fetch_array($res_ip);
	$ip_address=$row_ip['IP_Address'];

   	$fname=$row['GivenName'];
   	$sn=$row['sn'];
   	$atxusername=$row['AtxUserName'];
   	$displayname=$row['AtxDisplayName'];
	$Atxusername = $row['AtxUserName']; //Username display code [vastvikta][06-01-2025]
   	$company=$row['AtxCompany'];
   	$jobtitle=$row['AtxDesignation'];
   	$mail=$row['AtxEmail'];
   	$password=$row['AtxPassword'];
   	$mailpassword=$row['AtxEmailPwd'];
   	$category=$row['AtxUserCat'];
   	$bday=$row['AtxBirthDate'];
   	$gender=$row['AtxGender'];
   	$street=$row['AtxStreet'];
   	$city=$row['AtxCity'];
   	$state=$row['AtxState'];
   	$country=$row['AtxCountry'];
   	$atxpincode=$row['AtxPincode'];
   	$phone=$row['AtxHomePhone'];
   	$contactphone=$row['AtxContactPhone'];
   	$fax=$row['AtxContactFax'];
   	$pager=$row['AtxPager'];
   	$mobile=$row['AtxMobile'];
   	$desc=$row['AtxDescription'];
   	$status1=$row['AtxUserStatus'];
   	$status=user_status($status1);
   	$File_up = $row['File_up'];
   	$level = $row['AtxUserCat'];
   	$emailpassword=$row['AtxEmailPwd'];
      $i_Skillset = $row['i_Skillset'];
      $i_shiftID = $row['i_shiftpref'];
      // 
      $date_leave=$row['LeavingDate'];
      $date_leave1=view_dateformat1($date_leave);
      $date_join=$row['JoiningDate'];
      $date_join1=view_dateformat1($date_join);
    //   print_r($date_leave1);
   }
   /*** Close ***/ 
   /*** Fetching Skill and Shift details tbl_wfm_mst_skill/tbl_wfm_mst_shift table**/ 
   $sqlskill="select i_skillID,v_SkillName from $db.tbl_wfm_mst_skill where i_skillID='$i_Skillset'";
   $resskill=mysqli_query($link,$sqlskill);
   $rowskill=mysqli_fetch_array($resskill);
   $skillname = $rowskill['v_SkillName'];
   $sqlshift="select i_shiftID,v_shiftName from $db.tbl_wfm_mst_shift where i_shiftID = '$i_shiftID'";
   $resshift=mysqli_query($link,$sqlshift);
   $rowshift=mysqli_fetch_array($resshift);
   $shiftname = $rowshift['v_shiftName'];
   /*** Close ***/ 
   /*** Fetching vProjectAssign details userhead table**/ 
   $sql2="select HeadID,vProjectAssign from $db.userhead where UserID='$user'";
   $res=mysqli_query($link,$sql2) or die(mysqli_error());
   $row=mysqli_fetch_array($res);
   $reports=$row['HeadID'];
   $reportto=explode(',',$reports);
   /*** Close ***/
   /*** Fetching user asign group details **/  
   $sql3="select a.DisplayName,a.atxGid from $db.unigroupid a, $db.unigroupdetails b where a.atxGid=b.atxGid and b.ugdContactID='$user'";
   $res3=mysqli_query($link,$sql3);
   $row=mysqli_fetch_array($res3);
   $department=$row['DisplayName'];
   $dept=$row['atxGid'];
   /*** Close ***/
   // Feching channel assign list
	$count_list = "SELECT channel_type, COUNT(*) AS entry_count FROM $db.user_channel_assignment where userid = $user GROUP BY channel_type";
	  $respose=mysqli_query($link,$count_list);
	  $data_channelrow = array();
	  while($rows=mysqli_fetch_array($respose)){
	       $data_channelrow[$rows['channel_type']] = $rows['channel_type'];
	  }
	  if(!empty($data_channelrow)){
	       $data_channel = implode(',',$data_channelrow);
	  }
	  // close
?>
<!-- Start Html code for display user details  -->
	<form method="post" name="frmname" id="frmname">
      <span class="breadcrumb_head" style="height:37px;padding:9px 16px">User Detail</span>
		<div class="style2-table">
			<div class="table">
				<table cellspacing="0" cellpadding="1" width="100%" align="center" border="0"
					class="tableview tableview-2">
					<tr>
						<td width="21%" align="left" valign="top" class="normaltextabhi">
							<?php if($File_up!=''){ ?>
							     <img src="User/<?=$File_up?>" width="120" border="0" alt="photo" />
							<?php }else{ ?>
							  <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAgZVf5PkZRMT8jCdxiiewmLiQ4IDHNZClgw&usqp=CAU" width="120" border="0" alt="Photo Not Available"
								 />
							<?php } ?>
						</td>
						<td width="62%">
							<center><span class="Comp"><?=$company_name?>  User Profile </center>
							</span>
						</td>
						<td align="center" valign="middle" width="17%">
							<a href="#"><img alt="Home" border="0" src="<?=$SiteURL?>public/images//<?=$dbheadlogo?>"
									 style="width: 158px;height: 67px;"/></a>
						</td>
					</tr>
					<tr>
						<td colspan="3" class="background">
							<b>User Name: </b><?=$displayname?>
							( DOJ:
							<? if(empty($date_join1)){
						         echo'--------';
                        }else{ echo $date_join1; }?>
							     -DOL:
							<? if($date_leave1=='--'){
					          echo'--------';}
					       else{  
                        echo $date_leave1; 
                     }?> )
						</td>
					</tr>
				</table>
				<div id="frm1" style="position:inherit;" class="add-tasks-div user-register-page">
					<table cellspacing="0" cellpadding="1" width="100%" align="center" border="0"
						class="tableview tableview-2">
						<tr>
							<td width="16%" align="left" valign="top" class="normaltextabhi">
								<b>User ID</b></td>
							<td width="43%" align="left" valign="top"><?=$user?></td>
							<td width="19%" align="left" class="normaltextabhi"><b>Gender</b></td>
							<td width="22%" align="left" class="select">
								<? if($gender=='1'){ ?>
								Male
								<? }
						      if($gender=='0'){ ?>
								Female
								<? }?>
							</td>
						</tr>
						<tr>
							<td align="left" class="normaltextabhi"><b>First Name</b></td>
							<td align="left"><?=$fname?></td>
							<td align="left" class="normaltextabhi"><b>Last Name</b></td>
							<td align="left"><?=$sn?></td>
						</tr>
						<tr>
							<td align="left" class="normaltextabhi"><b>Full Name</b></td>
							<td align="left"><?=$displayname?></td>
							<td align="left" class="normaltextabhi"><b>Username</b> </td>
							<td align="left"><?=$Atxusername?>
							</td>
						</tr>
						<tr>
							<!-- comment on reports to  department and assign to [vastvikta][31-01-2025] -->
						<!-- <td align="left" class="normaltextabhi"><b>Assign to</b> </td>
							<td align="left">   
                       <?php 
                            // This table use for display assign to details
							$assign = mysqli_query($link,"Select vProjectAssign from $db.userhead where UserID=".$user."");
							$fetch_assign=mysqli_fetch_array($assign);
							 $assign_id=$fetch_assign['vProjectAssign'];
							$assign_to=explode(',',$assign_id);
							foreach($assign_to as $a){
								$s="select vProjectName from $db.web_projects where pId ='$a'";
								$re=mysqli_query($link,$s);
								$ro=mysqli_fetch_array($re);
								$assign_.=$ro['vProjectName'].', ';
							}
							echo $assignto_boss = substr($assign_,0,-2);
						 ?>
							</td> -->
							<td align="left" class="normaltextabhi"><b>Company</b></td>
							<td align="left"><?=$company?></td>
							<td align="left" class="normaltextabhi"><b>Email</b></td>
							<td align="left"><?=$mail?></td>
						</tr>
						<tr>
						<!-- <td align="left" class="normaltextabhi"><b>User Role</b></td>
							<td align="left"><?=$department?>
								<?php
      						$sql="select atxGid,DisplayName from $db.unigroupid";
      						$res=mysqli_query($link,$sql);
      						while($row=mysqli_fetch_array($res)){
         						$atxGid=$row['atxGid'];
         						$gps=$row['DisplayName'];
      						}
						      $gps
						      ?>-->
							</td>
							<td align="left" class="normaltextabhi"><b>Job Title</b></td>
							<td align="left">
								<? echo $jobtitle; ?>
							</td> 
							<td align="left" class="normaltextabhi"><b>Status</b></td>
							<td align="left"><?=$status?></td>
						</tr>
						<tr>
						<!-- <td align="left" class="normaltextabhi"><b>Reports To</b></td>
							<td align="left" class="select">
								<?php	
         						$report=explode(',',$reports); 
         						foreach($report as $r)
         						{
         						$sql6="select AtxUserName from $db.uniuserprofile where AtxUserID='$r'";
         						$res6=mysqli_query($link,$sql6);
         						$num6=mysqli_num_rows($res6);
         						$row6=mysqli_fetch_array($res6);
         						$reportsto.=$row6['AtxUserName'].', ';
         						}
         						echo $reportstoboss = substr($reportsto,0,-2);
         						?>
							</td> -->
							
						</tr>
						<tr>
						<td align="left" class="normaltextabhi"><b>Description</b></td>
							<td align="left"><?=$desc?></td>         
                    
							<td align="left" class="normaltextabhi"><b>Contact</b></td>
							<td align="left"><?=$phone?></td>
						            
						</tr>
                  <tr>
				 
					   <?php $module_flag = module_license('WFM');
                	if($module_flag == '1'){?>
					   <td align="left" class="normaltextabhi"><b>Shift</b></td>
                     <td align="left"><?php echo $shiftname;?></td>
					 <?}?>
							<td align="left" class="normaltextabhi"><b>Department</b></td>
                       <td align="left"><?php echo $department;?></td>
                  </tr>
                  <tr>
				  <!-- <td align="left" class="normaltextabhi"><b>Skill</b></td>
                     <td align="left"><?php echo $skillname;?></td>
                     
					  -->
                  </tr>
				  <tr>
				  <td align="left" class="normaltextabhi"><b>Channel Assign</b></td>
                       <td align="left"><?php echo $data_channel;?></td>
				
				  <td align="left" class="normaltextabhi"><b>Login System IP</b></td>
					   <?
					if(empty($ip_address)) {
						$localIP = getenv("REMOTE_ADDR");
					}else {
						$localIP = $ip_address;
					}
					?>
                       <td align="left"><?php echo $localIP?></td>
					   
							</tr>
					</table>
				</div>
				<!-- ritu ::11/15/2024 -->
				<?php 
				    $web_userhome = base64_encode('web_userhome');
				    $web_usercreate = base64_encode('web_usercreate');

				    // Check if $user is '060000' or '070000'
				    if ($user == '060000' || $user == '070000') {
				        // Do nothing (nothing will be displayed)
				    } else if ($usergroup == '0000' || $usergroup == '080000' || $usergroup == '050000') {
				        // Else block to display the Edit button for specific user groups
				?>
				        <a href="user_index.php?token=<?php echo $web_usercreate; ?>&id=<?php echo $user; ?>" class="button-orange1" style="float:inherit; padding: 6px; color: black;">Edit</a>
				<?php 
				    } 
				?>

            <a href="user_index.php?token=<?php echo $web_userhome;?>" class="button-orange1" style="float:inherit;padding: 6px;color: black;">Back</a>
			</div>
		</div>
	</form>
   <!-- table right panel End -->