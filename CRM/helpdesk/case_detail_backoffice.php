<?php
/***
 * Backoffice Page
 * Auth:  Aarti Ojha
 * Date: 16-01-2024
 * Description: this file use for fetch data and insert data in database
 * Admin all module Files added and common files added
 * 
*/
include('web_helpdesk_function.php'); // this file handle common function related ticket data
//decode url ticket id
if(isset($_GET['prob_id'])){
   // $id = base64_decode($_GET['prob_id']);
    $id = $_GET['prob_id'];
}else{
   $id = base64_decode($_GET['id']);
}

$ticketid = $id;
$docket_no = $ticketid;
$groupid    =   $_SESSION['user_group'];
/************ For showing Ticket details *************/
$Action = getLatestCaseInteraction($ticketid); // function for fetch ticketid details query
$row_interaction = mysqli_fetch_assoc($Action);

$rec_count = mysqli_num_rows($Action);
//added condition for fetching ticket details on the basis of the  deleted ticket or not [vastvikta][19-12-2024]
$delflag = $_GET['delflag'];
if($delflag=='1'){
   $resonse =get_ticket_delete_detail($docket_no); 
}
else{
// $res = mysqli_fetch_assoc($resonse);
$resonse = get_ticket_detail($docket_no); // function for fetch ticketid details query
}
$res = mysqli_fetch_assoc($resonse);
$old_data = $res;
$customer = $res['vCustomerID'];
$case_id =    $res['iPID'];
$phone =    $res['phone'];
$mobile =    $res['mobile'];
$email          = $res['email'];
$alternate_email          = $res['alternate_email'];
$age          = $res['age_grp'];
$gender          = $res['gender'];
$priority_user          = $res['priority_user'];
$source =    $res['i_source'];
$iAssignTo    =    $res['iAssignTo'];
$agent = agentname($res['i_CreatedBY']);
$name =  explode(' ', $res['fname']); // Split fname into an array of two values seperated by space.
$first_name = $name[0];
$last_name = $name[1];
$address_1 = $res['address'];
$address_2 = $res['v_Location'];
$district =  $res['district'];
$villages = $res['v_Village'];
$type =     $res['vCaseType'];
$catid =     $res['vCategory'];
$subcatid =  $res['vSubCategory'];
$group =     $res['vProjectID'];
$mno =     $res['mno'];
$isp =     $res['isp'];
$dfs =     $res['dfs'];
$perpetrator =     $res['perpetrator'];
$affected =     $res['affected'];
$service =     $res['service'];
$comp = $res['complaint_type'];
$remark =    $res['vRemarks'];
$status    = $res['iCaseStatus'];
$languagename = $res['language_id'];
$customer = $res['vCustomerID'];
$fbhandle = $res['fbhandle'];
$twitterhandle = $res['twitterhandle'];
$backoffice_remark = $res['b5_remark'];
$backoffice_last_remark = $res['b6_remark'];
$status_type_ = $res['iCaseStatus'];
$call_type = $res['call_type'];
$priority = $res['priority'];
$exceptional_case = $res['exceptional_case'];
$customer_id =    $res['vCustomerID'];
$organisation =    $res['organization'];
$source            = $res['i_source'];
$ticketid_old_case  = $res['ticketid'];
$supervisor_remark  = $res['v_ActionSupervisor'];
$v_OverAllRemark  = $res['v_OverAllRemark'];
$account_no_  = $res['customer_account_no'];
/*new screen related changes-07-09-23*/
$business_number = $res['business_number'];
$register_tpin = $res['tpin'];
$passport_number = $res['passport_number'];
$area = $res['area'];
$town = $res['town'];
$whatsappnumber = $res['whatsapphandle'];
$messengerhandle = $res['messengerhandle'];//for messenger sender id fetch[Aarti][14-08-2024]
$sms_number = $res['smshandle'];
$nationality = $res['nationality'];
$taxtype_id = $res['tax_id'];
$company_name_case = $res['company_name'];
$company_registration = $res['company_registration'];
$root_cause = $res['root_cause'];
$corrective_measure = $res['corrective_measure'];
$regional = $res['regional'];
$customertype = $res['customertype'];
$instagramhandle = $res['instagramhandle'];//for instagram sender id fetch[Aarti][19-11-2024]
if(!empty($res['d_updateTime'])){
   $last_date = date("Y-m-d H:i:s",strtotime($res['d_updateTime']));
}
else{
   $last_date = 'N/A';
}
$customertype = $res['customertype'];
$subString = '';
$subString2 = "";
if ($status_type_ == '1'){   // pending
   $subString2 = "AND id IN (1, 8)";
   $subString =    "AND id NOT IN (3,5)";
}elseif ($status_type_ == '2'){   // drop
   $subString2 = "AND id IN (2)";
   $subString =    "";
}elseif ($status_type_ == '3'){   // close	
   $subString2 = "AND id IN (3,1)";
   $subString =    "";
}else if ($status_type_ == '4'){   // escalte 
   $subString2 = "AND id IN (4,8)";
   $subString  = "AND id NOT IN (3,5,1)";
}else if ($status_type_ == '5'){  // reopen
   $subString2 = "AND id IN (5,8)";
   $subString  = "AND id NOT IN (3,1)";
}else if ($status_type_ == '8'){   // resolved
   // $subString2 = "AND id IN (2,3,8)" ;
   $subString2 = "AND id IN (3,8)";
   $subString  = "AND id NOT IN (5)";
}
$source = isset($_GET['mr'])  ? $_GET['mr'] : $source;
$category = category($catid); // for fetch category list
$subcategory = subcategory($subcatid); // for fetch sub category list
$mode_ticket = source($source); //for fetch source name
?>
<style type="text/css">
   .select2-container{
      width: 180px;
    background: #fff;
    border: #c2c2c2 1px solid;
   }
   .select2-container {
      width: 180px;
      background: #fff;
      border: #c2c2c2 1px solid;
   }

   /* CSS */
   .button-33 {
      background-color: #c2fbd7;
      border-radius: 100px;
      box-shadow: rgba(44, 187, 99, .2) 0 -25px 18px -14px inset, rgba(44, 187, 99, .15) 0 1px 2px, rgba(44, 187, 99, .15) 0 2px 4px, rgba(44, 187, 99, .15) 0 4px 8px, rgba(44, 187, 99, .15) 0 8px 16px, rgba(44, 187, 99, .15) 0 16px 32px;
      color: green;
      cursor: pointer;
      display: inline-block;
      font-family: CerebriSans-Regular, -apple-system, system-ui, Roboto, sans-serif;
      padding: 7px 20px;
      text-align: center;
      text-decoration: none;
      transition: all 250ms;
      border: 0;
      font-size: 12px;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
   }

   .button-33:hover {
      box-shadow: rgba(44, 187, 99, .35) 0 -25px 18px -14px inset, rgba(44, 187, 99, .25) 0 1px 2px, rgba(44, 187, 99, .25) 0 2px 4px, rgba(44, 187, 99, .25) 0 4px 8px, rgba(44, 187, 99, .25) 0 8px 16px, rgba(44, 187, 99, .25) 0 16px 32px;
      transform: scale(1.05) rotate(-1deg);
   }

   .modal-content {
      padding: 3.5em;
   }

   .width-btn {
      width: 110px;
   }


   .chat-history {
      max-height: 200px; /* Limit initial height */
      overflow: hidden; /* Hide overflowing content */
      background-color: lightgrey;
      padding: 10px;
      border-radius: 5px;
    }

    .chat-message {
      margin-bottom: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
    }

    .message-text {
      flex: 1;
    }

    .message-time {
      margin-left: 10px;
      color: grey;
      font-size: 12px;
      text-align: right;
    }

  .show-more-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin: 10px auto;
      padding: 8px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      text-align: center;
      font-size: 14px;
      font-weight: bold;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .show-more-btn:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }

    .show-more-btn span {
      margin-left: 5px;
      font-size: 18px;
      line-height: 1;
    }

    /* Add animation for dots */
    .dots {
      animation: blink 1.5s infinite;
    }

    @keyframes blink {
      0%, 20% {
        opacity: 0;
      }
      50% {
        opacity: 1;
      }
    }

    /* user menu settings */
   #dropdown { 
   display: block;
   padding: 7px 16px;
   width: 160px;
   /* margin: 0 auto; */
   position: relative;
   cursor: pointer;
   border-right: 4px solid #739cda;
   background: #fff;
   /* font-size: 1.55em; */
   color: #656565;
   font-weight: normal;
   -webkit-box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   -moz-box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   -webkit-transition: all 0.15s linear;
   -moz-transition: all 0.15s linear;
   -ms-transition: all 0.15s linear;
   -o-transition: all 0.15s linear;
   transition: all 0.15s linear;
   }
   #dropdown:hover { color: #898989; }

   #dropdown.open {
   background: #5a90e0;
   color: #fff;
   border-right-color: #6c6d70;
   }

   #dropdown ul { 
   position: absolute;
   top: 100%;
   left: -4px; /* move content -4px because of container left border */
   width: 163px;
   padding: 5px 0px;
   display: none;
   border-right: 4px solid #8e9196;
   background: #fff;
   -webkit-box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   -moz-box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   box-shadow: 1px 1px 2px rgba(0,0,0,0.3);
   }
   #dropdown ul li { font-size: 0.9em; }

   #dropdown ul li a { 
   text-decoration: none;
   display: block;
   color: #447dd3;
   padding: 7px 15px;
   }
   #dropdown ul li a:hover {
   color: #6fa0e9;
   background: #e7f0f7;
   }
</style>
<div class="col text-center">
<div class="row d-flex flex-row-reverse" style="margin-right:80px;">
       <div id="dropdown" class="ddmenu" style="color:black;font-size:1rem;">
          Compose <i class="fas fa-caret-down"></i>
          <ul>
            <li><a href="javascript:void(0)" class="emailConMess">Email</a></li>
            <li><a href="javascript:void(0)" class="smsConMess">SMS</a></li>
            <li><a href="javascript:void(0)" class="whatsappConMess">Whatsapp</a></li>
            <li><a href="javascript:void(0)" class="fbmessConMess">Messenger</a></li>
            <li><a href="javascript:void(0)" class="instagramConMess">Instagram</a></li>
          </ul>
        </div>
    </div>
   <h1><b>CASE SUMMARY</b></h1>
   
