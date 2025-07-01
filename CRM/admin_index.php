<?php
/***
 * Auth: Vastvikta Nishad
 * Date: 16-01-24
 * Description: this file use for fetch data and insert data in database
 * Admin all module Files added and common files added
 * 
*/

// Add security headers
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Input validation functions
function validateInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function validateToken($token) {
    $allowedTokens = [
        'Bulletin', 'Edit_Bulletin', 'Licence', 'view_category', 'new_category',
        'new_subcategory', 'view_subcategory', 'view_province', 'new_province',
        'view_village', 'new_village', 'view_mail', 'new_mail', 'view_sms',
        'new_sms', 'view_project', 'new_project', 'assign_dept', 'view_status',
        'new_status', 'view_base', 'new_base', 'view_emailstatus', 'view_login',
        'view_escalation', 'new_escalation', 'create_shift', 'create_breaks',
        'create_schedule', 'view_imap_smtp', 'new_imap', 'new_smtp',
        'view_disposition', 'new_disposition', 'view_callbacks', 'new_callbacks',
        'view_adhoc', 'email_sms_template', 'new_bulk_template', 'upload_doc',
        'upload_customer', 'multiple_case', 'upload_subcategory', 'upload_category',
        'upload_subcounty', 'upload_county', 'webchat_template', 'webchat_new_template',
        'whatsapp_template', 'whatsapp_new_template', 'spam_mail', 'new_spam_mail'
    ];
    return in_array($token, $allowedTokens) ? $token : '';
}

include("admin/web_admin_function.php"); // Include admin function file for insert/view/delete function added 

// Validate and decode token
$token = isset($_GET['token']) ? base64_decode($_GET['token']) : '';
$token = validateToken($token);

