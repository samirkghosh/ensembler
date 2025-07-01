<?php
/**
* Auth: Ritu modi 
* Date- 20-03-24
* Description: This file displays the CSAT (Customer Satisfaction) and DSAT (Dissatisfaction) dashboard for agents. It allows users to view CSAT, DSAT, NPS (Net Promoter Score), and customer effort reports within a specified date range.
*/
include_once("../include/web_mysqlconnect.php"); //database connection 
function get_name_ID($value,$columnname,$tablename,$condcolu)
{
	//echo "SELECT $columnname from tbl_mst_disposition where V_DISPO='$value';";
	global $link;
	$r=mysqli_query($link,"SELECT $columnname from asterisk.$tablename where $condcolu='$value';");
	$res=mysqli_fetch_array($r);
	return ($res[$columnname] && mysqli_num_rows($r)>0) ? $res[$columnname] : "";
}
function getDashboard_data($campaign,$starttime,$endtime,$type,$extra){
	global $link;
		if($campaign){		
			$whr_camp=" AND campaign_id='$campaign'";		
		}		
		if($extra){
			$whr_agent=" AND agent='$extra'";	
		}
		$date_cond=" AND  entry_date>= '$starttime' and entry_date<= '$endtime' ";
		if($type=="TOTAL_CONNECTED"){
			 $date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";
			 $whr_agent_log =" AND list.agent='$extra'";
			$sql_total="SELECT  count(*) AS dataCount FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
				where list.agent != '' AND list.comments IS NOT NULL    $date_cond	$whr_agent_log  order by list_agent_log.agent_log_id desc";
		}
		if($type=="TOTALDISPO_CONNECTED"){
			 $date_cond=" AND  event_time>= '$starttime' and event_time<= '$endtime' ";
			  $sql_total="SELECT count(*) As dataCount FROM  asterisk.autodial_agent_log WHERE STATUS IN (SELECT  v_Desc FROM asterisk.tbl_mst_DispoSubType  WHERE  i_DispoType =1
					AND  i_Status =1) $date_cond  $whr_camp ";
		}
		if($type=="TOTAL_DISP_NOTCONNECTED"){
			 $date_cond=" AND  event_time>= '$starttime' and event_time<= '$endtime' ";
			  $sql_total="SELECT count(*) As dataCount FROM  asterisk.autodial_agent_log WHERE STATUS IN (SELECT  v_Desc FROM asterisk.tbl_mst_DispoSubType  WHERE  i_DispoType =0
					AND  i_Status =1) $whr_agent_log $whr_camp
			$date_cond   ";
		}
		if($type=="TOTALDISPO_NOTCONNECTED"){
			 $date_cond=" AND  event_time>= '$starttime' and event_time<= '$endtime' ";
			  $sql_total="SELECT count(*) As dataCount FROM  asterisk.autodial_agent_log WHERE STATUS IN (SELECT  v_Desc FROM asterisk.tbl_mst_DispoSubType  WHERE  i_DispoType =0
					AND  i_Status =1) $date_cond  $whr_camp  ";
		}
			
		//echo '<br>'. $sql_total;	
		$query=mysqli_query($link,$sql_total);
		$fetch_obj=mysqli_fetch_object($query);
		return   $fetch_obj->dataCount=($fetch_obj->dataCount)?$fetch_obj->dataCount:0; 
}


