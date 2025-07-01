<?php
include("web_mysqlconnect.php");
include("web_function.php");

$logedin_agent 	= 	$_SESSION['logged'];
$vuserid	   	= 	$_SESSION['userid'];
$todaydate 		=	date("Y-m-d H:i:s");



$caller_id 	=	$_POST['caller_id'];
$agent 		=	$_POST['agent'];
$customerid =	$_POST['customerid'];
$lead_id 	=	$_POST['lead_id'];
$recording_file 	=	$_POST['file'];
$vendor_lead_code =	$_POST['vendor_lead_code'];
$from="rajdubey.alliance@gmail.com";



function closeCaseWorkstatus($docket_no)
{
	global $link,$db,$vuserid,$todaydate;
	/*Check For Allow Update or Not*/
	if(!empty($docket_no)){
		$query = "select iPID, ticketid, work_status, work_timestamp,i_CreatedBY FROM $db.web_problemdefination where ticketid='$docket_no'  " ;
		$res = mysqli_fetch_assoc(mysqli_query($link,$query));
		$id = $res['iPID'];
		$update_by = $res['i_CreatedBY'];
		$ticketid = $res['ticketid'];
		$work_status = $res['work_status'];
		$work_timestamp = $res['work_timestamp'];

		if( ($work_status == '1' && $update_by == "0") || ($work_status == '1' && $update_by == $vuserid)) {
			$sql_query = "update $db.web_problemdefination set work_status='0',  work_timestamp = '$todaydate', i_CreatedBY ='0' where IPID='$id' " ;
			mysqli_query($link,$sql_query);
			add_audit_log($vuserid, 'case_open_to_changes', $docket_no, 'Case Opened For Chnages', $db);
			// echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
		}
		else if($work_status == '1' && $update_by != $vuserid){
			add_audit_log($vuserid, 'case_not_opened', $docket_no, 'Someone is already working', $db);
			echo json_encode(array('status' => 'fail', "message" => "Someone is already working, you can't do any thing " ));die();

		} 
	}
	//echo json_encode(array('status' => 'fail', "message" => "Someone is already working 2, you can't do any thing" ));die();

	/*Check For Allow Update or Not*/
}

