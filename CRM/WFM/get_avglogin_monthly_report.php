<?php
include("../web_mysqlconnect.php");
include("../web_function.php");
//print_r($centralspoc);
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];

// echo "aarti ojha";  
if(isset($_REQUEST['s_dat']) && isset($_REQUEST['e_dat']))
{
      $agent_id="";
      extract($_REQUEST);
      if(!empty($_REQUEST['s_dat'])){ $startdatetime=date('Y-m-d',strtotime($_REQUEST['s_dat'])); }else{  $startdatetime=date('Y-m-d'); }
      if(!empty($_REQUEST['e_dat'])){ $enddatetime=date('Y-m-d',strtotime($_REQUEST['e_dat'])); }else{  $enddatetime=date('Y-m-d'); }
      if(!empty($_REQUEST['agent_id'])){ $agent_id=$_REQUEST['agent_id']; }
      $agent_uid=$_SESSION['userid'];
      // $sched_id=$_REQUEST['sched_id'];
      $dcolor=0;
      $sqlsource="select distinct(v_AgentName) from $db.tbl_servicelevel where v_AgentName!='' and (date(d_CreadtedDate)>='$startdatetime' and date(d_CreadtedDate)<='$enddatetime')";    // query for getting agent between satisfied date
      // echo $sqlsource;
      if($agent_id!="")
      {
      	$agent_user_name=getUserName($agent_id);
        $sqlsource.=" and v_AgentName='$agent_user_name'";
      }
      // if($_SESSION['user_group']=='070000')
      // {
      //   $sqlsource.=" and i_agentID='$agent_uid'";
      // }
      // echo $sqlsource;
      $sourceresult=mysqli_query($link,$sqlsource);
      while($row=mysqli_fetch_array($sourceresult)) 
      {
        // $agent_user_name=getUserName($agent_id);
        $agent_user_name=$row["v_AgentName"];
        $agent_id=getAUserID($row["v_AgentName"]);
        $agent_name=getUserName($agent_id);
        $rval="";
        if($dcolor%2==0)
        {
          $rval="row1";
        }
        
        ?>
        <div class="row <?=$rval?>">
          <div class="col-5"></div>
          <div class="col-15"><?php echo $agent_name; ?></div>
          <!-- <div class="col-25">
          
          </div> -->
          <?php
              // getAdherencePercentage($agent_id,$sched_id,$shift_id,$startdatetime,$enddatetime,$db);
              getLoginPercentage($agent_user_name,$startdatetime,$enddatetime,$db);
          ?>
           
         
        </div>


      <?$dcolor++;
  }

}      // Agent while end

// function getAdherencePercentage($agent_id,$sched_id,$shift_id,$startdatetime,$enddatetime,$db)
function getLoginPercentage($agent_user_name,$startdatetime,$enddatetime,$db)
{
  global $db, $link;
  // according to formula 
  // percentage=(sum of login time/ total number of days logged in)*100;
  $agent_id=getAUserID($agent_user_name);
  $sched_id=1;
  $sql_get_rows="select avg(i_loginTime) as avglogin,avg(i_talkTime) as avgtalktime,avg(i_breakTime) as avgbreak,sum(i_loginTime) as sumlogin,sum(i_talkTime) as sumtalk from $db.tbl_servicelevel where v_AgentName='$agent_user_name' and (date(d_CreadtedDate)>='$startdatetime' and date(d_CreadtedDate)<='$enddatetime')";
   ;
  $source_result_login=mysqli_query($link,$sql_get_rows);

  $sql_row_login=mysqli_fetch_array($source_result_login);

  $total_login=$sql_row_login['avglogin']-$sql_row_login['avgbreak'];
  $sumtalk=$sql_row_login['sumtalk'];
  $sumlogin=$sql_row_login['sumlogin'];

// ***********************getting adherence percentage for right time login************************
   $sql_get_rows="select i_adhere from $db.tbl_wfm_agent_sched_instance where (i_adhere in (1,2,3)) and i_agentID='$agent_id' and i_procSchedID='$sched_id' and (DATE( d_actualStartTime ) >='$startdatetime' and DATE( d_actualEndTime ) <='$enddatetime')";
  $source_result_adhere=mysqli_query($link,$sql_get_rows);
  $num_row_adhere=mysqli_num_rows($source_result_adhere);

  $sql_get_rows_green="select i_adhere from $db.tbl_wfm_agent_sched_instance where i_adhere=1 and i_agentID='$agent_id' and i_procSchedID='$sched_id' and (DATE( d_actualStartTime ) >='$startdatetime' and DATE( d_actualEndTime ) <='$enddatetime')";
  $source_result_adhere_green=mysqli_query($link,$sql_get_rows_green);
  $num_row_adhere_green=mysqli_num_rows($source_result_adhere_green);

  //according to formula occupancy=(total talk time/total login time)*100
  $occupancy=($sumtalk/$sumlogin)*100;

  // $percentage_green=($num_row_adhere_green/$num_row_adhere)*100;
  $percentage_green=getAdherencePercentage($agent_id,$sched_id,$startdatetime,$enddatetime,$db);
  // *********************end the adherence percentage********************

  echo '<div class="col-15">'.sec_to_his($total_login).'</div>
      <div class="col-15">'.sec_to_his($sql_row_login['avgtalktime']).'</div>
      <div class="col-15"> '.round($occupancy,2).'% </div>
      <div class="col-15"> '.round($percentage_green,2).'% </div>';

      //  echo '<div class="col-25">'.gmdate("H:i:s",$sql_row_login['avglogin']).'</div>
      // <div class="col-25">'.gmdate("H:i:s",$sql_row_login['avgtalktime']).'</div>';
 

}


