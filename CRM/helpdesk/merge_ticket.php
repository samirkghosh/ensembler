<?php 

/***
 * Merge Ticket
 * Author: Aarti
 * Date: 09-04-2024
 *  This code is used in a web application to Merge Ticket
-->
 **/
include("web_mysqlconnect.php"); // database connection

// for fetch ticket details
if($_POST['action'] == 'fetch_ticket'){
	$customer = $_POST['customer'];
	$ticketid = $_POST['ticket_id'];
	$case_querylist = "SELECT * FROM $db.web_problemdefination WHERE  vCustomerID='$customer' and ticketid!='$ticketid' and merge_ticketId IS NULL";
	$case_result_list = mysqli_query($link, $case_querylist);
	$html = '<option value="0">Selec TicketId</option> ';
		while ($sres = mysqli_fetch_array($case_result_list)){ 
		 	$html .='<option value="';
		 	$html .= $sres['ticketid'];
		 	$html .='">';
		 	$html .= $sres['ticketid'];
		 	$html .='</option>';
		}
	echo json_encode($html);
}
// for merge two ticket for create same category
if($_POST['action'] == 'submit_merge'){
	if( isset($_POST['ticket_id']) && !empty($_POST['ticket_id'])){
		$ticket_id 			= $_POST['ticket_id'] ;
		$source 			= $_POST['source_id'] ;
		$customerid 		= $_POST['customer'] ;
		$status_type		= $_POST['status_type'] ;
		$remarks           =$_POST['remarks'];
		$todaydate 		=	date("Y-m-d H:i:s");
		$logedin_agent 	= 	$_SESSION['logged'];
		$vuserid	   	= 	$_SESSION['userid'];
		$marge_ticket = $_POST['marge_ticket'];
		$action_changes = "This Ticket Merge with ".$ticket_id;
		$status_current = '3';
		$update_sql = "update $db.web_problemdefination set merge_ticketId='$ticket_id', iCaseStatus='$status_current' where ticketid='$marge_ticket'" ;
		mysqli_query($link,$update_sql);

		$sql = "insert into $db.web_case_interaction (caseID, custmer_id, mode_of_interaction, current_status, remark, created_date, created_by, interacation_type,action) values ('$marge_ticket', '$customerid', '$source', '$status_current', '$remarks', '$todaydate', '$logedin_agent','Remark','$action_changes')" ;
		mysqli_query($link,$sql);
		echo json_encode('succesfully merge'); 
	}
}
?>