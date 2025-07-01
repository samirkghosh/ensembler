<?php
/***
 * Report -> Agent
 * Author: VASTVIKTA NISHAD
 * Date: 11-09-2024
 * Desc: for  displaying the data of the break 
 **/?>
<title>agent</title>
<form name="myform" method="post">
<div class="style2-table ">
    <div class="table">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">crm users break time report</span>
    <!-- Main form table -->
      <table class="tableview tableview-2 main-form new-customer">
          <tbody>
            <tr>
            <?php
                 // Determine start and end datetime parameters[vastvikta][17-03-2025]
                                    
                 $dateRange = get_date_agent_break();

                 $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                     ? $_REQUEST['sttartdatetime'] 
                     : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                 $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                     ? $_REQUEST['enddatetime'] 
                     : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                 ?>
               
                <td class="left boder0-left boder0-right">
                <label> Start Time 
                    <input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
                </td> 
                  <td class="left boder0-left -right ">
                    <label>
                    End Time <input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
                </td>
            </tr>
            <tr>
              <!-- Table cell for submitting the form -->
                <td colspan="3" class="left boder0-center">
         
                <?php $web_agent_break = base64_encode('web_agent_break');?>
                 <input name="submit" id="submit" type="button" value="Run Report" class="button-orange1">
                
<input type="button" 
       class="button-orange1" 
       value="Reset" 
       onclick="window.location.href='report_index.php?token=<?php echo $web_agent_break; ?>';" 
       style="float:inherit; color:#222; text-decoration:none; " />
             
                </td>
            </tr>
          </tbody>
      </table>
    </div>
         <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id = 'agent_break_report'>
                <thead>
                  <tr class="background" style="font-size: 12px">
                      <th align="center">S.No.</th>
                      <th align="center">Username</th>
                      <th align="center">Event Date</th>
                      <th align="center">Toggle View</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</form>


