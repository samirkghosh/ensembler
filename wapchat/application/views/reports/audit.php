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
              <li class="breadcrumb-item active"><?php echo $breadcrumb; ?></li>
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
              <h3 class="card-title"><?php echo $breadcrumb; ?></h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                 
                 
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
                    <th>Name</th>
                    <th>Changes</th>
                  
                    <th>Change Time</th>

                  </tr>
                </thead>
                <tbody>
                  <? $id=1;
                    if(count($audit)>0 ){
                     foreach($audit as $row):?>
                     <? 
                     $change = '';
                    
                     $new = array_diff( json_decode(str_replace('\n', "", $row->previous_data), true) , json_decode($row->new_data, true) );
                     if(!empty($new)){
                        $p = json_decode( str_replace("\n", "", $row->previous_data) );
                        $n = json_decode($row->new_data);
                        
                        if($p->wa_channel_id != $n->wa_channel_id){
                          $change .= "Whatsapp Channel Id Change <b>From</b> ".$p->wa_channel_id ." <b>To</b> ".$n->wa_channel_id."<br>"; 
                        }
                        if($p->wa_live_key != $n->wa_live_key){
                          $change .= "Whatsapp API Key Change <b>From</b> ".$p->wa_live_key ." <b>To</b> ".$n->wa_live_key."<br>"; 
                        } 
                        if($p->imap_host != $n->imap_host){
                          $change .= "Imap Host Change <b>From</b> ".$p->imap_host ." <b>To</b> ".$n->imap_host."<br>"; 
                        }
                        if($p->imap_email != $n->imap_email){
                          $change .= "Imap Email Change <b>From</b> ".$p->imap_email ." <b>To</b> ".$n->imap_email."<br>"; 
                        }
                        if($p->imap_pass != $n->imap_pass){
                          $change .= "Imap Password Change <b>From</b> ".$p->imap_pass ." <b>To</b> ".$n->imap_pass."<br>"; 
                        }

                        if($p->smtp_host != $n->smtp_host){
                          $change .= "SMTP Host Change <b>From</b> ".$p->smtp_host ." <b>To</b> ".$n->smtp_host."<br>"; 
                        }
                        if($p->from_email != $n->from_email){
                          $change .= "SMTP Email Change <b>From</b> ".$p->from_email ." <b>To</b> ".$n->from_email."<br>"; 
                        }
                        if($p->smtp_password != $n->smtp_password){
                          $change .= "SMTP Password Change <b>From</b> ".$p->smtp_password ." <b>To</b> ".$n->smtp_password."<br>"; 
                        }

                        if($p->port != $n->port){
                          $change .= "SMTP PORT Change <b>From</b> ".$p->port ." <b>To</b> ".$n->port."<br>"; 
                        }
                        if($p->encryption != $n->encryption){
                          $change .= "SMTP Encryption Change <b>From</b> ".$p->encryption ." <b>To</b> ".$n->encryption."<br>"; 
                        }

                        /*InI  Variables*/

                        if($p->loglevel != $n->loglevel){
                          $change .= "Log Leve Change <b>From</b> ".$p->loglevel ." <b>To</b> ".$n->loglevel."<br>"; 
                        }
                        if($p->logpath != $n->logpath){
                          $change .= "Log Path Change <b>From</b> ".$p->logpath ." <b>To</b> ".$n->logpath."<br>"; 
                        } 
                        if($p->pollinterval != $n->pollinterval){
                          $change .= "PollInterval Change <b>From</b> ".$p->pollinterval ." <b>To</b> ".$n->pollinterval."<br>"; 
                        }
                        if($p->smpphost != $n->smpphost){
                          $change .= "SMPPHOST Change <b>From</b> ".$p->smpphost ." <b>To</b> ".$n->smpphost."<br>"; 
                        }
                        if($p->port != $n->port){
                          $change .= "SMPP PORT Change <b>From</b> ".$p->port ." <b>To</b> ".$n->port."<br>"; 
                        }

                        if($p->systemid != $n->systemid){
                          $change .= "System ID Change <b>From</b> ".$p->systemid ." <b>To</b> ".$n->systemid."<br>"; 
                        }
                        if($p->password != $n->password){
                          $change .= "Sysytem Password Change <b>From</b> ".$p->password ." <b>To</b> ".$n->password."<br>"; 
                        }
                        if($p->dbhost != $n->dbhost){
                          $change .= "DB HOST Change <b>From</b> ".$p->dbhost ." <b>To</b> ".$n->dbhost."<br>"; 
                        }

                        if($p->dbuid != $n->dbuid){
                          $change .= "DB USER ID Change <b>From</b> ".$p->dbuid ." <b>To</b> ".$n->dbuid."<br>"; 
                        }
                        if($p->dbpwd != $n->dbpwd){
                          $change .= "DB PASSWORD Change <b>From</b> ".$p->dbpwd ." <b>To</b> ".$n->dbpwd."<br>"; 
                        }

                         if($p->dbname != $n->dbname){
                          $change .= "DB NAME Change <b>From</b> ".$p->dbname ." <b>To</b> ".$n->dbname."<br>"; 
                        }
                        if($p->sms2email != $n->sms2email){
                          $change .= "SMS <b>To</b> Email Change <b>From</b> ".$p->sms2email ." <b>To</b> ".$n->sms2email."<br>"; 
                        }
                        if($p->email_from != $n->email_from){
                          $change .= "From Email Change <b>From</b> ".$p->email_from ." <b>To</b> ".$n->email_from."<br>"; 
                        }

                        if($p->email_to != $n->email_to){
                          $change .= "To Email Change <b>From</b> ".$p->email_to ." <b>To</b> ".$n->email_to."<br>"; 
                        }
                        if($p->from != $n->from){
                          $change .= "From Change <b>From</b> ".$p->from ." <b>To</b> ".$n->from."<br>"; 
                        }
                        if($p->maxduplicate != $n->maxduplicate){
                          $change .= "MaxDuplicate Change <b>From</b> ".$p->maxduplicate ." <b>To</b> ".$n->maxduplicate."<br>"; 
                        }
                        if($p->batchsize != $n->batchsize){
                          $change .= "Batch Size Change <b>From</b> ".$p->batchsize ." <b>To</b> ".$n->batchsize."<br>"; 
                        }





                        ?>
                       <tr>
                         <td><? echo $id++;?></td>
                         
                         <td><? echo $row->user_id;?></td>
                         <td><? echo $change;?></td>
                         <td><? echo date('d-m-Y H:i:s', strtotime($row->date_time));?></td>
                        
                         
                         
                         </td>
                       </tr>
                      <?
                     }

                      
                     
                      
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