function getAverageTalktime($campaign,$starttime,$endtime,$type,$extra)
{
	global $link;
		if($campaign) 
		{
		
		$whr_camp=" AND campaign_id='$campaign'";
		
		}
		
		if($extra)
		{
			$whr_agent=" AND user='$extra'";
			$whr_agentlist="AND list.agent='$extra'";
		}

		$date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";
		
			
		
		if($type=="TALKTIME")
		{
			//  $sql_q=" SELECT SUM(ans_duration) AS avg_Talktime FROM autodial_agent_log where callid!='' AND user!='' $whr_camp $date_cond $whr_agent";
			$sql_q="SELECT SUM(ans_duration) AS avg_Talktime FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
             where list.agent != '' AND list.comments IS NOT NULL $date_cond  $whr_agentlist  order by list_agent_log.agent_log_id desc";

			$query=mysqli_query($link,$sql_q);
			$fetch_obj=mysqli_fetch_object($query);
			return  $fetch_obj->avg_Talktime=($fetch_obj->avg_Talktime)? round($fetch_obj->avg_Talktime,2):0;
		}
		
		if($type=="PER_TALKTIME")
		{
			
			 $sql_q="SELECT  (SUM(ans_duration)*100/COUNT(*)) AS per_Talktime   FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
             where list.agent != '' AND list.comments IS NOT NULL $date_cond  $whr_agentlist  order by list_agent_log.agent_log_id desc";

			$query=mysqli_query($link,$sql_q);
			$fetch_obj=mysqli_fetch_object($query);
			return  $fetch_obj->per_Talktime=($fetch_obj->per_Talktime)? round($fetch_obj->per_Talktime,2):0;
		}
		
		
		
		if($type=="TOTAL_MEANINGFULLCALLS") 
		{
			
			 //For meaningfull call counts only connected calls
			 $sql_q="SELECT count(*) AS dataCount FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
             where list_agent_log.ans_duration>=15  AND list.agent != '' AND list.comments IS NOT NULL $date_cond  $whr_agentlist  order by list_agent_log.agent_log_id desc";
			//echo "<br>".$sql_q;
			$query=mysqli_query($link,$sql_q);
			$fetch_obj=mysqli_fetch_object($query);
			return  $fetch_obj->dataCount;
		}

		if($type=='LONGEST_TALKTIME')
			{
				$sql_q="SELECT   list.agent as user,MAX(list_agent_log.ans_duration) AS talk_sec  FROM asterisk.autodial_list as list 
				inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id   
				 where list.agent != '' AND list.comments IS NOT NULL $date_cond  $whr_agentlist  order by list_agent_log.agent_log_id desc";
				//echo "<br>".$sql_q;
				$query=mysqli_query($link,$sql_q);
				$fetch_obj=mysqli_fetch_object($query);
				return $result=$fetch_obj->user."@".$fetch_obj->talk_sec;
			}
		
	

}



function get_Agent_CallStatus($scoretype,$campaign,$starttime,$endtime)
{
	global $link;

		if($campaign) 
		{
		
		$whr_camp=" AND campaign_id='$campaign'";
		
		}
		
		$date_cond=" AND  event_time>= '$starttime' and event_time<= '$endtime' ";
			
			if($scoretype=='HIGHEST_MEANINGFULLCALLS')
			{
				$sql_q=" SELECT user,MAX(ans_duration) AS talk_sec FROM asterisk.autodial_agent_log where callid!='' AND ans_duration>=15 $whr_camp $date_cond  ";
				//echo "<br>".$sql_q;
				$query=mysqli_query($link,$sql_q);
				$fetch_obj=mysqli_fetch_object($query);
				return $result=$fetch_obj->user."@".$fetch_obj->talk_sec;
			}
			
			
			

}

function get_liveAgentCallDuration($agentName)
{
	global $link;
	$sql_a="SELECT TIMEDIFF( now( ) , last_update_time ) AS CALL_TIME_DIFF FROM asterisk.autodial_live_agents where user='".$agentName."' ";
	$query=mysqli_query($link,$sql_a);
	$fetch_obj=mysqli_fetch_object($query);
	return $result=$fetch_obj->CALL_TIME_DIFF;
}


function OverAllDispostion($campaign,$starttime,$endtime,$type,$extra)
{
	global $link;

	if($campaign) 
		{
		
		$whr_camp=" AND campaign_id='$campaign'";
		
		}
		$var_overall_disp="";
		$date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";
		
		
		 
		$sql_q="SELECT count(*) AS cnt,list_agent_log.status FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
				where  list.agent != '' AND list.comments IS NOT NULL $whr_camp $date_cond  GROUP BY status";


			 $query_data=mysqli_query($link,$sql_q);
			 $row_cnt=mysqli_num_rows($query_data);
			 if($row_cnt)
			 {
					while($rowdisp=mysqli_fetch_array( $query_data))
					{
					 	$subdisp_name=$rowdisp['status'];
					 	$subdisp_cnt =$rowdisp['cnt'];
						 $var_overall_disp.="{ name: '".$subdisp_name."', y: ".$subdisp_cnt.",sliced: true,  selected: true },";
					
					}//END OF WHILE
					return  $var_overall_disp;
			}else{
			
					return  "NODATA";
			}
	
			
		
}

