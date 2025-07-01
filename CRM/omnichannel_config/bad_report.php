<?php
/**
 * SMS Bad Report Script
 * 
 * This script generates a report of SMS-related data based on user-selected filters.
 * It includes DataTables for server-side processing, filter functionality, and report export options.
 * 
 * Author: Aarti
 * Last Modified: 09-24-2024
 */

// Include the database connection file
include("../../config/web_mysqlconnect.php");

// Include a file containing reusable functions
include("../web_function.php");

// Fetch the logged-in user information
$name = $_SESSION['logged'];

// Include the header file for consistent UI layout
include('header.php');

// Include the bulk report functionality class
include("bulk_report_function.php");  

// Initialize the BULK_REPORT class and fetch bulk upload data
$list_BULK = new BULK_REPORT();
$bulk_lists = $list_BULK->get_bulk_upload_list(); 
?>

<!-- Include the CSS stylesheet specific to this report -->
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/bad_report.css">
<div class="col-sm-9 mt-3" style="padding-left: 0;">
    <form name="myform" method="post">
        <div class="style2-table">
            <div class="table">
                <span class="breadcrumb_head" style="height: 37px; padding: 9px 16px;">SMS BAD REPORT</span>
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
                        <div class="title_name" style="display: none;">bad Report</div>
                        <div class="report_name" style="display: none;"><?=date('dmY')?></div>
                        <div class="download_label" style="display: none;"><?=$pdf_heading?></div>
                        <table class="table table-striped table-bordered table-sm" id="report-server-side">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Created Date Time</th>
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
   $(function () {
        var inout_report = '<?php echo $report_in_out ?>' ;
        $("#report-server-side").DataTable({
              "searching": false,
              "responsive": true, 
              "pageLength": 100, 
              "lengthChange": false,
              "autoWidth": false,
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
                            'action':'get_bad_records',
                            'channeltype':'SMS'
                          }
              }, 
              "columns": [
                  { "data": "#" },
                  { "data": "name" },
                  { "data": "mobile" },
                  { "data": "email" },
                  { "data": "create_date" },
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
            });
          
        });
</script>