<?php
include_once("../../config/web_mysqlconnect.php");
include("../web_function.php");
$name = $_SESSION['logged'];
?>
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/bulk_whatsapp_report.css">

<div class="col-sm-9 mt-3" style="padding-left: 0;">
  <form name="myform" method="post" id="reportForm">
    <div class="style2-table">
      <div class="table">
        <span class="breadcrumb_head" style="height: 37px; padding: 9px 16px;">Twitter REPORT</span>
        <table class="tableview tableview-2 main-form new-customer">
          <tbody>
            <tr>
              <td class="left boder0-right">
                <label>Agents</label>
                <div class="log-case">
                  <?php
                  $sqlagent = "select AtxUserID, AtxUserName from $db.uniuserprofile where AtxUserStatus='1'";
                  $result = mysqli_query($link, $sqlagent);
                  ?>
                  <select name="user_wise" id="user_wise" class="select-styl1">
                    <option value="">Select Agent</option>
                    <?php 
                    while ($row = mysqli_fetch_array($result)) {
                      $AtxUserID = $row['AtxUserID']; 
                      $AtxUserName = $row['AtxUserName'];
                      $sel = ($AtxUserName == $_POST['user_wise']) ? 'selected' : '';
                    ?>
                    <option value='<?=$AtxUserName?>' <?=$sel?>><?=$AtxUserName?></option>
                    <?php } ?>
                  </select>
                </div>
              </td>
              <?php
              $from_date = ($_REQUEST['from_date'] != '') ? ($_REQUEST['from_date']) : date("01-m-Y 00:00:00");
              $end_date = ($_REQUEST['end_date'] != '') ? ($_REQUEST['end_date']) : date("d-m-Y 23:59:59");
              ?>
              <td class="left boder0-right">
                <label>From 
                  <input type="text" name="from_date" class="date_class dob1" value="<?=$from_date?>" id="from_date">
                </label>
                <label>To 
                  <input type="text" name="end_date" class="date_class dob1" value="<?=$end_date?>" id="end_date">
                </label>
              </td>
            </tr>
            <tr>
              <td colspan="3" class="left boder0-right">
                <center>
                  <input type="submit" name="sub1" value="Run Report" class="button-orange1" style="float: inherit;">
                  <button type="button" id="reset" name="reset" class="btn button-orange1" title="Reset filters" style="float: inherit; color: #222; text-decoration: none; padding: 6px;"><i class="fas fa-power-off"></i> Reset</button>
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
            <div class="download_label" style="display: none;"><?=$pdf_heading?></div>
            <table class="tableview tableview-2" id="report-server-side">
              <thead>
                <tr>
                  <th># &nbsp;</th>
                  <th>Date</th>
                  <th>Queue</th>
                  <th>Submitted</th>
                  <th>Delivered</th>
                  <th>Not Delivered</th>
                  <th>Message</th>
                  <th>Schedule Time</th>
                </tr>
              </thead>                               
            </table>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    var table = $("#report-server-side").DataTable({
      "searching": false,
      "responsive": true,
      "pageLength": 100,
      "lengthChange": false,
      "autoWidth": true,
      "processing": true,
      "serverSide": true,
      "searchable": false,
      dom: "Bfrtip",
      buttons: [
        {
          extend: 'csvHtml5',
          text: '<i class="fa fa-file-excel-o"></i>',
          titleAttr: 'CSV',
          filename: 'csv',
          title: $('.download_label').html(),
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel-o"></i>',
          titleAttr: 'Excel',
          filename: 'excel',
          title: $('.download_label').html(),
          exportOptions: {
            columns: ':visible'
          }
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf-o"></i>',
            titleAttr: 'PDF',
            filename: $('.report_name').text() || 'Report',
            messageTop: $('.download_label').html() || 'No data available',
            orientation: 'landscape',
            pageSize: 'A3',
            exportOptions: { columns: ':visible' },
            // changed the customized function to download the pdf even when there is no data [vastvikta][19-3-2025]
            customize: function (doc) {
                if (doc.content.length < 2) { // If table content is missing
                    doc.content.push({
                        text: 'No data available to export',
                        alignment: 'center',
                        margin: [0, 10, 0, 10]
                    });
                }
            }
        },
        {
          extend: 'colvis',
          text: '<i class="fa fa-columns"></i>',
          titleAttr: 'Columns',
          title: $('.download_label').html(),
          postfixButtons: ['colvisRestore']
        },
      ],
      "ajax": {
        "url": "omnichannel_config/twiter_function.php",
        "dataType": "json",
        "type": "POST",
        "data": function (d) {
          d.from_date = $('#from_date').val();
          d.end_date = $('#end_date').val();
          d.user_wise = $('#user_wise').val();
          d.action = 'twiter_report';
          d.channeltype = 'twiter';
        }
      },
      "columns": [
        { "data": "#" },
        { "data": "created_date" },
        { "data": "queue" },
        { "data": "submitted" },
        { "data": "delivered" },
        { "data": "not_delivered" },
        { "data": "message_data" },
        { "data": "schedule_time" }
      ]
    });

    $('#reset').on('click', function () {
      $('#user_wise').val('');
      $('#from_date').val('<?= date("01-m-Y 00:00:00") ?>');
      $('#end_date').val('<?= date("d-m-Y 23:59:59") ?>');
      table.ajax.reload();
    });
  });
