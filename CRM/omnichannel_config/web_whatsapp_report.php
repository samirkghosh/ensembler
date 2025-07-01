<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 24 October 2024
 */
/* check license access or not for  this module*/ 
include_once("../../ensembler/function/classify_function.php"); 
/***END***/
include("../../config/web_mysqlconnect.php");

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_whatsapp_report();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

$I_Status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
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
                      		<div class="col-sm-12 facebook_titile">Whatsapp Queue Report</div>
						</div>
               		</div>
             	</div>
           	</div>
        </div>
	</span>
	<form method="post" name="cfrm" id="whatsapp_report_form">
		<table class="tableview tableview-2 main-form new-customer">
			<tr >
				<td width="95" class="left boder0-right">&nbsp;
					<label>Start Date </label>
				</td>
				<td width="226" class="left boder0-right">
					<span class="left boder0-left">
						<input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="startdatetime">&nbsp;
					</span>
				</td>
				<td width="112" class="left boder0-right"><label>End Date </label></td>
				<td width="230" align="left" class="left boder0-right">
					<span class="left boder0-left">
						<input type="text" name="enddatetime" class="date_class dob1"   value="<?=$enddatetime?>" id="enddatetime">
					</span>
				</td>
				<td width="112" class="left boder0-right"><label>Status</label>
			</td>
			<td width="230" align="left" class="left boder0-right">
				<span class="left boder0-left">
				
				<select name="status" id="status" class="select-styl1" style="width:180px">
				<option value="" Disabled selected >Select Status</option>
				<option value="0"  <? if($I_Status==1) echo "selected";?>>In Queue</option>
                <option value="1"  <? if($I_Status==2) echo "selected";?> >Delivered</option>
                <option value="2" <?= isset($I_Status) && $I_Status === "3" ? "selected" : "" ?>>Not Delivered</option>
                <option value="3" <?= isset($I_Status) && $I_Status === "4" ? "selected" : "" ?>>Expire</option>
                <option value="4" <?= isset($I_Status) && $I_Status === "5" ? "selected" : "" ?>>Rescheduling</option>
				</select>
				
				</span>
			</td>
				
			</tr>
			<tr>
                <td width="112" class="left boder0-right"><label>Sent To</label></td>
                <td width="150" align="left" class="left boder0-right">
                    <span class="left boder0-left">
                        <?php 
                        $query = mysqli_query($link, "SELECT DISTINCT(send_to) FROM $db.whatsapp_out_queue;");
                        ?>
                        
                        <input type="text" id="searchBox" placeholder="Search Phone Number." onkeyup="filterFunction('send_to', 'searchBox', 'noPhoneNumber')" style="width:150px; height:30px; margin-bottom: 5px;">
                        
                        <div id="dropdownContainer" class="dropdown-content" style="width:150px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
                            <select name="send_to" id="send_to" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0;" onchange="selectOption('send_to', 'searchBox')">
                                <option value="0">Select</option>
                                <?php while ($row = mysqli_fetch_assoc($query)) {
                                    $name = $row['send_to'];
                                    $selected = ($name == $_POST['send_to']) ? "selected" : "";
                                ?>
                                    <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div id="noPhoneNumber" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
                    </span>
                </td>  
                <td width="112" class="left boder0-right"><label>Group By</label></td>
                    <td width="230" class="left boder0-right">
                        <span class="left boder0-left" style="display: flex; gap: 15px; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" name="group_by" value="created_by"  id="group_agent" 
                                    <?= (isset($_POST['group_by']) && $_POST['group_by'] == 'created_by') ? "checked" : "" ?>>
                                <label for="group_agent" style = 'margin-bottom:10px;'>Agent</label>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px;">
                            <!-- value changed from sent to to send to [vastvikta][03-05-2025] -->
                                <input type="radio" name="group_by" style='margin-left:-100px;' value="send_to" id="group_customer" 
                                    <?= (isset($_POST['group_by']) && $_POST['group_by'] == 'send_to') ? "checked" : "" ?>>
                                <label for="group_customer" style = 'margin-bottom:10px;' >Customer</label>
                            </div>
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
                <?php $whatsapp_report = base64_encode('whatsapp_report');?>
				<input name="reset" id="reset" type="button" value="RESET"  onclick="window.location.href='omni_channel.php?token=<?php echo $whatsapp_report; ?>';"  class="button-orange1 reset_whatsapp_report" />
			    </td>
			</tr>
		</table>
		<table  class="tableview tableview-2 " id="whatsapp_report">
			<thead>
			<tr class="background">
			<td align="center" valign="middle" width="2%" style="text-align: center;">
                <input type="checkbox" id="select-all" />
            </td>
			<td align="center" valign="middle" width = "2%"style="text-align: center;"> S.No</td>
			<td align="center" valign="middle" width ="8%" >Send From</td>
			<td align="center" valign="middle" width ="8%">Send To</td>
			<td align="center" valign="middle" width ="8%">Message Date</td>
			<td align="center" valign="middle" width ="15%">Message </td>
			<td align="center" valign="middle" width ="8%">Status</td>
			<td align="center" valign="middle" width ="9%">Status Response</td>
            <td align="center" valign="middle" width ="9%">Agent Name</td>
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
        <h3>Reschedule WhatsApp</h3>
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
<script type="text/javascript">
    function filterFunction(selectId, searchBoxId, messageDivId) {
        var input = document.getElementById(searchBoxId);
        var filter = input.value.toUpperCase();
        var select = document.getElementById(selectId);
        var options = select.getElementsByTagName("option");
        var visibleOptionsCount = 0;
        var dropdownContainer = document.getElementById('dropdownContainer');
        var messageDiv = document.getElementById(messageDivId);

        dropdownContainer.style.display = filter.length > 0 ? "block" : "none";
        var hasVisibleOptions = false;

        for (var i = 1; i < options.length; i++) {
            var txtValue = options[i].textContent || options[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
                hasVisibleOptions = true;
            } else {
                options[i].style.display = "none";
            }
        }

        messageDiv.style.display = !hasVisibleOptions && filter.length > 0 ? "block" : "none";
    }

    function selectOption(selectId, searchBoxId) {
        var select = document.getElementById(selectId);
        var searchBox = document.getElementById(searchBoxId);
        searchBox.value = select.options[select.selectedIndex].text;
        document.getElementById('dropdownContainer').style.display = "none";
    }

    document.addEventListener('click', function(event) {
        var dropdownContainer = document.getElementById('dropdownContainer');
        var searchBox = document.getElementById('searchBox');
        if (!dropdownContainer.contains(event.target) && !searchBox.contains(event.target)) {
            dropdownContainer.style.display = 'none';
        }
    });
    // Handle "Select All" checkbox
$('#select-all').on('click', function () {
    const isChecked = $(this).prop('checked');
    $('.row-checkbox').prop('checked', isChecked);
});

// Collect selected row IDs
function getSelectedRowIds() {
    const selectedIds = [];
    $('.row-checkbox:checked').each(function () {
        selectedIds.push($(this).data('id'));
    });
    return selectedIds;
}

// Handle bulk actions
$('#bulk-reschedule').on('click', function () {
    const selectedIds = getSelectedRowIds();
    if (selectedIds.length === 0) {
        alert('No rows selected!');
        return;
    }
    // Open reschedule popup and pass selected IDs
    $('#reschedule-popup').fadeIn();
    $('#bulk-data-ids').val(selectedIds.join(','));
});

// Reschedule Selected
$('#confirm-reschedule').on('click', function () {
    const selectedIds = $('#bulk-data-ids').val();
    const rescheduleDate = $('#reschedule-date').val();
    if (selectedIds.length === 0) {
        alert('Please select at least one WhatsApp to reschedule.');
        return;
    }
    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids: selectedIds.split(','),
            DateNew: rescheduleDate,
            action: 'whatsapp_reschedule'
        },
        success: function(response) {
           alert('Rescheduling successful!');
            $('#reschedule-popup').fadeOut();
            window.location.reload();
        },
        error: function(xhr) {
            alert('Error rescheduling WhatsApp.');
        }
    });
});
document.getElementById('cancel-reschedule').addEventListener('click', function () {
    document.getElementById('reschedule-popup').style.display = 'none';
});
// Expire Selected
$('#expire_selected').on('click', function() {
    const selectedIds = getSelectedRowIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one WhatsApp to expire.');
        return;
    }
    // Show confirmation dialog
    const confirmation = confirm('Are you sure you want to mark the selected WhatsApp as expired?');
    if (!confirmation) {
        return; // Exit if the user cancels
    }
    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids:selectedIds,
            action: 'whatsapp_expire'
        },
        success: function(response) {
            alert('WhatsApp marked as expired.');
            window.location.reload();
        },
        error: function(xhr) {
            alert('Error expiring WhatsApp.');
        }
    });
});
</script>