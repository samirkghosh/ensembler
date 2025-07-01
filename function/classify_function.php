<?php
include_once "../config/web_mysqlconnect.php";

// Check if the action is to view a case report
if($_POST['action'] == 'break_details'){
	agent_break(); // Call function to view case report
  }

  function agent_break(){
    global $db, $link;

    // Retrieve the POST data from the AJAX request
    $logged_name = mysqli_real_escape_string($link, $_POST['logged_name']);
    $break_name = mysqli_real_escape_string($link, $_POST['break_name']);
    $break_start_time = mysqli_real_escape_string($link, $_POST['break_start_time']);
    $break_end_time = mysqli_real_escape_string($link, $_POST['break_end_time']);

    // Prepare the SQL query to insert break details into the agent_break table
    $query = "INSERT INTO $db.agent_break (break_name, username, startdatetime, enddatetime) 
              VALUES ('$break_name', '$logged_name', '$break_start_time', '$break_end_time')";

    // Execute the query and check if it was successful
    if (mysqli_query($link, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Break details inserted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error inserting break details: ' . mysqli_error($link)]);
    }
}
function classify_agent($userid)
{
	global $db,$link;
	$query ="select i_classify from $db.uniuserprofile where AtxUserID='$userid'; ";
	$res =mysqli_query($link,$query);
	$row=mysqli_fetch_array($res);
	return $row['i_classify'];

}
function module_license($module){
	global $link,$db;
	$query = "SELECT module_flag FROM $db.module_license WHERE module_name='$module'";
	$res =mysqli_query($link,$query);
	$row=mysqli_fetch_array($res);
	return $row['module_flag'];
}
//  for  login redirection on the basis of the group id in case of dashboard[03-04-2025][vastvikta]
function module_license_id($module, $groupId) {
    global $link, $db;

    // Query to check if the given module has the specified groupId
    $query = "SELECT group_Id FROM $db.module_license WHERE module_name='$module' AND FIND_IN_SET('$groupId', group_Id) > 0";

    $result = mysqli_query($link, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return 1; // Group ID found
    } else {
        return 0; // Group ID not found
    }
}

//  added the function for cheking licence module for admin and dashboard option [vastvikta][29-03-2025]
function module_license2($module, $groupId) {
    global $link, $db;
    
    $query = "SELECT * FROM $db.module_license WHERE module_name='$module'";
    $res = mysqli_query($link, $query);
    
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_array($res);
        $datainfo['module_flag'] = $row['module_flag'];
        
        // Ensure group_Ids is an array with properly trimmed values
        $datainfo['group_Ids'] = isset($row['group_Id']) ? array_map('trim', explode(',', $row['group_Id'])) : [];
		

        return $datainfo;
    }
    
    return ['module_flag' => 0, 'group_Ids' => []]; // Default return in case of failure
}


function break_sched_start(){
	global $groupid, $startdatetime,$db,$link;
	if($groupid != '0000'){
	    $shift_date = date("Y-m-d H:i:s");
	    $agent_id = $_SESSION['unique_id'];
	    $sqlsource_count="select * from $db.tbl_wfm_agent_sched_instance where d_schedStartDate like '$startdatetime%' and i_agentID='$agent_id'";
	    $sourceresult_count=mysqli_query($link,$sqlsource_count);
	    $row_count=mysqli_fetch_array($sourceresult_count);
	    $row_count_row = mysqli_num_rows($sourceresult_count);
	    if($row_count_row>0){
	        $schdule_start_time = date("H:i",strtotime($row_count["d_schedStartDate"]));
	        $schdule_end_time = date("H:i",strtotime($row_count["d_schedEndDate"]));
	        $list = '';
	        $shift_break=explode(",",$row_count['v_breakList']);
	        for($s_break=0;$s_break<count($shift_break);$s_break++){ // for loop for comma (,) seperated values
	            $assigned_break_id=$shift_break[$s_break];
	            $sqlsource_abreak="select v_breakName,d_startbreak,d_endbreak from $db.tbl_wfm_mst_break where i_breakID='$assigned_break_id'";
	            $sourceresult_abreak=mysqli_query($link,$sqlsource_abreak);
	            while($row_abreak=mysqli_fetch_array($sourceresult_abreak)) 
	            {   
	                $brk_stime=date("H:i",strtotime($row_abreak['d_startbreak']));
	                $brk_etime=date("H:i",strtotime($row_abreak['d_endbreak']));
	                $list .= "<li><stronge style=\"color: #d63384;\">Break  </stronge> : $brk_stime to $brk_etime</li>";
	            }
	        }
	    }
   } 
    $list['list']=$list;
    $list['schdule_start_time']=$schdule_start_time;
    $list['schdule_end_time']=$schdule_end_time;
   return $list;
}
function channel_license($userId){
	global $link,$db;
	$query = "SELECT * FROM $db.user_channel_assignment WHERE userid='$userId'";
	$res =mysqli_query($link,$query);
	$data = array();
	if (mysqli_num_rows($res) > 0) {
       while ($adminrow = mysqli_fetch_assoc($res)) {
       	  $data[$adminrow['channel_type']] = $adminrow['channel_type'];
       }
    }
	return $data;
}

function get_telephony_flag($userid) {
	global $link,$dbname;
	$query = $link->query("SELECT telephony_flag FROM $dbname.tbl_mst_user_company WHERE I_UserID ='$userid' ");
	$row = $query->fetch_row();
	return $row[0];
}