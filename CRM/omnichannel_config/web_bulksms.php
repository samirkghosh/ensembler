<?php
  include("bulk_function.php");  
  $list_BULK = new BULK();
  $recent_uploads = $list_BULK->get_bulk_upload_list();
  $campaign = $_GET['campaign'];
  $response_email = $list_BULK->get_email_list();
?>

<!-- <link rel="stylesheet" href="<?=$SiteURL?>public/bootstrap/css/bootstrap.css"> -->
<!-- this file disrupted the orignal css in the sidebar [vastvitka][19-05-2025] -->
<!-- <link rel="stylesheet" href="<?=$SiteURL?>public/bootstrap/css/bootstrap.min.css">-->
<link rel="stylesheet" href="<?=$SiteURL?>public/css/bulk_style.css">
<link rel="stylesheet" href="<?=$SiteURL?>public/css/select2.min.css">
<link rel="stylesheet" href="<?=$SiteURL?>public/css/select2-bootstrap4.min.css">
<style type="text/css">
  #single_send_button,
  #bulk_send_button{
    float: right;            /* Aligns the button to the right */
    color: white;            /* Sets text color to white */
    font-size: 0.9rem;       /* Slightly smaller text */
    background-color: #17a2b8; /* Optional: keeps Bootstrap's info blue */
    border: none;            /* Optional: cleaner look */
    padding: 8px 16px;       /* Optional: balanced padding */
    border-radius: 4px;      /* Optional: rounded corners */
    margin-bottom:20px;
}


   .select2-container--default .select2-selection--multiple {
       background-color: white;
       border: 1px solid #aaa;
       border-radius: 4px;
       cursor: text;
   }
   h2 {
    /* width: 100%; */
    text-align: center;
    border-bottom: 1px solid coral;
    line-height: 0.1em;
    margin: 30px 0 20px;
}
h2 span {
    background: #fff;
    padding: 0 10px;
}
.fade {
    opacity: 0;
    -webkit-transition: opacity .15s linear;
    -o-transition: opacity .15s linear;
    transition: opacity .15s linear
}

.fade.in {
    opacity: 1;
}

.collapse {
    display: none
}

.collapse.in {
    display: block
}

tr.collapse.in {
    display: table-row
}

tbody.collapse.in {
    display: table-row-group
}

.collapsing {
    position: relative;
    height: 0;
    overflow: hidden;
    -webkit-transition-timing-function: ease;
    -o-transition-timing-function: ease;
    transition-timing-function: ease;
    -webkit-transition-duration: .35s;
    -o-transition-duration: .35s;
    transition-duration: .35s;
    -webkit-transition-property: height,visibility;
    -o-transition-property: height,visibility;
    transition-property: height,visibility
}

.caret {
    display: inline-block;
    width: 0;
    height: 0;
    margin-left: 2px;
    vertical-align: middle;
    border-top: 4px dashed;
    border-top: 4px solid\9;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent
}

