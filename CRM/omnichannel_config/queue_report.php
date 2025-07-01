<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 24 October 2024
 * Description : Shows the data of queue sms  report
 */
/* check license access or not for  this module*/ 
/***END***/
include("../../config/web_mysqlconnect.php");
?>
<style type="text/css">
    .popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.popup-content {
        background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 377px;
    text-align: center;
    /* float: inline-end; */
    align-items: center;
    margin-left: 50%;
    margin-top: 73px;
}
}

.popup-content h3 {
    margin: 0;
    margin-bottom: 10px;
}

.popup-content textarea {
    width: 100%;
    height: 70px;
    margin-bottom: 15px;
    padding: 5px;
    resize: none;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.popup-actions {
    display: flex;
    justify-content: space-between;
}

.confirm-btn,
.cancel-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.confirm-btn {
    background-color: #4caf50;
    color: white;
}

.cancel-btn {
    background-color: #f44336;
    color: white;
}

.confirm-btn:hover {
    background-color: #45a049;
}

.cancel-btn:hover {
    background-color: #d32f2f;
}
.badge-success {
    background-color: #28a745;
    color: white;
    font-weight: bold;
}

.badge-warning {
    background-color: #ffcc00;
    color: black;
    font-weight: bold;
}
</style>
<div class="col-sm-10 mt-3" style="padding-left:0">
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
		<div class="row">
           	<div class="col-sm-12">
             	<div class="row">
               		<div class="col-sm-3">
                   		<div class="row">
                      		<div class="col-sm-12 facebook_titile">SMS  Report</div>
						</div>
               		</div>
             	</div>
           	</div>
        </div>
	</span>
	<form method="post" name="cfrm" id="SMS_report_form">
		<table class="tableview tableview-2 main-form new-customer">
			<tr >
				<td width="95" class="left boder0-right">&nbsp;
					<label>Start Date </label>
				</td>
				<td width="226" class="left boder0-right">
					<?php
						$startdate = ($_REQUEST['startdatetime']!='') ? $_REQUEST['startdatetime'] : date("01-m-Y 00:00:00");
						$enddate = ($_REQUEST['enddatetime']!='') ? $_REQUEST['enddatetime'] : date("d-m-Y 23:59:59");
					?>
					<span class="left boder0-left">
						<input type="text" name="startdatetime" class="date_class dob1"  value="<? if(!isset($_POST['startdatetime'])) echo date('01-m-Y 00:00:00'); else echo $_POST['startdatetime']; ?>" id="startdatetime">&nbsp;
					</span>
				</td>
				<td width="112" class="left boder0-right"><label>End Date </label></td>
				<td width="230" align="left" class="left boder0-right">
					<span class="left boder0-left">
						<input type="text" name="enddatetime" class="date_class dob1"   value="<? if(!isset($_POST['enddatetime'])) echo date('d-m-Y 23:59:59'); else echo $_POST['enddatetime']; ?>" id="enddatetime">
					</span>
				</td>
                <td width="112" class="left boder0-right"><label>Status</label>
			</td>
			<td width="230" align="left" class="left boder0-right">
				<span class="left boder0-left">			
                <select name="status" id="status" class="select-styl1" style="width:180px">
                    <option value="" <?= isset($_POST['status']) && $_POST['status'] === "" ? "selected" : "" ?>>Select Status</option>
                    <option value="0" <?= isset($_POST['status']) && $_POST['status'] === "0" ? "selected" : "" ?>>In Queue</option>
                    <option value="2" <?= isset($_POST['status']) && $_POST['status'] === "2" ? "selected" : "" ?>>Delivered</option>
                    <option value="1" <?= isset($_POST['status']) && $_POST['status'] === "1" ? "selected" : "" ?>>Submitted</option>
                    <option value="3" <?= isset($_POST['status']) && $_POST['status'] === "3" ? "selected" : "" ?>>Not Delivered</option>
                    <option value="4" <?= isset($_POST['status']) && $_POST['status'] === "4" ? "selected" : "" ?>>Expire</option>
                    <option value="5" <?= isset($_POST['status']) && $_POST['status'] === "5" ? "selected" : "" ?>>Rescheduling</option>
                </select>				
				</span>
			</td>
            
            </tr>
            <tr>
            <td width="112" class="left boder0-right"><label>Send To</label></td>
			<td width="150" align="left" class="left boder0-right">
				<span class="left boder0-left">
					<?php 
					$query = mysqli_query($link, "SELECT DISTINCT(send_to) FROM $db.sms_out_queue;");
					$selectedRecipient = isset($_POST['send_to']) ? $_POST['send_to'] : '';
					?>
					<input type="text" id="searchBox" placeholder="Search ID" onkeyup="filterFunction('send_to', 'searchBox', 'noMobileNO')" value="<?= $selectedRecipient; ?>" style="width:150px; height:30px; margin-bottom: 5px;">
					<div id="dropdownContainer" class="dropdown-content" style="width:150px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
						<select name="v_mobileNo" id="v_mobileNo" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0;" onchange="selectOption('v_mobileNo', 'searchBox')">
							<option value="0">Select</option>
							<?php while ($row = mysqli_fetch_assoc($query)) {
								$name = $row['send_to'];
								$selected = ($name == $selectedRecipient) ? "selected" : "";
							?>
								<option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
							<?php } ?>
						</select>
					</div>
					<div id="noMobileNO" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
				</span>
			</td>
            <td>
                <a href="javascript:void(0);" id="bulk-reschedule" style="font-size:24px;" title="Bulk Reschedule">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" id="expire_selected" style="font-size:24px;" title="Bulk Expire">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            </td>
			<td class="left  boder0-left" colspan="3">
				<input type="submit" name="sub1" value="Run Report" class="button-orange1 set_button">
				<?php $queue_report = base64_encode('queue_report');?>
                <input type="button" 
                    class="button-orange1" 
                    value="Reset" 
                    onclick="window.location.href='omni_channel.php?token=<?php echo $queue_report; ?>';" 
                    style="float:inherit; color:#222; text-decoration:none; " />
			</td>
			</tr>
		</table>
		<table  class="tableview tableview-2 " id="SMS_report">
			<thead>
			<tr class="background">
                <td align="center" valign="middle" width="2%" style="text-align: center;">
                    <input type="checkbox" id="select-all" />
                </td>
    			<td align="center" valign="middle" width = "2%"style="text-align: center;"> S.No</td>
    			<td align="center" valign="middle" width ="8%" >Send To</td>
                <td align="center" valign="middle" width ="8%" >Send From</td>
    			<td align="center" valign="middle" width ="25%">Message Data</td>
    			<td align="center" valign="middle" width ="8%">Status</td>
    			<td align="center" valign="middle" width ="9%">Status Response</td>
    			<td align="center" valign="middle" width ="8%">Message Date</td>
                <td align="center" valign="middle" width ="8%">Schedule Date</td>
                <td align="center" valign="middle" width="8%">Reschedule Status</td> <!-- New Column -->
			</tr>
		</thead>
		</table>
	</form>
</div>
<!-- Rescheduling Popup -->
<div id="reschedule-popup" class="popup-overlay" data-id="">
    <div class="popup-content">
        <input type="hidden" id="bulk-data-ids" />
        <h3>Reschedule SMS</h3>
        <p>Please provide the new rescheduling date:</p>
        <label for="reschedule-date">New Date:</label>
        <input type="text" id="reschedule-date" name="rescheduledate" class="date_class dob1"  value="<?echo date('01-m-Y 00:00:00'); ?>">
        <!-- <label for="reschedule-remark">Remark:</label> -->
        <!-- <textarea id="reschedule-remark" placeholder="Enter your remark here..."></textarea> -->
        <div class="popup-actions">
           <button id="confirm-reschedule" class="confirm-btn">Reschedule</button>
            <button id="cancel-reschedule" class="cancel-btn">Cancel</button>
        </div>
    </div>
</div>
<!-- JAVASCRIPT FILE -->
<script src="<?=$SiteURL?>public/js/sms_queue.js"></script>