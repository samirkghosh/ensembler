<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 24 October 2024
 * Details: Shows the data of messanger queue posts 
 */
/* check license access or not for  this module*/ 
include_once("../../ensembler/function/classify_function.php"); 
/***END***/

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_messenger_report();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

?>
<div class="col-sm-10 mt-3" style="padding-left:0">
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
		<div class="row">
           	<div class="col-sm-12">
             	<div class="row">
               		<div class="col-sm-3">
                   		<div class="row">
                      		<div class="col-sm-12 facebook_titile">Messenger Queue Report</div>
						</div>
               		</div>
             	</div>
           	</div>
        </div>
	</span>
	<form method="post" name="cfrm" id="messenger_report_form">
		<table class="tableview tableview-2 main-form new-customer">
			<tr >
				<td width="95" class="left boder0-right">&nbsp;
					<label>Start Date </label>
				</td>
				<td width="226" class="left boder0-right">
					
					<span class="left boder0-left">
						<input type="text" name="startdatetime" class="date_class dob1"  value="<?=$startdatetime?>" id="startdatetime">&nbsp;
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
				<option value="0">In Queue</option>
				<option value="2">Pending</option>
				<option value="1">Deliver</option>
				</select>
				
				</span>
			</td>
            </tr>
            <tr>
			<tr>
			
			<td width="112" class="left boder0-right"><label>Sent To</label></td>
			<td width="150" align="left" class="left boder0-right">
				<span class="left boder0-left">
					<?php 
					$query = mysqli_query($link, "SELECT DISTINCT(send_to) FROM $db.messenger_out_queue;");
					?>
					
					<input type="text" id="searchBox" placeholder="Search ID" onkeyup="filterFunction('send_to', 'searchBox', 'noPhoneNumber')" style="width:150px; height:30px; margin-bottom: 5px;">
					
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
			</td>
			
				<td class="left  boder0-left" colspan="3">
				<input type="submit" name="sub1" value="Run Report" class="button-orange1 set_button">
				<?php $messenger_report = base64_encode('messenger_report');?>
                <input type="button" 
                    class="button-orange1" 
                    value="Reset" 
                    onclick="window.location.href='omni_channel.php?token=<?php echo $messenger_report; ?>';" 
                    style="float:inherit; color:#222; text-decoration:none; " />
			</td>
			</tr>
		</table>
		<table  class="tableview tableview-2 " id="messenger_report">
			<thead>
			<tr class="background">
			<td align="center" valign="middle" width = "2%"style="text-align: center;"> S.No</td>
			<td align="center" valign="middle" width ="8%" >Send From</td>
			<td align="center" valign="middle" width ="8%">Send To</td>
			<td align="center" valign="middle" width ="15%">Message </td>
			<td align="center" valign="middle" width ="8%">Status</td>
			<td align="center" valign="middle" width ="9%">Status Response</td>
			<td align="center" valign="middle" width ="8%">Message Date</td>
			</tr>
		</thead>
		</table>
	</form>
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
</script>