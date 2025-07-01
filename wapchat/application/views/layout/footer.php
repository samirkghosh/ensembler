<footer class="main-footer" style="height:0">
    <div class="row">

        <div class="col-sm-4">
            <!-- <img src="<?php echo base_url()?>/assets/dist/img/alliance.png" style="height: 46px;margin-top:-10px"> -->
        </div>

        <div class="col-sm-8">
            <strong>Copyright &copy; 2021-2022 <a href="#" style="color:#1e3c72">Alliance Infotech Pvt Ltd</a>.</strong>
            All rights reserved.
        </div>

    </div>
</footer>

<div class="l_c_h" style="display:none">
	<div class="c_h">
    	<div class="left_c">
            <div class="left1 right_c left_icons">
                   <a href="#" class="mini" style="font-size:23px;">+</a>
            </div>
            <div class="left1 center_icons"><!--center_icons-->
                 Live Notifications!
            </div><!--end center_icons-->        	
        </div>
        <div class="right1 right_c" style="width:35px;">
        	<a href="#" class="logout" title="End chat" name="" style="display:none;"></a>        	
        </div>
        <div class="clear"></div>
    </div>
<div class="chat_container" style="display: none;">
	<div class="chat_message" style="display: none;">
    <input type="hidden" class="my_user" value="">
    	    </div>
		<div class="chat_text_area" style="display:none;">
        	<textarea name="messag_send" class="messag_send" id="messag_send" placeholder="Enter Your Message and press CTRL"></textarea>
        </div>
    	
      <div class="card">
        <div class="card-footer card-comments" style="max-height: 350px;overflow: auto;">
                
                
        </div>
      </div>
    	
     
    </div>
</div>



</div>
<!-- ./wrapper -->



<!-- jQuery -->
<script src="<?php echo base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
<!-- Toastr -->
<script src="<?php echo base_url() ?>assets/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sstoast.js"></script>

<!-- Bootstrap 4 -->
<script src="<?php echo base_url() ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url() ?>/assets/dist/js/adminlte.min.js"></script>

<!-- Select2 -->
<script src="<?php echo base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!-- Datetimepicker -->
<script src="<?php echo base_url() ?>assets/dist/js/jquery.datetimepicker.full.min.js"></script>

<!-- jquery-validation -->
<script src="<?php echo base_url() ?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/jquery-validation/additional-methods.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="<?php echo base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>



<!-- sweet alert -->
<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
<!-- AdminLTE for demo purposes -->
<!-- <script src="<?php echo base_url() ?>/assets/dist/js/demo.js"></script> -->
<!-- Page specific script -->
<script>

function get_todays_notifications() {
  var url = '<?php echo site_url('dashboard/get_today_in_sms')?>';
  $.ajax({
    url:url,
    data:false,
    dataType:'JSON',
    method:'GET',
    processData:false,
    contentType:false,
    cache:false,
    success:function(data, textStatus, jqXHR){
      console.log('data');
      // console.log(textStatus);
      // // console.log(jqXHR);
      // console.log(data.length);
      if(textStatus=='success' && data.length > 0){
        $.each([0,1,2,3,4,5,6,7], function(i,val){
           
            console.log("index "+i);
            console.log("value "+val);
            $( ".card-comments" ).append('<div class="card-comment"><div class="comment-text"><span class="username">Farhan Akhtar<span class="text-muted float-right">11:00 AM Today</span></span>hi</div></div>').delay(60*8000);

          
        });
      }

      //$(".c_h").trigger('click');
    },
    error:function(jqXHR, textStatus, errorThrown){},
    complete:function(){},

  });
}

