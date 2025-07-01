<?php
/***
 * Case Report Page
 * Author: Aarti
 * 
 * This form allows users to generate a case report based on various filters such as customer name, case number, agent name, date range, ticket status, department, category, subcategory, county, sub-county, complaint type, complaint origin, reasons for calling, priority, and customer type. The form submits the data via POST method to the server for processing.
**/
?>

<style>/* Add styles for the custom scrollbar */
.scroll-wrapper {
    position: relative;
    width: 100%;
    height: 12px; /* height of the custom scrollbar */
    background-color: #fff; /* Background of the custom scrollbar */
    overflow: hidden;
    margin-bottom: 10px; /* Space between the table and scrollbar */
}

#bar {
    position: absolute;
    top: 0;
    left: 0;
    height: 60%;
	background-color: rgba(128, 128, 128, 0.9);/* Color of the custom scrollbar */
    cursor: pointer;
	border-radius: 3px ;
}

.table-container {
    overflow-x: auto; /* Enable horizontal scrolling on the table */
    max-width: 100%;
}

.table-wrapper {
    overflow-x: auto;
    width: 100%;
}

td.details-control {
    background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
}
/* 
.rightpanels{
	width: 990px;
	float: right;
} */

</style>
<form name="myform" method="post">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Cases</span>
	<div class="table">
		<table class="tableview tableview-2 main-form new-customer">
			<tbody>	
				<tr>
					<td class="left boder0-right">
						<label>Customer Name</label>
						<div class="log-case">
							<input type="text" id="fname" class="select-styl1" style="width:190px" name="fname" value="<?=$fname?>" onkeypress="return isAlphabetKey(event)">
						</div>
					</td>
					<td width="50%" class="left  boder0-right">
						<label>Case Number</label>
						<div class="log-case">
							<input type="text" class="select-styl1" id="casee" name="case" oninput="validateNumericInput(this)" value="<?=$case?>">
						</div>
					</td>
				</tr>
				<tr>
					<td class="left boder0-right">
						<label>Agent Name</label>
						<div class="log-case">
							<?php
								$result=uniuserprofile();
							?>
							<select name="agent" id="agent" class="select-styl1" style="width:190px">
							<option value="">Select Agent</option>
							<?php
							while($row=mysqli_fetch_array($result)) {
								$AtxUserID=$row['AtxUserID'];	
								$AtxUserName=$row['AtxUserName'];
								if($AtxUserID == $agent){
									$sel = 'selected';
								}else{
									$sel = '';
								}
							?>
							<option value='<?=$AtxUserID?>' <?=$sel?>><?=$AtxUserName?></option>
							<?php } ?>
							</select>
						</div>
					</td>
					<td width="50%" class="left  boder0-right">
						<?php

						 // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
						 $dateRange = get_date_cases();

						 $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
							 ? $_REQUEST['sttartdatetime'] 
							 : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));
 
						 $enddatetime = (!empty($_REQUEST['enddatetime'])) 
							 ? $_REQUEST['enddatetime'] 
							 : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
						?>
						<label>From
						<input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
						<label>To
						<input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
					</td>
				</tr>

				<tr>
					<td width="50%" class="left  boder0-right">
						<label>Case Status</label>
						<div class="log-case">
							<select name="status" id="status" class="select-styl1" style="width:190px" onchange="if(this.value==3){ $('#closing_remarks_col').css('display','table-row'); }else{ $('#closing_remarks_col').css('display','none'); }">
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
					<td width="50%" class="left  boder0-right">
						<?php $project_query = get_projects();?>
						<label>Department</label>
						<select name="vProject" id="vProject" value="<?=$vProject?>" class="select-styl1" style="width:190px">
							<option value="" >Select Department</option>
							<?php while($project_res = mysqli_fetch_array($project_query)){?>
							<option value="<?=$project_res['pId']?>" <? if($project_res['pId']==$vProject){ echo "selected"; } ?>>
							<?=$project_res['vProjectName']?>
							</option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="left  boder0-right">
						<div class="log-case" id="Enq_pr" style="display: block;">
							<label>Category</label>
							<select name="category" id="category" class="select-styl1" style="width:190px;">
							<option value="">Select Category</option>
							<?php
							
								$sourceresult = get_category();
								while($row=mysqli_fetch_array($sourceresult)) {
									$SubI=$row['id'];	
									$subC=$row['category'];

								if($SubI == $category){
								  $sel = 'selected';
								}else{
								   $sel = '';
								}?>
							<option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
							<?php } ?>
							</select>
						</div>
					</td>
					<td class="left  boder0-right">
						<label>Sub Category</label>
						<select name="subcategory" id="subcategory" class="select-styl1" style="width:190px;">
							<option value="" style="width:100px;">Select Sub Category</option>
							<?php
							$sourceresult = get_subcategory($category);
								while($subc_res = mysqli_fetch_array($sourceresult)){ ?>
									<option value="<?=$subc_res['id']?>" <?=( $subc_res['id'] == $subcategory) ? 'selected' : '' ?>><?=$subc_res['subcategory']?> </option>
									<?php }  ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="left  boder0-right">
				        <label>County</label>
						<select name="district" id="district" class="select-styl1" style="width:190px;">
							<option value="">Select County</option>
							<?php
								$sourceresult = get_city();
								while($row=mysqli_fetch_array($sourceresult)) {
									$SubI=$row['id'];	
									$subC=$row['city'];
									if($SubI == $district){
										$sel = 'selected';
									}else{
										$sel = '';
									}
								?>
							<option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
							<?php } ?>
						</select>
					</td>
					<td class="left  boder0-right">
			        <label>Sub County</label>
						<select name="village" id="village" class="select-styl1" style="width:190px;">
							<option value="">Select Sub County</option>
							<?php
							$villages_query = get_Village($district);
                                while($villages_res = mysqli_fetch_array($villages_query)){ ?>
                                 <option value="<?=$villages_res['id']?>" <?=( $villages_res['id'] == $village) ? 'selected' : '' ?>><?=$villages_res['vVillage']?> </option>
                                 <?php }  ?>
						</select>

					</td>
				</tr>
				<tr>
					<!-- <td class="left border0-left">
				   <label>Complaint Type</label>
				   <select name="comp" id="comp" class="select-styl1" style="width:180px">
						<option value="">Select Complaint Type</option>
					  <?php
					  	$comp_query = get_complaint_type();
					   	if (mysqli_num_rows($comp_query) > 0) {
						 while ($comp_res = mysqli_fetch_array($comp_query)){ ?>
						<option value="<?= $comp_res['id'] ?>" <? if ($comp_res['id'] == $comp) {
							echo "selected";
							} ?>><?= $comp_res['category'] ?></option>
					  	<?php }
					  } ?>
				   </select>
				 </td>	 -->
				 <td class="left  boder0-right">
						<?php $sourceresult = get_source();?>
						<label>Complaint Origin</label>
						<select name="source" id="source" class="select-styl1" style="width:190px">
							<option value="">Select Complaint Origin</option>
							<?php
							while($row=mysqli_fetch_array($sourceresult)) { ?>
							<option value='<?=$row['id']?>' <?php echo ($source==$row['id']) ? 'selected' : '' ;?> ><?=$row['source']?>  </option>
							<? } ?>
						</select>
					</td>	
					<td class="left  boder0-right">
						<label>Reason of Calling</label>
							<div class="log-case">
							<?php
							//added the code for handling case type[vastvikta][16-12-2024]
							$casetype = isset($_GET['casetype']) ? $_GET['casetype'] : ''; // Get casetype from the URL
							
							
							$complaint_sql = get_callingtype();
							while ($rows = mysqli_fetch_array($complaint_sql)) {  
							if($rows['slug']=='none')break;
							?>
							<span class="slug"> 
							<input type="radio" name="casetype" id="type<?=$rows['id']?>" value="<?=$rows['complaint_name']?>"  
							<?php if ($casetype==$rows['complaint_name']) {echo "checked";}?> > <?=$rows['complaint_name']?>
						    </span>
							<?php  }?>
							</div>

					</td>																	
				</tr>
				<tr>
					<td class="left  boder0-right">
						<div class="log-case">
							<label>Priority Customer <em>*</em></label>
								<span class="slug"> <input type="radio" id="priority" name="priority" value="1" <? if ($priority == '1') {
								echo "checked";
								} ?>> Priority </span>
								<span class="slug"> <input type="radio" id="priority" name="priority" value="0" <? if ($priority == '0') {
								echo "checked";
								} ?>> Non Priority</span>
						</div>
					</td>											
					<td width="50%" class="left  boder0-right">
						<label>Escalation Status</label>
						<div class="log-case">
							<select name="status" id="esc_status" class="select-styl1" style="width:190px"
								onchange="if(this.value==3){ $('#closing_remarks_col').css('display','table-row'); }else{ $('#closing_remarks_col').css('display','none'); }">
								
								<option value="" disabled selected>Select Escalation Status</option>

								<?php 
								$selectedStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
								?>
								<option value="1" >Escalation Level 1</option>
								<option value="2" >Escalation Level 2</option>
								<option value="3" >Escalation Level 3</option>
								<option value="4" <?= ($selectedStatus == 4 ? 'selected' : '') ?>>ALL</option>
							</select>
						</div>

					</td>
				<!--<tr>
					<td class="left  boder0-right">
						<label>Customer Type</label>
						<select name="customertype" id="customertype" class="select-styl1" style="width:190px">
							<option value="">Select Customer Type</option>
                <?php 
                $tbl_regional = get_Customertype();
                if (mysqli_num_rows($tbl_regional) > 0) {
                    while ($tbregional = mysqli_fetch_array($tbl_regional)) { ?>
                    <option value="<?= $tbregional['id'] ?>" <? if ($tbregional['name'] == $customertype) {
                        echo "selected";
                    } ?>><?= $tbregional['name'] ?> </option>
                <?php }
                } ?>
						</select>
					</td>
				</tr>  -->
				
				</tr>
				<tr>
  <td class="left boder0-right">
    <center>
      <!-- Existing Buttons -->
      <input name="submit" id="submit_casee" type="button" value="Run Report" class="button-orange1">
      <input name="reset" id="reset_casee" type="button" value="RESET" class="button-orange1">

      <!-- Export Button -->
      <input name="export" id="exportBtn" type="button" value="Export" class="button-orange1">
    </center>
  </td>
  <td class="left boder0-right"></td>
</tr>
			</tbody>
<!-- Modal Sidebar -->
<div id="exportSidebar" style="display:none;position:fixed; top:0; right:0; width:420px; height:100%; background:#f9f9f9; border-left:1px solid #ccc; box-shadow:-2px 0 5px rgba(0,0,0,0.2); padding:20px; z-index:1000; overflow:auto;">
  <h3>Export Options</h3>

  <!-- Format -->
  <label><strong>Format:</strong></label><br>
  <label><input type="radio" name="exportType" value="excel" checked> Excel</label><br>
  <label><input type="radio" name="exportType" value="csv"> CSV</label><br>
  <label><input type="radio" name="exportType" value="pdf"> PDF</label><br><br>

  <!-- Time Period as Radio Buttons -->
  <label><strong>Time Period:</strong></label><br>
  <label><input type="radio" name="timePeriod" value="today" checked onchange="toggleDateRange()"> Today</label><br>
  <label><input type="radio" name="timePeriod" value="yesterday" onchange="toggleDateRange()"> Yesterday</label><br>
  <label><input type="radio" name="timePeriod" value="this_month" onchange="toggleDateRange()"> This Month</label><br>
  <label><input type="radio" name="timePeriod" value="last_month" onchange="toggleDateRange()"> Last Month</label><br>
  <label><input type="radio" name="timePeriod" value="last_30_days" onchange="toggleDateRange()"> Last 30 Days</label><br>
  <label><input type="radio" name="timePeriod" value="custom" onchange="toggleDateRange()"> Pick Time Range</label><br><br>

  <!-- Date Range Inputs (Hidden by Default) -->
  <div id="dateRangeFields" style="display:none;width:380px;margin-bottom:30px;">
    <label>From
      <input type="text" name="sttartdatetimenew" class="date_class dob1" value="<?= $startdatetime ?>" id="sttartdatetimenew">
   To
      <input type="text" name="enddatetimenew" class="date_class dob1" value="<?= $enddatetime ?>" id="enddatetimenew">
    </label>
  </div>

  <!-- Column Selector -->
  <label><strong>Select Columns:</strong></label><br>
<div id="columnCheckboxes" style="border: 1px solid #ccc; padding: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
  <div style="flex: 1 1 45%;">
    <label><input type="checkbox" value="0"> Case Id.</label><br>
    <label><input type="checkbox" value="2"> Aged</label><br>
    <label><input type="checkbox" value="4"> Closed On</label><br>
    <label><input type="checkbox" value="6"> Complaint Origin</label><br>
    <label><input type="checkbox" value="8"> Agent</label><br>
    <label><input type="checkbox" value="10"> Sub Category</label><br>
    <label><input type="checkbox" value="12"> County</label><br>
    <label><input type="checkbox" value="14"> Priority/Non Priority</label><br>
  </div>

  <div style="flex: 1 1 45%;">
    <label><input type="checkbox" value="1"> Created On</label><br>
    <label><input type="checkbox" value="3"> In Progress</label><br>
    <label><input type="checkbox" value="5"> Status</label><br>
    <label><input type="checkbox" value="7"> Name</label><br>
    <label><input type="checkbox" value="9"> Category</label><br>
    <label><input type="checkbox" value="11"> Department</label><br>
    <label><input type="checkbox" value="13"> Sub County</label><br>
    <label><input type="checkbox" value="15"> Remark</label><br>
  </div>
</div>



  <!-- Buttons --><!-- Buttons (No onclick attribute required) -->
<input name="submit" id="export-users" type="button" value="Export" class="button-orange1">
      
  <button onclick="closeSidebar()" class="button-orange1" style="background:#ccc;">Cancel</button>
</div>


		</table>
	</div>
	
	<div id="totalRecordsTop" style=" margin-bottom:10px;height:30px;padding:6px;font-size: 12px;color:red;background-color:#FBE5E3;">
	<CENTER>
  TOTAL RECORDS FOUND- <span id="recordTopValue">0</span>
  </center>
</div>
    <!-- Scroll wrapper on top of the table -->
    <div class="scroll-wrapper" id="scroll-wrapper-top">
        <div id="bar"></div>
		
    </div>

    <div class="table-container" styele ="margin:200px;">
        <form name="frmService" method="post">
            <div class="table-wrapper">
                <!-- Table with overflow-x -->
                <table class="tableview tableview-2" id="casee_data">
                    <thead>
                        <tr style="font-size: 12px;">
							<th></th> <!-- For the expand/collapse icon -->
                            <th>Case Id.</th>
                            <th>Created On</th>
                            <th>Aged</th>
                            <th>In Progress</th>
                            <th>Closed On</th>
                            <th>Status</th>
                            <th>Complaint Origin</th>
                            <th>Name</th>
                            <th>Agent</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Department</th>
                            <th>County</th>
                            <th>Sub County</th>
                            <th>Priority/Non Priority</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows go here -->
                    </tbody>
                </table>
				<table class="tableview tableview-2" style="display:none;"id="case_hide_data">
                    <thead>
                        <tr style="font-size: 12px;">
							<th></th> <!-- For the expand/collapse icon -->
                            <th>Case Id.</th>
                            <th>Created On</th>
                            <th>Aged</th>
                            <th>In Progress</th>
                            <th>Closed On</th>
                            <th>Status</th>
                            <th>Complaint Origin</th>
                            <th>Name</th>
                            <th>Agent</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Department</th>
                            <th>County</th>
                            <th>Sub County</th>
                            <th>Priority/Non Priority</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows go here -->
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</form>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var tableWrapper = document.querySelector('.table-wrapper');
		var customScrollbar = document.querySelector('#scroll-wrapper-top');
		var bar = document.querySelector('#bar');
		var isDragging = false;
		var startX = 0;
		var scrollLeft = 0;

		// Function to update the position of the custom scrollbar
		function updateScrollbar() {
			var tableWidth = tableWrapper.scrollWidth; // Total width of the table
			var wrapperWidth = tableWrapper.offsetWidth; // Visible width of the wrapper
			var scrollLeft = tableWrapper.scrollLeft; // Scroll position of the table

			// Set the scrollbar width relative to the visible portion of the table
			var scrollbarWidth = (wrapperWidth / tableWidth) * 100; // Percentage width of scrollbar
			var scrollbarLeft = (scrollLeft / tableWidth) * 100; // Percentage of table scrolled

			// Set the width and position of the custom scrollbar
			bar.style.width = scrollbarWidth + '%';
			bar.style.left = scrollbarLeft + '%';
		}

		// Initial update to set correct scrollbar size when the page loads
		setTimeout(updateScrollbar, 100);  // Delay to ensure table is fully rendered

		// Update the scrollbar whenever the table is scrolled
		tableWrapper.addEventListener('scroll', updateScrollbar);

		// Mouse down event to start dragging
		bar.addEventListener('mousedown', function(e) {
			isDragging = true;
			startX = e.pageX - bar.offsetLeft;
			scrollLeft = tableWrapper.scrollLeft;
			customScrollbar.style.cursor = 'grabbing';
		});

		// Mouse move event to drag the scrollbar
		document.addEventListener('mousemove', function(e) {
			if (!isDragging) return;
			var x = e.pageX - startX;
			var scrollPercentage = (x / customScrollbar.offsetWidth) * 100;
			tableWrapper.scrollLeft = (scrollPercentage / 100) * tableWrapper.scrollWidth;
		});

		// Mouse up event to stop dragging
		document.addEventListener('mouseup', function() {
			isDragging = false;
			customScrollbar.style.cursor = 'pointer';
		});
	});document.getElementById('exportBtn').addEventListener('click', function () {
  document.getElementById('exportSidebar').style.display = 'block';
});
function toggleDateRange() {
        const selected = document.querySelector('input[name="timePeriod"]:checked').value;
        const rangeFields = document.getElementById('dateRangeFields');
        rangeFields.style.display = (selected === 'custom') ? 'block' : 'none';
      }