</style>
<div class="col-sm-10 mt-3" style="padding-left:0">
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Bulk <?php if($campaign == 'SMS'){?> SMS <?php }else if($campaign == 'WhatsApp'){ ?> WhatsApp <?php }else if($campaign == 'Email'){ ?> Email <?php }?></span>
   <div class="Reports-page#"
      style="display: block;border: #d4d4d4 1px solid;marginbottom:20px;min-height: 420px;margin-top:37px">
      <div class="card card-info card-outline card-outline-tabs">
        <div class="card-header" style="border-bottom: 0px solid rgba(0,0,0,.125);">
            <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
               <li class="nav-item">
                 <a class="nav-link link active" id="custom-content-above-home-tab" data-toggle="pill" href="#custom-content-above-home" role="tab" aria-controls="custom-content-above-home" aria-selected="false">Send Single  <?php if($campaign == 'SMS'){?> SMS <?php }else if($campaign == 'WhatsApp'){ ?> WhatsApp <?php }else if($campaign == 'Email'){ ?> Email <?php }?> </a>
               </li>
               <li class="nav-item">
                 <a class="nav-link link " id="custom-content-above-profile-tab" data-toggle="pill" href="#custom-content-above-profile" role="tab" aria-controls="custom-content-above-profile" aria-selected="false">Campaign </a>
               </li>                   
            </ul>
        </div>
            <div class="card-body" style="padding: 0px;">
                <div class="tab-content" id="custom-content-above-tabContent">             
                  <div class="tab-pane fade show active in" id="custom-content-above-home" role="tabpanel" aria-labelledby="custom-content-above-home-tab">
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="card card-info card-outline" style="margin-top: 1px;margin-left: 3px;margin-bottom:20px;">
                              <div class="card-header">
                                <h3 class="card-title">Send Messages</h3>
                              </div>
                           <form id="quickForm" method="post">
                              <input type="hidden" name="action" value="send_sms_index">
                              <div class="card-body">
                                 <span class="text text-danger custom_sms"></span>
                                 <div class="form-group">
                                  <label>Channel Type </label>
                                  <select class="form-control  selectchannel"  style="width: 100%;" name="channel_type_direct" id="channel_type">
                                    <?php if($campaign == 'SMS'){ ?>
                                      <option value="SMS" selected>SMS</option>
                                    <?php }?>
                                    <?php if($campaign == 'WhatsApp'){ ?>
                                      <option value="WhatsApp" selected>WhatsApp</option>
                                    <?php }?>
                                    <?php if($campaign == 'Email'){ ?>
                                        <option value="Email" selected>Email</option>
                                    <?php }?>
                                  </select>
                                </div>
                                <?php if($campaign == 'SMS' || $campaign == 'WhatsApp'){ ?>
                                 <div class="form-group phone_div">
                                     <label for="exampleInputEmail1">To</label>
                                       <select class="form-control select2bs4" name="mobile[]" id="mobile" multiple="multiple" style="width: 100%;" data-placeholder="Select Mobile No"></select>
                                     <span class="text text-danger mobile_error"></span>
                                 </div>
                                <?php }?>
                                <?php if($campaign == 'Email'){ ?>
                                   <div class="form-group email_div">
                                     <label for="exampleInputEmail1">Email</label>
                                       <select class="form-control" name="email" id="email" data-placeholder="Select Mobile No">
                                          <?php  while($value=mysqli_fetch_array($response_email)){ ?>
                                            <option value="<?php echo $value['email'];?>"><?php echo $value['email'];?></option>
                                        <?php }?>
                                          </select>
                                         
                                     <span class="text text-danger mobile_error"></span>
                                   </div>
                                   <div class="form-group email_div">
                                     <label for="exampleInputEmail1">Subject</label>
                                         <input type="text" class="form-control" id="Subject" name="Subject" value="">
                                     <span class="text text-danger mobile_error"></span>
                                   </div>
                                 <?php }?>
                                 <div class="form-group">
                                    <label for="message">Message</label>
                                    <div class="textarea_div">
                                      <textarea class="form-control" id="message" placeholder="write your message" name="message" maxlength="1600" style="height: 150px; resize: none;"></textarea>
                                      <textarea style="display:none;" type="hidden" id="output"></textarea>     
                                    </div>
                                       <span class="limi" id="counter"></span>
                                       <span class="text text-danger message_error"></span>
                                 </div>
                                 <div class="form-group">
                                 </div>
                                 <div class="form-group">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                       <input type="checkbox" class="custom-control-input" id="scheduletime" name="scheduletime">
                                       <label class="custom-control-label" for="scheduletime">Schedule</label>
                                    </div>
                                 </div>
                                 <div class="form-group" id="hi" style="display:none">
                                    <input id="pickdate" class="form-control pickdate" placeholder="Select date and Time" autocomplete="off" name="date" value="">
                                 </div>
                                    <span class="text text-danger date_error"></span>
                                 <div class="form-group text-right">
                                     <button type="submit" id="single_send_button" class="btn btn-info">Send Now</button>
                                 </div>
                              </div>                         
                           </form>
                         </div>
                        </div>
                        <div class="col-sm" style="margin-right: 19px;">
                          <div class="card card-info card-outline direct-chat direct-chat-info collapsed-card">
                            <div class="card-header">
                              <h3 class="card-title">Select Template</h3>
                              <div class="card-tools" style="margin:0px 0px 0px 0px">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                              </div>
                            </div>
                            <div class="card-body" style="padding-left: 20px;">
                              <?php
                              $template_assign = $list_BULK->get_templates($campaign);
                              if(!empty($template_assign)){
                                 $id=0;
                                 foreach ($template_assign as $value) 
                                 {
                                 $id++;
                                 ?>
                                 <div class="custom-control custom-radio">
                                 <!-- <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_<?php echo $id?>" name="customRadio2" > -->
                                 <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_<?php echo $id?>" name="customRadio2" value="<?php echo $value['name']?>">
                                 <label for="customRadio_<?php echo $id?>" class="custom-control-label" style="font-weight: 200;"><?php  echo $value['name'];?></label>

                                 </div>
                                 <?
                                 }
                              
                               }
                             ?>    
                              <div class="custom-control custom-radio">
                               <input class="custom-control-input custom-control-input-danger" type="radio" id="customRadio_clear" name="customRadio2">
                               <label for="customRadio_clear" class="custom-control-label" style="font-weight: 200;">None</label>
                             </div>      
                           </div>
                          </div>                    
                            <div class="card card-info card-outline direct-chat direct-chat-info" id="recent" style="display: none;">
                              <div class="card-header">
                                <h3 class="card-title">Last interaction 360°</h3>
                              </div>                                                        
                              <div class="card-body">
                                  <div class="direct-chat-messages interaction-chat">
                                  <div id="interaction_history" class="direct-chat-msg left"></div>
                                  </div>
                              </div>
                            </div>
                        </div>                                                      
                    </div>
                  </div>
              <!-- /..Close tabContent -->
                  <div class="tab-pane fade " id="custom-content-above-profile" role="tabpanel" aria-labelledby="custom-content-above-profile-tab">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="card card-info card-outline">
                          <div class="card-header">
                            <h3 class="card-title"> <?php if($campaign == 'SMS'){?> SMS <?php }else if($campaign == 'WhatsApp'){ ?> WhatsApp <?php }else if($campaign == 'Email'){ ?> Email <?php }?> Bulk Upload</h3>
                          </div>
                          <!-- /.card-header -->
                          <form method="post" id="import_form" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="import_file"> 
                            <div class="card-body">
                              <div class="form-group">
                                <label> Channel Type </label>
                                <select class="form-control  selectchannel"  style="width: 100%;" name="channel_type_select" id="channel_type_select" disabled>
                                  <?php if($campaign == 'SMS'){ ?>
                                    <option value="SMS" selected disabled>SMS</option>
                                  <?php }?>
                                  <?php if($campaign == 'WhatsApp'){ ?>
                                    <option value="WhatsApp" selected disabled>WhatsApp</option>
                                  <?php }?>
                                  <?php if($campaign == 'Email'){ ?>
                                      <option value="Email" selected disabled>Email</option>
                                  <?php }?>
                                </select>
                                 <input type="hidden" name="channel_type" value="<?php echo $campaign;?>"> 
                              </div>
                              <div class="form-group">
                                <label> Select Template</label>
                                <select class="form-control  select2bs4s"  style="width: 100%;" name="selecttemplate" id="selecttemplate">
                                  <option value="0">Select Template</option>
                                  <?php
                                    $template_assign = $list_BULK->get_templates($campaign);
                                    if(!empty($template_assign)){
                                       $id=0;
                                       foreach ($template_assign as $value) 
                                       {
                                       $id++;
                                       ?>
                                        <option value="<?php echo $value['name'];?>"><?php echo $value['name'];?></option>
                                      <?php } 
                                    }
                                  ?>
                                </select>
                              </div> 

                              <div class="form-group">
                                <label> Select Uploaded Campaign</label>
                                <select class="form-control  select2bs4s"  style="width: 100%;" name="select_file_name" id="select_file_name" onchange="get_count_of_list(this.value)" >
                                  <option value="0">Select Campaign</option>
                                  <?php
                                    if(count($recent_uploads)>0 ){
                                      foreach($recent_uploads as $row){?>
                                        <option value="<?php echo $row['id'];?>"><?php echo $row['list_name'];?></option>
                                      <?php } 
                                    }
                                  ?>
                                </select>
                              </div>  
                              
                              <?php
                                  /* 
                                    * AUTHOR :: FARHAN AKHTAR
                                    * LAST MODIFIED ON :: 13-02-2025
                                    * PURPOSE :: TO IMPLEMENT FUNCTIONALLITY FOR GENERATE LIST (EXCEL SHEET) OF EMAILS (DATE WISE). 
                                  */
                                  if($campaign == 'Email'){
                              ?>
                              <div class="form-inline">
                                <div class="form-group mb-2">
                                 <input type="text" class="form-control date_class dob1" id="BulkEmailStartDate" placeholder="Start Date">
                                </div>
                                <div class="form-group mx-sm-3 mb-2">
                                 <input type="text" class="form-control date_class dob1" id="BulkEmailEndDate" placeholder="End Date">
                                </div>
                                <div class="form-group mx-sm-3 mb-2">
                                   <a href="#" class="btn btn-warning btn-sm" id="BulkEmailSubmit" data-toggle="tooltip" data-placement="top" title="Download Email Campaign List in CSV format">Download</a>
                                </div>
                              </div>
                              <? } ?>

                              <!-- END -->
                                                                 
                               <span id="show-counts"></span>                                             
                              <h2 id="divider_row"><span> OR </span></h2>
                              <span class="text text-danger file_upload_error"></span>
                              <div class="form-group" id="upload_file_div">
                              <label><i class="fas fa-file"></i> Select File</label><br>
                                <input type="file" name="file" id="file" accept=".xls, .xlsx ,.csv"> 
                              </div>
                              <div class="form-group" id="file_name_div">
                                <label>Campaign Name</label>
                                <input type="text" name="list_name" id="list_name" class="form-control" placeholder="Enter List Name" value="">
                                <span class="text text-danger list_name_error"></span>
                              </div>
                              <?php if($campaign == 'Email'){?>
                              <div class="form-group" id="desc_div">
                                <label>Subject</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Enter description" value="">
                                <span class="text text-danger description_error"></span>
                              </div>
                              <?php }?>
                              <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control messagebulk" id="message" placeholder="Your Message..." name="message" maxlength="1600" style="height: 110px; resize:none;"></textarea>
                                 <textarea style="display:none;" type="hidden" id="outputid"></textarea>
                                <span class="limi" id="counter"></span>
                                <span class="text text-danger message_error"></span>
                              </div>
                              <input type="hidden" name="file_upload_type" id="file_upload_type" value="new">
                              <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                  <input type="checkbox" class="custom-control-input" id="scheduletime2" name="scheduletime">
                                  <label class="custom-control-label" for="scheduletime2">Schedule</label>
                                </div>
                              </div>
                              <div class="form-group" id="datepicker_div" style="display:none;">
                                    <input id="pickdate" class="form-control pickdate" placeholder="Select date and Time" autocomplete="off" name="date" value="">
                              </div>
                                  
                              <div class="form-group text-right">
                                  <input type="submit" id="bulk_send_button" name="Upload" value="Send Now" class="btn btn-info">
                              </div>                                         
                              <div class="card-footer">
                                 <span><a style="text-decoration: none;color: #337ab7;" href="sample/excel_sample.csv">Download Template </a></span>  
                                    <br><span style="color:red">Note : Enter contact no between the quotes e.g : '264XXXXXXXXX'    </span>  
                                  
                              </div>                                          
                            </div>
                          </form>
                        </div>
                    
                        <pre id="data"></pre>
                      </div><!-- /.col -->
                      
                      <div class="col-sm" style="margin-right: 19px;">
                        <!-- Show Recent Bulk Uploaded In Queue -->
                        <!-- show Recent Uploaded List -->
                        <div class="card card-info card-outline">
                          <div class="card-header">
                            <h3 class="card-title">Recent Uploads </h3>
                          </div>
                          <!-- /.card-header -->
                          <div class="card-body">
                            <table class="table table-bordered table-hover table-sm" id="campaign_lists">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Campaign Name</th>
                                  <th>Channel Type</th>
                                  <th>Total Count</th>
                                  <th>Created Date</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                             <? $id=1;
                              if(count($recent_uploads)>0 ){
                               foreach($recent_uploads as $row){?>
                                 <tr>
                                   <td><?php echo $id++;?></td>
                                   <td><?php echo $row['list_name'];?></td>
                                   <td><?php echo $row['channel_type'];?></td>
                                   <td><?php echo $row['total_count'];?></td>
                                   <td><?php echo date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                   <td>
                                     <?php 
                                       $path = BasePathStorage.'/bulkcampaign/'.$row['file_name'];
                                        ?> 
                                       <span><a style="text-decoration: none;" href="../../<?php echo $path ; ?>">Download </a></span> 
                                   </td>
                                 </tr>
                                 <?php 
                               } 
                               }else{ ?>
                                 <tr>
                                   <td colspan="5">No Record Found</td>
                                 </tr>
                               <? }?>
                              </tbody>
                            </table>
                          </div>
                          <!-- /.card-body -->
                        </div>
                      </div>
                      <!-- /.col -->
                    </div><!-- /.row -->
                  </div>
                   
                </div>
            </div>
        <!-- /.Card-body -->
      </div>
   </div>
</div>