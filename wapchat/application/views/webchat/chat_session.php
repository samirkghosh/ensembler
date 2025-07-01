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
              <li class="breadcrumb-item active">Bot session list</li>
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
              <h3 class="card-title">Bot session List</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                 <div class="table-responsive">
             <table class="table table-bordered table-hover table-sm" id="contacts">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>Name</th>
                    <th>Agent Forwarded</th>
                    <th>Joined By</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <? $id=0;
                    if(count($bot_session)>0 ){
                     foreach($bot_session as $row):?>
                     <? $id++;?>
                       <tr>
                         <td><? echo $id;?></td>
                         <!-- <td><? echo $row->to?></td> -->
                         <td><? echo $row->from;?> </td>
                         <td> <?php echo $this->auth_model->get_contact($row->from)?></td>
                         <td><? echo ($row->agent_forworded =='1') ? 'Agent Converted' : '' ;?></td>
                         <td> <?php echo isset($this->user_model->getUsers($row->user_id)->username) ? $this->user_model->getUsers($row->user_id)->username : ''?></td>
                         <td> 
                          <?php if($row->user_id == 0 ): ?>
                          <a href="javascript:void(0)" id="joinbtn_<?php echo $row->conversation_id ;?>" onclick="join_agent_chat('<?php echo $row->conversation_id ?>')" >Join</button>
                            <?php else: ?>
                              Joined By Agent.
                            <?php endif; ?>
                          </td>
                          
                        
                       </tr>
                      <? 
                      endforeach;
                    }else{ ?>
                   <tr>
                    <td colspan="6">No Record Found</td>
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

<!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->

<script type="text/javascript">
	function join_agent_chat(conversation_id) {
		console.log('zConvertion id '+ conversation_id );
	  var url = '<?php echo site_url('agentbot/agentjoin_customer_bot_ajax')?>';
	  $("#joinbtn_"+conversation_id).text('joining...').prop('disabled', true);
	  $.ajax({
	    url:url,
	    data:{'conversation_id' : conversation_id},
	    dataType:'JSON',
	    method:'POST',
	    // processData:false,
	    // contentType:false,
	    // cache:false,
	    success:function(data, textStatus, jqXHR){

	      console.log('data');
	      console.log(data);
	      if(data.status=='success'){
            	$("#joinbtn_"+conversation_id).text('joined').prop('disabled', false);
	      	successMsg(data.msg);
            setTimeout(function(){
              window.location.href = '<?php echo site_url('agentbot/agentwindow')?>';
            },2000); 
	      }
	      else{
	      	$("#joinbtn_"+conversation_id).text('join').prop('disabled', false);
	        errorMsg(data.msg);
	      }
	    },
	    error:function(jqXHR, textStatus, errorThrown){},
	    complete:function(){},

	  });
	}
</script>

<script>

</script>