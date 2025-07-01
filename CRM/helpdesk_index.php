<?php
/***
 * helpdesk page
 * Author: Aarti
 * Date: 19-01-2024
 * Description: This file handle for case create page and case details page related code 
 **/
include "web_function.php"; // for common function files
?>
<style>
  /* Default title bar color */
  meta[name="theme-color"] {
      content: #ffffff;
  }
</style>
<!-- start html code -->
<div class="wrapper">
    <div class="container-fluid">
        <div class="row" style="min-height:90vh">
            <div class="col-sm-2" style="padding-left:0">
              <?php include("includes/sidebar.php"); ?> 
              <!-- Side menu file include -->
            </div>
            <?php $token = base64_decode($_GET['token']);
            if($token == 'web_helpdesk'){?>
            <div class="col-sm-2">
               <div class="breadcrumb_head mt-3" style="height:81px;margin-bottom:9px;">
                <form action="" method="post">
                     <label>Cases :</label>
                     <br>
                     <?php if (isset($_POST['search_ticket']) && $_POST['search_ticket'] != '') { ?>
                        <input name="search_ticket" id="search_ticket" type="text" class="input-style1" value="<?= $_POST['search_ticket'] ?>" onfocus="clearText(this)" onblur="clearText(this)" style="/*border: #0e0e0e47 1px solid;*/color: #4a4a4a;min-height: 26px;/*padding: 0 3px 0 5px;*/font-size: 10px;">
                     <?php } else { ?>
                        <input name="search_ticket" id="search_ticket" type="text" class="input-style1" value="" placeholder="Enter Name / Case Id" onfocus="clearText(this)" onblur="clearText(this)" style="/*border: #0e0e0e47 1px solid;*/color: #4a4a4a;min-height: 26px;/*padding: 0 3px 0 5px;*/font-size: 10px;">
                     <?php } ?>
                 <input id="ticket_search" type="button" value="Go" class="button-search" style="padding: 2px;">
                </form>
               </div>
               <div class="recentitem-bar-panel">
                  <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Recent 5 Cases</span>
                  <?php include_once "helpdesk/web_recent_complaints.php"; ?>
               </div>
            </div>
            <?php }
            if($token == 'web_helpdesk'){
              $class = 'col-sm-8 mt-3';
            }else{
              $class = 'col-sm-10 mt-3';
            }?>
            <div class="<?php echo $class;?>" style="padding-left:0">
                <div class="rightpanels"> 
                    <!-- this code change our requirement -->
                    <?php
                      if($token == 'web_helpdesk'){
                        include_once "helpdesk/web_helpdesk_home.php";
                      }else if($token == 'web_case_detail'){
                        include_once "helpdesk/case_detail_backoffice.php";
                      }else if($token == 'new_case_manual'){
                        include_once "helpdesk/new_case_manual.php";
                      }else if($token == 'case_detail_backoffice_c2c'){
                        include_once "helpdesk/case_detail_backoffice_c2c.php";
                      }else{
                          // If none of the conditions match, redirect to logout page
                          echo "<script>window.location.href = '../web_logout.php';</script>";
                          exit; // Stop script execution
                      }
                    ?>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
      <? include "includes/web_footer.php"; ?>
    </div>
</div>
<!-- JAVASCRIPT FILE FOR new_case_manual.php -->
<script src="<?=$SiteURL?>public/js/select2.min.js"></script>
<script type="text/javascript">
  // Reinitialize Colorbox for new elements
$(".ico-interaction2").colorbox({
    iframe: true,
    innerWidth: 1000,
    innerHeight: 550
});
</script>