<?php
include("../../../config/web_mysqlconnect.php");
include("../wfm_function.php");
$wfm_funct = new Wfm_connection;
$name= $_SESSION['logged'];
if($_REQUEST['export']){
	header("Content-type: application/octet-stream");  
	header("Content-Disposition: attachment; filename=Agent_assignmentwisereport.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
}else{
	$print='onLoad="Print()"';
}

?>
<body <?=$print?>>
<form name="myform" method="post">
	  <div class="Reports-page-right" style="width:100% !important">
		<div class="table">
			<div class="background-white"></div>
		</div>
			<?php
	         	if($_REQUEST['AtxUserID']!=""){
					$whr_cond_agent=" AND i_AgentID='".$_REQUEST['AtxUserID']."' ";
				
				}
				$qq = "select * from $db.tbl_wfm_agent_sched_assignment_hist  where 1=1 $whr_cond_agent ";
				$ticket_query = mysqli_query($link,"$qq");
				$total=mysqli_num_rows(mysqli_query($link,$qq));
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
								<td width="4%" align="center">Agent Name</td>
								<td width="4%" align="center">Schedule Name</td>
								<td width="4%" align="center">Skill Sets </td>
								<td width="4%" align="center">Preferred Shifts</td>
								<td width="4%" align="center">Assigned Shifts and Skills</td>
								<td width="4%" align="center">Under utilised</td>
								<td width="4%" align="center">From Date</td>
								<td width="4%" align="center">To Date</td>
								<td width="4%" align="center">Delete Date</td>
								</tr>
							<?php
							$no=0;
							while($ticket_res = mysqli_fetch_array($ticket_query)) { 
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
							?>
				    	    <tr style="background:; color:;">			  
								<td align="center"><?=$wfm_funct->displayagentname($ticket_res['i_AgentID'])?></td>
								<td align="center"><?=$wfm_funct->getProscheduleName_hist($procSched)?></td>
								<td align="center"><? //echo $i_Skillset;?><?php echo $db_skill_val;?></td>
								<td align="center"><?=$wfm_funct->getShiftName_hist($i_shiftpref)?></td>
								<td align="center"><?=$wfm_funct->getShiftName_hist($assign_shiftID)?></td>
								<td align="center"><?=$wfm_funct->skill_flag($skill_flag)?></td>
								<td align="center"><?=$ticket_res['d_startTime']?></td>
								<td align="center"><?=$ticket_res['d_endTime']?></td>
								<td align="center"><?=$ticket_res['del_Date']?></td>						   
							 </tr>
						  	<?php }?>
						</tbody>
	            	</table>
				</div>
			</div>
		</div>		
	</div>
     <!-- End Right panel --> 
</div>
</form>
</body>
</html>

<script type="text/javascript">
	function Print(){ 
	  window.print(); 
	  setTimeout('window.close()', 10); 
	} 
</script>