<?php
include("../../../config/web_mysqlconnect.php");
include("../wfm_function.php");
$wfm_funct = new Wfm_connection;
$name= $_SESSION['logged'];
if($_REQUEST['export']){
	header("Content-type: application/octet-stream");  
	header("Content-Disposition: attachment; filename=Agent_Adherence.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
}else{
	$print='onLoad="Print()"';
}
?>
</head>
<body <?=$print?>>
<form name="myform" method="post">
	  <div class="Reports-page-right" style="width:100% !important">
			<div class="table">
				<div class="background-white"></div>						
		  	</div>
			<?php
			$start = ($page-1)*$numofrecords;					
			if(!empty($_REQUEST['sttartdatetime'])){ 
				$sttartdatetime1=explode(" ",$_REQUEST['sttartdatetime']);
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
			$ticket_query = mysqli_query($link,"$qq order by d_schedStartDate desc ");
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
									<td width="4%" align="center">Agent name</td>
									<td width="4%" align="center">Shift</td>
									<td width="4%" align="center">Schedule</td>
									<td width="4%" align="center">Break</td>
									<td width="4%" align="center">Scheduled Start</td>
									<td width="4%" align="center">Scheduled End</td>
									<td width="4%" align="center">Actual Start</td>
									<td width="4%" align="center">Actual End</td>
									<td width="2%" align="center">Leave (Y/N)</td>
									<td width="2%" align="center">Substitute for</td>
									<td width="2%" align="center">Current Login</td>
									<td width="2%" align="center">Current Logout</td>
									<td width="2%" align="center">Total log on time</td>
									<td width="2%" align="center">Total Average Handling Time</td>
								</tr>								
								<?php
								$no=0;
								while($ticket_res = mysqli_fetch_array($ticket_query)){
								$no++;																
								$agent_name=$wfm_funct->getUserName($ticket_res['i_agentID']);
								if($agent_name!=""){
									$cond_agent=" AND v_AgentName='".$agent_name."' ";
									$cond_agent1=" AND UserName='".$agent_name."' ";
								}								
								?>
					    	    <tr style="background:; color:;">			  
									<td align="center"><?=$wfm_funct->displayagentname($ticket_res['i_agentID'])?></td>
									<td align="center"><?=$wfm_funct->getShiftName($ticket_res['i_shiftID'])?></td>
									<td align="center"><?=$wfm_funct->getProscheduleName($ticket_res['i_procSchedID'])?></td>
									<td align="center">&nbsp;</td>
									<td align="center"><?=$ticket_res['d_schedStartDate']?></td>
									<td align="center"><?=$ticket_res['d_schedEndDate']?></td>
									<td align="center"><?=$ticket_res['d_actualStartTime']?></td>
									<td align="center"><?=$ticket_res['d_actualEndTime']?></td>
									<td align="center"><?php
									if($ticket_res['i_status']==1) 
									{
										echo "Yes";
									}else{
										echo "No";
									}
									?></td>
									<td align="center"><?=$wfm_funct->displayagentname($ticket_res['substitute_id'])?></td>
									<td align="center"><?php
									 $qq="select AccessedAt,TimePeriod from $db.logip where UserName!='' $datefilter_3 $cond_agent1 order by AccessedAt desc";
									$agent_q=mysqli_query($link,$qq);
									$fetch_a=mysqli_fetch_array($agent_q);
									$intime=$fetch_a['AccessedAt'];
									$outtime=$fetch_a['TimePeriod'];
									//echo "Login".$intime."Logout".$outtime;
									if(!empty($fetch_a['AccessedAt']) )
									{
										echo $intime;
									}
									?></td>
									<td align="center"><?php echo $outtime;?></td>
									<td align="center">
									<?php
									$logintime=0;
									if($outtime=="" && $intime!='')
									{
										
										$current_date=date("Y-m-d H:i:s");
										$currentDate = strtotime($current_date);

										$lastlogintime=strtotime($intime);

										$logintime=($currentDate-$lastlogintime);
										//echo 'curtime sec'.$currentDate.'last in'.$lastlogintime.'(cur-login)'.$logintime;
									}else{
										$logintime=0;
									}
									
									$qq_serviceLevel = "select i_loginTime,i_breakTime from $db. tbl_servicelevel  where 1=1 $datefilter_2 $cond_agent ";
									$query_slevel=mysqli_query($link,$qq_serviceLevel);
									$fetch_sl=mysqli_fetch_array($query_slevel);
									$num_rec_servicelevel=mysqli_num_rows($query_slevel);
									//&& $fetch_sl['i_breakTime']!=''
									if($fetch_sl['i_loginTime']!='' && $num_rec_servicelevel!=0 )
									{
										//echo '<br>selogin'.$fetch_sl['i_loginTime'].'break'.$fetch_sl['i_breakTime'];
										$total_log_time=($fetch_sl['i_loginTime']-$fetch_sl['i_breakTime']);
										$total_log_time=$total_log_time+$logintime;
										//echo '<br>service'.$total_log_time;
										$tothours = floor($total_log_time / 3600);//in hours : Vipul Dwivedi
										$totminutes = floor(($total_log_time / 60) % 60);//in minutes  : Vipul Dwivedi
										$totseconds = $total_log_time % 60;//in seconds : Vipul Dwivedi
										echo '<br>'. $totalhour=$tothours.":".$totminutes.":".$totseconds;

									}else if($logintime!=0){
										//echo '<br>'. $logintime;
										$tothours = floor($logintime / 3600);//in hours : Vipul Dwivedi
										$totminutes = floor(($logintime / 60) % 60);//in minutes  : Vipul Dwivedi
										$totseconds = $logintime % 60;//in seconds : Vipul Dwivedi
										echo '<br>'. $totalhour=$tothours.":".$totminutes.":".$totseconds;
									}
									?></td>
									<td align="center"><?php
									if($ticket_res['i_agentID']!='' && $startdatetime!='')
									{
										$sql_aht="SELECT (`F1`+`F2`+`F3`+`F4`+`F5`+`F6`+`F7`+`F8`+`F9`+`F10`+`F11`+`F12`+`F13`+`F14`+`F15`+`F16`+`F17`+`F18`+`F19`+`F20`+`F21`+`F22`+`F23`+`F24`) as hourcall,`i_talkTime` FROM $db.`tbl_servicelevel` where 1=1 $datefilter_2 $cond_agent ";
										$q_aht=mysqli_query($link,$sql_aht)or die(mysqli_erro());
										$fetch_aht=mysqli_fetch_array($q_aht);
										//echo '<br>'. $fetch_aht['i_talkTime'].''.$fetch_aht['hourcall'];
										$avg=$fetch_aht['i_talkTime'] /$fetch_aht['hourcall'];
										echo gmdate("H:i:s",$avg);
									}
									
									?></td>
					                   
								 </tr>
								 <?php
										$v_breakList=$ticket_res['v_breakList'];
										if($v_breakList)
										{
											$breaklist_array=explode(',',$v_breakList);
											if(count($breaklist_array)>=1)
											{
												foreach ($breaklist_array as $key => $item) {
													
													$sql_break=mysqli_query($link,"SELECT v_breakName,d_startbreak,d_endbreak FROM $db.tbl_wfm_mst_break WHERE i_breakID='$item'");
													$fetch_break=mysqli_fetch_array($sql_break);
													
													
												 	 $q_actual_breaktime="SELECT d_breakStTime,d_breakEndTime FROM $db.tbl_wfm_break_instance  
													WHERE i_breakID='$item' AND i_agentID='".$ticket_res['i_agentID']."' 
													AND i_procSchedID='".$ticket_res['i_procSchedID']."'  AND i_shiftID='".$ticket_res['i_shiftID']."'
													 $datefilter_1
													 ";
													$query_actualbreaktime=mysqli_query($link,$q_actual_breaktime);
													$fetch_actualbreaktime=mysqli_fetch_array($query_actualbreaktime);
													$d_breakStTime=explode(' ',$fetch_actualbreaktime['d_breakStTime']);
													$d_breakEndTime=explode(' ',$fetch_actualbreaktime['d_breakEndTime']);
													?>
													<tr style="background:; color:;">
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center"><?=$fetch_break['v_breakName']?></td>
													<td align="center"><?=$fetch_break['d_startbreak']?></td>
													<td align="center"><?=$fetch_break['d_endbreak']?></td>
													<td align="center"><?php echo $d_breakStTime[1];?></td>
													<td align="center"><?php echo $d_breakEndTime[1];?></td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>									
													</tr>
													
													<?
												}
											}
										
										}
										
										?>
								
								<?php
								}
								?>
							
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
<script type="text/javascript">
	function Print(){ 
	  window.print(); 
	  setTimeout('window.close()', 10); 
	} 
</script>