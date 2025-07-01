<?php
/***
 * Report -> Audit
 * Author: Ritu modi
 * Date: 29-01-2024
 * 
 * This page is designed for generating an audit report. Users can select a specific agent, a date range, and then run the report to display details such as the created date, agent name, remarks, and IP address. The report also provides options for resetting the form.
**/?>
<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Audit report</span>
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <tr>
                        <td class="left boder0-right"><label>User</label>
                        <div class="log-case">
                        <?php $result=get_active_agents($db, $link); ?>
                            <select name="agent" id="agent" class="select-styl1"
                                style="width:190px">
                                <option value="">Select User</option>
                                <? 
                                while($row=mysqli_fetch_array($result)) {
                                $AtxUserID=$row['AtxUserID'];   
                                $AtxUserName=$row['AtxUserName'];
                                if($AtxUserID == $agent)
                                {
                                $sel = 'selected';
                                }
                                else
                                {
                                $sel = '';
                                }
                                ?>
                                <option value='<?=$AtxUserID?>' <?=$sel?>><?=$AtxUserName?>
                                </option>
                                <? } ?>
                            </select>
                        </div>
                     </td>
                    <?php
                        // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
                        $dateRange = get_date_audit_report();

                        $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                            ? $_REQUEST['sttartdatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                        $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                            ? $_REQUEST['enddatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                            ?>
                        <td class="left  boder0-right">
    					<label>From
                        <input type="text" name="sttartdatetime" class="date_class dob1" value="<?php echo $startdatetime?>" id="sttartdatetime"></label>
                        <label>To
                        <input type="text" name="enddatetime" class="date_class dob1" value="<?php echo $enddatetime?>" id="enddatetime"></label>
    					</td>
                        <td colspan="2" class="left boder0-right">
                            <!-- Buttons for running report and resetting form -->
                             <input name="submit" id="submit_audit" type="button" value="Run Report" class="button-orange1">
                              <input name="reset" id="reset_audit" type="button" value="RESET" class="button-orange1">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
         <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
                <?php 
                $pdf_heading = "Audit Report"; // Setting PDF heading
                ?>
               <div>
                <div class="download_label" style="display: none;"> <?php echo $pdf_heading?></div>
                  <table class="tableview tableview-2" id="audit_data">
                    <thead>
                        <tr class="" style="font-size: 12px">
                            <th>S.No.</th>
                            <th>Created On</th>
                            <th>Agent Name</th>
                            <th>Remark</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </form>