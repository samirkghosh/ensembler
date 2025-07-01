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
            <div class="card-header">
              <form name="report_search" id="report_search" autocomplete="on" action="<?php echo site_url('smsbox/sent') ?>"  method="post" >
                <div class="row">
                  <div class="col-md-10">
                    <div class="row">
                    
                                       
                    <div class="col-5">
                      <!--Filter-->
                      <div class="row">
                        
                        
                      
                        <div class="col-md-4">Start Date
                          <input type="text" name="from_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($from_date)?$from_date: '' ?>" placeholder="Select Start Date" autocomplete="off">
                        </div>
                      
                        <div class="col-md-4">End Date
                          <input type="text" name="end_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($end_date)?$end_date:'' ; ?>" placeholder="Select End Date" autocomplete="off">
                        </div>
                        <div class="col-md-4">Campaign wise              
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
                         
                      </div>
                    </div>
                    
                    <div class="col-7">
                      <!--  Show Advance Filter Options -->
                        <div class="row" id="show_advance_filter" style="display:none" >
                          

                          <div class="col-md-3">User wise              
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

                          <div class="col-md-2"> Type          
                            <select name="message_type" class="form-control form-control-sm">
                              <option value="all" <?php echo ($message_type=='all')?'selected':'' ?> >All</option>
                              <option value="0" <?php echo ($message_type=='0')?'selected':'' ?> >Direct</option>
                              <option value="1" <?php echo ($message_type=='1')?'selected':'' ?> >Bulk</option>
                              <option value="2" <?php echo ($message_type=='2')?'selected':'' ?> >Appication</option>
                              <option value="3" <?php echo ($message_type=='3')?'selected':'' ?> >EmailToSMS</option>
                            </select>
                          </div>

                          <div class="col-md-2">Status              
                            <select name="status" class="form-control form-control-sm">
                              <option value="all"  <?php echo ($status=='all')?'selected':'' ?> >All</option>
                              <!-- <option value="0"  <?php echo ($status=='0')?'selected':'' ?> >Queue</option>
                              <option value="1"  <?php echo ($status=='1')?'selected':'' ?> >Submitted</option> -->
                              <option value="2"  <?php echo ($status=='2')?'selected':'' ?> >Delivered</option>
                              <option value="3"  <?php echo ($status=='3')?'selected':'' ?> >Un delivered</option>
                              
                            </select>
                          </div>
                        </div>
                    </div>
                     </div>
                  </div>
                  <div class="col-md-2 text-right" style="margin-top: -10px;">
                    <div class="btn-group-horizontal">
                      <button type="submit" class="btn btn-info btn-sm" title="Apply Filter"><i class="far fa-paper-plane"></i>Send</button>
                      <button type="button" id="advanced" name="advanced" class="btn btn-sm" title="Advanced Filter"><i class="fas fa-filter"></i>Filter</button>
                      <button type="button"  id="reset" name="reset" class="btn btn-sm" title="Reset Filters"><i class="fas fa-power-off"></i>Reset</button>
                      <!-- <a href="#" class=" btn-block popover_ss" > <i class="fas fa-info-circle"></i> </a> -->
                    </div>
                  </div>


                </div>
              </form>
         
            </div>
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
                      <th>Status</th>
                      <th>Type</th>
                      <th>DateTime</th>
                      <th>Schedule</th>
                      <th>Remark</th>
                      <th>Created By</th>
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
    
      </div><!-- /.container-fluid -->
    </section>
    <!-- end main -->

    <div class="modal fade" id="show_message">
      <div class="modal-dialog">
        <div class="modal-content bg-default">
          <!-- <div class="modal-header">
            <h4 class="modal-title">Info Modal</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div> -->
          <div class="modal-body" id="show_message_text">
            
          </div>
          <div class="modal-footer justify-content-between">
            <!-- <a id="replay_button" href="" class="" title="Reply" style="color:coral" ><i class="fas fa-reply"></i></a> -->
            <button type="button" class="btn btn-outline-light btn-sm "   data-dismiss="modal">Close</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
  </div>
    
<?php  $this->load->view('layout/footer'); ?>


<script type="text/javascript">
   $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30 
  });

  $("#reset").on('click', function(){
    window.location.href = '<?php echo site_url('smsbox/sent') ?>';
  });

  var base_url = '<?php echo base_url('smsbox/new_message/') ?>';

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


   function show_full_message(elem, mobile) {
    
    $("#show_message_text").html('<p style="word-break: break-word;" >'+elem.id+'</p>');
    // $("#replay_button").attr('href', base_url+mobile);
    $("#show_message").modal('show');
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
        "url": "<?php echo site_url('smsbox/get_basic_records') ?>",
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
          box_name : '<?php echo $box_name ?>',
        }
      },
      "columns": [
        { "data": "#" },
        { "data": "send_to" },
        { "data": "name" },
        { "data": "message" },
        { "data": "status" },
        { "data": "message_type" },
        { "data": "create_date" },
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