/*
	Update Customer Feed Back
	this section is for customer feed back after case resolved
*/
// echo json_encode($_POST);die();
if(isset($_POST['customer_remark'])){
	if( isset($_POST['ticket_no']) && !empty($_POST['ticket_no'])){
	//echo json_encode($_POST);die();
		$docket_no 			= $_POST['ticket_no'] ;
		$feedback 			= $_POST['feedback'] ;
		$source 			= $_POST['feed_source_id'] ;
		$customer 			= $_POST['feed_customer_id'] ;
		$feed_email 			= $_POST['feed_email'] ;
		$status_type_ 		= $_POST['status_type_'] ;
		$customer_remark 	= $_POST['customer_remark'] ;


		$arr = getcustomers($customer);
		//print_r($arr);

		/*Check For Allow Update or Not*/
		if(!empty($_POST['docket_no'])  ){
			$query = "select iPID, ticketid, work_status, work_timestamp,i_CreatedBY FROM $db.web_problemdefination where ticketid='$docket_no'  " ;
			$res = mysqli_fetch_assoc(mysqli_query($link,$query));
			$id = $res['iPID'];
			$update_by = $res['i_CreatedBY'];
			$ticketid = $res['ticketid'];
			$work_status = $res['work_status'];
			$work_timestamp = $res['work_timestamp'];

			if( ($work_status == '1' && $update_by == "0") || ($work_status == '1' && $update_by == $vuserid)) {
				$sql_query = "update $db.web_problemdefination set work_status='0', current_working_agent ='0'  work_timestamp = '$todaydate', i_CreatedBY ='0' where IPID='$id' " ;
				mysqli_query($link,$sql_query);
				//echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
			}
			else if($work_status == '1' && $update_by != $vuserid){
				echo json_encode(array('status' => 'fail', "message" => "Someone is already working, you can't do any thing " ));die();

			} 
		}

		// Send Case Close Notification 
		if(isset($_POST['feed_email']) && !empty($_POST['feed_email']) && $status_type_ =='3')
		{
			$email 	= $_POST['feed_email'] ;		 
			$subject = "Case Closed [TICKET # $docket_no]";
			$message = "Dear Customer, <br><br>Thank you for your patience. <br><br> Your Ticket Number $docket_no has been Closed. We thank you for your very valuable feedback. <br><br>This is an automated generated response from LWSC Call Center System <br> Warm Regards <br>Team LWSC.<br> <br><br>";
			$todaytime=date("Y-m-d H:i:s");
		 
			// $sql_email="INSERT INTO $dbname.web_email_information_out(v_toemail, v_fromemail, v_subject, v_body, email_type, module , ICASEID) VALUES ('$email','$from','$subject','$message','OUT','Case Update By ' , '".$docket_no."')";
			if($status==3){
				/*Aarti-23-11-23
                insert data in web_email_information_out table and 
                replce the insert code and add new function for insert code*/
                $data_email=array();
                $data_email['Mail'] = $email;
                $data_email['from']= $from;
                $data_email['V_Subject']=$subject;
                $data_email['V_Content']=$message;
                $data_email['ICASEID']=$docket_no;
                $data_email['i_expiry']=$expiry;
                $data_email['view']='Case Updatedl';
                insert_emailinformationout($data_email);
                /*end - web_email_information_out*/                   
			 	// mysqli_query($link,$sql_email) or die(mysqli_error($link)."Err web_email_information_out");
			}
		}	
		$phone = $arr[0]['phone'];
		$fname = $arr[0]['fname'];
		// if(!empty($phone)){

		// 	// Get Sms String
		// 	$v_smsString  = get_sms_template($docket_no, $fname,  'close_case', '');
				
		// 	 $sql_sms="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status) values ('$docket_no','$phone','$v_smsString','Sms','$fname','$logedin_agent',NOW(), '1')";
		// 	$result_sms= mysqli_query($link,$sql_sms) or die("Error In Query24 ".mysqli_error($link));
		// }

		//send sms

		if(!empty($phone)){

			$sms_type = 'closed_case';
			// Get Sms String
		  
			$customer_name=ucwords($fname);
			$data_arr = array("name"=>$customer_name);
			$res_sms = sms_template($docket_no,$sms_type,$data_arr);
			$message = $res_sms['msg'];
			$expiry = $res_sms['expiry'];

			//  $sql_sms="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status,i_expiry) values ('$docket_no','$phone','$message','Sms','$fname','',NOW(), '1','$expiry')";
			// $result_sms= mysqli_query($link,$sql_sms) or die("Error In Query24 ".mysqli_error($link));
			/*Aarti-23-11-23
            insert data in tbl_smsmessages table*/
            $data_sms=array();
            $data_sms['v_category'] = $docket_no;
            $data_sms['v_mobileNo'] = $phone;
            $data_sms['v_smsString']= $message;
            $data_sms['V_Type']='Sms';
            $data_sms['V_AccountName']=$fname;
            $data_sms['V_CreatedBY']='';
            $data_sms['i_status']='1';
            $data_sms['i_expiry']=$expiry;                
            insert_smsmessages($data_sms);
            /*end - web_email_information_out*/
		 }
		 

		/*Check For Allow Update or Not*/
		$update_sql = "update $db.web_problemdefination set iCaseStatus='$status_type_', feedback_status = $feedback, customer_feedback ='$customer_remark' where ticketid='$docket_no'" ;
		mysqli_query($link,$update_sql);
		
		$sql = "insert into $db.web_case_interaction (caseID, custmer_id, mode_of_interaction, current_status, created_date, created_by, recording_filename, vendor_lead_code, lead_id, language, caller_id, remark, interaction_id, interacation_type) values ('$docket_no', '$customer', '$source', '$status_type_', '$todaydate', '$logedin_agent',  '$recording_file', '$vendor_lead_code', '$lead_id', '$lang', '$caller_id', '$customer_remark', 'customer_feedback' , 'Remark')" ;
		
		if(mysqli_query($link,$sql)){
			echo json_encode(array('status' => 'success', "message" => "Thankyou for Your valuable feedback."));die();
		}
		echo json_encode(array('status' => 'fail', "message" => "feedback not saved, please try after some time"));die();
	}
}

/*	
	Save Add More Interaction Docket No Wise 
	Author : Vijay  
*/
	
	//echo json_encode($_POST);die();
