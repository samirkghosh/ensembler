<?php
/***
 * IM Page
 * Author: Aarti Ojha
 * Date: 07-11-2024
 * Description: This file handles IM Realtime Chat chat flow
 */
include("/var/www/html/ensembler/config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// fetch user details

class ChatRooms{	
	function __construct() {
		global $link,$db,$db_asterisk;
		// $database_object = new Database_connection;
		$this->connect = $link;
		if(isset($_GET['action'])){
			if($_GET['action'] == 'get_chat'){
				$this->get_all_user_list();
			}else if($_GET['action'] == 'get_chat_user'){
				$this->get_chat_history();
			}else if($_GET['action'] == 'insert_message'){
				$this->insert_message();
			}else if($_GET['action'] == 'count_message'){
				$this->count_msg_display($data=array());
			}
		}else if(isset($_POST['action'])){
			if($_POST['action'] == 'update_read_message'){
				$this->update_read_message();
			}else if($_POST['action'] == 'notification_update'){
				$this->notification_statu_update();
			}
			if($_POST['action'] == 'insert_rating_feedback'){
				$this->insert_rating_feedback();
			}
		}
	}
	// get user list display IM UI
	function get_all_user_list(){
		global $link,$db,$db_asterisk;
		$outgoing_id = $_GET['unique_id'];
		$searchTerm = '';
		if(isset($_GET['searchTerm'])){
			$searchTerm = $_GET['searchTerm'];
		}
	    $query = $this->get_userlist($outgoing_id,$searchTerm);
	    $output = "";
	    $count = '';
	    $count_notify = 0;
	    if(mysqli_num_rows($query) == 0){
	        $output .= "No users are available to chat";

	    }elseif(mysqli_num_rows($query) > 0){
	    	 while($row = mysqli_fetch_assoc($query)){
	    	 	$data['flag'] = 'one_user';
	    	 	$data['id'] = $row['AtxUserID'];
	    	 	$data['sender_id'] = $outgoing_id;
	    	 	$total = $this->count_msg_display($data);

		        $sql2 = "SELECT * FROM $db.tbl_im_new WHERE (from_id = {$row['AtxUserID']}
		                OR to_id = {$row['AtxUserID']}) AND (to_id = {$outgoing_id} 
		                OR from_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
		        $query2 = mysqli_query($this->connect, $sql2);

		        $username = $row['AtxUserName'];
		        $sql_1 = "select * from $db_asterisk.autodial_live_agents where user='{$username}'";
		        $query_1 = mysqli_query($this->connect, $sql_1);
		        $status_live = '';
		        if(mysqli_num_rows($query_1) > 0){
		        	$row_status = mysqli_fetch_assoc($query_1);
		        	$status_live = $row_status['status'];
		        }
		        

		        // if(mysqli_num_rows($query2) > 0){
			        $row2 = mysqli_fetch_assoc($query2);
			        (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="No message available";
			        (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;

			        if(isset($row2['to_id'])){
			            ($outgoing_id == $row2['to_id']) ? $you = "You: " : $you = "";
			        }else{
			            $you = "";
			        }
			        
			    	//if (file_exists("images/".$row['img'])) {
					//     $images = "images/".$row['img'];
					// } else {
					    $images = "IMApp/images/dummy.jpg";
					// }
					
			        ($row['login_status'] == "offline") ? $offline = "offline" : $offline = "";

			        if($row['login_status'] == "online"){
			        	 $old_Date = $row['login_datetime'];
						// set dates
						$date_compare1= date("d-m-Y h:i:s a", strtotime($old_Date));
						// date now
						$date_compare2= date("d-m-Y h:i:s a");

						// calculate 
						$diff= $date_compare1-$date_compare2;
						if($diff>0){
							 $offline = "offline";
						}

			        }
			        if(!empty($status_live)){
						if($status_live == 'DIALING' || $status_live == 'INCALL'){
				        	$status_display = 'busy_status';

				        }else if($status_live == 'PAUSED' || $status_live == 'CLOSER'){
				        	$status_display = 'break_status';

				        }else if($status_live == 'IDLE' || $offline == 'online' || $status_live == 'READY'){
				        	$status_display = 'online_status';

				        }else if($offline == 'offline'){
				        	$status_display = 'offline_status';
				        }
				    }else if($row['login_status'] == 'online'){
				    	$status_display = 'online_status';

				    }else if($row['login_status'] == 'offline'){
				        	$status_display = 'offline_status';

				    }

			        ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

			        $output .= '<div class="flow"><a href="javascript:void(0);" class="chatbox_click" data-name="'. $row['AtxDisplayName']. " " . $row['lname'] .'" data-status="'.$row['status'].'" data-incomingid="'.$row['AtxUserID'].'" data-img="'.$images.'" data-userid="'.$row['AtxUserID'].'">';
			        $output .= '<div class="content_1">
			                    <img src="'.$images.'" alt="">
			                    <div class="details">
			                        <span>'. $row['AtxDisplayName']. " " . $row['lname'];
			                 if($total){
			                 	$output .= '<span class="msg_count">('.$total.')</span>';
			                 }
			        $output .= '</span><p>'. $you . $msg .'</p>
			                    </div>
			                    </div>';
			        $output .= '<div class="status-dot '. $offline .' '.$status_display.'"><i class="fas fa-circle"></i></div>';
			        $output .= '</a></div>';
			        
			        // checking notify update or not
			        $data['id'] = $outgoing_id;
			        $notify = $this->notification($data);
			        $count_notify = $count_notify + $notify;
			    

    		}
	    }
	    $data_format['output'] =  $output;
	    $data_format['notify'] =  $count_notify;
	    echo json_encode($data_format);
	}
	// get chat history user list display IM UI
	function get_chat_history(){
		global $link,$db;
		if(isset($_GET['unique_id'])){
	        $outgoing_id = $_GET['unique_id'];
	        $incoming_id = mysqli_real_escape_string($this->connect, $_GET['userid']);
	        $output = "";

	        $sql = "SELECT * FROM $db.tbl_im_new LEFT JOIN uniuserprofile ON uniuserprofile.AtxUserID = tbl_im_new.to_id
	                WHERE (to_id = {$outgoing_id} AND from_id = {$incoming_id})
	                OR (to_id = {$incoming_id} AND from_id = {$outgoing_id}) ORDER BY msg_id";
	        $query = mysqli_query($this->connect, $sql);

	        if(mysqli_num_rows($query) > 0){
	            while($row = mysqli_fetch_assoc($query)){
	            	// $date_time = date_format($row['msgTime'],"d-m-Y");
	            	$date_time = date("d M y h:i", strtotime($row['msgTime']));
	                if($row['to_id'] === $outgoing_id){
	                    $output .= '<div class="chat outgoing">
	                                <div class="details">
	                                    <p>'. $row['msg'] .'</p>
	                                </div>
	                                </div>';
	                }else{
	                    $output .= '<div class="chat incoming">
	                                <img src="IMApp/images/dummy.jpg" alt="">
	                                <div class="details">
	                                    <p>'. $row['msg'] .'</p>
	                                </div>
	                                </div>';
	                }
	                $output .= '<p class="timeline"><span>'.$date_time.'</span></p>';
	            }
	        }else{
	            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
	        }
	        // getting unread msg count
	        $sql2 = "SELECT count(msg) as total FROM tbl_im_new WHERE from_id = {$outgoing_id} and status = '0'";
	        $query2 = mysqli_query($this->connect, $sql2);
	        $row2 = mysqli_fetch_assoc($query2);
	        $total = $row2['total'];

	        $data_format['output'] =  $output;
		    $data_format['total'] =  $total;
		    echo json_encode($data_format);
	    }else{
	    	$output .= '<div class="text" style="color:black">No messages are available. Once you send message they will appear here.</div>';
	    	$data_format['output'] =  $output;
		    $data_format['total'] =  '0';
	    	echo json_encode($data_format);
	    }
	}
	// insert message user list
	function insert_message(){
		global $link,$db;
		if(isset($_GET['unique_id'])){
	        $outgoing_id = $_GET['unique_id'];
	        $incoming_id = mysqli_real_escape_string($this->connect, $_GET['incoming_id']);
	        $message = mysqli_real_escape_string($this->connect, $_GET['message']);
	        $date = date("Y-m-d H:i:s");
	        if(!empty($message)){
	            $sql = mysqli_query($this->connect, "INSERT INTO $db.tbl_im_new (from_id, to_id, msgTime, msg , status)
	            VALUES ({$incoming_id}, {$outgoing_id}, '{$date}', '{$message}','0')") or die();
	        }
	    }
	}
	// insert message user list
	function count_msg_display($data=array()){
		global $link,$db;
		if(isset($_GET['flag'])){
			$flag = $_GET['flag'];
			$id = $_GET['unique_id'];
			if($flag == 'all'){
				$outgoing_id = $id;
				$total = '';
			    $sql2 = "SELECT count(msg) as total FROM $db.tbl_im_new WHERE from_id = {$outgoing_id} and status = '0'";
		        $query2 = mysqli_query($this->connect, $sql2);
		        $row2 = mysqli_fetch_assoc($query2);
		        $total = $row2['total'];
		        echo $total;
			}
		}
		if(isset($data['flag']) && $data['flag'] == 'one_user'){
			$outgoing_id = $data['id'];
			$sender = $data['sender_id'];
			$total = '';
		    $sql2 = "SELECT count(msg) as total FROM tbl_im_new WHERE to_id = {$outgoing_id} and from_id = {$sender}  and status = '0'";
		    $query2 = mysqli_query($this->connect, $sql2);
		    $row2 = mysqli_fetch_assoc($query2);
		    $total = $row2['total'];
		    return $total;
			
		}
	}
	// update_read_message message user list
	function update_read_message(){
		global $link,$db;
		if(isset($_POST['sender'])){
			$sender = $_POST['sender'];
			$recived = $_POST['recived'];
			$sql = "UPDATE $db.tbl_im_new SET status = '1' WHERE from_id = {$sender} and to_id='{$recived}'";
			if(mysqli_query($this->connect, $sql)){
			    echo "Record was updated successfully.";
			} else {
			    echo "ERROR: Could not able to execute $sql. ";
			} 
		}
	}
	// get user list display IM UI
	function get_userlist($outgoing_id,$searchTerm=''){
		global $link,$db;
		$search = '';
		if(isset($searchTerm) && !empty($searchTerm)){
			$search = " AND (AtxDisplayName LIKE '%{$searchTerm}%' OR AtxDisplayName LIKE '%{$searchTerm}%') ";
		}
	    $sql = "SELECT * FROM $db.uniuserprofile WHERE NOT AtxUserID = {$outgoing_id} $search ORDER BY AtxUserName ASC";
	    $query = mysqli_query($this->connect, $sql);
	    return $query;
	}
	// get user list display IM UI
	function get_login_user_details($outgoing_id){
		global $link,$db;
		$sql_new = "SELECT * FROM $db.uniuserprofile WHERE AtxUserID = {$outgoing_id}";
		$sql = mysqli_query($this->connect, $sql_new);
        if(mysqli_num_rows($sql) > 0){
        	$row = mysqli_fetch_assoc($sql);
          	// $username = $row['AtxUserName'];
	        // $sql_1 = "select * from asterisk.autodial_live_agents where user='{$username}'";
	        // $query_1 = mysqli_query($this->connect, $sql_1);
	        // $status_live = '';
	        // if(mysqli_num_rows($query_1) > 0){
	        // 	$row_status = mysqli_fetch_assoc($query_1);
	        // 	$status_live = $row_status['status'];
	        // }
	        // if($status_live == 'READY' || $status_live == 'DIALING'){
	        // 	$status_display = 'Busy';

	        // }else if($status_live == 'PAUSED' || $status_live == 'CLOSER'){
	        // 	$status_display = 'Break';

	        // }else if($status_live == 'IDLE' || $row['status'] == 'online'){
	        // 	$status_display = 'online';

	        // }else if($row['status'] == 'offline'){
	        // 	$status_display = 'offline';
	        // }
	        // $row['status'] = $status_display;
          
        }
        return $row;
	}
	//get login and logout details
	function get_login_or_logout(){
		global $link,$db;
		$sql2 = "SELECT * FROM $db.logip WHERE AccessedAt = {$outgoing_id}";
	    $query2 = mysqli_query($this->connect, $sql2);
	    if(mysqli_num_rows($query2) > 0){
		    $row2 = mysqli_fetch_assoc($query2);
		    $notify = $row2['notify'];
		}
	}
	function notification($data){
		global $link,$db;
		$outgoing_id = $data['id'];
		$sender = $data['sender_id'];
		$notify = '0';
	    $sql2 = "SELECT count(notify) as notify FROM $db.tbl_im_new WHERE from_id = {$outgoing_id} and status = '0' and notify = '0'";
	    $query2 = mysqli_query($this->connect, $sql2);
	    if(mysqli_num_rows($query2) > 0){
		    $row2 = mysqli_fetch_assoc($query2);
		    $notify = $row2['notify'];
		}
	    return $notify;
	}
	function notification_statu_update(){
		global $link,$db;
		$outgoing_id = $_POST['unique_id'];
		$sql2 = "SELECT count(notify) as notify FROM $db.tbl_im_new WHERE from_id = {$outgoing_id} and status = '0' and notify = '0'";
		$query = mysqli_query($this->connect,$sql2);
		if(mysqli_num_rows($query) > 0){
			$sql = "UPDATE tbl_im_new SET notify = '1' WHERE from_id = {$outgoing_id}";
			mysqli_query($this->connect, $sql);
		}
	}
	// feedback record insert and delete
	function create_feedbacklink($data=array()){
		global $db,$SiteURL;
		if(isset($data['createdBy'])){
			//$id = $data['id'];
			$Type = $data['Type'];
			$Call_id = $data['Call_id'];
			$Ticket_id = $data['Ticket_id'];
			$Phone_Number = $data['Phone_Number'];
			$AgentID = $data['AgentID/Name'];
			$Extension_Number = $data['Extension_Number'];
			$customer_email = $data['customer_email'];
			$customer_name = $data['customer_name'];
			$Call_Time = date("Y-m-d H:i:s");
			$d_requestTime = date("Y-m-d H:i:s");
			
			$sql_qry = "INSERT INTO $db.tbl_survey_request (Type, Call_id, Ticket_id, Phone_Number ,Call_Time, AgentID_Name , Extension_Number,d_requestTime,customer_name,customer_email) VALUES ('{$Type}','{$Call_id}','{$Ticket_id}','{$Phone_Number}','{$Call_Time}','{$AgentID}','{$Extension_Number}','{$d_requestTime}','{$customer_name}','{$customer_email}')";
			mysqli_query($this->connect, $sql_qry);
			$last_id = mysqli_insert_id($this->connect);
			$id = base64_encode($last_id);
			return $SiteURL."CRM/feedback.php?".$id;
			
		}
	}
	function insert_rating_feedback(){
		global $db;
		// echo $_POST['companyID'];
		$companyID = $_POST['companyID'];
		
		$db = $this->get_db_name($companyID);
		
		if($_POST['user_id']){
			// $recaptcha_secret = "6LdFCpYnAAAAADntOVUuSpX5FTO-AiypuAn-DZct";
			// 	$recaptcha_response = $_POST['grecaptcha'];
			// 	$remote_ip = $_SERVER['REMOTE_ADDR'];

			// 	$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$recaptcha_response."&remoteip=".$remote_ip;
			// 	$recaptcha_response = file_get_contents($recaptcha_url);
			// 	$recaptcha_response = json_decode($recaptcha_response);

			// if ($recaptcha_response->success == true) {

				$id = $_POST['user_id'];
				$Type = !empty($_POST['Type']) ? $_POST['Type'] : 1; // Default to 1 if $_POST['Type'] is empty to set it to IVR
				// echo $Type;
				$sql_document="select * from $db.tbl_survey_request where id = $id";
				$record=mysqli_query($this->connect, $sql_document);
				$numrow=mysqli_num_rows($record);
				if($numrow == '0' || $numrow == ''){
					$flag = '1';
					echo $flag;
				}else{
					$info = mysqli_fetch_assoc($record);
					$date = $info['d_requestTime'];
					if(strtotime($date) < strtotime('-60 days')) {
						$flag = '1';
						echo $flag;
					}else{
						$Ticket_id = $info['Ticket_id'];
						$Phone_Number = $info['Phone_Number'];
						$Connect_time = $info['Call_Time'];
						$Unique_Id = $info['Call_id'];
						$AgentID = $info['AgentID_Name'];
						$Extension_Number = $info['Extension_Number'];
						$customer_email = $info['customer_email'];
						$customer_name = $info['customer_name'];
						$rating = $_POST['optradio'];

						$Customer_id = $customer_name;
						// $Customer_email = '';
						$Dialed_Digit = $_POST['optradio'];

						$sql_qry = "INSERT INTO $db.tbl_civrs_cdr (Type, Customer_id, Customer_email, ticket_id ,Phone_Number, Connect_time , Dialed_Digit,Unique_Id,Extension_Num,AgentName,Agent_id,Dialed_DigitP) VALUES ('{$Type}','{$Customer_id}','{$customer_email}','{$Ticket_id}','{$Phone_Number}','{$Connect_time}','{$Dialed_Digit}','{$Unique_Id}','{$Extension_Number}','{$AgentID}','{$AgentID}','{$rating}')";
						// echo $sql_qry;
						$result = mysqli_query($this->connect, $sql_qry);
						if($result){
							$sql_document="delete from $db.tbl_survey_request where id = $id";
							if ($this->connect->query($sql_document) === TRUE) {
								echo "Record deleted successfully";
							} else {
								echo "Error deleting record: " . $this->connect->error;
							}
						}else{
							echo "Error deleting record: " . $this->connect->error;
						}
					}
				}
			// } else {
			// 	$flag = '2';
			// 	echo $flag;
			// }

		}
		
	
	}
	// added this code to get the database on the basis of the  company id [vastvikta][03-02-2025]

	function get_db_name($companyID) {
		global $link, $db, $db_asterisk; // Access global variables

		$master = 'CampaignTracker'; // This should be a table or database reference

		// Query to get the related database name
		$query = "SELECT related_database_name FROM $master.companies WHERE company_id = '$companyID'";

		$result = $this->connect->query($query);

		if ($result) {
			if (mysqli_num_rows($result) > 0) {
				$company = mysqli_fetch_assoc($result);
				$relatedDatabaseName = $company['related_database_name'];
				$db = $relatedDatabaseName; // Set the global $db variable
			} else {
				echo 'Invalid company ID. No database found.'.$companyID;
			}
		} else {
			echo 'Query failed: ' . $this->connect->error;
		}

		return $db;
	}
}
$controller = new ChatRooms();
?>