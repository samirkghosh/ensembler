<!--link to the style sheet for the master administration-->
<link rel="stylesheet" href="/ensembler/public/css/master-admin.css">
<!--link to the style sheet for the WFM menu in master administration-->
<link rel="stylesheet" href="WFM/css/agent_styles.css">
<div class="popup-container" id="popupContainer_channel">
    <div class="popup-content">
        <span class="closebtn" id="closePopup">&times;</span>
        <p><strong>Info!</strong> You don't have rights to access it. Please Contact info@alliance-infotech.com</p>
    </div>
</div>
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Master's Administration</span>
<div class="submenu-panel" style="margin-top:37px">
<ul style="padding-left:0">
  <?php 
    $view_category = base64_encode('view_category');
    $view_subcategory = base64_encode('view_subcategory');
    $view_province = base64_encode('view_province');
    $view_village = base64_encode('view_village');
    $view_mail = base64_encode('view_mail');
    $view_sms = base64_encode('view_sms');
    $view_project = base64_encode('view_project');
    $assign_dept = base64_encode('assign_dept');
    $view_status = base64_encode('view_status');
    $view_base = base64_encode('view_base');
    $upload_doc = base64_encode('upload_doc'); // Added for Upload document Knowledge Base API - [03-02-2025] Farhan Akhtar
    $view_emailstatus = base64_encode('view_emailstatus');
    $view_login = base64_encode('view_login');
    $view_escalation = base64_encode('view_escalation');
    $view_wfm = base64_encode('view_wfm');
    $view_imap_smtp = base64_encode('view_imap_smtp');
    $view_disposition = base64_encode('view_disposition');
    $view_callbacks = base64_encode('view_callbacks');
    $create_shift = base64_encode('create_shift');
    $create_breaks = base64_encode('create_breaks');
    $create_schedule = base64_encode('create_schedule');
    $view_adhoc = base64_encode('view_adhoc');
    $email_sms_template = base64_encode('email_sms_template');
    $webchat_template = base64_encode('webchat_template');
    $whatsapp_template = base64_encode('whatsapp_template');
    $upload_customer = base64_encode('upload_customer');
    $multiple_case = base64_encode('multiple_case');
    $upload_subcategory = base64_encode('upload_subcategory');
    $upload_category = base64_encode('upload_category');
    $upload_county = base64_encode('upload_county');
    $upload_subcounty = base64_encode('upload_subcounty');
    $spam_mail = base64_encode('spam_mail');
    // Determine the current active page token
    $current_page_token = isset($_GET['token']) ? $_GET['token'] : '';
    
    function isCategoryActive($current_token, $target_tokens) {
      return in_array($current_token, $target_tokens);
    }

    function isLinkActive($current_token, $target_token, $target_action = null) {
      if ($target_action !== null) {
        return $current_token === $target_token && isset($_GET['action']) && $_GET['action'] === $target_action;
      } else {
        return $current_token === $target_token;
      }
    }
    ?>
    
    <!-- CATEGORY dropdown -->
    <?php 
    $module_flagcate = module_license('View Category');
    $module_flagsubcate = module_license('View Sub Category');
    ?>
    <div class="dropdown">
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_category, $view_subcategory]) ? 'active-dropdown' : '';  if($module_flagcate == '0' && $module_flagsubcate == '0'){?> disable_menu <?php }?> ">Category </button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_category, $view_subcategory]) ? 'style="display: block;"' : ''; ?>>
    <?php 
    if($module_flagcate == '1'){?>
      <li><a class="<?php echo isLinkActive($current_page_token, $view_category) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_category; ?>">View Category</a></li>
    <?php }?>
    <?php 
    if($module_flagsubcate == '1'){?>
      <li><a class="<?php echo isLinkActive($current_page_token, $view_subcategory) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_subcategory; ?>">View Sub Category</a></li>
      </div>
    <?php }?>
  </div>
   <!-- COUNTY dropdown -->
   <?php 
   $module_flagcounty = module_license('View County');
   $module_flagsubcounty = module_license('View Sub County');
   ?>
