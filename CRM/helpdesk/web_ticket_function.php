<?php
/***
 * CREATE TICKET
 * Author: Aarti Ojha
 * Date: 04-03-2024
 * This file is handling create ticket flow /Insert /Update
 * 1.Ticket Create many channel thought like - facebook,whatsapp,webchat,sms,twitter
 * 2.Handling ticket history record
 * Please do not modify this file without permission.
 **/
/**This function for fetch data according channel-[Email,twitter,facebook,sms]*/ 
// ajax call check action valid then fucntion init
/**
 * Change By : Vastvikta Nishad 
 * Date : 29/08/2024
 * Description : Added the code for attachment in the Ajax_Submit_Ticket(); function  for  omnichannels 
 */

session_start();
include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
						
// fetch user details
include "../web_function.php"; // For common function access
include_once "../../function/classify_function.php";
if(isset($_POST['action']) && $_POST['action'] == 'ajax_fecth_district'){ 
    ajax_fecth_district(); // Fetch district list to display in dropdown 
}
if(isset($_POST['action']) && $_POST['action'] == 'ajax_subCategory'){
    ajax_subCategory(); // Fetch Sub Category list to display in dropdown 
}
if(isset($_POST['action']) && $_POST['action'] == 'ajax_Category'){
    ajax_Category(); // Fetch Category list to display in dropdown 
}
if(isset($_POST['action']) && $_POST['action'] == 'ajax_department'){
    ajax_department(); // Fetch Department list to display in dropdown
}
if(isset($_POST['action']) && $_POST['action'] == 'ajax_Assign_Department'){
    ajax_Assign_Department();
}
if(isset($_POST['action']) && $_POST['action'] == 'ajax_addInteraction'){
    // addInteraction_form(); // Insert feedback details and update Ticket status
    interaction_remark();
}
if(isset($_POST['action']) && $_POST['action'] == 'Submit_Ticket'){
    Ajax_Submit_Ticket(); // This function for create Ticket and Insert/update Customer details
}
if(isset($_POST['action']) && $_POST['action'] == 'interaction_remark_form'){
    interaction_remark(); 
}
if(isset($_POST['action']) && $_POST['action'] == 'Dispose_Submit'){
    Ajax_Dispose_Submit(); // IVR Ticket Disposition submit code
}
if($_POST['action'] == 'interaction_data'){
	get_interaction_data();
}
if($_POST['action'] == 'update_open_time'){
	update_open_time();
}
/* Fetch Knowledge Base AI Assistant :: Farhan Akhtar [01-02-2025] */
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Ask_KnowledgeBase' && isset($_REQUEST['question'])) {

	header('Content-Type: application/json');

	try {
		// Debugging: Log received data
		// file_put_contents("debug.log", print_r($_REQUEST, true), FILE_APPEND);
		
		$category = "General";
		$top_k = 1;
		$question = trim($_REQUEST['question']);
	
		// Call the API function
		$response = askAPI($askAPI_url, $category, $top_k, $question);
	
		// Ensure valid JSON output
		if (!is_array($response) || empty($response)) {
			throw new Exception("Empty or invalid API response.");
		}
	
		echo json_encode($response, JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		http_response_code(400);
		// file_put_contents("debug.log", "Error: " . $e->getMessage(), FILE_APPEND);
		echo json_encode(["error" => $e->getMessage()]);
	}

	exit();
	
}

/* Modified By Farhan on 27-06-2024 */
$user_id = $_SESSION['userid'];
$groupid = $_SESSION['user_group'];
/* End */

