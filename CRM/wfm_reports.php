<?php
/***
 * Auth: Vastvikta Nishad 
 * Date: 04/03/2024
 * This file is  for wfm to handle the request of loading the  page on the basis of the request from side barwfm
 * 
 */?>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                    <?php include("includes/sidebar.php"); ?> <!-- Includes side menu file -->
                </div>
                <div class="col-sm-1" style="padding-left:0">
                    <div class="reports-page-left">
                        <?php include("WFM/wfm_left_nav.php"); ?> <!-- Includes admin master side menu file -->
                    </div>
                </div>
                <div class="col-sm-9 mt-3" style="padding-left:0">
                    <div class="rightpanels">
                <?php
                        $token = base64_decode($_GET['token']);
                      
                        if($token == 'shift_assignment_report'){
                            include_once("WFM/report/web_shift_assignment_report.php");  // Display agent report details
                        }else if($token == 'agentwise_assignment_report'){
                            include_once("WFM/report/web_agentwise_assignment_report.php");
                        }else if($token == 'schedule_adherence_report'){
                            include_once("WFM/report/web_schedule_adherence_report.php");
                        }else if($token == 'agentwise_assignment_report_hist'){
                            include_once("WFM/report/web_agentwise_assignment_report_hist.php");
                        }else if($token == 'schedule_adherence_report_hist'){
                            include_once("WFM/report/web_schedule_adherence_report_hist.php");
                        }else if($token == 'shift_assignment_report_hist'){
                            include_once("WFM/report/web_shift_assignment_report_hist.php");
                        }else{
                            // If none of the conditions match, redirect to logout page
                             echo "<script>window.location.href = '../web_logout.php';</script>";
                             exit; // Stop script execution
                         }
                        ?>
                         </div>
                </div>
               
            </div>
        </div>
        <div class="footer">
          <?php include("includes/web_footer.php"); ?> <!-- Includes web footer -->
        </div>
    </div>
    <script src="../public/js/wfm_sidebar.js"></script>
</body>