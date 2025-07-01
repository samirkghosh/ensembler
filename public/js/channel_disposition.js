//Disposition report data table
Disposition_datatable();
function Disposition_datatable(startdatetime = '', enddatetime = '', disposition = '', phone_number ='', caller_name = '', channel_type = '', email = '',sentiment =''){
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
   
      var dataTable = $('#disposition_data').DataTable({
         "processing": true,
             "serverSide": true,
             "order": [],
             "pageLength": 10,
             "lengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],
             "searching": false,
             "paging": true, // Ensure paging is enabled
             "dom": "lBfrtip",
         buttons: [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'csv', // Specify the desired filename here
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Excel', // Specify the desired filename here
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
         "ajax" : {
            url:"omnichannel_config/disposition_report_function.php",
            type:"POST",
            data:{
                  startdatetime:startdatetime,
                  enddatetime:enddatetime,
                  disposition:disposition, 
                  phone_number:phone_number,
                  caller_name:caller_name,
                  channel_type:channel_type,
                  email:email, 
                  sentiment:sentiment,               
                  action:'Disposition_Report'
            }
         }
      });
   }
// Store initial values of date inputs when the document is ready
var initialStartDate;
var initialEndDate;

$(document).ready(function() {
    // Store initial values
    initialStartDate = $('#startdatetime').val();
    initialEndDate = $('#enddatetime').val();
});

$('#submit_disposition').click(function(){
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var disposition = $('#disposition').val();
    var phone_number = $('#phone_number').val();
    var caller_name = $('#caller_name').val();
    var channel_type = $('#channel_type').val();
    var sentiment = $('#sentiment').val();
    var email = $('#email').val();
    // var category = $('#category').val();
    // var subcategory = $('#subcategory').val();
    // var source = $('#source').val();
    
    // Destroy DataTable instance to reinitialize with new data
    $('#disposition_data').DataTable().destroy();

    // Call disposition_data function with appropriate filter parameters
    Disposition_datatable(startdatetime, enddatetime, disposition, phone_number, caller_name, channel_type, email,sentiment);
});

$('#reset_disposition').click(function() {
    // Reset date inputs to initial values
    $('#startdatetime').val(initialStartDate);
    $('#enddatetime').val(initialEndDate);
    
    // Clear other filter inputs
     $('#disposition').val('');
     $('#phone_number').val('');
     $('#caller_name').val('');
     $('#channel_type').val('');
     $('#email').val('');
     $('#sentiment').val();
    // Destroy DataTable instance and reload with default data
    $('#disposition_data').DataTable().destroy();
    Disposition_datatable();
});