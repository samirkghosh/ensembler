// updated title for reports custom according to report and date [vastvikta][17-04-2025]
// Data table code (Report section)
$(document).ready(function(){

    fill_datatable_agent_break();
    function fill_datatable_agent_break( startdatetime = '', enddatetime = ''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
       var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
        
       var dataTable = $('#agent_break_report').DataTable({
             "processing": true,
             "serverSide": true,
             "order": [],
             "pageLength": 10,
             "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
             "searching": false,
             "paging": true, // Ensure paging is enabled
             "dom": "lBfrtip", // "p" ensures pagination is included in the layout
             "buttons": [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                        extend: 'pdfHtml5',
                        filename : 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        messageTop : 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
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
                    title: 'CRM Users Break Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
          "ajax" : {
             url:"Report/report_function.php",
             type:"POST",
             data:{
                   startdatetime:startdatetime,
                   enddatetime:enddatetime,
                   action:'agent_break_report'
             }
          }
       });
    }
    
    $(document).on('click', '.toggle-view', function(e) {
        e.preventDefault();
    
        // Get the username and startdate from the clicked link
        var username = $(this).data('username');
        var startdate = $(this).data('startdate');
        
        // Get the parent row of the clicked link
        var parentRow = $(this).closest('tr');
        
        // Check if the detail row already exists
        if ($(this).hasClass('open')) {
            // Detail row is already open, so remove it (collapse)
            parentRow.next('.detail-row').remove();
            $(this).removeClass('open'); // Mark it as collapsed
        } else {
            // Collapse any other open detail rows
            $('.detail-row').remove();
            $('.toggle-view').removeClass('open');
            
            // Insert the details table right after the parent row
            var detailRow = `
                <tr class="detail-row">
                    <td colspan="4">
                        <table class="tableview tableview-2 break-details" id="break_report_details">
                           <thead>
                              <tr class="background" style="font-size: 12px">
                                 <th align="center">Sno</th>
                                 <th align="center">Break Name</th>
                                 <th align="center">Break Time In</th>
                                 <th align="center">Break Time Out</th>
                                 <th align="center">Duration</th>
                              </tr>
                           </thead>
                        </table>
                    </td>
                </tr>
            `;
            
            // Append the detailRow after the current parent row
            parentRow.after(detailRow);
            $(this).addClass('open'); // Mark the row as open
    
            // Call the function to fill the details table
            fill_datatable_break_report_details(username, startdate, `#break_report_details_${username}_${startdate}`);
        }
    });
    
    
    function fill_datatable_break_report_details(username = '', startdate = '' ){
        var dataTable = $('#break_report_details').DataTable({
              "processing": true,
              "serverSide": true,
              "order": [],
              "pageLength": 10,
              "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
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
           "ajax" : {
              url:"Report/report_function.php",
              type:"POST",
              data:{
                username: username,
                startdate: startdate,
                action:'break_report_details'
              }
           }
        });
     }
   // Store initial values of date inputs when document is ready
 var initialStartDate;
 var initialEndDate;
 
 $(document).ready(function() {
     initialStartDate = $('#sttartdatetime').val();
     initialEndDate = $('#enddatetime').val();
 });
 $('#submit').click(function() {
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#agent_break_report').DataTable().destroy();

    // Call fill_datatable function with appropriate filter parameters
    fill_datatable_agent_break(startdatetime, enddatetime);
});

 
// Report-> Agent report datatable code
   fill_datatable();
   function fill_datatable(agent = '', timeperiod = '', startdatetime = '', enddatetime = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
        
       
      var dataTable = $('#agent_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                    {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename:'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Login User Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  agent_n:agent, 
                  timeperiod:timeperiod,
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  action:'view_agentreport'
            }
         }
      });
   }
  // Store initial values of date inputs when document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit').click(function(){
    var agent = $('#agent_n').val();
    var timeperiod = $('#timeperiod').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#agent_data').DataTable().destroy();

    // Call fill_datatable function with appropriate filter parameters
    fill_datatable(agent,timeperiod,startdatetime,enddatetime);
});

