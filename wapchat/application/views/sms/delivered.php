<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->
<style>
 .link{
   cursor:pointer;
   text-decoration:none;
 }
 .btn:hover{
 background:coral;
 border-color:#fff;
 color:#fff;
 }
 /* .link:hover{
   background:coral;
   color:#fff;
 } */
</style>
        <!-- Content page  -->
       <!-- Content Wrapper. Contains page content -->
  <div class="content">
    <!-- Content Header (Page header) -->
     <?php $this->load->view('breadcrumb') ?>

       <!-- Main -->
    <section class="content">
       <div class="container-fluid">
      <div class="row">
      <!-- sms menu -->
      <?php  $this->load->view('layout/smsmenu'); ?>
      <!-- sms menu End  --> 


        <!-- DELIVERED -->
        <div class="col-md-10 delivered" style="display:block">
          <div class="card card-info card-outline">
            <div class="card-header">
              <h3 class="card-title">Delivered</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              
              <div class="table-responsive mailbox-messages">
                  <table class="table table-hover table-bordered table-striped table-sm" id="sent-server-side">
                    <thead>
                      <tr>
                      <th>#</th>
                        <th>Send To</th>
                        <th>Message</th>
                        <th>Date Time</th>
                      </tr>
                    </thead>
                  </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
  
          </div>
          <!-- /.card -->
        </div>
        <!-- END DELIVERED -->


      </div>
      <!-- /.row -->
    </div>
    </section>

<!-- end main -->


     

    

    
<?php  $this->load->view('layout/footer'); ?>

<script type="text/javascript">
$(document).ready(function()
{
  $("#sent-server-side").DataTable({
        "searching": false,
        "responsive": true, 
        "pageLength": 10, 
        "lengthChange": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,

        "ajax":{ 
        "url": "<?php echo site_url('smsbox/get_basic_records') ?>",
        "dataType": "json",
        "type": "POST",
        "data" : {report_name : '<?php echo $report_name; ?>',
          report_in_out : '<?php echo $report_in_out ?>',
          user_wise : '<?php echo $user_wise ?>',
          box_name : '<?php echo $box_name ?>',
          status : '<?php echo $status ?>'
        }
      },
          "columns": [
          { "data": "#" },
          { "data": "send_to" },
          { "data": "message" },
          { "data": "create_date" },
          ],
          "columnDefs": [{
          targets: "_all",
          orderable: false
          }],
          "dom": "Bfrtip",
          "buttons": [ {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i>',
                titleAttr: 'Excel',
              
                title: $('.download_label').html(),
                exportOptions: {
                    columns: ':visible'
                }
                }, {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },],

  });
});
</script>