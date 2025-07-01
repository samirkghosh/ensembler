<?php 
/**
 * Sidebar Page
 * Author: Aarti Ojha
 * Date: 16-01-2024
 * Description: This file handles menu options and redirects to particular pages.
 *              It handles group ID conditions for showing/hiding menus.
 */
include "head.php"; // Includes JS/CSS file handling
include_once "../function/classify_function.php"; // Includes common function handling

// Check session availability and force logout if not available
if(isset($_SESSION['AccountNumber'])) {
    // Session is valid
} elseif(isset($_SESSION['SNo']) && isset($_SESSION['login_email'])) {
    // Session variables exist
} else {
    // Redirect to logout page if session variables are not set
    header("Location: ../web_logout.php"); 
    exit; // Ensure script stops execution after redirection
}
$uid = $_SESSION['AccountNumber'];
// Check menu access permission
$classify = classify_agent($_SESSION['userid']);
if($classify == 0) {
    $disable = 'disabled'; // Disable menu options if user is not classified
}

$groupid = $_SESSION['user_group']; // Get user's group ID
$VD_login = $_SESSION['VD_login'];
// [Aarti][16-04-2024] Social media license configuration code
$channel_license_flag = channel_license($_SESSION['userid']);
$telephony_flag = get_telephony_flag($_SESSION['userid']);


?>

<!--  // [Aarti][16-04-2024] for this code channel configuration and channel license acces provide -->
<!-- Popup Container -->
<div class="popup-container" id="popupContainer">
    <div class="popup-content">
        <span class="closebtn" id="closePopup">&times;</span>
        <p><strong>Info!</strong> Licence not available for this module. Please Contact info@alliance-infotech.com</p>
    </div>
</div>
<div class="popup-container" id="popupContainer_channel">
    <div class="popup-content">
        <span class="closebtn" id="closePopup">&times;</span>
        <p><strong>Info!</strong> You don't have rights to access it. Please Contact info@alliance-infotech.com</p>
    </div>
</div>
<div class="d-flex flex-column flex-shrink-0 p-3 text-dark bg-dark sidebar">
<!-- Farhan Akhtar :: 29-01-2025 :: UI For Knowledge Base -->

