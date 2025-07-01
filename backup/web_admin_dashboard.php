<?php
/**
 * Dashboard Page
 * Author: Ritu Modi
 * Date: 18-03-24
 * 
 * This page displays various statistics and charts related to cases and user activities. 
 */
// Encoding the 'new_dashboard' string using base64 encoding
$new_dashboard = base64_encode('new_dashboard');
$web_report = base64_encode('web_report');
$fcr_report = base64_encode('fcr_report');
$web_admin_csat_dashboard = base64_encode('web_admin_csat_dashboard');

// Retrieving user session data
$groupid = $_SESSION['user_group'];
$vuserid = $_SESSION['userid'];
$name = $_SESSION['logged'];
// Check if user session is valid and user group is '0000' (assuming '0000' is a specific user group)
if ($vuserid > 0 && $groupid == '0000') {
    // Check if the 'loggedin_audit' session variable is not set or not equal to 1
    if (!isset($_SESSION['loggedin_audit']) && $_SESSION['loggedin_audit'] != 1) {
        // Final action message when user logs in
        $final_action = "$name User LoggedIn";
        // Add an audit log for successful login
        add_audit_log($vuserid, 'loggedin', 'null', 'login success', $db);
        // Set 'loggedin_audit' session variable to '1' to indicate that audit log has been added
        $_SESSION['loggedin_audit'] = '1';
    }
}
?>
<div class="col-sm-12 mt-3" id="html-content" style="background-color: white !important;">
   <form name="frmagentdashboard" action="" method="post">
      <!-- Dashboard Section -->
      <div class="style2-table">
         <!-- Dashboard Title -->
         <div class="style-title">
            <div class="row">
               <div class="col-sm-6">
                  <h3>Dashboard</h3>
               </div>
               <div class="col-sm-6 d-flex flex-row-reverse bd-highlight">
               <a href="dashboard_index.php?token=<?php echo $new_dashboard;?>">Overall Dashboard</a>
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
               <div class="col-sm-12">
                  <!-- Date Range Selection -->
                  From : <input type="text" name="startdatetime" class="date_class dob1"  value="<?=$startdate?>" id="startdatetime" autocomplete="off">&nbsp;&nbsp;
                          To : <input type="text" name="enddatetime" class="date_class dob1"  value="<?=$enddate?>" id="enddatetime"  autocomplete="off">
                  <input type='submit' name='sub1' value='GO' class="btn btn-danger" style="font-size:small">
                  <input type="button" value="JPG Convert" id="btnConvert" class="btn btn-success" style="font-size:small">
               </div>
            </div>
         </div>
         <!-- Table Section -->
         <div class="table" id="SRallview">
         <?php
         // Get start datetime from form submission or default to the first day of the current month
            $startdatetime = isset($_POST['startdatetime']) ? date("Y-m-d 00:00:00",strtotime($_POST['startdatetime'])) : date("Y-m-01 00:00:00");
            // Get end datetime from form submission or default to current date and time
            $enddatetime = isset($_POST['enddatetime']) ? date("Y-m-d H:i:s",strtotime($_POST['enddatetime'])) : date("Y-m-d H:i:s");
            // Prepare query string for filtering cases by creation date
            $query_str = " and (d_createDate between '$startdatetime' and '$enddatetime')";
            // Prepare query string for filtering cases by email date (assuming email date field is present)
            $query_str2 = " and (d_email_date between '$startdatetime' and '$enddatetime')";               
            $cases_cnt = getTotalCases($startdatetime, $enddatetime);// Get total number of cases within the specified date range
            $ct_cnt = getComplaintCount( $startdatetime, $enddatetime);// Get count of complaints within the specified date range
            $re_cnt = getInquiryCount($query_str);// Get count of inquiries within the specified date range
            $oth_cnt = getOtherCount( $query_str);// Get count of other types of cases within the specified date range
            $men_cnt = getMentionCount( $query_str);// Get count of mentioned cases within the specified date range
            $pending_cnt = getPendingCount($query_str);// Get count of pending cases within the specified date range
            $resolved_cnt = getInProgressCount($query_str);// Get count of cases in progress within the specified date range
            $closed_cnt = getClosedCount($query_str);// Get count of closed cases within the specified date range
            $assigned_cnt = getAssignedCount($query_str);// Get count of assigned cases within the specified date range
            $escalated_cnt = getEscalatedCount($query_str);// Get count of escalated cases within the specified date range
            $voice_mail = getVoiceMailCount($startdatetime, $enddatetime);// Get count of voicemail messages within the specified date range
            // Get rate of resolution for complaints within the specified date range
            $resolution_rate= getRateOfResolution($startdatetime,$enddatetime);
            $fcr_cnt = getFCRCount($query_str); // Get count of First Call Resolution (FCR) cases within the specified date range
            $per_fcr = ($fcr_cnt/$cases_cnt['total'])*100;// Calculate percentage of First Call Resolution (FCR) cases
            // Initialize variables for highest call answered, highest talk time, and highest login time
            $highestCallAnswered = 0;
            $highestTalkTime = 0;
            $highestLoginTime = 0;
            $longCallAns=0;
            $longCallAnsName="";
            $query = executeRecordingLogQuery($link, $startdatetime, $enddatetime);// Execute recording log query
            $campCond = " AND campaign_id='BLENDEDensembler'";// Campaign condition
            // Loop through query results
            while ($row = mysqli_fetch_assoc($query)) {
               // Loop through all dates
               $no++;
                //LOOP THROUGH ALL DATES START
               $TotalLoginTime = 0;
               $userLoginTime = "";
               $userLogOutTime = "";
               $startTime = strtotime($startdatetime);
               $endTime = strtotime($enddatetime);
               for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
                  $thisDate = date('Y-m-d', $i);
                   //get first Login time start
                   $LogIn1 = getUserLogInTime($link, $campCond, $row['user'], $thisDate);
                   //get first Logout time start
                   $LogOut1 = getUserLogOutTime($link, $campCond, $row['user'], $thisDate);
                   //get first Logout time end
                    $TotalLoginTime = ($LogOut1 - $LogIn1) + $TotalLoginTime;
               }
                  // Get total login time for the current user within the specified date range
                  $fetch_total_login = getTotalLoginTime($link, $startdatetime, $enddatetime, $row['user']);
                  // Get attempted ADR (Automatic Dialer Reattempt) count for the current user within the specified date range
                  $query02Row1 = getADRAttemptedCount($link, $startdatetime, $enddatetime, $row['user']);
                  // Get total talk time for the current user within the specified date range
                  $query03Row = getTotalTalkTime($link, $startdatetime, $enddatetime, $row['user']);
                  // Get offered count for the current user within the specified date range
                  $num_userOffered = getUserOfferedCount($link, $startdatetime, $enddatetime, $row['user']);
                  // Update highest call answered count
                  $highestCallAnswered = $query02Row1['totalADRAttempted'];
                  // Update total call offered and total call answered counts
                  $totalcalloffered = $totalcalloffered + $num_userOffered;
                  $totalcallanswered = $totalcallanswered + $highestCallAnswered;
                  // Update highest talk time and corresponding user name if applicable
                  if ($highestTalkTime < $query03Row['totalTalkTime']) {
                     $highestTalkTime = $query03Row['totalTalkTime'];
                     $highestTalkTimeName = $row['full_name'];
                  }
                  // Update highest login time and corresponding user name if applicable
                  if ($highestLoginTime < $fetch1['totalLogin']) {
                     $highestLoginTime = $fetch1['totalLogin'];
                     $highestLoginTimeName = $row['full_name'];
                  }
                  // Update longest call answered count and corresponding user name if applicable
                  if ( $highestCallAnswered > $longCallAns)
                  {
                     $longCallAns = $highestCallAnswered;
                     $longCallAnsName = $row['full_name'];
                  }
               }                
         ?>
         <table class="tableview tableview-2">
            <tbody>
               <tr class="background5">
                  <th align="center" class="totalcomplaint" colspan="">
                     <center>Total Cases<span><a href="report_index.php?token=<?php echo $web_report;?>"><?=$cases_cnt['total']?></a></span></center>
                  </th>
                  <th align="center" class="totalcomplaint" colspan="">
                     <center>Complaint Cases<span><a href="report_index.php?token=<?php echo $web_report;?>&casetype=Complaint"><?=$ct_cnt?></a></span> </center>
                  </th>
                  <th align="center" class="totalcomplaint" colspan="">
                     <center>Inquiries Cases<span><a href="report_index.php?token=<?php echo $web_report;?>&casetype=Inquiry"><?=$re_cnt?></a></span> </center>
                  </th>
                  <th align="center" class="totalcomplaint" colspan="2">
                     <center>Others Cases<span><a href="report_index.php?token=<?php echo $web_report;?>&casetype=Others"><?=$oth_cnt?></a></span> </center>
                  </th>
               </tr>
               <tr>
                  <td colspan="5" class="totalcomplaint"><center><span style="color: #fff;font-weight: 600;">First Contact Resolution - <a style="color: #fff;" href="report_index.php?token=<?php echo $fcr_report;?>"><?=round($per_fcr,2)?>%</a></center></span></td>
               </tr>
               <tr class="background5">
                  <th align="center" style="background:#C41E3A;" colspan="">
                     <center>Pending<span><a href="report_index.php?token=<?php echo $web_report;?>&status=1"><?=$pending_cnt['total']?></span> </a></center>
                  </th>
                  <th align="center" style="background:#F28C28;"  colspan="">
                     <center>In Progress<span><a href="report_index.php?token=<?php echo $web_report;?>&status=8"><?=$resolved_cnt['total']?></span></a></center>
                  </th>
                  <th align="center" style="background:#228B22;"  colspan="">
                     <center>Closed<span><a href="report_index.php?token=<?php echo $web_report;?>&status=3"><?=$closed_cnt['total']?></span></a></center>
                  </th>
                  <th align="center" style="background:#00293c;" colspan="">
                     <center>Assigned<span><a href="report_index.php?token=<?php echo $web_report;?>&status=1&casetype=Complaint"><?=$assigned_cnt['total']?></span></a></center>
                  </th>
                  <th align="center" style="background:#cac531;">
                     <center>Escalated<span><a href="report_index.php?token=<?php echo $web_report;?>&status=4"><?=$escalated_cnt['total']?></span> </center>
                  </th> 
               </tr>
               <tr>
                  <td colspan="5" class="totalcomplaint"><center><a href="dashboard_index.php?token=<?php echo $web_admin_csat_dashboard;?>"><span style="color: #fff;font-weight: 600;">C-SAT/D-SAT Graph</center></span></a></td>
               </tr>

               <tr class="totalcomplaint" style="color: #fff;font-weight: 600;">  
                  <td>
                    Highest Call Answer -
                    <?=$longCallAnsName?> (<?=($longCallAns)?>)
                  </td> 
                  <td>
                    Highest Talk Time -
                    <?= $highestTalkTimeName ." (".getTimeInFormated($highestTalkTime). ")"?>
                  </td>
                  <td>
                    Highest Login Time -
                    <?= $highestLoginTimeName ." (".getTimeInFormated($highestLoginTime). ")"?>
                  </td>
                  <td colspan="2">Percentage of Answered Call -            
                     <?= round(($totalcallanswered / $totalcalloffered) * 100, 2)?>%
                  </td>
               </tr>
            </tbody>
         </table>
      </div>
      <!-- ADD START-->        
         <table width="100%">
            <tbody>
               <tr class="background3">
                  <td align="center" >
                     <?php
                     // Get language count for the specified date range
                        $language = getLanguageCount($link, $db, $startdatetime, $enddatetime);
                        $num_complaint3=mysqli_num_rows($language);// Count the number of language categories retrieved
                        $str_language=""; // Initialize an empty string to store the language data for chart rendering
                        if($num_complaint3 > 0 ): // Check if there are language categories available
                       	$str_language.="	{
                       		type: 'column',
                       		showInLegend: false,
                       		cursor: 'pointer',
                       		bevelEnabled: true,
                       		legend: {
                       				verticalAlign: 'bottom',
                       				horizontalAlign: 'center'
                       			},
                       			theme: 'theme1',
                       		dataPoints: [ ";
                           // Loop through each language category and retrieve its details
                           while($res = mysqli_fetch_array($language)):
                       			$cnt1++;
                              // Retrieve the name and count of the language category
                             $language_name=getlanguagename($res['language_id']);
                             $language_cnt=$res['language_cnt'];
                       		  $str_language .='{y: '.$language_cnt.', label: "'.$language_name.'", indexLabel: "'.$language_cnt.'" },';
                       	   endwhile;
                       	   $str_language = substr($str_language, 0,-1);
                          	$str_language.="	]
                       			},";                 
                           else:
                              // If no language categories are available, provide a default data point
                       	   $str_language ='{y: "1", label: "Complaint category" , indexLabel: "Category "},';
                           endif; 
                           // Remove the trailing comma from the last data point if applicable
                           $str_language = substr($str_language, 0,-1);
                     ?>
                     <input type="hidden" id="str_language" value="<?php echo $str_language;?>">
                     <div id="language_container" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
                  <td align="center">
                  <?php
                     // Retrieve sub-category counts using the provided query string
                     $qq = getSubCategoryCount($link, $db, $query_str);
                     // Retrieve sub-category counts using a different query string
                     $qq2 = getSubCategoryCount1($link, $db, $query_str);
                     // Count the number of sub-categories retrieved
              			$num_cat=mysqli_num_rows($qq);
                     // Initialize variables for storing sub-category data and total count
              			$str_category=""; $total_category = 0;
                     // Check if there are sub-categories available
              			if($num_cat > 0 ):
                        // Loop through the results to calculate the total count of sub-categories
                        while($resss = mysqli_fetch_array($qq2)):
                           $cnt2 += $resss['cnt'];
                           endwhile;
                           // Loop through each sub-category and retrieve its details
                       	while($res = mysqli_fetch_array($qq)):
                    			$vSubCategory = $res['vSubCategory'];
                           $cat_from_subcat = cat_from_subcat($vSubCategory);
                           $color_subcat = color($cat_from_subcat);	
                       		$SubcatgeoryName=subcategory($vSubCategory);
                           $cnt = $res['cnt'];
                           $subcatgeory_cnt_per=($cnt/$cnt2*100);
                       		$str_category .='{y: '.$cnt.', label: "'.($SubcatgeoryName).'" , indexLabel: "'.$SubcatgeoryName.'-'.$cnt.'('.round($subcatgeory_cnt_per,2).')%", color:"'.$color_subcat.'",indexLabelFontSize: 13,indexLabelFontWeight: "bold"},';
                       		endwhile;     
                        else:
                           // If no sub-categories are available, provide a default data point
                           $str_category ='{y: "1", label: "Sub Category-wise" , indexLabel: "Sub Category-wise ( 0 ) "},';
                        endif;	
                        // Remove the trailing comma from the last data point if applicable
                       		$str_category = substr($str_category, 0,-1);
                  ?>
                  <div id="chartContainer_category" class="charts_div" style="width:100%; height:350px; float:left;"></div>
                  </td>
               </tr>
               <tr class="background3">
                  <td align="center" colspan="2">
                  <?php
                  // Construct SQL query to retrieve category counts based on the provided query string 
                  $cat_sql = "select   vCategory, COUNT( * ) as catgeory_cnt  from $db.web_problemdefination where vCustomerID !='' $query_str group by vCategory ";
                  $qq = mysqli_query($link,$cat_sql);
                  // $qq = getCategoryCounts($query_str);
                  // Construct SQL query to retrieve total category counts based on the provided query string
                  $cat_sql2 = "select COUNT( * ) as catgeory_cnt  from $db.web_problemdefination where vCustomerID !='' $query_str group by vCategory ";
                  $qq22 = mysqli_query($link,$cat_sql2);
                  $num_complaint=mysqli_num_rows($qq); // Count the number of categories retrieved          
                  $str_complaint_cat=""; $total_complaint_cat = 0;
                  // Check if there are categories available
                  if($num_complaint > 0 ):
                     // Loop through the results to calculate the total count of categories
                     while($resss2 = mysqli_fetch_array($qq22)):
                        $cnt22 += $resss2['catgeory_cnt'];
                     endwhile;     
                     // Start constructing the data points for the chart                     
                     $str_complaint_cat.="   {
                        type: 'column',
                        showInLegend: false,
                        cursor: 'pointer',
                        bevelEnabled: true,
                        legend: {
                           verticalAlign: 'bottom',
                           horizontalAlign: 'center'
                          },
                        theme: 'theme1',
                        dataPoints: [ ";
                            // Loop through each category and retrieve its details
                        while($res = mysqli_fetch_array($qq)):
                           $cnt1++;
                           $vCategory=$res['vCategory'];
                           $catgeory_cnt=$res['catgeory_cnt'];
                           $catgeory_cnt_per=($catgeory_cnt/$cnt22*100);
                           $color = color($res['vCategory']);
                           $str_complaint_cat .='{y: '.$catgeory_cnt.', label: "'.category($vCategory).'", indexLabel: "'.$catgeory_cnt.'-('.round($catgeory_cnt_per,2).'%)", color: "'.$color.'" },';
                        endwhile;
                        $str_complaint_cat = substr($str_complaint_cat, 0,-1);
                        $str_complaint_cat.="   ]
                        },";
                        else:
                        $str_complaint_cat ='{y: "1", label: "Complaint category" , indexLabel: "Category "},';
                        endif; 
                        // Append the category data to the string
                        $str_complaint_cat = substr($str_complaint_cat, 0,-1);
                        ?>
                     <div id="chartContainer_Complaint" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
               </tr>
            </tbody>
         </table> 
         <!-- ADD END-->
      </div>
   </form>
</div>

