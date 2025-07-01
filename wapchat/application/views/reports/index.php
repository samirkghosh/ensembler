<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>

<!-- Header End  -->
<style>
table {
    /*table-layout:fixed;*/
}
td{
    /*overflow:hidden;*/
    /*text-overflow: ellipsis;*/
}

.btn {
  border: 0.5px solid coral;
  background-color: white;
  color: black;
  cursor: pointer;
}
.btn:hover{
  background-color:coral;
  border-color:coral;
  color:#fff;

}
</style>

<!-- Main Sidebar Container -->
<?php  ///$this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

  <!-- Content page  -->
 <!-- Content Wrapper. Contains page content -->
  <div class="content">
    <!-- Content Header (Page header) -->
    <?php $this->load->view('breadcrumb') ?>

    <!-- Main content -->
    <section class="content">
      
    <?php $this->load->view('message') ?>
    <div class="container-fluid">
      <div class="row">
         
        <!-- /.col -->
        <div class="col-md-12">
          <div class="card card-info card-outline">
            <div class="card-header">
              <form name="report_search" id="report_search" autocomplete="on" action="<?php echo site_url('reports') ?>"  method="post" >
             
            <div class="row">

              <div class="col-md-10 col-sm-12">
              
                <!-- All Filters -->
                <div class="row">

                    <div class="col-sm-6">
                      <!--Filter-->
                      <div class="row">
                        
                        <div class="col-md-2"> Report :             
                          <select name="report_name" class="form-control form-control-sm">
                            <option value="sms" <?php echo ($report_name=='sms')?'selected':'selected' ?> >SMS</option>
                            <option value="whatsapp" <?php echo ($report_name=='whatsapp')?'selected':'' ?>>Whatsapp</option>
                          </select>
                        </div>

                        <div class="col-md-2"> IN/OUT :             
                          <select name="report_in_out" class="form-control form-control-sm">
                            <option value="out" <?php echo ($report_in_out=='out')?'selected':'selected' ?> >OUT</option>
                            <option value="in" <?php echo ($report_in_out=='in')?'selected':'' ?>>IN</option>
                          </select>
                        </div>
                      
                        <div class="col-md-4">Start Date :
                          <input type="text" name="from_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($from_date)?$from_date: '' ?>" placeholder="Select Start Date" autocomplete="off">
                        </div>
                      
                        <div class="col-md-4">End Date :
                          <input type="text" name="end_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($end_date)?$end_date:'' ; ?>" placeholder="Select End Date" autocomplete="off">
                        </div>

                      </div>

                    </div>
                   
                    <div class="col-sm-6">
                      
                      <!--  Show Advance Filter Options -->
                        <div class="row" id="show_advance_filter" style="display:none" >
                          <div class="col-md-3">Campaign wise :              
                            <select name="list_wise" class="form-control form-control-sm">
                              <option value="all">All</option>
                              <?php if(count($lists) >0 ): 
                              foreach ($lists as $key => $list): ?>
                              <option value="<?=$list['file_name']?>" <?php echo ($list_wise==$list['file_name'])?'selected':'' ?> ><?=$list['list_name']?></option>
                              <?php endforeach;
                              endif;
                              ?>
                            </select>
                          </div>

                          <div class="col-md-3">User wise :              
                            <select name="user_wise" class="form-control form-control-sm">
                              <option value="all">All</option>
                              <?php if(count($users) >0 ): 
                              foreach ($users as $key => $user): ?>
                              <option value="<?=$user->id?>" <?php echo ($user_wise==$user->id)?'selected':'' ?>><?=$user->username?></option>
                              <?php endforeach;
                              endif;
                              ?>
                            </select>
                          </div>

                          <div class="col-md-2"> Scheduled             
                            <select name="schedule" class="form-control form-control-sm">
                              <option value="all" <?php echo ($schedule=='all')?'selected':'' ?> >All</option>
                              <option value="0" <?php echo ($schedule=='0')?'selected':'' ?> >Scheduled</option>
                              <option value="1" <?php echo ($schedule=='1')?'selected':'' ?> >Un scheduled</option>
                            </select>
                          </div>

                          <div class="col-md-2"> Type :          
                            <select name="message_type" class="form-control form-control-sm">
                              <option value="all" <?php echo ($message_type=='all')?'selected':'' ?> >All</option>
                              <option value="0" <?php echo ($message_type=='0')?'selected':'' ?> >Direct</option>
                              <option value="1" <?php echo ($message_type=='1')?'selected':'' ?> >Bulk</option>
                              <option value="2" <?php echo ($message_type=='2')?'selected':'' ?> >Application</option>
                              <option value="3" <?php echo ($message_type=='3')?'selected':'' ?> >Email to SMS</option>
                            </select>
                          </div>

                          <div class="col-md-2">Status :              
                            <select name="status" class="form-control form-control-sm">
                              <option value="all"  <?php echo ($status=='all')?'selected':'' ?> >All</option>
                              <option value="0"  <?php echo ($status=='0')?'selected':'' ?> >Queue</option>
                              <option value="1"  <?php echo ($status=='1')?'selected':'' ?> >Submitted</option>
                              <option value="2"  <?php echo ($status=='2')?'selected':'' ?> >Delivered</option>
                              <option value="3"  <?php echo ($status=='3')?'selected':'' ?> >Undelivered</option>
                              
                            </select>
                          </div>


                        </div>

                    </div>

                </div>

              </div>

              <!-- Buttons -->
              <div class="col-md-2 col-sm-6" style="margin-top: 0px;">
                <div class="btn-group-horizontal">
                  <button type="submit" class="btn btn-info btn-sm" title="Apply filter"><i class="far fa-paper-plane"></i>Send</button>
                  <button type="button" id="advanced" name="advanced" class="btn btn-sm" title="Advanced filters"><i class="fas fa-filter"></i>Filter</button>
                  <button type="button"  id="reset" name="reset" class="btn btn-sm" title="Reset filters"><i class="fas fa-power-off"></i>Reset</button>
                  <!-- <a href="#" class=" btn-block popover_ss" > <i class="fas fa-info-circle"></i> </a> -->
                </div>
              </div>



            </div>

            <!-- END of Modified filter -->

               

              </form>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="report-server-side">
                  <thead>
                    <tr>
                      <th>#</th>
                      <?php if($report_in_out =='in'): ?>
                      <th>From</th>
                      <th>Name</th>
                      <th>Message</th>
                      <th>Created Date Time</th>
                      <?php endif;?>
                       
                       
                       
                     




                      <?php if($report_in_out =='out'): ?>
                      <th>Sender ID</th>
                      <th>Name</th>
                      <th>Message</th>
                      <th>PDU</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th>Schedule</th>
                      <!-- <th>Trigger Type</th> -->
                      <th>Created Date Time</th>
                      <th>Remark</th>
                      <th>Created By</th>
                      <?php endif;?>
                      
                      
                      
                    </tr>
                  </thead>
                </table>
              </div>  <!-- /. Responseive close -->
                  
              
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

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<!-- AdminLTE -->
<!-- <script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script> -->


