<?
include("../../config/web_mysqlconnect.php");


$sel = $_POST['sel'];
if(isset($_POST['get_break_list']))
{

$get_break_list = $_POST['get_break_list'];

?>


	<option value="" selected="">Select Break</option>
	<?
	$break_query = mysqli_query($link,"select i_breakID,v_breakName,d_startbreak,d_endbreak from $db.tbl_wfm_mst_break where i_breakType='1' and i_status='1';");
	while($break_res = mysqli_fetch_array($break_query)):
	
	?><option value='<?=$break_res["i_breakID"]?>' <? if($sel==$break_res["i_breakID"]){ echo 'selected'; } ?> title="(<?=$break_res['d_startbreak']?>-<?=$break_res['d_endbreak']?>)"><?=$break_res["v_breakName"]." ( ".$break_res["d_startbreak"]." - ".$break_res["d_endbreak"]." )"?></option><?
	
	endwhile;
	?>


<? 

}

if(isset($_POST['get_agent_list']))
{

$get_agent_list = $_POST['get_agent_list'];

?>


	<option value="" selected="">Select Agent</option>
	<?
	$agent_query = mysqli_query($link,"select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserID!='$get_agent_list'");
	while($agent_res = mysqli_fetch_array($agent_query)):
	
	?><option value='<?=$agent_res["AtxUserID"]?>' <? if($sel==$agent_res["AtxUserID"]){ echo 'selected'; } ?>><?=$agent_res["AtxDisplayName"]?></option><?
	
	endwhile;
	
}

if(isset($_POST['get_agents_val']))
{

	$get_agents_val = $_POST['get_agents_val'];
	$shift_id = $_POST['shift_id'];
	$sched_id = $_POST['sched_id'];
	$from_time = date("Y-m-d H:i:s",strtotime($_POST['fromdate']));
	$to_time = date("Y-m-d H:i:s",strtotime($_POST['todate']));

	$from_date = date("Y-m-d",strtotime($_POST['fromdate']));
	$to_date = date("Y-m-d",strtotime($_POST['todate']));

	$cond = $_POST['cond'];
	?>


	<option value="" selected="">Select Agent</option>
	<?
	 // echo "select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_agentID!='$get_agents_val' and i_shiftID!='$shift_id' and i_procSchedID!='$sched_id' and i_status='0'";
	?><!-- <script>alert("select distinct(i_agentID) from <?=$db?>.tbl_wfm_agent_sched_instance where i_agentID not in (select distinct(i_agentID) from <?=$db?>.tbl_wfm_agent_sched_instance where i_shiftID='<?=$shift_id?>' and i_status='0' and d_actualStartTime>='$from_time' and d_actualEndTime<='$to_time')");</script> --><?
	$shift_list="";
	// $sel_ashift_query="select distinct(i_shiftID) from $db.tbl_wfm_agent_sched_instance where i_agentID='$get_agents_val' and date(d_actualStartTime)>='$from_date' and date(d_actualEndTime)<='$to_date' ";
	// $q_ashift_query=mysqli_query($link,$sel_ashift_query);
	// $num_row=mysqli_num_rows($q_ashift_query);
	// if($num_row)
	// {

	// 	$cond_whr=" and i_shiftID not in ($sel_ashift_query)";
	// }
	// $sel_ashift_query = mysqli_query($link,"select distinct(i_shiftID) from $db.tbl_wfm_agent_sched_instance where i_agentID='$get_agents_val' and date(d_actualStartTime)>='$from_date' and date(d_actualEndTime)<='$to_date' ");

	// while($ashift_res = mysqli_fetch_array($sel_ashift_query))
	// {
	// 	$shift_list.=$ashift_res['i_shiftID'].",";
	// }
if($cond==1)//reassign
{
	$sel_ashift_query="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_shiftID='$shift_id' and  d_actualStartTime>='$from_time' and d_actualEndTime<='$to_time'";
	$q_ashift_query=mysqli_query($link,$sel_ashift_query);
	$num_row=mysqli_num_rows($q_ashift_query);
	if($num_row)
	{

		$cond_whr=" and AtxUserID not in ($sel_ashift_query)";
	}

?>
	<!-- <script>alert("select distinct(AtxUserID)  as i_agentID from <?=$db?>.uniuserprofile where AtxDesignation='Agent' <?=$cond_whr?> ");</script> --><?



	 $sel_agent_query ="select distinct(AtxUserID) as i_agentID from $db.uniuserprofile where AtxDesignation='Agent' and AtxUserID!='$get_agents_val' $cond_whr ";
	$q_agent_query=mysqli_query($link,$sel_agent_query);

	while($agent_res = mysqli_fetch_array($q_agent_query))
	{
		$agent=$agent_res["i_agentID"];
		$agent_query = mysqli_query($link,"select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxUserID!='$get_agents_val' and AtxDisplayName!=''  and AtxUserID='$agent' order by AtxDisplayName");
		$agent_name_res = mysqli_fetch_array($agent_query);
		?><option value='<?=$agent_name_res["AtxUserID"]?>' <? if($sel==$agent_name_res["AtxUserID"]){ echo 'selected'; } ?>><?=$agent_name_res["AtxDisplayName"]?></option><?
		
	}
}
else if($cond==2)//Swap user
{
	$get_agent_shift_id="select distinct(i_shiftID) from $db.tbl_wfm_agent_sched_instance where i_agentID='$get_agents_val' and  d_actualStartTime>='$from_time' and d_actualEndTime<='$to_time'";

	$sel_ashift_query="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where   i_shiftID in ($get_agent_shift_id) and  d_actualStartTime>='$from_time' and d_actualEndTime<='$to_time'";
	$q_ashift_query=mysqli_query($link,$sel_ashift_query);
	$num_row=mysqli_num_rows($q_ashift_query);
	if($num_row)
	{

		$cond_whr=" and i_agentID not in ($sel_ashift_query)";
	}
	$sel_agent_query ="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_agentID!='$get_agents_val' $cond_whr ";
	$q_agent_query=mysqli_query($link,$sel_agent_query);

	while($agent_res = mysqli_fetch_array($q_agent_query))
	{
		$agent=$agent_res["i_agentID"];
		$agent_query = mysqli_query($link,"select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserID='$agent' order by AtxDisplayName");
		$agent_name_res = mysqli_fetch_array($agent_query);
		?><option value='<?=$agent_name_res["AtxUserID"]?>' <? if($sel==$agent_name_res["AtxUserID"]){ echo 'selected'; } ?>><?=$agent_name_res["AtxDisplayName"]?></option><?
		
	}

}
	
}