if(isset($_POST['interaction_remark'])){
	// echo json_encode($_POST);die();
	if( isset($_POST['docket_no']) && !empty($_POST['docket_no'])){
		$docket_no 			= $_POST['docket_no'] ;
		$source 			= $_POST['source_id'] ;
		$customerid 		= $_POST['customer_id'] ;
		$status_type_ 		= $_POST['inte_status_type_'] ;
		//$status_type_ 		= $_POST['status_type_'] ;
		$remark 			= $link->real_escape_string($_POST['interaction_remark']);
		$c_mobile 		= $_POST['c_mobile'] ;
		$c_email 		= $_POST['c_email'] ;
		$fname 				= $_POST['c_full_name'] ;
		$bll_assign 			= isset($_POST['bll_assign']) ? $_POST['bll_assign']  : '' ;
		$type           =$_POST['type'];


		//closeCaseWorkstatus($docket_no);
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
				$sql_query = "update $db.web_problemdefination set work_status='0',  work_timestamp = '$todaydate', current_working_agent ='0' where IPID='$id' " ;
				mysqli_query($link,$sql_query);
				//echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
			}
			// else if($work_status == '1' && $update_by != $vuserid){
			// 	echo json_encode(array('status' => 'fail', "message" => "Someone is already working, you can't do any thing " ));die();
			// } 
		}

		$status_name = ticketstatus($status_type_);

		if($_POST['status_old'] != $status_type_){
			$action_changes = "Status is Changed to $status_name";
		}else{
			$action_changes = "Remark is added For $status_name Case";
		}
		$status_rem = "Case with this ticked id -$docket_no  $action_changes";

		add_audit_log($vuserid, 'interaction_remark', $docket_no, $remark, $db, $status_rem);
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
 	
		$sql = "insert into $db.web_case_interaction (caseID, custmer_id, mode_of_interaction, current_status, remark, created_date, created_by, recording_filename, vendor_lead_code, lead_id, language, caller_id, interacation_type,action) values ('$docket_no', '$customerid', '$source', '$status_type_', '$remark', '$todaydate', '$logedin_agent',  '$recording_file', '$vendor_lead_code', '$lead_id', '$lang', '$caller_id', 'Remark','$action_changes')" ;

		if(mysqli_query($link,$sql)){
			/* Send Email and SMS To Customer If Case Resolved */
			// case resolved

			if($status_type_ == '8'){	
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
                    $data_email['from']= $from;
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

					//  $sql_sms="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status,i_expiry) values ('$docket_no','$c_mobile','$message','Sms','$fname','',NOW(), '1','$expiry')";
					// $result_sms= mysqli_query($link,$sql_sms) or die("Error In Query24 ".mysqli_error($link));
					/*Aarti-23-11-23
	                insert data in tbl_smsmessages table*/
	                $data_sms=array();
	                $data_sms['v_category'] = $docket_no;
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
			
			}else if($status_type_ == '3')
			{
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
                    $data_email['from']= $from;
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

						// $sql_sms="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status,i_expiry) values ('$docket_no','$c_mobile','$message','Sms','$fname','',NOW(), '1','$expiry')";
						// $result_sms= mysqli_query($link,$sql_sms) or die("Error In Query24 ".mysqli_error($link));
						/*Aarti-23-11-23
		                insert data in tbl_smsmessages table*/
		                $data_sms=array();
		                $data_sms['v_category'] = $docket_no;
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
				

			}

			/*-------------------------Send NPS Feedback link with MAIL and SMS-----------------*/
			if($status_type_ == '3'){
				include("common_function.php");
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
				$fname = $first_name." ".$last_name;
				$npslink = $common_function->addNpsFeddback($npsfeedback_data);
				$from="rajdubey.alliance@gmail.com";
				$case_type1 = 'nps_feedback';
				$data_array = array('nps_link' => $npslink);
				$ress_1 = mail_template($docket_no, $case_type1, $data_array);	
				$expiry = $ress_1['expiry'];
				$subject = $ress_1['sub'];
				$message_mail = $ress_1['msg'];

				$res2 = sms_template($docket_no, $case_type1, $data_array);
				$message_text = $res2['msg']; 
				$expiry = $res2['expiry']; 
				
				/*Aarti-23-11-23
                insert data in web_email_information_out table and 
                replce the insert code and add new function for insert code*/
                $data_email=array();
                $data_email['Mail'] = $email;
                $data_email['from']= $from;
                $data_email['V_Subject']=$subject;
                $data_email['V_Content']=$message_mail;
                $data_email['ICASEID']=$docket_no;
                $data_email['i_expiry']=$expiry;
                $data_email['view']='New Case Call';
                insert_emailinformationout($data_email);
                /*end - web_email_information_out*/

                /*Aarti-23-11-23
                insert data in web_email_information_out table and 
                replce the insert code and add new function for insert code*/
                $data_sms=array();
                $data_sms['v_category'] = $docket_no;
                $data_sms['v_mobileNo'] = $caller_id;
                $data_sms['v_smsString']= $message_text;
                $data_sms['V_Type']='Sms';
                $data_sms['V_AccountName']=$fname;
                $data_sms['V_CreatedBY']='';
                $data_sms['i_status']='1';
                $data_sms['i_expiry']=$expiry;                
                insert_smsmessages($data_sms);
                /*end - web_email_information_out*/

				//Send Customer Effort Score (CES) link 
					$ces_data = array();
					$ces_data['createdBy'] = 'CES';
					$ces_data['customer_id'] = $customerid;
					$ces_data['customer_email'] = $email;
					$ces_data['ticket_id'] = $docket_no;
					$ces_data['phone_number'] = $caller_id;
					$ces_data['unique_id'] = $caller_id;		
					$ces_data['feedback_value'] = '';
					$ces_data['media'] = 'MAIL';
					$ces_data['flag'] = '0';
					$ceslink = $common_function->addCES($ces_data);
					$case_types = 'customer_effort_feedback';
					$data_array = array('cef_link' => $ceslink);
					$ress = mail_template($docket_no, $case_types, $data_array);	
					$expiry = $ress['expiry'];
					$subject_ces = $ress['sub'];
					$ces_message_mail = $ress['msg'];

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
	                $data_sms['v_category'] = $docket_no;
	                $data_sms['v_mobileNo'] = $caller_id;
	                $data_sms['v_smsString']= $ces_message_text;
	                $data_sms['V_Type']='Sms';
	                $data_sms['V_AccountName']=$fname;
	                $data_sms['V_CreatedBY']='';
	                $data_sms['i_status']='1';
	                $data_sms['i_expiry']=$expiry;                
	                insert_smsmessages($data_sms);
	                /*end - web_email_information_out*/
			}

			$interact_id="CALL-IE-".mysqli_insert_id($link);
			$_SESSION['ticket_arr'][] = $interact_id." ".$docket_no;
			echo json_encode(array('status' => 'success', "message" => "New remark added successfully", "session_val" => $_SESSION['ticket_arr'],"customerid" =>$customerid));die();
		}
		echo json_encode(array('status' => 'fail', "message" => "Remark not saved, please try after some time"));die();
	}
}


