<?php
include("../web_mysqlconnect.php");
$content="";
if(isset($_REQUEST['get_agents_val']))
{

$sch_id = $_REQUEST['sch_id'];
$agents_id = $_REQUEST['get_agents_val'];


$data = array();

 $query = "SELECT * FROM $db.tbl_wfm_agent_sched_instance where i_agentID='$agents_id' and i_procSchedID='$sch_id'";
$sql_query_get=mysqli_query($link,$query);

while($row=mysqli_fetch_array($sql_query_get))
{
$shift_id=$row["i_shiftID"];
$query_shift = "SELECT * FROM $db.tbl_wfm_mst_shift where i_shiftID='$shift_id'";
$sql_query_shift=mysqli_query($link,$query_shift);
$row_shift=mysqli_fetch_array($sql_query_shift);

//checking for leave, 1=leave and 0=no leave
if($row['i_status']==1)
{
$content="Leave"."\r\n".date("H:i:s",strtotime($row["d_schedStartDate"]))." to ".date("H:i:s",strtotime($row["d_schedEndDate"]));

$class_leave="fc-leave";
}
else
{
$content=$row_shift["v_shiftName"]."\r\n".date("H:i:s",strtotime($row["d_schedStartDate"]))." to ".date("H:i:s",strtotime($row["d_schedEndDate"]));
$class_leave="fc-noleave";
}

 $data[] = array(
  'id'   			=> $row["i_shiftID"],
  'title'   		=> $content,
  'start'   		=> $row["d_schedStartDate"],
  'end'   			=> $row["d_schedEndDate"],
  'className'   	=> $class_leave
 );
}

echo json_encode($data);

}



?>