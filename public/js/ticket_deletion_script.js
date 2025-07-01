// [vastvikta][19-12-2024]
//handles  submission of ticket deletion form data
var jq3 = $.noConflict(true); // jq3 will be used for the latest jQuery

  // Now use jq3 for DataTable and other functionalities that need jQuery 3.x
  jq3(document).ready(function() {
    var ticketReport;

    // Function to initialize and fill the messenger report data table
    function fill_datatables_ticket_deletion_report(startdatetime = '', enddatetime = '', ticketid = '', i_CreatedBY = '') {
     
        ticketReport = $('#ticket_deletion_report').DataTable({
            "processing":false,
            "serverSide": true,
            "order": [],
            "pageLength": 25, 
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "paging": true, 
            "dom": "lBfrtip", 
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
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                ,{
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
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                }
            ],
            "ajax": {
                url: "omnichannel_config/ticket_deletion_data.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = startdatetime || $('#startdatetime').val();
                    d.enddatetime = enddatetime || $('#enddatetime').val();
                    d.ticketid = ticketid || $('#ticketid').val();
                    d.i_CreatedBY = i_CreatedBY || $('#i_CreatedBY').val();
                    d.action = 'ticket_deletion_report';
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                }
            }
        });
    }

    // Call function to initialize DataTable
    fill_datatables_ticket_deletion_report();

    // Refresh DataTable periodically
    setInterval(function() {
        if (ticketReport) {
            ticketReport.ajax.reload(null, false); 
        }
    }, 10000);
    jq3("#run_report").click(function() {
        $('#ticket_deletion_report').DataTable().destroy();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var ticketid = $('#ticketid').val();
        var i_CreatedBY = $('#i_CreatedBY').val();

        // Call the function with the values from input fields
        fill_datatables_ticket_deletion_report(startdatetime, enddatetime, ticketid, i_CreatedBY);
    });
});
