<?php
/***
 * Dashborad page
 * Author: Aarti
 * Date: 16-01-2024
 * Description: This file handles the display all chart, graphs, and bar charts.
 * C-SAT/D-SAT Graph
 * Overall Dashboard
 **/
// Including necessary PHP files
include "web_function.php"; // Includes some common functions
include "Dashboard/dashboard_function.php"; // Includes dashbord function 
include_once "Dashboard/functions_dashboard.php";
include_once "../config/web_mysqlconnect.php"; // Include database connection file
?>
<body>
   <div class="wrapper">
      <div class="container-fluid">
        <div class="row">
            <?php 
            // Decoding token from URL parameter
            $token = base64_decode($_GET['token']);
            if ($token != 'new_dashboard' && $token != 'live_wallboard') {
                $style = 'col-sm-10 mt-3';
            ?>
            <div class="col-sm-2" style="padding-left:0">
                <?php include_once('includes/sidebar.php'); ?> <!-- Includes side menu file -->
            </div>
            <?php }else{
                $style = 'col-sm-12';
                include_once('includes/head.php'); 
            }?>
            <!-- Main Content Area -->
            <div class="<?php echo $style;?>" >
                <?php 
                // Including dashboard based on token
                if ($token == 'web_admin_dashboard') {
                    include_once "Dashboard/web_admin_dashboard.php"; // Display dashborad details
                } else if ($token == 'new_dashboard') {
                    include_once "Dashboard/new_dashboard.php";  // Display Overall Dashboard

                } else if($token == 'web_admin_csat_dashboard'){
                    include_once "Dashboard/web_admin_csat_dashboard.php"; // Display C-SAT/D-SAT Graph
                }else if($token == 'live_wallboard'){
                    include_once "Dashboard/live_wallboard.php"; // Display C-SAT/D-SAT Graph
                }else if($token == 'sentiment'){
                    include_once "Dashboard/sentiment.php"; // Display sentiment Graph
                }
                 else {
                    // Default inclusion in case of invalid token
                    include_once "index.php";
                }
                ?>
            </div>
        </div>
    </div>
    <?php
        if ($token != 'new_dashboard' && $token != 'live_wallboard') {
        ?>
            <div class="footer">
        <?php
        include_once 'includes/web_footer.php'; 
        }?>

</div>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
   <!-- Dashboard data -->
   <script src="../public/js/web_dashboard.js"></script> 
   <script type = "text/javascript" >
      $(document).ready(function(){
        var rand_no = Math.floor((3-1)*Math.random()) + 1;
        $("#btnConvert").on('click', function () {
            html2canvas(document.getElementById("SRallview")).then(function (canvas) {                   
                var anchorTag = document.createElement("a");
                document.body.appendChild(anchorTag);
                // document.getElementById("previewImg").appendChild(canvas);
                anchorTag.download = "dashboard_"+rand_no+".jpg";
                anchorTag.href = canvas.toDataURL();
                anchorTag.target = '_blank';
                anchorTag.click();
            });
        });
      });
   </script> 




