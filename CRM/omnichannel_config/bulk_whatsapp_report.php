<?php
include_once("../../config/web_mysqlconnect.php");
include("../web_function.php");
$name= $_SESSION['logged'];

include("bulk_report_function.php");  
$list_BULK = new BULK_REPORT();
$bulk_lists =  $list_BULK->get_bulk_upload_list(); 
?>
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/bulk_whatsapp_report.css">
<div class="col-sm-9 mt-3" style="padding-left: 0;">
  <form name="myform" method="post">
    <div class="style2-table">
      <div class="table">
        <span class="breadcrumb_head" style="height: 37px; padding: 9px 16px;">WhatsApp BULK REPORT</span>
        <table class="tableview tableview-2 main-form new-customer">
          <tbody>
            <tr>
              <td class="left boder0-right">
                <label>Scheduled</label>
                <div class="log-case">
                  <select name="schedule" id="agent_n" class="select-styl1">
                    <option value="all" <?php echo ($schedule=='all')?'selected':'' ?>>All</option>
                    <option value="0" <?php echo ($schedule=='0')?'selected':'' ?>>Scheduled</option>
                    <option value="1" <?php echo ($schedule=='1')?'selected':'' ?>>Unscheduled</option>
                  </select>
                </div>
              </td>
              <td class="left boder0-right">
                <label>Campaign wise</label>
                <div class="log-case">
                  <select name="list_wise" id="agent_n" class="select-styl1">
                    <option value="all">All</option>
                    <?php if(count($bulk_lists) > 0): 
                      foreach ($bulk_lists as $key => $list): ?>
                      <option value="<?=$list['id']?>" <?php echo ($list_wise==$list['id'])?'selected':'' ?>><?=$list['list_name']?></option>
                    <?php endforeach;
                    endif;
                    ?>
                  </select>
                </div>
              </td>
            </tr>
            <tr>
              <td class="left boder0-left">
                <label>Agents</label>
                <div class="log-case">
                  <?php
                  $sqlagent="select AtxUserID,AtxUserName from $db.uniuserprofile where AtxUserStatus='1'";
                  $result=mysqli_query($link,$sqlagent);
                  ?>
                  <select name="user_wise" id="user_wise" class="select-styl1">
                    <option value="">Select Agent</option>
                    <?php 
                    while($row=mysqli_fetch_array($result)) {
                      $AtxUserID=$row['AtxUserID']; 
                      $AtxUserName=$row['AtxUserName'];
                      if($AtxUserName == $_POST['agent_n']) {
                        $sel = 'selected';
                      } else {
                        $sel = '';
                      }
                    ?>
                    <option value='<?=$AtxUserName?>' <?=$sel?>><?=$AtxUserName?></option>
                    <?php } ?>
                  </select>
                </div>
              </td>
              <?php
              $startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");
              $enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");
              ?>
              <td class="left boder0-left">
                <label>From 
                  <input type="text" name="from_date" class="date_class dob1" value="<?=$startdatetime?>" id="startdatetime">
                </label>
                <label>To 
                  <input type="text" name="end_date" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime">
                </label>
              </td>
            </tr>
            <tr>
              <td colspan="3" class="left boder0-right">
                <center>
                  <input type='submit' name='sub1' value='Run Report' class="button-orange1" style="float: inherit;">
                  <button type="button" id="reset" name="reset" class="btn button-orange1" title="Reset filters" style="float: inherit; color: #222; text-decoration: none; padding: 6px;"><i class="fas fa-power-off"></i>Reset</button>
                </center>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="table">
        <div class="wrapper1"></div>
        <div class="wrapper2">
          <div class="box-body table-responsive">
            <div class="title_name" style="display: none;">Agent Report</div>
            <div class="report_name" style="display: none;"><?=date('dmY')?></div>
            <div class="download_label" style="display: none;"><?=$pdf_heading?></div>
            <table class="tableview tableview-2" id="report-server-side">
              <thead>
                <tr>
                  <th># &nbsp;</th>
                  <th>Date</th>
                  <th>List Name</th>
                  <th>Count</th>
                  <th>Queue</th>
                  <th>Submitted</th>
                  <th>Delivered</th>
                  <th>Not Delivered</th>
                  <th>Message</th>
                  <th>Schedule Date</th>
                  <th>Schedule Type</th>
                  <th>Created By</th>
                </tr>
              </thead>                               
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- End Right panel --> 
  </form>
</div>
<script type="text/javascript">
 // $('.date_class').datetimepicker();
 $(function () {
    $("#report-server-side").DataTable({
      "searching": false,
      "responsive": true, 
      "pageLength": 100, 
      "lengthChange": false,
      "autoWidth": true,
      "processing": true,
      "serverSide": true,
      "searchable" : false, 
      "ajax":{
          "url": "omnichannel_config/bulk_report_function.php",
          "dataType": "json",
          "type": "POST",
          "data" : {report_name : '<?php echo $report_name; ?>',
                    report_in_out : '<?php echo $report_in_out ?>',
                    from_date : '<?php echo $from_date ?>',
                    end_date : '<?php echo $end_date ?>',
                    schedule : '<?php echo $schedule ?>',
                    message_type : '<?php echo $message_type ?>',
                    status : '<?php echo $status ?>',
                    list_wise : '<?php echo $list_wise ?>',
                    user_wise : '<?php echo $user_wise ?>',
                    'action':'bulk_sms_report',
                    'channeltype':'WhatsApp'
                  }
      },
      "columns": [
          { "data": "#" },
          { "data": "create_date" },
          { "data": "list_name" },
          { "data": "total_count" },
          { "data": "queue" },
          { "data": "submitted" },
          // { "data": "pending" },
          { "data": "delivered" },
          { "data": "not_delivered" },
          { "data": "message" },
          { "data": "schedule_time" },
          { "data": "schedule_flag" },
          { "data": "create_by" },
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
</script>