<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include("../../config/web_mysqlconnect.php");
include_once("erlang_function.php");
$cal = new ErlangC();
$Forecast_Cal = $cal->forcast_calculate();
?>
<body>
    <form name="frmagentdashboardd" action="" method="post">
      <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Forecast Accuracy</span>
          <form name="frmagentdashboardd" action="" method="post">              
            <table class="tableview tableview-2 main-form new-customer">
              <tbody>
                <tr>
                  <td class="left boder0-right">
                      <label for="">Date</label>

                      <input type="text" name="sttartdatetime" class="date_class dob1 select-styl1" style="width:190px;" value="<?=$_REQUEST['sttartdatetime']?>" id="startdatetime" autocomplete="off" style="width:150px;">
                  </td>
                            
                </tr>
                <tr>
                  <td class="left boder0-right">
                    <label for="">From Time</label>
                    <input type="text" name="from_time" class="date_class dob1 select-styl1" style="width:190px;" value="<?=$_REQUEST['from_time']?>" id="from_time" autocomplete="off" style="width:150px;">               
                  </td>
                  <td class="left boder0-right">
                    <label for="">To Time</label>
                    <input type="text" name="to_time" class="date_class dob1 select-styl1" style="width:190px;" value="<?=$_REQUEST['to_time']?>" id="to_time" autocomplete="off" style="width:150px;">
                  </td>
                  <td class="left boder0-right">
                    <input type='submit' name='sub1' value='Show' class="submit_wfm button-orange1" onclick="callback();">
                  <!-- <input type='submit' name='exp1' value='Export' onClick="export_excel();" class="submit_wfm button-orange1"> -->
              	</td>
                </tr>
                      </tbody>
              </table><br/>
          <div style="margin-bottom: 10px;">
            <table class="cch-table blue small text-center table table-striped table-bordered table-hover">
              <thead>
                <tr class="row_2">
                  <th><b>Interval</b></th>
                  <th><b>Call Offered</b></th>
                  <th><b>Forecast</b></th>
                  <th><b>Absolute Error</b></th>
                  <th><b>% Difference</b></th>
                </tr>
              </thead>
            <tbody id="div_forcast">
             
                <?php 
                $totalCall = 0;
                $totalForcast = 0;
                if(!empty($Forecast_Cal)){
                foreach ($Forecast_Cal as $value) {
                  $absolute_error =   $value['call_offered'] -$value['Forecast_value'];
                  $difference =  round(($absolute_error/$value['call_offered'])*100,2);
                ?>
                 <tr>
                <td><?php echo $value['interval'];?></td>
                <td><?php echo $value['call_offered'];?></td>
                <td><?php echo $value['Forecast_value'];?></td>
                <td><?php echo $absolute_error;?></td>
                <td><?php echo $difference;?></td>
                 </tr>
              <?php 
              $totalCall = $value['call_offered'] + $totalCall;
              $totalForcast = $value['Forecast_value'] + $totalForcast;
              $totalabsolute_error =   $totalCall - $totalForcast;
              $totaldifference =  round(($totalabsolute_error/$totalCall)*100,2);
              } ?>
              <tr>
                <td><strong>Total</strong></td>
                <td><?php echo $totalCall;?></td>
                <td><?php echo $totalForcast;?></td>
                <td><?php echo $totalabsolute_error;?></td>
                <td><?php echo $totaldifference;?></td>
              </tr>
              <?php
              }else{
              ?>

              <tr><td colspan="5"><div class="row"><div style="color:red;"><b><center>Data Not Found.</center></b></div></div></td></tr>
            <?php }?>
            </tbody>
          </table>
          </div>
      </form>
    </form>       