</script><script>
  // Utility to get date in dd-mm-yyyy format
  function formatDate(date) {
    const d = date.getDate().toString().padStart(2, '0');
    const m = (date.getMonth() + 1).toString().padStart(2, '0');
    const y = date.getFullYear();
    return `${d}-${m}-${y}`;
  }

  // Function to calculate start/end dates based on time period selection
  function getTimePeriodRange() {
    const period = document.querySelector('input[name="timePeriod"]:checked').value;
    const today = new Date();
    let startDate, endDate;

    switch (period) {
      case 'today':
        startDate = endDate = today;
        break;
      case 'yesterday':
        startDate = endDate = new Date(today.setDate(today.getDate() - 1));
        break;
      case 'this_month':
        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        break;
      case 'last_month':
        startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        endDate = new Date(today.getFullYear(), today.getMonth(), 0);
        break;
      case 'last_30_days':
        startDate = new Date(today.setDate(today.getDate() - 30));
        endDate = new Date();
        break;
      case 'custom':
        startDate = document.querySelector('input[name="sttartdatetimenew"]').value;
        endDate = document.querySelector('input[name="enddatetimenew"]').value;
        return { start: startDate, end: endDate }; // already formatted
    }

    return {
      start: `${formatDate(startDate)} 00:00:00`,
      end: `${formatDate(endDate)} 23:59:59`
    };
  }

  // Toggle custom date fields visibility
  function toggleDateRange() {
    const period = document.querySelector('input[name="timePeriod"]:checked').value;
    const rangeFields = document.getElementById("dateRangeFields");
    rangeFields.style.display = (period === 'custom') ? 'block' : 'none';
  }
