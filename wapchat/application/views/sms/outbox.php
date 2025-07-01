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
       
      
        <!-- SENT -->
        <div class="col-md-10 sent" style="display:block">
          <div class="card card-info card-outline">
           
            <!-- /.card-header -->
            <div class="card-body">
              
              <div class="table-responsive mailbox-messages">
              <table class="table table-sm" id="sent-server-side">
                  <thead>
                    <tr>
                      <th>#</th>                        
                      <th>To</th>
                      <th>Name</th>
                      <th>Message</th>
                      <th>DateTime</th>
                      <th>Status</th>
                      <th>Schedule</th>
                      <th>Remark</th>
                      <th>Action By</th>
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
        <!-- END SENT -->

      </div>
    

<!-- end main -->


      </div><!-- /.container-fluid -->
    </section>

    <div class="modal fade" id="show_message">
  <div class="modal-dialog">
    <div class="modal-content bg-default">
      <!-- <div class="modal-header">
        <h4 class="modal-title">Info Modal</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
      <div class="modal-body">
        <p id="show_message_text"></p>
      </div>
      <div class="modal-footer justify-content-between">
        <!-- <a id="replay_button" href="" class="" title="Reply" style="color:coral" ><i class="fas fa-reply"></i></a> -->
        <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

    
<?php  $this->load->view('layout/footer'); ?>


<script type="text/javascript">
   var base_url = '<?php echo base_url('smsbox/new_message/') ?>';
   function show_full_message(elem, mobile, row_id) {
    
    $("#show_message_text").text(elem.id);
    // $("#replay_button").attr('href', base_url+mobile);
    $("#show_message").modal('show');

      $.ajax({
      url:"<?php echo base_url(); ?>smsbox/read_inbox_message",
      method:"POST",
      data:{'row_id': row_id, 'message_type' : 'outbox' },
      dataType : 'json',
      success:function(data, textStatus, jqXHR){
        console.log(data);
        console.log(data.res.sent_by);
        $("#sent_by").empty();
        $("#replied_by").empty();
        $("#replied_date").empty();
        $("#content").empty();

        $("#sent_by").html(data.res.sent_by);
        $("#replied_by").html(data.res.replied_by);
        $("#replied_date").html(data.res.replied_date);
        $("#content").html(data.res.content);
        /*console.log('DATA');
        console.log(data);*/
        setTimeout(function() {
          // window.location.reload();
        },5000);                                 
      },
      error:function(jqXHR, textStatus, errorThrown){
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
      }
   });

  }

    
  
$(document).ready(function()
{
   $("#sent-server-side").DataTable({
      "searching": false,
      "responsive": false, 
      "pageLength": 100, 
      "lengthChange": false,
      "autoWidth": false,
      "processing": true,
      "serverSide": true,

      "ajax":{ 
        "url": "<?php echo site_url('smsbox/outbox_record') ?>",
        "dataType": "json",
        "type": "POST",
        "data" : {report_name : '<?php echo $report_name; ?>',
          report_in_out : '<?php echo $report_in_out ?>',
         
          box_name : '<?php echo $box_name ?>',
        }
      },
      "columns": [
        { "data": "#" },
        { "data": "send_to" },
         { "data": "name" },
        { "data": "message" },
        { "data": "create_date" },
        { "data": "status" },
        { "data": "schedule" },
        { "data": "remark" },
        { "data": "action_by" },
        ],
        "columnDefs": [{
          targets: "_all",
          orderable: false
        }],
    });
});
</script>