// added the code for the  save filter in login user report[vastvikta][26-03-2025]
$(document).ready(function() {// Event listener for clicking the "Save Filter" button
    $("#save_filters1").click(function() {
        // Get the filter name input and trim any extra spaces
        let filterName = $("#filter_name").val().trim();
        // Get the filter page value (hidden input field)
        let filterPage = $("#filter_page").val(); 
    
        // Validate if the filter name is empty
        if (filterName === "") {
            alert("Please enter a filter name!"); // Alert user to enter a filter name
            return; // Stop execution
        }
    
        // Collect selected filter values from the form
        let filterData = {
            agent_n: $("#agent_n").val(),  // Get selected agent name
            sttartdatetime: $("#startdatetime").val(), // Fix incorrect ID name
            enddatetime: $("#enddatetime").val(), // Get end date/time
            timeperiod: $("#timeperiod").val(), // Get time period selection
        };
    
        // Prepare the JSON payload for AJAX request
        let payload = {
            action: "save_filter", // Identify the action being performed
            filter_name: filterName, // Filter name entered by user
            filters: filterData, // Collected filter data
            filter_page: filterPage, // Page identifier
        };
    
        // Send filter name & values via AJAX request to save in the database
        $.ajax({
            url: "Report/save_filter.php", // PHP script handling the request
            type: "POST", // Use POST method for data submission
            contentType: "application/json", // Specify JSON format
            data: JSON.stringify(payload), // Convert payload to JSON string
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message); // Show success message
                    $("#filterModal").hide(); // Hide the modal after saving
                    location.reload(); // Refresh the page to update saved filters
                } else {
                    alert("Error: " + response.message); // Show error message from server
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error); // Debugging: log AJAX error details
                alert("Error saving the filter. Please try again."); // Inform user about failure
            }
        });
    });
    
    // Event listener for when a user selects a saved filter from the dropdown
    $("#saved_filters").change(function () {
        // Get the selected filter ID
        let selectedFilter = $("#saved_filters").val();
    
        // Validate if no filter is selected
        if (selectedFilter === "") {
            alert("Please select a saved filter."); // Alert user
            return; // Stop execution
        }
         // Send AJAX request to retrieve filter data
        $.ajax({
            url: "Report/save_filter.php", // PHP script handling filter retrieval
            type: "POST", // Use POST method
            contentType: "application/json",  // Specify JSON format
            data: JSON.stringify({ 
                action: "get_filter", // Identify the action as retrieving a filter
                filter_id: selectedFilter // Send the selected filter ID
            }),
            success: function (response) {
                if (response.status === "success") {
                    let filterData = response.filters; // Get filter data from response
                     if (filterData) {
                        // Populate form fields with retrieved filter data
                        $("#agent_n").val(filterData.agent_n);
                        $("#startdatetime").val(filterData.sttartdatetime);
                        $("#enddatetime").val(filterData.enddatetime);
                        $("#timeperiod").val(filterData.timeperiod);
    
                        // Destroy the existing DataTable instance before reloading data
                        $('#agent_data').DataTable().destroy();
    
                        // Call function to fill the table with filtered data
                        fill_datatable(filterData.agent_n, filterData.timeperiod, filterData.startdatetime, filterData.enddatetime);
    
                    } else {
                        alert("No data found in the filter."); // Inform user if no data is found
                    }
                } else {
                    alert("Error: " + response.message); // Show error message if retrieval fails
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error); // Debugging: log AJAX errors
                alert("Failed to fetch filter data. Please try again."); // Inform user about failure
            }
        });
    });
    

//  added this code  for filter active inactive [vastvikta][02-04-2025]
    // Event listener for clicking the "Delete Filter" button
    $("#delete_filter").on("click", function () {
        // Get the selected filter ID from the dropdown
        var selectedFilter = $("#saved_filters").val();
        // Check if a filter is selected, if not, show an alert and exit
        if (!selectedFilter) {
            alert("Please select a filter to delete.");
            return;
        }

        // Confirm deletion with the user
        if (confirm("Are you sure you want to delete this filter?")) {
            // Prepare the data payload for the AJAX request
            let payload = {
                action: "delete_filter",  // Specifies the action to perform
                filter_id: selectedFilter // The ID of the filter to be deleted
            };

            // Send an AJAX request to delete the filter
            $.ajax({
                url: "Report/save_filter.php", // The server-side script handling the request
                type: "POST", // HTTP method
                contentType: "application/json", // Specify that we're sending JSON data
                data: JSON.stringify(payload), // Convert the payload object to a JSON string
                success: function (response) { // Function executed if request is successful
                    if (response.status === "success") {
                        alert(response.message); // Show success message
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert("Error: " + response.message); // Show error message if deletion fails
                    }
                },
                error: function (xhr, status, error) { // Handle AJAX errors
                    console.error("AJAX Error:", status, error);
                    alert("Error deleting the filter. Please try again.");
                }
            });
        }
    });

    // Event listener for clicking the "Restore Filter" button
    $("#restore_filter").on("click", function () {
        // Get the selected filter ID from the dropdown
        var selectedFilter = $("#saved_filters").val();
        // Check if a filter is selected, if not, show an alert and exit
        if (!selectedFilter) {
            alert("Please select a filter to restore.");
            return;
        }

        // Confirm restoration with the user
        if (confirm("Are you sure you want to restore this filter?")) {
            // Prepare the data payload for the AJAX request
            let payload = {
                action: "restore_filter",  // Specifies the action to perform
                filter_id: selectedFilter // The ID of the filter to be restored
            };

            // Send an AJAX request to restore the filter
            $.ajax({
                url: "Report/save_filter.php", // The server-side script handling the request
                type: "POST", // HTTP method
                contentType: "application/json", // Specify that we're sending JSON data
                data: JSON.stringify(payload), // Convert the payload object to a JSON string
                success: function (response) { // Function executed if request is successful
                    if (response.status === "success") {
                        alert(response.message); // Show success message
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert("Error: " + response.message); // Show error message if restoration fails
                    }
                },
                error: function (xhr, status, error) { // Handle AJAX errors
                    console.error("AJAX Error:", status, error);
                    alert("Error restoring the filter. Please try again.");
                }
            });
        }
    });
    // Event listener for clicking the "Update Filter" button
    $("#save_filters2").click(function() {
        // Get the selected filter ID from the dropdown
        let filterId = $("#saved_filters").val();
         if (!filterId) {
            alert("Please select a filter to update.");
            return;
        }

        // Get the updated filter name from the input field
        let filterName = $("#filter_name_update").val();
        
        // Get the filter page value from a hidden input field (used for tracking)
        let filterPage = $("#filter_page").val();
        if (filterName === "") {
            alert("Please enter a filter name!");
            return;
        }

        // Collect selected filter values from form inputs
        let filterData = {
            agent_n: $("#agent_n").val(), // Get agent name or ID
            startdatetime: $("#startdatetime").val(), // Get start date and time
            enddatetime: $("#enddatetime").val(), // Get end date and time
            timeperiod: $("#timeperiod").val(), // Get selected time period
        };
        // Prepare JSON payload for updating the filter
        let payload = {
            action: "update_filter", // Specify the action type
            filter_id: filterId, // ID of the filter being updated
            filter_name: filterName, // New filter name
            filters: filterData, // New filter criteria
            filter_page: filterPage, // Maintain filter page reference
        };

        // Send an AJAX request to update the filter
        $.ajax({
            url: "Report/save_filter.php", // Server-side script handling filter updates
            type: "POST", // HTTP request method
            contentType: "application/json", // Specify JSON format
            data: JSON.stringify(payload), // Convert the payload object to JSON string
            success: function(response) { // Function executed if request is successful
                if (response.status === "success") {
                    alert(response.message); // Show success message
                    $("#filterModal2").hide(); // Hide modal after updating filter
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert("Error: " + response.message); // Show error message if update fails
                }
            },
            error: function(xhr, status, error) { // Handle AJAX errors
                console.error("AJAX Error:", status, error);
                alert("Error updating the filter. Please try again.");
            }
        });
    });
});

