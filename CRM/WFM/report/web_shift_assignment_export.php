<script type="text/javascript">
	function Print(){ 
	  window.print(); 
	  setTimeout('window.close()', 10); 
	} 
</script>
<?php
include("../../../config/web_mysqlconnect.php");
include("../wfm_function.php");
$name= $_SESSION['logged'];
$wfm_funct = new Wfm_connection;
if($_REQUEST['export']){
header("Content-type: application/octet-stream");  
header("Content-Disposition: attachment; filename=Agent_ShiftAssignment.xls");  
header("Pragma: no-cache");  
header("Expires: 0");  
}else{
	$print='onLoad="Print()"';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Report</title> 
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

</head>
<body <?=$print?>>
<form name="myform" method="post">
	<div class="Reports-page-right" style="width:100% !important">
		<div class="table">
			<div class="background-white"></div>
		</div>
		<?php
		$qq = "select * from $db.tbl_wfm_proc_schedule_list  where 1=1  ";
		// echo $qq;
		$ticket_query = mysqli_query($link,"$qq   ");
		$total=mysqli_num_rows(mysqli_query($qq));
		?>
		<!-- Start Display the filter data -->
		
		<div id="display-error" style="margin-top:5px;">Total Records Found - <?=$total?> </div>
		<div class="table">
			<div class="wrapper1">
				<div class="div1" style="width:1800px;"> </div>
			</div>
			<div class="wrapper2">
				<div class="div2" style="width:1800px;">
			        <table class="tableview tableview-2">
			           	<tbody>
				            <tr class="background">
								
								<td width="4%" align="center">Schedule Name</td>
								<td width="4%" align="center">Shift Name</td>
								<td width="4%" align="center">Total No Agent Required</td>
								<td width="4%" align="center">Total number of Agents Assigned</td>
								<td width="4%" align="center">Deficiency</td>
								<td width="4%" align="center">Number of Agents With Preferred Shift</td>
								<td width="4%" align="center">Number of Agents non-preferred  Shift</td>
								<td width="4%" align="center">Number Agents with Matching Skills</td>
								<td width="2%" align="center">Number Agents with over Skilled</td>
								<td width="2%" align="center">Number Agents with non-Matching Skills</td>
								</tr>
							
							<?
							$no=0;$total_Assigned_agent=0;$total_agent_need='';$Deficiency='';$Exact_PreferredShift_Agent='';$Non_PreferredShift_Agent='';
							$Matching_skill_count="";$Overmatching_skill_count="";$Nonmatching_skill_count="";

							while($ticket_res = mysqli_fetch_array($ticket_query)) { 

								$no++;
								$i_procSchedID=$ticket_res['i_procSchedID'];
								$i_shiftID=$ticket_res['i_shiftID'];
								$i_skillID=$ticket_res['i_skillID'];
								$total_agent_need=$ticket_res['i_noOfAgents'];
								$total_Assigned_agent=$wfm_funct->get_Agent_Assigned_For_Schedule($i_procSchedID,$i_shiftID);
								
								$Deficiency=($total_agent_need-$total_Assigned_agent);
								$Exact_PreferredShift_Agent=$wfm_funct->get_AgentWith_PreferredShift($i_procSchedID,$shiftid,1);//value 1 for exact match

								$Non_PreferredShift_Agent=$wfm_funct->get_AgentWith_PreferredShift($i_procSchedID,$shiftid,3);//value 3 for nonpreffered shift
 // print_r($Non_PreferredShift_Agent); die;
								$Matching_skill_count=$wfm_funct->get_Agent_Skills($i_procSchedID,$i_skillID,1,$shiftid);//value 1 for match
								$Overmatching_skill_count=$wfm_funct->get_Agent_Skills($i_procSchedID,$i_skillID,2,$shiftid);//value 2 for over skill match
								$Nonmatching_skill_count=$wfm_funct->get_Agent_Skills($i_procSchedID,$i_skillID,3,$shiftid);//value 3 for not match
								
							
							?>
				    	    <tr style="background:; color:;">			  
								<td align="center"><?=$wfm_funct->getProscheduleName($ticket_res['i_procSchedID'])?></td>
								<td align="center"><?=$wfm_funct->getShiftName($ticket_res['i_shiftID'])?></td>
								<td align="center"><?=$ticket_res['i_noOfAgents']?></td>
								<td align="center"><?=$total_Assigned_agent?></td>
								<td align="center"><?=$Deficiency?></td>
								<td align="center"><?=$Exact_PreferredShift_Agent?></td>
								<td align="center"><?=$Non_PreferredShift_Agent?></td>
								<td align="center"><?=$Matching_skill_count?></td>
								<td align="center"><?=$Overmatching_skill_count?></td>
								<td align="center"><?=$Nonmatching_skill_count?></td>
						   
							 </tr>
						  <?php }//end of while?>
						
						</tbody>
			        </table>
				</div>
			</div>            			
		</div>		
					
	  </div>
     <!-- End Right panel --> 
 </form>
</body>