$(document).ready(function(){
   fill_datatable();
   function fill_datatable(filter_status = '', filter_priority = '', filter_source = '', filter_priority_user = '', search_ticket = ''){
      var groupid = $("#groupid").val();
      var userid = $("#userid").val();
      
//   console.log("hello");
      var dataTable = $('#customer_data').DataTable({
         "processing" : true,
         "serverSide" : true,
         "ajax" : {
            url:"helpdesk/web_helpdesk_function.php",
            type:"POST",
            data:{
                  filter_status:filter_status, 
                  filter_priority:filter_priority,
                  filter_source:filter_source,
                  filter_priority_user:filter_priority_user,
                  groupid:groupid,
                  userid:userid,
                  search_ticket:search_ticket,
                  action:'helpdesk_list'
            }
         },
         "order" : [],
          "pageLength": 10, // Set default number of records per page to 10
          "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
          "searching" : false,
          dom: "<'row'<'col-sm-12 col-md-10'l><'col-sm-12 col-md-2'B>>" + // Length changing input control and export buttons
             // "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'p>>" + // Filtering input and pagination
             "<'row'<'col-sm-12'tr>>" + // Table
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // Info and pagination
        buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'excel', // Specify the desired filename here
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'csv', // Specify the desired filename here
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
      });
   }
   $('#submit').click(function(){
      var search_ticket = $('#search_ticket').val();
      var filter_status = $('#case_status').val();
      var filter_priority = $('#case_priority').val();
      var filter_source = $('#source').val();
      var filter_priority_user = $('#priority_user').val();
      
      // Destroy DataTable instance to reinitialize with new data
      $('#customer_data').DataTable().destroy();

      // Call fill_datatable function with appropriate filter parameters
      fill_datatable(filter_status, filter_priority, filter_source, filter_priority_user, search_ticket);
   });
   // ticket and customer name search
   $('#ticket_search').click(function(){
      var search_ticket = $('#search_ticket').val();
      var filter_status = $('#case_status').val();
      var filter_priority = $('#case_priority').val();
      var filter_source = $('#source').val();
      var filter_priority_user = $('#priority_user').val();
      // Destroy DataTable instance to reinitialize with new data
      $('#customer_data').DataTable().destroy();
      // Call fill_datatable function with appropriate filter parameters
      fill_datatable(filter_status, filter_priority, filter_source, filter_priority_user, search_ticket);
   });
   // Reset button click code
   $('#reset').click(function(){
      $('#customer_data').DataTable().destroy();
      $('#case_status, #case_priority, #source , #priority_user ,#search_ticket').val('');
      fill_datatable();
   });

});
// Datatable code close
function check_working_status(docket_no, case_id, user_id) {
   console.log('DC ' + docket_no);
   console.log('cas ' + case_id);
   console.log('user ' + user_id);
   if (docket_no != '') {
      $.ajax({
         url: 'helpdesk/process_ajax_request.php',
         type: 'post',
         data: {
            'work_status': docket_no,
            'case_id': case_id,
            'user_id': user_id,
         },
         success: function(data, status) {
            // Redirect to the appropriate page after success
            var encodedToken = btoa('web_case_detail');
            var docket= btoa(docket_no);
            var redirectURL = "helpdesk_index.php?token=" + encodeURIComponent(encodedToken)+"&id=" + encodeURIComponent(docket);
            window.location.href = redirectURL; 
            console.log(redirectURL);                    
         },
         error: function(xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
         }
      });
   }
}

// Code for deletion of a Ticket  using  secure  processes and authorisations Only supervisor can do it.
 function check_delete_action(docket_no, case_id, user_id) {
  console.log('check_delete_action');
    console.log(docket_no);
    $('#delete-popup').fadeIn(); // Show popup
    $('#remark').val(''); // Clear any previous remarks
    $('#delete-popup').data('id', docket_no); // Store ID for deletion
}
$(document).ready(function () {
    // Close Popup
    $('#cancel-delete').on('click', function () {
        $('#delete-popup').fadeOut(); // Hide popup
    });

    // Confirm Delete
    $('#confirm-delete').on('click', function () {
        const id = $('#delete-popup').data('id'); // Get ID
        const remark = $('#remark').val().trim(); // Get Remark

        if (!remark) {
            alert('Please provide a remark before confirming.');
            return;
        }
        // AJAX call to delete record
        $.ajax({
            url: 'helpdesk/web_helpdesk_function.php', // Replace with your server-side URL
            type: 'POST',
            data: { id: id, remark: remark,action:'delete_ticket' },
            success: function (response) {
                alert('Record deleted successfully!');
                $('#delete-popup').fadeOut(); // Hide popup
                 window.location.reload();
                // setTimeout(function () {
                //     location.reload()
                //      window.location.reload();
                // }, 100);
            },
            error: function (xhr, status, error) {
                alert('An error occurred: ' + error);
                console.error(xhr.responseText);
            }
        });
    });
});