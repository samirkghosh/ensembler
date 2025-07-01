<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php $this->load->view('layout/header'); ?>
<!-- Header End  -->

<!-- Main Sidebar Container -->
<?php  $this->load->view('layout/sidebar');?>
<!-- End Main sidebar -->

<!-- Content page  -->
<div class="content">
  <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <!-- <div class="col-sm-6">
              <h1 class="m-0">Send Sms</h1>
            </div> --><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb"> <!-- float-sm-right -->
                <li class="breadcrumb-item"><a href="" class="link">Home</a></li>
                <li class="breadcrumb-item active">Template</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

   <!-- Main content -->
   <section class="content">
<? if($this->session->flashdata('fail')){?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <? echo $this->session->flashdata('fail')?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<? }?>
<? if($this->session->flashdata('success')){?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <? echo $this->session->flashdata('success')?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<? }?>
      <div class="row">
        <div class="col-md-3">
        <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title">SMS Template</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" id="addcnt" action="<?php echo site_url('smstemplate/form_validation')?>" method="post">
                <!-- insert -->
                <div class="card-body">
                <?php  
           if(isset($user_data))  
           {  
                foreach($user_data->result() as $row)  
                {  
           ?>  
                 <!-- Update -->
                  <div class="form-group">
                    <input type="text" class="form-control" id="tempname" name="tempname" placeholder="Name" maxlength="100" 
                    value="<?php echo $row->name;?>">
                    <?php echo form_error('tempname','<span style="font-size:15px;color:red">', '</span>'); ?>
                    </div>
                    
                  <div class="form-group">
                  <textarea class="form-control" id="tempcontent" name="tempcontent" maxlength="100" style="height: 150px;resize: none; " placeholder="Template Content..."><?php echo $row->template_content; ?></textarea>
                  <span class="limi" id="counter"></span>
                    <?php echo form_error('tempcontent','<span style="font-size:15px;color:red">', '</span>'); ?>
                  </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                <input type="hidden" name="hidden_id" value="<?php echo $row->id; ?>" />
                  <input type="submit" class="btn btn-info" value="Update" name="update">
                </div>
                <!-- /.card-footer -->
                <?php       
                }  
           }  
           else  
           {  
           ?>  

            <!-- Insert -->
                <div class="form-group">
                    <input type="text" class="form-control" id="tempname" name="tempname" placeholder="Name" maxlength="100" 
                    value="<?php echo set_value('tempname'); ?>">
                    <?php echo form_error('tempname','<span style="font-size:15px;color:red">', '</span>'); ?>
                    </div>
                    
                  <div class="form-group">
                  <textarea class="form-control" id="tempcontent" name="tempcontent" maxlength="100" style="height: 150px; resize: none;" placeholder="Template Content..."><? echo set_value('tempcontent');?></textarea>
                  <span class="limi" id="counter"></span>
                    <?php echo form_error('tempcontent','<span style="font-size:15px;color:red">', '</span>'); ?>
                  </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <input type="submit" class="btn btn-info" value="Save" name="insert">
                </div>
                <!-- /.card-footer -->

                <?php  
           }  
           ?>

              </form>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
        <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title">SMS Templates</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-hover" id="contacts">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Name</th>
                      <th>Template Content</th>
                      <!-- <th>Slug</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
               <? $id=0;
                if($fetch_data->num_rows() > 0 ){
                 foreach($fetch_data->result() as $row):?>
                 <? $id++;?>
                   <tr>
                     <td><? echo $id;?></td>
                     <td><? echo $row->name?></td>
                     <td><? echo $row->template_content;?></td>
                     <!-- <td><? echo $row->slug?></td> -->
                     <td>
                     <a href="<?php echo base_url(); ?>smstemplate/update_sms_template/<?php echo $row->id; ?>" style="color:mediumseagreen"><i class="nav-icons fas fa-edit"></i></a>
                     <a href="#" style="color:crimson;" class="delete_data" id="<?php echo $row->id ?>"><i class="nav-icons fas fa-trash"></i>
                     </a>
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
      </div>
      <!-- /.row -->
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



<script>
const messageEle = document.getElementById('tempcontent');
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
<script>
// Delete template
$(document).ready(function(){  
    $('.delete_data').click(function(){  
          var id = $(this).attr("id");  
          if(confirm("Are you sure you want to delete this?"))  
          {  
              window.location="<?php echo base_url(); ?>smstemplate/delete_sms_template/"+id;  
          }  
          else  
          {  
              return false;  
          }  
    });  
});  

$(function () {
    $("#contacts").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["excel", "pdf","colvis"]
    }).buttons().container().appendTo('#contacts_wrapper .col-md-6:eq(0)');
  });


//Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);
</script>
