<?php
/* This file used for bulk sms and email related function-aarti*/
include("../../config/web_mysqlconnect.php");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
class BULK{
	function __construct(){
		global $link,$db;
		$this->customer_array = [] ;
		$this->bad_customer_array = [] ;
		// $this->load->library('excel');
		if(isset($_POST['action']) || isset($_GET['action'])){
			if($_POST['action'] == 'import_file'){
				$this->import_file();
			}
			if($_POST['action'] == 'send_sms_index'){
				$this->send_sms_index();
			}
			if($_GET['action'] == 'get_contact_list'){
				$this->get_contact_list();
			}
			if($_POST['action']== 'get_template_content'){
				$this->get_template_content();
			}
			if($_GET['action'] == 'ajax_campaign_contact_count'){
				$this->ajax_campaign_contact_count();
			}
			// By Farhan Akhtar on 13-02-2025
			if($_GET['action'] == 'GET-EMAIL-LIST'){
				$this->export_bulk_emails($_GET['startDate'], $_GET['endDate']);
			}
		} 	
	}
	/* 
		* AUTHOR :: FARHAN AKHTAR
		* LAST MODIFIED ON :: 13-02-2025
		* PURPOSE :: TO IMPLEMENT FUNCTIONALLITY FOR GENERATE LIST (EXCEL SHEET) OF EMAILS (DATE WISE). 
	*/