<!-- added module license in voicebot helps & resource , voicebot , ticket deletion report , disposition report [vastvitka][12-06-2025] -->
<?php $module_flag = module_license('Help & Resources');
            if($module_flag == '1'){?>
<div class="sticky-icon">
   <a  class="FAQ" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-question"></i> Help & Resources</a>
</div>
<?php }?>
    <a href="#">
        <span class="fs-4">
            <img class="vision2" src="<?=$SiteURL?>public/images/<?=$dbheadlogo?>" style="width: 140px;height: 80px;background:#fff"> 
        </span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
    <input type="hidden" name="unique_id" id="unique_id" value="<?php echo $_SESSION['unique_id'];?>">
    <!-- This menu allow only customer -->
    <?php if($_SESSION['customer_view']==1){?>
        <li class="nav-item">
            <a href="home_customer.php" class="nav-link <? if($selecttab==12){echo " active";}?>"
                aria-current="page">
                <i class="fas fa-chart-bar"></i>
                Home
            </a>
        </li>
        <li class="nav-item" style="display: none;">
            <a href='new_case_manual.php?customerid=<?=$uid?>' class="nav-link <? if($selecttab==13){echo " active";}?>"
                aria-current="page">
                <i class="fas fa-chart-bar"></i>
                Register Case
            </a>
        </li>
    <?php } ?>
        <!-- Admin Menu Options -->
    <?php if( $groupid=='0000' || $groupid=='080000' || $groupid=='070000' || $groupid=='060000' || $groupid== '050000'){?>
        <?php
        
// changed this code for licence module [vastvikta][29-03-2025]
         if(($groupid=='0000') || ($groupid=='080000') || ($groupid=='070000' || $groupid=='060000')){
            $web_admin_dashboard = base64_encode('web_admin_dashboard');
            
             // added the code for module license dashboard [vastvikta][28-03-2025]
             $module_flag = module_license2('Dashboard', $groupid);
            
             if($module_flag['module_flag'] == '1' && in_array($groupid, $module_flag['group_Ids'])) { ?>
                 
            <li class="nav-item">
                <a href="dashboard_index.php?token=<?php echo $web_admin_dashboard;?>" class="nav-link <? if($selecttab==2){echo " active";}?>"
                    aria-current="page">
                    <i class="fas fa-chart-bar"></i>
                    Dashboard
                </a>
            </li>
            <?php }?>
        <?php }?>
        <?php $web_consumer_home = base64_encode('web_consumer_home');?>
        <li class="nav-item">
            <a href="customer_index.php?token=<?php echo $web_consumer_home;?>" class="nav-link <? if($selecttab==3){echo " active";}?>">
                <i class="fa fa-user"></i>
                Customers
            </a>
        </li>
        <?php $web_helpdesk = base64_encode('web_helpdesk');?>
        <li class="nav-item">
            <a href="helpdesk_index.php?token=<?php echo $web_helpdesk;?>" class="nav-link  <? if($selecttab==1){echo " active";}?>">
             <i class="fas fa-desktop"></i>
                HelpDesk
            </a>
        </li>
        <?php $module_flag = module_license('View Knowledge base');
                if($module_flag == '1'){?>
        <?php $knowledge_base = base64_encode('knowledge_base');?>
        <li class="nav-item">
        <a href="web_faq_view.php?token=<?php echo $knowledge_base;?>" class="nav-link <? if($selecttab==4){echo " active";}?>" >
                <i class="fa fa-users"></i>
                Knowledge base
            </a>
        </li>
        <?php }?>
        <?php $web_agent = base64_encode('web_agent');?>
        <li class="nav-item">
            <a href="report_index.php?token=<?php echo $web_agent;?>" class="nav-link  <? if($selecttab==5){echo "  active";}?>">
            <i class="fas fa-file-excel"></i>
                Reports
            </a>
        </li>
        <?php 
        
            // changed this code for licence module [vastvikta][29-03-2025]
            if(($groupid=='0000') || ($groupid=='080000') || ($groupid=='070000' || $groupid=='060000' )){
            $view_category = base64_encode('view_category');
            $Bulletin = base64_encode('Bulletin');
            $Licence = base64_encode('Licence');
            $web_userhome = base64_encode('web_userhome');
            // added the code for module license admin [vastvikta][28-03-2025]
            
            $module_flag = module_license2('Admin',$groupid);
           
            if($module_flag['module_flag'] == '1' && in_array($groupid, $module_flag['group_Ids'])) { ?>
              
        <li class="nav-item">
            <a href="#" class="nav-link <? if($selecttab==6){echo " active";}?>">
             <i class="fas fa-users-cog"></i>
                Admin <i class="fas fa-caret-right"></i></a>
                <ul class="submenu dropdown-menu">
                <li class="nav-item"><a class="dropdown-item nav-link" href="user_index.php?token=<?php echo $web_userhome;?>">View Users</a></li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link" href="admin_index.php?token=<?php echo $view_category;?>">Master</a>
                </li>
                <?php $module_flag = module_license('Licence Information');
                if($module_flag == '1'){?>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="admin_index.php?token=<?php echo $Licence?>">Licence Information</a></li>
                <?php }?>
                <li class="nav-item">
                    <?php $module_flag = module_license('Bulletin');
                    if($module_flag == '1'){?>                        
                    <a class="dropdown-item nav-link" href="admin_index.php?token=<?php echo $Bulletin?>" >Bulletin</a>
                    <?php }else{?>
                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulletin</a>
                    <?php }?>
                </li>
            </ul>
            <?php }?>
        <?php }?>
        <!-- Omnichannel,WFM,voicemail,misscall access allow for admin,superwiser,agent -->
        <?php if(($groupid=='0000') || ($groupid=='080000') || ($groupid=='070000')){?>
            
            <li class="nav-item">
                <a href="javascript:void();" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                <i class="fab fa-stack-overflow"></i>
                    Omnichannel <i class="fas fa-caret-right"></i>
                </a>
                <?php
                 $omni_index = base64_encode('configuration');
                 $email_complaint = base64_encode('email_complaint');
                 $email_enquiry = base64_encode('email_enquiry');
                 $email_queue_report = base64_encode('email_queue_report');
                 $twitter = base64_encode('twitter');
                 $twitter_report = base64_encode('twitter_report');
                 $sms = base64_encode('sms');
                 $whatsapp = base64_encode('whatsapp');
                 $whatsapp_report = base64_encode('whatsapp_report');
                 $facebook = base64_encode('facebook');
                 $facebook_messanger = base64_encode('facebook_messanger');
                 $messenger_report = base64_encode('messenger_report');
                 $live_chat = base64_encode('live_chat');
                 $chat = base64_encode('chat');
                 $bulk_sms = base64_encode('bulk_sms');
                 $web_bulksms = base64_encode('web_bulksms');
                 $instagram = base64_encode('instagram'); //added instagram menu in sidebar[Aarti][18-11-2024]
                 $instagram_post = base64_encode('instagram_post'); //added instagram post menu in sidebar[Aarti][09-12-2024]
                 $insta_report = base64_encode('insta_report');
                 $bulk_email_report = base64_encode('bulk_email_report');
                 $bulk_twiter_report = base64_encode('bulk_twiter_report');
                 $bulk_sms_report = base64_encode('bulk_sms_report');
                 $bulk_whatsapp_report = base64_encode('bulk_whatsapp_report');
                 $queue_report = base64_encode('queue_report');
                 $bad_report = base64_encode('bad_report');
                 // <!-- disposition report path added [Aarti][23-07-2024] -->
                 $disposition_report = base64_encode('disposition_report');
                //  ticket deletion report [vastvikta][18-12-2024]
                 $ticket_deletion_report = base64_encode('ticket_deletion_report');
                //  multi_chat  code [vastvikta][30-12-2024]
                $multi_chat = base64_encode('multi_chat');
                ?>
                <ul class="submenu dropdown-menu">    
                    <!-- Added supervisor groupid so that the  configuration is visible  to the supervisor [vastvikta][12-02-2025] -->
                    <?php if(($groupid == '0000')||($groupid == '080000')){?><li class="nav-item"><a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $omni_index?>">Configuration</a></li><?php }?>
                    <li class="nav-item">
                        <?php $module_flag = module_license('Email');
                        if($module_flag == '1' && in_array('Email', $channel_license_flag)){?>
                        <!-- changed the link [vastvikta ][17-03-2025] -->
                        <a href="#" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                        <i class="fa fa-envelope"></i>
                            Email <i class="fas fa-caret-right"></i>
                        </a>
                        <ul class="submenu dropdown-menu">
                            <li class="nav-item">
                                 <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $email_complaint?>" >Email Complaint</a>
                                    <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >Email Complaint</a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $email_enquiry?>">Email Inquiry</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Email Inquiry</a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="bulkcampaign_index.php?token=<?php echo $web_bulksms?>&campaign=Email">Bulk Campaign </a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulk Campaign </a>
                                <?php }?>
                            </li>
                           <!--  <li class="nav-item">
                                <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_email_report?>">Compose Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                <?php }?>
                            </li> -->
                            <li class="nav-item">
                                <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $email_queue_report?>">Queue Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Queue Report</a>
                                <?php }?>
                            </li>
                        </ul>
                        <?php }else{ 
                            if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Email', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Email </a>
                        <?php }?>           
                    </li>
                    <li class="nav-item">
                        <?php $module_flag = module_license('Twitter');
                        if($module_flag == '1' && in_array('Twitter', $channel_license_flag)){?>
                            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $twitter?>" ><img src="../public/images/twitter.png" alt="X" style="width: 20px; height: 20px;"></a>
                            <ul class="submenu dropdown-menu"><!--Ritu 24-07-2024 -->
                            <li class="nav-item">
                                <?php $module_flag = module_license('Twitter');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $twitter_report?>">Queue Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >Queue Report</a>
                                <?php }?>
                            </li>
                                <li class="nav-item">
                                  
                                    <?php $module_flag = module_license('Twitter');
                                    if($module_flag == '1'){?>
                                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_twiter_report?>">Compose Report</a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                    <?php }?>
                                </li>

                                <li class="nav-item">
                        <?php $module_flag = module_license('Twitter');
                        if($module_flag == '1' && in_array('Twitter', $channel_license_flag)){?>
                            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $twitter?>" ><img src="../public/images/twitter.png" alt="X" style="width: 20px; height: 20px;"></a>
                            <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                    <?php }?>
                                </li>
                            </ul>
                        <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Twitter', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" ><img src="../public/images/twitter.png" alt="X" style="width: 20px; height: 20px;"></a>
                        <?php }?>
                        </li>
                        
                    <li class="nav-item">
                        <?php $module_flag = module_license('SMS');
                        if($module_flag == '1'  && in_array('SMS', $channel_license_flag)){?>
                           <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                            <i class="fas fa-sms"></i>
                                SMS <i class="fas fa-caret-right"></i>
                            </a>
                            <ul class="submenu dropdown-menu">
                                <li>
                                <?php $module_flag = module_license('SMS');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $sms?>" >SMS</a>
                                <?php }else{ ?>
                                   <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >SMS</a>
                                <?php }?>
                                </li>
                                <li class="nav-item">
                                    <?php $module_flag = module_license('SMS');
                                    if($module_flag == '1'){?>
                                        <a class="dropdown-item nav-link" href="bulkcampaign_index.php?token=<?php echo $web_bulksms?>&campaign=SMS">Bulk Campaign </a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulk Campaign </a>
                                    <?php }?>
                                </li>
                               <!--  <li class="nav-item">
                                    <?php $module_flag = module_license('SMS');
                                    if($module_flag == '1'){?>
                                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_sms_report?>">Compose Report</a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                    <?php }?>
                                </li> -->
                                <li class="nav-item">
                                    <?php $module_flag = module_license('SMS');
                                    if($module_flag == '1'){?>
                                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $queue_report?>">Queue Report</a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Queue Report</a>
                                    <?php }?>
                                </li>
                                <li class="nav-item">
                                    <?php $module_flag = module_license('SMS');
                                    if($module_flag == '1'){?>
                                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bad_report?>">Bad contact Report</a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bad contact Report</a>
                                    <?php }?>
                                </li>
                            </ul>
                        <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('SMS', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >SMS</a>
                        <?php }?>
                    </li>
                    <li class="nav-item">
                    <?php $module_flag = module_license('WhatsApp');
                    if($module_flag == '1' && in_array('WhatsApp', $channel_license_flag)){?>
                        <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                        <i class="fab fa-whatsapp" style="color: #000000;"></i>
                            Whatsapp <i class="fas fa-caret-right"></i>
                        </a>
                        <ul class="submenu dropdown-menu">
                            <li class="nav-item">
                                <?php $module_flag = module_license('WhatsApp');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $whatsapp?>">Whatsapp</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >Whatsapp</a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('WhatsApp');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $whatsapp_report?>">Whatsapp Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >Whatsapp</a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('WhatsApp');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="bulkcampaign_index.php?token=<?php echo $web_bulksms?>&campaign=WhatsApp">Bulk Campaign </a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulk Campaign </a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('WhatsApp');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_whatsapp_report?>">Compose Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                <?php }?>
                            </li>
                        </ul>
                        <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('WhatsApp', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                            <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Whatsapp</a>
                    <?php }?>
                    </li>
                    <!-- Facebook messenger menu option added [Aarti][23-08-2024] -->
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                        <i class="fas fa-comments"></i>
                            Facebook <i class="fas fa-caret-right"></i>
                        </a>
                        <ul class="submenu dropdown-menu">
                            <li class="nav-item">
                                <?php $module_flag = module_license('Facebook Post');
                                if($module_flag == '1' && in_array('Facebook Post', $channel_license_flag)){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $facebook?>">Facebook</a>
                                <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Facebook Post', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                        <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Facebook</a>
                                <?php } ?>
                            </li>  
                             <li class="nav-item">
                                <?php $module_flag = module_license('Facebook Messenger');
                                if($module_flag == '1' && in_array('Facebook Messenger', $channel_license_flag)){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $facebook_messanger?>">Messenger</a>
                                <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Facebook Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                        <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Messenger</a>
                                <?php } ?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Facebook Messenger');
                                if($module_flag == '1' && in_array('Facebook Messenger', $channel_license_flag)){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $messenger_report?>">Messenger Report</a>
                                <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Facebook Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                        <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Messenger Report</a>
                                <?php } ?>
                            </li>
                        </ul>  
                    </li> 
                    <!-- Instagram messenger menu option added [Aarti][18-11-2024] -->
                    <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                        <i class="fab fa-instagram"></i>
                            Instagram <i class="fas fa-caret-right"></i>
                        </a>
                        
                        <ul class="submenu dropdown-menu">
                        <li class="nav-item">
                            <?php $module_flag = module_license('Instagram Messenger');
                            if($module_flag == '1' && in_array('Instagram Messenger', $channel_license_flag)){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $instagram?>">
                                    <i class="fab fa-instagram"></i>instagram</a>
                            <?php 
                            
                            }else{ 
                                if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Instagram Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >
                                <i class="fab fa-instagram"></i>Instagram</a>
                            <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Instagram Messenger');
                                if($module_flag == '1' && in_array('Instagram Messenger', $channel_license_flag)){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $insta_report?>">Instagram Queue Report</a>
                                <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Instagram Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                        <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Instagram Queue Report</a>
                                <?php } ?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Instagram Post');
                                
                                if($module_flag == '1' && in_array('Instagram Post', $channel_license_flag)){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $instagram_post?>">Instagram Post</a>
                                <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Instagram Post', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                        <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Instagram Post</a>
                                <?php } ?>
                            </li>
                    </ul>
                    </li>               
                    <li class="nav-item">
                        <?php $module_flag = module_license('Chat');
                        if($module_flag == '1' && in_array('Chat', $channel_license_flag)){?>
                            <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                            <i class="fas fa-comments"></i>
                                Chat <i class="fas fa-caret-right"></i>
                            </a>
                            <ul class="submenu dropdown-menu">
                                <!-- for chat option acess only for agent [aarti][27-11-2024] -->
                                <!-- <?php if($groupid == '070000'){ ?>  -->
                                <!-- <li class="nav-item">
                                    <?php $module_flag = module_license('Chat');
                                    if($module_flag == '1'){?>
                                         <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $live_chat?>">Live Chat</a>
                                    <?php }else{ ?>
                                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Live Chat</a>
                                    <?php }?>
                                </li> -->
                                <!-- <?php }?> -->
                                <li class="nav-item">
                                <?php $module_chat = module_license('Chat');
                                if($module_chat == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $chat?>" >Chat</a>
                                <?php }else{ ?>
                                    <a class=" disable_menu dropdown-item nav-link" href="javascript:void(0);" >Chat</a>
                                <?php }?>
                                </li>
                                <li class="nav-item">
                                <?php $module_chat = module_license('Chat');
                                if($module_chat == '1'){?>
                                    <a class="dropdown-item nav-link" href="multi_chat_index.php?token=<?php echo $multi_chat?>" >Multi Chat</a>
                                <?php }else{ ?>
                                    <a class=" disable_menu dropdown-item nav-link" href="javascript:void(0);" >Chat</a>
                                <?php }?>
                                </li>
                            </ul>
                        <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Chat', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                                <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >Chat</a>
                        <?php }?>
                    </li>
                     <!-- disposition report path added [vastvikta][30-04-2025] -->
                     <?php $module_flag = module_license('Voicebot');
            if($module_flag == '1'){?>
                     <li class="nav-item">
                        <a class="dropdown-item nav-link" href="https://voicechat.alliance-infotech.in/">
                            <img src = "../public/images/voice.png" alt="Voicechat Icon" style="height: 13px; width: 13px;">
                            Voicebot
                        </a>
                    </li>
                    <?php }?>
                    <?php $module_flag = module_license('Channel Disposition Report');
            if($module_flag == '1'){?>
                    <!-- disposition report path added [Aarti][23-07-2024] -->
                    <li class="nav-item"><a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $disposition_report?>">Channel Disposition Report</a></li>
                    <?php } ?>
                    <?php $module_flag = module_license('Ticket Deletion Report');
            if($module_flag == '1'){?>
                     <!-- disposition report path added [vastvikta][18-12-2024] -->
                     <li class="nav-item"><a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $ticket_deletion_report?>">Ticket Deletion Report</a></li>
               <?php }?>
                </ul>
            </li>
            <li class="nav-item">
                <?php $module_flag = module_license('WFM');
                if($module_flag == '1'){?>
                <a href="#" class="nav-link <? if($selecttab==12){echo " active";}?>">
                 <i class="fas fa-users-cog"></i>
                    WFM <i class="fas fa-caret-right"></i></a>
                    <?php 
                        $real_time_adherence = base64_encode('real_time_adherence');
                        $agents_shifts = base64_encode('agents_shifts');
                        $agent_monthly_event = base64_encode('agent_monthly_event');
                        $adherence_report = base64_encode('adherence_report');
                        $web_erlang_index = base64_encode('web_erlang_index');
                        $forecast_accuracy = base64_encode('forecast_accuracy');
                        $web_erlang_index = base64_encode('web_erlang_index');
                        $wfm_reports = base64_encode('wfm_reports');
                       
                        $shift_assignment_report = base64_encode('shift_assignment_report');
                        /*
                        $agentwise_assignment_report = base64_encode('agentwise_assignment_report');
                        $schedule_adherence_report = base64_encode('schedule_adherence_report');
                        $shift_assignment_report_hist = base64_encode('shift_assignment_report_hist');
                        $agentwise_assignment_report_hist = base64_encode('agentwise_assignment_report_hist');
                        $schedule_adherence_report_hist = base64_encode('schedule_adherence_report_hist');*/
                    ?>
                <ul class="submenu dropdown-menu">
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $real_time_adherence?>">Real Time Adherence</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $agents_shifts?>">View Agents Schedule</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $agent_monthly_event?>">View Monthly Schedule</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $adherence_report?>">View Adherence %</a></li>
                    <!-- <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?action=web_login_avg_report">View Agent Avg</a></li> -->
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $web_erlang_index?>">Erlang Calculator</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php echo $forecast_accuracy?>">Forecast Accuracy</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_reports.php?token=<?php echo $shift_assignment_report?>">WFM Reports</a>    
                       <!-- <ul class="submenu dropdown-menu">
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php //echo $shift_assignment_report?>">Shift Assignment Report</a></li>
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php// echo $agentwise_assignment_report?>">Agentwise Assignment Report</a></li>
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php //echo $schedule_adherence_report?>">Schedule Adherence Report</a></li>
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php //echo $shift_assignment_report_hist?>">Shift Assignment Report Hist</a></li>
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php //echo $agentwise_assignment_report_hist?>">Agentwise Assignment Report Hist</a></li>
                            <li class="nav-item"><a class="dropdown-item nav-link" href="wfm_index.php?token=<?php //echo $schedule_adherence_report_hist?>">Schedule Adherence Report Hist</a></li>
                        </ul>-->
                    </li>
                </ul>
                <?php }else{?>
                <a href="#" class="nav-link disable_menu <? if($selecttab==12){echo " active";}?>">
                 <i class="fas fa-users-cog"></i>
                    WFM <i class="fas fa-caret-right"></i></a>
                <?php }?>
            </li>
            <?php 
            // Encoding token values for security
            // changed from VoiceMail_Report to VoiceMail  for module license  instructed by - Kewal Sir [vastvikta][14-02-2025] 
            $VoiceMail_report = base64_encode('VoiceMail_report');
            // Checking module license for VoiceMail_Report
            $module_flag = module_license('VoiceMail');
            if($module_flag == '1'){
                // If module license is valid, display VoiceMail_report menu item?>
                <li class="nav-item">
                    <a class="dropdown-item nav-link" href="VoiceMail_index.php?token=<?php echo $VoiceMail_report?>">
                        <i class='fa fa-voicemail'></i> VoiceMail</a>
                </li>
               
            <?php }?>
           <?php $misscall_report = base64_encode('misscall_report'); ?>
           <!-- Always display Misscall menu item -->
           <!-- updated the code for displaying the misscall count [vastvikta][16-12-2024] -->
            <li class="nav-item">
                    <a class="dropdown-item nav-link" href="VoiceMail_index.php?token=<?php echo $misscall_report?>">
                <i class='fa fa-phone'></i> Misscall (<span id="misscall_count">0</span>)
                </a>
            </li>
        <?php }?>
        <!-- END -->
    <?php } ?>
    <!-- End of admin Options -->

    <!-- Escalation User Menu Options  -->
    <?php if(($groupid=='090000') ){?>
        <a href="#" class="nav-link  <? if($selecttab==3){echo "  active";}?>">
            <i class="fa fa-user"></i>
            Escalation Cases
        </a>
    <?php }?>
    <!-- End of B5 & Admin Supervisour Menu Options -->

    <!-- End of Agent Menu Options  -->
    <?php if(empty($_SESSION['customer_view'])){
        if($groupid!='060000'){
        ?>
        <li class="nav-item">
            <?php $module_flag = module_license('Facebook Post');
            if($module_flag == '1' && in_array('Facebook Post', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $facebook?>" class="nav-link">
                <i class="fab fa-facebook"></i>
                Facebook(<span id="fbcount">0</span>)
            </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Facebook Post', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                    <i class="fab fa-facebook-messenger"></i>
                    Facebook(<span id="fbcount">0</span>)
                </a>
            <?php }?>
        </li>
        <!-- Facebook messenger menu option added [Aarti][23-08-2024] -->
        <li class="nav-item">
            <?php $module_flag = module_license('Facebook Messenger');
           
            if($module_flag == '1' && in_array('Facebook Messenger', $channel_license_flag)){?>
                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $facebook_messanger?>">
                <i class="fab fa-facebook"></i> Messenger(<span id="fbMcount">0</span>)</a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Facebook Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                    <a class="<?php echo $menus;?> dropdown-item nav-link" href="javascript:void(0);" >
                        <i class="fab fa-facebook-messenger"></i> Messengers(<span id="fbMcount">0</span>)</a>
            <?php } ?>
        </li>
        <li class="nav-item" >
            <?php $module_flag = module_license('Email');
            if($module_flag == '1' && in_array('Email', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $email_complaint?>" class="nav-link">
                <i class="fa fa-envelope"></i>
                Mail(<span id="mailcount">0</span>)
            </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Email', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                    <i class="fa fa-envelope"></i>
                    Mail(<span id="mailcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('Twitter');
            if($module_flag == '1' && in_array('Twitter', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $twitter?>" class="nav-link">
                <img src = "../public/images/x-twitter.svg" alt="X" style="width: 12px; height: 12px;">(<span id="tweetcount">0</span>)
            </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Twitter', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"> <a href="javascript:void(0);" class="disable_menu nav-link"><img src = "../public/images/x-twitter.svg" alt="X" style="width: 12px; height: 12px;">(<span id="tweetcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('Chat');
            if($module_flag == '1' && in_array('Chat', $channel_license_flag)){?>
                <!-- for chat option acess only for agent [aarti][27-11-2024] -->
                <!-- changed from chat to multichat [vastvikta][26-02-2024] -->
                <?php if($groupid == '070000'){ ?>
                    <a href="multi_chat_index.php?token=<?php echo  $multi_chat?>" class="nav-link">
                        <i class="fas fa-comments"></i>
                        Live Chat(<span id="chatcount">0</span>)
                    </a>
                <?php }else{ ?>
                    <a href="multi_chat_index.php?token=<?php echo $multi_chat?>" class="nav-link">
                        <i class="fas fa-comments"></i>
                        Live Chat(<span id="chatcount">0</span>)
                    </a>
                <?php } ?>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Chat', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                    <i class="fas fa-comments"></i>
                   Chats(<span id="chatcount">0</span>)
                </a>
            <?php }?>
        </li>
        <?php $module_flag = module_license('Voicebot');
            if($module_flag == '1'){?>
        <li class="nav-item">
            <a class="dropdown-item nav-link" href="https://voicechat.alliance-infotech.in/">
                <img src = "../public/images/voice.png" alt="Voicechat Icon" style="height: 13px; width: 13px;">
                Voicebot
            </a>
        </li>
        <?php } ?>
        <li class="nav-item">
            <?php $module_flag = module_license('WhatsApp');
            if($module_flag == '1' && in_array('WhatsApp', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $whatsapp?>" class="nav-link">
                    <i class="fab fa-whatsapp" style="color: #000000;"></i>
                    WHATSAPP(<span id="whatsapp_count">0</span>)
                </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('WhatsApp', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                <i class="fab fa-whatsapp" style="color: #000000;"></i>
                WHATSAPP(<span id="whatsapp_count">0</span>)
            </a>
            <?php }?>
        </li>
        <!-- Instagram messenger menu option added [Aarti][18-11-2024] -->
        <li class="nav-item">
            <?php $module_flag = module_license('Instagram Messenger');
            if($module_flag == '1' && in_array('Instagram Messenger', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $instagram?>" class="nav-link">
                    <i class="fab fa-instagram" style="color: #000000;"></i>
                    INSTAGRAM(<span id="instagram_count">0</span>)
                </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('Instagram Messenger', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                <i class="fab fa-instagram" style="color: #000000;"></i>
                INSTAGRAM(<span id="instagram_count">0</span>)
            </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('SMS');
            if($module_flag == '1' && in_array('SMS', $channel_license_flag)){?>
            <a href="omni_channel.php?token=<?php echo $sms?>" class="nav-link">
                <i class="fas fa-sms"></i>
                SMS(<span id="smscount">0</span>)
            </a>
            <?php }else{ if($module_flag != '1'){ $menus ='disable_menu'; }else if(!in_array('SMS', $channel_license_flag)){ $menus ='disable_channel'; } ?>
                <li class="nav-item"><a class=" <?php echo $menus;?> nav-link">
                    <i class="fas fa-sms"></i>
                    SMS(<span id="smscount">0</span>)
                </a>
            <?php }?>
        </li> 
        <!-- to  not to give break option with telephony [vastvikta] [03-04-2025] -->
        <?php if(empty($VD_login)){?> 
        <li class="nav-item">
        
            <a class="dropdown-item nav-link " id="breakMenu">Break <span id="onBreakText"></span></a>
            <ul class="submenu dropdown-menu scrollable-menu" id="breakList">
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Tea-Break">Tea-Break</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Lunch-Break">Lunch-Break</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Restroom">Restroom</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Work-Break">Work-Break</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Bio-Break">Bio-Break</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Training">Training</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Coaching">Coaching</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Short-Training">Short-Training</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Briefing">Briefing</a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item nav-link break-option" value="Busy">Busy</a>
                </li>
            </ul>
        </li>
        <?php }?>

    <?} }?>

    </ul>
    <?php 
    $list = break_sched_start();
    if(!empty($list['schdule_start_time'])){?>
    <ul style="font-size: .875em;word-wrap: break-word;margin-top: -65px;margin-left: -23px;">
        <li><stronge style="color: #d63384;">Schedule</stronge> : <?php echo $list['schdule_start_time'];?> to <?php echo $list['schdule_end_time'];?> </li>
        <?php echo $list['list'];?>
    </ul>
    <?php }?>
    <!-- This logout block allow custmer -->
    <?php if($_SESSION['customer_view']==1){?>
        <hr>
        <ul class="nav">
            <li class="nav-item">
                <a href="#" class="d-flex align-items-center" id="dropdownUser1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../public/images/user-img/user-img.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong>
                        <span class="loggedname">
                        <?=$_SESSION['name']?><i class="fas fa-caret-up"></i></a>
                       </span>
                    </strong>
                </a>

                <ul class="submenu dropdown-menu" style="position: absolute;left: 0;top: -20px;min-width: 192px;font-size: 12px; display: none;">
                    <li class="nav-item"><a class="dropdown-item nav-link ico-logout" href="javascript:void(0);"
                    onclick="logoutcall('../web_logout_customer.php')">Log out</a></li>
                </ul>
            </li>
        </ul>
        <!-- This logout block allow for all -->
    <?}else{?>
        <hr>
        <ul class="nav">
            <li class="nav-item">
                <a href="#" class="d-flex align-items-center" id="dropdownUser1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../public/images/user-img.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
                     <span id="status-icon" class="status-available"></span>
                    <strong>
                        <span class="loggedname">
                        <?=$logged_name?><i class="fas fa-caret-up"></i></a>
                       </span>
                    </strong>
                </a>
                <ul class="submenu dropdown-menu" style="position: absolute;left: 0;top:-110px;min-width:192px;font-size: 12px;">
                    <?php 
                    $web_userdetailview = base64_encode('web_userdetailview');
                    ?>
                    <li class="nav-item"><a class="dropdown-item nav-link ico-setting" href="../web_changepass.php?userid=<?=$_SESSION['userid']?>&value=1">Settings</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="user_index.php?token=<?php echo $web_userdetailview;?>&id=<?=$_SESSION['userid']?>">Profile</a></li>
                    
                    <li class="nav-item">
                        <input type="hidden" id="userId" value="<?php echo $_SESSION['userid'];?>">
                        <a class="dropdown-item nav-link ico-settingstatus" href="javascript:void(0);" id="current-status">
                            <span id="current-status-icon checked_status" class="status-icon available"></span> Available
                        </a>
                        <ul class="submenu dropdown-menu" id="status-dropdown">
                            <li class="nav-item">
                                <div class="status-option" data-status="Available" data-statusId="online">
                                    <span class="status-icon available"></span> Available
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="status-option" data-status="Appears Away" data-statusId="offline">
                                    <span class="status-icon away"></span> Appear away
                                </div>
                            </li>
                        </ul>
                    </li>
                    <hr class="dropdown-divider">
                    </li>  
                    <?php if($groupid == '070000' && !empty($VD_login)){?> 
                    <?php }else{?>                 
                    <li class="nav-item logout"><a class="dropdown-item nav-link ico-logout" href="javascript:void(0);"
                    onclick="logoutcall('../web_logout.php')">Log out</a></li>
                    <?php }?>
                </ul>
            </li>
        </ul>
    <?}?>
</div>

<!-- Farhan Akhtar :: 29-01-2025 :: UI For Knowledge Base -->
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-0">
      <div class="modal-header">
        <!-- <h1 class="modal-title fs-5" id="exampleModalLabel">Knowledge Base</h1> -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <!-- <div class="mb-3">
            <textarea class="form-control" rows="3" placeholder="write here....!"></textarea>
        </div> -->
        <header class="headerNew">
            <div class="header-title">
                <h1>AI Assistant</h1>
                <div class="bot-status">
                    <div class="status-indicator"></div>
                    <span>Online</span>
                </div>
            </div>
            <div class="controls">
                <button class="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </header>

        <div class="chat-container" id="chatContainer">
            <!-- Messages will be added here -->
        </div>

        <div class="typing-indicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <div class="input-container">
            <div class="input-wrapper">
                <input type="text" class="message-input" placeholder="Type your message..." aria-label="Message input">
                <div class="action-buttons">
                    <!-- <button class="action-button" aria-label="Add attachment">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="action-button" aria-label="Voice input">
                        <i class="fas fa-microphone"></i>
                    </button> -->
                    <button class="send-button">
                        <span>Send</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm">Ask</button>
      </div> -->
    </div>
  </div>
</div>
    <!-- Include jQuery if not already added -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../public/js/ai-assistant.js"></script>

<script>
    //code for displaying the value of the count of messages i from the  get_notif_file for the notification 
$(document).ready(function() {
    // AJAX request to fetch notification counts
    $.ajax({
        url: "notification/get_notif_data.php", // Replace with the correct path to your PHP file
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                // Update HTML elements with the data
                $('#fbcount').text(response.info.fbcount);
                $('#mailcount').text(response.info.mailcount);
                $('#tweetcount').text(response.info.tweetcount);
                $('#chatcount').text(response.info.chatcount);
                $('#smscount').text(response.info.smscount);
                $('#whatsapp_count').text(response.info.whatsapp_count);
                $('#fbMcount').text(response.info.fbMcount);
            } else {
                console.log("Failed to retrieve data.");
            }
        },
        error: function() {
            console.log("Error in AJAX request.");
        }
    });
});
// Scope only to break otions under #breakList
const breakItems = document.querySelectorAll('#breakList .dropdown-item.nav-link');
const onBreakText = document.getElementById('onBreakText');
let breakStartTime = null;  // To capture break start time

// Function to activate the break and store in localStorage
function activateBreak(item) {
    const breakValue = item.getAttribute('value');
    breakStartTime = new Date().toISOString(); // Capture the start time of the break

    // Set item as active and change color (green for active)
    item.classList.add('active-break');
    onBreakText.innerHTML = ' (On Break: ' + breakValue + ')';

    // Disable other break items within #breakList
    breakItems.forEach(i => {
        if (i !== item) i.classList.add('disabled');
    });

    // Store the selected break and start time in localStorage
    localStorage.setItem('activeBreak', JSON.stringify({
        value: breakValue,
        startTime: breakStartTime
    }));

    console.log('Break activated: ', breakValue, breakStartTime);
}

// Function to deactivate the break and clear localStorage
function deactivateBreak(item) {
    const breakValue = item.getAttribute('value');
    const storedBreak = JSON.parse(localStorage.getItem('activeBreak'));

    if (storedBreak && storedBreak.startTime) {
        const breakEndTime = new Date().toISOString();  // Capture the end time of the break
        const breakStartTime = storedBreak.startTime;   // Retrieve stored start time

        console.log('Break deactivated:', breakValue, breakStartTime, breakEndTime);

        // Remove the active-break class and reset background color
        item.classList.remove('active-break');
        onBreakText.innerHTML = '';

        // Enable all break items
        breakItems.forEach(i => i.classList.remove('disabled'));

        // Clear localStorage for active break
        localStorage.removeItem('activeBreak');

        // Send break details to the server (via AJAX)
        sendBreakDetails(breakValue, breakStartTime, breakEndTime);
    }
}

// Helper function to format date and time to 'YYYY-MM-DD HH:MM:SS'
function formatDateTime(dateString) {
    const date = new Date(dateString);

    const year = date.getFullYear();
    const month = ('0' + (date.getMonth() + 1)).slice(-2); // Adding leading zero
    const day = ('0' + date.getDate()).slice(-2);          // Adding leading zero
    const hours = ('0' + date.getHours()).slice(-2);        // Adding leading zero
    const minutes = ('0' + date.getMinutes()).slice(-2);    // Adding leading zero
    const seconds = ('0' + date.getSeconds()).slice(-2);    // Adding leading zero

    // Return formatted date as 'YYYY-MM-DD HH:MM:SS'
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// Function to send the break details to the server
function sendBreakDetails(breakName, breakStartTime, breakEndTime) {
    if (breakEndTime) {
        const formattedStartTime = formatDateTime(breakStartTime);
        const formattedEndTime = formatDateTime(breakEndTime);

        console.log('Sending break details...');
        console.log('name:', breakName);
        console.log('start:', formattedStartTime);
        console.log('end:', formattedEndTime);

        $.ajax({
            url: '../function/classify_function.php',  
            type: 'POST',
            data: {
                logged_name: '<?=$logged_name?>',  
                break_name: breakName,
                break_start_time: formattedStartTime,
                break_end_time: formattedEndTime,
                action: 'break_details'
            },
            success: function(response) {
                console.log('Break details sent successfully:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error sending break details:', error);
            }
        });
    }
}

// Add event listeners to each break item
breakItems.forEach(item => {
    item.addEventListener('click', function () {
        if (this.classList.contains('active-break')) {
            deactivateBreak(this);
        } else {
            activateBreak(this);
        }
    });
});

// Check if there's an active break stored in localStorage on page load
const activeBreak = JSON.parse(localStorage.getItem('activeBreak'));
if (activeBreak) {
    breakItems.forEach(item => {
        if (item.getAttribute('value') === activeBreak.value) {
            activateBreak(item);
        }
    });
}




//old code 
    agent_login = '<?=$telephony_flag?>';
    if(agent_login == '1'){
         $('.logout').hide()
    }else{
        $('.logout').show()
    }

    /* When agent on Call it opens all tab in new window*/
    if (localStorage.getItem('AgentOnCall') == '1') {
        // Select all anchor tags with a specific class
        var anchors = document.querySelectorAll('.nav-link');
        // Iterate through the selected anchor tags
        anchors.forEach(function(anchor) {
            // Add the target="_blank" attribute
            anchor.setAttribute('target', '_blank');
        });
        console.log('###################### Agent On Call ###########################');
    } 

    var cnt=0;
    function logoutcall(url){
        cnt=parseInt(cnt)+parseInt(1);
        if(cnt=='1') {
            window.location.href=url;
        }
    }
</script>