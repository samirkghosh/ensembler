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
              <li class="breadcrumb-item active">SMS Bulk Upload</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-4">
            <div class="card card-info card-outline" style="border-color: coral;">
              <div class="card-header">
                <h3 class="card-title">SMS Bulk Upload</h3>
              </div>
              <!-- /.card-header -->

              <form method="post" id="import_form" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label>Select file Name</label>
                    <select class="form-control select2bs4"  style="width: 100%;"  name="select_file_name" id="select_file_name">
                      <option value="0">select List</option>
                      <?php
                        if(count($recent_uploads)>0 ):
                          foreach($recent_uploads as $row):
                          ?>

                            <option value="<?php echo $row['id'];?>"><?php echo $row['list_name'];?></option>


                          <?php
                          endforeach;
                        endif;
                  
                      ?>
                    </select>
                    
                  </div> 



                  <div class="form-group">
                    <label>File Name</label>
                    <input type="text" name="list_name" id="list_name" class="form-control" placeholder="Enter List Name" value="<?php echo set_value('list_name') ?>">
                    <span class="text text-danger list_name_error"></span>
                  </div>

                  <div class="form-group">
                    <label>Message Contant</label>
                     
                    <textarea class="form-control" id="message" placeholder="Your Message..." name="message" maxlength="1600" style="height: 192px;"><? echo set_value('message');?></textarea>
                    <span class="limi" id="counter"></span>
                    <span class="text text-danger message_error"></span>
                  </div>


                  <br>

                  <label><i class="fas fa-file"></i> Please Select File</label>
                  
                  <div class="form-group">
                    <input type="file" name="file" id="file"  accept=".xls, .xlsx ,.csv" /></p>
                  </div>
                  <span class="text text-danger file_upload_error"></span>
                  
                  <br>
                  <input type="hidden" name="file_upload_type" id="file_upload_type" value="new">

                  <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                      <input type="checkbox" class="custom-control-input" id="scheduletime" name="scheduletime">
                      <label class="custom-control-label" for="scheduletime">Schedule</label>
                    </div>
                  </div>
                       
                        
                        
                      <div class="form-group" id="datepicker_div" style="display:none;" >
                         <input id="pickdate" class="form-control" placeholder="Select date and Time" autocomplete="off" name="date" value="<? echo set_value('date');?>">
                      </div>
                      <?php //echo form_error('date'); ?>

                  <div class="form-group text-right">
                      <input type="submit" name="Upload" value="Upload" class="btn btn-info" />
                  </div>

                   
                  <div class="card-footer">
                     <?php 
                        $path = 'samples/sample.csv';
                        $pdf_path = base_url().'samples/sample.csv';
                        if(file_exists($path) ): 

                        ?>
                        <span><a style="text-decoration: none;" href="<?php echo $pdf_path ?>">Downlaod Sample File </a></span>  
                       
                      <?php endif; ?>
                   </div>
                  

                  
                </div>
              </form>
            </div>
         
            <pre id="data"></pre>
          </div><!-- /.col -->
          
          <div class="col-md-8">
            <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title">Recent Uplaods </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-hover" id="contacts">
                  <thead>
                    <tr>
                      <th >#</th>
                      <th>File Name</th>
                      <th>Total Count</th>
                      <th>Session Id</th>
                      <th>Created Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
               <? $id=1;
                if(count($recent_uploads)>0 ){
                  
                 foreach($recent_uploads as $row):
                   
                  ?>
                  
                   <tr>
                     <td><?php echo $id++;?></td>
                     <td><?php echo $row['list_name'];?></td>
                     <td><?php echo $row['total_count'];?></td>
                     
                     <td><?php echo $row['file_name'];?></td>
                     <td><?php echo date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                     <td>
                      <?php 
                        $path = 'uploads/'.$row['file_name'].'.csv';
                        //$pdf_path = base_url().'samples/sample.csv';
                        if(file_exists($path) ): ?> 
                        <span><a style="text-decoration: none;" href="<?php echo base_url().$path ; ?>">Downlaod </a></span> 
                      <?php endif; ?>
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

        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    
<!-- End of content -->
</div>
  <!-- /.content-wrapper -->

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



<script>
$(document).ready(function(){

  $('#pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30,
    minDate:new Date()
  });

  $('#import_form').on('submit', function(event){
      event.preventDefault();
      if($("#list_name").val()==''){
        alert('Please Enter file name'); $("#list_name").focus(); return false ;
      }
      $.ajax({
          url:"<?php echo base_url(); ?>sms/import",
          method:"POST",
          data:new FormData(this),
          contentType:false,
          cache:false,
          processData:false,
          success:function(data, textStatus, jqXHR){
            console.log('DATA');
            console.log(data);
            var obj = JSON.parse(data)
            console.log(obj);
            console.log(obj.status);

            /*if (obj.status == 'fail') {
                $.each(obj.msg, function (key, value) {
                    $('.' + key + "_error").html(value);
                });

                if(obj.error_type=='1'){
                  var check_confirm = confirm('You want to overwrite the list ?\nCliek OK to overwrite or \nCancel for New file upload ?');
                  if(check_confirm){
                    console.log('YES OK');
                    $("#file_upload_type").val('overwrite');

                    $("#import_form").trigger("submit");
                  }
                  else{
                    // cancel for new file upload
                    $("#file_upload_type").val('no_new');
                     console.log('YES OK');
                     $("#import_form").trigger("submit");
                  }
                }

                  

            } 
            else {
                successMsg(obj.message);
                setTimeout(function(){
                 location.reload();
                },2000);
            }*/

            // $('#data').empty();  
            // $('#data').text(data).css('color', 'red');
          }
      });
  });



  $("#scheduletime").on('change', function(event) {
    console.log('check date ');
    if($(this).is(':checked'))
      $("#datepicker_div").show();
    else
      $("#datepicker_div").hide();


  })


  /*$("#select_file_name").on('change', function(event) {
    console.log(event);
    console.log(event);
  }); */ 

//   $(".select2bs4").select2({
//     ajax: {
//       url: '<?php echo base_url('sms/upload_list') ?>',
//       dataType: 'json',
//       delay: 250,
//       data: function (params) {
//         return {
//           q: params.term, // search term
//           page: params.page
//         };
//       },
//       processResults: function (data, params) {        
//         // parse the results into the format expected by Select2
//         // since we are using custom formatting functions we do not need to
//         // alter the remote JSON data, except to indicate that infinite
//         // scrolling can be used
//         console.log('processResults');
//         console.log(data);
//         // console.log('length coont '+data.length);
//         // console.log(params);
        
//         params.page = params.page || 1;

//         return {
//           results: data.items,
//           pagination: {
//             more: (params.page * 30) < data.total_count
//           }
//         };
//       },
//       cache: true
//     },
//     placeholder: 'Search for a repository',
//     minimumInputLength: 1,
//     //templateResult: formatRepo,
//     //templateSelection: formatRepoSelection
//   });  

});
</script>

<script>
    const messageEle = document.getElementById('message');
    const counterEle = document.getElementById('counter');

    messageEle.addEventListener('input', function(e) {
        const target = e.target;

        // Get the `maxlength` attribute
        const maxLength = target.getAttribute('maxlength');

        // Count the current number of characters
        const currentLength = target.value.length;

        counterEle.innerHTML = `${currentLength}/${maxLength}`;
    });
</script>