// end of the code for the save filter in login user report 
$('#reset').click(function() {
    // Reset date inputs to initial values
    $('#startdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);

    // Clear other filter inputs and reload DataTable
    $('#agent_data').DataTable().destroy();
    $('#agent_n, #timeperiod , #saved_filters').val('');
    fill_datatable();
});
   // report ->case data table
function case_datatable() {
    console.log("hello");
    var fname = $('#fname').val();
    var casee = $('#casee').val();      
    var agent = $('#agent').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();  
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
    var status = $('#status').val();
    var vProject = $('#vProject').val();
    var category = $('#category').val();      
    var subcategory = $('#subcategory').val();
    var district = $('#district').val();      
    var village = $('#village').val();
    var comp = $('#comp').val();
    var source = $('#source').val();      
    var priority = $('input[name="priority"]:checked').val(); // Fetch selected priority value
    var customertype = $('#customertype').val();    
    var esc_status = $('#esc_status').val(); 
// updated the export code [vastvikta][13-05-2025]
    var table = $('#casee_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
        "buttons": [
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'CSV',
                filename: 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                title: 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                exportOptions: { columns: ':visible,:hidden' },
            
                customizeData: function (csv) {
                    var api = $('#casee_data').DataTable();
                    var newBody = [];
            
                    // Add a placeholder for timeline headers
                    var timelineHeader = [
                        'Department', 'Start Time', 'End Time', 'Duration',
                        'Category', 'Subcategory', 'Status', 'Comment', 'Remark'
                    ];
            
                    csv.body.forEach(function (row, index) {
                        newBody.push(row); // Add main row
            
                        var tableRow = api.row(index);
                        var childHtml = tableRow.child && tableRow.child.isShown() ? $(tableRow.child()).html() : '';
            
                        if (childHtml) {
                            var tempDiv = $('<div>').html(childHtml);
                            tempDiv.find('tbody tr').each(function () {
                                var cells = $(this).find('td');
                                if (cells.length === 9) {
                                    var timelineData = [];
                                    for (let i = 0; i < 9; i++) {
                                        timelineData.push(cells.eq(i).text().trim());
                                    }
            
                                    // Insert header row before timeline data (optional)
                                    newBody.push(timelineHeader);
                                    newBody.push(timelineData);
                                }
                            });
                        }
                    });
            
                    // Replace original body with new one
                    csv.body = newBody;
            
                    // Add 'Timeline Row' indicator to header (optional)
                    csv.header.push(); // keep header same if not adding timeline directly in columns
                }
            },{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                filename: 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                title: 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                exportOptions: { columns: ':visible,:hidden' },
            
                customizeData: function (xlsx) {
                    var api = $('#casee_data').DataTable();
                    var newBody = [];
            
                    var timelineHeader = [
                        'Department', 'Start Time', 'End Time', 'Duration',
                        'Category', 'Subcategory', 'Status', 'Comment', 'Remark'
                    ];
            
                    xlsx.body.forEach(function (row, index) {
                        newBody.push(row); // Add main row
            
                        var tableRow = api.row(index);
                        var childHtml = tableRow.child && tableRow.child.isShown() ? $(tableRow.child()).html() : '';
            
                        if (childHtml) {
                            var tempDiv = $('<div>').html(childHtml);
                            tempDiv.find('tbody tr').each(function () {
                                var cells = $(this).find('td');
                                if (cells.length === 9) {
                                    var timelineData = [];
                                    for (let i = 0; i < 9; i++) {
                                        timelineData.push(cells.eq(i).text().trim());
                                    }
            
                                    newBody.push(timelineHeader);
                                    newBody.push(timelineData);
                                }
                            });
                        }
                    });
            
                    xlsx.body = newBody;
                }
            },{
                extend      : 'pdfHtml5',
                text        : '<i class="fa fa-file-pdf-o"></i>',
                titleAttr   : 'PDF',
                filename    : 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                messageTop  : 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                orientation : 'landscape',
                pageSize    : 'A3',
                title       : '.',
                exportOptions : { columns : ':visible' },
            
                customize: function (doc) {
                    var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                    doc.images = doc.images || {};
                    doc.images.logo = logoBase64;
                
                    // Insert logo at top
                    doc.content.splice(1, 0, {
                        margin: [0, 0, 0, 5],
                        alignment: 'left',
                        image: 'logo',
                        width: 250
                    });
                
                    var api = $('#casee_data').DataTable();
                    var table = doc.content[doc.content.length - 1].table;
                    var oldBody = table.body;
                    var newBody = [];
                
                    // Add headers for the main table
                    newBody.push(oldBody[0]);
                
                    api.rows().every(function (i) {
                        var rowData = oldBody[i + 1];
                        newBody.push(rowData); // main row
                
                        var row = this;
                        var childHtml = row.child && row.child.isShown() ? $(row.child()).html() : '';
                
                        if (childHtml) {
                            var tempDiv = $('<div>').html(childHtml);
                            tempDiv.find('tbody tr').each(function () {
                                var cells = $(this).find('td');
                                if (cells.length === 9) {
                                    // Add mini-table as a new row in the PDF
                                    newBody.push([{
                                        colSpan: rowData.length,
                                        margin: [0, 5, 0, 5],
                                        table: {
                                            widths: ['auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', '*', '*'],
                                            body: [
                                                // Header row for the timeline table
                                                [
                                                    { text: 'Department', style: 'tableHeader' },
                                                    { text: 'Start Time', style: 'tableHeader' },
                                                    { text: 'End Time', style: 'tableHeader' },
                                                    { text: 'Duration', style: 'tableHeader' },
                                                    { text: 'Category', style: 'tableHeader' },
                                                    { text: 'Subcategory', style: 'tableHeader' },
                                                    { text: 'Status', style: 'tableHeader' },
                                                    { text: 'Comment', style: 'tableHeader' },
                                                    { text: 'Remark', style: 'tableHeader' }
                                                ],
                                                // Data row for the timeline
                                                [
                                                    cells.eq(0).text(),
                                                    cells.eq(1).text(),
                                                    cells.eq(2).text(),
                                                    cells.eq(3).text(),
                                                    cells.eq(4).text(),
                                                    cells.eq(5).text(),
                                                    cells.eq(6).text(),
                                                    cells.eq(7).text(),
                                                    cells.eq(8).text()
                                                ]
                                            ],
                                            layout: {
                                                // Horizontal lines after each row
                                                hLineWidth: function (i, node) {
                                                    return (i === 0 || i === node.table.body.length) ? 1 : 0; // Add line after header and last row
                                                },
                                                vLineWidth: function (i) {
                                                    return 0; // Remove vertical lines
                                                },
                                                hLineColor: function () {
                                                    return '#000'; // Black color for the horizontal line
                                                },
                                                vLineColor: function () {
                                                    return '#000'; // Black color for vertical lines
                                                }
                                            }
                                        },
                                        layout: 'lightHorizontalLines'
                                    }]);
                                }
                            });
                        }
                    });
                
                    table.body = newBody;
                
                    // Optional styling
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 10,
                        color: 'black',
                        fillColor: '#e0e0e0' // light grey background
                    };
                
                    doc.styles.timelineRow = {
                        fontSize: 9,
                        color: '#333'
                    };
                }
                

                
                
            }
            ,{
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i>',
                titleAttr: 'Columns',
                title: 'Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                postfixButtons: ['colvisRestore']
            },
        ],
      "columns": [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
            { data: null, render: function(data, type, row) { return row[0]; } }, // Case ID (with link)
            { data: null, render: function(data, type, row) { return row[1]; } }, // Created On
            { data: null, render: function(data, type, row) { return row[2]; } }, // Aged
            { data: null, render: function(data, type, row) { return row[3]; } }, // In Progress
            { data: null, render: function(data, type, row) { return row[4]; } }, // Closed On
            { data: null, render: function(data, type, row) { return row[5]; } }, // Status
            { data: null, render: function(data, type, row) { return row[6]; } }, // Complaint Origin
            { data: null, render: function(data, type, row) { return row[7]; } }, // Name
            { data: null, render: function(data, type, row) { return row[8]; } }, // Agent
            { data: null, render: function(data, type, row) { return row[9]; } }, // Category
            { data: null, render: function(data, type, row) { return row[10]; } }, // Sub Category
            { data: null, render: function(data, type, row) { return row[11]; } }, // Department
            { data: null, render: function(data, type, row) { return row[12]; } }, // County
            { data: null, render: function(data, type, row) { return row[13]; } }, // Sub County
            { data: null, render: function(data, type, row) { return row[14]; } }, // Priority/Non Priority
            // { data: null, render: function(data, type, row) { return row[15]; } }, // Remark
            { 
                data: null,
                render: function(data, type, row) {
                    var remark = row[15] || '';
                    var truncated = remark.length > 50 ? remark.substring(0, 50) + '…' : remark;
                    return `<span title="${$('<div>').text(remark).html()}">${truncated}</span>`;
                }
            } // Remark
        ],
        "ajax": {
            url: "Report/report_function.php",
            type: "POST",
            data: function(d) {
                d.fname = fname;
                d.casee = casee;
                d.agent = agent;
                d.startdatetime = startdatetime;
                d.enddatetime = enddatetime;
                d.status = status;
                d.vProject = vProject;
                d.category = category;
                d.subcategory = subcategory;
                d.district = district;
                d.village = village;
                d.comp = comp;
                d.source = source;
                d.casetype = $('input[name="casetype"]:checked').val(); // Fetch selected casetype value from radio buttons
                d.priority = priority;
                d.customertype = customertype;
                d.esc_status = esc_status;
                d.action = 'view_case_reportt';
            }
        }
        
    });

    

    $('#casee_data tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = $('#casee_data').DataTable().row(tr);
        if (row.child.isShown()) {
            // Close the child row
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Get ticket ID from the first column (adjust index if needed)
            var ticketid = tr.find('a').text().trim();
    
            // Fetch child data
            $.ajax({
                url: 'Report/fetch_case_timelines.php', // Your PHP file path
                type: 'POST',
                data: { ticketid: ticketid },
                success: function (data) {
                    row.child(data).show();
                    tr.addClass('shown');
                },
                error: function () {
                    row.child('<div style="padding:10px;">Unable to load data.</div>').show();
                }
            });
        }
    });
    

     // ✅ Attach the XHR event listener here, after DataTable is initialized[vastvikta][18-04-2025]for total records 
    $('#casee_data').on('xhr.dt', function(e, settings, json, xhr) {
       if (json && json.recordsTotal !== undefined) {
            $('#recordTopValue').text(json.recordsTotal);
            $('#recordBottomValue').text(json.recordsTotal);
        }
    });
}

