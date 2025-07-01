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
<?php  //$this->load->view('layout/sidebar');?>
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
                
              <form autocomplete="on" name="report_search" id="report_search" action="<?php echo site_url('reports/customer_wise') ?>"  method="post"  >
              <div class="row">
                <div class="col-sm-10">
                   <div class="row">
                     <!--  <div class="col-md-1"> Report :             
                        <select name="report_name" class="form-control form-control-sm">
                          <option value="all"  <?php echo ($channel=='all')?'selected':'selected' ?> >All</option>
                          <option value="sms" <?php echo ($channel=='sms')?'selected':'' ?> >SMS</option>
                          <option value="whatsapp" <?php echo ($channel=='whatsapp')?'selected':'' ?>>Whatsapp</option>
                        </select>
                      </div>

                      <div class="col-md-1"> IN/OUT :             
                        <select name="report_in_out" class="form-control form-control-sm">
                          <option value="all"  <?php echo ($report_in_out=='all')?'selected':'selected' ?> >All</option>
                          <option value="out" <?php echo ($report_in_out=='out')?'selected':'' ?> >OUT</option>
                          <option value="in" <?php echo ($report_in_out=='in')?'selected':'' ?>>IN</option>
                        </select>
                      </div> -->

                      <div class="col-md-2"> Enter Mobile No :             
                        <input type="text" name="mobile" id="mobile" onkeyup="search_by_name_number(this.value)" value="<?php echo $mobile ?>" class="form-control form-control-sm" autocomplete="off" placeholder="Eg. +2649865423153">
                        <div id="search_result" style="position: absolute; width: 100px; z-index: 999; background:#fff; border:1px solid #fff ;color:black; padding: 5px; width: 208px;display: none;border: 1px solid orange;"> </div>
                      </div>
                    
                      <!-- <div class="col-md-2">Start Date :<input autocomplete="off" type="text" name="from_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($from_date)?$from_date: '' ?>"></div>
                      <div class="col-md-2">End Date :<input autocomplete="off" type="text" name="end_date" class="form-control form-control-sm pickdate" value="<?php echo !empty($end_date)?$end_date:'' ; ?>"></div> -->
                   </div>
                </div>


                <!-- Buttons -->
                <div class="col-sm-2" style="margin-top: 0px;">
                  <div class="btn-group-horizontal">
                    <button type="submit" class="btn btn-info btn-sm" title="Apply Filter"><i class="far fa-paper-plane"></i>Send</button>
                    <!-- <button type="button" id="advanced" name="advanced" class="btn btn-sm" title="Advanced"><i class="fas fa-filter"></i></button> -->
                    <button type="button"  id="reset" name="reset" class="btn btn-sm" title="Reset"><i class="fas fa-power-off"></i>Reset</button>
                  </div>
                </div>
              </div>
              </form>
            </div>


        
              

            <!-- /.card-header -->
            <div class="card-body">
             <div class="table-responsive">
              <table class="table table-striped table-bordered table-sm" id="report-server-side">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Channel</th>
                    <!-- <th>Type</th>
                    <th>Status</th> -->
                    <th>Message</th>
                    <!-- <th>Created By</th> -->
                  </tr>
                </thead>
              </table>
            </div>
                
              
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

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<!-- AdminLTE -->
<!-- <script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script> -->


<script>
   $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30,
    minDate:new Date()
  });

  $("#advanced").on('click', function(){
    var st = $("#show_advance_filter").slideToggle();
  });
      

  $("#reset").on('click', function(){
    window.location.href = '<?php echo site_url('reports/customer_wise') ?>';
  }); 


$(function () {
    $("#report-server-side").DataTable({
      "searching": false,
      "responsive": false, 
      "pageLength": 100, 
      "lengthChange": false,
      "autoWidth": false,
      "processing": true,
      "serverSide": true,
      "searchable" : false, 
      "ajax":{
          "url": "<?php echo site_url('reports/customer_wise_data') ?>",
          "dataType": "json",
          "type": "POST",
          "data" : { channel : '<?php echo $channel; ?>',
                    report_in_out : '<?php echo $report_in_out ?>',
                    from_date : '<?php echo $from_date ?>',
                    end_date : '<?php echo $end_date ?>',
                    mobile : '<?php echo $mobile ?>',
                    message_type : '<?php echo $message_type ?>',
                    status : '<?php echo $status ?>',
                    
                  }
      },
      "columns": [
          { "data": "#" },
          { "data": "date" },
          { "data": "channel" },
          { "data": "message" },
          /*{ "data": "type" },
          { "data": "status" },
          { "data": "create_by" },*/
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

// Code for input only numeric value : Mobile No
// $("#inputmobile").on("keypress keyup blur",function (event) {    
//    $(this).val($(this).val().replace(/[^\d].+/, ""));
//     if ((event.which < 48 || event.which > 57)) {
//         event.preventDefault();
//     }
// });
//Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);


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
    $("#mobile").val(number);
    $("#search_result").empty();
    $("#search_result").css("display", "none");
  } 
</script>

