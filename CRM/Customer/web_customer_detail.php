<?php
/**
 * Customer Page
 * Author: Ritu modi
 * Date: 19-02-2024
 * 
 * This page is used to display customer details and related information. It decodes the customer ID received through the GET request, fetches customer data based on the ID, and displays it on the page. It also allows for updating customer information and viewing associated case information and documents.
 */
// Decode the customer ID received through GET request
   $customerid =  base64_decode($_GET['CustomerID']);
   //echo $customerid; die;
   // Get the current user ID from session
   $vuserid     =   $_SESSION['userid'];
  
// Fetch customer data based on the customer ID
   $resonse = fetch_customer_data($customerid);
   $res = mysqli_fetch_assoc($resonse);
  // Extract individual data fields from the fetched customer data
    $phone =    $res['phone'];
    $mobile=    $res['mobile'];
    $name =  explode(' ', $res['fname']);// Split fname into an array of two values seperated by space.
    $first_name     = $name[0];
    $last_name      = $name[1];
    $email          =$res['email'];
    $gender          =$res['gender'];
    $age          =$res['age_grp'];
    $address_1      = $res['address'];
    $address_2      = $res['v_Location'];
    $district       = $res['district'];
    $v_Village       = $res['v_Village'];
    $languagename   = $res['language'];
    $fbhandle       = $res['fbhandle'];
    $twitterhandle  = $res['twitterhandle'];
    $type= $res['type'];
    $business_number = $res['business_number'];
    $register_tpin = $res['tpin'];
    $passport_number = $res['passport_number'];
    $area = $res['area'];
    $town = $res['town'];
    $whatsapphandle = $res['whatsapphandle'];
    $instagramhandle = $res['instagramhandle'];//[vastvikta nishad][29-11-2024]
    $messengerhandle = $res['messengerhandle'];//[vastvikta nishad][29-11-2024]
    $sms_number = $res['smshandle'];
    $nationality= $res['nationality'];
    $companyname= $res['company_name'];
    $company_registration= $res['company_registration'];
    $regional= $res['regional'];
    $customertype= $res['customertype'];
    $final_action = "Customer Detial View of Customer ID $customerid by $name User";
  //  add_audit_log($vuserid, 'customer_detial_view ', $customerid, 'Show Customer Detail', $db, $final_action);

    $selecttab=3;
    ?>
