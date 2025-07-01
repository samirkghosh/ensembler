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
 .interaction-chat {
    -webkit-transform: translate(0,0);
    transform: translate(0,0);
    height: 401px;
    padding: 10px;
    overflow: hidden;
}
 /* .link:hover{
   background:coral;
   color:#fff;
 } */
 #template{
    padding-left: 20px;
    margin-top: 12px;
    display: flex;
    column: thick;
    flex-wrap: wrap;
    column-gap: 20px;
    font-size: smaller;
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

        <!-- COMPOSE -->
        <div class="col-md-6 compose" style="display:block">
            <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title">Compose New Message</h3>
              </div>
         
              <form id="quickForm" action="<?php echo site_url('smsbox/compose')?>" method="post">
                <input type="hidden" name="message_id" value="<?php echo $message_id ;?>">
                <div class="card-body">
                    <div class="form-group">
                      <label for="exampleInputEmail1">To</label>
                      <?php if($this->uri->segment(3,0) > 0):  //echo $this->uri->segment(3,0); ?>
                          <select class="form-control select2bs4" name="mobile[]" id="mobile" style="width: 100%;" data-placeholder="Select Mobile No">
                        <option value="<?php echo $this->uri->segment(3,0) ; ?>" selected ><?php echo $this->auth_model->get_contact($this->uri->segment(3,0))?></option>
                      </select>
                      <?php else: ?>
                      <select class="form-control select2bs4" name="mobile[]" id="mobile" multiple="multiple" style="width: 100%;" data-placeholder="Select Mobile No"></select>
                      <?php endif; ?>
                     
                      <span class="text text-danger mobile_error"></span>
                    </div>
                    
                    <div class="form-group">
                      <label for="message">Message</label>
                      <div class="textarea_div">
                          <textarea class="form-control" id="message" placeholder="write your message..." name="message" maxlength="1600" style="height: 150px;resize: none;"><? echo set_value('message');?></textarea>
                          <textarea style="display:none;" type="hidden" id="output"></textarea>     
                      </div>
                      <span class="limi" id="counter"></span>
                      <span class="text text-danger message_error"></span>
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
                      <button type="submit" id="compose_button" class="btn btn-info" style="background: coral;border-color:coral">Send Now</button>
                    </div>
                </div>
               
              </form>
              <!-- /.card-body -->
              
            </div>
            <!-- /.card -->
        </div>
        <!-- END COMPOSE -->

        <!-- History and Template -->
        <div class="col-md-4">
          <div class="card card-info card-outline direct-chat direct-chat-info collapsed-card">
              <div class="card-header">
                <h3 class="card-title">Select Template</h3>
                <div class="card-tools" style="margin:0px 0px 0px 0px">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
              </div>
              <div class="card-body">
              <div id="template">
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
        <!-- End -->

      </div>
      <!-- /.row -->
    </div>
    </section>

    <!-- end main -->


     

    

    
<?php  $this->load->view('layout/footer'); ?>


<script type="text/javascript">
  var base_url = '<?php echo base_url();?>' ;

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

     

 /* function get_contact(){

    var mobile = '<?php echo $this->uri->segment(3,0) ?>' ;
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('sms/get_contact_list')?>",
        success: function(data) {
            console.log('get_contact');
            console.log(data);

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
  }*/

  function get_lastHistory(mobile) {
    console.log('mobile '+ mobile);
      if(mobile!= null){
        console.log('mobile 22222 '+ mobile);

          $("#recent").show("slow", function() {});

            $.ajax({
            url: "<?php echo site_url('sms/get_interaction_compose/')?>",
            type: "POST",
            data : { "mobile" : mobile },
            success: function(data) {
              console.log('data check response');
              console.log(data);
              var obj1 = JSON.parse(data);
              // console.log('SMS DATA ');
              // console.log(obj1.sms);
              var sms_val='';
              var wa_val='';

               if(obj1.sms !='')
               {
                    $.each(obj1.sms, function(index, val){
                      if(val.account_status == 'in'){
                        sms_val += '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-timestamp float-right">'+val.create_date+'</span></div><img class="direct-chat-img" src="'+base_url+'/assets/dist/img/sms.jpg" alt="Message User Image"><div class="direct-chat-text">'+val.message+'</div></div>';

                      }else if(val.account_status == 'out'){
                        sms_val += '<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-timestamp float-left">'+val.create_date+'</span></div><img class="direct-chat-img" src="'+base_url+'/assets/dist/img/sms.jpg" alt="Message User Image"><div class="direct-chat-text">'+val.message+'</div></div>';
                      }
                    });
                    console.log('VIJAY ');
                    $("#interaction_history").html(sms_val);
                }
                else{
                  $("#interaction_history").html('');
                }

               if(obj1.whatsapp !='')
               {
                    $.each(obj1.whatsapp, function(index, val){
                      
                        // wa_val += '<img class="direct-chat-img" src="'+base_url+'assets/dist/img/whatsapp.png" alt="User Avatar"><div class="contacts-list-info"><span class="contacts-list-msg">'+val.create_date+'<small class="contacts-list-date float-right">'+val.message+'</small></span></div>';
                        if(val.account_status == 'in'){
                        wa_val += '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-timestamp float-right text-white">'+val.create_date+'</span></div><img class="direct-chat-img" src="'+base_url+'/assets/dist/img/whatsapp.png" alt="Message User Image"><div class="direct-chat-text">'+val.message+'</div></div>';

                      }else if(val.account_status == 'out'){
                        wa_val += '<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-timestamp float-left text-white">'+val.create_date+'</span></div><img class="direct-chat-img" src="'+base_url+'/assets/dist/img/whatsapp.png" alt="Message User Image"><div class="direct-chat-text">'+val.message+'</div></div>';
                      }
                    });

                    console.log('VIJAY PIPPAL');
                    $("#wa_history").html(wa_val);
                  }else{
                    $("#wa_history").html('');
                  }
               
                 //console.log(data);
                },
            error: function(xhr, desc, err) {
              console.log(xhr);
              console.log("Details: " + desc + "\nError:" + err);
            }
          });
        }
        else{
          $("#recent").hide("slow", function() {});
        }
  }

  $(document).ready(function(){
     

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
    // get_contact();
    let selected_mobile= '<?php echo $this->uri->segment(3,0) ?>';
    get_lastHistory(selected_mobile);
  });

  var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";


  $("#quickForm").submit(function (e) {
    
      $("[class$='_error']").html("");

      $(".custom_loader").html('<img src="' + img_path + '">');
      var url = $(this).attr('action'); // the script where you handle the form input.
      $("#compose_button ").prop('disabled', true).text('Please Wait...');
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
                $("#compose_button ").prop('disabled', false).text('Send Now');
              } 
              else {
                  successMsg(data.msg);
                  setTimeout(function(){
                   window.location.href = '<?php echo site_url('smsbox/outbox')?>';
                  },2000);
              }
              $(".custom_loader").html("");

          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              $(".custom_loader").html("");
              $("#compose_button ").prop('disabled', false).text('Send Now');
              //if fails      
          }
      });

      e.preventDefault(); // avoid to execute the actual submit of the form.
  });
  
  $("#scheduletime").click(function () {
      if ($(this).is(":checked")) {
       $("#hi").show();
        $("#compose_button").text('Save');
      } else {
       $("#hi").hide();
       $("#compose_button").text('Send Now');
      }
  });

    //Fadeout alert(success and failed) after 2 seconds 
  setTimeout(function(){
   $('.alert').fadeOut('slow');
  },2000);

</script>