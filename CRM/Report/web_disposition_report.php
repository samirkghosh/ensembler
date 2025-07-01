<?php
/***
 * Disposition report page
 * Author: Ritu modi
 * Date: 11-04-2024
 * 
 * This is a form for generating a disposition report.
**/?>
<form name="myform" method="post">
  <div class="style2-table ">
      <div class="table">
      <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Disposition REPORT</span>
        <table class="tableview tableview-2 main-form new-customer">
            <tbody>
              <tr>
                  <td width="50%" class="left boder0-right">
                    <label>Customer Name</label>
                    <div class="log-case">
                      <input type="text" class="select-styl1" onkeypress="return isAlphabetKey(event)" name="fname" id="fname" value="<?=$fname?>">
                    </div>
                  </td>
                  <td  width="50%" class="left  boder0-right">
                    <label>Caller ID</label>
                    <div class="log-case">
                      <input type="text" class="select-styl1" name="CallerID" id="CallerID" value="<?=$CallerID?>">
                    </div>
                  </td>
              </tr>
              <tr>
                <td class="left  boder0-right">
                  <label>Reason of Calling</label>
                    <div class="log-case">
                    <?php
                    $complaint_sql = getComplaints();
                    while ($rows = mysqli_fetch_array($complaint_sql)) {  
                    if($rows['slug']=='none')break;
                    ?>
                    <span class="slug"> 
                    <input type="radio" name="casetype" id="type<?=$rows['id']?>" value="<?=$rows['complaint_name']?>"  
                    <? if ($casetype==$rows['complaint_name']) {echo "checked";}?> > <?=$rows['complaint_name']?>
                      </span>
                    <?  }?>
                    </div>
                  </td>
                  <td width="50%" class="left  boder0-right">
                    <label>Disposition<em>*</em></label>
                    <?php
                      $q = getDispositions() ?>
                      <div class="log-case">
                        <select class="select-styl1" name="disposition" id="disposition">
                          <option value="0">Select Disposition  </option>
                          <? while ($qq = mysqli_fetch_array($q)) { ?>
                            <option value="<?= $qq['V_DISPO'] ?>" <? if ($qq['V_DISPO'] == $_POST['disposition']) {
                              echo "selected";
                              } ?>>
                              <?= strtolower($qq['V_DISPOSITION']) ?>
                            </option>
                          <? } ?>
                        </select>
                        <?php mysqli_close($link1);
                        ?>
                  </td>
                </tr>
                <tr>
                  <td width="50%" class="left  boder0-right">
                    
                    <label>Sentiment</label>
                    <?php
                      $res_sen = getSentiments(); 
                      ?>
                      <select class="select-styl1" name="sentiment" id="sentiment">
                      <option value="0">Select Sentiment</option>
                      <? while ($row_sen = mysqli_fetch_array($res_sen)) { ?>
                      <option value="<?= $row_sen['sentiment'] ?>" <? if ($row_sen['sentiment'] == $_POST['sentiment']) {
                      echo "selected";
                      } ?>>
                      <?= strtolower($row_sen['sentiment']) ?>
                      </option>
                      <? } ?>
                      </select>
                  </td>
                  <td width="50%" class="left  boder0-right">
                      <?php
                        // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
                        $dateRange = get_date_dispo();

                        $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                            ? $_REQUEST['sttartdatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                        $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                            ? $_REQUEST['enddatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                      ?>
                      <label> From 
                      <input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
                      <label>
                      To <input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
                    </td>
              </tr>
              <tr>
                  <td colspan="3" class="left boder0-right">
                  <center>
                 <!-- Button to run the report -->
                    <input name="submit" id="submit_dis" type="button" value="Run Report" class="button-orange1">
                    <!-- Button to reset the form -->
                    <input name="reset" id="reset_dis" type="button" value="RESET" class="button-orange1">
              </center>
                  </td>
              </tr>
        </table>
      </div>
      <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="disposition_data">
                  <thead>
                    <tr class="" style="font-size: 12px">
              			   <th >S.No.</th>
              			   <th >Entry Date</th>
              			   <th >Caller ID</th>
              			   <th >Customer Name</th>
              			   <th >Alternate No.</th>
              			   <th >Agent Name</th>
              			   <th >Disposition</th>
              			   <th>Remarks</th>
              			   <th >Call Duration</th>
              			   <th >Sentiment</th>
              			   <th >Feedback Score</th>
                       <th >Reason of Calling</th>
                       <th >Recorded File</th>	   
              			</tr>
                  </thead>
                </table>
              </div>
            </div>
          </form>