<style type="text/css">
  .content__history {
    display: flex;
    flex-direction: column;
    height: calc(100% - 18px - 3em);
    /*overflow: auto;*/
    /*overflow-x: hidden;*/
    font-size: 13px;
}
.content__history__entry__content__data {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid rgba(123,123,123,.3);
    padding: .8em 0;
    margin-right: .8em;
    align-items: center;
    border-left: 1px solid rgba(123,123,123,.3);
}
.content__history__entry__header {
  font-size: inherit;
    font-weight: 500;
    height: 47px;
    text-transform: uppercase;
    border: #004b8b82 2px solid;
    border-bottom: none;
    border-right: none;
    /* display: inline-block; */
    /* float: left; */
    margin-bottom: unset;
    border-top-left-radius: 25px;
    padding: 13px 15px;
    width: 100%;
    background-color: #f5f5f5;
    display: flex;
    justify-content: space-between;
    /*margin-bottom: 1em;*/
    /*font-size: 16px;*/
    /*font-weight: 700;*/
}
.content__entry__left{
  margin-left: 10px;
}
.content__history__entry {
    padding: 1em;
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    background-color: #fff;
    /*height: calc(30% - 40px);*/
    height: calc(92% - 40px);
    margin-bottom: 1em;
        /*margin-bottom: -126px;*/

}
.content__history__entry__content {
    display: flex;
    flex-direction: column;
    /*height: calc(100% - 24px - -16em);*/
    overflow: auto;
    overflow-x: hidden;
    font-size: 13px;
    margin-bottom: -126px;
    height: 90%;
}
.text-with-dots::after {
    content: ".";
}
</style>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row" style="min-height:88vh">
            <div class="rightpanels">
                <div class="style2-table">        
                    <div class="table"> 
                        <div class="row">
                            <?php if(empty($customerid)){?>
                            <div class="col-sm-12"> 
                                <?php }else{?>   
                                    <div class="col-sm-8">
                                        <?php }?>                          
                                <div class="old-customer-simple-table" id="formContainer">
                                    <?php if(empty(!$customerid)){?>
                                        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Customer Profile</span>
                                    <?php }else{?>
                                        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">New Customer Profile</span>
                                    <?php }?>
                                    <form name="frmviewcustomer" id="frmviewcustomer" method="post" novalidate="novalidate">
                                        <input type="hidden" class="upadate_data" value="upadate_data" name="action">
                                        <table class="tableview tableview-2 main-form new-customer">
                                        <?php if(!empty($customerid)){?>
                                            <tbody>
                                                <tr class="">
                                                    <td height="44" colspan="2" align="left">
                                                        <div class="log-case">
                                                            <input name="customerid" id="customerid" type="hidden" value="<?=$customerid?>" class="input-style1" readonly="">
                                                        </div>
                                                    </td>
                                                    <td height="44" colspan="2" align="right">
                                                   <!-- <input name="customerid" id="customerid" type="submit" value="Edit"
                                                        class="button-orange1 float-right"> -->
                                                        <button type='button' id='editBtn' class="button-orange1 float-right" >Edit</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <?php }?>
                                        </table>
                                        <!-- changed layout of the form[vastvikta]][14-05-2025] -->
                                        <table class="tableview tableview-2 main-form new-customer">
                                            <tbody>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Registered Mobile Number<em>*</em></label>
                                                        <div class="log-case">
                                                            <input name="phone" id="phone" type="text" maxlength="12" value="<?=$phone?>" class="input-style1" oninput="validateNumericInput(this)" style="pointer-events: none;width:150px;">
                                                        </div>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Alternate Mobile Number</label>
                                                        <div class="log-case">
                                                            <input name="mobile" id="mobile" type="text" maxlength="12" value="<?=$mobile?>" class="input-style1" oninput="validateNumericInput(this)" readonly style="width:150px;">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Company Name</label>
                                                        <div class="log-case">
                                                            <input name="companyname" id="companyname" type="text" value="<?=$companyname?>" class="input-style1" readonly style="width:150px;">
                                                        </div>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Company Registration No.</label>
                                                        <input name="company_registration" id="company_registration" type="text" value="<?=$company_registration?>" class="input-style1" readonly style="width:150px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-right" width="40%">
                                                        <label>First Name<em>*</em></label>
                                                        <div class="log-case">
                                                            <?php $first_name = str_replace("singlequote", "'", $first_name); ?>
                                                            <input name="first_name" id="first_name" type="text" value="<?=$first_name?>" class="input-style1" readonly onkeypress="return isAlphabetKey(event)" style="width:150px;">
                                                        </div>
                                                    </td>
                                                    <td class="left boder0-right" width="40%">
                                                        <label>Last Name</label>
                                                        <?php $last_name = str_replace("singlequote", "'", $last_name); ?>
                                                        <div class="log-case">
                                                            <input name="last_name" id="last_name" type="text" value="<?=$last_name?>" class="input-style1" readonly onkeypress="return isAlphabetKey(event)" style="width:150px;">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Priority Customer<em>*</em></label>
                                                        <div class="log-case">
                                                            <span class="slug">
                                                                <input type="radio" name="priority" value="1" disabled <?= $priority == '1' ? 'checked' : '' ?>> Priority
                                                            </span>
                                                            <span class="slug">
                                                                <input type="radio" name="priority" value="0" disabled <?= $priority == '0' ? 'checked' : '' ?>> Non Priority
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>County</label>
                                                        <div class="log-case">
                                                            <select name="district" id="district" class="select-styl1" style="width:150px;" disabled>
                                                                <option value="">Select County</option>
                                                                <?php
                                                                    $sourceresult = city_list();
                                                                    while ($row = mysqli_fetch_array($sourceresult)) {
                                                                        $SubI = $row['id'];
                                                                        $subC = $row['city'];
                                                                        $sel = ($SubI == $district) ? 'selected' : '';
                                                                        echo "<option value='$SubI' $sel>$subC</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left border0-left" width="40%">
                                                        <label>Sub County</label>
                                                        <select name="village" id="village" class="select-styl1" disabled style="width:150px;">
                                                            <option value="">Select Sub County</option>
                                                            <?php
                                                                if (mysqli_num_rows($villages_query) > 0) {
                                                                    while ($villages_res = mysqli_fetch_array($villages_query)) {
                                                                        $selected = $villages_res['id'] == $v_Village ? "selected" : "";
                                                                        echo "<option value='{$villages_res['id']}' $selected>{$villages_res['vVillage']}</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Nationality</label>
                                                        <div class="log-case">
                                                            <input name="nationality" id="nationality" type="text" value="<?=$nationality?>" class="input-style1" readonly onkeypress="return isAlphabetKey(event)" style="width:150px;">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Gender<em>*</em></label>
                                                        <select name="gender" id="gender" class="select-styl1" style="width:150px" disabled>
                                                            <option value="0">Select Gender</option>
                                                            <?php
                                                                $gender_query = web_gender();
                                                                while ($gender_res = mysqli_fetch_array($gender_query)) {
                                                                    $selected = $gender_res['value'] == $gender ? "selected" : "";
                                                                    echo "<option value='{$gender_res['value']}' $selected>{$gender_res['name']}</option>";
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Email</label>
                                                        <input type="email" name="email" id="email" value="<?=$email?>" class="input-style1" disabled style="width:150px;">
                                                        <a href="javascript:void(0)" class="emailConMess">
                                                            <svg viewBox="0 0 48 48" width="20px" xmlns="http://www.w3.org/2000/svg">...</svg>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Facebook Handle</label>
                                                        <input type="text" name="fbhandle" id="fbhandle" value="<?=$fbhandle?>" class="input-style1" disabled style="width:150px;" >
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label><img src="../public/images/x-twitter.svg" alt="X" style="width:15px;height:15px;"> Handle</label>
                                                        <input type="text" name="twitterhandle" id="twitterhandle" value="<?=$twitterhandle?>" class="input-style1" disabled style="width:150px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>SMS Number</label>
                                                        <input type="text" name="smshandle" id="smshandle" value="<?=$sms_number?>" class="input-style1" oninput="validateNumericInput(this)" disabled style="width:150px;">
                                                        <a href="javascript:void(0)" class="smsConMess">
                                                            <img src="../public/images/chat.png" width="20" title="Reply">
                                                        </a>
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>WhatsApp Number</label>
                                                        <input type="text" name="whatsapphandle" id="whatsapphandle" value="<?=$whatsapphandle?>" class="input-style1" oninput="validateNumericInput(this)" disabled style="width:150px;">
                                                        <a href="javascript:void(0)" class="whatsappConMess">
                                                            <img src="../public/images/whatsapp.png" width="20" title="Reply">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Address</label>
                                                        <input type="text" name="address_1" id="address_1" value="<?=$address_1?>" class="input-style1" disabled style="width:150px;">
                                                    </td>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Instagram ID</label>
                                                        <input type="text" name="instagramhandle" id="instagramhandle" value="<?=$instagramhandle?>" class="input-style1" disabled style="width:150px;">
                                                        <a href="javascript:void(0)" class="instagramConMess">
                                                            <img src="../public/images/insta.png" width="20" title="Reply">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="left boder0-left" width="40%">
                                                        <label>Facebook Messenger ID</label>
                                                        <input type="text" name="messengerhandle" id="messengerhandle" value="<?=$messengerhandle?>" class="input-style1" readonly style="width:150px;">
                                                        <a href="javascript:void(0)" class="fbmessConMess">
                                                            <img src="../public/images/messenger_send.png" width="20" title="Reply">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php if (!empty($customerid)) { ?>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        <button type="button" id="updateBtn" class="button-orange1" style="display:none;">Update</button>
                                                        <button type="button" id="cancelBtn" class="button-orange1" style="display:none;">Cancel</button>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                <?php if (empty($customerid)) { ?>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        <button type="button" id="insertBtn" class="button-orange1">Insert</button>
                                                        <?php $web_customer_detail = base64_encode('web_consumer_home'); ?>
                                                        <a id="backLink" href="customer_index.php?token=<?=$web_customer_detail?>" style="display:none;"></a>
                                                        <button type="button" id="backBtn" class="button-orange1">Back</button>
                                                        <script>
                                                            document.getElementById("backBtn").addEventListener("click", function () {
                                                                document.getElementById("backLink").click();
                                                            });
                                                        </script>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                            <?php if(!empty($customerid)){?>
                            <!-- Developer:Aarti
                           CreateDtae : 27-10-23
                           For: Get all social medio details -->
                           <div class="col-sm-4">  
                             <div class="interaction_history content__history__entry" style="height: 429px;">
                                <div class="content__history__entry__header">
                                    <span  style="background:#f5f5f5">
                                    <span class="float:left"><b>Interaction History</b></span> 
                                    <span class="float:right"></span> 
                                  </span>   
                                </div>
                                <div class="content__history__entry__content">
                                <?php
                            // Initialize conditions array
                            $conditions = [];

                            if (!empty($customerid)) {
                                $conditions[] = "customer_id = '" . $customerid . "'";
                            }
                            
                            if (!empty($email)) {
                                $conditions[] = "email = '" . $email . "'";
                            }
                            
                            if (!empty($phone)) {
                                $conditions[] = "mobile = '" . $phone . "'";
                            }
                            
                            // Combine the OR conditions
                            $orClause = implode(' OR ', $conditions);
                            
                            // Start the query string
                            $qdk = "SELECT * FROM $db.interaction";
                            
                            // Build WHERE clause
                            if (!empty($orClause)) {
                                $qdk .= " WHERE ($orClause) AND `remarks` != ''";
                            } else {
                                $qdk .= " WHERE `remarks` != ''";
                            }
                            
                            
                            // Add ORDER BY clause
                            $qdk .= " ORDER BY created_date DESC";
                            
                            // Output the query for debugging
                            

                                    $ress = mysqli_query($link, $qdk);
                                    $numesms = mysqli_num_rows($ress);
                                    while ($row = mysqli_fetch_array($ress)) {
                                        $email = $row['email'];
                                        $remarks = $row['remarks'];
                                        $intraction_type = $row['intraction_type'];
                                        $created_date = $row['created_date'];    
                                        $dates = date("Y-m-d",strtotime($created_date));
                                        //   changed the time format to 24 hours [vastvikta][11-03-2025]
                                        $times = date('H:i:s', strtotime($created_date));
                                        $interact_id = $row['interact_id']; 
                                        $ID = $row['interact_id'];
                                        $page_link = '';

                                        if($intraction_type == 'email'){
                                            $email_complaint = base64_encode('email_complaint');
                                            $page_link = 'omni_channel.php?token='.$email_complaint;

                                        }else if($intraction_type == 'webchat'){
                                            $chat = base64_encode('chat');
                                            $page_link = 'omni_channel.php?token='.$chat;

                                        }else if($intraction_type == 'twitter'){
                                            $twitter = base64_encode('twitter');
                                            $page_link = 'omni_channel.php?token='.$twitter;

                                        }else if($intraction_type == 'facebook'){
                                            $facebook = base64_encode('facebook');
                                            $page_link = 'omni_channel.php?token='.$facebook;

                                        }else if($intraction_type == 'voicecall'){
                                            //[vastvikta][02-12-2024] updated the code so that the link doesn't open  for voicecall
                                            $page_link = 'javascript:void(0)';
                                            $filename = $row['filename'];
                                            $org_filename = getFileName($filename);
                                            $filenamed = "http://" . $ip . $org_filename;

                                        }else if($intraction_type == 'SMS'){
                                            $sms = base64_encode('sms');
                                            $page_link = 'omni_channel.php?token='.$sms;

                                        }else if($intraction_type == 'Whatsapp'){
                                            $whatsapp = base64_encode('whatsapp');
                                            $page_link = 'omni_channel.php?token='.$whatsapp;
                                        }
                                ?>
                                <!-- <div class="content__history"> -->
                                <div class="content__history__entry__content__data">
                                    <div class="content__entry__left">
                                        <div class="content__history__entry__content__entry__heading">
                                            <div><b><?php echo $dates; ?> | <?php echo $times; ?></b></div>
                                            <span>
                                                <!-- ADDED code for interaction reply in and out [vastvikta][14-04-2025] -->
                                            <b><?php if ($intraction_type == 'email') { ?> <?php if($row['type'] == 'IN'){?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                            <? }else{ ?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                                <?php }?>Subject : <?php } ?></b>
                                            </span>
                                            <?php if ($intraction_type == 'email') {
                                            if($row['type'] == 'IN'){?>

                                             <a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/subjectpopup.php?id=<?=$ID?>','_blank','height=550,width=900,scrollbars=0')" class="cptext text-with-dots"style="color: #0d6efd;" >
                                               <?php echo $remarks; ?></a>
                                             <?php }else{?>                                      
                                                <a href="javascript:void(0)" onClick="JavaScript:window.open('helpdesk/web_email_dess.php?id=<?=$ID?>&iid=<?=$ID?>&type=out','_blank','height=550,width=900,scrollbars=0')" class="cptext text-with-dots" style="color: #0d6efd;" >
                                               <?php echo $remarks; ?></a>
                                            </a>

                                             <?php }?>
                                            <?php } else if ($row['intraction_type'] == 'Whatsapp') {
                                                if($row['type'] == 'OUT'){
                                                    $sql = "SELECT * FROM $db.whatsapp_out_queue where id='$ID'";
                                                }else{
                                                    $sql = "SELECT * FROM $db.whatsapp_in_queue where id='$ID'";
                                                }
                                               $totalQuery = mysqli_query($link, $sql);
                                               $rowwhats = mysqli_fetch_assoc($totalQuery);
                                               
                                               $send_from= $rowwhats['send_from'];
                                               
                                               $send_to= $rowwhats['send_to'];
                                               $messageid = $rowwhats['id'];
                                               ?>
                                               <?php if($row['type'] == 'IN'){?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                                <a href="omnichannel_config/web_sent_whatsapp.php?i_WhatsAppID=<?=$ID?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                            <? }else{ ?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                                <a href="omnichannel_config/web_sent_whatsapp.php?i_WhatsAppID=<?=$ID?>&send_to=<?=$send_to?>&send_from=<?=$send_from?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_from?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                               
                                            <?php }?>
                                        
                                               <?php echo $remarks; ?></a>

                                            <?php } else if ($row['intraction_type'] == 'instagram') {

                                                if($row['type'] == 'OUT'){
                                                    $sql = "SELECT * FROM $db.instagram_out_queue where id='$ID'";
                                                }else{
                                                    $sql = "SELECT * FROM $db.instagram_in_queue where id='$ID'";
                                                }
                                               $totalQuery = mysqli_query($link, $sql);
                                               $rowwhats = mysqli_fetch_assoc($totalQuery);
                                               
                                               $send_from= $rowwhats['send_from'];
                                               $send_to= $rowwhats['send_to'];
                                               $messageid = $rowwhats['id'];
                                               ?>
                                               <?php if($row['type'] == 'IN'){?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                                <a  href="omnichannel_config/web_sent_instagram.php?ID=<?=$messageid?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                            <? }else{ ?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                                <a  href="omnichannel_config/web_sent_instagram.php?ID=<?=$messageid?>&send_to=<?=$send_to?>&send_from=<?=$send_from?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_from?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                            <?php }?>
                                               <?php echo $remarks; ?></a>

                                            <?php } else if ($row['intraction_type'] == 'messenger') {
                                                if($row['type'] == 'OUT'){
                                                    $sql = "SELECT * FROM $db.messenger_out_queue where id='$ID'";
                                                }else{
                                                    $sql = "SELECT * FROM $db.messenger_in_queue where id='$ID'";
                                                }
                                               $totalQuery = mysqli_query($link, $sql);
                                               $rowwhats = mysqli_fetch_assoc($totalQuery);
                                               
                                               $send_from= $rowwhats['send_from'];
                                               $send_to= $rowwhats['send_to'];
                                               $messageid = $rowwhats['id'];
                                               ?>
                                               <?php if($row['type'] == 'OUT'){?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                                <a href="omnichannel_config/web_sent_messanger.php?ID=<?=$messageid?>&send_to=<?=$send_to?>&send_from=<?=$send_from?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_from?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                             
                                            <? }else{ ?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                                <a href="omnichannel_config/web_sent_messanger.php?ID=<?=$messageid?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2 text-with-dots" style="color: #0d6efd;" >
                                              
                                            <?php }?>
                                               <?php echo $remarks; ?></a>

                                            <?php } else if ($intraction_type == 'webchat') {
                                               $mobile= $row['mobile'];
                                               $interactid = $row['interact_id'];
                                               ?>
                                               <?php if($row['type'] == 'IN'){?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                            <? }else{ ?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                            <?php }
                                            $sessionid = get_sessionid($interactid);
                                            ?>

                                               <a class="ico-interaction2 text-with-dots" href="omnichannel_config/chat_history.php?phone=<?=$mobile?>&caseid=<?=$docket_no?>&session_id=<?=$sessionid?>" style="color: #0d6efd;" >
                                               <?php echo $remarks; ?></a>
                                            <?php }else{?><?php if($row['type'] == 'IN'){?>
                                                <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                                            <? }else{ ?>
                                                <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                                            <?php }?>
                                               <?=$row['remarks'];?>
                                               
                                            <?php }?>
                                            <?php if ($intraction_type == 'voicecall') { ?>
                                                <audio controls>
                                                    <source src="<?php echo SmartFileName_voice($filename); ?>" type="audio/mpeg">
                                                </audio>
                                          <?php }?>
                                          </div>
                                      </div>
                                      
                                      <div class="content__entry__right">
                                      <?php  if($intraction_type == 'email'){?>
                                        <svg  viewBox="0 0 48 48" width="20px" xmlns="http://www.w3.org/2000/svg"><path d="M45,16.2l-5,2.75l-5,4.75L35,40h7c1.657,0,3-1.343,3-3V16.2z" fill="#4caf50"/><path d="M3,16.2l3.614,1.71L13,23.7V40H6c-1.657,0-3-1.343-3-3V16.2z" fill="#1e88e5"/><polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"/><path d="M3,12.298V16.2l10,7.5V11.2L9.876,8.859C9.132,8.301,8.228,8,7.298,8h0C4.924,8,3,9.924,3,12.298z" fill="#c62828"/><path d="M45,12.298V16.2l-10,7.5V11.2l3.124-2.341C38.868,8.301,39.772,8,40.702,8h0 C43.076,8,45,9.924,45,12.298z" fill="#fbc02d"/></svg>
                                                
                                      <?php }else if($intraction_type == 'twitter'){?>
                                          <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
                                      <?php }else if($intraction_type == 'facebook'){?>
                                         <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"/></svg>
                                      <?php }else if($intraction_type == 'voicecall'){?>

                                        <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
                                      <?php }else if($intraction_type == 'webchat'){?>

                                        <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM128 208a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm128 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm96 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                                      <?php }else if($intraction_type == 'SMS'){?>
                                        <img src="../public/images/chat.png" width="20" border='0' title="Reply">
 
                                      <?php }else if($intraction_type == 'Whatsapp'){?>
                                        <img src="../public/images/whatsapp.png" width="20" border='0' title="Reply">
                                      <?php }else if($intraction_type == 'instagram'){?>
                                        <img src="../public/images/insta.png" width="20" border='0' title="Reply">

                                     <?php }else if($intraction_type == 'messenger'){?>
                                        <img src="../public/images/messenger_send.png" width="20" border='0' title="Reply">

                                      <?php }?>
                                      </div>
                                  </div>
                                <!-- </div> -->
                               <?php }?>
                               <?php
                                if($numesms <= 0) { ?>
                                   <div class="content__history">
                                  <div class="content__history__entry__content__data">
                                  <spna style="margin-left: 10px;">No record found !!</spna></div>
                                </div>
                                <?php } ?>
                              </div>
                             </div>
                           </div>
                          </div>
                        </div>
                        <div class="old-customer-table">
                            <span class="breadcrumb_head" style="height:37px;padding:9px 16px;background:#f5f5f5">
                                <span class="float:left"> Cases Info </span> 
                            </span>
                            <table class="tableview tableview-2 main-form new-customer">
                                <tbody>
                                    <tr class="background">
                                        <td align="center" class="boder0-right">Case Id</td>
                                        <td align="center" class="boder0-right">Category</td>
                                        <td align="center" class="boder0-right">Sub Category</td>
                                        <td align="center" class="boder0-right">Status</td>
                                        <td align="center" class="boder0-right">Type</td>
                                        <td align="center" class="boder0-right">Assign Department</td>
                                        <td align="center" class="boder0-right">Created On</td>
                                    </tr>
                                    <?php
                                    $ticket_query = view_case_info($customerid);
                                       $ref=$web_customer_detail."&CustomerID=".base64_encode($id);
                                        $web_customer_detail = base64_encode('web_customer_detail');
                                        $case_detail_backoffice = base64_encode('web_case_detail');

                                         while($ticket_res = mysqli_fetch_array($ticket_query)) { 
                                         $cat = category($ticket_res['vCategory']);
                                         $subcat = subcategory($ticket_res['vSubCategory']);
                                         $status = ticketstatus($ticket_res['iCaseStatus']);
                                         $mid = base64_encode($ticket_res['ticketid']);
                                         $type = ($ticket_res['vCaseType']);
                                         if($type==2) $type="Project Stakeholder";
                                         if($type==3) $type="Project Affected Person";
                                         if($type==1) $type="Public Relation";
                                         // $assignto = assignto($ticket_res['iAssignTo']);
                                        $assignto = project($ticket_res['vProjectID']);
                                    ?>
                                    <tr style="background:; color:" >
                                        <td align="center">
                                        
                                        <a href="helpdesk_index.php?token=<?=$case_detail_backoffice;?>&id=<?=$mid?>" style="padding-left: 10px;"><?= $ticket_res['ticketid'] ?></a></td>
                                        <td align="center"><?=$cat?></td>
                                        <td align="center"><?=$subcat?></td>
                                        <td align="center"><?=$status?></td>
                                        <td align="center"><?=$type?></td>
                                        <td align="center"><?=$assignto?></td>
                                        <td align="center"><?=$ticket_res['d_createDate']?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               <!-- Documents upload -->
               <div class="old-customer-table">
                  <h6>Documents</h6> 
                  <a href="helpdesk/web_doctypeadd.php?opportunityid=<?=$id?>&doctype=4&pid=2&val=1" class="old-cs-link newdocument cboxElement" style="text-decoration:none">Add Document</a>
                  <table width="685" class="tableview tableview-2 main-form">
                  <tbody>
                     <tr bgcolor="#dddddd"class="background">
                        <td width="30% " align="center"><b>Document Name </b></td>
                        <td width="15%" align="center"><b>Uploaded By</b></td>
                        <td width="20%" align="center"><b>Uploaded On</b></td>
                        <td width="20%" align="center"><b>Document</b></td>
                     </tr>
                  <?php
                  $resopp = get_documents($customerid,$groupid);
                  $rowcolopp            =   "";
                  $rownumopp            =   0;
                  $enddocuments = '';
                  $documents = '';
                  while($rowopp=mysqli_fetch_array($resopp)){
                     $rownumopp=$rownumopp+1;
                     $I_DocumentID          = $rowopp['I_DocumentID'];
                     $V_Doc_Name=$rowopp['V_Doc_Name'];
                     $V_Doc_Description=$rowopp['V_Doc_Description'];
                     $V_Path=$rowopp['V_Path'];
                     $v_uploadedFile=$rowopp['v_uploadedFile'];
                     $downloads            ="document/$db";
                     $v_uploadedFile=$rowopp['v_uploadedFile'];
                     $exp_upload=explode(",",$v_uploadedFile);
                     if(count($exp_upload)>1){
                        for($k=0;$k<count($exp_upload);$k++)
                        {
                         $f=$downloads."/".$exp_upload[$k];
                           $img="<img src=../public/images/download.jpg title='Click to download $f' border=0>";
                           $downloadpath1.="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$img."</a> &nbsp;&nbsp;";
                      }
                     }else if(count($exp_upload)==1){
                       $f=$downloads."/".$v_uploadedFile;
                       $img="<img src=../public/images/download.jpg title='Click to download $f' border=0>";
                       $downloadpath1="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$img."</a>";
                     }
                     $cdateopp=$rowopp['I_UploadedON'];
                     $conopp1=explode(" ",$cdateopp);
                     $conopp=explode("-",$conopp1[0]);
                     $createdonopp=$conopp[2].'-'.$conopp[1].'-'.$conopp[0];
                     $madebyopp=$rowopp['I_UploadedBY'];
                     $countopp=$countopp+1;

                     if($rownumopp%2==0)
                     $rowcolopp="white";
                     else
                     $rowcolopp="#EFEFEF";
                  $documents.='<tr bgcolor="#FFFFFF">
                  <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$V_Doc_Name.'</td>
                  <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$madebyopp.'</td>
                  <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$createdonopp.'</td>
                  <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$downloadpath1.'</td>
                  </tr>';
                  }
                  if(empty($countopp) && $documents_str=='' && $documents_chat == ''){
                     $enddocuments.='<tr bgcolor="white" height=22><td colspan="9" align="center" class="ttext">No Documents added.</td></tr>';
                  }
                  echo $documents.$documents_str.$enddocuments.$documents_chat;
                  ?>
                  </tbody>
                  </table>
                  <?php }?>
                </div>
              
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?=$SiteURL?>public/js/ss.custom.js" ></script><script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    // Automatically copy phone to WhatsApp and SMS
    document.getElementById("phone").addEventListener("input", copyPhoneNumber);
});

function copyPhoneNumber() {
    let phoneNumber = document.getElementById("phone").value;
    document.getElementById("whatsapphandle").value = phoneNumber;
    document.getElementById("smshandle").value = phoneNumber;
}
</script>