<div class="dropdown">
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_province, $view_village]) ? 'active-dropdown' : ''; if($module_flagcounty == '0' && $module_flagsubcounty == '0'){?> disable_menu <?php }?>">County</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_province, $view_village]) ? 'style="display: block;"' : ''; ?>>
      <?php
      if($module_flagcounty == '1'){?>
          <li><a class="<?php echo isLinkActive($current_page_token, $view_province) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_province; ?>">View County</a></li>
      <?php }?>
      <?php 
      if($module_flagsubcounty == '1'){?>
          <li><a class="<?php echo isLinkActive($current_page_token, $view_village) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_village; ?>">View Sub County</a></li>
      <?php }?>
  </div>
</div>
 <!-- MAIL dropdown -->
<div class="dropdown">
  <?php
  $module_flagmail = module_license('View Mail Format');
  $module_flagsms = module_license('View SMS Format');
  $module_flagemail = module_license('View Email status');
  $module_flasmptpg = module_license('IMAP & SMTP Settings');
  $module_flagtemp = module_license('Bulk Email / SMS Template');
  $webchat_tempflag =  module_license('WebChat Template');
  $whatsapp_tempflag = module_license('WhatsApp Template');
  $spam_mail_flag = module_license('Spam Email Id');
  ?>
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_mail, $view_sms, $view_emailstatus, $view_imap_smtp,$email_sms_template,$webchat_template,$whatsapp_template,$spam_mail]) ? 'active-dropdown' : ''; if($module_flagmail == '0' && $module_flagsms == '0' && $module_flagemail == '0' && $module_flasmptpg == '0' && $module_flagtemp == '0' && $webchat_tempflag == '0' &&  $whatsapp_tempflag == '0'){?> disable_menu <?php }?>">Mail ,SMS ,WebChat & WhatsApp</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_mail, $view_sms, $view_emailstatus, $view_imap_smtp,$email_sms_template,$webchat_template,$whatsapp_template,$spam_mail]) ? 'style="display: block;"' : ''; ?>>
    <?php 
      if($module_flagmail == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_mail) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_mail; ?>">View Mail Format</a></li>
      <?php }?> 
    <?php 
    if($spam_mail_flag == '1'){?>
      <li><a class="<?php echo isLinkActive($current_page_token, $spam_mail) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $spam_mail; ?>">Spam Email Id</a></li>
    <?php }?>
    <?php 
    if($module_flagsms == '1'){?>  
        <li><a class="<?php echo isLinkActive($current_page_token, $view_sms) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_sms; ?>">View SMS Format</a></li>
    <?php }?>
    <?php 
    if($module_flagemail == '1'){?>
      <li><a class="<?php echo isLinkActive($current_page_token, $view_emailstatus) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_emailstatus; ?>">View Email status</a></li>
    <?php }?>
    <?php 
    if($module_flasmptpg == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_imap_smtp) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_imap_smtp; ?>">IMAP & SMTP Settings</a></li>
    <?php }?>
    <?php 
    if($webchat_tempflag == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $webchat_template) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $webchat_template; ?>">WebChat Template</a></li>
    <?php }?>
    <?php 
    if($whatsapp_tempflag == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $whatsapp_template) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $whatsapp_template; ?>">WhatsApp Template</a></li>
    <?php }?>
    <?php 
    if($module_flagtemp == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $email_sms_template) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $email_sms_template; ?>">Bulk Email, Whatsapp, SMS Template</a></li>
    <?php }?>
    <!-- <li><a class="" href="web_view_village.php">View District</a></li>
    <li><a class="" href="web_view_assign.php">View Service Provider</a></li> -->
  </div>
</div>

  <!-- DEPARTMENT dropdown -->
