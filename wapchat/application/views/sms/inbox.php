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


        <!-- INBOX -->
        <div class="col-md-10 inbox" style="display:block">
          <div class="card card-info card-outline">
            <div class="card-header">
               <form name="report_search" id="report_search" autocomplete="on" action="<?php echo site_url('smsbox/inbox') ?>"  method="post" >
                <div class="row">
                  <div class="col-md-10">
                    <div class="row">
                       
                        <div class="col-md-3">Search Name/Number
                          <input type="text" name="name_number" id="name_number" list="browsers" onkeyup="search_by_name_number(this.value)" class="form-control form-control-sm" value="<?php echo !empty($name_number) ? $name_number : '' ?>" placeholder="Search by name,number" autocomplete="off">
                            <div id="search_result" style="position: absolute; width: 100px; z-index: 999; background:#fff; border:1px solid #fff ;color:black; padding: 5px; width: 208px;display: none;border: 1px solid orange;"> </div>
                          </div>

                        <div class="col-md-3">Start Date
                          <input type="text" name="from_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($from_date)? date('d-m-Y H:i', strtotime($from_date)): '' ?>" placeholder="Select Start Date" autocomplete="off">
                        </div>
                      
                        <div class="col-md-3">End Date
                          <input type="text" name="end_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($end_date)?date('d-m-Y H:i', strtotime($end_date)):'' ; ?>" placeholder="Select End Date" autocomplete="off">
                        </div>

                        <div class="col-md-2">Status              
                          <select name="status" class="form-control form-control-sm">
                            <option value="all"  <?php echo ($status=='all')?'selected':'selected' ?> >All</option>
                            <option value="1"  <?php echo ($status=='1')?'selected':'' ?> >Read</option>
                            <option value="0"  <?php echo ($status=='0')?'selected':'' ?> >Unread</option>
                            <option value="2"  <?php echo ($status=='2')?'selected':'' ?> >Replied</option>
                          </select>
                        </div>                                                                            
                      </div>
                   
                  </div>
                    
                                       
                   
                       
                    
                     
                     
                  <div class="col-md-2 text-right" style="margin-top: -10px; "  >
                    <div class="btn-group-horizontal">
                      <button type="submit" class="btn btn-info btn-sm" title="Apply Filter"><i class="far fa-paper-plane"></i>Send</button>
                      <!-- <button type="button" id="advanced" name="advanced" class="btn btn-sm" title="Advanced Filter"><i class="fas fa-filter"></i></button> -->
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
                  <table class="table  table-sm" id="sent-server-side">
                    <thead>
                      <tr>
                          <th>#</th>                        
                          <th>From</th>
                          <th>Name</th>
                          <th>Message</th>
                          <th>DateTime</th>
                          <th>Reply</th>
                          <th>Replied By</th>
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
        <!-- END INBOX -->


      </div>
      <!-- /.row -->
    </div>
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
      <div class="modal-body">
        <p id="show_message_text"></p>
      </div>
      <div class="modal-footer justify-content-between">
        <a id="replay_button" href="" class="" title="Reply" style="color:coral" ><i class="fas fa-reply"></i></a>
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
  var box_name = '<?php echo $box_name ?>' ;

  $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30 
  });

  function show_full_message(elem, mobile, row_id) {
    
    $("#show_message_text").text(elem.id);
    $("#replay_button").attr('href', base_url+mobile);
    $("#show_message").modal('show');

    $.ajax({
      url:"<?php echo base_url(); ?>smsbox/read_inbox_message",
      method:"POST",
      data:{'row_id': row_id },
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

  $("#reset").on('click', function(){
    window.location.href = '<?php echo site_url('smsbox/inbox') ?>';
  });



$(document).ready(function(){

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
          status : '<?php echo $status ?>',
          name_number : '<?php echo $name_number ?>',
          box_name : '<?php echo $box_name ?>',          
        }
      },
      "columns": [
        { "data": "#" },
        { "data": "send_from" },
        { "data": "name" },
        { "data": "message" },
        { "data": "create_date" },
        { "data": "action" },
        { "data": "replyed" },
        // { "data": "action_by" },
        ],
        "columnDefs": [{
          targets: "_all",
          orderable: false
        }],
  });


  
});

  function search_by_name_number(value) {
   console.log('value');
   console.log(value);


    if(value.length >=3){
      $.ajax({
        url:"<?php echo base_url(); ?>contact/ajax_customer_search",
        method:"POST",
        data:{'search': value },
        dataType : 'json',
        success:function(data, textStatus, jqXHR){
          console.log(data);
          var list = '' ;  
          var options = '';
          mycars = data.search ;
         
          list ='<ul style="cursor: pointer; list-style:none; padding-left:0px" >';
          $.each(data.search, function(index, value){ 
            console.log('ID '+index +' VAL '+ value.mobile_no+' NAME '+ value.first_name);
            list += ' <li onclick="getCustomerDetails('+value.mobile_no+')">'+ value.mobile_no+', '+ value.first_name+'</li>' ;
          });
           list +='</ul>';
          $("#search_result").css("display", "block");
          $("#search_result").html(list);
        },
        error:function(jqXHR, textStatus, errorThrown){
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
     });
   }
  }

  function getCustomerDetails(number){
    $("#name_number").val(number);
    $("#search_result").empty();
    $("#search_result").css("display", "none");
  } 


</script>