// Store initial values of date inputs when document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
console.log("hello oo date start",initialStartDate);
    // Initialize DataTable to set time out to  call function just upon  reloading [vastvikta][16-12-2024]
   setTimeout(function() {
    case_datatable();
    }, 1000);  // Calls case_datatable() every 1 second

});

// Submit button click handler
$('#submit_casee').click(function() {

    // Destroy DataTable instance to reinitialize with new data
    $('#casee_data').DataTable().destroy();
    
    // Call case_datatable function with appropriate filter parameters
    case_datatable();
});

// Reset button click handler
$('#reset_casee').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);

    // Clear other filter inputs and reload DataTable
    $('#casee_data').DataTable().destroy();
    $('#fname, #casee, #agent, #status, #vProject, #category, #subcategory, #district, #village, #comp, #source, #customertype,#esc_status').val('');

    // Clear radio buttons for priority and casetype
    $('input[name="priority"]').prop('checked', false);
    $('input[name="casetype"]').prop('checked', false);

    // Reload DataTable
    case_datatable();
});



// report-> customer page datatable with filter
   customer_datatable();
   function customer_datatable(district = '', village = '', startdatetime = '', enddatetime='', phone = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
   
      var dataTable = $('#customer_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename:  'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Customers Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  district:district, 
                  village:village,
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  phone:phone,
                  action:'view_customer_report'
            }
         }
      });
   }
  // Store initial values of date inputs when document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_cust').click(function(){
    var district = $('#district').val();
    var village = $('#village').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var phone = $('#phone').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#customer_data').DataTable().destroy();

    // Call customer_datatable function with appropriate filter parameters
    customer_datatable(district, village, startdatetime, enddatetime, phone);
});

