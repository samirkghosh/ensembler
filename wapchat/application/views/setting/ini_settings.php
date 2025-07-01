<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

 
   
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  $this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

<style type="text/css">
  code {
  font-family: Consolas,"courier new";
  color: crimson;
  background-color: #f1f1f1;
  padding: 2px;
  font-size: 105%;
}
</style>


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
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <!-- <a href="compose.html" class="btn btn-primary btn-block mb-3">Compose</a> -->

          <div class="card card-info card-outline" style="border-color: coral;">
            <div class="card-header">
              <h3 class="card-title"><?php echo ucwords($breadcrumb) ?> <span style="background:#FF7F50; height: 5px; " >&nbsp;&nbsp;&nbsp;&nbsp;</span> Need to Resrat SMPP Service after changes </h3>

             <!--  <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div> -->
            </div>
            <div class="card-body p-0">
              <div class="card-body">
                <span>SMPP Restart Commad : &nbsp;&nbsp;&nbsp;&nbsp; <code>service smppclinet restart</code> </span>
                <!-- Row 1 -->
                <form role="form" name="ini_setting" id="ini_setting" action="<?php echo site_url('setting/ini_update') ?>" method="post" >
	               	<div class="row">
	               		<div class="col-md-12">
	               			<table class="table table-sm">
	               				<thead>
	               					<tr>
	               						<th>key</th>
	               						<th>value</th>
	               						<th>Description</th>
	               					</tr>
	               				</thead>
	               				<tbody> 
	               					<tr><td>Log Level</td> <td><input type="text" placeholder="e.g 255" name="loglevel" value="<?php echo isset($loglevel) ? $loglevel : ''?>"><span class="text text-danger loglevel_error"></span></td> <td>Values: 0 to 255</td> </tr>
	               					<tr><td>Log Path</td> <td><input type="text" placeholder="e.g /var/log/smpp" name="logpath" value="<?php echo isset($logpath) ? $logpath : ''?>"><span class="text text-danger logpath_error"></span></td> <td>Where logs files are kept</td> </tr>
	               					<tr><td>Poll Interval</td> <td><input type="text" placeholder="e.g 10" name="pollinterval" value="<?php echo isset($pollinterval) ? $pollinterval : ''?>"><span class="text text-danger pollinterval_error"></span></td> <td>Unit in Sec,Outqueue Checking Interval</td> </tr>
	               					<tr style="background:#FF7F50"><td>SMPP Host</td> <td><input type="text" placeholder="e.g xxx.xxx.xxx.xxx" minlength="9" maxlength="15"  name="smpphost" value="<?php echo $smpphost  ?>"><span class="text text-danger smpphost_error"></span> </td> <td>SMSC Server IP Address</td> </tr>
	               					<tr style="background:#FF7F50"><td>SMPP port</td> <td><input type="text" placeholder="e.g 9500" name="port" value="<?php echo isset($port) ? $port : ''?>"><span class="text text-danger port_error"></span></td> <td>SMSC Gateway Port</td> </tr>
	               					<tr style="background:#FF7F50"><td>System ID</td> <td><input type="text" placeholder="e.g " name="systemid" value="<?php echo isset($systemid) ? $systemid : ''?>"><span class="text text-danger systemid_error"></span></td> <td>SMSC Gateway User ID</td> </tr>
	               					<tr style="background:#FF7F50"><td>System Password</td> <td><input type="text" placeholder="e.g " name="password" value="<?php echo isset($password) ? $password : ''?>"><span class="text text-danger password_error"></span></td> <td>SMSC Gateway Password</td> </tr>
	               					<tr style="background:#FF7F50"><td>DB Host</td> <td><input type="text" placeholder="e.g DB Host" name="dbhost" value="<?php echo isset($dbhost) ? $dbhost : ''?>"><span class="text text-danger dbhost_error"></span></td> <td>Database Host IP</td> </tr>
	               					<tr style="background:#FF7F50"><td>DB User</td> <td><input type="text" placeholder="e.g DB username" name="dbuid" value="<?php echo isset($dbuid) ? $dbuid : ''?>"><span class="text text-danger dbuid_error"></span></td> <td>SDatabase User ID</td> </tr>
	               					<tr style="background:#FF7F50"><td>DB Pass</td> <td><input type="text" placeholder="e.g DB password" name="dbpwd" value="<?php echo isset($dbpwd) ? $dbpwd : ''?>"><span class="text text-danger dbpwd_error"></span></td> <td>Database Password</td> </tr>
	               					<tr style="background:#FF7F50"><td>DB Name</td> <td><input type="text" placeholder="e.g DB name" name="dbname" value="<?php echo isset($dbname) ? $dbname : ''?>"><span class="text text-danger dbname_error"></span></td> <td>Database Name</td> </tr>
	               					<tr><td>From Email</td> <td><input type="text" placeholder="e.g " name="email_from" value="<?php echo isset($email_from) ? $email_from : ''?>"><span class="text text-danger email_from_error"></span></td> <td>From address when sending to email</td> </tr>
	               					<tr><td>To Email</td> <td><input type="text" placeholder="e.g " name="email_to" value="<?php echo isset($email_to) ? $email_to : ''?>"><span class="text text-danger email_to_error"></span></td> <td></td> </tr>	
	               					<tr><td>SMS2Email</td> <td><input type="text" placeholder="e.g 0" name="sms2email" value="<?php echo isset($sms2email) ? $sms2email : ''?>"><span class="text text-danger sms2email_error"></span></td> <td>(0-1) : Send Incomg SMS to Email</td> </tr>
	               					<tr><td>From</td> <td><input type="text" placeholder="e.g XXXXX" name="from" value="<?php echo isset($from) ? $from : ''?>"><span class="text text-danger from_error"></span></td> <td>SMPP Client Code</td> </tr>
                          <!-- <tr><td>To</td> <td><input type="text" placeholder="e.g XXXXXX" name="to" value="<?php echo isset($to) ? $to : ''?>"><span class="text text-danger to_error"></span></td> <td></td> </tr> -->
	               					<tr><td>Max Duplicate</td> <td><input type="text" placeholder="e.g 3" name="maxduplicate" value="<?php echo isset($maxduplicate) ? $maxduplicate : ''?>"><span class="text text-danger maxduplicate_error"></span></td> <td>Maximum Duplicate SMS Allowed (0-10)</td> </tr>
	               					<tr><td>Batch Size</td> <td><input type="text" placeholder="e.g 10" name="batchsize" value="<?php echo isset($batchsize) ? $batchsize : ''?>"><span class="text text-danger batchsize_error"></span></td> <td>(1-100) SMS Sending Batch Size</td> </tr>
	               				</tbody>
	               			</table>
	               		</div>
	               	</div>
	               	<hr><br>
	               	<div class="col-md-6 offset-md-5">
	               	<button type="submit" class="btn btn-info col-md-offset-3" style="background: coral;border-color:coral"><?php echo $this->lang->line('save'); ?>
                  </button>
	               	</div>
               	</form>
                 
              </div>
                <!-- /.card-body -->
                 
              <!-- <div class="card-footer text-center">
                <input type="submit" class="btn btn-info" value="Save">
              </div> -->
              <!-- /.card-footer -->
             
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
  $("#ini_setting").on('submit', function(e) {
    
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
          data: $("#ini_setting").serialize(), // serializes the form's elements.
          success: function (data, textStatus, jqXHR){
          	console.log(data);
              if (data.st == 1) {
                  $.each(data.msg, function (key, value) {
                      $('.' + key + "_error").html(value);
                  });
              } 
              else {
                  successMsg(data.msg);
                  window.location.reload();
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

/*
  $("#mobile").on("keypress",function (event) {    
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

