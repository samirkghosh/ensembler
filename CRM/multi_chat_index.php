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
                if ($token == 'multi_chat') {//multichat file [vastvikta][30-12-2024]
                   include_once("multi_chat/web_chatting.php");
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