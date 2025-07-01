<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
  <!-- Header start -->
  <?php  $this->load->view('layout/header'); ?>
  <!-- Header End  -->
  <style type="text/css">
   h2 {
       /* width: 100%; */
      text-align: center;
      border-bottom: 1px solid coral;
      line-height: 0.1em;
      margin: 30px 0 20px; 
  } 

h2 span { 
    background:#fff; 
    padding:0 10px; 
}
   
 </style> 
  <!-- Main Sidebar Container -->
  <?php  $this->load->view('layout/sidebar');?>
  <!-- End Main sidebar -->

  <!-- Content page  -->
  <div class="content">
    <?php $this->load->view('breadcrumb') ?>

    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <?php $this->load->view('message') ?>
    <div class="container-fluid">

      <div class="card card-info card-outline card-outline-tabs">
        <div class="card-header" style="border-bottom: 0px solid rgba(0,0,0,.125);">
          <!-- <h3 class="card-title">
            <i class="fas fa-edit"></i>
            Tabs Custom Content Examples
          </h3> -->
          <?php //echo $this->uri->segment('2') ;?>
          <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
            <?php if ( $this->rbac->hasPrivilege('send_sms', 'can_add') ): ?>
            <li class="nav-item">
              <a class="nav-link link <?php echo $this->uri->segment('2')=='send_sms'?'active':'' ?>" id="custom-content-above-home-tab" data-toggle="pill" href="#custom-content-above-home" role="tab" aria-controls="custom-content-above-home" aria-selected="false">Send Single SMS </a>
            </li>
          <?php endif;?>
            <?php if ( $this->rbac->hasPrivilege('sms_bulk_upload', 'can_add') ): ?>
            <li class="nav-item">
              <a class="nav-link link <?php echo $this->rbac->hasPrivilege('send_sms', 'can_add') == false ?'active':'' ?>" id="custom-content-above-profile-tab" data-toggle="pill" href="#custom-content-above-profile" role="tab" aria-controls="custom-content-above-profile" aria-selected="false">Campaign </a>
            </li>
          <?php endif;?>
          </ul>
        </div>
          
          

        <div class="card-body" style="padding: 0px;">
          <div class="tab-content" id="custom-content-above-tabContent">
             <?php if ( $this->rbac->hasPrivilege('send_sms', 'can_add') ): ?>


            <div class="tab-pane fade active show" id="custom-content-above-home" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="card card-info card-outline" style="border-color: coral;">
                        <div class="card-header">
                          <h3 class="card-title">Send SMS Messages</h3>
                        </div>

                        <form id="quickForm" action="<?php echo site_url('sms/send_sms_index')?>" method="post">
                        <div class="card-body">
                        <div class="form-group">
                        <label for="exampleInputEmail1">To</label>
                            <select class="form-control select2bs4" name="mobile[]" id="mobile" multiple="multiple" style="width: 100%;" data-placeholder="Select Mobile No">
                               
                            </select>
                         
                        <span class="text text-danger mobile_error"></span>
                        </div>
                        <div class="form-group">
                        <label for="message">Message</label>
                        <div class="textarea_div">
                          <textarea class="form-control" id="message" placeholder="write your message" name="message" maxlength="1600" style="height: 150px; resize: none;"><? echo set_value('message');?></textarea>
                          
                          <textarea style="display:none;" type="hidden" id="output"></textarea>     
                        </div>
                        <span class="limi" id="counter"></span>
                        
                        <span class="text text-danger message_error"></span>
                        </div>

                        <div class="form-group">
                          

                        </div>

                        <?php 
                        $check=''; $display='';
                        if(isset($_POST['scheduletime'])&& $_POST['scheduletime']=='on'):
                        $check='checked';
                        $display='block';
                        else:
                        $display='none';
                        endif;
                        ?>
                        <div class="form-group">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="scheduletime" name="scheduletime" <?echo $check?>>
                        <label class="custom-control-label" for="scheduletime">Schedule</label>
                        </div>
                        </div>
                        <div class="form-group" id="hi" style="display:<?echo $display?>">
                        <input id="pickdate" class="form-control pickdate" placeholder="Select date and Time" autocomplete="off" name="date" value="<? echo set_value('date');?>">
                        </div>
                        <?php //echo form_error('date'); ?>
                        <span class="text text-danger date_error"></span>

                        <div class="form-group text-right">
                            <button type="submit" id="single_send_button" class="btn btn-info" style="background: coral;border-color:coral">Send Now</button>
                        </div>

                        </div>

                         
                         
                        </form>

                      </div>

                  </div>

                  <div class="col-sm-5 offset-sm-1">
                    <div class="card card-info card-outline direct-chat direct-chat-info collapsed-card">
                      <div class="card-header">
                        <h3 class="card-title">Select Template</h3>

                        <div class="card-tools" style="margin:0px 0px 0px 0px">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                      </div>

                      <div class="card-body" style="padding-left: 20px;">
                         
                          <?php
                            if(!empty($template_assign)):
                              $id=0;
                              foreach ($template_assign as $value) 
                              {
                              $id++;
                              ?>
                              <div class="custom-control custom-radio">
                              <!-- <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_<?php echo $id?>" name="customRadio2" > -->
                              <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_<?php echo $id?>" name="customRadio2" value="<?php echo $value->name;?>">
                              <label for="customRadio_<?php echo $id?>" class="custom-control-label" style="font-weight: 200;"><?php  echo $value->name;?></label>

                              </div>
                              <?
                              }
                           
                            endif;
                          ?>  

                          <div class="custom-control custom-radio">
                            <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_clear" name="customRadio2">
                            <label for="customRadio_clear" class="custom-control-label" style="font-weight: 200;">None</label>
                          </div>
                        </div>
                       
                    </div>
                     

                      <div class="card card-info card-outline direct-chat direct-chat-info" id="recent" style="display: none;">
                        <div class="card-header">
                          <h3 class="card-title">Last interaction 360°</h3>
                        </div>
                                      
                        <div class="card-body">
                            <div class="direct-chat-messages interaction-chat">
                            <div id="interaction_history" class="direct-chat-msg left"></div>
                            </div>
                        </div>
                      </div>
                  </div>
                                    
              </div>
            </div>
             <?php endif;?>
            <!-- /..Close tabContent -->


            <?php if ( $this->rbac->hasPrivilege('sms_bulk_upload', 'can_add') ): ?>
            <div class="tab-pane fade <?php echo $this->rbac->hasPrivilege('send_sms', 'can_add')== false ? 'active show':'' ?>" id="custom-content-above-profile" role="tabpanel" aria-labelledby="custom-content-above-profile-tab">
              <div class="row">
                <div class="col-5">
                  <div class="card card-info card-outline" style="border-color: coral;">
                    <div class="card-header">
                      <h3 class="card-title">SMS Bulk Upload</h3>
                    </div>
                    <!-- /.card-header -->

                    <form method="post" id="import_form" enctype="multipart/form-data">
                      <div class="card-body">
                        
                        

                        <div class="form-group">
                          <label> Select Uploaded Campaign</label>
                          <select class="form-control  select2bs4s"  style="width: 100%;" onchange="get_count_of_list(this.value)"  name="select_file_name" id="select_file_name">
                            <option value="0">Select Campaign</option>
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
                         
                         <span id="show-counts"></span> 
                          
                        <h2 id="divider_row"><span> OR </span></h2>

                        <span class="text text-danger file_upload_error"></span>
                        <div class="form-group" id="upload_file_div">
                        <label><i class="fas fa-file"></i> Select File</label><br>
                          <input type="file" name="file" id="file"  accept=".xls, .xlsx ,.csv" /> 
                        </div>
                        




                        <div class="form-group" id="file_name_div">
                          <label>Campaign Name</label>
                          <input type="text" name="list_name" id="list_name" class="form-control" placeholder="Enter List Name" value="<?php echo set_value('list_name') ?>">
                          <span class="text text-danger list_name_error"></span>
                        </div>



                        <div class="form-group" id="desc_div">
                          <label>Description</label>
                          <input type="text" name="description" id="description" class="form-control" placeholder="Enter description" value="<?php echo set_value('description') ?>">
                          <span class="text text-danger description_error"></span>
                        </div>

                        <div class="form-group">
                          <label>Message</label>
                          
                          <textarea class="form-control" id="message" placeholder="Your Message..." name="message" maxlength="1600" style="height: 110px; resize:none;"><? echo set_value('message');?></textarea>
                          <span class="limi" id="counter"></span>
                          <span class="text text-danger message_error"></span>
                        </div>


                        
                        
                       
                        <input type="hidden" name="file_upload_type" id="file_upload_type" value="new">

                        <div class="form-group">
                          <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="scheduletime2" name="scheduletime">
                            <label class="custom-control-label" for="scheduletime2">Schedule</label>
                          </div>
                        </div>
                            
                              
                              
                            <div class="form-group" id="datepicker_div" style="display:none;" >
                              <input id="pickdate" class="form-control pickdate" placeholder="Select date and Time" autocomplete="off" name="date" value="<? echo set_value('date');?>">
                            </div>
                            <?php //echo form_error('date'); ?>

                        <div class="form-group text-right">
                            <input type="submit" id="bulk_send_button" name="Upload" value="Send Now" class="btn btn-info" />
                        </div>

                        
                        <div class="card-footer">
                          <?php 
                              $path = 'samples/excel_sample.csv';
                              $pdf_path = base_url().'samples/excel_sample.csv';
                              if(file_exists($path) ): 

                              ?>
                              <span><a style="text-decoration: none;" href="<?php echo $pdf_path ?>">Download Template </a></span>  
                              <br><span style="color:red">Note : Enter contact no between the quotes e.g : '264XXXXXXXXX'    </span>  
                            
                            <?php endif; ?>
                        </div>
                        

                        
                      </div>
                    </form>
                  </div>
              
                  <pre id="data"></pre>
                </div><!-- /.col -->
                
                <div class="col-md-7">
                  <!-- Show Recent Bulk Uploaded In Queue -->
                   

                  <!-- show Recent Uploaded List -->
                  <div class="card card-info card-outline">
                    <div class="card-header">
                      <h3 class="card-title">Recent Uploads </h3>
                      <!-- <div class="card-tools" style="margin:0px">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                      </div> -->
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table class="table table-bordered table-hover table-sm" id="campaign_lists">
                        <thead>
                          <tr>
                            <th >#</th>
                            <th>Campaign Name</th>
                            <th>Total Count</th>
                            <!-- <th>Session Id</th> -->
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
                          
                          <!-- <td><?php echo $row['file_name'];?></td> -->
                          <td><?php echo date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                          <td>
                            <?php 
                              $path = 'uploads/'.$row['file_name'].'.csv';
                              //$pdf_path = base_url().'samples/sample.csv';
                              if(file_exists($path) ): ?> 
                              <span><a style="text-decoration: none;" href="<?php echo base_url().$path ; ?>">Download </a></span> 
                            <?php endif; ?> <!-- &nbsp;&nbsp;&nbsp;

                            <a href="#" style="color:crimson;" class="delete_data" onclick="delete_campaign(this)" data-file_name="<?php //echo $row['file_name'] ?>" id="<?php //echo $row['id'] ?>"><i class="nav-icons fas fa-trash"></i>
                     </a> -->
                          </td>
                        </tr>

                         
                        <? 
                        endforeach;
                      }
                      else{ ?>
                        <tr>
                          <td colspan="5">No Record Found</td>
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
            </div>
             <?php endif;?>

          </div>
        </div>
        <!-- /.Card-body -->
      </div>
        <!-- //. Close card outline -->
          
      </div>
        <!-- /.container-fluid -->

      <!-- /.content-wrapper -->
    </div>
  </div>

  <!-- End of content -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer --> 
  <?php $this->load->view('layout/footer');?>
  <!-- End of footer -->

<script type="text/javascript">
  var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
  var base_url = '<?php echo base_url();?>' ;

 /* function get_contact(){

    var mobile = '<?php echo $this->uri->segment(3,0) ?>' ;
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('sms/get_contact_list')?>",
        success: function(data) {
              if ( data != '') 
              {
                  var option ='';
                  var obj = JSON.parse(data);
                    $.each(obj, function(index, val){

                      if(mobile==val.mobile ){
                      
                        option += '<option selected value="'+val.mobile+'">'+val.first_name+'-'+val.mobile+'</option>';
                      }

                      option += '<option value="'+val.mobile+'">'+val.first_name+'-'+val.mobile+'</option>';
                    });
                    $("#mobile").append(option);

              }
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
     });
  }
   */          


  function get_count_of_list(campaign_id) {
    var campaign_id = campaign_id ;
    $.ajax({
        type: "GET",
        url: "<?php echo site_url('sms/ajax_campaign_contact_count/')?>"+campaign_id,
        success: function(data) {
          console.log('ajax_campaign_contact_count');
          console.log(data);
          $('#show-counts').empty();
          $('#show-counts').html(data);
                     
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
     });
  }


/*// Delete Campaign 
function delete_campaign(ele) {
  console.log('CP DLETE ele');  
  console.log(ele);  
  var id = ele.id;
  var file_name = ele.file_name;
  if(confirm("Are you sure you want to delete this.?")){  
      window.location="<?php echo base_url(); ?>contact//"+id;  
  }  
  else{  
      return false;  
  } 
} */                     

  

     



$(document).ready(function(){

  $('.select2bs4s').select2({
    theme: 'bootstrap4',
    maximumSelectionLength: 50
  })
   

  // farhan:: Clear textbox  :17-06-2021
  $("#customRadio_clear").click(function () {
      $("textarea#message").val('');
      $("#output").val('');
  });

  



  // farhan::  Template Redirect in textarea with Current Value :18-06-2021
  $( "input[name='customRadio2']" ).on( "click", function() {

   var tempname = $("input:checked").val();
   var currentVal = $("#output").val();
   console.log('currentVal '+currentVal);
   console.log('tempname '+tempname);
    
   $.ajax({
        url: "<?php echo site_url('smstemplate/get_template_content')?>",
        type: "POST",
        data : {"tempname" : tempname},
        success: function(data) {
          //console.log(data);
          var obj = JSON.parse(data);
          var temp_content = obj.template_content;
            //console.log(temp_content);
         $("#message").val(currentVal+'\n'+temp_content);

          
        },
        error: function(xhr, desc, err) {
          console.log(xhr);
          console.log("Details: " + desc + "\nError:" + err);
        }
    });
  });

  // farhan::  Get hidden Value textarea  :18-06-2021
  $("#message").keyup(function(event){
    event.preventDefault();
      // Getting the current value of textarea
      var currentText = $(this).val();
      console.log('currentText');
      console.log(currentText);
      // Setting the Div content
      $("#output").val(currentText);
  });



  $('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30,
    minDate:new Date()
  });

  // Vijay : 10-06-2021 : Save Bulk Upload File 
  $('#import_form').on('submit', function(event){
      event.preventDefault();
      $("#bulk_send_button").prop('disabled', true).val('Please Wait...');
      $.ajax({
          url:"<?php echo base_url(); ?>sms/import",
          method:"POST",
          data:new FormData(this),
          contentType:false,
          cache:false,
          processData:false,
          success:function(data, textStatus, jqXHR){
            $("#bulk_send_button").prop('disabled', false).val('Save');
           /* console.log('DATA');
            console.log(data);*/
            var obj = JSON.parse(data)
            /*console.log(obj);
            console.log(obj.status);*/

            if (obj.status == 'fail') {
                $.each(obj.msg, function (key, value) {
                    $('.' + key + "_error").html(value);
                });

                if(obj.error_type=='1'){
                  var check_confirm = confirm('You want to overwrite the list ?\nCliek OK to overwrite or \nCancel for New file upload ?');
                  if(check_confirm){
                    $("#file_upload_type").val('overwrite');

                    $("#import_form").trigger("submit");
                  }
                  else{
                    // cancel for new file upload
                    $("#file_upload_type").val('no_new');
                     $("#import_form").trigger("submit");
                  }
                }
            } 
            else {
                successMsg(obj.message);
                setTimeout(function(){
                  
                 window.location.href = '<?php echo site_url('smsbox/outbox')?>';
                },2000);
            }
          },
          error:function(jqXHR, textStatus, errorThrown) {
            $("#bulk_send_button").prop('disabled', false).val('Save');
            console.log('textStatus');
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          }
          // ...Close Success Function
      });
  });

                  




  $("#scheduletime2").on('change', function(event) {
    console.log('check date ');
    if($(this).is(':checked')){
      $("#bulk_send_button").val('Save');
      $("#datepicker_div").show();
    }
    else{
      $("#bulk_send_button").val('Send Now');
      $("#datepicker_div").hide();
    }
  });

  // Vijay : 11-06-2021
  $("#select_file_name").on('change', function(){
    var selected_val = $(this).val();
    if(selected_val != '0'){
      $("#upload_file_div").hide();
      $("#file_name_div").hide();
      $("#desc_div").hide();
      $("#divider_row").hide();

    }
    else{

      $("#upload_file_div").show();
      $("#file_name_div").show();
      $("#desc_div").show();
      $("#divider_row").show();
    }
    $("#file").val('');
  });



 
 
         
    // get_contact();

  });


   


    $("#quickForm").submit(function (e) {
      
        $("[class$='_error']").html("");

        $(".custom_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $("#single_send_button").prop('disabled', true).text('Please Wait...');
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#quickForm").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                }
                else if (data.st === 2) {
                  errorMsg(data.msg);
                } else {
                    successMsg(data.msg);
                    setTimeout(function(){
                      window.location.href = '<?php echo site_url('smsbox/outbox')?>';
                    },2000);
                }
                $(".custom_loader").html("");
                $("#single_send_button").prop('disabled', false).text('Save');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

              $("#single_send_button").prop('disabled', false).text('Save');
                $(".custom_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });


  



 
    $(function () {
      // $('.select2bs4').select2({
      //     theme: 'bootstrap4',
      //     maximumSelectionLength: 50
      //   })

        // $("#example").bsMultiSelect({cssPatch : {
        //   choices: {columnCount:'1' },
        // }});
        // for Schedule toggle : Checkbox 
        $("#scheduletime").click(function () {
            if ($(this).is(":checked")) {
              $("#single_send_button").text('Save');
             $("#hi").show();
            } else {
              $("#single_send_button").text('Send Now');
             $("#hi").hide();
            }
        });
    });

    $(function () {
      $("#campaign_list").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 50,"searchable" : false, 
        buttons: [
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i>',
          exportOptions: {
            columns: [0, 1, 2, 3]
            }
        }, {
          extend: 'colvis',
          text: '<i class="fa fa-columns"></i>',
          titleAttr: 'Columns',
          title: $('.download_label').html(),
          postfixButtons: ['colvisRestore']
        }]
      }).buttons().container().appendTo('#contacts_wrapper .col-md-6:eq(0)');
    });

        //Fadeout alert(success and failed) after 2 seconds 
     setTimeout(function(){
       $('.alert').fadeOut('slow');
     },2000);

    //  Message limit
  // const messageEle = document.getElementById('message');
  // const counterEle = document.getElementById('counter');

  // messageEle.addEventListener('input', function(e) {
  //     const target = e.target;

  //     // Get the `maxlength` attribute
  //     const maxLength = target.getAttribute('maxlength');

  //     // Count the current number of characters
  //     const currentLength = target.value.length;

  //     counterEle.innerHTML = `${currentLength}/${maxLength}`;
  // });


  
	</script>
