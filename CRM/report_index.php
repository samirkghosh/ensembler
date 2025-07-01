<?php
/***
 * Report Index
 * Author: Aarti
 * Date: 16-01-2024
 * Description: This file handles All Report Details
 * handling large amount data using server side datatable 
 * Export pdf,xml,jpg format
 **/

// Including necessary PHP files
include("web_function.php"); // Includes some common functions
include("Report/report_function.php"); // Includes all report functions to fetch report data from the database

?>
<!-- Fix code no need to be changed -->
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                    <?php include("includes/sidebar.php"); ?> <!-- Includes side menu file -->
                </div>
                <div class="col-sm-1" style="padding-left:0">
                    <div class="reports-page-left">
                        <?php include("includes/web_left_nav.php"); ?> <!-- Includes admin master side menu file -->
                    </div>
                </div>
                <div class="col-sm-9 mt-3" style="padding-left:0">
                    <div class="rightpanels"> 
                        <!-- Dynamic inclusion of different report files based on token -->
                        <?php
                        $token = base64_decode($_GET['token']);
                        // Determine which report to include based on the token
                        if($token == 'web_agent'){
                            include_once("Report/web_agent.php");  // Display agent report details
                        } else if($token == 'web_agent_break'){
                            include_once("Report/web_agent_break.php");  
                        } else if($token == 'web_report'){
                            include_once("Report/web_report_new.php"); // Include web report
                        } else if($token == 'web_customer'){
                            include_once("Report/web_customer.php"); // Include web customer report
                        } else if($token == 'report_overview'){
                            include_once("Report/report_overview.php"); // Include report overview
                        } else if($token == 'web_audit_report'){
                            include_once("Report/web_audit_report.php"); // Include web audit report
                        } else if($token == 'web_frequent_caller'){
                            include_once("Report/web_frequent_caller.php"); // Include web frequent caller report
                        } else if($token == 'web_sub_category_report'){
                            include_once("Report/web_sub_category_report.php"); // Include web sub-category report
                        } else if($token == 'web_customer_satis_report_detailed'){
                            include_once("Report/web_customer_satis_report_detailed.php"); // Include detailed web customer satisfaction report
                        } else if($token == 'web_customer_satis_report_summary'){
                            include_once("Report/web_customer_satis_report_summary.php"); // Include summary web customer satisfaction report
                        } else if($token == 'voicemail_report'){
                            include_once("Report/voicemail_report.php"); // Include voicemail report
                        } else if($token == 'fcr_report'){
                            include_once("Report/fcr_report.php"); // Include FCR (First Call Resolution) report
                        } else if($token == 'web_case_report'){
                            include_once("Report/web_case_report.php"); // Include web case report
                        } else if($token == 'customer_effort_report'){
                            include_once("Report/customer_effort_report.php"); // Include customer effort report
                        } else if($token == 'nps_report'){
                            include_once("Report/nps_report.php"); // Include NPS (Net Promoter Score) report
                        } else if($token == 'web_disposition_report'){
                            include_once("Report/web_disposition_report.php"); // Include web disposition report
                        } else {
                           // If none of the conditions match, redirect to logout page
                            echo "<script>window.location.href = '../web_logout.php';</script>";
                            exit; // Stop script execution
                        }
                        ?>
                        <!-- End dynamic inclusion -->
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
          <?php include("includes/web_footer.php"); ?> <!-- Includes web footer -->
        </div>
    </div>
</body>
<script src="<?=$SiteURL?>public/js/report.js"></script> <!-- Includes report.js -->
