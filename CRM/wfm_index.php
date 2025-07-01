<?php
/***
 * AUTH: Vastvikta Nishad
 * Date: 16-01-2024
 * this file use for fetch data and insert data in database
 * 
*/

include("../../config/web_mysqlconnect.php");
?>

<link rel="stylesheet" href="WFM/css/agent_styles.css">
<link rel="stylesheet" href="WFM/css/calender-time.css">
<link rel="stylesheet" href="WFM/css/colorbox.css">
<link rel="stylesheet" href="WFM/css/erlang_style.css">
<link rel="stylesheet" href="WFM/css/slicknav.css">
<link rel="stylesheet" href="WFM/css/slicknav.css">
<link rel="stylesheet" href="../public/css/facebook.css">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height: 90vh">
                <div class="col-sm-2" style="padding-left: 0">
                    <?php include("includes/sidebar.php"); ?> <!-- Side menu file include -->
                    <?php include("WFM/function.php"); ?>
                </div>
                <div class="col-sm-10 mt-3" style="padding-left: 0">
                    <div class="rightpanels">
                        <?php
                        $token = base64_decode($_GET['token']);
                   
                        if($token == 'real_time_adherence'){
                            include_once("WFM/real_time_adherence.php");
                        }else if($token == 'agents_shifts'){
                            include_once("WFM/agents_shifts.php");
                        }else if($token == 'agent_monthly_event'){
                            include_once("WFM/wfm_agent_monthly_event_detail.php");
                        }else if($token == 'adherence_report'){
                            include_once("WFM/wfm_adherence_report.php");
                        }else if($token == 'web_erlang_index'){
                            include_once("WFM/web_erlang_cal.php");
                        }else if($token == 'forecast_accuracy'){
                            include_once("WFM/forecast_accuracy.php");
                        }
                        else{
                           // If none of the conditions match, redirect to logout page
                            echo "<script>window.location.href = '../web_logout.php';</script>";
                            exit; // Stop script execution
                        }
                        ?>
                    </div>
                </div>
                    
                <div class="footer">
                    <?php include("includes/web_footer.php"); ?>
                </div>
            </div>
                    </div>

    <script src="<?=$SiteURL?>public/js/wfm_sidebar.js"></script>

    <script src="<?=$SiteURL?>public/js/wfm_master.js"></script>
    </div>