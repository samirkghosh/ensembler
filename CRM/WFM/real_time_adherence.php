<?php

/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include("../../config/web_mysqlconnect.php");
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
define("PIXCEL","4");
?>  
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/WFM/css/wfm_common.css">
<!-- <link rel="stylesheet" type="text/css" href="../templates/css/agent_styles.css"/> -->
  <form name="frmagentdashboardd" action="" method="post">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Real Time Adherence</span>
        <form action="">
          <table class="tableview tableview-2 main-form new-customer">
            <tbody>
                <tr>
                  <td class="left boder0-right">
                     <label>Schedule</label>
                     <div class="log-case">
                        <select class="select-styl1" style="width:190px" id="sch_id" name="sch_id">
                          <?
                          $sqlschedule="select i_procSchedID,v_schedName from $db.tbl_wfm_proc_schedule";    // query for getting agent between satisfied date
                          $scheduleresult=mysqli_query($link,$sqlschedule);
                           while($rowschedule=mysqli_fetch_array($scheduleresult)) 
                          {
                            ?>
                              <option value="<?=$rowschedule['i_procSchedID']?>"><?=$rowschedule['v_schedName']?></option>
                            <?
                          }
                          ?>
                        </select>
                   </div>
                  </td>
                  <td  class="left  boder0-right">
                     <?php
                        $startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");
                        $enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");
                        ?>
                     <label>Select Date</label>
                     <input type="text" name="startdatetime" class="dob1" value="<?=$_REQUEST['sttartdatetime']?>" id="startdatetime" autocomplete="off" style="width:180px">
                  </td>
                  <td class="left  boder0-right">
                     <input type='submit' name='sub1' value='Show' class="submit_wfm button-orange1">
                  </td>
                </tr>
                <tr>
                <td style="background-color: #009933;color:#fff;padding:7px;">Adhering Schedule</td>
                <td style="background-color: #FF0000;color:#fff;padding:7px;">No Adhering Schedule</td>
                <td style="background-color: #CCC;padding:7px;">On leave </td>
                <td style="background-color: #FFA500;color:#fff;padding:7px;">Less Adhering Schedule</td>
                <td style="background-color: #666;color:#fff;padding:7px;">No Adhering Break</td>
                <td style="background-color: #CCC;padding:7px;">Adhering Break</td>
              <td></td>
              </tr>
            </tbody>
          </table>
          <div  id="div_adherence">
          </div>
        </form>
  </form>
