<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  $this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

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
              <li class="breadcrumb-item active">Whatsapp Conversation</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
      <div class="row">
       
        <!-- /.col -->
        <div class="col-md-12">
          <div class="card card-info card-outline" style="border-color: coral;">
            <div class="card-header">
              <div class="row">
                <div class="col-sm-2">

                  <h3 class="card-title">Whatsapp Conversation</h3>
                </div>
                <div class="col-sm-10">
                    <b><? echo $contactname." - "."+".$mobile." (".$chat_session.")" ?></b>
                </div>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                 <div class="table-responsive">
              <table class="table table-bordered table-hover table-sm" id="conversation">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>To</th>
                    <th>From</th>
                    <th>Name</th>
                    <th>Direction</th>
                    <th>Message</th>
                    <th>Date</th>
                  </tr>
                </thead>

                <tbody>
                  <? $id=0;
                    if(count($conversation)>0 ){
                     foreach($conversation as $row):?>
                     <? $id++;?>
                       <tr>
                         <td><? echo $id;?></td>
                         <td><? echo $row->to;?></td>
                         <td><? echo $row->from;?></td>
                         <td><? echo $contactname;?></td>
                         <td><? echo $row->direction;?></td>
                         <td><? echo $row->content_text;?></td>
                         <td><? echo $row->create_date;?></td>
                         
                       </tr>
                      <? 
                      endforeach;
                    }else{ ?>
                   <tr>
                    <td colspan="5">No Record Found</td>
                  </tr>
                <? }?>
                </tbody>
              
              </table>
               </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
           
          </div>
          <!-- /.card -->
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

<!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->



<script>
$(function () {
  
    $('#conversation').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,

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