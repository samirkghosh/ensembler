 <?php
/***
 * CREATE TICKET
 * Author: Aarti Ojha
 * Date: 04-03-2024
 * This file is handling create ticket flow /Insert /Update
 * 1.Ticket Create many channel thought like - facebook,whatsapp,webchat,sms,twitter
 * 2.Handling ticket history record
 * Please do not modify this file without permission.
**/

include "helpdesk/web_ticket_function.php"; //this file hanlde common function to fetch data and insert data
$query_string = [];
foreach ($_GET as $key => $value) {
   if ($key != 'customerid') {
      $query_string[$key] = $value;
   }
}
if (!empty($_GET['phone_number'])) {
   $phone_number = $_GET['phone_number'];
}
/**Get data from session**/

$reginoal_spoc = $_SESSION['reginoal_spoc'];
$groupid = $_SESSION['user_group'];
$vuserid = $_SESSION['userid'];
$name = $_SESSION['logged'];
$logedin_agent = $_SESSION['logged'];
$status = 1;
$customer_id = 0;
/* get Audit Record */


// need discussion with aarti ma'am [vastvikta][23-04-2025]
// need to discussion with samir sir
// if ($vuserid > 0 && $groupid > 0) {
//    if (!isset($_SESSION['loggedin_audit']) && $_SESSION['loggedin_audit'] != 1) {
//       $final_action = "$name User Logged In";
//       add_audit_log($vuserid, 'loggedin', 'null', 'login successess', $db);
//       $_SESSION['loggedin_audit'] = '1';
//    }
// }
$sql = "select AtxEmail from $db.uniuserprofile  where AtxUserID='$vuserid'";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res);
$AtxEmail = $row['AtxEmail'];
$_SESSION['login_email'] = $AtxEmail;
// close

extract($_POST);
extract($_GET);
extract($_REQUEST);

/** START 1 - this Function is working for when case is create from queue */
$info = Social_Media_Queue();
if (isset($info['social_type_id'])) {
   $social_type_id = $info['social_type_id'];
} else {
   $social_type_id = '';
}
if (isset($info['fact'])) {
   $fact = $info['fact'];
   
} else {
   $fact = '';
}
if (isset($info['last_name'])) {
   $last_name = $info['last_name'];
} else {
   $last_name = '';
}
if (isset($_GET['emailid'])) { //case create email thought
   $email = $info['email'];
   $d_email_date = $info['d_email_date'];
}
if (isset($_GET['twitterid'])) {
   $first_name = $info['first_name'];
   $twitterhandle = $info['twitterhandle'];
}
if (isset($_GET['whatsappid'])) {
   $first_name = $info['first_name'];
   $whatsappnumber = $info['whatsapphandle'];
   $phone_number = $whatsappnumber;
}
if (isset($_GET['chatid'])) {
   $email = $info['email'];
   $phone_number = $info['phone'];
   $first_name = $info['first_name'];
}
// this is for used instagram and messenger related data fetching[Aarti][19-11-2024]
if (isset($_GET['messengerid'])) {
   $messengerhandle = $info['messengerhandle'];
}
if (isset($_GET['instagramid'])) {
   $instagramhandle = $info['instagramhandle'];
}
//[vastvikta][03-12-2024]
//code for populating the mobile field in the sms case creation $sms_id = $_GET['smsid'];
if(isset($_GET['smsid'])){
   $sms_id = $_GET['smsid']; // Fetch the ID from the GET request
   $sql = "SELECT v_mobileNo FROM tbl_smsmessagesin WHERE i_id = '$sms_id'"; // Construct the query

   // Execute the query
   $res = mysqli_query($link, $sql);

   if ($res) { // Ensure the query executed successfully
      $row = mysqli_fetch_assoc($res);

      if (isset($row['v_mobileNo']) && !empty($row['v_mobileNo'])) { 
          // Assign only if 'v_mobileNo' is set and not empty
          $phone_number = $row['v_mobileNo'];
      }
   }
}
/************End*****************/
/** START 2 - in case of other third party option like chat getting customer id **/
// if (isset($_GET['customerid']) && $_GET['customerid'] != '' && isset($_GET['phone_number']) && !isset($_GET['chatid'])) {
if (isset($_GET['customerid']) && $_GET['customerid'] != '' || isset($_GET['phone_number'])) {
   $customerid = $_GET['customerid'];
   $response = customer_data(); // This function fetches customer data
   if (!empty($response)) {
      $fname = $response['fname'];
      $name = explode(" ", $fname);
      if (count($name) > 0) {
         $first_name = $name[0];
         $last_name = $name[1];
      } else {
         $first_name = $fname;
         $last_name = '';
      }
      $mobile = $response['mobile'];
      
      $age = $response['age_grp'];
      $gender = $response['gender'];
      $phone = $response['phone'];
      $address_1 = $response['address'];
      $address_2 = $response['v_Location'];
      $district = $response['district'];
      $villages = $response['v_Village']; //added code for fethching  sub county [vastvikta][10-12-2024]
      $date = $response['createddate'];
      $gender = $response['gender'];
      $twitterhandle = ($response['twitterhandle'] != '') ? $response['twitterhandle'] : $twitterhandle;
      $fbhandle = ($response['fbhandle'] != '') ? $response['fbhandle'] : $fbhandle;
      $useridd = ($response['userid'] != '') ? $response['userid'] : $useridd;
      $language = ($response['language'] != '') ? $response['language'] : $language;
      $customer_id = $response['AccountNumber'];
      $priority_user = $response['priority_user'];
      $business_number = $response['business_number'];
      $register_tpin = $response['tpin'];
      $passport_number = $response['passport_number'];
      $area = $response['area'];
      $town = $response['town'];
      $sms_number = $response['smshandle'];
      $nationality = $response['nationality'];
      $taxtype_id = $response['tax_id'];
      $company_name_case = $response['company_name'];
      $company_registration = $response['company_registration'];
      $email = $response['email'];
      $phone_number = $response['phone'];
      if (empty($info['whatsapphandle'])) {
         $whatsappnumber = $response['whatsapphandle'];
      }
      $messengerhandle = $response['messengerhandle']; //for messenger sender id fetch[Aarti][14-08-2024]
      $instagramhandle = $response['instagramhandle']; //for instagram sender id fetch[Aarti][19-11-2024]
   }
}
/************End*****************/
/***START 3 - For existing cases , populate all the values in a form ***/
if (isset($_POST['search-docket']) && isset($_POST['docket_no_new'])) {

   $res = search_docket(); // fetch ticketid data from database
   $docket_no = (trim($_POST['search-docket']) == '') ? $_POST['docket_no_new'] : $_POST['search-docket'];
   $customer_id = $res['vCustomerID'];
   $source = $res['i_source'];
   $account_no_ = $res['customer_account_no'];
   $phone_number = $res['phone'];
   $mobile = $res['mobile'];
   $name = explode(' ', $res['fname']); // Split fname into an array of two values seperated by space.
   $first_name = $name[0];
   $last_name = $name[1];
   $address_1 = $res['address'];
   $address_2 = $res['v_Location'];
   $district = $res['district'];
   $villages = $res['v_Village'];
   $type = $res['vCaseType'];
   $v_category = $res['vCategory'];
   $v_subcategory = $res['vSubCategory'];
   $group_assign = $res['vProjectID'];
   $v_remark_type = $res['vRemarks'];
   $languagename = $res['language'];
   $customer = $res['vCustomerID'];
   $fbhandle = $res['fbhandle'];
   $twitterhandle = $res['twitterhandle'];
   $status_type_ = $res['iCaseStatus'];
   $ticketid_old_case = $res['ticketid'];
   $organisation = $res['organization'];
   /*new screen related changes-07-09-23*/
   $business_number = $res['business_number'];
   $register_tpin = $res['tpin'];
   $passport_number = $res['passport_number'];
   $area = $res['area'];
   $town = $res['town'];
   $whatsappnumber = $res['whatsapphandle'];
   $sms_number = $res['smshandle'];
   $nationality = $res['nationality'];
   $taxtype_id = $res['tax_id'];
   $company_name_case = $res['company_name'];
   $company_registration = $res['company_registration'];
   $messengerhandle = $res['messengerhandle']; //for messenger sender id fetch[Aarti][14-08-2024]
   $instagramhandle = $res['instagramhandle']; //for instagram sender id fetch[Aarti][19-11-2024]
}
/************End*****************/

