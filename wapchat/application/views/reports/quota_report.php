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
<?php  $this->load->view('layout/sidebar');?>
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
              <form name="report_search" id="report_search" action="<?php echo site_url('reports/quota_report') ?>"  method="post" >
              <div class="row">

                <div class="col-md-2"> Report :             
                    <select name="report_name" class="form-control form-control-sm">
                      <option value="sms" <?php echo ($report_name=='sms')?'selected':'selected' ?> >SMS</option>
                      <!-- <option value="whatsapp" <?php echo ($report_name=='whatsapp')?'selected':'' ?>>Whatsapp</option> -->
                    </select>
                  </div>
          
                  <div class="col-md-2"> Users :             
                      <select name="user_wise" id="user_wise" class="form-control form-control-sm">
                          <option value="all" >All</option>
                          <?php if(count($users) >0 ): 
                            foreach ($users as $key => $user):
                              if(isset($_POST['user_wise'])&& $_POST['user_wise']==$user->id):
                                $sel="selected";
                                else: 
                                $sel='';
                                endif;          
                            ?> 
                              <option value="<?=$user->id?>" <?php echo $sel?>><?=$user->username?></option>
                            <?php endforeach;
                            endif;
                            ?>
                        </select>
                   </div>

                      <div class="col-md-0" style="margin-top:24px;"> 
                        <button type="submit" class="btn btn-sm" title="Submit"><i class="far fa-paper-plane"></i>Send</button>
                      </div>
              </div>

      

          
              </div>

              </form>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                  <thead>
                    <tr>
                      <th>#</th>
                      <!-- <th>Username</th> -->
                      <?php if($report_name=='sms'):?>
                        <!-- <th>Previous Quota</th>
                        <th>New Quota</th>
                        <th>Total SMS Quota</th> -->
                        <th>Alloted Till Now</th>
                        <th>Available Till Now</th>
                        <th>Consumed Till Now</th>

                      <?php endif; ?>
                      <?php if($report_name=='whatsapp'):?>
                        <th>Previous Quota</th>
                        <th>New Quota</th>
                        <th>Total Whatsapp Quota</th>
                      <?php endif; ?>
                      <!-- <th>Update Date Time</th> -->
                    </tr>
                  </thead>
                  <tbody id="records">
               <? $id=0;
                if(count($quota_record) > 0 || !empty($quota_record)){
                 foreach($quota_record as $row):?>
                 <? $id++;?>
                   <tr>
                     <td><? echo $row['user_id'];?></td>
                     <td><? echo $row['total_alloted']?></td>
                     <td><? echo $this->auth_model->get_quota_sms($row['user_id']) ?></td>
                     <td><? echo $this->auth_model->get_consumend_quota_sms($row['user_id'])  ?></td>
                     
                     <!-- <td><? echo $row['user_id'];?></td> -->
                     <!-- <td><? //echo ddmmyyyy_date($row['created_date']) ?></td> -->
                   </tr>
                  <? 
                  endforeach;
                }else{ ?>
                   <tr>
                    <td colspan="5">No Record Found</td>
                  </tr>
                <? }?>
                  </tbody>
                </table>
              </div>  <!-- /. Responseive close -->
                  
              
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

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script> -->
<!-- Bootstrap -->
<!-- <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<!-- AdminLTE -->
<!-- <script src="<?php echo base_url() ?>assets/dist/js/adminlte.js"></script> -->


