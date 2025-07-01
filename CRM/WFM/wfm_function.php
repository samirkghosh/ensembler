<?php
include("../../config/web_mysqlconnect.php");//  Connection to database // Please do not remove this
include("../web_function.php");
class Wfm_connection
{   
    function __construct() {
      global $db,$link,$max_value,$min_value,$break_start_time_max,$break_start_time_mins;
      $this->db = $db;
      $this->link = $link;
      $start_time_mins = '1';
      $start_time_max = '5';
      $break_start_time_max = '5';
      $break_start_time_mins = '1';
      if($_POST['action'] == 'Wfm_break_insert'){
          $this->wfm_break_instance();
      }
    }
    function Schedule_List(){
      $sqlschedule="select i_procSchedID,v_schedName from $this->db.tbl_wfm_proc_schedule";
      $scheduleresult=mysqli_query($sqlschedule);
      print_r($sqlschedule);
    }
    // to adjust the extra pixels
    function shift_margin($shift_stime){
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
    function wfm_break_instance(){
      global $db,$link;
      $agentID=$_SESSION['userid'];
      $breakID=$_REQUEST['breakID'];
      $mode=$_REQUEST['mode'];
      if(!empty($breakID)){
        $break_qry="SELECT *  FROM $db.tbl_wfm_mst_break WHERE v_breakName='$breakID'";
        $break_qrys = mysqli_query($link,$break_qry);
        $break_qrval=mysqli_fetch_array($break_qrys);
        $breakID = $break_qrval['i_breakID'];
      }
      if($mode==1){ //code for break start --
        $sqlup="SELECT i_shiftpref  FROM $db.uniuserprofile WHERE AtxUserID='$agentID'";
        $stm = mysqli_query($link,$sqlup);
        $rows=mysqli_num_rows($stm);
        if($rows>0){ 
          $rowval=mysqli_fetch_array($stm);               
          $i_shiftpref=$rowval['i_shiftpref'];
          $i_starttime=date("Y-m-d H:i:s");
          $i_datetime=date("Y-m-d");
          $shft_id_q=mysqli_fetch_array(mysqli_query($link,"select i_shiftID FROM $db.tbl_wfm_agent_sched_instance where i_agentID='$agentID' and (d_actualStartTime like '$i_datetime%')" ));
          $shft_id=$shft_id_q['i_shiftID'];

          /*insert data in break table for checking agent break time*/ 
          $sql="insert into $db.`tbl_wfm_break_instance` (`i_agentID`, `i_procSchedID`, `i_shiftID`, `d_breakStTime`,  `i_breakID`) VALUES
          ('$agentID', 1, '$shft_id', '$i_starttime', '$breakID')";
          mysqli_query($link,$sql) or die(mysqli_error());
        }
      }else if($mode == 2){
        $user_id=$_SESSION['userid'];
        $timep=date("Y-m-d H:i:s");
        $today_date=date("Y-m-d");
        $i_endtime=date("Y-m-d H:i:s");
        $sql1="update $db.`tbl_wfm_break_instance` set  `d_breakEndTime`='$i_endtime' where  i_breakID='$breakID' and date(d_breakStTime)='$today_date' and i_agentID='$user_id'";
          mysqli_query($link,$sql1) or die(mysqli_error());
      }
    }
    /*NEW FUNCTIONS----------------------------------*/
    function getUserName($id)
    {
      global $db,$link;
      $q="select AtxUserName from $db.uniuserprofile where AtxUserID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['AtxUserName'];
      return $output;
    }
    function displayagentname($userid)
    {
      global $db,$link;
      $sql_user="select AtxDisplayName from $db.uniuserprofile where AtxUserID='$userid'";
      $res_user=mysqli_query($link,$sql_user) or die(mysqli_error());
      $row_user=mysqli_fetch_array($res_user);
      $AtxDisplayName=$row_user['AtxDisplayName'];

      return($AtxDisplayName);
    }
    function getShiftName($id)
    {
      global $db,$link;
      $q="select v_shiftName from $db.tbl_wfm_mst_shift where i_shiftID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_shiftName'];
      return $output;
    }


    function getSkillName($id)
    {
    global $db,$link;
      $q="select v_SkillName from $db.tbl_wfm_mst_skill where i_skillID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_SkillName'];
      return $output;

    }


    function getProscheduleName($id)
    {
      global $db,$link;
      $q="select v_schedName from $db.tbl_wfm_proc_schedule where i_procSchedID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_schedName'];
      return $output;
    }

    function getBreakName($id)
    {
      global $db,$link;
      $q="select v_breakName from $db.tbl_wfm_mst_break where i_breakID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_breakName'];
      return $output;
    }

    function getTotalWorkingHour($agentid,$datetime)
    {
    global $db,$link;
         if($agentid!=''){ 
               $agent_name=getUserName($agentid); 
             $out .=" where UserName='$agent_name' "; 
          }

         if($datetime!='')
              {
                $dt=date('Y-m-d',strtotime($datetime));
                $startdatetime ="$dt 00:00:00";
                $enddatetime ="$dt 23:59:59";

                $from=$startdatetime;
                $to=$enddatetime;
                
                $out .=" and AccessedAt>='$from' and AccessedAt<='$to'  "; 
              }
          
          
          
           $diffhr=0;// agent total hour : Vipul Dwivedi
               $totalhour="";
               $qq_intime="select * from $db.logip   $out order by AccessedAt asc";
               $qq_exec=mysqli_query($link,$qq_intime);
                while($res_intime=mysql_fetch_array($qq_exec))
                {
                  
                  $diffhour=0;//difference in datetime
            if(!empty($res_intime['AccessedAt']) && !empty($res_intime['TimePeriod']))//if in and out not empty : Vipul Dwivedi 27-07-2018
            {
              $intime=$res_intime['AccessedAt'];
              $outtime=$res_intime['TimePeriod'];
              }
              else
              {
              if(!empty($res_intime['AccessedAt']) && empty($res_intime['TimePeriod']))//if in not empty & out empty : Vipul Dwivedi 27-07-2018
              {
               // echo "<br>In ".$intime=$res_intime['AccessedAt'];
               if(!empty($_REQUEST['sttartdatetime']))
                {
                $req_date=date("Y-m-d",strtotime($_REQUEST['sttartdatetime']));
                if($today!=$req_date)
                {
                  $outtime= date('Y-m-d H:i:s',strtotime('+8 hour',strtotime($intime)));
                }
                }
              
              }
              else if(empty($res_intime['AccessedAt']) && !empty($res_intime['TimePeriod']))
              {
                $intime=date('Y-m-d H:i:s',strtotime('-8 hour',strtotime($res_intime['TimePeriod'])));
                $outtime=$res_intime['TimePeriod'];
                
              }
                  
            }
                  $timelogin  = strtotime($intime);
                  $timelogout = strtotime($outtime);
                  $diffhour = ($timelogout-$timelogin);
                  $diffhr+=$diffhour;//in seconds
                  
                  
                  
                  $tothours = floor($diffhr / 3600);//in hours : Vipul Dwivedi
                  $totminutes = floor(($diffhr / 60) % 60);//in minutes  : Vipul Dwivedi
                  $totseconds = $diffhr % 60;//in seconds : Vipul Dwivedi
                  if(!empty($_REQUEST['sttartdatetime']))
                  {
                    $req_date=date("Y-m-d",strtotime($_REQUEST['sttartdatetime']));
                    if($today!=$req_date)
                    {
                      $totalhour=$tothours.":".$totminutes.":".$totseconds;
                    }
                  }           
          }//end of while
          
      return $totalhour;
    }


    function getAgent_AHT($agentid,$datetime){
        global $db,$link;
         if($agentid!=''){ 
               $agent_name=getUserName($agentid); 
             $out .=" where v_AgentName='$agent_name' "; 
          }
          if($datetime!='')
              {
                $dt=date('Y-m-d',strtotime($datetime));
                $startdatetime ="$dt";
                $from=$startdatetime;
                $out .=" and DATE(d_CreadtedDate)='$from'   "; 
              }
          
           $qq_avg="select i_talkTime from $db.tbl_servicelevel   $out order by d_CreadtedDate asc";
           $query_avg=mysqli_query($link,$qq_avg);
           $fetch_avg=mysqli_fetch_row($query_avg);
           $avg_seconds=$fetch_avg[0];
           
          // only if seconds are less than 86400 (1 day) :
          return gmdate('H:i:s', $avg_seconds);
           
           
    }


    function get_Agent_Assigned_For_Schedule($i_procSchedID,$i_shiftID)
    {
    global $db,$link;

     $sql_agent_assignment   ="SELECT count(*) AS agent_assign_count from $db.tbl_wfm_agent_sched_assignment WHERE i_procSchedID='".$i_procSchedID."' AND i_shiftID='".$i_shiftID."' ";
    $q_agent_assigned    =mysqli_query($link,$sql_agent_assignment);
    $fetch_agent_assign    =mysqli_fetch_array($q_agent_assigned);
    return $fetch_agent_assign['agent_assign_count'];
    }

    function get_AgentWith_PreferredShift($i_procSchedID,$i_shiftID,$match_val)
    {
    global $db,$link;
     $sql_agent_preferredShift     ="SELECT count(*) AS agent_shift_count from $db.tbl_wfm_agent_sched_assignment WHERE shift_flag='".$match_val."' 
    AND i_shiftID='".$i_shiftID."'  AND i_procSchedID='".$i_procSchedID."' ";
    $q_agent_preferredShift      =mysqli_query($link,$sql_agent_preferredShift);
    $fetch_agent_preferredShift    =mysqli_fetch_array($q_agent_preferredShift);
    return $fetch_agent_preferredShift['agent_shift_count'];

    }



    function get_Agent_Skills($i_procSchedID,$i_skillID,$match_val,$i_shiftID)
    {
    global $db,$link;
     $sql_agent_preferredSkill     ="SELECT count(*) AS agent_skill_count from $db.tbl_wfm_agent_sched_assignment WHERE skill_flag='".$match_val."' 
    AND i_shiftID='".$i_shiftID."' AND i_procSchedID='".$i_procSchedID."' ";
    $q_agent_preferredSkill      =mysqli_query($link,$sql_agent_preferredSkill);
    $fetch_agent_preferredSkill    =mysqli_fetch_array($q_agent_preferredSkill);
    return $fetch_agent_preferredSkill['agent_skill_count'];

    }

    //for history



    function getShiftName_hist($id)
    {
      global $db,$link;
      $q="select v_shiftName from $db.tbl_wfm_mst_shift_hist where i_shiftID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_shiftName'];
      return $output;
    }


    function getSkillName_hist($id)
    {
    global $db,$link;
      $q="select v_SkillName from $db.tbl_wfm_mst_skill_hist where i_skillID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_SkillName'];
      return $output;

    }


    function getProscheduleName_hist($id)
    {
      global $db,$link;
      $q="select v_schedName from $db.tbl_wfm_proc_schedule_hist where i_procSchedID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_schedName'];
      return $output;
    }

    function getBreakName_hist($id)
    {
      global $db,$link;
      $q="select v_breakName from $db.tbl_wfm_mst_break_hist where i_breakID='$id'";
      $res=mysqli_query($link,$q);
      $rs=mysqli_fetch_array($res);
      $output=$rs['v_breakName'];
      return $output;
    }

    function get_Agent_Assigned_For_Schedule_hist($i_procSchedID,$i_shiftID){
      global $db,$link;
      $sql_agent_assignment="SELECT count(*) AS agent_assign_count from $db.tbl_wfm_agent_sched_assignment_hist WHERE i_procSchedID='".$i_procSchedID."' AND i_shiftID='".$i_shiftID."' ";
      $q_agent_assigned=mysqli_query($link,$sql_agent_assignment);
      $fetch_agent_assign=mysqli_fetch_array($q_agent_assigned);
      return $fetch_agent_assign['agent_assign_count'];
    }

    function get_AgentWith_PreferredShift_hist($i_procSchedID,$i_shiftID,$match_val)
    {
    global $db,$link;
     $sql_agent_preferredShift="SELECT count(*) AS agent_shift_count from $db.tbl_wfm_agent_sched_assignment_hist WHERE shift_flag='".$match_val."' 
    AND i_shiftID='".$i_shiftID."'  AND i_procSchedID='".$i_procSchedID."' ";
    $q_agent_preferredShift      =mysqli_query($link,$sql_agent_preferredShift);
    $fetch_agent_preferredShift    =mysqli_fetch_array($q_agent_preferredShift);
    return $fetch_agent_preferredShift['agent_shift_count'];

    }



    function get_Agent_Skills_hist($i_procSchedID,$i_skillID,$match_val,$i_shiftID)
    {
    global $db,$link;
     $sql_agent_preferredSkill     ="SELECT count(*) AS agent_skill_count from $db.tbl_wfm_agent_sched_assignment_hist WHERE skill_flag='".$match_val."' 
    AND i_shiftID='".$i_shiftID."' AND i_procSchedID='".$i_procSchedID."' ";
    $q_agent_preferredSkill      =mysqli_query($link,$sql_agent_preferredSkill);
    $fetch_agent_preferredSkill    =mysqli_fetch_array($q_agent_preferredSkill);
    return $fetch_agent_preferredSkill['agent_skill_count'];

    }

    //end history

    function skill_flag($skill_val)
    {
    //1:exact_match;2:more_match;3:no_match
      if($skill_val==1)
      {
        $skillFlag='Exact Match';
      }else if($skill_val==2)
      {
        $skillFlag='More Match';
      }else if($skill_val==3){
        $skillFlag='No Match';
      }else{
        $skillFlag='';
      }
      
      return $skillFlag;

    }


    function to_string($data) {
      $output = '';
     
      if(!empty($data) && count($data) > 0) {
        foreach ($data as $values) {
        
        $values11[]=getSkillName($values);
      }
      
      
      }//end of if
     
      $output = join(',', $values11);
      return $output;
    }
    function login_entry_wfm($user_id,$database_name){
      global $database_name,$link;
      $today_date=date("Y-m-d");
      $shft_id_q=mysqli_fetch_array(mysqli_query($link,"select i_shiftID FROM $database_name.tbl_wfm_agent_sched_instance where i_agentID='$user_id' and (d_actualStartTime like '$today_date%')"));
      $shft_id=$shft_id_q['i_shiftID'];
      date_default_timezone_set('Asia/Kolkata');
      $datetime_wfm=date("Y-m-d H:i:s");
      $sql_instance = "select i_login_flag from $database_name.tbl_wfm_agent_sched_instance where i_shiftID='$shft_id' and date(d_actualStartTime)='$today_date' and i_agentID='$user_id'";
      $res_instance = mysqli_query($link,$sql_instance);
      $row_instance = mysqli_fetch_array($res_instance);
      if($row_instance['i_login_flag']==0)
      {
        $sql_update = "Update $database_name.tbl_wfm_agent_sched_instance set i_login_flag='1',d_actualStartTime='$datetime_wfm',d_actualEndTime='$datetime_wfm' where i_shiftID='$shft_id' and date(d_actualStartTime)='$today_date' and i_agentID='$user_id'";
        $res_update=mysqli_query($link,$sql_update);
      }
    }
    function logout_entry_wfm(){
      global $db,$link;
      $dbs = 'ensembler';
      /*------------------------WFM Report related code added aarti 30:03-23--------------------------------*/
      $today_date=date("Y-m-d");
      // getting shift id
      $user_id=$_SESSION['userid'];
      $shft_id_q=mysqli_fetch_array(mysqli_query($this->link,"select i_shiftID FROM $dbs.tbl_wfm_agent_sched_instance where i_agentID='$user_id' and (d_actualStartTime like '$today_date%' )"));
      $shft_id=$shft_id_q['i_shiftID'];
      if($shft_id != ''){
        //$shiftID=$row_shift['i_shiftpref'];
        date_default_timezone_set('Asia/Kolkata');
        $timep_wfm=date("Y-m-d H:i:s");

        //inserting logout time (and updating the flag to 1 if logged-out : Vipul Dwivedi 23-10-2018)
        $sql_update = "Update $dbs.tbl_wfm_agent_sched_instance set d_actualEndTime='$timep_wfm',i_logout_flag=1 where i_shiftID='$shft_id' and date(d_actualStartTime)='$today_date' and i_agentID='$user_id'";
        $res_update=mysqli_query($this->link,$sql_update);

        //inserting breakend time without resuming break on logout 
        $sql_update_brk = "Update $dbs.tbl_wfm_break_instance set d_breakEndTime='$timep_wfm' where i_shiftID='$shft_id' and date(d_breakStTime)='$today_date' and i_agentID='$user_id' and d_breakEndTime is NULL";
        $res_update_brk=mysqli_query($this->link,$sql_update_brk);
      }
      /*------------------------WFM Report related code added aarti 30:03-23--------------------------------*/
    }
    function ERlang_Calculation(){
      
    }
}
$controller = new Wfm_connection();

/*-------------------------------------------WFM CREATE SHIFT / BREAK / SCHEDULE RELATED CODE Auth: VASTVIKTA NISHAD Date: 22/02/2024-----------------------------*/
/**
 * Inserts a new shift record into the tbl_wfm_mst_shift table.
 *
 * @global  $link The global MySQLi database connection object.
 * @global $db The global variable representing the database name.
 */

if($_POST['action']=='break_delete'){
  break_delete();
}
if ($_POST['action'] == 'submit_breaks' || $_POST['action'] == 'update_breaks') {
  insert_or_update_breaks();
}
if($_POST['action'] == 'submit_shift'){
  insert_shift();
}
if($_POST['action']=='schedule_delete'){
  scheduleDelete();
}
if ($_POST['action'] == 'insert_or_update_schedule') {
  insert_or_update_schedule();
}
//getting break details from asterisk database beasuse agent break and schdule break need to be same
function getReasonStatus(){
  global $link;
  // SQL query to select all records from the autodial_user_statuses table
  $sql_sel = "SELECT * FROM asterisk.autodial_user_statuses";
  // Execute the SQL query
  $reason_status = mysqli_query($link, $sql_sel);
  // Return the result set or false if the query fails
  return $reason_status;
}
//function to fetch all break details 
function getBreakData(){
  global $link, $db;
  // SQL query to select all records from tbl_wfm_mst_break where i_status is 1
  $sqlsource_abreak = "SELECT * FROM $db.tbl_wfm_mst_break WHERE i_status = 1";
  // Execute the SQL query
  $sourceresult_abreak = mysqli_query($link, $sqlsource_abreak);
  // Return the result set or false if the query fails
  return $sourceresult_abreak;         
}
//getting break details on the basis of the id 
function getBreakDetails($breakID) {
  global $link, $db;
  // SQL query to select all records from tbl_wfm_mst_break where i_breakID is $breakID and i_status is 1
  $sql = "SELECT * FROM $db.tbl_wfm_mst_break WHERE i_breakID = $breakID AND i_status = 1";
  // Execute the SQL query
  $result = mysqli_query($link, $sql);
  // Check if a row is returned
  if ($result && mysqli_num_rows($result) > 0) {
      // Fetch and return the break details as an associative array
      $breakDetails = mysqli_fetch_assoc($result);
      return $breakDetails;
  } else {
      // Return false if no record is found
      return false;
  }
}
//function to update or insert break in the create breaks
function insert_or_update_breaks() {
  global $db, $link;
  // Check if break_name is set and not empty in the request
  if (isset($_REQUEST['break_name']) && $_REQUEST['break_name'] != "") {
      $break_name = $_REQUEST['break_name'];
      $from_time = $_REQUEST['from_time'];
      $to_time = $_REQUEST['to_time'];
      $break_type = $_REQUEST['break_type'];
      $vuserid = $_SESSION['userid'];
      // Check if breakId is present and not empty
      if (!empty($_REQUEST['breakId'])) {
          $brk_id = $_REQUEST['breakId'];
          // Update the existing break by setting i_status to 0
          $sql_delete_brk = "UPDATE $db.tbl_wfm_mst_break SET i_status=0 WHERE i_breakID=$brk_id";
          add_audit_log($vuserid, 'break_updated', '', 'Break Updated: ' . $break_name, $db);
    
          mysqli_query($link, $sql_delete_brk) or die(mysqli_error($link));
      }
      // Insert a new break record into the database
      $insert_query = "INSERT INTO $db.tbl_wfm_mst_break(i_breakType, v_breakName, d_startbreak, d_endbreak) VALUES ('$break_type', '$break_name', '$from_time', '$to_time')";
          add_audit_log($vuserid, 'break_created', '', 'Break Created: ' . $break_name, $db);
    
      $result = mysqli_query($link, $insert_query);
      // Echo success or error message
      if ($result) {
          echo 'success';
      } else {
          echo 'Error: ' . mysqli_error($link);
      }
      // Redirect to the current page
      header("Location: {$_SERVER['PHP_SELF']}");
      exit();
  }
}
//function to delete the break
function break_delete() {
  global $db, $link;
  // Check if 'breakId' is set in the POST request
  if (isset($_POST['breakId'])) {
      $brk_id = $_POST['breakId'];
      // Update i_status to 0 for the specified break
      $sql_delete_brk = "UPDATE $db.tbl_wfm_mst_break SET i_status = 0 WHERE i_breakID = $brk_id";
      $res = mysqli_query($link, $sql_delete_brk);
      // Echo success or error message
      if ($res) {
          echo 'success';
      } else {
          echo 'error: ' . mysqli_error($link);
      }
  }
}
//functions from create shift 

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

function shift_margin1($hr,$min)
{
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

function days_case($days_no)
{
    switch ($days_no) {
        case 0:
          echo "S";
          break;
       case 1:
          echo "M";
          break;
       case 2:
          echo "T";
          break;
       case 3:
          echo "W";
          break;
       case 4:
          echo "TH";
          break;
       case 5:
          echo "F";
          break;
       case 6:
          echo "SA";
          break;
      
      }
}
//function to fetch all the shifts from the database
function getShift() {
  global $link, $db;
  // Query to select all records from tbl_wfm_mst_shift
  $sqlsource_shift = "SELECT * FROM $db.tbl_wfm_mst_shift";
  // Execute the query
  $sourceresult_shift = mysqli_query($link, $sqlsource_shift);
  // Return the result set or false on failure
  return $sourceresult_shift;
}
//function to insert new shift in the table 
function insert_shift() {
  global $link, $db;

  $vuserid = $_SESSION['userid'];
  // Ensure the request contains the necessary data
  if (isset($_REQUEST["shift_name"], $_REQUEST["chk_days"], $_REQUEST["from_time"], $_REQUEST["to_time"])) {
      // Escape and validate input data
      $shift_name = mysqli_real_escape_string($link, $_REQUEST["shift_name"]);
      $days_val = $_REQUEST["chk_days"];
      $days_list = implode(",", $days_val);
      $from_time = mysqli_real_escape_string($link, $_REQUEST["from_time"]);
      $to_time = mysqli_real_escape_string($link, $_REQUEST["to_time"]);
     
      // Insert data into the database
      
      $query = "INSERT INTO $db.tbl_wfm_mst_shift(v_shiftName, v_weekDays, t_fromTime, t_toTime) VALUES ('$shift_name', '$days_list', '$from_time', '$to_time')";
      // Execute the query and output result messages
      if (mysqli_query($link, $query)) {
          echo "Shift submitted successfully";
          add_audit_log($vuserid, 'shift_created', '', 'Shift Created: ' . $shift_name, $db);
      } else {
          echo "Error: " . mysqli_error($link);
      }
  } else {
      echo "Incomplete data received";
  }
}
//function to fetch the details of schedule on the basis of the id 
function getProc($id)
{
  global $db,$link;
  $sql2 = "select * from $db.tbl_wfm_proc_schedule where i_procSchedID = '".$id."'";
	$fetch2 = mysqli_query($link,$sql2);
	return $fetch2;
}
//function to delete the schedule on the basis of the id 
function scheduleDelete()
{
  global $db,$link;
  $id = $_POST['i_procSchedID'];
  //get schedule details for requested schedule id
    $sql_sch = "select * from $db.tbl_wfm_proc_schedule where i_procSchedID = '".$id."'";
    $fetch_sch = mysqli_query($link,$sql_sch);
    $fetch_num_row=mysqli_num_rows($fetch_sch);
    while($row_sch = mysqli_fetch_array($fetch_sch))
    { 
      $v_schedName        = $row_sch['v_schedName'];
      $i_noOfShift        = $row_sch['i_noOfShift'];
      $v_shiftList        = $row_sch['v_shiftList'];
      $d_startDate        = $row_sch['d_startDate'];
      $d_endDate          = $row_sch['d_endDate'];
      if($fetch_num_row > 0)
      {
            $d_delDate  = date("Y-m-d h:i:s");

            //insert all the details in history table before deleting it
            $sql_sch_ins = "insert into $db.tbl_wfm_proc_schedule_hist(v_schedName,i_noOfShift,v_shiftList,d_startDate,d_endDate,del_Date) values('".$v_schedName."','".$i_noOfShift."','".$v_shiftList."','".$d_startDate."','".$d_endDate."','".$d_delDate."');";
            mysqli_query($link,$sql_sch_ins);
            $proc_last_id=mysqli_insert_id($link);
            //get shift details
            $sql_shft = "select * from $db.tbl_wfm_mst_shift";
            $fetch_shft = mysqli_query($link,$sql_shft);
            while($row_shft = mysqli_fetch_array($fetch_shft)){
                
                $i_shiftID         = $row_shft['i_shiftID'];
                $v_shiftName       = $row_shft['v_shiftName'];
                $v_weekDays        = $row_shft['v_weekDays'];
                $t_fromTime        = $row_shft['t_fromTime'];
                $t_toTime          = $row_shft['t_toTime'];

                //insert shift details in history table
                $sql_shft_ins = "insert into $db.tbl_wfm_mst_shift_hist(i_shiftID,v_shiftName,v_weekDays,t_fromTime,t_toTime,i_procID,del_Date) values('".$i_shiftID."','".$v_shiftName."','".$v_weekDays."','".$t_fromTime."','".$t_toTime."','".$proc_last_id."','".$d_delDate."');";
                
                mysqli_query($link,$sql_shft_ins);
                
            }
            //get skill details
            $sql_skill = "select * from $db.tbl_wfm_mst_skill";
            $fetch_skill = mysqli_query($link,$sql_skill);
            while($row_skill = mysqli_fetch_array($fetch_skill))
            {
                
              $i_skillID         = $row_skill['i_skillID'];
              $v_skillName       = $row_skill['v_SkillName'];

              //insert skill details in history table
              $sql_skill_ins = "insert into $db.tbl_wfm_mst_skill_hist(i_skillID,v_SkillName,i_procID,del_Date) values('".$i_skillID."','".$v_skillName."','".$proc_last_id."','".$d_delDate."');";
              mysqli_query($link,$sql_skill_ins);
                
            }
            //get break details
            $sql_break = "select * from $db.tbl_wfm_mst_break";
            $fetch_break = mysqli_query($link,$sql_break);
            while($row_break = mysqli_fetch_array($fetch_break))
            {
                
                $i_breakID         = $row_break['i_breakID'];
                $i_breakType       = $row_break['i_breakType'];
                $v_breakName       = $row_break['v_breakName'];
                $d_startbreak       = $row_break['d_startbreak'];
                $d_endbreak       = $row_break['d_endbreak'];
                $i_status       = $row_break['i_status'];

                //insert break details in history table
            $sql_break_ins = "insert into $db.tbl_wfm_mst_break_hist(i_breakID,i_breakType,v_breakName,d_startbreak,d_endbreak,i_status,i_procID,del_Date) values('".$i_breakID."','".$i_breakType."','".$v_breakName."','".$d_startbreak."','".$d_endbreak."','".$i_status."','".$proc_last_id."','".$d_delDate."');";
             mysqli_query($link,$sql_break_ins);
                
            }

             $sql_sch_list = "select * from $db.tbl_wfm_proc_schedule_list";
            $fetch_sch_list = mysqli_query($link,$sql_sch_list);
            while($row_sch_list = mysqli_fetch_array($fetch_sch_list)){
                
                $i_shiftID       = $row_sch_list['i_shiftID'];
                $i_skillID       = $row_sch_list['i_skillID'];
                $i_noOfAgents    = $row_sch_list['i_noOfAgents'];
                $v_breakid       = $row_sch_list['v_breakid'];

                //insert break details in history table
              $sql_sch_list_ins = "insert into $db.tbl_wfm_proc_schedule_list_hist(i_procSchedID,i_shiftID,i_skillID,i_noOfAgents,v_breakid,del_Date) values('".$proc_last_id."','".$i_shiftID."','".$i_skillID."','".$i_noOfAgents."','".$v_breakid."','".$d_delDate."');";
              mysqli_query($link,$sql_sch_list_ins);
                
            }

            $sql_break_inst = "select * from $db.tbl_wfm_break_instance";
            $fetch_break_inst = mysqli_query($link,$sql_break_inst);
            while($row_break_inst = mysqli_fetch_array($fetch_break_inst))
            { 
                $i_agentID       = $row_break_inst['i_agentID'];
                $i_shiftID       = $row_break_inst['i_shiftID'];
                $d_breakStTime   = $row_break_inst['d_breakStTime'];
                $d_breakEndTime  = $row_break_inst['d_breakEndTime'];
                $i_breakID       = $row_break_inst['i_breakID'];

                //insert break details in history table
            $sql_break_inst_ins = "insert into $db.tbl_wfm_break_instance_hist(i_agentID,i_procSchedID,i_shiftID,d_breakStTime,d_breakEndTime,i_breakID,del_Date) values('".$i_agentID."','".$proc_last_id."','".$i_shiftID."','".$d_breakStTime."','".$d_breakEndTime."','".$i_breakID."','".$d_delDate."');";
             mysqli_query($sql_break_inst_ins);
                
            }

            //get the details of schedule assignment
            $sql_sch_ass = "select * from $db.tbl_wfm_agent_sched_assignment";
            $fetch_sch_ass = mysqli_query($link,$sql_sch_ass);
            while($row_sch_ass = mysqli_fetch_array($fetch_sch_ass))
            { 
                
                $i_shiftID       = $row_sch_ass['i_shiftID'];
                $i_AgentID       = $row_sch_ass['i_AgentID'];
                $d_startTime     = $row_sch_ass['d_startTime'];
                $d_endTime       = $row_sch_ass['d_endTime'];
                $v_breakList     = $row_sch_ass['v_breakList'];
                $shift_flag      = $row_sch_ass['shift_flag'];
                $skill_flag      = $row_sch_ass['skill_flag'];

                //insert break details in history table
            $sql_sch_ass_ins = "insert into $db.tbl_wfm_agent_sched_assignment_hist(i_procSchedID,i_shiftID,i_AgentID,d_startTime,d_endTime,v_breakList,shift_flag,skill_flag,del_Date) values('".$proc_last_id."','".$i_shiftID."','".$i_AgentID."','".$d_startTime."','".$d_endTime."','".$v_breakList."','".$shift_flag."','".$skill_flag."','".$d_delDate."');";
             mysqli_query($link,$sql_sch_ass_ins);
                
            }
            //get details of agent schedule instance
            $sql_sch_ass_inst = "select * from $db.tbl_wfm_agent_sched_instance";
            $fetch_sch_ass_inst = mysqli_query($link,$sql_sch_ass_inst);
            while($row_sch_ass_inst = mysqli_fetch_array($fetch_sch_ass_inst))
            { 
                
                $i_agentID            = $row_sch_ass_inst['i_agentID'];
                $i_procSchedID        = $row_sch_ass_inst['i_procSchedID'];
                $i_shiftID            = $row_sch_ass_inst['i_shiftID'];
                $d_schedStartDate     = $row_sch_ass_inst['d_schedStartDate'];
                $d_schedEndDate       = $row_sch_ass_inst['d_schedEndDate'];
                $d_actualStartTime    = $row_sch_ass_inst['d_actualStartTime'];
                $d_actualEndTime      = $row_sch_ass_inst['d_actualEndTime'];
                $v_breakList          = $row_sch_ass_inst['v_breakList'];
                $i_status             = $row_sch_ass_inst['i_status'];
                $v_remarks            = $row_sch_ass_inst['v_remarks'];
                $substitute_id        = $row_sch_ass_inst['substitute_id'];
                $i_login_flag         = $row_sch_ass_inst['i_login_flag'];
                $i_logout_flag        = $row_sch_ass_inst['i_logout_flag'];
                $i_adhere             = $row_sch_ass_inst['i_adhere'];

                //insert break details in history table
          $sql_sch_ass_inst_ins = "insert into $db.tbl_wfm_agent_sched_instance_hist(i_agentID,i_procSchedID,i_shiftID, d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,v_breakList,i_status,v_remarks,substitute_id,i_login_flag,i_logout_flag,i_adhere,del_Date) values('".$i_agentID."','".$proc_last_id."','".$i_shiftID."','".$d_schedStartDate."','".$d_schedEndDate."','".$d_actualStartTime."','".$d_actualEndTime."','".$v_breakList."','".$i_status."','".$v_remarks."','".$substitute_id."','".$i_login_flag."','".$i_logout_flag."','".$i_adhere."','".$d_delDate."');";
             mysqli_query($link,$sql_sch_ass_inst_ins);                
            }//while end
      }//if end
      
  }// while end


  $vuserid = $_SESSION['userid'];
  $sql_delete_sched="DELETE from $db.tbl_wfm_proc_schedule where i_procSchedID=$id";
  
  $name = get_schedulename($id);

  add_audit_log($vuserid, 'delete_schedule', '', 'Schedule Deleted: ' . $name, $db);
  mysqli_query($link,$sql_delete_sched);

  $sql_delete_sched_list="DELETE from $db.tbl_wfm_proc_schedule_list where i_procSchedID=$id";
  mysqli_query($link,$sql_delete_sched_list);

  $sql_delete_assign="DELETE from $db.tbl_wfm_agent_sched_assignment where i_procSchedID=$id";
  mysqli_query($link,$sql_delete_assign);

  $sql_delete_instance="DELETE from $db.tbl_wfm_agent_sched_instance where i_procSchedID=$id";
  mysqli_query($link,$sql_delete_instance);

  $sql_delete_break="DELETE from $db.tbl_wfm_break_instance where i_procSchedID=$id"; // only delete and select code added not added insert code
  mysqli_query($link,$sql_delete_break);

   $sql_reset_instance="ALTER TABLE $db.tbl_wfm_proc_schedule AUTO_INCREMENT = 1";
  mysqli_query($link,$sql_reset_instance);
}
function get_schedulename($id){
  global $db, $link;
  $sql = "SELECT v_schedName FROM $db.tbl_wfm_proc_schedule WHERE i_procSchedID = '$id'";
  $result = mysqli_query($link, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      return $row['v_schedName'];
  } else {
      return null; // or return "Not Found";
  }
}
// added code for audit history [vastvikta][19-04-2025]
//function to insert or update the schedule in the database
function insert_or_update_schedule()
{
  global $db,$link;
  $id = $_POST['i_procSchedID'];
  $sql_sch = "select * from $db.tbl_wfm_proc_schedule"; //get schedule details for requested schedule id
  $fetch_sch = mysqli_query($link,$sql_sch);
  $fetch_num_row=mysqli_num_rows($fetch_sch);
  $vuserid = $_SESSION['userid'];
  // print_r($fetch_num_row);
  while($row_sch = mysqli_fetch_array($fetch_sch)){
      $v_schedName        = $row_sch['v_schedName'];
      $i_noOfShift        = $row_sch['i_noOfShift'];
      $v_shiftList        = $row_sch['v_shiftList'];
      $d_startDate        = $row_sch['d_startDate'];
      $d_endDate          = $row_sch['d_endDate'];
      $id                 = $row_sch['i_procSchedID'];
      if($fetch_num_row > 0){
              $d_delDate      = date("Y-m-d h:i:s");

              //insert all the details in history table before deleting it
              $sql_sch_ins = "insert into $db.tbl_wfm_proc_schedule_hist(v_schedName,i_noOfShift,v_shiftList,d_startDate,d_endDate,del_Date,i_type) values('".$v_schedName."','".$i_noOfShift."','".$v_shiftList."','".$d_startDate."','".$d_endDate."','".$d_delDate."','1');";
              add_audit_log($vuserid, 'update_schedule', '', 'Schedule Updated: ' . $v_schedName, $db);
 
              mysqli_query($link,$sql_sch_ins);
              $proc_last_id=mysqli_insert_id($link);
              //get shift details
              $sql_shft = "select * from $db.tbl_wfm_mst_shift";
              $fetch_shft = mysqli_query($link,$sql_shft);
              while($row_shft = mysqli_fetch_array($fetch_shft)){
                  
                  $i_shiftID         = $row_shft['i_shiftID'];
                  $v_shiftName       = $row_shft['v_shiftName'];
                  $v_weekDays        = $row_shft['v_weekDays'];
                  $t_fromTime        = $row_shft['t_fromTime'];
                  $t_toTime          = $row_shft['t_toTime'];

                  //insert shift details in history table
                $sql_shft_ins = "insert into $db.tbl_wfm_mst_shift_hist(i_shiftID,v_shiftName,v_weekDays,t_fromTime,t_toTime,i_procID,del_Date) values('".$i_shiftID."','".$v_shiftName."','".$v_weekDays."','".$t_fromTime."','".$t_toTime."','".$proc_last_id."','".$d_delDate."');";
                mysqli_query($link,$sql_shft_ins);
                  
              }

              //get skill details
              $sql_skill = "select * from $db.tbl_wfm_mst_skill";
              $fetch_skill = mysqli_query($link,$sql_skill);
              while($row_skill = mysqli_fetch_array($fetch_skill)){                   
                  $i_skillID         = $row_skill['i_skillID'];
                  $v_skillName       = $row_skill['v_SkillName'];

                  //insert skill details in history table
              $sql_skill_ins = "insert into $db.tbl_wfm_mst_skill_hist(i_skillID,v_SkillName,i_procID,del_Date) values('".$i_skillID."','".$v_skillName."','".$proc_last_id."','".$d_delDate."');";
               mysqli_query($link,$sql_skill_ins);
                  
              }
              //get break details
              $sql_break = "select * from $db.tbl_wfm_mst_break";
              $fetch_break = mysqli_query($link,$sql_break);
              while($row_break = mysqli_fetch_array($fetch_break))
              {
                  
                  $i_breakID         = $row_break['i_breakID'];
                  $i_breakType       = $row_break['i_breakType'];
                  $v_breakName       = $row_break['v_breakName'];
                  $d_startbreak       = $row_break['d_startbreak'];
                  $d_endbreak       = $row_break['d_endbreak'];
                  $i_status       = $row_break['i_status'];

                  //insert break details in history table
              $sql_break_ins = "insert into $db.tbl_wfm_mst_break_hist(i_breakID,i_breakType,v_breakName,d_startbreak,d_endbreak,i_status,i_procID,del_Date) values('".$i_breakID."','".$i_breakType."','".$v_breakName."','".$d_startbreak."','".$d_endbreak."','".$i_status."','".$proc_last_id."','".$d_delDate."');";
               mysqli_query($link,$sql_break_ins);
                  
              }

              $sql_sch_list = "select * from $db.tbl_wfm_proc_schedule_list";
              $fetch_sch_list = mysqli_query($link,$sql_sch_list);
              while($row_sch_list = mysqli_fetch_array($fetch_sch_list)){
                  
                  $i_shiftID       = $row_sch_list['i_shiftID'];
                  $i_skillID       = $row_sch_list['i_skillID'];
                  $i_noOfAgents    = $row_sch_list['i_noOfAgents'];
                  $v_breakid       = $row_sch_list['v_breakid'];

                  //insert break details in history table
              $sql_sch_list_ins = "insert into $db.tbl_wfm_proc_schedule_list_hist(i_procSchedID,i_shiftID,i_skillID,i_noOfAgents,v_breakid,del_Date) values('".$proc_last_id."','".$i_shiftID."','".$i_skillID."','".$i_noOfAgents."','".$v_breakid."','".$d_delDate."');";
               mysqli_query($link,$sql_sch_list_ins);
                  
              }

              $sql_break_inst = "select * from $db.tbl_wfm_break_instance";
              $fetch_break_inst = mysqli_query($link,$sql_break_inst);
              while($row_break_inst = mysqli_fetch_array($fetch_break_inst))
              { 
                  $i_agentID       = $row_break_inst['i_agentID'];
                  $i_shiftID       = $row_break_inst['i_shiftID'];
                  $d_breakStTime   = $row_break_inst['d_breakStTime'];
                  $d_breakEndTime  = $row_break_inst['d_breakEndTime'];
                  $i_breakID       = $row_break_inst['i_breakID'];

                  //insert break details in history table
              $sql_break_inst_ins = "insert into $db.tbl_wfm_break_instance_hist(i_agentID,i_procSchedID,i_shiftID,d_breakStTime,d_breakEndTime,i_breakID,del_Date) values('".$i_agentID."','".$proc_last_id."','".$i_shiftID."','".$d_breakStTime."','".$d_breakEndTime."','".$i_breakID."','".$d_delDate."');";
               mysqli_query($link,$sql_break_inst_ins);
                  
              }

              //get the details of schedule assignment
              $sql_sch_ass = "select * from $db.tbl_wfm_agent_sched_assignment";
              $fetch_sch_ass = mysqli_query($link,$sql_sch_ass);
              while($row_sch_ass = mysqli_fetch_array($fetch_sch_ass))
              { 
                  
                  $i_shiftID       = $row_sch_ass['i_shiftID'];
                  $i_AgentID       = $row_sch_ass['i_AgentID'];
                  $d_startTime     = $row_sch_ass['d_startTime'];
                  $d_endTime       = $row_sch_ass['d_endTime'];
                  $v_breakList     = $row_sch_ass['v_breakList'];
                  $shift_flag      = $row_sch_ass['shift_flag'];
                  $skill_flag      = $row_sch_ass['skill_flag'];

                  //insert break details in history table
              $sql_sch_ass_ins = "insert into $db.tbl_wfm_agent_sched_assignment_hist(i_procSchedID,i_shiftID,i_AgentID,d_startTime,d_endTime,v_breakList,shift_flag,skill_flag,del_Date) values('".$proc_last_id."','".$i_shiftID."','".$i_AgentID."','".$d_startTime."','".$d_endTime."','".$v_breakList."','".$shift_flag."','".$skill_flag."','".$d_delDate."');";
               mysqli_query($link,$sql_sch_ass_ins);
                  
              }
              //get details of agent schedule instance
              $sql_sch_ass_inst = "select * from $db.tbl_wfm_agent_sched_instance";
              $fetch_sch_ass_inst = mysqli_query($link,$sql_sch_ass_inst) or die("instance table".mysql_error());
              while($row_sch_ass_inst = mysqli_fetch_array($fetch_sch_ass_inst))
              { 
                  
                  $i_agentID            = $row_sch_ass_inst['i_agentID'];
                  $i_procSchedID        = $row_sch_ass_inst['i_procSchedID'];
                  $i_shiftID            = $row_sch_ass_inst['i_shiftID'];
                  $d_schedStartDate     = $row_sch_ass_inst['d_schedStartDate'];
                  $d_schedEndDate       = $row_sch_ass_inst['d_schedEndDate'];
                  $d_actualStartTime    = $row_sch_ass_inst['d_actualStartTime'];
                  $d_actualEndTime      = $row_sch_ass_inst['d_actualEndTime'];
                  $v_breakList          = $row_sch_ass_inst['v_breakList'];
                  $i_status             = $row_sch_ass_inst['i_status'];
                  $v_remarks            = $row_sch_ass_inst['v_remarks'];
                  $substitute_id        = $row_sch_ass_inst['substitute_id'];
                  $i_login_flag         = $row_sch_ass_inst['i_login_flag'];
                  $i_logout_flag        = $row_sch_ass_inst['i_logout_flag'];
                  $i_adhere             = $row_sch_ass_inst['i_adhere'];

                  //insert break details in history table
                $sql_sch_ass_inst_ins = "insert into $db.tbl_wfm_agent_sched_instance_hist(i_agentID,i_procSchedID,i_shiftID, d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,v_breakList,i_status,v_remarks,substitute_id,i_login_flag,i_logout_flag,i_adhere,del_Date) values('".$i_agentID."','".$proc_last_id."','".$i_shiftID."','".$d_schedStartDate."','".$d_schedEndDate."','".$d_actualStartTime."','".$d_actualEndTime."','".$v_breakList."','".$i_status."','".$v_remarks."','".$substitute_id."','".$i_login_flag."','".$i_logout_flag."','".$i_adhere."','".$d_delDate."');";
               mysqli_query($link,$sql_sch_ass_inst_ins);
               // exit;
                  
              }//while end
      }//if end
      
  }// while end
  $sql_delete_sched="DELETE from $db.tbl_wfm_proc_schedule"; //delete record then insert new update time
  mysqli_query($link,$sql_delete_sched);

  $sql_delete_sched_list="DELETE from $db.tbl_wfm_proc_schedule_list"; //delete record then insert new update time
  mysqli_query($link,$sql_delete_sched_list);

  $sql_delete_assign="DELETE from $db.tbl_wfm_agent_sched_assignment";  //only delete record not insert and update 
  mysqli_query($link,$sql_delete_assign);

  $sql_delete_instance="DELETE from $db.tbl_wfm_agent_sched_instance"; //only delete record not insert and update 
  mysqli_query($link,$sql_delete_instance);

  $sql_delete_break="DELETE from $db.tbl_wfm_break_instance"; //only delete and select code added not added insert code
  mysqli_query($link,$sql_delete_break);

  $sql_reset_instance="ALTER TABLE $db.tbl_wfm_proc_schedule AUTO_INCREMENT = 1";
  mysqli_query($link,$sql_reset_instance);
  $sch_name   = $_POST["scheduleName"];
  $no_shift   = $_POST["numberOfShifts"];
  
  $shifts     = $_POST["shifts"];    // Expecting an array like ['Shift1', 'Shift2', 'Shift3']
  $agents     = $_POST["agents"];    // Expecting an array like [10, 12, 15]
  $breaks     = $_POST["breaks"];    // Expecting an array like ['Break1', 'Break2', 'Break3']
  $from_date = date("Y-m-d", strtotime($_POST["fromDate"]));
  $to_date   = date("Y-m-d", strtotime($_POST["toDate"]));

  // Assigning individual shift/agent/break values if needed
  $shift_1 = isset($shifts[0]) ? $shifts[0] : null;
  $shift_2 = isset($shifts[1]) ? $shifts[1] : null;
  $shift_3 = isset($shifts[2]) ? $shifts[2] : null;
  
  $no_agent1 = isset($agents[0]) ? $agents[0] : 0;
  $no_agent2 = isset($agents[1]) ? $agents[1] : 0;
  $no_agent3 = isset($agents[2]) ? $agents[2] : 0;
  
  $break_1 = isset($breaks[0]) ? $breaks[0] : null;
  $break_2 = isset($breaks[1]) ? $breaks[1] : null;
  $break_3 = isset($breaks[2]) ? $breaks[2] : null;
  
  $break_list1="";$break_list2="";$break_list3="";

  $shifts_list=$shift_1.",".$shift_2.",".$shift_3.",";
  $agent_list=$no_agent1.",".$no_agent2.",".$no_agent3.",";
  //echo $shifts_list;
  $sql_insert="insert into $db.tbl_wfm_proc_schedule(v_schedName,i_noOfShift,v_shiftList,d_startDate,d_endDate) values('$sch_name','$no_shift','$shifts_list','$from_date','$to_date')";

  add_audit_log($vuserid, 'add_schedule', '', 'Schedule Created: ' . $sch_name, $db);
    
      mysqli_query($link,$sql_insert);
      $last_id=mysqli_insert_id($link);

      for($b1=0;$b1 < count($break_1);$b1++)
      {
          $break_list1.=$break_1[$b1].",";
      }
      for($b2=0;$b2 < count($break_2);$b2++)
      {
          $break_list2.=$break_2[$b2].",";
      }
      for($b3=0;$b3 < count($break_3);$b3++)
      {
          $break_list3.=$break_3[$b3].",";
      }
      //echo "<br>".$break_list1.":".$break_list2.":".$break_list3;
      
      $shift_list_val=explode(",",$shifts_list);
      $agent_list_val=explode(",",$agent_list);
      //echo count($shift_list_val);
      if($last_id!=""){
          for($shift_i=0;$shift_i < count($shift_list_val);$shift_i++){
              if($shift_list_val[$shift_i]!="")
              {
                  $breaklist="";
                  if($shift_i==0)
                  {
                      $breaklist= $break_list1;
                  }
                  else if($shift_i==1)
                  {
                      $breaklist= $break_list2;
                  }
                  else if($shift_i==2)
                  {
                      $breaklist= $break_list3;
                  }
                  
                  $shift_id=$shift_list_val[$shift_i];
                  $agent_id=$agent_list_val[$shift_i];
                  $sql_insert_schlist="insert into $db.tbl_wfm_proc_schedule_list(i_procSchedID,i_shiftID,i_skillID,i_noOfAgents, v_breakid) values ('$last_id','$shift_id','1','$agent_id','$breaklist');";
                  // echo "<br>".$sql_insert_schlist;
                  mysqli_query($link,$sql_insert_schlist);
                  echo "<script>$('.button').css('display','none');</script>";
              }

          }
      }
  }
?>
