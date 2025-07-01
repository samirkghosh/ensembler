<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  $this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

        <!-- Content page  -->
       <!-- Content Wrapper. Contains page content -->
        <div class="content">
          <!-- Content Header (Page header) -->
          <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
         <!--  <div class="col-sm-6">
            <h1>Inbox</h1>
          </div> -->
          <div class="col-sm-6">
            <ol class="breadcrumb ">
              <li class="breadcrumb-item"><a class="link" href="#">Home</a></li>
              <li class="breadcrumb-item active">SMS in Queue</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      
    <? if($this->session->flashdata('fail')){?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <? echo $this->session->flashdata('fail')?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <? }?>
    <? if($this->session->flashdata('success')){?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <? echo $this->session->flashdata('success')?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <? }?>
    <div class="container-fluid">
      <div class="row">
         
        <!-- /.col -->
        <div class="col-md-12">
          <div class="card card-info card-outline">
            <div class="card-header">
              <h3 class="card-title">Contacts</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table table-bordered table-hover" id="contacts">
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>From</th>
                    <th>Send To</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Message Type</th>
                    <th>schedule</th>
                    <th>schedule Time</th>
                    <th>Session ID</th>
                    <th>Response</th>
                  </tr>
                </thead>
                <tbody>
             <? $id=1;
              if(count($sms_reports->result_array())>0 ){
                
               foreach($sms_reports->result_array() as $row):
                 
                ?>
                
                 <tr>
                   <td><?php echo $id++;?></td>
                   <td><?php echo $row['send_from'];?></td>
                   <td><?php echo $row['send_to'];?></td>
                   <td><?php echo $row['message'];?></td>
                   <td><?php echo $row['status']=='0'?'pending':'sent';?></td>
                   <td><?php echo $row['message_type_flag']=='0'?'direct':($row['message_type_flag']=='1'?'bulk':'application');?></td>
                   <td><?php echo $row['scheduler_flag']=='0'?'scheduled':'not scheduled';?></td> 
                   <td><?php echo date('d-m-Y H:i', strtotime($row['scheduled_time']));?></td>    
                   <td><?php echo $row['bulk_session_id'];?></td>   
               
                   
                   <td>
                 
                   </td>
                 </tr>
                <? 
                endforeach;
              }else{ ?>
                 <tr>
                  <td colspan="3">No Record Found</td>
                </tr>
              <? }?>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div>
      <!-- /.Container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<!-- End of content -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer --> 
    <?php $this->load->view('layout/footer');?>
    <!-- End of footer -->

 </div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<!-- AdminLTE -->
<!-- <script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script> -->


<script>

$(function () {
    $("#contacts").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength" : 50,
      "buttons": [ "excel", "colvis"]
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#contacts_wrapper .col-md-6:eq(0)');
  });

// Code for input only numeric value : Mobile No
 $("#inputmobile").on("keypress keyup blur",function (event) {    
           $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
//Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);
</script>