<div class="dropdown">
  <?php 
  $module_flag_view_de = module_license('View Department');
  $module_flag_assgn_de = module_license('Assign Department'); 
  ?>
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_project, $assign_dept]) ? 'active-dropdown' : ''; if($module_flag_view_de == '0' && $module_flag_assgn_de == '0'){?> disable_menu <?php }?>">Department</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_project, $assign_dept]) ? 'style="display: block;"' : ''; ?>>
    <?php 
    if($module_flag_view_de == '1'){?>
      <li><a class="<?php echo isLinkActive($current_page_token, $view_project) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_project; ?>">View Department</a></li>
     <?php }?>
    <?php
    if($module_flag_assgn_de == '1'){?>   
      <li><a class="<?php echo isLinkActive($current_page_token, $assign_dept) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $assign_dept; ?>">Assign Department</a></li>
    <?php }?>  
  </div>
</div>
<!-- STATUS dropdown -->
<?php 
$module_flagviewest = module_license('View Status');
$module_flagviewlog = module_license('View User Logins');
?>
<div class="dropdown">
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_login, $view_status]) ? 'active-dropdown' : ''; if($module_flagviewest == '0' && $module_flagviewlog == '0'){?> disable_menu <?php }?>">Status</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_login, $view_status]) ? 'style="display: block;"' : ''; ?>>
    <?php 
    // Check license for 'View Login User IP' and display the link if allowed
    if ($module_flagviewlog == '1') { ?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_login) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_login; ?>">View Login User IP</a></li>
    <?php } ?>
    <?php 
    // Check license for 'View Status' and display the link if allowed
    if ($module_flagviewest == '1') { ?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_status) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_status; ?>">View Status</a></li>
    <?php } ?>
  </div>
</div>


    <!-- KNOWLEDGE BASE dropdown -->
  <?php $module_flag = module_license('View Knowledge base');
    if($module_flag == '1'){?>
    <div class="dropdown">
      <!-- corrected logic for upload document [Vastvikta Nishad] [28-04-2025] -->
    <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_base,$upload_doc]) ? 'active-dropdown' : ''; ?>">Knowledge Base</button>
    <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_base,$upload_doc]) ? 'style="display: block;"' : ''; ?>>
      <li><a class="<?php echo isLinkActive($current_page_token, $view_base) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_base; ?>">View Knowledge base</a></li>
    </div>
    <!-- Added for Upload document Knowledge Base API - [03-02-2025] Farhan Akhtar -->
    <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_base,$upload_doc]) ? 'style="display: block;"' : ''; ?>>
      <li><a class="<?php echo isLinkActive($current_page_token, $upload_doc) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_doc; ?>">Upload document</a></li>
      </div>
  </div>
  <?php }?>
    <!-- LOGINS & ESCALATION dropdown -->
    <div class="dropdown">
      <?php 
      $module_flagviewus = module_license('View User Logins');
      $module_flagviewes = module_license('View Escalation');
      $module_flagviewad = module_license('Adhoc settings');
      ?>
   <button class="dropbtn <?php echo isCategoryActive($current_page_token, [ $view_escalation,$view_adhoc]) ? 'active-dropdown' : ''; if($module_flagviewus == '0' && $module_flagviewes == '0' && $module_flagviewad == '0'){?> disable_menu <?php }?>">Escalation</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [ $view_escalation,$view_adhoc]) ? 'style="display: block;"' : ''; ?>>
    <!-- <?php 
    if($module_flagviewus == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_login) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_login; ?>">View User Logins</a></li> -->
    <?php 
    }
    if($module_flagviewes == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_escalation) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_escalation; ?>">View Escalation</a></li>
    <?php 
    }
    if($module_flagviewad == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $view_adhoc) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_adhoc; ?>">View Adhoc</a></li>
    <?php }?>
    </div>
</div>
   <!-- WFM dropdown -->
   <div class="dropdown">
    <?php 
    $module_flagwfm_shft = module_license('WFM Create Shift');
    $module_flagwfmbreak = module_license('WFM Create Break');
    $module_flagwfmsch = module_license('WFM Create Schedules')
    ?>
  <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$create_shift,$create_breaks,$create_schedule]) ? 'active-dropdown' : ''; if($module_flagwfm_shft == '0' && $module_flagwfmbreak == '0' && $module_flagwfmsch == '0'){?> disable_menu <?php }?> ">WFM</button>
  <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$create_shift,$create_breaks,$create_schedule]) ? 'style="display: block;"' : ''; ?>>

    <?php 
    if($module_flagwfm_shft == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $create_shift) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $create_shift; ?>&action=create_shift">WFM Create Shift</a></li>
    <?php }?>
    <?php 
    if($module_flagwfmbreak == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $create_breaks) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $create_breaks; ?>&action=create_breaks">WFM Create Break</a></li>
    <?php }?>
    <?php ;
    if($module_flagwfmsch == '1'){?>
        <li><a class="<?php echo isLinkActive($current_page_token, $create_schedule) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $create_schedule; ?>&action=create_schedule">WFM Create Schedules</a></li>
   <?php }?>
     </div>
