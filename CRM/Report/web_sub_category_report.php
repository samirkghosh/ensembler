<?php
/***
 * Frequent Ticket page
 * Author: Ritu modi
 * Date: 02-02-2024
 * 
 * This form generates a report of frequent tickets based on specified time periods or custom date ranges. It allows users to filter the report by selecting predefined time periods or entering custom date ranges. The form submits the data via POST method to the server for processing.
**/?>

<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Frequent Cases</span>
    <div class="table">                   
    <table class="tableview tableview-2 main-form new-customer" style="background-color: #fff;">
        <tbody>                              
            <tr>
                <!-- Select box for choosing time period -->
                <td class="left boder0-right"><label>Time Period</label>
                <span class="left boder0-left">
                <select name="timeperiod" id="timeperiod" class="select-styl1">
                <option value="">Select a Period</option>
                <option value="1" <? if($_POST['timeperiod']==1){ echo "selected" ;
                } ?>>This Month</option>
                <option value="2" <? if($_POST['timeperiod']==2){ echo "selected" ;
                } ?>>Previous Month</option>
                <option value="3" <? if($_POST['timeperiod']==3){ echo "selected" ;
                } ?>>This Financial Year</option>
                <option value="4" <? if($_POST['timeperiod']==4){ echo "selected" ;
                } ?>>Previous Financial Year</option>
                </select>
                </span>
                </td>
                <!-- Separator between time period and date range options -->
                <td class="left boder0-left boder0-right">OR</td>
                <?php
                // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
                $dateRange = get_date_overview();

                $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                    ? $_REQUEST['sttartdatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                    ? $_REQUEST['enddatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                ?>
                <td class="left boder0-left "><span class="left  boder0-right">
                From
                <input type="text" name="sttartdatetime" class="date_class dob1"value="<?=$startdatetime?>" id="sttartdatetime">&nbsp;
                To <input type="text" name="enddatetime" class="date_class dob1"value="<?=$enddatetime?>" id="enddatetime"></span></td>
            </tr>
            <tr>
                <!-- Submit button to run the report -->
                <td colspan="3" class="left boder0-right">
                <span class="left  boder0-left">
                    <input name="submit_ticket" id="submit_ticket" type="button" value="Run Report" class="button-orange1">
                    <input name="reset_ticket" id="reset_ticket" type="button" value="RESET" class="button-orange1"> 
                </span>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <div class="table">
        <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="ft_data">
                    <thead>
                        <tr class="" style="font-size: 12px">
                            <th align="center">S.No.</th>
                            <th align="center">Category</th>
                            <th align="center">Sub Category</th>
                            <th align="center">Case Count</th>
                        </tr>
                    </thead>
                  </table>
              </div>
          </div>
      </form>
