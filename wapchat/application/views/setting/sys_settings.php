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
              <li class="breadcrumb-item"><a href="#" class="link">Home</a></li>
              <li class="breadcrumb-item active"><?php echo $breadcrumb ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
       <div class="container-fluid">
       <?php $this->load->view('message') ?>
    		<!-- <form class="form-horizontal" id="addcnt" action="<?php echo site_url('setting/general_settings')?>" method="post"> -->
				   <div class="row">
				      <div class="col-md-8 offset-md-2">
				         <!-- <a href="compose.html" class="btn btn-primary btn-block mb-3">Compose</a> -->
				         <div class="card card-info card-outline" style="border-color: coral;">
				            <div class="card-header">
				               <h3 class="card-title"><?php echo ucwords($breadcrumb) ?></h3>
				            </div>
				             
				            <div class="card-body p-0">
				               <div class="card-body">
				                  <!-- Row 1 -->
				                  <form class="form-horizontal" id="whatsapp_setting" action="<?php echo site_url('setting/whatsapp_setting')?>" method="post" encryption="multipart/form-data">
					                  <h5 >Whatsapp Settings : </h5>				                  
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Channels ID :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="wa_channel_id" name="wa_channel_id" placeholder="Whatsapp Channel Id" maxlength="255" value="<?php echo set_value('wa_channel_id', get_settings('wa_channel_id')); ?>">
					                        <span class="text text-danger wa_channel_id_error"></span><?php //echo form_error('wa_channel_id'); ?>
					                     </div>
					                  </div>
					                   
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Live API Key :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="wa_live_key" name="wa_live_key" placeholder="Live API Key" maxlength="255" value="<?php echo set_value('wa_live_key', get_settings('wa_live_key')); ?>">
					                        <span class="text text-danger wa_live_key_error"><?php //echo form_error('wa_live_key'); ?>
					                     </div>
					                  </div>
					                  <br>
					              		<input style="float: right;" id="whatsapp_setting_button"  type="submit" class="btn btn-info" value="Save">

				                  </form>
					                  <br>
					                  <br>


				                  <h5>Imap Settings : (Email To SMS) </h5>
				                  <form class="form-horizontal" id="imap_setting" action="<?php echo site_url('setting/imap_setting')?>" method="post" encryption="multipart/form-data">
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Imap Host :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="imap_host" name="imap_host" placeholder="Imap Hostname" maxlength="255" value="<?php echo set_value('imap_host', get_settings('imap_host')); ?>">
					                        <span class="text text-danger imap_host_error"><?php //echo form_error('imap_host'); ?>
					                     </div>
					                  </div>
					                 
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Imap Email :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="imap_email" name="imap_email" placeholder="Imap Email" maxlength="255" value="<?php echo set_value('imap_email', get_settings('imap_email')); ?>">
					                        <span class="text text-danger imap_email_error"><?php //echo form_error('imap_email'); ?>
					                     </div>
					                  </div>
					                  
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Imap Password :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="imap_pass" name="imap_pass" placeholder="Imap Password" maxlength="255" value="<?php echo set_value('imap_pass', get_settings('imap_pass')); ?>">
					                        <span class="text text-danger imap_pass_error"><?php echo form_error('imap_pass'); ?>
					                     </div>
					                  </div>
					                  <br>
				              			<input style="float: right;" id="imap_setting_button"  type="submit" class="btn btn-info" value="Save">

				                  </form>
					                  <br>
					                  <br>
				                  
				                  <h5>SMTP Settings : (SMS To Email) </h5>
				                  
				                  <form class="form-horizontal" id="smtp_setting" action="<?php echo site_url('setting/smtp_setting')?>" method="post" encryption="multipart/form-data">

					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">SMTP Host :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="smtp_host" name="smtp_host" placeholder="SMTP Hostname" maxlength="255" value="<?php echo set_value('smtp_host', get_settings('smtp_host')); ?>">
					                        <span class="text text-danger smtp_host_error"><?php echo form_error('smtp_host'); ?>
					                     </div>
					                  </div>
					                   
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Email :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="from_email" name="from_email" placeholder="Email" maxlength="255" value="<?php echo set_value('from_email', get_settings('from_email')); ?>">
					                        <span class="text text-danger from_email_error"><?php echo form_error('from_email'); ?>
					                     </div>
					                  </div>
					                   
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Password :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="smtp_password" name="smtp_password" placeholder="Password" maxlength="100" value="<?php echo set_value('smtp_password', get_settings('smtp_password')); ?>">
					                        <span class="text text-danger smtp_password_error"><?php echo form_error('smtp_password'); ?>
					                     </div>
					                  </div>
					                   
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Port :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="port" name="port" placeholder="Port" maxlength="3" value="<?php echo set_value('port', get_settings('port')); ?>">
					                        <span class="text text-danger port_error"><?php echo form_error('port'); ?>
					                     </div>
					                  </div>
					                   
					                  <div class="row">
					                     <div class="col-sm-4">
					                        <h3 class="card-title">Encryption :</h3>
					                     </div>
					                     <div class="col-sm-8">
					                        <input type="text" class="form-control " id="encryption" name="encryption" placeholder="encryption" maxlength="10" value="<?php echo set_value('encryption', get_settings('encryption')); ?>">
					                        <span class="text text-danger encryption_error"><?php echo form_error('encryption'); ?>
					                     </div>
					                  </div>
					                <br>
				              		<input style="float: right;" id="smtp_setting_button"  type="submit" class="btn btn-info" value="Save">

				                  </form>
				                  <br>
				                  <br>
				               </div>
				                   	
				               <!-- /.card-body -->
				               <div class="card-footer text-center">
				                  <!-- <input type="submit" class="btn btn-info" value="Save"> -->
				               </div>
				               <!-- /.card-footer -->
				            </div>
				            <!-- /.card-body -->
				         </div>
				      </div>
				      <!-- /.col -->
				   </div>
				   <!-- /.row -->
				<!-- </form> -->
      </div>
      <!-- /.Container-fluid -->
    </section>
    <!-- /.content -->
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