if (empty($token)) {
    header("Location: ../web_logout.php");
    exit;
}
?>
<!-- Fix code no need to be change -->
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                    <? include("includes/sidebar.php"); ?> <!-- Side menu file include -->
                </div>
                <?php 
                if ($token == 'Bulletin' || $token == 'Edit_Bulletin' || $token == 'Licence') {
                }else{?>
                <div class="col-sm-2 mt-3" style="padding-left:0">
                    <?php include("includes/web_master_services.php"); ?> <!-- admin master Side menu file include -->
                </div>
              <?php }
               if ($token == 'Bulletin' || $token == 'Edit_Bulletin' || $token == 'Licence') {?>
                <div class="col-sm-10 mt-3" style="padding-left:0">
                <?php }else{?>
                <div class="col-sm-8 mt-3" style="padding-left:0">
                <?php } ?>
                    <div class="rightpanels"> 
                        <!-- this code change our requirement -->
                        <?php
                          if($token == 'view_category'){
                            include_once("admin/web_view_category.php");
                          }else if($token == 'new_category'){
                            include_once("admin/web_new_category.php");
                          }else if($token == 'new_subcategory'){  //for adding new sub category 
                            include_once("admin/web_new_sub_category.php");
                          }else if($token == 'view_subcategory'){     //for viewing sub category
                            include_once("admin/web_view_sub_category.php");
                          }else if($token == 'view_province'){   //for  viewing province 
                            include_once("admin/web_view_brand.php");
                          }else if($token == 'new_province'){ //for adding or updating new county in the database 
                            include_once("admin/web_new_brand.php");
                          }else if($token == 'view_village'){ //for viewing sub county 
                            include_once("admin/web_view_village.php");
                          }else if($token == 'new_village'){ //for adding or updating  sub county
                            include_once("admin/web_new_village.php");
                          }else if ($token == 'view_mail') { //for viewing  mail format
                            include_once("admin/web_view_user_registration.php");
                          }else if ($token == 'new_mail') { //for adding or updating  mail format
                            include_once("admin/web_new_user_registration.php"); 
                          }else if ($token == 'view_sms') { //for viewing  sms format
                            include_once("admin/web_view_sms_format.php");
                          }else if ($token == 'new_sms') { //for adding or updating  sms format
                            include_once("admin/web_new_sms_format.php");
                          }else if ($token == 'view_project') { //for adding or updating  sms format
                            include_once("admin/web_view_project.php");
                          }else if ($token == 'new_project') { //for adding or updating  sms format
                            include_once("admin/web_new_project.php");
                          }else if ($token == 'assign_dept') { //for assigning department 
                            include_once("admin/web_user_proj_assign.php");
                          }else if ($token == 'view_status') { // for viewing status 
                            include_once("admin/web_view_status.php");
                          }else if ($token == 'new_status') { // for updating or inserting status 
                            include_once("admin/web_new_status.php");
                          }else if ($token == 'view_base') { // for viewing knowledge base
                            include_once("admin/web_view_faq.php");
                          }else if ($token == 'new_base') { // for updating or inserting knowledge base 
                            include_once("admin/web_add_faq.php");
                          }else if ($token == 'view_emailstatus') { // for viewing  email status 
                            include_once("admin/web_view_emailstatus.php");
                          }else if ($token == 'view_login') { // for viewing  user logins 
                            include_once("admin/web_live_ip_log.php");
                          }else if ($token == 'view_escalation') { // for viewing escalation 
                            include_once("admin/web_view_escalation.php");
                          }else if ($token == 'new_escalation') { // for viewing escalation 
                            include_once("admin/web_new_escalation.php");
                          }else if ($token == 'create_shift') { //  for viewing wfm create shift/break/schedule
                            include_once("WFM/create_shift.php");
                          }else if ($token == 'create_breaks') { //  for viewing wfm create shift/break/schedule
                            include_once("WFM/create_breaks.php");
                          }else if ($token == 'create_schedule') { //  for viewing wfm create shift/break/schedule
                            include_once("WFM/create_schedule.php");
                          }else if ($token == 'view_imap_smtp') { //  for viewing imap and smtp
                            include_once("admin/web_imap_smtp.php");
                          }else if ($token == 'new_imap') { //  for updating imap 
                            include_once("admin/web_new_imap.php");
                          }else if ($token == 'new_smtp') { //  for updating smtp
                            include_once("admin/web_new_smtp.php");
                          }else if ($token == 'view_disposition') { // for viewing disposition 
                            include_once("admin/web_view_disposition.php");
                          }else if ($token == 'new_disposition') { // for inserting and updating  disposition 
                            include_once("admin/web_new_disposition.php");
                          }else if ($token == 'view_callbacks') { // for viewing callbacks 
                            include_once("admin/view_callbacks.php");
                          }else if ($token == 'new_callbacks') { // for updating callbacks 
                            include_once("admin/update_callbacks.php");
                          }else if ($token == 'Bulletin') { // for Bulletin 
                            include_once("admin/web_show_bulletin.php");
                          }else if ($token == 'Edit_Bulletin') { // for Bulletin 
                            include_once("admin/web_bulletin.php");
                          }else if ($token == 'Licence') { // for Licence 
                            include_once("admin/licence-info.php");
                          }else if ($token == 'view_adhoc') { // for Licence 
                            include_once("admin/adhoc_setting.php");
                          }else if ($token == 'email_sms_template') { // for Licence 
                            include_once("admin/bulk_template.php");
                          }else if ($token == 'new_bulk_template') { // for Licence 
                            include_once("admin/new_bulk_template.php");
                          }else if ($token == 'upload_doc') { // for Licence 
                            include_once("admin/upload_doc.php");
                          }else if($token == 'upload_customer'){     //for adding cutomer details[vastvikta][23-04-2025]
                            include_once("admin/web_upload_customer.php");
                          }else if($token == 'multiple_case'){     //for adding multile case creation alert [vastvikta][25-04-2025]
                            include_once("admin/multiple_case_alert.php");
                          }else if($token == 'upload_subcategory'){     //[vastvikta][25-04-2025]
                            include_once("admin/upload_subcategory.php");
                          }else if($token == 'upload_category'){     //[vastvikta][25-04-2025]
                            include_once("admin/upload_category.php");
                          }else if($token == 'upload_subcounty'){     //[vastvikta][25-04-2025]
                            include_once("admin/upload_subcounty.php");
                          }else if($token == 'upload_county'){     //[vastvikta][25-04-2025]
                            include_once("admin/upload_county.php");
                          }else if($token == 'webchat_template'){     //[vastvikta][23-05-2025]
                            include_once("admin/webchat_template.php");
                          }else if($token == 'webchat_new_template'){     //[vastvikta][23-05-2025]
                            include_once("admin/webchat_new_template.php");
                          }else if($token == 'whatsapp_template'){     //[vastvikta][26-05-2025]
                            include_once("admin/whatsapp_template.php");
                          }else if($token == 'whatsapp_new_template'){     //[vastvikta][26-05-2025]
                            include_once("admin/whatsapp_new_template.php");
                          }else if($token == 'spam_mail'){     //[vastvikta][07-06-2025]
                            include_once("admin/spam_mail.php");
                          }else if($token == 'new_spam_mail'){     //[vastvikta][07-06-2025]
                            include_once("admin/new_spam_mail.php");
                          }
                          else { 
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
          <? include("includes/web_footer.php"); ?>
        </div>
    </div>
</body>
<script src="<?=$SiteURL?>public/js/select2.min.js"></script>
<script src="<?=$SiteURL?>public/js/admin_master.js"></script>
<!-- JAVASCRIPT FILE FOR WFM action=create_breaks/create_shift/create_schedules -->
<script src="<?=$SiteURL?>public/js/wfm_master.js"></script>
<script src="<?=$SiteURL?>public/js/doc-upload.js"></script>
<!-- JAVASCRIPT FILE FOR WFM create_breaks.php  -->
<script src="<?=$SiteURL?>public/js/jquery-ui-timepicker-addon2.js"></script>
<script src="<?=$SiteURL?>public/js/jquery.datepick.js"></script>
  <!-- End -->
   