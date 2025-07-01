<?php
/***
Auth: Vastvikta Nishad
Date: 09 Feb 2024
Description:Display Outgoing Email queue  Report
*/
include_once("admin/web_admin_function.php");

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_email_report();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

$I_Status = isset($_REQUEST['I_Status']) ? $_REQUEST['I_Status'] : '';
$to_email = isset($_REQUEST['to_email']) ? $_REQUEST['to_email'] : '';
$from_email = isset($_REQUEST['from_email']) ? $_REQUEST['from_email'] : '';
$timeperiod = isset($_REQUEST['timeperiod']) ? $_REQUEST['timeperiod'] : '';
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
    <div>
        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Outgoing Email Report</span>
    </div>
    <div id="success"></div>
    <form name="frmService" action="" method="post" id ="email_report_form">
        <div class="table" id="SRallview"> 
            <div class="">
                <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                <tr>
                   
                    <td class="left boder0-left "  colspan="1"><span class="left  boder0-right">
                        From 
                        <input type="text" id="startdatetime" name="startdatetime" class="date_class dob1" style="width:160px" value="<?=$startdatetime?>"  autocomplete="off">&nbsp;
                        To <input type="text" name="enddatetime" id="enddatetime" class="date_class dob1"  style="width:160px" value="<?=$enddatetime?>" autocomplete="off">
                        </span>
                    </td>
                    <td class="left boder0-right">
                        <label>To</label>
                        <div class="log-case">
                        <?php 
                            $agnetresult=getToEmailInformation(); ?>
                            <select name="to_email" id="to_email" class="select-styl1" style="width:190px">
                            <option value="">All</option>
                                <?                                                 
                                while($row=mysqli_fetch_array($agnetresult)) {
                                    $v_toemail=$row['v_toemail'];
                                    $id=$row['id'];
                                    if($v_toemail == $_REQUEST['to_email'])
                                    {
                                    $sel = ' selected';
                                    }
                                    else
                                    {
                                    $sel = '';
                                    }
                                ?>  
                            <option value='<?php echo$v_toemail?>' <?php echo $sel?>><?php echo $v_toemail?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                    <td class="left boder0-right">
                        <label>From</label>
                        <?php 
                            $agnetresults=getFromEmailInformation();
                            ?>
                            <select name="from_email" id="from_email" class="select-styl1" style="width:190px">
                            <option value="">All</option>
                                <?                                                 
                                while($rows=mysqli_fetch_array($agnetresults)){
                                    $v_fromemail=$rows['v_fromemail'];
                                    $id=$rows['id'];
                                    if($v_fromemail == $_REQUEST['from_email'])
                                    {
                                    $sel = ' selected';
                                    }
                                    else
                                    {
                                    $sel = '';
                                    }
                                ?>       
                            <option value='<?php echo $v_fromemail?>' <?php echo $sel?>><?php echo $v_fromemail?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="left boder0-right">
                        <label>Status</label>
                        <div class="log-case">
                        <select name="I_Status" id="I_Status" class="select-styl1" style="width:190px">
                            <option value="">Select status</option>
                            <option value="1"  <? if($I_Status==1) echo "selected";?>>In Queue</option>
                            <option value="2"  <? if($I_Status==2) echo "selected";?> >Delivered</option>
                            <option value="3" <?= isset($I_Status) && $I_Status === "3" ? "selected" : "" ?>>Not Delivered</option>
                            <option value="4" <?= isset($I_Status) && $I_Status === "4" ? "selected" : "" ?>>Expire</option>
                            <option value="5" <?= isset($I_Status) && $I_Status === "5" ? "selected" : "" ?>>Rescheduling</option>
                        </select>
                        </div>
                    </td>
                    <td>
                        <a href="javascript:void(0);" id="bulk-reschedule" style="font-size:24px;" title="Bulk Reschedule">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </a>
                        <a href="javascript:void(0);" id="expire_selected" style="font-size:24px;" title="Bulk Expire">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td  class="left boder0-right" colspan="2">                       
                        <?php $email_queue_report = base64_encode('email_queue_report');?>
                        <input type="submit" name="sub1" value="Run Report" class="button-orange1" />
                        <input type="button" 
                               class="button-orange1" 
                               value="Reset" 
                               onclick="window.location.href='omni_channel.php?token=<?php echo $email_queue_report; ?>';" 
                               style="float:inherit; color:#222; text-decoration:none; " />
                    </td>
                </tr>
                </tbody>
                </table>
                <div class="div2">
                    <table class="tableview tableview-2" id="email_queue_report">
                        <thead>
                            <tr class="background">
                                <td align="center" valign="middle" width="2%" style="text-align: center;">
                                    <input type="checkbox" id="select-all" />
                                </td>
                                <td align="center" valign="middle" width="5%"> S.No</td>
                                <td align="center" valign="middle" width="10%" >Send To </td>
                                <td align="center" valign="middle" width="10%">Send From </td>
                                <td align="center" valign="middle" width="20%">Message Date </td>
                                <td align="center" valign="middle" width="5%">Status </td>
                                <td align="center" valign="middle" width="30%">Status Response </td>
                                <td align="center" valign="middle" width ="8%">Schedule Date</td>
                                <td align="center" valign="middle" width="8%">Reschedule Status</td> <!-- New Column -->
                            </tr>
                        </thead>
                        <tbody>
                       
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Rescheduling Popup -->
<div id="reschedule-popup" class="popup-overlay" data-id="">
    <div class="popup-content">
        <input type="hidden" id="bulk-data-ids" />
        <h3>Reschedule EMAIL</h3>
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
<script>
$(document).ready(function() {
    var emailQueueReport;
    // Function to initialize and fill the WhatsApp data table
    function fill_datatables_email_report(startdatetime ='',enddatetime ='',I_Status ='',from_email ='',to_email = '') {
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
      
    emailQueueReport = $('#email_queue_report').DataTable({
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
                d.from_email = $('#from_email').val();
                d.to_email = $('#to_email').val();
                d.I_Status = $('#I_Status').val();
                d.action = 'email_queue_report';
            }
        }
    });
    }

    // Initialize WhatsApp report table
    fill_datatables_email_report();

    // Set an interval to refresh the table every 10 seconds
    setInterval(function() {
        emailQueueReport.ajax.reload(null, false); // Reload data without resetting pagination
    }, 20000);

    // Submit form and reload data based on new filters
    $('#email_report_form').submit(function(e) {
        e.preventDefault();

        $('#email_queue_report').DataTable().destroy();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var I_Status = $('#I_Status').val();
        var from_email = $('#from_email').val();
        var to_email = $('#to_email').val();
        fill_datatables_email_report(startdatetime,enddatetime,I_Status,from_email,to_email);// Refresh the table with the new filter parameters
    });

    // Reset button functionality
    $('.reset_email_report').click(function () { 
        // Destroy the existing DataTable
        emailQueueReport.destroy();
        // Get the current date and time
        var currentDate = new Date();
        // Format the first day of the current month as '01-mm-yyyy 00:00:00'
        var startOfMonth = '01-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + currentDate.getFullYear() + ' 00:00:00';

        // Format the current date as 'dd-mm-yyyy 23:59:59'
        var formattedEndDate = ('0' + currentDate.getDate()).slice(-2) + '-' +
                            ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' +
                            currentDate.getFullYear() + ' 23:59:59';
        // Reset the form fields
        $('#startdatetime').val(startOfMonth);
        $('#enddatetime').val(formattedEndDate);
        $('#I_Status').val('');
        $('#from_email').val('');
        $('#to_email').val('');
        
        // Reinitialize the table with default filters
        fill_datatables_email_report(); // Ensure this function initializes the table correctly
    });
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
        alert('Please select at least one Email to reschedule.');
        return;
    }
    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids: selectedIds.split(','),
            DateNew: rescheduleDate,
            action: 'email_reschedule'
        },
        success: function(response) {
           alert('Rescheduling successful!');
            $('#reschedule-popup').fadeOut();
            window.location.reload();
        },
        error: function(xhr) {
            alert('Error rescheduling Email.');
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
        alert('Please select at least one Email to expire.');
        return;
    }
    // Show confirmation dialog
    const confirmation = confirm('Are you sure you want to mark the selected Email as expired?');
    if (!confirmation) {
        return; // Exit if the user cancels
    }
    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids:selectedIds,
            action: 'email_expire'
        },
        success: function(response) {
            alert('Email marked as expired.');
            // window.location.reload();
        },
        error: function(xhr) {
            alert('Error expiring Email.');
        }
    });
});
</script>