function is_active_gateway() {
  var url = '<?php echo site_url('setting/is_active_gateway')?>';
  $.ajax({
    url:url,
    data:false,
    // dataType:'JSON',
    method:'GET',
    processData:false,
    contentType:false,
    cache:false,
    success:function(data, textStatus, jqXHR){
      
      if(data=='true'){
        $("#indecator").html('YES').css('color', 'green').css('background-color', 'green');
      }
      else{
        $("#indecator").html('NO').css('color', 'red').css('background-color', 'red');
      }
    },
    error:function(jqXHR, textStatus, errorThrown){},
    complete:function(){},

  });
}
  function is_active_sendstatus() {
      var url = '<?php echo site_url('setting/is_active_sendstatus')?>';
      $.ajax({
        url:url,
        data:false,
        dataType:'JSON',
        method:'GET',
        processData:false,
        contentType:false,
        cache:false,
        success:function(data, textStatus, jqXHR){
        
          if(data.i_sendstatus=='0'){
            $("#sender_status").html('YES').css('color', 'green').css('background-color', 'green');
            $("#sendreason").text(data.v_sendreason);
          }
          else{
            $("#sender_status").html('NO').css('color', 'red').css('background-color', 'red');
            $("#sendreason").text(data.v_sendreason);
          }
        },
        error:function(jqXHR, textStatus, errorThrown){},
        complete:function(){},

      });
  }

  function reset_sms_sender_status(){

    if(confirm('You want to change SMS Sender Settings')){
      var url = '<?php echo site_url('setting/update_sendstatus') ?>' // the script where you handle the form input.
      $.ajax({
          type: "POST",
          // dataType: 'JSON',
          url: url,
          data: {'dash' : '1'}, // serializes the form's elements.
          success: function (data, textStatus, jqXHR){
            window.location.reload();
          },
          error: function (jqXHR, textStatus, errorThrown){}
      });
    }
  }

var gatway_status = setInterval(function() {
  // is_active_gateway();
  // is_active_sendstatus();
}, 3000);

// Start
$(function(){


  // get_todays_notifications();
   $(".c_h").click(function(e) {
      if ($(".chat_container").is(":visible")) {
          $(".c_h .right_c .mini").text("+")
      } else {
          $(".c_h .right_c .mini").text("-")
      }
      $(".chat_container").slideToggle("slow");
      return false
  });
   
   var auto_refresh = setInterval(
    function ()
    {
    var url = '<?php echo site_url('dashboard/get_today_in_sms')?>';
     $('.card-comments').load(url).fadeIn("slow");
    
    }, 10000); // refresh every 10000 milliseconds
});


// END


// If condition work for reply section 
<?php if ($this->uri->segment(3,0) == 0): ?>
  //Initialize Select2 Elements : Multiple select
  $(function () {
    $('.select2bs4').select2({
      ajax: {
        delay: 250,
        url: '<?php echo base_url('sms/get_contact_list') ?>',
        dataType: 'json',
        data: function (params) {
          var queryParameters = {
            q: params.term
          }

          return queryParameters;
        },
        processResults: function (data) {
         /* console.log('DATA AJAZ');
          console.log(data);*/
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data
          };
        }
      },
      placeholder: 'Search for a contact',
      minimumInputLength: 3,
      maximumSelectionLength: 100
    }); 
  }); 
<?php endif;?>  
      
// get last contac history of last selected number
  $('select#mobile' ).change(function(e){
      e.preventDefault()
     // console.log('Get Mobile no ');
     // console.log($(this).val());
     
      // var mobile = $('#mobile option:selected').last().val();
         //console.log(mobile);
      if(mobile!= null){
        $("#recent").show("slow", function() {});

          $.ajax({
          url: "<?php echo site_url('sms/get_interaction/')?>",
          type: "POST",
          data : { "mobile" : $(this).val() },
          success: function(data) {
            // console.log('data check response');
            // console.log(data);
            var obj1 = JSON.parse(data);
           
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
                  $("#interaction_history").html(sms_val);
              }else{
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

        
      }else{
        $("#recent").hide("slow", function() {});
      }
  });


    


    
    
</script>
</body>
</html>