</div>
<div class="rightpanels">
<div class="style2-table">
         <span class="breadcrumb_head" style="height:37px;padding:9px 16px"></span>
         <!-- table right panel start -->
         <form method="post" name="viewproblemfrm" id="viewproblemfrm" method="post" enctype="multipart/form-data">
            <input type="hidden" id="action" value="Update_Case" name="action">
            <input type="hidden" name="docket_no" id="docket_no" value="<?= $docket_no ?>">
            <input type="hidden" name="source" id="source" value="<?= $source ?>">
            <input type="hidden" name="type" value="<?=$type?>">
            <input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id ?>">
            <div class="table">
               <table width="100%" align="center" border="0" class="tableview tableview-2 main-form">
                  <tbody>
                     <tr>
                        <td colspan="2" id="show_ticket" style="color:#ffffff;  background:#004b8b82; padding:5px; border:1px solid #fff; text-align:center;display: none;">
                          
                         </td>
                     </tr>
                     <tr align="right">
                        <input type="hidden" name="steamid" id="steamid" value="">
                        <td><strong>
                              <font size="2" color="#003399">Case Status : </font>
                              <font size="2" color="#993300"><?= ticketstatus($status) ?></font>
                           </strong></td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Case Id</b></font>
                           <font size="2" color="#993300"><b><?= $ticketid ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Category:</b></font>
                           <font size="2" color="#993300"><b><?= $category ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Sub Category:</b></font>
                           <font size="2" color="#993300"><b><?= $subcategory ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Company Name:</b></font>
                           <font size="2" color="#993300"><b><?= $company_name_case; ?></b></font>
                        </td>
                     </tr>
                     <tr>
                        <td align="center">
                           <font size="2" color="#003399"><b>Case Origin:</b></font>
                           <font size="2" color="#993300"><b><?= $mode_ticket ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Customer Name:</b></font>
                           <font size="2" color="#993300"><b><?= $res['fname'] ?></b></font>
                        </td>
                        <td align="center" >
                           <font size="2" color="#003399"><b>Last Action Date:</b></font>
                           <font size="2" color="#993300"><b><?= $last_date ?></b></font>
                        </td>
                        <td align="center" >
                           <font size="2" color="#003399"><b>Last Action By:</b></font>
                           <font size="2" color="#993300"><b><?=$row_interaction['created_by']?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Creation Date and Time:</b></font>
                           <font size="2" color="#993300"><b><?= $res['d_createDate']; ?></b></font>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <style>
                  .new-customer td {
                     padding: 8px 10px !important;
                  }
               </style>
               <div class="container-fluid">
                  <div class="row">
                     <div class="col text-center">
                        <h1><b>CASE DETAIL</b></h1>
                     </div>
                  </div>
               </div>
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Customer Profile</span>
               <?php $module_flag_customer = module_license('CUSTOMER PROFILE'); ?>
               <table cellpadding="3" cellspacing="2" width="90%" align="center" border="1" class="tableview tableview-2 main-form new-customer" <?php if($module_flag_customer!='1'){?> style="display: none;" <?php }?>>
                  <tbody>
                     <tr>
                        <td class="left boder0-left">
                           <label>Registered Mobile Number<em>*</em></label>
                           <div class="log-case">
                              <input readonly="readonly" name="phone" id="phone" type="text" value="<?= $phone ?>" class="input-style1" onKeyUp="ChkIntOnly(this);NumOnly(this)" maxlength="12" readonly>
                                 <?php if (isset($_SESSION['VD_login']) && $_SESSION['VD_login'] != "" && agentManualTranferConfer($link, $_SESSION['VD_login'], $_SESSION['companyid'],'agentcall_manual')) { ?>
                                 <a href="javascript:void(0);" onClick="clickcall_c2c('<?= $phone ?>','<?= $ticketid ?>','1','<?= $_SESSION['VD_login'] ?>');">
                                    <img src="../../universus/img/call_1.png" border="0" style="height:15px;width:15px;margin-left: -3px;margin-top: 5px;"></a>
                              <?php } ?>
                           </div>
                        </td>
                        <td class="left  boder0-left">
                           <label>Alternate Number</label>
                           <input name="mobile" id="mobile" readonly="readonly" type="text" maxlength="10" value="<?= $mobile ?>" class="input-style1 inputDisabled" onKeyUp="ChkIntOnly(this);NumOnly(this)">
                        </td>
                        <td class="left boder0-left">
                           <label>Company Name</label>
                           <input type="text" name="company_name" readonly="readonly" id="company_name" value="<?= $company_name_case ?>" class="input-style1">
                        </td>
                        <td class="left boder0-left">
                           <label>Company Registration Number</label>
                           <input type="text" name="company_registration" readonly="readonly" id="company_registration" value="<?= $company_registration ?>" class="input-style1">
                        </td>                                 </tr>
                     <tr>
                        <td class="left boder0-left">
                           <label>First Name <em>*</em></label>
                           <div class="log-case">
                              <? $first_name = str_replace("singlequote", "'", $first_name); ?>
                              <input name="first_name" id="first_name" readonly="readonly"  maxlength="200" type="text" value="<?= $first_name ?>" class="input-style1 ">
                           </div>
                        </td>
                        <td class="left boder0-left">
                           <label>Last Name <em>*</em></label>
                           <? $last_name = str_replace("singlequote", "'", $last_name); ?>
                           <div class="log-case">
                              <input name="last_name" id="last_name" readonly="readonly" type="text" maxlength="200" value="<?= $last_name ?>" class="input-style1 ">
                           </div>
                        </td>
                        <td class="left boder0-left">
                         <label>Priority Customer <em>*</em></label>
                           <div class="log-case">

                              <span class="slug"> <input  disabled type="radio" name="priority_user" value="1" checked <? if ($priority_user == '1') {
                              echo "checked";
                              } ?>> Priority</span>
                              <span class="slug"> <input disabled type="radio" name="priority_user" value="0" <? if ($priority_user == '0') {
                              echo "checked";
                              } ?>> Non Priority</span>

                           </div>
                        </td>
                        <td class="left boder0-left">
                           <label>County</label>
                           <div class="log-case">
                             <select name="district" id="district" class="select-styl1" onchange="get_villages(this.value)" style="width:180px" disabled>
                                 <option value="0">Select County</option>
                                 <?php
                                 $city_query = city_list();
                                 while ($city_res = mysqli_fetch_array($city_query)){?>
                                    <option value="<?= $city_res['id'] ?>" <? if ($city_res['id'] == $district) {
                                    echo "selected";
                                    } ?>><?= $city_res['city'] ?> </option>
                                 <?php } ?>
                           </div>
                        </td>
                     </tr>
                     <tr>                       
                        <td class="left boder0-left">
                           <label>Sub County</label>
                           <div class="log-case">
                              <?php $villages_query = get_Village($district); ?>
                              <select name="villages" id="villages" class="select-styl1" style="width:180px" disabled>
                                 <option value="0">Select Sub County</option>
                                 <?php
                                 if (mysqli_num_rows($villages_query) > 0) {
                                    while ($villages_res = mysqli_fetch_array($villages_query)){?>
                                       <option value="<?= $villages_res['id'] ?>" <? if ($villages_res['id'] == $villages) {
                                             echo "selected";
                                    } ?>><?= $villages_res['vVillage'] ?> </option>
                                 <?php } } ?>
                              </select>
                           </div>
                       </td>
                       <td class="left boder0-left">
                        <label>Nationality</label>
                        <div class="log-case">
                           <input name="nationality" readonly="readonly" id="nationality" type="text" value="<?= $nationality ?>" class="input-style1">
                        </div>
                     </td>
                     <td class="left border0-left">
                           <label>Gender <em>*</em></label>
                           <select name="gender" id="gender" class="select-styl1" style="width:180px" disabled>
                              <option value="0">Select Gender</option>
                              <?php
                               $gender_query = web_gender();
                              if (mysqli_num_rows($gender_query) > 0) {
                                 while ($gender_res = mysqli_fetch_array($gender_query)){?>
                                 <option value="<?= $gender_res['value'] ?>" <? if ($gender_res['value'] == $gender) {
                                 echo "selected";
                                 } ?>><?= $gender_res['name'] ?> </option>
                              <?php }
                              } ?>
                           </select>
                        </td>   
                        <td class="left boder0-left">
                           <label>Email</label>
                           <input type="email" name="email" readonly="readonly" id="email" value="<?=$email?>" maxlength="100" class="input-style1">
                        </td>                                       
                     </tr>
                     <tr>
                        
                        <td class="left boder0-left">
                           <label>Facebook Handle</label>
                           <input type="text" readonly="readonly" name="fbhandle" id="fbhandle" value="<?= $fbhandle ?>" class="input-style1">
                        </td>
                        <td class="left boder0-left">
                           <label><img src = "../public/images/x-twitter.svg" alt="X" style="width:15px"> Handle</label>
                           <input type="text" readonly="readonly" name="twitterhandle" id="twitterhandle" value="<?= $twitterhandle ?>" class="input-style1">
                        </td>
                         
                        <td class="left boder0-left">
                           <label>Whatsapp Number</label>
                           <input type="text" readonly="readonly" name="whatsapphandle" id="whatsapphandle" value="<?= $whatsappnumber ?>" class="input-style1">
                        </td>
                        <!-- //for messenger Handling html code [Aarti][14-08-2024] -->
                         <td class="left boder0-left">
                           <label>Facebook Messenger ID</label>
                           <input type="text" readonly="readonly" name="messengerhandle" id="messengerhandle" value="<?= $messengerhandle ?>" class="input-style1">
                        </td>
                     </tr>

                     <tr>  
                        <td class="left boder0-left">
                           <label>Instagram ID</label>
                           <input type="text" readonly="readonly"name="instagramhandle" id="instagramhandle" value="<?= $instagramhandle ?>" class="input-style1">
                        </td>
                     <td class="left boder0-left">
                           <label>SMS Number</label>
                           <input type="text" readonly="readonly" name="smshandle" id="smshandle" value="<?= $sms_number ?>" class="input-style1">
                        </td>                    
                        <td class="left boder0-left">
                           <label>Address</label>
                           <textarea name="address_1" readonly="readonly" id="address_1" class="input-style1" ><?= $address_1 ?></textarea>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <?php $module_flag_customer = module_license('CUSTOMER INFORMATION'); ?>
            <div class="old-customer-simple-table" <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>>
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Customer Information</span>
               <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                        <tr>
                           <td class="left boder0-left">
                              <label>Complaint Origin<em>*</em></label>
                              <?php 
                              $sourceresult = source_list();
                              ?>
                              <select name="source" id="source" class="select-styl1" style="width:190px" disabled>
                                    <option value="0">Select Complaint Origin</option>
                                    <?php
                                    while ($row = mysqli_fetch_array($sourceresult)) { ?>
                                       <option value="<?= $row['id'] ?>" <?= ($source == $row['id']) ? 'selected' : ''; ?>>
                                          <?= $row['source'] ?>
                                       </option>
                                    <?php } ?>
                              </select>
                           </td>
                           <td class="left boder0-left">
                              <label>Call <em>*</em></label>
                              <div class="log-case">
                                    <span class="slug">
                                       <input class="radio" type="radio" name="call_type" value="real" disabled <?= ($call_type == 'real') ? "checked" : ""; ?>> Real Call
                                    </span>
                                    <span class="slug">
                                       <input class="radio" type="radio" name="call_type" value="spam" disabled <?= ($call_type == 'spam') ? "checked" : ""; ?>> Spam Call
                                    </span>
                              </div>
                           </td>
                           <td class="left boder0-left">
                              <label>Agent Name</label>
                              <input type="text" readonly="readonly" name="agent_name" id="agent_name" value="<?= $agent ?>" class="input-style1">
                           </td>
                           <td class="left boder0-left">
                              <label>Language</label>
                              <?php $langresult = language_list(); ?>
                              <select name="lang" id="lang" class="select-styl1" style="width:190px" disabled>
                                    <?php
                                    while ($row = mysqli_fetch_array($langresult)) {
                                       $sel = '';
                                       if ($languagename == $row['id']) {
                                          $sel = "selected";
                                       }
                                    ?>
                                       <option value="<?= $row['id'] ?>" <?= $sel ?>>
                                          <?= $row['lang_Name'] ?>
                                       </option>
                                    <?php } ?>
                              </select>
                           </td>
                        </tr>
                  </tbody>
               </table>
            </div>
            <?php $module_flag_customer = module_license('INCIDENT INFORMATION'); ?>
            <div class="old-customer-simple-table " <?php if($module_flag_customer!='1'){?> style="display: none;" <?php }?>>
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">INCIDENT INFORMATION</span>
               <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                     <tr>
                        <td class="left boder0-right">
                           <label>Reason of Calling <em>*</em></label>
                           <div class="log-case">
                              <?php $complaint_sql = complaint_type();
                              while ($rows = mysqli_fetch_array($complaint_sql)) {
                                 if ($rows['slug'] == 'none') break;
                              ?>
                              <span class="slug"> <input class="radio" type="radio"  name="type" id="type<?= $rows['id'] ?>" value="<?= $rows['slug'] ?>" <? if ($type == $rows['slug']) { echo "checked";} ?> disabled="disabled">
                                    <?= $rows['complaint_name'] ?></span>
                              <?php }
                              ?>
                           </div>
                        </td>
                        <td class="left boder0-right">
                           <label>Priority of Call <em>*</em></label>
                           <div class="log-case">
                              <span class="slug"> <input class="radio" type="radio" name="priority" value="high"<?php if ($priority == 'high') {
                              echo "checked";
                              } ?> disabled="disabled"> High </span>
                              <span class="slug"> <input class="radio" type="radio" name="priority" value="medium"  <? if ($priority == 'medium') {
                              echo "checked";
                              } ?> disabled="disabled"> Medium </span>
                              <span class="slug"> <input class="radio" type="radio" name="priority" value="low"  <? if ($priority == 'low') {
                              echo "checked";
                              } ?> disabled="disabled"> Low </span>
                           </div>
                        </td>
                        <td class="left  boder0-right">
                           <label>Status of Complaint <em>*</em></label>
                           <div class="log-case">
                              <?php $status = !empty($status) ? $status : '1';?>
                              <input type="hidden" name="status_type_" value="<?php echo $status ?>">
                              <select name="status_type_2" class="select-styl1" <? echo (isset($status) && $status == '8') ? 'disabled' : 'disabled'
                                 ?> onchange="getfeedback(this.value)" style="width:180px;">
                                 <option value="0">Select Status</option>
                                 <?php $ticketstatus_query = web_ticketstatus($subString,$subString2);
                                 while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                                    <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status){ echo "selected";} ?>>
                                       <?= $ticketstatus_res['ticketstatus'] ?>
                                    </option>
                                 <?php }?>
                              </select>
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td class="left  boder0-left">
                           <label>Category <em>*</em></label>
                           <div class="log-case" id="category_div">
                              <select name="v_category" id="v_category" class="select-styl1 select2" style="width:180px" onChange="web_subcat(this.value);get_department(this.value)" disabled="">
                                 <option value="0">Select Category</option>
                                 <?php $cat_query = web_category_list();
                                 while ($cat_res = mysqli_fetch_array($cat_query)){
                                    print_r($cat_res);?>
                                    <option value="<?= $cat_res['id'] ?>" <? if ($cat_res['id'] == $catid) {
                                    echo "selected";
                                    } ?>>
                                       <?= $cat_res['category'] ?>
                                    </option>
                                 <?php }?>
                              </select>
                           </div>
                        </td>
                        <!-- web_subcat('<?= $catid ?>','<?= $subcatid ?>'); -->
                        <td class="left  boder0-left">
                           <label>Sub Category <em>*</em></label>
                           <div class="log-case" id="subcategory_div">
                              <select name="v_subcategory" id="v_subcategory" class="select-styl1 select2" style="width:180px" disabled="">
                                 <option value="0">Select Sub Category</option>
                                 <?php $subcat_query = web_subcategory_list($catid);
                                 if (mysqli_num_rows($subcat_query) > 0){
                                    while ($subcat_res = mysqli_fetch_array($subcat_query)){?>
                                       <option value="<?= $subcat_res['id'] ?>" <? if ($subcat_res['id'] == $subcatid) { echo "selected"; } ?>><?= $subcat_res['subcategory'] ?>
                                       </option>
                                 <?php } }?>
                              </select>
                           </div>
                        </td>
                        <?php if ($type == 'complaint'){?>
                           <td class="left  boder0-right">
                              <label>Assign Department </label>
                              <div class="log-case">
                                 <select name="group_assign" id="group_assign" class="select-styl1 select2" style="width:180px;" disabled="disabled">
                                    <option value="0">Select Department </option>
                                    <?php 
                                    $group_query = assign_department();
                                    while ($group_res = mysqli_fetch_array($group_query)){?>
                                       <option value="<?= $group_res['pId'] ?>" <? if ($group_res['pId'] == $group) {echo "selected";} ?>>
                                          <?= $group_res['vProjectName'] ?>
                                       </option>
                                    <?php }?>
                                 </select>
                              </div>
                              <div id="show_emails"></div>
                           </td>
                        <?php }?>
                     </tr>
                     <tr>
                        <td class="left border0-left">
                           <label>Root Cause</label>
                           <input type="text" name="root_cause" id="root_cause" value="<?= $root_cause ?>" class="input-style1">
                        </td>
                        <td class="left border0-left">
                           <label>Corrective Measure</label>
                           <input type="text" name="corrective_measure" id="corrective_measure" value="<?= $corrective_measure ?>" class="input-style1">
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>            <!-- Agent Level View (FCR)-->
            <input type="hidden" name="status_old" value="<?=$status?>">
            <input type="hidden" name="emails" id="feed_email" value="<?= $email ?>">
             <input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id ?>">
            <?php if ($groupid == '070000'){?>
               <div class="old-customer-simple-table ">
                  <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Agent</span>
                  <table class="tableview tableview-2 main-form new-customer">
                     <tbody>
                        <tr>
                           <td>
                              <label>Agent Remarks <em>*</em> </label>
                              <div class="log-case" id=""><br>
                                 <?= $remark ?>
                              </div>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            <?php }?>
            <!--  Back Office first Level View (BSL) -->
            <?php if ($groupid == '060000'){?>
               <div class="old-customer-simple-table ">
                  <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Back Office</span>
                  <table class="tableview tableview-2 main-form new-customer">
                     <tbody>
                        <tr>
                           <td width="50%" class="left boder0-left">
                              <label>Assign Case</label>
                              <!-- <select name="group_assign" id="group_assign" class="select-styl1 " style="width:180px;">
                                 <option value="">Select Department</option>
                                 <?php
                                 $group_query = assign_department();
                                 while ($group_res = mysqli_fetch_array($group_query)){
                                 ?>
                                 <option value="<?= $group_res['pId'] ?>" <? if ($group_res['pId'] == $group_assign) {echo "selected";} ?>>
                                 <?= $group_res['vProjectName'] ?>
                                 </option>
                              <?php } ?>
                              </select> -->
                              <?php $currentGroup = $group_assign; // Store current group in a JS-friendly format ?>
                                 <select name="group_assign" id="group_assign" class="select-styl1 ForwardGroup" style="width:180px;">
                                    <option value="">Select Department</option>
                                    <?php
                                    $group_query = assign_department();
                                    while ($group_res = mysqli_fetch_array($group_query)) {
                                    ?>
                                       <option value="<?= $group_res['pId'] ?>" <?= ($group_res['pId'] == $group) ? "selected" : "" ?>>
                                          <?= $group_res['vProjectName'] ?>
                                       </option>
                                    <?php } ?>
                                 </select>

                                 <!-- Textarea for remark, hidden by default -->
                                 <div class="remarkContainer" style="margin-top:10px; display:none;">
                                    <label for="new_remark">Remark:</label><br>
                                    <textarea name="new_remark" id="new_remark" class="input-style1 new-remark" rows="3" cols="40" placeholder="Enter reason for department change..."></textarea>
                                 </div>
                           </td>
                           <td class="left   boder0-left">
                              <label>Status of complaint <em>*</em></label>
                              <div class="log-case">
                                 <select name="status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)">
                                    <option value="0">Select Status</option>
                                    <?php
                                    $ticketstatus_query = web_ticketstatus($subString, $subString2);
                                    while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                                       <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status) {
                                       echo "selected";
                                       } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </td>
                        </tr>
                        <tr>
                           <td width="50%" class="left  boder0-right">
                              <label>Agent Remarks<em>*</em> </label>
                              <div class="log-case" id=""><?= $remark ?>
                              </div>
                           </td>
                           <td>
                              <label>Back Officer Remarks <em>*</em> </label>
                              <div class="log-case" id="">
                                 <?php if ($status!='1') : 
                                    echo $backoffice_remark 
                                 ?>
                                    <input type="hidden" name="backoffice_remark" id="backoffice_remark" value="<?= $backoffice_remark ?>">
                                 <? else : ?>
                                    <textarea name="backoffice_remark" id="backoffice_remark" class="input-style1" type="text" style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"></textarea>
                                 <? endif; ?>
                              </div>
                           </td>
                        </tr>
                        <tr>
                           <td class="left   boder0-left">
                              <div class="log-case" id="">
                                 <input type="checkbox" <?php echo $exceptional_case == '1' ? 'checked' : '' ?> name="exceptional_case" id="exceptional_case" value="1"> &nbsp;&nbsp;&nbsp;Exceptional Case <br> <small style="color:red">Please check for 5 days Escalation</small>
                           </td>
                        </tr>
               </tbody>
               </table>
            </div>
         <?php } ?>
         <!-- Back Office LAST CALL (BLL)-->
         <?php if ($groupid == '090000'){?>
            <div class="old-customer-simple-table ">
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Branch Manager</span>
               <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                     <tr>
                        <td width="50%" class="left  boder0-right">
                           <label><strong>Agent Remarks</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $remark ?></div>
                        </td>
                        <td>
                           <label><strong>Branch Group Officer Remarks</strong> <em>*</em>
                           </label>
                           <div class="log-case" id=""><br><?= $backoffice_remark ?></div>
                        </td>
                     </tr>
                     <tr>
                        <td class="left   boder0-left" colspan="2">
                           <label>Status of complaint <em>*</em></label>
                           <div class="log-case">
                              <select name="status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)" <?= !empty($backoffice_last_remark) ? 'disabled' : '' ?>>
                                 <option value="0">Select Status</option>
                                 <?php
                                 $ticketstatus_query = web_ticketstatus($subString,$subString2);
                                 while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                                    <option value="<?= $ticketstatus_res['id'] ?>" <?php if ($ticketstatus_res['id'] == $status){ echo "selected";
                                    } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2" class="left  boder0-left">
                           <label><strong>Branch Manager Remarks </strong><em>*</em> </label>
                           <div class="log-case" id="">
                              <? if (!empty($backoffice_last_remark)) : echo $backoffice_last_remark ?>
                              <? else : ?>
                                 <textarea name="backoffice_last_remark" id="backoffice_last_remark" class="input-style1" type="text" <? echo
                                 !empty($backoffice_last_remark) ? 'readonly' : ''
                                 ?> style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"><?= $backoffice_last_remark ?></textarea>
                              <? endif; ?>
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         <?php } ?>
         <?php if ($groupid == '080000'){?>
            <div class="old-customer-simple-table ">
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Supervisor</span>
               <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                     <tr>
                        <td width="50%" class="left boder0-left">
                           <label><strong>Assign Case</strong></label>
                           <select name="group_assign" id="group_assign" class="select-styl1 " style="width:180px;">
                              <option value="">Select Department</option>
                              <?php $group_query = assign_department();
                              while ($group_res = mysqli_fetch_array($group_query)){
                              ?>
                               <option value="<?= $group_res['pId'] ?>" <? if ($group_res['pId'] == $group) {echo "selected";} ?>>
                              <?= $group_res['vProjectName'] ?>
                              </option>
                           <?php } ?>
                           </select>
                        </td>
                        <td class="left   boder0-left">
                           <label><strong>Status of complaint </strong><em>*</em></label>
                           <div class="log-case">
                              <select name="status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)">
                                 <option value="0">Select Status</option>
                                 <?
                                 $ticketstatus_query = web_ticketstatus($subString,$subString2);
                                 while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){ 
                                 ?>
                                    <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status) {
                                    echo "selected";
                                    } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <td width="50%" class="left  boder0-right">
                           <label><strong>Agent Remarks</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $remark ?></div>
                        </td>
                        <td>
                           <label><strong>Back office Remark</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $backoffice_remark ?></div>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2" class="left  boder0-left">
                           <label><strong>Supervisor Remarks </strong><em>*</em> </label>
                           <div class="log-case" id=""><br><?= $supervisor_remark ?></div>
                           <div class="log-case" id="">
                              <? if (!empty($supervisor_remark)) : echo $supervisor_remark ?>
                              <? else : ?>
                                 <textarea name="supervisor_remark" class="input-style1" id="supervisor_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 600px;height: 50px;resize: none;"><?php echo $supervisor_remark ?></textarea>
                              <? endif; ?>
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         <?php } ?>
         <?php if ($groupid == '0000'){?>
            <div class="old-customer-simple-table ">
               <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Adminstrator</span>
               <table class="tableview tableview-2 main-form new-customer">
                  <tbody>
                     <tr>
                        <tr>
                        <td width="50%" class="left boder0-left">
                           <label><strong>Assign Case</strong></label>
                           <!-- <select name="group_assign" id="group_assign" class="select-styl1 " style="width:180px;">
                              <option value="">Select Department</option>
                              <?
                              $group_query = assign_department();
                              while ($group_res = mysqli_fetch_array($group_query)){
                              ?>
                              <option value="<?= $group_res['pId'] ?>" <? if ($group_res['pId'] == $group) {echo "selected";} ?>>
                              <?= $group_res['vProjectName'] ?>
                              </option>
                           <?php } ?>
                           </select> -->
                           <?php $currentGroup = $group; // Store current group in a JS-friendly format ?>
                           <select name="group_assign" id="group_assign" class="select-styl1 ForwardGroup" style="width:180px;">
                              <option value="">Select Department</option>
                              <?php
                              $group_query = assign_department();
                              while ($group_res = mysqli_fetch_array($group_query)) {
                              ?>
                                 <option value="<?= $group_res['pId'] ?>" <?= ($group_res['pId'] == $group) ? "selected" : "" ?>>
                                    <?= $group_res['vProjectName'] ?>
                                 </option>
                              <?php } ?>
                           </select>

                           <!-- Textarea for remark, hidden by default -->
                           <div class="remarkContainer" style="margin-top:10px; display:none;">
                              <label for="new_remark">Remark:</label><br>
                              <textarea name="new_remark" id="new_remark" class="input-style1 new-remark" rows="3" cols="40" placeholder="Enter reason..."></textarea>
                           </div>
                        </td>
                        <td class="left   boder0-left">
                           <label><strong>Status of complaint </strong><em>*</em></label>
                           <div class="log-case">
                              <select name="status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)">
                                 <option value="0">Select Status</option>
                                 <?php $ticketstatus_query = web_ticketstatus($subString,$subString2);
                                 while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                                    <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status) {
                                    echo "selected";
                                    } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </td>
                        </tr>
                     </tr>
                     <tr>
                        <td width="50%" class="left  boder0-right">
                           <label><strong>Agent Remarks</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $remark ?></div>
                        </td>
                        <td>
                           <label><strong>Back office Remark</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $backoffice_remark ?></div>
                        </td>
                     </tr>
                     <tr>
                        <td class="left  boder0-left">
                           <label><strong>Supervisor Remark</strong> <em>*</em> </label>
                           <div class="log-case" id=""><br><?= $supervisor_remark ?></div>
                        </td>
                        <td colspan="2" class="left  boder0-left">
                           <label><strong> Admin Remarks </strong><em>*</em> </label>
                           <? if (!empty($v_OverAllRemark)) :?>
                              <div class="log-case" id="">
                                 <textarea name="v_OverAllRemark" class="input-style1" id="v_OverAllRemark" type="text" style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"><?=$v_OverAllRemark?></textarea>
                              </div>
                           <? else : ?>
                              <div class="log-case" id="">
                                 <textarea name="v_OverAllRemark" class="input-style1" id="v_OverAllRemark" type="text" style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"></textarea>
                              </div>
                           <? endif; ?>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         <?php } ?>
         <!-- Feedback from cutstomer in case case resolved -->
         <center>
            <input name="Update" id="update_btn" type="submit" value="Update" class="button-orange1" style="float:inherit;" onclick="return validate_existing(<?php echo $groupid;?>)" />&nbsp;
            <input type="button" value="Print Case" onclick="printdiv('rightpanels');" class="button-orange1" style="float:none;">
         </center>
         </form>
   <input type="hidden" id="save_current_status" value="<?= $status_type_ ?>">
   <!-- ######################        ADD MORE INTERACTION SCETION FORM SART        ############################ -->
   <form name="addInteraction_form" id="addInteraction_form" enctype="multipart/form-data" method="post" style="display: none">
      <input type="hidden" name="docket_no" id="docket_no" value="<?= $ticketid_old_case ?>">
      <input type="hidden" name="rowid" id="rowid" value="0">
      <input type="hidden" name="source_id" id="source_id" value="<?= $source ?>">
      <input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id ?>">
      <input type="hidden" name="c_mobile" id="c_mobile" value="<?= $phone ?>">
      <input type="hidden" name="c_email" id="c_email" value="<?= $email ?>">
      <input type="hidden" name="c_full_name" id="c_full_name" value="<?= $first_name . ' ' . $last_name ?>">
      <input type="hidden" name="type" value="<?=$type?>">
      <input type="hidden" name="status_old" value="<?=$status_type_?>">
      <input type="hidden" name="action" id="interaction_remark_form" value="interaction_remark_form">
      <div class="old-customer-simple-table">
         <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
            <span style="float:left">
               Add New Remark
            </span>
            <span style="float:right">
               <button type="button" onclick="$('#addInteraction_form').hide();" class="button-orange1" style="float: right; padding: 0px;">close</button>
            </span>
         </span>
         <table class="tableview tableview-2 main-form new-customer">
            <tr>
               <?php $ticketstatus_query = web_ticketstatus($subString,$subString2); ?>
               <td class="left  boder0-left" colspan="3">
                  <label>Status of complaint <em>*</em></label>
                  <div class="log-case">
                     <select name="inte_status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)" required>
                        <?php
                        while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                           <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status_type_) { echo "selected"; } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </td>
            </tr>
            <tr>
               <td colspan="3">
                  <? if ($groupid == $Agent_groupId) : ?>
                     <label>Remarks/Feedback<em>*</em> </label>
                  <? elseif ($groupid == $NonLogin_groupId) : ?>
                     <label>Backoffice Last Level Remarks/Feedback<em>*</em> </label>
                  <? elseif ($groupid == $Backoffice_groupId) : ?>
                     <label>Backoffice Remarks/Feedback<em>*</em> </label>
                  <? elseif ($groupid == $Admin_groupId) : ?>
                     <label>Remarks/Feedback<em>*</em> </label>
                  <? endif; ?>
                  <div class="log-case">
                     <textarea name="interaction_remark" class="input-style1" id="interaction_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 708px;height: 50px;resize: none;"><?= $customer_remark ?></textarea>
                  </div>
               </td>
            </tr>
            <tr>
               <!-- ADDED the code for the call back [vastvikta][21-03-2025] -->
               <td colspan="3">
               <label>Call Back</label>
                     <div class="log-case" id="category_div">
                        <input type="checkbox" name="callbk" id="callbk"
                           onClick="if(this.checked==true){ $('.cb').css('display',''); }else{ $('.cb').css('display','none'); }"
                           value="1" <? if (isset($_POST['callbk']) == '1') {
                              echo "checked";
                           } ?> />
                     </div>
                     <div class="log-case cb" style="margin-left:5px;display:<?= $displayy ?>;">
                        <label class="cb">Call Back Time</label>
                        <input type=text name='cb_date' id='cb_date' value='<?= $_POST['cb_date'] ?>' size='15'
                           class='date_class input-style1' autocomplete="off">
                           <input type="hidden" name="caller_id" value="<?=$phone?>">
                          </div>
               </tr>
            <tr>
               <td colspan="3">
                  <input type="submit" class="button-orange1" name="remark_button" id="remark_button" value="Save New Remarks">
                  <span id="responseMessage2"></span>
               </td>
            </tr>
         </table>
      </div>
   </form>
   <!-- ######################        ADD MORE INTERACTION SCETION FORM CLOSE        ############################ -->
   <!-- ######################        FEEDBACK SCETION FORM SART        ############################ -->
   <?php
   $customer_feedback = false;
   if ($customer_feedback){?>
      <form name="feedback_form" id="<?= ($groupid == "070000" ? 'feedback_form' : 'feedback_form_notallow')  ?>" enctype="multipart/form-data" method="post" style="display: none;">
         <input type="hidden" name="ticket_no" id="ticket_no" value="<?= $ticketid ?>">
         <input type="hidden" name="feed_source_id" id="source_id" value="<?= $source ?>">
         <input type="hidden" name="feed_customer_id" id="customer_id" value="<?= $customer_id ?>">
         <input type="hidden" name="feed_email" id="feed_email" value="<?= $email ?>">
         <input type="hidden" name="status_type_" id="feed_current_status_type_" value="<?= $status_type_ ?>">
         <div class="old-customer-simple-table">
            <span style="float:left;">
               Customer Feedback
            </span>
            <span style="float:right;">
               <button type="button" onclick="$('#feedback_form').hide();" class="button-orange1" style="float: right; padding: 0px;">close</button>
            </span>
            <table class="tableview tableview-2 main-form new-customer">
               <tr>
                  <td colspan="2">
                     <label><input type="radio" name="feedback" value="1" onchange="set_status_values('1')"> Satisfied </label>
                     <label><input type="radio" name="feedback" value="0" onchange="set_status_values('0')"> No Satisfied </label>
                     <label><input type="radio" name="feedback" checked value="2" onchange="set_status_values('2')"> No Response </label>
                  </td>
                  <td class="left  boder0-right">
                     <label>Status of complaint <em>*</em></label>
                     <div class="log-case">
                        <select name="status_type_" id="current_status_type_" class="select-styl1" disabled="disabled" style="width:180px;">
                           <option value="0">Select Status</option>
                           <?
                           $ticketstatus_query = mysqli_query($link, "select id, ticketstatus from $db.web_ticketstatus where status='1' AND id NOT IN (1,4) ORDER BY ticketstatus ASC");
                           while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)){?>
                              <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status) { echo "selected"; } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td colspan="3">
                     <label>Customer Feedback Remarks <em>*</em> </label>
                     <div class="log-case">
                        <textarea name="customer_remark" class="input-style1" id="customer_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 533px;height: 50px;resize: none;"><?= $customer_remark ?></textarea>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td colspan="3">
                     <input type="submit" class="button-orange1" name="feedback_button" id="feedback_button" value="Save Feedback">
                     <span id="responseMessage"></span>
                  </td>
               </tr>
            </table>
         </div>
      </form>
   <?php } ?>
   <!-- ######################        FEEDBACK SCETION FORM CLOSE        ############################ -->
   <br>
   <?php if (isset($docket_no)){ ?>
      <div id="ticket_history_docket" style="max-height: 300px;overflow: scroll;">
         <?php
         if ($customer) {
            $ticket_querys = get_ticket($customer);

         ?>
            <table class="tableview tableview-2 main-form new-customer">
               <tbody>
                  <tr class="background">
                     <td align="center">Case Id</td>
                     <td align="center">Merge Cases</td>
                     <td align="center">Type</td>
                     <td align="center">SubCategory</td>
                     <td align="center">Status</td>
                     <td align="center">Created On</td>
                     <td align="center">Department</td>
                     <td align="center">View</td>
                     <td align="center">Action</td>
                  </tr>
                  <?php while ($ticket_res = mysqli_fetch_array($ticket_querys)){
                    $token =  base64_encode('web_case_detail');
                    $deid = base64_encode($ticket_res['merge_ticketId']);
                    $mid = base64_encode($ticket_res['ticketid']);
                     ?>
                     <tr style="background: ; color: ;">
                        <td align="center"><a href="helpdesk_index.php?token=<?=$token;?>&id=<?=$mid?>" style="padding-left: 10px;" target="_blank"><?= $ticket_res['ticketid'] ?></a></td>
                        <td><?php if($ticket_res['merge_ticketId'] != ''){?>
                        <a href="../helpdesk_index.php?token=<?=$token;?>&id=<?=$deid?>" style="padding-left: 10px;" target="_blank">
                        <?php echo $ticket_res['merge_ticketId'];
                        }else{ echo 'N/A';  }?></td>
                        <td align="center"><?= $ticket_res['vCaseType'] ?></td>
                        <td align="center"><?= category($ticket_res['vCategory']) ?></td>
                        <td align="center"><?= ticketstatus($ticket_res['iCaseStatus']) ?></td>
                        <td align="center">
                           <?= date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate'])) ?></td>
                        <td align="center"><?= department($ticket_res['vProjectID'])?></td>
                        <td align="center"><a href="helpdesk/interaction_view.php?docketid=<?=$ticket_res['ticketid']?>&mode=<?=$source?>" class="ico-interaction2" style="padding-left: 10px;"><i class="fas fa-plus"></i></a></td>
                        <td align="center">
                           <? if ($ticket_res['ticketid'] == $ticketid_old_case) : ?>
                              <a href="#addInteraction_form" style="color: #222; text-decoration: none;" onclick="addmore_interaction('<?= $ticket_res['ticketid'] ?>', '<?= $ticket_res['vCustomerID'] ?>', '<?= $ticket_res['i_source'] ?>', '<?= $ticket_res['iPID'] ?>')"><img src="<?=$SiteURL?>/public/images/icons8-add-30.png" title="Add Remark"></a>
                           <? endif; ?>
                          <?php if($ticket_res['merge_ticketId'] == '' && $status != '3' && ($groupid == '080000' || $groupid == '0000')){?>
                          <a href="javascript:void(0);"style="padding-left: 10px;" onclick="addMergeTicket('<?= $ticket_res['ticketid'] ?>', '<?= $ticket_res['vCustomerID'] ?>','<?= $ticket_res['i_source'] ?>','<?=$ticket_res['iCaseStatus'];?>')" title="Merge Ticket">
                              <img src="<?=$SiteURL?>/public/images/icons8-merge-30.png" title="Merge Ticket">
                           </a>
                           <?php } ?>
                        </td>
                     </tr>
                  <?php } ?>
               </tbody>
            </table>
         <?php } ?>
      </div>
   <?php } ?>
   
   <!-- Case Audit History :: Farhan Akhtar [16-04-2025]  -->
   <br>
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Case Audit</span>
   <div class="old-customer-table">
      <?php
          $result = getCaseAuditHistory($link, $db, $ticketid);
          $rec_count = mysqli_num_rows($result);
      ?>
      <table class="tableview tableview-2 main-form new-customer">
      <tbody>
      <tr class="background" style="border-top:none">
         <td>S.No.</td>
         <td>DateTime</td>
         <td>Action</td>
         <td>Remark</td>      
         <td>IP Addresss</td>
         <td>Action By</td>
      </tr>
      <?php
      if($rec_count!=0)
      {
         while($data=mysqli_fetch_array($result)){ 
            $sno++;?>
            <tr>
               <td><?php echo $sno;?></td>
               <td><?=makeDateInddmmyyyy($data['created_on'])?></td>
               <td><?=$data['action']?></td>
               <td><?=$data['comments']?></td>
               <td><?=$data['ip_address']?></td>
               <td><?=get_username($data['user_id'])?></td>
            </tr>
         <?php }                        
      }else{   ?>
         <tr>
            <td colspan="6">No Data</td>
         </tr>
      <?php }?>
   </tbody>
   </table>
   </div>

   <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Case History</span>
   <div class="old-customer-table">
      <?php
          $result = getCaseInteractionCount($link, $db, $ticketid);
          $rec_count = mysqli_num_rows($result);
      ?>
      <table class="tableview tableview-2 main-form new-customer">
      <tbody>
      <tr class="background" style="border-top:none">
         <td>S.No.</td>
         <td>DateTime</td>
         <td>Action By</td>
         <td>Action</td>
         <td>Remark</td>      
         <td>Status</td>
         <td>Mode</td>
         <? if($mode == 1):?>
         <td>Recording</td>
         <?endif;?>
      </tr>
      <?php
      if($rec_count!=0)
      {
         while($data=mysqli_fetch_array($result)){ 
            $sno++;?>
            <tr>
               <td><?php echo $sno;?></td>
               <td><?=makeDateInddmmyyyy($data['created_date'])?></td>
               <td><?=ucfirst($data['created_by'])?></td>
               <!-- <td><?=ucfirst($data['created_by'])?><br> <?php echo $data['interacation_type'] ?> <?php echo empty($data['list_id']) ? '' : ($data['list_id']=='1000' ? 'IN' : 'OUT'); ?> </td> -->
               <td><?=$data['action']?></td>
               <td><?=wordwrap(ucfirst($data['remark']), 25,"<br>\n") ?>  </td>
               <td><?=ticketstatus($data['current_status'])?></td>
               <td><?=source($data['mode_of_interaction'])?></td>
               <? if($mode == 1):?>
               <td><?php
               $recording_filename  =$data['recording_filename'];
               $org_filename=getFileName($recording_filename);
               $filename_r = "http://".$ip.$org_filename;
               if(!empty($recording_filename))
                  {
               ?>
                  <a download="" href="<?=$filename_r?>" target="_blank">  
                  <img src='images/playsound.png' >
            <?php }?>
            </td>
            <?endif;?>
            </tr>
         <?php }                        
      }else{   ?>
         <tr>
            <td colspan="4">No Data</td>
         </tr>
      <?php }?>
   </tbody>
   </table>
   </div>
   <!----START---->
   <? $displayy="none"; 
   if($_GET['action']=="IVR"){ 
      $displayy=""; 
   }else{ 
      $displayy="none"; 
   } 
   ?>
   <div class="old-customer-simple-table " style="display: <?php echo $displayy;?>">
      <form method="POST" name="disposeAction" id="disposeAction">
      <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Disposition</span>
         <table class="tableview tableview-2 main-form new-customer">
            <tbody>
            <tr>
               <td width="50%" class="left  boder0-right" >
                  <label>Disposition </label>
                  <? 
                     $qdispo="select V_DISPO,V_DISPOSITION from $db_asterisk.tbl_disposition where I_Status=1 order by V_DISPOSITION asc ;";
                     $q=mysqli_query($link,$qdispo); ?>
                  <div class="log-case">
                     <select class="select-styl1" name="disposition" id="disposition">
                     <option value="0">Select Disposition <em>*</em> </option>
                     <? while($qq=mysqli_fetch_array($q)) { ?>
                     <option value="<?=$qq['V_DISPO']?>" <? if($qq['V_DISPO']==$_POST['disposition']){ echo "selected"; }?> ><?=strtolower($qq['V_DISPOSITION'])?></option>
                     <? } ?>
                     </select>
                     
                  </div>
               </td>
               <td width="50%" class="left  boder0-right" colspan="2">
                  <label>Remarks <em>*</em> </label>
                  <div class="log-case" id="">
                     <textarea name="dispose_remark" id="dispose_remark" class="input-style1" style="margin: 0px; width: 552px; height: 90px;"><?=$dispose_remark?></textarea>
                  </div>
               </td>
            </tr>
            <tr>
               <td colspan="2" class="left boder0-right">
                  <? //print_r($_SESSION); 
                  // if (isset($agent) == "") $agent = isset($_SESSION['VD_login']);
                  $agent = $_SESSION['VD_login'];
                  //if(isset($ticketid12) && $ticketid12!='') { 
                     if($customer_id!="")
                     {
                        $customerid=$customer_id;
                     }else if($customerid!="")
                     {
                        $customerid=$customer_id;
                     }
                     ?>
                  <span style="display:block; text-align:center;" >
                  &nbsp;
                  <input name="btnDispose" type="submit" value="Dispose" class="button-orange1" onclick="return validate_dispose()" style="float:inherit;"  />
                  <input type="hidden" name="caller_id" value="<?=$phone?>">
                  <input type="hidden" name="action" value="Dispose_Submit"/>
                  <input type="hidden" name="agent" value="<?=$agent?>"/>
                  <input type="hidden" name="list_id" id="list_id" value="<?=$_GET['list_id']?>">
                  <input type="hidden" name="docket_no" id="docket_no" value="<?=$docket_no?>">
                  <input type="hidden" name="campaignForPop" id="campaignForPop" value="<?=$_GET['campaignForPop']?>">
                  <input type="hidden" name="lead_id" id="lead_id" value="<?=$_GET['lead_id']?>">
                  <input type="hidden" name="vendor_lead_code" id="vendor_lead_code" value="<?=$_GET['vendor_lead_code']?>">
                  <input type="hidden" name="file" id="file" value="<?=$_GET['file']?>">
                  <input type="hidden" name="customerid" id="customerid_new" value="<?=$customerid?>">
                  <input type="hidden" name="file" id="file" value="<?= $_GET['file'] ?>">
                  <!-- value assign docket no [aarti][20-07-2024]-->
                  <input type="hidden" name="docket_no_new" id="docket_no_new" value="<?=$docket_no?>">
                  <input type="hidden" name="email" id="email" value="<?= $email ?>">
                  </span>
                  <!-- Modified by farhan on 27-06-2024 -->
                  <input type="hidden" name="fname" id="fname" value="<?= $first_name?>">
                  <input type="hidden" name="lname" id="lname" value="<?= $last_name ?>">
                  <!-- End -->
                  <? // } ?>
               </td>
            </tr>
            </tbody>
         </table>
      </form>
   </div>
   <!--END---------->
   <div class="container-fluid">
      <div class="row">
         <div class="col text-center">
            <h1><b>INTERACTION HISTORY</b></h1>
         </div>
      </div>
   </div>
            <!--- Outgoing Email----->
   <div class="old-customer-table">
      <h6>INTERACTION HISTORY</h6>
      <?php 
      $ress = get_interaction_data($ticketid);
      $numesms = mysqli_num_rows($ress);
      ?>
      <div <? if ($numesms > 4) { ?>style="height: 300px; overflow-y: auto; width:100% !important;
                        border-bottom:1px solid #d4d4d4;" <? } ?>>
         <table width="685" class="tableview tableview-2 main-form">
            <tbody>
               <tr bgcolor="#dddddd" class="background">
                  <td width="8%" align="center">
                     <div align="left"><b>Channel Type</b></div>
                  </td>
                  <td width="6%" align="center">
                     <div align="left"><b>From</b></div>
                  </td>
                  <td width="17%" align="center">
                     <div align="left"><b>Subject</b></div>
                  </td>
                  <td width="8%" align="center">
                     <div align="left"><b>Created By</b></div>
                  </td>
                  <td width="8%" align="center">
                     <div align="left"><b>DateTime</b></div>
                  </td>
               </tr>
               <?php
               $index = 1;
               while ($row = mysqli_fetch_array($ress)) {
                  $ID = $row['interact_id'];
               ?>
               <!-- updated icons in the interaction history [vastvikta][18-04-2025] -->
               <tr bgcolor="#e8eeff">
                     <td bgcolor="<?= $rowcol ?>" class="normaltextabhi">
                        <?php if($row['intraction_type'] == 'email'){?>
                           <svg  viewBox="0 0 48 48" width="20px" xmlns="http://www.w3.org/2000/svg"><path d="M45,16.2l-5,2.75l-5,4.75L35,40h7c1.657,0,3-1.343,3-3V16.2z" fill="#4caf50"/><path d="M3,16.2l3.614,1.71L13,23.7V40H6c-1.657,0-3-1.343-3-3V16.2z" fill="#1e88e5"/><polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"/><path d="M3,12.298V16.2l10,7.5V11.2L9.876,8.859C9.132,8.301,8.228,8,7.298,8h0C4.924,8,3,9.924,3,12.298z" fill="#c62828"/><path d="M45,12.298V16.2l-10,7.5V11.2l3.124-2.341C38.868,8.301,39.772,8,40.702,8h0 C43.076,8,45,9.924,45,12.298z" fill="#fbc02d"/></svg>
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'SMS'){?>
                           <img src="../public/images/chat.png" width="20" border='0' title="Reply">
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'Whatsapp'){?>
                           <img src="../public/images/whatsapp.png" width="20" border='0' title="Reply">
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'instagram'){?>
                           <img src="../public/images/insta.png" width="20" border='0' title="Reply">
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'messenger'){?>
                           <img src="../public/images/messenger_send.png" width="20" border='0' title="Reply">
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'webchat'){?>
                           <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM128 208a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm128 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm96 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <? }else if($row['intraction_type'] == 'voicecall'){?>
                           <svg xmlns="http://www.w3.org/2000/svg" height="1.5em" viewBox="0 0 512 512"><style>svg{fill:#0d0080}</style><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
                           <?php if($row['type'] == 'IN'){?>
                              <img src="../public/images/reply.png" width="14" border='0' title="Reply">
                           <? }else{ ?>
                              <img src="../public/images/newemail.png" width="15" border="0" title="Forward">
                           <?php }?>
                        <?php  }?>
                     </td>
                     <td bgcolor="<?= $rowcol ?>" class="normaltextabhi">
                        <?php if($row['intraction_type'] == 'email'){?>
                           <?= $row['email'] ?>&nbsp;
                        <? }else{ ?>
                           <?= $row['mobile'] ?>&nbsp;
                        <?php }?>
                     </td>
                     <td bgcolor="<?= $rowcol ?>" class="normaltextabhi" >
                        <?php if ($row['intraction_type'] == 'email') {
                        if($row['type'] == 'IN'){?>

                            <a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/subjectpopup.php?id=<?=$ID?>&showdiv=1','_blank','height=550,width=900,scrollbars=0')" class='cptext'>
                            <?=$row['remarks'];?></a>
                         <?php }else{?>
                          
                            <a href='javascript:void(0)' onClick="JavaScript:window.open('helpdesk/web_email_dess.php?id=<?=$ID?>&iid=<?=$ID?>&type=out','_blank','height=550,width=900,scrollbars=0')" class='cptext'>
                            <?=$row['remarks'];?></a>

                         <?php }?>
                         <!-- updated condition for in and out type [vastvikta][06-05-2025] -->
                        <?php } else if ($row['intraction_type'] == 'Whatsapp') {
                           if($row['type'] == 'IN'){
                           $sql = "SELECT * FROM $db.whatsapp_in_queue where id='$ID'";
                           }else{
                           $sql = "SELECT * FROM $db.whatsapp_out_queue where id='$ID'";
                           }
                           $totalQuery = mysqli_query($link, $sql);
                           $rowwhats = mysqli_fetch_assoc($totalQuery);
                           if($row['type'] == 'IN'){
                           $send_from= $rowwhats['send_from'];
                           $send_to= $rowwhats['send_to'];
                           }else{
                              $send_from= $rowwhats['send_to'];
                           $send_to=  $rowwhats['send_from'];
                           }
                           $messageid = $rowwhats['id'];
                           ?>
                           <a style="text-decoration: none;" href="omnichannel_config/web_sent_whatsapp.php?i_WhatsAppID=<?=$messageid?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2">
                           <?=$row['remarks']; ?></a>

                           <?php } else if ($row['intraction_type'] == 'instagram') {
                           if($row['type'] == 'IN'){
                           $sql = "SELECT * FROM $db.instagram_in_queue where id='$ID'";}
                           else{
                              $sql = "SELECT * FROM $db.instagram_out_queue where id='$ID'";
                           }
                           $totalQuery = mysqli_query($link, $sql);
                           $rowwhats = mysqli_fetch_assoc($totalQuery);
                           
                           if($row['type'] == 'IN'){
                              $send_from= $rowwhats['send_from'];
                              $send_to= $rowwhats['send_to'];
                           }else{
                              $send_from= $rowwhats['send_to'];
                              $send_to=  $rowwhats['send_from'];
                           }
                           $messageid = $rowwhats['id'];
                           ?>
                           <a style="text-decoration: none;" href="omnichannel_config/web_sent_instagram.php?ID=<?=$messageid?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$messageid?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2">
                           <?=$row['remarks']; ?></a>

                        <?php } else if ($row['intraction_type'] == 'messenger') {
                           if($row['type'] == 'IN'){
                           $sql = "SELECT * FROM $db.messenger_in_queue where id='$ID'";
                           }else{
                              $sql = "SELECT * FROM $db.messenger_out_queue where id='$ID'";
                           }
                           $totalQuery = mysqli_query($link, $sql);
                           $rowwhats = mysqli_fetch_assoc($totalQuery);
                           
                           if($row['type'] == 'IN'){
                              $send_from= $rowwhats['send_from'];
                              $send_to= $rowwhats['send_to'];
                           }else{
                              $send_from= $rowwhats['send_to'];
                              $send_to=  $rowwhats['send_from'];
                           }
                           $messageid = $rowwhats['id'];
                           ?>
                           <a style="text-decoration: none;" href="omnichannel_config/web_sent_messanger.php?ID=<?=$messageid?>&send_to=<?=$send_from?>&send_from=<?=$send_to?>&messageid=<?=$i_WhatsAppID?>&id=&account_sender_id=<?=$send_to?>&showdiv=1" class="ico-interaction2">
                           <?=$row['remarks']; ?></a>

                        <?php } else if ($row['intraction_type'] == 'webchat') {
                           $mobile= $row['mobile'];
                           ?>
                           <a class="ico-interaction2" href="omnichannel_config/chat_history.php?phone=<?=$mobile?>&caseid=<?=$docket_no?>&session_id="><?=$row['remarks'];?></a>
                           <?php } else { ?>
                              <!-- Make the link visible but non-clickable [vastvikta][11-02-2025]-->
                              <span style="pointer-events: none; color: inherit;"><?= $row['remarks']; ?></span>
                           <?php } ?>

                     </td>
                     <!-- code for displaying agent name created by [vastvikta][06-05-2025] -->
                     <?php 
                     $agentname = get_agent_name($row['created_by']);?>
                     <td bgcolor="<?= $rowcol ?>" class="normaltextabhi">
                        <?= $agentname ?>
                     </td>
                     <td bgcolor="<?= $rowcol ?>" class="normaltextabhi">
                        <?= date("d-m-Y H:i:s", strtotime($row['created_date'])) ?>&nbsp;
                     </td>
                  </tr>
               <?php
                  $index++;
               } ?>
               <? if ($numesms <= 0) { ?>
                  <tr>
                     <td colspan="8" align="center">No record found !!</td>
                  </tr>
               <? } ?>
            </tbody>
         </table>
      </div>
   </div>
   <!--- Incoming Email----->
   <div class="old-customer-table">
      <h6>Incoming Email</h6>
      <?php
      $ress =  get_email_information($id);
      $numesms = mysqli_num_rows($ress);
      $groupid = $_SESSION['user_group'];
      $index = 1;
      $documents_str = "";
      $doc_array = array();
      while ($row = mysqli_fetch_array($ress)) {
         $v_fromemail = $row['v_fromemail'];
         $half_from = substr($v_fromemail, 0, strpos($v_fromemail, '@'));
         $reply_to = $v_fromemail;
         $v_toemail = $row['v_toemail'];
         $half_to   = substr($v_toemail, 0, strpos($v_toemail, '@'));
         $d_email_date = $row['d_email_date'];
         $v_subject = $row['v_subject'];
         $v_subject = substr($v_subject, 0, 20);
         $ID = $row['EMAIL_ID'];
         $emailtype = $row['email_type'];
         if ($v_fromemail == $v_toemail && $emailtype == 'IN') {
         } else {
            $V_Attachment = $row['V_rule'];
            $body = addslashes(substr($row['v_body'], 0, 50));
            $i_templateid = getTemplateInfo("v_templateName", $row['i_templateid']);
            if ($emailtype == 'IN') {
               $newmailto = $v_fromemail;
            } else {
               $newmailto = $v_toemail;
            }
            if ($rownum % 2 == 0)
               $rowcol = "white";
            else
               $rowcol = "#EFEFEF";
            ?>
            <table width="100%" border="0" class="tableview tableview-2">
               <tr style="height: 20px;   background: #f5f5f5 !important;
                  font-weight: bold;
                  color: #222;
                  text-align: left;
                  border-top: 2px solid #004b8b82;">
                  <td width="30%" style="font-weight: bold;">Customer Email</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Subject</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Date</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Time</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Reply</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Forward</td>
               </tr>
               <tr>
                  <td width="10%" style="padding: 1rem;"><?= $v_fromemail ?></td>
                  <td width="10%" style="padding: 1rem; ">
                     <a href='javascript:void(0)' onClick="JavaScript:window.open('helpdesk/web_email_dess.php?id=<?= $ID ?>&iid=<?= $id ?>','_blank','height=550, width=900,scrollbars=0')" class='cptext'><?= $v_subject ?></a>
                  </td>
                  <td width="70%" style="padding: 1rem; ">
                     <?= date("d-M-Y", strtotime($d_email_date)) ?></td>
                  <td width="70%" style="padding: 1rem; ">
                     <?= date("H:i:s", strtotime($d_email_date)) ?></td>
                  <? if ((($groupid == '080000') || ($groupid == '0000'))) { ?>
                 
                  <td width="70%" style="padding: 1rem; ">
                     <a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/web_send_email_reply.php?replyid=<?= $ID ?>&iid=<?= $id ?>&reply_to=<?= $reply_to ?>','_blank','height=550, width=900,scrollbars=0')" class='cptext'><img src="../public/images/reply.png" width="14" border='0' title="Reply"></a>
                  </td>
                 
                  <td width="70%" style="padding: 1rem; "><a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/web_send_email_reply.php?replyid=<?= $ID ?>&iid=<?= $id ?>&forward=&reply_to=','_blank','height=550, width=900,scrollbars=0')" class='cptext'><img src="../public/images/newemail.png" width="15" border='0' title="Forward"></a></td>
                  <? } else {
                     $reply_to = $v_fromemail; ?>
                  
                  <td width="70%" style="padding: 1rem; ">
                     <a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/web_send_email_reply.php?replyid=<?= $ID ?>&iid=<?= $id ?>&reply_to=<?= $reply_to ?>','_blank','height=550, width=900,scrollbars=0')" class='cptext'><img src="../public/images/reply.png" width="14" border='0' title="Reply"></a>
                  </td>
                  
                  <td width="70%" style="padding: 1rem; "><a href='javascript:void(0)' onClick="JavaScript:window.open('omnichannel_config/web_send_email_reply.php?replyid=<?= $ID ?>&iid=<?= $id ?>&forward=&reply_to=','_blank','height=550, width=900,scrollbars=0')" class='cptext'><img src="../public/images/newemail.png" width="15" border='0' title="Forward"></a></td>
                  <? } ?>
               </tr>
             
            </table>
      <?php
            $index++;
         } // end else here 
      }
      ?>
      <?php if ($numesms <= 0) { ?>
         <table width="100%" border="0" class="tableview tableview-2">
               <tr style="height: 20px;   background: #f5f5f5 !important;
                  font-weight: bold;
                  color: #222;
                  text-align: left;
                  border-top: 2px solid #004b8b82;">
                  <td width="30%" style="font-weight: bold;">Customer Email</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Subject</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Date</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Time</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Reply</td>
                  <td width="30%" style="padding: 0.6rem;  font-weight: bold;">Forward</td>
               </tr>
               <tr style="height: 20px;background: #fff">
                  <td align="center" colspan="6">No record found !!</td>
               </tr>
            </table>
      <?php } ?>
   </div>
   <!--- END Incoming Email----->
   <div id="rec_f" style="text-align:right; height:40px; width:70%; margin:auto;"></div>
   <script language="javascript">
      function cl12(nval,val){ 
         document.getElementById('rec_f').innerHTML="<embed height='40' width='100%' src='"+nval+"' type='audio/mpeg'>"; 
         return false;                 
      }
   </script>
   <!-- Disposition Display and used Disposition function for fetch deta in database -->
   <div class="old-customer-table">
      <h6>Disposition</h6>
      <?php
      $ress_incoming = get_disposition($customer_id,$ticketid);
      $numeqdk_incoming = mysqli_num_rows($ress_incoming);
      ?>
      <div style="height: 100px; overflow-y: auto; width:100% !important; ">
         <table width="685" class="tableview tableview-2 main-form">
            <tbody>
               <tr bgcolor="#dddddd" class="background">
                  <td width="10%" align="center">
                     <div align="left"><b>S.No.</b></div>
                  </td>
                  <td width="21%" align="center">
                     <div align="left"><b>Disposition</b></div>
                  </td>
                  <td width="30%" align="center">
                     <div align="left"><b>Start Time</b></div>
                  </td>
                  <td width="25%" align="center">
                     <div align="left"><b>Remarks</b></div>
                  </td>
                  <td width="14%" align="center">
                     <div align="left"><b>Recorded File</b></div>
                  </td>
               </tr>
               <?
               $sno = 0;
               while ($rowincoming = mysqli_fetch_array($ress_incoming)) {
                  $disposition = $rowincoming['disposition'];
                  $entry_date = $rowincoming['entry_date'];
                  $remarks = $rowincoming['remarks'];
                  $filename = $rowincoming['filename'];
                  $org_filename = getFileName($filename);
                  $filenamed = "http://" . $ip . $org_filename;

                  $sno++;
               ?>
                  <tr>
                     <td width="10%" align="center">
                        <div align="left"><b><?= $sno ?></b></div>
                     </td>
                     <td width="21%" align="center">
                        <div align="left"><b><?= $disposition ?></b></div>
                     </td>
                     <td width="30%" align="center">
                        <div align="left"><b><?= $entry_date ?></b></div>
                     </td>
                     <td width="25%" align="center">
                        <div align="left"><b><?= $remarks ?></b></div>
                     </td>
                     <td width="14%" align="center">
                     <?php
                        // Define the file path
                        $filePath = SmartFileName_voice($filename);

                        // Check if the file exists
                        if (file_exists($filePath)) {
                        ?>
                           <audio controls>
                              <source src="<?php echo $filePath; ?>" type="audio/mpeg">
                           </audio>
                        <?php
                        } else {
                           echo "No File";
                        }
                     ?>
                     </td>
                  </tr>
               <? } ?>
               <? if ($numeqdk_incoming <= 0) { ?>
                  <tr>
                     <td colspan="8" align="center">No record found !!</td>
                  </tr>
               <? } ?>
            </tbody>
         </table>
      </div>
   </div>

   <div class="old-customer-table">
      <h6>Documents</h6> 
      <a href="helpdesk/web_doctypeadd.php?opportunityid=<?=$id?>&doctype=4&pid=2&val=1" class="old-cs-link newdocument cboxElement" style="text-decoration:none">Add Document</a>
      <table width="685" class="tableview tableview-2 main-form">
      <tbody>
         <tr bgcolor="#dddddd"class="background">
            <td width="30%" align="center"><b>Document Name</b></td>
            <td width="15%" align="center"><b>Uploaded By</b></td>
            <td width="20%" align="center"><b>Uploaded On</b></td>
            <td width="20%" align="center"><b>Document</b></td>
         </tr>
      <?php
      $resopp = get_documents($id,$groupid);
      $rowcolopp			=	"";
      $rownumopp			=	0;
      $enddocuments = '';
      $documents = '';
      while($rowopp=mysqli_fetch_array($resopp)){
         $rownumopp=$rownumopp+1;
         $I_DocumentID          = $rowopp['I_DocumentID'];
         $V_Doc_Name=$rowopp['V_Doc_Name'];
         $V_Doc_Description=$rowopp['V_Doc_Description'];
         $V_Path=$rowopp['V_Path'];
         $v_uploadedFile=$rowopp['v_uploadedFile'];
         // $downloads	           ="document/$db";
         $v_uploadedFile=$rowopp['v_uploadedFile'];
         $exp_upload=explode(",",$v_uploadedFile);
         if(count($exp_upload)>1){
            for($k=0;$k<count($exp_upload);$k++){

               if($rowopp['I_DocumentType'] == '4'){
                  $f='../imap/'.$exp_upload[$k];
               }else{
                  $f=$exp_upload[$k];
               }
               

              $img="<img src=../public/images/download.jpg title='Click to download $f' border=0>";
              $downloadpath1.="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$img."</a> &nbsp;&nbsp;";
            }
         }else if(count($exp_upload)==1){

              
              if($rowopp['I_DocumentType'] == '4'){
                  $f='document/ensembler/'.$v_uploadedFile;
               }else{
                  $f=$v_uploadedFile;
               }

              $img="<img src=../public/images/download.jpg title='Click to download $f' border=0>";
              $downloadpath1="<a href='JavaScript:void(0)' onClick=\"JavaScript:window.open('$f')\" class='cptext' 'view','height=350, width=550,scrollbars=0'>".$v_uploadedFile.$img."</a>";
         }
         $cdateopp=$rowopp['I_UploadedON'];
         $madebyopp=$rowopp['I_UploadedBY'];
         $countopp=$countopp+1;

         if($rownumopp%2==0){
            $rowcolopp="white";
         }else{
            $rowcolopp="#EFEFEF";
         }

         $documents.='<tr bgcolor="#FFFFFF">
         <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$V_Doc_Name.'</td>
         <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$madebyopp.'</td>
         <td bgcolor="'.$rowcolopp.'" class="normaltextabhi">'.$cdateopp.'</td>
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
   </div>
   </div>
   <!-- Start Pop for merge ticketid -->
   <div id="myModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
       <!-- Modal content-->
       <div class="modal-content" style="width: 88%;">
         <div class="modal-header">
           <h5 class="modal-title">Merge Ticket</h5>
           <button type="button" class="close closemodal" data-dismiss="modal" style="border: unset;background-color: unset;color:black">&times;</button>
         </div>
         <div class="modal-body">
           <div id="success"></div>
            <form name="extension_submit" id="extension_submit" style="margin-top: 20px;margin-bottom: 15px;">
             <input type="hidden" name="marge_source" value="" id="marge_source">   
             <input type="hidden" name="marge_status_type" value="" id="marge_status_type">    
             <input type="hidden" name="marge_customerr" value="" id="marge_customerr">  
             <input type="hidden" name="marge_ticket" value="" id="marge_ticket"> 
             <div class="form-group row">
               <label for="exampleInputConfirmPassword2" class="col-sm-3 col-form-label">Merge Ticket</label>
               <div class="col-sm-9 select2-container-wrapper">
                  <select id="marge_select" name="marge_select" class="select-styl1 select2" style="width:180px"> 
                 </select>
               </div>
             </div>
             <div class="form-group row">
               <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Remarks/Feedback<em>*</em></label>
               <div class="col-sm-9">
                 <textarea name="merge_remarks" class="input-style1" id="merge_remarks" type="text" style="width: 270px;resize: none;"></textarea>
               </div>
             </div>
           </form>         
         </div>
         <div class="modal-footer" style="background-color:unset;">
           <!-- <button type="submit" class="btn btn-inverse-dark" id="handleClear">Clear</button> -->
             <input name="submit_form" type="submit" value="Save" class="btn btn-inverse-dark button-orange1" id="handleSubmitExtenstion" onclick="handleMergeSubmit()">
         </div>
       </div>
     </div>
   </div>
</div>
<script src="<?=$SiteURL?>public/js/backoffice.js"></script>
<!-- [vastvikta nishad][29-11-2024][code for the print case] -->
<script >

$(document).ready(function () {

const currentGroup = '<?= $currentGroup ?>';

$('.ForwardGroup').on('change', function() {
   const selectedGroup = $(this).val();

   if (selectedGroup && selectedGroup !== currentGroup) {
      $('.remarkContainer').slideDown();
   } else {
      $('.remarkContainer').slideUp();
      $('.new-remark').val(''); // Optional: clear remark if hidden
   }
});

      $("#dropdown").on("click", function (e) {
         e.preventDefault();
         console.log('dropdpwn clicked');
         if ($(this).hasClass("open")) {
            $(this).removeClass("open");
            $(this).children("ul").slideUp("fast");
         } else {
            $(this).addClass("open");
            $(this).children("ul").slideDown("fast");
         }
      });

      // Close dropdown when clicking anywhere outside
      $(document).on("click", function (e) {
         if (!$(e.target).closest("#dropdown").length) { // Check if the click is outside the dropdown
            $("#dropdown").removeClass("open");
            $("#dropdown").children("ul").slideUp("fast");
         }
      });
     
   });
function printdiv(printpage) {
    // Fetch head.php content using AJAX
   
        // Prepare the head section with the fetched data
        var headstr = `<html><head>
         <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/<?=$dbtheme?>.css" />
         <!-- Common CSS -->
         <link href="<?=$SiteURL?>public/css/common.css" rel="stylesheet" />
         </head><body>`;
        var footstr = "</body></html>"; // Close the HTML document properly
        var newstr = $("." + printpage).html(); // Get the content of the specified class
        var oldstr = document.body.innerHTML; // Backup the current webpage content

        // Replace content with printable section
        document.body.innerHTML = headstr + newstr + footstr;
        console.log(document.body.innerHTML);
        window.print(); // Trigger the print dialog
        document.body.innerHTML = oldstr; // Restore original content

    return false; // Prevent default action
}

</script>

