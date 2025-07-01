<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  //$this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

  <!-- Content page  -->
  <div class="content">
    <!-- Content Header (Page header) -->
     <?php  $this->load->view('layout/sidebar');?>
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
                <h3 class="card-title">Message</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" novalidate="novalidate">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Whatsapp No</label>
                    <input type="text" class="form-control" name="to" placeholder="Enter Whatsapp No">
                  </div>
                  <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" maxlength="1600" style="height: 192px;"></textarea>
                     <span class="limi" id="counter"></span>
                    <span class="limi"></span>
                  </div>
                  <div class="form-group">
                  <div class="btn btn-default btn-file">
                    <i class="fas fa-paperclip"></i> Attachment
                    <input type="file" name="attachment">
                  </div>
                  <p class="help-block">Max. 32MB</p>
                </div>
                  
               
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info" style="background: coral;border-color:coral">Send</button>
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
        <?php //$this->load->view('cms/footer');?>
        <!-- End of footer -->
    </div>
    <!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script>
<!-- jquery-validation -->
<script src="<?php echo base_url() ?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
<!-- OPTIONAL SCRIPTS -->
<script src="<?php echo base_url() ?>assets/plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url() ?>assets/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url() ?>assets/dist/js/pages/dashboard3.js"></script>
<!-- Select2 -->
<script src="<?php echo base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!-- Datetimepicker -->
<script src="<?php echo base_url() ?>assets/dist/js/jquery.datetimepicker.full.min.js"></script>

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
</body>
</html>