</div>

<!-- CALLBACK dropdown -->
<?php $module_flag = module_license('View Callbacks');
if($module_flag == '1'){?>
  <div class="dropdown">
    <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_callbacks]) ? 'active-dropdown' : ''; ?>">Callback</button>
    <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_callbacks]) ? 'style="display: block;"' : ''; ?>>
      
          <li><a class="<?php echo isLinkActive($current_page_token, $view_callbacks) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_callbacks; ?>">View Callbacks</a></li>
      
      </div>
  </div>
<?php }?>

    <!-- DISPOSITION dropdown -->
  <?php $module_flag = module_license('View Disposition');
  if($module_flag == '1'){?>
    <div class="dropdown">
      <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$view_disposition]) ? 'active-dropdown' : ''; ?>">Disposition</button>
      <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$view_disposition]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $view_disposition) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $view_disposition; ?>">View Disposition</a>
            </li>
        </div>
    </div>
    <?php }?>

    <!-- upload customer details using  excel sheet dropdown -->
    <?php $module_flag = module_license('Upload Customer Detail');
  if($module_flag == '1'){?>
    <div class="dropdown">
      <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$upload_customer]) ? 'active-dropdown' : ''; ?>">Upload Customers</button>
      <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$upload_customer]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $upload_customer) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_customer; ?>">Add Customer</a>
            </li>
        </div>
    </div>
    <?php }?>
     <!--  dropdown -->
    <?php $module_flag = module_license('Multiple Case alert');
  if($module_flag == '1'){?>
    <div class="dropdown">
      <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$multiple_case]) ? 'active-dropdown' : ''; ?>">Multiple Case alert</button>
      <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$multiple_case]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $multiple_case) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $multiple_case; ?>">Update Mail alert</a>
            </li>
        </div>
    </div>
    <?php }?>

     <!--  dropdown -->
    <?php $module_flag = module_license('Upload Category / Sub Category');
    if($module_flag == '1'){?>
    <div class="dropdown">
      <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$upload_subcategory,$upload_category]) ? 'active-dropdown' : ''; ?>">Upload Category / Sub Category</button>
      <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$upload_subcategory,$upload_category]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $upload_category) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_category; ?>">Upload Category</a>
            </li>
        </div>
        <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$upload_subcategory,$upload_category]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $upload_subcategory) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_subcategory; ?>">Upload Sub Category</a>
            </li>
        </div>
    </div>
    <?php }?>
    <!-- upload Multiple County / Sub Countyusing  excel sheet dropdown -->
    <?php $module_flag = module_license('Upload County / Sub County');
    if($module_flag == '1'){?>
    <div class="dropdown">
      <button class="dropbtn <?php echo isCategoryActive($current_page_token, [$upload_subcounty,$upload_county]) ? 'active-dropdown' : ''; ?>">Upload County / Sub County</button>
      <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$upload_subcounty,$upload_county]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $upload_county) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_county; ?>">Upload county</a>
            </li>
        </div>
        <div class="dropdown-content" <?php echo isCategoryActive($current_page_token, [$upload_subcounty,$upload_county]) ? 'style="display: block;"' : ''; ?>>
            <li><a class="<?php echo isLinkActive($current_page_token, $upload_subcounty) ? 'active-link' : ''; ?>" href="admin_index.php?token=<?php echo $upload_subcounty; ?>">Upload Sub County</a>
            </li>
        </div>
    </div>
    <?php }?>
  </ul>
</div>