function get_NullSubDispostion($campaign,$starttime,$endtime,$type,$extra,$status)
{
	global $link;
	if($campaign) 
		{
		
		$whr_camp=" AND list_agent_log.campaign_id='$campaign'";
		
		}
		
		
		
		$whr_status=" AND list_agent_log.status IS NULL";
		
		$date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";
		
		if($extra)
		{
			$whr_agent=" AND list.agent='$extra'";
		}
		
				
			$q="SELECT count(*) AS totalSubDispo FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
			where  list.agent != '' AND list.comments IS NOT NULL $date_cond $whr_agent $whr_status $whr_camp
			order by list_agent_log.agent_log_id desc";

 			$sql_q=mysqli_query($link,$q);
		    $fetch_q=mysqli_fetch_array($sql_q);
			return $fetch_q['totalSubDispo'];
}
function getAgent_totalSubDisp($campaign,$starttime,$endtime,$type,$extra,$status)
{
	global $link;
		if($campaign) 
		{
		
		$whr_camp=" AND list_agent_log.campaign_id='$campaign'";
		
		}
		
		if($status) 
		{
		
		$whr_status=" AND list_agent_log.status='$status'";
		
		}

		$date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";
		
		if($extra)
		{
			$whr_agent=" AND list.agent='$extra'";
		}
		
			//echo "select count(*) AS totalSubDispo FROM  autodial_agent_log where callid!=''  $whr_camp $date_cond $whr_agent $whr_status";
			//$sql_q=mysqli_query($link,"select count(*) AS totalSubDispo FROM  autodial_agent_log where callid!=''  $whr_camp $date_cond $whr_agent $whr_status ");	
			$q="SELECT count(*) AS totalSubDispo FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
			where  list.agent != '' AND list.comments IS NOT NULL $date_cond $whr_agent $whr_status $whr_camp
			order by list_agent_log.agent_log_id desc";
			//echo $q;
 			$sql_q=mysqli_query($link,$q);
		    $fetch_q=mysqli_fetch_array($sql_q);
			return $fetch_q['totalSubDispo'];
		

}

function AgentwiseDispostions($campaign,$starttime,$endtime,$type,$extra)
{
	
	global $link;
	$array_agent=array();

    $orderby=" ORDER BY ans_duration DESC ";
    //$limitVal= "LIMIT 0,5";
    $limitVal= "";
	if($campaign) 
		{
		
		$whr_camp=" AND campaign_id='$campaign'";
		
		}

		$date_cond=" AND  event_time>= '$starttime' and event_time<= '$endtime' ";
		//echo "<br>select distinct user FROM  autodial_agent_log where callid!=''  $whr_camp $date_cond  $orderby $limitVal ";
		
		$sql_q=mysqli_query($link,"select distinct user FROM  asterisk.autodial_agent_log where callid!=''  $whr_camp $date_cond  $orderby $limitVal ");	
		while($fetch_q=mysqli_fetch_array($sql_q))
		{		
		     //echo '<br>Agent --'.$user_agent=$fetch_q['user'];
			 $array_agent[]=$fetch_q['user'];
		}
			
		return $array_agent;
}

function get_Callstatus($begin_date)
{
	global $link;
		/* incoming calls by hours */
		
		$hours_array=array();$stackedcolumnsdata="";
		for($i=0 ; $i<=23 ; $i++)
		{
			if($i<10){ $i="0".$i; }
			array_push($hours_array,$i);
		}
		//print_r($hours_array);
		foreach($hours_array as $hours)
		{
			   // echo "<br>select count(*) from autodial_agent_log where event_time like '%$begin_date $hours%' ";
				$q=mysqli_fetch_array(mysqli_query($link,"select count(*) from asterisk.autodial_agent_log where event_time like '%$begin_date $hours%' ;"));
				$answeredcalls=$q[0];
				$hours=($hours>12) ? ($hours-12)." pm" : $hours." am";
				$stackedcolumnsdata .='{  y: '.$answeredcalls.' , label: "'.$hours.'"},';
		}
		
		echo '<br>'.$stackedcolumnsdata;
}


function get_LiveAgentStatus($agent)
{
	global $link;
	$agent_array=array();
	if($agent!="") $whr_cond_usr=" AND user='$agent' ";
	
	   $sql_agent="select count(*) as c,user,status from asterisk.autodial_live_agents WHERE user!='' $whr_cond_usr GROUP BY status";
	 $query=mysqli_query($link,$sql_agent);
	 while($row=mysqli_fetch_array($query))
	 {
	 	//$agent_array['user']=$row['user'];$agent_array['cnt']=$row['c'];$agent_array['status']=$row['status'];
		$agent_array[$row['status']]=$row['c'];
	 }
	 
	 return $agent_array;
}

