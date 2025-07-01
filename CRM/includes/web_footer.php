<?php
/**
 * Footer Page
 * Author: Aarti Ojha
 * Date: 16-01-2024
 * Description: This file handles the Footer bar and includes some common JavaScript code.
 */
include_once("../function/classify_function.php");
// Check if the 'IM' module is licensed
$module_flag = module_license('IM');
if($module_flag == '1'){
  include_once("IMApp/index.php");
}/****end***/
if(!isset($_SESSION['VD_login'])){
?>
<!-- End of conditional flow -->
<style type="text/css">
.holder { 
    position: relative;
    overflow: hidden;
    margin: 0;
    margin-left: 36px;
    font-size: 15px;
    height: 22px;
    padding: -4px;
    margin-top: 7px;
    margin-bottom: -7px;
}
.holder > p { 
    position: absolute;
    -webkit-transition: all 5s;
    transition: all 4s;
    padding-left: -65px;
    color: #efffff;
    font-family: fangso;
    font-weight: 500;
}
.left   { left: -100%; }
.right  { left: 100%; }
.middle { left: 0; }
.holder p:before {
    content: "\201C";
    font-size: 3em;
    line-height: 0.1em;
    margin-right: 0.1em;
    vertical-align: -0.4em;
}
.holder p:after {
    content: "\201D";
    font-size: 3em;
    line-height: 0.1em;
    margin-left: 0.1em;
    vertical-align: -0.45em;
}
.footer-wrap {
    padding-top: 8px !Important;
}
</style>

<?php }?>
<div class="holder">
</div>
<div class="footer-wrap">
   <div class="row">
      <div class="col-sm-12">
            <img class="vision2" src="<?=$SiteURL?>public/images/ensembler-logo.png" style="width: 106px;
            height: 37px;background:#fff;margin-top: -11px;">
         <strong style="color:#adb1b9">Copyright &copy; <?=date('Y')?>-<?=date('Y',strtotime('+1 year'))?> <a href="#" style="text-decoration:none;<?=($dbtheme=='yoda')?'color:#222':'color:#fff'?>"><?=strtoupper($_SESSION['companyName'])?></a>.</strong>
         All rights reserved.
      </div>
   </div>
</div>

<!-- jquery files  -->
<script type="text/javascript" src="<?=$SiteURL?>public/js/chart.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery.validate.min.js"></script>
<!-- <script src="<?=$SiteURL?>public/js/common.js"></script> -->
<script src="<?=$SiteURL?>public/js/form-validation.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery.smartmenus.js"></script>
<script type="text/javascript">
    $(function() {
        $('#main-menu').smartmenus({
            mainMenuSubOffsetX: -1,
            subMenusSubOffsetX: 10,
            subMenusSubOffsetY: 0
        });
    });
