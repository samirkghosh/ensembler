<?php
// Including the file for database connection
include_once("../config/web_mysqlconnect.php"); // Connection to database // Please do not remove this
// Global variable for database connection
global $link;

// Class definition for common functions
class common_function
{
    // This class contains common functions for the application
	function __construct() {
		global $db,$link;
		$this->connection = $link;
		if(isset($_POST['action'])){
			//[Ritu][12-05-2024] for channel add update bulletin flow
			if($_POST['action'] == 'addUpdateBulletin'){
				$this->addUpdateBulletin();
			}else if($_POST['action'] == 'delete_bulletin'){//[Ritu][12-05-2024] for channel delete bulletin flow
				$this->deleteBulletin();
			}else if($_POST['action'] == 'updateNpsFeddback'){
				$this->updateNpsFeddback();
			}else if($_POST['action'] == 'Add_CES'){
				$this->updateCES();
			}else if($_POST['action'] == 'fetching_bulletin'){//[Ritu][12-05-2024] for channel delete bulletin flow
				$this->fetching_bulletin();
			}
			if($_POST['action'] == 'license_update'){
				$this->license_update();
			}
			if($_POST['action'] == 'license_update_role'){
				$this->license_update_role();
			}
			//[Aarti][16-04-2024] for channel license flow
			if($_POST['action'] == 'license_update_channel'){
				$this->license_update_channel();
			}
			//[Aarti][22-04-2024] for channel license flow
			if($_POST['action'] == 'license_list_channel'){
				$this->license_list_channel();
			}
		}
	}
	/**
     * Function to add or update bulletin
     */
	function addUpdateBulletin(){
		global $db;
		if(isset($_POST['createdBy'])){
			$id = $_POST['id'];
	        $v_createdBy = $_POST['createdBy'];
	        $Message = $_POST['message'];
	        $d_startDate = date("Y-m-d H:i:s",strtotime($_POST['startdatetime']));
	        $d_endDate = date("Y-m-d H:i:s",strtotime($_POST['enddatetime']));
	        $d_creation = date("Y-m-d H:i:s");
	        $msg_type = $_POST['msg_type'];
	        if(empty($v_createdBy) && empty($id)){
	        	$sql_qry = "INSERT INTO $db.tbl_bulletinboard (Message, d_creation, v_createdBy, d_startDate , d_endDate , i_msgType) VALUES ('{$Message}','{$d_creation}','{$v_createdBy}', '{$d_startDate}','{$d_endDate}','{$msg_type}')";
	        	mysqli_query($this->connection, $sql_qry);
	        }else{
        		$sql = "UPDATE $db.tbl_bulletinboard SET Message = '{$Message}' , d_creation = '{$d_creation}', v_createdBy = '{$v_createdBy}' , d_startDate = '{$d_startDate}' , d_endDate = '{$d_endDate}' , i_msgType = '{$msg_type}'  WHERE id = {$id}";
        		mysqli_query($this->connection, $sql);
	        }
	    }
	}
	function gell_all_bulletin(){
		global $db;
		$sql_document="select * from $db.tbl_bulletinboard";
        $res=mysqli_query($this->connection,$sql_document);
       	return $res;
	}
	function deleteBulletin(){
		global $db;
		$id = $_POST['id'];
		if($id){
			$sql_document="delete from $db.tbl_bulletinboard where id = $id";
	      	if ($this->connection->query($sql_document) === TRUE) {
			  echo "Record deleted successfully";
			} else {
			  echo "Error deleting record: " . $this->connection->error;
			}
		}
	}
	function get_bulletin_record($id){
		global $db;
		$sql_document="select * from $db.tbl_bulletinboard where id = $id";
        $info=mysqli_query($this->connection,$sql_document);
        $row_doc=mysqli_fetch_array($info);
       	return $row_doc;
	}
	function fetching_message_list(){
		global $db;
		$sql_document="select * from $db.tbl_bulletinboard";
        $info=mysqli_query($this->connection,$sql_document);
        $array_bulletin = array();
        $numrow=mysqli_num_rows($row_doc);
        // date_default_timezone_set('Asia/Kolkata');
		$hoursver = "4"; // hours set and adjust you time properly

		// time is behind the server time
		$timeadjust = ($hoursver * 3600);

		$real_time = date("Y-m-d h:i:s",time() + $timeadjust);
        // $now = time();
        // $current_date = date("Y-m-d H:i:s" , $now);
        // print_r($current_date);
       	if($numrow != '0'){
       		$i = 0;
	        while($row_doc=mysqli_fetch_array($info)){
	        	if($row_doc['d_endDate'] >= $real_time){
	        		$array_bulletin[$i]['id']  = $row_doc['id'];
                    $array_bulletin[$i]['Message']  = $row_doc['Message'];
                    $array_bulletin[$i]['startDate']  = $row_doc['d_startDate'];
                    $array_bulletin[$i]['endDate']  = $row_doc['d_endDate'];
	        	}
	        	$i++;
	        }
	    }
       	return $array_bulletin;
	}
	function fetching_bulletin(){
		global $db;
		$sql_document="select * from $db.tbl_bulletinboard"; 
        $info=mysqli_query($this->connection,$sql_document);
        $array_bulletin = array();
        $numrow=mysqli_num_rows($row_doc);
        // date_default_timezone_set('Asia/Kolkata');
		$hoursver = "4"; // hours set and adjust you time properly
		// time is behind the server time
		$timeadjust = ($hoursver * 3600);
		$real_time = date("Y-m-d h:i:s",time() + $timeadjust);
       	if($numrow != '0'){
       		$i=0;
	        while($row_doc=mysqli_fetch_array($info)){

	        	if($row_doc['d_endDate'] >= $real_time){
	        		// print_r($row_doc);
                   if($i==0){
                   	$html .= '<p class="middle"><span class="tick-orange"></span>';
                   	$html .= $row_doc['Message'];
                   	$html .= '</p>';
                   }
        			$html .= '<p class="right"><span class="tick-orange">';
        			$html .= '</span>';
        			$html .= $row_doc['Message'];
        			$html .= '</p>';
        			
        			$i++;
	        	}
	        	
	        }
	    }
       	echo json_encode($html);
	}
	public function addNpsFeddback($data,$companyID){
		global $db;
		// $companyID = $_POST['companyID'];
		$db = $this->get_db_name($companyID);
		
		if(isset($data['createdBy'])){
	        $customer_id = $data['customer_id'];
	        $customer_email = $data['customer_email'];
	        $ticket_id = $data['ticket_id'];
	        $phone_number = $data['phone_number'];	        
	        $unique_id = $data['unique_id'];
	        $feedback_value = $data['feedback_value'];
	        $media = $data['media'];
	        $connect_time = date("Y-m-d H:i:s");
	        $flag = $data['flag'];
        	$sql_qry = "INSERT INTO $db.tbl_nps (customer_id, customer_email, ticket_id, phone_number ,created_date, unique_id , feedback_value,media,flag) VALUES ('{$customer_id}','{$customer_email}','{$ticket_id}','{$phone_number}','{$connect_time}','{$unique_id}','{$feedback_value}','{$media}','{$flag}')";
			
			mysqli_query($this->connection, $sql_qry) or die("Error In Query2 " . mysqli_error($this->connection));
        	$last_id = mysqli_insert_id($this->connection);
        	$id = base64_encode($last_id);
        	return $id;
        	// [Aarti][10-04-2024]- for comment combine cse and nps form
        	// return "http://165.232.183.220/ensembler/CRM/Npsfeedback.php?".$id;
	        
	    }
	}
	
