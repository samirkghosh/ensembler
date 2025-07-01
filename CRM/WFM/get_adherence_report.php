<?php
include("../../config/web_mysqlconnect.php");
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];

if(isset($_REQUEST['s_dat']) && isset($_REQUEST['e_dat'])){

      $agent_id="";
      extract($_REQUEST);
      if(!empty($_REQUEST['s_dat'])){ 
        $startdatetime=date('Y-m-d',strtotime($_REQUEST['s_dat'])); 
      }else{  
        $startdatetime=date('Y-m-d'); 
      }
      if(!empty($_REQUEST['e_dat'])){ 
        $enddatetime=date('Y-m-d',strtotime($_REQUEST['e_dat'])); 
      }else{  
        $enddatetime=date('Y-m-d'); 
      }

      if(!empty($_REQUEST['agent_id'])){ 
        $agent_id=$_REQUEST['agent_id']; 
      }
      $agent_uid=$_SESSION['userid'];
      $sched_id=$_REQUEST['sched_id'];
      $dcolor=0;
      $sqlsource="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_procSchedID='$sched_id' and (date(d_schedStartDate)>='$startdatetime' and date(d_schedEndDate)<='$enddatetime')";    // query for getting agent \;
      if($agent_id!=""){
        $sqlsource.=" and i_agentID='$agent_id'";
      }
      $sourceresult=mysqli_query($link,$sqlsource);
      $num_adh_row=mysqli_num_rows($sourceresult);

      if($num_adh_row==0){
        echo '<div class="row"><div style="color:red;"><b><center>Schedule Not Found Or No Agent Assigned.</center></b></div></div>';
      }
      $noofagent = 0;
      while($row=mysqli_fetch_array($sourceresult)){
        $agent_id=$row["i_agentID"];
        // $shift_id=$row["i_shiftID"];
        $sqlsource_agent="select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserID='$agent_id' and AtxUserStatus=1";                
        // query for getting agent name

        $sourceresult_agent=mysqli_query($link,$sqlsource_agent);
        $row_agent=mysqli_fetch_array($sourceresult_agent);
        $num_adh_row1=mysqli_num_rows($sourceresult_agent);
        if($num_adh_row1==0){ 

          // echo '<div class="row"><div style="color:red;"><b><center>Schedule Not Found Or No Agent Assigned.</center></b></div></div>';
        }else{
          $agent_name=$row_agent["AtxDisplayName"];
          // print_r($sqlsource_agent);
          if($agent_name!=""){
            $noofagent++;
            $rval="";
            if($dcolor%2==0)
            {
              $rval="row1";
            }
            
            ?>
              <tr><td><font color='red'> <?=$agent_name?></font></td>
              <?php
                 $finals =  getAdherencePercentage($agent_id,$sched_id,$startdatetime,$enddatetime,$db);
                 $total_values += $finals;
              ?>
               
             </tr>
          <? $dcolor++;
        }
      }
  }
  ?>
  <tr>
    <td colspan="2"><font><strong>Total</strong></font></td>
<?php 
  $shrinkage_count = round(($total_values / $noofagent),2);
  ?>
   <td>
    <font><strong><?php echo $shrinkage_count;?>%</strong></font><br/>
  </td><td colspan="3"></td></tr>
  <?php
}      // Agent while end

