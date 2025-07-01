<?php
/***
 * Cases List
 * Author: Aarti Ojha
 * Date: 04-03-2024
 * This file is handling fetching Cases List display in helpdesk page
 **/
include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
// fetch user details
include "../web_function.php";
if($_POST['action'] == 'Update_Case'){
	update_case_details();
}
if($_POST['action'] =='web_assign'){
	web_assign(); // check assign 
}
if($_POST['action'] =='helpdesk_list'){
	helpdesk_list();
}
// for delete ticket acess only superadmin
if($_POST['action'] == 'delete_ticket'){
	TicketarchiveAndDelete();
}

/* Modified By Farhan on 27-06-2024 */
$user_id = $_SESSION['userid'];
$groupid = $_SESSION['user_group'];
/* End */

/********************************************************************************************************
* this file used for fetch ticket list and create ticket,update ticket
********************************************************************************************************/
function helpdesk_list(){
	global $db,$link,$SiteURL,$groupid,$Agent_groupId,$Backoffice_groupId,$Superviouser_groupId,$Admin_groupId,$NonLogin_groupId;
	$rspoc = $_SESSION['reginoal_spoc'];
	$user_id = $_SESSION['userid'];
	$column = ['ticketid', 'vCustomerID', 'vCategory', 'vSubcategory', 'vProjectID', 'i_source', 'iCaseStatus', 'd_createDate'];
	
	// Agent Login
	// Agent Login
	if ($groupid == $Agent_groupId) {
		$query = "SELECT * FROM web_problemdefination 
				LEFT JOIN web_accounts ON web_problemdefination.vCustomerID = web_accounts.AccountNumber 
				WHERE web_problemdefination.iPID <>'' ";

	} else if ($groupid == $Backoffice_groupId) {  // Back office Branch login (SECOND LEVEL) 
		$query = "SELECT * FROM web_problemdefination 
				LEFT JOIN web_accounts ON web_problemdefination.vCustomerID = web_accounts.AccountNumber 
				LEFT JOIN web_project_assigne ON web_problemdefination.vProjectID = web_project_assigne.project_id 
				WHERE web_problemdefination.vProjectID IN (
					SELECT project_id FROM web_project_assigne  
					WHERE web_project_assigne.user_id = '$user_id' 
				)";

	} else if ($groupid == $NonLogin_groupId) {  // Branch Manager Login Last Level
		// In Branch Manager Login, Escalated Cases Are Shown By Default
		$query = "SELECT * FROM web_problemdefination 
				LEFT JOIN web_accounts ON web_problemdefination.vCustomerID = web_accounts.AccountNumber 
				LEFT JOIN web_project_assigne ON web_problemdefination.vProjectID = web_project_assigne.project_id 
				WHERE web_problemdefination.vProjectID IN (
					SELECT project_id FROM web_project_assigne  
					WHERE web_project_assigne.user_id IN (
						SELECT UserID FROM userhead AS uh 
						WHERE uh.HeadID = '$user_id' 
					)
				)";

	} else if ($groupid == $Superviouser_groupId) {  // B6 (SUPERVISOR) CRM ADMIN
		// [Vastvikta][12-02-2025] - Fixed incorrect WHERE clause in the subquery
		$query = "SELECT * FROM web_problemdefination 
				LEFT JOIN web_accounts ON web_problemdefination.vCustomerID = web_accounts.AccountNumber 
				LEFT JOIN web_project_assigne ON web_problemdefination.vProjectID = web_project_assigne.project_id 
				WHERE web_problemdefination.vProjectID IN (
					SELECT project_id FROM web_project_assigne  
					WHERE project_id IS NOT NULL
				) 
				AND web_problemdefination.iPID <>'' ";

	} else {  // OVERALL SUPERVISOR (AS SYSTEM ADMIN)
		$query = "SELECT * FROM web_problemdefination 
				LEFT JOIN web_accounts ON web_problemdefination.vCustomerID = web_accounts.AccountNumber 
				WHERE web_problemdefination.iPID <>'' ";
	}

	// Apply Filters
	if (isset($_POST['filter_status']) && !empty($_POST['filter_status'])) {
		$query .= ' AND iCaseStatus = "' . $_POST['filter_status'] . '"';
	}
	if (isset($_POST['filter_priority']) && !empty($_POST['filter_priority'])) {
		$query .= ' AND priority = "' . $_POST['filter_priority'] . '"';
	}
	if (isset($_POST['filter_source']) && !empty($_POST['filter_source'])) {
		$query .= ' AND i_source = "' . $_POST['filter_source'] . '"';
	}
	if (isset($_POST['filter_priority_user']) && !empty($_POST['filter_priority_user'])) {
		if ($_POST['filter_priority_user'] == 1) {
			$query .= ' AND priority_user = "' . $_POST['filter_priority_user'] . '"';
		} else {
			$query .= " AND priority_user ='0'";
		}
	}

	// Search Functionality
	$search = $_POST['search_ticket'];
	if (isset($search) && !empty($search)) {
		$query .= " AND ( ticketid LIKE '%" . $search . "%' || fname LIKE '%" . $search . "%') ";
	}

	// Ordering Logic
	if (isset($_POST['order'])) {
		$query .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
	} else {
		$query .= ' GROUP BY ticketid ORDER BY ticketid DESC ';
	}

	// Pagination
	$query1 = '';
	if ($_POST["length"] != -1) {
		$query1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}

	$ticket_query =  mysqli_query($link, $query . $query1);
	$number_filter_row = mysqli_num_rows(mysqli_query($link, $query));
	
	$data = array();
	while ($row = mysqli_fetch_array($ticket_query)){
	    $ticked_status = $row['iCaseStatus'];
	    $ticket = $row['ticketid'];
	    $closure_pdf = '';
	    $id = $row['iPID'];
	    $web_case_detail = base64_encode('web_case_detail');
		 $ref = $web_case_detail."?id=base64_encode($ticket)";
	    if ($ticked_status == 1) {                // Pending
	        $color_code = 'crimson';
	        $text_color = '#f3f1f1';
	     }else if ($ticked_status == 3) {           // closed    
	        $color_code = '#00A36C';
	        $text_color = '#f3f1f1';
	        $closure_pdf = "<a href='closure_pdf.php?id=".$ticket."' style='color:#222222'><i class='fa fa-download' style=''></i></a>";
	     } else if ($ticked_status == 4) {          // escalated
	        $color_code = '#FFBF00';
	        $text_color = '#000000';
	     } else if ($ticked_status == 8) {          // resolved
	        $color_code = '#e1ba02';
	        $text_color = '#000000';
	     } 

	     if($row['priority_user']=='1')
	     {
	       $color = "color:#CFB53B;";
	     }else
	     {
	       $color="color:cadetblue";
	     }

	     if($row['priority'] == 'high' || $row['priority'] == 'extremelyhigh'){
	        $img = "<img src='".$SiteURL."/public/images/icons8-priority-48.png' class='img_priorty'>";
	     }else if($row['priority'] == 'medium'){
	        $img = "<img src='".$SiteURL."/public/images/icons8-priority-48 (1).png' class='img_priorty'>";
	     }else if($row['priority'] == 'low'){
	        $img = "<img src='".$SiteURL."/public/images/icons8-priority-48 (2).png' class='img_priorty'>";
	     }
	     $url_ticket = "<span><a href='javascript:void(0);' onclick='return check_working_status(".$ticket.",".$id.",".$user_id.")' style='color: ".$color_code.";font-weight: 700;'>".$ticket."</a></span>";
	     $delete_html;
	     if($groupid == '0000'){
	     	$delete_html = "<span><a href='javascript:void(0);' onclick='return check_delete_action(".$ticket.",".$id.",".$user_id.")' style='color: ".$color_code.";font-weight: 700;' data-id='".$ticket."'><i class='fa fa-trash'></i></a>";
	     }

	 $sub_array = array();
	 $sub_array[] = $url_ticket.$img.$closure_pdf;
	 $sub_array[] = "<span style='".$color.";font-weight: 700;'>".getfname($row['vCustomerID'])."</span>";
	 $sub_array[] = category($row['vCategory']);
	 $sub_array[] = subcategory($row['vSubCategory']);
	 $sub_array[] = project($row['vProjectID']);
	 $sub_array[] = source($row['i_source']);
	 $sub_array[] = ticketstatus($row['iCaseStatus']);
	 $sub_array[] = date('d-m-Y H:i',strtotime($row['d_createDate']));
	 $sub_array[] = $delete_html;
	 $data[] = $sub_array;
	}
	$output = array(
	 "draw"       =>  intval($_POST["draw"]),
	 "recordsTotal"   =>  count_all_data(),
	 "recordsFiltered"  =>  $number_filter_row,
	 "data"       =>  $data
	);
	echo json_encode($output);
}
function count_all_data(){
 global $db,$link;
 $query = "SELECT * FROM web_problemdefination";
 $case_result = mysqli_query($link, $query); // get status filter option
 $total = mysqli_num_rows($case_result);
 return $total;
}
/********************************************************************************************************
* For Showing Cases as of status
********************************************************************************************************/
function get_status_list(){
	global $db,$link;
	$case_query = "SELECT * FROM $db.web_ticketstatus WHERE status =1 ";
	$case_result = mysqli_query($link, $case_query); // get status filter option
	return $case_result;
}
/********************************************************************************************************
* For Showing Priority User and Case Priority High total count
********************************************************************************************************/ 
function get_priority(){
	global $db,$link,$Agent_groupId,$Backoffice_groupId,$Superviouser_groupId,$Admin_groupId,$NonLogin_groupId,$General_groupId,$groupid;
	
	// this code depend group id (login user according change query condi)
	if ($groupid == $Agent_groupId || $groupid == $Superviouser_groupId) {
		$total_recd = "select COUNT(p.priority) as case_priority_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID and p.priority='high'";
	    $user_total_recd = "select COUNT(a.priority_user) as priority_user_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID and a.priority_user='1'";
	} else if ($groupid == $Backoffice_groupId) {    //  Back office Branch login (SECOND LEVEL ) // 20-05-2020
		$total_recd = "select COUNT(p.priority) as case_priority_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID AND p.vProjectID IN (SELECT project_id FROM $db.web_project_assigne AS pas WHERE pas.user_id   ='" . $_SESSION['userid'] . "' ) and p.priority='high'";
	   $user_total_recd = "select COUNT(a.priority_user) as priority_user_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID and p.vProjectID IN (SELECT project_id FROM $db.web_project_assigne AS pas WHERE pas.user_id   ='" . $_SESSION['userid'] . "' ) and a.priority_user='1'";
	}else if ($groupid == $NonLogin_groupId) {       // Branch Manager Login Last Level 
	   // In Branch Manger Login Escalated Cases Are Showing... by default
	   $total_recd = "SELECT COUNT(priority) AS case_priority_total FROM $db.web_problemdefination WHERE priority='high'";
	   $user_total_recd = "SELECT COUNT(a.priority_user) AS priority_user_total  FROM $db.web_accounts a , $db.web_problemdefination p WHERE a.AccountNumber=p.vCustomerID and a.priority_user='1'";
	// } else if ($groupid == $Superviouser_groupId) {    //B6  ( SUPERVIOUSER ) 	CRM ADMIN 
	// 	$total_recd = "select COUNT(p.priority) as case_priority_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID and p.priority='high'";
	//    $user_total_recd = "select COUNT(a.priority_user) as priority_user_total  from ensembler.web_accounts a , ensembler.web_problemdefination p where a.AccountNumber=p.vCustomerID and a.priority_user='1'";
	} else { 
		$total_recd = "SELECT COUNT(priority) AS case_priority_total FROM $db.web_problemdefination WHERE priority='high'";
	   $user_total_recd =  "SELECT COUNT(a.priority_user) AS priority_user_total  FROM $db.web_accounts a , $db.web_problemdefination p WHERE a.AccountNumber=p.vCustomerID and a.priority_user='1'";
	}
	// total record case priorty
	$total_recd_query =  mysqli_query($link, $total_recd);
	$total_recd_query = mysqli_fetch_assoc($total_recd_query);

	// total record user priorty
	$usertotal_recd_query =  mysqli_query($link, $user_total_recd);
	$usertotal_recd_querys = mysqli_fetch_assoc($usertotal_recd_query);

	$user_info['total_recd_query'] = $total_recd_query;
	$user_info['usertotal_recd_querys'] = $usertotal_recd_querys;
	return $user_info;
}
/********************************************************************************************************
* For remove special chars in stringe func
********************************************************************************************************/
function test_input($data){
   $data = trim($data);
   $data = ltrim($data);
   $data = rtrim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
/********************************************************************************************************
* For get interaction id
********************************************************************************************************/
function create_interaction_id($mode, $case_no){
   $source = source($mode);   // mode name
   $last_id = get_last_modeOfInteraction($mode) + 1; // last mode id
   $interaction_id  = strtoupper($source) . '-IE-' . $last_id;
   return $interaction_id;
}
// get diff btw dates
function getDiffenceAfterCaseUpdate($old_data, $update_data){
   $result = array_diff($old_data, $update_data);   // Check Difference in Two Array 
   return json_encode($result);
}
/********************************************************************************************************
* For Update AnD Insert Case issues
********************************************************************************************************/
function insertAndUpdate_case_isseues($db, $ticked_id, $isses_id, $remarks){
   global $link;
   $sql_check_case = "SELECT i_IID FROM $db.web_Issues WHERE i_TID ='$ticked_id' AND i_IssueID ='$isses_id'  ";
   $result =  mysqli_query($link, $sql_check_case);
   if (mysqli_num_rows($result) > 0) {
      $case_query = "UPDATE $db.web_Issues SET v_Remarks ='$remarks' WHERE i_TID ='$ticked_id' AND i_IssueID ='$isses_id' ";
   } else {
      $case_query = "INSERT INTO $db.web_Issues (i_TID, i_IssueID, v_Remarks) VALUES ('$ticked_id', '$isses_id', '$remarks')";
   }
   mysqli_query($link, $case_query);
} 
/********************************************************************************************************
* For This will get information before update for the pourpose of find out the update changes
********************************************************************************************************/
function update_case_details(){
	global $db,$link,$Agent_groupId,$Backoffice_groupId,$Superviouser_groupId,$Admin_groupId,$NonLogin_groupId,$General_groupId,$from_email,$Closed_status,$Resolved_status,$Pending_status;
	    $name       = $_SESSION['logged'];
		$logedin_agent    =    $_SESSION['logged'];
		$vuserid   =   $_SESSION['userid'];
		$groupid    =   $_SESSION['user_group'];
	    extract($_POST);
	    if ($groupid == $Backoffice_groupId || $groupid == $Superviouser_groupId) {
	      $case_query = "SELECT * FROM $db.web_problemdefination WHERE  ticketid='$docket_no' ";
	    }else if ($groupid == $Admin_groupId) {
	       $case_query = "SELECT p.*,a.* FROM $db.web_problemdefination  p , $db.web_accounts  a WHERE a.AccountNumber=p.vCustomerID AND p.ticketid='$docket_no'  GROUP BY ticketid order by p.iPID  desc ";
	   	}
	    $case_result = mysqli_fetch_assoc(mysqli_query($link, $case_query));

	   //Get customer ID
	   $query = "select * from $db.web_accounts a, $db.web_problemdefination p where a.AccountNumber=p.vCustomerID  and ticketid='$docket_no' ";
		$res = mysqli_fetch_assoc(mysqli_query($link, $query));
		$customer = $res['vCustomerID'];

	    // get previous department.
	    $old_department =  $case_result['vProjectID'];
	    $vCaseType =  $case_result['vCaseType'];
	    $v_category =  $case_result['vCategory'];
	    $v_subcategory =  $case_result['vSubCategory'];
	    $todaydate = date("Y-m-d H:i:s");
	    $feedback = isset($_POST['feedback']) ? $_POST['feedback'] : '';
	    $status_type_ = isset($_POST['feedback']) ? '3' : $_POST['status_type_'];
	    $bll_assign = isset($_POST['bll_assign']) ? $_POST['bll_assign'] : '';
	    $backoffice_last_remark = isset($_POST['backoffice_last_remark']) ? $_POST['backoffice_last_remark'] : '';
	    $source = $_POST['source'];
	    $v_remark_type = $_POST['v_remark_type'];
	    $backoffice_last_remark = $_POST['backoffice_last_remark'];
	    $service = $_POST['service'];
	    $email = $_POST['emails'];
	    $mobile = $_POST['mobile'];
	    $phone = $_POST['phone'];
	    $root_cause = $_POST['root_cause'];
	    $corrective_measure = $_POST['corrective_measure'];
	    $first_name = $_POST['first_name'];
	    $last_name = $_POST['last_name'];
	    $name = $first_name . " " . $last_name;
	    $root_cause = $_POST['root_cause'];
		$corrective_measure = $_POST['corrective_measure'];

	    //update Problemdefinition table - group id condition added
	    if ($groupid == $Agent_groupId) {
	      $update_by = "agent";
	      $remark = $v_remark_type;
	      $sql = "update $db.web_problemdefination set iStatus ='1', iCaseStatus='$status_type_', vRemarks='$v_remark_type',customer_feedback ='$feedback',root_cause='$root_cause',corrective_measure='$corrective_measure' where vCustomerID='$customer' and ticketid='$docket_no'";
	    	// added  update status in audit report [vastvikta][12-02-2025]
			add_audit_log($vuserid, 'update_case_detail', $docket_no, 'Update case details ', $db);
			
		} else if ($groupid == $Backoffice_groupId) {
	      $exceptional_case = isset($exceptional_case) ? '1' : '0';  // handel case for 5days escalation
	      $remark = $backoffice_remark;
	      $update_by = "backoffice";
	      $update_arr['exceptional_case']   = $exceptional_case;
	      $update_arr['vProjectID']   = $group_assign;
	      $update_arr['iCaseStatus']    = $status_type_;
	      $update_arr['b5_remark']       = $backoffice_remark;
	      $update_arr['v_ActionSupervisor']      = test_input($_POST['v_ActionSupervisor']);
	      	if(isset($group_assign)){
	         	$str_department = ",vProjectID='$group_assign'";
	         	$update_arr['vProjectID']       = $group_assign;
	      	}	      
	      	$sql = "update $db.web_problemdefination set vCategory='$v_category',vSubCategory='$v_subcategory',vProjectID='$group_assign',exceptional_case='$exceptional_case', back_office_action_by='$vuserid', iStatus ='1',iCaseStatus='$status_type_',  b5_remark ='$backoffice_remark', customer_feedback ='$feedback', iAssignTo='$bll_assign' ,root_cause='$root_cause',corrective_measure='$corrective_measure' $str_department where vCustomerID='$customer' and ticketid='$docket_no'";
			// added  update status in audit report [vastvikta][12-02-2025]
			add_audit_log($vuserid, 'update_case_detail', $docket_no, 'Update case details ', $db);
			
		} else if ($groupid == $NonLogin_groupId) {
			$remark = $backoffice_last_remark;
			// this section work for BLL 
			$update_by = "Branch Manager";
			$sql = "update $db.web_problemdefination set back_office_action_by='$vuserid', iStatus ='1', iCaseStatus='$status_type_',  b6_remark ='$backoffice_last_remark', customer_feedback ='$feedback', iAssignTo='$bll_assign',root_cause='$root_cause',corrective_measure='$corrective_measure'  where vCustomerID='$customer' and ticketid='$docket_no'";
			// added  update status in audit report [vastvikta][12-02-2025]
			add_audit_log($vuserid, 'update_case_detail', $docket_no, 'Update case details ', $db);
		} else if ($groupid == $Superviouser_groupId) {
	      $remark = $supervisor_remark;
	      // this section work for BLL 
	      $update_by = "supervisor";
	      $update_arr['iCaseStatus']    = $status_type_;
	      $update_arr['v_ActionSupervisor']      = $supervisor_remark;
	      $sql = "update $db.web_problemdefination set back_office_action_by='$vuserid', iStatus ='1', iCaseStatus='$status_type_',  v_ActionSupervisor ='$supervisor_remark', customer_feedback ='$feedback', iAssignTo='$bll_assign',root_cause='$root_cause',corrective_measure='$corrective_measure'  where vCustomerID='$customer' and ticketid='$docket_no'";
	    	// added  update status in audit report [vastvikta][12-02-2025]
			add_audit_log($vuserid, 'update_case_detail', $docket_no, 'Update case details', $db);
			
		} else {
			$remark = $v_OverAllRemark;
			// this section work for BLL 
			$update_by = "case";
			$dfs = (empty($dfs)) ? '' : $currency." ".$dfs;  
			$feedback = (empty($feedback)) ? '' : $feedback;  
			$bll_assign = (empty($bll_assign)) ? '' : $bll_assign;  
			$v_categoryval = '';
			if(!empty($v_category)){
				$v_categoryval = ",vCategory='$v_category'";
			}
			$vSubCategoryval = '';
			if(!empty($v_subcategory)){
				$vSubCategoryval = ",vSubCategory='$v_subcategory'";
			}
			$sql = "update $db.web_problemdefination set vCaseType='$type' $v_categoryval $vSubCategoryval ,vProjectID='$group_assign', back_office_action_by='$vuserid', iStatus ='1', iCaseStatus='$status_type_',  v_OverAllRemark ='$v_OverAllRemark', customer_feedback ='$feedback', iAssignTo='$bll_assign',regional='$regional',root_cause='$root_cause',corrective_measure='$corrective_measure'  where vCustomerID='$customer' and ticketid='$docket_no'";
			if ($status_type_ == '1'){   // pending
				$status_new = 'pending';
			}elseif ($status_type_ == '2'){   // drop
				$status_new = 'drop';
			}elseif ($status_type_ == '3'){   // close	
				$status_new = 'Close';
			}else if ($status_type_ == '4'){   // escalte 
				$status_new = 'Escalate';
			}else if ($status_type_ == '5'){  // reopen
				$status_new = 'Reopen';
			}else if ($status_type_ == '8'){   // resolved
				$status_new = 'In Progress';
			}
			// added  update status in audit report [vastvikta][12-02-2025]
			add_audit_log($vuserid, 'update_case_detail', $docket_no, "Updated case status to $status_new", $db);
		}
	   $changed_action = '';

	   if($_POST['status_old'] != $_POST['inte_status_type_']){
	      $changed_action = 'status changed';
	   } 

	   if($old_department != $group_assign && !empty($group_assign)){
	      $changed_action = 'forward this department - '.department($group_assign);

		// insert records for caputuring department Assingnments for case :: farhan akhtar [16-04-2025]
		$new_time = date("Y-m-d H:i:s");
		$dataArr =[
			'case_id'     => $docket_no,
			'department'  => $group_assign,
			'new_time'    => $new_time,
			'category'    => $v_category,
			'subcategory' => $v_subcategory,
			'case_status' => $status_type_,
			'comment'     => 'Forwarded to other Department.',
			'remark'      => $new_remark
		];		  
		if (!forwardToDept($link, $dataArr)) {
			$errorMessage = "[" . date("Y-m-d H:i:s") . "] Failed to forward department for Case ID: $docket_no - Error: " . $link->error . PHP_EOL;
			file_put_contents("DeptlogEntryFailed.txt", $errorMessage, FILE_APPEND);
		}

	   }
	    //   $changed_action = 'remark added';
	   
	   if ($groupid == $Agent_groupId) {
	      $inert_remark = $v_remark_type;
	   } else if ($groupid == $Backoffice_groupId) {
	      $inert_remark = $backoffice_remark;
	   } else if ($groupid == $NonLogin_groupId) {
	      $inert_remark = $backoffice_last_remark;
	   } else if ($groupid == $Superviouser_groupId) {
	      $inert_remark = $supervisor_remark;
	   }else{
	      $inert_remark = $v_OverAllRemark;
	   }
	    if(!empty($changed_action)){
	      	$sql_insert = "insert into $db.web_case_interaction (caseID, custmer_id, remark, created_date, created_by, mode_of_interaction, interaction_id , current_status,action) values ('$docket_no', '$reginoal_spoc','$inert_remark','$todaydate', '$logedin_agent', '$source', '$ie_id', '$status_type_','$changed_action')" ;
	      	mysqli_query($link,$sql_insert);
	   	}
	    $sql_query = "update $db.web_problemdefination set work_status='0',  work_timestamp = '$todaydate', current_working_agent ='0' where ticketid='$id' ";
	    mysqli_query($link, $sql_query);
		// added  update status in audit report [vastvikta][12-02-2025]
		//add_audit_log($vuserid, 'update_work_status', $docket_no, 'Update work status', $db);
		
	   //Query trigger
		if (mysqli_query($link, $sql)) {
		      // get Last Interaction ID and create new  
		      $ie_id = create_interaction_id($source, $docket_no);
		      /*******CASE NOTIFICATION EMAIL-SMS*****************/
		    //   $todaytime = date("Y-m-d H:i:s"); 
		      $status = $status_type_;
		    /* Admin Email Section Start Again Mail to Department in case changed */
		    $todaytime = date("Y-m-d H:i:s");
		    if ($old_department != $group_assign){
		        if ($vCaseType == 'complaint') {
		            $data_arr = ['name' => $name, 'phone' => $phone, 'email' => $email, 'category' => category($v_category), 'sub_category' => department($group_assign)];            
		            $case_type = 'reassign_new_case';
		            $res = get_email_template($ticketid, $case_type, $data_arr, $assign_to = 'true');
		            $subject = $res['sub'];
		            $admin_message = $res['msg'];
		            $admin_query = "select user.V_EmailID FROM $db.web_project_assigne AS dept INNER JOIN $dbname.tbl_mst_user_company AS user ON dept.user_id = user.I_UserID where dept.project_id='$group_assign' ";
		            $admin_res = mysqli_query($link, $admin_query) or 'ERROR ' . mysqli_error($link);
		            if (mysqli_num_rows($admin_res) > 0) {
		                while ($adminrow = mysqli_fetch_assoc($admin_res)) {
		                  $email = $adminrow['V_EmailID'];
		                  /*Aarti-23-11-23
		                    insert data in web_email_information_out table and 
		                    replce the insert code and add new function for insert code*/
		                    $data_email= [];
		                    $data_email['Mail'] = $email;
		                    $data_email['from']= $from_email;
		                    $data_email['V_Subject']=$subject;
		                    $data_email['V_Content']=$admin_message;
		                    $data_email['ICASEID']=$ticketid;
		                    $data_email['V_rule']=$path;
		                    $data_email['view']='New Case Assigned';
		                    insert_emailinformationout($data_email);
		                    /*end - web_email_information_out*/
		               }
		            } 
		        }
		    }

		    /* Admin Email Section Close */
		   	/* Send sms and email code here */
		    if ($status == $Pending_status || $status == $Resolved_status || $status == $Closed_status) {// case resolved
		        if (isset($_POST['email'])) {
		        	interaction_remark_backoffice($status,$email,$docket_no,$name,$phone);
		        }
		    }
		   /*******CASE NOTIFICATION EMAIL-SMS*****************/

		   if($status == $Closed_status) {
			$dataArrr = [
							'case_id'  => $docket_no,
							'datetime' => $todaytime
						];
			if (!closeCase($link, $dataArrr)) {
				$errorMessage = "[" . date("Y-m-d H:i:s") . "] Failed to closed Department for Case ID: $docket_no - Error: " . $link->error . PHP_EOL;
				file_put_contents("DeptlogEntryFailed.txt", $errorMessage, FILE_APPEND);
			}
		   }

	      echo json_encode("$docket_no Successfully updated");
	    } else {
	        echo json_encode("$docket_no Not update Please try Again");
	    }
	/***********************
	 End of Update Section 
	***********	************/
}
function interaction_remark_backoffice($status_type_,$c_email,$docket_no,$fname,$c_mobile){
	global $dbname,$from_email,$db,$link,$SiteURL,$groupid,$Pending_status,$Resolved_status,$Closed_status;
	$vuserid        =  $_SESSION['userid'];
	$logedin_agent    =    $_SESSION['logged'];
	$type           	=$_POST['type'];
	$customerid 		= $_POST['customer_id'] ;
	$group_assign = $_POST['group_assign'];
	/* Send Email and SMS To Customer If Case Resolved */
	// case resolved
	if($status_type_ == $Resolved_status){
		if(isset($_POST['emails']) && !empty($c_email)){
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
            // add_audit_log($vuserid, 'mail_sent', $docket_no, 'Mail Sent To Customer For Resolved Case to '.$c_email, $db);   
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

	}else if($status_type_ == $Pending_status && $_POST['status_old'] == $Closed_status){	
		if(isset($_POST['emails']) && !empty($c_email)){
			$case_type = 'com_reopen_case';											
			$res = mail_template($docket_no, $case_type, $data=[]);
			$subject = $res['sub'];
			$message = $res['msg'];
			$expiry = $res['expiry'];
			$from = $from_email;
			$data_email=array();
            $data_email['Mail'] = $c_email;
            $data_email['from']= $from_email;
            $data_email['V_Subject']=$subject;
            $data_email['V_Content']=$message;
            $data_email['ICASEID']=$docket_no;
            $data_email['i_expiry']=$expiry;
            $data_email['view']='Case Updated';
            insert_emailinformationout($data_email);  
		}
		if(!empty($c_mobile)){
			$sms_type = 'reopen_case';
			$customer_name=ucwords($fname);
			$data_arr = array("name"=>$customer_name);
			$res_sms = sms_template($docket_no,$sms_type,$data_arr);
			$message = $res_sms['msg'];
			$expiry = $res_sms['expiry'];
			$data_sms=array();
			$v_category = '';
            $data_sms['v_category'] = $docket_no;
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
        $data_arr = array('name' => $fname, 'mobile' => $c_mobile, 'email' =>$c_email, 'category' =>category($v_category), 'sub_category' =>  department($group_assign));
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
	if($status_type_ == $Closed_status){
		// $module_flag_customer = module_license('NPS&CustomerEfforts'); // check linces avaiable or not
		// if($module_flag_customer == '1' ){
			include("../common_function.php");
			$common_function = new common_function();
			$email = $_POST['email'];
			$docket_no = trim($_POST['docket_no']);
			$caller_id = $c_mobile;
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
			$npsid = $common_function->addNpsFeddback($npsfeedback_data,$_SESSION['companyid']);
			//Send Customer Effort Score (CES) link 
			$cesid = $common_function->addCES($npsfeedback_data,$_SESSION['companyid']);
			$from=$from_email;
			$case_types = 'nps&customer_effort_feedback';
			$nps_ceslink = $SiteURL.'CRM/CES_NPS_feedback.php?npsid='.$npsid.'&cesid='.$cesid;

			$data_array = array('nps_ceslink' => $nps_ceslink);
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
        // }
	}
}
/********************************************************************************************************
* For showing ticket details
********************************************************************************************************/
function get_ticket_detail($docket_no){
	global $db,$link;
	$query = "select * from $db.web_accounts a, $db.web_problemdefination p where a.AccountNumber=p.vCustomerID  and ticketid='$docket_no' ";
	$res=mysqli_query($link, $query);
	return $res;
}
//added this function for fethcing deleted ticket number details[vastvikta][19-12-2024]
function get_ticket_delete_detail($docket_no){
	global $db,$link;
	$query = "select * from $db.web_accounts a, $db.web_problemdefination_archive p where a.AccountNumber=p.vCustomerID  and ticketid='$docket_no' ";
	$res=mysqli_query($link, $query);
	return $res;
}
function SmartFileName_voice($SmartFileName){		
   $filename=$SmartFileName;
   $filename=substr($filename, 0, 8);//12Jul2019
   $year=substr($filename, 0, 4);
   $day=substr($filename, 6, 2);
   $m=$year.substr($filename, 4, 2).$day;
   $month1=date('M',strtotime($m));
   //echo '<br>Date::'.$day.$month1.$year;
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
//    changed the file path from tmp to recordings [vastvikta]k[11-04-2025]
   $destFile = "../../recordings/".$recFile;
   $cmd = "/usr/bin/sox $pathWithoutExtention -b 8 $destFile";
   system($cmd);
   return $destFile;
}
/******For showing get city list for dropdown ********/
function city_list(){
	global $db,$link;
	$city_query = mysqli_query($link, "select id,city from $db.web_city where status='1' ");
	return $city_query;
}
/******For showing village list ********/
function get_Village($district){
   global $db,$link;
   $villages_query = mysqli_query($link, "select id, vVillage from $db.web_Village where iDistrictID='$district' AND status ='1' ORDER BY vVillage ASC");
   return $villages_query;
}
/******For showing gender list ********/
function web_gender(){
	global $db,$link;
	$gender_query = mysqli_query($link, "select * from $db.web_gender");
	return $gender_query;
}
/******For showing web_source list ********/
function source_list(){
 	global $db,$link;
	$source=mysqli_query($link,"select id,source from $db.web_source");
	return $source;
}
/******For showing web_language list ********/
function language_list(){
	global $db,$link;
	$sqllang = "select id,lang_Name  from $db.web_language where status='1'";
    $langresult = mysqli_query($link, $sqllang);
    return $langresult;
}
/******For showing web_language list ********/
function complaint_type(){
	global $db,$link;
	$complaint_sql = mysqli_query($link, "select id,complaint_name, slug , status from $db.complaint_type where status =1 ");
	return $complaint_sql;
}
/******For showing web_ticketstatus list ********/
function web_ticketstatus($subString = '', $subString2 = ''){
	global $db,$link;
	$ticketstatus_query = mysqli_query($link, "select id,ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2  ORDER BY ticketstatus ASC");
	return $ticketstatus_query;
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
/******For showing assign_department list ********/
function assign_department(){
	global $db,$link;
	$group_query = mysqli_query($link, "select pId,vProjectName from $db.web_projects where i_Status='1' ORDER BY vProjectName ASC ");
	return $group_query;
}
/******For showing get_email_information_out list in case details page ********/
function get_email_information_out($id){
	global $db,$link;
	$qdk = "select EMAIL_ID,v_fromemail,v_toemail,d_email_date,v_subject,v_body,email_type,V_rule,i_templateid, original_subject from $db.web_email_information_out where (ICASEID='" . $id. "' or v_subject like '%[Case Id # $id]%')  and email_type='OUT' order by d_email_date desc limit 0,20";
    $ress = mysqli_query($link, $qdk);
    return $ress;
}
/******For showing get_email_information list in case details page ********/
function get_email_information($id){
	global $db,$link;
    $qdk = "select EMAIL_ID,v_fromemail,v_toemail,d_email_date,v_subject,v_body,email_type,V_rule,i_templateid from $db.web_email_information where (ICASEID='" . $id . "' or v_subject like '%[Case Id # $id]%' or ICASEID='$id') and email_type='IN' order by d_email_date desc";
    $ress = mysqli_query($link, $qdk);
    return $ress;
}
/******For showing mode list in helpdesk home page ********/
function display_mode(){
	global $db,$link;
	$sqlsource = "select id,source from $db.web_source where status='1' and source <> 'Customer Portal' order by id='2' desc";
    $sourceresult = mysqli_query($link, $sqlsource);
     return $sourceresult;
}
function get_ticket($customer){
	global $db,$link;
	$ticket_query = mysqli_query($link, "select * from $db.web_problemdefination where vCustomerID='$customer' order by d_createDate desc limit 15; ");
	return $ticket_query;
}
function get_disposition($customer_id,$ticketid){
	global $db,$link;
	$qdk_incoming = "select * from $db.web_wrapcall where customer_id ='$customer_id' and ticketid='$ticketid' order by id desc";
    $ress_incoming = mysqli_query($link, $qdk_incoming);
    return $ress_incoming;
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
function get_twitter_details($id){
	global $db,$link;
    $qdk_twitter = "select * from $db.tbl_tweet where ICASEID='" . $id . "' and irrelevant_status=0 order by d_TweetDateTime desc limit 20";
    $ress_twitter = mysqli_query($link, $qdk_twitter);
    return $ress_twitter;
}
function get_twitterScreenName($tuserid, $db){
    global $db,$link;
     $sql = "select v_Screenname from $db.tbl_tweet where tuser_id='" . $tuserid . "' ";
     $qfetch = mysqli_fetch_row(mysqli_query($link, $sql));
     $v_Screenname = $qfetch[0];
    return $v_Screenname;
}
function get_twitterDM($id){
	global $db,$link;
	$qdm = "select * from $db.web_twitter_directmsg where caseid='" . $id . "' AND caseid !='0' order by id desc";
   $ress = mysqli_query($link, $qdm);
   return $ress;
}
function get_chathistory($id){
	global $db,$link;
	$qdk = "select * from in_out_data where (caseid='" . $id . "') order by create_datetime desc";
    $ress = mysqli_query($link, $qdk);
    return $ress;
}
function get_documents($id,$groupid){
	global $db,$link;
	if($groupid=='080000'){
         $sqlopp="SELECT * FROM $db.web_documents WHERE  I_section_ID = '".$id."' AND I_Doc_Status = '1' ORDER BY I_UploadedON DESC";
    }else{
        $sqlopp="SELECT * FROM $db.web_documents WHERE I_section_ID = '".$id."' AND I_Doc_Status = '1' and (I_PP=0 || I_UploadedBy='".$_SESSION['logged']."') ORDER BY I_UploadedON DESC";
    }
    $resopp=mysqli_query($link,$sqlopp);
    return $resopp;
}
/**
 For assign department backoffice
 I have remove web_assign.php file and add code here
**/ 
function web_assign(){
	global $db,$link,$user_id;
	$groupid    =   $_SESSION['user_group'];
	$sel = $_POST['sel'];
	$product_type2=$_POST['product_type2'];
	$assignto=$_POST['assignto'];
	if(isset($_POST['type']) && isset($_POST['vProjectID']) ){
		$html = '';
		$type = $_POST['type'];
		$vProjectID = $_POST['vProjectID'];
		$html = '<select name="product_type2" id="product_type2" class="select-styl1" style="width:190px" onChange="web_assign(this.value)" value="'.$product_type2.'">
		<option value="">Select Project</option>';
		$category_query = mysql_query("select pId,vProjectName from $db.web_projects where i_Status='1'  AND FIND_IN_SET($type, Type ) ; ");
		while($category_res = mysql_fetch_array($category_query)){
			$selected = ($category_res['pId'] == $vProjectID) ? 'selected' : '' ;
			$html .= '<option value="'.$category_res['pId'].'"' . $selected;
			$html .= '>';
			$html .= $category_res['vProjectName'];
			$html .= '</option>';
		}
		$html = '</select>';
		echo $html;
	}
	if(isset($_POST['assignid'])){
		$assignid = $_POST['assignid'];//project id
		$selusr=$_POST['selusr'];
		$html = '';
		$html = '<select name="assignto" id="assignto" class="select-styl1" style="width:190px">
		<option value="">Select Assign To</option>';
		if($groupid == '070000' ||  $groupid == '0000' ){
		   $query = "SELECT * FROM uetcl.uniuserprofile u, uetcl.web_project_assigne pa WHERE u.AtxUserID = pa.user_id AND u.AtxDesignation = 'Backoffice Officer' AND pa.project_id ='$assignid' GROUP BY u.AtxUserID ";
	    }else{
		   $query = "SELECT * FROM uetcl.uniuserprofile u, uetcl.web_project_assigne pa WHERE u.AtxUserID = pa.user_id AND u.AtxDesignation = 'Backoffice Officer' AND pa.user_id ='$user_id' GROUP BY u.AtxUserID ";
	    }
		$result = mysql_query($query);
		while($Ares = mysql_fetch_array($result)){
			$selected = ($selusr == $Ares['AtxUserID']) ? 'selected' : '';
			$html .= '<option value="'.$Ares['AtxUserID'] .'" '. $selected;
			$html .= '>';
			$html .= $Ares['AtxDisplayName'];
			$html .= '</option>';
		}	
		$html = '</select>';
		echo $html;
	}
	if(isset($_POST['assignid2'])){
		$assignid = $_POST['assignid2'];
		$selusr=$_POST['selusr'];
		$html = '';
		$html = '<select name="assignto2" id="assignto2" class="select-styl1" style="width:190px">
		<option value="">Select Assign To</option>';
		if($groupid == '070000' ||  $groupid == '0000' ){
		   $query = "SELECT * FROM uetcl.uniuserprofile u, uetcl.web_project_assigne pa WHERE u.AtxUserID = pa.user_id AND u.AtxDesignation = 'Backoffice Officer' AND pa.project_id ='$assignid' GROUP BY u.AtxUserID ";
	    }
	    else{
		   $query = "SELECT * FROM uetcl.uniuserprofile u, uetcl.web_project_assigne pa WHERE u.AtxUserID = pa.user_id AND u.AtxDesignation = 'Backoffice Officer' AND pa.user_id ='$user_id' GROUP BY u.AtxUserID ";
	    }
		$result = mysql_query($query);
		while($Ares = mysql_fetch_array($result)){
			$html .= '<option value="'.$Ares['AtxUserID'].'"';
			if($selusr == $Ares['AtxUserID']){ 
				$html .= "selected"; 
			} 
			$html .= '>';
	  		$html .= $Ares['AtxDisplayName'];
  			$html .='</option>';
		}
		$html .= '</select>';
		echo $html;
	}
	if(isset($_POST['assignfor'])){
		$assignfor = $_POST['assignfor'];
		$html = '';
		$html = '<select name="actiontaken" id="actiontaken" class="select-styl1" style="width:190px;">
		<option value="">Select Action Taken</option>';
		$actiontaken_query = mysql_query("select id,actiontaken from $db.web_actiontaken where status='1' and assignfor='$assignfor';");
		while($actiontaken_res = mysql_fetch_array($actiontaken_query)){
			$html .= '<option value="'.$actiontaken_res['id'].'"';
			if($actiontaken_res['id']==$sel){ 
				$html .= "selected"; 
			} 
			$html .= '>';
	  		$html .= $actiontaken_res['actiontaken'];
  			$html .='</option>';
		}
		$html .= '</select>';
		echo $html;
	}
}
//Retrieves the latest interaction for a given case ID from the database.
function getLatestCaseInteraction($ticketid) {
	 global $db,$link;
    // Construct and execute the SQL query to fetch the latest interaction for the given case ID
    $query = mysqli_query($link, "SELECT * FROM $db.web_case_interaction WHERE caseID = '$ticketid' ORDER BY id DESC LIMIT 1");
    return $query;
}
// Retrieves the result set of interactions for a given case ID.
function getCaseInteractionCount($link, $db, $ticketid) {
    $result = mysqli_query($link, "SELECT * FROM $db.web_case_interaction WHERE caseID = '$ticketid' ORDER BY id DESC");
    return $result;
}

// Retrieves the result set of audits for a given case ID.[farhan akhtar :: 15-04-2025]
function getCaseAuditHistory($link, $db, $ticketid) {
    $result = mysqli_query($link, "SELECT * FROM $db.web_audit_history WHERE ticket_id = '$ticketid' ORDER BY created_on DESC");
    return $result;
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
/********** code for delete ticket [Aarti][17-12-2024] ********/
// Archive and Delete Functionality
function TicketarchiveAndDelete(){
	global $db,$link,$user_id;
	
	$id = $_POST['id'];
	$remarkdelete = $_POST['remark'];
	$deleted_createddate = date("Y-m-d H:i:s");
	$deleted_bys = $_SESSION['userid'];

	// Define the condition for archiving (customize as needed)
	$condition = "ticketid='$id'";

	// Step 1: Copy data to archive table
    $copyQuery = "INSERT INTO web_problemdefination_archive (
            iPID, i_source, vCustomerID, customer_account_no, vCaseType, 
            vCategory, vSubCategory, vTypeOfcaller, iCaseStatus, vProjectID, 
            iAssignTo, call_type, priority, exceptional_case, organization, 
            vHouseHoldID, vRefNumber, vOwnerName, vVillageName, last_case_id, 
            vN1, vN2, vN3, vNinNumber, vStakeholder, vRemarks, ticketid, 
            d_createDate, i_CreatedBY, iStatus, d_updateTime, vAgentName, 
            back_office_action_by, vAssignDepartname, v_Enq_Complaint, 
            vOverAllStatus, v_OverAllRemark, feedback_status, customer_feedback, 
            b5_remark, b6_remark, v_SuggestFollowup, v_ActionSupervisor, 
            work_status, current_working_agent, work_timestamp, escalate_status, 
            escalation_hours, escalation_level, escalte_date, language_id, mno, 
            isp, dfs, perpetrator, affected, service, complaint_type, tpin_no, 
            adhoc_flag, root_cause, corrective_measure, regional, customertype, 
            merge_ticketId, deleted_remark,deleted_by,deleted_createddate
        )
        SELECT 
            iPID, i_source, vCustomerID, customer_account_no, vCaseType, 
            vCategory, vSubCategory, vTypeOfcaller, iCaseStatus, vProjectID, 
            iAssignTo, call_type, priority, exceptional_case, organization, 
            vHouseHoldID, vRefNumber, vOwnerName, vVillageName, last_case_id, 
            vN1, vN2, vN3, vNinNumber, vStakeholder, vRemarks, ticketid, 
            d_createDate, i_CreatedBY, iStatus, d_updateTime, vAgentName, 
            back_office_action_by, vAssignDepartname, v_Enq_Complaint, 
            vOverAllStatus, v_OverAllRemark, feedback_status, customer_feedback, 
            b5_remark, b6_remark, v_SuggestFollowup, v_ActionSupervisor, 
            work_status, current_working_agent, work_timestamp, escalate_status, 
            escalation_hours, escalation_level, escalte_date, language_id, mno, 
            isp, dfs, perpetrator, affected, service, complaint_type, tpin_no, 
            adhoc_flag, root_cause, corrective_measure, regional, customertype, 
            merge_ticketId, '$remarkdelete' AS remark, '$deleted_bys' AS deleted_by ,
            '$deleted_createddate' AS deleted_createddate
        FROM 
            web_problemdefination 
        WHERE  $condition";
    if ($link->query($copyQuery) === TRUE) {
        echo "Data copied to archive successfully.<br>";

		// added  ticket deletion audit report [vastvikta][11-02-2025]
        add_audit_log($deleted_bys, 'Ticket deleted', $id, 'Ticket deleted', $db);
        // Step 2: Delete data from the original table
        $deleteQuery = "DELETE FROM web_problemdefination WHERE $condition";
        if ($link->query($deleteQuery) === TRUE) {
            echo "Data deleted from the original table successfully.";
        } else {
            echo "Error deleting data: " . $link->error;
        }
    } else {
        echo "Error copying data: " . $link->error;
    }
}
// updated the query so that it doesn't display   interaction with attachment[vastvikta][21-04-2025]

function get_interaction_data($ticketid){
	global $db,$link;
	$sql_cases = "select * from $db.interaction where caseid='$ticketid' AND `remarks` != '' order by created_date";
	$ticket_query = mysqli_query($link, $sql_cases);
	return $ticket_query;
}

function agentManualTranferConfer($link, $user, $companyid, $column) {
    // Whitelist allowed column names to prevent SQL injection
    $allowed_columns = ['agentcall_manual', 'agentonly_callbacks', 'transfer', 'konfer']; // Add allowed columns
    if (!in_array($column, $allowed_columns)) {
        return false; // Invalid column name
    }

    // Prepare the query
    $query = "SELECT $column FROM asterisk.autodial_users WHERE user = ? AND company_id = ?";
    $stmt = mysqli_prepare($link, $query);
    
    if (!$stmt) {
        return false; // Return false if query preparation fails
    }

    // Bind the parameters to the query
    mysqli_stmt_bind_param($stmt, "ss", $user, $companyid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch and return the column value
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        return $data[$column];  // Corrected this line
    } else {
        return false;
    }
}
// function to get agent name  for the interaction history table [vatvikta][06-05-2025]
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

?>