<script type="text/javascript">
 var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
 	// $("#ini_setting").submit(function (e) {


 	$("#whatsapp_setting").on('submit', function(e) {
  
  	if(confirm('Are you sure to update?')== false){
  		return false;
  	}    
		$("[class$='_error']").html("");
		$(".custom_loader").html('<img src="' + img_path + '">');
		var url = $(this).attr('action'); // the script where you handle the form input.

		$.ajax({
		    type: "POST",
		    dataType: 'JSON',
		    url: url,
		    data: $("#whatsapp_setting").serialize(), // serializes the form's elements.
		    success: function (data, textStatus, jqXHR){
		    	console.log(data);
		        if (data.st == 1) {
		            $.each(data.msg, function (key, value) {
		                $('.' + key + "_error").html(value);
		            });
		        } 
		        else {
		            successMsg(data.msg);
		            
		            setTimeout(function() {
		            	window.location.reload();
		            },1000);
		            	
		        }
		        $(".custom_loader").html("");

		    },
		    error: function (jqXHR, textStatus, errorThrown)
		    {
		        $(".custom_loader").html("");
		        //if fails      
		    }
		});
    e.preventDefault();
  });


  $("#imap_setting").on('submit', function(e) {
  
  	if(confirm('Are you sure to update?')== false){
  		return false;
  	}    
		$("[class$='_error']").html("");
		$(".custom_loader").html('<img src="' + img_path + '">');
		var url = $(this).attr('action'); // the script where you handle the form input.

		$.ajax({
		    type: "POST",
		    dataType: 'JSON',
		    url: url,
		    data: $("#imap_setting").serialize(), // serializes the form's elements.
		    success: function (data, textStatus, jqXHR){
		    	console.log(data);
		        if (data.st == 1) {
		            $.each(data.msg, function (key, value) {
		                $('.' + key + "_error").html(value);
		            });
		        } 
		        else {
		            successMsg(data.msg);
		            
		            setTimeout(function() {
		            	window.location.reload();
		            },1000);
		            	
		        }
		        $(".custom_loader").html("");

		    },
		    error: function (jqXHR, textStatus, errorThrown)
		    {
		        $(".custom_loader").html("");
		        //if fails      
		    }
		});
    e.preventDefault();
  });

  $("#smtp_setting").on('submit', function(e) {
  
  	if(confirm('Are you sure to update?')== false){
  		return false;
  	}    
		$("[class$='_error']").html("");
		$(".custom_loader").html('<img src="' + img_path + '">');
		var url = $(this).attr('action'); // the script where you handle the form input.

		$("#smtp_setting_button").prop('disabled', true).text('Please Wait...');
		$.ajax({
		    type: "POST",
		    dataType: 'JSON',
		    url: url,
		    data: $("#smtp_setting").serialize(), // serializes the form's elements.
		    success: function (data, textStatus, jqXHR){
		    	console.log(data);
		        if (data.st == 1) {
		            $.each(data.msg, function (key, value) {
		                $('.' + key + "_error").html(value);
		            });
		            $("#smtp_setting_button").prop('disabled', false).text('Save');
		        } 
		        else {
		            successMsg(data.msg);
		            
		            setTimeout(function() {
		            	window.location.reload();
		            },1000);
		            	
		        }
		        $(".custom_loader").html("");

		    },
		    error: function (jqXHR, textStatus, errorThrown)
		    {
		        $(".custom_loader").html("");
		        //if fails      
		    }
		});
    e.preventDefault();
  });



  /*$("#mobile").on("keypress",function (event) {    
     //$(this).val($(this).val().replace(/[^\d].+/, ""));
     console.log("EVENT CODE "+event.which);
      if ((event.which < 48 || event.which > 57 ) && event.which !=43) {
          event.preventDefault();
      }
  });

  window.onload = () => {
   const myInput = document.getElementById('mobile');
   myInput.onpaste = e => e.preventDefault();
  }*/
</script>