</script>
<script src="<?=$SiteURL?>public/js/jquery.colorbox.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        //Examples of how to assign the Colorbox event to elements
        $(".ico-setting").colorbox({
            iframe: true,
            innerWidth: 410,
            innerHeight: 200
        });
        $(".ico-interaction").colorbox({
            iframe: true,
            innerWidth: 800,
            innerHeight: 600
        });
        $(".ico-interaction2").colorbox({
            iframe: true,
            innerWidth: 800,
            innerHeight: 600
        });
        $(".ico-display").colorbox({
            iframe: true,
            innerWidth: 800,
            innerHeight: 65
        });
        $(".supportsection").colorbox({
            iframe: true,
            innerWidth: 550,
            innerHeight: 400
        });
        $(".newdocument").colorbox({
            iframe: true,
            innerWidth: 550,
            innerHeight: 390
        });
        $(".group3").colorbox({
            rel: 'group3',
            transition: "none",
            width: "75%",
            height: "75%"
        });
        $(".group4").colorbox({
            rel: 'group4',
            slideshow: true
        });
        $(".ajax").colorbox();
        $(".form-ele").colorbox({
            iframe: true,
            innerWidth: 250,
            innerHeight: 390
        });
        $(".vimeo").colorbox({
            iframe: true,
            innerWidth: 500,
            innerHeight: 409
        });
        $(".iframe").colorbox({
            iframe: true,
            width: "80%",
            height: "80%"
        });
        $(".inline").colorbox({
            inline: true,
            width: "450"
        });
        $(".pop-all").colorbox({
            iframe: true,
            width: "50%",
            height: "70%"
        });
        $(".callbacks").colorbox({
            onOpen: function() {
                alert('onOpen: colorbox is about to open');
            },
            onLoad: function() {
                alert('onLoad: colorbox has started to load the targeted content');
            },
            onComplete: function() {
                alert('onComplete: colorbox has displayed the loaded content');
            },
            onCleanup: function() {
                alert('onCleanup: colorbox has begun the close process');
            },
            onClosed: function() {
                alert('onClosed: colorbox has completely closed');
            }
        });

        $('.non-retina').colorbox({
            rel: 'group5',
            transition: 'none'
        })
        $('.retina').colorbox({
            rel: 'group5',
            transition: 'none',
            retinaImage: true,
            retinaUrl: true
        });
        //Example of preserving a JavaScript event for inline calls.
        $("#click").click(function() {
            $('#click').css({
                "background-color": "#f00",
                "color": "#fff",
                "cursor": "inherit"
            }).text("Open this window again and this message will still be here.");
            return false;
        });
    });
</script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/jquery-ui.min2.js"></script>
<!-- <script type="text/javascript" src="templates/js/jquery-ui-timepicker-addon2.js"></script> -->


<!-- <link href="templates/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print" /> -->
<script src="<?=$SiteURL?>public/js/moment.min.js"></script>
<!-- <script src="templates/fullcalendar/fullcalendar.min.js"></script> -->
<!-- <script type="text/javascript" src="templates/js/jquery.datepick.js"></script> -->
<script src="<?=$SiteURL?>public/datatables/js/dataTables.js"></script>
<script src="<?=$SiteURL?>public/js/jquery.datetimepicker.js"></script>
<!-- social notification and bulletin related code in this file -->
<script src="<?=$SiteURL?>/public/js/app.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/nprogress.js" ></script>
<script src="<?=$SiteURL?>/public/js/dropify.min.js"></script>


<script type="text/javascript" src="<?=$SiteURL?>public/bootstrap/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/jszip.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/pdfmake.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/vfs_fonts.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/buttons.colVis.min.js" ></script>
<script type="text/javascript" src="<?=$SiteURL?>public/datatables/js/dataTables.responsive.min.js" ></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/ss.custom.js" ></script>
<script src="<?=$SiteURL?>/public/js/notificaion_script.js"></script>
<!-- <script src="<?=$SiteURL?>/public/js/broadcast.js"></script> -->

<script type="text/javascript">
    $('.date_class').datetimepicker({
        format : 'd-m-Y H:i:s',
        formatTime:'H:i:s',
        formatDate:'d-m-Y'

    });
       // DOMContentLoaded  end
   setInterval(function () {
        $(".holder > p:first")
            .removeClass("middle, right")
            .next()
            .addClass("middle").removeClass("right")
            .end()
                      
            .addClass("right").removeClass("middle").appendTo(".holder");
    }, 6000); 
    fetching_bulletin();
   inverval_timer = setInterval(function() { 
        fetching_bulletin();
    },30000);

 function fetching_bulletin(){
        var Data = {}
        Data.action = 'fetching_bulletin';
        var url = "common_function.php";
        $.ajax({
            url: url,
            type: "POST",
            data:  Data,
            dataType:"JSON",
            success: function(data){
             console.log('fetching_message_list');
             $('.holder').html(data);
            }
        });
 }
</script>

