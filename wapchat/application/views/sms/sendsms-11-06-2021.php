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
              <li class="breadcrumb-item active">SMS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
          <!-- /.content-header -->

          <!-- Main content -->
          <div class="content">
            <?php $this->load->view('message') ?>
            <div class="container-fluid">
              <div class="row">
              <div class="col-md-8">
                  <!-- jquery validation -->
                  <div class="card card-info card-outline" style="border-color: coral;">
                    <div class="card-header">
                      <h3 class="card-title">Send SMS Messages</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="quickForm" action="<?php echo site_url('sms/send_sms')?>" method="post">
                      <div class="card-body">
                        <div class="form-group">
                          <label for="exampleInputEmail1">To</label>
                          <select class="form-control select2bs4" name="mobile[]" multiple="multiple" style="width: 100%;" data-placeholder="Select Mobile No">
                          <?php foreach($contacts as $row):?>
                            <?php 
                            
                            $sel='';
                            if( isset($_POST['mobile']) && in_array($row['mobile_no'],$_POST['mobile'])):
                              $sel='selected'; 
                            endif;
                              ?>
                          <option value="<? echo $row['mobile_no'];?>" <? echo $sel;?> ><? echo $row['name'];?>-<? echo $row['mobile_no'];?></option>
                          <?php endforeach;?>
                        </select>
                        <?php echo form_error('mobile[]'); ?>
                        </div>
                         <div class="form-group">
                          <label for="message">Message</label>
                           <textarea class="form-control" id="message" placeholder="message write here..." name="message" maxlength="1600" style="height: 192px;"><? echo set_value('message');?></textarea>
                           <span class="limi" id="counter"></span>
                           <?php echo form_error('message'); ?>
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
                           <input id="pickdate" class="form-control" placeholder="Select date and Time" autocomplete="off" name="date" value="<? echo set_value('date');?>">
                        </div>
                        <?php echo form_error('date'); ?>
                     
                      </div>
                       
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer">
                         <button type="submit" class="btn btn-info" style="background: coral;border-color:coral">Send Now</button>
                      </div>
                    </form>
                  </div>
                  <!-- /.card -->
                  </div>
              </div>
              <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
          </div>
          <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- End of content -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer --> 
        <?php $this->load->view('layout/footer');?>
        <!-- End of footer -->
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

<script type="text/javascript">
// for datepicker
		$('#pickdate').datetimepicker({
      format : 'd-m-Y H:i',
      formatTime: 'H:i',
      formatDate : 'd-m-Y',
      step : 30,
      minDate:new Date()
		});

    $(function () {
    //Initialize Select2 Elements : Multiple select
        $('.select2bs4').select2({
          theme: 'bootstrap4'
        })
    // for Schedule toggle : Checkbox 
        $("#scheduletime").click(function () {
            if ($(this).is(":checked")) {
             $("#hi").show();
            } else {
             $("#hi").hide();
            }
        });
    });

        //Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);

	</script>