function Social_Media_Queue(){
	global $db,$link;
	// STEP-1 Email Queue Data
	$social_type_id = '';
	$vsubject = "";
	$vbody = "";
	$conversation = $fact = '';
	$d_email_date ='';
	$dbname = $db.'_master';
	if (isset($_REQUEST['emailid']) && $_REQUEST['emailid'] != '') {
	   $emailid = $_REQUEST['email'];
	   $ssssss  = "select v_subject,v_body,v_fromemail,d_email_date from $db.web_email_information where EMAIL_ID='" . $_REQUEST['emailid'] . "'";
	   $query5 = mysqli_query($link, $ssssss);
	   $query5 = mysqli_fetch_array($query5);

	   $vsubject = $query5['v_subject'];
	   $vbody = $query5['v_body']; 
	   $d_email_date = $query5['d_email_date'];//[vastvikta][09-01-2025]
	   $fact = $vbody;
	//    $fact = $vbody;
	   $first_name = 'na';
	   $last_name = 'na';
	   $emailid = $_GET['emailid'];
	   $email = $query5['v_fromemail'];

	   $vuserid = $_SESSION['userid'];
	   $sql_update = "UPDATE $db.web_email_information SET  i_reminder=1,case_open_time=NOW(),userid = $vuserid WHERE  EMAIL_ID =$emailid  ";
	   mysqli_query($link, $sql_update) or die("Error In web_email_information " . mysqli_error($link));
	   $equery = "select AccountNumber from $db.web_accounts where (email = '$emailid' ) ;";
	   $res11 = mysqli_query($link, $equery) or die('MYSQLERROR ' . mysqli_error($link));
	   $rowdat =  mysqli_fetch_assoc($res11);
	   $AccountNumber = $rowdat['AccountNumber'];
	   $social_type_id = $_GET['emailid'];
	}
	// STEP-2 Twitter Queue Data
	if (isset($_REQUEST['twitterid']) && $_REQUEST['twitterid'] != '') {
	   $query5 = mysqli_query($link, "select v_TweeterDesc,i_TweetID,v_Screenname from $db.tbl_tweet where i_ID='" . $_REQUEST['twitterid'] . "'");
	   $query5 = mysqli_fetch_array($query5);
	   $fact = $query5['v_TweeterDesc'];
	   $fname = $query5['v_Screenname'];
	   $twitterhandle = $fname;
	   $first_name = $twitterhandle;
	   $tweet_id = $_REQUEST['twitterid'];
	   $sql_update_tweet = "UPDATE $db.tbl_tweet SET  i_reminder=1,case_open_time=NOW() WHERE  i_ID =$tweet_id  ";
	   mysqli_query($link, $sql_update_tweet) or die("Error In Query tweet " . mysqli_error($link));

	   $equery = "select AccountNumber from $db.web_accounts where (twitterhandle = '$twitterhandle' ) ;";
	   $res11 = mysqli_query($link, $equery) or die('MYSQLERROR ' . mysqli_error($link));
	   $rowdat =  mysqli_fetch_assoc($res11);

	   $AccountNumber = $rowdat['AccountNumber'];
	   $social_type_id = $_GET['twitterid'];
	}
	// STEP-2 Twitter Queue Data
	if (isset($_REQUEST['whatsappid']) && $_REQUEST['whatsappid'] != '') {
	   $query5 = mysqli_query($link, "select * from $db.whatsapp_in_queue where id='" . $_REQUEST['whatsappid'] . "'");
	   $query5 = mysqli_fetch_array($query5);
	   $whatsapphandle = $query5['send_from'];;
	   $whatsappid = $_REQUEST['whatsappid'];

	   $equery = "select AccountNumber from $db.web_accounts where (whatsapphandle = '$whatsapphandle' ) ;";
	   $res11 = mysqli_query($link, $equery) or die('MYSQLERROR ' . mysqli_error($link));
	   $rowdat =  mysqli_fetch_assoc($res11);

	   $AccountNumber = $rowdat['AccountNumber'];
      $social_type_id = $_GET['whatsappid'];

      $sqldm="SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.whatsapp_out_queue WHERE send_to ='".$whatsapphandle."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.whatsapp_in_queue WHERE send_from ='".$whatsapphandle."' ORDER BY create_date asc";
      $res11 = mysqli_query($link, $sqldm) or die('MYSQLERROR ' . mysqli_error($link));
      while($rm=mysqli_fetch_assoc($res11)){
         $color = 'lightgrey';
         $name = ($rm['send_to']!=$whatsapphandle) ? ' Sender ' : 'Recipient ';
         $whatsap_mesg .= '<div class="chat-message"><span class="message-text">' . $name . ' : ' . $rm['message'] . '</span><span class="message-time">' . $rm['create_date'] . '</span></div>';
      }
      $fact = $whatsap_mesg;

	}
	// STEP-3 WEB Chat Queue Data
	if (isset($_GET['chatid']) && isset($_GET['mr'])) {
	   $source = $_GET['mr'];
	   $query5 = mysqli_query($link, "select * from $db.overall_bot_chat_session where chat_session='".$_GET['chatid']."'");
	   $query5 = mysqli_fetch_array($query5);
	   $id   =  $query5['id'];
	   $phone   =  $query5['from'];
	   $email   =     $query5['email'];
	   $datetime = $query5['createdDatetime'];
		$fact = $query5['query'];
		$name = explode(" ", $query5['name']);
		if (count($name) > 0) {
			$first_name = $name[0];
			$last_name = isset($name[1]) ? $name[1] : '';
		} else {
			$first_name = $query5['name'];
			$last_name = '';
		}

		$social_type_id = $_GET['chatid'];

		$sql = "SELECT * FROM $db.in_out_data WHERE chat_session_id='$social_type_id'";
		$mquery511 = mysqli_query($link, $sql) or die("mysqli error " . mysqli_error($link));

		$chat_mesg = '';
		while ($ro = mysqli_fetch_assoc($mquery511)) {
			$color = 'lightgrey';
			$name = ($ro['direction'] == 'IN') ? 'Customer' : 'Agent';
			$chat_mesg .= '<div class="chat-message" style="background-color:' . $color . ';">';
			$chat_mesg .= '<span class="message-text">' . $name . ': ' . htmlspecialchars($ro['message'], ENT_QUOTES, 'UTF-8') . '</span>';
			$chat_mesg .= '<span class="message-time" style="float: right;">' . htmlspecialchars($ro['create_datetime'], ENT_QUOTES, 'UTF-8') . '</span>';
			$chat_mesg .= '</div>';
		}

		$fact = $chat_mesg;

	}
	// STEP-4 Facebook Queue Data
	if (isset($_REQUEST['facebookid']) && $_REQUEST['facebookid'] != '') {
	   $query5 = mysqli_query($link, "select userid,name,comment from $db.tbl_facebook where id='" . $_REQUEST['facebookid'] . "'");
	   $query6 = mysqli_fetch_array($query5);
	   $useridd = $query6['userid'];
	   $fact = $query6['comment'];
	   $fname = $query6['name'];
	   $fbhandle = $useridd;
	}
	// STEP-4 SMS Queue Data
	if (isset($_REQUEST['smsid']) && $_REQUEST['smsid'] != '') {
	   $query_sms = mysqli_query($link, "select v_smsString from $db.tbl_smsmessagesin where i_id='" . $_REQUEST['smsid'] . "'");
	   $res_sms = mysqli_fetch_array($query_sms);
	   $fact = $res_sms['v_smsString'];
	}

	// messenger chat display handling code [Aarti][14-06-2024]
   if (isset($_REQUEST['messengerid']) && $_REQUEST['messengerid'] != '') {
      $query5 = mysqli_query($link, "select * from $db.messenger_in_queue where id='" . $_REQUEST['messengerid'] . "'");
      $query5 = mysqli_fetch_array($query5);
      // $fact = $query5['message'];
      $messengerhandle = $query5['send_from'];
      $messengerid = $_REQUEST['messengerid'];

      $equery = "select AccountNumber from $db.web_accounts where (fbhandle = '$messengerhandle' ) ;";
      $res11 = mysqli_query($link, $equery) or die('MYSQLERROR ' . mysqli_error($link));
      $rowdat =  mysqli_fetch_assoc($res11);

      $AccountNumber = $rowdat['AccountNumber'];
      $social_type_id = $_GET['messengerid'];

      $sqldm="SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.messenger_out_queue WHERE send_to ='".$messengerhandle."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.messenger_in_queue WHERE send_from ='".$messengerhandle."' ORDER BY create_date asc";
      $res11 = mysqli_query($link, $sqldm) or die('MYSQLERROR ' . mysqli_error($link));

      while($rm=mysqli_fetch_assoc($res11)){
        $color = 'lightgrey';
        $name = ($rm['send_to']!=$messengerhandle) ? ' Sender ' : 'Recipient ';
        $fact .= '<div class="chat-message"><span class="message-text">' . $name . ' : ' . $rm['message'] . '</span><span class="message-time">' . $rm['create_date'] . '</span></div>';
      }
   }	
    // Instagram chat display handling code [Aarti][19-11-2024]
   if (isset($_REQUEST['instagramid']) && $_REQUEST['instagramid'] != '') {
      $query5 = mysqli_query($link, "select * from $db.instagram_in_queue where id='" . $_REQUEST['instagramid'] . "'");
      $query5 = mysqli_fetch_array($query5);
      // $fact = $query5['message'];
      $instagramhandle = $query5['send_from'];
      $instagramid = $_REQUEST['instagramid'];

      $equery = "select AccountNumber from $db.web_accounts where (fbhandle = '$instagramhandle' ) ;";
      $res11 = mysqli_query($link, $equery) or die('MYSQLERROR ' . mysqli_error($link));
      $rowdat =  mysqli_fetch_assoc($res11);

      $AccountNumber = $rowdat['AccountNumber'];
      $social_type_id = $_GET['instagramid'];

      $sqldm="SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.instagram_out_queue WHERE send_to ='".$instagramhandle."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM $db.instagram_in_queue WHERE send_from ='".$instagramhandle."' ORDER BY create_date asc";
      $res11 = mysqli_query($link, $sqldm) or die('MYSQLERROR ' . mysqli_error($link));

      while($rm=mysqli_fetch_assoc($res11)){
        $color = 'lightgrey';
        $name = ($rm['send_to']!=$instagramhandle) ? ' Sender ' : 'Recipient ';
        $fact .= '<div class="chat-message"><span class="message-text">' . $name . ' : ' . $rm['message'] . '</span><span class="message-time">' . $rm['create_date'] . '</span></div>';
      }
   }

	$info['social_type_id'] = $social_type_id;
	$info['fact'] = $fact;
	if(isset($first_name)){ 
   		// $first_name = $first_name;
	}else{
	   $first_name = '';
	}
	if(isset($last_name)){ 
	//    $last_name = $last_name;
	}else{
	   $last_name = '';
	}
	$info['last_name'] = $last_name;
	$info['first_name'] = $first_name;
	$info['twitterhandle'] = $twitterhandle;
	$info['whatsapphandle'] = $whatsapphandle;
	$info['messengerhandle'] = $messengerhandle;
	$info['instagramhandle'] = $instagramhandle;
	$info['email'] = $email;
	$info['d_email_date'] = $d_email_date;//[vastvikta][09-01-2025]
	$info['phone'] = $phone;
	return $info;
}
/**** in case of other third party option like chat getting customer id ***/
function customer_data(){
	global $db,$link;
	if ((isset($_GET['customerid']) && $_GET['customerid'] != '') || isset($_GET['phone_number'])) {
	    if (isset($_REQUEST['language'])) {
	      $language = $_REQUEST['language'];
	    }
	    $customerid =  $_GET['customerid']; 
	    if($_GET['phone_number']){
	      $phone = str_replace(' ','',$_GET['phone_number']);
	      $query = "select * from $db.web_accounts  where phone ='$phone' ";
	    }else{
	       $query = "select * from $db.web_accounts a  where a.AccountNumber = '$customerid' ";
	    }
	    $res = mysqli_fetch_assoc(mysqli_query($link, $query));
	    return $res;
	}
}
/** Search For existing cases , populate all the values in a form **/ 
function search_docket(){
	global $db,$link;
	$docket_no = (trim($_POST['search-docket']) == '') ? $_POST['docket_no_new'] : $_POST['search-docket'];
	if (!empty($docket_no)) {
		$query = "select * from $db.web_accounts a, $db.web_problemdefination p where a.AccountNumber=p.vCustomerID  and ticketid='$docket_no'  ";
		$res = mysqli_fetch_assoc(mysqli_query($link, $query));
	}
	return $res;
}
/** Handle html special charts **/ 
function test_input($data){
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
/* Fetch country list for display in dropdwon **/
function County_list(){
 global $db,$link;
  $res=mysqli_query($link, "select id,city from $db.web_city where status='1' ");
  return $res;
}
/* Fetch Sub Country list for display in dropdwon**/
function SubCounty($district_id){
	global $db,$link;
	// echo "select id, vVillage from $db.web_Village where iDistrictID='$district_id' AND status ='1' ORDER BY vVillage ASC  ";
	$villages_query = mysqli_query($link, "select id, vVillage from $db.web_Village where iDistrictID='$district_id' AND status ='1' ORDER BY vVillage ASC  ");
	return $villages_query;
}
/** Fetch gender list **/
function getgender(){
	global $db,$link;
	$sql = "select * from $db.web_gender";
	$result = mysqli_query($link,$sql);
	return $result;
}
/*** Complaint Origin data ***/
function getComplaint(){
	global $db,$link;
	if(isset($_SESSION['customer_view'])==1){
	    $sqlsource = "select id,source from $db.web_source where source='Customer Portal'";
	}else{
	    $sqlsource = "select id,source from $db.web_source where status='1' and source <> 'Customer Portal' order by id='2' desc";
	}
	$result = mysqli_query($link,$sqlsource);
	return $result;
}
/** Fetch  languge list ***/
function getlanguage(){
	global $db,$link;
	$sqllang = "select id,lang_Name from $db.web_language where status='1'";
    $langresult = mysqli_query($link, $sqllang);
    return $langresult;
}
/*** Reasons_calling list ***/
function getreasons_calling(){
	global $db,$link;
	$sqllang = "select id, complaint_name, slug, status from $db.complaint_type where status = '1'";
    $complaint_sql = mysqli_query($link, $sqllang);
    return $complaint_sql;
}
/*** Reasons_calling list ***/
function getcomplaintlist(){
	global $db,$link;
	$sqllang = "select id,ticketstatus from $db.web_ticketstatus where status='1' and id<>4";
    $complaint_sql = mysqli_query($link, $sqllang);
    return $complaint_sql;
}
/** Fetch category list ***/
function getwebcategory(){
	global $db,$link;
	// $sqllang = "select id, category from $db.web_category and type='$type' where status=1 ";
	$sqllang = "select id, category from $db.web_category where status=1 ";
    $sourceresult = mysqli_query($link, $sqllang);
    return $sourceresult;
}
/** Fetch  Sub Category list ***/
function getwebsubcategory($catid){
	global $db,$link;
	$sqllang = "select * from $db.web_subcategory where category='$catid' AND status =1 ";
    $sourceresult = mysqli_query($link, $sqllang);
    return $sourceresult;
}
/** Fetch web_projects table details **/ 
function getwebprojects(){
	global $db,$link;
	$sqllang = "select pId,vProjectName from $db.web_projects where i_Status='1'";
    $sourceresult = mysqli_query($link, $sqllang);
    return $sourceresult;
}
/*** Fetcg details ticket history table ***/
function gethistory($customer_id){
	global $db,$link;
	$date = date('Y-m-') . '01 00:00:00';
	$whr_cond = " AND d_createDate >= DATE_SUB('" . $date . "', INTERVAL 60 DAY) ";  // last 60 days record 
	$sql_cases = "select * from $db.web_problemdefination where vCustomerID='$customer_id' $whr_cond order by d_createDate desc limit 10";
	$ticket_query = mysqli_query($link, $sql_cases);
	return $ticket_query;
}
/** Disposition details Fetch **/ 
function disposition(){
	global $db_asterisk,$link;
	$qdispo = "select V_DISPO,V_DISPOSITION from $db_asterisk.tbl_disposition where I_Status=1 order by V_DISPOSITION asc ;";
    $ticket_query = mysqli_query($link, $qdispo);
    return $ticket_query;
}
/**this func call from ajax onchange county get subcounty list***/
function ajax_fecth_district(){
	global $db,$link;
	if(isset($_POST['district_id']) && $_POST['district_id'] > 0){
		$district_id = trim($_POST['district_id']);
		$query = "select id, vVillage  FROM $db.web_Village WHERE iDistrictID='$district_id' AND status ='1' ORDER BY vVillage ASC  " ;
		$res = mysqli_query($link,$query);
		while ($row = mysqli_fetch_assoc($res)) {
			$resp[] = $row;
		}
		echo json_encode($resp);die();
	}
}
function ajax_department(){
	global $db,$link;
	if(isset($_POST['category_id2']) && $_POST['category_id2'] > 0){
		$category_id = trim($_POST['category_id2']);
		 $query = "select pId, vProjectName  FROM $db.web_projects WHERE FIND_IN_SET($category_id,Type) AND i_Status ='1' " ;
		$res = mysqli_query($link,$query);
		while ($row = mysqli_fetch_assoc($res)) {
			$resp[] = $row;
		}
		echo json_encode($resp);die();
	}
}
/* for onchange Category  then fetch subCategory list (ajax call)*/
function ajax_subCategory(){
	global $db,$link;
	$category = $_POST['cat_id'];
	$subcategory = $_POST['subcat_id'];
	$html = '';
	if(isset($_POST['cat_id'])){	
		$html ='<select name="v_subcategory" id="v_subcategory" class="select-styl1" style="width:190px;">
			<option value="">Select Sub Category</option>';
		$subcat_query = mysqli_query($link,"select * from $db.web_subcategory where category='$category' AND status =1 ");
		while($subcat_res = mysqli_fetch_array($subcat_query)){
			$html .= '<option value="'.$subcat_res['id'].'"';
			 if($subcat_res['id']==$subcategory){ 
			 	echo "selected"; 
			 }
			 $html .= '>';
			$html .= $subcat_res['subcategory'];
			$html .= '</option>';
		} 
		$html .= '</select>';
		echo $html;
	}			                        				                           
}
/* for onchange Category list (ajax call)*/
function ajax_Category(){
	global $db,$link;
	$type = $_POST['type'];
	$catid = $_POST['catid'];
	if(isset($_POST['type'])){
		$html = '';
		$cat_query = mysqli_query($link,"SELECT id,category FROM $db.web_category WHERE status = 1 ORDER BY category ASC");
		if($type =='others'){
			$cat_query = mysqli_query($link,"SELECT id,category FROM $db.web_category WHERE status = 1 AND category='Others' ORDER BY category ASC");
		}
	    $html .= '<select name="v_category" id="v_category" class="select-styl1" style="width:180px" onChange="web_subcat(this.value,'."''".');get_department(this.value)">';

	    $html .= '<option value="0">Select Category</option>';			
		while($cat_res = mysqli_fetch_array($cat_query)){
	        $html .= '<option value="'.$cat_res['id'].'"';
	        	if($cat_res['id']==$catid){ 
	        		echo "selected"; 
	        	}
	        $html .= '>';
	        $html .= $cat_res['category'];
	        $html .= '</option>';
        }
		$html .= '</select>';
		echo $html;
	}			                        				                           
}
/* get Assign Department List */
function ajax_Assign_Department(){
	global $db,$link;
	if(isset($_POST['department_id']) && $_POST['department_id'] > 0){
		$department_id = trim($_POST['department_id']);
		 $query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $db.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$department_id'  " ;
		$res = mysqli_query($link,$query) or 'ERROR '. mysqli_error($link);
		$str = [];
		if(mysqli_num_rows($res) > 0){
			while ($row = mysqli_fetch_assoc($res)) {
				array_push($str, $row['V_EmailID']);
			}
		echo implode(", ", $str);die();
		}
		echo '';die();		
	}
}

/*** FOR Insert/Update Customer details and Create Ticket Code Start****/ 
/**
 * Create ticket using multiple channels like SMS, EMAIL, TWITTER, FACEBOOK, WHATSAPP, IVR, MANUAL
 *
 * Step-1: Validation
 * Step-2: Create user and create ticket flow
 * Step-3: Send Ticket Mails
 */

function Ajax_Submit_Ticket(){
	global $from_email,$db,$link,$Closed_status,$Resolved_status,$Pending_status,$whatsapp_path,$DocumentType_WhatsappId,$messenger_path,$DocumentType_MessengerId,$chat_path,$DocumentType_ChatId;
		// Sanitize input data
		$vuserid        =  $_SESSION['userid'];
	    $logedin_agent    =    $_SESSION['logged'];
	   foreach ($_POST as $key => $input_arr) {
	      $_POST[$key] = addslashes(trim($input_arr));
	   }
	   /***** Validation Code Start *****/ 
	   $count = 1;
	   $errors = [];
	   extract($_POST); 

	   $fname = $first_name . " " . $last_name;
	   // Name validation
	   if ($first_name == '') {
	      $errors[] = "Firts Name can not be blank!";
	      $count = 2;
	      $datajson['status'] = false;
	      $datajson['ticketid'] = '';
	      $datajson['message'] = 'Firts Name can not be blank!';
	      echo json_encode($datajson);
	      exit();
	   }
	   //Category validation check
	   if ($v_category == '') { 
	      $errors[] = "Please Select Category !";
	      $count = 2;
	      $datajson['status'] = false;
	      $datajson['ticketid'] = '';
	      $datajson['message'] = 'Please Select Category !';
	      echo json_encode($datajson);
	      exit();
	   }
	   //Sub category validation check
	   if ($v_subcategory == '') { 
	      $errors[] = "Please Select Sub Category !";
	      $count = 2;
	      $datajson['status'] = false;
	      $datajson['ticketid'] = '';
	      $datajson['message'] = 'Please Select Sub Category !';
	      echo json_encode($datajson);
	      exit();
	   }
	   // Phone validation
	   if ($phone == '') {
	      $errors[] = "Caller Number can not be blank!";
	      $count = 2;
	      $datajson['status'] = false;
	      $datajson['ticketid'] = '';
	      $datajson['message'] = 'Caller Number can not be blank!';
	      echo json_encode($datajson);
	      exit();
	   }
	   // Email validation
	   /***email section to check email exist or not ***/
	   if (!empty($_POST['email']) && $_POST['email'] != "NA" && $_POST['email'] != "na") {
	      $email1 = test_input($_POST["email"]);
	      $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
	      if (!preg_match($regex, $email1)) {
	         $errors[] = "Invalid email format!";
	         $count = 2;
	         $datajson['status'] = false;
		      $datajson['ticketid'] = '';
		      $datajson['message'] = 'Invalid email format!';
		      echo json_encode($datajson);
		      exit();
	      } 
	   }
	   // Twitter handle validation
	   /**** twitter section to check twitter exist or not ****/
	   if (!empty($_POST['twitterhandle']) && $_POST['twitterhandle'] != "NA" && $_POST['twitterhandle'] != "na") {
	      if ((isset($_REQUEST['customerid']) && $_REQUEST['customerid'] != '') || (isset($_REQUEST['mr']) && $_REQUEST['mr'] > 0)) {
	         if (!empty($_GET['customerid'])) {
	            $cus_id = empty($_GET['customerid']) ? $AccountNumber : $_GET['customerid'];
	         } else {
	            $cus_id = empty($_REQUEST['customerid']) ? $AccountNumber : $_REQUEST['customerid'];
	         }

	         $twitter_sql = "select AccountNumber,email, phone from $db.web_accounts where (twitterhandle = '$twitterhandle' and twitterhandle!='') && AccountNumber !='$cus_id' ";
	      } else {
	         $twitter_sql = "select AccountNumber,email, phone from $db.web_accounts where (twitterhandle = '$twitterhandle' and twitterhandle!='') email != '$email'";
	      }
	      $twitter_result = mysqli_query($link, $twitter_sql);
	        if (mysqli_num_rows($twitter_result) > 0) {
	         	$errors[] = "This twitter handle already Exist !";
	         	$count = 2;
	         	$datajson['status'] = false;
		      	$datajson['ticketid'] = '';
		      	$datajson['message'] = 'This twitter handle already Exist !';
		      	echo json_encode($datajson);
		      	exit();
	        } else {
		         $get_query = "select AccountNumber,email, phone from $db.web_accounts where (phone = '$phone' and phone!='') ";
		         $get_res = mysqli_query($link, $get_query);
		         if (mysqli_num_rows($get_res) > 0) {
		            $res1 = mysqli_fetch_assoc($get_res);
		         }
	        }
	    }//...!!! twitter section Close
	    // Web chat validation
	    /**** web chat section to check email & phone exist or not ****/
	   	if (!empty($_REQUEST['chatid']) && $_REQUEST['chatid'] != "NA" && $_REQUEST['chatid'] != "na") {
	        if (isset($_REQUEST['customerid']) && $_REQUEST['customerid'] != '') {
		         if (!empty($_GET['customerid'])) {
		            $cus_id = empty($_GET['customerid']) ? $AccountNumber : $_GET['customerid'];
		         } else {
		            $cus_id = empty($_REQUEST['customerid']) ? $AccountNumber : $_REQUEST['customerid'];
		         }
		         $chat_query = "select AccountNumber,email, phone from $db.web_accounts where ((phone = '$phone' and phone!='') || (email = '$email' and email!='') ) && AccountNumber !='$cus_id' ";
	     	} else {
	          $chat_query = "select AccountNumber,email, phone from $db.web_accounts where ( (phone = '$phone' and phone!='') || (email = '$email' and email!='') ) ";
	      	}
	        $chat_result = mysqli_query($link, $chat_query);
	        if (mysqli_num_rows($chat_result) > 0) {
		         if($_POST['mr']!=5) // changes for chat :: 23-06-2022
		         {
		         	$errors[] = "Either email or phone is already exist !";
		         	$count = 2;
		         	$datajson['status'] = false;
		      		$datajson['ticketid'] = '';
		      		$datajson['message'] = 'Either email or phone is already exist !';
		      		echo json_encode($datajson);
		      		exit();
		         }
	      	} else {
	         	$res1 = [];
	         	$get_chat_query = "select AccountNumber,email, phone from $db.web_accounts where (phone = '$phone' and phone!='') || (email = '$email' and email!='') ";
	        	 $getchat_res111 = mysqli_query($link, $get_chat_query);
	         	if (mysqli_num_rows($getchat_res111) > 0) {
	            	$res1 = mysqli_fetch_assoc($getchat_res111);
	         	}
	      	}
	   	} //...!!! web chat section Close

	   // If any errors found 
	    if ($count == 2) {
	    	$datajson['status'] = false;
        	$datajson['ticketid'] = '';
         $datajson['message'] = 'something went wrong';
	      echo json_encode($datajson);
	      exit();
	    } else {
	    	// Code for getting social media feedback
	    	/** this code for get social media feedback **/ 
	        if (isset($_GET['chatid']) && isset($_GET['mr'])) {
	           $v_remark_type = addslashes($v_remark_type);
	        }
	        if (isset($_POST['chat_remark']) && !empty($_POST['chat_remark'])) {
	           $chat_remark = $_POST['v_remark_type'];
	           $v_remark_type = addslashes($chat_remark);
	        }
	        if (isset($_POST['email_remark']) && !empty($_POST['email_remark'])) {
	           $email_remark = $_REQUEST['v_remark_type'];
	           $email_remark = addslashes($email_remark);
	        }
	        if (isset($_POST['twitter_remark']) && !empty($_POST['twitter_remark'])) {
	           $twitter_remark = $_REQUEST['v_remark_type'];
	           $v_remark_type = addslashes($twitter_remark);
	        }

	        if (isset($_POST['facebook_remark']) && !empty($_POST['facebook_remark'])) {
	           $facebook_remark = $_REQUEST['v_remark_type'];
	           $v_remark_type = addslashes($facebook_remark);
	        }
	        if (isset($_POST['sms_remark']) && !empty($_POST['sms_remark'])) {
	           $sms_remark = $_REQUEST['v_remark_type'];
	           $v_remark_type = addslashes($sms_remark);
	        }
	        // END

	        /*** Insert/Update details in web account table and web_problemdefination ***/ 
		      $get_chat_query = "select AccountNumber,email, phone from $db.web_accounts where (phone = '$phone' and phone!='') || (email = '$email' and email!='') ";
		      $getchat_res111 = mysqli_query($link, $get_chat_query);
		      if (mysqli_num_rows($getchat_res111) > 0) {
		         $res1 = mysqli_fetch_assoc($getchat_res111);
		      }
		      $vuserid        =  $_SESSION['userid'];
		      $todaydate = date("Y-m-d H:i:s");
		      $rows = $res1;
		      $customerid = $rows['AccountNumber'];
		      $phone_no = $_POST['phone'];
		      $pdf_genrate = false ; 
		      $customer_id = $_POST['customerid'];

		      $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : '';
		      $smshandle = isset($_POST['smshandle']) ? $_POST['smshandle'] : '';
		      $whatsapphandle = isset($_POST['whatsapp_number']) ? $_POST['whatsapp_number'] : '';
		      $town = isset($_POST['town']) ? $_POST['town'] : '';
		      $area = isset($_POST['area']) ? $_POST['area'] : '';
		      $passport_number = isset($_POST['passport_number']) ? $_POST['passport_number'] : '';
		      $business_number = isset($_POST['business_number']) ? $_POST['business_number'] : '';
		      $tpin = isset($_POST['register_tpin']) ? $_POST['register_tpin'] : '';
		      $tax_id = isset($_POST['taxtype']) ? $_POST['taxtype'] : '';
		      $company_name_case = $_POST['company_name'];
			   $company_registration = $_POST['company_registration'];
			   $root_cause = $_POST['root_cause'];
			   $corrective_measure = $_POST['corrective_measure'];
			   $regional = $_POST['regional'];
			   $customertype  = $_POST['customertype'];
			  	$currentdate = date("Y-m-d H:i:s");
		         $feedback = isset($_POST['feedback']) ? $_POST['feedback'] : '';
		         $status_type_ = isset($_POST['feedback']) ? '3' : $_POST['status_type_'];
		         $lang = ( empty($lang) || $lang=='0' ) ? '1' : $lang;
		         $root_cause = $_POST['root_cause'];
		         $corrective_measure = $_POST['corrective_measure'];

		         $messengerhandle = $_POST['messengerhandle']; //for messenger sender id fetch[Aarti][14-08-2024]
		         $instagramhandle = $_POST['instagramhandle']; //for Instagram sender id fetch[Aarti][19-11-2024]
			/** if the customer already exists then update otherwise insert a new record **/
	      if (count($rows) <= 0) {
		        $insert_customer_query = "insert into $db.web_accounts(fname, createddate, address,v_Location, district, phone, mobile, country, fbhandle, twitterhandle, email,alternate_email, v_Village,gender,age_grp,priority_user,v_passwd,nationality,smshandle,whatsapphandle,town,area,passport_number,business_number,company_name,company_registration,regional,customertype,messengerhandle,instagramhandle) values('$fname','$todaydate','" . addslashes($address_1) . "','" . addslashes($address_2) . "','$district','$phone_no','$mobile','$country','$fbhandle','$twitterhandle','$email','$alternate_email' ,'$villages','$gender','$age','$priority_user','$first_name','$nationality','$smshandle','$whatsapphandle','$town','$area','$passport_number','$business_number','$company_name_case','$company_registration','$regional','$customertype','$messengerhandle','$instagramhandle')  ";
		         mysqli_query($link, $insert_customer_query) or die("Error In web_accounts " . mysqli_error($link));
		         $customerid = mysqli_insert_id($link);
		         $final_action = "$name User Create New Cutomer In Process $process_case_type  And Cutomer Id is $fname";
		         // add_audit_log($vuserid, 'create_customer', $customerid, json_encode($_REQUEST), $db);		        

	      }else{
	         	/*** update customer information if exist  ***/
	         	$update_customer = "update $db.web_accounts set phone='$phone_no' ,fname='$fname' , address='" . addslashes($address_1) . "' , v_Location='" . addslashes($address_2) . "', district='$district', v_Village='$villages', mobile='$mobile',   updatedate=NOW() ,  twitterhandle='$twitterhandle',fbhandle='$fbhandle', email='$email',alternate_email='$alternate_email', gender='$gender' , age_grp='$age' ,priority_user='$priority_user',nationality='$nationality',smshandle='$smshandle',whatsapphandle='$whatsapphandle',town='$town',area='$area',passport_number='$passport_number',company_name='$company_name_case', company_registration='$company_registration',business_number='$business_number',messengerhandle='$messengerhandle',instagramhandle='$instagramhandle' where AccountNumber='$customerid'";
	         	mysqli_query($link, $update_customer);
				 /* If a case is created for a particlar meter number more than 10 times and closed (on same sub category), the case then should be escalated to the supervisor (sebetet@lec.co.ls) the next time it is created */
				//  function copied  and updated from lec droplet [vastvikta][25-04-2025]
				checkAndEscalate($customerid,$v_subcategory);

	      } // Update customer close
	      	
	      /*** Customer create and update after satrt ticket create code ***/ 
		   if ($customerid > 0) {
		        $ticketid = getticket(); // this function for get ticket id
		        // Insert code for ticket details
		        $sql = "insert into $db.web_problemdefination(vCustomerID, vCaseType, vCategory, vSubCategory, iCaseStatus, vProjectID, vRemarks, ticketid, d_createDate, d_updateTime, customer_feedback , i_source, i_CreatedBY, call_type, priority, last_case_id, organization,	language_id,perpetrator,affected,service,complaint_type,root_cause,corrective_measure,regional,customertype) values('$customerid',  '$type', '$v_category', '$v_subcategory','$status_type_','$group_assign', '" . $v_remark_type . "', '$ticketid', '$currentdate', '$currentdate', '$feedback', '$source', '$vuserid', '$call_type', '$priority', '', '$organisation','$lang','$perpetrator','$affected','$servicepro','$comp','$root_cause','$corrective_measure','$regional','$customertype')";
            	mysqli_query($link, $sql) or die("Error In Query2 " . mysqli_error($link));
            	$ticket = mysqli_insert_id($link);
				// added  ticket creation audit report [vastvikta][11-02-2025]
				add_audit_log($vuserid, 'create_case', $ticketid, 'Case created '.$ticketid, $db);
		        // If ticket is successfully created
		        if ($ticket > 0) {
		            $pdf_genrate = true;
		            $action_changes = 'Case created';
		            mysqli_query($link, "insert into $db.web_case_interaction (caseID, custmer_id, caller_id, remark, created_date, created_by, current_status, mode_of_interaction,action) values ('$ticketid','$customerid','$phone','$v_remark_type','$currentdate', '$logedin_agent', '$status_type_', '$source','$action_changes') ") or die("Error In Query27 " . mysqli_error($link));

					// insert records for caputuring department Assingnments for case :: farhan akhtar [18-04-2025]

					$dataArr =[
						'case_id'     => $ticketid,
						'department'  => $group_assign,
						'category'    => $v_category,
						'subcategory' => $v_subcategory,
						'case_status' => $status_type_,
						'comment'     => 'Initial case created.',
						'remark'      => $v_remark_type
					];
					if (!createNewCase($link, $dataArr)) {
						$errorMessage = "[" . date("Y-m-d H:i:s") . "] Failed to create new case for Case ID: $ticketid - Error: " . $link->error . PHP_EOL;
						file_put_contents("DeptlogEntryFailed.txt", $errorMessage, FILE_APPEND);
					}


		            // Update email, Twitter, voicemail, Facebook, SMS with case ID
		            if (isset($_POST['chatid']) && isset($_POST['mr'])) {
		               $chat_id = $_POST['chatid'];
		               $sql_update = "UPDATE overall_bot_chat_session SET caseid='$ticketid' WHERE chat_session='$chat_id'";
		               mysqli_query($link, $sql_update) or die("Error In overall_bot_chat_session" . mysqli_error($link));

		               $sql_update_chat = "UPDATE in_out_data SET caseid='$ticketid' WHERE chat_session_id='$chat_id'";
		               mysqli_query($link, $sql_update_chat) or die("Error In in_out_data" . mysqli_error($link));

		              //interaction data store in interaction table[Aarti][07-01-2025]
		              // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'webchat';
				    	interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);
				    
		            }
		            $email_type = '';
		            // update email with case ID
		            if ($_POST['emailid'] && isset($_POST['mr']) && $_POST['mr'] == '6') {
		               // status = '2'   means case created
		               $chat_id = $_POST['emailid'];
		               //  email_test='$orginal_subject'
		               $sql_update = "UPDATE $db.web_email_information SET ICASEID='$ticketid', i_Update_status='1' ,i_flag=1 , i_reminder=0 ,case_open_time='$todaydate' WHERE  EMAIL_ID =$chat_id  ";
		               mysqli_query($link, $sql_update) or die("Error In web_email_information " . mysqli_error($link));
		               $email_type = 'By email';

		               // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'email';
				    	interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);

		            }
		            // update Twitter with case ID
		            if (isset($_POST['twitterid']) && $_POST['mr'] == '3') {
		               // status = '2'   means case created
		               $chat_id = $_POST['twitterid'];
		               //  email_test='$orginal_subject'
		               $update_tweet = "UPDATE $db.tbl_tweet SET ICASEID='$ticketid', i_Update_status='1', i_reminder=0 ,case_open_time='$todaydate' WHERE  i_ID =$chat_id  ";
		               mysqli_query($link, $update_tweet) or die("Error In tbl_tweet " . mysqli_error($link));

		               // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'twitter';
				    	interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);
		            }
		            //update voicemail with case ID
		            if (isset($_POST['voicemailid']) && isset($_POST['mr']) && $_POST['mr'] == '10') {
		               // status = '2'   means case created
		               $voice_id = $_POST['voicemailid'];
		               //  email_test='$orginal_subject'
		               $update_voicemail = "UPDATE $db_asterisk.tbl_cc_voicemails SET case_id='$ticketid' WHERE id =$voice_id  ";
		               mysqli_query($link, $update_voicemail) or die("Error In tbl_cc_voicemails " . mysqli_error($link));

		                // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'voicecall';
				    	interaction_history_insert($ticketid,$voice_id,$intraction_type,$customerid);
		            }		            
		            //update facebook with case ID
		            $facebook_type  = '';
		            if (isset($_POST['facebookid']) && isset($_POST['mr']) && $_POST['mr'] == '4') {
		               // status = '2'   means case created
		               $facebook_id = $_POST['facebookid'];
		               //  email_test='$orginal_subject'
		               $update_facebook= "UPDATE $db.tbl_facebook SET ICASEID='$ticketid' WHERE id =$facebook_id  ";
		               mysqli_query($link, $update_facebook) or die("Error In tbl_facebook " . mysqli_error($link));
		               $facebook_type = 'By facebook';
		            }
		            //update sms with case ID
		            if (isset($_POST['smsid']) && isset($_POST['mr']) && $_POST['mr'] == '13') {
		               // status = '2'   means case created
		               $smsid = $_POST['smsid'];
		               //  email_test='$orginal_subject'
		               $update_sms= "UPDATE $db.tbl_smsmessagesin SET ICASEID='$ticketid', flag=1 WHERE i_id =$smsid  ";
		               mysqli_query($link, $update_sms) or die("Error In tbl_smsmessagesin " . mysqli_error($link));
		               // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'SMS';
				    	interaction_history_insert($ticketid,$smsid,$intraction_type,$customerid);
		            }
		             // update Twitter with case ID
		            if (isset($_REQUEST['whatsappid']) && isset($_POST['mr']) && $_POST['mr'] == '8') {
		               // status = '2'   means case created
		               $whatsappid = $_POST['whatsappid'];
		               //  email_test='$orginal_subject'
		               $update_tweet = "UPDATE $db.whatsapp_in_queue SET ICASEID='$ticketid' WHERE  id =$whatsappid  ";
		               mysqli_query($link, $update_tweet) or die("Error In whatsapp_in_queue " . mysqli_error($link));

		               // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'Whatsapp';
				    	interaction_history_insert($ticketid,$whatsappid,$intraction_type,$customerid);
		            }

					 //update sms with case ID
					 if (isset($_POST['messengerid']) && isset($_POST['mr']) && $_POST['mr'] == '14') {
						$messengerid = $_POST['messengerid'];		
						$update_messenger= "UPDATE $db.messenger_in_queue SET ICASEID='$ticketid', flag=1 WHERE id =$messengerid";
						mysqli_query($link, $update_messenger) or die("Error In messenger_in_queue " . mysqli_error($link));

						// for update customer id in sendfrom field messenger table
		               $update_tweet = "UPDATE $db.messenger_in_queue SET customer_id='$customerid' WHERE  id = '$messengerid'";
		               mysqli_query($link, $update_tweet) or die("Error In messenger_in_queue " . mysqli_error($link));

		               // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'messenger';
				    	interaction_history_insert($ticketid,$messengerid,$intraction_type,$customerid);
					 }
					  //update sms with case ID handling instagram [Aarti][19-11-2024]
					 if (isset($_POST['instagramid']) && isset($_POST['mr']) && $_POST['mr'] == '15') {
						$instagramid = $_POST['instagramid'];		
						$update_messenger= "UPDATE $db.instagram_in_queue SET ICASEID='$ticketid', flag=1 WHERE id =$instagramid";
						mysqli_query($link, $update_messenger) or die("Error In instagram_in_queue " . mysqli_error($link));

						// for update customer id in sendfrom field messenger table
		               $update_tweet = "UPDATE $db.instagram_in_queue SET customer_id='$customerid' WHERE  send_from =$instagramhandle  ";
		               mysqli_query($link, $update_tweet) or die("Error In instagram_in_queue " . mysqli_error($link));

		                // for any interactio coming with any channel when insert data in interactio table
			      	   // [Aarti][07-01-2025]
			      	    $intraction_type = 'instagram';
				    	interaction_history_insert($ticketid,$instagramid,$intraction_type,$customerid);
					 }
					
					// Check attachment and file store in folder and database - Whatsapp
					if(isset($_POST['whatsappid']) && isset($_POST['mr'])) { 
						// Query to fetch message and attachment based on whatsappid
						$whatsaquery = "SELECT message, attachment FROM $db.whatsapp_in_queue WHERE id='" . $_POST['whatsappid'] . "'";
						$query5 = mysqli_query($link, $whatsaquery);							
						if($query5) {
							$query5 = mysqli_fetch_array($query5);// Fetching message and attachments
							$vsubject = $query5['message'];
							$multi_attach = $query5['attachment'];// Processing each attachment
							$I_PP = $_POST['mr']; 
							$attach_base_path = $whatsapp_path;
							$types = $DocumentType_WhatsappId;
							$mediaId = $query5['id'];
							// for insert data document table
							process_attachments($multi_attach, $vsubject, $types, $attach_base_path, $ticketid,  $I_PP,$mediaId);
						} 
					}
					// Check attachment and file store in folder and database - Whatsapp
					if(isset($_POST['messengerid']) && isset($_POST['mr'])) { 
						// Query to fetch message and attachment based on whatsappid
						$whatsaquery = "SELECT message, attachment FROM $db.messenger_in_queue WHERE id='" . $_POST['messengerid'] . "'";
						$query5 = mysqli_query($link, $whatsaquery);							
						if($query5) {
							$query5 = mysqli_fetch_array($query5);// Fetching message and attachments
							$vsubject = $query5['message'];
							$multi_attach = $query5['attachment'];// Processing each attachment
							$I_PP = $_POST['mr']; 
							$attach_base_path = $messenger_path;
							$types = $DocumentType_MessengerId;
							$mediaId = $query5['id'];
							// for insert data document table
							process_attachments($multi_attach, $vsubject, $types, $attach_base_path, $ticketid,  $I_PP,$mediaId);
						} 
					}	
					// Chatbox attachment  
					if (isset($_POST['chatid']) && isset($_POST['mr'])) {
						$chat_id = $_POST['chatid'];
						$chatquery = "SELECT message,attachment FROM in_out_data WHERE attachment!='' and chat_session_id='" . $chat_id . "'";
						$query = mysqli_query($link, $chatquery);							
						if($query) {
							while ($query5 = mysqli_fetch_assoc($query)){
								$vsubject = $query5['message'];
								$multi_attach = $query5['attachment'];// Processing each attachment
								$I_PP = $_POST['mr']; 
								$attach_base_path = $chat_path;
								$mediaId = $query5['id'];
								$types = $DocumentType_ChatId;
								// for insert data document table
								process_attachments($multi_attach,$vsubject,$types,$attach_base_path,$ticketid,$I_PP,$mediaId);
							}
						} 
					}			
	            	// Check attachment and file store in folder
					
			      if(isset($_POST['emailid']) && isset($_POST['mr'])) {
			         $ssssss  = "select v_subject,v_body,V_rule from $db.web_email_information where EMAIL_ID='" . $_REQUEST['emailid'] . "'";
			         $query5 = mysqli_query($link, $ssssss);
			         $query5 = mysqli_fetch_array($query5);
			         $vsubject = $query5['v_subject'];
			         $multi_attach = explode(",", $query5['V_rule']);
			         $mediaId = $query5['EMAIL_ID'];
			         $types = '4';
			         foreach($multi_attach as $attach){ 
			            process_attachments($attach, $vsubject, $types, '', $ticketid,$I_PP,$mediaId);
			         }
			      }
					   
			      	   

					   // Function For Sending mail customer to inform case Status
					   TicketEmail_Send($type,$email,$status_type_,$ticketid,$fname,$phone_no,$v_category,$group_assign);
		            /* Admin Email Section Close */
		            $datajson['status'] = true;
		            $datajson['ticketid'] = $ticketid;
		            $datajson['message'] = 'successfully Case created!!';
		            echo json_encode($datajson);
		        } else {
		            $msg = "Due To Some Technical Issue Record Not Saved, Please try Again After Some Time.";
		            $final_action = "$name User Create New Case Fail  For Customer $fname IN $process_case_type";
		            // add_audit_log($vuserid, 'new_case_create_issue', $ticket, json_encode($_REQUEST), $db);
		        }
		      }
	   }
	   // For creating ticket PDF file
	   if($pdf_genrate == true && !empty($ticketid)){
	      include_once 'dom_pdf.php';  
	   }  
}
function process_attachments($attach, $vsubject, $type, $attach_base_path, $ticketid,  $I_PP,$mediaId) {
	global $db,$link;
    // foreach($multi_attach as $attach) { 
		$i = 0;
        if(!empty($attach)) {
        	if($type == '4'){ //for email
	        	if($attach=='../lead_doc/' || $attach=='' || f=='../uploaded/') {
	        	}else{ 
		          $i++;
		          if(strpos($attach,"imap/")!==false){ 
		             $attachpath = "../$attach"; 
		          }
		          $attachs = explode('/', $attach);
		          $attachpath = "../imap/$attach";
		       	}
		    }
            $attachs = explode('/', $attach); 

            // Insert into web_documents
            $I_DocumentType = $type;
            if($type == '5'){
            	$document_name = $attachs[2];
            }else{
            	$document_name = $attachs[1];
            }
            $uploadedFileStr = "$attach_base_path$attach";	        
            $V_DOC_Description = "$type: $vsubject";
            $I_UploadedBY = $_SESSION['logged'];
            $inset_sql = "INSERT INTO $db.web_documents 
                          (I_DocumentType, V_Doc_Name, v_uploadedFile, V_DOC_Description, I_PP, I_UploadedON, I_UploadedBY, I_Doc_Status, I_section_ID,mediaId) 
                          VALUES ('$I_DocumentType', '$document_name', '$uploadedFileStr', '$V_DOC_Description', '$I_PP', NOW(), '$I_UploadedBY', '1', '$ticketid','$mediaId')";
            mysqli_query($link, $inset_sql) or die(mysqli_error($link));
        }
    // }
}
//..!! Close Submit Section

