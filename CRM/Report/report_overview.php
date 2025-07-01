<?php
/***
 * Report Overview page
 * Author: Ritu modi
 * Date: 25-01-2024
 * 
 Description: This page is used to display an overview of case data.
				  Users can select a date range to view case data within that range.
				  After selecting the date range, users can run the report to view the case overview.
				  The report includes details such as case type, status, and count.
**/
// made change to the  startdate and enddate [vastvikta][17-03-2025]
$dateRange = get_date_overview();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
    ? $_REQUEST['sttartdatetime'] 
    : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
    ? $_REQUEST['enddatetime'] 
    : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

?>
<form name="myform" method="post">
	<!-- Start of CASE OVERVIEW section -->
   <div class="style2-table mt-3" >
   	<div class="table">
		   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">CASE COUNT REPORT</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
					   <tr>
					   	<!-- Column for selecting date range -->
						   <td class="left boder0-right">
							<?php
							?>
							<label>From
							<input type="text" name="sttartdatetime" class="date_class dob1" value="<?php echo $startdatetime?>" id="sttartdatetime"></label>
							<label>To
							<input type="text" name="enddatetime" class="date_class dob1" value="<?php echo $enddatetime?>" id="enddatetime"></label>
							</td>
							<!-- added status filter [vastvikta][16-05-2025] -->
							<td class="left  boder0-right">
								<label>Status</label>
									<div class="log-case">
										<select name="status" id="status" class="select-styl1" style="width:190px">
											<option value="">Select Status</option>
											<?php
												$qry = get_status();
												while($res = mysqli_fetch_row($qry)){
												?>
											<option value="<?=$res[0]?>" <? if($_REQUEST['status']==$res[0]){ echo "selected"; } ?>><?=$res[1]?></option>
											<?php
											}
											?>
										</select>
									</div>
								</td>
							<td colspan="2" class="left boder0-right">
			                 	<input name="submit_overview" id="submit_overview" type="button" value="Run Report" class="button-orange1">
			                  	<input name="reset_overview" id="reset_overview" type="button" value="RESET" class="button-orange1">
							</td>
						</tr>
					</tbody>
				</table>
		</div>
         <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="overview_data">
                <thead>
					<tr class="" style="font-size: 12px">
					   <th >S.No.</th>
					   <th >Channels Type </th>
					   <th >Status</th>
					   <th >Count</th>
					</tr>
				   </thead>
				</table>
			   </div>
			</div>
		</div>
	</div>
</form>