document.getElementById("export-users").addEventListener("click", function () {
  const exportType = document.querySelector('input[name="exportType"]:checked').value;
  const { start, end } = getTimePeriodRange();

  // Collect selected column indexes
  const selectedColumns = Array.from(
    document.querySelectorAll('#columnCheckboxes input[type="checkbox"]:checked')
  ).map(cb => cb.value);

  // Collect form data
  const formData = {
    fname: $('#fname').val(),
    casee: $('#casee').val(),
    agent: $('#agent').val(),
    status: $('#status').val(),
    vProject: $('#vProject').val(),
    category: $('#category').val(),
    subcategory: $('#subcategory').val(),
    district: $('#district').val(),
    village: $('#village').val(),
    comp: $('#comp').val(),
    source: $('#source').val(),
    priority: $('input[name="priority"]:checked').val(),
    customertype: $('#customertype').val(),
    casetype: $('input[name="casetype"]:checked').val(),
    esc_status: $('#esc_status').val(),
    startdatetime: start,
    enddatetime: end,
    exportType: exportType,
    selectedColumns: selectedColumns,
    action: 'export_case_data'
  };

  $.ajax({
  url: 'Report/report_function.php',
  method: 'POST',
  data: formData,
  dataType: 'json',
  success: function (data) {
    console.log("Received data:", data);

    const $table = $('#case_hide_data');
    const $thead = $table.find('thead');
    const $tbody = $table.find('tbody');
    $thead.empty();
    $tbody.empty();

    // BUILD HEADERS BASED ON selectedColumns
    const headers = [];
    selectedColumns.forEach(col => {
      switch (col) {
		case '0':  headers.push('Case Id.'); break;
		case '1':  headers.push('Created On'); break;
		case '2':  headers.push('Aged'); break;
		case '3':  headers.push('In Progress'); break;
		case '4':  headers.push('Closed On'); break;
		case '5':  headers.push('Status'); break;
		case '6':  headers.push('Complaint Origin'); break;
		case '7':  headers.push('Name'); break;
		case '8':  headers.push('Agent'); break;
		case '9':  headers.push('Category'); break;
		case '10': headers.push('Sub Category'); break;
		case '11': headers.push('Department'); break;
		case '12': headers.push('County'); break;
		case '13': headers.push('Sub County'); break;
		case '14': headers.push('Priority/Non Priority'); break;
		case '15': headers.push('Remark'); break;
      }
    });

    const headerRow = $('<tr></tr>');
    headerRow.append('<th></th>'); // Expand column
    headers.forEach(h => headerRow.append(`<th>${h}</th>`));
    $thead.append(headerRow);

    // ADD ROWS
    data.forEach(row => {
      const tr = $('<tr></tr>');
      tr.append('<td></td>');
      row.forEach(cell => {
        tr.append(`<td>${cell}</td>`);
      });
      $tbody.append(tr);
    });

    $table.show();

    // Destroy old DataTable if any
    if ($.fn.DataTable.isDataTable('#case_hide_data')) {
      $('#case_hide_data').DataTable().clear().destroy();
    }

    // Initialize new DataTable
    const dt = $('#case_hide_data').DataTable({
      order: [],
      pageLength: 10,
      searching: false,
      paging: true,
      dom: "lBfrtip",
      buttons: [
        {
          extend: 'csvHtml5',
          text: 'CSV',
          titleAttr: 'CSV',
          filename: 'Case Report FROM ' + start + ' TO ' + end,
          title: 'Case Report FROM ' + start + ' TO ' + end,
          exportOptions: { columns: ':visible' }
        },
        {
          extend: 'excelHtml5',
          text: 'Excel',
          titleAttr: 'Excel',
          filename: 'Case Report FROM ' + start + ' TO ' + end,
          title: 'Case Report FROM ' + start + ' TO ' + end,
          exportOptions: { columns: ':visible' }
        },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          titleAttr: 'PDF',
          orientation: 'landscape',
          pageSize: 'A3',
          filename: 'Case Report FROM ' + start + ' TO ' + end,
          messageTop: 'Case Report FROM ' + start + ' TO ' + end,
          title: '.',
          exportOptions: { columns: ':visible' },
          customize: function (doc) {
            const logoBase64 = document.getElementById('pdf-logo-base64')?.innerText;
            if (logoBase64) {
              doc.images = doc.images || {};
              doc.images.logo = logoBase64;
              doc.content.splice(1, 0, {
                image: 'logo',
                width: 200,
                alignment: 'left',
                margin: [0, 0, 0, 10]
              });
            }

            doc.styles.tableHeader = {
              bold: true,
              fontSize: 10,
              color: 'black',
              fillColor: '#e0e0e0'
            };
          }
        }
      ]
    });

    // ✅ Trigger export
    setTimeout(() => {
      switch (exportType) {
        case 'csv':
          dt.button('.buttons-csv').trigger();
          break;
        case 'excel':
          dt.button('.buttons-excel').trigger();
          break;
        case 'pdf':
          dt.button('.buttons-pdf').trigger();
          break;
      }
    }, 500);
    setTimeout(() => {
	location.reload();},2000);
  },
  error: function () {
    alert('Error fetching export data');
  }
	});

});

</script>