$('#reset_cust').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);

    // Clear other filter inputs and reload DataTable
    $('#customer_data').DataTable().destroy();
    $('#district, #village, #phone').val('');

    // Reload DataTable
    customer_datatable();
});

// Report-> report overview page data table
// added status field [vastvikta][16-05-2025]
   overview_datatable();
   function overview_datatable(startdatetime = '', enddatetime='',status = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
    
      var dataTable = $('#overview_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Case Overview Report  FROM ' + startDateOnly + ' TO ' + endDateOnly, // Specify the desired filename here
                    title: 'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename :'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Case Overview Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime, 
                  enddatetime:enddatetime,
                  status:status,
                  action:'view_report_overview'
            }
         }
      });
   }
// Store initial values of date inputs when document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#startdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_overview').click(function(){
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var status = $('#status').val();
    // Destroy DataTable instance to reinitialize with new data
    $('#overview_data').DataTable().destroy();
    // Call overview_datatable function with appropriate filter parameters
    overview_datatable(startdatetime,enddatetime,status);
});

$('#reset_overview').click(function() {
    // Reset date inputs to initial values
    $('#startdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    $('#status').val(status);
    // Destroy DataTable instance and reload with default data
    $('#overview_data').DataTable().destroy();
    overview_datatable();
});
// Report-> audit page datatable with filter
   audit_datatable();
   function audit_datatable(agent = '', startdatetime = '', enddatetime = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
   
      var dataTable = $('#audit_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout

         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    customize: function ( doc ) {
                        var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 5 ],
                            alignment: 'left', image: 'logo',  
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
                    title: 'Audit Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  agent:agent, 
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  action:'view_audit_report'
            }
         }
      });
   }
  // Store initial values of date inputs and agent filter when document is ready
