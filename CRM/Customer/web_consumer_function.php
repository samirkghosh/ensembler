<?php
/**
 * Customer function Page
 * Author: Ritu modi
 * Date: 19-02-2024
 * This PHP page is used for managing customer-related functionalities. It includes functions for viewing customer lists, fetching customer data, retrieving case information for a customer, managing cities and villages, handling gender-related information, and updating customer account data.
 */
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// Include necessary functions
include("../web_function.php");

// Add code for logging code
include_once("../../logs/config.php");
include_once("../../logs/logs.php");

if($_POST['action'] == 'display_subcounty'){
  display_subcounty(); // Call function to display subcounty
}


// Check if the action is to update a case
if($_POST['action'] == 'upadate_data'){
	upadate_data();
}

function view_customer_list(){
	global $db, $link;
    $search = trim($_POST['search']);
    $string = '';
    if($search != '') {
    	$string = " and (fname like '%".$search."%' || phone like '%".$search."%' || email like '%".$search."%'||twitterhandle like '%".$search."%'||fbhandle like '%".$search."%' )";
    }
    $query = "select * from $db.web_accounts where AccountNumber != '' $string order by priority_user='1' desc, AccountNumber desc"; 
	$no = 0;
	$customer_query = mysqli_query($link, $query);
	return $customer_query;
}
// Function to fetch customer data based on customer ID
function fetch_customer_data($customerid){
	global $db, $link;
	$query="select * from $db.web_accounts where AccountNumber='$customerid' ; ";
	//echo $query; die;
	$result = mysqli_query($link,$query);
	if (!$result) {
        die("Error: " . mysqli_error($link));
    }
	return $result;
}
// Function to fetch case information for a customer
function view_case_info($customerid){
	global $db, $link;
	$query = "select * from $db.web_problemdefination where vCustomerID='$customerid' order by d_createDate desc; ";
	$ticket_query = mysqli_query($link, $query);
	return $ticket_query;
}
// Function to retrieve list of cities
function city_list(){
	global $db,$link;
	$city_query = mysqli_query($link, "select id,city from $db.web_city where status='1' ");
	return $city_query;
}
// Function to get villages based on district
function get_village($district){
  global $db,$link;
    // Prepare the SQL query
    $sql = "SELECT id, vVillage FROM $db.web_Village WHERE iDistrictID='$district' AND status='1' ORDER BY vVillage ASC";
    // Execute the query
    $villages_query = mysqli_query($link, $sql);    
    // Check if the query was successfully
    if (!$villages_query) {
        // Query failed, handle the error (this is just a basic example)
        die('Error: ' . mysqli_error($link));
    }
    // Return the result set
    return $villages_query;

}
// Function to get villages based on district
function get_village_id(){
	global $db,$link;
	  // Prepare the SQL query
	  $sql = "SELECT id, vVillage FROM $db.web_Village WHERE status='1' ORDER BY vVillage ASC";
	  // Execute the query
	  $villages_query = mysqli_query($link, $sql);    
	  // Check if the query was successfully
	  if (!$villages_query) {
		  // Query failed, handle the error (this is just a basic example)
		  die('Error: ' . mysqli_error($link));
	  }
	  // Return the result set
	  return $villages_query;
  
  }

function display_subcounty(){
  global $link,$db;
  $district = $_POST['dis_id'];
  $village = $_POST['vill_id'];
  if(isset($_POST['dis_id'])){
    ?>
    <select name="village" id="village" class="select-styl1" style="width:190px;">
    <option value="">Select Sub County</option>
    <?php
    $villages_query = mysqli_query($link,"select * from $db.`web_Village` where `iDistrictID`='$district' AND `status` =1 ORDER BY `vVillage` ASC ");
    while($villages_res = mysqli_fetch_array($villages_query)){?>
    <option value="<?=$villages_res['id']?>" <?php if($villages_res['id']==$village){ echo "selected"; } ?>>
      <?=$villages_res['vVillage']?>
    </option>
      <?php } ?>
    </select>
    <?php
  }
}
/******For showing gender list ********/
function web_gender(){
	global $db,$link;
	$gender_query = mysqli_query($link, "select * from $db.web_gender");
	return $gender_query;
}
/******For showing gender list ********/
function regional_list(){
	global $db,$link;
	$tbl_regional = mysqli_query($link, "select * from $db.tbl_regional");
	return $tbl_regional;
}
/******For showing customertype list ********/
function customertype_list(){
	global $db,$link;
	$tbl_customertype = mysqli_query($link, "select * from $db.tbl_customertype");
	return $tbl_customertype;
}
/******For showing web_source list ********/
function source_list(){
 	global $db,$link;
	$source=mysqli_query($link,"select id,source from $db.web_source");
	return $source;
}

/******For showing complaint_type list ********/
function complaint_type(){
	global $db,$link;
	$complaint_sql = mysqli_query($link, "select id,complaint_name, slug , status from $db.complaint_type where status =1 ");
	return $complaint_sql;
}
/******For showing web_category_list list ********/
function web_category_list(){
	global $db,$link;
	$cat_query = mysqli_query($link, "select id,category from $db.web_category where status = 1 ORDER BY category ASC ");
	return $cat_query;
}
/******For showing web_subcategory_list list ********/
function web_subcategory_list($catid){
	global $db,$link;
	$subcat_query = mysqli_query($link, "select * from $db.web_subcategory where category='$catid' AND status =1 ");
	return $subcat_query;
}
// Function to get documents based on provided ID and group ID
function get_documents($id,$groupid){
	global $db,$link;
	if($groupid=='080000'){
         $sqlopp="SELECT * FROM $db.web_documents WHERE I_DocumentType = '4' AND I_section_ID = '".$id."' AND I_Doc_Status = '1' ORDER BY I_UploadedON DESC";
    }else{
        $sqlopp="SELECT * FROM $db.web_documents WHERE I_DocumentType = '4' AND I_section_ID = '".$id."' AND I_Doc_Status = '1' and (I_PP=0 || I_UploadedBy='".$_SESSION['logged']."') ORDER BY I_UploadedON DESC";
    }
    $resopp=mysqli_query($link,$sqlopp);
    return $resopp;
}

