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
<!-- Content Wrapper. Contains page content -->
  <div class="content">
    <!-- Content Header (Page header) -->
     <?php $this->load->view('breadcrumb') ?>

    <!-- Main content -->
    <section class="content">
      
   

    <div class="container-fluid">
       <?php $this->load->view('message') ?>
      <div class="row">
        <div class="col-md-3">
        <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title"><?php echo $breadcrumb?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" autocomplete="on" id="addcnt" action="<?php echo site_url('Contact/form_validation')?>" method="post">
                <div class="card-body">
                <?php  
           if(isset($user_data))  
           {  
                foreach($user_data->result() as $row)  
                {  
                  ?>  
                 <!-- Update -->
                  <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputfirstname" name="inputfirstname" placeholder="First Name" maxlength="25" value="<?php echo $row->first_name;?>">
                    <?php echo form_error('inputfirstname','<span style="font-size:15px;color:red">', '</span>'); ?>
                 </div>

                 <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputlastname" name="inputlastname" placeholder="Last Name" maxlength="25" value="<?php echo $row->last_name;?>">
                 </div>

                 <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputemail" name="inputemail" placeholder="Email" value="<?php echo $row->email;?>">
                 </div>

                 <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputreference" name="inputreference" placeholder="Reference ID" value="<?php echo $row->reference;?>">
                 </div>

                  <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputmobile" name="inputmobile" placeholder="Mobile No" maxlength="13" value="<?php echo $row->mobile_no; ?>">
                    <?php echo form_error('inputmobile','<span style="font-size:15px;color:red">', '</span>'); ?>
                  </div>
                  <?php if ( $this->rbac->hasPrivilege('contacts', 'can_add') ): ?>
                   <div class="form-group text-center">
                      <input type="hidden" name="hidden_id" value="<?php echo $row->id; ?>" />
                       <input type="submit" class="btn btn-info btn-sm" value="Update" name="update">
                   </div>
                  <?php endif; ?>  
                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer">
                   
                   
                  </div>
                  <?php       
                }  
           }  
           else  
           {  
              ?>  
                <!-- Insert -->
                  <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputfirstname" name="inputfirstname" placeholder="First Name" maxlength="25" value="<?php echo set_value('inputfirstname'); ?>" autocomplete="off">
                    <?php echo form_error('inputfirstname','<span style="font-size:15px;color:red">', '</span>'); ?>
                    </div>

                  <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputlastname" name="inputlastname" placeholder="Last Name" maxlength="25" value="<?php echo set_value('inputlastname'); ?>" autocomplete="off">
                 </div>

                 <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputemail" name="inputemail" placeholder="Email"  value="<?php echo set_value('inputemail'); ?>" autocomplete="off">
                 </div>

                 <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputreference" name="inputreference" placeholder="Reference ID" value="<?php echo set_value('inputreference'); ?>" autocomplete="off">
                 </div>
                    
                  <div class="form-group">
                    <input type="text" class="form-control form-control-sm" id="inputmobile" name="inputmobile" placeholder="Mobile No" maxlength="13" value="<?php echo set_value('inputmobile'); ?>" autocomplete="off">
                    <?php echo form_error('inputmobile','<span style="font-size:15px;color:red">', '</span>'); ?>
                  </div>
                  <?php if ( $this->rbac->hasPrivilege('contacts', 'can_add') ): ?>
                   <div class="form-group text-center">
                      <input type="submit" class="btn btn-info btn-sm " value="Save" name="insert">
                   </div>
                  <?php endif; ?>
                </div>
              
                      
           <?php } ?>  
              </form>
            </div>
        </div>

        <!-- /.col -->
        <div class="col-md-9">
        <div class="card card-info card-outline">
             <!--  <div class="card-header">
                <h3 class="card-title">Contacts</h3>
              </div> -->
              <!-- /.card-header -->
              <div class="card-body">
                <form id="remove_contact" name="remove_contact" method="post" enctype="multipart/form-data" >
                <table class="table table-bordered table-hover table-sm" id="contacts">
                  <thead>
                    <tr>
                      <th colspan="6">
                        <button type="submit" class="btn btn-warning btn-sm" id="delete_contact">Remove Select contact</button>
                       
                        </th>
                      </tr>
                    <tr>
                      <th style="width: 10px"><input type="checkbox" name="select_all" id="select_all" class="select_all"></th>
                      <th style="width: 10px">#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile no</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
               
                  </tbody>
                </table>
               </form> 
              </div>
              <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col -->
      </div>
      </div>
      <!-- /.Container-fluid -->
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
/*select all */
$("#select_all").click(function(){
    $("input[type=checkbox]").prop('checked', $(this).prop('checked'));

});