var initialStartDate;
var initialEndDate;
var initialAgent;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
    initialAgent = $('#agent').val();
});

$('#submit_audit').click(function(){
    var agent = $('#agent').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#audit_data').DataTable().destroy();

    // Call audit_datatable function with appropriate filter parameters
    audit_datatable(agent, startdatetime, enddatetime);
});

$('#reset_audit').click(function() {
    // Reset date inputs and agent filter to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    $('#agent').val(initialAgent);

    // Destroy DataTable instance and reload with default data
    $('#audit_data').DataTable().destroy();
    audit_datatable();
});

// Report-> Frequent caller page datatable with filter
   fc_datatable();
   function fc_datatable(timeperiod = '', startdatetime = '', enddatetime = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
   
      var dataTable = $('#fc_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename:  'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title:  'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title:  'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop :  'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title:  'Frequent Caller Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  timeperiod:timeperiod, 
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  action:'view_frequent_caller'
            }
         }
      });
   }
// Store initial values of date inputs and timeperiod filter when document is ready
var initialStartDate;
var initialEndDate;
var initialTimeperiod;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
    initialTimeperiod = $('#timeperiod').val();
});

$('#submit_caller').click(function(){
    var timeperiod = $('#timeperiod').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#fc_data').DataTable().destroy();

    // Call fc_datatable function with appropriate filter parameters
    fc_datatable(timeperiod, startdatetime, enddatetime);
});

$('#reset_caller').click(function() {
    // Reset date inputs and timeperiod filter to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    $('#timeperiod').val(initialTimeperiod);

    // Destroy DataTable instance and reload with default data
    $('#fc_data').DataTable().destroy();
    fc_datatable();
});

// Report-> Frequent ticket page datatable with filter
   ft_datatable();
   function ft_datatable(timeperiod = '', startdatetime = '', enddatetime = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
   
      var dataTable = $('#ft_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title:'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Frequent Ticket  Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Frequent Ticket Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  timeperiod:timeperiod, 
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  action:'view_frequent_ticket'
            }
         }
      });
   }
   // This code for submit buttion get filter data
   $('#submit_ticket').click(function(){
      var timeperiod = $('#timeperiod').val();
      var startdatetime = $('#sttartdatetime').val();
      var enddatetime = $('#enddatetime').val();
      // Destroy DataTable instance to reinitialize with new data
      $('#ft_data').DataTable().destroy();
      // Call ft_datatable function with appropriate filter parameters
      ft_datatable(timeperiod,startdatetime,enddatetime);
   });
   // Reset button click code reset filter option
   $('#reset_ticket').click(function(){
      $('#ft_data').DataTable().destroy();
      $('#timeperiod').val('');
      ft_datatable();
   });


// Report-> Customer satis report summary
   csrs_datatable();
   function csrs_datatable(startdatetime = '', enddatetime = '', agent = ''){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val(); 
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
   

      var dataTable = $('#csrss_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title:'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'CSAT-DSAT Summary Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  agent:agent, 
                  action:'view_CSAT_DSAT_summary'
            }
         }
      });
   }
// Store initial values of date inputs and agent filter when document is ready
var initialStartDate;
var initialEndDate;
var initialAgent;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
    initialAgent = $('#agent').val();
});

$('#submit_summary').click(function(){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var agent = $('#agent').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#csrss_data').DataTable().destroy();

    // Call csrs_datatable function with appropriate filter parameters
    csrs_datatable(startdatetime, enddatetime, agent);
});

$('#reset_summary').click(function() {
    // Reset date inputs and agent filter to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    $('#agent').val(initialAgent);

    // Destroy DataTable instance and reload with default data
    $('#csrss_data').DataTable().destroy();
    csrs_datatable();
});

// Report-> Customer satis report detail
   csrd_datatable();
   function csrd_datatable(Type = '', startdatetime = '', enddatetime = '', agent = '', Phone_Number = '', Customer_email = '', source = '',mode =''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
       
        var dataTable = $('#csrd_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    customize: function ( doc ) {
                        var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 5 ],
                            alignment: 'left', image: 'logo',  
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
                    title: 'CSAT-DSAT Detailed Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
            "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                    Type : Type,
                    startdatetime:startdatetime,
                    enddatetime:enddatetime,
                    agent:agent, 
                    Phone_Number:Phone_Number,
                    Customer_email: Customer_email,
                    source: Customer_email,
                    mode:mode,
                    action:'view_CSAT_DSAT_detail'
            }
            }
        });
   }
   // This code for submit buttion get filter data
   $('#submit_detail').click(function(){
      var Type = $('#Type').val();
      var startdatetime = $('#sttartdatetime').val();
      var enddatetime = $('#enddatetime').val();
      var agent = $('#agent').val();
      var Phone_Number = $('#Phone_Number').val();
      var Customer_email = $('#Customer_email').val();
      var source = $('#source').val();
      
      // Destroy DataTable instance to reinitialize with new data
      $('#csrd_data').DataTable().destroy();

      // Call csrd_datatable function with appropriate filter parameters
      csrd_datatable(Type,startdatetime,enddatetime,agent, Phone_Number, Customer_email, source);
   });