<script>

  function show_full_message(elem, row_id) {
    
    $("#show_message_text").text(elem.id);
    // $("#replay_button").attr('href', base_url+mobile);
    $("#show_message").modal('show');
  }
  
   $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30 
  });

  $(document).ready(function(){
     // $('.popover_ss').popover({title: "<em>Help</em>", content: "<b>Queue</b> : It means in a queue waiting for send gateway<br><b>Submitted</b> : Sent to gateway waiting for response<br><b>Delivered</b> : Sent gateway to customer<br><b> Not delivered</b> : Sent gateway to customer and fail", html: true, placement: "left"});
  });

  $(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});

  $("#advanced").on('click', function(){
    // var st = $("#show_advance_filter").slideToggle();
    var st = $("#show_advance_filter").toggle("slide");
      /*if ($(this).is(':hidden')) {
            // some code when content is hidden
            console.log('is hiddden');
        }
        else {
            // some code when content is shown
            console.log('is show');
        }*/

  });
  $("#reset").on('click', function(){
    // $("#show_advance_filter").show();
    // location.reload();
    window.location.href = '<?php echo site_url('reports') ?>';

  }); 

/*$(function () {
    $("#contacts").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["excel", "pdf","colvis"]
    }).buttons().container().appendTo('#contacts_wrapper .col-md-6:eq(0)');
  });*/

$(function () {
  var inout_report = '<?php echo $report_in_out ?>' ;
  
  if(inout_report =='in'){
     
        $("#report-server-side").DataTable({
          "searching": false,
          "responsive": true, 
          "pageLength": 100, 
          "lengthChange": false,
          "autoWidth": false,
          "processing": true,
          "serverSide": true,
          "searchable" : false,
         /* "columns": [
            { "width": "50%" }
          ],*/ 
          "ajax":{
              "url": "<?php echo site_url('reports/get_basic_records') ?>",
              "dataType": "json",
              "type": "POST",
              "data" : {report_name : '<?php echo $report_name; ?>',
                        report_in_out : '<?php echo $report_in_out ?>',
                        from_date : '<?php echo $from_date ?>',
                        end_date : '<?php echo $end_date ?>',
                        schedule : '<?php echo $schedule ?>',
                        message_type : '<?php echo $message_type ?>',
                        status : '<?php echo $status ?>',
                        list_wise : '<?php echo $list_wise ?>',
                        user_wise : '<?php echo $user_wise ?>',
                      }
          }, 
          "columns": [
              { "data": "#" },
              { "data": "send_to" },
              { "data": "name" },
              { "data": "message" },
            
              { "data": "create_date" }
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

          
          // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
    else{
      $("#report-server-side").DataTable({
        "searching": false,
        "responsive": true, 
        "pageLength": 100, 
        "lengthChange": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "searchable" : false,
       /* "columns": [
          { "width": "50%" }
        ],*/ 
        "ajax":{
            "url": "<?php echo site_url('reports/get_basic_records') ?>",
            "dataType": "json",
            "type": "POST",
            "data" : {report_name : '<?php echo $report_name; ?>',
                      report_in_out : '<?php echo $report_in_out ?>',
                      from_date : '<?php echo $from_date ?>',
                      end_date : '<?php echo $end_date ?>',
                      schedule : '<?php echo $schedule ?>',
                      message_type : '<?php echo $message_type ?>',
                      status : '<?php echo $status ?>',
                      list_wise : '<?php echo $list_wise ?>',
                      user_wise : '<?php echo $user_wise ?>',
                    }
        }, 
        "columns": [
            { "data": "#" },
            { "data": "send_to" },
            { "data": "name" },
            { "data": "message" },
            { "data": "units" },
            { "data": "message_type_flag" },
            { "data": "status" },
            { "data": "schedule" },
            // { "data": "scheduler_flag" },
            { "data": "create_date" },
            { "data": "status_response" },
            { "data": "create_by" },
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

        
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      });
    }

    
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