function getAgentLiveInfo($agent)
{
	global $link;
	 if($agent!="") $whr_cond_usr=" AND user='$agent' ";
 	 $sql_agent="select user,status,campaign_id from asterisk.autodial_live_agents WHERE user!='' $whr_cond_usr GROUP BY status";
	 $query=mysqli_query($link,$sql_agent);
	 $fetch_row=mysqli_fetch_array($query);
	 return  $fetch_row;
}


function getAgentlogINtime($agent)
{
	global $link;
	$date_today=date("Y-m-d");

	$sql_agentlog="SELECT * FROM  asterisk.agent_status_log WHERE  status ='LOG IN' AND agent_id =  '$agent' AND status_date_time LIKE  '$date_today%' LIMIT 0,1";
	$query=mysqli_query($link,$sql_agentlog);
	$fetch_row=mysqli_fetch_array($query);
	return  $fetch_row['status_date_time'];

}


function get_DispostionStatus($V_CampaignName,$start_date,$end_date,$type,$extra)
{
	global $link;
	$array_Dispo=array();
	$whr_datecond=" AND  event_time>= '$start_date' and event_time<= '$end_date' ";
	
	if($V_CampaignName!="") $whr_campaign=" AND campaign_id='$V_CampaignName'";
	
	 $sql_disp="SELECT distinct status FROM asterisk.autodial_agent_log where status!='' $whr_datecond  $whr_campaign";
	//echo "<br>".$sql_disp;
	$query=mysqli_query($link,$sql_disp);
	while($fetch_status=mysqli_fetch_array($query))
	{
		$array_Dispo[]=$fetch_status['status'];
	}
	return $array_Dispo;
}


function get_user_Licence_Count()
{
	global $link;
	$sql="SELECT i_count FROM  asterisk.tbl_licence where v_module='user' ";
	$query=mysqli_query($link,$sql);
	$rowf=mysqli_fetch_array($query);
	$i_count=$rowf['i_count'];
	//echo '<br>Decode user val::'. $str_decode_val_usr=(base64_decode(strrev($i_count)));
	return $userCount=base64_decode(strrev($i_count));

}

function get_agent_Licence_Count()
{
	global $link;
	$sql="SELECT i_count FROM  asterisk.tbl_licence where v_module='agent' ";
	$query=mysqli_query($link,$sql);
	$rowf=mysqli_fetch_array($query);
	$i_count=$rowf['i_count'];
	//echo '<br>Decode user val::'. $str_decode_val_usr=(base64_decode(strrev($i_count)));
	return $agentCount=base64_decode(strrev($i_count));

}

function  getUserfirstLoginTime($agentName)
{
	global $link;
	$startDate=date("Y-m-d")." 00:00:00";
	$endDate=date("Y-m-d")." 23:59:59";
	$whr_cond_a= " AND ( agent_id Like '%$agentName%' ) ";
	$date_cond= " AND status_date_time>= '$startDate' and status_date_time<= '$endDate' ";
	
	$sql="SELECT status_date_time from asterisk.agent_status_log where live_status_id!='' AND status='LOG IN' $whr_cond_a  $date_cond 
	limit 0,1";
	$rowf=mysqli_fetch_array(mysqli_query($link,$sql));
	$date_e=explode(" ",$rowf['status_date_time']);
	return ($date_e[1])?$date_e[1] : " ";

}


function CampaignList($campaign,$starttime,$endtime,$type,$extra)
{
	global $link;
		if($campaign) 
		{
		
			$whr_camp=" AND campaign_id='$campaign'";
		
		}

		$date_cond=" AND  list_agent_log.event_time>= '$starttime' and list_agent_log.event_time<= '$endtime' ";

		$sql_q="SELECT  count(*) AS dataCount,campaign_id FROM asterisk.autodial_list as list inner join asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id 
				where list.agent != '' AND list.comments IS NOT NULL    $date_cond	$whr_camp group by campaign_id order by list_agent_log.agent_log_id desc";

			 $query_data=mysqli_query($link,$sql_q);
			 $row_cnt=mysqli_num_rows($query_data);
			 $prefix = $campList = '';
			 if($row_cnt)
			 {
					while($rowc=mysqli_fetch_array( $query_data))
					{
						$campaignName=$rowc['campaign_id'];
						$campList .= $prefix . '"' . $campaignName. '"';
						$prefix = ', ';

					}

					return $campList ;
			}else{

				return "No Campaign" ;

			}



}


?>