/**
 * TicketEmail_Send Function
 *
 * This function sends emails and SMS notifications related to ticket status changes.
 *
 * @param string $type The type of ticket ('complaint' or 'Inquiry').
 * @param string $email The email address of the customer.
 * @param string $status_type_ The status type of the ticket.
 * @param string $ticketid The ID of the ticket.
 * @param string $fname The first name of the customer.
 * @param string $phone_no The phone number of the customer.
 * @param string $v_category The category of the ticket.
 * @param string $group_assign The group assignment of the ticket.
 * @return void
 */
 function TicketEmail_Send($type,$email,$status_type_,$ticketid,$fname,$phone_no,$v_category,$group_assign){
 	global $from_email,$db,$link,$SiteURL,$Pending_status,$Resolved_status,$Closed_status;
   /** For check email id and send mail **/ 
 
   if (!empty($email)) {
      if ($type == 'complaint' || $type == 'Inquiry') {

      	// Check if the type is 'complaint' or 'Inquiry'
         if ($type == 'complaint') {
         	// Determine the case type based on type and status
            if ($status_type_ == $Pending_status) {
               $case_type = 'com_new_case';
            } else if ($status_type_ == $Resolved_status) {
               $case_type = 'com_resolved';
            }else if ($status_type_ == $Closed_status) {
               $case_type = 'com_close_case';
            }
         } else if ($type == 'Inquiry') {
            if ($status_type_ == $Pending_status) {
               $case_type = 'inquiry_new_case';
            } else if ($status_type_ == $Resolved_status) {
               $case_type = 'inquiry_resolved';
            } else if ($status_type_ == $Closed_status) {
               $case_type = 'inquiry_close_case';
            }
         }
         // Get voice path
	      $voice_path= '';
		   if (isset($_REQUEST['voicemailid']) && isset($_GET['mr']) && $_GET['mr'] == '10') {
		      $voice_file = get_filename($_REQUEST['voicemailid']);
		      $newFileName = substr( $voice_file, 0 , (strrpos( $voice_file, ".")));
		      $voice_path = "../../../voicemail/IVR/DROP/$newFileName.WAV"; // need to be check path exists
		   }
         if(!empty($voice_path)){
            $path = $voice_path;		                       
         }else{
            $path ='';
         }
         // Generate email template
         $res = mail_template($ticketid, $case_type, $data = []);
         $subject = $res['sub'];
         $message = $res['msg'];
         $expiry = $res['expiry'];

         /*Aarti-23-11-23
         insert data in web_email_information_out table and 
         replce the insert code and add new function for insert code*/
         $data_email=array();
         $data_email['Mail'] = $email;
         $data_email['from']= $from_email ;
         $data_email['V_Subject']=$subject;
         $data_email['V_Content']=$message;
         $data_email['ICASEID']=$ticketid;
         $data_email['i_expiry']=$expiry;
         $data_email['V_rule']=$path;
         $data_email['view']='New Case Manual';
         // Insert data into web_email_information_out table
         insert_emailinformationout($data_email);
      }
   }
   // Check if phone number is not empty
   if(!empty($phone_no)){
   	// Determine SMS type based on status
	    if($status_type_ == $Pending_status){
	      $sms_type = 'new_case';
	    }else if($status_type_ == $Resolved_status){
	      $sms_type = 'resolved_case';
	    }else if($status_type_ == $Closed_status){
	      $sms_type = 'closed_case';
	    }
      // Generate SMS template
     $customer_name=ucwords($fname);
     $data_arr = ["name" => $customer_name];
     $res_sms = sms_template($ticketid,$sms_type,$data_arr);
     $message = $res_sms['msg'];
     $expiry = $res_sms['expiry'];
      /*insert data in tbl_smsmessages table*/
       $data_sms=array();
       $data_sms['v_mobileNo'] = $phone_no;
       $data_sms['v_smsString']= $message;
       $data_sms['V_Type']='Sms';
       $data_sms['V_AccountName']=$fname;
       $data_sms['V_CreatedBY']='';
       $data_sms['i_status']='1';
       $data_sms['i_expiry']=$expiry;  
       $data_sms['ICASEID']=$ticketid;
       // Insert data into tbl_smsmessages table              
       insert_smsmessages($data_sms);
       /*end - web_email_information_out*/
   }
   /**For if status id pending than send mail and sms related code**/ 
   // Send email and SMS if status is pending
    if ($status_type_ == $Pending_status) {
      if ($type == 'complaint') {
      	// Prepare data array for email template
         $data_arr = ['name' => $fname, 'mobile' => $phone_no, 'email' => $email, 'category' => category($v_category), 'sub_category' => department($group_assign)];
         $case_type = 'assign_new_case';

          // Generate email template
         $res = mail_template($ticketid, $case_type, $data_arr);
         $subject = $res['sub'];
         $admin_message = $res['msg'];
         $expiry = $res['expiry'];
         // Get department path
         $dep_path = BasePathStorage.'/pdf/'.$ticketid.'.pdf';

         // Fetch admin email from database
         $admin_query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $db.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$group_assign' ";
         $admin_res = mysqli_query($link, $admin_query);
         if (mysqli_num_rows($admin_res) > 0) {
            while ($adminrow = mysqli_fetch_assoc($admin_res)) {
               $email = $adminrow['V_EmailID'];
               /*Aarti-23-11-23
               insert data in web_email_information_out table and 
               replce the insert code and add new function for insert code*/
               $data_email= [];
               $data_email['Mail'] = $email;
               $data_email['from']= $from_email ;
               $data_email['V_Subject']=$subject;
               $data_email['V_Content']=$admin_message;
               $data_email['ICASEID']=$ticketid;
               $data_email['i_expiry']=$expiry;
               $data_email['V_rule']=$dep_path;
               $data_email['view']='New Case Manual';
               // Insert data into web_email_information_out table for admin email
               insert_emailinformationout($data_email);
               /*end - web_email_information_out*/
            }
         }
      }
  	}

  	// This code for send message whatsappp user [25-03-2025][Aarti]
	$getqury = "select * from $db.tbl_whatsapp_connection ";
	$getsqls = mysqli_query($link, $getqury);
	if (mysqli_num_rows($getsqls) > 0) {
		$rows = mysqli_fetch_assoc($getsqls);
		$account_phone_number = $rows['phone_number_id'];
	
		$type='new_case_creation';
		// Prepare message from template
		$customer_name = ucwords($fname);
		$data_arr = ["name" => $customer_name];
		
		$res = whatsapp_template($ticketid, $type, $data_arr);
		$message = $res['msg'];
	
		// Prepare data array
		$data_whatsapp = array(
			'send_to' => $phone_no,
			'send_from' => $account_phone_number,
			'message' => $message,
			'template_name' => $type,
			'ICASEID' => $ticketid,
			'customer_name' => $customer_name,
			'customerid' => $customerid
		);
	
		// Insert WhatsApp message
		insert_whatsapp_message($data_whatsapp);
	}
	
 } 
 // close
