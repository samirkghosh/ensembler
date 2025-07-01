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
              <form name="report_search" id="report_search" autocomplete="on" action="<?php echo site_url('reports/whatsapp_report') ?>"  method="post" >
             
            <div class="row">

              <div class="col-md-10 col-sm-12">
              
                <!-- All Filters -->
                <div class="row">

                    <div class="col-sm-6">
                      <!--Filter-->
                      <div class="row">
                    
                        <div class="col-md-4">Start Date :
                          <input type="text" name="from_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($from_date)?$from_date: '' ?>" placeholder="Select Start Date" autocomplete="off">
                        </div>
                      
                        <div class="col-md-4">End Date :
                          <input type="text" name="end_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($end_date)?$end_date:'' ; ?>" placeholder="Select End Date" autocomplete="off">
                        </div>

                        <div class="col-md-4">Agent :
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

                      </div>

                    </div>
            
                </div>

              </div>

              <!-- Buttons -->
              <div class="col-md-2 col-sm-6" style="margin-top: 0px;">
                <div class="btn-group-horizontal">
                  <button type="submit" class="btn btn-info btn-sm" title="Apply filter"><i class="far fa-paper-plane"></i>Send</button>
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
                      <th>To</th>
                      <th>From</th>
                      <th>Name</th>
                      <th>Bot Session ID</th>
                      <th>Agent Name</th>
                      <th>Bot / Agent</th>
                      <th>Date</th>
          
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


<script>

  function show_full_message(chat_session,mobile) {

    var href = '<?php echo site_url("reports/view_conversation")?>/'+chat_session+'/'+mobile;
    window.open(href,'_blank')

  }
  
   $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i:s',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30 ,
    
  });


  $("#reset").on('click', function(){
    window.location.href = '<?php echo site_url('reports/whatsapp_report') ?>';

  }); 


$(function () {
  $('[data-toggle="tooltip"]').tooltip();

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
        "url": "<?php echo site_url('reports/get_wa_records') ?>",
        "dataType": "json",
        "type": "POST",
        "data" : {report_name : '<?php echo $report_name; ?>',
                  report_in_out : '<?php echo $report_in_out ?>',
                  from_date : '<?php echo $from_date ?>',
                  end_date : '<?php echo $end_date ?>',
                  user_wise : '<?php echo $user_wise ?>'
                }
    }, 
    "columns": [
        { "data": "#" },
        { "data": "to" },
        { "data": "from" },
        { "data": "content_text" },
        { "data": "chat_session" },
        { "data": "user" },
        { "data": "bot_agent_flag" },
        { "data": "session_start_time" }
        
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
});

//Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);
</script>