if(isset($_POST['agent_details_id']))
{

$agent_details_id = $_POST['agent_details_id'];
$shift_details_id = $_POST['shift_details_id'];

	$agent_detail_query = mysqli_query($link,"select d_startTime,d_endTime,v_breakList,i_shiftID from $db.tbl_wfm_agent_sched_assignment where i_AgentID='$agent_details_id' and i_shiftID='$shift_details_id'");
	$agent_detail_res = mysqli_fetch_array($agent_detail_query);
	$shift_ids = $agent_detail_res['i_shiftID'];
	$shiftname = '';
	if($shift_ids){
		$shift_query = mysqli_query($link,"select * from $db.tbl_wfm_mst_shift where i_shiftID='$shift_ids'");
		$shift_query_res = mysqli_fetch_array($shift_query);
		$shiftname = $shift_query_res['v_shiftName'];
	}
	echo $agent_detail_res["d_startTime"]."|".$agent_detail_res["d_endTime"]."|".$agent_detail_res["v_breakList"] . "|".$shiftname;
}


if(isset($_POST['sched_id']))
{

	$sched_id = $_POST['sched_id'];

	$sched_detail_query = mysqli_query($link,"select * from $db.tbl_wfm_proc_schedule where i_procSchedID='$sched_id'");
	$sched_detail_res = mysqli_fetch_array($sched_detail_query);
	echo $sched_detail_res["v_schedName"]."|".$sched_detail_res["i_noOfShift"]."|".$sched_detail_res["v_shiftList"]."|".$sched_detail_res["d_startDate"]."|".$sched_detail_res["d_endDate"];
}

if(isset($_POST['sched_id_details']))
{

$sched_id = $_POST['sched_id_details'];
$shift_id = $_POST['shift_id'];

	$sched_detail_query = mysqli_query($link,"select * from $db.tbl_wfm_proc_schedule_list where i_procSchedID='$sched_id' and i_shiftID='$shift_id'");
	$sched_detail_res = mysqli_fetch_assoc($sched_detail_query);
	echo $sched_detail_res["i_shiftID"]."|".$sched_detail_res["i_skillID"]."|".$sched_detail_res["i_noOfAgents"]."|".$sched_detail_res["v_breakid"];
}

if(isset($_POST['brk_id']))
{
    $brk_id = $_POST['brk_id'];

    $break_query = mysqli_query($link, "SELECT * FROM $db.tbl_wfm_mst_break WHERE i_breakID='$brk_id';");

    if ($break_query) {
        $break_res = mysqli_fetch_array($break_query);

        if ($break_res) {
            echo $break_res["i_breakType"] . "|" . $break_res["v_breakName"] . "|" . $break_res["d_startbreak"] . "|" . $break_res["d_endbreak"];
        } else {
            echo "Error: No data found for brk_id $brk_id";
        }
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

if(isset($_POST['get_agents_val_sch'])){

	$get_agents_val = $_POST['get_agents_val'];
	$shift_id = $_POST['shift_id'];
	$sched_id = $_POST['sched_id'];
	$from_time = date("Y-m-d H:i:s",strtotime($_POST['fromdate']));
	$to_time = date("Y-m-d H:i:s",strtotime($_POST['todate']));
	$from_date = date("Y-m-d",strtotime($_POST['fromdate']));
	$to_date = date("Y-m-d",strtotime($_POST['todate']));
	$cond = $_POST['cond'];
	$select = '';
 	if($cond==3){
		$shift_id = $_POST['shift_id'];
		$querys = "select * from $db.tbl_wfm_mst_shift where i_shiftID in (select distinct(i_shiftID) from $db.tbl_wfm_proc_schedule_list where i_shiftID !='$shift_id')";
		$res_new=mysqli_query($link,$querys);
		$select .= '<option value="select">Select Shift</option>';
		while($respo = mysqli_fetch_array($res_new)){
			$select .= "<option value='".$respo['i_shiftID']."'>".$respo['v_shiftName']."</option>";
		}
		echo $select;
	}
}
