<?php
/***
 * Auth: Vastvikta Nishad 
 * Date: 04/03/2024
 * This file is  for Omnichannel to handle the request of loading the  page on the basis of the request from side bar omni channel menu 
 * 
 */
include_once("../../config/web_mysqlconnect.php");
?>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                   <?php include("includes/sidebar.php"); ?> <!-- Side menu file include -->
                </div>
                <?php
                // Decode the token from the GET parameter
                $token = base64_decode($_GET['token']);
                // Check the decoded token and include the corresponding file               
                if($token == 'web_bulksms'){ // Bulk Campaign
                    include_once("omnichannel_config/web_bulksms.php");
                    
                }else if($token == 'bad_report'){ // Bulk Campaign
                    include_once("omnichannel_config/bad_report.php");

                }else if($token == 'bulk_email_report'){ // Bulk Campaign
                    include_once("omnichannel_config/bulk_email_report.php");

                }else if($token == 'email_queue_report'){ // Bulk Campaign
                    include_once("omnichannel_config/email_queue_report.php");

                }else if($token == 'bulk_sms_report'){ // Bulk Campaign
                    include_once("omnichannel_config/bulk_sms_report.php");

                }else if($token == 'queue_report'){ // Bulk Campaign
                    include_once("omnichannel_config/queue_report.php");
                }else if($token == 'bad_report'){ // Bulk Campaign
                    include_once("omnichannel_config/bad_report.php");
                }else if($token == 'bulk_whatsapp_report'){ // Bulk Campaign
                    include_once("omnichannel_config/bulk_whatsapp_report.php");
                }else if($token == 'bulk_twiter_report'){ // Bulk Campaign //twiter report path added [ritu][24-07-2024]
                    include_once("omnichannel_config/bulk_twiter_report.php");
                }else {
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
    <!-- Bootstrap 4 -->
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/bootstrap/js/bootstrap.min.js" ></script>
<script type="text/javascript" src="<?=$SiteURL?>public/bootstrap/js/bootstrap.js" ></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/select2.min.js" ></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/bulk_script.js" ></script>