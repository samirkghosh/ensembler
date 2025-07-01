<?php
/***
    * Instagram Post Detail Page
    * Author: Aarti Ojha
    * Date: 09-12-2024
    * This file handles Instagram Chat display
    * To integrate Instagram messaging in your PHP application using the Instagram Business API, you need to follow several steps, including setting up your Instagram Developer account, creating a Instagram Business Account, configuring your webhook, and handling incoming and outgoing messages
    * 
    * Please do not modify this file without permission.
**/

include("../../config/web_mysqlconnect.php");
$module_flag_customer = module_license('Instagram Post');
if($module_flag_customer !='1'){
  header("Location:web_admin_dashboard.php"); 
  exit();
}
$iallstatus=(isset($_REQUEST['allstatus'])) ? $_REQUEST['allstatus'] : 4; ////              Status 
$msg=$_REQUEST['mg'];
$email=$_REQUEST['email'];
$mode=$_GET['mode'];
?>
<!--  CSS code -->
<link href="<?=$SiteURL?>public/css/channel_all_style.css" rel="stylesheet" type="text/css" />
<!-- Start Right panel -->
<div class="col-sm-10 mt-3" style="padding-left:0">
 <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
            <div class="row">
              <div class="col-sm-12">
                <div class="row">
                  <div class="col-sm-3">
                      <div class="row">
                         <div class="col-sm-7">INSTAGRAM POST</div>
                       </div>
                  </div>
                  <div class="col-sm-1 read-container">
                      <div class="row">
                         <div class="col-sm-3 ">Unread</div>
                         <div class="col-sm-5"><div class="fil_div Unread" ></div></div>
                      </div> 
                   </div>                      
                    <div class="col-sm-2 ">
                      <div class="row">
                         <div class="col-sm-3">Read</div>
                         <div class="col-sm-5"><div class="fil_div Read" ></div></div>
                      </div> 
                   </div>
                </div>
              </div>
            </div>
          </span>
    <form method="post" name="cfrm" id="post_complaint">
          <?
            $channel_type = $_POST['channel_type'];
             
             ?>
         <table class="tableview tableview-2 main-form new-customer">
            <tr>
               <td width="95" class="left boder0-right">&nbsp;
                  <label>Start Date </label>
               </td>
               <td width="226" class="left boder0-right">
                  <?
                     $startdate = ($_REQUEST['startdatetime']!='') ? $_REQUEST['startdatetime'] : date("01-m-Y 00:00:00");
                     $enddate = ($_REQUEST['enddatetime']!='') ? $_REQUEST['enddatetime'] : date("d-m-Y H:i:s");
                     ?>
                  <span class="left boder0-left">
                  <input type="text" name="startdatetime" class="date_class dob1"  value="<? if(!isset($_POST['startdatetime'])) echo date('01-m-Y 00:00:00'); else echo $_POST['startdatetime']; ?>" id="startdatetime">&nbsp;
                  </span>
               </td>
               <td width="112" class="left boder0-right"><label>End Date </label></td>
               <td width="230" align="left" class="left boder0-right"><span class="left boder0-left">
                  <input type="text" name="enddatetime" class="date_class dob1"   value="<? if(!isset($_POST['enddatetime'])) echo date('d-m-Y H:i:s'); else echo $_POST['enddatetime']; ?>" id="enddatetime">
                  </span>
               </td>
               <td class="left boder0-right"> <label>Status</label></td>
               <td class="left boder0-right">
                  &nbsp;<select name="allstatus" class="select-styl1" id="iallstatus">
                  <? if($iallstatus==3){ ?> 
                  <option value="3" selected="">New Case</option>
                  <? } ?>
                  <option value="4" <? if($iallstatus==4) echo "selected"; ?>>ALL</option>
                  <option value="0" <? if($iallstatus==0) echo "selected"; ?>>Case Not Created</option>
                  <option value="1" <? if($iallstatus==1) echo "selected"; ?>>Case Created</option>
                  <!-- <option value="2" <? if($iallstatus==2) echo "selected"; ?>>Deleted</option>
                   -->
            </tr>
            <tr>
                <td class="left boder0-right"> <label>Channel Type</label></td>
               <td class="left boder0-right">Instagram Post
                </td>
             <!--   * Author: Ritu Modi 
            * Date: 02-09-2024  
            * On the advice of ajay sir i have added two filters so that the data can be seached easily.-->
        <td width="211" class="left boder0-right"><label>Case ID</label></td>
        <td width="192" align="left" class="left boder0-right">
            <span class="left boder0-left">
                <?php $query = mysqli_query($link, "SELECT ICASEID FROM $db.instagram_posts WHERE ICASEID != '' GROUP BY ICASEID ASC "); ?>
                
                <input type="number" id="searchBox" placeholder="Search Case ID.. And Select." onkeyup="filterFunction('ICASEID', 'searchBox', 'noCaseIdMessage')" style="width:155px; height:30px; margin-bottom: 5px;">
                
                <select name="ICASEID" id="ICASEID" class="select-styl1" size="5" style="width:155px; display: none;" onchange="selectOption('ICASEID', 'searchBox')">
                    <option value="">Select</option>
                    <?php while ($row = mysqli_fetch_assoc($query)) {
                        $name = $row['ICASEID'];
                        $selected = ($name == $_POST['ICASEID']) ? "selected" : "";
                    ?>
                        <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                    <?php } ?>
                </select>
                <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px;">No records found</div> <!-- Message for Case ID -->
            </span>
        </td>

        <td width="112" class="left boder0-right"><label>Sender ID</label></td>
        <td width="230" align="left" class="left boder0-right">
            <span class="left boder0-left">
                <?php $query = mysqli_query($link, "SELECT send_from FROM $db.instagram_in_queue WHERE send_from != '' GROUP BY send_from ASC "); ?>
                
                <input type="number" id="searchSenderBox" placeholder="Search Sender Id. And Select.." onkeyup="filterFunction('send_from', 'searchSenderBox', 'noSenderMessage')" style="width:180px; height:30px; margin-bottom: 5px;">
                
                <select name="send_from" id="send_from" class="select-styl1" size="5" style="width:180px; display: none;" onchange="selectOption('send_from', 'searchSenderBox')">
                    <option value="">Select</option>
                    <?php while ($row = mysqli_fetch_assoc($query)) {
                        $name = $row['send_from'];
                        $selected = ($name == $_POST['send_from']) ? "selected" : "";
                    ?>
                        <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                    <?php } ?>
                </select>
                <div id="noSenderMessage" style="display: none; color: red; font-size: 12px;">No records found</div> <!-- Message for Sender -->
            </span>
          </td>
        </tr>
        <tr>         
            <td class="left boder0-right">
                <label>Sender Name</label>
                    </td>
                        <td class="left boder0-right">
                            &nbsp;<select name="send_name" class="select-styl1" id="send_name">
                                <?php
                                // Initialize the dropdown with a default option
                                echo '<option value="">Select Sender Name</option>';

                                // Fetch all names from the database
                                $result = mysqli_query($link, "SELECT fname, lname ,instagramhandle FROM $db.web_accounts where instagramhandle!='' ");

                                if ($result) {
                                    // Loop through the result set to populate the dropdown
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $fname = $row['fname'];
                                        $lname = $row['lname'];
                                        $instagramhandle= $row['instagramhandle'];
                                        $fullname = $fname . ' ' . $lname;

                                        // Output each name as a dropdown option
                                        echo '<option value="' . $instagramhandle . '">' . htmlspecialchars($fullname) . '</option>';
                                    }
                                } else {
                                    // Handle errors or empty result set
                                    echo '<option value="">No names available</option>';
                                }
                                ?>
                    </select>
                </td>                       
               <td class="left  boder0-left" colspan="2"><input type="submit" name="sub1" value="Run Report" class="button-orange1">
         <input type='button' name='reset' value='Reset' class="button-orange1"  id = "reset_button_messanger" >
            </tr>
         </table>
            <div class="table" id="facebook">
          <table class="tableview tableview-2" id="messenger_table">
              <thead>
                  <tr class="background">
                      <td>S.No.</td>
                      <td align="left" valign="center" width="10%">Date </td>
                      <td>Sender ID</td>
                      <!-- <td>RecipientId </td> -->
                      <td>Post Title</td>
                      <td>Post Attachment</td>
                      <!-- <td align="left" valign="center" width="10%">Case</td> -->
                      <td align="left" valign="center" width="10%">Action</td>
                  </tr>
              </thead>
              <tbody>
              </tbody>
          </table>                  
      </div>
    </form>
