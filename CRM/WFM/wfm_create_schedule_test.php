<?
include("../web_mysqlconnect.php");
//print_r($centralspoc);
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
if(isset($_POST['get_schedule_data'])){	
	$process_id=$_POST['get_schedule_data'];
	$sql_get_sched="select * from $db.tbl_wfm_proc_schedule where i_procSchedID='$process_id' ";
	$query_sched=mysqli_query($link,$sql_get_sched);
	
	$query_sched_res=mysqli_fetch_array($query_sched);
	$start_time="";$end_time="";
	$start_time=$query_sched_res['d_startDate'];
	$end_time=$query_sched_res['d_endDate'];
	$sql_get_sched_list="select * from $db.tbl_wfm_proc_schedule_list where i_procSchedID='$process_id' ";
	$query_sched_list=mysqli_query($link,$sql_get_sched_list);
	while($row=mysqli_fetch_array($query_sched_list))
	{
		$shift_id="";$skill_id="";$no_of_agent="";$count_val=0;
		$shift_id=$row['i_shiftID'];
		$skill_id=$row['i_skillID'];
		$no_of_agent=$row['i_noOfAgents'];
		$break_id=$row['v_breakid'];
		// echo "i am here"; 
		$rows=get_pref_shift_skill($process_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id);
		$count_val=$no_of_agent-$rows;
		if($count_val>0)
		{
			$rows_pref_skill=get_pref_noshift_skill($process_id,$shift_id,$skill_id,$start_time,$end_time,$count_val,$db,$break_id);
			$count_val_pref_skill=$count_val-$rows_pref_skill;
			if($count_val_pref_skill>0)
			{
				$rows_pref_shift=get_pref_shift_moreskill($process_id,$shift_id,$skill_id,$start_time,$end_time,$count_val_pref_skill,$db,$break_id);
				$count_val_pref_shift=$count_val_pref_skill-$rows_pref_shift;
				if($count_val_pref_shift>0)
				{
					$rows_pref_moreskill=get_pref_noshift_moreskill($process_id,$shift_id,$skill_id,$start_time,$end_time,$count_val_pref_shift,$db,$break_id);
					$count_val_pref_moreskill=$count_val_pref_shift-$rows_pref_moreskill;
					if($count_val_pref_moreskill>0)
					{
						$rows_pref_noshift=get_pref_noshift_matchskill($process_id,$shift_id,$skill_id,$start_time,$end_time,$count_val_pref_moreskill,$db,$break_id);
						$count_val_pref_noshift=$count_val_pref_moreskill-$rows_pref_noshift;
						if($count_val_pref_noshift>0)
						{
							$rows_pref_noshift_moreskill=get_pref_assigned_noshift_moreskill($process_id,$shift_id,$skill_id,$start_time,$end_time,$count_val_pref_noshift,$db,$break_id);
							
						}
					}
				}
			}
		}


	}
	echo "Assigned Succesfully";
}

function get_pref_shift_skill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents not assigned in shift and having matching preferred shift and skills
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID not in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time') and i_shiftpref='$shift_id' and i_Skillset='$skill_id' and AtxUserStatus=1 and AtxDesignation='Agent' limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($link,$sql_query);
	while($sql_res=mysqli_fetch_array($sql_query))
	{
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','1','1')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;

}

function get_pref_noshift_skill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents not assigned in shift and not having matching preferred shift and matching skills
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID not in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time') and i_shiftpref!='$shift_id' and i_Skillset='$skill_id' and AtxUserStatus=1 and AtxDesignation='Agent'  limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($sql_query);
	while($sql_res=mysqli_fetch_array($sql_query))
	{
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','2','1')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;
}

function get_pref_shift_moreskill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents not assigned in shift and having matching preferred shift and more skills than required
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID not in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time') and i_shiftpref='$shift_id' and AtxUserStatus=1  and AtxDesignation='Agent' and FIND_IN_SET('$skill_id',i_Skillset)>0 limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($sql_query);
	while($sql_res=mysqli_fetch_array($sql_query)){
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','1','2')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;
}