	public function export_bulk_emails($startDate, $endDate) {
		global $db, $link;
	
		// Convert dates to MySQL format (yyyy-mm-dd)
		$startFormatted = DateTime::createFromFormat('d-m-Y H:i:s', $startDate)->format('Y-m-d H:i:s');
		$endFormatted = DateTime::createFromFormat('d-m-Y H:i:s', $endDate)->format('Y-m-d H:i:s');		
	
		// SQL Query to fetch data within the date range
		$sql = "SELECT 
					wa.phone AS Send_To, 
					wa.fname AS first_name, 
					'' AS last_name, 
					wei.v_toemail AS email, 
					'' AS reference_contact_id 
				FROM $db.web_email_information wei 
				LEFT JOIN $db.web_accounts wa ON wei.v_toemail = wa.email 
				WHERE wei.d_email_date BETWEEN '$startFormatted' AND '$endFormatted'
				GROUP BY wei.v_toemail";
		$result = mysqli_query($link, $sql);
	
		// Check if data exists
		if (mysqli_num_rows($result) > 0) {
			// Define the filename with current date
			$fileName = "web_information_" . date("Ymd_His") . ".csv";

			// Set headers for download
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $fileName . '"');

			// Open the output stream
			$output = fopen('php://output', 'w');

			// CSV Header
			$header = ['Send To', 'first_name', 'last_name', 'email', 'reference_contact_id'];
			fputcsv($output, $header);

			// Fetch and write rows
			while ($row = mysqli_fetch_assoc($result)) {
				fputcsv($output, $row);
			}

			// Close the output stream
			fclose($output);
			exit();
		} 
	}


	// farhan Akhtar :: Function to Get Active Prefix Country Code for SMS number [14-02-2025]
	public function getActivePrefixSMS(){
	    global $db,$link;
		$sql = "SELECT `v_prefix` FROM $db.`tbl_sms_connection` WHERE `status` = 1 ORDER BY `id` DESC";
		$result = mysqli_query($link,$sql);
		$row = mysqli_fetch_assoc($result);
		return $row['v_prefix'];
	}
	


	// Get bulksms templates  : By Aarti on 19-13-2023
	public function get_templates($campaign){
	    global $db,$link;
		$sql = "SELECT * FROM $db.bulksms_templates where type = '$campaign'";
		$result = mysqli_query($link,$sql);
		$template_assign = array();
		while($row = mysqli_fetch_assoc($result)){
			$template_assign[] = $row;
		}
		return $template_assign;
	}
	// Get bulksms templates  : By Aarti on 19-13-2023
	public function get_bulk_upload_list(){
	    global $db,$link;
	    $campaign =$_GET['campaign'];
		$sql = "SELECT * FROM $db.channel_bulk_uploads where channel_type = '$campaign' order by id desc";
		$result = mysqli_query($link,$sql);
		$recent_uploads = array();
		while($row = mysqli_fetch_assoc($result)){
			$recent_uploads[] = $row;
		}
		return $recent_uploads;
	}

	/*Start Single sms insert code*/
	// Insert single sms in database
	// STEP-5 function mapping insert_sms
	// corrected the function as the data wasn't being  handled correctly [vastvikta][17-05-2025]
	public function insert_smsmessages($data) {
		global $link, $db;
	
		$todaytime      = date("Y-m-d H:i:s");
		$caller_id      = isset($data['send_to']) ? trim($data['send_to']) : '';
		$message_text   = isset($data['message']) ? mysqli_real_escape_string($link, trim($data['message'])) : '';
		$V_Type         = 'SMS'; // Assuming this is a varchar column
		$v_prefix       = $this->getActivePrefixSMS(); // e.g., "91" for India
		$V_CreatedBY    = isset($data['created_by']) ? $data['created_by'] : '';
		$queue_session  = isset($data['queue_session']) ? $data['queue_session'] : '';
		$schedule_flag  = isset($data['schedule_flag']) ? $data['schedule_flag'] : '0';
		$schedule_time  = isset($data['schedule_time']) ? $data['schedule_time'] : null;
		$send_from      = 'alliance';
	
		
	
		// Fetch user name based on mobile
		$web_accounts = "SELECT name FROM $db.web_accounts WHERE mobile = '$caller_id' LIMIT 1";
		$response     = mysqli_query($link, $web_accounts);
		$value        = mysqli_fetch_array($response);
		$fname        = isset($value['name']) ? mysqli_real_escape_string($link, $value['name']) : 'Unknown';
	
		// Add prefix to mobile number (e.g., 91)
		$full_number = $v_prefix . $caller_id;
	
		// Prepare final SQL query
		$sql_sms_feed = "INSERT INTO $db.sms_out_queue 
			(send_to, send_from, message, message_type_flag, AccountName, created_by, create_date, status, queue_session, schedule_time, schedule_flag) 
			VALUES (
				'$full_number', 
				'$send_from', 
				'$message_text', 
				'1', 
				'$fname', 
				'$V_CreatedBY', 
				'$todaytime', 
				'1', 
				'$queue_session', 
				'$schedule_time', 
				'$schedule_flag'
			)";
	
		$result_sms = mysqli_query($link, $sql_sms_feed) or die("Error in insert: " . mysqli_error($link));
	
		return '1';
	}
	
	
	
	/*Start Single whatsapp outgoing insert code*/
	// Insert single sms in database
	// STEP-6 function mapping whatsapp outgoing
	// corrected the function as the data wasn't being  handled correctly [vastvikta][17-05-2025]
	public function insert_WhatsApp_Out($data) {
		global $link, $db;
		
		
		$todaytime     = date("Y-m-d H:i:s");
		$caller_id     = isset($data['send_to']) ? trim($data['send_to']) : '';
		$message_text  = isset($data['message']) ? trim($data['message']) : '';
		$V_CreatedBY   = isset($data['created_by']) ? $data['created_by'] : '';
		$queue_session = isset($data['queue_session']) ? $data['queue_session'] : '';
		$send_from     = isset($data['send_from']) ? trim($data['send_from']) : '';
		$mesg_type     = '1';  // default to '1'
		$status        = isset($data['status']) ? $data['status'] : '0';
		$channel_type  = '1';  // Assuming 1 = WhatsApp
		$msg_flag      = 'OUT'; // Keeping your existing use — if this should be a number, change accordingly
		$bulk_session_id = isset($data['bulk_session_id']) ? $data['bulk_session_id'] : ''; // Optional
	
		$sql_sms_feed = "INSERT INTO $db.whatsapp_out_queue 
			(send_to, send_from, message, message_type_flag, status, create_date, bulk_session_id, created_by, channel_type, msg_flag, queue_session)
			VALUES (
				'$caller_id',
				'$send_from',
				'$message_text',
				'1',
				'$status',
				'$todaytime',
				'$bulk_session_id',
				'$V_CreatedBY',
				'$channel_type',
				'$msg_flag',
				'$queue_session'
			)";
		
		$result_sms = mysqli_query($link, $sql_sms_feed) or die("Error In whatsapp_out_queue: " . mysqli_error($link));
		return '1';
	}
	// updated the function to handle the data correctly [vastvikta][17-05-2025]
	public function insert_web_email_information_out($data) {
		global $link, $db;
	
		
		$Mail         = isset($data['email']) ? trim($data['email']) : '';
		$from         = "rajdubey.alliance@gmail.com";
		$V_Subject    = !empty($data['Subject']) ? trim($data['Subject']) : trim($data['description']);
		$V_CreatedBY  = isset($data['created_by']) ? $data['created_by'] : '';
		$queue_session = isset($data['queue_session']) ? $data['queue_session'] : '';
		$todaytime    = date("Y-m-d H:i:s");
		$caller_id    = isset($data['send_to']) ? $data['send_to'] : '';
		$V_Content    = isset($data['message']) ? trim($data['message']) : '';
	
		$schedule_time = '';
		$schedule_flag = '0';
	
		if (!empty($data['schedule_flag']) && $data['schedule_flag'] == '1') {
			$schedule_time = $data['schedule_time'];
			$schedule_flag = '1';
		}
	
		$sql_email = "INSERT INTO $db.web_email_information_out 
			(v_toemail, v_fromemail, v_subject, v_body, d_email_date, email_type, module, I_Status, queue_session, ICASEID, scheduling_date, schedule_flag) 
			VALUES (
				'$Mail', 
				'$from', 
				'$V_Subject', 
				'$V_Content', 
				'$todaytime', 
				'OUT', 
				'Bulk Email', 
				'1', 
				'$queue_session', 
				'$caller_id', 
				'$schedule_time', 
				'$schedule_flag'
			)";
	
		
		$result_sms = mysqli_query($link, $sql_email) or die("Error in web_email_information_out: " . mysqli_error($link));
		
		return '1';
	}
	
	
	// Insert single sms in database
	// STEP-5 function mapping send_sms_index
	// updated the code related to insertion in email [vastvikta][17-05-2025]
    public function insert_sms_email_whatsapp(){
        $message_id = $_POST['message_id']; 
        $mobile = $_POST['mobile']; 
        $message = $_POST['message'];
        $scheduletime = $_POST['scheduletime'];
        $channel_type = $_POST['channel_type_direct'];
        if($scheduletime=='on'){
            $scheduledate = date('Y-m-d H:i', strtotime($_POST['date']));
            $scheduleflag='0';
        }else{
            $scheduleflag='1';
            $scheduledate = date('Y-m-d H:i');
        }        
       
		
        if($channel_type == 'SMS'){
    	 	foreach($mobile as $mob){
	            $data = array(
	                'send_to'=>$mob,
	                'send_from'=>'ZRA',
	                'message' => $message,
	                'message_type_flag'=>'0',
	                'status'=>'0',
	                'schedule_flag' =>$scheduleflag,
	                'schedule_time' => $scheduledate,
	                'created_by'  =>$_SESSION['userid'],
	            );
	          $respose = $this->insert_smsmessages($data);  
	        }	
        }else if($channel_type == 'Email'){
        	$Subject = $_POST['Subject'];
        	$email = $_POST['email']; 
			
	            $data = array(
	                'email'=>$email,
	                'Subject'=>$Subject,
	                'send_from'=>'ZRA',
	                'message' => $message,
	                'message_type_flag'=>'0',
	                'status'=>'0',
	                'schedule_flag' =>$scheduleflag,
	                'schedule_time' => $scheduledate,
	                'created_by'  =>$_SESSION['userid'],
	            );
				
	          $respose = $this->insert_web_email_information_out($data); 
			 	        
        }else if ($channel_type == 'WhatsApp') {
			foreach ($mobile as $mob) {
				
				// Make sure these variables are defined; assuming $scheduleflag and $scheduledate are derived from $scheduletime
				$scheduleflag = !empty($scheduletime) ? 1 : 0;
				$scheduledate = $scheduletime;
	
				$data = array(
					'send_to' => $mob,
					'send_from' => 'ZRA',
					'message' => $message,
					'message_type_flag' => '0',
					'status' => '0',
					'schedule_flag' => $scheduleflag,
					'schedule_time' => $scheduledate,
					'created_by' => $_SESSION['userid'],
				);
				
				$respose = $this->insert_WhatsApp_Out($data);
			}
		}
        return $respose;
    }
    // get sms quota for insert single sms
    // STEP-3 function mapping sms_quota_update
    public function get_quota_sms($user_id=''){
    	global $db,$link;
        if(empty($user_id)){
            $user_id=$_SESSION['userid'];
        }        
		$web_accounts="select * FROM $db.uniuserprofile where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
		$res=mysqli_fetch_array($response);
		return $res['sms_quota'];
    }
    // STEP-3 function mapping get_quota_whatsapp
    public function get_quota_whatsapp($user_id=''){
    	global $db,$link;
        if(empty($user_id)){
            $user_id=$_SESSION['userid'];
        }        
		$web_accounts="select * FROM $db.uniuserprofile where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
		$res=mysqli_fetch_array($response);
		return $res['whatsapp_quota'];
    }
    // get consumend quota sms for insert single sms
    // STEP-4 function mapping sms_quota_update
    public function get_consumend_quota_sms($user_id =''){
    	global $db,$link;
        if(empty($user_id)){
            $user_id=$_SESSION['userid'];
        }
        $web_accounts="select * FROM $db.uniuserprofile where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
		$res=mysqli_fetch_array($response);
        return $res['sms_quota_consumed'];
    }
    // get consumend quota sms for insert single sms
    // STEP-4 function mapping sms_quota_update
    public function get_consumend_quota_whatsapp($user_id =''){
    	global $db,$link;
        if(empty($user_id)){
            $user_id=$_SESSION['userid'];
        }
        $web_accounts="select * FROM $db.uniuserprofile where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
		$res=mysqli_fetch_array($response);
        return $res['whatsapp_quota_consumed'];
    }

    // update sms quata limit for insert single sms
    // STEP-2 - function mapping send_sms_index
    public function sms_quota_update($mobCnt){  
    	global $db,$link;      
        $quotaCnt = $this->get_quota_sms();
        $consumed_ct = $this->get_consumend_quota_sms();
        $user_id=$_SESSION['userid'];
        $sms_quota_count = $quotaCnt-$mobCnt;
        $sms_quota = $sms_quota_count ;
        $sms_quota_consumed = $consumed_ct+$mobCnt;

        $web_accounts="update $db.uniuserprofile set sms_quota='$sms_quota',sms_quota_consumed='$sms_quota_consumed' where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
    }
    // update sms quata limit for insert single sms
    // STEP-2 - function mapping send_sms_index
    public function whatsapp_quota_update($mobCnt){  
    	global $db,$link;      
        $quotaCnt = $this->get_quota_whatsapp();
        $consumed_ct = $this->get_consumend_quota_whatsapp();
        $user_id=$_SESSION['userid'];
        $whatsapp_quota = $quotaCnt-$mobCnt;
        $whatsapp_quota_consumed = $consumed_ct+$mobCnt;

        $web_accounts="update $db.uniuserprofile set whatsapp_quota='$whatsapp_quota',whatsapp_quota_consumed='$whatsapp_quota_consumed' where AtxUserID='$user_id'";
		$response=mysqli_query($link,$web_accounts);
    }
    // Insert single sms 
    // STEP-1
	public function send_sms_index() {
		$channel_type = $_POST['channel_type_direct'];
        if ($_POST['mobile'] && ($channel_type = 'SMS' || $channel_type = 'WhatsApp')) {  
        	if($channel_type = 'SMS'){
        		$quota = $this->get_quota_sms();
        	}else if($channel_type = 'WhatsApp'){
        		$quota = $this->get_quota_whatsapp();
        	}
			$mobCnt = count($_POST['mobile']);
			$msgLnth = strlen($_POST['message']);
            $finalcnt = (floor($msgLnth/153)+1) * $mobCnt;
			// No Quota limts For Admin 				
			if($_SESSION['userid'] != '1'){
				if($quota == 0){
					echo json_encode(array('st' => 2, 'msg' => "You Can't Send messages Please contact admin..Quota Exceed it's limit"));die();
				}else{
					if($quota >= $finalcnt){
						$result = insert_sms_email_whatsapp();
						if($result){
							$this->sms_quota_update($finalcnt);
							echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
						}else{
							echo json_encode(array('st' => 2, 'msg' => "Send Sms Fail, please Try after some time."));die();
						}
					}else{
						echo json_encode(array('st' => 2, 'msg' => "Your available quota limit is ".$quota.""));die();
					}
				}
			}else{
				$result = $this->insert_sms_email_whatsapp();
				if($result){
					if($channel_type = 'WhatsApp'){
						$this->whatsapp_quota_update($finalcnt); // update whatsapp quota
					}else{
						$this->sms_quota_update($finalcnt); // update sms quota
					}
					echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
				}else{
					echo json_encode(array('st' => 2, 'msg' => "Send Whatsapp message Fail, please Try after some time."));die();
				}
			}
		}else if ($_POST['email'] && $channel_type = 'Email') { 
			$result = $this->insert_sms_email_whatsapp();
			if($result){
				echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
			}else{
				echo json_encode(array('st' => 2, 'msg' => "Send Email Fail, please Try after some time."));die();
			}
        } else {
            $data = array(
                'mobile' => 'mobile[]',
                'message' => 'message',
                'date' => 'date',
            );
            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }
    }
    // get list contact when user search mobile
    public function get_contact_list(){
    	global $db,$link;
		$q = $_GET['q'];
		if(!empty($q)){
			$web_accounts="select * FROM $db.contact where first_name like '%$q%' or mobile_no like '%$q%' limit 20";
			$response=mysqli_query($link,$web_accounts);
			$contact = [];
			$num = mysqli_num_rows($response);
			if($num>0){
				while($value=mysqli_fetch_array($response)){
					$ar = array('id'=> $value['mobile_no'], 'text' => $value['first_name'].'-'.$value['mobile_no'],'Name'=>$value['first_name']);
					array_push($contact, $ar);
				}
			}else {
				$web_accounts = "SELECT * FROM $db.web_accounts 
								WHERE fname LIKE '%$q%' 
									OR mobile LIKE '%$q%' 
									OR phone LIKE '%$q%' 
								LIMIT 20";
				$response = mysqli_query($link, $web_accounts);
				$contact = [];

				while ($value = mysqli_fetch_array($response)) {
					$matched_number = '';
					if (strpos($value['mobile'], $q) !== false) {
						$matched_number = $value['mobile'];
					} elseif (strpos($value['phone'], $q) !== false) {
						$matched_number = $value['phone'];
					}

					$text = $value['fname'] . '-' . $matched_number;
					$ar = array(
						'id' => $matched_number,
						'text' => $text,
						'Name' => $value['fname']
					);
					array_push($contact, $ar);
				}
			}
		}
		echo json_encode($contact);die();
	}
	 // get list contact when user search mobile
    public function get_email_list(){
    	global $db,$link;
		$web_accounts="select * FROM $db.contact";
		$response=mysqli_query($link,$web_accounts);
		return $response;
	}
	//view single sms template
    public function get_template_content(){
    	global $db,$link;	
    	$tempname=$_POST['tempname'];
        $web_accounts="select * FROM $db.bulksms_templates where name='$tempname'";
		$response=mysqli_query($link,$web_accounts);
		$value=mysqli_fetch_array($response);
        print_r(json_encode($value));

    }
    ###########################################Code Bulk##############################
    /*Bulk insert sms code start*/
    public function ajax_campaign_contact_count(){
    	global $db,$link;
		$campaign_id = $_GET['campaign_id'];
		if($campaign_id > 0 ){
			$sql = "SELECT * FROM $db.channel_bulk_uploads where id = $campaign_id";
			$result = mysqli_query($link,$sql);
			$results = mysqli_fetch_assoc($result);
			echo 'Total Count In selected Campaign '. $results['total_count'];die(); 
		}
		echo '' ;die();
	}

    // aarti update : 20-12-2023 
    // Save Contact From Bulk Upload list
    // STEP - 4
    public function multi_unique($src){
		$output = array_map("unserialize",array_unique(array_map("serialize", $src)));
	   	return $output;
	}

	// Validation for mobile number
	public function validate_mobile_number($mobile) {
	    // Example: Check if the number has 10-15 digits
	    return preg_match('/^\d{10,15}$/', $mobile);
	}

	// Validation for email
	public function validate_email($email) {
	    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	/*Insert Contact From Bulk Upload list*/
	public function insert_batch_contact($filtered){
		global $db,$link;
		$mobile_no = $filtered['mobile_no'];
		$email= $filtered['email'];
		$first_name = $filtered['first_name'];
		$last_name = $filtered['last_name'];
		$sql_qry = "INSERT INTO $db.contact (first_name,mobile_no,email,last_name) VALUES ('{$first_name}','{$mobile_no}','{$email}','{$last_name}')";
	    mysqli_query($link, $sql_qry);
	    // echo $sql_qry;
	}
	/*Insert Bad Contact From Bulk Upload list*/
	public function insert_batch_bad_contact($filtered){
		global $db,$link;
		$mobile_no = $filtered['mobile_no'];
		$email= $filtered['email'];
		$first_name = $filtered['first_name'];
		$last_name = $filtered['last_name'];
		$sql_qry = "INSERT INTO $db.bad_contact (first_name, mobile_no,email,last_name) VALUES ('{$first_name}','{$mobile_no}','{$email}','{$last_name}')";
	    mysqli_query($link, $sql_qry);
	}
	public function save_contact_from_list($value=''){ 
    	if(count($this->customer_array) > 0 ){
    		$customer_data = $this->multi_unique($this->customer_array) ;
    		// $customer_data = $this->customer_array;
    		$filtered = [] ;
    		foreach ($customer_data as $key => $value) {
    			if(!$this->is_customer_exist($value['mobile_no'])){
    				// array_push($filtered, $value);
    				$this->insert_batch_contact($value);
    			}
    		}
    		// insert in batch
    		if(count($filtered) > 0 ){
    			// $this->insert_batch_contact($filtered);
    		}
    	}
    	if(count($this->bad_customer_array) > 0 ){    		
    		// $this->insert_batch_bad_contact($this->bad_customer_array)    		
    	}
    }
	
	// get select file name
	public function get_selected_file_name($file_id){	
		global $db,$link;
		$web_accounts="select file_name FROM $db.channel_bulk_uploads where id='$file_id'";
		$response=mysqli_query($link,$web_accounts);
		$data=mysqli_fetch_array($response);
		return $data['file_name']; // Return only the file name
		
	}
	//  this function filter a list and make simple array
	// STEP - 3  
	public function make_bulk_upload_list($path, $scheduletime, $bulk_session_id='', $queue_session_id='' ){
		// check for scheduel date time
	    if($scheduletime=='on'){
            $schedule_date_time = date('Y-m-d H:i', strtotime($_POST['date']));
            $schedule_flag='0';
        }
        else{
            $schedule_date_time = date('Y-m-d H:i');
            $schedule_flag='1';
        }	

        $bad_customer_data =[] ;
        $customer_data =[] ;
        $list_data =[] ;
		// Open the CSV file for reading
		$csvFile = fopen($path, 'r');
		if ($csvFile !== false) {
		    // Read the headers (first row)
		    $headers = fgetcsv($csvFile);
		    // Output headers
		    // echo "Headers: " . implode(', ', $headers) . "\n";
		    // Read and output each row of data
		    $i=1;
		    while (($data = fgetcsv($csvFile)) !== false) {
		        // echo "Values: " . implode(', ', $data) . "\n";
		        $bad_records = false ;
		        $send_to = $data[0];
		        $first_name = $data[1];
		        $last_name = $data[2];
		        $email = $data[3];
		        $reference_id = $data[4];

		        $mobile = trim($send_to,'\'"');
				$mobile = preg_replace("/[^0-9]/", '', $mobile);

				// Validate mobile number
	            if (!$this->validate_mobile_number($mobile)) {
	                $bad_records = true;
	                $data['file_upload'] = "Invalid Mobile Number in line no $i. Value is: $mobile";
	                // $bad_customer_data[] = $data;
	                // continue;
	                echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
	            }

	            // Validate email
	            if (!$this->validate_email($email)) {
	                $bad_records = true;
	                $data['file_upload'] = "Invalid Email Address in line no $i. Value is: $email";
	                // $bad_customer_data[] = $data;
	                echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
	                // continue;
	            }

				if($bad_records){
					$bad_customer_data[] = array(
						'first_name' 	=>	$first_name,
						'last_name' 	=>	$last_name,
						'email' 		=>	$email,
						'mobile_no' 	=>	$mobile,
						'reference' 	=>	$reference_id,
						'campaign_name' 	=>	$_POST['file_upload_type'] == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id,
						'created_by' 	=>	$_SESSION['userid']
					);
				}else{
					// Good Records 
					$customer_data[] = array(
						'first_name' 	=>	$first_name,
						'last_name' 	=>	$last_name,
						'email' 		=>	$email,
						'mobile_no' 	=>	$mobile,
						'reference' 	=>	$reference_id,
						'created_by' 	=>	$_SESSION['userid']
					);
					// message array 					 
					$list_data[] = array(
						'send_to' =>	$mobile,
						'send_from' =>	'ZRA',
						'message' =>	$_POST['message'],
						'message_type_flag' => '1',
						'status' =>	'0',
						'schedule_flag'  => $schedule_flag,
						'schedule_time'  => $schedule_date_time,
						'queue_session'  => $queue_session_id,
						'created_by'  => $_SESSION['userid'],
						'email' 		=>	$email,
						'description' => $_POST['description'],
						'bulk_session_id' => $_POST['file_upload_type'] == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id
					);
					$this->customer_array = $customer_data ;
					$this->bad_customer_array = $bad_customer_data ;
				}
				$i++;
		    }
		    // Close the file
		    fclose($csvFile);
		} else {
		    // Handle file opening error
		    echo "Error opening the CSV file.";
		}
			/* Close Foreach Loop Here */
		return $list_data;
	}
	
    // aarti update : 20-12-2023 check customer exist in database
    public function is_customer_exist($mobile){	
    	global $db,$link;
		// if(!empty($filename)){
			$web_accounts="select * FROM $db.contact where mobile_no='$mobile'";
			$response=mysqli_query($link,$web_accounts);		
			$num = mysqli_num_rows($response);
			if($num>0){
				return true;
			}else{
				return false;
			}
		// }
    }
    //This is my uploade file code//
    public function do_upload($bulk_session_id){
    	global $bulkcampaign_path;
	    $new_name = $_POST['file_upload_type'] == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id; // Assuming 'uploaded_file' is the name of your file input
	    $target_dir = $bulkcampaign_path;
		$target_file = $target_dir . basename($new_name); 
		$imageFileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
		// Check file size
		$uploadOk = 1;
		if ($_FILES["file"]["size"] > 1000000) {
		  $data['file_upload'] = 'Sorry, your file is too large.';
		  echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
		  $uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "xls" && $imageFileType != "csv" ) {
		  $data['file_upload'] = 'Sorry, only xls|xlsx|csv & GIF files are allowed.';
		  echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
		  $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		  $data['file_upload'] = 'Sorry, your file was not uploaded.';
		  echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
		} else {
			if (!file_exists($target_dir)) {
			    mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
			}
			$newpath = $target_file.'.'.$imageFileType;
		    if (move_uploaded_file($_FILES["file"]["tmp_name"], $newpath)) {
			    // echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
			    return true;
			} else {
			    // echo "Sorry, there was an error uploading your file.";
			     return false;

			}
		}
	}
	#################################### (Bulk SMS) ################################################
    /*Insert sms bulk uploads data in database*/
    //mapping with import_file function
    public function insert_channel_bulk_uploads($upload_data){
    	global $db,$link;
    	$list_name = $upload_data['list_name'];
       	$description = $upload_data['description'];
       	$file_name = $upload_data['file_name'];
		$slug = $upload_data['slug'];
		$total_count = $upload_data['total_count'];
		$created_by = $upload_data['created_by'];
		$channel_type = $upload_data['channel_type'];
		$created_at = date('Y-m-d H:i');
    	$sql_sms_feed="insert into $db.channel_bulk_uploads (list_name,slug,description,total_count,file_name,created_by,created_at,channel_type) values ('$list_name','$slug','$description','$total_count','$file_name', '{$created_by}','{$created_at}','$channel_type')";
		$result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In Query24 ".mysqli_error($link));
		$last_insert_id = mysqli_insert_id($link);
		return $last_insert_id;

    }
    /*Update sms bulk uploads data in database*/
    //mapping with import_file function
    public function update_channel_bulk_uploads(){
    	global $db,$link;
    	// $total_count = $upload_data['total_count'];
		// $file_name = $upload_data['file_name'];
    	$total_count = '';
		$file_name = '';
		$updated_at = date('Y-m-d H:i');
    	$sql_sms_feed="update $db.channel_bulk_uploads set total_count='$total_count',updated_at='$updated_at' where file_name='$file_name'";
		$result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In Query24 ".mysqli_error($link));
		// Get the last inserted ID
		$last_insert_id = mysqli_insert_id($link);
		return $last_insert_id;
    }
    // aarti update : 20-12-2023 check uploade file exits or not
	// STEP - 2
	//mapping with import_file function
	public function check_upload_file_exist($filename='',$channel_type){
		global $db,$link;
		if(!empty($filename)){
			$web_accounts="select * FROM $db.channel_bulk_uploads where file_name='$filename' and channel_type='$channel_type'";
			$response=mysqli_query($link,$web_accounts);		
			$num = mysqli_num_rows($response);
			if($num>0){
				$data=mysqli_fetch_array($response);
				return $data;
			}else{
				return false;
			}
		}
		return false ;
		// Response error 
	}
	//Insert record in queue_bulk_relation table
	//mapping with import_file function
    public function insert_queue_bulk_relation($relation_data){
		global $db,$link;
		$list_id = $relation_data['list_id'] ;
		$message = $relation_data['message'];
		$queue_session_id = $relation_data['out_queue_session'];
		$total_count = $relation_data['total_count'];
		$schedule_flag= $relation_data['schedule_flag'];
		$schedule_time = $relation_data['schedule_time'];
		$created_at = $relation_data['created_at'];
		$created_by = $relation_data['created_by'];
		$channel_type = $relation_data['channel_type'];
    	$sql_sms_feed="insert into $db.queue_bulk_relation (list_id,message,out_queue_session,total_count,schedule_flag, schedule_time,created_at,created_by,channel_type) values ('$list_id','$message','$queue_session_id','$total_count','$schedule_flag','$schedule_time', '$created_at','$created_by','$channel_type')";
		$result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In Query24 ".mysqli_error($link));
		return '1';
	}
	//Aarti update : 10-06-2021    
    //this function is responsible to upload lsit
	public function import_file(){
		global $bulkcampaign_path;
		$bulk_session_id="Bulk_".uniqid()."_".rand(0,1000000);	// Make Ther Unique BULK Upload ID 
		$queue_session_id = md5(microtime() . mt_rand());	// Make Ther Unique BULK Upload ID 		
		$schedule_time = $_POST['scheduletime'];

		if($schedule_time == 'on'){
            $schedule_date_time = date('Y-m-d H:i', strtotime($_POST['date']));
            $schedule_flag='0';
        }else{
            $schedule_date_time = date('Y-m-d H:i');
            $schedule_flag='1';
        }

		// fetch avialable sms quota for loggedin user
		if($_POST['channel_type'] == 'WhatsApp'){
			$quota = $this->get_quota_whatsapp();
		}else if($_POST['channel_type'] == 'SMS'){
			$quota = $this->get_quota_sms();
		}
		
		// Read and store for new file if User Add New File 
		if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]) ){
			$path = $_FILES["file"]["tmp_name"];
			// Check file exist or not 
			if($_POST['file_upload_type'] != 'overwrite'){
				$result = $this->check_upload_file_exist($bulk_session_id.'.csv',$_POST['channel_type']); 
				if($result != false){
					$list_id = $result['id'];
					if($_POST['file_upload_type'] != 'no_new'){
						$data['file_upload'] = "you want to update file";
						echo json_encode(array('status' => 'fail', 'error_type' => '1' , 'msg' => $data  ));
						die();
					}
				}
			}
			
			/* make bulk Upload data from file */
			/* fetch data from excel file and store database*/
		    $data = $this->make_bulk_upload_list($path, $schedule_time, $bulk_session_id, $queue_session_id);
		    // Check for Qouta avialablty
		    $msgLnth = strlen($_POST['message']);
        	$finalcnt = (floor($msgLnth/153)+1) * count($data);
        	
        	// No Quota limts For Admin 				
			if($_SESSION['userid'] != '1' && ($_POST['channel_type'] == 'WhatsApp' || $_POST['channel_type'] == 'SMS')){
            	if($quota < $finalcnt){
            		$data['file_upload'] = "Your available quota limit is $quota, less than that required $finalcnt";
					echo json_encode(array('status' => 'fail', 'msg' => $data));die();
            	}
			}; 

			// File Remove first before save it in case of overwrite
			if($_POST['file_upload_type'] == 'overwrite'){
				// remove old file in case or overwrite
				$file_with_path = $bulkcampaign_path.$_FILES["file"]["name"] ;
				// log_message('error', ' file_with_path '.$file_with_path);
				if(file_exists($file_with_path)){
					unlink($file_with_path);
				}

			}          		 
			// uploading file function used here
			$do_uploade = $this->do_upload($bulk_session_id);
			if($do_uploade){
            	// Save Customer Details 
            	$this->save_contact_from_list(); // insert record contact table
            	if($_SESSION['userid'] != '1'){// update SMS Quota 
	            	if($_POST['channel_type'] == 'WhatsApp'){
						$this->whatsapp_quota_update($finalcnt);
					}else if($_POST['channel_type'] == 'SMS'){
						$this->sms_quota_update($finalcnt);
					}	
	            }
            	// file Upload Successfully
            	foreach ($data as $key_bulk => $value_bulk){
            		if($_POST['channel_type'] == 'WhatsApp'){
            			$this->insert_WhatsApp_Out($value_bulk);	// insert channel_bulk_uploads table 
            		}else if($_POST['channel_type'] == 'SMS'){
            			$this->insert_smsmessages($value_bulk);	// insert channel_bulk_uploads table 
            		}else if($_POST['channel_type'] == 'Email'){
            			$this->insert_web_email_information_out($value_bulk);	// insert channel_bulk_uploads table 
            		}	
            	}
            	$new_name = $_POST['file_upload_type'] == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id; // Assuming 'uploaded_file' is the name of your file input
            	$imageFileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));

            	$new_filename = $new_name.'.'.$imageFileType;
				// Dont make any change in here in case of overwite
				if($_POST['file_upload_type'] != 'overwrite'){
					// save new list record 
					
		           	$upload_data['list_name'] = $_POST['list_name'];
		           	$upload_data['description'] = $_POST['description'];
		           	$upload_data['file_name']	= $new_filename;
					$upload_data['slug'] = str_replace(" ", "-", strtolower($_POST['list_name']))  ;
					$upload_data['total_count'] = count($data);
					$upload_data['created_by']  = $_SESSION['userid'];
					if($_POST['channel_type'] == 'WhatsApp'){
						$upload_data['channel_type'] = 'WhatsApp';
					}else if($_POST['channel_type'] == 'SMS'){
						$upload_data['channel_type'] = 'SMS';
					}else if($_POST['channel_type'] == 'Email'){
						$upload_data['channel_type'] = 'Email';
					}
					$list_id = $this->insert_channel_bulk_uploads($upload_data); // insert sms bulk upload table						
				}else{
					// if File Overwrite than update Count In Mail list Table
					// $new_name = basename($_FILES["file"]["name"], '.csv') ;
					// $this->db->where('file_name', $new_name);
					$this->update_channel_bulk_uploads(array('total_count' =>count($data) ,'updated_at' => date('Y-m-d H:i:s', strtotime('now')), 'file_name' =>$new_filename));

					// if file exist get list id
					$result = $this->check_upload_file_exist($new_filename,$_POST['channel_type']); 
					$list_id = $result['id'];

				}
				// save Relation 
				$relation_data['list_id'] = $list_id ;
				$relation_data['message'] = $_POST['message'];
				$relation_data['total_count'] = count($data);
				$relation_data['out_queue_session'] = $queue_session_id;
				$relation_data['schedule_flag']	= $schedule_flag ;
				$relation_data['schedule_time']	= $schedule_date_time ;
				$relation_data['created_at']  = date('Y-m-d H:i', strtotime('now'));
				$relation_data['created_by']  = $_SESSION['userid'];
				if($_POST['channel_type'] == 'WhatsApp'){
					$relation_data['channel_type'] = 'WhatsApp';
				}else if($_POST['channel_type'] == 'SMS'){
					$relation_data['channel_type'] = 'SMS';
				}else if($_POST['channel_type'] == 'Email'){
					$relation_data['channel_type'] = 'Email';
				}
				
				/* insert queue_bulk_relation data in table*/
				$this->insert_queue_bulk_relation($relation_data);
		        echo json_encode(array('status' => 'success', "message" => "Data Imported successfully"));die();					
			}else{
				$data['file_upload'] = 'file upload Fial, Please try after some time';
				echo json_encode(array('status' => 'fail', 'msg' => $data));die();
			}			
		}else{// this is responsible for file slected from dropdown
			if($_POST['select_file_name'] > 0){					
				$selected_file = $this->get_selected_file_name($_POST['select_file_name']);
				// file path 
				$path = $bulkcampaign_path.$selected_file ;
				if(file_exists($path)){
					/*Code Remove from here*/
				    $data = $this->make_bulk_upload_list($path, $schedule_time, $selected_file, $queue_session_id);
					
					// Check for Qouta avialablty
				    $msgLnth = strlen($_POST['message']);
	            	$finalcnt = (floor($msgLnth/153)+1) * count($data);
	            	
	            	// No Quota limts For Admin 				
					if($_SESSION['userid'] != '1'){
		            	if($quota < $finalcnt){
		            		$data['file_upload'] = "Your available quota limit is $quota, less than that required $finalcnt";
							echo json_encode(array('status' => 'fail', 'msg' => $data));die();
		            	}
					}
					// $res = insert_bulk($data);
					// chnaged the handling for the multiple and single data [vastvikta][17-05-2025]
					if($_POST['channel_type'] == 'WhatsApp'){
						foreach ($data as $row) {
            			$this->insert_WhatsApp_Out($row);	// insert channel_bulk_uploads table 
					    }
            		}else if($_POST['channel_type'] == 'SMS'){
						foreach ($data as $row) {
            			$this->insert_smsmessages($row);	// insert channel_bulk_uploads table 
						}
            		}
					else if ($_POST['channel_type'] == 'Email') {
						// updated as it was giving error [vastvikta][17-05-2025]
						foreach ($data as $row) {
							$this->insert_web_email_information_out($row); // Call one-by-one
						}
					}

					if($_SESSION['userid'] != '1'){
						if($_POST['channel_type'] == 'WhatsApp'){
							$this->whatsapp_quota_update($finalcnt);
						}else if($_POST['channel_type'] == 'SMS'){
							$this->sms_quota_update($finalcnt);
						}
					}

					// save Relation 
					$relation_data['list_id'] = $_POST['select_file_name'];
					$relation_data['message'] = $_POST['message'];
					$relation_data['total_count'] = count($data);
					$relation_data['out_queue_session'] = $queue_session_id;
					$relation_data['schedule_flag']	= $schedule_flag ;
					$relation_data['schedule_time']	= $schedule_date_time ;
					$relation_data['created_at']  = date('Y-m-d H:i', strtotime('now'));
					$relation_data['created_by']  = $_SESSION['userid'];
					if($_POST['channel_type'] == 'WhatsApp'){
						$relation_data['channel_type'] = 'WhatsApp';
					}else if($_POST['channel_type'] == 'SMS'){
						$relation_data['channel_type'] = 'SMS';
					}else if($_POST['channel_type'] == 'Email'){
						$relation_data['channel_type'] = 'Email';
					}
					$this->insert_queue_bulk_relation($relation_data);
					echo json_encode(array('status' => 'success', "message" => "Data Imported successfully, Selected file is $selected_file"));die();
			        /* START TRANS CLOSE HERE */
			    }else{
					$data['file_upload'] = 'file not exist on path';
					echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
				}
			}
			else{
				$data['file_upload'] = 'Please select file to upload';
				echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
			}
		}		
		#################################################################################################
	    ###  ..../// Bulk Upload Section Close here 						
	    #################################################################################################			 				
	}
}
$a = new BULK();
?>