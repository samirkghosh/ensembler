<?php
/***
 * CREATE TICKET
 * Author: Aarti Ojha
 * Date: 13-03-2024
 * This file is handling create User/Delete/Update user details
 */
/** This function for Fetch user listing display in web_userhome.php page
**/
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// fetch user details
include("../web_function.php"); //Handling common function

if($_POST['action'] == 'user_delete'){
	ajax_user_delete(); // delete user function
}
/*** For Checking user name already exits or not ***/ 
if($_POST['action'] == 'ajax_check_nameexits'){
	ajax_check_nameexits();
}
/***** Fetching user listing  *******/ 
/*** For Checking Email already exits or not :: Author :: Farhan Akhtar 26-09-2024 ***/ 
if($_POST['action'] == 'ajax_check_email_exits'){
	ajax_check_email_exits();
}
/***** Fetching user listing  *******/ 
function user_home_list(){
	global $link,$db;
	/*** This code for Filter go button click ****/ 
	if(!empty($_REQUEST['go'])){
			$go	=	$_REQUEST['go'];
			$search	=	trim($_REQUEST['search']);
	}
	$addq="";
	$department = $_POST['department'];
	$status_delete = '';
	if($go==1){
	   $addq=" And AtxDisplayName LIKE '$search%'";
	}else{
		$status_delete = "AtxUserStatus=1 AND ";
	}
	if(!empty($department)) {
	   $condd = "AND AtxDesignation = '$department' ";
	}
	$timeview		=	$_POST['timeview'];
	if($timeview==''){
		$sql5="select AtxUserID,AtxDisplayName,AtxUserName,AtxContactPhone,AtxDesignation,AtxMobile,AtxUserStatus,AtxEmail,AtxUserCat from $db.uniuserprofile where  AtxUserID != '1' $condd $addq";
	 }else{
		 $sql5="select AtxUserID,AtxDisplayName,AtxUserName,AtxContactPhone,AtxDesignation,AtxMobile,AtxUserStatus,AtxEmail,AtxUserCat from $db.uniuserprofile where AtxUserStatus='$timeview' AND AtxUserID != '1' $condd $addq";
	 }
	 $res5=mysqli_query($link,$sql5) or die(mysqli_error($link));
	 return $res5;
}
/***Close*/ 
/*** Fetch Department Name ***/ 
function department_list(){
	global $link,$db;
	$groupid = $_SESSION['user_group'];
	$companyid = $_SESSION['companyid'];
	$comp_no_userlicense=F_CompanyUserLicense($companyid,$db); // Fetch company user license details
	$comp_no_users=F_Count_UserID($companyid,$db); // Fetch Count User
	if($groupid=='0000'){ 
		$rrights = "WHERE DisplayName NOT IN ('Engineer','Web Users','Jr Engineer') AND status ='1' ORDER BY DisplayName ASC"; 
	}else{ 
		$rrights = "WHERE (DisplayName != 'Master Admin') && (DisplayName != 'Admin') && (DisplayName != 'Web Users') && (DisplayName != 'Jr Engineer') and status='1'"; 
	}
	if(($comp_no_users) >= ($comp_no_userlicense)){ 
		$rrights = "WHERE ((DisplayName != 'Ewsa Admin') && (DisplayName != 'Agent') && (DisplayName != 'Team Lead'))"; 
	} 				
	$sql="select atxGid,DisplayName from $db.unigroupid $rrights ";
	$department=mysqli_query($link,$sql);
	return $department;
}
/*** Close ***/ 
/** fecth uniuserprofile agent name ***/
function uniuserprofile(){
	global $link,$db;
	$sql="select AtxUserName,AtxUserID, AtxDesignation from $db.uniuserprofile WHERE AtxUserStatus='1' and AtxDesignation NOT IN ('Agent')";
	$res=mysqli_query($link,$sql);
	return $res;
} 
/** Fetch skill list **/
function skill_list(){
	global $link,$db;
	$sqlskill="select i_skillID,v_SkillName from $db.tbl_wfm_mst_skill";
	$resskill=mysqli_query($link,$sqlskill);
	return $resskill;
} 
/*** Fetch Shift listing **/
function shift_list(){
	global $link,$db;
	$sqlshift="select i_shiftID,v_shiftName from $db.tbl_wfm_mst_shift WHERE i_shiftID IN (4)";
	$resshift=mysqli_query($link,$sqlshift);
	return $resshift;
} 
/*** For Delete user in multiple tables ***/ 
function ajax_user_delete(){
	global $link,$db,$db_asterisk;
	$vuserid=$_SESSION['userid'];
	$checked_user=$_POST['checked_user'];
	foreach ($checked_user as $check) {
		$username = assignto($check);
		// Delete-[status] change for user detail in multiple tables 
		$sql1="UPDATE $db.`tbl_mst_user_company` SET `I_UserStatus`=0 where `I_UserID`='$check'";
		mysqli_query($link,$sql1) or die ("invalid query tbl_mst_user_company:" . mysqli_error($link));

		$sql2="UPDATE $db.`uniuserprofile` SET `AtxUserStatus` = 0 WHERE `AtxUserID`='$check'";
		mysqli_query($link,$sql2) or die ("invalid query uniuserprofile:" . mysqli_error($link));

		$sql3="UPDATE  $db.`userhead` SET `status` = 0 WHERE `UserID`='$check'";
		mysqli_query($link,$sql3) or die ("invalid query userhead:" . mysqli_error($link));

		$sql4="UPDATE  $db.`uniautoatt` SET `uaaStatus` = 0 WHERE `uaaUserID`='$check'";
		mysqli_query($link,$sql4) or die ("invalid query uniautoatt:" . mysqli_error($link));

		$sql6="UPDATE  $db.`encryp1` SET `Status` = 0 WHERE `UserName`='$username'";
		mysqli_query($link,$sql6) or die ("invalid query encryp1:" . mysqli_error($link));

		$sql7="UPDATE  $db.`unigroupdetails` SET `status` = 0 WHERE `ugdContactID`='$check'";
		mysqli_query($link,$sql7) or die ("invalid query unigroupdetails:" . mysqli_error($link));

		$sql15="UPDATE  $db_asterisk.`autodial_users` SET `i_status` = 0 AND `active_status` = 0 WHERE `user`='$username'";
		mysqli_query($link,$sql15) or die ("invalid query autodial_users:" . mysqli_error($link));
		add_audit_log($vuserid, 'user_remove', 'null', "User deleted ".$username, $db, $username);

		// for user delete free channel 
		$update_sql = "DELETE FROM $db.user_channel_assignment where userid='$check'";
		mysqli_query($link,$update_sql);
	} 
}
// END
/*** For Checking user name already exits or not ***/ 
function ajax_check_nameexits(){
	global $link,$db;
	if(!empty($_REQUEST["string"])){
		$username = $_REQUEST["string"];
		$SQL="SELECT count(*) as cou FROM $db.uniuserprofile WHERE AtxUserName = '$username'";
		$rs=mysqli_query($link,$SQL);
		$row=mysqli_fetch_array($rs);
		$cou=$row['cou'];
		if($cou>=1){ $response='<font face="Tahoma" color="#FF3333" size="1">This User Name is already in use.</font>'; }else{ $response='<font face="Tahoma" color="#00FF00" size="1">User Name Available</font>';}
		echo $response;
		
	}
}
/*** For Checking user email already exits or not :: Author :: Farhan Akhtar 26-09-2024***/ 
function ajax_check_email_exits(){
	global $link,$db;
	if(!empty($_REQUEST["string"])){
		$email = $_REQUEST["string"];
		$SQL="SELECT count(*) as count FROM $db.uniuserprofile WHERE AtxEmail = '$email'";
		$rs=mysqli_query($link,$SQL);
		$row=mysqli_fetch_array($rs);
		$cou=$row['count'];
		$response = ($cou >= 1) ? '<font face="Tahoma" color="#FF3333" size="1">This Email is already in use.</font>' : '<font face="Tahoma" color="#00FF00" size="1">Email is Available</font>';
		echo $response;
	}
}
?>