// Store initial dates when the page loads
var originalStartDate = $('#sttartdatetime').val();
var originalEndDate = $('#enddatetime').val();

// Adjusted reset button click event
$('#reset_detail').click(function() {
    // Clear the other filter fields
    $('#Type, #agent, #Phone_Number, #Customer_email, #source').val('');

    // Restore the start and end dates to their original values
    $('#sttartdatetime').val(originalStartDate);
    $('#enddatetime').val(originalEndDate);

    // Destroy the DataTable instance and reinitialize without filters
    $('#csrd_data').DataTable().destroy();
    csrd_datatable(); // Calling without parameters to load all data
});

// Customer Effort datatable
   EFFORT_datatable();
   function EFFORT_datatable(startdatetime = '', enddatetime = '', customer_effort = '', category = '', subcategory = '', source = '', mode = ''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
        var dataTable = $('#CEFFORT_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Custome Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,// Specify the desired filename here
                    title: 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Customer Effort Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime, 
                  enddatetime:enddatetime,
                  customer_effort:customer_effort,
                  category:category,
                  subcategory:subcategory,
                  source:source,
                  mode:mode,
                  action:'view_customer_effort_report'
            }
         }
      });
   }
   // Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_effort').click(function(){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var customer_effort = $('#customer_effort').val();
    var category = $('#category').val();
    var subcategory = $('#subcategory').val();
    var source = $('#source').val();
    var mode = $('#mode').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#CEFFORT_data').DataTable().destroy();

    // Call EFFORT_datatable function with appropriate filter parameters
    EFFORT_datatable(startdatetime, enddatetime, customer_effort, category, subcategory, source, mode);
});

$('#reset_effort').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    
    // Clear other filter inputs
    $('#customer_effort, #category, #subcategory, #source, #mode').val('');

    // Destroy DataTable instance and reload with default data
    $('#CEFFORT_data').DataTable().destroy();
    EFFORT_datatable();
});

   //nps report data table
   nps_datatable();
   function nps_datatable(startdatetime = '', enddatetime = '', mode = '', category = '', subcategory = '', source = ''){
    var startdatetime = $('#sttartdatetime').val();
   
    var enddatetime = $('#enddatetime').val();
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
       
      var dataTable = $('#nps_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,// Specify the desired filename here
                    title: 'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'ENSEMBLER \n NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename :'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'NPS Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  mode:mode,
                  category:category,
                  subcategory:subcategory,
                  source:source,                  
                  action:'view_nps_report'
            }
         }
      });
   }
// Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_nps').click(function(){
    var startdatetime = $('#sttartdatetime').val();
   
    var enddatetime = $('#enddatetime').val();
    var mode = $('#mode').val();
    var category = $('#category').val();
    var subcategory = $('#subcategory').val();
    var source = $('#source').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#nps_data').DataTable().destroy();

    // Call nps_datatable function with appropriate filter parameters
    nps_datatable(startdatetime, enddatetime, mode, category, subcategory, source);
});

$('#reset_nps').click(function() {
    // Reset date inputs to initial values
    $('#startdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    
    // Clear other filter inputs
    $('#mode, #category, #subcategory, #source').val('');

    // Destroy DataTable instance and reload with default data
    $('#nps_data').DataTable().destroy();
    nps_datatable();
});
   //voice mail data table
   voicemail_datatable();
   function voicemail_datatable(startdatetime = '', enddatetime='', status = ''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
        
        var dataTable = $('#voice_data').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
                "searching": false,
                "paging": true, // Ensure paging is enabled
                "dom": "lBfrtip", // "p" ensures pagination is included in the layout
            buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'CSV',
                        filename: 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        title: 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        filename: 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        title: 'ENSEMBLER \n Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },{
                        extend: 'pdfHtml5',
                        filename : 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        messageTop : 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
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
                        title: 'Voice Mail Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                        postfixButtons: ['colvisRestore']
                    },
                ],
            "ajax" : {
                url:"Report/report_function.php",
                type:"POST",
                data:{
                    startdatetime:startdatetime,
                    enddatetime:enddatetime,
                    status:status,
                    action:'view_voicemail'
                }
            }
        });
   }
   // This code for submit buttion get filter data
   $('#submit_voice').click(function(){
      var startdatetime = $('#sttartdatetime').val();
      var enddatetime = $('#enddatetime').val();
      var status = $('#status').val();
      // Destroy DataTable instance to reinitialize with new data
      $('#voice_data').DataTable().destroy();
      // Call voicemail_datatable function with appropriate filter parameters
      voicemail_datatable(startdatetime,enddatetime,status);
   });
   // fcr data table
   fcr_datatable();
   function fcr_datatable(startdatetime = '', enddatetime = ''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
       
      var dataTable = $('#fcr_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename:  'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,// Specify the desired filename here
                    title:  'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title:  'ENSEMBLER \nFCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop :  'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'FCR Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  action:'view_fcr_report'
            }
         }
      });
   }

// Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_fcr').click(function(){
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#fcr_data').DataTable().destroy();

    // Call fcr_datatable function with appropriate filter parameters
    fcr_datatable(startdatetime, enddatetime);
});

