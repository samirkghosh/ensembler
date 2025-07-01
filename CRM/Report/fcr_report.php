<?php 
/***
 * FCR report page
 * Author: Ritu modi
 * Date: 08-02-2024
 * 
 * Description: This page is used to display and filter the FCR (First Call Resolution) report.
                It allows users to specify date ranges, view FCR data, and reset the form for filtering.
**/?>
<form name="myform" method="post">
    <!-- Breadcrumb for FCR (First Call Resolution) -->
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">FCR (First Call Resolution)</span>
    <div class="style2-table ">
        <div class="table">
            <!-- Main form for filtering FCR report -->
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <tr>
                        <td width="50%" class="left  boder0-right">
                            <?php
                            // Determine start and end datetime parameters[vastvikta][17-03-2025]
                                    
                            $dateRange = get_date_fcr();

                            $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                                ? $_REQUEST['sttartdatetime'] 
                                : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                            $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                                ? $_REQUEST['enddatetime'] 
                                : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                     ?>
                            <label>From
                            <input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
                            <label>To
                            <input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
                        </td>
                        <td class="left  boder0-right">
                            <center>
                                <!-- Button to run the report -->
                             <input name="submit" id="submit_fcr" type="button" value="Run Report" class="button-orange1">
                              <input name="reset" id="reset_fcr" type="button" value="RESET" class="button-orange1">
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="fcr_data">
                        <thead>
                            <tr style="font-size: 12px">
                                <th >Case ID</th>
                                <th style="width:10%" >Created On</th>
                                <!-- <th >Aged</th> -->
                                <th style="width:10%">Closed On</th>
                                <th >Status</th>
                                <th >Complaint Origin</th>
                                <th >Name</th>
                                <!-- <th >Age Range</th> -->
                                <th >Agent</th>
                                <th >Category</th>
                                <th >Sub Category</th>
                                <th >Department</th>
                                <th >Remark</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </form>