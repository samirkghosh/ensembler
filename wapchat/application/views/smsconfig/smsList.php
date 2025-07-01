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
                <li class="breadcrumb-item active"><?=strtolower($breadcrumb)?></li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <?php $this->load->view('message') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card card-info card-outline" style="border-color: coral;">
                    
                        <div class="card-header">
                            <h3 class="card-title"><?=$page_title?></h3>
                        </div>
                    <form role="form" id="custom" id="custom" action="<?php echo site_url('smsconfig/custom') ?>" class="form-horizontal" method="post">

                        <div class="card-body">
                           <?php
                              $custom_result = check_in_array('custom', $smslist);
                              ?>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('gateway_name'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control" name="name" value="<?php echo $custom_result->name; ?>">
                                 <span class="text text-danger name_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('username'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control" name="username" value="<?php echo $custom_result->username; ?>">
                                 <span class="text text-danger username_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('password'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control" name="password" value="<?php echo $custom_result->password; ?>">
                                 <span class="text text-danger password_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('url'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="url" class="form-control" name="url" value="<?php echo $custom_result->url; ?>">
                                 <span class="text text-danger url_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('sender_id'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control" name="sender_id" value="<?php echo $custom_result->senderid; ?>">
                                 <span class="text text-danger sender_id_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('content_type'); ?><small class="req"> *</small>
                              </label>
                              <div class="col-sm-7">
                                 <input type="text" class="form-control" name="content_type" value="<?php echo $custom_result->contact; ?>">
                                 <span class="text text-danger content_type_error"></span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-sm-5 control-label"><?php echo $this->lang->line('status'); ?></label>
                              <div class="col-sm-7">
                                 <select class="form-control" name="custom_status">
                                    <?php
                                       foreach ($statuslist as $s_key => $s_value) {
                                           ?>
                                    <option 
                                       value="<?php echo $s_key; ?>"
                                       <?php
                                          if ($custom_result->is_active == $s_key) {
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
                        <!-- /.card-body -->

                        <div class="card-footer">
                           <button type="submit" class="btn btn-info col-md-offset-3" style="background: coral;border-color:coral"><?php echo $this->lang->line('save'); ?></button>&nbsp;&nbsp;<span class="custom_loader"></span> 
                        </div>
                    </form>
                </div>
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

  <!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->


<?php

function check_in_array($find, $array) {

    foreach ($array as $element) {
        if ($find == $element->type) {
            return $element;
        }
    }
    $object = new stdClass();
    $object->id = "";
    $object->type = "";
    $object->api_id = "";
    $object->username = "";
    $object->url = "";
    $object->name = "";
    $object->contact = "";
    $object->password = "";
    $object->authkey = "";
    $object->senderid = "";
    $object->is_active = "";
    return $object;
}
?>


<script type="text/javascript">
    var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
      
    $("#custom").submit(function (e) {
        $("[class$='_error']").html("");

        $(".custom_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action'); // the script where you handle the form input.

        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#custom").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR)
            {
                if (data.st === 1) {
                    $.each(data.msg, function (key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else {
                    successMsg(data.msg);
                }
                $(".custom_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
</script>


