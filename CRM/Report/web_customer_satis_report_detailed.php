<?php
/***
 * CSAT-DSAT Detailed Page
 * Author: Ritu modi
 * Date: 03-02-2024
 * 
 * This form is used to generate a detailed report on CSAT-DSAT (Customer Satisfaction and Dissatisfaction) data. It allows users to filter the report based on various criteria such as type, agent, phone number, and date range. The form submits the data via POST method to the server for processing.
**/?>
<form name="myform" method="post">
    <!-- Hidden inputs for Dialed_DigitP and Dialed_Digit -->
    <input name="Dialed_DigitP" id="Dialed_DigitP" type="hidden" value="<?=$Dialed_DigitP?>"  >
    <input name="Dialed_Digit" id="Dialed_Digit" type="hidden" value="<?=$Dialed_Digit?>" >
    <div class="style2-table ">
        <div class="table">
        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">CSAT-DSAT DETAILED REPORT</span>
          <table class="tableview tableview-2 main-form new-customer">
              <tbody>
                <tr>
                    <td class="left boder0-right"><label>Feedback Mode</label>
                        <span class="left boder0-left">
                        <select name="Type" id="Type" class="select-styl1">
                        <option value="">All</option>
                        <option value="1" <? if($_REQUEST['Type']==1){ echo "selected"; } ?>>IVR</option>
                        <option value="2" <? if($_REQUEST['Type']==2){ echo "selected"; } ?>>Email</option>
                        <option value="3" <? if($_REQUEST['Type']==3){ echo "selected"; } ?>>SMS</option>
                        <option value="4" <? if($_REQUEST['Type']==4){ echo "selected"; } ?>>CHAT</option>
                        </select>
                        </span>
                    </td>
                  <?php
                      // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
                      $dateRange = get_date_csat_dsat();

                      $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                          ? $_REQUEST['sttartdatetime'] 
                          : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                      $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                          ? $_REQUEST['enddatetime'] 
                          : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                     ?>
                    <td class="left">
                        From 
                        <input type="text" name="sttartdatetime" class="dob1 date_class" value="<?=$startdatetime?>" id="sttartdatetime" autocomplete="off">&nbsp;
                        To <input type="text" name="enddatetime" class="dob1 date_class" value="<?=$enddatetime?>" id="enddatetime" autocomplete="off">
                    </td>
                </tr>
                <tr>
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
                <td  class="left  boder0-right"><label>Phone Number</label>
                <?
                $agentresult= get_agent_number();
                ?>
                <input name="Phone_Number" id="Phone_Number" type="text" oninput="validateNumericInput(this)" value="<?=$Phone_Number?>" class="input-style1">
                </td>
            </tr>
                    <tr>
            <td  class="left  boder0-left"><label>Email Address</label>
              <input name="Customer_email" id="Customer_email" type="text" value="<?=$Customer_email?>" class="input-style1">           
            
              <!-- <?php
                // Fetch and display modes
                $result = get_mode();
                ?>
                <td class="left  boder0-right">
                    <label>Feedback Mode</label>
                    <select name="mode" id="mode" class="select-styl1">
                        <option value="">Select</option>
                        <? $sel ='';
                        while($row = mysqli_fetch_assoc($result)){
                          if($row['mode'] == $mode)
                          {
                             $sel="selected";
                          }else
                          {
                            $sel='';
                          }
                        ?>
                        <option value="<?=$row['mode']?>" <?=$sel?>><?=$row['mode']?></option>
                        <?php }?>
                    </select>
                </td>  
                Row for buttons
            <tr>
              -->
                <td class="left  boder0-right">
                     <input name="submit" id="submit_detail" type="button" value="Run Report" class="button-orange1">
                      <input name="reset" id="reset_detail" type="button" value="RESET" class="button-orange1">
                </td>                       
            </tr>                
        </table>
        </div>
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="csrd_data">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>S.No.</th>
                            <th>Created On</th>
                            <th>Phone No.</th>
                            <th>Case Id</th>
                            <th>Agent</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Customer Feedback</th>
                            <th>Score</th>
                            <th>Feedback Mode</th>
                        </tr>
                </thead>
            </table>
        </div>
    </div>
</form>