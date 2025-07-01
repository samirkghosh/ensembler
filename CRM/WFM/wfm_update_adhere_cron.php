<?

include("web_mysqlconnect.php");

      $todate=strtotime(date('Y-m-d'));
      $startdatetime=date('Y-m-d', strtotime('-1 day',$todate));
	//$startdatetime=date('Y-m-d');
      $dcolor=0;
      // to get all distinct agents assigned between dates
      $sqlsource="select distinct(i_agentID),i_procSchedID from dlight.tbl_wfm_agent_sched_instance where (d_schedStartDate>='$startdatetime 00:00:00' and d_schedEndDate<='$startdatetime 23:59:59')";    // query for getting agent between satisfied date
      
    $sourceresult=mysql_query($sqlsource);
    while($row=mysql_fetch_array($sourceresult)) 
    {
      $sched_id=$row['i_procSchedID'];
      $agent_id=$row["i_agentID"];
      // query for getting agent name
      $sqlsource_agent="select AtxUserID,AtxDisplayName from dlight.uniuserprofile where AtxDisplayName!='' and AtxUserID='$agent_id'";  
      $sourceresult_agent=mysql_query($sqlsource_agent);
      $row_agent=mysql_fetch_array($sourceresult_agent);
      
      $agent_name=$row_agent["AtxDisplayName"];
      $rval="";
 
      //************* margin for shift attended start time ******************
      $class_margin_break="";
      $sqlsource_st="select i_shiftID,d_schedStartDate,d_schedEndDate,d_actualStartTime,d_actualEndTime,i_status,i_logout_flag from dlight.tbl_wfm_agent_sched_instance where i_agentID='$agent_id' and i_procSchedID='$sched_id' and (d_actualStartTime like '$startdatetime%' or d_actualEndTime like '$startdatetime%')";
      $sourceresult_st=mysql_query($sqlsource_st);
      while($row_stime=mysql_fetch_array($sourceresult_st))
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
		      // $st_margin=shift_margin($s_time[0]);
		      // $st_min=floor(($s_time[1])/15);
		      // $st_margin+=$st_min;
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
		     	$adhere_instance="0";
		      ################# margin for shift attended width end ###############
		        if($leave!=1)
		        {
		          // if not logged-out atleast one time
		          if($row_stime['i_logout_flag']!=0 )
		           // if logged-out atleast one time
		          {
		            if($d_actualStartTime_sec<=$d_schedStartTime_sec && $d_schedEndTime_sec<=$d_actualEndTime_sec)
		            {
		                  $class_instance="class_instance_green";
		                  $adhere_instance="1";
		            }
		            else
		            {
		              $diff_start_time_sec=$d_actualStartTime_sec-$d_schedStartTime_sec;
		              $diff_start_time_mins=intval($diff_start_time_sec/60);

		              $diff_end_time_sec=$d_schedEndTime_sec-$d_actualEndTime_sec;
		              $diff_end_time_mins=intval($diff_end_time_sec/60);

		              if($diff_start_time_mins>=5 || $diff_end_time_mins>=5)
		              {
		                  $class_instance="class_instance_red";
		                  $adhere_instance="3";
		              }
		              else if(($diff_start_time_mins>=1 && $diff_start_time_mins<5) || ($diff_end_time_mins>=1 && $diff_end_time_mins<5))
		              {
		                  $class_instance="class_instance_orange";
		                  $adhere_instance="2";
		              }
		              else
		              {
		                  $class_instance="class_instance_green";
		                  $adhere_instance="1";
		              }
		            }
		          }
			   	}
			   	else
			   	{
			   		$class_instance="class_instance_grey";
			   		$adhere_instance="0";
			   	}
		      
		        ################# breaks for agents ############################
		     	// echo "<br>".$adhere_instance;
		     	$sqlsource_update="update dlight.tbl_wfm_agent_sched_instance set i_adhere='$adhere_instance' where i_agentID='$agent_id' and i_procSchedID='$sched_id' and i_shiftID='$shift_id' and (d_actualStartTime like '$startdatetime%' or d_actualEndTime like '$startdatetime%')";
		      	$sourceresult_update=mysql_query($sqlsource_update);


      }     //attended schedule while end
        
}			//distinct user while ends

// }      // if isset end
    ?>

