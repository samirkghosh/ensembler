<?php
/**
 * Auth: Vastivkta Nishad
 * Date: 17 May 2024
 * Description: to fetch data related to adherence schedule
 */
include("../../config/web_mysqlconnect.php");
include("wfm_function.php");
//print_r($centralspoc);
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
define("PIXCEL","4");
global $start_time_mins,$start_time_mix,$break_start_time_max,$break_start_time_mins;

// to adjust the extra pixels
function shift_margin($shift_stime)
{
    $shift = $shift_stime; //$shift_etime[0];
    $shftm=0;
    if (  $shift <= 4)
      $shiftm = 1;
    else if ( $shift<= 8)
      $shiftm = 2;
    else if ( $shift <= 12)
      $shiftm = 3;
    else if ( $shift<= 16)
      $shiftm = 4;
    else if ( $shift<= 20)
      $shiftm = 5;
    else if ( $shift<= 24)
      $shiftm = 6;

  $shift_margin = (PIXCEL * $shift) - $shiftm;
  return $shift_margin;
}

function shift_margin1($hr,$min){
  /* Break hr and min */
  $hrPix = $hr * PIXCEL;

  $minPix = $min/15;
  $hrPix = $hrPix + $minPix;
 $shift = $hrPix/PIXCEL;
 // echo"<br>shift:".$shift;
        $shftm=0;
        if (  $shift <= 4)
          $shiftm = 1;
        else if ( $shift<= 8)
            $shiftm = 2;
         else if ( $shift <= 12)
            $shiftm = 3;
          else if ( $shift<= 16)
              $shiftm = 4;
          else if ( $shift<= 20)
              $shiftm = 5;
            else if ( $shift<= 24)
              $shiftm = 6;
            else
              $shiftm = 0;

      $shift_margin = (PIXCEL * $shift)-$shiftm;
     // echo "<br>".$shift_margin.'pix:'.PIXCEL.'shift:'.$shift.'shtime:'.$shiftm;
     return $shift_margin;
}