// added neccesary function to be used in updating callback time while adding interaction 
// this fucntion fetches the username according to the session logged user id [vastvikta][24-03-2025]
function get_agent_name($vuserid) {
    global $db, $link;
	// select the username on the basis of the userid 
    $sql = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID = '$vuserid'";
    $result = mysqli_query($link, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['AtxUserName'] ?? null;
    } else {
        return null; // Return null if query fails
    }
}
function get_user_data($customerid){
    global $db, $link;

    $sql = "SELECT * FROM $db.web_accounts WHERE AccountNumber = '$customerid'";

    $res = mysqli_query($link, $sql);

    if (!$res) {
        die("Error: " . mysqli_error($link)); // Handle SQL errors
    }

    if (mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res); // Return user data as an associative array
    } else {
        return null; // Return null if no data found
    }
}

// function to fetch or create lead id if created or not 
// function copied from var/lib/asterisk/agi-bin/add_to_callback.php

function createLeadID($callerid,$dnid,$customerid,$remark)
{
    global $db_asterisk,$link;
	$no=$callerid;
	$date=date("Y-m-d");
	$tdate=date("Y-m-d H:i:s");
	$vuserid        =  $_SESSION['userid'];
	$agent = get_agent_name($vuserid);
   
	if(strlen($no)>10)
	{
        $no=substr($no,-10);
	}

	$q = "Select * from $db_asterisk.autodial_list where phone_number like '%$no%' and not empty  order by modify_date desc limit 1";

	$res = mysqli_query($link,$q);

	$r12 = mysqli_fetch_array($res);

	$cnt12 = mysqli_num_rows($res);

    if($cnt12 >= 1)
    {
        $leadID = $r12['lead_id'];
		
    }
    else
    {
		$status = 'CALL BACK';
		$res = get_user_data($customerid);
		$q = "INSERT INTO $db_asterisk.autodial_list 
		(entry_date, modify_date, vendor_lead_code, phone_number, list_id, language, first_name, last_name, address1, address2, email, comments, customer_id, date_of_lead, priority_user,status,user) 
		VALUES (NOW(), NOW(), '$dnid-INCM-$no', '$no', '1000', '', '" . $res['fname'] . "', '" . $res['lname'] . "', '$no', '$no', '" . $res['email'] . "', '$remark', '$customerid', NOW(), '" . $res['priority'] . "','$status','$agent')";
		$query6 = mysqli_query($link,$q);
		$leadID = mysqli_insert_id($link);    
    }
    return $leadID;
}
// function to add callback details  in the autodial callbacks table  
function add_to_callback($callerid,$dnid,$cb,$customerid,$remark)
{
	global $db_asterisk,$link;
	$vuserid        =  $_SESSION['userid'];
	$agent = get_agent_name($vuserid);

    $lead_id = createLeadID($callerid,$dnid,$customerid,$remark);

    $list_id = '1000';
    $campaign = 'BLENDEDINBOUND';
    $status = 'ACTIVE';
    $recepient = 'ANYONE';
    $entry_time = date('Y-m-d H:i:s');
    $comment = 'CALL BACK';

    $stmt="INSERT INTO $db_asterisk.autodial_callbacks (`lead_id`,`list_id`,`campaign_id`,`status`,`entry_time`,`callback_time`,`recipient`,`comments`,`user`,`remark`) values('$lead_id','$list_id','$campaign','$status','$entry_time','$cb','$recepient','$comment','$agent','$remark');";
 
	$res = mysqli_query($link,$stmt);
    return $res;
}
/*	
Save Add More Interaction Docket No Wise   
*/
function interaction_remark(){
	global $from_email,$db,$link,$SiteURL,$groupid,$Pending_status,$Resolved_status,$Closed_status,$dbname,$db_asterisk;
	$vuserid        =  $_SESSION['userid'];
	$logedin_agent    =    $_SESSION['logged'];
	$agent = get_agent_name($vuserid);
	if(isset($_POST['interaction_remark'])){
		if( isset($_POST['docket_no']) && !empty($_POST['docket_no'])){
			$docket_no 			= $_POST['docket_no'] ;
			$source 			= $_POST['source_id'] ;
			$customerid 		= $_POST['customer_id'] ;
			
			$status_type_ 		= $_POST['inte_status_type_'] ;
			$remark 			= $link->real_escape_string($_POST['interaction_remark']);
			$c_mobile 			= $_POST['c_mobile'] ;
			$c_email 			= $_POST['c_email'] ;
			$fname 				= $_POST['c_full_name'] ;
			$bll_assign 		= $_POST['bll_assign'] ?? '' ;
			$type           	=$_POST['type'];
			$v_category           	=$_POST['v_category'];
			$time                 =  date("Y-m-d H:i:s");
			$cbdate               =  ($_POST['cb_date'] != '' && $_POST['callbk'] == 1) ? date("Y-m-d H:i:s", strtotime($_POST['cb_date'])) : "";
			$cb = date("Y-m-d H:i:s", strtotime($_REQUEST['cb_date']));
			$campaign = $_POST['campaignForPop'];
			// Modified by farhan on 27-06-2024
			$recording_file = $_POST['recording_file'];
			$vendor_lead_code = $_POST['vendor_lead_code'];
			$lead_id = $_POST['lead_id'];
			$lang = $_POST['lang'];
			$caller_id = $_POST['caller_id'];
			$group_assign = $_POST['group_assign'];
			$status='ACTIVE';
			$dnid = '';
			
			// added code for the callback [vastvikta] [21-03-2025]
			if (!empty($_POST['cb_date'])) {  
				
				$res2 = add_to_callback($caller_id, $dnid, $cb, $customerid, $remark);
				if (!$res2) {
					echo "MySQL Error: " . mysqli_error($link); // Debugging: Print MySQL error message
				} else {
					// echo "Insert successful!";
				}
			}

	
			
			/*Check For Allow Update or Not*/
			if(!empty($_POST['docket_no']) && $status_type_ != '3'){
				$query = "select iPID, ticketid, work_status, work_timestamp,current_working_agent FROM $db.web_problemdefination where ticketid='$docket_no'  " ;
				$res = mysqli_fetch_assoc(mysqli_query($link,$query));
				$id = $res['iPID'];
				$update_by = $res['current_working_agent'];
				$ticketid = $res['ticketid'];
				$work_status = $res['work_status'];
				$work_timestamp = $res['work_timestamp'];
				if( ($work_status == '1' && $update_by == "0") || ($work_status == '1' && $update_by == $vuserid)) {
					$sql_query = "update $db.web_problemdefination set work_status='0',  work_timestamp = NOW(), current_working_agent ='0' where IPID='$id' " ;
					mysqli_query($link,$sql_query);
					// added  update status in audit report [vastvikta][11-02-2025]
					add_audit_log($vuserid, 'update_status', $ticketid, 'Update working status', $db);
					//echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
				}
			}
			$status_name = ticketstatus($status_type_);
			if($_POST['status_old'] != $status_type_){
				$action_changes = "Status is Changed to $status_name";
			}else{
				$action_changes = "Remark is added For $status_name Case";
			}
			$status_rem = "Case with this ticked id -$docket_no  $action_changes";
			$update_string = ''; 
			if($groupid == '070000'):	// Agent   first call resolution
	 			$update_string = ", vRemarks ='$remark' " ; 
			elseif($groupid =='060000'):	// second call resolution
				$update_string = ", b5_remark ='$remark', iAssignTo='$bll_assign'  " ;
			elseif($groupid =='080000'):	// Supervisor
				$update_string = ", v_ActionSupervisor ='$remark' " ;
			elseif($groupid =='090000'):	// last call resolution
				$update_string = ", b6_remark ='$remark' " ; 
			elseif($groupid =='0000'): 	// Master Admin 
				$update_string = ", v_OverAllRemark ='$remark' " ; 
			endif; 

			$update_sql = "update $db.web_problemdefination set back_office_action_by='$vuserid',  iCaseStatus='$status_type_',adhoc_flag=0 $update_string where ticketid='$docket_no'" ;
			mysqli_query($link,$update_sql);

			// added  update status in audit report [vastvikta][11-02-2025]

			if ($status_type_ == '1'){   // pending
				$status_new = 'pending';
			}elseif ($status_type_ == '2'){   // drop
				$status_new = 'drop';
			}elseif ($status_type_ == '3'){   // close	
				$status_new = 'closed';
			}else if ($status_type_ == '4'){   // escalte 
				$status_new = 'escalate';
			}else if ($status_type_ == '5'){  // reopen
				$status_new = 'reopen';
			}else if ($status_type_ == '8'){   // resolved
				$status_new = 'in progress';
			}
			add_audit_log($vuserid, 'update_case_status', $docket_no, 'Updated remark on '.$status_new.' ticket no. '.$docket_no, $db);
					
			$sql = "insert into $db.web_case_interaction (caseID, custmer_id, mode_of_interaction, current_status, remark, created_date, created_by, recording_filename, vendor_lead_code, lead_id, language, caller_id, interacation_type,action) values ('$docket_no', '$customerid', '$source', '$status_type_', '$remark', NOW(), '$logedin_agent',  '$recording_file', '$vendor_lead_code', '$lead_id', '$lang', '$caller_id', 'Remark','$action_changes')" ;
			if(mysqli_query($link,$sql)){
				/* Send Email and SMS To Customer If Case Resolved */
				// case resolved
				if($status_type_ == $Resolved_status){	
					if(isset($_POST['c_email']) && !empty($c_email)){
							if ($type == 'complaint') {							   
								  $case_type = 'com_resolved';
							} else if ($type == 'Inquiry') {							    
								  $case_type = 'inquiry_resolved';
							}						
						$res = mail_template($docket_no, $case_type, $data=[]);
						$subject = $res['sub'];
						$message = $res['msg'];
						$expiry = $res['expiry'];
						/*Aarti-23-11-23
	                    insert data in web_email_information_out table and 
	                    replce the insert code and add new function for insert code*/
	                    $data_email=array();
	                    $data_email['Mail'] = $c_email;
	                    $data_email['from']= $from_email;
	                    $data_email['V_Subject']=$subject;
	                    $data_email['V_Content']=$message;
	                    $data_email['ICASEID']=$docket_no;
	                    $data_email['i_expiry']=$expiry;
	                    $data_email['view']='Case Updatedl';
	                    insert_emailinformationout($data_email);
	                    /*end - web_email_information_out*/
		                add_audit_log($vuserid, 'mail_sent', $docket_no, 'Mail Sent To Customer For Resolved Case to '.$c_email, $db);   
					}
					if(!empty($c_mobile)){
						$sms_type = 'resolved_case';
						$customer_name=ucwords($fname);
						$data_arr = array("name"=>$customer_name);
						$res_sms = sms_template($docket_no,$sms_type,$data_arr);
						$message = $res_sms['msg'];
						$expiry = $res_sms['expiry'];
						/*Aarti-23-11-23
		                insert data in tbl_smsmessages table*/
		                $data_sms=array();
		                $data_sms['ICASEID'] = $docket_no;
		                $data_sms['v_mobileNo'] = $c_mobile;
		                $data_sms['v_smsString']= $message;
		                $data_sms['V_Type']='Sms';
		                $data_sms['V_AccountName']=$fname;
		                $data_sms['V_CreatedBY']='';
		                $data_sms['i_status']='1';
		                $data_sms['i_expiry']=$expiry;                
		                insert_smsmessages($data_sms);
		                /*end - web_email_information_out*/
					 }
				
				}else if($status_type_ == $Closed_status){
						if ($type == 'complaint') {

						$case_type ='com_close_case' ;

						} else if ($type == 'Inquiry') {

						$case_type ='inquiry_close_case' ;
						}
						$res = mail_template($docket_no, $case_type, $data=[]);
						$subject = $res['sub'];
						$message = $res['msg'];
						$expiry = $res['expiry'];
						/*Aarti-23-11-23
	                    insert data in web_email_information_out table and 
	                    replce the insert code and add new function for insert code*/
	                    $data_email=array();
	                    $data_email['Mail'] = $c_email;
	                    $data_email['from']= $from_email;
	                    $data_email['V_Subject']=$subject;
	                    $data_email['V_Content']=$message;
	                    $data_email['ICASEID']=$docket_no;
	                    $data_email['i_expiry']=$expiry;
	                    $data_email['view']='Case Updatedl';
	                    insert_emailinformationout($data_email);
	                    /*end - web_email_information_out*/  
						if(!empty($c_mobile)){
							$sms_type = 'closed_case';
							$customer_name=ucwords($fname);
							$data_arr = array("name"=>$customer_name);
							$res_sms = sms_template($docket_no,$sms_type,$data_arr);
							$message = $res_sms['msg'];
							$expiry = $res_sms['expiry'];
			                /*insert data in tbl_smsmessages table*/
			                $data_sms=array();
			                $data_sms['ICASEID'] = $docket_no;
			                $data_sms['v_mobileNo'] = $c_mobile;
			                $data_sms['v_smsString']= $message;
			                $data_sms['V_Type']='Sms';
			                $data_sms['V_AccountName']=$fname;
			                $data_sms['V_CreatedBY']='';
			                $data_sms['i_status']='1';
			                $data_sms['i_expiry']=$expiry;                
			                insert_smsmessages($data_sms);
			                /*end - web_email_information_out*/
						}					

				}else if($status_type_ == $Pending_status && $_POST['status_old'] == $Closed_status){
					if(isset($_POST['c_email']) && !empty($c_email)){
						$case_type = 'com_reopen_case';											
						$res = mail_template($docket_no, $case_type, $data=[]);
						$subject = $res['sub'];
						$message = $res['msg'];
						$expiry = $res['expiry'];

						$data_email= [];
	                    $data_email['Mail'] = $c_email;
	                    $data_email['from']= $from_email;
	                    $data_email['V_Subject']=$subject;
	                    $data_email['V_Content']=$message;
	                    $data_email['ICASEID']=$docket_no;
	                    $data_email['i_expiry']=$expiry;
	                    $data_email['view']='Case Updated';
	                    insert_emailinformationout($data_email);
		                add_audit_log($vuserid, 'mail_sent', $docket_no, 'Mail Sent To Customer For Resolved Case to '.$c_email, $db);   
					}
					if(!empty($c_mobile)){
						$sms_type = 'reopen_case';
						$customer_name=ucwords($fname);
						$data_arr = array("name"=>$customer_name);
						$res_sms = sms_template($docket_no,$sms_type,$data_arr);
						$message = $res_sms['msg'];
						$expiry = $res_sms['expiry'];
						$data_sms= [];
		                $data_sms['ICASEID'] = $docket_no;
		                $data_sms['v_mobileNo'] = $c_mobile;
		                $data_sms['v_smsString']= $message;
		                $data_sms['V_Type']='Sms';
		                $data_sms['V_AccountName']=$fname;
		                $data_sms['V_CreatedBY']='';
		                $data_sms['i_status']='1';
		                $data_sms['i_expiry']=$expiry;                
		                insert_smsmessages($data_sms);
					}
					// this code for backoffice mail send
		            $data_arr = ['name' => $fname, 'mobile' => $c_mobile, 'email' => $c_email, 'category' => category($v_category), 'sub_category' => department($group_assign)];

		            $case_type ='com_reopen_case' ;
		            $res = mail_template($docket_no, $case_type, $data_arr);
		            $subject = $res['sub'];
		            $admin_message = $res['msg'];
		            $expiry = $res['expiry'];

		            // $ticketname = str_replace('/', '-', $ticketid);
		            $path = '../pdf/'.$docket_no.'.pdf';

		            $admin_query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $dbname.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$group_assign' " ;
		            $admin_res = mysqli_query($link,$admin_query) or 'ERROR '. mysqli_error($link);
		            if(mysqli_num_rows($admin_res) > 0){
		               while ($adminrow = mysqli_fetch_assoc($admin_res)) {
		                   $email = $adminrow['V_EmailID'] ; 
			                $data_email=array();
		                    $data_email['Mail'] = $email;
		                    $data_email['from']= $from_email;
		                    $data_email['V_Subject']=$subject;
		                    $data_email['V_Content']=$admin_message;
		                    $data_email['ICASEID']=$docket_no;
		                    $data_email['i_expiry']=$expiry;
		                    $data_email['view']='Case Updated';
		                    insert_emailinformationout($data_email);
		               }
		            }
				}
				/*-------------------------Send NPS Feedback link with MAIL and SMS-----------------*/
				if ($status_type_ == $Closed_status) {
					
					$module_flag_customer = module_license('NPS&CustomerEfforts'); // Check license availability
					
					include("../common_function.php");
					$common_function = new common_function();
					
					$email = $_POST['c_email'];
					$docket_no = trim($_POST['docket_no']);
					$caller_id = $_POST['c_mobile'];
					
					$npsfeedback_data = array();
					$npsfeedback_data['createdBy'] = 'Npsfeedback';
					$npsfeedback_data['customer_id'] = $customerid;
					$npsfeedback_data['customer_email'] = $email;
					$npsfeedback_data['ticket_id'] = $docket_no;
					$npsfeedback_data['phone_number'] = $caller_id;
					$npsfeedback_data['unique_id'] = $caller_id;
					$npsfeedback_data['feedback_value'] = '';
					$npsfeedback_data['media'] = 'MAIL';
					$npsfeedback_data['flag'] = '0';
				
					$first_name = $_POST['first_name'];
					$last_name = $_POST['last_name'];
					$fname = $first_name . " " . $last_name;
				
					// storing companay id from the session [vastvikta][04-02-2025]
					$companyID = $_SESSION['companyid'];
				
					$npsid = $common_function->addNpsFeddback($npsfeedback_data,$companyID);
			
				
					$cesid = $common_function->addCES($npsfeedback_data,$companyID);
			
				// updated code for type and company id [vastvikta][06-02-2025]
						$from=$from_email;
						$case_types = 'nps&customer_effort_feedback'; 
						$nps_ceslink_email = $SiteURL.'CRM/CES_NPS_feedback.php?npsid='.$npsid.'&cesid='.$cesid.'&company_id='.$companyID.'&Type=2'; // For email
						$data_array = array('nps_ceslink' => $nps_ceslink_email);

						$ress = mail_template($docket_no, $case_types, $data_array);	
						
						$expiry = $ress['expiry'];
						$subject_ces = $ress['sub'];
						$ces_message_mail = $ress['msg'];


						// added code for short url link for sms [vastvikta][26-02-2025]
						$nps_ceslink_sms = $SiteURL.'CRM/CES_NPS_feedback.php?npsid='.$npsid.'&cesid='.$cesid.'&company_id='.$companyID.'&Type=3'; // For SMS
						$short_url_nps_ceslink_sms = shortenUrl($nps_ceslink_sms);
						
						$data_array = array('nps_ceslink' => $short_url_nps_ceslink_sms);

						$res = sms_template($docket_no, $case_types, $data_array);
						$ces_message_text = $res['msg'];
						$expiry = $res['expiry'];

						/*Aarti-23-11-23
			            insert data in web_email_information_out table and 
		                replce the insert code and add new function for insert code*/
		                $data_email=array();
		                $data_email['Mail'] = $email;
		                $data_email['from']= $from;
		                $data_email['V_Subject']=$subject_ces;
		                $data_email['V_Content']=$ces_message_mail;
		                $data_email['ICASEID']=$docket_no;
		                $data_email['view']='New Case Call';
		                $data_email['expiry']=$expiry;	                
		                insert_emailinformationout($data_email);

		                $data_sms=array();
		                $data_sms['ICASEID'] = $docket_no;
		                $data_sms['v_mobileNo'] = $caller_id;
		                $data_sms['v_smsString']= $ces_message_text;
		                $data_sms['V_Type']='Sms';
		                $data_sms['V_AccountName']=$fname;
		                $data_sms['V_CreatedBY']='';
		                $data_sms['i_status']='1';
		                $data_sms['i_expiry']=$expiry;                
		                insert_smsmessages($data_sms);
		                /*end - web_email_information_out*/
		            // }
				}
				$interact_id="CALL-IE-".mysqli_insert_id($link);
				$_SESSION['ticket_arr'][] = $interact_id." ".$docket_no;
				echo json_encode(array('status' => 'success', "message" => "New remark added successfully", "session_val" => $_SESSION['ticket_arr'],"customerid" =>$customerid));die();
			}
			echo json_encode(array('status' => 'fail', "message" => "Remark not saved, please try after some time"));die();
		}
	}	
}

