<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  26 Mar  2024
 * Description: To Display Data Related to web sms and Create Delete or Reply to the SMS 
 * 
*/
// Include necessary files and database connection

include("../../config/web_mysqlconnect.php");
include_once("../../ensembler/function/classify_function.php"); 

include("date.php"); // Assuming date.php contains necessary date-related functions

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_sms();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

$msg=$_POST['mg'];
$phone=$_POST['phone'];
$mode=$_GET['mode'];
$yest=date("Y-m-d", $yesterday);
$iallstatus=$_POST['allstatus']; ////              Status 
?>
 <style>
    .input-style1 {
        background: none repeat scroll 0 0 #EDEDED;
        border: 1px solid #C2C2C2;
        color: #5C5C5C;
        float: left;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        padding: 4px;
    }
    .flag-yellow {
        background:#f1a00bd4 !important; 
        color:#fff;
    }

    .flag-red{
        background:#e34234db !important; 
        color:#fff;
    }
    .flag-green{
        background:#228b22de !important; 
        color:#fff;
    }
        .fil_div{
        width:20px;
        height:20px;
        border:1px solid #999999;
        border-radius:5px;
        white-space: nowrap;
    }
    .read-container {
        margin-left: 400px; /* Adjust this value to control the gap */
    }

    .sms-text {
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: pre-wrap; /* Ensures that any long continuous text without spaces wraps properly */
        max-width: 15px!important;
    }