/* Get Account No if exist */
if (isset($_GET['phone_number']) && $_GET['phone_number'] != '') {
   $rmn_no = trim($_GET['phone_number']);
   $acc_sql = "SELECT * FROM $db.web_account_mapping WHERE rmn_no ='$rmn_no' ";
   $account_result = mysqli_query($link, $acc_sql);
}
$district_id = isset($_POST["district"]) ? $_POST["district"] : '';
$village_id = isset($_POST["villages"]) ? $_POST["villages"] : '';
// query_string used new_case_script.js file 
$query_string = array();
foreach ($_GET as $key => $value) {
   if ($key != 'customerid') {
      $query_string[$key] = $value;
   }
}

?>
<!-- Farhan Akhtar :: Modified on (16-10-2024) -->
<style type="text/css">
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
<!-- Farhan Akhtar :: Modified on (16-10-2024) -->
<!-- Ticket layout Html code start -->
<div class="style2-table">
   <!-- Farhan Akhtar :: Modified on (16-10-2024) -->
   <!-- <button class="button-33 btn trigger" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Content Messaging</button> -->
   <!-- Farhan Akhtar :: Modified on (16-10-2024) -->

    <!-- Farhan Akhtar :: Modified on (07-01-2025) :: Design is modified as per Samir Sir -->
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
    </div>
    <!-- Farhan Akhtar :: Modified on (07-01-2025) :: Design is modified as per Samir Sir -->
   <form method="post" action="" name="customerrr" id="customerrr" autocomplete="autocomplete_off_hack_sfr3!g">
      <input type="hidden" name="PHP_SELF" id="PHP_SELF" value="<?= $_SERVER['PHP_SELF'] ?>">
      <input type="hidden" name="query_string" id="query_string" value="<?= $query_string ?>">
      <input type="hidden" name="ticketid" id="ticketid" value="<?= $ticketid ?>">
      <input type="hidden" name="customerid" id="customerid" value="<?= $customer_id ?>">
      <?php if (isset($_GET['mr'])) { ?>
         <input type="hidden" name="mr" value="<?= $_GET['mr'] ?>">
         <?php if (isset($_GET['chatid'])) { ?>
            <input type="hidden" name="chatid" value="<?= $_GET['chatid'] ?>">
         <?php } else if (isset($_GET['emailid'])) { ?>
               <input type="hidden" name="emailid" value="<?= $_GET['emailid'] ?>">
         <?php } else if (isset($_GET['twitterid'])) { ?>
                  <input type="hidden" name="twitterid" value="<?= $_GET['twitterid'] ?>">
         <?php } else if (isset($_GET['voicemailid'])) { ?>
                     <input type="hidden" name="voicemailid" value="<?= $_GET['voicemailid'] ?>">
                     <!-- added code for fetching records on the basis of the registered phone number with voicemail [vastvikta][12-05-2025] -->
                     <?php $phone_number = $_GET['phone_number']; ?>
           
         <?php } else if (isset($_GET['facebookid'])) { ?>
                        <input type="hidden" name="facebookid" value="<?= $_GET['facebookid'] ?>">
         <?php } else if (isset($_GET['smsid'])) { ?>
                           <input type="hidden" name="smsid" value="<?= $_GET['smsid'] ?>">
         <?php } else if (isset($_GET['whatsappid'])) { ?>
               <input type="hidden" name="whatsappid" value="<?= $_GET['whatsappid'] ?>">
         <?php } else if (isset($_GET['messengerid'])) { ?>
            <input type="hidden" name="messengerid" value="<?= $_GET['messengerid'] ?>">
         <?php } else if (isset($_GET['instagramid'])) { ?>
            <input type="hidden" name="instagramid" value="<?= $_GET['instagramid'] ?>">
         <?php } 
      } ?>
      <!-- For check license access -->
      <?php $module_flag_customer = module_license('CUSTOMER PROFILE'); ?>
      <div class="old-customer-simple-table" <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>>
         <div class="row">
            <div class="col-sm-8">
               <div class="row">
                  <div class="col-sm-6"></div>
                  <div class="col-sm-6">
                     <center>
                        <span class="h4" style="color:#00000070;">
                           <!-- <?=strtoupper($_SESSION['companyName'])?> -->
                            HELB CALL CENTER</span>
                     </center>
                  </div>
               </div>
            </div>
            <!-- <?php
            $new_case_manual = base64_encode('new_case_manual');
            ?>
            <div class="col-sm-4">
               <span class="breadcrumb_1">
                  <label><input type="radio" class="no_disabled" name="case_presence" checked="checked"
                        onclick="$('.docket_show').css('display','none');$('#search_res').css('display','block');window.location.replace('helpdesk_index.php?token=<?php echo $new_case_manual; ?>');">
                     New Case
                  </label>

                  <label><input type="radio" class="no_disabled" name="case_presence"
                        onclick="$('.docket_show').css('display','block');$('#search_res').css('display','none'); $('.taxpayer_div').css('display','none');"
                        <?php if (isset($docket_no))
                           echo "checked"; ?>> Search by ticket no
                  </label>
               </span>
            </div> -->
         </div>
         <div class="errormanual" style="display: none"></div>
         <div class="successmanual" style="display: none"></div>
         <div class="breadcrumb_head" style="height:56px">
            <div class="row">
               <div class="col-sm-10">
                  <input type="text" id="search_res" name="search-box" maxlength="100"
                     onkeyup="search_customer(this.value,'customer')" value=""
                     placeholder="SEARCH CUSTOMER NAME, CALLER ID, EMAIL" class="select-styl1 search_res"
                     style="width: 320px;height:30px;border:none;padding: 10px;" autocomplete='off'>
                  <div id="search_result"
                     style="position: absolute; width: 100px; z-index: 999; background:#fff; border:1px solid #fff ;color:rgb(143 143 143); padding: 5px; width: 300px;display: none;">
                  </div>
               </div>
               <div class="col-sm-2">
                  <!-- Content Messaging !-->

               </div>
            </div>
            <span class="docket_show" style="display : none; border: none;">
               <!-- button-orange css on line no :328 : by farhan-->
               <div class="row">
                  <div class="col-sm-3" style="margin-right: 48px;">
                     <input type="text" name="search-docket" id="search-docket" maxlength="100"
                        placeholder="ENTER Case Id" class="select-styl1 search_res"
                        style="width: 300px;height:30px;border:none;padding: 10px" autocomplete='off'
                        value="<?= $docket_no ?>"> &nbsp; &nbsp;
                  </div>
                  <div class="col-sm-3">
                     <input type="submit" id="search_btn" value="Search" class="button-orange1">
                  </div>
               </div>
            </span>
            <div class="log-case taxpayer_div" style="">
               <input type="text" name="search-taxpayer" id="search-taxpayer" maxlength="100"
                  placeholder="Please Enter Company name,Co. registration,ERP ID" onkeyup="search_taxpayer(this.value)"
                  class="select-styl1" value=""
                  style="width: 197px;height: 30px;margin-right: 45px;border: none;margin-left: -37px;display: none;">
               &nbsp; &nbsp;
            </div>
         </div>
         <?php
         // This code for user already exists then Registered disable not edit mode[Aarti][24-07-2024]
         $query_dis = "select * from $db.web_accounts  where phone='$phone_number' ";
         $result_dis = mysqli_query($link, $query_dis);
         $row_dis = mysqli_num_rows($result_dis);
         if ($row_dis == 0) {
            $readonly = '';
            $disable = '';
         } else {
            $readonly = 'readonly';
            $disable = 'disabled';
         }
         ?>
         <table class="tableview tableview-2 main-form new-customer">
            <tbody>
               <tr>
                  <td class="left boder0-left">
                     <label>Registered Mobile Number <em>*</em></label>
                     <input name="phone" id="phone" maxlength="20" type="text" value="<?= $phone_number ?>"
                        class="input-style1" onKeyUp="ChkIntOnly(this); NumOnly(this);" >
                  </td>
                  <td class="left  boder0-left">
                     <label>Alternate Mobile number</label>
                     <input name="mobile" id="mobile" maxlength="20" type="text" value="<?= $mobile ?>"
                        class="input-style1" onKeyUp="ChkIntOnly(this);NumOnly(this)">
                  </td>
                  <td class="left boder0-left">
                     <label>Company Name</label>
                     <input type="text" name="company_name" id="company_name" value="<?= $company_name_case ?>"
                        class="input-style1">
                  </td>
                  <td class="left boder0-left">
                     <label>Company Registration Number</label>
                     <input type="text" name="company_registration" id="company_registration"
                        value="<?= $company_registration ?>" class="input-style1"
                        onKeyUp="ChkIntOnly(this);NumOnly(this)">
                  </td>
               </tr>
               <tr>
                  <td class="left boder0-left">
                     <label>First Name<em>*</em></label>
                     <!-- <? $first_name = str_replace("singlequote", "'", $first_name); ?> -->
                     <input name="first_name" id="first_name" type="text" value="<?php echo $first_name ?>"
                        class="input-style1" onkeypress="return isAlphabetKey(event)">
                  </td>
                  <td class="left boder0-left">
                     <label>Last Name</label>
                     <!-- <? $last_name = str_replace("singlequote", "'", $last_name); ?> -->
                     <div class="log-case">
                        <input name="last_name" id="last_name" type="text" value="<?php echo $last_name ?>"
                           class="input-style1" onkeypress="return isAlphabetKey(event)">
                     </div>
                  </td>
                  <td class="left boder0-left">
                     <label>Priority Customer<em>*</em></label>
                     <div class="log-case">
                        <span class="slug"> <input type="radio" name="priority_user" id="priority_1" value="1" <? if (isset($priority_user) == '1') {
                           echo "checked";
                        } ?>> Priority</span>
                        <span class="slug"> <input type="radio" name="priority_user" id="priority_0" value="0" <? if (isset($priority_user) == '0') {
                           echo "checked";
                        } ?>> Non Priority</span>
                     </div>
                  </td>
                  <td class="left boder0-left">
                     <label>County</label>
                     <div class="log-case">
                        <select name="district" id="district" class="select-styl1" onchange="get_villages(this.value)"
                           style="width:180px">
                           <option value="0">Select County</option>
                           <?php
                           $city_query = County_list(); // this function created in function file for fetch county list
                           while ($city_res = mysqli_fetch_array($city_query)) { ?>
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
                        <?php

                        if (!empty($district) && !empty($villages)) {
                           $villages_query = SubCounty($district); // this function created in function file for fetch subcounty list
                        }
                        ?>
                        <select name="villages" id="villages" class="select-styl1" style="width:180px">
                           <option value="0">Select Sub County</option>
                           <?php
                           if (mysqli_num_rows($villages_query) > 0) {
                              while ($villages_res = mysqli_fetch_array($villages_query)) { ?>
                                 <option value="<?= $villages_res['id'] ?>" <? if ($villages_res['id'] == $villages) {
                                      echo "selected";
                                   } ?>><?= $villages_res['vVillage'] ?> </option>
                              <?php }
                           } ?>
                        </select>

                     </div>
                  </td>
                  <td class="left boder0-left">
                     <label>Nationality</label>
                     <div class="log-case">
                        <input name="nationality" id="nationality" type="text" value="<?= $nationality ?>"
                           class="input-style1">
                     </div>
                  </td>
                  <td class="left border0-left">
                     <label>Gender</label>
                     <select name="gender" id="gender" class="select-styl1" style="width:180px">
                        <option value="0">Select Gender</option>
                        <?php
                        $gender_query = getgender(); // this function created in function file for fetch gender list
                        if (mysqli_num_rows($gender_query) > 0) {
                           while ($gender_res = mysqli_fetch_array($gender_query)) { ?>
                              <option value="<?= $gender_res['value'] ?>" <? if ($gender_res['value'] == $gender) {
                                   echo "selected";
                                } ?>><?= $gender_res['name'] ?> </option>
                           <?php }
                        } ?>
                     </select>
                  </td>
                  <td class="left boder0-left">
                     <label>Email</label>
                     <input type="email" name="email" id="email" value="<?php echo $email; ?>" class="input-style1">
                  </td>
               </tr>
               <tr>
                  <td class="left boder0-left">
                     <label>Facebook Handle</label>
                     <input type="text" name="fbhandle" id="fbhandle" value="<?= $fbhandle; ?>" class="input-style1">
                  </td>
                  <td class="left boder0-left">
                     <label><img src="../public/images/x-twitter.svg" alt="X" style="width:15px"> Handle</label>
                     <input type="text" name="twitterhandle" id="twitterhandle" value="<?= $twitterhandle; ?>"
                        class="input-style1">
                  </td>
                  <td class="left boder0-left">
    <label>WhatsApp Number</label>
    <input type="text" name="whatsapp_number" id="whatsapp_number" value="<?= $whatsappnumber; ?>"
        class="input-style1" oninput="validateNumericInput(this)">
</td>
                   <!-- //for messenger Handling html code [Aarti][14-08-2024] -->
                  <td class="left boder0-left">
                     <label>Facebook Messenger ID</label>
                     <input type="text" name="messengerhandle" id="messengerhandle" value="<?= $messengerhandle ?>" class="input-style1">
                  </td>
                  
               </tr>
               <tr>
                  <td class="left boder0-left">
                     <label>Instagram ID</label>
                     <input type="text" name="instagramhandle" id="instagramhandle" value="<?= $instagramhandle ?>" class="input-style1">
                  </td>
                  <td class="left boder0-left">
    <label>SMS Number</label>
    <input type="text" name="smshandle" id="smshandle" value="<?= $sms_number; ?>" class="input-style1"
        oninput="validateNumericInput(this)">
</td>
                  <td class="left boder0-left">
                     <label>Address</label>
                     <textarea name="address_1" id="address_1" class="input-style1"><?= $address_1; ?></textarea>
                  </td>
               </tr>
               <!-- Chat Section Show When Case Create from chat  -->
               <? if (isset($_GET['chatid']) && isset($_GET['mr'])): ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <p><b>Chat Query</b></p>
                        <div class="chat-history" id="chatHistory">
                           <?= $fact ?>
                        </div>
                        <p class="show-more-btn" id="showMoreBtn">
                         Show More<span class="dots">...</span>
                       </p>
                     </td>
                  </tr>
               <? endif; ?>
               <!-- Chat Section Close ..!!  -->
               <!-- Email Section Show When Case Create from Email  -->
               <?php if (isset($_GET['emailid']) && isset($_GET['mr'])) { ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <p><b>Email Query</b></p>
                        <div class="chat-history" id="chatHistory">
                           <?php echo htmlspecialchars_decode($fact); ?>
                        <br/> <br/> <br/>
                           <?php echo $d_email_date; ?>
                        </div>
                        <!-- <p class="show-more-btn" id="showMoreBtn">
                         Show More<span class="dots">...</span>
                       </p> -->
                     </td>
                  </tr>

               <?php } ?>
               <!-- Email Section Close ..!!  -->
               <!-- Twitter Section Show When Case Create from Twitter  -->
               <?php if (isset($_GET['twitterid']) && isset($_GET['mr'])) { ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <label><img src="../public/images/x-twitter.svg" alt="X" style="width:10px"> Remark</label>
                        <div class="log-case">
                           <textarea name="twitter_remark" id="twitter_remark" type="text"
                              style="margin: 0px;padding: 0.5rem;width: 533px;height: 150px;resize: none;"
                              class="input-style1" readonly><?= $fact ?></textarea>
                        </div>
                     </td>
                  </tr>
               <?php } ?>
               <!-- Twitter Section Close ..!!  -->
               <!-- Facebook Section Show When Case Create from facebook  -->
               <?php if (isset($_GET['facebookid']) && isset($_GET['mr'])) { ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <label>Facebook Remark</label>
                        <div class="log-case">
                           <textarea name="facebook_remark" id="facebook_remark" type="text"
                              style="margin: 0px;padding: 0.5rem;width: 533px;height: 150px;resize: none;"
                              class="input-style1" readonly><?= $fact ?></textarea>
                        </div>
                     </td>
                  </tr>
               <?php } ?>
               <!-- Facebook Section Show When Case Create from facebook  -->
               <?php if (isset($_GET['whatsappid']) && isset($_GET['mr'])) { ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <p><b>Whatsapp Messenger Query</b></p>
                        <div class="chat-history" id="chatHistory">
                           <?= $fact ?>
                        </div>
                        <p class="show-more-btn" id="showMoreBtn">
                         Show More<span class="dots">...</span>
                       </p>
                     </td>
                  </tr>
               <?php } ?>
               <!-- // this is for used instagram and messenger related data fetching[Aarti][19-11-2024] -->
               <? if (isset($_GET['messengerid']) && isset($_GET['mr'])) : ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <p><b>Facebook Messenger Query</b></p>
                        <div class="chat-history" id="chatHistory">
                           <?= $fact ?>
                        </div>
                        <p class="show-more-btn" id="showMoreBtn">
                         Show More<span class="dots">...</span>
                       </p>
                     </td>
                  </tr>
               <? endif; ?>
               <? if (isset($_GET['instagramid']) && isset($_GET['mr'])) : ?>
                  <tr>
                     <td class="left boder0-left" colspan="4">
                        <p><b>Instagram Messenger Query</b></p>
                        <div class="chat-history" id="chatHistory">
                           <?= $fact ?>
                        </div>
                        <p class="show-more-btn" id="showMoreBtn">
                         Show More<span class="dots">...</span>
                       </p>
                     </td>
                  </tr>
               <? endif; ?>
               <!-- Facebook Section Close ..!!  -->
               <!-- sms Section Show When Case Create from sms  -->
               <?php if (isset($_GET['smsid']) && isset($_GET['mr'])) { ?>
                  <tr>
                     <td class="left boder0-left" colspan="3">
                        <label>SMS Remark</label>
                        <div class="log-case">
                           <textarea name="sms_remark" id="sms_remark" type="text"
                              style="margin: 0px;padding: 0.5rem;width: 533px;height: 150px;resize: none;"
                              class="input-style1" readonly><?= $fact ?></textarea>
                        </div>
                     </td>
                  </tr>
               <?php } ?>
               <!-- Facebook Section Close ..!!  -->
            </tbody>
         </table>
      </div>
      <!--  Channel Information -->
      <?php $module_flag_customer = module_license('CUSTOMER INFORMATION'); ?>
      <div class="old-customer-simple-table " <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>>
         <div class="breadcrumb_head"> Customer Information </div>
         <div>
            <table class="tableview tableview-2 main-form new-customer">
               <tbody>
                  <tr>
                     <td class="left boder0-left">
                        <label>Complaint Origin<em>*</em></label>
                        <?php
                        $sourceresult = getComplaint();
                        ?>
                        <select name="source" id="source" class="select-styl1" style="width:180px">
                           <?php
                           if ($_GET['action'] == 'IVR') {
                              $source = isset($_GET['mr']) ? $_GET['mr'] : 1;
                           } else {
                              $source = isset($_GET['mr']) ? $_GET['mr'] : $source;
                           }
                           while ($row = mysqli_fetch_array($sourceresult)) { ?>
                              <option value='<?= $row['id'] ?>' <?php echo ($source == $row['id']) ? 'selected' : ''; ?>>
                                 <?= $row['source'] ?>
                              </option>
                           <?php } ?>
                        </select>
                     </td>
                     <td class="left boder0-right">
                        <label>Call <em>*</em></label>
                        <div class="log-case">
                           <span class="slug"> <input type="radio" name="call_type" value="real" checked <? if (isset($call_type) == 'real') {
                              echo "checked";
                           } ?>> Real Call</span>
                           <span class="slug"> <input type="radio" name="call_type" value="spam" <? if (isset($call_type) == 'spam') {
                              echo "checked";
                           } ?>> Spam Call</span>

                        </div>
                     </td>
                     <td class="left boder0-right" colspan="3">
                        <label>Agent Name</label>
                        <input type="text" readonly="readonly" name="agent_name" id="agent_name"
                           value="<?= $logedin_agent ?>" class="input-style1">
                     </td>
                     <td class="left boder0-right">
                        <label>Language</label>
                        <div class="log-case">
                           <?php
                           $langresult = getlanguage();
                           ?>
                           <select name="lang" id="lang" class="select-styl1" style="width:180px">
                              <option value="">Select</option>
                              <?
                              while ($row = mysqli_fetch_array($langresult)) { ?>
                                 <option value='<?= $row['id'] ?>' <?php echo ($languagename == $row['id']) ? 'selected' : ''; ?>><?= $row['lang_Name'] ?> </option>
                              <? } ?>
                           </select>
                        </div>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      <!--  Tax Information -->
      <?php $module_flag_customer = module_license('TAX INFORMATION'); ?>
      <div class="old-customer-simple-table " <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>>
         <div class="breadcrumb_head"> Tax Information</div>
         <div>
            <table class="tableview tableview-2 main-form new-customer">
               <tr>
                  <td>
                     <link rel="stylesheet" href="<?= $SiteURL ?>public/css/web_services.css">
                     <span id="taxpayer_profile" class="button-orange1" style="margin-top: 10px;"> Taxpayer
                        Profile</span>
                     <span id="tax_clearance_form" class="button-orange1" style="margin-top: 10px;">Tax Clearance
                        Certificate</span>
                     <span id="taxpayer_form" class="button-orange1" style="margin-top: 10px;">Send Taxpayer
                        Certificate</span>
                     <span id="return_filling_form" class="button-orange1" style="margin-top: 10px;">Return Filing
                        Status</span>
                     <span id="return_form" class="button-orange1" style="margin-top: 10px;">Return Data</span>
                     <span id="withholding_cat" class="button-orange1" style="margin-top: 10px;">Withholding Tax
                        Certificate</span>
                     <span id="app_refund_status_form" class="button-orange1" style="margin-top: 10px;">Application
                        Refund Status</span>
                     <span id="reject_refund_form" class="button-orange1" style="margin-top: 10px;">View Rejected
                        Refund</span>
                     <span id="payments_form" class="button-orange1" style="margin-top: 10px;">All Payments</span>
                     <span id="outstanding_payments_form" class="button-orange1" style="margin-top: 10px;">Outstanding
                        Payments</span>
                     <span id="ibdf_form" class="button-orange1" style="margin-top: 10px;">IBDF</span>
                     <span id="declaration_from" class="button-orange1" style="margin-top: 10px;">Declaration</span>

                     <!-- <span id="workflow_form" class="button-orange1" style="margin-top: 10px;">Workflow Transaction Headers</span> -->
                     <span id="workflow_details_form" class="button-orange1" style="margin-top: 10px;">Workflow
                        Transaction Details</span>
                     <span id="currency_form" class="button-orange1" style="margin-top: 10px;">Currency</span>
                     <span id="quota_form" class="button-orange1" style="margin-top: 10px;">Quota</span>
                     <span id="rebates_form" class="button-orange1" style="margin-top: 10px;">Rebates</span>
                     <span id="mineralpermit_form" class="button-orange1" style="margin-top: 10px;">Mineral Export
                        Permit</span>
                     <span id="window_permit_form" class="button-orange1" style="margin-top: 10px;">Single Window
                        Permit</span>
                     <span id="assessments_form" class="button-orange1" style="margin-top: 10px;">Assessments</span>
                     <span id="query_form" class="button-orange1" style="margin-top: 10px;">E-Query</span>
                     <span id="monitoring_form" class="button-orange1" style="margin-top: 10px;">Transaction
                        Monitoring</span>
                     <span id="incomeTax_form" class="button-orange1" style="margin-top: 10px;">Advance IncomeTax</span>
                     <span id="configurations_form" class="button-orange1" style="margin-top: 10px;">System
                        Configurations</span>
                  </td>
               </tr>
            </table>
            <div>
               <div id="model_div_display" class="w3-modal modal_hide"></div>
            </div>
         </div>
      </div>
      <!--  Other Information -->
      <?php $module_flag_customer = module_license('INCIDENT INFORMATION'); ?>
      <div class="old-customer-simple-table " <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>>
         <div class="breadcrumb_head"> INCIDENT INFORMATION </div>
         <table class="tableview tableview-2 main-form new-customer">
            <tbody>
               <tr>
                  <td class="left boder0-right">
                     <label>Reasons for calling <em>*</em></label>
                     <div class="log-case">
                        <?php
                        $complaint_sql = getreasons_calling(); //for reason list from web_ticket_function file
                        while ($rows = mysqli_fetch_array($complaint_sql)) {
                           if ($rows['slug'] == 'none')
                              break;
                           ?>
                           <span class="slug"> <input type="radio" name="type" id="type<?= $rows['id'] ?>"
                                 value="<?= $rows['slug'] ?>" <?php if (isset($type) == $rows['slug']) {
                                      echo "checked";
                                   } ?>> <?= $rows['complaint_name'] ?></span>
                        <?php }
                        ?>
                     </div>
                  </td>
                  <td class="left boder0-right">
                     <label>Priority of call <em>*</em></label>
                     <div class="log-case">
                        <span class="slug"> <input type="radio" name="priority" value="low" <? if (isset($priority) == 'low') {
                           echo "checked";
                        } ?>> Low </span>

                        <span class="slug"> <input type="radio" name="priority" value="high" checked <? if (isset($priority) == 'high') {
                           echo "checked";
                        } ?>> High </span>
                        <span class="slug"> <input type="radio" name="priority" value="extremelyhigh" <? if (isset($priority) == 'extremelyhigh') {
                           echo "checked";
                        } ?>> Extremely High </span>
                     </div>
                  </td>
                  <td class="left  boder0-right">
                     <label>Status of complaint <em>*</em></label>
                     <div class="log-case">
                        <?php $ticketstatus_query = getcomplaintlist(); ?>
                        <select name="status_type_" id="status_type_" class="select-styl1"
                           onchange="getfeedback(this.value)" style="width:180px;">
                           <option value="0">Select Status</option>
                           <?php
                           $status_type_ = !empty($status_type_) ? $status_type_ : '1';
                           while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)) {
                              ?>
                              <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status_type_) {
                                   echo "selected";
                                } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td class="left  boder0-left">
                     <label>Category <em>*</em></label>
                     <div class="log-case" id="category_div">
                        <select name="v_category" id="v_category" class="select-styl1 " style="width:180px"
                           onChange="web_subcat(this.value, ''); get_department(this.value)">
                           <option value="0">Select Category</option>
                           <?php
                           $sourceresult = getwebcategory();
                           while ($row = mysqli_fetch_array($sourceresult)) {
                              $SubI = $row['id'];
                              $subC = $row['category'];
                              if ($SubI == $v_category) {
                                 $sel = 'selected';
                              } else {
                                 $sel = '';
                              }
                              ?>
                              <option value='<?= $SubI ?>' <?= $sel ?>> <?= $subC ?> </option>
                           <?php } ?>
                        </select>
                     </div>
                  </td>
                  <td class="left  boder0-left">
                     <label>Subcategory <em>*</em></label>
                     <div class="log-case" id="subcategory_div">
                        <select name="v_subcategory" id="v_subcategory" class="select-styl1" style="width:180px">
                           <option value="0">Select Sub Category</option>
                           <?php
                           $subcat_query = getwebsubcategory($catid);
                           if (mysqli_num_rows($subcat_query) > 0) {
                              while ($subcat_res = mysqli_fetch_array($subcat_query)) { ?>
                                 <option value="<?= $subcat_res['id'] ?>" <? if ($subcat_res['id'] == $subcatid) {
                                      echo "selected";
                                   } ?>>
                                    <?= $subcat_res['subcategory'] ?>
                                 </option>

                              <?php }
                           }
                           ?>
                        </select>
                     </div>
                  </td>
                  <td class="left  boder0-right">
                     <div id="assign_to_backofice">
                        <label>Assign Department <em>*</em></label>
                        <div class="log-case" id="group_assign_divs">
                           <!-- onclick="get_assigne_email(this.value)" -->
                           <select name="group_assign" id="group_assign" class="select-styl1 " style="width:180px;">
                              <option value="0">Select Department</option>
                              <?
                              $group_query = getwebprojects();
                              while ($group_res = mysqli_fetch_array($group_query)) {
                                 ?>
                                 <option value="<?= $group_res['pId'] ?>" <? if ($group_res['pId'] == $group_assign) {
                                      echo "selected";
                                   } ?>>
                                    <?= $group_res['vProjectName'] ?>
                                 </option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                     <div id="show_emails"></div>
                  </td>
               </tr>
               <tr>
               </tr>
               <td class="left border0-left">
                  <label>Root Cause</label>
                  <input type="text" name="root_cause" id="root_cause" value="<?= $root_cause; ?>" class="input-style1">
               </td>
               <td class="left border0-left">
                  <label>Corrective measure</label>
                  <input type="text" name="corrective_measure" id="corrective_measure"
                     value="<?= $corrective_measure; ?>" class="input-style1">
               </td>
               <td class="left  boder0-right" colspan="2">
                  <label>Remarks <em>*</em> </label>
                  <div class="log-case" id="">
                     <? if (!empty($v_remark_type) && !empty($ticketid)):
                        echo $v_remark_type; ?>
                     <? else: ?>

                        <textarea name="v_remark_type" id="v_remark_type" type="text" class="input-style1"
                           style="margin: 0px;padding: 0.5rem;width: 180px;height: 50px;"><?= $v_remark_type; ?></textarea>
                     <? endif; ?>
                  </div>
               </td>
               </tr>
            </tbody>
         </table>
         </table>
      </div>
      <center>
         <?php if (!empty($ticketid) || !empty($ticketid_old_case)) { ?>
            <input type="hidden" name="Action" value="update" />
         <?php } else { ?>
            <input name="Submit" type="submit" value="Create" class="button-orange1" style="float:inherit;"
               id="create_ticket" />
            <input type="hidden" name="action" value="Submit_Ticket" />
         <?php } ?>
      </center>
   </form>
   <input type="hidden" id="save_current_status" value="<?= $status_type_ ?>">
   <form name="addInteraction_form" id="addInteraction_form" enctype="multipart/form-data" method="post"
      style="display: none">
      <input type="hidden" name="action" id="ajax_addInteraction" value="ajax_addInteraction">
      <input type="hidden" name="docket_no" id="docket_no" value="<?= $ticketid_old_case ?>">
      <input type="hidden" name="rowid" id="rowid" value="0">
      <input type="hidden" name="source_id" id="source_id" value="<?= $source ?>">
      <input type="hidden" name="social_type_id" id="social_type_id" value="<?= $social_type_id ?>">
      <input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id ?>">
      <input type="hidden" name="v_category" id="v_category" value="<?= $v_category ?>">
      <input type="hidden" name="c_mobile" id="c_mobile" value="<?= $phone_number ?>">
      <input type="hidden" name="c_full_name" id="c_full_name" value="<?= $first_name . ' ' . $last_name ?>">

      <!-- Modifed by farhan on 27-06-2024 -->
      <input type="hidden" name="recording_file" value="<?= $_GET['file'] ?>">
      <input type="hidden" name="vendor_lead_code" value="<?= $_GET['vendor_lead_code'] ?>">
      <input type="hidden" name="lead_id" value="<?= $_GET['id'] ?>">
      <input type="hidden" name="lang" value="<?= $_GET['language'] ?>">
      <input type="hidden" name="caller_id" value="<?= $_GET['phone_number'] ?>">
      <!-- End -->

      <input type="hidden" name="action" id="interaction_remark_form" value="interaction_remark_form">
      <div class="old-customer-simple-table">
         <table class="tableview tableview-2 main-form new-customer">
            <tr class="background2">
               <th height="44" colspan="2" align="left">Add New Remark</th>
               <th height="44" align="left"><button type="button" onclick="$('#addInteraction_form').hide();"
                     class="button-orange1" style="float: right; padding: 0px;">close</button></th>
            </tr>
            <tr>
               <td colspan="3" class="left  boder0-left">
                  <label>Status of complaint <em>*</em></label>
                  <div class="log-case">
                     <select name="inte_status_type_" id="status_type_" class="select-styl1" style="width:180px;"
                        onchange="change_new_status(this.value)">
                        <option value="0">Select Status</option>
                        <?php
                        if ($status_type_ == '8') {
                           $ticketstatus_query = mysqli_query($link, "select id,ticketstatus from $db.web_ticketstatus where status='1' ORDER BY ticketstatus ASC");
                        } else {
                           $ticketstatus_query = mysqli_query($link, "select id,ticketstatus from $db.web_ticketstatus where status='1' AND id NOT IN (3,4) ORDER BY ticketstatus ASC");
                        }
                        while ($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)) {
                           ?>
                           <option value="<?= $ticketstatus_res['id'] ?>" <? if ($ticketstatus_res['id'] == $status_type_) {
                                echo "selected";
                             } ?>> <?= $ticketstatus_res['ticketstatus'] ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </td>
            </tr>
            <tr>
               <td colspan="3">
                  <label>Remarks<em>*</em> </label>
                  <div class="log-case">
                     <textarea name="interaction_remark" id="interaction_remark" type="text"
                        style="margin: 0px;padding: 0.5rem;width: 533px;height: 50px;resize: none;"><?= $customer_remark ?></textarea>
                  </div>
               </td>
            </tr>
            <tr>
               <td colspan="3">
                  <input type="submit" class="button-orange1" name="remark_button" id="remark_button"
                     value="Save New Remark">
                  <span id="responseMessage2"></span>
               </td>
            </tr>
         </table>
      </div>
   </form>
   <br>
   <div id="ticket_history"> </div>
   <?php
   if (isset($docket_no) || isset($_GET['customerid']) || isset($_GET['phone_number'])) {
      $ticket_query = gethistory($customer_id);
      $count = mysqli_num_rows($ticket_query);
      ?>
      <div id="ticket_history_docket" style="max-height: 300px; overflow: scroll;">
         <table class="tableview tableview-2 main-form new-customer" id="interactiontable">
            <tbody>
               <tr class="background">
                  <td align="center" class="boder0-right">Case Id</td>
                  <td align="center" class="boder0-right">Type</td>
                  <td align="center" class="boder0-right">Category</td>
                  <td align="center" class="boder0-right">Status</td>
                  <td align="center" class="boder0-right">Created On</td>
                  <td align="center" class="boder0-right">Department</td>
                  <td align="center" class="boder0-right">View</td>
                  <td align="center" class="boder0-right">Action</td>
               </tr>
               <?php
               if ($count > 0) {
                  while ($ticket_res = mysqli_fetch_array($ticket_query)) { ?>
                     <tr style="background: ; color: ;" id="row_<?= $ticket_res['iPID'] ?>">
                        <td align="center"><a style="text-decoration: none;"
                              href="case_detail_backoffice.php?id=<?= $ticket_res['ticketid'] ?>" class="ico-interaction"
                              target="_blank"><?= $ticket_res['ticketid'] ?></a>
                        </td>
                        <td align="center">
                           <?= $ticket_res['vCaseType'] ?>
                        </td>
                        <td align="center">
                           <?= category($ticket_res['vCategory']) ?>
                        </td>
                        <td align="center">
                           <?= ticketstatus($ticket_res['iCaseStatus']) ?>
                        </td>
                        <td align="center">
                           <?= date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate'])) ?>
                        </td>
                        <td align="center">
                           <?= township($ticket_res['vProjectID']) ?>
                        </td>
                        <td align="center"><a href="helpdesk/interaction_view.php?docketid=<?= $ticket_res['ticketid'] ?>"
                              class="ico-interaction">view</a></td>
                        <td align="center">
                           <? if ($ticket_res['ticketid'] == $ticketid_old_case && $ticket_res['iCaseStatus'] != 3) { ?>
                              <button type="button" class="button-orange1"
                                 onclick="addmore_interaction('<?= $ticket_res['ticketid'] ?>', '<?= $ticket_res['vCustomerID'] ?>', '<?= $ticket_res['i_source'] ?>', '<?= $ticket_res['iPID'] ?>')"
                                 style="color:#222">Add More</button>
                           <?php } ?>
                        </td>
                     </tr>
                  <?php } ?>
               <?php } else { ?>
                  <tr>
                     <td colspan="8" align="center">No Record Found</td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   <?php } ?>
   <!----START---->
   <? $displayy = "none";
   $displayy = (isset($_POST['callbk']) == "1") ? "" : "none"; ?>
   <?php $module_flag_customer = module_license('DISPOSITION'); ?>
   <div class="old-customer-simple-table " <?php if ($module_flag_customer != '1') { ?> style="display: none;" <?php } ?>
      <?php if ($_GET['action'] == 'IVR') {
      } else { ?> style="display: none;" <?php } ?>>
      <div class="breadcrumb_head">Disposition</div>
      <form method="POST" name="disposeAction" id="disposeAction">
         <table class="tableview tableview-2 main-form new-customer">
            <tbody>
               <tr>
                  <td width="50%" class="left  boder0-left">
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
                     </div>
                  </td>
                  <!-- sentiment commented [vastvikta][11-04-2025]-->
                  <!-- <td width="50%" class="left  boder0-left">
                     <label>Sentiment</label>
                     <?php
                     $sentiment = "select sentiment from $db.tbl_sentiment where status=1";
                     $res_sen = mysqli_query($link, $sentiment);
                     ?>
                     <select class="select-styl1" name="sentiment" id="sentiment" >
                        <option value="0">Select Sentiment</option>
                        <? while ($row_sen = mysqli_fetch_array($res_sen)) { ?>
                           <option value="<?= $row_sen['sentiment'] ?>" <? if ($row_sen['sentiment'] == $_POST['sentiment']) {
                                echo "selected";
                             } ?>>
                              <?= strtolower($row_sen['sentiment']) ?>
                           </option>
                        <? } ?>
                     </select>
                  </td> -->
                  <td width="50%" class="left  boder0-right">
                     <label>Disposition </label>
                     <?php $qdisposition = disposition(); ?>
                     <div class="log-case">
                        <!-- css added for First letter capital [Aarti][03-02-20025] -->
                        <select class="select-styl1" name="disposition" id="disposition" style="text-transform: capitalize;">
                           <option value="0">Select Disposition <em>*</em> </option>
                           <? while ($sql_disposition = mysqli_fetch_array($qdisposition)) { 
                              print_r($sql_disposition['V_DISPOSITION']);
                              ?>
                              <option value="<?= $sql_disposition['V_DISPO'] ?>" <? if ($sql_disposition['V_DISPO'] == $_POST['disposition']) {
                                   echo "selected";
                                } ?>>
                                 <?=$sql_disposition['V_DISPOSITION'];?>
                              </option>
                           <? } ?>
                        </select>
                     </div>
                  </td>
               </tr>
               <tr>
                  <!-- <td width="50%" class="left  boder0-right">
                     <label>Disposition </label>
                     <?php $qdisposition = disposition(); ?>
                     <div class="log-case"> -->
                        <!-- css added for First letter capital [Aarti][03-02-20025] -->
                        <!-- <select class="select-styl1" name="disposition" id="disposition" style="text-transform: capitalize;">
                           <option value="0">Select Disposition <em>*</em> </option>
                           <? while ($sql_disposition = mysqli_fetch_array($qdisposition)) { 
                              print_r($sql_disposition['V_DISPOSITION']);
                              ?>
                              <option value="<?= $sql_disposition['V_DISPO'] ?>" <? if ($sql_disposition['V_DISPO'] == $_POST['disposition']) {
                                   echo "selected";
                                } ?>>
                                 <?=$sql_disposition['V_DISPOSITION'];?>
                              </option>
                           <? } ?>
                        </select>
                     </div>
                  </td> -->
                  <td width="50%" class="left  boder0-right" colspan="2">
                     <label>Remarks <em>*</em> </label>
                     <div class="log-case" id="">
                        <textarea name="dispose_remark" id="dispose_remark" class="input-style1"
                           style="margin: 0px; width: 450px; height: 90px;"><?= $remark; ?></textarea>
                     </div>
                  </td>
               </tr>
               <tr>
                  <td colspan="2" class="left boder0-right">
                     <?php
                     // if (isset($agent) == "") $agent = isset($_SESSION['VD_login']);
                     $agent = $_SESSION['VD_login'];

                     if ($customer_id != "") {
                        $customerid = $customer_id;
                     } else if (isset($customerid) != "") {
                        $customerid = $customer_id;
                     }
                     ?> <span style="display:block; text-align:center;">
                        &nbsp;
                        <input name="btnDispose" type="submit" value="Dispose" class="button-orange1"
                           onclick="return validate_dispose()" style="float:inherit;" />
                        <!-- farhan akhtar :: 30-01-2025 :: Dispose and Break Updates Break Flag and Current Timestamp in asterisk (autodial_live_agents) -->
                           <input name="btnDisposeNBreak" type="submit" value="Dispose & Break" class="button-orange1" style="float:inherit;" />
                        <!-- End -->
                        <input type="hidden" name="action" value="Dispose_Submit" />
                        <input type="hidden" name="caller_id" value="<?= $phone_number ?>">
                        <input type="hidden" id="registerno" name="registerno">
                        <input type="hidden" name="agent" value="<?= $agent ?>" />
                        <input type="hidden" name="list_id" id="list_id" value="<?= $_GET['list_id'] ?>">
                        <input type="hidden" name="campaignForPop" id="campaignForPop"
                           value="<?= $_GET['campaignForPop'] ?>">
                        <input type="hidden" name="lead_id" id="lead_id" value="<?php echo $_GET['id'] ?>">
                        <input type="hidden" name="vendor_lead_code" id="vendor_lead_code"
                           value="<?= $_GET['vendor_lead_code'] ?>">
                        <input type="hidden" name="file" id="file" value="<?= $_GET['file'] ?>">
                        <input type="hidden" name="customerid" id="customerid_new" value="<?= $customerid ?>">
                        <!-- value assign docket no [aarti][20-07-2024]-->
                        <input type="hidden" name="docket_no_new" id="docket_no_new" value="<?= $docket_no ?>">
                        <input type="hidden" name="email" id="email" value="<?= $email ?>">
                        <!-- <input type="hidden" name="first_name" id="first_name" value="<?= $first_name . ' ' . $last_name ?>"> -->

                        <!-- Modified by farhan on 27-06-2024 -->
                        <input type="hidden" name="fname" id="fname" value="<?= $first_name ?>">
                        <input type="hidden" name="lname" id="lname" value="<?= $last_name ?>">
                        <!-- End -->
                     </span>
                  </td>
               </tr>
            </tbody>
         </table>
      </form>
   </div>

   <div class="old-customer-table">
      <h6>INTERACTION HISTORY</h6>
      <div id="interactionBox" style="height: 200px; overflow-y: hidden; width: 100%; border-bottom: 1px solid #d4d4d4;transition: height 0.3s;">
      <table width="100%" class="tableview tableview-2 main-form">
      <tbody class="interaction_data" id="interaction_data">
                <tr bgcolor="#dddddd" class="background">
                  <td width="8%" align="center">
                     <div align="left"><b>Channel Type</b></div>
                  </td>
                  <td width="10%" align="center">
                     <div align="left"><b>From</b></div>
                  </td>
                  <td width="17%" align="center">
                     <div align="left"><b>Subject</b></div>
                  </td> 
                  <td width="11%" align="center">
                     <div align="left"><b>Created by</b></div>
                  </td>
                  <td width="11%" align="center">
                     <div align="left"><b>DateTime</b></div>
                  </td>
               </tr>
               
            </tbody>
         </table>
      </div>
       <!-- Toggle Button  updated code [vastvikta][13-05-2025] -->
      <div style="margin-top: 10px;">
      <button id="toggleInteractionBtn" 
      style="padding: 6px 14px; background-color: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background-color 0.3s;display: none;">
      Show More
      </button>
   </div>
</div>

   <!--END---------->
</div>
<!-- Customer history html code start -->
<div class="l_c_h">
   <div class="c_h">
      <div class="left_c">
         <div class="left1 right_c left_icons">     
            <a href="#" class="mini" style="font-size:23px;">+</a>
         </div>
         <div class="left1 center_icons">
            <!--center_icons-->
            CASE HISTORY
         </div>
         <!--end center_icons-->
      </div>
      <div class="right1 right_c" style="width:35px;">
         <a href="#" class="logout" title="End chat" name="" style="display:none;"></a>
      </div>
      <div class="clear"></div>
   </div>
   <div class="chat_container" style="display: none;">
      <div class="chat_message" style="display: none;">
         <input type="hidden" class="my_user" value="">
      </div>
      <div class="chat_text_area" style="display:none;">
         <textarea name="messag_send" class="messag_send" id="messag_send"
            placeholder="Enter Your Message and press CTRL"></textarea>
      </div>

      <div class="card">
         <div class="card-footer card-comments" style="max-height: 350px;overflow: auto;background:#fff;padding:6px">
            <?
            if (isset($_GET['customerid']) || isset($customer_id)) {
               $ticket_query = gethistory($customer_id);
               $ticket_arr = [];
               while ($ticket_res = mysqli_fetch_array($ticket_query)) {
                  $ticket_arr[] = $ticket_res;
               }
            }
            ?>
            <table class="tableview tableview-2" id="interactiontable">
               <thead>
                  <tr class="background">
                     <td>ID</td>
                     <td>Date</td>
                     <td>Name</td>
                     <td>Case Id</td>
                     <td>Category</td>
                     <td>Status</td>
                  </tr>
               </thead>
               <tbody id="case_detail_table">
                  <? if (count($ticket_arr) > 0) {
                     $cnt = 0;
                     foreach ($ticket_arr as $key => $ticket_res) { $cnt++;?>
                        <tr id="row_<?= $ticket_res['iPID'] ?>">
                           <td align="center"><?= $cnt ?></td>
                           <td align="center"><?= date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate'])) ?></td>
                           <td align="center"><?= wordwrap($first_name . ' ' . $last_name, 12, "<br>\n") ?></td>
                           <td align="center">
                           <a href="helpdesk/interaction_view.php?docketid=<?= $ticket_res['ticketid'] ?>" class="ico-interaction"><?= $ticket_res['ticketid'] ?></a></td>
                           <td align="center"><?= category($ticket_res['vCategory']) ?></td>
                           <td align="center"><?= ticketstatus($ticket_res['iCaseStatus']) ?></td>
                        </tr>

                     <?php }
                     $id = $ticket_res['iPID'];
                     ?>
                     <tr id="remove_row">
                        <td colspan="6">
                           <button id="load_more" class="button-orange1" data-item="<?php echo $id; ?>">Load More</button>
                        </td>
                     </tr>
                  <?php } else { ?>
                     <tr style="background: ; color: ;">
                        <td align="center" colspan="6">No Record Found</td>
                     </tr>
                  <?php } ?>
               </tbody>
            </table>

         </div>
      </div>
   </div>
</div>
<!-- Modal content -->

<div id="processModel" class="modalprocess">
   <div class="modal-content">
      <div class="modal-body">
         <center>
            <h3><img src="<?= $SiteURL ?>public/images/loader.gif"
                  style="height:30px;width: 30px;">&nbsp;&nbsp;Processing... Please wait...</h3>
         </center>
      </div>
   </div>
</div>

<?php $emailid = $_GET['emailid'];?>

<?php if (isset($_GET['mr']) && $_GET['mr'] == 6 && !empty($email)): ?>
   
<script>
   const emailId = '<?php echo addslashes($emailid); ?>'; // safely embed PHP variable into JS

  function updateOpenTime() {
    const email = $('#email').val().trim();

    if (email === '') return;

    $.ajax({
      url: 'helpdesk/web_ticket_function.php',
      type: 'POST',
      data: { email: email,emailid: emailId,action:'update_open_time'},
      success: function(response) {
        console.log('Open time updated:', response);
      },
      error: function(xhr, status, error) {
        console.error('AJAX error:', status, error);
      }
    });
  }

  // Run every 10 seconds
  setInterval(updateOpenTime, 10000);
</script>
<?php endif; ?>
<!-- Farhan Akhtar :: Modified on (17-10-2024) -->

<!-- Modal For Content Messaging -->
<!-- <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
             <span class="close-button btn-close" data-bs-dismiss="modal" aria-label="Close">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-x-circle text-danger">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="15" y1="9" x2="9" y2="15"></line>
                  <line x1="9" y1="9" x2="15" y2="15"></line>
               </svg>
            </span>
            <div class="row">
               <div class="col-sm-3"><button class="button-33 width-btn emailConMess">Email</button></div>
               <div class="col-sm-3"><button class="button-33 width-btn smsConMess">SMS</button></div>
               <div class="col-sm-3"><button class="button-33 width-btn whatsappConMess">Whatsapp</button></div>
               <div class="col-sm-3"><button class="button-33 width-btn fbmessConMess">Messenger</button></div>
            </div>
      </div>
   </div>
</div> -->
<!-- Farhan Akhtar :: Modified on (17-10-2024) -->

<!-- Close Html code -->
<script language="javascript" src="<?= $SiteURL ?>public/js/new_case_script.js"></script>
<script language="javascript" src="<?= $SiteURL ?>public/js/new_vision.js"></script>

<!-- Farhan Akhtar :: Modified on (07-01-2025) :: Compose Messaging is modified as per Samir Sir -->
 <script>

document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    const phoneInput = document.getElementById("phone");
    const whatsappInput = document.getElementById("whatsapp_number");
    const smsHandleInput = document.getElementById("smshandle");

    if (phoneInput && whatsappInput && smsHandleInput) {
        console.log("All input elements found");

        phoneInput.addEventListener("input", function() {
            let phoneNumber = this.value;
            console.log("Phone input changed:", phoneNumber);

            whatsappInput.value = phoneNumber;
            console.log("Updated WhatsApp number to:", whatsappInput.value);

            smsHandleInput.value = phoneNumber;
            console.log("Updated SMS handle to:", smsHandleInput.value);
        });
    } else {
        console.warn("One or more input elements not found in the DOM");
    }
});


   $(document).ready(function () {
      $("#dropdown").on("click", function (e) {
         e.preventDefault();
         
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

    const chatHistory = document.getElementById('chatHistory');
    const showMoreBtn = document.getElementById('showMoreBtn');

    showMoreBtn.addEventListener('click', function () {
      // Toggle the 'hidden' class to reveal more content
      if (chatHistory.style.maxHeight === 'none') {
        chatHistory.style.maxHeight = '200px';
        showMoreBtn.innerHTML = 'Show More<span class="dots">...</span>';
      } else {
        chatHistory.style.maxHeight = 'none'; // Expand to full height
        showMoreBtn.innerHTML = 'Show Less<span class="dots">...</span>';
      }
    });
  </script>
  <script>
$(document).ready(function () {
  let expanded = false;

  $('#toggleInteractionBtn').click(function () {
    const $box = $('#interactionBox');

    if (expanded) {
      // Scroll to top first
      $box.animate({ scrollTop: 0 }, 300, function () {
        // After scroll finishes, collapse box
        $box.css({
          'height': '200px',
          'overflow-y': 'hidden'
        });
        $('#toggleInteractionBtn').text('Show More');
        expanded = false;
      });
    } else {
      // Expand box
      $box.css({
        'height': '600px',
        'overflow-y': 'auto'
      });
      $('#toggleInteractionBtn').text('Show Less');
      expanded = true;
    }
  });
});
</script>
