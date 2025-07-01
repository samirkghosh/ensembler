<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php //$this->load->view('layout/header')?>
   
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  //$this->load->view('layout/sidebar');?>
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
          <div class="col-sm-5">
            <ol class="breadcrumb ">
              <li class="breadcrumb-item"><a href="#" class="link">Home</a></li>
              <li class="breadcrumb-item active">Inbox</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <?php //print_r($user_detail);  ?>
    <!-- Main content -->
    <section class="content">
       <?php $this->load->view('message') ?>
        <div class="container-fluid">
      <form class="form-horizontal" id="addcnt" action="<?php echo site_url('user/edit/'.$user_id)?>" method="post">
        <div class="row">
        <div class="col-md-7">
          <!-- <a href="compose.html" class="btn btn-primary btn-block mb-3">Compose</a> -->

          <div class="card card-info card-outline" style="border-color: coral;">
            <div class="card-header">
              <h3 class="card-title"><?php echo $user_id>0?'Update User':'Add New User'?></h3>

             <!--  <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div> -->
            </div>
            <div class="card-body p-0">
            
                <!-- farhan : 24-06-2021-->
                <div class="card-body">
                <!-- Row 1 -->
                <div class="row">
                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >First Name</label>
                        <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" maxlength="100" value="<?php echo set_value('fname',$user_detail->first_name); ?>">
                        <?php echo form_error('fname'); ?>
                      </div>
                   </div>

                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >Last Name</label>
                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" maxlength="100" value="<?php echo set_value('lname',$user_detail->last_name); ?>">
                        <?php echo form_error('lname'); ?>
                      </div>
                   </div>

                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >User Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="User Name" maxlength="100" value="<?php echo set_value('name',$user_detail->username); ?>">
                        <?php echo form_error('name'); ?>
                      </div>
                   </div>
                </div>

                <!-- Row 2 -->
                <div class="row">
                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter User Email" maxlength="100" value="<?php echo set_value('email',$user_detail->email); ?>">
                        <?php echo form_error('email'); ?>
                      </div>

                   </div>

                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter User mobile" maxlength="12" value="<?php echo set_value('mobile',$user_detail->mobile); ?>">
                        <?php echo form_error('mobile'); ?>
                      </div>
                   </div>

                   <div class="col-sm-4">
                      <div class="form-group">
                        <label >SMS Quota</label>
                        <input type="number" class="form-control" id="sms_quota" name="sms_quota" placeholder="Enter sms quota" maxlength="15" value="<?php echo set_value('sms_quota',$user_detail->sms_quota); ?>">
                        <?php echo form_error('sms_quota'); ?>
                      </div>
                   </div>
                </div>

                <!-- Row 3 -->
                <div class="row">
                   <div class="col-sm-4" style="display:none;">
                      <div class="form-group">
                        <label >Whatsapp Quota</label>
                        <input type="number" class="form-control" id="whatsapp_quota" name="whatsapp_quota" placeholder="Enter whatsapp quota" maxlength="15" value="0">
                        <?php echo form_error('whatsapp_quota'); ?>
                      </div>
                   </div>

                   <div class="col-sm-4">
                        <div class="form-group">
                        <label class="card-title">Select Role</label>
                          <select class="form-control" name="roles">
                            <option value="0">--Select Roles--</option>
                              <?php
                                foreach ($user_groups as $s_key => $s_value) {
                                    ?>

                                
                                  <option value="<?php echo $s_value->id; ?>" <?php echo $s_value->id == $user_detail->role_id?'selected':'';?> ><?php echo $s_value->name; ?></option>
                              <?php
                                }
                                ?>
                          </select>
                        </div>

                   </div>
                   
                   <div class="col-sm-4">
                        <div class="form-group">
                            <label class=" control-label"><?php echo $this->lang->line('status'); ?></label>
                            <div class="form-group">
                              <select class="form-control" name="custom_status">
                              <?php
                                foreach ($statuslist as $s_key => $s_value) {
                                  if ( $s_key=='')
                                      continue;
                                    ?>
                              <option 
                                value="<?php echo $s_key; ?>"
                                <?php
                                    if ( $s_key==$user_detail->is_active) {
                                        echo "selected=selected";
                                    }
                                    ?>
                                ><?php echo $s_value; ?></option>
                              <?php
                                }
                                ?>
                              </select>
                              <span class=" text text-danger clickatell_api_id_error"></span>
                            </div>
                        </div>
                   </div>
                </div>

               <!-- End of rows-->   

                  
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-right">
                  <input type="submit" class="btn btn-info" value="Save">
                </div>
                <!-- /.card-footer -->
              
            </div>
            <!-- /.card-body -->
          </div>
          
        </div>
        <!-- /.col -->

        <div class="col-md-5">
          <div class="card card-info card-outline" style="border-color: coral;">
            <div class="card-header">
              <h3 class="card-title">Users SMS Templates </h3>

               
            </div>
            <!-- /.card-header -->
            <div class="card-body">
             <table class="table table-bordered table-hover" id="contacts">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Content</th>
                  </tr>
                </thead>
                <tbody>
                  <? $id=0;
                  
                    if(count($sms_templates->result())>0 ){
                     foreach($sms_templates->result() as $row):?>
                     <? $id++;?>
                       <tr>

                         <td><input type="checkbox" <?php echo !empty($user_detail->templates_id) ? (in_array($row->id, explode(",", $user_detail->templates_id))?'checked':'') : '' ?> name="templates[]" value='<?php echo $row->id ?>'> </td>
                         <td><? echo $row->name;?></td>
                         <td><? echo $row->template_content;?></td>
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
               
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
           
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
       </form>
     </div>
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
  }
</script>

<?php //$this->load->view('layout/footerjs')?>   