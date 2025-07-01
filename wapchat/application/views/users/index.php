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
              <li class="breadcrumb-item"><a class="link" href="#">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
      <div class="row">
       
        <!-- /.col -->
        <div class="col-md-12">
          <div class="card card-info card-outline" style="border-color: coral;">
            <div class="card-header">
              <h3 class="card-title">Users</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <?php if ( $this->rbac->hasPrivilege('add_user', 'can_add') ): ?>
                   <a href="<?php echo site_url('user/add_user') ?>" class="btn btn-sm btn-info btn-block" style="background: coral;border-color:coral">Add New User</a>
                   <?php endif; ?>
                  <!-- <input type="text" class="form-control" placeholder="Search Mail">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div> -->
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                 <div class="table-responsive">
             <table class="table table-bordered table-hover table-sm" id="contacts">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>SMS</th>
                    <!-- <th>Whatsapp</th> -->
                    <th>Role</th>
                    <th>Status</th>
                    <th>First Time Login</th>

                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <? $id=0;
                    if(count($users)>0 ){
                     foreach($users as $row):?>
                     <? $id++;?>
                       <tr>
                         <td><? echo $id;?></td>
                         <td><? echo $row->first_name?></td>
                         <td><? echo $row->last_name;?></td>
                         <td><? echo $row->username;?></td>
                         <td><? echo $row->mobile;?></td>
                         <td><? echo $row->email;?></td>
                         <td> <span style="cursor: pointer;" title="Update Sms Quota" onclick="update_quota_status('sms', '<?php echo  $row->id ?>', '<? echo $row->first_name.' '.$row->last_name?>')" > <? echo $row->sms_quota;?> <i class="fas fa-pencil-alt fa-xs"></i></span></div></td>
                         <!-- <td> <span style="cursor: pointer;" title="Update Whatsapp Quota" onclick="update_quota_status('whatsapp', '<?php echo  $row->id ?>', '<? echo $row->first_name.' '.$row->last_name?>')"><? echo $row->whatsapp_quota;?> <i class="fas fa-pencil-alt fa-xs"></i></span> </td> -->
                         <td><? echo $this->user_model->get_role_by_id($row->role_id);?></td>
                         <td><? echo $row->is_active;?></td>
                         <td><? echo $row->first_login_status=='0'?'NO':'YES' ;?></td>
                         <td>
                         <?php if ( $this->rbac->hasPrivilege('add_user', 'can_edit') ): ?> 
                         <a href="<?php echo site_url('roles/permission/'.$row->role_id) ?>" data-toggle="tooltip" title="Assign Permissions"> <i class="fas fa-user-tag"></i></a>&nbsp;&nbsp;
                         <a href="<?php echo site_url('user/edit/'.$row->id) ?>" style="color:mediumseagreen" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></a>&nbsp;&nbsp;
                         <?php endif; ?>
                         <!-- //farhan::24-06-2021 -->
                         <?php if ( $this->rbac->hasPrivilege('add_user', 'can_delete') ): ?> 
                         <a href="#" style="color:crimson" class="delete_data" id="<?php echo $row->id; ?>" sdata-toggle="tooltip" title="delete"><i class="fas fa-trash-alt"></i></a>
                         <?php endif; ?>
                         
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
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
           
          </div>
          <!-- /.card -->
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

<div class="modal fade" id="update_user_quota">
  <div class="modal-dialog modal-sm">
    <div class="modal-content bg-default">
      <div class="modal-header">
        <h6 class="modal-title" id="title"></h6>  
         <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal"> <i class="far fa-window-close fa-xs"></i> </button>      
      </div>
      <div class="modal-body">
       <h6 id="user_name"></h6>
        <br>
        <form method="post" name="update_quota", id="update_quota", action="<?php echo site_url('user/update_user_quota') ?>">
            <div class="form-group">
              <label>Enter Quota Value</label>
              <input  type="number" min="1" max="10000000" class="form-control form-control-sm" name="count_value" id="count_value" >
            </div>

            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="flag" id="flag">
            <br>
            <div class="form-group text-right">
              <button type="submit" class="btn  btn-sm" >Update</button>
            </div>
              
        </form>
      </div>
      <!-- <div class="modal-footer justify-content-between">
        <a id="replay_button" href="" class="" title="Reply" style="color:coral" ><i class="fas fa-reply"></i></a>
        <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">Close</button>
      </div>-->
    </div> 
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
  

<!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->




<script>

  function update_quota_status(flag, user_id, user_name) {
    $('#update_quota').trigger("reset");
    $("#user_id").val(user_id);
    $("#user_name").html('<strong>User</strong> :'+user_name);
    $("#flag").val(flag);
    if(flag == 'sms')
      $("#title").text('Update SMS Quota');
    else if(flag == 'whatsapp')
      $("#title").text('Update Whatsapp Quota');
  
    $("#update_user_quota").modal('show');
 }
   
    

 $("#update_quota").on('submit', function(e) {
   e.preventDefault();

   $.ajax({
      url:"<?php echo base_url(); ?>user/update_user_quota",
      method:"POST",
      data:new FormData(this),
      contentType:false,
      cache:false,
      processData:false,
      success:function(data, textStatus, jqXHR){
        console.log('DATA');
        console.log(data);
        var obj = JSON.parse(data)
        console.log(obj);
        console.log(obj.status);
        if(obj.status=='success'){
            successMsg(obj.message);                
            setTimeout(function() {
              window.location.reload();
              $("#update_user_quota").modal('hide');
            },2000);
        }
        else if(obj.status=='fail'){
            errorMsg(obj.message);
        }
       
      },
      error:function(jqXHR, textStatus, errorThrown){
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
      }
   });

        

 });



  $(function () {
    $("#contacts").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": true,
      // "buttons": ["excel", "colvis"]
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

  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   

   //farhan::24-06-2021
    $('.delete_data').click(function(){  
          var id = $(this).attr("id");  
          if(confirm("Are you sure you want to delete this?"))  
          {  
              window.location="<?php echo base_url(); ?>user/delete/"+id;  
          }  
          else  
          {  
              return false;  
          }  
    });  

  });

  //farhan :: 24-06-2021 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);

  



</script>