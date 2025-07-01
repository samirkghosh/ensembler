<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include("../../config/web_mysqlconnect.php");
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
define("PIXCEL","4");
$selecttab=9;
?>

<link rel="stylesheet" href="<?=$SiteURL?>public/css/style.css">
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/WFM/css/wfm_common.css">
<body onload="get_break_list();">
  <div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
      <form method="POST">
        <div class="container1" style="border-radius: 10px;"><h1>Edit Schedule</h1>
          <div class="row_new">
            <span class="close" onclick="close_modal()">&times;</span>
            <div class="col-25">
              <label for="fname">Agent Name</label>
            </div>
            <div class="col-50">
              <input type="text" id="agent_name" name="agent_name" readonly>
              <input type="hidden" id="agent_id" name="agent_id" readonly required>
              <input type="hidden" id="shift_id" name="shift_id" readonly required>
              <input type="hidden" id="sched_id" name="sched_id" readonly required>
            </div>
          </div>  
          <div class="row_new">
            <div class="col-25">
              <label for="country">Break</label>
            </div>
            <div class="col-50">
              <select name="break_list[]" multiple style="height:150px;">
                <option>Select Break</option>  
              </select>
            </div>
            <div class="col-50">
              <label for="country">Shift : </label>
              <span style="color: red" id="shiftnames"></span>
            </div>
          </div>
          <div class="row_new">
            <div class="col-25">
              <label for="country"></label>
            </div>
            <div class="col-50">
                <input type="text" name="from_time" class="dob1" value="" id="from_time" autocomplete="off" placeholder="From" required>
            </div>
            <div class="col-50">
              <input type="text" name="to_time" class="dob1" value="" id="to_time" autocomplete="off" placeholder="To" required>
            </div>
          </div>
          <div class="col-5">  
          </div>
          <div class="row_new" id="div_more_breaks">
          </div>

          <div class="row_new">
            <div class="col-25">
              <label for="country"> <input id="chk_leave" name="chk_leave" type="checkbox" value=""> Mark on leave</label>
            </div>
            <div class="col-50">
              <input type="text" name="from_leave_time" class="dob1" value="" id="from_leave_time" autocomplete="off" placeholder="From" style="background-color: #ccc;" disabled>
            </div>
            <div class="col-50">
              <input type="text" name="to_leave_time" class="dob1" value="" id="to_leave_time" autocomplete="off" placeholder="To" style="background-color: #ccc;" disabled>
            </div>
          </div>
          <div class="row_new">
            <div class="col-25">
              <label for="country"> <input id="chk_reassign" name="chk_reassign" type="checkbox" value=""> Reassign To</label>
            </div>
            <div class="col-50">
                <input type="text" name="from_reassign_time" class="dob1" value="" id="from_reassign_time" autocomplete="off" placeholder="From" style="background-color: #ccc;" disabled>
            </div>
              <div class="col-50">
                <input type="text" name="to_reassign_time" class="dob1" value="" id="to_reassign_time" autocomplete="off" placeholder="To" style="background-color: #ccc;" disabled>
            </div>
          </div>
          <div class="row_new">
            <div class="col-25">
            </div>
            <div class="col-50">
                <select id="agent_list" name="agent_list" style="background-color: #ccc;" disabled>
                <option value="select">Select</option>
                </select>
            </div>
            <div class="col-50">
            </div>
          </div>     
          <div class="row_new">
            <div class="col-25">
              <label for="country"> <input id="chk_swap" name="chk_swap" type="checkbox" value=""> Swap User</label>
            </div>
            <div class="col-50">
              <input type="text" name="from_swap_time" class="dob1" value="" id="from_swap_time" autocomplete="off" placeholder="From" style="background-color: #ccc;" disabled>
            </div>
            <div class="col-50">
              <input type="text" name="to_swap_time" class="dob1" value="" id="to_swap_time" autocomplete="off" placeholder="To" style="background-color: #ccc;" disabled>
            </div>
          </div>
          <div class="row_new">
            <div class="col-25"> 
            </div>   
            <div class="col-50">
              <select id="agent_list_swap" name="agent_list_swap" style="background-color: #ccc;" disabled>
                <option value="select">Select</option>
              </select>
            </div>
            <div class="col-50">
            </div>
          </div>
          <div class="row_new">
            <div class="col-25">
              <label for="country"> <input id="chk_move" name="chk_move" type="checkbox" value=""> Move User</label>
            </div>
            <div class="col-50">
              <input type="text" name="from_move_time" class="dob1" value="" id="from_move_time" autocomplete="off" placeholder="From" style="background-color: #ccc;" disabled>
            </div>
            <div class="col-50">
              <input type="text" name="to_move_time" class="dob1" value="" id="to_move_time" autocomplete="off" placeholder="To" style="background-color: #ccc;" disabled>
            </div>
          </div>
          <div class="row_new">
            <div class="col-25"> 
            </div>   
            <div class="col-50">
              <select id="schd_list_move" name="schd_list_move" style="background-color: #ccc;" disabled>
                
              </select>
            </div>
            <div class="col-50">
            </div>
          </div>
          <div class="botton">
            <input type="submit" name="btn_Submit" id="btn_Submit" class="submit_wfm btn_Submit" value="Submit"/>
            <input name="btn_Cancel" id="btn_Cancel" value="Cancel" class="submit_wfm btn_Cancel" type="button" />
          </div>  
        </div>
      </form>
    </div>
  </div>
