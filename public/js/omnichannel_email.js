
//script code shifted from web_email_complaint.php 
    // initialize_datatables();
function initialize_datatables(startdatetime = '', enddatetime = '', email = '', iallstatus = '', classification = '', sentiment = '', ICASEID = '') {
    var id = $('#inte_id').val();

    var startdatetime = $('#startdatetime').val();
            var enddatetime = $('#enddatetime').val();
            
            console.log(startdatetime);
            console.log(enddatetime);
    var dataTable = $('#email_complaint_table').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "destroy": true,  // Allows DataTable to be reinitialized
        "ajax": {
            url: "omnichannel_config/fetchDataEmailComplaint.php",
            type: "POST",
            data: function(d) {
                // Pass all filters, including ICASEID, to the server-side script
                d.startdatetime = startdatetime;
                d.enddatetime = enddatetime;
                d.email = email;
                d.iallstatus = iallstatus;
                d.classification = classification;
                d.sentiment = sentiment;
                d.ICASEID = ICASEID;  // Ensure ICASEID is passed correctly
                d.id = id;
                d.action = 'email_complaint';
            }
        },
        "createdRow": function(row, data, dataIndex) {
            // Apply row classes based on the value in the color field
            var clr = data[8];  // Assuming the color field is at index 9
            if (clr == 'yellow') {
                // $(row).addClass('flag-yellow');
            } else if (clr == 'red') {
                $(row).addClass('mail-row');
            } else if (clr == 'green') {
                // $(row).addClass('flag-green');
            }
        }
    });

    return dataTable;
}
    function initialize_datatables_enquiry(startdatetime = '', enddatetime = '', email = '', iallstatus = '',classification = '' , sentiment = '', ICASEID ='') {
        var startdatetime = $('#startdatetime').val();
            var enddatetime = $('#enddatetime').val();
            
        var dataTable = $('#email_enquiry_table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
            "searching": false,
            "ajax": {
                url: "omnichannel_config/fetchDataEmailComplaint.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = startdatetime;
                    d.enddatetime = enddatetime;
                    d.email = email;
                    d.iallstatus = iallstatus;
                    d.classification = classification;
                    d.sentiment = sentiment;
                    d.ICASEID = ICASEID;
                    d.action = 'email_enquiry';
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[8]; // Assuming clr value is in the nine column
               
                if (clr == 'yellow') {
                    // $(row).addClass('flag-yellow');
                } else if (clr == 'red') {
                    $(row).addClass('mail-row');
                } else if (clr == 'green') {
                    // $(row).addClass('flag-green');
                }
            }
        });
        return dataTable;
    }   
    $(document).ready(function() {
        // Ths code for datatable reload fetch lates data
        // Set an interval to refresh data every 2 seconds

        // Initialize the DataTable on page load
        // var dataTable = initialize_datatables();
        initialize_datatables();
        // Set an interval to refresh data every 9 seconds
        setInterval(function() {
            // Get the current page info before reloading
            var dataTable = $('#email_complaint_table').DataTable();
            var pageInfo = dataTable.page.info();

            // Reload the data without resetting the paging
            dataTable.ajax.reload(null, false);

            // After the reload, set the page back to the original page
            dataTable.page(pageInfo.page).draw(false);
        }, 20000); // 9000 milliseconds = 9 seconds

    

        $('#post_complaint').submit(function(e) {
            e.preventDefault();
            var startdatetime = $('#startdatetime').val();
            var enddatetime = $('#enddatetime').val();
            var email = $('#email').val();
            var iallstatus = $('#iallstatus').val();
            var classification = $('.classification').val();
            var sentiment = $('#sentiment').val();
            var ICASEID = $('#ICASEID').val();
            // Destroy DataTable instance to reinitialize with new data
            $('#email_complaint_table').DataTable().destroy();
        
            // Call initialize_datatables function with appropriate filter parameters
            initialize_datatables(startdatetime, enddatetime, email, iallstatus ,classification ,sentiment,ICASEID);
        });
        
        $('#reset_button_complaint').click(function () {
            $('#email_complaint_table').DataTable().destroy();
            $('#startdatetime,#enddatetime,#email,#iallstatus,.classification','#sentiment', '#ICASEID').val('');
            initialize_datatables();
        });
        // Initialize the DataTable on page load
        // var dataTable = initialize_datatables_enquiry();
        initialize_datatables_enquiry();
        // Set an interval to refresh data every 9 seconds
        setInterval(function() {
            // Get the current page info before reloading
            var dataTable_Inquiry = $('#email_enquiry_table').DataTable();
            var pageInfo = dataTable_Inquiry.page.info();
            // Reload the data without resetting the paging
            dataTable_Inquiry.ajax.reload(null, false);
            // After the reload, set the page back to the original page
            dataTable_Inquiry.page(pageInfo.page).draw(false);
        }, 20000); // 9000 milliseconds = 9 seconds

        
        $('#post_enquiry').submit(function(e) {
            e.preventDefault();
            var startdatetime = $('#startdatetime').val();
            var enddatetime = $('#enddatetime').val();
            var email = $('#email').val();
            var iallstatus = $('#iallstatus').val();
            var classification = $('.classification').val(); 
            var sentiment = $('#sentiment').val();
            var ICASEID = $('#ICASEID').val();
        
            console.log(sentiment);
            // Destroy DataTable instance to reinitialize with new data
            $('#email_enquiry_table').DataTable().destroy();
            // Call fill_datatables function with appropriate filter parameters
            initialize_datatables_enquiry(startdatetime, enddatetime, email, iallstatus ,classification ,sentiment,  ICASEID);
        });
        
        $('#reset_button_enquiry').click(function () {
            $('#email_enquiry_table').DataTable().destroy();
            $('#startdatetime,#enddatetime,#email,#iallstatus,.classification','#sentiment', '$ICASEID').val('');
            initialize_datatables_enquiry();
        });
    });