/**
* Ivr call start - Dispose Conditon START
**/
 /**************************START DISPOSTION****************************/
function Ajax_Dispose_Submit(){
	global $db,$link,$from_email,$db_asterisk,$base_path;
	extract($_POST);
	$agent				  =  $_POST['agent'];
	
	// farhan akhtar :: 31-01-2025 :: Dispose and Break Updates Break Flag and Current Timestamp in asterisk (autodial_live_agents) 

	// Check if the 'action' parameter is set and equals 'dispose & break'
	if (isset($_POST['actionBreak']) && $_POST['actionBreak'] == 'dispose & break') {
		// Prepare the update query
		$query = "UPDATE $db_asterisk.autodial_live_agents 
					SET request_for_break = 1, request_for_break_time = NOW() 
					WHERE user = ?";
		
		// Use prepared statements to prevent SQL injection
		$stmt = $link->prepare($query);
		$stmt->bind_param("s", $agent); // Assuming 'user' is a string (adjust type if necessary)

		// Execute the query
		if (!$stmt->execute()) {
			$check = false; // Query failed
			error_log("Query execution failed: " . $stmt->error); // Log the error for debugging
		}

		// Close the statement
		$stmt->close();
	}

	$myquery = "select live_agent_id,rec_filename from $db_asterisk.autodial_live_agents where user='$agent' and status in ('MWRAPUP','WRAPUP')";
	$myquery1 = mysqli_query($link, $myquery);
	$queryfilenamedata = mysqli_fetch_assoc($myquery1);
	if ($file == "") {
		$file = $queryfilenamedata['rec_filename'];
	}
	$logfile = $queryfilenamedata['rec_filename'];
	if (mysqli_num_rows($myquery1) > 0) {

		foreach ($_POST as $key => $input_arr) {
			$_POST[$key] = addslashes(trim($input_arr));
		}
		$disposition          =  $_POST['disposition'];
		$cbdate               =  ($_POST['cb_date'] != '' && $_POST['callbk'] == 1) ? date("Y-m-d H:i:s", strtotime($_POST['cb_date'])) : "";
		$time                 =  date("Y-m-d H:i:s");
		$dispo_type = "";
		$cb = date("Y-m-d H:i:s", strtotime($_REQUEST['cb_date']));
		$cbstr = strtotime($cb);
		$nowdatestr = strtotime(date("Y-m-d H:i:s"));
		$remark = $_POST['dispose_remark'];
		$remark = addslashes($remark);
		$subdisposition = $disposition;
		$lead_id 	=	$_GET['lead_id'];
		$customerid =	$_GET['customerid'];
		error_log(print_r('UPDATEDISPOSION IN RECORSING LOG 11111111111' . date('d-m-Y H:i:s'), true));
		error_log(print_r($_POST, true));
		if ($disposition == '') {
			$msgg = "Select Disposition!";
		} else if ($remark == '') {
			$msgg = "Enter Remarks!";
		} else {
			$ticket_arr	= $_SESSION['ticket_arr'];
			$caller_id = $_REQUEST['caller_id'];
			$ticket_arr = implode(", ", $ticket_arr);
			$customerid = $_REQUEST['customerid'];
			$lead_id = $_REQUEST['lead_id'];
			$docket_no = $_REQUEST['docket_no_new'];
			if ($lead_id != "" && $logfile != "") $file = $logfile;
			$log  = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . PHP_EOL . json_encode($_REQUEST) .
				"Vendor Lead Code: " . $vendor_lead_code . PHP_EOL . "Lead ID: " . $lead_id . PHP_EOL .
				"Customer ID: " . $customerid . PHP_EOL . "Caller Number: " . $caller_id . " Filename" . $file . "" . PHP_EOL .
				"-------------------------" . PHP_EOL;
				
			$insert_wrapcall = "INSERT INTO $db.web_wrapcall(customer_id,disposition,remarks,filename,entry_date,agent,ticketid,campaign,calling_number,lead_id) VALUES('$customerid', '$disposition', '$remark', '" . $file . "', '$time', '$agent', '$docket_no', '" . $campaignForPop . "', '" . $caller_id . "','" . $lead_id . "') ";
			mysqli_query($link, $insert_wrapcall) or die(mysqli_error($link) . ">Err while inserting web_wrapcall");
			$wrap_id = mysqli_insert_id($link);

			//  Update Vijay : 26-04-2021 to save wrapup in interaction also
			$sql_status = "SELECT iCaseStatus FROM $db.web_problemdefination WHERE ticketid ='$docket_no' ";
			$status_res = mysqli_query($link, $sql_status) or die(mysqli_error($link) . ">GET current Ststus");
			$status_rows = mysqli_fetch_assoc($status_res);
			$status_type_ = $status_rows['iCaseStatus'];
			// added action  value for the interaction view page during dispose of the call [vastvikta][07-02-2025]
			$action_changes = 'Dispose the call';
			mysqli_query($link, "insert into $db.web_case_interaction (caseID, custmer_id, remark, created_date, created_by, current_status, mode_of_interaction, caller_id, lead_id, vendor_lead_code, recording_filename,interacation_type,list_id,action) values ('$docket_no','$customerid','$remark','$time', '$agent', '$status_type_', '1','$caller_id', '$lead_id','$vendor_lead_code', '$file','in_out_dispose','$list_id','$action_changes') ") or die("Error In Query27 " . mysqli_error($link));

			$query_cust = "INSERT into $db_asterisk.autodial_customer_info (lead_id, vendor_lead_code, name, phone_number, alt_number, email, disposition, country, city, remark, address, description, entry_date, customer_id, cust_remarks, agent, attempts, source,  filename, v_subDispo, module, industry, organisation_name,pulp_cust_id) values( '$lead_id', '$vendor_lead_code', '$fname', '$caller_id', '$mobile', '$email',  '$disposition', '$country',  '$district', '$remark', '$address_1', '$remark', '$time' , '$customerid', '$remark', '$agent', '$attempts', '$source', '$file', '$subdisposition', '" . $campaignForPop . "', '$district', '$ticket_arr', '$customerid');";
			mysqli_query($link, $query_cust) or die(mysqli_error($link) . " < 3");
			$log .= "<br>WRAP CALL::" . $insert_wrapcall;
			$log .= "<br>Autodial cust::" . $query_cust;
			if ($lead_id) {
				 $query_auto = "update $db_asterisk.autodial_list set first_name='$fname',last_name='$lname', email='$email', alt_email='$alt_email', address1='$caller_id', alt_phone='$mobile',  city='$district', address3='$address_1', country_code='$country', comments='$remark', agent='$agent',  source='$source', address2='$caller_id', date_of_lead='$time', customer_id='$customerid', industry='$language', status='ensembler',priority_user='$priority_user' where lead_id='$lead_id'";
				mysqli_query($link, $query_auto) or die(mysqli_error($link) . "< 1");
				$log .= "<br>" .	$query_auto;
			}
			if ($file) {
				error_log(print_r('UPDATEDISPOSION IN RECORSING LOG ' . date('d-m-Y H:i:s'), true));
				$disposition          =  $_POST['disposition'];
				$query12 = "update $db_asterisk.recording_log set location='$disposition', remark ='$remark' where  filename = '$file' ";
				mysqli_query($link, $query12) or die(mysqli_error($link) . "< 12");
				error_log(print_r('UPDATEDISPOSION IN RECORSING LOG ' . $query12, true));
				$query15 = "select * from $db_asterisk.autodial_agent_log where filename='" . $file . "'";
				$q1 = mysqli_query($link, $query15) or die(mysqli_error($link) . "< query15");
				$num_q = mysqli_num_rows($q1);
				if ($num_q) {
					 $q1_new = "UPDATE $db_asterisk.autodial_agent_log SET remarks = '$remark', status='$disposition', lead_id='$lead_id',sentiment='$sentiment' where filename='" . $file . "'";
					mysqli_query($link, $q1_new) or die(mysqli_error($link) . ">4545");
					$log .= "<br>Agent  log " .	$q1_new;
					error_log(print_r("UPDATEAGENT LOG HERE ALOS  " . $q1_new, true));
				} else {
					$log .= "<br>No file ENTRY Agent  log " . $query15;
					error_log(print_r("UPDATEAGENT LOG HERE ALOS  NOT UPFATE" . $q1_new, true));
				}
				$log .= "<br>Recording log " .	$query12;
			}
			/********************CALL BACK***************************************/
			if ($_POST['callbk'] != "1") {
				$cb = "";
				 $query_callback = "UPDATE $db.web_wrapcall set callbk_lead_id='',callback_date='' WHERE id='$wrap_id'; ";
				mysqli_query($link, $query_callback) or die(mysqli_error($link) . "Err while updating web_wrapcall");
				$log .= "<br>" . $query_callback;
			} else {
				$query_callback1 = "UPDATE $db.web_wrapcall set callbk_lead_id='$lead_id',callback_date='$time'  WHERE id='$wrap_id'; ";
				mysqli_query($link, $query_callback1) or die(mysqli_error($link) . "4785");
				$dispo_type = "CALLBK";
				$time = $cb;
				$log .= "<br>" .	$query_callback1;
			}
			/********************CALL BACK***************************************/
			/********************DNC***************************************/
			if ($disposition == "DNC") {
				$query44 = mysqli_query($link, "select * FROM $db_asterisk.autodial_dnc where phone_number='$caller_id'") or die(mysqli_error($link) . "dnc error");
				if (mysqli_num_rows($query44) > 0) {
				} else {
					mysqli_query($link, "INSERT INTO $db_asterisk.autodial_dnc(phone_number) VALUES ('$caller_id')") or die(mysqli_error($link) . "dnc error1") or die(mysqli_error($link) . ">34343dnc");
				}
				$log .= "<br>INSERT INTO $db_asterisk.autodial_dnc(phone_number) VALUES ('$caller_id')";
			}
			file_put_contents('/var/www/html/log/' . $agent . '_popuplog_' . date("j.n.Y") . '.txt', $log, FILE_APPEND);
			/********************DNC***************************************/
			$module_flag_customer = module_license('Feedback'); // check linces avaiable or not
			// if($module_flag_customer == '1' ){
				if(isset($_POST['email']) && !empty($_POST['email'])){
					include("../IMApp/function.php");
					$feedback_data = array();
					$feedback_data['createdBy'] = 'feedback';
					// by default blank , code updated below 
					$feedback_data['Type'] = '';
					$feedback_data['Call_id'] = $caller_id;
					$feedback_data['Ticket_id'] = $docket_no;
					$feedback_data['Phone_Number'] = $caller_id;
					$feedback_data['AgentID/Name'] = $agent;
					$feedback_data['Extension_Number'] = '';
					$feedback_data['customer_email'] = $_POST['email'];
					$feedback_data['customer_name'] = $_POST['customerid'];
					$feedback_object = new ChatRooms;
					$feedback_link = $feedback_object->create_feedbacklink($feedback_data);
					$case_type = 'feedback';
					// storing companay id from the session [vastvikta][06-02-2025]
					$companyID = $_SESSION['companyid'];
					
					// Set Type for Email  = 2 [vastvikta][02-12-2024] added company id [03-02-2025][vastvikta]
					$data['feedback_link'] = $feedback_link . "&Type=2&company_id=" . $companyID;
					// $data['feedback_link'] = $feedback_link;

					$res = mail_template($docket_no, $case_type, $data);	
					$subject = $res['sub'];
					$message = $res['msg'];

					
					// Set Type for SMS = 3 [vastvikta][02-12-2024] 
					// added code for short url link for sms [vastvikta][26-02-2025]
					$LongUrl = $feedback_link ."&Type=3&company_id=" . $companyID;

					$short_url_feedback_link = shortenUrl($LongUrl);
						
					$data['feedback_link'] = $short_url_feedback_link;

					$res = sms_template($docket_no, $case_type, $data);
					$message_text = $res['msg'];
					
					/*Aarti-23-11-23
			         insert data in web_email_information_out table and 
			        replce the insert code and add new function for insert code*/
					$data_email=array();
					$data_email['Mail'] = $email;
					$data_email['from']= $from_email;
					$data_email['V_Subject']=$subject;
					$data_email['V_Content']=$message;
					$data_email['ICASEID']=$docket_no;
					insert_emailinformationout($data_email);
					/*Aarti-23-11-23
	                insert data in tbl_smsmessages table*/
	                $data_sms= [];
	                $data_sms['ICASEID'] = $docket_no;
	                $data_sms['v_mobileNo'] = $caller_id;
	                $data_sms['v_smsString']= $message_text;
	                $data_sms['V_Type']='Sms';
	                $data_sms['V_AccountName']=$fname;
	                $data_sms['V_CreatedBY']='';
	                $data_sms['i_status']='1';               
	                insert_smsmessages($data_sms);
	               
			}
			$serverip = $_SERVER['SERVER_NAME'];

			// Store it to session to show again in the form :: farhan akhtar (14 april 25)
			$_SESSION['remark_dispo'] = $remark;

			// change ip to domain name [Aarti][160-01-2025]
			$url = $base_path."agc/hello_demo.php?DIS=$disposition&TIME=$time&DISPO_TYPE=$dispo_type&Agent=$agent&call_mode=<?=$call_mode?>&break_reason=<?=$break_reason?>";
			
			$check = true;
			//End
    		$message = "Successfully Disposed";
    		
			$_SESSION['ticket_arr'] = "";
			$_SESSION['c2c_url']="";
			$_SESSION['problem_defID']="";
		}
	} else {
		$check = false;
        $message = "You can not dispose the call during INCALL status.";
	}
	if($check == true){
    	echo json_encode(["status"=>"success","message"=>$message,"url"=>$url]);exit();
	}else{
    	echo json_encode(["status"=>"failed","message"=>$message]);exit();
	}
}
/******************END OF DISPOSE CONDITION*******************/
// getting interaction channel data[Aarti][07-01-2025]
function get_interaction_data(){
	global $db,$link;
	$phone = $_POST['phone'];
	$equery = "select AccountNumber,email,phone from $db.web_accounts where phone = '$phone';";
    $res11 = mysqli_query($link, $equery);
    $rowdat =  mysqli_fetch_assoc($res11);

	$phone = $rowdat['phone'];
	$customerid = $rowdat['AccountNumber'];
	$email = $rowdat['email'];
	$qdk = "select * from $db.interaction where (customer_id='".$customerid."' || email='".$email."' || mobile='".$phone. "' ) AND `remarks` != '' order by created_date desc";

	$ticket_query = mysqli_query($link,$qdk);
	$num = mysqli_num_rows($ticket_query);
	$html= '';
	// added code for uploaded by [vastvikta][05-05-2025]
	$html .= '<tr  class="background"><td width="8%" align="center"><div align="left"><b>Channel Type</b></div></td><td width="12%" align="center"><div align="left"><b>From</b></div></td><td width="17%" align="center"><div align="left"><b>Subject</b></div></td><td width="8%" align="center"><div align="left"><b>Created By</b></div></td><td width="11%" align="center"><div align="left"><b>DateTime</b></div></td></tr>';
	// updated interaction code for displaying the icon[vastvikta][21-04-2025]
	while ($row = mysqli_fetch_array($ticket_query)) {
		$html .= '<tr>
         	<td  class="normaltextabhi">';
			 if($row['intraction_type'] == 'email'){
				$html .= ' <svg  viewBox="0 0 48 48" width="20px" xmlns="http://www.w3.org/2000/svg"><path d="M45,16.2l-5,2.75l-5,4.75L35,40h7c1.657,0,3-1.343,3-3V16.2z" fill="#4caf50"/><path d="M3,16.2l3.614,1.71L13,23.7V40H6c-1.657,0-3-1.343-3-3V16.2z" fill="#1e88e5"/><polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"/><path d="M3,12.298V16.2l10,7.5V11.2L9.876,8.859C9.132,8.301,8.228,8,7.298,8h0C4.924,8,3,9.924,3,12.298z" fill="#c62828"/><path d="M45,12.298V16.2l-10,7.5V11.2l3.124-2.341C38.868,8.301,39.772,8,40.702,8h0 C43.076,8,45,9.924,45,12.298z" fill="#fbc02d"/></svg>';
			 }else if ($row['intraction_type'] == 'SMS') {
				 $html .= '<img src="../public/images/chat.png" width="20" border="0" title="Reply">';
			 } else if ($row['intraction_type'] == 'Whatsapp') {
				 $html .= '<img src="../public/images/whatsapp.png" width="20" border="0" title="Reply">';
			 } else if ($row['intraction_type'] == 'instagram') {
				 $html .= '<img src="../public/images/insta.png" width="20" border="0" title="Reply">';
			 } else if ($row['intraction_type'] == 'messenger') {
				 $html .= '<img src="../public/images/messenger_send.png" width="20" border="0" title="Reply">';
			 }
			 else if($row['intraction_type'] == 'webchat'){
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM128 208a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm128 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm96 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>';
			 }else if($row['intraction_type'] == 'voicecall'){
				 $html .= '<svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';
			 }
			 $type = $row['type'];
			 if($type == 'IN'){
				 $html .= ' <img src="../public/images/reply.png" width="14" border="0" title="Reply">';
			 }else{
				 $html .= '<img src="../public/images/newemail.png" width="15" border="0" title="Forward">';
			 }
				
         $html .= '</td>';
         $html .= '<td class="normaltextabhi">';
		            if($row['intraction_type'] == 'email'){
		               $html .= $row['email'];
		            }else{
		               $html .= $row['mobile'];
		            }
         $html .= '</td>';
         $html .= '<td  class="normaltextabhi">';

         $ID = $row['interact_id'];

        if ($row['intraction_type'] == 'email') {
		    $html .= "<a href='javascript:void(0)' onClick=\"JavaScript:window.open('helpdesk/web_email_dess.php?id=$ID&iid=$ID&type=out','_blank','height=550,width=900,scrollbars=0')\" class='cptext'>";
		    $html .= $row['remarks'];
		    $html .= "</a>";
		} else if ($row['intraction_type'] == 'Whatsapp') {
			$sql = "SELECT * FROM $db.whatsapp_in_queue where id='$ID'";
			$totalQuery = mysqli_query($link, $sql);
			$rowwhats = mysqli_fetch_assoc($totalQuery);

			$i_WhatsAppID= $rowwhats['id'];
			$send_from= $rowwhats['send_from'];
			$send_to= $rowwhats['send_to'];
			$messageid = $rowwhats['id'];

			$html .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_whatsapp.php?i_WhatsAppID='.$i_WhatsAppID.'&amp;send_to='.$send_from.'&amp;id=&amp;send_from='.$send_to.'&amp;messageid='.$i_WhatsAppID.'&amp;account_sender_id='.$send_to.'" class="ico-interaction2">';
			$html .= $row['remarks'];
			$html .= '</a>';

		} else if ($row['intraction_type'] == 'instagram') {
			$sql = "SELECT * FROM $db.instagram_in_queue where id='$ID'";
			$totalQuery = mysqli_query($link, $sql);
			$rowwhats = mysqli_fetch_assoc($totalQuery);

			$send_from= $rowwhats['send_from'];
			$send_to= $rowwhats['send_to'];
			$messageid = $rowwhats['id'];

			$html .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_instagram.php?ID='.$messageid.'&amp;send_to='.$send_from.'&amp;id=&amp;send_from='.$send_to.'&amp;messageid='.$messageid.'&amp;account_sender_id='.$send_to.'" class="ico-interaction2">';
			$html .= $row['remarks'];
			$html .= '</a>';

		} else if ($row['intraction_type'] == 'messenger') {
			$sql = "SELECT * FROM $db.messenger_in_queue where id='$ID'";
			$totalQuery = mysqli_query($link, $sql);
			$rowwhats = mysqli_fetch_assoc($totalQuery);

			$send_from= $rowwhats['send_from'];
			$send_to= $rowwhats['send_to'];
			$messageid = $rowwhats['id'];

			$html .= '<a style="text-decoration: none;" href="omnichannel_config/web_sent_messanger.php?ID='.$messageid.'&amp;send_to='.$send_from.'&amp;id=&amp;send_from='.$send_to.'&amp;messageid='.$i_WhatsAppID.'&amp;account_sender_id='.$send_to.'" class="ico-interaction2">';
			$html .= $row['remarks'];
			$html .= '</a>';

		} else if ($row['intraction_type'] == 'webchat') {
			$mobile= $row['mobile'];
		    $html .= '<a class="ico-interaction2" href="omnichannel_config/chat_history.php?phone='.$mobile.'&caseid=&session_id=">'.$row['remarks'].'</a>';
		}else {
			$html .= strip_tags($row['remarks']);
		}
		$agentname = get_agent_name($row['created_by']);
         $html .= '</td>';
		 $html .= '<td class="normaltextabhi">';
         $html .= $agentname;
         $html .= '</td>';
         $html .= '<td class="normaltextabhi">';
         $html .= date("d-m-Y H:i:s", strtotime($row['created_date']));
         $html .= '</td></tr>';
	}
	if ($num <= 0) { 
      $html .= '<tr><td colspan="8" align="center">No record found !!</td></tr>';
    }
    echo $html;
}