// function getAdherencePercentage($agent_id,$sched_id,$shift_id,$startdatetime,$enddatetime,$db)
function getAdherencePercentage($agent_id,$sched_id,$startdatetime,$enddatetime,$db)
{
  global $db, $link;

      // according to formula 
      //PERCENTAGE=(LOGIN_TIME_SECONDS/ASSIGNED_TIME_IN_SECONDS)*100
    $percentage_red=0;
    // $extranal_schrikage = '';
    $sql_get_rows="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$sched_id' and (DATE(d_actualStartTime ) >='$startdatetime' and DATE( d_actualEndTime ) <='$enddatetime')";
    // and i_login_flag=1 and i_logout_flag=1
    $finals = '';
    $working_hours_agent = '';
    $source_result_adhere=mysqli_query($link,$sql_get_rows);
    $num_row_adhere=mysqli_num_rows($source_result_adhere);
    $diff_ass=0;$late_by_start=0;$late_by_end=0;$diff_att_sec=0;$actual_Nume=0;$diff_att=0;
    while($row=mysqli_fetch_array($source_result_adhere)){
      $working_hours_agent  = calculateTotalTime($row['d_schedStartDate'],$row['d_schedEndDate']);
      if($row['i_login_flag'] == '1' && $row['i_logout_flag'] == '1'){

        /*--------------getting break start and end diff with schdule break time------------*/
        $shift_id = $row['i_shiftID'];
        $sqlsource_break="select d_breakStTime,d_breakEndTime,i_breakID from $db.tbl_wfm_break_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and d_breakStTime>='$startdatetime 00:00:00:' and d_breakEndTime<='$enddatetime 23:59:59'";
        $sourceresult_break=mysqli_query($link,$sqlsource_break);
        while($row_break=mysqli_fetch_array($sourceresult_break)){
          //getting agent used break count in min
          $break_min_count += time_difference_minutes($row_break['d_breakStTime'],$row_break['d_breakEndTime']);
        
        }
        /*------------------------------End break diff code-------------------------------------------------*/

          $st_assgn="";$end_assgn="";$st_att=0;$end_att=0;
          $sch_st_assigned=date("H:i:s",strtotime($row['d_schedStartDate']));
          $sch_end_assigned=date("H:i:s",strtotime($row['d_schedEndDate']));

          $sch_st_attended=date("H:i:s",strtotime($row['d_actualStartTime']));
          $sch_end_attended=date("H:i:s",strtotime($row['d_actualEndTime']));

          $login_start_end_time = calculateTotalTime($row['d_actualStartTime'],$row['d_actualEndTime']);
           //if start attended time after schedule start assigned time
		      if($sch_st_attended<$sch_st_assigned){
            $sch_st_attended=$sch_st_assigned;
          }
          //if end attended time before schedule end assigned time
          if($sch_end_assigned<$sch_end_attended){
            $sch_end_attended=$sch_end_assigned;
          }
    		  $late_by_start= strtotime($sch_st_attended)-strtotime($sch_st_assigned);
    		  $late_by_end= strtotime($sch_end_assigned)-strtotime($sch_end_attended);
    		
    			$diff_att+= ($late_by_start+  $late_by_end);//in seconds
    			//$diff_att_sec=$diff_att*3600;

    			$diff_ass+=strtotime($sch_end_assigned)-strtotime($sch_st_assigned);

          /*getting total working hr*/;
          $shift_break=explode(",",$row['v_breakList']);
          for($s_break=0;$s_break<count($shift_break);$s_break++)// for loop for comma (,) seperated values
          {
            $assigned_break_ids=$shift_break[$s_break];
            if(!empty($assigned_break_ids)){
              $sqlsource_break="select v_breakName,d_startbreak,d_endbreak from $db.tbl_wfm_mst_break where i_breakID = '$assigned_break_ids'";
              $sourceresult_break=mysqli_query($link,$sqlsource_break);
              while($row_break=mysqli_fetch_array($sourceresult_break)){
                  /*getting agent assigned break*/             
                  $break_final_min += time_difference_minutes($row_break['d_startbreak'],$row_break['d_endbreak']);
              }
            }
          }
         
          $working_hr_with_break  += calculateTotalTime($row['d_schedStartDate'],$row['d_schedEndDate']);
          $final_working_without_break = abs($break_final_min - $working_hr_with_break);
          
          // echo "assigned break min:  "; echo $break_final_min;
          // echo "<br/>";
          // echo "working hr without break:  "; echo $final_working_without_break;
          // echo "<br/>";

          // $user_used_time = $login_start_end_time - $break_min_count; 
          $internal_schrikage = $working_hr_with_break - $login_start_end_time;
          $internal_schrikage_count = $internal_schrikage + $break_min_count;
          // echo "<br/>"; echo "internal schrikage: "; echo $internal_schrikage_count; echo"<br/>";

          $agent_shrinkage = round(($internal_schrikage_count/$working_hr_with_break) * 100);
          $agent_shrinkage_total = round($agent_shrinkage,2);

          // echo "working hr with break :  "; echo $working_hr_with_break; echo"<br/>";
          // echo"<br/>";
          // echo "agent worked  "; echo $login_start_end_time;
          // echo "<br/>";
          // echo "agent used break "; echo $break_min_count;
          // echo "<br/>";

          // echo "<br/>"; echo "Agent final schrikage  "; print_r($agent_shrinkage_total); echo"<br/>";
          // echo "<br/>"; echo "Formula for internal_schrikage_diff : login_start_end_time - working_hr_with_break  "; echo "<br/>";
          // echo "<br/>"; echo "Formula for internal_schrikage : 227  = (85 + 142) "; echo "<br/>";
          // echo "<br/>"; echo "Formula for agent_worked : (internal_schrikage_diff + agent_used_break) "; echo "<br/>";

          //  echo "<br/>"; echo "Formula for agent wise schrikage : 40% = round(227 / 570 * 100)"; echo"<br/>";
          // echo "<br/>"; echo "Formula for agent wise schrikage : (internal_schrikage / working_hr_with_break * 100)"; echo"<br/>";

          /*display working hours*/
          $hours_internal = floor($internal_schrikage_count / 60);
          $remaining_minutes_inter = $internal_schrikage_count % 60;
          $internal_hourse = $hours_internal . ':' . $remaining_minutes_inter;


      }else{
          if($row['i_status'] != '' && $row['i_status'] != '0'){
            $total_leave += $row['i_status'];
          }
          
          if($row['i_login_flag'] == '0' && $row['i_logout_flag'] =='0'){
            $total_login += $row['i_login_flag'];
          }
          // $planned_leave = $total_leave;
          // $unplanned_leave = $total_login;
          if(empty($agent_shrinkage_total)){
            $agent_shrinkage_total = 100;
            $extranal_schrikage = 100 .'%';
          }
      }
    }

      /*-----------------Adherence count---------------*/
	    $actual_Numenator=$diff_ass-$diff_att; 
      $percentage_red=round(($actual_Numenator/$diff_ass)*100,2);


      /*---------display working in hours------------*/
      $hours = floor($working_hours_agent / 60);
      $remaining_minutes = $working_hours_agent % 60;
      $working_hours = $hours . ':' . $remaining_minutes;

      if(empty($internal_hourse)){
        $internal_hourse = '-';
      }
      if(empty($extranal_schrikage)){
        $extranal_schrikage = '-';
      }
      /*--------------shrinkage count------------------*/
      $finals = $agent_shrinkage_total;
      echo '<td class="tdpercentage_red">'.$percentage_red.'%</td>     
            <td class="tdpercentage_red">'.$agent_shrinkage_total.'%</td> 
            <td class="tdpercentage_red">'.$internal_hourse.'</td> 
            <td class="tdpercentage_red">'.$extranal_schrikage.'</td> 
            <td class="tdpercentage_red">'.$working_hours.'</td>';
      // echo '<br>';
      return $finals;

}
function calculateTotalTime($fromtime_temp, $totime_temp){
  date_default_timezone_set('IST');

  // Define the start and end date and time values
  // $start_datetime = '2023-04-04 07:00:00';
  // $end_datetime = '2023-04-04 16:30:00';

  $start_datetime = $fromtime_temp;
  $end_datetime = $totime_temp;

  // Convert the date and time values to Unix timestamps
  $start_timestamp = strtotime($start_datetime);
  $end_timestamp = strtotime($end_datetime);

  // Calculate the difference in minutes between the two timestamps
  $minutes = round(($end_timestamp - $start_timestamp) / 60);
  return $minutes;
  // Output the result
  // echo "The number of minutes between $start_datetime and $end_datetime is: $minutes";
}
function time_difference_minutes($time1,$time2){
  // Convert time values to seconds
  $time1_seconds = strtotime($time1);
  $time2_seconds = strtotime($time2);

  // Calculate the time difference in seconds
  $time_difference_seconds = abs($time2_seconds - $time1_seconds);

  // Convert the time difference to minutes
  $time_difference_minutes = round($time_difference_seconds / 60);

  // Display the time difference in minutes
  return $time_difference_minutes;
}
?>
  