<!-- Only for CRM login code for logout after 10 min inactivity [Aarti][11-04-2025] -->
<?php 
if(!isset($_SESSION['VD_login'])){?>
    <script type="text/javascript">
        $(document).ready(function(){
            var groupid = "<?php echo $groupid; ?>";
            let inactivityTimer; // Timer for changing status
            let logoutTimer;     // Timer for logging out
            let isAway = false;  // Flag to track if the status is currently "Away"

            // Function to reset the inactivity and logout timers
            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                clearTimeout(logoutTimer); // Also reset logout timer

                if (isAway) {
                    updateStatus('Available', 'available-icon', 'online');
                    isAway = false;
                }

                startInactivityTimer(); // Restart both timers
                // startLogoutTimer();
            }

            // Function to start the inactivity timer (for status change)
            function startInactivityTimer() {
                inactivityTimer = setTimeout(function() {
                    updateStatus('Appears Away', 'away-icon', 'offline');
                    isAway = true;
                }, 10 * 60 * 1000); // 10 minutes
            }

            // ✅ Function to start the logout timer
            // added code for logout in case of supervisor and backofficer login [vastvikta][07-04-2025]

            // function startLogoutTimer() {
            //     logoutTimer = setTimeout(function() {
            //         window.location.href = "../web_login.php?flage1=1"; // Redirect to actual logout page
            //     }, 10 * 60 * 1000); // 10 minutes
            // }
            
            // Start both timers when the page loads
            startInactivityTimer();
            if (groupid === "060000" || groupid === "080000") {
                startLogoutTimer();
            }

            // Reset timers on any user activity
            $(document).on('mousemove keydown click', function() {
                resetInactivityTimer();
            });
        });
    </script>
<?php } ?>


<!-- This code handles updating the user’s status (e.g., Available, Away) in the database. -->
<!-- Aarti ojha [07-11-2024] -->
<script type="text/javascript">
    $(document).ready(function(){
        // Show or hide dropdown on clicking the current status
        $('#current-status').click(function(e) {
            e.stopPropagation();
            $('#status-dropdown').toggle();
        });

        // Function to update the status display and send it to the backend
        function updateStatus(newStatus, newStatusClass,newStatusId) {
            console.log('updateStatus');
            console.log(newStatusClass);
            console.log(newStatus);
            // Update displayed status text and icon color
            $('#current-status').html(`<span id="current-status-icon" class="status-icon ${newStatus}"></span> ${newStatus}`);
            
            if(newStatus === 'Available'){
                $('#status-icon').addClass('status-available');
                $('#status-icon_IM').addClass('status-available');
                $('#status-icon').removeClass('status-away');
                $('#status-icon_IM').removeClass('status-away');
                $('#IM_statustext').text('online');
            }else{
                $('#status-icon').addClass('status-away');
                $('#status-icon').removeClass('status-available');
                 $('#status-icon_IM').addClass('status-away');
                $('#status-icon_IM').removeClass('status-available');

                
                $('#IM_statustext').text('offline');
            }
            // Send status update to backend
            const userId = $('#userId').val();
            $.post('update_status.php', { user_id: userId, status: newStatusId, action: 'change_status' }, function(response) {
                if (!response.success) {
                    // alert('Failed to update status');
                }
            }, 'json');
        }

        // Update status on selecting an option from the dropdown
        $('.status-option').click(function() {
            const newStatus = $(this).data('status');
            const newStatusId = $(this).data('statusid');
            const newStatusClass = $(this).find('.status-icon').attr('class').split(' ')[1]; // Get color class
            // Update the status and close dropdown
            updateStatus(newStatus,newStatusClass,newStatusId);
            $('#status-dropdown').hide();
        });

        // Close dropdown if clicked outside
        $(document).click(function(e) {
            if (!$(e.target).closest('#status-dropdown, #current-status').length) {
                $('#status-dropdown').hide();
            }
        });
    });

</script>