function get_pref_noshift_moreskill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents not assigned in shift and not having matching preferred shift and more skills than required
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID not in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time') and i_shiftpref!='$shift_id' and AtxUserStatus=1 and AtxDesignation='Agent' and FIND_IN_SET('$skill_id',i_Skillset)>0 limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($sql_query);
	while($sql_res=mysqli_fetch_array($sql_query))
	{
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','2','2')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;
}

function get_pref_noshift_matchskill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents assigned in shift and not having matching preferred shift and  matching skills
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time' and i_shiftID!='$shift_id') and i_shiftpref!='$shift_id' and i_Skillset='$skill_id' and AtxUserStatus=1 and AtxDesignation='Agent' limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($sql_query);
	while($sql_res=mysqli_fetch_array($sql_query))
	{
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','2','1')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;
}

function get_pref_assigned_noshift_moreskill($sched_id,$shift_id,$skill_id,$start_time,$end_time,$no_of_agent,$db,$break_id)
{	
	global $db, $link;
	//**********get all agents not assigned in shift and not having matching preferred shift and more matching skills
	$sql_get_agents="select * from $db.uniuserprofile where AtxUserID in (select distinct(i_AgentID) from $db.tbl_wfm_agent_sched_assignment where d_startTime>='$start_time' and d_endTime<='$end_time' and i_shiftID!='$shift_id') and i_shiftpref!='$shift_id' and AtxUserStatus=1 and AtxDesignation='Agent' and FIND_IN_SET('$skill_id',i_Skillset)>0 limit $no_of_agent";
	$sql_query=mysqli_query($link,$sql_get_agents);
	$sql_rows=mysqli_num_rows($link,$sql_query);
	while($sql_res=mysqli_fetch_array($sql_query))
	{
		$agent_id=$sql_res['AtxUserID'];
		$sql_insert="insert into $db.tbl_wfm_agent_sched_assignment(i_procSchedID, i_shiftID, i_AgentID, d_startTime, 	d_endTime, v_breakList, shift_flag, skill_flag) values('$sched_id', '$shift_id', '$agent_id', '$start_time', '$end_time','$break_id','2','2')";
		mysqli_query($link,$sql_insert);
		create_instance($agent_id,$sched_id,$shift_id,$start_time,$end_time,$break_id,$db);
	}
	return $sql_rows;
}

function create_instance($agentid,$sched_id,$shift_id,$start_date,$end_date,$breakid,$db){
	global $db, $link;
	$no_days=dateDiff($end_date,$start_date);
	for($i=0;$i<=$no_days;$i++)
	{
		$sql_get_shifts="select t_fromTime,t_toTime from $db.tbl_wfm_mst_shift where i_shiftID=$shift_id";
		$sql_query_shifts=mysqli_query($link,$sql_get_shifts);
		$sql_res_shifts=mysqli_fetch_array($sql_query_shifts);

		$instance_date=date('Y-m-d', strtotime($start_date. ' + '.$i.' days'));
		$instance_startdate_time=date('Y-m-d H:i:s',strtotime($instance_date." ".$sql_res_shifts['t_fromTime']));
		$instance_enddate_time=date('Y-m-d H:i:s',strtotime($instance_date." ".$sql_res_shifts['t_toTime']));

		$sql_insert="insert into $db.tbl_wfm_agent_sched_instance(i_agentID, i_procSchedID, i_shiftID, d_schedStartDate, d_schedEndDate, d_actualStartTime, d_actualEndTime, v_breakList, i_status) values('$agentid', '$sched_id', '$shift_id', '$instance_startdate_time', '$instance_enddate_time', '$instance_startdate_time', '$instance_enddate_time', '$breakid', '0')";
		// echo $sql_insert;
		mysqli_query($link,$sql_insert);
	}
}


function dateDiff ($d1, $d2) {

    // Return the number of days between the two dates:    
    return round(abs(strtotime($d1) - strtotime($d2))/86400);

} // end function dateDiff



