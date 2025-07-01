<?php
/***
 * Auth: Aarti Ojha
 * Date: 19-06-2024
 * Description: this file use for Display Case Create Page 
 * page name : new_case_manual_popup.php
 * 
**/
include_once("../config/web_mysqlconnect.php"); // Connection to database // Please do not remove this

session_start();
extract($_REQUEST);
//some variable define  for use
$vendor_lead_code=trim($vendor_lead_code);
$array=explode("-",$vendor_lead_code);

$flenmeg=$rfilename;
$callerid=(isset($array[2]) && $array[2]!='') ? $array[2] : $array[0]; // PHONE NUMBER
$agent=$user;
$sip_user=explode("/",$sip);
$ext=$sip_user[1];
	
include("../logs/logs.php"); // for log handling 
include("../logs/config.php"); // for log handling 

$query=mysqli_query($link,"select * from $db_asterisk.autodial_list where vendor_lead_code='$vendor_lead_code' order by lead_id desc")or die("Error autodial_list".mysqli_error($link));
	 $r1=mysqli_fetch_assoc($query);
	 if(__DBG__){
		$szMsg="Vendor lead ##".$vendor_lead_code."##";
		DbgLog(_LOG_INFO,__LINE__, "Page redirect--", $szMsg);
		//die;
	}
$id=$r1['lead_id'];
$name=$r1['first_name'];
$customer_id=$r1['customer_id'];//aAccount Number
$alt_phone=$r1['alt_phone'];//mobile
$list_id=$r1['list_id'];
$phone_number=$r1['phone_number'];//phone
$email=$r1['email'];
$org_name=$r1['organisation_name'];//landmark
$industry=$r1['industry'];//districtt
$website=$r1['website'];//location
$zip_code=$r1['postal_code'];
$city=$r1['city'];//district
$state=$r1['state'];
$address=$r1['address3'];//address
$description=$r1['comments'];
$cust_remark=$r1['description'];
$pulpagent=$r1['agent'];
$attempts=$r1['attempts'];
$source=$r1['source']; 
$lead_score=$r1['lead_score'];
$date_of_lead=($r1['date_of_lead']) ? $r1['date_of_lead'] : "";
$language=$r1['language'];

$myquery="select campaign_id from $db_asterisk.autodial_live_agents where user='$agent'";
	$myquery1=mysqli_query($link,$myquery);
	$myquery2=mysqli_fetch_assoc($myquery1);
	$campaignForPop=$myquery2['campaign_id'];
	

$myquery="select * from $db_asterisk.autodial_live_agents where user='$agent'";
$myquery1=mysqli_query($link,$myquery);
$myquery21=mysqli_fetch_array($myquery1);
$caller_unique_id=$myquery21['callerid'];
$caller_file=$myquery21['rec_filename'];
$phone_no=$myquery21['phone_no'];

$additional_info=$campaignForPop.'-List id-'.$list_id."-Lead ID:".$id."Agent-".$r1['agent']."-langu:".$language."CustomerId".$customer_id;
 $insert_log="INSERT INTO  $db_asterisk.tbl_log_vendorcode (vendor_code ,vdate ,v_agent ,ArrayRequest,file_name,caller_unique_id,flag )
VALUES (
'".$vendor_lead_code."',  NOW(),  '$agent',  '".$additional_info."','".$caller_file."','".$caller_unique_id."',1)";
mysqli_query($link,$insert_log);	
		

// get campaign name from list id ends
if($callerid=="") $callerid=$phone_number;
$todaytime=date("Y-m-d H:i:s");

$new_case_manual = base64_encode('new_case_manual');
$web_case_detail = base64_encode('web_case_detail');
$case_detail_backoffice_c2c = base64_encode('case_detail_backoffice_c2c');

$problem_defID = $_SESSION['problem_defID'];
$_SESSION['ticket_arr']="";
if($_SESSION['problem_defID']==""){
	$_SESSION['c2c_url']="";
	$_SESSION['problem_defID']="";
?>
<script>
	// Set a variable in local storage for new tab from sidebar
	localStorage.setItem('AgentOnCall', '1');
	
 	console.log("<?=$SiteURL?>CRM/helpdesk_index.php?token=<?=$new_case_manual?>&id=<?=$id?>&idd=<?=$acoount_cust_id?>&agent=<?=$agent?>&file=<?=$flenmeg?>&<?=$failed?>&campaignForPop=<?=$campaignForPop?>&vendor_lead_code=<?=$vendor_lead_code?>&id=<?=$id?>&language=<?=$language?>&phone_number=<?=$phone_no?>&list_id=<?=$list_id?>");

	window.location.href="<?=$SiteURL?>CRM/helpdesk_index.php?token=<?=$new_case_manual?>&id=<?=$id?>&idd=<?=$acoount_cust_id?>&agent=<?=$agent?>&file=<?=$flenmeg?>&<?=$failed?>&campaignForPop=<?=$campaignForPop?>&vendor_lead_code=<?=$vendor_lead_code?>&id=<?=$id?>&language=<?=$language?>&phone_number=<?=$phone_no?>&list_id=<?=$list_id?>&action=IVR";
</script>
<?php 
}else{
?>
<script>
	console.log("<?=$SiteURL?>CRM/helpdesk_index.php?token=<?=$web_case_detail?>&agent=<?=$agent?>&file=<?=$flenmeg?>&<?=$failed?>&campaignForPop=<?=$campaignForPop?>&vendor_lead_code=<?=$vendor_lead_code?>&prob_id=<?=$_SESSION['problem_defID']?>&lead_id=<?=$id?>&language=<?=$language?>&phone_number=<?=$callerid?>&list_id=<?=$list_id?>&action=IVR");
	
 	window.location.href="<?=$SiteURL?>CRM/helpdesk_index.php?token=<?=$web_case_detail?>&agent=<?=$agent?>&file=<?=$flenmeg?>&<?=$failed?>&campaignForPop=<?=$campaignForPop?>&vendor_lead_code=<?=$vendor_lead_code?>&prob_id=<?=$_SESSION['problem_defID']?>&lead_id=<?=$id?>&language=<?=$language?>&phone_number=<?=$callerid?>&list_id=<?=$list_id?>&action=IVR";
</script>
<?php
}
?>