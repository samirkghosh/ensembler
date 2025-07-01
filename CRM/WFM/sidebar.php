<?php 
include("head.php");
include_once("../function/classify_function.php");
if(isset($_SESSION['AccountNumber'])){
}else if(isset($_SESSION['SNo']) && isset($_SESSION['login_email'])){
}else{
    header("Location:../web_logout.php"); 
}
$uid = $_SESSION['AccountNumber'];

/***Check menu access proveded or not****/
$classify =classify_agent($_SESSION['userid']);

if($classify== 0){
    $disable = 'disabled';
}
/**End**/
$groupid='0000';

?>
<!-- Popup Container -->
<div class="popup-container" id="popupContainer">
    <div class="popup-content">
        <span class="closebtn" id="closePopup">&times;</span>
        <p><strong>Info!</strong> licence not available for this module.</p>
    </div>  
</div>
<div class="d-flex flex-column flex-shrink-0 p-3 text-dark bg-dark sidebar" >
    <a href="#">
        <span class="fs-4">
            <img class="vision2" src="<?=$SiteURL?>public/images/<?=$dbheadlogo?>" style="width: 180px;height: 100px;background:#fff"> 
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
    <?}?>
        <!-- Admin Menu Options -->
    <?php if(($groupid=='0000') || ($groupid=='080000') || ($groupid=='070000') ($groupid=='060000') || ($groupid=='050000')){?>
        <?php

         if(($groupid=='0000') || ($groupid=='080000')){
            $web_admin_dashboard = base64_encode('web_admin_dashboard');
            ?>
        <li class="nav-item">
            <a href="dashboard_index.php?token=<?php echo $web_admin_dashboard;?>" class="nav-link <? if($selecttab==2){echo " active";}?>"
                aria-current="page">
                <i class="fas fa-chart-bar"></i>
                Dashboard
            </a>
        </li>
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
        <?php $knowledge_base = base64_encode('knowledge_base');?>
        <li class="nav-item">
        <a href="web_faq_view.php?token=<?php echo $knowledge_base;?>" class="nav-link <? if($selecttab==4){echo " active";}?>" >
                <i class="fa fa-users"></i>
                Knowledge base
            </a>
        </li>
        <?php $web_agent = base64_encode('web_agent');?>
        <li class="nav-item">
            <a href="report_index.php?token=<?php echo $web_agent;?>" class="nav-link  <? if($selecttab==5){echo "  active";}?>">
            <i class="fas fa-file-excel"></i>
                Reports
            </a>
        </li>
        <?php if(($groupid=='0000') || ($groupid=='080000')){
            $view_category = base64_encode('view_category');
            $Bulletin = base64_encode('Bulletin');
            $Licence = base64_encode('Licence');
            $web_userhome = base64_encode('web_userhome');
        ?>
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
                        <a class="disable_menu dropdown-item nav-link" href="admin_index.php">Bulletin</a>
                    <?php }?>
                </li>
            </ul>
           
        <?php }?>
        <!-- Omnichannel,WFM,voicemail,misscall access allow for admin,superwiser,agent -->
        <?php if(($groupid=='0000') || ($groupid=='080000') || ($groupid=='070000')){?>
            
            <li class="nav-item">
                <a href="index.php" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                <i class="fab fa-stack-overflow"></i>
                    Omnichannel <i class="fas fa-caret-right"></i>
                </a>
                <?php
                 $omni_index = base64_encode('configuration');
                 $email_complaint = base64_encode('email_complaint');
                 $email_enquiry = base64_encode('email_enquiry');
                 $twitter = base64_encode('twitter');
                 $sms = base64_encode('sms');
                 $whatsapp = base64_encode('whatsapp');
                 $facebook = base64_encode('facebook');
                 $live_chat = base64_encode('live_chat');
                 $chat = base64_encode('chat');
                 $bulk_sms = base64_encode('bulk_sms');
                 $web_bulksms = base64_encode('web_bulksms');

                 $bulk_email_report = base64_encode('bulk_email_report');
                 $bulk_sms_report = base64_encode('bulk_sms_report');
                 $bulk_whatsapp_report = base64_encode('bulk_whatsapp_report');
                 $queue_report = base64_encode('queue_report');
                 $bad_report = base64_encode('bad_report');
                ?>
                <ul class="submenu dropdown-menu">                   
                    <?php if($groupid == '0000'){?><li class="nav-item"><a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $omni_index?>">Configuration</a></li><?php }?>
                    <li class="nav-item">
                        <a href="admin-twitter_requests1.php" class="nav-link  <? if($selecttab==8){echo " active";}?>">
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
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $web_bulksms?>&campaign=Email">Bulk Campaign </a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulk Campaign </a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('Email');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_email_report?>">Compose Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                <?php }?>
                            </li>
                        </ul>           
                    </li>
                    <li class="nav-item">
                    <?php $module_flag = module_license('Twitter');
                    if($module_flag == '1'){?>
                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $twitter?>" >Twitter</a>
                    <?php }else{ ?>
                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);" >Twitter</a>
                    <?php }?>
                    </li>
                    <li class="nav-item">
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
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $web_bulksms?>&campaign=SMS">Bulk Campaign </a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Bulk Campaign </a>
                                <?php }?>
                            </li>
                            <li class="nav-item">
                                <?php $module_flag = module_license('SMS');
                                if($module_flag == '1'){?>
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $bulk_sms_report?>">Compose Report</a>
                                <?php }else{ ?>
                                    <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Compose Report</a>
                                <?php }?>
                            </li>
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
                    </li>
                    <li class="nav-item">
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
                                    <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $web_bulksms?>&campaign=WhatsApp">Bulk Campaign </a>
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
                    </li>
                    <li class="nav-item">
                    <?php $module_flag = module_license('Facebook');
                    if($module_flag == '1'){?>
                        <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $facebook?>">Facebook</a>
                    <?php }else{ ?>
                        <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Facebook</a>
                    <?php } ?>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link  <? if($selecttab==8){echo " active";}?>">
                        <i class="fas fa-comments"></i>
                            Chat <i class="fas fa-caret-right"></i>
                        </a>
                        <ul class="submenu dropdown-menu">
                            <li class="nav-item">
                            <?php $module_flag = module_license('Chat');
                            if($module_flag == '1'){?>
                                 <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $live_chat?>">Live Chat</a>
                            <?php }else{ ?>
                                <a class="disable_menu dropdown-item nav-link" href="javascript:void(0);">Live Chat</a>
                            <?php }?>
                            </li>
                            <li class="nav-item">
                            <?php $module_chat = module_license('Chat');
                            if($module_chat == '1'){?>
                                <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $chat?>" >Chat</a>
                            <?php }else{ ?>
                                <a class=" disable_menu dropdown-item nav-link" href="javascript:void(0);" >Chat</a>
                            <?php }?>
                            </li>
                        </ul>
                    </li>
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
                    <li class="nav-item"><a class="dropdown-item nav-link" href="WFM/wfm_reports.php?token=<?php echo $shift_assignment_report?>">WFM Reports</a>    
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
            $VoiceMail_report = base64_encode('VoiceMail_report');
            // Checking module license for VoiceMail_report
            $module_flag = module_license('VoiceMail_report');
            if($module_flag == '1'){
                // If module license is valid, display VoiceMail_report menu item?>
                <li class="nav-item">
                    <a class="dropdown-item nav-link" href="VoiceMail_index.php?token=<?php echo $VoiceMail_report?>">
                        <i class='fa fa-voicemail'></i> VoiceMail</a>
                </li>
               
            <?php }?>
           <?php $misscall_report = base64_encode('misscall_report'); ?>
           <!-- Always display Misscall menu item -->
            <li class="nav-item">
                    <a class="dropdown-item nav-link" href="VoiceMail_index.php?token=<?php echo $misscall_report?>">
                <i class='fa fa-phone'></i>Misscall <span id="misscall"></span>
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
            <?php $module_flag = module_license('Facebook');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $facebook?>">
                <i class="fab fa-facebook"></i>
                Facebook(<span id="fbcount">0</span>)
            </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                    <i class="fab fa-facebook"></i>
                    Facebook(<span id="fbcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('Email');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $email_complaint?>" >
                <i class="fa fa-envelope"></i>
                Mail(<span id="mailcount">0</span>)
            </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                    <i class="fa fa-envelope"></i>
                    Mail(<span id="mailcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('Twitter');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $twitter?>">
            <img src = "../public/images/x-twitter.svg" alt="X" style="width:10px">
                (<span id="tweetcount">0</span>)
            </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                <img src = "../public/images/x-twitter.svg" alt="X" style="width:10px">
                    (<span id="tweetcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('Chat');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $live_chat?>">
                <i class="fas fa-comments"></i>
                Chat(<span id="chatcount">0</span>)
            </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                    <i class="fas fa-comments"></i>
                    Chat(<span id="chatcount">0</span>)
                </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('WhatsApp');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link" href="omni_channel.php?token=<?php echo $whatsapp?>">
                    <i class="fab fa-whatsapp" style="color: #000000;"></i>
                    WHATSAPP(<span id="#">0</span>)
                </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                <i class="fab fa-whatsapp" style="color: #000000;"></i>
                WHATSAPP(<span id="#">0</span>)
            </a>
            <?php }?>
        </li>
        <li class="nav-item">
            <?php $module_flag = module_license('SMS');
            if($module_flag == '1'){?>
            <a class="dropdown-item nav-link <?=$disable?>" href="omni_channel.php?token=<?php echo $sms?>" >
                <i class="fas fa-sms"></i>
                SMS(<span id="smscount">0</span>)
            </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" class="disable_menu nav-link">
                    <i class="fas fa-sms"></i>
                    SMS(<span id="smscount">0</span>)
                </a>
            <?php }?>
        </li>
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
                    <img src="images/user-img/user-img.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
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
                    <strong>
                        <span class="loggedname">
                        <?=$logged_name?><i class="fas fa-caret-up"></i></a>
                       </span>
                    </strong>
                </a>
                <ul class="submenu dropdown-menu" style="position: absolute;left: 0;top:-110px;min-width:192px;font-size: 12px;">
                    <li class="nav-item"><a class="dropdown-item nav-link ico-setting"
                    href="../web_changepass.php?userid=<?=$_SESSION['userid']?>&value=1">Settings</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="web_userdetailview.php?id=<?=$_SESSION['userid']?>">Profile</a></li>
                    <li class="nav-item">
                    <hr class="dropdown-divider">
                    </li>
                    <li class="nav-item"><a class="dropdown-item nav-link ico-logout" href="javascript:void(0);"
                    onclick="logoutcall('../web_logout.php')">Log out</a></li>
                </ul>
            </li>
        </ul>
    <?}?>
</div>
<?php
// include("../include/web_mysqlconnect.php");

// if(($groupid=='080000') || ($groupid=='0000')){ $class='class="Reports-page-right"'; }
// else { $class=''; }

if($groupid=='060000' || $groupid=='080000'){
?>
<script type="text/javascript">
    // var idleMax = 10; // Logout after 10 minutes of IDLE
    // var idleTime = 0;
    // var idleInterval = setInterval("timerIncrement()", 60000);  // 1 minute interval    
    // $( "body" ).mousemove(function( event ) {
    //     idleTime = 0; // reset to zero
    // });
    // // count minutes
    // function timerIncrement() {
    //     console.log("idleTime --- "+idleTime);
    //     console.log("idleMax --- "+idleMax);
    //     idleTime = idleTime + 1;
    //     if (idleTime > idleMax){
    //         console.log('LogOut'); 
    //         window.location="../web_logout.php";
    //     }
    // }
</script>
<?php } ?>
<script type="text/javascript">
    /* When agent on Call it opens all tab in new window*/
    if (localStorage.getItem('AgentOnCall') == '1') 
    {
        // Select all anchor tags with a specific class
        var anchors = document.querySelectorAll('.nav-link');
        // Iterate through the selected anchor tags
        anchors.forEach(function(anchor) {
            // Add the target="_blank" attribute
            anchor.setAttribute('target', '_blank');
        });
        console.log('Agent On Call');
    } 
    var cnt=0;
    function logoutcall(url)
    {
        cnt=parseInt(cnt)+parseInt(1);
        if(cnt=='1')
        {
            window.location.href=url;
        }
    }
</script>