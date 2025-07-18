<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
$name = $_SESSION['logged'];
$db = strtolower($db);

$startdatetime = !empty($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : date("d-m-Y 00:00:00");
?>
<form name="myform" method="post" id="schedule_hist_form">
    <div>
        <div class="table">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <tr class="background2">
                        <th height="44" colspan="2" align="left">Schedule Adherence Report</th>
                    </tr>
                    <tr>
                        <td colspan="2" class="left boder0-left">
                            <span class="left boder0-right">
                                Select Date 
                                <input type="text" name="startdatetime" class="dob1 date_class" value="<?= $startdatetime ?>" id="startdatetime">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label></label>
                            <span class="left boder0-left">
                                <input type="submit" name="sub1" value="Run Report" class="button-orange1" onclick="dosubmit_schedule_hist(1)">
                                <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_schedule_hist(2);">
                                <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_schedule_hist(3);">
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
		<?php
			
			$startdatetime = '';
			if(!empty($_REQUEST['sttartdatetime'])){ 
					$sttartdatetime1=explode(" ",$_REQUEST['sttartdatetime']);
					$startdatetime=$sttartdatetime1[0]; 
			}else{  
					// $startdatetime=date('Y-m-d');
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
			$qq = "select * from $db.tbl_wfm_agent_sched_instance_hist  where 1=1 $datefilter ";
			$ticket_query = mysqli_query($link,$qq);
			$total=mysqli_num_rows(mysqli_query($link,$qq));
			?>
        
        <!-- Start Display the filter data -->
        <div id="display-error" style="margin-top:5px;">Total Records Found - <?= $total ?> </div>
        <div class="table">
            <div class="wrapper1">
                <div class="div1" style="width:1800px;"></div>
            </div>
            <div class="wrapper2">
                <div class="div2" style="width:1800px;">
                    <table class="tableview tableview-2" id="schedule_report_hist">
                        <thead>
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
                                <td width="2%" align="center">Deleted on</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Display the filter data -->
        <span style="display:block; text-align:right;">
            <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_schedule_hist(2);">
            <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_schedule_hist(3);">
        </span>
    </div>
    <!-- End Right panel -->
</form>

<script type="text/javascript">
$(document).ready(function() {
    // Add any JavaScript initialization here
});
</script>
