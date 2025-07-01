<?php
/**
* Auth: Ritu modi 
* Date: 07/02/2024
* this file is  for Voicemail Report
*/ 
   include_once ("../config/web_mysqlconnect.php"); // Include database connection file
   include("web_function.php");
   include_once ("VoiceMail/VoiceMail_function.php");
   $name= $_SESSION['logged'];
   $page = $_POST['page'];
   $vuserid = $_SESSION['userid'];

?>
<body>
   <div class="wrapper">
      <div class="container-fluid">
        <div class="row">
            <?php 
            // Decoding token from URL parameter
            $token = base64_decode($_GET['token']);
            ?>
            <div class="col-sm-2" style="padding-left:0">
                <?php 
                    include_once('includes/sidebar.php');         
                ?>
            </div>
            <!-- Main Content Area -->
            <div class="col-sm-10 mt-3" style="padding-left:0">
                <?php 
                // Including voicemail based on token
                if ($token == 'VoiceMail_report') {
                    include_once("VoiceMail/VoiceMail_report.php");
                }else if($token == 'misscall_report'){  // If the token is 'misscall_report', include the corresponding PHP file
                            include_once("VoiceMail/misscall_report.php");
                } else {
                       // If none of the conditions match, redirect to logout page
                        echo "<script>window.location.href = '../web_logout.php';</script>";
                        exit; // Stop script execution
                }
                ?>
            </div>
        </div>
    </div>
    <div class="footer">
         <?php include_once("includes/web_footer.php"); ?>
    </div>
</div>
</body>




