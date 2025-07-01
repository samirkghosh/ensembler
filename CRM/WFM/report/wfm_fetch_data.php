<?php
/**
 * Author: Vastivkta Nishad
 * Date: 20 May 2024
 * Description: This file is used to fetch the data from the database using DataTables
 */

// Include database connection and functions with error handling
include("../../../config/web_mysqlconnect.php");
include("../wfm_function.php");

$wfm_funct = new Wfm_connection;


if($_POST['action'] =='shift_report'){
	shift_report();
}
if($_POST['action'] =='agentwise_report'){
	agentwise_report();
}
if($_POST['action'] =='adherence_report'){
	adherence_report();
}
if($_POST['action'] =='shift_report_hist'){
	shift_report_hist();
}
if($_POST['action'] =='agentwise_report_hist'){
	agentwise_report_hist();
}
if($_POST['action'] =='schedule_report_hist'){
	schedule_report_hist();
}

function shift_report(){
    global $db,$link,$wfm_funct;


    $i_procSchedID  = $_POST['i_procSchedID'];

    // SQL query to fetch the data
    $sql = "SELECT * FROM $db.tbl_wfm_proc_schedule_list WHERE 1=1";
    $total=mysqli_num_rows(mysqli_query($link,$sql));

    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $sql .= " LIMIT $start, $length";
    }
    $result = mysqli_query($link, $sql);

    $qq = "SELECT * FROM $db.tbl_wfm_proc_schedule_list ";
    $result = mysqli_query($link, $qq);
    $totalRecords=mysqli_num_rows(mysqli_query($link,$qq));

    
    $data = array();
    $serial = 0;

    while ($ticket_res = mysqli_fetch_assoc($result)) { 
        $serial++;
        $i_procSchedID = $ticket_res['i_procSchedID'];
        $i_shiftID = $ticket_res['i_shiftID'];
        $i_skillID = $ticket_res['i_skillID'];
        $total_agent_need = $ticket_res['i_noOfAgents'];
        $total_Assigned_agent = $wfm_funct->get_Agent_Assigned_For_Schedule($i_procSchedID, $i_shiftID);
        $Deficiency = ($total_agent_need - $total_Assigned_agent);
        $Exact_PreferredShift_Agent = $wfm_funct->get_AgentWith_PreferredShift($i_procSchedID, $i_shiftID, 1);
        $Non_PreferredShift_Agent = $wfm_funct->get_AgentWith_PreferredShift($i_procSchedID, $i_shiftID, 2);
        $Matching_skill_count = $wfm_funct->get_Agent_Skills($i_procSchedID, $i_skillID, 1, $i_shiftID);
        $Overmatching_skill_count = $wfm_funct->get_Agent_Skills($i_procSchedID, $i_skillID, 2, $i_shiftID);
        $Nonmatching_skill_count = $wfm_funct->get_Agent_Skills($i_procSchedID, $i_skillID, 3, $i_shiftID);

        $sub_array[] =  $wfm_funct->getProscheduleName($i_procSchedID) ;
        $sub_array[] = $wfm_funct->getShiftName($i_shiftID);
        $sub_array[] = $total_agent_need;
        $sub_array[] = $total_Assigned_agent;
        $sub_array[] = $Deficiency;
        $sub_array[] = $Exact_PreferredShift_Agent;
        $sub_array[] = $Non_PreferredShift_Agent;
        $sub_array[] = $Matching_skill_count;
        $sub_array[] = $Overmatching_skill_count;
        $sub_array[] = $Nonmatching_skill_count;
        
        $data[] = $sub_array;
    }
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $total, 
        "data" => $data
    );

    echo json_encode($output);
}
function agentwise_report(){
    global $db, $link,$wfm_funct;



    $sql = "select * from $db.tbl_wfm_agent_sched_assignment ";


    $AtxUserID = $_POST['AtxUserID'];

    if($AtxUserID!="")
    {
        $whr_cond_agent=" AND i_AgentID='".$AtxUserID."' ";
    
    }
    $qq = "select * from $db.tbl_wfm_agent_sched_assignment  where 1=1 $whr_cond_agent ";
    $total=mysqli_num_rows(mysqli_query($link,$qq));
    
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $qq .= " LIMIT $start, $length";
    }
    $ticket_query = mysqli_query($link,$qq);
       
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);
    


    $data = array();

    while($ticket_res = mysqli_fetch_assoc($ticket_query)) { 
        $no++;
        $i_procSchedID=$ticket_res['i_procSchedID'];
        $assign_shiftID=$ticket_res['i_shiftID'];
        $i_skillID=$ticket_res['i_skillID'];
        $i_AgentID=$ticket_res['i_AgentID'];
        $skill_flag=$ticket_res['skill_flag'];
        
        $sql_uni_profile="SELECT i_Skillset,i_shiftpref FROM $db.uniuserprofile where AtxUserID='".$i_AgentID."' ";
        $query_uniprofile=mysqli_query($link,$sql_uni_profile);
        $fetch_uniprofile=mysqli_fetch_array($query_uniprofile);
        $i_Skillset=$fetch_uniprofile['i_Skillset'];
        $array_skills=explode(',',$i_Skillset);
        $skill_cnt=count($array_skills);
        if($skill_cnt>1)
        {
            $db_skill_val = $wfm_funct->to_string($array_skills);
        }else{
            $db_skill_val = $wfm_funct->getSkillName($i_Skillset);
        }

        $i_shiftpref=$fetch_uniprofile['i_shiftpref'];

        $sub_array[] = $wfm_funct->displayagentname($ticket_res['i_AgentID']);
        $sub_array[] = $db_skill_val;
        $sub_array[] = $wfm_funct->getShiftName($i_shiftpref);
        $sub_array[] = $wfm_funct->getShiftName($assign_shiftID);
        $sub_array[] = $wfm_funct->skill_flag($skill_flag);
        $sub_array[] = $ticket_res['d_startTime'];
        $sub_array[] = $ticket_res['d_endTime'];
        $data[] = $sub_array;

    }

    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $total, 
        "data" => $data
    );

    echo json_encode($output);
}
function adherence_report(){
    global $db, $link,$wfm_funct;

    $qq = "select * from $db.tbl_wfm_agent_sched_instance";
    $result = mysqli_query($link, $qq);
    $totalRecords=mysqli_num_rows(mysqli_query($link,$qq));

    $startdatetime  = $_POST['startdatetime'];
    	
		if(!empty($startdatetime)){ 
			$sttartdatetime1=explode(" ",$startdatetime);
				$startdatetime=$sttartdatetime1[0]; 
		}else{  
				$startdatetime=date('Y-m-d');
		 }
		
		$datefilter="";$datefilter_1="";
		if( $startdatetime!="" ){			
			$from=date('Y-m-d',strtotime($startdatetime));			
			if($startdatetime!=''){ 
				$datefilter .=" and DATE(d_schedStartDate)='$from'   ";
				
				$datefilter_1 .=" and DATE(d_breakStTime)='$from'   ";

				$datefilter_2 .=" and DATE(d_CreadtedDate)='$from'   ";

				$datefilter_3 .=" and DATE(AccessedAt)='$from'   ";
									
			 }
		}
		$qq = "select * from $db.tbl_wfm_agent_sched_instance  where 1=1 $datefilter ";
		// echo $qq;
        $total=mysqli_num_rows(mysqli_query($link,$qq));
        
        if ($_POST["length"] != -1) {
            $start = intval($_POST['start']);
            $length = intval($_POST['length']);
            $qq .= " LIMIT $start, $length";
        }
		$ticket_query = mysqli_query($link,$qq);
		$no=0;$total_log_time="";
        $data = array();
        while ($ticket_res = mysqli_fetch_array($ticket_query)) {
            $no++;
            $agent_name = $wfm_funct->getUserName($ticket_res['i_agentID']);
            if ($agent_name != "") {
                $cond_agent = " AND v_AgentName='" . $agent_name . "' ";
                $cond_agent1 = " AND UserName='" . $agent_name . "' ";
            }
        
            // Query to fetch AccessedAt and TimePeriod
            $qq = "SELECT AccessedAt, TimePeriod FROM $db.logip WHERE UserName != '' $datefilter_3 $cond_agent1 ORDER BY AccessedAt DESC";
            $agent_q = mysqli_query($link, $qq);
        
            $fetch_a = mysqli_fetch_array($agent_q);
            $intime = $fetch_a['AccessedAt'];
            $outtime = $fetch_a['TimePeriod'];
        
            $logintime = 0;
            if ($outtime == "" && $intime != '') {
                $current_date = date("Y-m-d H:i:s");
                $currentDate = strtotime($current_date);
        
                $lastlogintime = strtotime($intime);
        
                $logintime = ($currentDate - $lastlogintime);
            } else {
                $logintime = 0;
            }
        
            $qq_serviceLevel = "select i_loginTime,i_breakTime from $db. tbl_servicelevel  where 1=1 $datefilter_2 $cond_agent ";
            $query_slevel = mysqli_query($link, $qq_serviceLevel);
            $fetch_sl = mysqli_fetch_array($query_slevel);
            $num_rec_servicelevel = mysqli_num_rows($query_slevel);
        
            if ($fetch_sl['i_loginTime'] != '' && $num_rec_servicelevel != 0) {
                $total_log_time = ($fetch_sl['i_loginTime'] - $fetch_sl['i_breakTime']);
                $total_log_time = $total_log_time + $logintime;
        
                $tothours = floor($total_log_time / 3600);
                $totminutes = floor(($total_log_time / 60) % 60);
                $totseconds = $total_log_time % 60;
                $totalhour = $tothours . ":" . $totminutes . ":" . $totseconds;
            } else if ($logintime != 0) {
                $tothours = floor($logintime / 3600);
                $totminutes = floor(($logintime / 60) % 60);
                $totseconds = $logintime % 60;
                $totalhour = $tothours . ":" . $totminutes . ":" . $totseconds;
            }
        
            if ($ticket_res['i_agentID'] != '' && $startdatetime != '') {
                $sql_aht = "SELECT 
                                (`F1` + `F2` + `F3` + `F4` + `F5` + `F6` + `F7` + `F8` + `F9` + `F10` + `F11` + `F12` + `F13` + `F14` + `F15` + `F16` + `F17` + `F18` + `F19` + `F20` + `F21` + `F22` + `F23` + `F24`) AS hourcall,
                                `i_talkTime` 
                            FROM $db.tbl_servicelevel 
                            WHERE 1=1 $datefilter_2 $cond_agent";
                $q_aht = mysqli_query($link, $sql_aht) or die(mysqli_error($link));
                $fetch_aht = mysqli_fetch_array($q_aht);
                $avg = $fetch_aht['i_talkTime'] / $fetch_aht['hourcall'];
                $averageHandlingTime = gmdate("H:i:s", $avg);
            } else {
                $averageHandlingTime = "00:00:00";
            }
        
            // Initialize the sub_array with agent details
            $sub_array[] = $wfm_funct->displayagentname($ticket_res['i_agentID']);
            $sub_array[] = $wfm_funct->getShiftName($ticket_res['i_shiftID']);
            $sub_array[] = $wfm_funct->getProscheduleName($ticket_res['i_procSchedID']);
            $sub_array[] = $ticket_res['d_schedStartDate'];
            $sub_array[] = $ticket_res['d_schedEndDate'];
            $sub_array[] = $ticket_res['d_actualStartTime'];
            $sub_array[] = $ticket_res['d_actualEndTime'];
            $sub_array[] = ($ticket_res['i_status'] == 1) ? "Yes" : "No";
            $sub_array[] = $wfm_funct->displayagentname($ticket_res['substitute_id']);
            $sub_array[] = (!empty($fetch_a['AccessedAt']) ? $intime : '');
            $sub_array[] = $outtime;
            $sub_array[] = $totalhour;
            $sub_array[] = $averageHandlingTime;

        
            $data[] = $sub_array;
        
            $v_breakList = $ticket_res['v_breakList'];
            if ($v_breakList) {
                $breaklist_array = explode(',', $v_breakList);
                if (count($breaklist_array) >= 1) {
                    foreach ($breaklist_array as $key => $item) {
                        $sql_break = mysqli_query($link, "SELECT v_breakName,d_startbreak,d_endbreak FROM $db.tbl_wfm_mst_break WHERE i_breakID='$item'");
                        $fetch_break = mysqli_fetch_array($sql_break);
        
                        $q_actual_breaktime = "SELECT d_breakStTime,d_breakEndTime FROM $db.tbl_wfm_break_instance  
                                               WHERE i_breakID='$item' AND i_agentID='" . $ticket_res['i_agentID'] . "' 
                                               AND i_procSchedID='" . $ticket_res['i_procSchedID'] . "'  AND i_shiftID='" . $ticket_res['i_shiftID'] . "'
                                               $datefilter_1";
                        $query_actualbreaktime = mysqli_query($link, $q_actual_breaktime);
                        $fetch_actualbreaktime = mysqli_fetch_array($query_actualbreaktime);
        
                        $d_breakStTime = explode(' ', $fetch_actualbreaktime['d_breakStTime']);
                        $d_breakEndTime = explode(' ', $fetch_actualbreaktime['d_breakEndTime']);
        
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = "";
                        $break_sub_array[] = $fetch_break['v_breakName'];
                        $break_sub_array[] = $fetch_break['d_startbreak'];
                        $break_sub_array[] = $fetch_break['d_endbreak'];
                        $break_sub_array[] = $d_breakStTime[1];
                        $break_sub_array[] = $d_breakEndTime[1];
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = "";
                        $break_sub_array[] = "";
                        $break_sub_array[] = "";
                        $break_sub_array[] = "";
                        $data[] = $break_sub_array;
                    }
                }
            }
        }
        

    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $total, 
        "data" => $data
    );

    echo json_encode($output);
}
function shift_report_hist(){
    global $db, $link, $wfm_funct;

    $i_procSchedID = $_POST['i_procSchedID'];
    $cond_sc = '';
    if ($i_procSchedID != '') {
        $cond_sc = " WHERE i_procSchedID = '" . mysqli_real_escape_string($link, $i_procSchedID) . "'";
    }

    $sqlTotal = "SELECT *  FROM $db.tbl_wfm_proc_schedule_list_hist ";

    $totalRecords = mysqli_num_rows(mysqli_query($link, $sqlTotal));
    $rowTotal = mysqli_fetch_assoc($resultTotal);
   
    $sqlData = "SELECT * FROM $db.tbl_wfm_proc_schedule_list_hist $cond_sc";

    $resultData = mysqli_query($link, $sqlData);

    $total=mysqli_num_rows(mysqli_query($link,$sqlData));

    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $sqlData .= " LIMIT $start, $length";
    }
    $resultData = mysqli_query($link, $sqlData);

    $data = array();
    while ($ticket_res = mysqli_fetch_assoc($resultData)) { 
        $no++;
        $i_procSchedID=$ticket_res['i_procSchedID'];
        $i_shiftID=$ticket_res['i_shiftID'];
        $i_skillID=$ticket_res['i_skillID'];
        $total_agent_need=$ticket_res['i_noOfAgents'];
        $total_Assigned_agent=$wfm_funct->get_Agent_Assigned_For_Schedule_hist($i_procSchedID,$i_shiftID);
        $Deficiency=($total_agent_need-$total_Assigned_agent);
        $Exact_PreferredShift_Agent=$wfm_funct->get_AgentWith_PreferredShift_hist($i_procSchedID,$i_shiftID,1);//value 1 for exact match
        $Non_PreferredShift_Agent=$wfm_funct->get_AgentWith_PreferredShift_hist($i_procSchedID,$i_shiftID,2);//value 2 for nonpreffered shift
        $Matching_skill_count=$wfm_funct->get_Agent_Skills_hist($i_procSchedID,$i_skillID,1,$i_shiftID);//value 1 for match
        $Overmatching_skill_count=$wfm_funct->get_Agent_Skills_hist($i_procSchedID,$i_skillID,2,$i_shiftID);//value 2 for over skill match
        $Nonmatching_skill_count=$wfm_funct->get_Agent_Skills_hist($i_procSchedID,$i_skillID,3,$i_shiftID);//value 3 for not match
			
    
        $sub_array[] = $wfm_funct->getProscheduleName_hist($ticket_res['i_procSchedID']);
        $sub_array[] = $wfm_funct->getShiftName_hist($ticket_res['i_shiftID']);
        $sub_array[] = $ticket_res['i_noOfAgents'];
        $sub_array[] = $total_Assigned_agent;
        $sub_array[] = $Deficiency;
        $sub_array[] = $Exact_PreferredShift_Agent;
        $sub_array[] = $Non_PreferredShift_Agent;
        $sub_array[] = $Matching_skill_count;
        $sub_array[] = $Overmatching_skill_count;
        $sub_array[] = $Nonmatching_skill_count;
        $sub_array[] = $ticket_res['del_Date'];
        $data[] = $sub_array;
    }
    
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $total, 
        "data" => $data
    );

    echo json_encode($output);
}
function agentwise_report_hist(){
    global $db,$link,$wfm_funct;


    $sql = "SELECT * FROM $db.tbl_wfm_agent_sched_assignment_hist";
    $ticket_query = mysqli_query($link,$sql);
    $totalRecords=mysqli_num_rows(mysqli_query($link,$sql));

    if($_REQUEST['AtxUserID']!="")
    {
        $whr_cond_agent=" AND i_AgentID='".$_REQUEST['AtxUserID']."' ";
    }				
    $qq = "select * from $db.tbl_wfm_agent_sched_assignment_hist  where 1=1 $whr_cond_agent ";
    $total=mysqli_num_rows(mysqli_query($link,$qq));

    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $qq .= " LIMIT $start, $length";
    }
    $ticket_query = mysqli_query($link,$qq);
    
    
    $data = array();
    while($ticket_res = mysqli_fetch_assoc($ticket_query)) { 
        $no++;
        $i_procSchedID=$ticket_res['i_procSchedID'];
        $assign_shiftID=$ticket_res['i_shiftID'];
        $i_skillID=$ticket_res['i_skillID'];
        $i_AgentID=$ticket_res['i_AgentID'];
        $skill_flag=$ticket_res['skill_flag'];
        $procSched=$ticket_res['i_procSchedID'];
        
        $sql_uni_profile="SELECT i_Skillset,i_shiftpref FROM $db.uniuserprofile where AtxUserID='".$i_AgentID."' ";
        $query_uniprofile=mysqli_query($link,$sql_uni_profile);
        $fetch_uniprofile=mysqli_fetch_array($query_uniprofile);
        $i_Skillset=$fetch_uniprofile['i_Skillset'];
        $array_skills=explode(',',$i_Skillset);
        $skill_cnt=count($array_skills);
        if($skill_cnt>1)
        {
            $db_skill_val = $wfm_funct->to_string($array_skills);
        }else{
            $db_skill_val = $wfm_funct->getSkillName_hist($i_Skillset);
        }
        $i_shiftpref=$fetch_uniprofile['i_shiftpref'];


        $sub_array[] = $wfm_funct->displayagentname($ticket_res['i_AgentID']);
        $sub_array[] = $wfm_funct->getProscheduleName_hist($procSched);
        $sub_array[] = $db_skill_val;
        $sub_array[] = $wfm_funct->getShiftName_hist($i_shiftpref);
        $sub_array[] = $wfm_funct->getShiftName_hist($assign_shiftID);
        $sub_array[] = $wfm_funct->skill_flag($skill_flag);
        $sub_array[] = $ticket_res['d_startTime'];
        $sub_array[] = $ticket_res['d_endTime'];
        $sub_array[] = $ticket_res['del_Date'];
        $data[] = $sub_array;
    }
      
    $output = array(
        "draw"            => intval($_POST["draw"]),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $total, 
        "data" => $data
    );

    echo json_encode($output);
}
function schedule_report_hist(){
    global $db, $link, $wfm_funct;

    $startdatetime = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : '';

    $sql = "SELECT * FROM $db.tbl_wfm_agent_sched_instance_hist";
    $ticket_query = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($ticket_query);

    $startdatetime = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
    if (!empty($startdatetime)) {
        $startdatetime1 = explode(" ", $startdatetime);
        $startdatetime = $startdatetime1[0];
    }

    $datefilter = "";
    $datefilter_1 = "";
    $datefilter_2 = "";
    $datefilter_3 = "";

    if ($startdatetime != "") {
        $from = date('Y-m-d', strtotime($startdatetime));
        $datefilter .= " and DATE(d_schedStartDate)='$from'";
        $datefilter_1 .= " and DATE(d_breakStTime)='$from'";
        $datefilter_2 .= " and DATE(d_CreadtedDate)='$from'";
        $datefilter_3 .= " and DATE(AccessedAt)='$from'";
    }

    $qq = "SELECT * FROM $db.tbl_wfm_agent_sched_instance_hist WHERE 1=1 $datefilter";
    $total = mysqli_num_rows($ticket_query);
   
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $qq .= " LIMIT $start, $length";
    }
    $ticket_query = mysqli_query($link, $qq);
    $data = [];
    $no = 0;
    $total_log_time = "";

    if (!$ticket_query) {
        error_log("MySQL Error: " . mysqli_error($link)); // Log MySQL error
        return; // Exit function to prevent further execution
    }

    while ($ticket_res = mysqli_fetch_array($ticket_query)) {
        $no++;

        $agent_name = $wfm_funct->getUserName($ticket_res['i_agentID']);
        if ($agent_name != "") {
            $cond_agent = " AND v_AgentName='" . $agent_name . "'";
            $cond_agent1 = " AND UserName='" . $agent_name . "'";
        }

        $qq = "SELECT AccessedAt, TimePeriod FROM $db.logip WHERE UserName!='' $datefilter_3 $cond_agent1 ORDER BY AccessedAt DESC";
        $agent_q = mysqli_query($link, $qq);
        $fetch_a = mysqli_fetch_array($agent_q);
        $intime = $fetch_a['AccessedAt'];
        $outtime = $fetch_a['TimePeriod'];

        $logintime = 0;
        if ($outtime == "" && $intime != '') {
            $current_date = date("Y-m-d H:i:s");
            $currentDate = strtotime($current_date);
            $lastlogintime = strtotime($intime);
            $logintime = ($currentDate - $lastlogintime);
        }

        $qq_serviceLevel = "SELECT i_loginTime, i_breakTime FROM $db.tbl_servicelevel WHERE 1=1 $datefilter_2 $cond_agent";
        $query_slevel = mysqli_query($link, $qq_serviceLevel);
        $fetch_sl = mysqli_fetch_array($query_slevel);
        $num_rec_servicelevel = mysqli_num_rows($query_slevel);

        if ($fetch_sl['i_loginTime'] != '' && $num_rec_servicelevel != 0) {
            $total_log_time = ($fetch_sl['i_loginTime'] - $fetch_sl['i_breakTime']) + $logintime;
        } else if ($logintime != 0) {
            $total_log_time = $logintime;
        }

        if ($total_log_time != "") {
            $tothours = floor($total_log_time / 3600);
            $totminutes = floor(($total_log_time / 60) % 60);
            $totseconds = $total_log_time % 60;
            $totalhour = $tothours . ":" . $totminutes . ":" . $totseconds;
        } else {
            $totalhour = "00:00:00";
        }

        if ($ticket_res['i_agentID'] != '' && $startdatetime != '') {
            $sql_aht = "SELECT 
                            (`F1` + `F2` + `F3` + `F4` + `F5` + `F6` + `F7` + `F8` + `F9` + `F10` + `F11` + `F12` + `F13` + `F14` + `F15` + `F16` + `F17` + `F18` + `F19` + `F20` + `F21` + `F22` + `F23` + `F24`) AS hourcall,
                            `i_talkTime` 
                        FROM $db.tbl_servicelevel 
                        WHERE 1=1 $datefilter_2 $cond_agent";
            $q_aht = mysqli_query($link, $sql_aht) or die(mysqli_error($link));
            $fetch_aht = mysqli_fetch_array($q_aht);
            $avg = $fetch_aht['i_talkTime'] / $fetch_aht['hourcall'];
            $averageHandlingTime = gmdate("H:i:s", $avg);
        } else {
            $averageHandlingTime = "00:00:00";
        }

        // Initial sub_array for agent details
        $sub_array[] = $wfm_funct->displayagentname($ticket_res['i_agentID']);
        $sub_array[] = $wfm_funct->getShiftName_hist($ticket_res['i_shiftID']);
        $sub_array[] = $wfm_funct->getProscheduleName_hist($ticket_res['i_procSchedID']);
        $sub_array[] = "";
        $sub_array[] = $ticket_res['d_schedStartDate'];
        $sub_array[] = $ticket_res['d_schedEndDate'];
        $sub_array[] = $ticket_res['d_actualStartTime'];
        $sub_array[] = $ticket_res['d_actualEndTime'];
        $sub_array[] = ($ticket_res['i_status'] == 1) ? "Yes" : "No";
        $sub_array[] = $wfm_funct->displayagentname($ticket_res['substitute_id']);
        $sub_array[] = (!empty($fetch_a['AccessedAt']) ? $intime : '');
        $sub_array[] = $outtime;
        $sub_array[] = $totalhour;
        $sub_array[] = $averageHandlingTime;
        $sub_array[] = $ticket_res['del_Date'];

        // Add sub_array to data before processing breaks
        $data[] = $sub_array;

        // Process break list if available
        $v_breakList = $ticket_res['v_breakList'];
        if ($v_breakList) {
            $breaklist_array = explode(',', $v_breakList);
            if (count($breaklist_array) >= 1) {
                foreach ($breaklist_array as $item) {
                    $sql_break = mysqli_query($link, "SELECT v_breakName, d_startbreak, d_endbreak FROM $db.tbl_wfm_mst_break_hist WHERE i_breakID='$item'");
                    $fetch_break = mysqli_fetch_array($sql_break);

                    $q_actual_breaktime = "SELECT d_breakStTime, d_breakEndTime FROM $db.tbl_wfm_break_instance_hist  
                                           WHERE i_breakID='$item' AND i_agentID='" . $ticket_res['i_agentID'] . "' 
                                           AND i_procSchedID='" . $ticket_res['i_procSchedID'] . "' AND i_shiftID='" . $ticket_res['i_shiftID'] . "'
                                           $datefilter_1";
                    $query_actualbreaktime = mysqli_query($link, $q_actual_breaktime);
                    $fetch_actualbreaktime = mysqli_fetch_array($query_actualbreaktime);

                    if (!empty($fetch_actualbreaktime)) {
                        $d_breakStTime = explode(' ', $fetch_actualbreaktime['d_breakStTime']);
                        $d_breakEndTime = explode(' ', $fetch_actualbreaktime['d_breakEndTime']);

                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = "";
                        $break_sub_array[] = $fetch_break['v_breakName'];
                        $break_sub_array[] = $fetch_break['d_startbreak'];
                        $break_sub_array[] = $fetch_break['d_endbreak'];
                        $break_sub_array[] = $d_breakStTime[1];
                        $break_sub_array[] = $d_breakEndTime[1];
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = "";
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $break_sub_array[] = ""; 
                        $data[] = $break_sub_array;
                    }
                }
            }
        }
    }

    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $total,
        "data" => $data
    );

    echo json_encode($output);
}

?>