/*	
	Create case request from telephony popup page  
	Author : Vijay  
	05-12-2020
*/

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
	
	// echo json_encode($_POST); die();
/* Cerate New Case : Vijay Pippal */
if(isset($_POST['type']) && isset($_POST['Action']) && $_POST['Action'] =='Add'){
	//echo json_encode($_POST);die();

		foreach ($_POST as $key => $input_arr) {
		$_POST[$key] = addslashes(trim($input_arr));
	}

	$count=1; 
	extract($_POST); 
	error_log(print_r('POST DATA ', true));
	error_log(print_r($_POST, true));
	 
	$to_be_transfered  = isset($_POST['to_be_transfered']) ? 'true' : 'false' ;
	$caller_id 	=	$_POST['caller_id'];
	$agent 		=	$_POST['agent'];
	$customerid =	$_POST['customerid'];
	$lead_id 	=	$_POST['lead_id'];
	$recording_file 	=	$_POST['file'];
	$vendor_lead_code =	$_POST['vendor_lead_code'];
	$fname = $first_name." ".$last_name;

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
	if($first_name==''){
		echo json_encode(array('status' => 'fail', 'error' => '0', "message" => "Firts Name can not be blank!"));die();
	}
	else if($v_category==''){
		echo json_encode(array('status' => 'fail', 'error' => '0', "message" => "Please Select Category !"));die();
	}else if($v_subcategory==''){
		echo json_encode(array('status' => 'fail', 'error' => '0', "message" => "Please Select Sub Category !"));die();
	}
	else if($phone==''){
		echo json_encode(array('status' => 'fail', 'error' => '0', "message" => "Caller Number can not be blank!"));die();
	}
	else{

		// For New Case Customer Id Not Getting to Go to else in case of case creation 
		if(isset($_REQUEST['customerid']) && $_REQUEST['customerid']!=''){
			
			$customerid =  $_REQUEST['customerid']; 
			$q=mysqli_fetch_row(mysqli_query($link,"select count(AccountNumber) from $db.web_accounts where ((email = '$email' and email!='') OR (phone = '$phone' and phone!='' and phone!='' and phone!='NA' and phone!='Na' and phone!='na')   OR (twitterhandle = '$twitterhandle' and twitterhandle!='') OR (fbhandle = '$fbhandle' and fbhandle!='')  and AccountNumber!='$customerid';"));
			if($q[0]<=0){	
				$sqlupdate = "update $db.web_accounts set fname='$fname' , address='$address' , district='$district' , phone='$phone' , mobile='$mobile' , v_Village='$villages' , v_Location='$v_Location' ,  gender='$gender' , updatedate=NOW() , userid='$useridd', service_provider='$mr', project_type='$type', twitterhandle='$twitterhandle',fbhandle='$fbhandle',email='$email',alternate_email='$alternate_email',priority_user='$priority_user',nationality='$nationality',smshandle='$smshandle',whatsapphandle='$whatsapphandle',town='$town',area='$area',passport_number='$passport_number',business_number='$business_number',tpin='$tpin' where AccountNumber='$customerid'"; 
				$final_action ="$name User Update $process_case_type";
				mysqli_query($link,$sqlupdate) or die("Error In Query ".mysqli_error($link));
				add_audit_log($vuserid, 'update', $ticketid, json_encode($_REQUEST), $db);
			}
			else{ 
				// $count=2; 
				// $msg="Caller Number already exists!";  
				// $final_action ="$name User Cutomer Update Fail  $process_case_type  $msg";
				// add_audit_log($vuserid, 'update_Fail', $ticketid, json_encode($_REQUEST), $db);
			}
		}
		else{
			
			$query="select AccountNumber from $db.web_accounts where ((email = '$email' and email!='') OR (phone = '$phone' and phone!='' and phone!='' and phone!='NA' and phone!='Na' and phone!='na')   OR (twitterhandle = '$twitterhandle' and twitterhandle!='') OR (fbhandle = '$fbhandle' and fbhandle!='') ) ;";
			$result = mysqli_query($link,$query) ;
			$rows 	= mysqli_fetch_assoc($result);
			//echo '<br>Num Rows:'.$q1[0];	
			$customerid = $rows['AccountNumber'];		
			
			if(mysqli_num_rows($result) <= 0){
			
				//echo "<br>Check Customer if NOT exist <br>";
				$todaydate=date("Y-m-d H:i:s");
               $name = $first_name." ".$last_name;
			  $sqlinsert = " insert into $db.web_accounts(fname, createddate, address,v_Location, district, phone, mobile,country,fbhandle,twitterhandle,email,alternate_email,v_Village,gender,age_grp,priority_user,v_passwd,nationality,smshandle,whatsapphandle,town,area,passport_number,business_number,tpin) values('$name','$todaydate','$address_1','$address_2','$district','$phone','$mobile','$country','$fbhandle','$twitterhandle','$email','$alternate_email','$villages','$gender','$age','$priority_user','$first_name','$nationality','$smshandle','$whatsapphandle','$town','$area','$passport_number','$business_number','$tpin')";
			  	mysqli_query($link,$sqlinsert) or die("Error In Query ".mysqli_error($link));
				$customerid = mysqli_insert_id($link);
				$final_action ="$name User Create New Cutomer In Process $process_case_type  And Cutomer Id is $fname";
				add_audit_log($vuserid, 'create_customer', $customerid, json_encode($_REQUEST), $db);
			}
			else{ 
				
				$name = $first_name." ".$last_name;
				mysqli_query($link,"update $db.web_accounts set fname='$name' , address='$address_1' , district='$district' , phone='$phone' , mobile='$mobile' ,  country='$country' ,  updatedate=NOW() ,service_provider='$mr', v_Location='$address_2', twitterhandle='$twitterhandle',fbhandle='$fbhandle',email='$email', gender = '$gender' , age_grp= '$age',priority_user='$priority'  where AccountNumber='$customerid' ; "); 
				$final_action ="$name User Update ";
				add_audit_log($vuserid, 'update', $ticketid, json_encode($_REQUEST), $db);
				/*************/
			}
		}
	}


	$currentdate	=date("Y-m-d H:i:s");
	$feedback 		= isset($_POST['feedback']) ? $_POST['feedback'] : '' ;
	$status_type_ 	= isset($_POST['feedback']) ? '3' : $_POST['status_type_'] ;
	$v_remark_type 	= test_input($v_remark_type) ;

	/*
		
	$last_ticket_id  = last_ticket_id_categorywise($v_category) ;
	$last_ticket_no = !empty($last_ticket_id) ? $last_ticket_id+1 : 1 ;
	$ticketid= getticket($last_ticket_no, $v_category);
*/ 

	// Above code Update For Zicta : vijay: 14-02-2022 Remove category wisecase

	// lock
	mysqli_query($link,"LOCK TABLES $db.web_problemdefination WRITE;");


		$ticketid = getticket();
		$lang = ( empty($lang) || $lang=='0' ) ? '1' : $lang;
		$dfs = (empty($dfs)) ? '' : $currency." ".$dfs; 
		error_log(print_r(" PRINT TICKED FROM IVR ".$ticketid,true )) ;
		$sql = "insert into  $db.web_problemdefination(vCustomerID,vCaseType, vCategory, vSubCategory, iCaseStatus,vProjectID, vRemarks, ticketid, d_createDate, d_updateTime, customer_feedback , i_source, call_type, priority,  last_case_id, organization, i_CreatedBY,language_id,mno,isp,dfs,perpetrator,affected,service,complaint_type,root_cause,corrective_measure,regional,customertype) values('$customerid', '$type', '$v_category', '$v_subcategory','$status_type_','$group_assign', '$v_remark_type', '$ticketid', '$currentdate','$currentdate', '$feedback', '$source', '$call_type', '$priority', '', '$organisation', '$vuserid','$lang','$mno','$isp','$dfs','$perpetrator','$affected','$servicepro','$comp','$root_cause','$corrective_measure','$regional','$customertype') " ;
		mysqli_query($link,$sql) or die("Error In Query ".mysqli_error($link));
		error_log(print_r('LOGIN PAGE '. $sql, true)); 

	mysqli_query($link, "UNLOCK TABLES;");
	//unlock

	 
	$ticket = mysqli_insert_id($link);

	//create pdf
	include_once('dom_pdf.php'); 

	if($ticket > 0){
		
		
		mysqli_query($link,"insert into $db.web_case_interaction (caseID, custmer_id, remark, created_date, created_by, mode_of_interaction, recording_filename, vendor_lead_code, lead_id, language, caller_id, current_status, interacation_type) values ('$ticketid','$customerid','$v_remark_type','$currentdate', '$logedin_agent', '$source', '$recording_file', '$vendor_lead_code', '$lead_id', '$lang', '$caller_id','$status_type_', 'Remark') ") or die("Error In Query IN WEB CASE  ".mysqli_error($link));

		  // echo "CHECK IF CONDITION";

		// $interact_id="CALL-IE-".mysqli_insert_id($link);
		$interact_id=mysqli_insert_id($link);

		//$_SESSION['ticket_arr'][] = $interact_id;
		/*************END OF UPDATING QUEUEE************/

		//Add a ticket ID to ticket session variable.
		//$_SESSION['ticket_arr'][] = $ticketid;
		//echo "CHECK IF CONDITION WITH INSRET ID $ticketid"; die();

		//$case_assign_to = !($assignto) ? assignto($assignto) : !($assignt2) ? assignto($assignto2) : 'NO Assign In PR' ;
		/********Audit entry******************/
		//$user_id=$_SESSION['userid'];
		/*$actionlog="New case-".getTypeName($type)." and ticket id is".$ticketid;
		$type_log="NEW CASE";
		$comments="New case created for the type ".getTypeName($type)." and ticket $ticketid for the customer $fname and Case Assign to $case_assign_to"  ;

		$final_action ="$name User Create New Case For for the customer $fname  And ticket $ticketid IN $process_case_type";*/
        //echo json_encode($_POST);die();
		add_audit_log($vuserid, 'new_case_create', $ticketid, json_encode($_REQUEST), $db);
		/********Audit entry******************/
        
        //echo "code is working";
        //exit();
	
         
		// NOTIFICATIONS 

		/******* EMAIL SMS*****************/
		$from='rajdubey.alliance@gmail.com';
		$todaytime=date("Y-m-d H:i:s");
		/* Caller Email Section Start */
		//echo "call  test";
		// $subject = "New case "."[TICKET # $ticketid]";
		if(!empty($email) && $to_be_transfered == 'false' ){

			//echo "email";				
			if($type=='complaint' || $type=='Inquiry'){
				if ($type == 'complaint') {
					if ($status_type_ == '1') {
					   $case_type = 'com_new_case';
					} else if ($status_type_ == '8') {
					   $case_type = 'com_resolved';
					}else if ($status_type_ == '3') {
					   $case_type = 'com_close_case';
					}
				 } else if ($type == 'Inquiry') {
					if ($status_type_ == '1') {
					   $case_type = 'inquiry_new_case';
					} else if ($status_type_ == '8') {
					   $case_type = 'inquiry_resolved';
					} else if ($status_type_ == '3') {
					   $case_type = 'inquiry_close_case';
					}
				 }

				$res = mail_template($ticketid, $case_type, $data=[]);
				
				$subject = $res['sub'];
				$message = $res['msg'];
				$expiry = $res['expiry'];
				//$ticketname = str_replace('/', '-', $ticketid);
				$path = '../CRM/pdf/'.$ticketid.'.pdf';

				/*Aarti-23-11-23
                insert data in web_email_information_out table and 
                replce the insert code and add new function for insert code*/
                $data_email=array();
                $data_email['Mail'] = $email;
                $data_email['from']= $from;
                $data_email['V_Subject']=$subject;
                $data_email['V_Content']=$message;
                $data_email['ICASEID']=$ticketid;
                $data_email['expiry']=$expiry;
                $data_email['V_rule']=$path;
                $data_email['view']='New Case Call';
                insert_emailinformationout($data_email);
                /*end - web_email_information_out*/
			}

			if(!empty($phone)){

				if($status_type_ == '1')
				{
				   $sms_type = 'new_case';
 
				}else if($status_type_ == '8')
				{
				   $sms_type = 'resolved_case';
 
				}else if($status_type_ == '3')
				{
				   $sms_type = 'closed_case';
				}
			  

				$customer_name=ucwords($fname);
				$data_arr = array("name"=>$customer_name);
				$res_sms = sms_template($ticketid,$sms_type,$data_arr);
				$message = $res_sms['msg'];
				$expiry = $res_sms['expiry'];

 
				//  $sql_sms="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status,i_expiry) values ('$ticketid','$phone','$message','Sms','$customer_name','',NOW(), '1','$expiry')";
				// $result_sms= mysqli_query($link,$sql_sms) or die("Error In Query24 ".mysqli_error($link));
				/*Aarti-23-11-23
                insert data in tbl_smsmessages table*/
                $data_sms=array();
                $data_sms['v_category'] = $ticketid;
                $data_sms['v_mobileNo'] = $phone;
                $data_sms['v_smsString']= $message;
                $data_sms['V_Type']='Sms';
                $data_sms['V_AccountName']=$customer_name;
                $data_sms['V_CreatedBY']='';
                $data_sms['i_status']='1';
                $data_sms['i_expiry']=$expiry;                
                insert_smsmessages($data_sms);
                /*end - web_email_information_out*/
			 }
		}

         



		
		/* Admin Email Section Start */
		if($status_type_ =='1' && $to_be_transfered == 'false'):
			if($type=='complaint'){
				$data_arr = array('name' => $fname, 'mobile' => $caller_id, 'email' =>$email, 'category' =>category($v_category), 'sub_category' =>  department($group_assign) );
				
				$case_type ='assign_new_case' ;
				$res = mail_template($ticketid, $case_type, $data_arr);
				$subject = $res['sub'];
				$admin_message = $res['msg'];
				$expiry = $res['expiry'];
				// $ticketname = str_replace('/', '-', $ticketid);

				$path = '../CRM/pdf/'.$ticketid.'.pdf';

				$admin_query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $dbname.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$group_assign' " ;
				$admin_res = mysqli_query($link,$admin_query) or 'ERROR '. mysqli_error($link);
				if(mysqli_num_rows($admin_res) > 0){
					while ($adminrow = mysqli_fetch_assoc($admin_res)) {
						$email = $adminrow['V_EmailID'] ; 

						/*Aarti-23-11-23
		                insert data in web_email_information_out table and 
		                replce the insert code and add new function for insert code*/
		                $data_email=array();
		                $data_email['Mail'] = $email;
		                $data_email['from']= $from;
		                $data_email['V_Subject']=$subject;
		                $data_email['V_Content']=$admin_message;
		                $data_email['ICASEID']=$ticketid;
		                $data_email['expiry']=$expiry;
		                $data_email['V_rule']=$path;
		                $data_email['view']='New Case Call';
		                insert_emailinformationout($data_email);
		                /*end - web_email_information_out*/
					}
				}
			}

			
		endif;	
		/* Admin Email Section Close */
		/*******EMAIL-SMS*****************/

		

		echo json_encode(array('status' => 'success', "message" => "New Case Created Successfully", "docket_no" =>$ticketid, "session_val" => $_SESSION['ticket_arr'],"customerid" =>$customerid ,"registerno" =>$phone ));die();
		

		/*******EMAIL-SMS*****************/
	}
	else {
		$msg ="Due To Some Technical Issue Record Not Saved, Please try Again After Some Time.";
		$final_action ="$name User Create New Case Fail  For Customer $fname IN $process_case_type";
		add_audit_log($vuserid, 'new_case_create_issue', $ticket, json_encode($_REQUEST), $db);
		echo json_encode(array('status' => 'fail', "message" => "Due To Some Technical Issue Record Not Saved, Please try Again After Some Time"));die();
	}

	



	if( isset($_POST['docket_no']) && !empty($_POST['docket_no'])){
		$docket_no 			= $_POST['docket_no'] ;
		$source 			= $_POST['source_id'] ;
		$customer 			= $_POST['customer_id'] ;
		$status_type_ 		= $_POST['status_type_'] ;
		$customer_remark 	= $_POST['interaction_remark'] ;

		$update_sql = "update $db.web_problemdefination set iCaseStatus='$status_type_' where ticketid='$docket_no'" ;
		mysqli_query($link,$update_sql);


		$sql = "insert into $db.web_case_interaction (caseID, custmer_id, mode_of_interaction, current_status, remark, created_date, created_by,  recording_filename, vendor_lead_code, lead_id, language, caller_id, interacation_type) values ('$docket_no', '$customer', '$source', '$status_type_', '$customer_remark', '$todaydate', '$logedin_agent',  '$recording_file', '$vendor_lead_code', '$lead_id', '$lang', '$caller_id', 'Remark')" ;
		if(mysqli_query($link,$sql)){
            
            $interact_id="CALL-IE-".mysqli_insert_id($link);
			$_SESSION['ticket_arr'][] = $interact_id." ".$docket_no ;

			echo json_encode(array('status' => 'success', "message" => "New remark Added Successfully","customerid" =>$customerid, "session_val" => $_SESSION['ticket_arr']));die();
		}
		//echo json_encode(['status' => 'fail', "message" => "Remark not saved, please try after some time"]);die();
	}
}


