<?php
/***
 * Disposition Report
 * Author: Aarti Ojha
 * Date: 23-07-2024
 * 
 * Description: This page is designed to generate and display the All Channel Disposition Report.
**/

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_disposition();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

?>
<div class="col-sm-10 mt-3" style="padding-left:0">
    <form name="myform" method="post">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Channel Disposition Report</span>
<div class="table">
    <table class="tableview tableview-2 main-form new-customer" style="background-color: #fff;">
        <tbody>
            <tr>
                <td width="95" class="left boder0-right">&nbsp;
                     <label>Start Date </label>
                  </td>
                  <td width="226" class="left boder0-right">
                <input type="text" name="sttartdatetime" class="dob1 date_class"
                    value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                </td>
                <td width="112" class="left boder0-right"><label>End Date </label></td>
                  <td width="230" class="left boder0-right" align="left">
                    <span class="left boder0-left">
                 <input type="text" name="enddatetime" class="dob1 date_class"
                    value="<?=$enddatetime?>" id="enddatetime">
                </span>
                </td>                       
                <td width="211" class="left boder0-right">&nbsp;
                    <label for="caller_name">Caller Name:</label>
                </td>
                <td width="211" class="left boder0-right">
                    <span class="left boder0-left">
                        <select name="caller_name" id="caller_name" class="select-styl1" size="1" style="width: 180px;">
                            <option value="">Select Caller Name</option>
                            <?php
                            // Fetch Caller Names from SMS and WhatsApp data
                            $sms_callers = mysqli_query($link, "SELECT DISTINCT AccountName FROM $db.sms_out_queue WHERE AccountName IS NOT NULL AND AccountName != ''");
                            
                            $whatsapp_callers = mysqli_query($link, "SELECT DISTINCT user_name FROM $db.whatsapp_in_queue WHERE user_name IS NOT NULL AND user_name != ''");

                            // Add SMS Caller Names
                            if ($sms_callers) {
                                while ($row = mysqli_fetch_assoc($sms_callers)) {
                                    $callerName = htmlspecialchars($row['AccountName']);
                                    echo "<option value='$callerName'>$callerName</option>";
                                }
                            }
                            // Add WhatsApp Caller Names
                            if ($whatsapp_callers) {
                                while ($row = mysqli_fetch_assoc($whatsapp_callers)) {
                                    $callerName = htmlspecialchars($row['user_name']);
                                    echo "<option value='$callerName'>$callerName</option>";
                                }
                            }
                            ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="211" class="left boder0-right">&nbsp;
                     <label>Customer Email ID</label>
                  </td>
                  <td width="192" class="left boder0-right">
                     <span class="left boder0-left"> 
                        <select name="email" id="email" class="select-styl1">
                            <option value="">Select Email ID</option>
                            <?php
                                $query = "SELECT DISTINCT v_fromemail FROM $db.web_email_information";
                                $result = mysqli_query($link, $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='".$row['v_fromemail']."'>".$row['v_fromemail']."</option>";
                                }
                            ?>
                        </select>
                    </span>
                </td>
                <td width="211" class="left boder0-right">&nbsp;
                    <label for="phone_number">Phone Number</label></td>
                <td width="192" class="left boder0-right">
                    <select name="phone_number" id="phone_number" class="select-styl1">
                        <option value="">Select Phone Number</option>
                        <?php
                            $query = "SELECT DISTINCT send_to FROM $db.sms_out_queue";
                            $result = mysqli_query($link, $query);
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='".$row['send_to']."'>".$row['send_to']."</option>";
                            }
                        ?>
                    </select>
                </td>
                <td width="211" class="left boder0-right">&nbsp;
                    <label>Agent Name</label>
                </td>
                <td width="192" class="left boder0-right">
                    <span class="left boder0-left">
                        <select name="agent_name" id="agent_name" class="select-styl1">
                            <option value="">Select Agent Name</option>
                            <?php
                            // Query to fetch the AtxUserName by joining the multichannel_disposition and uniuserprofile tables
                            $agents = mysqli_query($link, "
                                SELECT DISTINCT up.AtxUserName
                                FROM multichannel_disposition md
                                INNER JOIN uniuserprofile up ON md.createdby = up.AtxUserID
                            ");
                            // Loop through the results and populate the dropdown
                            while ($row = mysqli_fetch_assoc($agents)) {
                                echo "<option value='" . htmlspecialchars($row['AtxUserName']) . "'>" . htmlspecialchars($row['AtxUserName']) . "</option>";
                            }
                            ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <td width="211" class="left boder0-right">&nbsp;
                    <label>Disposition</label>
                </td>
                <td width="192" class="left boder0-right">
                    <span class="left boder0-left">
                    <select name="disposition" id="disposition" class="select-styl1">
                        <option value="">Select Disposition</option>
                        <?php
                        $dispositions = mysqli_query($link, "SELECT DISTINCT disposition_type FROM multichannel_disposition");
                        while ($row = mysqli_fetch_assoc($dispositions)) {
                            echo "<option value='" . htmlspecialchars($row['disposition_type']) . "'>" . htmlspecialchars($row['disposition_type']) . "</option>";
                        }
                        ?>
                    </select>
                    </span>
                </td>
                <td width="211" class="left boder0-right">&nbsp;
                    <label>Channel Type</label>
                </td>
                <td width="192" class="left boder0-right">
                    <span class="left boder0-left">
                        <select name="channel_type" id="channel_type" class="select-styl1">
                            <option value="">Select Channel Type</option>
                            <?php
                            $channels = mysqli_query($link, "SELECT DISTINCT channel_type FROM multichannel_disposition");
                            while ($row = mysqli_fetch_assoc($channels)) {
                                echo "<option value='" . htmlspecialchars($row['channel_type']) . "'>" . htmlspecialchars($row['channel_type']) . "</option>";
                            }
                            ?>
                        </select>
                    </span>
                </td>
                </td>
                <td class="left boder0-right"> <label>Sentiment</label></td>
                <td class="left boder0-right">
                    <select name="sentiment" id="sentiment" class="select-styl1">
                            <option value="">Select</option>
                            <option value="negative">Negative</option>
                            <option value="positive">Positive</option>
                            <option value="neutral">Neutral</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="left  boder0-left" colspan="4">
                    <input name="submit" id="submit_disposition" type="button" value="Run Report" class="button-orange1">
                    <?php $disposition_report = base64_encode('disposition_report');?>
                    <input name="reset" id="reset_disposition" type="button" value="RESET" onclick="window.location.href='omni_channel.php?token=<?php echo $disposition_report; ?>';" 
                     class="button-orange1">
                </td>
            </tr>
        </tbody>
    </table>
</div>
    <!-- Start Display the filter data -->
    <div class="table">
            <div class="wrapper6">
               <div>
                    <table class="tableview tableview-2" id="disposition_data">
                            <thead>
                            <tr class="" style="font-size: 12px">
                                <th align="center" style="width:3%">S.No.</th>
                                <th align="center" style="width:10%">Agent Name</th>
                                <th align="center" style="width:10%">Caller Name</th>
                                <th align="center" style="width:10%">Phone No.</th>
                                <th align="center" style="width:15%">Email ID</th>
                                <th align="center" style="width:10%">Channel Type</th>
                                <th align="center" style="width:10%">Disposition </th>
                                <th align="center" style="width:10%">Sentiment</th>
                                <th align="center" style="width:10%">Remark</th>
                                <th align="center" style="width:10%">Entry Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>