</div>  
   <script type="text/javascript">
      //######Datatable code start for fetching latest data with handling pagination [Aarti][12-08-2024]
    //STEP -1
    $(document).ready(function() {
    var dataTable;

    // Store the initial values of the fields
    var initialStartDateTime = $('#startdatetime').val();
    var initialEndDateTime = $('#enddatetime').val();
    var initialIallStatus = $('#iallstatus').val();
    var initialname = $('#send_name').val();
    var initialSearchSenderBox = $('#searchSenderBox').val(); // Assuming this is the send_from input
    var initialSearchBox = $('#searchBox').val(); // Assuming this is the ICASEID input

    function initialize_datatables() {
        dataTable = $('#messenger_table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, 
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "omnichannel_config/fetchData.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = $('#startdatetime').val();
                    d.enddatetime = $('#enddatetime').val();
                    d.iallstatus = $('#iallstatus').val();
                    d.send_name = $('#send_name').val();
                    d.ICASEID = $('#searchBox').val(); // Updated for ICASEID
                    d.send_from = $('#searchSenderBox').val(); // Updated for send_from
                    d.action = 'Instagram_Datatable_Post';
                },
                error: function(xhr, error, code) {
                    console.error("Error occurred:", xhr, error, code);
                    alert('An error occurred while processing the request.');
                }
            },
            "createdRow": function(row, data, dataIndex) {
                var clr = data[6]; // Assuming clr value is in the ninth column
                if (clr == 'yellow') {
                    // $(row).addClass('flag-yellow');
                } else if (clr == 'red') {
                    $(row).addClass('mail-row');
                } else if (clr == 'green') {
                    // $(row).addClass('flag-green');
                }
            },
            "drawCallback": function(settings) {
                $(".ico-interaction2").colorbox({
                    iframe: true,
                    innerWidth: 1000,
                    innerHeight: 550
                });
            }
        });
    }

    // Call the initialize_datatables function
    initialize_datatables();

    // Set an interval to refresh data every 70 seconds
    setInterval(function() {
        dataTable.ajax.reload(null, false); 
    }, 70000);

    // Submit click fetch data
    $('#post_complaint').submit(function(e) {
        e.preventDefault();
        dataTable.ajax.reload(); 
    });

    // Reset button click handler
    $('#reset_button_messanger').click(function() {
        // Restore the initial values
        $('#startdatetime').val(initialStartDateTime);
        $('#enddatetime').val(initialEndDateTime);
        $('#iallstatus').val(initialIallStatus);
        $('#searchSenderBox').val(initialSearchSenderBox); // Restore send_from value
        $('#searchBox').val(initialSearchBox); // Restore ICASEID value
         // Optionally hide the dropdown and messages when reset
        $('#ICASEID').hide();
        $('#send_name').val(initialname);
        $('#send_from').hide();
        $('#noCaseIdMessage').hide();
        $('#noSenderMessage').hide();

        // Reload the DataTable with the restored values
        dataTable.ajax.reload();
    });
});
</script>
</body>
</html>