/*Get Case details by docket no */

if(isset($_POST['docket_no'])){
	$docket_no = trim($_POST['docket_no']);
	if(!empty($docket_no)){
		$query = "select * from $db.web_accounts a, $db.web_problemdefination p where a.AccountNumber=p.vCustomerID  and ticketid='$docket_no'  " ;
		$res = mysqli_fetch_assoc(mysqli_query($link,$query));
		echo json_encode(array('status' => 'success', 'casedetails' => $res, "message" => "getting Case details.","customerid" =>$customerid));die();
	}
}

/*
	Check case Status Working OR Not
*/

if(isset($_POST['work_status'])){
	// echo json_encode($_POST);die();
	$docket_no = trim($_POST['work_status']);
	$user_id = trim($_POST['user_id']);
	$case_id = trim($_POST['case_id']);
	
	if(!empty($docket_no))
	{
		 $query = "select iPID, ticketid, work_status, work_timestamp,current_working_agent, iCaseStatus FROM $db.web_problemdefination where ticketid='$docket_no' AND iPID = '$case_id'  " ;
		$res = mysqli_fetch_assoc(mysqli_query($link,$query));
		$id = $res['iPID'];
		$update_by = $res['current_working_agent'];
		$ticketid = $res['ticketid'];
		$work_status = $res['work_status'];
		$iCaseStatus = $res['iCaseStatus'];
		$work_timestamp = $res['work_timestamp'];

		if($iCaseStatus == '3'){
			echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
		}
		// echo json_encode(array('status' => 'success', 'casedetails' => $work_status ));die();
		if( ($work_status == '0' && $update_by == '') || ($work_status == '0' && $update_by == $vuserid) ) {
			$sql_query = "update $db.web_problemdefination set work_status='1',   work_timestamp = '$todaydate', current_working_agent ='$user_id' where IPID='$case_id' " ;
			mysqli_query($link,$sql_query);
			echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
		}
		else if( ($work_status == '0' && $update_by == '0') || ($work_status == '1' && $update_by == $vuserid) ) {
			$sql_query = "update $db.web_problemdefination set work_status='1',   work_timestamp = '$todaydate', current_working_agent ='$user_id' where IPID='$case_id' " ;
			mysqli_query($link,$sql_query);
			echo json_encode(array('status' => 'success', "message" => "Allow To update Case" ));die();
		} 
		else if($work_status == '1' && $update_by != $vuserid){
			echo json_encode(array('status' => 'fail', "message" => "Someone is working on this case, You are not allow to update. " ));die();

		}
		//echo json_encode(array('status' => 'fail', 'casedetails' => $res, "message" => "getting Case details.","customerid" =>$customerid));die();


	}
}		

/*
	Get Villages List based on district
	: Vijay Pippal : 16-07-2021
*/
#########################################################################     
#############
#########################################################################

// get District  list

if(isset($_POST['district_id']) && $_POST['district_id'] > 0){
	$district_id = trim($_POST['district_id']);
	$query = "select id, vVillage  FROM $db.web_Village WHERE iDistrictID='$district_id' AND status ='1' ORDER BY vVillage ASC  " ;
	$res = mysqli_query($link,$query);
		while ($row = mysqli_fetch_assoc($res)) {
			$resp[] = $row;
		}
	echo json_encode($resp);die();
}


if(isset($_POST['category_id2']) && $_POST['category_id2'] > 0){
	$category_id = trim($_POST['category_id2']);
	 $query = "select pId, vProjectName  FROM $db.web_projects WHERE FIND_IN_SET($category_id,Type) AND i_Status ='1' " ;
	$res = mysqli_query($link,$query);
	while ($row = mysqli_fetch_assoc($res)) {
		$resp[] = $row;
	}
	echo json_encode($resp);die();
}


// get List Back Office List 

if(isset($_POST['department_id']) && $_POST['department_id'] > 0){
	$department_id = trim($_POST['department_id']);
	 $query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $dbname.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$department_id'  " ;
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