</script>
      // $("#report-server-side").DataTable({
      //   "searching": false,
      //   "responsive": true, 
      //   "pageLength": 100, 
      //   "lengthChange": false,
      //   "autoWidth": true,
      //   "processing": true,
      //   "serverSide": true,
      //   "searchable": false, 
      //   "ajax": {
      //       "url": "omnichannel_config/twiter_function.php",
      //       "dataType": "json",
      //       "type": "POST",
      //       "data": {
      //           report_name: '<?php echo $report_name; ?>',
      //           report_in_out: '<?php echo $report_in_out ?>',
      //           from_date: '<?php echo $from_date ?>',
      //           end_date: '<?php echo $end_date ?>',
      //           schedule: '<?php echo $schedule ?>',
      //           message_type: '<?php echo $message_type ?>',
      //           status: '<?php echo $status ?>',
      //           list_wise: '<?php echo $list_wise ?>',
      //           user_wise: '<?php echo $user_wise ?>',
      //           'action': 'twiter_report',
      //           'channeltype': 'twiter'
      //       },
      //       "success": function(response) {
      //           console.log(response); // Log the response to the console
      //       },
      //       "error": function(xhr, status, error) {
      //           console.error(xhr.responseText); // Log any error to the console
      //       }
      //   },
      //   "columns": [
      //       { "data": "#" },
      //       { "data": "create_date" },
      //       { "data": "list_name" },
      //       { "data": "total_count" },
      //       { "data": "queue" },
      //       { "data": "submitted" },
      //       { "data": "delivered" },
      //       { "data": "not_delivered" },
      //       { "data": "message" },
      //       { "data": "schedule_time" },
      //       { "data": "create_by" },
      //   ],
      //   "columnDefs": [{
      //       targets: "_all",
      //       orderable: false
      //    }],
      //   "dom": "Bfrtip",
      //   "buttons": [{
      //       extend: 'excelHtml5',
      //       text: '<i class="fas fa-file-excel"></i>',
      //       titleAttr: 'Excel',
      //       title: $('.download_label').html(),
      //       exportOptions: {
      //           columns: ':visible'
      //       }
      //   }, {
      //       extend: 'colvis',
      //       text: '<i class="fa fa-columns"></i>',
      //       titleAttr: 'Columns',
      //       title: $('.download_label').html(),
      //       postfixButtons: ['colvisRestore']
      //   }],
      // });
    

