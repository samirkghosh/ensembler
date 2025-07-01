<?php
/***
 * CREATE TICKET
 * Author: Aarti Ojha
 * Date: 04-03-2024
 * This file is Display Ticket listing Details and filter option
 **/
include('web_helpdesk_function.php'); // this file handle common function related ticket data
$name = $_SESSION['logged'];
$vuserid        =   $_SESSION['userid'];
$groupid        =   $_SESSION['user_group'];
#-----------end of delete-----------------------------------------------------------------------------------
$case_result = get_status_list(); /* For Showing Cases as of status */
$user_info = get_priority(); /* For Showing Priority User and Case Priority High */
$total_recd_query = $user_info['total_recd_query'];
$usertotal_recd_querys = $user_info['usertotal_recd_querys'];
?>
<link href="<?=$SiteURL?>public/css/helpdesk.css" rel="stylesheet" type="text/css" />
<!-- Start Html code  -->
<style type="text/css">
   body {
    font-family: Arial, sans-serif;
}</style>
<!-- base 64 code for the pdf download image [vastvikta][18-04-2025] -->
<div id="pdf-logo-base64" style="display: none;">
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJUAAABiCAYAAABZNZHuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RjNGNTBGNkYxNjZEMTFFNUFGMzM4OTRGQTQ2MzhCQjIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RjNGNTBGNzAxNjZEMTFFNUFGMzM4OTRGQTQ2MzhCQjIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpGM0Y1MEY2RDE2NkQxMUU1QUYzMzg5NEZBNDYzOEJCMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpGM0Y1MEY2RTE2NkQxMUU1QUYzMzg5NEZBNDYzOEJCMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pjpgqp0AADnQSURBVHja7H0HnFTV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
</div>
<div class="rightpanels mt-3">
   <form class="main-form" method="post">
      <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
         <div class="row">
            <div class="col-sm-12">
               <div class="row">
                  <div class="col-sm-2">
                     <div class="row">
                        <div class="col-sm-7">Pending</div>
                        <div class="col-sm-5"><div style="background:crimson;width:20px;height:20px;border:1px solid #999999;border-radius:5px;margin-left: -28px;"></div></div>
                     </div> 
                  </div>
                  <div class="col-sm-2">
                  <div class="row">
                        <div class="col-sm-10">In Progress</div>
                        <div class="col-sm-2"><div style="background:yellow;width:20px;height:20px;border:1px solid #999999;border-radius:5px;margin-left:-33px"></div></div>
                     </div> 
                  </div>
                  <div class="col-sm-2">
                     <div class="row">
                        <div class="col-sm-7">Closed</div>
                        <div class="col-sm-5"><div style="background:#00A36C;width:20px;height:20px;border:1px solid #999999;border-radius:5px;margin-left: -37px;"></div></div>
                     </div> 
                  </div>
                  <div class="col-sm-2">
                     <div class="row">
                        <div class="col-sm-7">Escalated</div>
                        <div class="col-sm-5"><div style="background:#FFBF00;width:20px;height:20px;border:1px solid #999999;border-radius:5px;margin-left: -17px;"></div></div>
                     </div> 
                  </div>
                  <div class="col-sm-2">
                     <div class="row">
                        <div class="col-sm-7">Priority</div>
                        <div class="col-sm-5"><div style="background:#CFB53B;width:20px;height:20px;border:1px solid #999999;border-radius:5px;;margin-left: -29px;"></div></div>
                     </div> 
                  </div>
                  <div class="col-sm-2">
                     <div class="row">
                        <div class="col-sm-10">Non Priority</div>
                        <div class="col-sm-2"><div style="background:#fff;width:20px;height:20px;border:1px solid #999999;border-radius:5px;margin-left:-33px"></div></div>
                     </div> 
                  </div>
               </div>
            </div>
           </div>
      </span>
      <div class="style2-table">
      <?php
         if (($groupid == '0000')  || ($groupid == '070000') || ($groupid == '060000') || ($groupid == '080000')) { ?>
            <div class="style-title2 st-title2-wth-lable" >
               <select name="case_status" id="case_status" class="select-styl1" style="width:140px;">
                  <option value="">select status</option>
                  <?php while ($row =  mysqli_fetch_assoc($case_result)){
                     $sel = ($_REQUEST['case_status'] == $row['id']) ? 'selected' : '';
                  ?>
                     <option value="<?php echo $row['id']; ?>" <?= $sel ?>><?php echo $row['ticketstatus']; ?></option>
                  <?php }?>
               </select> 
               <select name="priority_user" id="priority_user" class="select-styl1" style="width:155px;">
                  <option value="" >select priority customer</option>
                  <option value="1" <?php if($_REQUEST['priority_user'] == '1'){ echo "selected"; }?>>Priority</option>
                  <option value="0" <?php if($_REQUEST['priority_user'] == '0'){ echo "selected"; }?> >Non Priority</option>
               </select>
               <select name="case_priority" id="case_priority" class="select-styl1" style="width:140px;">
                  <option value="">select case priority</option>
                  <option value="high" <?php if($_REQUEST['case_priority'] == 'high'){ echo "selected"; }?> >high</option>
                  <option value="medium" <?php if($_REQUEST['case_priority'] == 'medium'){ echo "selected"; }?> >medium</option>
                  <option value="low" <?php if($_REQUEST['case_priority'] == 'low'){ echo "selected"; }?> >low</option>
               </select>
               <?php
               $web_case_detail = base64_encode('web_case_detail');
               $new_case_manual = base64_encode('new_case_manual');
               $sourceresult = display_mode();
               ?>
               <select name="source" id="source" class="select-styl1" style="width:140px">
                  <option value="">select mode</option>
                  <?php while ($row = mysqli_fetch_array($sourceresult)) { ?>
                  <option value='<?= $row['id'] ?>' <?php echo ($_REQUEST['source'] == $row['id']) ? 'selected' : ''; ?>><?= $row['source'] ?> </option>
                  <?php } ?>
               </select>
               <input name="New" type="button" value="NEW CASE" class="button-orange1" onclick="window.location.href='helpdesk_index.php?token=<?php echo $new_case_manual;?>';" />
               <input name="reset" id="reset" type="button" value="RESET" class="button-orange1">
               <input name="submit" id="submit" type="button" value="SEARCH" class="button-orange1">
            </div>
         <? } else if ($groupid == '080000' || $groupid == '090000' || $groupid == '050000' || $groupid == '060000') { ?>
            <div class="style-title2 st-title2-wth-lable">
               <select name="case_status" id="case_status" class="select-styl1" style="width:140px;">
                  <option value="">select status</option>
                  <?php while ($row =  mysqli_fetch_assoc($case_result)){
                     $sel = ($_REQUEST['case_status'] == $row['id']) ? 'selected' : '';
                  ?>
                  <option value="<?php echo $row['id']; ?>" <?= $sel ?>><?php echo $row['ticketstatus']; ?></option>
                  <?php } ?>
               </select>
            </div>
         <? } ?>
         <div class="row justify-content-sm-center">
            <div class="col col-lg-2">
            <img src="<?=$SiteURL?>public/images/icons8-priority-48.png" class="img_priorty"> High
            </div>
            <div class="col col-lg-2">
            <img src="<?=$SiteURL?>public/images/icons8-priority-48 (1).png" class="img_priorty"> Medium
            </div>
            <div class="col col-lg-2">
            <img src="<?=$SiteURL?>public/images/icons8-priority-48 (2).png" class="img_priorty"> Low
            </div>
         </div>
      </form>
         <div class="table">
            <div class="wrapper6">
               <div class="div2">
                  <table width="100%" align="center" border="0" class="tableview tableview-2"  id="customer_data">
                     <thead>
                        <tr class="background">
                           <td align="left" style="padding:6px;width: 13%;">Case Id</td>
                           <td align="center">Customer Name</td>
                           <td align="center">Category</td>
                           <td align="center">Subcategory</td>
                           <td align="center">Department</td>
                           <td align="center">Mode</td>
                           <td align="center">Status</td>
                           <td align="center">Created On</td>
                           <?php if ($groupid == '0000'){?>
                           <td align="center">Action</td>
                           <?php }?>
                        </tr>
                     </thead>
                  </table>
                   <input type="hidden" name="groupid" id="groupid" value="<?=$groupid?>">
                   <input type="hidden" name="userid" id="userid" value="<?=$vuserid?>">
                  <center>
                     <?php if (($groupid == '0000')  || ($groupid == '060000') || ($groupid == '070000')) { ?>
                           <span ><b>Priority User (<?php echo $usertotal_recd_querys['priority_user_total'];?>)</b></span>
                        <span ><b> Case Priority High (<?php echo $total_recd_query['case_priority_total'];?>)</b></span>
                     <?php } ?>
                  </center>               
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="Action">
      <input type="hidden" name="I_WrapID">
   </form>
</div>
<!-- Confirmation Popup -->
    <div id="delete-popup" class="popup-overlay" data-id="">
        <div class="popup-contents">
            <h3>Confirm Deletion</h3>
            <p>Please provide a remark before confirming:</p>
            <textarea id="remark" placeholder="Enter your remark here..."></textarea>
            <div class="popup-actions">
                <button id="confirm-delete" class="confirm-btn">OK</button>
                <button id="cancel-delete" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
<!-- Close HTML code  -->
<script src="<?=$SiteURL?>public/js/helpdesk.js"></script>