function getAdherencePercentage($agent_id,$sched_id,$startdatetime,$enddatetime,$db)
{
  // according to formula 
  //PERCENTAGE=(LOGIN_TIME_SECONDS/ASSIGNED_TIME_IN_SECONDS)*100
  global $db, $link;
  $percentage_red=0;
  $sql_get_rows="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$sched_id' and (DATE(d_actualStartTime ) >='$startdatetime' and DATE( d_actualEndTime ) <='$enddatetime') and i_login_flag=1 and i_logout_flag=1";
  $source_result_adhere=mysqli_query($link,$sql_get_rows);
  $num_row_adhere=mysqli_num_rows($source_result_adhere);
  $diff_ass=0;$late_by_start=0;$late_by_end=0;$diff_att_sec=0;$actual_Nume=0;$diff_att=0;

  while($row=mysqli_fetch_array($source_result_adhere)) 
      {

          $agent_user_name=getUserName($agent_id);

          $st_assgn="";$end_assgn="";$st_att=0;$end_att=0;

          $sch_st_assigned=date("H:i:s",strtotime($row['d_schedStartDate']));
          $sch_end_assigned=date("H:i:s",strtotime($row['d_schedEndDate']));

          $sch_st_attended=date("H:i:s",strtotime($row['d_actualStartTime']));
          $sch_end_attended=date("H:i:s",strtotime($row['d_actualEndTime']));
           // echo "<br>schedule start". $sch_st_assigned."Actual start". $sch_st_attended.'schedule end'. $sch_end_assigned.'Actualend '. $sch_end_attended.'<br>';
           //if start attended time after schedule start assigned time
          if($sch_st_attended<$sch_st_assigned)
          {

            $sch_st_attended=$sch_st_assigned;
          }
          //if end attended time before schedule end assigned time
          if($sch_end_assigned<$sch_end_attended)
          {
            $sch_end_attended=$sch_end_assigned;
          }
      
          $late_by_start= strtotime($sch_st_attended)-strtotime($sch_st_assigned);
          $late_by_end= strtotime($sch_end_assigned)-strtotime($sch_end_attended);
           
          
          $diff_att+= ($late_by_start+  $late_by_end);//in seconds
          // echo '<br>'. "late start by". $late_by_start."Late end by". $late_by_end.' Total Adherence:'.$diff_att.'<br>';
         
          //$diff_att_sec=$diff_att*3600;
          $diff_ass+=strtotime($sch_end_assigned)-strtotime($sch_st_assigned);
      
          //$diff_att+=strtotime($sch_st_attended)-strtotime($sch_end_attended);
           
      
          /*  $diff_att+=strtotime($sch_end_attended)-strtotime($sch_st_attended);
           $diff_ass+=strtotime($sch_end_assigned)-strtotime($sch_st_assigned);*/
         // echo ''. $string='diff_atcual***'.$diff_att_sec.'diff_assigned***'.$diff_ass."Actual NUm".$actual_Nume.'<br>';

      }
    //$diff_att_sec=$diff_att*3600;
  // echo '<br>'. "Total start". $late_by_start."Total end". $late_by_end.' Total Adherence:'.$diff_att.'<br>';
     
    
      $actual_Numenator=$diff_ass-$diff_att; 
      // echo ''. $string='Numenator***'.$actual_Numenator.'Denomenator***'.$diff_ass.'<br>';
      $percentage_red=round(($actual_Numenator/$diff_ass)*100,2);
      return $percentage_red;
      // echo '<div class="col-70">
      //         <table width="100%">
      //           <tr>
      //             <td class="tdpercentage_red">'.$percentage_red.'%</td>            
      //           </tr>
      //         </table>
      //       </div>';
      // echo '<br>';

}

function sec_to_his($sec)
{
// $init = 685;
$hours = floor($sec / 3600);
$minutes = floor(($sec / 60) % 60);
$seconds = $sec % 60;
$hr=sprintf("%02d", $hours);
$min=sprintf("%02d", $minutes);
$secs=sprintf("%02d", $seconds);
return "$hr:$min:$secs";
}

    ?>

