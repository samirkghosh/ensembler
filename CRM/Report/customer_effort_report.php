<?php
/***
 *  Report -> Customer effort report page
 * Author: Ritu modi
 * Date: 16-02-2024
 * 
 * Description: This form generates a report based on various filters related to customer effort.
                It allows users to select date range, customer effort, category, subcategory, complaint origin, and mode.
**/?>

<!-- added this code for increasing the width size  of the created date field [vastvikta][11-02-2025]-->
<style>
    th.created-date {
        width: 150px;  /* Adjust width as needed */
        min-width: 150px;
    }
</style>
<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Customer Effort Score (CES)</span>
<div class="table">
    <table class="tableview tableview-2 main-form new-customer" style="background-color: #fff;">
        <tbody>
            <tr>
                <?php
                // Determine start and end datetime parameters[vastvikta][17-03-2025]
                        
                $dateRange = get_date_ces();

                $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                    ? $_REQUEST['sttartdatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                    ? $_REQUEST['enddatetime'] 
                    : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
               ?>
                <td class="left boder0-left "><span class="left  boder0-right">
                From
                <input type="text" name="sttartdatetime" class="dob1 date_class"
                    value="<?=$startdatetime?>" id="sttartdatetime">&nbsp;
                To <input type="text" name="enddatetime" class="dob1 date_class"
                    value="<?=$enddatetime?>" id="enddatetime">
                </span>
                </td>
                <?php $result = get_status_ces(); ?>
                <td class="left boder0-left ">
                    <label>Customer Effort &nbsp; </label>
                    <select name="customer_effort" id="customer_effort" class="select-styl1">
                            <option value="">Select</option>
                            <? $sel ='';
                            while($row = mysqli_fetch_assoc($result)){
                                if($row['id'] == $_REQUEST['customer_effort'])
                                {
                                    $sel="selected";
                                }else
                                {
                                    $sel='';
                                }
                            ?>
                            <option value="<?=$row['id']?>" <?=$sel?>><?=$row['customer_effort']?></option>
                            <? }?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="left  boder0-right">
                    <div class="log-case" id="Enq_pr" style="display: block;">
                        <label>Category</label>
                        <select name="category" id="category" class="select-styl1" style="width:190px;">
                            <option value="">Select Category</option>
                            <?php
                            // Fetch and display categories
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
                    <select name="subcategory" id="subcategory" class="select-styl1" style="width:190px; ">
                        <option value="">Select Sub Category</option>
                        <?php
                            // Fetch and display subcategories based on the selected category
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
                <td class="left boder0-right">
        <?php
        // Fetch and display complaint origins
        $sourceresult = get_source(); // Assuming get_source() function fetches sources from database
        ?>
        <label>Complaint Origin</label>
        <select name="source" id="source" class="select-styl1" style="width: 190px">
            <option value="">Select Complaint Origin</option> <!-- Changed value to empty string -->
            <?php  
            while ($row = mysqli_fetch_array($sourceresult)) {
                $selected = ($source == $row['id']) ? 'selected' : '';
                ?>
                <option value='<?= $row['id'] ?>' <?= $selected ?>><?= $row['source'] ?></option>
            <?php } ?>
        </select>
    </td>                                                                
                <?php
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
            </tr>
            <tr>
                <td class="left boder0-right">
                <span class="left  boder0-left">
                    <!-- Submit button and Reset link -->
                     <input name="submit" id="submit_effort" type="button" value="Run Report" class="button-orange1">
                      <input name="reset" id="reset_effort" type="button" value="RESET" class="button-orange1">
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
                  <table class="tableview tableview-2" id="CEFFORT_data">
            <thead>
                <tr class="" style="font-size: 12px">
                    <th align="center">S.No.</th>
                    <th align="center">Case ID</th>
                    <th align="center">Customer Name</th>
                    <th align="center">Phone No.</th>
                    <th align="center">Email </th>
                    <th align="center">Category </th>
                    <th align="center">SubCategory </th>
                    <th align="center">Customer Effort</th>
                    <th align="center">Feedback Mode</th>
                    <th align="center">Complaint Origin</th>
                    <th align="center" class="created-date">Created Date</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</form>