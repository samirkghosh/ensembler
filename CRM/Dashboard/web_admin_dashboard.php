<?php
/**
 * Dashboard Page
 * Author: Farhan 
 * Date: 04-07-2024
 * 
 * This page displays various statistics and charts related to cases and user activities. 
 */
// Encoding the 'new_dashboard' string using base64 encoding

$new_dashboard = base64_encode('new_dashboard');
$web_report = base64_encode('web_report');
$fcr_report = base64_encode('fcr_report');
$web_admin_csat_dashboard = base64_encode('web_admin_csat_dashboard');
$wallboard = base64_encode('live_wallboard');

// Retrieving user session data
$groupid = $_SESSION['user_group'];
$vuserid = $_SESSION['userid'];
$name = $_SESSION['logged'];
// need discussion with aarti ma'am [vastvikta][23-04-2025]
// Check if user session is valid and user group is '0000' (assuming '0000' is a specific user group)
// if ($vuserid > 0 && $groupid == '0000') {
//     // Check if the 'loggedin_audit' session variable is not set or not equal to 1
//     if (!isset($_SESSION['loggedin_audit']) && $_SESSION['loggedin_audit'] != 1) {
//         // Final action message when user logs in
//         $final_action = "$name User LoggedIn";
//         // Add an audit log for successful login
//         add_audit_log($vuserid, 'loggedin', 'null', 'login success', $db);
//         // Set 'loggedin_audit' session variable to '1' to indicate that audit log has been added
//         $_SESSION['loggedin_audit'] = '1';
//     }
// }
// added the code for module licensing if disabled redirect to customers [vastvikta][03-04-2025]
$web_helpdesk = base64_encode('web_helpdesk');
$module_flag_customer = module_license('Dashboard');
$module_groupid = module_license_id('Dashboard',$groupid);
if ($module_flag_customer != '1' || $module_groupid != '1') {
   echo "<script>
       setTimeout(function() {
           window.location.href = 'helpdesk_index.php?token=" . $web_helpdesk . "';
       }, 100); 
   </script>";
   exit();
}

?>
            <style>
