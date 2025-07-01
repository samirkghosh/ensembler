<?php
/***
 * CSAT-DSAT Summary Page
 * Author: Ritu modi
 * Date: 05-02-2024
 * 
 * This form generates a summary report of CSAT-DSAT (Customer Satisfaction and Dissatisfaction) data. It allows users to filter the report by specifying a date range and agent. The form submits the data via POST method to the server for processing.
**/

// made change to the  startdate and enddate [vastvikta][17-03-2025]
$dateRange = get_date_csat_dsat();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
    ? $_REQUEST['sttartdatetime'] 
    : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
    ? $_REQUEST['enddatetime'] 
    : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

?>

<form name="myform" method="post">
<!-- Hidden inputs for Dialed_DigitP and Dialed_Digit values -->
<input name="Dialed_DigitP" id="Dialed_DigitP" type="hidden" value="<?=$Dialed_DigitP?>"  >
<input name="Dialed_Digit" id="Dialed_Digit" type="hidden" value="<?=$Dialed_Digit?>" >
    <div class="style2-table ">
    <div class="table">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">CSAT-DSAT SUMMARY REPORT</span>
        <table class="tableview tableview-2 main-form new-customer">
            <tbody>
                <tr>
                <?php
                // Setting default start and end date values
                ?>
                    <td class="left">
                    From 
                    <input type="text" name="sttartdatetime" class="dob1 date_class" value="<?=$startdatetime?>" id="sttartdatetime" autocomplete="off">&nbsp;
                    To <input type="text" name="enddatetime" class="dob1 date_class" value="<?=$enddatetime?>" id="enddatetime" autocomplete="off">
                    </td>
                    <td  class="left  boder0-right"><label>Agent</label>
                            <?php
                            // Fetching user profile data
                                $result=uniuserprofile();
                            ?>
                            <select name="agent" id="agent" class="select-styl1" style="width:190px">
                            <option value="">Select Agent</option>
                            <?php
                            while($row=mysqli_fetch_array($result)) {
                                $AtxUserID=$row['AtxUserID'];   
                                $AtxUserName=$row['AtxUserName'];
                                if($AtxUserID == $agent){
                                    $sel = 'selected';
                                }else{
                                    $sel = '';
                                }
                            ?>
                            <option value='<?php echo $AtxUserName?>' <?=$sel?>><?=$AtxUserName?></option>
                            <?php } ?>
                            </select>
                            
                    </td>
                    <!-- Run Report and Reset buttons -->
                    <td class="left  boder0-right">
                      <input name="submit" id="submit_summary" type="button" value="Run Report" class="button-orange1">
                      <input name="reset" id="reset_summary" type="button" value="RESET" class="button-orange1">
                    </td>
                </tr>
            </table>
        </div>
         <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="csrss_data">
                    <thead>
                    <tr class="" style="font-size: 12px">
                        <th>S.No.</th>
                        <th>Agent</th>
                        <th>Percentage</th>
                    </tr>
                    </thead>
                  </table>
                </div>
   <table width="100%">
        <?php 
          //  $no_of_survey_total = mysqli_fetch_assoc(mysqli_query($link,"SELECT count(*) as total FROM $db.`tbl_civrs_cdr` where Connect_time>='$from' and Connect_time<='$to'"));
          //  $total=mysqli_num_rows(mysqli_query($link,$total_score));
            $total_percentage =  calculateTotalPercentage($from, $to);
            if($no>0)
            { 
        ?>    
        <tr>
            <td style="font-weight:700;color:crimson;">Overall Percentage - <?=round($total_percentage,2)?>%</td>
        </tr> 
        <? 
            } 
        ?>
    </table> 
</div>
</form>
