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
                   <!-- <a href="<?php //echo site_url('user/add_user') ?>" class="btn btn-sm btn-info btn-block" style="background: coral;border-color:coral">Add New User</a> -->
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
             <table class="table table-bordered table-hover table-sm" >
                <thead>
                  <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Action</th>
                    

                  </tr>
                </thead>
                <tbody>
                  <? $id=0;
                    if(count($loggedin_users)>0 ){
                     foreach($loggedin_users as $row):?>
                     <? $id++;?>
                       <tr>
                         <td><? echo $id;?></td>
                         <td><? echo $row->first_name?></td>
                         <td><? echo $row->last_name;?></td>
                         <td><? echo $row->username;?></td>
                         <td><? echo $row->email;?></td>
                         <td><? echo $row->active_login=='0'?'NO':'YES' ;?></td>
                         <td><? echo ddmmyyyy_date($row->last_login);?></td>

                         
                         <td>
                           <a href="#" style="color:crimson" class="release_user" id="<?php echo $row->id; ?>" sdata-toggle="tooltip" title="Release">Release</a>
                         </td>

                          
                         
                       </tr>
                      <? 
                      endforeach;
                    }else{ ?>
                   <tr>
                    <td colspan="8">No Record Found</td> 
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

 
<!-- /.modal -->
  

<!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->




<script>

 
 



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
    $('.release_user').click(function(){  
          var id = $(this).attr("id");  
          if(confirm("Are you sure you want to release this?")){  
              window.location="<?php echo base_url(); ?>login/userrelease/"+id;  
          }  
          else{  
              return false;  
          }  
    });  

  });

  //farhan :: 24-06-2021 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);

  



</script>