$('#reset_fcr').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);

    // Destroy DataTable instance and reload with default data
    $('#fcr_data').DataTable().destroy();
    fcr_datatable();
});
   // Web case data table
   Custumcase_datatable();
   function Custumcase_datatable(fname = '', casee = '', agent = '', startdatetime = '', enddatetime = '', status = '', category = '', subcategory = '', comp = '', source = ''){
     
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val(); 
    var startDateOnly = startdatetime.substring(0, 10);
    var endDateOnly = enddatetime.substring(0, 10);
     
      var dataTable = $('#Customecase_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'ENSEMBLER \n Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Custom Case Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  fname:fname,
                  casee:casee,
                  agent:agent,
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  status:status,
                  category:category,
                  subcategory:subcategory,
                  comp:comp,
                  source:source,
                  action:'view_Customecase_report'
            }
         }
      });
   }
// Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_Customecase').click(function(){
    var fname = $('#fname').val();
    var casee = $('#casee').val();      
    var agent = $('#agent').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();      
    var status = $('#status').val();
    var category = $('#category').val();      
    var subcategory = $('#subcategory').val();
    var comp = $('#comp').val();      
    var source = $('#source').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#Customecase_data').DataTable().destroy();

    // Call Custumcase_datatable function with appropriate filter parameters
    Custumcase_datatable(fname, casee, agent, startdatetime, enddatetime, status, category, subcategory, comp, source);
});

$('#reset_Customecase').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    
    // Clear other filter inputs
    $('#fname, #casee, #agent, #status, #category, #subcategory, #comp, #source').val('');

    // Destroy DataTable instance and reload with default data
    $('#Customecase_data').DataTable().destroy();
    Custumcase_datatable();
});

// Disposition datatable
   disposition_datatable();
   function disposition_datatable(fname = '', CallerID = '', type = '', disposition = '', sentiment = '', startdatetime = '', enddatetime = ''){
        var startdatetime = $('#sttartdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var startDateOnly = startdatetime.substring(0, 10);
        var endDateOnly = enddatetime.substring(0, 10);
       
      var dataTable = $('#disposition_data').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
            "searching": false,
            "paging": true, // Ensure paging is enabled
            "dom": "lBfrtip", // "p" ensures pagination is included in the layout
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    title: 'ENSEMBLER \n Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'pdfHtml5',
                    filename : 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    messageTop : 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
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
                    title: 'Disposition Report FROM ' + startDateOnly + ' TO ' + endDateOnly,
                    postfixButtons: ['colvisRestore']
                },
            ],
         "ajax" : {
            url:"Report/report_function.php",
            type:"POST",
            data:{
                  fname:fname,
                  CallerID:CallerID,
                  type:type,
                  disposition:disposition,
                  sentiment:sentiment,
                  startdatetime:startdatetime, 
                  enddatetime:enddatetime,
                  action:'generateReport'
            }
         }
      });
   }
// Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#sttartdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_dis').click(function(){
    var fname = $('#fname').val();
    var CallerID = $('#CallerID').val();
    var type = $('#type').val();
    var disposition = $('#disposition').val();
    var sentiment = $('#sentiment').val();
    var startdatetime = $('#sttartdatetime').val();
    var enddatetime = $('#enddatetime').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#disposition_data').DataTable().destroy();

    // Call disposition_datatable function with appropriate filter parameters
    disposition_datatable(fname, CallerID, type, disposition, sentiment, startdatetime, enddatetime);
});

$('#reset_dis').click(function() {
    // Reset date inputs to initial values
    $('#sttartdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    
    // Clear other filter inputs
    $('#fname, #CallerID, #type, #disposition, #sentiment').val('');

    // Destroy DataTable instance and reload with default data
    $('#disposition_data').DataTable().destroy();
    disposition_datatable();
});

});
// Close Data table code

jQuery(function ($){
    var Report = {
        init: function (){
            jQuery("body").on('change', '#district', this.HandleSubCounty);//function to handle break delete request
            jQuery("body").on('change', '#category', this.HandleCategory);//function to handle break delete request

            // Initialize date time picker
             // $('.date_class').datetimepicker();
             // Function to clear default text in input field on focus
             function clearText(field){
                 if (field.defaultValue == field.value) field.value = '';
                 else if (field.value == '') field.value = field.defaultValue;
             }
        },
        HandleSubCounty: function (e) {
          e.preventDefault();
        // ajax script for getting  subcategory data
            var disID = $(this).val();
            var vilID = $('#village').val();
            // alert(catID);
              $.ajax({
                type:'POST',
                url:'Report/report_function.php',
                data:{'dis_id':disID,'vill_id':vilID,'action':'display_subcounty'},
                success:function(result){
                  $('#village').html(result);
                  
                }
            });
      },
      HandleCategory: function(e){
        e.preventDefault();
        var catID = $(this).val();
        var subID = $('#subcategory').val();
        // alert(catID);
          $.ajax({
            type:'POST',
            url:'helpdesk/web_ticket_function.php',
            data:{'cat_id':catID,'subcat_id':subID,'action':'ajax_subCategory'},
            success:function(result){
              $('#subcategory').html(result);
              
            }
          });
      }
    }
    Report.init();
});
// close