if(isset($_POST['get_dat']))
{

  $sched_id=$_POST['sched_id'];
?>
  <div class="rows">
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
</div></div><br><br>
  <?
  // echo "wew ".date('Y-m-d');
       extract($_REQUEST);
      if(!empty($_REQUEST['get_dat'])){ $startdatetime=date('Y-m-d',strtotime($_REQUEST['get_dat'])); }else{  $startdatetime=date('Y-m-d'); }
      $agent_uid=$_SESSION['userid'];
      $sched_id=$_REQUEST['sched_id'];
      $dcolor=0;
      $sqlsource="select distinct(i_agentID) from $db.tbl_wfm_agent_sched_instance where i_procSchedID='$sched_id' and (d_schedStartDate>='$startdatetime 00:00:00' and d_schedEndDate<='$startdatetime 23:59:59')";    // query for getting agent between satisfied date
      if($_SESSION['user_group']=='070000')
      {
      	$sqlsource.=" and i_agentID='$agent_uid'";
      }
    $sourceresult=mysqli_query($link,$sqlsource);
    while($row=mysqli_fetch_array($sourceresult)) {
      
      $agent_id=$row["i_agentID"];
      $shift_id=$row["i_shiftID"];
      $sqlsource_agent="select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserID='$agent_id'";                
      // query for getting agent name
      $sourceresult_agent=mysqli_query($link,$sqlsource_agent);
      $row_agent=mysqli_fetch_array($sourceresult_agent);
      
      $agent_name=$row_agent["AtxDisplayName"];
      $rval="";
      if($dcolor%2==0)
      {
        $rval="row1";
      }
      
  ?>
    <div class="rows row_2 <?=$rval?>">
      <div class="col-25">
  <?=$agent_name?>
      </div>
      <div class="col-70">
        
        <div class="w3-light-grey w3-round-xlarge">
      <?
      //************* margin for shift attended start time ******************
      $class_margin_break="";
      $sqlsource_st="select i_shiftID,d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,i_status,i_logout_flag from $db.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$sched_id' and (d_actualStartTime like '$startdatetime%' or d_actualEndTime like '$startdatetime%')";
      $sourceresult_st=mysqli_query($link,$sqlsource_st);
      while($row_stime=mysqli_fetch_array($sourceresult_st))
      {
      $assigned_start_time="00:00:00";$assigned_end_time="00:00:00";
      $st_margin=0;$st_width=0;$st_min_w=0;
      $leave=$row_stime['i_status'];

      $shift_id=$row_stime['i_shiftID'];
      $s_time=explode(":",date("H:i:s",strtotime($row_stime['d_actualStartTime'])));
      $e_time=explode(":",date("H:i:s",strtotime($row_stime['d_actualEndTime'])));

      $s_date=date("Y-m-d",strtotime($row_stime['d_actualStartTime'])); //schedule start date
      $e_date=date("Y-m-d",strtotime($row_stime['d_actualEndTime']));   //schedule end date

     $attended_start_time=date("H:i:s",strtotime($row_stime['d_actualStartTime']));
      $attended_end_time=date("H:i:s",strtotime($row_stime['d_actualEndTime']));

      $assigned_start_time=date("H:i:s",strtotime($row_stime['d_schedStartDate']));
      $assigned_end_time=date("H:i:s",strtotime($row_stime['d_schedEndDate']));


      // for not logged out first time : By Vipul Dwivedi 23-10-2018
      $d_actualStartTime_sec=strtotime($row_stime['d_actualStartTime']);
      $d_schedStartTime_sec=strtotime($row_stime['d_schedStartDate']);

      // for logged out : By Vipul Dwivedi 23-10-2018
      $d_actualEndTime_sec=strtotime($row_stime['d_actualEndTime']);
      $d_schedEndTime_sec=strtotime($row_stime['d_schedEndDate']);
       // echo "<br>time".$s_time[0];
      $st_margin=shift_margin($s_time[0]);
      $st_min=floor(($s_time[1])/15);
      $st_margin+=$st_min;
      ################# margin for shift attended start time end ###############
      //************* margin for shift attended width ******************
      $endtime=0;$diff_assigned_mins="";$show_instance_end_time="";
      $currdate=date("Y-m-d");
      //*********************** condition for checking current date **********************
      if($s_date==$currdate && $attended_end_time==NULL)
      {

        // date_default_timezone_set('Asia/Kolkata');    //need to remove later
        $show_instance_end_time= date("H:i:s");
        $currtime=explode(":",date("H:i:s"));
        $endtime=$currtime[0];
        $st_min_w=($currtime[1])/15;

        $diff_assigned=strtotime($attended_start_time)-strtotime($assigned_start_time);
        $diff_assigned_mins=$diff_assigned/60;
        $total_diff_shift=floor($diff_assigned_mins);
      }
      else
      {
        if($s_time[0]>$e_time[0])
        {       
          $endtime=24;
        }
        else
        {        
          $endtime=$e_time[0];
          $st_min_w=($e_time[1])/15;
        }

        // echo "Assst".$attended_start_time." Assend".$attended_end_time;
        $show_instance_end_time=$attended_end_time;
        $diff_assigned=strtotime($assigned_end_time)-strtotime($assigned_start_time);
        $diff_assigned_mins=$diff_assigned/60;

        $diff_attended=strtotime($attended_end_time)-strtotime($attended_start_time);
        $diff_attended_mins=$diff_attended/60;
        $total_diff_shift=floor($diff_assigned_mins-$diff_attended_mins);
      }
      
      //*************** Condition for current date ends********************
        
      	// echo "endtime".$endtime;
        $st_width=shift_margin($endtime);
        $st_width+=$st_min_w;
        $st_width-=$st_margin;

        // echo "width:".$st_width." margin:".$st_margin;
        //echo "tot".$total_diff_shift;
     
      ################# margin for shift attended width end ###############
        if($leave!=1)
        {
          // if not logged-out atleast one time
          if($row_stime['i_logout_flag']==0 )
          {
            // echo$d_actualStartTime_sec."sched:".$d_schedStartTime_sec;
            if($d_actualStartTime_sec<=$d_schedStartTime_sec){  //actual start time and schedule time are equal than display green grid
                  $class_instance="class_instance_green";
            }else{
              $diff_start_time_sec=$d_actualStartTime_sec-$d_schedStartTime_sec;
              $diff_start_time_mins=intval($diff_start_time_sec/60);
              if($diff_start_time_mins>= $start_time_max) //actual start time and schedule time are not equal 5min grater than display red grid
              {
                  $class_instance="class_instance_red";
              }
              else if($diff_start_time_mins>= $start_time_mins && $diff_start_time_mins< $start_time_max)
              {
                  $class_instance="class_instance_orange"; //actual start time and schedule time are not equal 1 to 5 min than display orange grid
              }
              else
              {
                  $class_instance="class_instance_green";
              }
            }
          }
          else // if logged-out atleast one time
          {
            if($d_actualStartTime_sec<=$d_schedStartTime_sec && $d_schedEndTime_sec<=$d_actualEndTime_sec)
            {
                  $class_instance="class_instance_green";
            }
            else
            {
              $diff_start_time_sec=$d_actualStartTime_sec-$d_schedStartTime_sec;
              $diff_start_time_mins=intval($diff_start_time_sec/60);

              $diff_end_time_sec=$d_schedEndTime_sec-$d_actualEndTime_sec;
              $diff_end_time_mins=intval($diff_end_time_sec/60);

              if($diff_start_time_mins>= $start_time_max || $diff_end_time_mins>= $start_time_max)
              {
                  $class_instance="class_instance_red"; //no adhering schedule grid display code - aarti
              }
              else if(($diff_start_time_mins>= $start_time_mins && $diff_start_time_mins< $start_time_max) || ($diff_end_time_mins>= $start_time_mins && $diff_end_time_mins< $start_time_max))
              {
                  $class_instance="class_instance_orange"; // less adhering schedule grid display code - aarti
              }
              else
              {
                  $class_instance="class_instance_green"; // adhering schedule grid display code - aarti
              }
            }
    	        // if($total_diff_shift>5)
    	        // {
    	        //     $class_instance="class_instance_red";
    	        // }
    	        // else if($total_diff_shift>=1 && $total_diff_shift<5)
    	        // {
    	        //     $class_instance="class_instance_orange";
    	        // }
    	        // else
    	        // {
    	        //     $class_instance="class_instance_green";
    	        // }
          }
  	   	}
  	   	else
  	   	{
  	   		$class_instance="class_instance_grey"; //leave adhering schedule grid display code - aarti
  	   	}
      
        $class_margin_st="margin-left:".$st_margin."%;";
        $class_width_st="width:".$st_width."%;";

        $sqlsource_shiftnm="select v_shiftName, t_fromTime,t_toTime from $db.tbl_wfm_mst_shift where i_shiftID='$shift_id'";                // query for getting assigned schedule
         $sourceresult_nm=mysqli_query($link,$sqlsource_shiftnm);
         $row_nm=mysqli_fetch_array($sourceresult_nm);
      
      ?>
    <div class="w3-container <?=$class_instance?> tooltip_instance" style="<?=$class_width_st?><?=$class_margin_st?>"><span class="tooltiptext_instance"> <b><?=$row_nm['v_shiftName']?> :</b> <?=date("H:i:s",strtotime($row_stime['d_actualStartTime']))?> to <?=$show_instance_end_time?></span>&nbsp;</div>
    <?
        ################# breaks for agents ############################
      
      // $sqlsource_break="select d_breakStTime,d_breakEndTime,i_breakID from $db.tbl_wfm_break_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and d_breakStTime>='$startdatetime 00:00:00' and d_breakEndTime<='$startdatetime 23:59:59'";
     $sqlsource_break="select d_breakStTime,d_breakEndTime,i_breakID from $db.tbl_wfm_break_instance where i_agentID='$agent_id' and i_shiftID='$shift_id' and i_procSchedID='$sched_id' and DATE(d_breakStTime)='$startdatetime'";
      $sourceresult_break=mysqli_query($link,$sqlsource_break);
      while($row_break=mysqli_fetch_array($sourceresult_break)) 
      {
        $break_id=$row_break["i_breakID"];
        $sqlsource_breakname="select v_breakName,d_startbreak,d_endbreak from $db.tbl_wfm_mst_break where i_breakID='$break_id'";
        $sourceresult_breakname=mysqli_query($link,$sqlsource_breakname);
        $row_breakname=mysqli_fetch_array($sourceresult_breakname);

        $break_margin=0;$break_width=0;$total_diff_break=0;
        $st_min_b=0;$et_min_b=0;
        $sb_time=explode(":",date("H:i:s",strtotime($row_break['d_breakStTime'])));
        $eb_time=explode(":",date("H:i:s"));
        // print_r($sb_time);  print_r($eb_time);
        $break_st_attended=date("H:i:s",strtotime($row_break['d_breakStTime']));
        $break_end_attended=date("H:i:s",strtotime($row_break['d_breakEndTime']));

        $break_date_start=date("Y-m-d",strtotime($row_break['d_breakStTime']));
        $break_date_end=date("Y-m-d",strtotime($row_break['d_breakEndTime']));

        $break_st_assigned=$row_breakname['d_startbreak'];
        $break_end_assigned=$row_breakname['d_endbreak'];
        $currdate=date("Y-m-d");
  
        $class_break="";$show_break_end_time="";
        if($break_date_start==$currdate && $row_break['d_breakEndTime']==NULL)
        {
          $show_break_end_time= date("H:i:s");
          $diff_assigned_break_start=strtotime($break_st_attended)-strtotime($break_st_assigned);
          $diff_assigned_mins_break_st=$diff_assigned_break_start/60;

          $diff_assigned_break_end=strtotime($break_end_attended)-strtotime($break_end_assigned);
          $diff_assigned_mins_break_end=$diff_assigned_break_end/60;

          $total_diff_break_st=floor($diff_assigned_mins_break_st);
          $total_diff_break_end=floor($diff_assigned_mins_break_end);
          // echo date("H:i:s");
          $eb_time=explode(":",date("H:i:s"));
          if($total_diff_break_st> $break_start_time_max || $total_diff_break_end> $break_start_time_max)
          {
              $class_break="break_grey";
          }
          
          else
          {
              $class_break="break_silver";
          }
        }
        else
        {
          $show_break_end_time=$break_end_attended;

          //commented on 23-10-2018 at 3:40PM By Vipul Dwivedi working code
          $diff_assigned_break=strtotime($break_end_attended)-strtotime($break_st_attended);
          $diff_assigned_mins_break=$diff_assigned_break/60;

          $diff_attended_break=strtotime($break_end_assigned)-strtotime($break_st_assigned);
          $diff_attended_mins_break=$diff_attended_break/60;

          $total_diff_break=floor($diff_assigned_mins_break-$diff_attended_mins_break);
          $eb_time=explode(":",date("H:i:s",strtotime($row_break['d_breakEndTime'])));

          if($total_diff_break> $break_start_time_max)
          {
              $class_break="break_grey";
          }
          
          else
          {
              $class_break="break_silver";
          }
        }

        if($sb_time[0]>=$s_time[0] && $eb_time[0]<=$endtime)
        {
           // for break margin
           $break_margin=shift_margin1($sb_time[0],$sb_time[1]);
           

           // echo$eb_time[0];
           $break_width=shift_margin($eb_time[0]);
           $et_min_b=($eb_time[1])/15;
           $break_width+=$et_min_b;
           $break_width-=$break_margin;
           
         
         $class_margin_break="margin-left:".$break_margin."%;";
         $class_width_break="width:".$break_width."%;";
      ################# breaks for agents end ########################
  ?>
     <div class="<?=$class_break?> tooltip_break" style="<?=$class_margin_break?><?=$class_width_break?>"><span class="tooltiptext_break"> <?=$row_breakname['v_breakName']?> : <?=date("H:i:s",strtotime($row_break['d_breakStTime']))?> to <?=$show_break_end_time?></span>&nbsp;</div>
        <?
        }//break if condition end


        }   //break while end


      }     //attended schedule while end
        ?>
  </div>
        </div>
    </div>  
    <?$dcolor++;
}

}      // Agent while end
    ?>
   <!-- <div class="botton row row1 "><input name="Save" class="button" value="Create" type="button" /></div>
  -->
