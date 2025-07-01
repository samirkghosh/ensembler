<?php 
/***
 * Auth: Vastvikta Nishad
 * Date:  05 Mar  2024
 * Description: Display the Data Related to Different channels 
 * Mainly SMTP IMAP Twitter Facebook and SMS
 * 
*/
include_once('config_function.php');
$list = new OMNI_CLASS();
$list_data = $list->get_config_list();
$smtp_list_data = $list->get_smt_list();
$Imap_list_data = $list->get_imap_list();
$Twitter_list_data = $list->get_twitter_list();
$facebook_list_data = $list->get_facebook_list();
$whatsapp_list_data = $list->get_whatsapp_list();
$messenger_list_data = $list->get_messenger_list();
$instagram_list_data = $list->get_instagram_list();
$sms_list_data = $list->get_sms_list();
?>
<style>

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 12px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
<div class="col-sm-10 mt-3" style="padding-left:0">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Omnichannel Configuration Panel</span>
    <div class="style2-table">
        <div class="style-title2">
            <div class="row">
                <div class="col-sm-5">
                    <input type="button" class="button-orange add_channel" value="New" onClick="redirect();" target="_black">
                </div>
            </div>      
            <div class="table">
                <div class="wrapper21">
                    <div>
                        <table width="595" class="tableview tableview-2" style="display: none">
                            <tbody>
                            <tr class="background">
                                <td>Sr.No</td>
                                <td>Channel Name</td>
                                <td>Name</td>
                                <td>Server IP</td>
                                <td>UserID</td>
                                <td>Password</td>
                                <td>Status</td>
                                <td>Debug Status</td>
                                <td>Debug</td>
                                <td colspan="2">Action</td>
                            </tr>
                            <tr>
                            
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab">
              <button class="tablinks active" id="Smtp" onclick="openCity(event, 'SMTP')">SMTP</button>
              <button class="tablinks" id="Imap" onclick="openCity(event, 'IMAP')">IMAP</button>
              <button class="tablinks" id="Twitter" onclick="openCity(event, 'TWITTER')">TWITTER</button>
              <button class="tablinks" id="SMS" onclick="openCity(event, 'Sms')">SMS</button>
              <button class="tablinks" id="Facebook" onclick="openCity(event, 'FACEBOOK')">FACEBOOK</button>
              <button class="tablinks" id="Whatsapp" onclick="openCity(event, 'WHATSAPP')">WHATSAPP</button>
              <button class="tablinks" id="Messenger" onclick="openCity(event, 'MESSENGER')">MESSENGER</button>
              <button class="tablinks" id="Instagram" onclick="openCity(event, 'INSTAGRAM')">INSTAGRAM</button>
            </div>
            <div class="style-title tab" style="display:none">
                <h3>View SMTP Settings</h3>
                <h3>View IMAP Settings</h3>
                <h3>View Twitter Settings</h3>
                <h3>View SMS Settings</h3>
                <h3>View Facebook Settings</h3>
                <h3>View WhatsApp Settings</h3>
                <h3>View Messenger Settings</h3>
                <h3>View Instagram Settings</h3>
            </div>
            <div class="table tabcontent" id="SMTP">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">UserName</td>
                            <td align="left">Server</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                        <?php
                        $no=0;
                        while($config = mysqli_fetch_array($smtp_list_data)){
                        $no++;
                        ?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><?php echo $config['channel_name'];?></td>
                            <td><?php echo $config['v_username'];?></td>
                            <td><?php echo $config['v_server'];?></td>
                            <td><?php if($config['i_status'] == '1'){ echo 'Active';}else{ echo 'Inactive';}?></td>
                            <td><?php if($config['i_debug'] == '0'){ echo 'failed';}else if($config['i_debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td>
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');

                            $id = base64_encode($config['id']);
                            $channel = base64_encode($config['channel_name']);
                            ?>
                            <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                <img src="../public/images/edit-icon.png" border="0" alt="Edit"></a>
                                                            
                            <a href="javascript:void(0);" data-id="<?php echo $config['id'];?>" data-channel="<?php echo $config['channel_name'];?>" class="delete_channel"><img src="../public/images/delete-icon.png" border="0" alt="delete" ></a>

                            <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                               <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;">
                            </a>   
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="IMAP">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">UserName</td>
                            <td align="left">Server</td>
                            <td align="left">Type</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                        <?php
                        while($res = mysqli_fetch_array($Imap_list_data)){
                        $i++;
                        ?>
                        <tr>
                            <td align="left"><?=$i?></td>
                            <td align="left"><?=$res['v_connectionname']?></td>
                            <td align="left"><?=$res['v_username']?></td>
                            <td align="left"><?=$res['v_ipaddress']?></td>
                            <td align="left"><?=$res['v_type']?></td>
                            <td align="left"><?php if($res['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                            <td><?php if($res['v_debug'] == '0'){ echo 'failed';}else if($res['v_debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td>
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');


                            $id = base64_encode($res['I_ID']); 
                            $channel = base64_encode($res['v_connectionname']);
                            ?>

                            <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?=$channel?>" class="edit_channel">
                            <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                            </a>
                            <a href="javascript:void(0);" data-id="<?php echo $res['I_ID'];?>" data-channel="<?php echo $res['channel_name'];?>" class="delete_channel">
                                <img src="../public/images/delete-icon.png" border="0" alt="delete">
                            </a>
                            <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                             <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;">
                            </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="TWITTER">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <td align="left">Access Token</td>
                            <!-- <td align="left">Access Key</td> -->
                            <td align="left">Consumer Key</td>
                            <!-- <td align="left">Consumer Secret</td> -->
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <?php
                        while($Twitter = mysqli_fetch_array($Twitter_list_data)){
                        $t++;
                        ?>
                        <tr>
                            <td align="left"><?=$t?></td>
                            <td align="left"><?=$Twitter['channel_name']?></td>
                            <td align="left"><?=$Twitter['name']?></td>
                            <td align="left"><?=$Twitter['access_token_secret']?></td>
                            <!-- <td align="left"><?=$Twitter['access_token']?></td> -->
                            <td align="left"><?=$Twitter['consumer_key']?></td>
                            <!-- <td align="left"><?=$Twitter['consumer_secret']?></td> -->
                            <td align="left"><?php if($Twitter['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                            <td><?php if($Twitter['debug_status'] == '0'){ echo 'failed';}else if($Twitter['debug_status'] == '1'){ echo 'Passed';}else{ echo'-';}?></td>
                            <td>
                            <?php
                             $add_config = base64_encode('add_config');
                             $debug_config = base64_encode('debug_config');
                             $configuration = base64_encode('configuration');
 
                            $id = base64_encode($Twitter['id']);
                            $channel = base64_encode($Twitter['channel_name']);
                            ?>
                           <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                            </a>
                            <a href="javascript:void(0);" data-id="<?php echo $Twitter['id'];?>" data-channel="<?php echo $Twitter['channel_name'];?>" class="delete_channel">
                                <img src="../public/images/delete-icon.png" border="0" alt="delete">
                            </a>
                            <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                 <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;">
                            </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="Sms">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <!-- <td align="left">Sender Id</td> -->
                            <td align="left">Sms Type</td>
                            <td align="left">Api Key</td>
                            <td align="left">Client Key</td>
                            <td align="left">Prefix To SMS Number</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <tr>
                         <?php
                        while($sms_list = mysqli_fetch_array($sms_list_data)){
                        $s++;
                        ?>
                        <tr>
                            <td align="left"><?=$s?></td>
                            <td align="left"><?=$sms_list['channel_name']?></td>
                            <td align="left"><?=$sms_list['name']?></td>
                            <!-- <td align="left"><?=$sms_list['senderId']?></td> -->
                            <td align="left"><?=$sms_list['sms_type']?></td>
                            <td align="left"><?=$sms_list['apikey']?></td>
                            <td align="left"><?=$sms_list['clientId']?></td>
                            <td align="left"><?=$sms_list['v_prefix']?></td>
                            <td align="left"><?php if($sms_list['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                            <td><?php if($sms_list['debug_status'] == '0'){ echo 'failed';}else if($sms_list['debug_status'] == '1'){ echo 'Passed';}else{ echo'-';}?></td>
                            <td>
                            <?php
                             $add_config = base64_encode('add_config');
                             $debug_config = base64_encode('debug_config');
                             $configuration = base64_encode('configuration');
 
                            $id = base64_encode($sms_list['id']);
                            $channel = base64_encode($sms_list['channel_name']);
                            ?>
                            <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                            </a>
                            <a href="javascript:void(0);" data-id="<?php echo $sms_list['id'];?>" data-channel="<?php echo $sms_list['channel_name'];?>" class="delete_channel">
                                <img src="../public/images/delete-icon.png" border="0" alt="delete">
                            </a>
                            <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                 <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;">
                            </a>
                            </td>
                        </tr>
                        <?php }?>
                       </tr>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="FACEBOOK">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <td align="left">APP Id</td>
                            <td align="left">APP Token</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <?php
                        while($facebook = mysqli_fetch_array($facebook_list_data)){
                        $f++;
                        ?>
                        <tr>
                            <td align="left"><?=$f?></td>
                            <td align="left"><?=$facebook['channel_name']?></td>
                            <td align="left"><?=$facebook['name']?></td>
                            <td align="left"><?=$facebook['app_id']?></td>
                            <td align="left"><?=$facebook['app_token']?></td>
                            <td align="left"><?php if($facebook['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                            <td><?php if($facebook['debug'] == '0'){ echo 'failed';}else if($facebook['debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td>
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');

                            $id = base64_encode($facebook['id']);
                            $channel = base64_encode($facebook['channel_name']);
                            ?>
                                <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                    <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                                </a>
                                <a href="javascript:void(0);" data-id="<?php echo $facebook['id'];?>" data-channel="<?php echo $facebook['channel_name'];?>" class="delete_channel">
                                    <img src="../public/images/delete-icon.png" border="0" alt="delete">
                                </a>
                                <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                  <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;">
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="WHATSAPP">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <td align="left">APP Id</td>
                            <td align="left">APP Token</td>
                            <td align="left">Whatsapp URL</td>
                            <td align="left">STD</td>
                            <td align="left">Token Expire Date</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <?php
                        while($whatsapp = mysqli_fetch_array($whatsapp_list_data)){
                        $w++;
                        ?>
                        <tr>
                            <td align="left"><?=$w?></td>
                            <td align="left"><?=$whatsapp['channel_name']?></td>
                            <td align="left"><?=$whatsapp['name']?></td>
                            <td align="left">
                                <?= (strlen($whatsapp['app_id']) > 10) ? substr($whatsapp['app_id'], 0, 10) . '...' : $whatsapp['app_id']; ?>
                            </td>
                            <td align="left"><?=$whatsapp['app_token']?></td>
                            <td align="left"><?=$whatsapp['whatsapp_url']?></td>
                            <td align="left"><?=$whatsapp['STD']?></td>
                            <td align="left"><?=$whatsapp['token_expire_date']?></td>
                            <td align="left"><?php if($whatsapp['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                           <td><?php if($whatsapp['debug'] == '0'){ echo 'failed';}else if($whatsapp['debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td> 
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');

                            $id = base64_encode($whatsapp['id']);
                            $channel = base64_encode($whatsapp['channel_name']);
                            ?>
                                <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                    <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                                </a>
                                <a href="javascript:void(0);" data-id="<?php echo $whatsapp['id'];?>" data-channel="<?php echo $whatsapp['channel_name'];?>" class="delete_channel">
                                    <img src="../public/images/delete-icon.png" border="0" alt="delete">
                                </a>
                               <!-- <a href="omni_channel.php?token=<?php echo $debug_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                  <img src="../public/images/bugnew.png" border="0" alt="delete" style="width: 18px; height: 16px;"> -->
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="MESSENGER">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <td align="left">APP Id</td>
                            <td align="left">Access Token</td>
                            <td align="left">Facebook URL</td>
                            <td align="left">Token Expire Date</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <?php
                        while($messenger = mysqli_fetch_array($messenger_list_data)){
                        $m++;
                        ?>
                        <tr>
                            <td align="left"><?=$m?></td>
                            <td align="left"><?=$messenger['channel_name']?></td>
                            <td align="left"><?=$messenger['name']?></td>
                            <td align="left"><?=$messenger['app_id']?></td>
                            <td align="left" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"> <?=$messenger['access_token']?></td>
                            <td align="left"><?=$messenger['facebook_url']?></td>
                            <td align="left"><?=$messenger['token_expire_date']?></td>
                            <td align="left"><?php if($messenger['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                           <td><?php if($messenger['debug'] == '0'){ echo 'failed';}else if($messenger['debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td> 
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');

                            $id = base64_encode($messenger['id']);
                            $channel = base64_encode($messenger['channel_name']);
                            ?>
                                <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                    <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                                </a>
                                <a href="javascript:void(0);" data-id="<?php echo $messenger['id'];?>" data-channel="<?php echo $messenger['channel_name'];?>" class="delete_channel">
                                    <img src="../public/images/delete-icon.png" border="0" alt="delete">
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="table tabcontent" id="INSTAGRAM">
                <table class="tableview tableview-2">
                    <tbody>
                        <tr class="background">
                            <td align="left">S.No.</td>
                            <td align="left">Channel Type</td>
                            <td align="left">Channel Name</td>
                            <td align="left">APP Id</td>
                            <td align="left">Access Token</td>
                            <td align="left">Facebook URL</td>
                            <td align="left">Token Expire Date</td>
                            <td align="left">Status</td>
                            <td align="left">Debug Status</td>
                            <td align="left">Modify</td>
                        </tr>
                       <?php
                        while($instagram = mysqli_fetch_array($instagram_list_data)){
                        $I++;
                        ?>
                        <tr>
                            <td align="left"><?=$I?></td>
                            <td align="left"><?=$instagram['channel_name']?></td>
                            <td align="left"><?=$instagram['name']?></td>
                            <td align="left"><?=$instagram['app_id']?></td>
                            <td align="left" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"> <?=$instagram['access_token']?></td>
                            <td align="left"><?=$instagram['instagram_url']?></td>
                            <td align="left"><?=$instagram['token_expire_date']?></td>
                            <td align="left"><?php if($instagram['status'] == '1'){ echo'Active';}else{echo'Inactive';} ?></td>
                           <td><?php if($instagram['debug'] == '0'){ echo 'failed';}else if($instagram['debug'] == '1'){ echo 'Passed';}else{ echo'-';}?></td> 
                            <td>
                            <?php
                            $add_config = base64_encode('add_config');
                            $debug_config = base64_encode('debug_config');
                            $configuration = base64_encode('configuration');

                            $id = base64_encode($instagram['id']);
                            $channel = base64_encode($instagram['channel_name']);
                            ?>
                                <a href="omni_channel.php?token=<?php echo $add_config?>&id=<?= $id ?>&channel=<?= $channel?>" class="edit_channel">
                                    <img src="../public/images/edit-icon.png" border="0" alt="Edit"><i class="fas fa-debug"></i>
                                </a>
                                <a href="javascript:void(0);" data-id="<?php echo $instagram['id'];?>" data-channel="<?php echo $instagram['channel_name'];?>" class="delete_channel">
                                    <img src="../public/images/delete-icon.png" border="0" alt="delete">
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
    <!--uncaught refrenece  $ error was showing so this file was added Auth: Vastvikta Nishad Date : 05/03/2024  -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$('#SMTP').show();
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
function redirect(id){
    var encodedToken = btoa($('.tab').find('.active').attr('id'));
    var Token = btoa('add_config');
    window.location.href='omni_channel.php?token='+encodeURIComponent(Token)+'?&channel='+encodeURIComponent(encodedToken );
}

</script>