// Function to update account data
function upadate_data(){
	global $db, $link;
	$vuserid = $_SESSION['userid'];
// Retrieve data from POST request
	$customerid = $_POST['customerid'];
	$phone = $_POST['phone'];
	$mobile = $_POST['mobile'];
	$email = $_POST['email'];
	$gender = $_POST['gender'];
	$address = $_POST['address_1'];
	$v_Location = $_POST['address_2'];
	$district = $_POST['district'];
	$v_Village = $_POST['village'];
	$fbhandle = $_POST['fbhandle'];
	$priority = $_POST['priority'];
	$twitterhandle = $_POST['twitterhandle'];
	$whatsapphandle = $_POST['whatsapphandle'];
	$instagramhandle = $_POST['instagramhandle'];
	$messengerhandle = $_POST['messengerhandle'];
	$sms_number = $_POST['smshandle'];
	$nationality = $_POST['nationality'];
	$company_name = $_POST['companyname'];
	$company_registration = $_POST['company_registration'];
	$regional = $_POST['regional'];
   	$customertype= $_POST['customertype'];
	$firstname = $_POST['first_name'];
	$fname = $_POST['first_name'] .' '. $_POST['last_name'];
	// SQL query to update account data

	// updated the update  query as it was not updating the priority of the customer and update  date time [vastvikta][03-04-2025]
	if(!empty($customerid)){
		
		$query ="UPDATE $db.web_accounts SET fname='$fname',priority_user = '$priority', mobile='$mobile', phone= '$phone', address='$address', v_Location='$v_Location', district='$district', v_Village='$v_Village', email='$email', fbhandle='$fbhandle', twitterhandle='$twitterhandle', gender='$gender', customertype='$customertype',  company_registration='$company_registration', smshandle='$sms_number', whatsapphandle='$whatsapphandle', instagramhandle='$instagramhandle' ,messengerhandle = '$messengerhandle' , regional = '$regional', nationality='$nationality', updatedate = NOW() ,company_name='$company_name' WHERE AccountNumber='$customerid'";
		add_audit_log($vuserid, 'customer_updated', '', 'Customer detail updated: ' . $fname, $db,'');

	}else{
		// added insert query in case when new customer is being added [vastvikta ][03-04-2025]
		$query = "INSERT INTO `$db`.`web_accounts` 
		(`fname`, `mobile`, `phone`, `address`, `v_Location`, `district`, `v_Village`, 
		`email`, `fbhandle`, `twitterhandle`, `gender`, `customertype`, `company_registration`, 
		`smshandle`, `whatsapphandle`, `instagramhandle`, `messengerhandle`, `regional`, 
		`nationality`, `company_name`, `priority_user`,`v_passwd`) 
	VALUES 
		('$fname', '$mobile', '$phone', '$address', '$v_Location', '$district', '$v_Village', 
		'$email', '$fbhandle', '$twitterhandle', '$gender', '$customertype', '$company_registration', 
		'$sms_number', '$whatsapphandle', '$instagramhandle', '$messengerhandle', '$regional', 
		'$nationality', '$company_name', '$priority','$firstname')";
add_audit_log($vuserid, 'customer_insert', '', 'New Customer added: ' . $fname, $db,'');

	}
	$result = mysqli_query($link, $query);
    // Check if the query was successful
    if (!$result){
			if (__DBGLOG__){
		      		DbgLog(_LOG_ERROR,__LINE__, __FILE__,"DB insert error web_accounts: $query". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "web_accounts Database error";
			echo json_encode($response);
			exit();
		}
 }
// for audio file format
function SmartFileName_voice($SmartFileName){    
       $filename=$SmartFileName;
       $filename=substr($filename, 0, 8);//12Jul2019
       $year=substr($filename, 0, 4);
       $day=substr($filename, 6, 2);
       $m=$year.substr($filename, 4, 2).$day;
       $month1=date('M',strtotime($m));
       $folderpath=$day.$month1.$year."/";
        $path='../../calls/'.$folderpath.$SmartFileName.'.wav';
        $pathWithoutExtention='../../calls/'.$folderpath.$SmartFileName;
       if (file_exists($path)) {
          $recFile= $SmartFileName.".wav";
          $pathWithoutExtention=$pathWithoutExtention.".wav";
       }else{
          $pathWithoutExtention=$pathWithoutExtention.".WAV";
          $recFile= $SmartFileName.".WAV";
       }
       $recFile = str_replace('/','_', $recFile);
       $destFile = "../../tmp/".$recFile;
       $cmd = "/usr/bin/sox $pathWithoutExtention -b 8 $destFile";
       //now convert
       system($cmd);
		return $destFile;
   	}
	// function to return session id on the basis of the chatwith the user [vastvikta][15-04-2025]
	   function get_sessionid($interactid) {
		global $db, $link;
	
		// Prevent SQL injection by using prepared statements (if using mysqli or PDO, preferred)
		$sql = "SELECT chat_session_id FROM $db.in_out_data WHERE id = '$interactid'";
		$result = mysqli_query($link, $sql);
	
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			return $row['chat_session_id'];
		} else {
			return null; // or false, depending on how you want to handle no result
		}
	}
	
?>