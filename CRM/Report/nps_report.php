<?php
/***
 * NET PROMOTER SCORE PAGE
 * Author: Ritu modi
 * Date: 14-02-2024
 * 
 * Description: This page is designed to generate and display the Net Promoter Score (NPS) report.
  Users can filter the report by specifying date ranges, category, subcategory, complaint origin, and mode.
  After applying the filters, users can run the report to view the NPS data.
  The report includes details such as case ID, customer name, phone number, email, category, subcategory, score, NPS, mode, complaint origin, and created date.
**/?>
<!-- added this code for increasing the width size  of the created date field [vastvikta][11-02-2025]-->
<style>
    th.created-date {
        width: 150px;  /* Adjust width as needed */
        min-width: 150px;
    }
</style>

<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Net Promoter Score (NPS)</span>
<div class="table">
    <table class="tableview tableview-2 main-form new-customer" style="background-color: #fff;">
        <tbody>
            <tr>
                <?php
                // Determine start and end date values or set defaults
                        
                $dateRange = get_date_NPS();

                $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                    ? $_REQUEST['sttartdatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                    ? $_REQUEST['enddatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
               ?>
                <td class="left boder0-left "><span class="left  boder0-right">
                From
                <input type="text" name="startdatetime" class="dob1 date_class"
                    value="<?=$startdatetime?>" id="sttartdatetime">&nbsp;
                To <input type="text" name="enddatetime" class="dob1 date_class"
                    value="<?=$enddatetime?>" id="enddatetime">
                </span>
                </td>
                    <?php
                    // Fetch mode options
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
                            <?php } ?>
                    </select>
                </td>
            </tr>
            <!-- Category and Subcategory selection -->
            <tr>
                <td class="left  boder0-right">
                    <div class="log-case" id="Enq_pr" style="display: block;">
                        <label>Category</label>
                        <select name="category" id="category" class="select-styl1">
                            <option value="">Select Category</option>
                            <?php
                                $sourceresult = get_category();
                                while($row=mysqli_fetch_array($sourceresult)) {
                                    $SubI=$row['id'];   
                                    $subC=$row['category'];
                                if($SubI == $category){
                                  $sel = 'selected';
                                }else{
                                   $sel = '';
                                }?>
                            <option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="left  boder0-right">
                    <label>Sub Category</label>
                    <select name="subcategory" id="subcategory" class="select-styl1" >
                        <option value="">Select Sub Category</option>
                        <?php
                            $sourceresult = get_subcategory($link, $db, $category);
                            while($row=mysqli_fetch_array($sourceresult)) {
                                $SubI=$row['id'];   
                                $subC=$row['subcategory'];
                                if($SubI == $subcategory){
                                    $sel = 'selected';
                                }else{
                                    $sel = '';
                                }
                                ?>
                            <option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
                            <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
            <td class="left  boder0-right">
                <?php
                // Fetch and display complaint origin options (this function is in the file named report_function)
                    $sourceresult=get_source();
                ?>
                <label>Complaint Origin</label>
                <select name="source" id="source" class="select-styl1">
                    <option value="">Select Complaint Origin</option>
                    <?php 
                    while($row=mysqli_fetch_array($sourceresult)) { ?>
                    <option value='<?=$row['id']?>' <?php echo ($source==$row['id']) ? 'selected' : '' ;?> ><?=$row['source']?>  
                    </option>
                    <? } ?>
                </select>
            </td>
            <td  class="left boder0-right">
            <span class="left  boder0-left">
                <!-- Run Report and Reset buttons -->
                 <input name="submit" id="submit_nps" type="button" value="Run Report" class="button-orange1">
                  <input name="reset" id="reset_nps" type="button" value="RESET" class="button-orange1">
            </span>
            </td>
        </tr>  
    </tbody>
    </table>
</div>
    <!-- Start Display the filter data -->
         <div class="table">
         <form name="frmService" method="post">
            <div class="wrapper6">
               <div>
                  <table class="tableview tableview-2" id="nps_data">
                <thead>
                <tr class="" style="font-size: 12px">
                    <th align="center">S.No.</th>
                    <th align="center">Case ID</th>
                    <th align="center">Customer Name</th>
                    <th align="center">Phone No.</th>
                    <th align="center">Email</th>
                    <th align="center">Category</th>
                    <th align="center">SubCategory</th>
                    <th align="center">Score</th>
                    <th align="center">NPS</th>
                    <th align="center">Feedback Mode</th>
                    <th align="center">Complaint Origin</th>
                    <th align="center" class="created-date">Created Date</th>
                </tr>
            </thead>
        </table>
    </div>
    </div>
</form>
