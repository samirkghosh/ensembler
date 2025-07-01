<?php
/**
 * Auth: Vastvikta Nishad 
 * Date: 22 November 2024
 * Description : Shows the data of queue instagram  report
 */
/* check license access or not for  this module*/ 
/***END***/

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_insta_report();

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
                      		<div class="col-sm-12 facebook_titile">Instagram Report</div>
						</div>
               		</div>
             	</div>
           	</div>
        </div>
	</span>
	<form method="post" name="cfrm" id="insta_report_form">
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
            <option value="" <?= isset($_POST['status']) && $_POST['status'] === "" ? "selected" : "" ?>>Select Status</option>
            <option value="0" <?= isset($_POST['status']) && $_POST['status'] === "0" ? "selected" : "" ?>>In Queue</option>
            <option value="2" <?= isset($_POST['status']) && $_POST['status'] === "2" ? "selected" : "" ?>>Pending</option>
            <option value="1" <?= isset($_POST['status']) && $_POST['status'] === "1" ? "selected" : "" ?>>Deliver</option>
        </select>
   

				
				</span>
			</td>
            </tr>
            <tr>
            <td width="112" class="left boder0-right"><label>Send To</label></td>
			<td width="150" align="left" class="left boder0-right">
				<span class="left boder0-left">
					<?php 
					$query = mysqli_query($link, "SELECT DISTINCT(send_to) FROM $db.instagram_out_queue;");
					$selectedRecipient = isset($_POST['send_to']) ? $_POST['send_to'] : '';
					?>
					<input type="text" id="searchBox" placeholder="Search ID" onkeyup="filterFunction('send_to', 'searchBox', 'noMobileNO')" value="<?= $selectedRecipient; ?>" style="width:150px; height:30px; margin-bottom: 5px;">
					<div id="dropdownContainer" class="dropdown-content" style="width:150px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
						<select name="send_to" id="send_to" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0;" onchange="selectOption('send_to', 'searchBox')">
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
			</td>
			
				<td class="left  boder0-left" colspan="3">
				<input type="submit" name="sub1" value="Run Report" class="button-orange1 set_button">
				<?php $insta_report = base64_encode('insta_report');?>
                <input type="button" 
                    class="button-orange1" 
                    value="Reset" 
                    onclick="window.location.href='omni_channel.php?token=<?php echo $insta_report; ?>';" 
                    style="float:inherit; color:#222; text-decoration:none; " />
			</td>
			</tr>
		</table>
		<table  class="tableview tableview-2 " id="insta_report">
			<thead>
			<tr class="background">
			<td align="center" valign="middle" width = "2%"> S.No</td>
			<td align="center" valign="middle" width ="8%" >Send From </td>
            <td align="center" valign="middle" width ="8%" >Send To </td>
			<td align="center" valign="middle" width ="25%">Message</td>
			<td align="center" valign="middle" width ="8%">Status</td>
			<td align="center" valign="middle" width ="9%">Status Response</td>
			<td align="center" valign="middle" width ="8%">Date</td>
			</tr>
		</thead>
		</table>
	</form>
</div>
<script>

function filterFunction(selectId, searchBoxId, messageDivId) {
        var input = document.getElementById(searchBoxId);
        var filter = input.value.toUpperCase();
        var select = document.getElementById(selectId);
        var options = select.getElementsByTagName("option");
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

	document.getElementById('searchBox').addEventListener('blur', function() {
	    setTimeout(function() {
	        document.getElementById('dropdownContainer').style.display = 'none';
	    }, 200);
	});

    document.addEventListener('click', function(event) {
        var dropdownContainer = document.getElementById('dropdownContainer');
        var searchBox = document.getElementById('searchBox');
        if (!dropdownContainer.contains(event.target) && !searchBox.contains(event.target)) {
            dropdownContainer.style.display = 'none';
        }
    });
   $(document).ready(function() {

var instaReport;

// Function to initialize and fill the WhatsApp data table
function fill_datatables_insta_report(startdatetime ='',enddatetime ='',status ='',send_to ='') {

console.log($('#startdatetime').val());
console.log($('#enddatetime').val());
console.log($('#status').val());
instaReport = $('#insta_report').DataTable({
    "processing": true,
    "serverSide": true,
    "order": [],
    "pageLength": 25, // Default records per page
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "searching": false,
    "paging": true, // Ensure paging is enabled
    "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         "buttons": [
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'CSV',
                filename: 'csv',
                title: $('.download_label').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                filename: 'Excel',
                title: $('.download_label').html(),
                exportOptions: {
                    columns: ':visible'
                }
            },{
                    extend: 'pdfHtml5',
                    filename : $('.report_name').text(),
                    messageTop : $('.download_label').html(),
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                     // changed logo code [vastvikta][05-05-2025]
                     customize: function ( doc ) {
                            
                            var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                        
                        doc.content.splice( 1, 0, {
                            margin: [ 0, 0, 0, 5 ],
                                alignment: 'left',
                                image: 'logo', 
                                 width: 250
                            } );
                        },
                    title: '.',
                    exportOptions: {
                        columns: ':visible'
                        
                    }
                },{
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i>',
                titleAttr: 'Columns',
                title: $('.download_label').html(),
                postfixButtons: ['colvisRestore']
            },
        ],
    "ajax": {
        url: "omnichannel_config/fetchData.php",
        type: "POST",
        data: function(d) {
            d.startdatetime = $('#startdatetime').val();
            d.enddatetime = $('#enddatetime').val();
            d.status = $('#status').val();
            d.send_to = $('#send_to').val();
            d.action = 'insta_report';
        }
    }
});
}

// Initialize instagram report table
fill_datatables_insta_report();

// Set an interval to refresh the table every 10 seconds
setInterval(function() {
instaReport.ajax.reload(null, false); // Reload data without resetting pagination
}, 10000);

// Submit form and reload data based on new filters
$('#insta_report_form').submit(function(e) {
console.log('exe');
e.preventDefault();

$('#insta_report').DataTable().destroy();
var startdatetime = $('#startdatetime').val();
var enddatetime = $('#enddatetime').val();
var status = $('#status').val();
var send_to = $('#send_to').val();
fill_datatables_insta_report(startdatetime,enddatetime,status,send_to);// Refresh the table with the new filter parameters
});
});
    </script>