.toggle-header {
  cursor: pointer;
  font-weight: bold;
  margin-bottom: 15px;
  display: flex;
  justify-content: right; /* aligns text left and sign right */
  align-items: center;
  padding: 0 10px;
}
.toggle-button {
  font-size: 18px;
}
.charts_wrapper {
  border: 1px solid #ccc;
  margin: 5px;
  width: 100%;
}
.charts_container {
  padding: 10px;
  height: 400px;
  display: flex;
  flex-direction: column;
  justify-content: start;
}
.charts_div {
  flex: 1;
  width: 100%;
}
</style>
<div class="col-sm-12 mt-3"  style="background-color: white !important;">
   <form name="frmagentdashboard" action="" method="post">
      <!-- Dashboard Section -->
      <div class="style2-table">
         <!-- Dashboard Title -->
         <div class="style-title">
            <div class="row">
               <div class="col-sm-6">
                  <h3>Dashboard</h3>
               </div>
            </div>
         </div>
         <div class="style-title2 st-title2-wth-lable main-form">
          <?php
          // Set default start and end dates if not submitted
            $startdate = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : date("01-m-Y 00:00:00");
            $enddate = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : date("d-m-Y 23:59:59");
            ?>
            <div class="row">
               <div class="col-sm-8">
                  <!-- Date Range Selection -->
                  From : <input type="text" name="startdatetime" class="date_class dob1" value="<?=$startdate?>" id="startdatetime" autocomplete="off">&nbsp;&nbsp;
                          To : <input type="text" name="enddatetime" class="date_class dob1"  value="<?=$enddate?>" id="enddatetime"  autocomplete="off">
                  <input type='submit' name='sub1' id="filter_record" value='GO' class="btn btn-danger" style="font-size:small">
                  <input type="button" value="JPG Convert" id="btnConvert" class="btn btn-success" style="font-size:small">
                  <a href="dashboard_index.php?token=<?php echo $wallboard;?>">Live</a>
               </div>
               <div class="col-sm-4 btn" style = 'font-size:13px;'>
               <a href="dashboard_index.php?token=<?php echo $new_dashboard;?>">Overall Dashboard</a>
               </div>
            </div>
         </div>
         <!-- Dashboard -->
         <div class="table" id="SRallview">
               <table class="tableview tableview-2">
                  <tbody>
                     <tr class="background5">
                        <th align="center" class="totalcomplaint" colspan="">
                           <center>Total Cases<a href="report_index.php?token=<?php echo $web_report;?>"><span id="total_cases_e"></span></a></center>
                        </th>
                        <th align="center" class="totalcomplaint" colspan="">
                           <center>Complaint Cases<a href="report_index.php?token=<?php echo $web_report;?>&casetype=Complaint"><span id="complaints_e"></span></a></center>
                        </th>
                        <th align="center" class="totalcomplaint" colspan="">
                           <center>Inquiries Cases<a href="report_index.php?token=<?php echo $web_report;?>&casetype=Inquiry"><span id="inquiries_e"></span></a></center>
                        </th>
                        <th align="center" class="totalcomplaint" colspan="2">
                           <center>Others Cases<a href="report_index.php?token=<?php echo $web_report;?>&casetype=Others"><span id="others_e"></span></a></center>
                        </th>
                     </tr>
                     <tr>
                        <td colspan="5" class="totalcomplaint"><center><span style="color: #fff;font-weight: 600;">First Contact Resolution - <a style="color: #fff;" href="report_index.php?token=<?php echo $fcr_report;?>"><span id="resolution_rate_e"></span></a></center></span></td>
                     </tr>
                     <tr class="background5">
                        <th align="center" style="background:#C41E3A;" colspan="">
                           <center>Pending<a href="report_index.php?token=<?php echo $web_report;?>&status=1"><span id="pending_e"></span></a></center>
                        </th>
                        <th align="center" style="background:#F28C28;"  colspan="">
                           <center>In Progress<a href="report_index.php?token=<?php echo $web_report;?>&status=8"><span id="inprogress_e"></span></a></center>
                        </th>
                        <th align="center" style="background:#228B22;"  colspan="">
                           <center>Closed<a href="report_index.php?token=<?php echo $web_report;?>&status=3"><span id="closed_e"></span></a></center>
                        </th>
                        <th align="center" style="background:#00293c;" colspan="">
                           <center>Assigned<a href="report_index.php?token=<?php echo $web_report;?>&status=1&casetype=Complaint"><span id="assigned_e"></span></a></center>
                        </th>
                        <th align="center" style="background:#cac531;">
                           <center>Escalated<a href="report_index.php?token=<?php echo $web_report;?>&status=4"><span id="escalated_e"></span></a></center>
                        </th> 
                     </tr>
                     <!-- added the link for the sentiment graph[vastvikta][21-03-2025] -->
                     <tr>
                        <td class="totalcomplaint" colspan="5">
                           <center>
                              <a href="dashboard_index.php?token=<?php echo $web_admin_csat_dashboard;?>">
                                    <span style="color: #fff;font-weight: 600;margin-right:200px;">C-SAT/D-SAT Graph</span>
                              </a> | 
                              <a href="dashboard_index.php?token=<?php echo base64_encode('sentiment'); ?>">
                                    <span style="color: #fff;font-weight: 600;margin-left:200px;">Sentiment Graph</span>
                              </a>
                           </center>
                        </td>
                     </tr>
                     <tr class="totalcomplaint" style="color: #fff;font-weight: 600;">
                                    
                        <td >
                           Highest Call Answer -
                           <span id="longCallAnsName_e"></span>(<span id="longCallAns_e"></span>)
                        </td> 

                        <td>
                           Highest Talk Time -
                           <span id="highestTalkTimeName_e"></span>(<span id="highestTalkTime_e"></span>)
                        </td>

                        <td>
                           Highest Login Time -
                           <span id="highestLoginTimeName_e"></span>(<span id="highestLoginTime_e"></span>)

                        </td>

                        <td colspan="2">Percentage of Answered Call -
                           <span id="percentage_answeredcall_e"></span>

                        </td>
                     </tr>


                  </tbody>
               </table>
            
            <!-- ADD START-->     
            <table width="100%">
               <tbody>
                  <tr class="background3">
                     <td align="center" valign="top">
                     <div class="charts_wrapper">
                        <div class="toggle-header" onclick="toggleContainer('category_container')">
                           <span class="toggle-button" id="toggle-category_container">–</span>
                        </div>
                        <div id="category_container" class="charts_container">
                           <div id="category_e" class="charts_div"></div>
                        </div>
                     </div>
                     </td>
                     <td align="center" valign="top">
                     <div class="charts_wrapper">
                        <div class="toggle-header" onclick="toggleContainer('subcategory_container')">
                           <span class="toggle-button" id="toggle-subcategory_container">–</span>
                        </div>
                        <div id="subcategory_container" class="charts_container">
                           <div id="subcategory_e" class="charts_div"></div>
                        </div>
                     </div>
                     </td>
                  </tr>
                  <tr class="background3">
                     <td align="center" colspan="2">
                     <input type="hidden" id="str_language" value="<?php echo $str_language;?>">
                     <div class="charts_wrapper" style="width: 98%;">
                        <div class="toggle-header" onclick="toggleContainer('language_container')">
                           <span class="toggle-button" id="toggle-language_container">–</span>
                        </div>
                        <div id="language_container" class="charts_container" style="height: 300px;">
                           <div id="language_e" class="charts_div"></div>
                        </div>
                     </div>
                     </td>
                  </tr>
               </tbody>
            </table>
            <!-- ADD END-->
         </div>
      </div>
   </form>
</div>
<script>
function toggleContainer(id) {
  const container = document.getElementById(id);
  const toggleBtn = document.getElementById('toggle-' + id);

  if (container.style.display === 'none') {
    container.style.display = 'flex';
    toggleBtn.textContent = '–';
  } else {
    container.style.display = 'none';
    toggleBtn.textContent = '+';
  }
}
</script>