	function updateNpsFeddback(){
		global $db;
		$companyID = $_POST['companyID'];
		$Type = $_POST['Type'];
		 // Update Type based on the condition
		if ($Type == 2) {
			$Type = 'MAIL';
		} elseif ($Type == 3) {
			$Type = 'SMS';
		}
		$db = $this->get_db_name($companyID);
		
        $npsid = $_POST['user_id'];	   
        $feedback_value = $_POST['options'];
        $updated_date = date("Y-m-d H:i:s");
        $flag = '1';
        $sql="select * from $db.tbl_nps where id = $npsid and flag = '1'";
        $info=mysqli_query($this->connection,$sql);
        $numrow=mysqli_num_rows($info);
        if($numrow == 0){
        	$update_sql = "update $db.tbl_nps set flag='$flag',feedback_value='$feedback_value',updated_date='$updated_date',media='$Type' where id='$npsid'" ;
			mysqli_query($this->connection,$update_sql);
        }else{
        	echo "1";
        }
       	   	    
	}
	function addCES($data,$companyID){
		global $db;
		// $companyID = $_POST['companyID'];
		$db = $this->get_db_name($companyID);
		
		if(isset($data['createdBy'])){
	        $customer_id = $data['customer_id'];
	        $customer_email = $data['customer_email'];
	        $ticket_id = $data['ticket_id'];
	        $phone_number = $data['phone_number'];	        
	        $unique_id = $data['unique_id'];
	        $feedback_value = $data['feedback_value'];
	        $media = $data['media'];
	        $connect_time = date("Y-m-d H:i:s");
	        $flag = $data['flag'];

        	$sql_qry = "INSERT INTO $db.tbl_customer_effort (customer_id, customer_email, ticket_id, phone_number ,created_date, unique_id , feedback_value,media,flag) VALUES ('{$customer_id}','{$customer_email}','{$ticket_id}','{$phone_number}','{$connect_time}','{$unique_id}','{$feedback_value}','{$media}','{$flag}')";
			mysqli_query($this->connection, $sql_qry) or die("Error In Query2 " . mysqli_error($this->connection));
        	$last_id = mysqli_insert_id($this->connection);
        	$id = base64_encode($last_id);
        	return $id;
        	// [Aarti][10-04-2024]- for comment combine cse and nps form
        	// return "http://165.232.183.220/ensembler/CRM/customer_effort_form.php?".$id;
	        
	    }
	}
	// changed the code handling for company id  [vastvikta][04-02-2025]
	function updateCES(){
		global $db;
		$companyID = $_POST['companyID'];
		$Type = $_POST['Type'];
		$db = $this->get_db_name($companyID);
		 // Update Type based on the condition
		if ($Type == 2) {
			$Type = 'MAIL';
		} elseif ($Type == 3) {
			$Type = 'SMS';
		}
        $cesid = $_POST['user_id'];	   
        $feedback_value = $_POST['options'];
        $updated_date = date("Y-m-d H:i:s");
        $flag = '1';
        $sql="select * from $db.tbl_customer_effort where id = $cesid and flag = '1'";
        $info=mysqli_query($this->connection,$sql);
        $numrow=mysqli_num_rows($info);
        if($numrow == 0){
        	$update_sql = "update $db.tbl_customer_effort set flag='$flag',feedback_value='$feedback_value',updated_date='$updated_date',media = '$Type' where id='$cesid'" ;
			mysqli_query($this->connection,$update_sql);
        }else{
        	echo "1";
        }
       	   	    
	}
	/*Aarti-06-12-23
	code for - adding permission menu and report.
	this function linked with license_module.php page*/
	function license_update(){
		global $db;
        $checked = $_POST['checked'];	   
        $id = $_POST['id'];
        $date = date("Y-m-d H:i:s");
        $module_name = $_POST['module_name'];
        if($checked == 'true'){
        	$module_flag ='1';
        }else{
        	$module_flag ='0';
        }
        if($id!=''){
        	$update_sql = "update $db.module_license set module_flag='$module_flag',updated_date='$date' where id='$id'" ;
			mysqli_query($this->connection,$update_sql);
        }else{
        	$sql_qry = "INSERT INTO $db.tbl_customer_effort (module_name, module_flag,created_date) VALUES ('{$module_name}','{$module_flag}','{$created_date}')";
        	mysqli_query($this->connection, $sql_qry);
        }
	}
	/*Aarti-06-12-23
	code for - add role base permission on report.
	this function linked with license_module.php page*/
	function license_update_role(){
		global $db;
        $checked = $_POST['checked'];	
        $group_Id = $_POST['groupval'];   
        $id = $_POST['id'];
        $date = date("Y-m-d H:i:s");
        $module_name = $_POST['module_name'];
        $group_name =$_POST['group_name'];
        if($checked == 'true'){
        	$module_flag ='1';
        }else{
        	$module_flag ='0';
        }

        $sql="select * from $db.module_license where id='$id'";
        $info=mysqli_query($this->connection,$sql);
        $module=mysqli_fetch_array($info);
        $group_Iddata = $module['group_Id'];
	    $group_Idarr = array();
	    if(!empty($group_Iddata)){
	      $group_Idarr = explode(',', $group_Iddata);
	    }
	    // Check if the value exists in the array
	    if(in_array($group_Id,$group_Idarr)){
	    	// Remove the value from the array
	    	$group_Idarr = array_diff($group_Idarr, array($group_Id));
	    	// Optional: Re-index the array if needed
    		$group_Idarr = array_values($group_Idarr);
	    }else{
	    	// value push in array
	    	array_push($group_Idarr, $group_Id);
	    }
	    if(!empty($group_Idarr)){
	    	$final_group = implode(',', $group_Idarr);
	    }
        if($id!=''){
        	$update_sql = "update $db.module_license set group_Id='$final_group',group_name='$group_name',updated_date='$date' where id='$id'" ;
			mysqli_query($this->connection,$update_sql);
		}
	}
	/*end*/
	/*Aarti-07-12-23
	Fetch report related permission to show/hide report*/
	function module_license_Report($module,$groupId){
		global $link,$db;
		$query = "SELECT * FROM $db.module_license WHERE module_name='$module' and master_name='Report'";
		$res =mysqli_query($link,$query);
		$row=mysqli_fetch_array($res);
		$datainfo['module_flag'] = $row['module_flag'];
		$datainfo['group_Ids'] = explode(',',$row['group_Id']);
		return $datainfo;
	}
	/*end*/
	/*Aarti-16-04-2023
	code for - social media channel license flow.
	this function linked with license_channel.php page*/
	function license_update_channel(){
		global $db;
        $select_menu = $_POST['select_menu'];	
        $count = $_POST['count'];   
        $sql="select * from $db.channel_license where name='$select_menu'";
        $info=mysqli_query($this->connection,$sql);
        $module=mysqli_fetch_array($info);
        $id = $module['id'];
        if($id!=''){
        	$update_sql = "update $db.channel_license set count='$count' where name='$select_menu'" ;
			mysqli_query($this->connection,$update_sql);
		}else{
			$sql_qry = "INSERT INTO $db.channel_license (name, count) VALUES ('{$select_menu}','{$count}')";
        	mysqli_query($this->connection, $sql_qry);
		}
	}
	/*end*/
	function license_list_channel(){
		global $db,$link;
		$channel_name = $_POST['select_menu'];
		$query = "SELECT * FROM $db.user_channel_assignment WHERE channel_type='$channel_name'";
		$res =mysqli_query($link,$query);
		$html = '';
		$html ='<tr class="background"><td>User Name</td><td>Channel Type</td></tr>';
		while($row=mysqli_fetch_array($res)){
			$userid = $row['userid'];
			$sql_user_ID = "SELECT u.AtxUserName FROM  $db.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where tmuc.V_EmailID = u.AtxEmail and tmuc.I_UserID=".$userid;
			$res_userid=mysqli_query($link,$sql_user_ID);
			$row_user=mysqli_fetch_array($res_userid);
			$name = $row_user['AtxUserName'];
			$web_userdetailview = base64_encode('web_userdetailview');
            $html .= '<tr><td><a href="user_index.php?token='.$web_userdetailview.'&id='.$userid.'" target="_blank">'.$name.'</a></td><td>'.$row['channel_type'].'</td></tr>';
        }
        echo $html;
	}
	//   code for fetching the db name on  the basis of the company id [vastvikta][03-02-2025]
	function get_db_name($companyID) {
		global $link, $db, $db_asterisk; // Access global variables

		$master = 'CampaignTracker'; // This should be a table or database reference

		// Query to get the related database name
		$query = "SELECT related_database_name FROM $master.companies WHERE company_id = '$companyID'";

		$result = $this->connection->query($query);

		if ($result) {
			if (mysqli_num_rows($result) > 0) {
				$company = mysqli_fetch_assoc($result);
				$relatedDatabaseName = $company['related_database_name'];
				$db = $relatedDatabaseName; // Set the global $db variable
			} else {
				echo 'Invalid company ID. No database found.';
			}
		} else {
			echo 'Query failed: ' . $this->connection->error;
		}

		return $db;
	}
}
$controller = new common_function();
?>