</body>
<?php
if(isset($_POST["agent_id"]) && isset($_POST["shift_id"])){
    $agent_id=$_POST["agent_id"];
    $shift_id=$_POST["shift_id"];
    $breaks_from=date("Y-m-d",strtotime($_POST["from_time"]));
    $breaks_to=date("Y-m-d",strtotime($_POST["to_time"]));
    $breaks_id=$_POST["break_list"];
    $from_leave_time=date("Y-m-d",strtotime($_POST["from_leave_time"]));
    $to_leave_time=date("Y-m-d",strtotime($_POST["to_leave_time"]));
    $assignee_id=$_POST["agent_list"];
    $from_reassign_time=date("Y-m-d",strtotime($_POST["from_reassign_time"]));
    $to_reassign_time=date("Y-m-d",strtotime($_POST["to_reassign_time"]));
    $agent_list_swap=$_POST["agent_list_swap"];
    $from_swap_time=date("Y-m-d",strtotime($_POST["from_swap_time"]));
    $to_swap_time=date("Y-m-d",strtotime($_POST["to_swap_time"]));
    $pro_id=$_POST["sched_id"];
    // move shift data
    $from_move_time=date("Y-m-d",strtotime($_POST["from_move_time"]));
    $to_move_time=date("Y-m-d",strtotime($_POST["to_move_time"]));
    $i_shiftID_move = $_POST['schd_list_move'];
  if(isset($_POST["chk_leave"]) && !isset($_POST["chk_reassign"]))
  {
      // only status change
      //  $sql_query="update $db.tbl_wfm_agent_sched_instance set i_status='1' where i_agentID='$agent_id' and i_procSchedID='$pro_id'  and d_actualStartTime>='$from_leave_time' and d_actualEndTime<='$to_leave_time'";
        // mysqli_query($link,$sql_query) or die(mysqli_error());//vipul

        $sql_query="update $db.tbl_wfm_agent_sched_instance set i_status='1' where i_agentID='$agent_id' and i_procSchedID='$pro_id'  and d_schedStartDate>='$from_leave_time 00:00:00' and d_schedEndDate<='$to_leave_time 23:59:59'";
        mysqli_query($link,$sql_query) or die(mysqli_error());//vipul

  }
  else if(!isset($_POST["chk_leave"]) && isset($_POST["chk_reassign"])){

      // delete prev agent value and insert new agent value and check the assignee is not in the same shift
      $break_list_val="";
      $sql_sel_query="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$pro_id' and d_schedStartDate>='$from_reassign_time 00:00:00' and d_schedEndDate<='$to_reassign_time 23:59:59'";
        $sel_res=mysqli_query($link,$sql_sel_query);
        while($row_sel=mysqli_fetch_array($sel_res))
      {
          $act_st_time=$row_sel["d_actualStartTime"];
          $act_end_time=$row_sel["d_actualEndTime"];

          for($bcount=0;$bcount<count($breaks_id);$bcount++)
          {
            $break_list_val.=$breaks_id[$bcount].",";
          }
          // $sql_ins_query="insert into $db.tbl_wfm_agent_sched_instance(i_agentID,i_procSchedID,i_shiftID,d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,v_breakList,i_status,v_remarks) values ('$assignee_id','$pro_id','$shift_id','$act_st_time','$act_end_time','$act_st_time','$act_end_time','$break_list_val','0','')";

          $sql_ins_query="update $db.tbl_wfm_agent_sched_instance set i_status='0',i_agentID='$assignee_id',substitute_id='$agent_id' where i_agentID='$agent_id' and i_shiftID='$shift_id' and i_procSchedID='$pro_id' and d_schedStartDate>='$from_reassign_time 00:00:00' and d_schedEndDate<='$to_reassign_time 23:59:59'";


            mysqli_query($link,$sql_ins_query) or die(mysqli_error());//vipul
      }

      // $sql_query="delete from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and i_procSchedID='$pro_id' and d_actualStartTime>='$from_reassign_time' and d_actualEndTime<='$to_reassign_time'";
        // mysqli_query($link,$sql_query) or die(mysqli_error());//vipul

  }else if(isset($_POST["chk_leave"]) && isset($_POST["chk_reassign"])){
      // change status for prev agent and insert new for new agent and check assignee is not in the same shift
    // only status change
      $sql_query="update $db.tbl_wfm_agent_sched_instance set i_status='1' where i_agentID='$agent_id' and i_shiftID='$shift_id' and i_procSchedID='$pro_id'  and d_schedStartDate>='$from_leave_time 00:00:00' and d_schedEndDate<='$to_leave_time 23:59:59'";
        mysqli_query($link,$sql_query) or die(mysqli_error());//vipul

      $break_list_val="";
      $sql_sel_query="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and d_schedStartDate>='$from_reassign_time 00:00:00' and d_schedEndDate<='$to_reassign_time 23:59:59'";
        $sel_res=mysqli_query($link,$sql_sel_query);
        while($row_sel=mysqli_fetch_array($sel_res))
      {
          $act_st_time=$row_sel["d_schedStartDate"];
          $act_end_time=$row_sel["d_schedEndDate"];

          for($bcount=0;$bcount<count($breaks_id);$bcount++)
          {
            echo $break_list_val.=$breaks_id[$bcount].",";
          }
          $sql_ins_query="insert into $db.tbl_wfm_agent_sched_instance(i_agentID,i_procSchedID,i_shiftID,d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,v_breakList,i_status,substitute_id) values ('$assignee_id','$pro_id','$shift_id','$from_reassign_time','$to_reassign_time','$act_st_time','$act_end_time','$break_list_val','0','$agent_id')";
            mysqli_query($link,$sql_ins_query) or die(mysqli_error());//vipul
      }

  }else if(isset($_POST["chk_swap"])){
      // swap users' id between dates - need two user [change morning to eve][change user and shfit both]
      $sql_sel_query="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_list_swap' and d_schedStartDate>='$from_swap_time 00:00:00' and d_schedEndDate<='$to_swap_time 23:59:59'";
        $sel_res=mysqli_query($link,$sql_sel_query);
        while($row_sel=mysqli_fetch_array($sel_res))
      {
          $assignee_shift_id=$row_sel["i_shiftID"];// and i_procSchedID='$pro_id'
          $sql_query="update $db.tbl_wfm_agent_sched_instance set i_agentID='$agent_id', i_status=0 where i_agentID='$agent_list_swap' and i_shiftID='$assignee_shift_id' and d_schedStartDate>='$from_swap_time' and d_schedEndDate<='$to_swap_time 23:59:59'";
            mysqli_query($link,$sql_query) or die(mysqli_error());//vipul

          $sql_query_assignee="update $db.tbl_wfm_agent_sched_instance set i_agentID='$agent_list_swap', i_status=0 where i_agentID='$agent_id' and i_shiftID='$shift_id' and d_schedStartDate>='$from_swap_time 00:00:00' and d_schedEndDate<='$to_swap_time 23:59:59'";
            mysqli_query($link,$sql_query_assignee) or die(mysqli_error());//vipul
      }
  }else if(isset($_POST["chk_move"])){
      // move shift with user' id between dates [need one user][only change shfit]
        $sqlagent_count="select * from $db.tbl_wfm_proc_schedule_list where i_shiftID='$i_shiftID_move'";
        $sourceagent_count=mysqli_query($link,$sqlagent_count);
        $row_count_agent=mysqli_fetch_array($sourceagent_count);
        $v_breakid=$row_count_agent['v_breakid'];

        $querys = "select * from $db.tbl_wfm_mst_shift where i_shiftID = $i_shiftID_move";
        $res_new=mysqli_query($link,$querys);
        $sql_res_shifts = mysqli_fetch_array($res_new);

        $sql_sel_query="select * from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and d_schedStartDate>='$from_move_time 00:00:00' and d_schedEndDate<='$to_move_time 23:59:59'";
        $sel_res=mysqli_query($link,$sql_sel_query);

        $d_actualStartTime= '';
        $d_actualEndTime = '';
        while($row_sel=mysqli_fetch_array($sel_res)){
            $instance_date_from = date('Y-m-d', strtotime($row_sel['d_schedStartDate']));
            $instance_date = $instance_date_from." ".$sql_res_shifts['t_fromTime'];

            $instance_date_to = date('Y-m-d', strtotime($row_sel['d_schedEndDate']));
            $instance_enddate_time=$instance_date_to." ".$sql_res_shifts['t_toTime'];

            $d_actualStartTime = $instance_date;
            $d_actualEndTime = $instance_enddate_time;

          $assignee_shift_id=$row_sel["i_shiftID"];// and i_procSchedID='$pro_id'
          $sql_query="update $db.tbl_wfm_agent_sched_instance set i_shiftID='$i_shiftID_move', v_breakList='$v_breakid',d_actualStartTime='$d_actualStartTime',d_actualEndTime='$d_actualEndTime',d_actualStartTime='$d_actualStartTime',d_schedEndDate='$d_actualEndTime', i_status=0 where  d_schedStartDate>='$from_move_time 00:00:00' and d_schedEndDate<='$to_move_time 23:59:59' and i_agentID='$agent_id'";
            mysqli_query($link,$sql_query) or die(mysqli_error());//vipul
        }

  }else{
      // change breaks definition only given between dates
    $break_list_val="";
    for($bcount=0;$bcount<count($breaks_id);$bcount++)
          {
            $break_list_val.=$breaks_id[$bcount].",";
          }
        $sql_query="update $db.tbl_wfm_agent_sched_instance set v_breakList='$break_list_val' where i_agentID='$agent_id' and i_shiftID='$shift_id' and i_procSchedID='$pro_id'  and d_schedStartDate>='$breaks_from 00:00:00' and d_schedEndDate<='$breaks_to 23:59:59'";
        mysqli_query($link,$sql_query) or die(mysqli_error());//vipul
  }

}