/* ASK API Function for Knowledge Base AI Assistant :: Farhan Akhtar [01-02-2025] */
function askAPI($askAPI_url, $category, $top_k, $question) {
    // Prepare the data to be sent in the POST request
    $post_data = [
        'category' => $category,
        'query' => $question,
		'top_k' => $top_k
    ];

    // Initialize cURL
    $ch = curl_init($askAPI_url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_POST, true); // Use POST method
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)); // Send the data as JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Set the content type to JSON
    ]);

    // Execute the cURL request and store the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $response_data = json_decode($response, true);

    // Check if the response is valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error decoding JSON response from the API.');
    }

    // Return the decoded response
    return $response_data;
}
// added code for short url link for sms [vastvikta][26-02-2025]
function shortenUrl($longUrl) {
    $apiUrl = "https://tinyurl.com/api-create.php?url=" . urlencode($longUrl);
    $shortUrl = file_get_contents($apiUrl);
    return $shortUrl;
}

function update_open_time() {
    global $db, $link;

    $email = mysqli_real_escape_string($link, $_POST['emailid']);

    $sql_update = "UPDATE {$db}.web_email_information 
                   SET case_open_time = NOW() 
                   WHERE EMAIL_ID = '$email'";

    if (mysqli_query($link, $sql_update)) {
        echo 'Success';
    } else {
        http_response_code(500);
        echo "Error: " . mysqli_error($link);
    }
}
/* END */