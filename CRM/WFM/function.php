<?php

include("../../config/web_mysqlconnect.php");
function get_callbk_date($lead_id,$columnname)
{
	//echo "SELECT $columnname from autodial_callbacks where lead_id='$lead_id';";
	$r=mysql_query("SELECT $columnname from autodial_callbacks where lead_id='$lead_id';") or die(mysql_error());
	$res=mysql_fetch_array($r);
	return ($res[$columnname] && mysql_num_rows($r)>0) ? $res[$columnname] : "";
}

//user level condtion  -changed By suma
function get_user_levelName($usrlevel_id)
{
	$r=mysql_query("SELECT username from tbl_user_rights where user='$usrlevel_id';") or die(mysql_error());
	$res=mysql_fetch_array($r);
	return ($res['username'] && mysql_num_rows($r)>0) ? $res['username'] : "";
}

function get_loginUserDetail($user,$pwd)
{
	$r=mysql_query("SELECT full_name,user_level,user_camp,closer_campaigns ,v_department  from asterisk.autodial_users  where user='$user' and pass='".$pwd."';") or die(mysql_error());
	$res=mysql_fetch_array($r);
	return $res;
}

function get_commasep_campaigns($user_campign)
{
	
	
    $camp_val=explode(",",$user_campign);
        
        if(count($camp_val)>1)
        {
        	for($k=0;$k<count($camp_val);$k++)
        	{
        		$camp_valc.= " '".$camp_val[$k]."'".",";
        	}
        	
        	
        	$user_campign=rtrim($camp_valc,',');
        }else{
        	$user_campign="'".$user_campign."'";
        }

       return $user_campign;
}

function get_assignedCampaign_Drop()
{
	/*TABLE-autodial_campaigns*/

	$user=$_SESSION['PHP_AUTH_USER'];
	$pwd =$_SESSION['PHP_AUTH_PW'];
	$userdetails=get_loginUserDetail($user,$pwd);

	$user_campign=$userdetails['user_camp'];
	$userlevel=$userdetails['user_level'];
    $camp_val=explode(",",$user_campign);
        
        if(count($camp_val)>1)
        {
        	for($k=0;$k<count($camp_val);$k++)
        	{
        		$camp_valc.= " '".$camp_val[$k]."'".",";
        	}
        	
        	
        	$user_campign=rtrim($camp_valc,',');
        }else{
        	$user_campign="'".$user_campign."'";
        }
      /*LEVEL-9 Is SUPER admin*/
		if($userlevel!=9 )
		{  
				return $where_cond_camp= " AND campaign_id IN ( $user_campign) ";
		}else{
			    return $where_cond_camp= "  ";
			
		}


}



function get_assignedUser_Drop()
{

	/*TABLE-autodial_users*/

	$user=$_SESSION['PHP_AUTH_USER'];
	$pwd =$_SESSION['PHP_AUTH_PW'];
	$userdetails=get_loginUserDetail($user,$pwd);
	$userlevel=$userdetails['user_level'];
    $v_department=$userdetails['v_department'];//user level condtion  -changed By suma
    $where_user_assignCond=" WHERE i_status=1 AND 	active_status=1 ";
    if($userlevel!=9 )
	{
					//If Department is assigned to the user then select all user which is lower to the level of the user that logined
					if($v_department!='') $whr_dep_cond=" AND v_department='".$v_department."' " ;
					$whr_level_cond="  AND user_level	<='".$userlevel."' AND user_level	!=9	";
				 	$where_user_assignCond.=" $whr_dep_cond $whr_level_cond ";
	}else{
					$where_user_assignCond=$where_user_assignCond;
	}

	return $where_user_assignCond;

}



//user level condtion  -changed By suma
//function from agent_shift.php

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
	return $shift_margin;
}

function get_Agent_Assigned_For_Schedule($i_procSchedID,$i_shiftID){
	global $db,$link;
  
	$sql_agent_assignment   ="SELECT count(*) AS agent_assign_count from $db.tbl_wfm_agent_sched_assignment WHERE i_procSchedID='".$i_procSchedID."' AND i_shiftID='".$i_shiftID."' ";
	$q_agent_assigned    =mysqli_query($link,$sql_agent_assignment);
	$fetch_agent_assign    =mysqli_fetch_array($q_agent_assigned);
	return $fetch_agent_assign['agent_assign_count'];
}
?>