</style>

  <!--  CSS code -->
  <link href="<?=$SiteURL?>public/css/channel_all_style.css" rel="stylesheet" type="text/css" />
 <div class="col-sm-10 mt-3" style="padding-left:0">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
          <div class="row">
            <div class="col-sm-12">
              <div class="row">
                <div class="col-sm-2">
                    <div class="row">
                       <div class="col-sm-7">SMS</div>
                     </div>
                </div>
                <div class="col-sm-2 read-container">
                        <div class="row">
                           <div class="col-sm-7 ">Unread</div>
                           <div class="col-sm-5"><div class="fil_div Unread" ></div></div>
                        </div> 
                     </div>                      
                      <div class="col-sm-2 ">
                        <div class="row">
                           <div class="col-sm-7">Read</div>
                           <div class="col-sm-5"><div class="fil_div Read" ></div></div>
                        </div> 
                 </div>
              </div>
            </div>
          </div>
        </span>

    <div class="Reports-page#" style="display: block;border: #d4d4d4 1px solid;margin-bottom:20px;min-height: 420px;margin-top:37px">
        <!-- Filter -->
        <form method="post" name="cfrm" id="post_sms">
            <input type="hidden" name="id" value="<?php echo $_GET['id'];?>" id="inte_id">
            <div style="margin-top:-1px;">
                <table class="tableview tableview-2 main-form new-customer">
                    <tr style="height:40px;">
                        <td width="95" class="left boder0-right">&nbsp;
                            <label>Start Date </label>
                        </td>
                        <td width="226" class="left boder0-right">
                           
                            <span class="left boder0-left">
                            <input type="text" name="sttartdatetime" class="date_class dob1"
                                value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                            </span>
                        </td>
                        <td width="112" class="left boder0-right"><label>End Date </label></td>
                        <td width="230" class="left boder0-right" align="left">
                            <span class="left boder0-left">
                            <input type="text" name="enddatetime" class="date_class dob1"
                                value="<?=$enddatetime?>" id="enddatetime">
                            </span>
                        </td>
                        <td width="211" class="left boder0-right">&nbsp;
                            <label>Phone</label>
                        </td>
                        <td width="192" class="left boder0-right">
                            <span class="left boder0-left">
                            <input type="text" class="input-style1" id="phone" name="phone" value="<?=$phone?>" style="background:#fff">
                            </span>
                        </td>
                    </tr>
                    <td width="112" class="left boder0-right"><label>Case Id</label>
                        <span class="left boder0-left" style="display: inline-block;">
                            <?php 
                            $query = mysqli_query($link, "SELECT ICASEID FROM $db.tbl_smsmessagesin WHERE ICASEID != '' GROUP BY ICASEID ASC ");
                            ?>                                    
                            <input type="number" id="searchBox" placeholder="Search Case ID.. And Select." onkeyup="filterFunction('ICASEID', 'searchBox', 'noCaseIdMessage')" style="width:170px; height:30px; margin-bottom: 0px;">

                            <div id="dropdownContainer" class="dropdown-content" style="width:170px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
                                <select name="ICASEID" id="ICASEID" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0; display: none;" onchange="selectOption('ICASEID', 'searchBox')">
                                    <option value="">Select</option>
                                    <?php while ($row = mysqli_fetch_assoc($query)) {
                                        $name = $row['ICASEID'];
                                        $selected = ($name == $_POST['ICASEID']) ? "selected" : "";
                                    ?>
                                        <option value="<?= htmlspecialchars($name) ?>" <?= $selected ?>><?= htmlspecialchars($name) ?></option>
                                    <?php } ?>
                                </select>
                                <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
                            </div>
                        </span>
                    </td>
                    <td> <label>Status</label>
                    
                        &nbsp;
                        <select name="allstatus" id="iallstatus" placeholder="select status" class="select-styl1">
                            
                            <option value="4" <?php if($iallstatus == 4) echo "selected"; ?>>ALL</option>
                            <option value="0" <?php if($iallstatus == 0) echo "selected"; ?>>Case Not Created</option>
                            <option value="1" <?php if($iallstatus == 1) echo "selected"; ?>>Case Created</option>
                            <!-- <option value="2" <?php if($iallstatus == 2) echo "selected"; ?>>Deleted</option> -->
                        </select>

                    </td>
                        <td class="left  boder0-left" colspan="4">
                            <input type='submit' name='sub1'
                            value='Run Report' class="button-orange1 set_button" />
                            <?php $sms = base64_encode('sms');?>
                <input type="button" 
                    class="button-orange1" 
                    value="Reset" 
                    onclick="window.location.href='omni_channel.php?token=<?php echo $sms; ?>';" 
                    style="float:inherit; color:#222; text-decoration:none; " />
                    </tr>
                </table>
                </div> <br>
                <div class="table" id="sms">
                <table class="tableview tableview-2" id="sms_table">
                        <thead>
                            <tr class="background">
                                <!--<th><input type="checkbox" id="checkall"></span></th>-->
                                <th align="center" valign="middle" width="5%" style="font-size:12px;font-weight: normal;">S.No.</th>
                                <th align="left" valign="middle" width="15%" style="font-size:12px;font-weight: normal;" >Phone </th>
                                <th align="left"  valign="middle" width="18%" style="font-size:12px;font-weight: normal;" >Message</th>
                                <th align="left" valign="middle"  width="15%" style="font-size:12px;font-weight: normal;">Case</th>
                                <th align="left" valign="middle"  width="15%" style="font-size:12px;font-weight: normal;">Status</th>
                                <th align="left" valign="middle" width="15%" style="font-size:12px;font-weight: normal;">Date </th>
                                <th align="left" valign="middle"  width="10%" style="font-size:12px;font-weight: normal;" >Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    function filterFunction(selectId, searchBoxId, messageDivId) {
        var input, filter, select, options, i;
        input = document.getElementById(searchBoxId);
        filter = input.value.toUpperCase();        
        select = document.getElementById(selectId);
        options = select.getElementsByTagName("option");
        var hasVisibleOptions = false;

        // Show the dropdown if the input has text
        var dropdownContainer = document.getElementById('dropdownContainer');
        if (filter.length > 0) {
            dropdownContainer.style.display = "block";
        } else {
            dropdownContainer.style.display = "none";
            return;
        }

        // Loop through all options and filter them
        for (i = 1; i < options.length; i++) { // Skip the first option
            var txtValue = options[i].textContent || options[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
                hasVisibleOptions = true;
            } else {
                options[i].style.display = "none";
            }
        }

        // Handle "No records found" message
        var messageDiv = document.getElementById(messageDivId);
        if (!hasVisibleOptions && filter.length > 0) {
            select.style.display = "none";  // Hide the select dropdown
            messageDiv.style.display = "block"; // Show "No records found"
        } else {
            select.style.display = "block";  // Show the select dropdown
            messageDiv.style.display = "none"; // Hide "No records found"
        }
    }

    function selectOption(selectId, searchBoxId) {
        var select = document.getElementById(selectId);
        var searchBox = document.getElementById(searchBoxId);
        
        // Update the search box value with the selected option text
        searchBox.value = select.options[select.selectedIndex].text;
        
        // Hide the dropdown after an option is selected
        document.getElementById('dropdownContainer').style.display = "none";
    }

    // Hide the dropdown if clicking outside of it
    document.addEventListener('click', function(event) {
        var dropdownContainer = document.getElementById('dropdownContainer');
        var searchBox = document.getElementById('searchBoxId');
        if (!dropdownContainer.contains(event.target) && !searchBox.contains(event.target)) {
            dropdownContainer.style.display = 'none';
        }
    });
</script>