?>    
<form name="frmagentdashboardd" action="" method="post">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Agent Schedule</span>
        <div class=" row row1">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                      <tr>
                          <td class="left boder0-right" style="width: 250px">
                              <label>Schedule</label>
                              <div class="log-case">
                                <select class="select-styl1" style="width:190px" id="sch_id" name="sch_id">
                                  <?
                                    $sqlschedule="select i_procSchedID,v_schedName from $db.tbl_wfm_proc_schedule";    // query for getting agent between satisfied date
                                    $scheduleresult=mysqli_query($link,$sqlschedule);
                                    while($rowschedule=mysqli_fetch_array($scheduleresult)) 
                                  {
                                    ?>
                                      <option value="<?=$rowschedule['i_procSchedID']?>"><?=$rowschedule['v_schedName']?></option>
                                    <?
                                  }
                                ?>
                                </select>
                              </div>
                          </td>
                          <td  class="left  boder0-right" style="width: 300px">
                              <?php
                                $startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");
                                $enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");
                                ?>
                              <label>Select Date</label>
                              <input type="text" name="sttartdatetime" class="select-styl1 dob1 date_class" value="<?=$_REQUEST['sttartdatetime']?>" id="startdatetime" style="width:210px" autocomplete="off">
                             
                          </td>
                          <td class="left  boder0-right">
                          <div class="button_div">
                                <input style="margin-left: 10px;" type='submit' name='sub1' value='Show' class="submit_wfm button-orange1">
                              </div>
                                </td>
                          <td class="left  boder0-right" colspan="2">
                              <div class="details">
                                <?php 
                                $qq = "select * from $db.tbl_wfm_proc_schedule_list  where 1=1  ";
                                $i = 1;
                                $ticket_query = mysqli_query($link,"$qq");
                                while($ticket_res = mysqli_fetch_array($ticket_query)){
                                  $i_procSchedID=$ticket_res['i_procSchedID'];
                                  $noOfAgents = $ticket_res['i_noOfAgents'];
                                  $i_shiftID = $ticket_res['i_shiftID'];
                                  $total_Assigned_agent=get_Agent_Assigned_For_Schedule($i_procSchedID,$i_shiftID);
                                  $Deficiency=($noOfAgents-$total_Assigned_agent);
                                ?>
                                  <span><strong>Shft- <?php echo $i;?> - </strong><span style="color:red"> Total</span> : <?php echo $noOfAgents;?>,<span style="color:green">Assigned</span> : <?php echo $total_Assigned_agent;?>, <span style="color:blue"> Diff</span> : <?php echo $Deficiency;?>,</span>
                                <?php 
                                $i++; }?>
                              </div>
                          </td>
                      </tr>
                      <tr>
                      <td style="background-color: #2196F3;color:#fff;padding:7px;">Assigned Schedule</td>
                      <td style="background-color: #FFFF00;padding:7px;">Assigned Break</td>
                      <td style="background-color: #CCC;padding:7px;">On leave</td>
                      <td style="background-color: #CDEEFA;padding:7px;">Unallocated Space</td>
                      <td>
              </tr>
                </tbody>
            </table>
        </div>
        <div  id="div_adherence">
          <div class=" rows">
            <div class="col-25"></div>
            <div class="col-60">
              <div class="chart">
                    <? for($t=0;$t<24;$t++)
                    {
                      if($t==0)
                      {
                        $margin_tick=0;
                      }
                      else
                      {
                        $margin_tick+=PIXCEL;
                      }
                      $class_margin_tick="left:".$margin_tick."%";
                      ?>
                  <div class="tick" style="<?=$class_margin_tick?>"><span><?=$t?></span></div>
                  
                  <?
                    }
                ?>
              </div>
            </div>
          </div><br><br>
          <?
          // echo "wew ".date('Y-m-d');
                extract($_REQUEST);
              if(!empty($_REQUEST['sttartdatetime'])){ $startdatetime=date('Y-m-d',strtotime($_REQUEST['sttartdatetime'])); }else{  $startdatetime=date('Y-m-d'); }

              $schedid=$_REQUEST['sch_id'];
              // echo$startdatetime;
              $dcolor=0;
              // $sqlsource="select distinct(i_AgentID),i_shiftID,v_breakList from $db.tbl_wfm_agent_sched_assignment where i_procSchedID='$schedid' and (d_startTime<='$startdatetime 00:00:00' and d_endTime>='$startdatetime 23:59:59')  ";

              $sqlsource_ac="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_procSchedID='$schedid' and (  d_schedStartDate>='$startdatetime 00:00:00' and  d_schedEndDate<='$startdatetime 23:59:59')  ";     // query for getting agent between satisfied date
                    $sourceresult_ac=mysqli_query($link,$sqlsource_ac);
            
            // echo $num_row;
            
                      while($row_ac=mysqli_fetch_array($sourceresult_ac)) 
            {

              $agent_id=$row_ac["i_agentID"];
              $sqlsource="select distinct(i_agentID),i_shiftID,v_breakList from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$schedid' and (  d_schedStartDate>='$startdatetime 00:00:00' and  d_schedEndDate<='$startdatetime 23:59:59')  ";     // query for getting agent between satisfied date
                      $sourceresult=mysqli_query($link,$sqlsource);
                      $num_row=mysqli_num_rows($sourceresult); $c_i=1;$str_blank="";
            // echo $num_row;
            if($dcolor%2==0)
              {
                $rval="row1";
              }
          ?>
            <div class="rows row_2 <?=$rval?>">
              <?
              $sqlsource_agent="select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserID='$agent_id'";                // query for getting agent name
                      $sourceresult_agent=mysqli_query($link,$sqlsource_agent);
                      $row_agent=mysqli_fetch_array($sourceresult_agent);
              
              $agent_name=$row_agent["AtxDisplayName"];
              $rval="";   
              ?>  
              <div class="col-25">
                <?
                if($_SESSION['user_group']=='0000' || $_SESSION['user_group']=='080000')
                {
                  ?>
                  <!-- <img class="img_icon btn_edit" src="images/box_edit-512.png" onclick="modal_popup('<?=$agent_id?>','<?=$shift_id?>','<?=$agent_name?>','<?=$schedid?>');"> -->
                  <?
                }
                ?>
                <b><?=$agent_name?></b>
              </div>
              <?   if($num_row>0)
              {
                      while($row=mysqli_fetch_array($sourceresult)) 
              {
                $assigned_start_time="00:00:00";$assigned_end_time="00:00:00";
                
                $shift_id=$row["i_shiftID"];
                ?>
                <div class="col-25">
                  <!-- <img class="img_icon btn_edit" src="images/box_edit-512.png" onclick="modal_popup('<?=$agent_id?>','<?=$shift_id?>','<?=$agent_name?>','<?=$schedid?>');"> -->
                </div>
                <div class="col-70">
                  <div class="w3-light-grey w3-round-xlarge">
                        <?
                        //  count agent for blank rows which is for unassigned agents
                        $sqlsource_count="select count(i_agentID) as cagent from $db.tbl_wfm_agent_sched_instance where i_shiftID='$shift_id' and (d_schedStartDate like '$startdatetime%' or d_schedEndDate like '$startdatetime%')  and i_procSchedID='$schedid'";
                        // echo $sqlsource_count;
                    $sourceresult_count=mysqli_query($link,$sqlsource_count);
                    $row_count=mysqli_fetch_array($sourceresult_count);
                        $count_agent=$row_count['cagent'];
                        
                        
                        $sqlsource_shift="select d_schedStartDate, d_schedEndDate,i_status from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and (d_schedStartDate like '$startdatetime%' or d_schedEndDate like '$startdatetime%')  and i_procSchedID='$schedid'";
                        // $sqlsource_shift="select v_shiftName, t_fromTime,t_toTime from $db.tbl_wfm_mst_shift where i_shiftID='$shift_id'";                // query for getting assigned schedule

                        // echo $sqlsource_shift;
                    $sourceresult_shift=mysqli_query($link,$sqlsource_shift);
                    while($row_shift=mysqli_fetch_array($sourceresult_shift))
                    { 
                      // print_r($row_shift);
                      $shift_margin=0;$shift_width=0;$st_margin=0;$st_width=0;$st_min_w=0;
                      $leave=$row_shift['i_status'];
                      $shift_stime=explode(":",date("H:i:s",strtotime($row_shift['d_schedStartDate'])));
                      $shift_etime=explode(":",date("H:i:s",strtotime($row_shift['d_schedEndDate'])));

                      // $shift_stime=explode(":",$row_shift['d_schedStartDate']);
                      // $shift_etime=explode(":",$row_shift['d_schedEndDate']);

                      // $assigned_start_time=$row_shift['d_schedStartDate'];
                      // $assigned_end_time=$row_shift['d_schedEndDate'];

                      $assigned_start_time=date("H:i:s",strtotime($row_shift['d_schedStartDate']));
                      $assigned_end_time=date("H:i:s",strtotime($row_shift['d_schedEndDate']));
                        // echo "aarti--2";
                      $shift_margin=shift_margin($shift_stime[0]);
                      $st_min=floor(($shift_stime[1])/15);
                      $shift_margin+=$st_min;
                            
                      $shiftetime= 0;              // to get the difference between hours assigned in shift : By Vipul Dwivedi
                        // echo "aarti--1";
                      if($shift_stime[0]>$shift_etime[0])
                      {
                        $shiftetime=24;
                      }
                      else
                      {
                        $shiftetime=$shift_etime[0];
                        $st_min_w=($shift_etime[1])/15;
                      }

                      $shift_width=shift_margin($shiftetime);
                      $shift_width+=$st_min_w;
                      $shift_width-=$shift_margin;

                      $class_margin_shift="margin-left:".$shift_margin."%;";
                      $class_width_shift="width:".$shift_width."%;";

                      if($leave!=1)
                      {
                          $class_instance="w3-blue";
                      }
                      else
                      {
                          $class_instance="class_instance_grey";
                      }


                        $sqlsource_shiftnm="select v_shiftName, t_fromTime,t_toTime from $db.tbl_wfm_mst_shift where i_shiftID='$shift_id'";                // query for getting assigned schedule
                    $sourceresult_nm=mysqli_query($link,$sqlsource_shiftnm);
                    $row_nm=mysqli_fetch_array($sourceresult_nm); 

                    // echo "aarti";
                      ?>

                      <div class="w3-container <?=$class_instance?>  tooltip_shift" style="<?=$class_margin_shift?><?=$class_width_shift?>" onclick="<?     if($_SESSION['user_group']=='0000' || $_SESSION['user_group']=='080000'){?>modal_popup('<?=$agent_id?>','<?=$shift_id?>','<?=$agent_name?>','<?=$schedid?>');<?}?>"><span class="tooltiptext_shift"> <b><?=$row_nm['v_shiftName']?> :</b> <?=$assigned_start_time?> to <?=$assigned_end_time?></span>&nbsp;</div>
                      <?
                        ################# breaks for assigned  ############################
                      $shift_break=explode(",",$row['v_breakList']);

                        for($s_break=0;$s_break<count($shift_break);$s_break++)// for loop for comma (,) seperated values
                        {
                          $assigned_break_id=$shift_break[$s_break];
                          $sqlsource_abreak="select v_breakName,d_startbreak,d_endbreak from $db.tbl_wfm_mst_break where i_breakID='$assigned_break_id'";
                    $sourceresult_abreak=mysqli_query($link,$sqlsource_abreak);
                    while($row_abreak=mysqli_fetch_array($sourceresult_abreak)) 
                          {
                            $break_id=$row_abreak["i_breakID"];
                            $break_amargin=0;$break_awidth=0;
                            $st_amin_b=0;$et_amin_b=0;
                            $sb_atime=explode(":",$row_abreak['d_startbreak']);
                            $eb_atime=explode(":",$row_abreak['d_endbreak']);
                            
                                // for break margin
                                $break_amargin=shift_margin1($sb_atime[0],$sb_atime[1]);
                              
                                $break_awidth=shift_margin($eb_atime[0]);
                                $et_amin_b=($eb_atime[1])/15;
                                $break_awidth+=$et_amin_b;
                                $break_awidth-=$break_amargin;


                                
                              
                              $class_margin_abreak="margin-left:".$break_amargin."%;";
                              $class_width_abreak="width:".$break_awidth."%;";
                                ################# breaks for agents end ########################
                              ?>
                              <div class="break_assigned tooltip_break" style="<?=$class_margin_abreak?><?=$class_width_abreak?>"><span class="tooltiptext_break"><?=$row_abreak['v_breakName']?> : <?=date("H:i:s",strtotime($row_abreak['d_startbreak']))?> to <?=date("H:i:s",strtotime($row_abreak['d_endbreak']))?></span>&nbsp;</div>
                                  <?
                          }   //break while end

                        }     // comma seperated for loop end
                      
                      }
                    ?>
                  </div><br />
                </div>
                
              <?
              }  //while agent end
              ?>    
            </div> 
          <?$dcolor++;
          }     // if num row end
          }

            ?>

            <?
        //  count agent for blank rows which is for unassigned agents
                  $sqlagent_count="select i_noOfAgents,i_shiftID from $db.tbl_wfm_proc_schedule_list where i_procSchedID='$schedid'";
                          $sourceagent_count=mysqli_query($link,$sqlagent_count);
                          while($row_count_agent=mysqli_fetch_array($sourceagent_count))
                  {     
                    $count_agent_master=$row_count_agent['i_noOfAgents'];
                    $shift_id=$row_count_agent['i_shiftID'];


                    

                      $sqlsource_shift_count="select count(i_agentID) as count_agent from $db.tbl_wfm_agent_sched_instance where i_shiftID='$shift_id' and (d_schedStartDate like '$startdatetime%' or d_schedEndDate like '$startdatetime%')  and i_procSchedID='$schedid'";
                  
                    
                            $sourceresult_shift_count=mysqli_query($link,$sqlsource_shift_count);
                            while($row_shift_count=mysqli_fetch_array($sourceresult_shift_count))
                    {

                      $total_count_agent=$count_agent_master-$row_shift_count['count_agent'];
                      $shift_margin=0;$shift_width=0;$st_margin=0;$st_width=0;$st_min_w=0;

                      $sqlsource_shift_nm="select v_shiftName, t_fromTime,t_toTime from $db.tbl_wfm_mst_shift where i_shiftID='$shift_id'";
                                  // query for getting assigned schedule
                              $sourceresult_shift_nm=mysqli_query($link,$sqlsource_shift_nm);
                              $row_shift_nm=mysqli_fetch_array($sourceresult_shift_nm);
                      
                      $shift_stime=explode(":",date("H:i:s",strtotime($row_shift_nm['t_fromTime'])));
                      $shift_etime=explode(":",date("H:i:s",strtotime($row_shift_nm['t_toTime'])));

                      // echo $shift_stime[0];

                      // $shift_stime=explode(":",$row_shift['d_schedStartDate']);
                      // $shift_etime=explode(":",$row_shift['d_schedEndDate']);

                      // $assigned_start_time=$row_shift['d_schedStartDate'];
                      // $assigned_end_time=$row_shift['d_schedEndDate'];

                      $assigned_start_time=date("H:i:s",strtotime($row_shift_nm['t_fromTime']));
                      $assigned_end_time=date("H:i:s",strtotime($row_shift_nm['t_toTime']));

                      $shift_margin=shift_margin($shift_stime[0]);
                      $st_min=floor(($shift_stime[1])/15);
                      $shift_margin+=$st_min;
                            
                      $shiftetime= 0;              // to get the difference between hours assigned in shift : By Vipul Dwivedi
                      if($shift_stime[0]>$shift_etime[0])
                      {
                        $shiftetime=24;
                      }
                      else
                      {
                        $shiftetime=$shift_etime[0];
                        $st_min_w=($shift_etime[1])/15;
                      }

                      $shift_width=shift_margin($shiftetime);
                      $shift_width+=$st_min_w;
                      $shift_width-=$shift_margin;

                      $class_margin_shift="margin-left:".$shift_margin."%;";
                      $class_width_shift="width:".$shift_width."%;";
                    }
                    $str_blank="";
                    for($i_count=0;$i_count<$total_count_agent;$i_count++)
                    {
                    echo $str_blank="<br><div class='col-25'> </div>
              <div class='col-70'><div class='w3-container tooltip_shift' style='".$class_margin_shift." ".$class_width_shift." background-color:#CDEEFA;'><span class='tooltiptext_shift'> <span style='color:#FF0000;font-weight:bold;'>(Unallocated)</span> <b>".$row_shift_nm['v_shiftName']." :</b> ".$assigned_start_time." to ".$assigned_end_time."</span>&nbsp;</div></div><br>";
                    }
                      
                      // echo $str_blank;
                      // $str_blank="";
                      // $c_i=1;
                    
                  }
              ?>
          </div>
</form> 