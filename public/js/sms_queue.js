
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

var SMSReport;

// Function to initialize and fill the WhatsApp data table
function fill_datatables_SMS_report() {    
    SMSReport = $('#SMS_report').DataTable({
        "processing": true,
        "serverSide": true,
        "order": false,
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
                d.v_mobileNo = $('#v_mobileNo').val();
                d.Bulkstatus = $('#Bulkstatus').val();
                d.action = 'SMS_report';
            }
        }
    });
}

// Initialize WhatsApp report table
fill_datatables_SMS_report();

// Set an interval to refresh the table every 10 seconds
setInterval(function() {
SMSReport.ajax.reload(null, false); // Reload data without resetting pagination
}, 30000);

// Submit form and reload data based on new filters
$('#SMS_report_form').submit(function(e) {
console.log('exe');
e.preventDefault();

$('#SMS_report').DataTable().destroy();
    fill_datatables_SMS_report();// Refresh the table with the new filter parameters
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
        alert('Please select at least one SMS to reschedule.');
        return;
    }

    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids: selectedIds.split(','),
            DateNew: rescheduleDate,
            action: 'sms_reschedule'
        },
        success: function(response) {
           alert('Rescheduling successful!');
            $('#reschedule-popup').fadeOut();
            window.location.reload();
        },
        error: function(xhr) {
            alert('Error rescheduling SMS.');
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
        alert('Please select at least one SMS to expire.');
        return;
    }

    // Show confirmation dialog
    const confirmation = confirm('Are you sure you want to mark the selected SMS as expired?');
    if (!confirmation) {
        return; // Exit if the user cancels
    }
    $.ajax({
        url: 'omnichannel_config/reschedule.php',
        type: 'POST',
        data: {
            ids:selectedIds,
            action: 'sms_expire'
        },
        success: function(response) {
            alert('SMS marked as expired.');
            window.location.reload();
        },
        error: function(xhr) {
            alert('Error expiring SMS.');
        }
    });
});