// Remove Single Contacts 
function remove_contacts(ele) {
  var id = ele.id;  
    if(confirm("Are you sure you want to delete this?")){  
        window.location="<?php echo base_url(); ?>contact/delete_contact/"+id;  
    }  
    else{  
        return false;  
    } 
}

// Remove multiple contacts

$("#remove_contact").on('submit', function(event) {
  event.preventDefault();

});

$("#remove_contact").on('submit', function(event) {
  event.preventDefault();
   
  var url = '<?php echo site_url('contact/remove_multiple_contacts')?>';
  $.ajax({
    url:url,
    method:'POST',
    data:new FormData(this),
    dataType:'JSON',
    processData:false,
    contentType:false,
    cache:false,
    success:function(data, textStatus, jqXHR){
      console.log(data);
      if(data.status=='success'){
         successMsg(data.msg);
         window.location.href="<?php echo base_url(); ?>contact";  
        // $("#indecator").html('YES').css('color', 'green').css('background-color', 'green');
      }
      else{
        errorMsg(data.msg);
        // $("#indecator").html('NO').css('color', 'red').css('background-color', 'red');
      }
    },
    error:function(jqXHR, textStatus, errorThrown){},
    complete:function(){},
  });
}); 



$(function () {
  $("#contacts").DataTable({
    "searching": false,
    "responsive": true, 
    "pageLength": 100, 
    "lengthChange": false,
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "searchable" : false,
   /* "columns": [
      { "width": "50%" }
    ],*/ 
    "ajax":{
        "url": "<?php echo site_url('contact/get_contacts') ?>",
        "dataType": "json",
        "type": "POST",
        "data" : { name : '<?php //echo $report_name; ?>',
                }
    }, 
    "columns": [
        { "data": "" },
        { "data": "#" },
        { "data": "name" },
        { "data": "email" },
        { "data": "mobile" },
        { "data": "action" }
      
      ],
    "columnDefs": [{
        targets: "_all",
        orderable: false
     }],
    "dom": "Bfrtip",
    "buttons": [ {
                  extend: 'excelHtml5',
                  text: '<i class="fas fa-file-excel"></i>',
                  titleAttr: 'Excel',
                 
                  title: $('.download_label').html(),
                  exportOptions: {
                      columns: ':visible'
                  }
                  }, {
                      extend: 'colvis',
                      text: '<i class="fa fa-columns"></i>',
                      titleAttr: 'Columns',
                      title: $('.download_label').html(),
                      postfixButtons: ['colvisRestore']
                  },],

    
    // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  });
});
    



// Code for input only numeric value : Mobile No 
  $("#inputmobile").on("keypress",function (event) {    
     $(this).val($(this).val().replace(/[^\d].+/, ""));
     console.log("EVENT CODE "+event.which);
      if ((event.which < 48 || event.which > 57 ) && event.which !=43) {
          event.preventDefault();
      }
  });




//Fadeout alert(success and failed) after 2 seconds 
 setTimeout(function(){
   $('.alert').fadeOut('slow');
 },2000);




 // Delete Contact
$(document).ready(function(){ 
  $('.delete_data').click(function(){  
    console.log('OK');
      var id = $(this).attr("id");  
      if(confirm("Are you sure you want to delete this?"))  
      {  
          window.location="<?php echo base_url(); ?>contact/delete_contact/"+id;  
      }  
      else  
      {  
          return false;  
      }  
  });  
});
</script>


