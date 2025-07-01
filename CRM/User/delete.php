<?php 
/***
 * USER CREATE PAGE
 * Author: Aarti Ojha
 * Date: 14-03-2024
 * This file is handling user record to insert multiple tables 
 * Handling error logs
 * Edit function for update user details
 * 
 */
include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
// fetch user details
require '../../function/SimpleImage.php'; // Include image upload function
include "../web_function.php"; // include common function code

// Add code for logging code
include_once "../../logs/config.php";
include_once "../../logs/logs.php";

// Fetching necessary data
$vuserid	   	= 	$_SESSION['userid'];
## function to get the email id of a particlar username ###
function get_emailid($logged_name){
	global $db,$link;
	$sql_email = "select AtxEmail from $db.uniuserprofile where AtxUserName='$logged_name' and AtxUserStatus='1'";
	$res_email=mysqli_query($link,$sql_email);
	$row_email=mysqli_fetch_array($res_email);
	return $email=$row_email['AtxEmail'];
}	
function get_display_atx($atx){
 global $db,$link;
 $sql_atx = "SELECT DisplayName FROM $db.unigroupid WHERE atxGid =$atx";
 $result_atx = mysqli_query($link,$sql_atx);
 $row_atx = mysqli_fetch_assoc($result_atx);
 return $row_atx['DisplayName'];
}
function chkassignmentsunik($boss1,$uid){
   global $db,$link;  
   //check if they already assigned
   $sqlm="SELECT I_UserId FROM $db.uniuserreportedto WHERE I_ReportedTo = '$boss1' AND I_UserId = '$uid'";
   $resm=mysqli_query($link,$sqlm) or die(mysqli_error($link));
   if(mysqli_num_rows($resm)>0){
   }else{
      $sqlmi="insert into $db.uniuserreportedto(I_ReportedTo,I_UserId)values('$boss1','$uid')";
      $resm=mysqli_query($link,$sqlmi) or die(" Query Error In uniuserreportedto ".mysqli_error($link));
   }
}
function newmaxuserid(){
	global $link,$db;
	$sql_userid = "select max(I_UserID) as maxuserid from $db.tbl_mst_user_company  order by I_UserID asc ";
	$res_userid=mysqli_query($link,$sql_userid);
	$row_user=mysqli_fetch_array($res_userid);
	$v_userid=$row_user['maxuserid']+1;
	return $v_userid;
}
if($_POST['action'] == 'create_user'){
	Create_User();
}else if($_POST['action'] == 'edit_user'){
	Edit_User();
}
// This function for Create user Insert Code
function Create_User(){
	// define global variable 
	global $wpdb, $db,$link, $domain, $base,$Admin_groupId,$NonLogin_groupId,$Backoffice_groupId,$Link_Login,$from_helpdesk_email,$uc_ip,$licence_Named;
	// Fetching necessary data
	$fname   	=$_POST['fname'];
	$email      =$_POST['email'];
	$passwordwp=md5($_POST['password']);
	$data = [];
	$companyid	= $_SESSION['companyid'];
	$authenticated_mobile =($_POST['authenticated_mobile']);
	$tel_password = $_POST['tel_password'];
	######### END #####
	$username   = $_POST['username'];
	$fullname    = $_POST['fname'];
	$lastname     = $_POST['lastname'];
	$uid          = $_POST['uid'];
	$password     = $_POST['password'];
	$company      = $_POST['AtxCompany'];
	$email        = $_POST['email'];
	$emailpassword= $_POST['emailpassword'];
	$ip_address= $_POST['ip_address'];
	$level= $_POST['level'];
	$dateof_join = $_POST['dateof_join'];
	#### Date Join / Leave #########
	$dateof_join1 =view_dateformat($dateof_join);
	$AtxBirthDate = '';
	$atbexp              =view_dateformat($AtxBirthDate);
	$gender              =($_POST['gender']);
	if($gender==''){
		$gender='2';   ########## gender is 2 if not selected any radio box  #################
	}
	$street              = $_POST['hstreet'];
	$city                = $_POST['hcity'];
	$state               = $_POST['hstate'];
	$country             = $_POST['hcountry'];
	$pincode             = $_POST['pincode'];
	$phone               = $_POST['phone'];
	$contactphone        = $_POST['contactphone'];
	$fax                 = $_POST['fax'];
	$pager               = $_POST['pager'];
	$mobile              = $_POST['mobile'];
	$desc                = $_POST['desc'];
	$departmentId          = $_POST['department'];
	$jobtitle            = $_POST['jobtitle'];
	$output              = $_POST['output'];
	$category            = $_POST['category'];
	$boss                =$_POST['boss'];
	$assign              =$_POST['assign'];
	$ext                 = $_POST['extention'];
	$tpin				 = $_POST['tpin'];	
	$branchhead          = $_POST['branchhead'];
	//WFM Changes - aarti ojha 29-03-23
	$ddl_shift           =$_POST['ddl_shift'];
	$ddl_skill           =$_POST['ddl_skill'];

	if($jobtitle=='070000'){
		$classify = $_POST['classify'];
	}else{
		$classify = 1;
	}
	if(count($boss)<=1){
		$boss1=$boss[0];
	}else{
		$boss1=implode(",",$boss);
	}
	if(count($assign)<=1){
		$assign1=$assign[0];
	}else{
		$assign1=implode(",",$assign);
	}
	//OVER WRITE THE USER NAME username_full
	$username_full= $_POST['username_full'];
	$usern= "$fullname $lastname";
	$year              	= $_POST['year'];

	//--------employment history--------------- 
	$creatdon				= date("Y-m-d");
	$JoiningDate  =($_POST['JoiningDate']);
	$JoinDate1    =explode(" ",$JoiningDate);
	$jod          =$JoinDate1[0];
	$jod1         =explode("-",$jod);
	$jod2         =$jod1[0];
	$jod3         =$jod1[1];
	$jod4         =$jod1[2];
	$dateof_join =$jod4."-".$jod3."-".$jod2;
	//$passwd = md5($fullname);
	$passwd1 = $_POST['password'];
	$passwd = md5(sha1(md5($passwd1)));
	// This condition not for groupId 090000
	$SQL="SELECT count(*) as cou FROM $db.uniuserprofile WHERE AtxUserName = '$username_full'";
	$rs=mysqli_query($link,$SQL);
	$rowss=mysqli_fetch_array($rs);
	$cou=$rowss['cou'];
	if($cou>=1){ 
		$data['status'] = 'false';
		$data['message'] = 'This User Name is already in use!!!';
		echo json_encode($data);
		exit;
	}
	$SQLs="SELECT count(*) as cou FROM $db.uniuserprofile WHERE emailid = '$email'";
	$rss=mysqli_query($link,$SQLs);
	$rowss=mysqli_fetch_array($rss);
	$cous=$rowss['cou'];
	if($cous>=1){ 
		$data['status'] = 'false';
		$data['message'] = 'This Email is already in use!!!';
		echo json_encode($data);
		exit;
	}
	// echo"<pre>";print_r($_POST);
	if($jobtitle != $NonLogin_groupId){

		/*******************Licence condition*************************/
		/*** This condition not for groupId 0000 ***/
		if($jobtitle!="" && $jobtitle!=$Admin_groupId){
			
			$sql=mysqli_query($link,"SELECT UserLicence,DisplayName	 FROM $db.unigroupid where atxGid	='$jobtitle'");
			$row=mysqli_fetch_array($sql);
			$user_licence_cnt=$row['UserLicence'];
			$DisplayName=$row['DisplayName'];

			/****Total created user for the department*/
			$sql=" select COUNT(*) as named_licence from $db.unigroupid where atxGid = '$jobtitle' and user_type='$licence_Named' ";
			$query=mysqli_query($link,$sql);
			$fetch1=mysqli_fetch_array($query);
			$named_licenceType=$fetch1['named_licence'];

			// check licence condition
			if($named_licenceType){
				$sql_1="SELECT COUNT( * ) AS usr_cnt_exist FROM $db.tbl_mst_user_company AS u LEFT JOIN $db.unigroupdetails AS ug 
					ON u.I_UserID	 = ug.ugdContactID
					WHERE  ug.atxGid = '$departmentId'  ";
				$query=mysqli_query($link,$sql_1);
				$fetch=mysqli_fetch_row($query);
				$usr_cnt_exist=$fetch[0];
				if($user_licence_cnt <=$usr_cnt_exist){
					$data['status'] = 'false';
					$data['message'] = "\n"."Licence issue!!!. Please Check"." Total licence ".$user_licence_cnt." for the ".$DisplayName."  and total created user cnt is ".$usr_cnt_exist."\n\n";
					echo json_encode($data);
					exit;
				}		
			}//NAMED LICENCE CONDITION
		}//end of department condition
		// die;
		/**************Licence condition*********************/
		$title_arr = [];
		$tele_rights_sql = "SELECT atxGid FROM $db.unigroupid WHERE telephony_rights ='1'" ;
		$tele_rights_res = mysqli_query($link,$tele_rights_sql);
		 
		while ($tele_rows = mysqli_fetch_array($tele_rights_res) ) {
			array_push($title_arr, $tele_rows['atxGid'])  ;
		}	
		/*** This condition not for groupId 060000 ***/
		if($jobtitle != $Backoffice_groupId){ // I have commnet code for give access backoffice also
			if(in_array($jobtitle, $title_arr)){
				$response = get_web_page("http://$uc_ip/agc/executequery.php?mode=insert&user=$username_full&pass=$passwd1&emailid=$email");		
				if($response=='exists'){
					$data['status'] = 'false';
					$data['message'] = 'User Name already Exists in UC. Please Check.';
					echo json_encode($data);
					exit;
				}		
				if($response=='Error'){
					$data['status'] = 'false';
					$data['message'] = 'Something went wrong. Please Check.';
					echo json_encode($data);
					exit;
				}		
				if($response=='noext'){		
					$data['status'] = 'false';
					$data['message'] = 'There is no extension. Please Contact Admin.';
					echo json_encode($data);
					exit;
				}
			}
		}
	}
	############# TO CHCEK THE USERNAME DUPLICACY 
	$sql3="select AtxUserName from $db.uniuserprofile where AtxUserName='$username_full' and AtxUserStatus='1'";
	$res3=mysqli_query($link,$sql3)or die(" Query Error In uniuserprofile ".mysqli_error($link));
	$sql3_num = mysqli_num_rows($res3);

	########### CODE TO CHECK THE EMAIL ID DUPLICACY ####################
	$uid=newmaxuserid();
	$sql_user="select count(*) as countuser from $db.tbl_mst_user_company where V_EmailID='$email' and V_EmailID!='' and I_UserStatus='1' ";
	$res_user=mysqli_query($link,$sql_user);
	$num_rows=mysqli_fetch_array($res_user);
	if(($num_rows["countuser"]>'0')&&($sql3_num>'0')){				
		if($sql3_num > 0){
			$data['status'] = 'false';
			$data['message'] = "Sorry the UserName '$username_full' already exists !";
			echo json_encode($data);
			exit;
		}
		if($num_rows["countuser"]>0){
			$data['status'] = 'false';
			$data['message'] = "Sorry the EmailID '$email' already exists !";
			echo json_encode($data);
			exit;
		}
	}else if(($sql3_num =='0')&&($num_rows["countuser"]=='0')){
		$day						=	($_POST['day']);
		$month						=	($_POST['month']);
		$year						=	($_POST['year']);
		/******  Make format as mentioned in db tables  whenever its null value.*****/
		if((empty($day))||(empty($month))||(empty($year))){
			$atbexp="0000-00-00";
		}else{
			$atbexp					=	$year.'-'.$month.'-'.$day;
		}
		/******  Make format as mentioned in db tables  whenever its null value.*****/
		$userfile_tmp_name = $_FILES['fileup']['name'];
		$archive_dir = "emp_photo";
		$userfile_name = $userid.$userfile_tmp_name;
		$userfile_name_path = $archive_dir."/".$userid.$userfile_tmp_name;	
		if($userfile_tmp_name!=''){	
			$image = new SimpleImage();
			$image->load($_FILES['fileup']['tmp_name']);
			$image->resize(120,120);
			$image->save($userfile_name_path);
		}else{
			$userfile_name_path ='';
		}
		$jobtitle_name = get_display_atx($jobtitle);
		if($jobtitle ==$NonLogin_groupId || $departmentId=='Non Login User'){
			$passwd = "";
		}
		/*** Insert Personal Information In  uniuserprofile table ***/
		$sql_createuser="REPLACE INTO $db.uniuserprofile(AtxUserName,AtxUserID, GivenName, sn, AtxDisplayName, AtxCompany, AtxDesignation, AtxEmail, AtxEmailPwd, AtxBirthDate, AtxGender, AtxStreet, AtxCity, AtxState, AtxCountry, AtxPinCode, AtxHomePhone, AtxContactPhone, AtxContactFax, AtxPager, AtxMobile, AtxDescription, AtxPassword, AtxUserStatus, File_up, AtxUserCat,i_Skillset,i_shiftpref,i_classify,JoiningDate) VALUES ('$username_full','$uid','$fullname','$lastname', '$usern','$company','$jobtitle_name','$email','$emailpassword','$atbexp','$gender','$street','$city','$state','$country', '$pincode','$phone','$contactphone','$fax','$pager','$mobile','$desc','$tpin','1','$userfile_name_path','$level','$ddl_skill','$ddl_shift','$classify','$dateof_join1')"; 
		$result_createuser = mysqli_query($link,$sql_createuser);
		
		if (!$result_createuser){
			if (__DBGLOG__){
		       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error uniuserprofile: $sql_createuser". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "uniuserprofile Database error";
			echo json_encode($response);
			exit();
		} 

		if($result_createuser){
			/*** Insert Personal Information In  tbl_mst_user_company table ***/
			$passchangedate = date('Y-m-d H:i:s');
			$sql_customer_user="REPLACE INTO $db.tbl_mst_user_company (I_UserID,I_CompanyID,V_EmailID,V_Password,Datetime_registration,V_MobileNo,V_Tel_Pwd,password_change_date) VALUES ('$uid','$companyid','$email','$passwd','$creatdon','$phone','$tpin','$passchangedate')";
			$createuser = mysqli_query($link,$sql_customer_user);
			if (!$createuser){
				if (__DBGLOG__){
			       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error tbl_mst_user_company: $createuser". mysqli_error($link));
				}
				$response['error'] = TRUE;
				$response['error_msg'] = "tbl_mst_user_company Database error";
				echo json_encode($response);
				exit();
			} 

		}
		#################################
		// Assignd User to its Bosses
		if(count($boss)<=1){
			$boss1=$boss[0];
			chkassignmentsunik($boss1,$uid);
		}else{
			foreach($boss as $kboss => $vboss){
				chkassignmentsunik($vboss,$uid);
			}
		}
		if(count($assign)<=1){
			$assign1=$assign[0];
			chkassignmentsunik($assign1,$uid);
		}else{
			foreach($assign as $kassign => $vassign){
				chkassignmentsunik($vassign,$uid);
			}
		}
		/*** Insert data in userhead table ***/
		$sql2="REPLACE INTO $db.userhead(UserID,HeadID,status,vProjectAssign) VALUES ('$uid','$boss1','1','$assign1')";
		$res2=mysqli_query($link,$sql2);
		if (!$res2){
			if (__DBGLOG__){
		       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error userhead: $res2". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "userhead Database error";
			echo json_encode($response);
			exit();
		} 
		
		/*** Insert data in uniautoatt table ***/
		$sqlm="REPLACE INTO $db.uniautoatt(uaaUserID,uaaExtnNo) VALUES ('$uid','$ext')";
		$resm=mysqli_query($link,$sqlm);	
		if (!$resm){
			if (__DBGLOG__){
		       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error uniautoatt: $resm". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "uniautoatt Database error";
			echo json_encode($response);
			exit();
		} 

		/*** Insert data in group details table ***/
		$slo="INSERT INTO $db.encryp1(UserName,Pwd,IP_Address) VALUES ('$username_full','$passwd','$ip_address')";
		$reso=mysqli_query($link,$slo);
		if (!$reso){
			if (__DBGLOG__){
		       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error encryp1: $reso". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "encryp1 Database error";
			echo json_encode($response);
			exit();
		} 
		
		$sql5="select atxGid from $db.unigroupid where atxGid='$departmentId'";
		// echo $sql5;
		$res5=mysqli_query($link,$sql5) ;
		$row5=mysqli_fetch_array($res5);
		$gid=$row5['atxGid'];

		/*** Insert data in group details table ***/ 
		$sql4="REPLACE INTO $db.unigroupdetails(atxGid,ugdContactID) values('$gid','$uid')";
		// echo $sql4;
		$res4=mysqli_query($link,$sql4);
		if (!$res4){
			if (__DBGLOG__){
		       		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error unigroupdetails: $res4". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "unigroupdetails Database error";
			echo json_encode($response);
			exit();
		} 

		if($jobtitle ==$NonLogin_groupId){
			$data['status'] = 'true';
			$data['message'] = 'Non Login User is successfully created';
			echo json_encode($data);
			exit;
		}else{	  
			$log= "$username_full($jobtitle Created )";
			$from=$from_helpdesk_email; // global variable define in common-constants.php file
			$case_type1 = 'user_created';
			$LINK = $Link_Login; // global variable define in common-constants.php file
			$data_array = [];
			$data_array['name'] = $usern;
			$data_array['password_user'] = $passwd1;
			$data_array['email'] = $email;
			$data_array['login_link'] = $LINK;
			
			/** fetch mail and sms template data **/ 
			$ress_1 = mail_template($phone, $case_type1, $data_array);	
			$expiry = $ress_1['expiry'];
			$subject = $ress_1['sub'];
			$message_mail = $ress_1['msg'];
			 
			/*** insert notification data in EMAIL outgoin table ****/ 
			$sql_email="insert into $db.web_email_information(v_toemail,v_fromemail,v_subject, v_body,email_type,module, ICASEID,i_expiry) values ('$email', '$from', '$subject', '$message_mail', 'OUT', 'New Case Call','$phone','$expiry')";
			mysqli_query($link,$sql_email) or die(" Query Error In web_email_information Table ".mysqli_error($link));

			/*** insert notification data in SMS outgoin table ****/ 
			$res2 = sms_template($phone, $case_type1, $data_array);
			$message_text = $res2['msg']; 
			$expiry = $res2['expiry'];
			// Common function to insert SMS outgoing data into the database.[Aart][05-12-2024]
              $data_sms=array();
              $data_sms['v_mobileNo'] = $phone;
              $data_sms['v_smsString']= $message_text;
              $data_sms['V_AccountName']=$usern;
              $data_sms['i_status']='0';
              $data_sms['i_expiry']=$expiry; 
             insert_smsmessages($data_sms);

			//END 
			/*Aarti-16-04-2023
			code for - social media channel license flow.*/
			$channel_license = $_POST['channel_license'];
			$update_sql = "DELETE FROM $db.user_channel_assignment where userid='$uid'";
			mysqli_query($link,$update_sql);
			foreach($channel_license as $keychannel){	
				$sql_Channel="insert into $db.user_channel_assignment(userid,channel_type)values('$uid','$keychannel')";
			   mysqli_query($link,$sql_Channel) or die ("8> ".mysqli_error($link));
			}

			$data['status'] = 'true';
			$data['message'] = 'your login password has been sent on email & sms.';
			echo json_encode($data);
			exit;
	    }
	}else{
		$data['status'] = 'false';
		$data['message'] = 'Both User Name and Email ID should be Unique. Either of them already Exists. Please Check';
		echo json_encode($data);
		exit;
	}
}
// This function for Edit user Information Code
function Edit_User(){
	// define global variable 
	global $wpdb, $db,$link, $domain, $base,$Admin_groupId,$NonLogin_groupId,$Backoffice_groupId,$Link_Login,$from_helpdesk_email,$uc_ip,$db_asterisk;
    $user_id	= $_POST['userid'];
    $email	=$_POST['AtxEmail'];
	$data = array();
    $sql_user="select count(*) as countuser from $db.tbl_mst_user_company where V_EmailID='$email' and V_EmailID!='' AND I_UserStatus='1' AND I_UserID != '$user_id'";
    $res_user=mysqli_query($link,$sql_user);
    $num_rows=mysqli_fetch_array($res_user);
  	if($num_rows["countuser"]>'0'){
		$data['status'] = 'false';
		$data['message'] = 'Email ID should be Unique. Email ID is already Exists. Please Check';
		echo json_encode($data);
		exit;
  	}else{
		####### variables posted for IVR SETTINGS ##############
		$authenticated_mobile =($_POST['authenticated_mobile']);
		$tel_password =($_POST['tel_password']);
		$ip_address=($_POST['ip_address']);
		#---------------------UNI USER PROFILE TABLE-----------------------#
		$gender				=($_POST['gender']);
		$fname				=($_POST['fname']);
		$lastname			=($_POST['lastname']);
		$username_full		=($_POST['username_full']);
		$usern				=$fname." ".$lastname;
		$level				=($_POST['level']);
		$username			=($_POST['username']);
		$password			=($_POST['password']);
		$company			=($_POST['AtxCompany']);
		$department			=($_POST['department']);
		$jobtitle			=($_POST['jobtitle']);
		$boss				=$_POST['boss'];
	    $email				=($_POST['email']);
		$emailpassword		=($_POST['emailpassword']);
		$desc				=($_POST['desc']);
		$closedate1			=($_POST['AtxBirthDate']);//birthday
		$closedate			=view_dateformat($closedate1);
		$dateof_leave 		= ($_POST['dateof_join']);
		$dateof_leave1 	=	view_dateformat($dateof_leave);
		$AtxRegion=($_POST['Region']);
		/***check for update***/ 
		$sql_query_check = "SELECT AtxUserStatus,AtxEmail FROM $db.uniuserprofile WHERE AtxUserID='$user_id' " ;	
		$res_query_check = mysqli_query($link,$sql_query_check);
		$row_check = mysqli_fetch_assoc($res_query_check);
		$old_email = $row_check['AtxEmail'];
		$previous_status = $row_check['AtxUserStatus'] ;
		//if the user is already left and  try to change the status to Not left
		if($previous_status == '0' && $_REQUEST['status']==1){
			if($department!="" && $department!="0000"){
				$user_group=$department;//department id
				$sql_l="SELECT UserLicence,con_lic_cnt,named_lic_cnt FROM  $db.unigroupid where atxGid	='$user_group' ";
				$res_licen=mysqli_query($link,$sql_l);
				$row_licen=mysqli_fetch_array($res_licen);
				$named_lic_cnt=$row_licen['named_lic_cnt'];//to get the licence count 
				$con_lic_cnt=$row_licen['con_lic_cnt'];
				if($named_lic_cnt!=0){
					//if the user created count is greater than licence user count we should prompt a message to contact Administrator
					/*Get active user count*/
					$sql_c="select u.AtxUserID,u.AtxDisplayName,u.AtxUserName,u.AtxDesignation from $db.uniuserprofile as u ,
					$db.unigroupdetails as ug
					where u.AtxUserStatus=1 AND u.AtxUserID != '1' AND ug.atxGid='$user_group'
					AND ug.ugdContactID=u.AtxUserID";
					$q_createuser=mysqli_query($link,$sql_c);
					$rowq=mysqli_fetch_array($q_createuser);
					$AtxDesignation=$rowq['AtxDesignation'];
					$num_created_user=mysqli_num_rows($q_createuser);
					if($num_created_user>=$named_lic_cnt){
						$data['status'] = 'false';
						$data['message'] = "Named licence for  $AtxDesignation ($user_group) is $named_lic_cnt and the total user created is $num_created_user Please Contact your Administrator";
						echo json_encode($data);
						exit;
					}
				}
			}//end of department condition
		}
		/*******************Licence condition*************************/
		// if($_REQUEST['status']==1){	
		// 	/* Update : vijay  : 22-04-2021 */
		// 	// As disscused with team 
		// 	$title_arr =array();
		// 	$tele_rights_sql = "SELECT atxGid FROM $db.unigroupid WHERE telephony_rights ='1' " ;
		// 	$tele_rights_res = mysqli_query($link,$tele_rights_sql);
		// 	while ($tele_rows = mysqli_fetch_array($tele_rights_res) ) {
		// 		array_push($title_arr, $tele_rows['atxGid'])  ;
		// 	}
		// 	if(in_array($department, $title_arr)){
		// 		$uc_ip = $uc_ip;
		// 		$response = get_web_page("http://$uc_ip/agc/executequery.php?mode=insert&user=$username_full&pass=1234&emailid=$email"); 
		// 	}	
		// }//END OF STATUS COND
		// Fetching necessary data
		$hstreet			=($_POST['hstreet']);
		$hcity				=($_POST['hcity']);
		$hstate				=($_POST['hstate']);
		$hcountry			=($_POST['hcountry']);
		$pincode			=($_POST['pincode']);
		$phone				=($_POST['phone']);
		$contactphone		=($_POST['contactphone']);
		$fax				=($_POST['fax']);
		$pager				=($_POST['pager']);
		$mobile				=($_POST['mobile']);
		$status				=($_POST['status']);
	    $ext				=($_POST['ext']);
		$userfile_tmp_name = $_FILES['fileup']['name'];
		$assign = $_POST['assign'];
		$jobtitle_name = get_display_atx($jobtitle);
		if($userfile_tmp_name!=''){
			$archive_dir = "emp_photo";
			$userfile_name = $archive_dir."/".$user_id.$userfile_tmp_name;
			move_uploaded_file($_FILES['fileup']['tmp_name'], $userfile_name);
			 $sql_image="UPDATE  $db.uniuserprofile set File_up='".$userfile_name."' where AtxUserID='$user_id'";
			mysqli_query($link,$sql_image);
	    }
	    if($gender==''){
			$gender='2';   ########## gender is 2 if not selected any radio box  #################
		}
		// WFM related code adding - aarti ojha:29/03/23
		$vExtension =$_POST['ext'];
		$ddl_skill	=$_POST['ddl_skill'];
		$ddl_shift	=$_POST['ddl_shift'];

		// if($jobtitle == 'Agent'){
		// 	$classify=$_POST['classify'];
		// }else{
		// 	$classify=1;
		// }
		$classify=$_POST['classify'];
		if(count($ddl_skill)<=1){
			$ddl_skill1=$ddl_skill[0];
		}else{
			$ddl_skill1=implode(",",$ddl_skill);
		}
		//AtxPassword='$password', this field is used for IVR password
		/*** Update Personal Information In  uniuserprofile table ***/
		$sql1_up="UPDATE $db.uniuserprofile SET GivenName='$fname',sn='$lastname', AtxUserCat = '$level', AtxUserName='$username_full',AtxDisplayName='$usern',AtxCompany='$company',AtxDesignation='$jobtitle_name',AtxEmail='$email',AtxEmailPwd='$emailpassword',AtxBirthDate='$closedate',AtxGender='$gender',AtxStreet='$hstreet',AtxCity='$hcity',AtxState='$hstate',AtxCountry='$hcountry',AtxPinCode='$pincode',AtxHomePhone='$phone', AtxContactPhone='$contactphone', AtxContactFax='$fax', AtxPager='$pager', AtxMobile='$mobile',AtxDescription='$desc',AtxUserStatus='$status',i_Skillset='$ddl_skill1',i_shiftpref='$ddl_shift',i_classify='$classify',LeavingDate='$dateof_leave1' WHERE AtxUserID='$user_id'";
		$res=mysqli_query($link,$sql1_up) or die(" Query Error In uniuserprofile Table ".mysqli_error($link));
		$sql_en="update $db.encryp1 set IP_Address='$ip_address' where UserName='$username_full'";
		$res_en=mysqli_query($link,$sql_en) or die(" Query Error In encryp1 Table ".mysqli_error($link));
		if($res){
			$sql="update $db.tbl_mst_user_company set V_EmailID='$email', I_UserStatus='$status', V_MobileNo='$phone' where I_UserID='$user_id'";
			$result=mysqli_query($link,$sql) or die (" Query Error In tbl_mst_user_company Table " . mysqli_error($link));
		}
		/*** Close ****/ 
		/*** Update userhead Information In  uniuserprofile table ***/
		if(count($boss)<=1){
			$boss1=$boss[0];
		}else{
			$boss1=implode(",",$boss);
		}
		if(count($assign)<=1){
			$assign1=$assign[0];
		}else{
			$assign1=implode(",",$assign);
		}
		$num_rows=num_rows('userhead','UserID',$user_id);
		if($num_rows=='0'){
		 $sql2_boss="insert into $db.userhead (UserID,HeadID,vProjectAssign) values ('$user_id','$boss1','$assign1')";
		}else{
		   $sql2_boss="UPDATE $db.userhead SET HeadID='$boss1',vProjectAssign='$assign1' WHERE UserID='$user_id'";
		}
		$res2=mysqli_query($link,$sql2_boss) or die(" Query Error In userhead Table ".mysqli_error($link));
		/*** Close ****/ 
		//--------------------------------------start unigroupdetails--------------------------
 		$department	= $_POST['department'];
 		$sql5="select atxGid from $db.unigroupid where atxGid='$department'";
		$res5=mysqli_query($link,$sql5) ;
		$row5=mysqli_fetch_array($res5);
		$gid=$row5['atxGid'];

		$num_rows=num_rows('unigroupdetails','ugdContactID',$user_id);
		if($num_rows=='0'){
			$sql_pro="insert into $db.unigroupdetails (ugdContactID,atxGid) values ('$user_id','$gid')";
		}else{
			$sql_pro="UPDATE $db.unigroupdetails SET atxGid='$gid' WHERE ugdContactID='$user_id'";
		}
		$res_pro=mysqli_query($link,$sql_pro);
		#------------------------------end of unigroupdetails-----------------------------------#

		###### extension in Business Information ##############
		$num_rows=num_rows('uniautoatt','uaaUserID',$user_id);
		if($num_rows=='0'){
		 	$sql100="insert into $db.uniautoatt(uaaUserID,uaaExtnNo) values ('$user_id','$ext')";
		}else{
			$sql100="UPDATE $db.uniautoatt set uaaExtnNo='$ext' where uaaUserID='$user_id'";
		}
		mysqli_query($link,$sql100) or die ("Query Error In uniautoatt Table ".mysqli_error($link));
		/** Close ****/ 
		
		/*Aarti-16-04-2023
		code for - social media channel license flow.*/
		$channel_license = $_POST['channel_license'];
		$update_sql = "DELETE FROM $db.user_channel_assignment where userid='$user_id'";
		mysqli_query($link,$update_sql);
		if($_REQUEST['status'] == 1){ //for user active channel assignment
			foreach($channel_license as $keychannel){	
				$sql_Channel="insert into $db.user_channel_assignment(userid,channel_type)values('$user_id','$keychannel')";
			   mysqli_query($link,$sql_Channel) or die ("8> ".mysqli_error($link));
			}
		}
		// if email change then update autodial_users table also [aarti][19-09-2024]
		$email_update = '';
		if($old_email != $email){
			$email_update = ",emailid='$email'";
		}
		//[Aarti][18-04-2024] - for change autodial active status 
		$sql_autodial = "UPDATE $db_asterisk.autodial_users SET active_status='$status' $email_update WHERE emailid='$old_email'";
		mysqli_query($link,$sql_autodial);

		$data['status'] = 'true';
		$data['message'] = 'User Update successfully!!';
		echo json_encode($data);
		exit;
	}
}
function num_rows($tablename,$id,$user_id){
	global $db,$link;
	$sql="select * from $db.$tablename where $id='$user_id'";
	$res=mysqli_query($link,$sql);
	 $num_rows=mysqli_num_rows($res);
	return $num_rows;
}
############# function to find the MAXIMUM ID,  author :: sushila #############
function max_id($tablename,$id){
	global $db,$link;
	$sql="select max($id) as maximum from $tablename";
	$res=mysqli_query($link,$sql);
	if($row=mysqli_fetch_array($res)){
		$salarysno=$row['maximum'];
		//print $sno."<br>";
	 	$salarysno=$salarysno+1;
	}else{
		$salarysno=1;
	}
	return $salarysno;
}
################## end of function #################
?>